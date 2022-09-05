<?php
/*
* load common and WordPress based resources
*
* @since 1.0.0
*/
class Advanced_Ads_Selling_Plugin {

    /**
     *
     * @var Advanced_Ads_Selling_Plugin
     */
    protected static $instance;

    /**
     * plugin options
     *
     * @var     array (if loaded)
     */
    protected $options = false;
    
    /**
     * sale types with key => label
     * 
     * @var	array
     */
    public $sale_types = array( );
    
    /**
     * ad store slug
     * 
     * @const
     */
    const AD_STORE_SLUG = 'ad-setup';
    
    /**
     * name of options in db
     *
     * @const
     */
    const OPTION_KEY = 'advanced-ads-selling';    
    

    public function __construct() {
	
	add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
    }

    /**
    *
    * @return Advanced_Ads_Selling_Plugin
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
	if ( ! class_exists( 'Advanced_Ads', false ) || ! class_exists( 'WooCommerce', false ) ) {
	    return ;
	}
	
	$this->sale_types = array( 
	    'flat' => _x( 'flat price', 'sales type', 'advanced-ads-selling' ),
	    'days' => _x( 'days', 'sales type', 'advanced-ads-selling' ),
	    // 'months' => _x( 'months', 'sales type', 'advanced-ads-selling' ),
	);
	// more types with the Tracking add-on
	if( defined( 'AAT_VERSION' ) ){
		$this->sale_types['impressions'] = _x( 'impressions', 'sales type', 'advanced-ads-selling' );
		$this->sale_types['clicks'] = _x( 'clicks', 'sales type', 'advanced-ads-selling' );
	}	
	
	$this->load_plugin_textdomain();
	
	// add ad product type
	add_filter( 'product_type_selector', array( $this, 'add_ad_product' ) );
	$this->register_ad_product_type();

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
	   load_plugin_textdomain( 'advanced-ads-selling', false, AASA_BASE_DIR . '/languages' );
    }

    /**
    * load advanced ads selling settings
    */
    public function options(){
	    return $this->get_options();
    }
    
    /**
     *
     * @return array
     */
    public function get_options() {
	    if ( ! $this->options ) {
		    $defaultOptions = array(
			    'admin-email' => get_bloginfo( 'admin_email' ),
			    'sender-email' => Advanced_Ads_Selling_Notifications::get_default_sender_email()
		    );
		    $this->options = get_option( self::OPTION_KEY, $defaultOptions );
	    }
	    
	    if( empty( $this->options['sender-email'] ) ){
		    $this->options['sender-email'] = Advanced_Ads_Selling_Notifications::get_default_sender_email();
	    }

	    return $this->options;
    }    

    /**
    * register plugin for the auto updater in the base plugin
    *
    * @param arr $plugins plugin that are already registered for auto updates
    * @return arr $plugins
    */
    public function register_auto_updater( array $plugins = array() ){

	$plugins['selling'] = array(
	    'name' => AASA_PLUGIN_NAME,
	    'version' => AASA_VERSION,
	    'path' => AASA_BASE_PATH . 'advanced-ads-selling.php',
	    'options_slug' => self::OPTION_KEY,
	);
	return $plugins;
    }
    
    /**
     * add ad product type to product type select field
     * 
     * @param array $types
     * @return type
     */
    public function add_ad_product( $types ){

	// Key should be exactly the same as in the class product_type parameter
	$types[ 'advanced_ad' ] = __( 'Advanced Ad', 'advanced-ads-selling' );

	return $types;

    }    
    
    /**
     * register the ad product type
     * 
     * @deprecated since WooCommerce seems to have an autoloader
     */
    public function register_ad_product_type() {

	include( AASA_BASE_PATH . 'classes/WooCommerce-product.php' );
	new WC_Product_Advanced_Ad( null );
	
    }
    
    /**
     * get ad prices
     * 
     * @param int $post_id id of the post/product
     * @return arr $prices array with prices
     */
    public static function get_prices( $post_id ){
	
	$post_id = absint( $post_id );
	
	if( ! $post_id ){
	    return array();
	}
	
	// get price from database
	$prices_raw = get_post_meta( $post_id, '_ad_prices', true );
	
	// get lines
	$prices_lines = explode( "\n", $prices_raw );
	
	// load details
	$prices = array();
	foreach( $prices_lines as $_line ){
	    $_details = explode( "|", $_line );
	    
	    if( !isset( $_details[2] ) ){
		continue;
	    }
	    
	    $value = esc_attr( $_details[1] );
	    // remove all non-numeric values
	    $prices[ $value ] = array(
		'label' => esc_attr( $_details[0] ),
		'value' => $value,
		'price' => $_details[2],
	    );
	}
	
	return $prices;
	
    }
    
	/**
	 * check an ad and return if it is ordered or not
	 * 
	 * @param   int	$ad_id	ID of the ad post
	 * @return  bool    false, if not ordered ad
	 */
	public static function is_ordered_ad( $ad_id ){

		// check, if the ad has the order item meta value set
		return ( 0 !== absint ( get_post_meta( $ad_id, 'advanced_ads_selling_order_item', true ) ) );
	    
	}
	
	/**
	 * get url to public setup page
	 * 
	 * @param   str	$hash	hash to attach to url
	 */
	public function get_ad_setup_url( $hash = '' ){
	    
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$public_page_id = ( isset( $options['setup-page-id'] ) && $options['setup-page-id'] ) ? absint( $options['setup-page-id'] ) : false;
		
		if( $public_page_id ){
			return add_query_arg( 'h', esc_attr( $hash ), get_permalink( $public_page_id ) );
		} else {
			return home_url( self::AD_STORE_SLUG . '/' . $hash );
		}
			
	}
	
	/**
	 * compare WooCommerce version
	 * with 3.0 by default
	 */
	public static function version_check( $version = '3.0' ) {
		global $woocommerce;
		
		if ( isset( $woocommerce->version ) && version_compare( $woocommerce->version, $version, ">=" ) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * return whether clients should receive the ad setup link and notifications or not
	 * 0 = they receive it
	 * 1 = no info for ad setup is public
	 */
	public static function hide_ad_setup(){
	    
	    $options = self::get_instance()->get_options();
	    
	    return isset( $options['hide-ad-setup'] ) ? $options['hide-ad-setup'] : 0;
	}
}

