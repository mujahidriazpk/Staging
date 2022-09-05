<?php

if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 * Favourites Display
 */

Class SWF_Display{

    /*
        Init Display
    */
    public static function init(){

        add_shortcode( 'simple_print_favourites', array( __CLASS__, 'print_favourites' ) );

        /* Support for both english spellings */
        add_shortcode( 'simple_print_favorites', array( __CLASS__, 'print_favourites' ) );

        // Account tab display
        add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_nav_item' ), 10, 2 );                          
        add_action( 'init', array( __CLASS__, 'add_endpoint' ) );
        add_action( 'woocommerce_account_favourites_endpoint', array( __CLASS__, 'account_display' ) );

    }

    /*
        Print the display
    */
    public static function print_favourites( $atts ){
            
        extract( shortcode_atts( array(
            'user_id' => false
        ), $atts ) );

        add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'remove_button' ), 10 );

        $favourites = swf_get_favourites($user_id);
        ob_start();
            Simple_Woocommerce_Favourites::view( 'favourites-template', array( 'favourites' => $favourites ) );
        $view = ob_get_clean();
        return $view;

    }

    /*
        Display the 'Remove' button
    */
    public static function remove_button(){
        global $product;
        echo '<button class="swf_remove_from_favourites" data-product_id="'. $product->id .'">Remove</button>';
    }

    /*  
        Add account nav item
    */
    public static function add_nav_item( $items, $endpoints ){

        if( 'account' === SWF_Settings::get_display_option() ){
            $new_items = array_slice( $items, 0, array_search( 'customer-logout', array_keys( $items ) ), true ) +
            array( 'favourites' => 'Favourites' ) +
            array_slice( $items, array_search( 'customer-logout', array_keys( $items ) ), count($items ) );
            return $new_items;
        }

        return $items;

    }

    /*
        Add account endpoint
    */
    public static function add_endpoint(){

        if( 'account' === SWF_Settings::get_display_option() ){
            add_rewrite_endpoint( 'favourites', EP_PAGES );
        }

    }

    /*
        Account Page Display
    */
    public static function account_display(){
        echo do_shortcode('[simple_print_favourites]');
    }

}
SWF_Display::init();