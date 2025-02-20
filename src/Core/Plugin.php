<?php
/**
 * Main Plugin Class
 *
 * Handles initialization and basic setup of the UddoktaPay Tutor LMS integration plugin.
 *
 * @package UddoktaPay\Tutor\Core
 * @since   1.0.0
 */

namespace UddoktaPay\Tutor\Core;

use UddoktaPay\Tutor\Admin\NoticeHandler;
use UddoktaPay\Tutor\Gateway\InitTutor;

/**
 * Class Plugin
 *
 * Manages plugin lifecycle and coordinates component interactions.
 * Implements singleton pattern to ensure single instance.
 *
 * @package UddoktaPay\Tutor\Core
 * @since   1.0.0
 */
final class Plugin {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var   Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Notice handler instance.
	 *
	 * @since 1.0.0
	 * @var   NoticeHandler
	 */
	private NoticeHandler $notice_handler;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->initHooks();
	}

	/**
	 * Get plugin instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @since  1.0.0
	 * @return Plugin Plugin instance.
	 */
	public static function run(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize plugin hooks.
	 *
	 * @since 1.0.0
	 */
	private function initHooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );

		// Activation hook.
		register_activation_hook( TUTOR_UDDOKTAPAY_DIR, array( $this, 'activate' ) );

		// Deactivation hook.
		register_deactivation_hook( TUTOR_UDDOKTAPAY_DIR, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize plugin components.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		$this->initializeDependencies();

		if ( ! class_exists( \TUTOR\Tutor::class ) ) {
			add_action( 'admin_notices', array( $this->notice_handler, 'missingNotice' ) );
			return;
		}

		$this->registerHooks();
		$this->initTutor();
	}

	/**
	 * Initialize plugin dependencies.
	 *
	 * @since 1.0.0
	 */
	private function initializeDependencies(): void {
		$this->notice_handler = new NoticeHandler();
	}

	/**
	 * Load Tutor gateway class.
	 *
	 * @since 1.0.0
	 */
	private function initTutor(): void {
		new InitTutor();
	}

	/**
	 * Register plugin hooks.
	 *
	 * @since 1.0.0
	 */
	private function registerHooks(): void {
		// Admin notices.
		add_action( 'admin_notices', array( $this->notice_handler, 'displayNotices' ) );

		// Plugin links.
		add_filter(
			'plugin_action_links_' . plugin_basename( TUTOR_UDDOKTAPAY_FILE ),
			array( $this->notice_handler, 'addActionLinks' )
		);
		add_filter(
			'plugin_row_meta',
			array( $this->notice_handler, 'addPluginRowMeta' ),
			10,
			2
		);
	}

	/**
	 * Activate plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate(): void {
		$activator = new Activator();
		$activator->activate();
	}

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.0.0
	 */
	public function deactivate(): void {
		$deactivator = new Deactivator();
		$deactivator->deactivate();
	}

	/**
	 * Prevent cloning of the instance.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {
	}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @since  1.0.0
	 * @throws \Exception If attempt is made to unserialize instance.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}
}
