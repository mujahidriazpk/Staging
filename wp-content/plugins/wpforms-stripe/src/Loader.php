<?php

namespace WPFormsStripe;

/**
 * WPForms Stripe loader class.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
final class Loader {

	/**
	 * Have the only available instance of the class.
	 *
	 * @var Loader
	 *
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Stripe processing instance.
	 *
	 * @since 1.0.0
	 *
	 * @var WPFormsStripe\Process
	 */
	public $process;

	/**
	 * URL to a plugin directory. Used for assets.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Path to a plugin directory. Used for loading Stripe PHP library.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $path = '';

	/**
	 * Initiate main plugin instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Loader
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Loader ) ) {
			self::$instance = new Loader();
		}

		return self::$instance;
	}

	/**
	 * Loader constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->url  = plugin_dir_url( __DIR__ );
		$this->path = plugin_dir_path( __DIR__ );

		add_action( 'wpforms_loaded', array( $this, 'init' ) );
		add_action( 'wpforms_updater', array( $this, 'updater' ) );
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// WPForms Pro is required.
		if ( ! class_exists( 'WPForms_Pro', false ) ) {
			return;
		}

		// Load translated strings.
		load_plugin_textdomain( 'wpforms-stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( wpforms_is_admin_page( 'builder' ) ) {
			new StripePayment();
			new Admin\Builder();
		} elseif ( wpforms_is_admin_page( 'settings' ) ) {
			new Admin\Settings();
		}
		new Frontend();
		$this->process = new Process();
	}

	/**
	 * Load the plugin updater.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		new \WPForms_Updater(
			array(
				'plugin_name' => 'WPForms Stripe',
				'plugin_slug' => 'wpforms-stripe',
				'plugin_path' => plugin_basename( WPFORMS_STRIPE_FILE ),
				'plugin_url'  => trailingslashit( $this->url ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_STRIPE_VERSION,
				'key'         => $key,
			)
		);
	}
}
