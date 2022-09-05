<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://linksoftwarellc.com
 * @since             2.0.0
 * @package           Wp_Terms_Popup
 *
 * @wordpress-plugin
 * Plugin Name:       WP Terms Popup
 * Plugin URI:        https://termsplugin.com
 * Description:       Ask users to agree to a popup before they are allowed to view your site.
 * Version:           2.5.1
 * Author:            Link Software LLC
 * Author URI:        https://linksoftwarellc.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-terms-popup
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('WP_TERMS_POPUP_VERSION', '2.5.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-terms-popup-activator.php.
 */
function activate_wp_terms_popup()
{
    require_once plugin_dir_path(__FILE__).'includes/class-wp-terms-popup-activator.php';
    Wp_Terms_Popup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-terms-popup-deactivator.php.
 */
function deactivate_wp_terms_popup()
{
    require_once plugin_dir_path(__FILE__).'includes/class-wp-terms-popup-deactivator.php';
    Wp_Terms_Popup_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_terms_popup');
register_deactivation_hook(__FILE__, 'deactivate_wp_terms_popup');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__).'includes/class-wp-terms-popup.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_terms_popup()
{
    $plugin = new Wp_Terms_Popup();
    $plugin->run();
    return $plugin;
}
$wp_terms_popup = run_wp_terms_popup();
