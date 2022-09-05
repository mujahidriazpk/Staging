<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$demo_listing;

$product_id = trim( $demo_listing );
global $wpdb,$monday,$thursday,$flash_cycle_start,$flash_cycle_end;
$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$product_id));
$_auction_dates_to = get_post_meta($product_id, '_auction_dates_to', true );
if(strtotime($_auction_dates_to) <= strtotime(date('Y-m-d H:i'))){
	$timezone = getTimeZone_Custom();
	date_default_timezone_set($timezone);
	$today_date_time = date('Y-m-d H:i');
	$this_monday = date("Y-m-d",strtotime("monday this week"))." 09:30";
	if ($today_date_time < $this_monday) {
		$monday = $this_monday;
	}
	update_post_meta( $product_id, '_auction_dates_from', $monday );
	update_post_meta( $product_id, '_auction_dates_to', $thursday );
	update_post_meta( $product_id, '_flash_cycle_start', $flash_cycle_start );
	update_post_meta( $product_id, '_flash_cycle_end', $flash_cycle_end );
	delete_post_meta($product_id, '_auction_fail_email_sent', 1);
	delete_post_meta($product_id, '_auction_finished_email_sent', 1);
	delete_post_meta($product_id, '_auction_fail_reason', 1);
	delete_post_meta($product_id, '_auction_closed', 1);
	update_post_meta( $product_id, '_auction_relist_expire','no');
	update_post_meta( $product_id, '_flash_status','no');
	update_post_meta( $product_id, '_auction_has_started',0);
	

	$fourRandomDigit = rand(1000,9999);
	$auction_no = 'CA91604-'.date('Y-md',strtotime($monday))."-".$fourRandomDigit."-".str_pad($product_id,4,'0', STR_PAD_LEFT);
	update_post_meta( $product_id, 'auction_#', $auction_no);
	
	$my_post = array('ID'           => $product_id,'post_status'   => 'publish',);
				 
	// Update the post into the database
	  wp_update_post( $my_post );
}     
?>