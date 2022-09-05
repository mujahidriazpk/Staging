<?php

if( ! defined( 'ABSPATH' ) ) exit();

class WKFE_Dependency{

    private static $instance;
    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }
    public function __construct(){
        if (!did_action('elementor/loaded')) {
            add_action( 'admin_notices', array($this, 'check_dependend_plugin_elementor') );
        }
    }

    public function check_dependend_plugin_elementor(){
        if (!current_user_can('activate_plugins')) {
            return;
        }
        $elementor_main_file = 'elementor/elementor.php';

        if(! $this->get_all_plugin_in_my_site($elementor_main_file)){
            /**
             * if plugin not found
             */
            $activation_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
            $message = sprintf(__('<strong>Widgetkit for Elementor</strong> requires <strong>Elementor</strong> plugin to be installed and activated. Please install <strong>Elementor</strong> to continue.', 'widgetkit-for-elementor'), '<strong>', '</strong>');
            $button_text = __('Install Elementor Now', 'widgetkit-for-elementor');
        }else{
            /**
             * if found
             */
            $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $elementor_main_file . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor_main_file);
            $message = __('<strong>Widgetkit for Elementor</strong> requires <strong>Elementor</strong> plugin to be active. Please activate Elementor to continue.', 'widgetkit-for-elementor');
            $button_text = __('Activate Elementor Now', 'widgetkit-for-elementor');
        }
        $button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
        printf('<div class="error"><p>%1$s</p>%2$s</div>', __($message), $button);
    }

    public function get_all_plugin_in_my_site($plugin_base_name){
        $installed_plugins = get_plugins();
        return isset($installed_plugins[$plugin_base_name]);
    }



}



