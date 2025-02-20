<?php
/**
 * UddoktaPay Gateway Class
 *
 * Handles Gateway class functionality
 *
 * @package UddoktaPay\Tutor\Gateway
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Gateway;

use Tutor\PaymentGateways\GatewayBase;

/**
 * Handles UddoktaPay payment integration for Tutor LMS
 *
 * @since 1.0.0
 */
class UddoktaPayGateway extends GatewayBase {

	/**
	 * Gateway directory name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $dir_name = 'uddoktapay';

	/**
	 * Gateway config class
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $config_class = UddoktaPayConfig::class;

	/**
	 * Payment processing class
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $payment_class = UddoktaPay::class;

	/**
	 * Get gateway directory name
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_root_dir_name(): string {
		return $this->dir_name;
	}

	/**
	 * Get payment class name
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_payment_class(): string {
		return $this->payment_class;
	}

	/**
	 * Get config class name
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_config_class(): string {
		return $this->config_class;
	}

	/**
	 * Get autoload file path
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_autoload_file() {
		return TUTOR_UDDOKTAPAY_DIR . '/vendor/autoload.php';
	}
}
