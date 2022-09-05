<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/includes
 * @author     Link Software LLC <support@linksoftwarellc.com>
 */
class Wp_Terms_Popup_i18n {
    /**
     * Load the plugin text domain for translation.
     *
     * @since    2.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wp-terms-popup',
            false,
            dirname(dirname(plugin_basename(__FILE__))).'/languages/'
        );
    }
}
