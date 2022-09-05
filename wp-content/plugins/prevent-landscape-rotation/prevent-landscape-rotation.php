<?php
/**
 * Plugin Name: Prevent Landscape Rotation
 * Description: Prevent Landscape Rotation On Mobile Devices
 * Author:      Arul Prasad J
 * Author URI:  https://profiles.wordpress.org/arulprasadj/
 * Plugin URI:  https://wordpress.org/plugins/prevent-landscape-rotation/
 * Text Domain: prevent-landscape-rotation
 * Domain Path: /languages/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Version:     2.0
 */

/*
Copyright (C)  2020-2021 arulprasadj

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

*/
// Quit, if now WP environment.
defined( 'ABSPATH' ) || exit;

define( 'APJ_PLR_VERSION', '2.0' );

define( 'APJ_PLR_REQUIRED_WP_VERSION', '4.5' );

define( 'APJ_PLR_PLUGIN', __FILE__ );

define( 'APJ_PLR_PLUGIN_NAME', 'Prevent Landscape Rotation');

define( 'APJ_PLR_PLUGIN_PATH', 'prevent-landscape-rotation.php');

define( 'APJ_PLR_MENU_SLUG', 'prevent-landscape-rotation/public/admin/adminpage.php' );

define( 'APJ_PLR_DEFAULT_MESSAGE', 'We don\'t support landscape mode yet. Please go back to portrait mode for the best experience.');

define( 'APJ_PLR_OPT_MESSAGE', 'apj_plr_message');

define( 'APJ_PLR_OPT_BG_COLOR_CODE', 'apj_plr_bg_clr_code');

define( 'APJ_PLR_OPT_TXT_COLOR_CODE', 'apj_plr_txt_clr_code');

define( 'APJ_PLR_OPT_ERR_NAME', 'apj_plr_admin_error');

if (!defined('APJ_PLR_PLUGIN_IMAGES_PATH')) {
  define('APJ_PLR_PLUGIN_IMAGES_PATH', plugins_url('/public/assets/images/', plugin_basename(__FILE__)));
}
if (!defined('APJ_PLR_PLUGIN_JS_PATH')) {
  define('APJ_PLR_PLUGIN_JS_PATH', plugins_url('/public/assets/js/', plugin_basename(__FILE__)));
}

require_once plugin_dir_path(__FILE__) . 'public/apj-functions.php';

//Activate plugin
register_activation_hook(__FILE__, array('apjPLR\PreventLandscapeRotation', 'activate'));

//Uninstall plugin
register_uninstall_hook(__FILE__, array('apjPLR\PreventLandscapeRotation', 'uninstall'));

//Init hooks
\apjPLR\PreventLandscapeRotation::initHooks();
