<?php
/**
 * UddoktaPay Payment Class
 *
 * Handles payment processing functionality
 *
 * @package UddoktaPay\Tutor\Gateway
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Gateway;

use ErrorException;
use Ollyo\PaymentHub\Core\Payment\BasePayment;
use Ollyo\PaymentHub\Core\Support\Arr;
use Ollyo\PaymentHub\Core\Support\System;
use Throwable;
use UddoktaPay\Tutor\Api\ApiClient;
use UddoktaPay\Tutor\Api\ApiException;

/**
 * Handles payment processing operations
 *
 * @since 1.0.0
 */
class UddoktaPay extends BasePayment {

	/**
	 * API Client instance
	 *
	 * @since 1.0.0
	 * @var ApiClient
	 */
	protected ApiClient $client;

	/**
	 * Check required configurations
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check(): bool {
		$config_keys = Arr::make( array( 'api_url', 'api_key' ) );

		$is_config_ok = $config_keys->every(
			function ( $key ) {
				return $this->config->has( $key ) && ! empty( $this->config->get( $key ) );
			}
		);

		return $is_config_ok;
	}

	/**
	 * Setup payment gateway
	 *
	 * @since 1.0.0
	 *
	 * @throws Throwable If client initialization fails.
	 */
	public function setup(): void {
		try {
			$this->client = new ApiClient( $this->config->get( 'api_key' ), $this->config->get( 'api_url' ) );
		} catch ( Throwable $error ) {
			throw $error;
		}
	}

	/**
	 * Set payment data
	 *
	 * @since 1.0.0
	 *
	 * @param object $data Payment data.
	 *
	 * @throws Throwable If data structuring fails.
	 */
	public function setData( $data ): void {
		try {
			$structured_data = $this->prepareData( $data );
			parent::setData( $structured_data );
		} catch ( Throwable $error ) {
			throw $error;
		}
	}

	/**
	 * Prepare payment data
	 *
	 * @since 1.0.0
	 *
	 * @param object $data Raw payment data.
	 *
	 * @return array
	 */
	private function prepareData( $data ) {
		return array(
			'full_name'    => $data->billing_address->name ?? 'Default Name',
			'email'        => $data->billing_address->email,
			'amount'       => $data->total_price,
			'metadata'     => array(
				'order_id'     => $data->order_id,
				'redirect_url' => $this->config->get( 'success_url' ),
			),
			'redirect_url' => $this->config->get( 'webhook_url' ),
			'return_type'  => 'GET',
			'cancel_url'   => $this->config->get( 'cancel_url' ),
			'webhook_url'  => $this->config->get( 'webhook_url' ),
		);
	}

	/**
	 * Create payment request
	 *
	 * @since 1.0.0
	 *
	 * @throws ApiException    If payment gateway returns an error.
	 * @throws ErrorException If payment creation fails.
	 */
	public function createPayment() {
		try {
			$response = $this->client->createPayment( $this->getData() );

			if ( ! empty( $response['payment_url'] ) ) {
				// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- Payment gateway trusted redirect URL
				wp_redirect( $response['payment_url'] );
				exit;
			}

			if ( ! empty( $response['message'] ) ) {
				/* translators: %s: Error message from payment gateway */
				throw new ApiException( sprintf( esc_html__( 'Gateway Error: %s', 'tutor-uddoktapay' ), esc_html( $response['message'] ) ) );
			}

			throw new ApiException( esc_html__( 'Failed to get payment URL from UddoktaPay', 'tutor-uddoktapay' ) );

		} catch ( ApiException $error ) {
			throw new ErrorException( esc_html( $error->getMessage() ) );
		}
	}

	/**
	 * Verify payment and prepare order data
	 *
	 * @since 1.0.0
	 *
	 * @param object $payload Payment payload.
	 *
	 * @return object
	 *
	 * @throws Throwable|ApiException If payment validation fails.
	 */
	public function verifyAndCreateOrderData( object $payload ): object {
		try {
			$invoice_id = $this->extractInvoiceId( $payload );
			$response   = $this->validatePaymentResponse( $invoice_id );

			return $this->prepareOrderData( $response );
		} catch ( Throwable $error ) {
			throw new ApiException(
				sprintf(
				/* translators: %s Error message */
					esc_html__( 'Payment verification failed: %s', 'tutor-uddoktapay' ),
					esc_html( $error->getMessage() )
				)
			);
		}
	}

	/**
	 * Extract invoice ID from payload
	 *
	 * @since 1.0.0
	 *
	 * @param object $payload Payment payload.
	 *
	 * @return string
	 * @throws ApiException If invoice ID is missing.
	 */
	private function extractInvoiceId( object $payload ): string {
		// Check GET parameters first.
		if ( ! empty( $payload->get['invoice_id'] ) ) {
			return sanitize_text_field( $payload->get['invoice_id'] );
		}

		// Check POST body if not in GET.
		if ( ! empty( $payload->stream ) ) {
			$stream_data = json_decode( $payload->stream, true );
			if ( ! empty( $stream_data['invoice_id'] ) ) {
				return sanitize_text_field( $stream_data['invoice_id'] );
			}
		}

		throw new ApiException(
			esc_html__( 'Invoice ID not found in the request', 'tutor-uddoktapay' )
		);
	}

	/**
	 * Validate payment response
	 *
	 * @since 1.0.0
	 *
	 * @param string $invoice_id Invoice ID.
	 *
	 * @return array
	 * @throws ApiException If validation fails.
	 */
	private function validatePaymentResponse( string $invoice_id ): array {
		$response = $this->client->verifyPayment( $invoice_id );

		if ( empty( $response ) || ! is_array( $response ) ) {
			throw new ApiException(
				esc_html__( 'Invalid response from payment gateway', 'tutor-uddoktapay' )
			);
		}

		$required_fields = array( 'status', 'transaction_id', 'metadata' );
		foreach ( $required_fields as $field ) {
			if ( empty( $response[ $field ] ) ) {
				throw new ApiException(
					sprintf(
					/* translators: %s Field name */
						esc_html__( 'Missing required field: %s', 'tutor-uddoktapay' ),
						esc_html( $field )
					)
				);
			}
		}

		if ( empty( $response['metadata']['order_id'] ) || empty( $response['metadata']['redirect_url'] ) ) {
			throw new ApiException(
				esc_html__( 'Missing required metadata in response', 'tutor-uddoktapay' )
			);
		}

		return $response;
	}

	/**
	 * Prepare order data from response
	 *
	 * @since 1.0.0
	 *
	 * @param array $response Payment gateway response.
	 *
	 * @return object
	 */
	private function prepareOrderData( array $response ): object {
		$return_data = System::defaultOrderData();
		$status_map  = array(
			'ERROR'     => 'failed',
			'COMPLETED' => 'paid',
			'PENDING'   => 'pending',
		);

		// Set basic order data.
		$return_data->id             = sanitize_text_field( $response['metadata']['order_id'] );
		$return_data->payment_status = $status_map[ $response['status'] ] ?? 'failed';
		$return_data->transaction_id = sanitize_text_field( $response['transaction_id'] );

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$return_data->redirectUrl = esc_url_raw( $response['metadata']['redirect_url'] );

		// Set additional data.
		$return_data->payment_error_reason = '';
		$return_data->payment_payload      = $response;
		$return_data->fees                 = '';
		$return_data->earnings             = '';

		return $return_data;
	}
}
