<?php
/*
* load common and WordPress based resources
*
* @since 1.0.0
*/
class Advanced_Ads_Geo_Plugin {

	/**
	 *
	 * @var Advanced_Ads_Geo_Plugin
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

	/**
	 * subdirectory in wp-content/uploads in which the db files are saved
	 *
	 * @car     string
	 */
	public $upload_dir = '/advanced-ads-geo';


	private function __construct() {
	    add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );	    
	}

	/**
	*
	* @return Advanced_Ads_Geo_Plugin
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
	    $this->options_slug =  AAGT_SLUG;

	    // register plugin for auto updates
	    if( is_admin() ){
		add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
	    }
	}

	/**
	* Load the plugin text domain for translation.
	*
	* @since    1.0.0
	*/
	public function load_plugin_textdomain() {
	       // $locale = apply_filters('advanced-ads-plugin-locale', get_locale(), $domain);
	       load_plugin_textdomain( AAGT_SLUG, false, AAGT_BASE_DIR . '/languages' );
	}

	/**
	* load advanced ads settings
	*/
	public function options(){
	    // don’t initiate if main plugin not loaded
	    if(!class_exists('Advanced_Ads')) {
		return array();
	    }

		return Advanced_Ads_Plugin::get_instance()->options();
	}

	/**
	* add visitor condition
	*
	* @since 1.0.0
	* @param arr $conditions visitor conditions of the main plugin
	* @return arr $conditions new global visitor conditions
	*/
	public function visitor_conditions( $conditions ){

	    add_action( 'wp_ajax_load_visitor_conditions_metabox', array( $this, 'load_visitor_condition' ) );

	    $conditions['geo_targeting'] = array(
		'label' => __( 'geo location', 'advanced-ads-geo' ),
		'description' => __( 'Display ads based on geo location.', 'advanced-ads-geo' ),
		'metabox' => array( 'Advanced_Ads_Geo_Admin', 'metabox_geo' ), // callback to generate the visitor condition
		'check' => array( 'Advanced_Ads_Geo', 'check_geo' ), // callback for frontend check
		'passive_info' => array( 'hash_fields' => null, 'function' => array( 'Advanced_Ads_Geo', 'get_passive' ) )
	    );

	    return $conditions;
	}

	/**
	 * Register plugin for the auto updater in the base plugin
	 *
	 * @param array $plugins plugin that are already registered for auto updates.
	 * @return array $plugins
	 */
	public function register_auto_updater( array $plugins = array() ) {

		$plugins['geo'] = array(
			'name' 		   => AAGT_PLUGIN_NAME,
			'version' 	   => AAGT_VERSION,
			'path' 		   => AAGT_BASE_PATH . 'advanced-ads-geo.php',
			'options_slug' => $this->options_slug,
			'item_id' 	   => 8387
	    );
	    return $plugins;
	}

	/**
	 * get available targeting methods
	 */
	public static function get_targeting_methods(){

		$methods = array(
			'default' => array(
				'description' => __( 'MaxMind database (default)', 'advanced-ads-geo' )
			)
		);

		if( isset( $_SERVER[ 'HTTP_X_SUCURI_COUNTRY' ] ) ){
			$methods['sucuri'] = array(
				'description' => __( 'Sucuri Header (country only)', 'advanced-ads-geo' )
			);
		}

		return $methods;

	}
	
	/**
	 * get current targeting method
	 */
	public static function get_current_targeting_method(){
		
		$methods = self::get_targeting_methods();
		$options = self::get_instance()->options();
		
		if( empty( $options[ AAGT_SLUG ]['method'] ) 
			|| ! isset( $methods[ $options[ AAGT_SLUG ]['method'] ] ) ){
		    return 'default';
		} else {
		    return $options[ AAGT_SLUG ]['method'];
		}
		
	}
	
	/**
	 * get Sucuri country code
	 */
	public static function get_sucuri_country(){
		return isset( $_SERVER['HTTP_X_SUCURI_COUNTRY'] ) ? $_SERVER['HTTP_X_SUCURI_COUNTRY'] : '';
	}
	
	/**
	 * get the upload subdirectory
	 */
	public static function get_upload_dir(){
		// allow to manipulate the upload dir
		return apply_filters( 'advanced-ads-geo-upload-dir', self::get_instance()->upload_dir );
	}

	/**
	 * Get the full path to the upload subdirectory.
	 */
	public static function get_upload_full() {
		// Get the WordPress upload directory information, which is where we have stored the MaxMind database.
		$upload_dir = wp_upload_dir();
		if ( ! isset( $upload_dir['basedir'] ) ) {
			return false;
		}

		return $upload_dir['basedir'] . trailingslashit( self::get_upload_dir() );
	}

	/**
	 * Get the prefix of the maxmind databases.
	 *
	 */
	public static function get_maxmind_file_prefix() {
		$prefix = get_option( 'advanced-ads-geo-maxmind-file-prefix', '' );
		// Allow only digits and letters to prevent going up using `../`.
		return preg_replace( '/[^a-z0-9]/i', '', $prefix );
	}

}

