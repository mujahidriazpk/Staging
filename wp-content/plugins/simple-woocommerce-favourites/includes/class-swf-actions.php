<?php

if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 * Post and Ajax Hooks
 */

Class SWF_Actions{

    /*
        Attach Hooks
    */
    public static function hooks(){

        // Add favourite
        add_action( "wp_ajax_simple_ajax_add_to_favourites", array( __CLASS__, "add_favourite" ) );
        add_action( "wp_ajax_nopriv_simple_ajax_add_to_favourites", array( __CLASS__, "add_favourite" ) );

        // Remove Favourite
        add_action( "wp_ajax_simple_ajax_remove_from_favourites", array( __CLASS__, "remove_favourite" ) );
        add_action( "wp_ajax_nopriv_simple_ajax_remove_from_favourites", array( __CLASS__, "remove_favourite" ) );

        if( SWF_Settings::get_auto_add_option() ){
            add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'add_order_favourites' ) );
        }

    }

    /*
        Ajax call to add Favourite
    */
    public static function add_favourite(){
        check_ajax_referer('simple_favourites_nonce', 'simple_favourites_nonce');
        $prod_id        = sanitize_text_field( $_POST['prod_id'] );
        $favourites     = swf_get_favourites();
        if( in_array( $prod_id, $favourites ) ){
            echo 'This item is already in your favorites.';
        }
        else{
            swf_add_favourite( $prod_id, $favourites, true );
            echo 'This item has been added to your favorites.';
        }
        die();
    }

    /*
        Ajax call to Remove Favourite
    */
    public static function remove_favourite(){
        check_ajax_referer('simple_favourites_nonce', 'simple_favourites_nonce');
        $prod_id = (int)sanitize_text_field($_POST['prod_id']);
        swf_remove_favourite( $prod_id );
        echo true;
        die();
    }

    /*  
        Add Order Products to Favourites
    */
    public static function add_order_favourites( $order_id ){
        $order      = wc_get_order( $order_id );
        $items      = $order->get_items();
        $favourites = swf_get_favourites();
        foreach( $items as $item ){
            $product_id = $item->get_product_id();
            $favourites = swf_add_favourite( $product_id, $favourites );
        }
        swf_update_favourites( $favourites );
    }

}
SWF_Actions::hooks();