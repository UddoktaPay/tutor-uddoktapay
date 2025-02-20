<?php
/**
 * Plugin Name: Tutor LMS - UddoktaPay Gateway
 * Plugin URI: https://github.com/UddoktaPay/tutor-uddoktapay
 * Description: UddoktaPay Gateway integration for Tutor LMS
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: UddoktaPay
 * Author URI: https://uddoktapay.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: tutor-uddoktapay
 * Domain Path: /languages
 *
 * @package UddoktaPay\Tutor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin constants.
 */
define( 'TUTOR_UDDOKTAPAY_FILE', __FILE__ );
define( 'TUTOR_UDDOKTAPAY_DIR', dirname( TUTOR_UDDOKTAPAY_FILE ) );

/**
 * Autoloader.
 */
require TUTOR_UDDOKTAPAY_DIR . '/vendor/autoload.php';

/**
 * Initialize plugin.
 */
if ( class_exists( \UddoktaPay\Tutor\Core\Plugin::class ) ) {
	\UddoktaPay\Tutor\Core\Plugin::run();
}
