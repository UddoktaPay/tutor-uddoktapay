<?php
/**
 * Admin Notice Handler
 *
 * Handles various admin notices and plugin links for the UddoktaPay Tutor LMS integration.
 *
 * @package UddoktaPay\Tutor\Admin
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Admin;

/**
 * Class NoticeHandler
 *
 * Manages admin notices, plugin action links, and meta information
 * for the UddoktaPay Tutor LMS integration.
 *
 * @package UddoktaPay\Tutor\Admin
 * @since   1.0.0
 */
class NoticeHandler {

	/**
	 * Display notice for missing Tutor LMS plugin dependency.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function missingNotice(): void {
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			wp_kses(
				sprintf(
					/* translators: %1$s: Plugin name, %2$s: Required plugin name */
					esc_html__(
						'%1$s requires %2$s to be installed and activated.',
						'tutor-uddoktapay'
					),
					'<strong>UddoktaPay Gateway</strong>',
					'<strong>Tutor LMS</strong>'
				),
				array(
					'strong' => array(),
				)
			)
		);
	}

	/**
	 * Display admin notices if any are set.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function displayNotices(): void {
		if ( get_transient( 'tutor-uddoktapay-admin-notice' ) ) {
			$this->displayActivationNotice();
			delete_transient( 'tutor-uddoktapay-admin-notice' );
		}
	}

	/**
	 * Display plugin activation success notice.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private function displayActivationNotice(): void {
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			wp_kses(
				sprintf(
					/* translators: %s: Settings page URL */
					__(
						'Thank you for activating the Tutor LMS: UddoktaPay Add On. <a href="%s">Visit the payment settings page</a> to configure the UddoktaPay Payment Gateway.',
						'tutor-uddoktapay'
					),
					esc_url( admin_url( 'admin.php?page=tutor_settings&tab_page=ecommerce_payment' ) )
				),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			)
		);
	}

	/**
	 * Add plugin action links.
	 *
	 * @since  1.0.0
	 * @param  array $links Existing plugin action links.
	 * @return array Modified plugin action links.
	 */
	public function addActionLinks( array $links ): array {
		if ( current_user_can( 'manage_options' ) ) {
			$links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=tutor_settings&tab_page=ecommerce_payment' ) ),
				esc_html__( 'Configure UddoktaPay', 'tutor-uddoktapay' )
			);
		}
		return $links;
	}

	/**
	 * Add plugin row meta links.
	 *
	 * @since  1.0.0
	 * @param  array  $links Existing plugin row meta links.
	 * @param  string $file  Plugin file path.
	 * @return array Modified plugin row meta links.
	 */
	public function addPluginRowMeta( array $links, string $file ): array {
		if ( strpos( $file, 'tutor-uddoktapay.php' ) !== false ) {
			$new_links = array(
				'docs'    => sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( 'https://github.com/UddoktaPay/tutor-uddoktapay' ),
					esc_html__( 'Documentation', 'tutor-uddoktapay' )
				),
				'support' => sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( 'https://my.uddoktapay.com/submitticket.php' ),
					esc_html__( 'Support', 'tutor-uddoktapay' )
				),
			);
			$links     = array_merge( $links, $new_links );
		}
		return $links;
	}
}
