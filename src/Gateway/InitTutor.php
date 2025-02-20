<?php
/**
 * Init Tutor Class
 *
 * Handles Tutor class initialization.
 *
 * @package UddoktaPay\Tutor\Gateway
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Gateway;

use UddoktaPay\Tutor\Gateway\UddoktaPayConfig;
use UddoktaPay\Tutor\Gateway\UddoktaPayGateway;
use Tutor\Ecommerce\Settings;

/**
 * Initialize Tutor Gateway Integration
 *
 * @since 1.0.0
 */
final class InitTutor {

	/**
	 * Register hooks and filters
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'tutor_gateways_with_class', __CLASS__ . '::paymentGatewaysWithRef', 10, 2 );
		add_filter( 'tutor_payment_gateways_with_class', __CLASS__ . '::filterPaymentGateways' );
		add_filter( 'tutor_payment_gateways', array( $this, 'addPaymentMethod' ), 100 );
	}

	/**
	 * Get payment gateways with reference class
	 *
	 * @since 1.0.0
	 *
	 * @param array  $value   Gateway with ref class.
	 * @param string $gateway Payment gateway name.
	 *
	 * @return array
	 */
	public static function paymentGatewaysWithRef( $value, $gateway ) {
		$arr = array(
			'uddoktapay' => array(
				'gateway_class' => UddoktaPayGateway::class,
				'config_class'  => UddoktaPayConfig::class,
			),
		);

		if ( isset( $arr[ $gateway ] ) ) {
			$value[ $gateway ] = $arr[ $gateway ];
		}

		return $value;
	}

	/**
	 * Filter payment gateways
	 *
	 * @since 1.0.0
	 *
	 * @param array $gateways Tutor payment gateways.
	 *
	 * @return array
	 */
	public static function filterPaymentGateways( $gateways ) {
		$arr = array(
			'uddoktapay' => array(
				'gateway_class' => UddoktaPayGateway::class,
				'config_class'  => UddoktaPayConfig::class,
			),
		);

		$gateways = $gateways + $arr;

		return $gateways;
	}

	/**
	 * Add UddoktaPay payment method
	 *
	 * @since 1.0.0
	 *
	 * @param array $methods Tutor existing payment methods.
	 *
	 * @return array
	 */
	public function addPaymentMethod( $methods ) {
		$settings = Settings::get_payment_gateway_settings( 'uddoktapay' );

		$uddoktapay_payment_method = array(
			'name'                 => 'uddoktapay',
			'label'                => isset( $settings['fields'][0]['value'] ) ? $settings['fields'][0]['value'] : 'BD Payment Methods',
			'is_installed'         => true,
			'is_active'            => true,
			'icon'                 => '',
			'support_subscription' => false,
			'fields'               => array(
				array(
					'name'  => 'display_name',
					'type'  => 'text',
					'label' => 'Display Name',
					'value' => 'BD Payment Methods',
				),
				array(
					'name'  => 'api_url',
					'type'  => 'text',
					'label' => 'API URL',
					'value' => '',
				),
				array(
					'name'  => 'api_key',
					'type'  => 'secret_key',
					'label' => 'API Key',
					'value' => '',
				),
			),
		);

		$methods[] = $uddoktapay_payment_method;

		return $methods;
	}
}
