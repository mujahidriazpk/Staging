<?php

if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 * Favourites Button Functionality
 */

Class SWF_Favourites_Button{

    public static function init(){

        // Load the button shortcode
        self::shortcode();

        //add_action( 'woocommerce_after_single_product', array( __CLASS__, 'display_button' ) );

    }

    /*
        Favourites Button Shortcode
    */
    private static function shortcode(){

        add_shortcode( 'simple_favourites_button', array( __CLASS__, 'display_button' ) );

    }

    /*
        Check if the button should be shown
    */
    private static function should_show(){
        global $product;
        if( empty( $product ) || !is_user_logged_in() ){
            return false;
        }
        $user_id    = get_current_user_id();
        $favourites = swf_get_favourites( $user_id) ;
        if( in_array($product->id, $favourites) ){ 
            return false; 
        }
        return true;
    }

    /*
        Display the button
    */
    public static function display_button(){
        if( self::should_show() ){
            Simple_Woocommerce_Favourites::view( 'add-to-favourites-button' );
        }else{
			if(is_user_logged_in() ){
				Simple_Woocommerce_Favourites::view( 'remove-favourite-button' );
			}
		}
    }


}
SWF_Favourites_Button::init();