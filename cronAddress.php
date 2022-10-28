<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$demo_listing;
$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}usermeta WHERE meta_key like '%_future%' and meta_value !=''", OBJECT );
date_default_timezone_set('America/Los_Angeles');
$monday_this_week = date("Y-m-d",strtotime( "monday this week" ))." 08:30";
$flash_cycle_end = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
$today_date_time = date('Y-m-d H:i');
if ($today_date_time >= $monday_this_week && $today_date_time <= $flash_cycle_end) {
	foreach($results as $result){
		//print_r($result);
		//echo $result->user_id."==".str_replace("_future","",$result->meta_key)."==".htmlentities($result->meta_value);
		update_user_meta($result->user_id,str_replace("_future","",$result->meta_key), htmlentities($result->meta_value));
		update_user_meta($result->user_id,str_replace("_future","_old_address",$result->meta_key), htmlentities($result->meta_value));
		//update_user_meta($result->user_id,$result->meta_key,htmlentities($result->meta_value));
		delete_user_meta($result->user_id,$result->meta_key);
	}
}
?>