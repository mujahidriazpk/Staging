<?php

if( ! defined( 'ABSPATH' ) ) exit();

class WKFE_Addons_Integration{



    private static $instance;
    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        // add_action( 'elementor/preview/enqueue_styles', array( $this, 'widgetkit_enqueue_preview_styles' ) );
        add_action( 'elementor/frontend/after_register_styles', array( $this, 'widgetkit_register_frontend_styles' ) );
        add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widgetkit_register_frontend_scripts' ) );
    }
    
    public function widgetkit_register_frontend_styles(){
        wp_register_style( 'widgetkit_bs', WK_URL.'dist/css/bootstrap.css', array(), WK_VERSION, 'all');
        wp_register_style( 'owl-css', WK_URL.'dist/css/owl.carousel.min.css', array(), WK_VERSION, 'all');
        wp_register_style( 'animate-text', WK_URL.'dist/css/animate-text.css', array(), WK_VERSION, 'all');
        wp_register_style( 'animate-css', WK_URL.'dist/css/animate.min.css', array(), WK_VERSION, 'all');
        wp_register_style( 'fontawesome', WK_URL.'dist/css/font-awesome.min.css', array(), WK_VERSION, 'all');
        wp_register_style( 'ionsicon', WK_URL.'dist/css/ionicons.min.css', array(), WK_VERSION, 'all');
        wp_register_style( 'uikit', WK_URL.'dist/css/uikit.custom.min.css', array(), WK_VERSION, 'all');
        wp_register_style( 'widgetkit_main', WK_URL.'dist/css/widgetkit.css', array(), WK_VERSION, 'all');
    }
    
    public function widgetkit_enqueue_preview_styles() {
        wp_enqueue_style('owl-css');
        wp_enqueue_style('animate-css');
        wp_enqueue_style('fontawesome');
        wp_enqueue_style('ionsicon');
    }

    public function widgetkit_register_frontend_scripts(){
        wp_register_script( 'bootstarp-js', WK_URL.'dist/js/bootstrap.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'owl-carousel', WK_URL.'dist/js/owl.carousel.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'hoverdir', WK_URL.'dist/js/jquery.hoverdir.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'modernizr', WK_URL.'dist/js/modernizr.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'animate-text', WK_URL.'dist/js/animate-text.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'mixitup-js', WK_URL.'dist/js/mixitup.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'anime-js', WK_URL.'dist/js/anime.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'widgetkit-imagesloaded', WK_URL.'dist/js/imagesloaded.pkgd.min.js', array('jquery'), WK_VERSION, true);
        wp_register_script( 'widgetkit-slider', WK_URL.'dist/js/slider-3.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'countdown', WK_URL.'dist/js/countdown.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'lottie-js', WK_URL.'dist/js/lottie.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'widgetkit-main', WK_URL.'dist/js/widgetkit.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'uikit-js', WK_URL.'dist/js/uikit.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'uikit-icons', WK_URL.'dist/js/uikit-icons.min.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'event-move', WK_URL.'dist/js/jquery.event.move.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'image-compare', WK_URL.'dist/js/jquery.image-compare.js' , array('jquery'), WK_VERSION, true);
        // wp_register_script( 'vanilla-tilt', WK_URL.'dist/js/vanilla-tilt.js' , array('jquery'), WK_VERSION, true);
        wp_register_script( 'youtube-popup', WK_URL.'dist/js/youtube-popup.js' , array('jquery'), WK_VERSION, true);
        // wp_register_script( 'magnific-popup', WK_URL.'dist/js/jquery.magnific-popup.js' , array('jquery'), WK_VERSION, true);
        $js_info = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'wkfe_security_nonce' => wp_create_nonce('wkfe-ajax-security-nonce')
        ];
        wp_localize_script('widgetkit-main', 'wkfelocalizesettings', $js_info);
    }

}

