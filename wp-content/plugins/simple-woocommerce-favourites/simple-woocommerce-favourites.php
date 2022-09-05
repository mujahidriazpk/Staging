<?php
/**
 * Plugin Name: Simple Woocommerce Favourites
 * Plugin URI: https://simplistics.ca
 * Description: Manages a simple list of favourite products for each user and displays it with a shortcode
 * Version: 2.1
 * Author: Jonathan Boss
 * Author URI: https://simplistics.ca
 */

define( 'SWF_BASE_FILE', __FILE__ );

if( !class_exists( 'Simple_Woocommerce_Favourites' ) ){
    require_once 'includes/class-simple-woocommerce-favourites.php';
}