<?php
/*
Plugin Name: Float To Top Button
Plugin URI: http://cagewebdev.com/float-to-top-button
Description: This plugin will add a floating scroll to top button to posts / pages
Version: 2.3.6
Date: 10/21/2020
Author: Rolf van Gelder
Author URI: http://cagewebdev.com
License: GPLv2 or later
*/
	 
/***********************************************************************************
 * 	MAIN CLASS
 ***********************************************************************************/	 
// CREATE INSTANCE
global $fttb_class;
$fttb_class = new Fttb;

class Fttb {
	var $fttb_version       = '2.3.6';
	var $fttb_release_date  = '10/21/2020';
	
	/*******************************************************************************
	 * 	CONSTRUCTOR
	 *******************************************************************************/
	function __construct() {
		// GET OPTIONS FROM DB (JSON FORMAT)
		$this->fttb_options = get_option('fttb_options');
		
		// USE THE NON-MINIFIED VERSION WHILE DEBUGGING (since v2.0.6)
		$this->script_debug = (defined('WP_DEBUG') && WP_DEBUG) ? '' : '.min';

		// FIRST RUN: SET DEFAULT SETTINGS (since v2.0.1)
		$this->fttb_init_settings();

		// BASE NAME OF THE PLUGIN
		$this->plugin_basename = plugin_basename(__FILE__);
		$this->plugin_basename = substr($this->plugin_basename, 0, strpos( $this->plugin_basename, '/'));
		
		// IMAGE LOCATION
		$this->imgurl = plugins_url().'/'.$this->plugin_basename.'/images/';
		$this->imgdir = plugin_dir_path( __FILE__ ).'images/';

		// LOCALIZATION
		add_action('init', array(&$this, 'fttb_i18n'));

		if ($this->fttb_is_regular_page()) {
			// ADD FRONTEND SCRIPTS
			// DISABLED FOR DESKTOP / LAPTOP? (v2.3.4)
			if ('Y' === $this->fttb_options['disable_desktop'] && !wp_is_mobile()) return;
			
			// DISABLED FOR MOBILE DEVICES?
			if ('Y' === $this->fttb_options['disable_mobile'] && wp_is_mobile()) return;
			
			// THE METHOD IS LOCATED IN THIS INSTANCE
			add_filter('wp_footer', array(&$this, 'fttb_hide_button'));	
			add_action('init', array(&$this, 'fttb_fe_scripts'));
			add_action('init', array(&$this, 'fttb_styles'));
		} else {
			// ON UN-INSTALLATION
			register_uninstall_hook(__FILE__, array('FloatToTopButton', 'fttb_uninstallation_handler'));

			// ADD BACKEND SCRIPTS
			// v3.0.9			
			if ($this->fttb_is_relevant_page()) {
				add_action('admin_enqueue_scripts', array(&$this, 'fttb_be_scripts'));
				add_action('init', array(&$this, 'fttb_styles'));
			}
		} // if ($this->fttb_is_regular_page())

		if (is_admin()) {
			// ADD BACKEND ACTIONS
			add_action('admin_menu', array(&$this, 'fttb_admin_menu'));
			add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this, 'fttb_settings_link'));
		} // if (is_admin())
	} // function __construct()


	/*******************************************************************************
	 * 	PLUGIN UN-INSTALLATION
	 *******************************************************************************/
	function fttb_uninstallation_handler() {
		// DELETE THE OPTIONS
		delete_option('fttb_options');		
	} // fttb_uninstallation_handler()


	/*******************************************************************************
	 *	HIDE CURRENT POST / PAGE? (CUSTOM FIELD)
	 *******************************************************************************/	
	function fttb_hide_button() {
		// IS THE BUTTON ENABLED FOR THIS PAGE / POST?
		$hide_fttb = get_post_meta(get_the_ID(), 'hide_fttb', 'N');
		echo "<!-- Float to Top Button v" . $this->fttb_version . " [" . $this->fttb_release_date . "] CAGE Web Design | Rolf van Gelder, Eindhoven, NL -->\n";
		echo "<script>var hide_fttb = '" . $hide_fttb . "'</script>\n";
		return;
	} // fttb_hide_button()


	/*******************************************************************************
	 * 	DEFINE TEXT DOMAIN
	 *******************************************************************************/
	function fttb_i18n() {
		load_plugin_textdomain('float-to-top-button', false, dirname(plugin_basename( __FILE__ )).'/language/');
	} // fttb_action_init()


	/*******************************************************************************
	 * 	IS THIS A FRONTEND PAGE?
	 *******************************************************************************/
	function fttb_is_regular_page() {
		if (isset($GLOBALS['pagenow']))
			return !is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
		else
			return !is_admin();
	} // fttb_is_regular_page()


	/*******************************************************************************
	 * 	ARE WE ON A, FOR THIS PLUGIN, RELEVANT PAGE?
	 *	Since v3.0.9
	 *******************************************************************************/	
	function fttb_is_relevant_page() {
		$this_page = '';
		if(isset($_GET['page'])) $this_page = $_GET['page'];	
		return ($this_page == 'fttb_settings');
	} // fttb_is_relevant_page()


	/*******************************************************************************
	 * 	LOAD STYLESHEET(S)
	 *******************************************************************************/
	function fttb_styles() {
		if(isset($fttb_options))
			if ('Y' === $fttb_options['disable_mobile'] && wp_is_mobile()) return;
		wp_register_style('fttb-style', plugins_url( 'css/float-to-top-button'.$this->script_debug.'.css', __FILE__ ), array(), $this->fttb_version);
		wp_enqueue_style('fttb-style');
	} // fttb_styles()


	/*******************************************************************************
	 * 	ADD PAGE TO THE SETTINGS MENU
	 *******************************************************************************/
	function fttb_admin_menu() {
		if (function_exists('add_options_page')) {
			global $fttb_options;
			$fttb_options = add_options_page(__('Float to Top Button Settings', 'float-to-top-button'), __( 'Float to Top Button', 'float-to-top-button' ), 'manage_options', 'fttb_settings', array( &$this, 'fttb_settings'));
		} // if (function_exists('add_options_page'))
	} // fttb_admin_menu()


	/*******************************************************************************
	 * 	ADD 'SETTINGS' LINK TO THE MAIN PLUGIN PAGE
	 *******************************************************************************/
	function fttb_settings_link($links) {
		array_unshift($links, '<a href="options-general.php?page=fttb_settings">Settings</a>');
		return $links;
	} // fttb_settings_link()
	
	
	/*******************************************************************************
	 * 	LOAD FRONTEND JAVASCRIPT
	 *******************************************************************************/
	function fttb_fe_scripts() {
		wp_enqueue_script('fttb-script', plugin_dir_url( __FILE__ ).'js/jquery.scrollUp.min.js', array('jquery'), $this->fttb_version, true);
		wp_enqueue_script('fttb-active', plugin_dir_url( __FILE__ ).'js/float-to-top-button'.$this->script_debug.'.js', array('jquery'), $this->fttb_version, true);
		wp_localize_script('fttb-active', 'fttb', $this->fttb_get_javascript_vars());
	} // fttb_fe_scripts()	


	/*******************************************************************************
	 * 	LOAD BACKEND JAVASCRIPT
	 *******************************************************************************/
	function fttb_be_scripts($hook) {
		global $fttb_options;
		if ($hook !== $fttb_options) return;
		wp_enqueue_script('fttb-jquery-validate', plugin_dir_url( __FILE__ ).'js/jquery.validate.min.js', array('jquery'), '0.1', true);
		wp_register_script('fttb-validate', plugin_dir_url( __FILE__ ) . 'js/fttb-validate.min.js', array( 'jquery', 'fttb-jquery-validate' ), '1.0' );
		$fttb_js_strings = array();
		$fttb_js_strings['topdistance'] = __( 'Distance from top is a required number', 'float-to-top-button' );
		$fttb_js_strings['topspeed'] = __( 'Speed back to top is a required number', 'float-to-top-button' );
		$fttb_js_strings['animationinspeed'] = __( 'Animation in speed is a required number', 'float-to-top-button' );
		$fttb_js_strings['animationoutspeed'] = __( 'Animation out speed is a required number', 'float-to-top-button' );
		$fttb_js_strings['opacity_out'] = __( 'Opacity is a required number (0-99)', 'float-to-top-button' );
		$fttb_js_strings['opacity_over'] = __( 'Opacity is a required number (0-99)', 'float-to-top-button' );
		$fttb_js_strings['zindex'] = __( 'Z-index is a required number (0-9999999999)', 'float-to-top-button' );				
		wp_localize_script('fttb-validate', 'fttb_strings', $fttb_js_strings);
		wp_enqueue_script('fttb-validate');
	} // fttb_be_scripts()


	/*******************************************************************************
	 * 	INITIALIZE SETTINGS (FIRST RUN)
	 *******************************************************************************/
	function fttb_settings() {
		// INITIALIZE SETTINGS (FIRST RUN)
		include_once(trailingslashit(dirname( __FILE__ )).'/admin/settings.php');
	} // fttb_settings()


	/*******************************************************************************
	 * 	INITIALIZE SETTINGS
	 *******************************************************************************/
	function fttb_init_settings() {
		// CHECK SETTINGS AND CREATE DEFAULT VALUES IF NEEDED
		$this->fttb_set_defaults();
		
		if (false === $this->fttb_options) {
			if (false !== get_option('fttb_topdistance')){
				global $wpdb;
				$old_options = $wpdb->get_col("SELECT option_name from $wpdb->options where option_name LIKE 'fttb%'");
				if (!empty($old_options)) {
					// DELETE ALL OPTIONS FROM v1.2.1 AND EARLIER
					foreach ($old_options as $option) {
						$value = get_option($option);
						$option_array = substr($option, 5);
						$this->fttb_options[$option_array] = $value;
						delete_option($option);
					} // foreach ($old_options as $option)
				} // if (!empty($old_options))
			} // if (false !== get_option( 'fttb_topdistance')){
		} // if (false === $this->fttb_options)

		// SAVE OPTIONS ARRAY
		update_option('fttb_options', $this->fttb_options);
	} // fttb_init_settings()


	/*******************************************************************************
	 * 	CHECK SETTINGS AND CREATE DEFAULT VALUES IF NEEDED
	 *******************************************************************************/	
	function fttb_set_defaults() {
		if (!isset($this->fttb_options['topdistance'])) $this->fttb_options['topdistance'] = 300;
		if (!isset($this->fttb_options['topspeed'])) $this->fttb_options['topspeed'] = 300;
		if (!isset($this->fttb_options['animation'])) $this->fttb_options['animation'] = 'fade';
		if (!isset($this->fttb_options['animationinspeed'])) $this->fttb_options['animationinspeed'] = 200;
		if (!isset($this->fttb_options['animationoutspeed'])) $this->fttb_options['animationoutspeed'] = 200;
		if (!isset($this->fttb_options['scrolltext'])) $this->fttb_options['scrolltext'] = __( 'Top of Page', 'float-to-top-button' );
		if (!isset($this->fttb_options['arrow_img'])) $this->fttb_options['arrow_img'] = 'arrow001.png';
		if (!isset($this->fttb_options['arrow_img_url'])) $this->fttb_options['arrow_img_url'] = '';
		if (!isset($this->fttb_options['position'])) $this->fttb_options['position'] = 'lowerright';
		if (!isset($this->fttb_options['spacing_horizontal'])) $this->fttb_options['spacing_horizontal'] = '20px';
		if (!isset($this->fttb_options['spacing_vertical'])) $this->fttb_options['spacing_vertical'] = '20px';			
		if (!isset($this->fttb_options['opacity_out'])) $this->fttb_options['opacity_out'] = 75;
		if (!isset($this->fttb_options['opacity_over'])) $this->fttb_options['opacity_over'] = 99;			
		if (!isset($this->fttb_options['disable_mobile'])) $this->fttb_options['disable_mobile'] = 'N';
		if (!isset($this->fttb_options['disable_desktop'])) $this->fttb_options['disable_desktop'] = 'N';
		if (!isset($this->fttb_options['zindex'])) $this->fttb_options['zindex'] = 2147483647;		
	} // fttb_set_defaults()


	/*******************************************************************************
	 * 	PASS SETTINGS TO JAVASCRIPT
	 *******************************************************************************/
	function fttb_get_javascript_vars() {
		return array(
			'topdistance'        => $this->fttb_options['topdistance'],
			'topspeed'           => $this->fttb_options['topspeed'],
			'animation'          => $this->fttb_options['animation'],
			'animationinspeed'   => $this->fttb_options['animationinspeed'],
			'animationoutspeed'  => $this->fttb_options['animationoutspeed'],
			'scrolltext'         => __( $this->fttb_options['scrolltext'], 'float-to-top-button' ),
			'imgurl'             => $this->imgurl,
			'arrow_img'          => $this->fttb_options['arrow_img'],
			'arrow_img_url'      => $this->fttb_options['arrow_img_url'],
			'position'           => $this->fttb_options['position'],
			'spacing_horizontal' => $this->fttb_options['spacing_horizontal'],
			'spacing_vertical'   => $this->fttb_options['spacing_vertical'],
			'opacity_out'        => $this->fttb_options['opacity_out'],
			'opacity_over'       => $this->fttb_options['opacity_over'],
			'zindex'             => $this->fttb_options['zindex']
		);		
	} // fttb_get_javascript_vars()


	/*******************************************************************************
	 * 	SANITIZE INTEGER FIELD
	 *******************************************************************************/	
	function fttb_sanitize_int($var, $digits) {
		$safe_int = intval($var);
		if(!$safe_int) $safe_int = '';
		if (strlen($safe_int) > $digits) $safe_int = substr($safe_int, 0, $digits);
		return $safe_int;
	} // fttb_sanitize_int()

} // Fttb
?>