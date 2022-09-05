<?php

/**
 * load common and WordPress based resources
 *
 * @since 1.2.0
 */
class Advanced_Ads_Tracking_Plugin {

	/**
	 *
	 * @var Advanced_Ads_Tracking_Plugin
	 */
	protected static $instance;

	/**
	 * plugin options
	 *
	 * @var     array (if loaded)
	 */
	protected $options;

	/**
	 * name of options in db
	 *
	 * @var     string
	 */
	public $options_slug;
	
	/**
	 * array with ad types that use click tracking
	 *  AdSense and AMP are not among them
	 * 
	 * @var	    array
	 */
	public static $types_using_click_tracking = array( 'plain', 'dummy', 'content', 'image', 'flash' );
	

	private function __construct() {
		if ( ! defined( 'ADVADS_SLUG' ) ) {
			return ;
		}
		$this->options_slug =  ADVADS_SLUG . '-tracking';

		// register plugin for auto updates
		// -TODO this is true for any AJAX call
		if( is_admin() ){
			add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
            add_action( 'wp_ajax_advads_track_i327', array( $this, 'db_repair_i327' ) );
            add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		}
	}

	/**
	 *
	 * @return Advanced_Ads_Tracking_Plugin
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * load advanced ads settings
	 */
	public function options() {
		// don't initiate if main plugin not loaded
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return false;
		}

		// return options if already loaded
		if ( isset($this->options ) ) {
			return $this->options;
		}

		$this->options = get_option( $this->options_slug, array() );

		// get "old" options
		if ( $this->options === array() ) {
			$old_options = Advanced_Ads_Plugin::get_instance()->options();
			if ( isset( $old_options['tracking'] ) ) {
				$this->options = $old_options['tracking'];
				// save as new options
				$this->update_options($this->options);
			}
		}

		return $this->options;
	}
	
	/**
	 * get the tracking method used in the main options
	 * ignores whether Analytics is enabled with the constant in wp-config.php or not
	 */
	public function get_tracking_method(){
		$plugin_options = $this->options();
		return isset( $plugin_options['method'] ) ? $plugin_options['method'] : false;
	}

	/**
	 * load advanced ads settings
	 */
	public function update_options( array $options ){
		// don’t allow to clear options
		if ( $options === array() ) {
			return;
		}

		$this->options = $options;
		update_option( $this->options_slug, $options );
	}

	/**
	 * register plugin for the auto updater in the base plugin
	 *
	 * @param arr $plugins plugin that are already registered for auto updates
	 * @return arr $plugins
	 */
	public function register_auto_updater( array $plugins = array() ){
		$plugins['tracking'] = array(
			'name' => AAT_PLUGIN_NAME,
			'version' => AAT_VERSION,
			'path' => AAT_BASE_PATH . 'tracking.php',
			'options_slug' => $this->options_slug,
		);

		return $plugins;
	}

	/**
	* check, whether to track a specific ad or not
	*
	* @param obj $ad ad object
	* @param str $what, what to track. default value 'impression'
	*/
	public function check_ad_tracking_enabled( Advanced_Ads_Ad $ad, $what = 'impression' ) {
		$options = $ad->options();
		$tracking = isset( $options['tracking']['enabled'] ) && $options['tracking']['enabled'] ? $options['tracking']['enabled'] : null;

		// check for default settings
		if ( ! isset( $tracking ) || $tracking == 'default' ) {
			// check global setting
			$global_options = $this->options();
			if ( is_array( $global_options ) ) {
				if ( !isset( $global_options['everything'] ) ) {
					return true;
				} else {
					switch ( $global_options['everything'] ) {
						case 'true':
							return true;
						case 'false':
							return false;
						case 'impressions':
							return ( 'click' != $what );
						case 'clicks':
							return ( 'impression' != $what );
						default:
					}
				}
			}
		}

		if ( isset( $tracking ) ) {
			switch( $tracking ) {
				case 'enabled':
					return true;
				case 'disabled':
					return false;
				case 'impressions':
					return ( 'click' != $what );
				case 'clicks':
					return ( 'impression' != $what );
				default:
			}
		}
	}

	/**
	 * return true if this is a logged-in user and those should not be tracked
	 * based on constant ADVANCED_ADS_TRACKING_IGNORE_LOGGED_IN_USERS
	 * 
	 * @return bool true, if current interaction should not be tracked
	 */
	public function ignore_logged_in_user(){
	    
		if( defined( 'ADVANCED_ADS_TRACKING_IGNORE_LOGGED_IN_USERS' ) && is_user_logged_in() ){
			return true;
		}
		
		return false;
	    
	}	
    
    /**
     * Fix corrupted data for 2018/12/31
     */
    public function db_repair_i327() {
        $nonce = wp_unslash( $_GET['nonce'] );
        if ( false !== wp_verify_nonce( $nonce, 'advads-tracking-i327' ) ) {
            global $wpdb;
            $impressions = $wpdb->prefix . Advanced_Ads_Tracking_Util::TABLE_BASENAME;
            $clicks = $wpdb->prefix . Advanced_Ads_Tracking_Util::TABLE_CLICKS_BASENAME;
            
            $result = $wpdb->query( "UPDATE $impressions SET `timestamp` = 1812523106 WHERE `timestamp` = 1812013106" );
            $result2 = $wpdb->query( "UPDATE $clicks SET `timestamp` = 1812523106 WHERE `timestamp` = 1812013106" );
            echo $result . '//' . $result2;
            
            $options = $this->options();
            $options['i327'] = true;
            update_option( $this->options_slug, $options );
        }
        die;
    }
    
    /**
     * Prints inline scripts markup on admin footer.
     */
    public function admin_footer() {
        $options = $this->options();
        if ( !isset( $options['i327'] ) ) {
            $nonce = wp_create_nonce( 'advads-tracking-i327' );
            echo '<iframe frameborder=0 width="1" height="1" style="display:none !important;" src="' .
            admin_url( 'admin-ajax.php?action=advads_track_i327&nonce=' . $nonce ) . '"></iframe>';
        }
    }
}
