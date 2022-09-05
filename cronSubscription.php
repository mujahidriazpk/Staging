<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb;
$query = "SELECT post_id FROM wp_postmeta where meta_value = 'active_cancelled'";
$results = $wpdb->get_results($query, OBJECT);
foreach($results as $row){
	$end_date = get_post_meta($row->post_id,'end_date',true);
	if($end_date !=""){
		if(strtotime(date("Y-m-d H:i:s")) >=$end_date){
			$tstamp     = current_time( 'timestamp' );
			update_post_meta($row->post_id,'end_date',$tstamp);
			update_post_meta($row->post_id,'payment_due_date','');
			update_post_meta($row->post_id,'cancelled_date',$tstamp);
			update_post_meta($row->post_id,'status','cancelled');
			update_post_meta($row->post_id,'_plan_status','cancelled');
		}
	}
}
$query = "SELECT post_id FROM wp_postmeta where meta_value = 'active'";
$results = $wpdb->get_results($query, OBJECT);
foreach($results as $row){
	echo $end_date = get_post_meta($row->post_id,'expired_date',true);
	if($end_date !=""){
		if(strtotime(date("Y-m-d H:i:s")) >= $end_date){
		//	echo $row->post_id."==".date("Y-m-d H:i",$end_date)."<br />";
			update_post_meta($row->post_id,'status','cancelled');
			/*$tstamp     = current_time( 'timestamp' );
			update_post_meta($row->post_id,'end_date',$tstamp);
			update_post_meta($row->post_id,'payment_due_date','');
			update_post_meta($row->post_id,'cancelled_date',$tstamp);
			update_post_meta($row->post_id,'status','cancelled');
			update_post_meta($row->post_id,'_plan_status','cancelled');*/
		}
	}
}
?>