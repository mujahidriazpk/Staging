<?php	
require(dirname(__FILE__) . '/wp-load.php');
set_time_limit ( 300 );
global $wpdb;
global $demo_listing;
$post_statuses = array('publish','acf-disabled','future','draft','pending','private');
$ids = array();
if(isset($_GET['service']) && $_GET['service'] !="" ){
	$myposts = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title LIKE '%s'", '%'. $wpdb->esc_like($_GET['service']) .'%'), 'ARRAY_A');
	foreach($myposts as $mypost){
		array_push($ids,$mypost['ID']);
	}
	$result[0]['service'] = $_GET['service'];
}
$args = array(
				'post__in' 			=> $ids ,
				'post__not_in' => array($demo_listing),
				'post_status'         => $post_statuses,
				'ignore_sticky_posts' => 1,
				//'meta_key' => '_auction_dates_from',
				'orderby' => 'ID',
				'order'               => 'desc',
				'posts_per_page'      => -1,
				'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
				'auction_archive'     => TRUE,
				'show_past_auctions'  => TRUE,
			);
if(isset($_GET['auction_city']) && $_GET['auction_city'] !="" ){
$args['meta_query'] =    array(
			'key'   => 'auction_city',
			'compare'   => 'LIKE',
			'value'   => $_GET['auction_city'],

		);	
$result[0]['auction_city'] = $_GET['auction_city'];
}
if(isset($_GET['auction_state']) && $_GET['auction_state'] !="" ){
$args['meta_query'] =    array(
			'key'   => 'auction_state',
			'compare'   => 'LIKE',
			'value'   => $_GET['auction_state'],

		);	
$result[0]['auction_state'] = $_GET['auction_state'];
}
if(isset($_GET['auction_zip_code']) && $_GET['auction_zip_code'] !="" ){
$args['meta_query'] =    array(
			'key'   => 'auction_zip_code',
			'compare'   => 'LIKE',
			'value'   => $_GET['auction_zip_code'],

		);	
$result[0]['auction_zip_code'] = $_GET['auction_zip_code'];
}
if(isset($_GET['mishaDateFrom']) && $_GET['mishaDateFrom'] !="" && $_GET['mishaDateTo'] ==""){
$args['meta_query'] =    array(
			'key'   => '_auction_dates_from_org',
			'compare'   => '>=',
			'value'   => date('Ymd',strtotime($_GET['mishaDateFrom'])),
			'type'        => 'date'

		);	

}elseif($_GET['mishaDateFrom'] =="" && isset($_GET['mishaDateTo']) && $_GET['mishaDateTo'] !=""){
$args['meta_query'] = array(   
		array(
			'key'   => '_auction_dates_to',
			'compare'   => '<=',
			'value'   => date('Ymd',strtotime($_GET['mishaDateTo'])),
			'type'        => 'date'

		));	
}elseif(isset($_GET['mishaDateFrom']) && $_GET['mishaDateFrom'] !="" && isset($_GET['mishaDateTo']) && $_GET['mishaDateTo'] !=""){
$args['meta_query'] = array( 
		'relation' => 'AND',              
		array(
			'key'   => '_auction_dates_from_org',
			'compare'   => '>=',
			'value'   =>date('Ymd',strtotime($_GET['mishaDateFrom'])),
			'type'        => 'date'

		),
		array(
			'key'   => '_auction_dates_to',
			'compare'   => '<=',
			'value'   => date('Ymd',strtotime($_GET['mishaDateTo'])),
			'type'        => 'date'

		));	
}
$product_query = new WP_Query( $args );
$count = $product_query->found_posts;
if($count > 0){
	$posts = $product_query->posts;
	
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Auctions.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('Start Date','auction #','Ask Fee','City','State','Zip','Status','Service'));
	foreach($posts as $post){
		$record = array();
		$_auction_dates_from =  get_post_meta($post->ID, '_auction_dates_from_org', true );
		$record['Start Date'] = $_auction_dates_from; 
		$auction_no =  get_post_meta($post->ID, 'auction_#', true );
		$record['auction #'] = $auction_no;
		//$auction_no =  get_post_meta($post->ID, 'auction_#', true );
		$_auction_start_price = get_post_meta($post->ID, '_auction_start_price',TRUE);
		$record['Ask Fee'] = "$".abs($_auction_start_price); 
		$auction_city =  get_post_meta($post->ID, 'auction_city', true );
		$record['City'] = $auction_city;
		$auction_state =  get_post_meta($post->ID, 'auction_state', true );
		$record['State'] = $auction_state;
		$auction_zip_code =  get_post_meta($post->ID, 'auction_zip_code', true );
		$record['Zip'] = $auction_zip_code;
		
		$product = dokan_wc_get_product($post->ID);
		if($product->is_closed() === TRUE){	
				global $today_date_time;
				$_flash_cycle_start = get_post_meta( $product->get_id() , '_flash_cycle_start' , TRUE);
				$_flash_cycle_end = get_post_meta( $product->get_id() , '_flash_cycle_end' , TRUE);
				if(strtotime($today_date_time) < strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
					if(!$_auction_current_bid){
						$status = 'countdown to Flash Bid Cycle®';
						$class = " ended";
					}else{
						if($customer_winner == $user_id){
							$status = 'Email (Spam)';
						}else{
							$status = 'ended';
						}
						$class = " ended";
					}
				}else{
					if($customer_winner == $user_id){
						$status = 'Email (Spam)';
					}else{
						$status = 'ended';
					}
					$class = " ended";
				}
				/*if($customer_winner == $user_id){
					$status = 'Email (Spam)';
				}else{
					$status = 'ended';
				}*/
				$class = " ended";
		}else{
			if(($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )){
				if($post->post_status=='pending'){
					$status = 'countdown to auction';
					$class = " upcoming-pending";
				}else{
					$status = 'countdown to auction';
					$class = " upcoming";
				}
			}else{
				if($post->post_status=='pending'){
					$status = 'Live: Pending Review';
					$class = " live";
				}else{
					if ($_auction_dates_extend == 'yes') {
						$status = 'extended';
						$class = " extended";
					}else{
						if($_flash_status == 'yes'){
							$status = 'Flash Bid Cycle® live';
							$class = " live";
						}else{
							$status = 'auction live';
							$class = " live";
						}
					}
				}
			}
		}
		$record['Status'] = $status;
		$record['Service'] = $post->post_title;
		fputcsv($output,$record);
	}
}
exit;
?>
