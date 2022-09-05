<?php
define('WP_USE_THEMES', true);
require(dirname(__FILE__) . '/wp-load.php');
global $wpdb;
$code= $_POST['code'];
$price= $_POST['price'];
// first check if data exists with select query
$result = $wpdb->get_results("SELECT * FROM wp_posts WHERE `post_title` = '".trim($code)."' and post_type='shop_coupon' and post_status='publish'");
if($wpdb->num_rows > 0) {
	$post_id = $result[0]->ID;
	$term_list = wp_get_post_terms( $post_id, 'coupon_category', array( 'fields' => 'ids' ) );
	if(in_array('127',$term_list)){
		$discount_type= get_post_meta($post_id, 'discount_type', true );
		$coupon_amount= get_post_meta($post_id, 'coupon_amount', true );
		$usage_count= get_post_meta($post_id, 'usage_count', true );
		$date_expires= get_post_meta($post_id, 'date_expires', true );
		$expiry_date= get_post_meta($post_id, 'expiry_date', true );
		if($discount_type=='percent'){
			$discount = ($price * $coupon_amount)/100;
			$after_discount = $price - $discount;
			echo $after_discount;
		}
	}else{
		echo 'error';
	}
}else{
	echo 'error';
}
?>