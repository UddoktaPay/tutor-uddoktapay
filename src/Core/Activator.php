<?php
/**
 * Plugin Activator
 *
 * This class handles the activation process of the UddoktaPay Tutor LMS integration,
 * including dependency checks and initialization tasks.
 *
 * @package UddoktaPay\Tutor
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Core;

/**
 * Class Activator
 *
 * Handles plugin activation tasks and dependency checks.
 *
 * @package UddoktaPay\Tutor\Core
 * @since   1.0.0
 */
class Activator {

	/**
	 * Plugin activation handler.
	 *
	 * Sets up necessary options, checks dependencies, and performs
	 * required initialization tasks during plugin activation.
	 *
	 * @since  1.0.0
	 * @return void
	 * @throws \WP_Error If required dependencies are not met.
	 */
	public function activate(): void {
		set_transient( 'tutor-uddoktapay-admin-notice', true, 5 );

		if ( ! $this->checkDependencies() ) {
			deactivate_plugins( plugin_basename( TUTOR_UDDOKTAPAY_FILE ) );
			wp_die(
				esc_html__(
					'This plugin requires Tutor LMS to be installed and activated.',
					'tutor-uddoktapay'
				),
				'Plugin Activation Error',
				array( 'back_link' => true )
			);
		}

		flush_rewrite_rules();

		do_action( 'tutor_uddoktapay_activated' );
	}

	/**
	 * Check if required dependencies are met.
	 *
	 * Verifies that Tutor LMS is either active or at least installed.
	 *
	 * @since  1.0.0
	 * @return bool True if dependencies are met, false otherwise.
	 */
	private function checkDependencies(): bool {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$tutor_active     = is_plugin_active( 'tutor/tutor.php' );
		$tutor_pro_active = is_plugin_active( 'tutor-pro/tutor-pro.php' );
		$tutor_exists     = file_exists( WP_PLUGIN_DIR . '/tutor/tutor.php' );
		$tutor_pro_exists = file_exists( WP_PLUGIN_DIR . '/tutor-pro/tutor-pro.php' );

		return ( ( $tutor_active || $tutor_exists ) || ( $tutor_pro_active || $tutor_pro_exists ) );
	}
}
