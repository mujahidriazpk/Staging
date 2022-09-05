<?php
/*
Plugin Name: All-in-One Addons for Elementor - WidgetKit
Description: Everything you need to create a stunning website with <strong>Elementor, WooCommerce, LearnDash, Sensei & LearnPress</strong> and more.
Version: 2.3.16.1
Text Domain: widgetkit-for-elementor
Author: Themesgrove
Author URI: https://themesgrove.com
Plugin URI: https://themesgrove.com/widgetkit-for-elementor/
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
@package  WidgetKit_For_Elementor
Domain Path: /languages
WC requires at least: 3.0.0
WC tested up to: 3.8.0
*/

    /**
     * Define absoulote path
     * No access of directly access
     */
    if( !defined( 'ABSPATH' ) ) exit; 

    define('WK_VERSION', '2.3.16.1');
    define('WK_FILE', __FILE__); 
    define('WK_URL', plugins_url('/', __FILE__ ) );
    define('WK_PATH', plugin_dir_path( __FILE__ ) );


    class WidgetKit_For_Elementor {
        
        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'plugin_setup' ) );
            add_action( 'elementor/init', array( $this, 'elementor_init' ) );
            add_action( 'init', array( $this, 'elementor_resources' ), -999 );
            add_action('admin_head', array($this, 'remove_all_admin_notice'));
            add_filter( 'elementor/utils/get_placeholder_image_src', [ __CLASS__, 'wk_placeholder_image' ] );
            require_once(WK_PATH . 'vendor/autoload.php');
            if(!class_exists('Parsedown')){
                require_once(WK_PATH . 'vendor/erusev/parsedown/Parsedown.php');
            }
        }

        public static function wk_placeholder_image() {
            return WK_URL . 'dist/images/placeholder.jpg';
        }

        public function activate(){
            flush_rewrite_rules();
        }
        public function deactivate(){
            flush_rewrite_rules();
        }

        public function plugin_setup() {
            $this->load_text_domain();
            $this->load_admin_files();
            if(is_admin()){
                $this->check_dependency();
            }
        }
        public function load_admin_files() {
            require_once(WK_PATH. 'includes/appsero-init.php');
            require_once(WK_PATH. 'includes/widgetkit-pro-init.php');
            require_once(WK_PATH. 'includes/elements.php');
            require_once(WK_PATH. 'includes/widgetkit-admin-resources.php');
            
            WKFE_Appsero_Init::init();
            WKFE_PRO_Init::init();
            WKFE_Elements::init();
            WKFE_Admin_Resources::init();
        }
        public function load_text_domain() {
            load_plugin_textdomain( 'widgetkit-for-elementor' );
        }

        public function elementor_init(){
            require_once ( WK_PATH . 'includes/elementor-integration.php' );

        }
        public function elementor_addons() {
            require_once ( WK_PATH . 'includes/addons-integration.php' );
            WKFE_Addons_Integration::init();
        }
        public function elementor_resources() {
            $this->elementor_addons();
        }

        public function check_dependency(){
            require_once(WK_PATH. 'includes/dependency.php');
            WKFE_Dependency::init();
        }
        public function remove_all_admin_notice($hook){
            global $wp;  
            $current_url = add_query_arg(array($_GET), $wp->request);
            $current_url_slug = explode("=", $current_url);
            if(count($current_url_slug) > 1):
            if($current_url && $current_url_slug[1] === 'widgetkit-settings'){
                if (is_super_admin()) {
                    remove_all_actions('admin_notices');
                }
            }
            endif;
        }

    }

    if (class_exists('WidgetKit_For_Elementor')) {
        $widgetkit_for_elementor = new WidgetKit_For_Elementor();
    }

    register_activation_hook( __FILE__, array($widgetkit_for_elementor, 'activate' ));
    register_deactivation_hook( __FILE__, array($widgetkit_for_elementor, 'deactivate' ));


    
