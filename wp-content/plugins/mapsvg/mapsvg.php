<?php
/*
Plugin Name: MapSVG
Plugin URI: http://codecanyon.net/item/mapsvg-interactive-vector-maps/2547255?ref=RomanCode
Description: Interactive Vector Maps (SVG), Google maps, Image maps.
Author: Roman S. Stepanov
Author URI: http://codecanyon.net/user/RomanCode
Version: 5.16.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The MAPSVG_DEBUG constant switches between Development and Production modes.
 * When MAPSVG_DEBUG is "true" MapSVG loads full (not minified) versions of JS/CSS files.
 * Also logging from PHP to browser console gets enabled.
 * To enable the Dev mode, add the following line to wp_config.php:
 *
 * define('MAPSVG_DEBUG', true);
 *
 * Also, debugging can be enabled by adding ?mapsvg_debug=SECRET_KEY to URL
 */
if(!defined('MAPSVG_DEBUG')){
    if(isset($_GET['mapsvg_debug'])){
	    $secret_key = $_GET['mapsvg_debug'];
        $open_key   = 'salted_maps_2020';
        $key        = $secret_key.$open_key;
        $md5        = md5($key);
	    define('MAPSVG_DEBUG', $md5 === 'd603c50bd2fd093451c0c483e7eff3fe');
    } else {
	    define('MAPSVG_DEBUG', false);
    }
}

if(MAPSVG_DEBUG){
    // The following line is required for FirePHP
    ob_start();
	require_once('vendor/FirePHPCore/fb.php');
	//error_reporting(E_ALL);
}

/**
 * Include class that generates pages with shortcodes content.
 * It is used in handlebars templates for the following tags:
 * {{shortcode '[apple id="123"]'}}
 */
if(isset($_GET['mapsvg_shortcode']) || isset($_GET['mapsvg_shortcode_inline']) || isset($_GET['mapsvg_embed_post'])) {
	include( 'shortcodes.php' );
}

/**
 * If MAPSVG_RAND == true && MAPSVG_DEBUG == true
 * then a random number is added to js/css file URLs to disable cache
 */
define('MAPSVG_RAND', isset($_GET['norand']) ? false : true);

$upload_dir = wp_upload_dir();
$plugin_dir_url = plugin_dir_url( __FILE__ );
if(is_ssl()){
	$upload_dir['baseurl'] = str_replace('http:','https:', $upload_dir['baseurl']);
	$plugin_dir_url = str_replace('http:','https:', $plugin_dir_url);
}


define('MAPSVG_INFO', 'INFO');
define('MAPSVG_ERROR', 'ERROR');

define('MAPSVG_PLUGIN_URL', $plugin_dir_url);
define('MAPSVG_PLUGIN_DIR', realpath(plugin_dir_path( __FILE__ )));
$parts = parse_url(MAPSVG_PLUGIN_URL);
define('MAPSVG_PLUGIN_PATH', $parts['path']);
define('MAPSVG_MAPS_DIR', realpath(MAPSVG_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'maps'));
define('MAPSVG_MAPS_UPLOADS_DIR', $upload_dir['basedir'] . DIRECTORY_SEPARATOR. 'mapsvg');
define('MAPSVG_MAPS_UPLOADS_URL', $upload_dir['baseurl'] . '/mapsvg/');
define('MAPSVG_MAPS_URL', MAPSVG_PLUGIN_URL . 'maps/');
define('MAPSVG_PINS_DIR', realpath(MAPSVG_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'markers'));
define('MAPSVG_PINS_URL', MAPSVG_PLUGIN_URL . 'markers/');
if(MAPSVG_DEBUG){
    define('MAPSVG_GIT_BRANCH', shell_exec('cd '.__DIR__.'; git branch | sed -n -e \'s/^\* \(.*\)/\1/p\''));
} else {
	define('MAPSVG_GIT_BRANCH', '');
}

define('MAPSVG_VERSION', '5.16.1');
define('MAPSVG_ASSET_VERSION', MAPSVG_VERSION.( MAPSVG_DEBUG && MAPSVG_RAND ? rand():''));
define('MAPSVG_JQUERY_VERSION', MAPSVG_VERSION.( MAPSVG_DEBUG && MAPSVG_RAND ? rand():''));
define('MAPSVG_DB_VERSION', '1.0');
define('MAPSVG_TABLE_NAME',  'mapsvg');

/**
 * List of MapSVG version numbers with incompatible code changes (space-separated).
 * If the map version is between of these numbers it needs to be upgraded.
 */
define('MAPSVG_INCOMPATIBLE_VERSIONS',  '2.0.0 3.2.0 5.0.0');

$mapsvg_inline_script = array();
$mapsvg_page = 'index';

/**
 * Checking the purchase code
 */
$mapsvg_purchase_code = get_option('mapsvg_purchase_code');
if(!empty($mapsvg_purchase_code)){
	require MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'vendor/plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://mapsvg.com/wp-updates/?action=info',
		__FILE__, //Full path to the main plugin file or functions.php.
		'mapsvg'
	);
	//Add the license key to query arguments.
	$myUpdateChecker->addQueryArgFilter('mapsvg_filter_update_checks');
	function mapsvg_filter_update_checks($queryArgs) {
		global $mapsvg_purchase_code;
		$queryArgs['purchase_code'] = $mapsvg_purchase_code;
		return $queryArgs;
	}
}


/**
 * Add buttons to WP Page editor (for WP 4.x versions)
 */
function mapsvg_setup_tinymce_plugin(){
// Check if the logged in WordPress User can edit Posts or Pages
    // If not, don't register our TinyMCE plugin
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
        return;
    }

    // Check if the logged in WordPress User has the Visual Editor enabled
    // If not, don't register our TinyMCE plugin
    if ( get_user_option( 'rich_editing' ) !== 'true' ) {
        return;
    }

    wp_register_style('mapsvg-tinymce', MAPSVG_PLUGIN_URL . "css/mapsvg-tinymce.css");
    wp_enqueue_style('mapsvg-tinymce');

    // Setup some filters
    add_filter('mce_external_plugins', 'mapsvg_add_tinymce_plugin');
    add_filter('mce_buttons', 'mapsvg_add_tinymce_button');
    add_action('admin_footer', 'add_thickbox');

}

if ( is_admin() ) {
    add_action( 'init', 'mapsvg_setup_tinymce_plugin' );
}

/**
 * Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
 *
 * @param array $plugin_array Array of registered TinyMCE Plugins
 * @return array Modified array of registered TinyMCE Plugins
 */
function mapsvg_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['mapsvg'] = MAPSVG_PLUGIN_URL . 'js/tinymce-mapsvg.js';
    return $plugin_array;
}

/**
 * Adds a button to the TinyMCE / Visual Editor which the user can click
 * to insert a custom CSS class.
 *
 * @param array $buttons Array of registered TinyMCE Buttons
 * @return array Modified array of registered TinyMCE Buttons
 */
function mapsvg_add_tinymce_button($buttons){
	array_push( $buttons, 'mapsvg' );
	return $buttons;
}



/**
 * Add common JS & CSS
 */
function mapsvg_add_jscss_common(){


    // If MAPSVG_DEBUG == true, load full versions of JS/CSS
    // If MAPSVG_DEBUG == false, load minified merged JS/CSS
    if(MAPSVG_DEBUG){
	    wp_register_style('mapsvg', MAPSVG_PLUGIN_URL . 'css/mapsvg.css', null, MAPSVG_ASSET_VERSION);
	    wp_enqueue_style('mapsvg');

	    wp_register_style('nanoscroller', MAPSVG_PLUGIN_URL . 'css/nanoscroller.css');
	    wp_enqueue_style('nanoscroller');

	    wp_register_style('select2', MAPSVG_PLUGIN_URL . 'css/select2.min.css', null, '4.0.31');
	    wp_enqueue_style('select2');

        wp_register_script('jquery.mousewheel', MAPSVG_PLUGIN_URL . 'js/jquery.mousewheel.min.js',array('jquery'), '3.0.6');
        wp_enqueue_script('jquery.mousewheel', null, '3.0.6');

        wp_register_script('handlebars', MAPSVG_PLUGIN_URL . 'js/handlebars.js', null, '4.0.2'.MAPSVG_ASSET_VERSION);
        wp_enqueue_script('handlebars');
        wp_enqueue_script('handlebars-helpers', MAPSVG_PLUGIN_URL . 'js/handlebars-helpers.js', null, MAPSVG_ASSET_VERSION);

        wp_register_script('mselect2', MAPSVG_PLUGIN_URL . 'js/select2.full.min.js', array('jquery'), '4.0.31',true);
        wp_enqueue_script('mselect2');

        wp_register_script('form.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/form.mapsvg.js', array('jquery','mapsvg'), MAPSVG_ASSET_VERSION);
        wp_enqueue_script('form.controller.admin.mapsvg');

        wp_register_script('typeahead', MAPSVG_PLUGIN_URL . 'js/typeahead.bundle.min.js', null, '1.2.1');
        wp_enqueue_script('typeahead');

//        wp_enqueue_script('database-service.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/database-service.js', array('jquery', 'mapsvg'), MAPSVG_ASSET_VERSION);

        wp_register_script('nanoscroller', MAPSVG_PLUGIN_URL . 'js/jquery.nanoscroller.min.js', null, '0.8.7');
        wp_enqueue_script('nanoscroller');

	    wp_register_script('mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg/globals.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-resize', MAPSVG_PLUGIN_URL . 'js/mapsvg/resize.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-map', MAPSVG_PLUGIN_URL . 'js/mapsvg/map.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-controller', MAPSVG_PLUGIN_URL . 'js/mapsvg/controller.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-database', MAPSVG_PLUGIN_URL . 'js/mapsvg/database-service.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-mapobject', MAPSVG_PLUGIN_URL . 'js/mapsvg/mapobject.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-region', MAPSVG_PLUGIN_URL . 'js/mapsvg/region.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-marker', MAPSVG_PLUGIN_URL . 'js/mapsvg/marker.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-cluster', MAPSVG_PLUGIN_URL . 'js/mapsvg/markercluster.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-color', MAPSVG_PLUGIN_URL . 'js/mapsvg/tinycolor.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-popover', MAPSVG_PLUGIN_URL . 'js/mapsvg/popover.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-details', MAPSVG_PLUGIN_URL . 'js/mapsvg/detailsview.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-dir', MAPSVG_PLUGIN_URL . 'js/mapsvg/directory.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-filter', MAPSVG_PLUGIN_URL . 'js/mapsvg/filters.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-location', MAPSVG_PLUGIN_URL . 'js/mapsvg/location.js', array('jquery'), (MAPSVG_RAND?rand():''));
	    wp_enqueue_script('mapsvg-locationaddress', MAPSVG_PLUGIN_URL . 'js/mapsvg/locationaddress.js', array('jquery'), (MAPSVG_RAND?rand():''));

    } else {
	    wp_register_style('mapsvg-front-min-css', MAPSVG_PLUGIN_URL . 'dist/mapsvg-front.min.css', null, MAPSVG_ASSET_VERSION);
	    wp_enqueue_style('mapsvg-front-min-css');
	    wp_register_script('mapsvg', MAPSVG_PLUGIN_URL . 'dist/mapsvg-front.min.js', array('jquery'), MAPSVG_JQUERY_VERSION,true);
    }

    wp_localize_script('mapsvg','mapsvg_paths', array(
        'root'      => MAPSVG_PLUGIN_PATH,
        'templates' => MAPSVG_PLUGIN_PATH.'js/mapsvg-admin/templates/',
        'maps'      => parse_url(MAPSVG_MAPS_URL, PHP_URL_PATH),
        'uploads'   => parse_url(MAPSVG_MAPS_UPLOADS_URL, PHP_URL_PATH)
    ));
    wp_localize_script('mapsvg','mapsvg_ini_vars', array(
        'post_max_size'       => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize')
    ));
    wp_enqueue_script('mapsvg');

}


/**
 * Add admin's JS & CSS
 */
function mapsvg_add_jscss_admin($hook_suffix){

    global $mapsvg_settings_page, $wp_version;

	// If the map version is older than 2.x then load old interface
    if(isset($_GET['map_id']) && !empty($_GET['map_id'])){
        $mapsvg_version = get_post_meta($_GET['map_id'], 'mapsvg_version', true);
        if(version_compare($mapsvg_version, '3.0.0', '<')){
            mapsvg_add_jscss_admin_2();
            return;
        }
    }

    // Load scripts only if it's MapSVG config page! Don't load scripts on all WP Admin pages
    if ( $mapsvg_settings_page != $hook_suffix )
        return;

    if(isset($_GET['page']) && $_GET['page']=='mapsvg-config'){

        // Load scripts and CSS for WP Media file uploader
        wp_enqueue_media();

	    // Load full versions of JS/CSS files if MAPSVG_DEBUG == true
	    if(MAPSVG_DEBUG){


            wp_register_script('admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/admin.js', array('jquery','mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('admin.mapsvg');
            wp_enqueue_script('controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/controller.js', array('mapsvg','admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('settings.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/settings-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('regions.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/regions-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('regions-list.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/regions-list-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('regions-structure.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/regions-structure-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('regions-settings.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/regions-settings-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('regions-csv.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/regions-csv-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('directory.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/directory-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('details.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/details-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('filters.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/filters-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('filters-structure.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/filters-structure-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('filters-settings.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/filters-settings-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('actions.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/actions-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('modal.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/modal-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('colors.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/colors-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('javascript.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/javascript-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('templates.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/templates-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('database.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/database-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('database-list.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/database-list-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('google-maps.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/google-maps.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('layers.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/layers-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('layers-list.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/layers-list-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('layers-settings.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/layers-settings-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('draw.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/draw.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('draw-region.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/draw-region-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('floors.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/floors-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('floors-list.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/floors-list-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('database-structure.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/database-structure-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('database-settings.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/database-settings-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('database-csv.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/database-csv-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('css.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/css-controller.js', array('mapsvg','admin.mapsvg','controller.admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_register_script('form.controller.admin.mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg-admin/form.mapsvg.js', array('jquery','mapsvg','admin.mapsvg'), MAPSVG_ASSET_VERSION);
            wp_enqueue_script('form.controller.admin.mapsvg');
	        wp_register_script('papaparse', MAPSVG_PLUGIN_URL . "js/papaparse.min.js", null, '4.6.0');
	        wp_enqueue_script('papaparse');

	        wp_register_script('bootstrap', MAPSVG_PLUGIN_URL . "js/bootstrap.min.js", null, '3.3.6');
	        wp_enqueue_script('bootstrap');
	        wp_register_style('bootstrap', MAPSVG_PLUGIN_URL . "css/bootstrap.min.css", null, '3.3.6');
	        wp_enqueue_style('bootstrap');

	        wp_register_script('bootstrap-toggle', MAPSVG_PLUGIN_URL . "js/bootstrap-toggle.min.js", null, '3.3.6');
	        wp_enqueue_script('bootstrap-toggle');
	        wp_register_style('bootstrap-toggle', MAPSVG_PLUGIN_URL . "css/bootstrap-toggle.min.css", null, '3.3.6');
	        wp_enqueue_style('bootstrap-toggle');

	        wp_register_style('fontawesome', MAPSVG_PLUGIN_URL . "css/font-awesome.min.css", null, '4.4.0');
	        wp_enqueue_style('fontawesome');

	        wp_register_script('bootstrap-colorpicker', MAPSVG_PLUGIN_URL . 'js/bootstrap-colorpicker.min.js');
	        wp_enqueue_script('bootstrap-colorpicker');
	        wp_register_style('bootstrap-colorpicker', MAPSVG_PLUGIN_URL . 'css/bootstrap-colorpicker.min.css');
	        wp_enqueue_style('bootstrap-colorpicker');

	        wp_enqueue_script('growl', MAPSVG_PLUGIN_URL . 'js/jquery.growl.js', array('jquery'), '4.0',true);
	        wp_register_style('growl', MAPSVG_PLUGIN_URL . 'css/jquery.growl.css', null, '1.0');
	        wp_enqueue_style('growl');

	        wp_register_style('main.css', MAPSVG_PLUGIN_URL . 'css/main.css', null, MAPSVG_ASSET_VERSION);
	        wp_enqueue_style('main.css');


	        wp_register_script('mselect2', MAPSVG_PLUGIN_URL . 'js/select2.full.min.js', array('jquery'), '4.0.31',true);
	        wp_enqueue_script('mselect2');
	        wp_register_style('select2', MAPSVG_PLUGIN_URL . 'css/select2.min.css',null,'4.0.3');
	        wp_enqueue_style('select2');

	        wp_register_script('ionslider', MAPSVG_PLUGIN_URL . 'js/ion.rangeSlider.min.js', array('jquery'), '2.1.2');
	        wp_enqueue_script('ionslider');
	        wp_register_style('ionslider', MAPSVG_PLUGIN_URL . 'css/ion.rangeSlider.css');
	        wp_enqueue_style('ionslider');
	        wp_register_style('ionslider-skin', MAPSVG_PLUGIN_URL . 'css/ion.rangeSlider.skinNice.css');
	        wp_enqueue_style('ionslider-skin');

	        wp_register_script('codemirror', MAPSVG_PLUGIN_URL . 'js/codemirror.js', null, '1.0');
	        wp_enqueue_script('codemirror');
	        wp_register_style('codemirror', MAPSVG_PLUGIN_URL . 'css/codemirror.css');
	        wp_enqueue_style('codemirror');
	        wp_register_script('codemirror.javascript', MAPSVG_PLUGIN_URL . 'js/codemirror.javascript.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.javascript');
	        wp_register_script('codemirror.xml', MAPSVG_PLUGIN_URL . 'js/codemirror.xml.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.xml');
	        wp_register_script('codemirror.css', MAPSVG_PLUGIN_URL . 'js/codemirror.css.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.css');
	        wp_register_script('codemirror.htmlmixed', MAPSVG_PLUGIN_URL . 'js/codemirror.htmlmixed.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.htmlmixed');
	        wp_register_script('codemirror.simple', MAPSVG_PLUGIN_URL . 'js/codemirror.simple.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.simple');
	        wp_register_script('codemirror.multiplex', MAPSVG_PLUGIN_URL . 'js/codemirror.multiplex.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.multiplex');
	        wp_register_script('codemirror.handlebars', MAPSVG_PLUGIN_URL . 'js/codemirror.handlebars.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.handlebars');
	        wp_register_script('codemirror.hint', MAPSVG_PLUGIN_URL . 'js/codemirror.show-hint.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.hint');
	        wp_register_script('codemirror.anyword-hint', MAPSVG_PLUGIN_URL . 'js/codemirror.anyword-hint.js', array('codemirror'), '1.0');
	        wp_enqueue_script('codemirror.anyword-hint');
	        wp_register_style('codemirror.hint.css', MAPSVG_PLUGIN_URL . 'css/codemirror.show-hint.css', array('codemirror'), '1.0');
	        wp_enqueue_style('codemirror.hint.css');

//        wp_register_style('codemirror.lint', MAPSVG_PLUGIN_URL . 'css/codemirror.lint.css', array('codemirror'), '1.0');
//        wp_enqueue_style('codemirror.lint');
//        wp_register_script('jshint', MAPSVG_PLUGIN_URL . 'js/jshint.js', array('codemirror'), '1.0');
//        wp_enqueue_script('jshint');
//        wp_register_script('codemirror.lint', MAPSVG_PLUGIN_URL . 'js/codemirror.lint.js', array('codemirror'), '1.0');
//        wp_enqueue_script('codemirror.lint');
//        wp_register_script('codemirror.html-lint', MAPSVG_PLUGIN_URL . 'js/codemirror.html-lint.js', array('codemirror'), '1.0');
//        wp_enqueue_script('codemirror.html-lint');
//        wp_register_script('codemirror.javascript-lint', MAPSVG_PLUGIN_URL . 'js/codemirror.javascript-lint.js', array('codemirror'), '1.0');
//        wp_enqueue_script('codemirror.javascript-lint');
//        wp_register_script('codemirror.css-lint', MAPSVG_PLUGIN_URL . 'js/codemirror.css-lint.js', array('codemirror'), '1.0');
//        wp_enqueue_script('codemirror.css-lint');

//        wp_register_script('typeahead', MAPSVG_PLUGIN_URL . 'js/typeahead.bundle.min.js', null, '1.0');
//        wp_enqueue_script('typeahead');

	        wp_register_script('sortable', MAPSVG_PLUGIN_URL . 'js/sortable.min.js', null, '1.4.2');
	        wp_enqueue_script('sortable');

	        wp_register_script('jscrollpane', MAPSVG_PLUGIN_URL . 'js/jquery.jscrollpane.min.js', null, '0.8.7');
	        wp_enqueue_script('jscrollpane');
	        wp_register_style('jscrollpane', MAPSVG_PLUGIN_URL . 'css/jquery.jscrollpane.css');
	        wp_enqueue_style('jscrollpane');

	        wp_register_script('html2canvas', MAPSVG_PLUGIN_URL . 'js/html2canvas.min.js', null, '0.5.0');
	        wp_enqueue_script('html2canvas');

	        wp_register_script('bootstrap-datepicker', MAPSVG_PLUGIN_URL . 'js/bootstrap-datepicker.min.js', array('bootstrap'), '1.6.4.2');
	        wp_enqueue_script('bootstrap-datepicker');
	        wp_register_script('bootstrap-datepicker-locales', MAPSVG_PLUGIN_URL . 'js/datepicker-locales/locales.js', array('bootstrap','bootstrap-datepicker'), '1.0');
	        wp_enqueue_script('bootstrap-datepicker-locales');
	        wp_register_style('bootstrap-datepicker', MAPSVG_PLUGIN_URL . 'css/bootstrap-datepicker.min.css', array('bootstrap'), '1.6.4.2');
	        wp_enqueue_style('bootstrap-datepicker');

	        wp_register_script('path-data-polyfill', MAPSVG_PLUGIN_URL . 'js/path-data-polyfill.js', null, '1.0');
	        wp_enqueue_script('path-data-polyfill');

        } else {
	        // Load minified JS/CSS files if MAPSVG_DEBUG == false:
	        wp_register_script('admin.mapsvg', MAPSVG_PLUGIN_URL . 'dist/mapsvg-admin.min.js', array('jquery','mapsvg'), MAPSVG_ASSET_VERSION);
	        wp_enqueue_script('admin.mapsvg');
	        wp_register_style('mapsvg-admin-min-css', MAPSVG_PLUGIN_URL . "dist/mapsvg-admin.min.css", null, MAPSVG_ASSET_VERSION);
	        wp_enqueue_style('mapsvg-admin-min-css');
        }
    }

	// Load common JS/CSS files
	mapsvg_add_jscss_common();
}


/**
 * Add menu element to WP Admin menu
 */
$mapsvg_settings_page = '';

function mapsvg_config_page() {
    global $mapsvg_settings_page;

	if ( function_exists('add_menu_page') && current_user_can('edit_posts'))
		$mapsvg_settings_page = add_menu_page('MapSVG', 'MapSVG', 'edit_posts', 'mapsvg-config', 'mapsvg_conf', '', 66);


    add_action('admin_enqueue_scripts', 'mapsvg_add_jscss_admin',0);
}

add_action( 'admin_menu', 'mapsvg_config_page' );


/**
 *  Render [mapsvg] shortcode and load the map.
 *
 *  Shortcode returns an empty <div id="mapsvg-XXX" class="mapsvg"</div> container
 *  and adds a JS script at the bottom of the page with MapSVG execution code
 *  that creates the map in the container
 *
 * @param $atts
 * Attributes from the shortcode
 *
 * @return string
 * String that replaces the [mapsvg] shortcode
 */
function mapsvg_print( $atts ){
    global $mapsvg_inline_script;

    if(!isset($atts['id'])){
	    return 'Error: no ID in mapsvg shortcode.';
    }

    // Load old interface if map version < 3.0
    $mapsvg_version = get_post_meta($atts['id'], 'mapsvg_version', true);


    if(version_compare($mapsvg_version, '3.0.0', '<')){
	    if(version_compare($mapsvg_version, '2.0.0', '<')){
	        mapsvg_maybe_update_the_map($atts['id'], $mapsvg_version);
        }
        mapsvg_add_jscss_common_2();
        return mapsvg_print_2($atts);
    }

    // Check if map settings need to be upgraded
    mapsvg_maybe_update_the_map($atts['id'], $mapsvg_version);

	// Load JS/CSS files
    mapsvg_add_jscss_common();
    do_action('mapsvg_shortcode');

	// Load map settings
    $post = mapsvg_get_map($atts['id']);

    if (empty($post->ID)){
	    return 'Map not found, please check "id" parameter in your shortcode.';
    }


	$options = json_decode($post->post_content, ARRAY_A);

    if($options && is_array($options)){
        $query = array(
	        'map_id' => $post->ID,
	        'table'  => 'regions',
	        'with_schema' => true,
	        'perpage' => 0,
	        'sortBy' => ( isset($options['menu']) && $options['menu']['source'] == 'regions' ? $options['menu']['sortBy'] : ( isset($options['menu']) && strpos($options['menu']['source'],'geo-cal') !== false ? 'title' : 'id' ) ),
	        'sortDir' => isset($options['menu']) &&  $options['menu']['source'] == 'regions' ? $options['menu']['sortDirection'] : 'asc'
        );

        if(isset($options['menu']) && isset($options['menu']['filterout']) && $options['menu']['source']=='regions' && !empty($options['menu']['filterout']['field'])){
	        $query['filterout'][$options['menu']['filterout']['field']] = $options['menu']['filterout']['val'];
        }


	    $options['data_regions'] = mapsvg_data_get_all($query);

	    $perpage = isset($options['database']) && (int)$options['database']['pagination']['on'] ? $options['database']['pagination']['perpage'] : 0;

	    $query_db = array(
		    'map_id' => $post->ID,
		    'table'  => 'database',
		    'with_schema' => true,
		    'perpage' => $perpage,
		    'sortBy' => isset($options['menu']) && $options['menu']['source'] == 'database' ? $options['menu']['sortBy'] : 'id',
		    'sortDir' => isset($options['menu']) && $options['menu']['source'] == 'database' ? $options['menu']['sortDirection'] : 'desc'
        );

	    if(isset($options['menu']) && isset($options['menu']['filterout']) && $options['menu']['source']=='database' && !empty($options['menu']['filterout']['field'])){
		    $query_db['filterout'][$options['menu']['filterout']['field']] = $options['menu']['filterout']['val'];
	    }

	    $options['data_db'] = mapsvg_data_get_all($query_db);

	    $js_mapsvg_options = json_encode($options);
    }else{
	    $js_mapsvg_options = $post->post_content;
    }

	$no_double_render = !empty($atts['no_double_render']) ? true : false;

	// Prepare MapSVG container (short
	$container_id = $no_double_render ? $post->ID : mapsvg_generate_container_id($post->ID, 0);
    $data    = '<div id="mapsvg-'.$container_id.'" class="mapsvg"></div>';
    $script  = "<script type=\"text/javascript\">";
    $script .= "jQuery(document).ready(function(){";
    $script .= "MapSVG.version = '".MAPSVG_VERSION."';\n";
    $script .= 'var mapsvg_options = '.$js_mapsvg_options.';';

    if(!empty($atts['selected'])){
      $country = str_replace(' ','_', $atts['selected']);
      $script .= 'jQuery.extend( true, mapsvg_options, {regions: {"'.$country.'": {selected: true}}} );';
    }
    $script .= 'jQuery.extend( true, mapsvg_options, {svg_file_version: '.(int)get_post_meta($post->ID, 'mapsvg_svg_file_version', true).'} );';

    $script .= 'jQuery("#mapsvg-'.$container_id.'").mapSvg(mapsvg_options);});</script>';

    $mapsvg_inline_script[$container_id] = $script;


    // Load MapSVG execution script at the bottom of the page
    add_action('admin_footer', 'mapsvg_script', 9998);

    return $data;
}
add_shortcode( 'mapsvg', 'mapsvg_print' );

/**
 * Generate container ID for the map
 */
function mapsvg_generate_container_id($map_id, $iteration = 0){
   global $mapsvg_inline_script;

   $iteration_str = '';

   if($iteration !== 0){
	   $iteration_str = '-'.$iteration;
   }
   if(isset($mapsvg_inline_script[$map_id.$iteration_str])){
	   $iteration++;
       return mapsvg_generate_container_id($map_id, $iteration);
   } else {
       return $map_id.$iteration_str;
   }
}

/**
 * Output MapSVG scripts
 */
function mapsvg_script(){
    global $mapsvg_inline_script;

    foreach($mapsvg_inline_script as $m){
        echo $m;
    }
}

/**
 * This is a fix for the WP bug that replaces "&" sign with "&#038;" in post_content field
 * The bug breaks URLs
 * The fix converts "&#038;" back to "&"
 */
function mapsvg_so_handle_038($content) {
    $content = str_replace(array("&#038;","&amp;"), "&", $content);
    return $content;
}
add_filter('the_content', 'mapsvg_so_handle_038', 199, 1);


/**
 * Get map settings from the database
 */
function mapsvg_get_map($id, $format = 'object'){
    global $wpdb;

    $res = $wpdb->get_results(
        $wpdb->prepare("select * from $wpdb->posts WHERE ID = %d", (int)$id)
    );
    $res = $res && isset($res[0]) ? $res[0] : array();
    return $format == 'object' ? $res : json_encode($res);
}

/**
 * Save map settings as custom post (post_type = mapsvg)
 */
function mapsvg_save( $data ){
    global $wpdb;

    // Check user rights
    if(!current_user_can('edit_posts'))
        die();

    $data_js   = stripslashes($data['mapsvg_data']);

    // Apache mod_sec module often blocks requests that contain "MySQL" words such as:
    // select / table / database / varchar / etc.
    // These words are encoded before sending to the server. Here we decode them back:
    $data_js = str_replace("!mapsvg-encoded-slct", "select",   $data_js);
    $data_js = str_replace("!mapsvg-encoded-tbl",  "table",    $data_js);
    $data_js = str_replace("!mapsvg-encoded-db",   "database", $data_js);
    $data_js = str_replace("!mapsvg-encoded-vc",   "varchar", $data_js);

    // Prepare the post data that will be saved to the database
    $postarr = array(
    	'post_type'    => 'mapsvg',
    	'post_status'  => 'publish'
    );

	// Set map title
    if(isset($data['title'])){
        $postarr['post_title'] = strip_tags(stripslashes($data['title']));
    }else{
        $postarr['post_title'] = "New Map";
    }

	// Map settings are stored in the post_content field as a JSON string
    $postarr['post_content'] = $data_js;


    // If map_id is set and it's not a "new" map then update the map:
    if(isset($data['map_id']) && $data['map_id']!='new'){

        $postarr['ID'] = (int)$data['map_id'];

        // Load old 2.x interface if the map version < 3.0
        $mapsvg_version = get_post_meta($postarr['ID'], 'mapsvg_version', true);
        if(version_compare($mapsvg_version, '3.0.0', '<')){
            return mapsvg_save_2($data);
        }

        // Save settings to the database
        $wpdb->query(
            $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID = %d", array($postarr['post_title'], $postarr['post_content'], $postarr['ID']))
        );
        update_post_meta($postarr['ID'], 'mapsvg_version', MAPSVG_VERSION);

        // This is in the development yet:
        if(isset($data['mapsvg_manual_regions'])){
            $manual_regions = (bool)$data['mapsvg_manual_regions'];
            if(!metadata_exists('post', $postarr['ID'], 'mapsvg_manual_regions')){
                $res = add_post_meta($postarr['ID'], 'mapsvg_manual_regions', $manual_regions);
            }else{
                $res = update_post_meta($postarr['ID'], 'mapsvg_manual_regions', $manual_regions);
            }
        }

        // Get map ID
	    $post_id = $postarr['ID'];

        // SVG file could be changed, may be reload the list of regions from SVG file:
        mapsvg_set_regions_table($post_id, $data['source'], (isset($data['region_prefix'])?$data['region_prefix']:null));

        // Save the file path
        update_post_meta($post_id, 'mapsvg_svg_file', $data['source']);

	    // Save the regions prefix
        if(isset($data['region_prefix'])){
            update_post_meta($post_id, 'mapsvg_region_prefix', $data['region_prefix']);
        }

    }else{
        // Parameter map_id is not set, create a new map:
        $post_id = wp_insert_post( $postarr );
        $wpdb->query(
            $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID = %d", array($postarr['post_title'], $postarr['post_content'], $post_id))
        );
        add_post_meta($post_id, 'mapsvg_version', MAPSVG_VERSION);
    }

    return $post_id;
}

/**
 * Get MySQL table name by table type and map_id
 *
 * @param $map_id
 * ID of the map
 *
 * @param $table
 * Table type: "database" or "regions"
 *
 * @return string
 * Full table name
 */
function mapsvg_table_name($map_id, $table){
    global $wpdb;
    return $wpdb->prefix.MAPSVG_TABLE_NAME.'_'.$table.'_'.$map_id;
}

/**
 * Get schema of Regions or Database tables created in MapSVG
 *
 * @param $map_id
 * ID of the map
 *
 * @param $table
 * Table type: "database" or "regions"
 *
 * @return string
 * JSON string, table schema
 */
function mapsvg_get_schema($map_id, $table){
    global $wpdb;
    $table_name = mapsvg_table_name($map_id, $table);
    return $wpdb->get_var("SELECT fields FROM ".$wpdb->prefix."mapsvg_schema WHERE table_name LIKE '%mapsvg_".$table."_".$map_id."'");
}
function _mapsvg_get_schema(){
    echo mapsvg_get_schema((int)$_GET['map_id'], $_GET['table']);
    die();
}
add_action('wp_ajax_mapsvg_get_schema', '_mapsvg_get_schema');
add_action('wp_ajax_nopriv_mapsvg_get_schema', '_mapsvg_get_schema');


/**
 * @param $map_id
 * ID of the map
 * @param $_table
 * Table type: database / regions
 * @param $schema
 * Array, schema of a table
 * @param bool $skip_db_update
 * If true, table will not be altered (only the schema is saved)
 */
function _mapsvg_save_schema($map_id, $_table, $schema, $skip_db_update = false){
    global $wpdb;

    $table_type  = $_table;
    $table       = mapsvg_table_name($map_id, $_table);
    $schema_json = json_encode($schema);
    $prev_schema = json_decode(mapsvg_get_schema($map_id, $_table), true);
    $schema_id = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."mapsvg_schema WHERE table_name='".$table."'");

    if($schema_id)
        $wpdb->update($wpdb->prefix."mapsvg_schema", array('table_name'=>$table,'fields'=>$schema_json), array('id'=>$schema_id));
    else
        $wpdb->insert($wpdb->prefix."mapsvg_schema", array('table_name'=>$table,'fields'=>$schema_json));

    // Set connections WP Posts > Maps
    foreach($schema as $s){
        if($s['type']=='post'){
            $option_name = 'mapsvg_to_posts';
            $connections = (array)json_decode(get_site_option($option_name,'[]'));
            if($s['add_fields']=='true'){
                if(!$connections[$s['post_type']])
                    $connections[$s['post_type']] = array();
                if(!in_array($map_id,$connections[$s['post_type']])){
                    $connections[$s['post_type']][] = $map_id;
                    $connections = json_encode($connections);
                    update_site_option($option_name, $connections);
                }
            }else{
                if($connections[$s['post_type']]){
                    $post_connections = $connections[$s['post_type']];
                    $post_connections = array_diff( $post_connections, array($map_id) );
                    $connections[$s['post_type']] = $post_connections;
                    $connections = json_encode($connections);
                    update_site_option($option_name, $connections);
                }
            }
        }
    }

    // create / update mysql table
    if(!$skip_db_update)
        mapsvg_set_db($map_id, $table_type, $schema, $prev_schema);
}


/**
 * Ajax function that saves schema of a table (regions/database) created in MapSVG
 */
function mapsvg_save_schema(){

	mapsvg_check_nonce();

	$schema_json = stripslashes($_POST['schema']);

    $schema_json = str_replace("!mapsvg-encoded-slct", "select",   $schema_json);
    $schema_json = str_replace("!mapsvg-encoded-tbl",  "table",    $schema_json);
    $schema_json = str_replace("!mapsvg-encoded-db",   "database", $schema_json);
    $schema_json = str_replace("!mapsvg-encoded-vc",   "varchar", $schema_json);


    $schema      = json_decode($schema_json, true);
    _mapsvg_save_schema((int)$_POST['map_id'], $_POST['table'], $schema);
    die();
}
add_action('wp_ajax_mapsvg_save_schema', 'mapsvg_save_schema');


/**
 * Delete a map
 *
 * @param $id
 * ID of a map
 *
 * @param $ajax
 * Ajax request or not, if not then function redirects to another page at the end
 */
function mapsvg_delete($id, $ajax){
    global $wpdb;

    // Check nonce
    check_ajax_referer( 'ajax_mapsvg_delete-'.$id);
    // Check user rights
    if(!current_user_can('delete_posts'))
        die();


    // 2.x backward compatibility
    $mapsvg_version = get_post_meta($id, 'mapsvg_version', true);
    if(version_compare($mapsvg_version, '3.0.0', '<')){
        return mapsvg_delete_2($id, $ajax);
    }

    wp_delete_post($id);

    $mapsvg_table = $wpdb->get_var("SHOW TABLES LIKE '".mapsvg_table_name($id, 'database')."'");

    if($mapsvg_table){
        $wpdb->query("DROP TABLE ".$mapsvg_table);
        $wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id' => $id));
        $wpdb->query("DELETE FROM ".$wpdb->prefix."mapsvg_schema WHERE table_name='".$mapsvg_table."'");
    }

    $mapsvg_table = $wpdb->get_var("SHOW TABLES LIKE '".mapsvg_table_name($id, 'regions')."'");

    if($mapsvg_table){
        $wpdb->query("DROP TABLE ".$mapsvg_table);
        $wpdb->query("DELETE FROM ".$wpdb->prefix."mapsvg_schema WHERE table_name='".$mapsvg_table."'");
    }

    if(!$ajax)
        wp_redirect(admin_url('plugins.php?page=mapsvg-config'));
}

/**
 * Make a copy of a map
 *
 * @param $id
 * Map ID
 * @param $new_title
 * New map title
 *
 * @return mixed
 */
function mapsvg_copy($id, $new_title){
    global $wpdb;

    // Check nonce
    check_ajax_referer( 'ajax_mapsvg_copy-'.$_POST['id']);
    // Check user rights


    // 2.x backward compatibility
    $mapsvg_version = get_post_meta($id, 'mapsvg_version', true);
    if(version_compare($mapsvg_version, '3.0.0', '<')){
        return mapsvg_copy_2($id, $new_title);
    }

    $post = mapsvg_get_map($id);

    $copy_post = array(
    	'post_type'    => 'mapsvg',
    	'post_status'  => 'publish'
    );

    $new_title = stripslashes(strip_tags($new_title));
    $post_content = $post->post_content;

	$options = json_decode($post_content, true);
	$file    = $options['source'];
	$res     = mapsvg_svg_copy($file);
	if(isset($res['error'])){
		return false;
	} else {
		$options['source'] = $res['filepath'];
		$post_content = json_encode($options);
	}

    $new_id = wp_insert_post($copy_post);

	$post_content = str_replace('#mapsvg-map-'. $id, '#mapsvg-map-'.$new_id, $post_content);
	$post_content = str_replace('{{mapsvg_gallery '. $id, '{{mapsvg_gallery '.$new_id, $post_content);


    $wpdb->query(
        $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID=%d", array($new_title, $post_content, $new_id))
    );

    $mapsvg_version              = get_post_meta($id, 'mapsvg_version', true);
    $mapsvg_database_schema_json = mapsvg_get_schema($id, 'database');
    $mapsvg_regions_schema_json  = mapsvg_get_schema($id, 'regions');
//    $mapsvg_css                  = get_post_meta($id, 'mapsvg_css', true);
    $mapsvg_table_db             = $wpdb->get_var("SHOW TABLES LIKE '".mapsvg_table_name($id, 'database')."'");
    $mapsvg_table_regions        = $wpdb->get_var("SHOW TABLES LIKE '".mapsvg_table_name($id, 'regions')."'");


    add_post_meta($new_id, 'mapsvg_version', $mapsvg_version);
//    add_post_meta($new_id, 'mapsvg_css', $mapsvg_css);

    if($mapsvg_table_db){
        $wpdb->query("CREATE TABLE ".mapsvg_table_name($new_id, 'database')." LIKE ".mapsvg_table_name($id, 'database'));
        $wpdb->query("INSERT ".mapsvg_table_name($new_id, 'database')." SELECT * FROM ".mapsvg_table_name($id, 'database'));
        $wpdb->query("INSERT INTO ".$wpdb->prefix."mapsvg_r2d  (map_id,region_id,object_id) SELECT '".$new_id."', _r2d.region_id, _r2d.object_id FROM ".$wpdb->prefix."mapsvg_r2d _r2d WHERE _r2d.map_id=".$id);
    }
    if($mapsvg_table_regions){
        $wpdb->query("CREATE TABLE ".mapsvg_table_name($new_id, 'regions')." LIKE ".mapsvg_table_name($id, 'regions'));
        $wpdb->query("INSERT ".mapsvg_table_name($new_id, 'regions')." SELECT * FROM ".mapsvg_table_name($id, 'regions'));
    }

    if($mapsvg_database_schema_json){
        $table       = mapsvg_table_name($new_id, 'database');
        $wpdb->insert($wpdb->prefix."mapsvg_schema", array('table_name'=>$table,'fields'=>$mapsvg_database_schema_json));
    }
    if($mapsvg_regions_schema_json){
        $table       = mapsvg_table_name($new_id, 'regions');
        $wpdb->insert($wpdb->prefix."mapsvg_schema", array('table_name'=>$table,'fields'=>$mapsvg_regions_schema_json));
    }

   return $new_id;
}


/**
 * Upgrade map settings (the function was used in older MapSVG versions, can't remove it because of that)
 */
function ajax_mapsvg_update() {

	mapsvg_check_nonce();

	if(!empty($_POST['id']) && !empty($_POST['update_to'])){
		$params = array();
		if(isset($_POST['disabledRegions']))
			$params['disabledRegions'] = $_POST['disabledRegions'];
		if(isset($_POST['disabledColor']))
			$params['disabledColor'] = $_POST['disabledColor'];
		echo mapsvg_update_map($_POST['id'], $_POST['update_to'], $params);
	}
	die();
}
add_action('wp_ajax_mapsvg_update', 'ajax_mapsvg_update');


/**
 * Get the list of all created maps
 *
 * @return array
 * List of the maps
 */
function mapsvg_get_maps(){
    $maps = array();
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(MAPSVG_MAPS_DIR)) as $filename)
    {
        if(strpos($filename,'.svg')!==false){
            $path_s = ltrim(str_replace('\\','/',str_replace(MAPSVG_MAPS_DIR,'',$filename)),'/');
            $maps[] = array(
                "url"       => parse_url(MAPSVG_MAPS_URL . $path_s, PHP_URL_PATH),
                "path_fake" => $path_s,
                "path_true" => $path_s,
                "package"   => 'default'
            );
        }
    }
    if(is_dir(MAPSVG_MAPS_UPLOADS_DIR)){
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(MAPSVG_MAPS_UPLOADS_DIR)) as $filename)
        {
            if(strpos($filename,'.svg')!==false){
                $path_s = ltrim(str_replace('\\','/',str_replace(MAPSVG_MAPS_UPLOADS_DIR,'',$filename)),'/');

                $maps[] = array(
                    "url"       => parse_url(MAPSVG_MAPS_UPLOADS_URL . $path_s, PHP_URL_PATH),
                    "path_fake" => 'user-uploads/'.$path_s,
                    "path_true" => $path_s,
                    "package"   => 'uploads'
                );
            }
        }
    }

    sort($maps);
    return $maps;
}

/**
 * Create a new map by using a provided SVG file
 *
 * @param $svg_file_url_path
 * File path
 *
 * @return int
 * ID of the new map
 */
function mapsvg_create_new_map($svg_file_url_path){
    global $wpdb;

    $gmap = '';

    $mapsvg_data = array('source'=>$svg_file_url_path, 'svgFileVersion'=>1);

    if (isset($_GET['gmap']) && $_GET['gmap']==true){
        $key = get_option('mapsvg_google_api_key');
	    $mapsvg_data['googleMaps'] = array(
            'on'=>true,
            'apiKey'=> $key,
            'zoom'=> 1,
            'center'=> array(
		    'lat'=> 41.99585227532726, 'lng'=> 10.688006500000029
            )
        );
    }

    $data = array('map_id'=>'new','mapsvg_data'=> json_encode($mapsvg_data));
    $id = mapsvg_save($data);

    $obj = new stdClass();
    $obj->{'1'} = array("label"=>"Enabled","value"=>'1',"color"=>"","disabled"=>false);
    $obj->{'0'} = array("label"=>"Disabled","value"=>'0',"color"=>"","disabled"=>true);

    $regions_schema = array(
        array('type'=>'status',
            'db_type'=>'varchar (255)',
            'label'=> 'Status',
            'name'=> 'status',
            'visible'=>true,
            'options'=>array(
                $obj->{'1'},
                $obj->{'0'}
            ),
            'optionsDict' => $obj
        ),
	    array(
		    'type'=>'text',
		    'db_type'=>'varchar(255)',
		    'name'=>'link',
		    'label'=>'Link',
		    'visible'=>'true'
	    )
    );


    $db_schema = array(
        array(
            'type'=>'text',
            'db_type'=>'varchar(255)',
            'name'=>'title',
            'label'=>'Title',
            'visible'=>'true'
        ),
        array(
            'type'=>'textarea',
            'db_type'=>'text',
            'name'=>'description',
            'label'=>'Description',
            'visible'=>'true',
            'html'=>'true',
            'help'=>'You can use HTML in this field'
        ),
        array(
            'type'=>'location',
            'db_type'=>'text',
            'name'=>'location',
            'label'=>'Location',
            'visible'=>'true'
        ),
        array(
            'type'=>'region',
            'db_type'=>'text',
            'name'=>'regions',
            'label'=>'Regions',
            'visible'=>'true'
        ),
        array(
            'type'=>'image',
            'db_type'=>'text',
            'name'=>'images',
            'label'=>'Images',
            'visible'=>'true'
        ),
	    array(
		    'type'=>'text',
		    'db_type'=>'varchar(255)',
		    'name'=>'link',
		    'label'=>'Link',
		    'visible'=>'true'
	    )
    );

    mapsvg_set_db($id, 'regions', array(), array());
    _mapsvg_save_schema($id, 'regions', $regions_schema);

    mapsvg_set_db($id, 'database', $db_schema, array());
    _mapsvg_save_schema($id, 'database', $db_schema);

    $prefix = '';

    mapsvg_set_regions_table($id, $svg_file_url_path, $prefix);

    $regions_table = mapsvg_table_name($id, 'regions');
    $wpdb->query('UPDATE '.$regions_table.' SET status=1, status_text="Enabled"');
    return $id;
}


/**
 * Render MapSVG settings page in WP Admin
 */
function mapsvg_conf(){

    global $mapsvg_page, $wpdb;

    // Check user rights
    if(!current_user_can('edit_posts'))
        die();


    if(isset($_GET['action']) && $_GET['action']=='download_google_map'){
        mapsvg_download_google_map(); // forces SVG download and dies
        die();
	}

	// check PHP version
	preg_match("#^\d+(\.\d+)*#", PHP_VERSION, $match);
	$php_version = $match[0];
	if(version_compare($php_version, '5.4.0', '<')){
	    $mapsvg_error = 'Your PHP version is '.$php_version.'. MapSVG requires version 5.4.0 or higher.';
    }

	// check MySQL version
//	if(version_compare($wpdb->db_version(), '5.6.0', '<')){
//	    $mapsvg_error = 'Your MySQL version is '.$wpdb->db_version().'. MapSVG requires version 5.6.0 or higher.';
//    }

    $file         = null;
    $map_chosen   = false;
    $svg_file_path = "";
    $svg_file_url_path = "";
    if (isset($_GET['path']) && isset($_GET['package'])){
        if($_GET['package'] == 'default'){
            $svg_file_path     = MAPSVG_MAPS_DIR."/".$_GET['path'];
            $svg_file_url_path = parse_url(MAPSVG_MAPS_URL . $_GET['path'], PHP_URL_PATH);
        }elseif($_GET['package']=='uploads'){
            $svg_file_path     = MAPSVG_MAPS_UPLOADS_DIR."/".$_GET['path'];
            $svg_file_url_path = parse_url(MAPSVG_MAPS_UPLOADS_URL . $_GET['path'], PHP_URL_PATH);
        }
    }

    // If $_GET['map_id'] is set then we should get map settings from the DB
    $map_id = isset($_GET['map_id']) ? $_GET['map_id'] : null;

    // If it's new map - create it & reload the page
    if($map_id == 'new'){
        $map_id = mapsvg_create_new_map($svg_file_url_path);
        wp_redirect(admin_url('?page=mapsvg-config&map_id='.$map_id));
        exit();
    }

    // Load list of available maps
    $maps = mapsvg_get_maps();

    $js_mapsvg_options = "";
    $title = "";

    if($map_id && $map_id!='new'){

        $mapsvg_page = 'edit';

        // 2.x backward compatibility
        $mapsvg_version = get_post_meta($map_id, 'mapsvg_version', true);
        $mapsvg_version = explode('-', $mapsvg_version);
        $mapsvg_version = $mapsvg_version[0];

        if(version_compare($mapsvg_version, '3.0.0', '<')){
            return mapsvg_conf_2();
        }

        mapsvg_maybe_update_the_map($map_id, $mapsvg_version);

        $post = mapsvg_get_map($map_id);

        $js_mapsvg_options = $post->post_content;

        $title = isset($post) && $post->post_title ? $post->post_title : "New map";

        if ($js_mapsvg_options == "" && $svg_file_url_path!="")
            $js_mapsvg_options = json_encode(array('source' => $svg_file_url_path));

        $markerImages = get_marker_images();

	    $options = json_decode($js_mapsvg_options, ARRAY_A);

	    if($options && is_array($options)){
		    $options['data_regions'] = mapsvg_data_get_all(array(
			    'map_id' => $post->ID,
			    'table'  => 'regions',
			    'with_schema' => true,
			    'perpage' => 0,
			    'sortBy' => ( isset($options['menu']) && $options['menu']['source'] == 'regions' ? $options['menu']['sortBy'] : ( isset($options['menu']) && strpos($options['menu']['source'],'geo-cal') !== false ? 'title' : 'id' ) ),
			    'sortDir' => isset($options['menu']) &&  $options['menu']['source'] == 'regions' ? $options['menu']['sortDirection'] : 'asc'
		    ));

		    $perpage = isset($options['database']) && (int)$options['database']['pagination']['on'] ? $options['database']['pagination']['perpage'] : 0;

		    $options['data_db'] = mapsvg_data_get_all(array(
			    'map_id' => $post->ID,
			    'table'  => 'database',
			    'with_schema' => true,
			    'perpage' => $perpage,
			    'sortBy' => isset($options['menu']) && $options['menu']['source'] == 'database' ? $options['menu']['sortBy'] : 'id',
			    'sortDir' => isset($options['menu']) && $options['menu']['source'] == 'database' ? $options['menu']['sortDirection'] : 'desc'
		    ));

		    $js_mapsvg_options = json_encode($options);
	    }


    }else{
        $mapsvg_page = 'index';

        if(isset($_GET['mapsvg_rollback'])){
            rollBack();
        }

        $generated_maps = get_posts(array('numberposts'=>999, 'post_type'=>'mapsvg'));
    }

    $mapsvg_version = MAPSVG_VERSION;
    $fulltext_min_word = $wpdb->get_row("show variables like 'ft_min_word_len'", OBJECT);
    $fulltext_min_word = $fulltext_min_word ? $fulltext_min_word->Value: 0;

    if(isset($post)){
        $svg_file_version = (int)get_post_meta($post->ID, 'mapsvg_svg_file_version', true);
    }

    $mapsvg_google_api_key = get_option('mapsvg_google_api_key');
    $mapsvg_google_geocoding_api_key = get_option('mapsvg_google_geocoding_api_key');

    $template = 'template_'.$mapsvg_page.'.inc';

    $purchase_code = get_option('mapsvg_purchase_code');

    include(MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'header.inc');
    include(MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.$template);

    $post_types = mapsvg_get_post_types();
    include(MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'footer.inc');

    return true;
}

/**
 * Parse an SVG file, get all SVG objects that must be added to the "Regions" table
 * and update "regions" table in MySQL database
 *
 * @param $map_id
 * ID of the map
 *
 * @param $svg_file_path
 * SVG file path
 *
 * @param $prefix
 * If prefix is provided, only SVG objects with the provided prefix get into the "Regions" list
 */
function mapsvg_set_regions_table($map_id, $svg_file_path, $prefix){

    global $wpdb;

    $root = ABSPATH;
    $root = wp_normalize_path($root);

    if(strpos($svg_file_path, basename(WP_CONTENT_DIR))!==false){
	    list($junk,$important_stuff) = explode(basename(WP_CONTENT_DIR),$svg_file_path);
	    $important_stuff = WP_CONTENT_DIR.$important_stuff;
    } else {
	    list($junk,$important_stuff) = explode(basename(MAPSVG_MAPS_UPLOADS_DIR),$svg_file_path);
	    $important_stuff = MAPSVG_MAPS_UPLOADS_DIR.$important_stuff;
    }

    if(file_exists($important_stuff)){
	    $map_svg = simplexml_load_file($important_stuff);
    } else {
        echo 'File does not exists: '.$important_stuff;
        die();
    }

    $allowed_objects = array(null,'path','ellipse','rect','circle','polygon','polyline');
    $namespaces = $map_svg->getDocNamespaces();
    $map_svg->registerXPathNamespace('_ns', $namespaces['']);

    $regions = array();
    $region_ids = array();
    $region_titles = array();
    $regions_assoc = array();

//	$status_field = $wpdb->get_row('SHOW COLUMNS FROM '.mapsvg_table_name($map_id, 'regions').' LIKE \'status\'');
	$db_types = mapsvg_get_db_types($map_id, 'regions');
	$db_types = array_flip($db_types);

	$status_field = isset($db_types['status']);

    while($obj = next($allowed_objects)){

        $nodes = $map_svg->xpath('//_ns:'.$obj);

        // TODO: mistake about defs below?
        // if(empty($defs) && !empty($nodes)){
	    if(!empty($nodes)){

            foreach($nodes as $o){


                if(isset($o['id']) && ! empty($o['id'])){

                    $defs = $map_svg->xpath('//_ns:'.$obj.'[@id="'.$o['id'].'"]/ancestor::_ns:defs');
                    if(!empty($defs)){
                        continue;
                    }

                    // strip prefix
                    if(!$prefix || ($prefix && strpos($o['id'],$prefix)===0)){
                        $rid                 = str_replace($prefix, '', (string)$o['id']);
                        $title               = isset($o['title']) && ! empty($o['title']) ? (string)$o['title'] : '';
                            $regions[]           = "('".esc_sql($rid) ."','".esc_sql($title)."'".($status_field? ",1" : "" ).")";
                        $region_ids[]        = $rid;
                        $region_titles[]     = esc_sql($title);
                        $regions_assoc[$rid] = $title;
                    }
                }
            }
        }
    }


    // TODO: check with prefixes
    $ids = $wpdb->get_results('SELECT id, region_title FROM '.mapsvg_table_name($map_id, 'regions'));
    $r_compare = array();
    $t_compare = array();
    foreach($ids as $id_row){
        $r_compare[] = $id_row->id;
        $t_compare[] = $id_row->region_title;
    }

    $diff = array_diff($r_compare, $region_ids);

    $table = mapsvg_table_name($map_id, 'regions');

    foreach($diff as $id){
        $wpdb->query('DELETE FROM ' . $table . ' WHERE id =\'' .$id.'\'');
    }

    //    if($region_ids != $r_compare) {
    //        $wpdb->get_results('DELETE FROM ' . mapsvg_table_name($map_id, 'regions') . ' WHERE id NOT IN (\'(' . implode('\',\'', $region_ids) . ')\')');
    //    }

    if($region_titles != $t_compare || ($region_ids != $r_compare)){
        if(!empty($regions)){
            // TODO: duplicate key set status as well
            $wpdb->query('INSERT INTO '.mapsvg_table_name($map_id, 'regions').' (id, region_title'.($status_field? ",`".$db_types['status']."`" : "" ).') VALUES '.implode(',',$regions).' ON DUPLICATE KEY UPDATE region_title=VALUES(region_title)');
            if($wpdb->last_error){
                mapsvg_log($wpdb->last_error, 'MySQL', MAPSVG_ERROR);
            }
        }
    }

}

/**
 * Save SVG file edits (the function is used by "Edit SVG file" mode in MapSVG)
 */
function ajax_mapsvg_save_svg(){

    global $wpdb;

	mapsvg_check_nonce();

	$root = ABSPATH;
	$root = wp_normalize_path($root);
	$svg_file_path = stripslashes($_POST['filepath']);
	$body = stripslashes($_POST['body']);


	if(empty($body)){
		echo '{"status": "Error", "error": "The file data didn\'t reach the server. Please increase "post_max_size" value in php.ini."}';
		die();
	}

	// We expect that the file is already in the uploads folder:
    $filename = basename($svg_file_path);
	$filepath = MAPSVG_MAPS_UPLOADS_DIR.DIRECTORY_SEPARATOR.$filename;

	/*
	list($junk,$important_stuff) = explode(basename(MAPSVG_MAPS_UPLOADS_DIR),$svg_file_path);
	$important_stuff = MAPSVG_MAPS_UPLOADS_DIR.$important_stuff;
	$filepath = $important_stuff;
	*/
	
	if(!file_exists($filepath)){
		echo '{"status": "Error", "error": "The target file '.$filepath.' does not exists"}';
		die();
    }

	$f = fopen($filepath, 'w');
	$body = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'."\n".$body;
	$res = fwrite($f,$body);
	fclose($f);

	if($res === false){
		echo '{"status": "Error", "error": "Can\'t write data to the file"}';
		die();
    }

    $auto_update = get_post_meta($_POST['map_id'], 'mapsvg_manual_regions', true);

    mapsvg_set_regions_table($_POST['map_id'], $_POST['filepath'], '');

    $map = mapsvg_get_map($_POST['map_id']);
	$mapsvg_version = get_post_meta((int)$_POST['map_id'], 'mapsvg_version');


	$parts1 = json_decode($map->post_content);

	if(!$parts1){
		if(strpos($map->post_content, 'svgFileVersion:')){
			$parts1    = explode('svgFileVersion:', $map->post_content, 2);
			$parts2    = explode(',', $parts1[1], 2);
			$parts2[0] = (int)$parts2[0] + 1;
			$parts1[1] = implode(',', $parts2);
			$parts1    = implode('svgFileVersion:', $parts1);
		}else{
			$parts1    = substr_replace($map->post_content, 'svgFileVersion:2,', 1, 0);
		}
	} else {
	    if(isset($parts1->svgFileVersion)){
		    $parts1->svgFileVersion++;
        } else {
		    $parts1->svgFileVersion = 2;
        }
		$parts1 = json_encode($parts1);
	}

    $data = array(
      'ID'=>$map->ID,
      'post_content'=>$parts1
    );

	$wpdb->query(
		$wpdb->prepare("update $wpdb->posts set post_content=%s WHERE ID = %d", array($parts1, $map->ID))
	);

	echo '{"status": "OK"}';

    die();
}
add_action('wp_ajax_mapsvg_save_svg', 'ajax_mapsvg_save_svg');


/**
 * Make a copy of one of the included SVG files, which are "read-only", put the copy to "uploads" folder
 * so users could edit the SVG file.
 */
function mapsvg_svg_copy($file){

	$filename = basename($file);

	$actual_name = pathinfo($filename,PATHINFO_FILENAME);
	$original_name = $actual_name;
	$extension = pathinfo($filename, PATHINFO_EXTENSION);

	$i = 1;
	$final_name = $actual_name.".".$extension;
	while(file_exists(MAPSVG_MAPS_UPLOADS_DIR."/".$actual_name.".".$extension))
	{
		$actual_name = (string)$original_name.'_'.$i;
		$i++;
	}

	$newfile =  MAPSVG_MAPS_UPLOADS_DIR."/".$actual_name.".".$extension;

	$root = ABSPATH;
	$root = wp_normalize_path($root);
	list($junk,$important_stuff) = explode(basename(WP_CONTENT_DIR), $file);
	$file = WP_CONTENT_DIR.$important_stuff;

	$error = mapsvg_check_upload_dir();

	if(!$error){
		if(!copy($file, $newfile)){
			$error = "Failed to copy the file";
		}
	}

	if(!$error){
		$newfile = MAPSVG_MAPS_UPLOADS_URL.$actual_name.".".$extension;
		return array('filepath' => $newfile);
	}else{
		return array('error'=>$error);
	}
}


/**
 * Make a copy of one of the included SVG files, which are "read-only", put the copy to "uploads" folder
 * so users could edit the SVG file.
 */
function ajax_mapsvg_svg_copy(){
	mapsvg_check_nonce();
	echo json_encode(mapsvg_svg_copy($_POST['filepath']));
    die();
}
add_action('wp_ajax_mapsvg_svg_copy', 'ajax_mapsvg_svg_copy');

/**
 * Get the list all marker images paths from /mapsvg/markers folder
 * @return array
 */
function get_marker_images(){

    $img_files = @scandir(MAPSVG_PINS_DIR);
    if($img_files){
        array_shift($img_files);
        array_shift($img_files);
    }
    $safeMarkerImagesURL = safeURL(MAPSVG_PINS_URL);
    $markerImages = array();
    $allowed =  array('gif','png' ,'jpg','jpeg','svg');
    foreach($img_files as $p){
        $ext = pathinfo($p, PATHINFO_EXTENSION);
        if(in_array($ext,$allowed) )
            $markerImages[] = array("url"=>$safeMarkerImagesURL.$p, "file"=>$p, "folder"=>'default');
    }

	$img_files2 = @scandir(MAPSVG_MAPS_UPLOADS_DIR . "/markers");
	if($img_files2){
		array_shift($img_files2);
		array_shift($img_files2);
		$safeMarkerImagesURL2 = safeURL(MAPSVG_MAPS_UPLOADS_URL .'markers/');
		foreach($img_files2 as $p2){
			$ext = pathinfo($p2, PATHINFO_EXTENSION);
			if(in_array($ext, $allowed) )
				$markerImages[] = array("url"=>$safeMarkerImagesURL2.$p2, "file"=>$p2, "folder"=>'uploads/markers');
		}

	}


    return $markerImages;
}

/**
 * Format fields of Regions or Database before saving them to MySQL
 *
 * @param $map_id
 * ID of the map
 * @param $table
 * Table type: regions / database
 *
 * @param $data
 * Data that needs to be encoded
 *
 * @return array
 */
function mapsvg_encode_data($map_id, $table, $data, $convert_latlng_to_address = false){

    global $db_schema, $db_types, $db_options, $db_multi, $wpdb;

    if(!$db_schema){
        $db_schema = mapsvg_get_schema($map_id, $table);
        $db_schema = json_decode($db_schema, true);
    }

    if(!$db_types){
        $db_options = array();
        $db_multi = array();
        $db_types = array('id'=>'id');
        foreach($db_schema as $s){
            $db_types[$s['name']] = $s['type'];
            if($s['name'] === 'post_id'){
	            $db_types['post'] = 'post';
            }
            if(isset($s['options']))
                $db_options[$s['name']] = $s['optionsDict'];
            if(isset($s['multiselect']) && $s['multiselect'] === true)
                $db_multi[$s['name']] = true;
        }
    }

    $_data = array();

    foreach($data as $key=>$value){
        if(isset($db_types[$key])) switch ($db_types[$key]){
            case 'region':
//                $titles = $wpdb->get_results('SELECT id, region_title as title FROM '.mapsvg_table_name($map_id, 'regions').' WHERE id IN (\''.implode('\',\'', $data[$key]).'\')');
//                echo 'SELECT id, region_title as title FROM '.mapsvg_table_name($map_id, 'regions').' WHERE id IN (\''.implode('\',\'', $data[$key]).'\')';
                if(!empty($data[$key]) && is_array($data[$key])){
	                $_data[$key] = json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                } else {
	                $_data[$key] = '';
                }
                break;
            case 'status':
                $key_text = $key.'_text';
                if(isset($db_options[$key][$value])){
                    $_data[$key] = $value;
                    $_data[$key_text] = $db_options[$key][$value]['label'];
                }else{
                    $_data[$key] = '';
                    $_data[$key_text] = '';
                }
                break;
            case 'select':
            case 'radio':
                $key_text = $key.'_text';

                if(isset($db_multi[$key]) && $db_multi[$key]) {
                    $_data[$key] = json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                }else{
                    if(isset($db_options[$key][$value])){
                        $_data[$key] = $value;
                        $_data[$key_text] = $db_options[$key][$value];
                    }else {
                        $options = array_flip($db_options[$key]);
                        if(isset($options[$value])){
                            $_data[$key] = $options[$value];
                            $_data[$key_text] = $value;
                        }else {
                            $_data[$key] = '';
                            $_data[$key_text] = '';
                        }
                    }
                }
                break;
            case 'checkbox':
                $_data[$key] = (int)($data[$key] === true || $data[$key] === 'true' || $data[$key] === '1' || $data[$key] === 1);
                break;
            case 'image':
            case 'marker':
                if(is_array($data[$key])){
                    $_data[$key] = json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                }else{
                    $_data[$key] = $data[$key];
                }
                break;
            case 'location':
                if(!empty($data[$key])){
                    $location = array();

                    if(is_array($data[$key])){
                        $location = $data[$key];
                    } else {
                        $location = json_decode($data[$key]);
                    }

	                if((isset($location['lat']) && isset($location['lng'])) && (!empty($location['lat']) && !empty($location['lng']))) {
		                $_data['location_lat'] = $location['lat'];
		                $_data['location_lng'] = $location['lng'];
	                } else if((isset($location['x']) && isset($location['y'])) && (!empty($location['x']) && !empty($location['y']))){
		                $_data['location_x'] = $location['x'];
		                $_data['location_y'] = $location['y'];
	                }
	                if(isset($location['address'])){
		                $_data['location_address'] = isset($location['address']) ? json_encode($location['address'], JSON_UNESCAPED_UNICODE) : '';
                    }

	                $_data['location_img'] = isset($location['img']) ? $location['img'] : '';

                    if(!empty($location)){

                        $addressYesCoordsNo = (isset($location['address']) && !empty($location['address']) && is_string($location['address']))
                            && (( !isset($location['lat']) || !isset($location['lng'])) || (empty($location['lat']) || empty($location['lng'])));
                        $addressNoCoordsYes = (!isset($location['address']) || empty($location['address']))
                            && (( isset($location['lat']) && isset($location['lng'])) && (!empty($location['lat']) && !empty($location['lng'])));

                        if($addressYesCoordsNo || $addressNoCoordsYes){



                            if($addressNoCoordsYes){
                                $response = mapsvg_geocoding($location['lat'].','.$location['lng'], true, $convert_latlng_to_address);
                            } elseif($addressYesCoordsNo){
                                $response = mapsvg_geocoding($location['address']);
                            }

	                        if($response && isset($response['status'])){

                                switch($response['status']){
                                    case 'OK':
                                        $address = array();
                                        if($addressNoCoordsYes && $convert_latlng_to_address){
	                                        $result = $response['results'][1];
                                        } else {
	                                        $result = $response['results'][0];
                                        }

                                        if($addressYesCoordsNo){
                                            $_data['location_lat'] = $result['geometry']['location']['lat'];
                                            $_data['location_lng'] = $result['geometry']['location']['lng'];
                                        } else {
                                            $_data['location_lat'] = $location['lat'];
                                            $_data['location_lng'] = $location['lng'];
                                        }
                                        $address = array();
                                        $address['formatted'] = $result['formatted_address'];
                                        foreach($result['address_components'] as $addr_item){
                                            $type = $addr_item['types'][0];
                                            $address[$type] = $addr_item['long_name'];
                                            if($addr_item['short_name'] != $addr_item['long_name']){
                                                $address[$type.'_short'] = $addr_item['short_name'];
                                            }
                                        }

                                        $_data['location_address'] = json_encode($address, JSON_UNESCAPED_UNICODE);

                                        break;
                                    case 'ZERO_RESULTS':
                                        break;
                                    case 'OVER_DAILY_LIMIT':
                                        // TODO notify user that:
                                        // The API key is missing or invalid.
                                        // Billing has not been enabled on your account.
                                        // A self-imposed usage cap has been exceeded.
                                        // The provided method of payment is no longer valid (for example, a credit card has expired).
                                        //See the Maps FAQ to learn how to fix this.
                                        break;
                                    case 'OVER_QUERY_LIMIT':
                                        // TODO
                                        // Tell user to retry tomorrow (notify that some locations where not converted)
                                        break;
                                    case 'REQUEST_DENIED':
                                        // TODO add a field location_error?
                                        // To mark which locations can still be converted
                                        break;
                                    case 'INVALID_REQUEST':
                                        // TODO add a field location_error?
                                        // To mark which locations can still be converted
                                        break;
                                    case 'UNKNOWN_ERROR':
                                        // TODO add a field location_error?
                                        // To mark which locations can still be converted
                                        break;
                                    case 'CONNECTION_ERROR':
                                        // TODO add a field location_error?
                                        // Don't try to import more locations
                                        break;
                                    case 'NO_API_KEY':
                                        // TODO add a field location_error?
                                        // Don't try to import more locations
                                        break;
                                    default: null;
                                    break;
                                }
                            }
                        }

                    }
                } else {
	                $_data['location_address'] = '';
	                $_data['location_lat'] = '';
	                $_data['location_lng'] = '';
	                $_data['location_x'] = '';
	                $_data['location_y'] = '';
	                $_data['location_img'] = '';
                }

                break;
            case "post":
                if($key === 'post'){
                    if(!is_numeric($value) && strlen($value)>0){
	                    $post = get_page_by_path($value, OBJECT);
	                    $_data['post_id'] = $post->ID;
                    } else {
	                    $_data['post_id'] = $value;
                    }
                } else {
	                $_data[$key] = $value;
                }
                break;
            default:
                $_data[$key] = $value;
                break;
        }
    }

    return $_data;
}

/**
 * Format regions/database fields after loading them from the MySQL database
 *
 * @param $db_types
 * Field types
 *
 * @param $data
 * Unforatted rows from MySQL table
 *
 * @return array
 * Formatted rows
 */
function mapsvg_decode_data($db_types, $data){

	$data_formatted = array();

	$data_formatted['id'] = $data['id'];
	if(isset($data['title'])){
		$data_formatted['title'] = $data['title'];
	}
	if(isset($data['id_no_spaces'])){
		$data_formatted['id_no_spaces'] = $data['id_no_spaces'];
	}

	foreach ($db_types as $field_name => $field_type){
		switch ($field_type) {
//    foreach($data as $key=>$value){
//        if (isset($db_types[$key])) switch ($db_types[$key]){
			case 'status':
				$data_formatted[$field_name] = $data[$field_name];
				if(!empty($data[$field_name.'_text'])){
					$data_formatted[$field_name.'_text'] = $data[$field_name.'_text'];
				}
				break;
			case 'radio':
			case 'select':
                if(strpos($data[$field_name], '[{')===0){
                    $data_formatted[$field_name] = json_decode(stripslashes($data[$field_name]));
                } else {
                    $data_formatted[$field_name] = $data[$field_name];
                }
				if(!empty($data[$field_name.'_text'])){
					$data_formatted[$field_name.'_text'] = $data[$field_name.'_text'];
				}
				break;
			case 'region':
				if(!empty($data[$field_name])) {
					$data_formatted[$field_name] = json_decode(stripslashes($data[$field_name]));
				}
				break;
			case 'post':
				if(!empty($data['post_id'])){
					$data_formatted['post_id'] = (int)$data['post_id'];
					$data_formatted['post'] = get_post($data[$field_name]);
					if($data_formatted['post']){
						$data_formatted['post']->post_content = wpautop($data_formatted['post']->post_content);
						$data_formatted['post']->url = get_permalink($data_formatted['post']);
						if (function_exists('get_fields') ) {
							$data_formatted['post']->acf = get_fields($data['post_id']);
						}
                    }
				}
				break;
			case 'checkbox':
				$data_formatted[$field_name] = (bool)$data[$field_name];
				break;
			case 'image':
				$data_formatted[$field_name] = json_decode(stripslashes($data[$field_name]));
				break;
			case 'marker':
				$data_formatted[$field_name] = json_decode(stripslashes($data[$field_name]));
				break;
			case 'location':
				if(($data['location_lat'] && $data['location_lng']) || ($data['location_x'] && $data['location_y'])){
					$data_formatted[$field_name] = array(
						'address' => isset($data['location_address']) ? json_decode($data['location_address']) : '',
						'lat'     => isset($data['location_lat'])     ? $data['location_lat'] : '',
						'lng'     => isset($data['location_lng'])     ? $data['location_lng'] : '',
						'img'     => isset($data['location_img'])     ? $data['location_img'] : '',
						'x'       => isset($data['location_x'])       ? $data['location_x'] : '',
						'y'       => isset($data['location_y'])       ? $data['location_y'] : ''
					);
				} else {
					$data_formatted[$field_name] = '';
				}
				break;
			default:
				$data_formatted[$field_name] = $data[$field_name];
		}
	}

	return $data_formatted;
}


/**
 * Get all fields types from regions / database table schema
 *
 * @param $db_schema
 * Table schema
 *
 * @return array|bool
 * List of field types
 */
function mapsvg_get_db_types_from_schema($db_schema){
    if(empty($db_schema)){
        return false;
    }
    if(is_string($db_schema)){
        $db_schema = json_decode($db_schema);
    }
    $db_types = array();
    foreach($db_schema as $s){
        $db_types[$s->name] = $s->type;
    }
    return $db_types;
}
function mapsvg_get_db_types($map_id, $table){
    $db_schema = mapsvg_get_schema($map_id, $table);
    return mapsvg_get_db_types_from_schema($db_schema);
}


/**
 * (Ajax) Create new object (regions / database)
 */
function mapsvg_data_create(){
    global $wpdb;

	mapsvg_check_nonce();

	$data   = $_POST['data'] ;//array_intersect_key($_POST['data'], array('map_id','region_id','marker_id','lat','lng','post_id','params'));
    $data = stripslashes_deep($data);
    if(is_string($data)){
        $data = json_decode($data, true);
    }
    $table  = $_POST['table'];

    $_data = mapsvg_encode_data($_POST['map_id'], $table, $data);
    $wpdb->insert(mapsvg_table_name($_POST['map_id'], $table), $_data);

    // Add regions-to-dbObject relations
    $object_id = $wpdb->insert_id;

    if($object_id && isset($data['regions']) && is_array($data['regions'])){
        $regions = $data['regions'];
        $wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id' => $_POST['map_id'], 'object_id'=>$object_id));
        foreach($regions as $region){
            $wpdb->insert($wpdb->prefix.'mapsvg_r2d', array('map_id'    => $_POST['map_id'],
                                                            'region_id' => $region['id'],
                                                            'object_id' => $object_id));
        }
    }
    $data['id'] = $object_id;

    if($wpdb->last_error){
        fb($wpdb->last_error, MAPSVG_ERROR);
    }

    echo json_encode($data);
    die();
}
add_action('wp_ajax_mapsvg_data_create', 'mapsvg_data_create');

/**
 * (Ajax) Update object (regions / database)
 */
function mapsvg_data_update(){
	global $wpdb;

	mapsvg_check_nonce();

	$data  = stripslashes_deep($_POST['data']); //array_intersect_key($_POST['data'], array('map_id','region_id','marker_id','lat','lng','post_id','params'));
	if(is_string($data)){
		$data = json_decode($data, true);
	}
	$table = $_POST['table'];

	$_data = mapsvg_encode_data($_POST['map_id'], $table, $data);

	$data = array();
	$data_id = false;

	if(isset($_data['id'])){
		$id = $_data['id'];
		unset($_data['id']);
		if(is_array($id)){
			foreach($id as $key=>$val){
				$id[$key] = esc_sql($val);
			}
		}else{
			$id = esc_sql($id);
		}
		if(!is_array($id)){
			$data_id = $wpdb->get_var("SELECT id FROM ".mapsvg_table_name($_POST['map_id'], $table)." WHERE id='".esc_sql($id)."'");
		}else{
			$data_id = true;
		}
	}

	if($data_id){

		$set = array();
		foreach($_data as $key=>$value){
			$set[] = '`'.esc_sql($key).'` = \''.esc_sql($value).'\'';
		}

		if(!is_array($id)){
			$id = array($id);
		}

		$wpdb->query('UPDATE '.mapsvg_table_name($_POST['map_id'], $table).' SET '.implode(',',$set).' WHERE id IN (\''.implode('\',\'',$id).'\')');
        mapsvg_log('Updating a row in the "'.$table.'" table: UPDATE '.mapsvg_table_name($_POST['map_id'], $table).' SET '.implode(',',$set).' WHERE id IN (\''.implode('\',\'',$id).'\')', 'MySQL');
	}
	else{
		$wpdb->insert(mapsvg_table_name($_POST['map_id'], $table), mapsvg_encode_data($_POST['map_id'], $table, $data));
	}


	if($table == 'database' && $data_id && isset($_data['regions'])){
		$regions = 	json_decode($_data['regions']);
		$wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id' => $_POST['map_id'], 'object_id'=>$data_id));
		if(!empty($regions)){
			foreach($regions as $region){
				$wpdb->insert($wpdb->prefix.'mapsvg_r2d', array(
					'map_id'    => $_POST['map_id'],
					'region_id' => $region->id,
					'object_id' => $data_id));
			}
        }
	}

	echo '{"status": "OK"}';

	die();
}
add_action('wp_ajax_mapsvg_data_update', 'mapsvg_data_update');

/**
 * (Ajax) Delete object (regions / database)
 * @param $id
 * Object ID
 */
function mapsvg_data_delete($id){
	global $wpdb;

	mapsvg_check_nonce();

	$table = $_POST['table'];
	$data = stripslashes_deep($_POST['data']);
	$wpdb->delete(mapsvg_table_name((int)$_POST['map_id'], $table), array('id'=>$data['id']));
	$wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id'=>$_POST['map_id'], 'object_id'=>$data['id']));
	echo '{"status": "OK"}';
	die();
}
add_action('wp_ajax_mapsvg_data_delete', 'mapsvg_data_delete');

/**
 * Import CSV data to regions / database
 */
function mapsvg_data_import(){
    global $wpdb;

	mapsvg_check_nonce();

	$data   = $_POST['data'];
    $data   = stripslashes_deep($data);
    $data   = json_decode($data, true);
    $table_type  = $_POST['table'];
    $_data  = array();
    $map_id = (int)$_POST['map_id'];
    $convert_latlng_to_address = $_POST['convertLatlngToAddress'] == 'true' ? true : false;
    $table = mapsvg_table_name($map_id, $table_type);
    $r2d = array();

    foreach($data as $index => $object){
        $_data[$index] = mapsvg_encode_data($map_id, $table_type, $object, $convert_latlng_to_address);
    }

    $values = array();

    $keys = array_keys($_data[0]);
    $placeholders = array_map(function($key){ return '%s'; }, $keys);

	foreach ( $_data as $object) {
		$values2 = array();
		foreach ( $keys as $key) {
			$values2[] = isset($object[$key]) ? $object[$key] : '';
		}
		$t = $wpdb->prepare( "(".implode(',',$placeholders).")",  $values2 );
		$values[] = $t;
	}

    $query = "INSERT INTO ".$table." (`".implode('`,`', $keys)."`) VALUES ";
    $query .= implode( ", ", $values ).' ON DUPLICATE KEY UPDATE ';
    $k = array();
    foreach($keys as $key){
        $k[] .= '`'.$key.'`=VALUES(`'.$key.'`)';
    }
    $query .= implode(', ', $k);
    $wpdb->query($query);

    if($wpdb->last_error){
        mapsvg_log($wpdb->last_error, 'MySQL',MAPSVG_ERROR);
        die();
    }

    if($table_type=='database' && isset($_data[0]['regions'])){
        $wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id' => $_POST['map_id']));
        $objects = $wpdb->get_results('SELECT id, regions FROM '.$table);
        $regions_sql_values = array();
        foreach($objects as $object){
            $regions = json_decode($object->regions);
            foreach($regions as $r){
                $regions_sql_values[] = "(".$map_id.",'".$object->id."','".$r->id."')";
            }
        }
        if(!empty($regions_sql_values)){
            $query2 = "INSERT INTO ".$wpdb->prefix.'mapsvg_r2d'." (map_id, object_id, region_id) VALUES ";
            $query2 .= implode( ", ", $regions_sql_values );
            $wpdb->query($query2);
        }
    }

    if($wpdb->last_error){
        mapsvg_log($wpdb->last_error,'MySQL', MAPSVG_ERROR);
        die();
    }

    echo json_encode($data);
    die();
}
add_action('wp_ajax_mapsvg_data_import', 'mapsvg_data_import');

/**
 * (Ajax) Clear regions / database table
 */
function mapsvg_data_clear(){
    global $wpdb;

	mapsvg_check_nonce();

    $table  = $_POST['table'];
    $map_id = $_POST['map_id'];
    $wpdb->query("DELETE FROM ".mapsvg_table_name($map_id, $table));
    $wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id' => $map_id));
	echo '{"status": "OK"}';
	die();
}
add_action('wp_ajax_mapsvg_data_clear', 'mapsvg_data_clear');


/**
 * [Deprecated?]
 *
 * (Ajax) Get object from regions / database table
 *
 * @param $id
 * Object ID
 */
function mapsvg_data_get($id){
    global $wpdb;

    $id = $_POST['data']['id'];
    $table = $_POST['table'];

    $data = $wpdb->get_row('SELECT * FROM '.mapsvg_table_name($_POST['map_id'], $table).' WHERE id='.$id);
    if($db_types = mapsvg_get_db_types($_POST['map_id'], $table))
        $data = mapsvg_decode_data($_POST['map_id'], $table, $data);

    echo json_encode($data);
    die();
}
add_action('wp_ajax_mapsvg_data_get', 'mapsvg_data_get');


/**
 * Get all objects from regions / database
 * With filters, text search, sort by, sort dir parameters passed in $_GET
 */
function mapsvg_data_get_all($query){
    global $wpdb, $db_schema;

    $map_id = (int)$query['map_id'];
    $table  = $query['table'];

    $table_name = mapsvg_table_name($map_id, $table);

	$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if(!$table_exists) {
	    $response = array();
        $response['objects'] = array();
        $response['schema'] = array();

	    $response['error'] = 'Table '.$table_name.' not found.';

	    return $response;
    }

    $filters_sql = array();
    $filter_regions = '';

    $perpage = isset($query['perpage']) ? (int)$query['perpage'] : 30;

    $page = isset($query['page']) && (int)$query['page']>=0 ? (int)$query['page'] : 1;

    $start = ($page-1)*$perpage;

    $filters = isset($query['filters']) ? $query['filters'] : null;
    $filterout = isset($query['filterout']) ? $query['filterout'] : null;
    $search  = isset($filters['search']) ? $filters['search'] : null;
    if(isset($filters['search'])){
        unset($filters['search']);
    }
    $search_fallback  = isset($query['searchFallback']) ? $query['searchFallback'] === 'true' : false;

    $fields_schema = json_decode(mapsvg_get_schema($map_id, $table));
    $fields = array();
    $fields_dict = array();
    $searchable_fields = array();

    if($fields_schema) foreach($fields_schema as $fs){
        $fields_dict[$fs->name] = (array)$fs;
        $fields[] = $fs->name;
        if(isset($fs->searchable) && $fs->searchable===true){
            $searchable_fields[] = (array)$fs;
        }
    }

    if($table == 'regions'){
        $searchable_fields[] = array('name'=>'id', 'type'=>'id');
        $searchable_fields[] = array('name'=>'region_title', 'type'=>'text');
    }

    $select_distance = '';
    $having = '';
    if(isset($filters) && !empty($filters) && is_array($filters)){
        foreach($filters as $key=>$value){
//            if(in_array($key, $fields)){
                if($value!=''){
                    if($key == 'regions'){
	                    if(is_array($value)){
		                    $regions_array = array();
	                        foreach($value as $r){
		                        $regions_array[] = ' r2d.region_id = \''.$r.'\' ';
                            }
		                    $regions_sql = implode(' OR ', $regions_array);
	                    } else {
		                    $regions_sql = 'r2d.region_id = \''.esc_sql($value).'\'';
	                    }
                        $filter_regions = 'INNER JOIN '.$wpdb->prefix.'mapsvg_r2d r2d ON r2d.map_id='.$map_id.' AND r2d.object_id=id AND ('.$regions_sql.')';
                    }else if ($key == 'distance'){
//                        $filters_sql[] = '`distance` < '.esc_sql($value['length']).' ';
                        $having = ' HAVING distance < '.esc_sql($value['length']).' ';

                        $latlng = explode(',',$value['latlng']);
                        if(count($latlng) === 2 && !empty($latlng[0]) && !empty($latlng[1])){
                            $latlng = array('lat'=>$latlng[0], 'lng'=>$latlng[1]);
                            $koef = $value['units'] === 'mi' ? 3959 : 6371;

                            $select_distance = ", (
                            ".$koef." * acos(
                                cos( radians(".$latlng['lat'].") )
                                * cos( radians( location_lat ) )
                                * cos( radians( location_lng ) - radians(".$latlng['lng'].") )
                                + sin( radians(".$latlng['lat'].") )
                                * sin( radians( location_lat ) )
                            )
                            ) AS distance ";
                        }
//                        $having = 'HAVING distance < '
                    }else{
                        if(isset($fields_dict[$key]['multiselect']) && $fields_dict[$key]['multiselect'] === true){
//                            $filters_sql[] = 'MATCH (`'.$key.'`) AGAINST (\''.esc_sql($fields_dict[$key]['optionsDict']->{$value}).'*\' IN BOOLEAN MODE)';
	                        if(is_array($value)){
	                            foreach($value as $index=>$v){
		                            $value[$index] = '`'.$key.'` LIKE \'%"'.esc_sql($v).'"%\'';
                                }
		                        $filters_sql[] = "(".implode(' AND ', $value).")";
	                        } else {
		                        $filters_sql[] = '`'.$key.'` LIKE \'%"'.esc_sql($value).'"%\'';
	                        }
                        }else{
                            if(is_array($value)){
                                $values = '\''.implode('\',\'', $value).'\'';
	                            $filters_sql[] = '`'.$key.'` IN ('.$values.')';
                            } else {
	                            $filters_sql[] = '`'.$key.'`=\''.esc_sql($value).'\'';
                            }
                        }
                    }
                }
//            }
        }
    }
    if(isset($filterout) && !empty($filterout) && is_array($filterout)){
        foreach($filterout as $key=>$value){
            if($value!=''){
                $filters_sql[] = '`'.$key.'`!=\''.esc_sql($value).'\'';
            }
        }
    }
    if(isset($search) && !empty($search)){

//        $searchable_fields = $wpdb->get_var("SELECT GROUP_CONCAT( DISTINCT column_name )
//        FROM information_schema.STATISTICS
//        WHERE table_schema =  '".$wpdb->dbname."'
//        AND table_name =  '".mapsvg_table_name($map_id, $table)."'
//        AND index_type =  'FULLTEXT'
//        LIMIT 0 , 30");

//        $searchable_fields = array();
//        $schema = json_decode(mapsvg_get_schema($map_id, $table), true);
//        foreach($schema as $field){
//            if(isset($field['searchable']) && $field['searchable']===true){
//                $searchable_fields[] = $field;
//            }
//        }


        $like_fields = array();
        if($searchable_fields){
            if(isset($search_fallback) && $search_fallback){
//                $searchable_fields = explode(',',$searchable_fields);
                foreach($searchable_fields as $f){
                    if((isset($f['type']) && $f['type'] == 'region') || (isset($f['multiselect'])&&$f['multiselect']===true))
                        $like_fields[] = '`'.$f['name'].'` LIKE \'%"'.esc_sql($search).'%\'';
                    else
                        $like_fields[] = '`'.$f['name'].'` REGEXP \'(^| )'.esc_sql($search).'\'';
                }
                $filters_sql[] = '('.implode(' OR ', $like_fields).')';
            }else{
                $_search = array();
//                $searchable_fields = explode(',',$searchable_fields);
                $match = array();
                $search_like  = array();
                $search_exact = array();
                foreach($searchable_fields as $index=>$f){
                        if($f['type'] === 'location'){
                            $match[] = $f['name'].'_address';
                        } elseif($f['type'] === 'select' || $f['type'] === 'radio'){
	                        $match[] = $f['name'].'_text';
                        } elseif($f['type'] === 'text') {
                            if(isset($f['searchType'])){
                                if($f['searchType'] == 'fulltext'){
	                                $match[] = $f['name'];
                                } elseif($f['searchType'] == 'like'){
	                                $search_like[] = '`'.$f['name'].'` LIKE \''.esc_sql($search).'%\'';
                                } else {
	                                $search_exact[] = '`'.$f['name'].'`  = \''.esc_sql($search).'\'';
                                }
                            } else {
	                            $match[] = $f['name'];
                            }
                        } else {
	                        $match[] = $f['name'];
                        }
//                    }
                }
                $text_search_sql = array();
                if(count($match))
                    $_search[] = 'MATCH ('.implode(',',$match).') AGAINST (\''.esc_sql($search).'*\' IN BOOLEAN MODE)';
                if(!empty($search_like)){
	                $_search[] = '('.implode(' OR ', $search_like).')';
                }
                if(!empty($search_exact)){
	                $_search[] = '('.implode(' OR ', $search_exact).')';
                }
	            $filters_sql[] = '('.implode(' OR ', $_search).')';


            }
        }
    }

    if($filters_sql)
        $filters_sql = ' WHERE '.implode(' AND ', $filters_sql);
    else
        $filters_sql = '';

	$sort  = '';

	// TODO check if sortBy field exists

	if(isset($query['sort']) && !empty($query['sort'])){
		$sortArray = array();
		$distanceSortPresent = false;
		foreach($query['sort'] as $group){
		    if((isset($group['field']) && isset($group['order'])) && (!empty($group['field']) && in_array(strtolower($group['order']), array('asc','desc')))){
			    if($group['field'] === 'distance'){
				    $distanceSortPresent = true;
				    if(!isset($filters['distance']) || empty($filters['distance'])){
                        continue;
                    }
			    }
                $sortArray[] = '`'.$group['field'].'` '.$group['order'];
            }
		}
		if(isset($filters['distance']) && !empty($filters['distance']) && !$distanceSortPresent){
			array_unshift($sortArray, '`distance` ASC');
        }
		$sort = implode(',',$sortArray);
	} else {
		$sortBy  = 'id';
		$sortDir = 'DESC';
		if(isset($query['sortBy']) && !empty($query['sortBy'])){
			$sortBy = $table == 'regions' && $query['sortBy'] == 'title' ? 'region_title' : $query['sortBy'];
			$sortBy = '`'.$sortBy.'`';
			if(isset($filters['distance'])){
				$sortBy = 'distance ASC, '.$sortBy.' ';
			}
		}
		if(isset($query['sortDir']) && !empty($query['sortDir'])){
			if(in_array(strtolower($query['sortDir']), array('desc','asc'))){
				$sortDir = $query['sortDir'];
			}
		}
		$sort = $sortBy.' '.$sortDir;
	}

	$region_title = $table == 'regions' ? ', REPLACE(id,\' \',\'_\') as id_no_spaces,  `region_title` as `title` ' : '';

    $query_sql = 'SELECT *'.$region_title.$select_distance.' FROM '.mapsvg_table_name($map_id, $table).'
    '.$filter_regions.'  
    '.$filters_sql.'    
    '.$having.'     
    '.($sort ? 'ORDER BY '.$sort : '');

    mapsvg_log('Getting all objects from the "'.$table.'" table: '.$query_sql, 'MySQL');

	// Easy pagination: take +1 record to check that there are items available for the next page,
    // then remove that extra record
    if($perpage > 0){
        $perpage++;
	    $query_sql .= ' LIMIT '.$start.','.$perpage;
    }
    
    $data = $wpdb->get_results($query_sql, ARRAY_A);

    if($wpdb->last_error){
        mapsvg_log($wpdb->last_error, 'MySQL',MAPSVG_ERROR);
    }

    if($data && $db_types = mapsvg_get_db_types_from_schema($fields_schema)){
        foreach ($data as $index=>$object){
            if($table === 'database'){
            }
            $data[$index] = mapsvg_decode_data($db_types, $object);
        }
    }

    $response = array();
    if($data){
        $response['objects'] = $data;
    }else{
        $response['objects'] = array();
    }
    if(isset($query['with_schema'])){
        $response['schema'] = $fields_schema;
    }

    return $response;
}

function ajax_mapsvg_data_get_all($query){
    echo json_encode(mapsvg_data_get_all($_GET));
	die();
}
add_action('wp_ajax_mapsvg_data_get_all', 'ajax_mapsvg_data_get_all');
add_action('wp_ajax_nopriv_mapsvg_data_get_all', 'ajax_mapsvg_data_get_all');


/**
 * (Ajax) Save map settings
 */
function ajax_mapsvg_save() {

	mapsvg_check_nonce();

    if(isset($_POST['data'])){
        echo $post_id = mapsvg_save($_POST['data']);
    }

	die();
}
add_action('wp_ajax_mapsvg_save', 'ajax_mapsvg_save');

/**
 * (Ajax) Delete map
 */
function ajax_mapsvg_delete() {
    if(isset($_POST['id']))
        mapsvg_delete($_POST['id'], true);
	die();
}
add_action('wp_ajax_mapsvg_delete', 'ajax_mapsvg_delete');

/**
 * (Ajax) Copy map
 */
function ajax_mapsvg_copy() {
    if(!empty($_POST['id']) && !empty($_POST['new_name']))
        echo mapsvg_copy($_POST['id'], $_POST['new_name']);
	die();
}
add_action('wp_ajax_mapsvg_copy', 'ajax_mapsvg_copy');


/**
 * (Ajax) Get map settings
 */
function mapsvg_get() {
    if(isset($_GET['id'])){
        $post = mapsvg_get_map($_GET['id']);
        if (get_post_type($post)!='mapsvg'){
            echo 'Post type must be "mapsvg"';
            die();
        }
        
        $mapsvg_options = $post->post_content;
    }
        echo $mapsvg_options;

	die();
}
add_action('wp_ajax_mapsvg_get', 'mapsvg_get');
add_action( 'wp_ajax_nopriv_mapsvg_get', 'mapsvg_get' ); 


/**
 *  Register "mapsvg" post type
 */
function reg_mapsvg_post_type(){
    $post_args = array(
        'labels' => array(
            'name' => 'MapSVG',
            'singular_name' => 'mapSVG map'),
        'description' => 'Allows you to insert a map to any page of your website',
        'public' => false,
        'show_ui' => false,
        'exclude_from_search' => true,
        'can_export' => true
    );

    register_post_type('mapsvg', $post_args);
}
add_action('init','reg_mapsvg_post_type');


/**
 * Add "ajaxurl" JavaScript global variable that contains URL of the admin-ajax.php file
 */
function mapsvg_ajaxurl() {
    $url = '';
    if ( is_admin() )
        $url = admin_url( 'admin-ajax.php' );
    else
        $url = site_url( 'wp-admin/admin-ajax.php' );
    ?>
        <script type="text/javascript">
        var ajaxurl = '<?php echo $url; ?>';
        </script>
    <?php
}
add_action('wp_head','mapsvg_ajaxurl');


/**
 * Get contents of a WP post (some people use it in a custom JS code that they add to MapSVG event handlers)
 */
function mapsvg_get_post () {

    $pid        = intval($_POST['post_id']);
    $the_query  = new WP_Query(array('p' => $pid));
    $format     = $_POST['format']  == 'html' ? 'html' : 'json';

    if ($the_query->have_posts()) {
        while ( $the_query->have_posts() ) {
            $the_query->the_post();

            if($format == 'html'){
                $data = '
                    <div class="post-container">
                        <div id="project-content">
                            <h1 class="entry-title">'.get_the_title().'</h1>
                            <div class="entry-content">'.get_the_content().'</div>
                        </div>
                    </div>
                ';
            }else{
                $data = json_encode(array("title"=>get_the_title(),"content"=>get_the_content()));
            }

        }
    }
    else {
        echo __('Didnt find anything', THEME_NAME);
    }
    wp_reset_postdata();


    echo $data;
}
add_action ( 'wp_ajax_nopriv_load-content', 'mapsvg_get_post' );
add_action ( 'wp_ajax_load-content', 'mapsvg_get_post' );


/**
 * Get all maps created in MapSVG. Used in old WordPress page editor (WordPress 4.x)
 */
function ajax_mapsvg_get_maps () {
//    $data = get_posts(array('numberposts'=>999, 'post_type'=>'mapsvg');
//    echo json_encode($data);
    $args = array( 'post_type' => 'mapsvg');
    $loop = new WP_Query( $args );
    $array = array();

    while ( $loop->have_posts() ) : $loop->the_post();

        $array[] = array(
            'id' => get_the_ID(),
            'title' => get_the_title()
        );

    endwhile;

    wp_reset_query();
    ob_clean();
    echo json_encode($array);
    die();
}
add_action ( 'wp_ajax_mapsvg_get_maps', 'ajax_mapsvg_get_maps' );


/**
 * Find posts by title.
 * Used in MapSVG Database forms, when users attaches posts to MapSVG DB objects
 */
function mapsvg_search_posts(){
    global $wpdb;

    $title = $_GET['query'];
    $post_type = $_GET['post_type'];

    $results = $wpdb->get_results("SELECT id, post_title, post_content FROM $wpdb->posts WHERE post_type='".$post_type."' AND post_title LIKE '".$title."%' AND post_status='publish' LIMIT 20");

    foreach($results as $r){
        $r->url = get_permalink($r->id);
        $r->ID = $r->id;
	    if (function_exists('get_fields') ) {
		    $r->acf = get_fields($r->id);
	    }
    }

	echo json_encode($results);
    die();
}
add_action ( 'wp_ajax_mapsvg_search_posts', 'mapsvg_search_posts' );

/**
 * Replace http:// and https:// with "//"
 *
 * @param $url
 * URL
 *
 * @return string
 * Formatted URL
 */
function safeURL($url){
    if(strpos("http://",$url) === 0 || strpos("https://",$url) === 0){
        $s = explode("://", $url);
        $url = "//".array_pop($s);
    }
    return $url;
}

function getOldOptions(){
    global $wpdb;

    $r = $wpdb->get_results("
        SELECT meta_value FROM ".$wpdb->postmeta." WHERE meta_key = 'mapsvg_options'
    ");
}

/**
 * [Deprecated]
 *
 * Get outdated maps that need to be updated
 * @return array
 */
function getOutdated(){
    global $wpdb;

    $r = $wpdb->get_results("
        SELECT t.pid as id, t.ver as version FROM (SELECT p.ID as pid, pm.meta_value as ver FROM ".$wpdb->posts." p
        LEFT JOIN ".$wpdb->postmeta." pm ON pm.post_id = p.ID AND pm.meta_key = 'mapsvg_version'
        WHERE p.post_type='mapsvg') t WHERE t.ver != '".MAPSVG_VERSION."' OR t.ver IS NULL
    ");

    $maps_outdated = array();

    if($r)
        foreach ( $r as $other_version ){
            if($other_version->version == null || version_compare($other_version->version, '2.0.0', '<')){
                $maps_outdated[$other_version->id] = $other_version->version ? $other_version->version : '1.6.4' ;
            }
        }


    return $maps_outdated;
}

/**
 * [Deprecated]
 *
 * Upgrade outdated maps
 *
 * @param $maps
 * List of maps to be upgraded
 *
 * @return int
 * Number of updated maps
 */
function updateOutdatedMaps($maps){
    $i = 0;
    if($maps)
        foreach($maps as $id=>$version){
            if($version == null || version_compare($version,'2.0.0','<'))
                if(updateMapTo2($id))
                    $i++;
        }
    return $i;
}

/**
 * Upgrade map to version 2.0
 *
 * @param $id
 * Map ID
 *
 * @return bool
 * Returns true if the map was updated
 */
function updateMapTo2($id){
    $d = get_post_meta($id,'mapsvg_options');
    if($d && isset($d[0]['m']))
        $data = $d[0]['m'];
    else
        return false;

    $events = array();
    if(isset($d[0]['events']))
        foreach($d[0]['events'] as $key=>$val)
            if(!empty($val))
                $events[$key] = $val;


    if(isset($data['pan'])){
        // do
        $data['scroll'] = array('on'=>($data['pan']=="1"));
        unset($data['pan']);
    }


    if(isset($data['zoom'])){
        $data['zoom'] = array('on'=>($data['zoom']=="1"));
    }else{
        $data['zoom'] = array();
    }

    if(isset($data['zoomButtons'])){
        $data['zoom']['buttons'] = array('location'=>$data['zoomButtons']['location']);
        unset($data['zoomButtons']);
    }
    if(isset($data['zoomLimit'])){
        $data['zoom']['limit'] = $data['zoomLimit'];
        unset($data['zoomLimit']);
    }
    if(isset($data['zoomDelta'])){
        unset($data['zoomDelta']);
    }
    if(isset($data['popover'])){
        unset($data['popover']);
    }

    if(isset($data['tooltipsMode'])){
        $data['tooltips'] = array('mode'=>($data['tooltipsMode']=='names'?'id':'off'));
        unset($data['tooltipsMode']);
    }

    if(isset($data['regions'])){
        if(count($data['regions'])>0){
            foreach($data['regions'] as &$r){
                if(isset($r['attr'])){
                    foreach($r['attr'] as $key=>$value){
                        if(!empty($value))
                            $r[$key] = $value;
                    }
                    unset($r['attr']);
                }
            }
        }
    }

    if(isset($data['marks'])){
        if(count($data['marks'])>0){
            $data['markers'] = $data['marks'];
            $inc = 0;
            foreach($data['markers'] as &$m){
                $m['id'] = 'marker_'.$inc;
                $inc++;
                if(isset($m['attrs'])){
                    foreach($m['attrs'] as $key=>$value){
                        if(!empty($value))
                            $m[$key] = $value;
                    }
                    unset($m['attrs']);
                }
            }
        }
        unset($data['marks']);
    }

    $data = json_encode($data);
    // We should add events to options separately as they
    // shouldn't be enclosed with quotes by json_encode
    $str = array();
    if(!empty($events)){
        foreach($events as $e=>$func)
            $str[] = $e.':'.stripslashes_deep($func);
        $events = implode(',',$str);

        $data = substr($data,0,-1).','.$events.'}';
    }

//        $data = str_replace("'","\'",$data);
    $data = addslashes($data);

//    delete_post_meta($id, 'mapsvg_options');
    mapsvg_save(array('map_id'=>$id, 'mapsvg_data'=>$data));

    return true;
}

/**
 * Rollback map upgrades. Used in case of any problems.
 */
function rollBack(){
    global $wpdb;

    $res = $wpdb->get_results("
        SELECT post_id, meta_value FROM ".$wpdb->postmeta." WHERE meta_key = 'mapsvg_options'
    ");
    foreach ( $res as $r ){
        delete_post_meta($r->post_id, 'mapsvg_version');
    }
}

function mapsvg_get_post_types(){
    global $wpdb;

    $args = array(
        '_builtin'   => false
    );

    $_post_types = get_post_types($args,'names');
    if(!$_post_types)
        $_post_types = array();

    $post_types = array();
    foreach ($_post_types as $pt){
        if($pt!='mapsvg')
            $post_types[] = $pt;
    }
    $post_types[] = 'post';
    $post_types[] = 'page';
    return $post_types;
}


/**
 * Create / update MapSVG custom Database (or Regions database) structure
 * Used by the following 2 screens:
 * MapSVG > Menu > Region > Edit fields
 * MapSVG > Menu > Database > Edit fields
 *
 * @param $map_id
 * Map ID
 *
 * @param $table
 * Table type: regions / database
 *
 * @param $schema
 * New table schema (list of fields and their options)
 *
 * @param $prev_schema1
 * Previous table schema
 */
function mapsvg_set_db($map_id, $table, $schema, $prev_schema) {
    global $wpdb;

    $schema = $schema;

    $table_name = mapsvg_table_name($map_id, $table);

    $fields                 = array();

    if($table == 'regions')
        $fields[]               = 'id varchar(255) NOT NULL';
    else
        $fields[]               = 'id int(11) NOT NULL AUTO_INCREMENT';

    $old_fulltext_fields    = array();
    $old_index_fields       = array();
    $fulltext_fields        = array();
	$index_fields           = array();
    $old_keywordable_fields = array(); // for select & radio fields
    $new_field_names        = array('id');
    $primary_key            = '';
    $update_options         = array();
    $new_options            = array();
    $prev_options           = array();
    $clear_fields           = array();


    if($table == 'regions'){
        $fields[]            = 'region_title varchar(255)';
        $new_field_names[]   = 'region_title';
        $searchable_fields[] = 'id';
        $searchable_fields[] = 'region_title';
        $primary_key = 'PRIMARY KEY  (id(40))';
    }else{
        $primary_key = 'PRIMARY KEY  (id)';
    }

    foreach($schema as $field){
	    if($field['type'] == 'select'){
	        if(!isset($field['multiselect'])){
		        $field['multiselect'] = false;
            }
        }

        if($field['type'] == 'select' && $field['multiselect']===true){
            $field['type'] = 'text';
        }

        $fields[]          = '`'.$field['name'].'` '.$field['db_type'];
        $new_field_names[] = $field['name'];

        if(($field['type'] == 'select' && $field['multiselect']!==true) || $field['type'] == 'radio' || $field['type'] == 'status'){
            $fields[] = $field['name'].'_text varchar(255)';
            $new_field_names[] = $field['name'].'_text';
        }

        if($field['type'] == 'location'){
            $fields[] = 'location_lat FLOAT(10,7)';
            $fields[] = 'location_lng FLOAT(10,7)';
            $fields[] = 'location_x FLOAT';
            $fields[] = 'location_y FLOAT';
            $fields[] = 'location_address TEXT';
            $fields[] = 'location_img varchar(255)';
            $new_field_names[] = 'location_lat';
            $new_field_names[] = 'location_lng';
            $new_field_names[] = 'location_x';
            $new_field_names[] = 'location_y';
            $new_field_names[] = 'location_address';
            $new_field_names[] = 'location_img';
        }

        if(isset($field['options']) && $field['type']!='marker' && $field['type']!='region'){
            $new_options[$field['name']] = array();
            foreach($field['options'] as $o){
                $new_options[$field['name']][(string)$o['value']] = $o['label'];
            }
        }

        if(isset($field['searchable']) && $field['searchable'] == 'true')

	        if($field['type']=='text') {
		        if ( $field['searchType'] === 'fulltext' ) {
			        $fulltext_fields[] = $field['name'];
		        } else {
			        $index_fields[] = $field['name'];
                }
	        } else if($field['type']=='textarea' || $field['type']=='region') {
                $fulltext_fields[] = $field['name'];
            } else if ($field['type']=='location'){
                $fulltext_fields[] = $field['name'].'_address';
            } else {
                $fulltext_fields[] = $field['name'].'_text';
            }
    }

    if(!empty($prev_schema)) foreach($prev_schema as $_field){

        if(isset($_field['options']) && $_field['type']!='marker'&&$_field['type']!='region'){
            $prev_options[$_field['name']] = array();
            foreach($_field['options'] as $_o){
                $prev_options[$_field['name']][(string)$_o['value']] = $_o['label'];
            }
            if(!isset($prev_options[$_field['name']]) || !is_array($prev_options[$_field['name']]))
                $prev_options[$_field['name']] = array();
            if(!isset($new_options[$_field['name']]) || !is_array($new_options[$_field['name']]))
                $new_options[$_field['name']] = array();

            $diff = array_diff_assoc($new_options[$_field['name']], $prev_options[$_field['name']]);
            if(!isset($_field['multiselect'])){
	            $_field['multiselect'] = false;
            }

            if($diff){
                $update_options[] = array('name'             => $_field['name'],
                                          'type'             => $_field['type'],
                                          'next_multiselect' => (bool)$_field['multiselect'],
                                          'prev_multiselect' => (bool)$_field['multiselect'],
                                          'options'          => $diff
                                         );
            }

            if($_field['type']=='select' && ((bool)$_field['multiselect'] != (bool)$_field['multiselect'])){
                $clear_fields[] = $_field['name'];
            }
        }

        if(isset($_field['searchable']) && $_field['searchable'] == 'true'){
            if ($_field['type']=='text'){
                if(isset($_field['searchType'])){
                    if($_field['searchType'] === 'fulltext'){
	                    $old_fulltext_fields[] = $_field['name'];
                    } else {
	                    $old_index_fields[] = $_field['name'];
                    }
                } else {
	                $old_fulltext_fields[]  = $_field['name'];
                }
            } else if ($_field['type']=='textarea'|| $field['type']=='region'){
	            $old_fulltext_fields[]  = $_field['name'];
            } else if ($field['type']=='location'){
	            $old_fulltext_fields[] = $_field['name'].'_address';
            } else {
	            $old_fulltext_fields[] = $_field['name'].'_text';
            }
        }
    }


    $table_exists = $wpdb->get_var('SHOW TABLES LIKE \''.$table_name.'\'');
    if($table_exists){
        if($fulltext_fields != $old_fulltext_fields){
	        $index = $wpdb->get_row('SHOW INDEX FROM '.$table_name.' WHERE Key_name = \'_keywords\';', OBJECT);
	        if($index)
		        $wpdb->query('DROP INDEX `_keywords` ON '.$table_name);
        }
        if(!empty($old_index_fields) && ($index_fields != $old_index_fields)){
            $sql = array();
            $diff = array_diff($old_index_fields, $index_fields);
            foreach($diff as $key=>$value){
	            $sql[] = 'DROP INDEX `'.$value.'`';
            }
            $sql = implode(',', $sql);
            $wpdb->query('ALTER TABLE '.$table_name.' '.$sql);
        }
    }

//    $charset_collate   = $wpdb->get_charset_collate();
    $charset_collate = "default character set utf8\ncollate utf8_unicode_ci";

    if(!empty($fulltext_fields))
        $fulltext_fields = ",\nFULLTEXT KEY _keywords (".implode(',', $fulltext_fields).')';
    else
        $fulltext_fields = '';

    if(!empty($index_fields)){
	    foreach($index_fields as $key=>$value){
		    $index_fields[$key] = ",\nINDEX (`".$value."`)";
	    }
	    $index_fields = implode('', $index_fields);
    } else {
		$index_fields = '';
    }

    if(version_compare($wpdb->db_version(), '5.6.0', '<')){
	    $engine = " ENGINE=MyISAM ";
	} else {
		$engine = " ENGINE=InnoDB ";
    }


    $sql = "CREATE TABLE $table_name (
".implode(",\n", $fields).",
".$primary_key.$index_fields.$fulltext_fields."
) ".$engine.$charset_collate;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    mapsvg_log('Changing table structure: '.$sql, 'MySQL');

    dbDelta( $sql );

    // DROP removed columns
    $columns = $wpdb->get_col( "DESC " . $table_name, 0 );
    foreach ( $columns as $column_name ) {
        if(!in_array($column_name, $new_field_names)){
            $wpdb->query( "ALTER TABLE $table_name DROP COLUMN $column_name" );
        }
    }

    if($update_options){
        $field = '';
        foreach($update_options as $field){
            foreach($field['options'] as $id=>$label){
                $data = array();
                if($field['type']=='select' && ($field['prev_multiselect']===true || $field['next_multiselect']===true)){
                    if($field['prev_multiselect']===true && $field['next_multiselect']===true){
                        $prev = $prev_options[$field['name']][$id];
                        $wpdb->query('UPDATE '.$table_name.' SET `'.esc_sql($field['name']).'`=REPLACE(`'.esc_sql($field['name']).'`, \'"label":"'.esc_sql($prev).'"\',\'"label":"'.esc_sql($label).'"\')');
                    }else{
                        $wpdb->query('UPDATE '.$table_name.' SET `'.$field['name'].'`=\'\' ');
                    }
                }else{
                    $f = $field['name'].'_text';
                    $data[$f] = $label;
                    $where = array();
                    $where[$field['name']] = $id;
                    $wpdb->update($table_name, $data, $where);
                }
            }
        }
    }

    if($clear_fields){
        $field = '';
        foreach($clear_fields as $field){
            $wpdb->query('UPDATE '.$table_name.' SET `'.$field.'`=\'\' ');
        }
    }

}

/**
 * [This function needs to be replaced with something more effective]
 *
 * Check if wp_mapsvg_schema and wp_mapsvg_r2d tables exists.
 * Create if needed.
 */
function mapsvg_update_db_check() {
    global $wpdb;

    $schema_table_exists = $wpdb->get_var('SHOW TABLES LIKE \''.$wpdb->prefix.'mapsvg_schema\'');
    if(!$schema_table_exists){
        $charset_collate = "default character set utf8\ncollate utf8_unicode_ci";
        $wpdb->query("CREATE TABLE ".$wpdb->prefix."mapsvg_schema (id int(11) NOT NULL AUTO_INCREMENT, table_name VARCHAR (255) NOT NULL, fields text, PRIMARY KEY (id)) ".$charset_collate);
    }

    $r2d_table_exists = $wpdb->get_var('SHOW TABLES LIKE \''.$wpdb->prefix.'mapsvg_r2d\'');
    if(!$r2d_table_exists){
        $charset_collate = "default character set utf8\ncollate utf8_unicode_ci";
        $wpdb->query("CREATE TABLE ".$wpdb->prefix."mapsvg_r2d (map_id int(11) NOT NULL, region_id varchar(255) NOT NULL, object_id int(11) NOT NULL, INDEX (map_id, region_id), INDEX(map_id, object_id)) ".$charset_collate);
    }
}
add_action( 'plugins_loaded', 'mapsvg_update_db_check' );


/**
 * Check the map version and upgrade it if it's needed
 *
 * @param $map_id
 * Map ID
 *
 * @param $map_version
 * Map version number
 *
 * @return bool
 * Returns "false" if the map version is the same as current plugin version
 */
function mapsvg_maybe_update_the_map($map_id, $map_version){

    if($map_version === MAPSVG_VERSION){
        return false;
    }

    // Update the map
    $versions = explode(' ',MAPSVG_INCOMPATIBLE_VERSIONS);
    foreach($versions as $version){
        if($map_version < $version){
            if(mapsvg_update_map($map_id, $version, $map_version)){
                $map_version = $version;
            };
        }
    }
}

/**
 * Upgrade map settings to the next version
 *
 * @param $map_id
 * Map ID
 *
 * @param $update_to
 * Version number the map needs to be updated to
 *
 * @param $current_version
 * Current version number of the map
 *
 * @param array $params
 * Extra options
 *
 * @return bool
 */
function mapsvg_update_map($map_id, $update_to, $current_version, $params = array()){
    global $wpdb;

    switch ($update_to){
        case '5.0.0':

            if(version_compare($current_version, '3.2.0', '<')){
                return false;
            }

            $schema_fields = mapsvg_get_schema($map_id, 'database');
            $schema_fields = json_decode($schema_fields, true);
            $schema_with_markers = false;

            foreach($schema_fields as $field){
                if($field['type'] === 'marker'){
                    $schema_with_markers = true;
                }
            }

            $location_field = array('label'=>'Location','name'=>'location','type'=>'location','db_type'=>'text', 'visible' => true);

            if($schema_with_markers){
                //2. Iterate over found schemas to update them
                $new_schema   = $schema_fields;
                $new_schema[] = $location_field;

                // Update the schema (add location field)
                _mapsvg_save_schema($map_id, 'database', $new_schema);

                //4. SELECT id, marker FROM table_xxx
                $rows = $wpdb->get_results('SELECT id, marker FROM '.mapsvg_table_name($map_id, 'database'), ARRAY_A);
                if($rows){
                    // Iterate over all rows in the table and build "location" from "marker"
                    foreach($rows as $row){
                        $location = array();
                        $marker = json_decode($row['marker'], true);
                        if(isset($marker['geoCoords']) && isset($marker['geoCoords'][0]) && isset($marker['geoCoords'][1])){
                            $location['location_lat'] = $marker['geoCoords'][0];
                            $location['location_lng'] = $marker['geoCoords'][1];
                        } else if (isset($marker['xy'])){
                            $location['location_x'] = $marker['xy'][0];
                            $location['location_y'] = $marker['xy'][1];
                        } else if (isset($marker['x']) && isset($marker['y'])){
	                        $location['location_x'] = $marker['x'];
	                        $location['location_y'] = $marker['y'];
                        }
                        if(isset($marker['src'])){
                            $arr = explode('/',$marker['src']);
                            $location['location_img'] = array_pop($arr);
                        }
                        // Update DB record, add location data
                        $wpdb->update(mapsvg_table_name($map_id, 'database'), $location, array('id'=>$row['id']));
                    }
                }
                // If ALL rows (ONLY) were converted successfully then remove the marker column
                // $new_schema = $new_schema - marker_col;
                $new_schema = array_values(array_filter($new_schema, function($elem){ return $elem['type']!=='marker'; }));
                _mapsvg_save_schema($map_id, 'database', $new_schema);

            }

            // 5. Update map version
            update_post_meta($map_id, 'mapsvg_version', '5.0.0');
            break;
        case '3.2.0':

            if(version_compare($current_version, '3.0.0', '<')){
                return false;
            }

            // 1. Change region_id to regions (to allow multiple regions)
            $table = mapsvg_table_name($map_id, 'database');
            $regions_table = mapsvg_table_name($map_id, 'regions');
            if($wpdb->get_row('SHOW TABLES LIKE \''.$table.'\'') && $wpdb->get_row('SHOW COLUMNS FROM '.$table.' LIKE \'region_id\'')){
                $wpdb->query('UPDATE  '.$table.' t1, '.$regions_table.' t2 SET t1.region_id_text = CONCAT(\'[{"id": "\', t2.id, \'", "title": "\', t2.region_title,\'"}]\') WHERE t1.region_id = t2.id');
                $wpdb->query('ALTER TABLE '.$table.' DROP COLUMN `region_id`');
                $wpdb->query('ALTER TABLE '.$table.' CHANGE `region_id_text` `regions` TEXT');
            }
            $schema = $wpdb->get_var('SELECT fields FROM '.$wpdb->prefix.'mapsvg_schema WHERE table_name=\''.$table.'\'');
            if($schema){
                $schema = str_replace('"name":"region_id"','"name":"regions"',$schema);
                $wpdb->query('UPDATE '.$wpdb->prefix.'mapsvg_schema SET `fields`=\''.$schema.'\'  WHERE table_name=\''.$table.'\'');
            }


            // 2. Check if there is "status"/ "status_text" field in regions table and if there is, rename it to "_status"
            $schema = json_decode(mapsvg_get_schema($map_id, 'regions'), true);
            if(!$schema){
                $schema = array();
                _mapsvg_save_schema($map_id, 'regions', $schema);
            }else{
                $need_rename_status_field = false;
                $need_rename_status_text_field = false;
                foreach($schema as &$field){
                    if($field['name']=='status'){
                        $field['name'] = '_status';
                        $need_rename_status_field = $field['db_type'];
                    }elseif($field['name']=='status_text'){
                        $field['name'] = '_status_text';
                        $need_rename_status_text_field = $field['db_type'];
                    }
                }
                _mapsvg_save_schema($map_id, 'regions', $schema, true);
                if($need_rename_status_field){
                    $wpdb->query('ALTER TABLE '.$regions_table.' CHANGE `status` `_status` '.$need_rename_status_field);
                }
                if($need_rename_status_text_field){
                    $wpdb->query('ALTER TABLE '.$regions_table.' CHANGE `status_text` `_status_text` '.$need_rename_status_text_field);
                }
            }


            // 3. Add "status" field to regions table (new feature instead of "disabled" Region property)
            $disabledColor = isset($params['disabledColor']) && !empty($params['disabledColor']) ? $params['disabledColor'] : '';

            $obj = new stdClass();
            $obj->{'1'} = array("label"=>"Enabled","value"=>'1',"color"=>"","disabled"=>false);
            $obj->{'0'} = array("label"=>"Disabled","value"=>'0',"color"=> $disabledColor,"disabled"=>true);

            $status_field = array(
                'type'=>'status',
                'db_type'=>'varchar (255)',
                'label'=> 'Status',
                'name'=> 'status',
                'visible'=>true,
                'options'=>array(
                    $obj->{'1'},
                    $obj->{'0'}
                ),
                'optionsDict' => $obj
            );

            $schema[] = $status_field;
            _mapsvg_save_schema($map_id, 'regions', $schema);

            // 4. Get enabled/disabled status from regions and convert it into status
            $wpdb->query('UPDATE '.$regions_table.' SET status=1');

            if(isset($params['disabledRegions'])){
                foreach($params['disabledRegions'] as $d_id){
                    $wpdb->update($regions_table, array('status'=>0), array('id'=>$d_id));
                }
            }

            // 5. Update map version
            update_post_meta($map_id, 'mapsvg_version', '3.2.0');

            break;
        case '2.0.0':

	        $_data = mapsvg_getMetaOptions($map_id);

	        $data = $_data['m'];

	        $events = array();
	        if(isset($_data['events']))
		        foreach($_data['events'] as $key=>$val)
			        if(!empty($val))
				        $events[$key] = $val;


            if(isset($data['pan'])){
                // do
                $data['scroll'] = array('on'=>($data['pan']=="1"));
                unset($data['pan']);
            }


            if(isset($data['zoom'])){
                $data['zoom'] = array('on'=>($data['zoom']=="1"));
            }else{
                $data['zoom'] = array();
            }

            if(isset($data['zoomButtons'])){
                $data['zoom']['buttons'] = array('location'=>$data['zoomButtons']['location']);
                unset($data['zoomButtons']);
            }
            if(isset($data['zoomLimit'])){
                $data['zoom']['limit'] = $data['zoomLimit'];
                unset($data['zoomLimit']);
            }
            if(isset($data['zoomDelta'])){
                unset($data['zoomDelta']);
            }
            if(isset($data['popover'])){
                unset($data['popover']);
            }

            if(isset($data['tooltipsMode'])){
                $data['tooltips'] = array('mode'=>($data['tooltipsMode']=='names'?'id':'off'));
                unset($data['tooltipsMode']);
            }

            if(isset($data['regions'])){
                if(count($data['regions'])>0){
                    foreach($data['regions'] as &$r){
                        if(isset($r['attr'])){
                            foreach($r['attr'] as $key=>$value){
                                if(!empty($value))
                                    $r[$key] = $value;
                            }
                            unset($r['attr']);
                        }
                    }
                }
            }

            if(isset($data['marks'])){
                if(count($data['marks'])>0){
                    $data['markers'] = $data['marks'];
                    $inc = 0;
                    foreach($data['markers'] as &$m){
                        $m['id'] = 'marker_'.$inc;
                        $inc++;
                        if(isset($m['attrs'])){
                            foreach($m['attrs'] as $key=>$value){
                                if(!empty($value))
                                    $m[$key] = $value;
                            }
                            unset($m['attrs']);
                        }
                    }
                }
                unset($data['marks']);
            }

            $data = json_encode($data);
            // We should add events to options separately as they
            // shouldn't be enclosed with quotes by json_encode
            $str = array();
            if(!empty($events)){
                foreach($events as $e=>$func)
                    $str[] = $e.':'.stripslashes_deep($func);
                $events = implode(',',$str);

                $data = substr($data,0,-1).','.$events.'}';
            }

            $data = addslashes($data);

            mapsvg_save(array('map_id'=>$map_id, 'mapsvg_data'=>$data));

            // Update map version
            update_post_meta($map_id, 'mapsvg_version', '2.0.0');

            break;
        default:
            null;
    }
}

/**
 * MapSVG 2.4.1 code
 *
 * All of the code below down to the end of file is used to load old MapSVG interface for maps created in MapSVG 2.x versions
 * Those maps can't be upgraded to higher versions. The code is too different.
 */
function mapsvg_add_jscss_common_2(){

    wp_register_style('mapsvg2', MAPSVG_PLUGIN_URL . 'mapsvg2/css/mapsvg.css');
    wp_enqueue_style('mapsvg2', null, '0.9');

    wp_register_script('jquery.mousewheel', MAPSVG_PLUGIN_URL . 'mapsvg2/js/jquery.mousewheel.min.js',array('jquery'), '3.0.6');
    wp_enqueue_script('jquery.mousewheel', null, '3.0.6');

    wp_register_script('handlebars', MAPSVG_PLUGIN_URL . 'mapsvg2/js/handlebars.js', null, '4.0.2');
    wp_enqueue_script('handlebars');

    wp_register_script('typeahead', MAPSVG_PLUGIN_URL . 'mapsvg2/js/typeahead.bundle.min.js', null, '1.2.1');
    wp_enqueue_script('typeahead');

    wp_register_script('nanoscroller', MAPSVG_PLUGIN_URL . 'mapsvg2/js/jquery.nanoscroller.min.js', null, '0.8.7');
    wp_enqueue_script('nanoscroller');
    wp_register_style('nanoscroller', MAPSVG_PLUGIN_URL . 'mapsvg2/css/nanoscroller.css');
    wp_enqueue_style('nanoscroller');


    if(MAPSVG_DEBUG)
        wp_register_script('mapsvg2', MAPSVG_PLUGIN_URL . 'mapsvg2/js/mapsvg.js', array('jquery'), (MAPSVG_RAND?rand():''));
    else
        wp_register_script('mapsvg2', MAPSVG_PLUGIN_URL . 'mapsvg2/js/mapsvg.min.js', array('jquery'), MAPSVG_JQUERY_VERSION);
    wp_enqueue_script('mapsvg2');
}
function mapsvg_add_jscss_admin_2(){

    global $mapsvg_settings_page, $wp_version;

    mapsvg_add_jscss_common_2();

    if(isset($_GET['page']) && $_GET['page']=='mapsvg-config'){

        wp_register_script('admin.mapsvg', MAPSVG_PLUGIN_URL . 'mapsvg2/js/admin.js', array('jquery','mapsvg2'), MAPSVG_ASSET_VERSION);
        wp_enqueue_script('admin.mapsvg');

        wp_register_script('bootstrap', MAPSVG_PLUGIN_URL . "mapsvg2/js/bootstrap.min.js", null, '3.3.6');
        wp_enqueue_script('bootstrap');
        wp_register_style('bootstrap', MAPSVG_PLUGIN_URL . "mapsvg2/css/bootstrap.min.css", null, '3.3.6');
        wp_enqueue_style('bootstrap');
        wp_register_style('fontawesome', MAPSVG_PLUGIN_URL . "mapsvg2/css/font-awesome.min.css", null, '4.4.0');
        wp_enqueue_style('fontawesome');

        wp_register_script('bootstrap-colorpicker', MAPSVG_PLUGIN_URL . 'mapsvg2/js/bootstrap-colorpicker.min.js');
        wp_enqueue_script('bootstrap-colorpicker');
        wp_register_style('bootstrap-colorpicker', MAPSVG_PLUGIN_URL . 'mapsvg2/css/bootstrap-colorpicker.min.css');
        wp_enqueue_style('bootstrap-colorpicker');

        wp_register_script('jquery.message', MAPSVG_PLUGIN_URL . 'mapsvg2/js/jquery.message.js', array('jquery'));
        wp_enqueue_script('jquery.message');

        wp_register_style('jquery.message.css', MAPSVG_PLUGIN_URL . 'mapsvg2/css/jquery.message.css');
        wp_enqueue_style('jquery.message.css');

        wp_register_style('main.css', MAPSVG_PLUGIN_URL . 'mapsvg2/css/main.css');
        wp_enqueue_style('main.css');

        wp_register_style('codemirror', MAPSVG_PLUGIN_URL . 'mapsvg2/css/codemirror.css');
        wp_enqueue_style('codemirror');

        wp_enqueue_script('select2', MAPSVG_PLUGIN_URL . 'mapsvg2/js/select2.min.js', array('jquery'), '4.0',true);
        wp_register_style('select2', MAPSVG_PLUGIN_URL . 'mapsvg2/css/select2.min.css',null,'4.0.31');
        wp_enqueue_style('select2');

        wp_register_script('ionslider', MAPSVG_PLUGIN_URL . 'mapsvg2/js/ion.rangeSlider.min.js', array('jquery'), '2.1.2');
        wp_enqueue_script('ionslider');
        wp_register_style('ionslider', MAPSVG_PLUGIN_URL . 'mapsvg2/css/ion.rangeSlider.css');
        wp_enqueue_style('ionslider');
        wp_register_style('ionslider-skin', MAPSVG_PLUGIN_URL . 'mapsvg2/css/ion.rangeSlider.skinNice.css');
        wp_enqueue_style('ionslider-skin');

        wp_register_script('codemirror', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.js', null, '1.0');
        wp_enqueue_script('codemirror');
        wp_register_script('codemirror.javascript', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.javascript.js', null, '1.0');
        wp_enqueue_script('codemirror.javascript');
        wp_register_script('codemirror.xml', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.xml.js', null, '1.0');
        wp_enqueue_script('codemirror.xml');
        wp_register_script('codemirror.htmlmixed', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.htmlmixed.js', null, '1.0');
        wp_enqueue_script('codemirror.htmlmixed');
        wp_register_script('codemirror.simple', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.simple.js', null, '1.0');
        wp_enqueue_script('codemirror.simple');
        wp_register_script('codemirror.multiplex', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.multiplex.js', null, '1.0');
        wp_enqueue_script('codemirror.multiplex');
        wp_register_script('codemirror.handlebars', MAPSVG_PLUGIN_URL . 'mapsvg2/js/codemirror.handlebars.js', null, '1.0');
        wp_enqueue_script('codemirror.handlebars');

        if(version_compare($wp_version, "3.8", '>=')){
            wp_register_style('mapsvg-grey', MAPSVG_PLUGIN_URL . 'mapsvg2/css/grey.css');
            wp_enqueue_style('mapsvg-grey');
        }
    }

}
function mapsvg_conf_2(){
    global $mapsvg_page;

    // Check user rights
    if(!current_user_can('edit_posts'))
        die();

    $file       = null;
    $map_chosen = false;
    $svg_file_url = "";
    if (isset($_GET['map']))
        $svg_file_url = $_GET['map'];

    // If $_GET['map_id'] is set then we should get map's settings and from DB
    $map_id = isset($_GET['map_id']) ? $_GET['map_id'] : 'new';

    $js_mapsvg_options = "";
    if($map_id && $map_id!='new'){
        $post = mapsvg_get_map($map_id);
        $js_mapsvg_options = $post->post_content;

        $mapsvg_version = get_post_meta((int)$map_id, 'mapsvg_version');
    }


    $title = "";
    if($svg_file_url || ($map_id && $map_id!='new')){

        $mapsvg_page = 'edit';

        $title = isset($post) && $post->post_title ? $post->post_title : "New map";

        if ($js_mapsvg_options == "" && $svg_file_url!="")
            $js_mapsvg_options = json_encode(array('source' => $svg_file_url));

        // Load pin images
        $pin_files = @scandir(MAPSVG_PINS_DIR);
        if($pin_files){
            array_shift($pin_files);
            array_shift($pin_files);
        }

        $safeMarkerImagesURL = safeURL(MAPSVG_PINS_URL);
        $markerImages = array();
        $allowed =  array('gif','png' ,'jpg','svg','jpeg');
        foreach($pin_files as $p){
            $ext = pathinfo($p, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed) )
                $markerImages[] = array("url"=>$safeMarkerImagesURL.$p, "file"=>$p);
        }
        $safeMarkerImagesURL2 = safeURL(MAPSVG_MAPS_UPLOADS_URL . '/markers');
	    $pin_files2 = @scandir(MAPSVG_MAPS_UPLOADS_DIR . '/markers');
	    if($pin_files){
		    array_shift($pin_files);
		    array_shift($pin_files);
		    foreach($pin_files2 as $p){
			    $ext = pathinfo($p, PATHINFO_EXTENSION);
			    if(in_array($ext,$allowed) )
				    $markerImages[] = array("url"=>$safeMarkerImagesURL2.$p, "file"=>$p);
		    }
	    }

    }else{
        $mapsvg_page = 'index';
        // Load list of available maps from MAPSVG_MAPS_DIR

        $maps = array();
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(MAPSVG_MAPS_DIR)) as $filename)
        {
            if(strpos($filename,'.svg')!==false){
                $path_s = ltrim(str_replace('\\','/',str_replace(MAPSVG_MAPS_DIR,'',$filename)),'/');
                $maps[] = array(
                    "url" => MAPSVG_MAPS_URL . $path_s,
                    "path" => $path_s
                );
            }
        }
        if(is_dir(MAPSVG_MAPS_UPLOADS_DIR)){
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(MAPSVG_MAPS_UPLOADS_DIR)) as $filename)
            {
                if(strpos($filename,'.svg')!==false){
                    $path_s = ltrim(str_replace('\\','/',str_replace(MAPSVG_MAPS_UPLOADS_DIR,'',$filename)),'/');

                    $maps[] = array(
                        "url" => MAPSVG_MAPS_UPLOADS_URL.$path_s,
                        "path" => 'user-uploads/'.$path_s
                    );
                }
            }
        }

        if(isset($_GET['mapsvg_rollback'])){
            rollBack();
        }

        $generated_maps = get_posts(array('numberposts'=>999, 'post_type'=>'mapsvg'));

        $outdated_maps = getOutdated();
        $num = count($outdated_maps);
        if($num>0){
            // do update
            $num_updated = updateOutdatedMaps($outdated_maps);
            if ($num == 1 && $num_updated = 1)
                $mapsvg_notice = "There was 1 outdated map created in old version of MapSVG. The map was successfully updated.";
            elseif ($num == $num_updated)
                $mapsvg_notice = "There were ".$num." outdated maps created in old versions of MapSVG. All maps were successfully updated.";
            elseif ($num_updated == 0)
                $mapsvg_notice = "An error occured during update of your maps created in previous versions of MapSVG plugin. Please contact MapSVG support to get help.";
            elseif ($num != $num_updated)
                $mapsvg_notice = "There were ".$num." outdated maps created in old versions of MapSVG - and ".$num_updated." were successfully updated.";

        }

    }


    $template = 'template_'.$mapsvg_page.'.inc';

    include(MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'mapsvg2/header.inc');
    include(MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'mapsvg2'.DIRECTORY_SEPARATOR.$template);
    if($template == 'template_edit.inc'){
        include (MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'mapsvg2'.DIRECTORY_SEPARATOR.'template_handlebars.hbs');
    }
    include(MAPSVG_PLUGIN_DIR.DIRECTORY_SEPARATOR.'mapsvg2'.DIRECTORY_SEPARATOR.'footer.inc');

    return true;
}
function mapsvg_save_2( $data ){
    global $wpdb;

    $data_js   = stripslashes($data['mapsvg_data']);

    $postarr = array(
        'post_type'    => 'mapsvg',
        'post_status'  => 'publish'
    );

    if(isset($data['title'])){
        $postarr['post_title'] = strip_tags(stripslashes($data['title']));
    }else{
        $postarr['post_title'] = "New Map";
    }

    $postarr['post_content'] = $data_js;

    if(isset($data['map_id']) && $data['map_id']!='new'){
        $postarr['ID'] = (int)$data['map_id'];
        // PREPARE STATEMENT AND PUT INTO DB
        $wpdb->query(
            $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID = %d", array($postarr['post_title'], $postarr['post_content'], $postarr['ID']))
        );
        update_post_meta($postarr['ID'], 'mapsvg_version', '2.4.1');
        $post_id = $postarr['ID'];
    }else{
        $post_id = wp_insert_post( $postarr );
        // PREPARE STATEMENT AND PUT INTO DB
        $wpdb->query(
            $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID = %d", array($postarr['post_title'], $postarr['post_content'], $post_id))
        );
        add_post_meta($post_id, 'mapsvg_version', MAPSVG_VERSION);
    }

    return $post_id;
}
function mapsvg_delete_2($id, $ajax){
    wp_delete_post($id);
    delete_post_meta($id, 'mapsvg_version');
    if(!$ajax)
        wp_redirect(admin_url('?page=mapsvg-config'));
}
function mapsvg_copy_2($id, $new_title){
    global $wpdb;

    $post = mapsvg_get_map($id);

    $copy_post = array(
        'post_type'    => 'mapsvg',
        'post_status'  => 'publish'
    );

    $new_title = stripslashes(strip_tags($new_title));
    $post_content = $post->post_content;

    $new_id = wp_insert_post($copy_post);

    $wpdb->query(
        $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID=%d", array($new_title, $post_content, $new_id))
    );

    $version = get_post_meta($id, 'mapsvg_version', true);
    add_post_meta($new_id, 'mapsvg_version', $version);
    return $new_id;
}
function mapsvg_print_2( $atts ){
    global $mapsvg_inline_script;

    $post = mapsvg_get_map($atts['id']);

    if (empty($post->ID))
        return 'Map not found, please check "id" parameter in your shortcode.';

    $data  = '<div id="mapsvg-'.$post->ID.'" class="mapsvg"></div>';
    $script = '<script type="text/javascript">';

    if(!empty($atts['selected'])){
        $country = str_replace(' ','_', $atts['selected']);
        $script .= '
      var mapsvg_options = '.$post->post_content.';
      jQuery.extend( true, mapsvg_options, {regions: {"'.$country.'": {selected: true}}} );
      jQuery("#mapsvg-'.$post->ID.'").mapSvg2(mapsvg_options);</script>';
    }else{
        $script .= 'jQuery("#mapsvg-'.$post->ID.'").mapSvg2('.$post->post_content.');</script>';
    }
    $mapsvg_inline_script[] = $script;

    //wp_footer('script');
    add_action('admin_footer', 'mapsvg_script', 9999);

    //return //wp_specialchars_decode($data);
    return $data;
}

function mapsvg_download_svg()
{

    if (!isset($_POST['png']) || !isset($_POST['bounds']))
        die();

    $bounds = implode(' ',$_POST['bounds']);

    $png = $_POST['png'];
    $width = (int)$_POST['width'];
    $height = (int)$_POST['height'];
    $filename = 'mapsvg' . ($_POST['map_id']?'-'.$_POST['map_id']:'') . '.svg';
//    list($width, $height, $type, $attr) = getimagesize($png);

    $mapsvg_error = mapsvg_check_upload_dir();

    if (!$mapsvg_error) {
//        $target_file = MAPSVG_MAPS_UPLOADS_DIR . "/tmp/" . $filename;
        $target_file = MAPSVG_MAPS_UPLOADS_DIR . "/" . $filename;

        $svg = '';
        $svg .= '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
    xmlns:mapsvg="http://mapsvg.com"
    xmlns:xlink="http://www.w3.org/1999/xlink"    
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:cc="http://creativecommons.org/ns#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:svg="http://www.w3.org/2000/svg"  
    xmlns="http://www.w3.org/2000/svg"
    width="' . $width*20 . '"
    height="' . $height*20 . '"
    mapsvg:geoViewBox="'.$bounds.'"
>
';

        $svg .= '<image id="mapsvg-google-map-background" xlink:href="' . $png . '"  x="0" y="0" height="' . $height*20 . '" width="' . $width*20 . '"></image>';
        $svg .= '</svg>';

        $file = fopen($target_file, 'w');
        $res = fwrite($file, $svg);
        fclose($file);

        echo admin_url('?page=mapsvg-config')."&action=download_google_map&noheader=true";
        die();

    }
}

add_action('wp_ajax_mapsvg_download_svg', 'mapsvg_download_svg');

function mapsvg_check_upload_dir(){
    $mapsvg_error = false;
    if(!file_exists(MAPSVG_MAPS_UPLOADS_DIR)){
        if(!wp_mkdir_p(MAPSVG_MAPS_UPLOADS_DIR))
            $mapsvg_error = "Unable to create directory ".MAPSVG_MAPS_UPLOADS_DIR.". Is its parent directory writable by the server?";
    }else{
        if(!wp_is_writable(MAPSVG_MAPS_UPLOADS_DIR))
            $mapsvg_error = MAPSVG_MAPS_UPLOADS_DIR." is not writable. Please change folder permissions.";
    }
    return $mapsvg_error;
}

function mapsvg_upload () {

	mapsvg_check_nonce();

    $mapsvg_error = mapsvg_check_upload_dir();

    if ( ! $mapsvg_error ) {
        $filename    = sanitize_file_name( basename( $_POST["filename"] ) );
        $target_file = MAPSVG_MAPS_UPLOADS_DIR . "/" . $filename;

        //        $file_parts = pathinfo($_FILES['svg_file']['name']);

        $file = fopen( $target_file, 'w' );
        fwrite( $file, stripslashes( $_POST['data'] ) );
        fclose( $file );

        echo $filename;

    }
    die();
}
add_action('wp_ajax_mapsvg_upload', 'mapsvg_upload');

function mapsvg_marker_upload () {

	mapsvg_check_nonce();

    $mapsvg_error = mapsvg_check_upload_dir();

    if(!$mapsvg_error){

	    if (isset($_FILES['file'])) {

            if (!file_exists(MAPSVG_MAPS_UPLOADS_DIR . "/markers")) {
                mkdir(MAPSVG_MAPS_UPLOADS_DIR . "/markers", 0777, true);
            }
		    if(move_uploaded_file($_FILES['file']['tmp_name'], MAPSVG_MAPS_UPLOADS_DIR . "/markers/".$_FILES['file']['name'])){
                $marker = array(
                    'url' => MAPSVG_MAPS_UPLOADS_URL."markers/".$_FILES['file']['name'],
                    'file' => $_FILES['file']['name'],
                    'folder' => 'uploads/markers',
                    'default' => false
                );

			    echo json_encode($marker);
		    } else {
			    echo '{"error": "Can\'t write the file"}';
		    }
		    exit;
	    } else {
		    echo '{"error": "No files to upload"}';
	    }

    }
    die();
}
add_action('wp_ajax_mapsvg_marker_upload', 'mapsvg_marker_upload');

function mapsvg_save_google_api_key () {

	mapsvg_check_nonce();

    $maps_api_key = trim($_POST['maps_api_key']);
    $geocoding_api_key = trim($_POST['geocoding_api_key']);
    if($maps_api_key){
        update_option('mapsvg_google_api_key', $maps_api_key);
    }
    if($geocoding_api_key){
        update_option('mapsvg_google_geocoding_api_key', $geocoding_api_key);
    }

    echo '{"ok":true}';

    die();
}
add_action('wp_ajax_mapsvg_save_google_api_key', 'mapsvg_save_google_api_key');

function mapsvg_geocoding ($address, $return_as_array = true, $convert_latlng_to_address = true) {
    global $geocoding_quota_per_second, $google_geocode_api_key, $permanent_error;

    if(empty($address)){
        return false;
    }

    if(isset($permanent_error)){
        return $return_as_array ? json_decode($permanent_error, true) : $permanent_error;
    }
    if(!$geocoding_quota_per_second){
        $geocoding_quota_per_second = 1;
    }
    if(!$google_geocode_api_key){
        $google_geocode_api_key = get_option('mapsvg_google_geocoding_api_key');
        if(!$google_geocode_api_key){
            $google_geocode_api_key = get_option('mapsvg_google_api_key');
        }
    }

    $address_is_coordinates = false;
    $reg_latlng = "/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/";
    if(preg_match($reg_latlng, $address)){
	    $address_is_coordinates = true;
	    $coords = explode(",", $address);
	    $coords[0] = trim($coords[0]);
	    $coords[1] = trim($coords[1]);
	    $coords_item = array(
				    "geometry" => array("location" => array("lat"=>$coords[0], "lng"=>$coords[1])),
				    "formatted_address" => $address,
                    "address_components" => array()
			    );
    }

    if((!$address_is_coordinates || $convert_latlng_to_address === true) && $google_geocode_api_key) {
	    if ( $geocoding_quota_per_second > 49 ) {
		    sleep( 1 );
		    $geocoding_quota_per_second = 1;
	    }
	    $address = urlencode( $address );
	    // TODO if $_GET['language'] is not set then read lang from the map settings (if it's import from CSV)
	    $lang = isset($_REQUEST['language']) ? $_REQUEST['language'] : 'en';
	    $country = isset($_REQUEST['country']) ? '&components=country:'.$_REQUEST['country'] : '';

	    $data    = url_get_contents( 'https://maps.googleapis.com/maps/api/geocode/json?key=' . $google_geocode_api_key . '&address=' . $address . '&sensor=true&language=' . $lang . $country);
	    if ( $data && !isset($data['error_message'])) {
		    $response = json_decode( $data['body'], true );
		    if ( $response['status'] === 'OVER_DAILY_LIMIT' || $response['status'] === 'OVER_QUERY_LIMIT' ) {
			    $permanent_error = $data;
		    } else {
			    if($address_is_coordinates){
				    array_unshift($response['results'], $coords_item);
			    }
            }
	    } else {
		    $response = $data;
	    }
    } else {
        if($address_is_coordinates){
            $response = array(
	                "status"  => "OK",
                    "results" => array($coords_item)
            );
        } else {
	        $response = array('status'=>'NO_API_KEY', 'error_message' => 'No Google Geocoding API key. Add the key on MapSVG start screen.');
        }
    }
	return $return_as_array ? $response : json_encode($response);
}
function ajax_mapsvg_geocoding () {
    // TODO check for nonce
    echo mapsvg_geocoding($_GET['address'], false);
    die();
}
add_action('wp_ajax_mapsvg_geocoding', 'ajax_mapsvg_geocoding');
add_action('wp_ajax_nopriv_mapsvg_geocoding', 'ajax_mapsvg_geocoding');

function url_get_contents ($url) {

    if (function_exists('curl_exec')){
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        $url_get_contents_data = (curl_exec($conn));
        curl_close($conn);
        $response = array("body"=>$url_get_contents_data, "status"=>"OK");
        return $response;
    } else if(ini_get('allow_url_fopen')){
        $url_get_contents_data = file_get_contents($url);
	    $response = array("body"=>$url_get_contents_data, "status"=>"OK");
	    return $response;
    }else{
//        $url_get_contents_data = json_encode(array('status'=>'PHP_SETTINGS_ERROR','error'=>'Can\'t connect to the remote server. Please enable "allow_url_fopen" option in php.ini settings or install cURL.'));
	    $response = array("body"=>"","status"=>"PHP_SETTINGS_ERROR", "error_message"=>'Can\'t connect to the remote server. Please enable "allow_url_fopen" option in php.ini settings or install cURL.');
	    return $response;
    }
}



function mapsvg_download_google_map(){

    $url = MAPSVG_MAPS_UPLOADS_URL.'mapsvg.svg?nocache='.rand();

    $response = url_get_contents($url);

	if($response && !isset($response['error_message'])){
		header('Content-type: image/svg+xml');
		header("Content-Disposition: attachment; filename=mapsvg.svg");
		echo $response['body'];
	}else{
		echo json_encode($response);
		die();
	}
}

function mapsvg_save_purchase_code(){

	mapsvg_check_nonce();

	$code = $_POST['purchase_code'];

	$response = url_get_contents('https://mapsvg.com/wp-updates/?action=info&purchase_code='.$code);

	if($response && isset($response['body'])){
		$data = json_decode($response['body'], true);
		if(isset($data['error'])){
			$response['error_message'] = $data['error'];
        }
    }

	if($response && !isset($response['error_message'])){
		update_option('mapsvg_purchase_code', $code);
		echo '{"ok": 1}';
	} else {
		echo json_encode($response);
	}
	die();

}
add_action('wp_ajax_mapsvg_save_purchase_code', 'mapsvg_save_purchase_code');

function mapsvg_shortcode() {
    $shortcode = stripslashes($_REQUEST['shortcode']);
	echo do_shortcode( $shortcode );
	exit;
}
add_action('wp_ajax_mapsvg_shortcode', 'mapsvg_shortcode');
add_action('wp_ajax_nopriv_mapsvg_shortcode', 'mapsvg_shortcode');

function mapsvg_serialize_corrector($serialized_string){
	// at first, check if "fixing" is really needed at all. After that, security checkup.
	if ( @unserialize($serialized_string) !== true &&  preg_match('/^[aOs]:/', $serialized_string) ) {
		$serialized_string = preg_replace_callback( '/s\:(\d+)\:\"(.*?)\";/s',    function($matches){return 's:'.strlen($matches[2]).':"'.$matches[2].'";'; },   $serialized_string );
	}
	return $serialized_string;
}
function mapsvg_getMetaOptions($map_id){
	global $wpdb;
	$r = $wpdb->get_row("
              SELECT meta_value FROM ".$wpdb->postmeta." WHERE post_id='".$map_id."' AND meta_key = 'mapsvg_options'
            ");
	if($r){
		$data = unserialize(mapsvg_serialize_corrector($r->meta_value));
    } else {
	    $data = array();
    }
	return $data;
}

/*
add_filter( 'query_vars', 'analytics_rewrite_add_var' );
function analytics_rewrite_add_var( $vars )
{
	$vars[] = 'analytic';
	return $vars;
}
function add_analytic_rewrite_rule(){
	add_rewrite_tag( '%mapz%', '([^&]+)' );
	add_rewrite_rule(
//		'^test/([^/]*)/?',
//		'index.php?test=$matches[1]',
		'^mapz',
		'index.php?mapz=$matches[1]',
		'top'
	);
}
add_action('init', 'add_analytic_rewrite_rule');
add_action( 'template_redirect', 'analytics_rewrite_catch' );
function analytics_rewrite_catch()
{
	global $wp_query;

	if ( array_key_exists( 'mapz', $wp_query->query_vars ) ) {
		echo do_shortcode($_GET['test']);
		exit;
	}
}
*/

function mapsvg_check_nonce($nonce = '', $dont_die = false){
    $nonce = $nonce ? $nonce : $_REQUEST['_wpnonce'];
	if(wp_verify_nonce($nonce, 'mapsvg')){
	    return true;
	} else {
		if(!$dont_die){
			die();
		}
    }
}

function mapsvg_log($data, $label = '', $type = MAPSVG_INFO){
    if(MAPSVG_DEBUG){
        fb($data, $label, $type);
    }
}

?>
