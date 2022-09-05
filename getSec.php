<?php
define('WP_USE_THEMES', true);
require($_SERVER['DOCUMENT_ROOT'].'wp-load.php');
if($_REQUEST['future']=='yes'){
	$product = wc_get_product($_REQUEST['product_id']);
	$remaining_second = $product->get_seconds_to_auction();
}elseif($_REQUEST['future']=='future_flash'){
	$_flash_cycle_start = get_post_meta($_REQUEST['product_id'], '_flash_cycle_start' , TRUE);
	$remaining_second = apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($_flash_cycle_start)  -  (get_option( 'gmt_offset' )*3600) - time() ,  1 );
}else{
	$product = wc_get_product($_REQUEST['product_id']);
	$remaining_second = $product->get_seconds_remaining(); 
}
echo $remaining_second;
die;
?>