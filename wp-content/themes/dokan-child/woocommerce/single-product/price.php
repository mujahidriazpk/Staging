<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post,$_auction_start_price;
$product_id = $post->ID;
$current_user = wp_get_current_user();
$_auction_current_bid = get_post_meta($product_id, '_auction_current_bid', true );
$_auction_closed = get_post_meta($product_id, '_auction_closed', true );
$message_update= "";
if(isset($_POST['mode']) && $_POST['mode']=='update'){
	update_post_meta( $product_id, '_auction_maximum_travel_distance', $_POST['_auction_maximum_travel_distance'] );
	$_auction_start_price = get_post_meta( $product_id, '_auction_start_price',TRUE);
	if($_POST['_new_price'] == $_auction_start_price){
		$message_update = "<p style='color:red;'>Asking price should be greater than current price.</p>";
	}else{
		update_post_meta( $product_id, '_auction_start_price', $_POST['_new_price']);
		$message_update = "<p style='color:green;'>Asking price has been successfully updated.</p>";
	}
	$_auction_start_price = get_post_meta( $product_id, '_auction_start_price',TRUE);
	$message_update = "<p style='color:green;'>Travel Miles has been successfully updated to ".$_POST['_auction_maximum_travel_distance']." Miles.</p>";
}
if (isset($_POST['mode']) && $_POST['mode']=='update_price') {
	
	$_auction_start_price = get_post_meta( $product_id, '_auction_start_price',TRUE);
	if($_POST['_new_price'] == $_auction_start_price){
		$message_update = "<p style='color:red;'>Asking price should be greater than current price.</p>";
	}else{
		update_post_meta( $product_id, '_auction_start_price', $_POST['_new_price']);
		$message_update = "<p style='color:green;'>Asking price has been successfully updated.</p>";
	}
	$_auction_start_price = get_post_meta( $product_id, '_auction_start_price',TRUE);
}
?>