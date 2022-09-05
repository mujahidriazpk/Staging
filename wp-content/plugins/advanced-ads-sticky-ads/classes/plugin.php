<?php

/*
 * load common and WordPress based resources
 *
 * @since 1.2.0
 */

class Advanced_Ads_Sticky_Plugin {

		/**
	 *
	 * @var Advanced_Ads_Sticky_Plugin
	 */
	protected static $instance;

	/**
	 * plugin options
	 *
	 * @var     array (if loaded)
	 */
	protected $options = false;

		/**
		 * name of options in db
		 *
		 * @car     string
		 */
		public $options_slug;

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.2.3
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'advanced-ads-sticky', false, AASADS_BASE_DIR . '/languages' );
	}

	/**
	 *
	 * @return Advanced_Ads_Sticky_Plugin
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

		/**
		 * load actions and filters
		 *
		 * @todo include more of the hooks used in public and admin class
		 */
	public function wp_plugins_loaded() {
			// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return ;
		}

		$this->load_plugin_textdomain();
		$this->options_slug = ADVADS_SLUG . '-sticky';

			// register plugin for auto updates
		if ( is_admin() ){
			add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
		}

		// force advanced js to be activated
		add_filter( 'advanced-ads-activate-advanced-js', array( $this, 'force_advanced_js' ) );
	}

		/**
		 * load advanced ads settings
		 */
		public function options(){
			// don’t initiate if main plugin not loaded
		if ( ! class_exists( 'Advanced_Ads' ) ) { return array(); }

			return Advanced_Ads::get_instance()->options();
		}

		/**
	 * register plugin for the auto updater in the base plugin
	 *
	 * @param arr $plugins plugin that are already registered for auto updates
	 * @return arr $plugins
	 */
		public function register_auto_updater( array $plugins = array() ){

			$plugins['sticky'] = array(
			'name' => AASADS_PLUGIN_NAME,
			'version' => AASADS_VERSION,
			'path' => AASADS_BASE_PATH . 'sticky-ads.php',
			'options_slug' => $this->options_slug,
			);
			return $plugins;
		}

		/**
	 * force advanced js file from base plugin to be implemented
	 *
	 * @param bool $is_activated whether or not the file is enqueued
	 * @return true to enqueue file
	 */
	public function force_advanced_js( $is_activated ){
		return true;
	}

}

