<?php
/*
Plugin Name: Responsive Thickbox
Plugin URI: http://wordpress.wproute.com/responsive-thickbox/
Description: Allow the thickbox to adjust its size based on the width of the browser window.
Version: 1.0.1
Author: Lyquidity Solutions Limited
Author URI: wordpress.wproute.com
Copyright: Lyquidity Solutions Limited (c) 2016
License: Lyquidity Commercial
Text Domain: responsive-thickbox
Domain Path: /languages
Updateable: true

*/

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* -----------------------------------------------------------------
 * Plugin class
 * -----------------------------------------------------------------
 */
class WordPressPlugin {

	/**
	 * @var WordPressPlugin The one true WordPressPlugin
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Main WordPressPlugin instance
	 *
	 * Insures that only one instance of WordPressPlugin exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WordPressPlugin ) ) {
			self::$instance = new WordPressPlugin;
			self::$instance->actions();
		}
		return self::$instance;
	}

	/**
	 * PHP5 constructor method.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		/* Set the constants needed by the plugin. */
		$this->constants();
	}

	/**
	 * Setup any actions
	 */
	function actions()
	{
		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 10 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

		/* Perform actions on admin initialization. */
		if(is_admin())
		{
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
		else
		{
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		add_filter( 'plugin_action_links',  array( $this, 'plugin_action_links' ),    10, 2   );
		add_filter( 'plugin_row_meta',  array( $this, 'plugin_links' ), 10, 2 );

		add_action( 'init', array($this, 'init' ), 3 );
		add_action( 'activate', array( $this, 'plugin_activation' ) );

		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
	}

	/**
	 * Take an action when the plugin is activated
	 * @since 1.0
	 * @global $wpdb
	 * @param  bool $network_side If the plugin is being network-activated
	 * @return void
	 */
	function plugin_activation()
	{
		error_log( 'plugin_activation' );

		$network_wide = false;

		$current_version = get_option( 'responsive_thickbox_version', RESPONSIVE_THICKBOX_VERSION );
		update_option( 'responsive_thickbox_version', RESPONSIVE_THICKBOX_VERSION );

		require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'class-roles.php';

		$roles = new Roles;
		$roles->add_roles();

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

	}

	/**
	 * Take an action when the plugin is activated
	 */
	function plugin_deactivation()
	{
		error_log( 'plugin_deactivation' );

		if ( ! $this->settings->get( 'remove-tables', false ) ) return;

		try
		{
			error_log("Removing roles");

			$roles = new Roles;
			$roles->remove_roles();

			$this->settings->delete_all();
		}
		catch( \Exception $ex )
		{
			error_log( "An error occurred removing the Responsive Thickbox plugin tables" );
			error_log( $ex->getMessage() );
		}
	}

	/**
	 * Defines constants used by the plugin. (Strings that are subject to translation are
	 * defined in the i18n method.)
	 *
	 * @since 1.0
	 */
	function constants()
	{
		if ( ! defined( 'RESPONSIVE_THICKBOX_PLUGIN_DIR' ) )
			define( 'RESPONSIVE_THICKBOX_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		if ( ! defined( 'RESPONSIVE_THICKBOX_INCLUDES_DIR' ) )
			define( 'RESPONSIVE_THICKBOX_INCLUDES_DIR', RESPONSIVE_THICKBOX_PLUGIN_DIR . "includes/" );

		if ( ! defined( 'RESPONSIVE_THICKBOX_PLUGIN_URL' ) )
			define( 'RESPONSIVE_THICKBOX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		if ( ! defined( 'RESPONSIVE_THICKBOX_PLUGIN_FILE' ) )
			define( 'RESPONSIVE_THICKBOX_PLUGIN_FILE', __FILE__ );

		if ( ! defined( 'RESPONSIVE_THICKBOX_VERSION' ) )
			define( 'RESPONSIVE_THICKBOX_VERSION', '1.0.1' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_WORDPRESS_COMPATIBILITY' ) )
			define( 'RESPONSIVE_THICKBOX_WORDPRESS_COMPATIBILITY', '4.5' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_STORE_API_URL' ) )
			define( 'RESPONSIVE_THICKBOX_STORE_API_URL', 'http://wordpress.wproute.com' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_PRODUCT_NAME' ) )
			define( 'RESPONSIVE_THICKBOX_PRODUCT_NAME', 'Responsive Thickbox' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_ACTIVATION_ERROR_NOTICE' ) )
			define( 'RESPONSIVE_THICKBOX_ACTIVATION_ERROR_NOTICE', 'RESPONSIVE_THICKBOX_ACTIVATION_ERROR_NOTICE' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_ACTIVATION_UPDATE_NOTICE' ) )
			define( 'RESPONSIVE_THICKBOX_ACTIVATION_UPDATE_NOTICE', 'RESPONSIVE_THICKBOX_ACTIVATION_UPDATE_NOTICE' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_DEACTIVATION_ERROR_NOTICE' ) )
			define( 'RESPONSIVE_THICKBOX_DEACTIVATION_ERROR_NOTICE', 'RESPONSIVE_THICKBOX_DEACTIVATION_ERROR_NOTICE' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_DEACTIVATION_UPDATE_NOTICE' ) )
			define( 'RESPONSIVE_THICKBOX_DEACTIVATION_UPDATE_NOTICE', 'RESPONSIVE_THICKBOX_DEACTIVATION_UPDATE_NOTICE' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_LICENSE_KEY' ) )
			define( 'RESPONSIVE_THICKBOX_LICENSE_KEY', 'responsive-thickbox_license_key' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_LICENSE_ACTIVE' ) )
			define( 'RESPONSIVE_THICKBOX_LICENSE_ACTIVE', 'responsive-thickbox_license_active' );
		
		if ( ! defined( 'RESPONSIVE_THICKBOX_SETTINGS' ) )
			define( 'RESPONSIVE_THICKBOX_SETTINGS', 'responsive_thickbox_settings' );

		if ( ! defined( 'RESPONSIVE_THICKBOX_ACTION' ) )
			define( 'RESPONSIVE_THICKBOX_ACTION', 'responsive_thickbox_action' );
	}

	/*
	 |--------------------------------------------------------------------------
	 | INTERNATIONALIZATION
	 |--------------------------------------------------------------------------
	 */

	/**
	 * Load the translation of the plugin.
	 *
	 * @since 1.0.1
	 */
	public function i18n() {
		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'RESPONSIVE_THICKBOX', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		define( 'RESPONSIVE_THICKBOX_DOMAIN_LOADED', true );
	}

	/*
	 |--------------------------------------------------------------------------
	 | INCLUDES
	 |--------------------------------------------------------------------------
	 */

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since 1.0
	 */
	public function includes()
	{
		// The Responsive Thickbox plugin will not be available while at the network level
		// unless the SL is active in blog #1.
		if (is_network_admin()) return;

		require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'actions.php';
		require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'class-settings.php';
		require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'utility-functions.php';
		require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'class-roles.php';

		$this->settings = new Responsive_Thickbox_Settings;

		if ( is_admin() )
		{
			require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'admin-notices.php';
			require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'settings.php';
			require_once RESPONSIVE_THICKBOX_INCLUDES_DIR . 'class-html-elements.php';

			$this->html = new Responsive_Thickbox_HTML_Elements;
		}
	}

	/**
	 * Enqueue scripts and styles
	 */
	function enqueue_scripts()
	{
		// wp_enqueue_style  ( "responsive_thickbox_style",  RESPONSIVE_THICKBOX_PLUGIN_URL . "assets/css/responsive-thickbox.css", null, null, "screen");
	} // end enqueue_scripts

	/**
	 * Enqueue admin scripts and styles
	 */
	function admin_enqueue_scripts()
	{
		wp_enqueue_style  ("responsive_thickbox_admin_style",  RESPONSIVE_THICKBOX_PLUGIN_URL . "assets/css/responsive-thickbox-admin.css", null, null, "screen");
		wp_enqueue_script ("responsive_thickbox_admin_script", RESPONSIVE_THICKBOX_PLUGIN_URL . "assets/js/responsive-thickbox-admin.js", array( 'jquery' ));
		wp_localize_script("responsive_thickbox_admin_script", 'responsive_thickbox_vars', array(
		));
	}

	/**
	 * Get AJAX URL
	 *
	 * @since 1.3
	 * @return string URL to the AJAX file to call during AJAX requests.
	*/
	function get_ajax_url() {
		$scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

		$current_url = edd_get_current_page_url();
		$ajax_url    = admin_url( 'admin-ajax.php', $scheme );

		if ( preg_match( '/^https/', $current_url ) && ! preg_match( '/^https/', $ajax_url ) ) {
			$ajax_url = preg_replace( '/^http/', 'https', $ajax_url );
		}

		return apply_filters( 'responsive_thickbox_ajax_url', $ajax_url );
	}

	/**
	 * Perform actions on frontend initialization.
	 */
	function init()
	{

	}

	/**
	 *
	 */
	function admin_menu()
	{
		/*
		* Function to display admin menu.
		*/
		add_management_page( __( 'Responsive Thickbox', 'responsive-thickbox' ), __( 'Responsive Thickbox', 'responsive-thickbox' ), 'responsive_thickbox', RESPONSIVE_THICKBOX_SETTINGS, '\\lyquidity\\responsive_thickbox\\' . RESPONSIVE_THICKBOX_SETTINGS );
	}

	 /**
	  * Perform actions on admin initialization.
	  */
	function admin_init()
	{
		// $this->activate_license();
		// $this->deactivate_license();
	}

	function plugin_links( $links, $file )
	{
		/* Static so we don't call plugin_basename on every plugin row. */
		static $base;
		if ( ! $base )
			$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[] = '<a href="http://wordpress.org/plugins/responsive-thickbox/faq" target="_blank">' . __( 'FAQ','responsive-thickbox' ) . '</a>';
		}
		return $links;
	}

	/**
	 *
	 */
	function plugin_action_links( $links, $file )
	{
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin )
			$this_plugin = plugin_basename( __FILE__ );
		if ( $file === $this_plugin ) {
			$settings_link = '<a href="tools.php?page=responsive_thickbox_settings">' . __( 'Settings', 'responsive-thickbox' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Callback to return plugin values to the updater
	 */
	function sl_updater_responsive_thickbox($data, $required_fields)
	{
		// Can't rely on the global $edd_options (if your license is stored as an EDD option)
		$license_key = get_option( RESPONSIVE_THICKBOX_LICENSE_KEY );

		$data['license']	= $license_key;					// license key (used get_option above to retrieve from DB)
		$data['item_name']	= RESPONSIVE_THICKBOX_PRODUCT_NAME;		// name of this plugin
		$data['api_url']	= RESPONSIVE_THICKBOX_STORE_API_URL;
		$data['version']	= RESPONSIVE_THICKBOX_VERSION;			// current version number
		$data['author']		= 'Lyquidity Solutions';		// author of this plugin

		return $data;
	}

	/**
	 * Request a license activation from the home site
	 * and update the options record if successful
	 */
	function activate_license()
	{

		if ( get_option( RESPONSIVE_THICKBOX_LICENSE_ACTIVE ) === 'valid' )
		{
			// error_log("License is valid");
			return;
		}

		if ( ! isset( $_POST[ RESPONSIVE_THICKBOX_SETTINGS ][ RESPONSIVE_THICKBOX_LICENSE_KEY ] ) )
		{
			// error_log("Can't find the license key in the post data [ " . RESPONSIVE_THICKBOX_SETTINGS . " ][ " . RESPONSIVE_THICKBOX_LICENSE_KEY . " ]");
			return;
		}

		$license = sanitize_text_field( $_POST[ RESPONSIVE_THICKBOX_SETTINGS ][ RESPONSIVE_THICKBOX_LICENSE_KEY ] );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license' 	 => $license,
			'item_name'  => urlencode( RESPONSIVE_THICKBOX_PRODUCT_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, RESPONSIVE_THICKBOX_STORE_API_URL ) ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
		{
			set_transient( RESPONSIVE_THICKBOX_ACTIVATION_ERROR_NOTICE, "Plugin activation failed.  You may have exceeded your activation count.", 10 );
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license === 'valid' )
			set_transient( RESPONSIVE_THICKBOX_ACTIVATION_UPDATE_NOTICE, RESPONSIVE_THICKBOX_PRODUCT_NAME . " plugin activated.", 10 );
		else
			set_transient( RESPONSIVE_THICKBOX_ACTIVATION_ERROR_NOTICE, RESPONSIVE_THICKBOX_PRODUCT_NAME . " plugin activation failed.  You may have exceeded your activation count.", 10 );

		update_option( RESPONSIVE_THICKBOX_LICENSE_ACTIVE, $license_data->license );

	}


	/**
	 * Request a license activation from the home site
	 * and update the options record if successful
	 */
	function deactivate_license()
	{
		global $edd_options;

		if ( ! isset( $_POST[ RESPONSIVE_THICKBOX_SETTINGS ] ) )
		{
			// error_log("Responsive Thickbox Settings not found");
			return;
		}

		if ( ! isset( $_POST[ RESPONSIVE_THICKBOX_SETTINGS ][ RESPONSIVE_THICKBOX_LICENSE_KEY ] ) )
		{
			// error_log("Can't find the license key in the post data [ " . RESPONSIVE_THICKBOX_SETTINGS . " ][ " . RESPONSIVE_THICKBOX_LICENSE_KEY . " ]");
			return;
		}

		// listen for our activate button to be clicked
		if ( ! isset( $_POST[ RESPONSIVE_THICKBOX_LICENSE_KEY . '_deactivate' ] ) )
			return;

		// retrieve the license from the database
		$license = trim( $this->settings->get( RESPONSIVE_THICKBOX_LICENSE_KEY, '' ) );

		if ( ! $license ) return;

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( RESPONSIVE_THICKBOX_PRODUCT_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, RESPONSIVE_THICKBOX_STORE_API_URL ) ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
		{
			set_transient( RESPONSIVE_THICKBOX_DEACTIVATION_ERROR_NOTICE, RESPONSIVE_THICKBOX_PRODUCT_NAME . __( ' plugin deactivation failed.', 'responsive-thickbox' ), 10 );
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed|inactive|expired|invalid_key|invalid_name"
		if ( $license_data->license !== 'failed' )
		{
			set_transient( RESPONSIVE_THICKBOX_DEACTIVATION_UPDATE_NOTICE, RESPONSIVE_THICKBOX_PRODUCT_NAME . __( ' plugin deactivated.', 'responsive-thickbox' ), 10 );
			delete_option( RESPONSIVE_THICKBOX_LICENSE_ACTIVE );
			delete_option( RESPONSIVE_THICKBOX_LICENSE_KEY );
		}
		else
			set_transient( RESPONSIVE_THICKBOX_DEACTIVATION_ERROR_NOTICE, RESPONSIVE_THICKBOX_PRODUCT_NAME . __( ' plugin deactivation failed.', 'responsive-thickbox' ), 10 );
	}
}

/**
 * The main function responsible for returning the one true example plugin
 * instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: &lt;?php $plugin = initialize(); ?&gt;
 *
 * @since 1.0
 * @return WordPressPlugin The one true WordPressPlugin Instance
 */
function responsive_thickbox() {
	return WordPressPlugin::instance();
}

/*
 * Get the plugin running
 *
 * @var WordPressPlugin Responsive Thickbox
 */
responsive_thickbox();

?>
