<?php

class WKFE_Admin_Resources{
    private static $instance = null;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        add_action( 'elementor/editor/before_enqueue_scripts', array($this, 'style_resources') );
    }
    public function style_resources(){
        wp_enqueue_style( 'widgetkit_admin_resource', WK_URL.'dist/css/widgetkit-admin.css', array(), WK_VERSION, 'all');
    }
}
?>