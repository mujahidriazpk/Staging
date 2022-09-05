<?php

if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 * Settings Admin Class
 */

Class SWF_Settings{

    /* Display Option Setting */
    private static $display_option = [
        'id' => 'product_favourites_display_option',
        'value' => null
    ];

    private static $auto_add_option = [
        'id' => 'purchased_products_auto_favourite'
    ];

    /*
        Initialize settings
    */
    public static function init(){

        // Add settings page
        add_filter( 'woocommerce_get_sections_products', array( __CLASS__, 'add_settings_link' ), 10, 1 );
        add_filter( 'woocommerce_get_settings_products', array( __CLASS__, 'add_settings_view'), 20, 2 );

        // Flush rewrite rules on save
        add_action( 'woocommerce_update_options_products_favourites', 'flush_rewrite_rules' );

    }

    /*
        Add link to settings
    */
    public static function add_settings_link( $sections ){
        $sections['favourites'] = 'Favourites Settings';
        return $sections;
    }

    /*
        Add the settings content
    */
    public static function add_settings_view( $settings, $current_section ){

        if( 'favourites' !== $current_section ){
            return $settings;
        }

        $settings = array(

            array(
                'title' => 'Favourites Settings',
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'product_favourites_settings',
            ),

            // Display Options
            array(
                'title'    => 'Display Type',
                'desc'     => 'Select how the plugin will display your favourites. By default this is manual with a shortcode - but you can set the plugin to display favourites automatically under the WooCommerce account page',
                'desc_tip' => true,
                'id'       => self::$display_option['id'],
                'type'     => 'radio',
                'default'  => 'shortcode',
                'options'  => array(
                    'shortcode' => 'Display using [simple_print_favourites] shortcode',
                    'account' => "Automatically add a 'Favourites' tab to the WooCommerce account"
                )
            ),

            // Automatic add option
            array(
                'title'    => 'Add purchased products',
                'desc'     => "Automatically add purchased products to favourites",
                'id'       => self::$auto_add_option['id'],
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

            array(
                'type'  => 'sectionend',
                'id'    => 'product_favourites_settings'
            )

        );

        return $settings;

    }

    /*
        Get selected display setting
    */
    public static function get_display_option(){
        if( ! empty( self::$display_option['value'] ) ){
            return self::$display_option['value'];
        }
        self::$display_option['value'] = get_option( self::$display_option['id'] );
        if( empty( self::$display_option['value'] ) ){
            self::$display_option['value'] = 'shortcode';
        }
        return self::$display_option['value'];
    }

    /*
        Get Auto Add setting
    */
    public static function get_auto_add_option(){
        $auto_add = get_option( self::$auto_add_option['id'] );
        if( 'yes' == $auto_add ){
            return true;
        }
        return false;
    }

}
SWF_Settings::init();