<?php

if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 * Favourites Base Class
 */

Class Simple_Woocommerce_Favourites{

    public static $version = '2.1';
    public static $plugin_url;
    public static $plugin_path;

    /*
        Initialize Plugin
    */
    public static function init(){

        self::$plugin_url   = plugin_dir_url( SWF_BASE_FILE );
        self::$plugin_path  = plugin_dir_path( SWF_BASE_FILE );

        self::add_hooks();

        self::includes();

    }

    /*
        Load Hooks
    */
    private static function add_hooks(){

        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_assets' ) );

        add_filter( 'plugin_action_links_' . plugin_basename( SWF_BASE_FILE ), array( __CLASS__, 'plugin_links' ), 20, 1 );

    }

    /*
        Load Assets
    */
    public static function load_assets(){

        wp_register_script( 'swf_script', self::$plugin_url . 'assets/js/add-to-favourites.js', array( 'jquery' ), self::$version );
        wp_enqueue_script( 'swf_script');
        wp_localize_script( 'swf_script', 'swfAjax', array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'simple_favourites_nonce' )
        ));

        wp_enqueue_style( 'swf_styles', self::$plugin_url . 'assets/styles/swf_styles.css', array(), self::$version );

    }

    /*
        Include files
    */
    private static function includes(){

        // Core functionality
        include self::$plugin_path . 'includes/swf-core-functions.php';

        // Admin Settings
        include self::$plugin_path . 'includes/class-swf-settings.php';

        // Action hooks for Ajax and Post
        include self::$plugin_path . 'includes/class-swf-actions.php';

        // Display
        include self::$plugin_path . 'includes/class-swf-display.php';

        // Button
        include self::$plugin_path . 'includes/class-swf-favourites-button.php';

    }

    /*
        Plugin Links
    */
    public static function plugin_links( $links ){
        array_unshift( $links, "<a href='". admin_url( 'admin.php?page=wc-settings&tab=products&section=favourites' ) ."'>Settings</a>" );
        return $links;
    }

    /*
        Get a View File
    */
    public static function view( $template, $args = array() ){
        if( !empty( $template ) ){
            require self::$plugin_path . 'views/' . $template . '.php';
        }
    }

}
Simple_Woocommerce_Favourites::init();