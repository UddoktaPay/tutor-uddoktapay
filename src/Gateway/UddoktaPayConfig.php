<?php
/**
 * UddoktaPay Config Class
 *
 * Handles config management for UddoktaPay gateway.
 *
 * @package UddoktaPay\Tutor\Gateway
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Gateway;

use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;
use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Tutor\Ecommerce\Settings;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;

/**
 * Manages UddoktaPay gateway configuration
 *
 * @since 1.0.0
 */
class UddoktaPayConfig extends BaseConfig implements ConfigContract {

	use PaymentUrlsTrait;

	/**
	 * API URL for the gateway
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $api_url;

	/**
	 * API Key for the gateway
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $api_key;

	/**
	 * Gateway name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $name = 'uddoktapay';

	/**
	 * Initialize config settings
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		$settings    = Settings::get_payment_gateway_settings( 'uddoktapay' );
		$config_keys = array_keys( $this->get_uddoktapay_config_keys() );

		foreach ( $config_keys as $key ) {
			$this->$key = $this->get_field_value( $settings, $key );
		}
	}

	/**
	 * Get API URL
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getApiUrl(): string {
		return $this->api_url;
	}

	/**
	 * Get API Key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function getApiKey(): string {
		return $this->api_key;
	}

	/**
	 * Check if gateway is configured
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_configured() {
		return $this->api_url && $this->api_key;
	}

	/**
	 * Get config field keys
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_uddoktapay_config_keys(): array {
		return array(
			'api_url' => 'text',
			'api_key' => 'secret_key',
		);
	}

	/**
	 * Create gateway configuration
	 *
	 * @since 1.0.0
	 */
	public function createConfig(): void {
		parent::createConfig();

		$config = array(
			'api_key' => $this->getApiKey(),
			'api_url' => $this->getApiUrl(),
		);
		$this->updateConfig( $config );
	}
}
