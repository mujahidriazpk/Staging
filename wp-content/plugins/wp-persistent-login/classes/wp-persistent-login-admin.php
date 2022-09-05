<?php


// If this file is called directly, abort.
defined( 'WPINC' ) || die( 'Well, get lost.' );

/**
 * Class WP_Persistent_Login_Admin
 *
 * @since 2.0.0
 */
class WP_Persistent_Login_Admin {


	
    /**
	 * Initialize the class and set its properties.
	 *
	 * We register all our common hooks here.
	 *
	 * @since  1.4.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {

		add_filter( 'plugin_action_links_'.WPPL_PLUGIN_BASENAME, array($this, 'add_settings_link') );
		add_action('admin_menu', array($this, 'create_menu_page') );
		
	}


		
	/**
	 * add_settings_link
	 *
	 * @since 2.0.0
	 * @param  array $links
	 * @return array
	 */
	public function add_settings_link( $links ) {

		$settings_link = '<a href="'.WPPL_SETTINGS_PAGE.'">' . __('Settings', WPPL_TEXT_DOMAIN) . '</a>';
		array_unshift($links, $settings_link);
		
		return $links;
	
	}


	
	/**
	 * create_menu_page
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function create_menu_page() {

		add_submenu_page( 
			'users.php', 
			'Persistent Login', 
			'Persistent Login', 
			'administrator',
			'wp-persistent-login', 
			array(new WP_Persistent_Login_Settings, 'persistent_login_options_display')
		); 
	
	} 




}

?>