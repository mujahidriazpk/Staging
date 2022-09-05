<?php

/*
 * load common and WordPress based resources
 *
 * @since 1.2.0
 */

class Advanced_Ads_Responsive_Plugin {

        /**
	 *
	 * @var Advanced_Ads_Responsive_Plugin
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
	 *
	 * @return Advanced_Ads_Responsive_Plugin
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
	    
	    // add new visitor condition
	    add_filter( 'advanced-ads-visitor-conditions', array( $this, 'visitor_conditions' ) );
            $this->options_slug =  ADVADS_SLUG . '-responsive';

	    // register plugin for auto updates
	    if( is_admin() ){
		    add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
	    }
	}

        /**
         * load advanced ads settings
         */
        public function options(){
            // don’t initiate if main plugin not loaded
            if(!class_exists('Advanced_Ads')) return array();

            return Advanced_Ads::get_instance()->options();
        }
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.2.9
	 */
	public function load_plugin_textdomain() {
	       // $locale = apply_filters('advanced-ads-plugin-locale', get_locale(), $domain);
	       load_plugin_textdomain( 'advanced-ads-responsive', false, AAR_BASE_DIR . '/languages' );
	}	

	/**
	 * add visitor condition
	 *
	 * @since 1.2.1
	 * @param arr $conditions visitor conditions of the main plugin
	 * @return arr $conditions new global visitor conditions
	 */
	public function visitor_conditions( $conditions ){

		if( ! defined( 'ADVANCED_ADS_RESPONSIVE_DISABLE_BROWSER_WIDTH' ) ){
			$conditions['device_width'] = array(
				'label' => __( 'browser width', 'advanced-ads-responsive' ),
				'description' => __( 'Display ads based on the browser width.', 'advanced-ads-responsive' ),
				'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_number' ), // callback to generate the metabox
				'check' => array( 'Advanced_Ads_Responsive', 'check_browser_width' ) // callback for frontend check
			);
		}
		$conditions['tablet'] = array(
			'label' => __( 'tablet', 'advanced-ads-responsive' ),
			'description' => __( 'Display ads based on tablet device.', 'advanced-ads-responsive' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_is_or_not' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Responsive', 'check_tablet' ) // callback for frontend check
		);

		return $conditions;
	}

	/**
	 * register plugin for the auto updater in the base plugin
	 *
	 * @param arr $plugins plugin that are already registered for auto updates
	 * @return arr $plugins
	 */
	public function register_auto_updater( array $plugins = array() ){

		$plugins['responsive'] = array(
			'name' => AAR_PLUGIN_NAME,
			'version' => AAR_VERSION,
			'path' => AAR_BASE_PATH . 'responsive-ads.php',
			'options_slug' => $this->options_slug,
		);
		return $plugins;
	}
}

