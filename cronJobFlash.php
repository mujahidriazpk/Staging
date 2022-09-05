<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$today_date_time;
$args = array(
				'post_status'         => array('publish','pending'),
				'posts_per_page'      => -1,
				'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
				'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
				//'auction_archive'     => TRUE,
				//'show_past_auctions'  => TRUE,
              );
		$query = new WP_Query($args );
		$posts = $query->posts;
foreach($posts as $post) {
	global $today_date_time;
	$_flash_cycle_start = get_post_meta($post->ID, '_flash_cycle_start' , TRUE);
	$_flash_cycle_end = get_post_meta($post->ID, '_flash_cycle_end' , TRUE);
	if(strtotime($today_date_time) > strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
		//echo $post->ID."<br />";
		$product_id = $post->ID;
		//$_flash_cycle_start = get_post_meta( $product_id , '_flash_cycle_start' , TRUE);
		//$_flash_cycle_end = get_post_meta( $product_id , '_flash_cycle_end' , TRUE);
		//update_post_meta( $product_id, '_auction_start_price', $_POST['_new_price']);
		update_post_meta( $product_id, '_auction_dates_from', $_flash_cycle_start);
		update_post_meta( $product_id, '_auction_dates_to', $_flash_cycle_end);
		update_post_meta( $product_id, '_flash_status','yes');
		delete_post_meta($product_id, '_auction_fail_email_sent', 1);
		delete_post_meta($product_id, '_auction_finished_email_sent', 1);
		delete_post_meta($product_id, '_auction_fail_reason', 1);
		delete_post_meta($product_id, '_auction_closed', 1);
	}
}
?>