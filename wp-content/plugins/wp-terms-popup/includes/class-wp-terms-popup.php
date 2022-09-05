<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/includes
 * @author     Link Software LLC <support@linksoftwarellc.com>
 */
class Wp_Terms_Popup
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    2.0.0
     * @access   protected
     * @var      Wp_Terms_Popup_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'wp-terms-popup';

        if (defined('WP_TERMS_POPUP_VERSION')) {
            $this->version = WP_TERMS_POPUP_VERSION;
        } else {
            $this->version = '2.0.0';
        }

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wp_Terms_Popup_Loader. Orchestrates the hooks of the plugin.
     * - Wp_Terms_Popup_i18n. Defines internationalization functionality.
     * - Wp_Terms_Popup_Admin. Defines all hooks for the admin area.
     * - Wp_Terms_Popup_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    2.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'includes/class-wp-terms-popup-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'includes/class-wp-terms-popup-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'admin/class-wp-terms-popup-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)).'public/class-wp-terms-popup-public.php';

        $this->loader = new Wp_Terms_Popup_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wp_Terms_Popup_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    2.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Wp_Terms_Popup_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    2.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Wp_Terms_Popup_Admin($this->get_plugin_name(), $this->get_version());

        // Admin Menu and Script
        $this->loader->add_action('admin_menu', $plugin_admin, 'menu');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Post Type
        $this->loader->add_action('init', $plugin_admin, 'post_type');
        $this->loader->add_action('manage_termpopup_posts_custom_column', $plugin_admin, 'post_type_manage_columns', 10, 2);
        $this->loader->add_filter('manage_edit-termpopup_columns', $plugin_admin, 'post_type_edit_columns');

        // Meta Boxes
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'meta_boxes');
        $this->loader->add_action('save_post', $plugin_admin, 'meta_boxes_save_post_type', 10, 2);
        $this->loader->add_action('save_post', $plugin_admin, 'meta_boxes_save_post', 10, 2);

        // Settings
        $this->loader->add_action('wptp_settings_tabs', $plugin_admin, 'settings_tabs');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    2.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        global $plugin_public;
        $plugin_public = new Wp_Terms_Popup_Public($this->get_plugin_name(), $this->get_version());

        // Public Scripts
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Cookie
        $this->loader->add_action('init', $plugin_public, 'set_cookie', 1);

        // Popup
        $this->loader->add_action('wp_footer', $plugin_public, 'display_check', 9999);
        $this->loader->add_action('wptp_popup_after_content', $plugin_public, 'after_content', 10);

        if (get_option('termsopt_javascript') == 1) {
            $this->loader->add_action('wp_ajax_nopriv_wptp_ajaxhandler_css', $plugin_public, 'ajaxhandler_css');
            $this->loader->add_action('wp_ajax_wptp_ajaxhandler_css', $plugin_public, 'ajaxhandler_css');
            $this->loader->add_action('wp_ajax_nopriv_wptp_ajaxhandler_popup', $plugin_public, 'ajaxhandler_popup');
            $this->loader->add_action('wp_ajax_wptp_ajaxhandler_popup', $plugin_public, 'ajaxhandler_popup');
        }

        // Shortcode
        $this->loader->add_shortcode('wpterms', $plugin_public, 'shortcode');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    2.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     2.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     2.0.0
     * @return    Wp_Terms_Popup_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     2.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
