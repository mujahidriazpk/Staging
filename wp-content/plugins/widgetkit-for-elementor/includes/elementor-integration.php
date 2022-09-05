<?php

namespace Elementor;

if( ! defined( 'ABSPATH' ) ) exit();

class WKFE_Elementor_Category {

    private static $instance = null;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        $this->elementor_integration();
        $this->elementor_deprecate_integration();
    }

    public function elementor_integration(){
        Plugin::instance()->elements_manager->add_category(
            'widgetkit_elementor',
            [
                'title'  => 'WidgetKit Elements',
                'icon'   => 'eicon-font'
            ],
            1
        );
    }
    public function elementor_deprecate_integration(){
        Plugin::instance()->elements_manager->add_category(
            'widgetkit_deprecated_elements',
            [
                'title'  => 'WidgetKit Legacy Elements',
                'icon'   => 'eicon-font'
            ],
            1
        );
    }

}
return WKFE_Elementor_Category::init();



