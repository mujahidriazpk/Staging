<?php	
require(dirname(__FILE__) . '/wp-load.php');
set_time_limit ( 300 );
 function get_orders_ids_by_product_id_custom( $product_id, $order_status){
    global $wpdb;
    $results = $wpdb->get_col("
			SELECT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE posts.post_type = 'shop_order'
			AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
			AND order_items.order_item_type = 'line_item'
			AND order_item_meta.meta_key = '_product_id'
			AND order_item_meta.meta_value = '$product_id'
		");
	
		return $results;
	}
global $wpdb;
	$args = '';
	$order_status = array('wc-processing','wc-on-hold','wc-completed');
	$orders_ids_array = array();
	if(isset($_GET['service']) && $_GET['service'] !="" ){
		$orders_ids_array = $wpdb->get_col('select t1.order_id FROM wp_woocommerce_order_items as t1 JOIN wp_woocommerce_order_itemmeta as t2 ON t1.order_item_id = t2.order_item_id where t2.meta_key ="Service" and t2.meta_value ="'.$_GET['service'].'"' );
		//print_r($orders_ids_array);
		if(empty($orders_ids_array)){
			$orders_ids_array = array(0);
		}
		$result[0]['service'] = $_GET['service'];
	}
	if(isset($_GET['order_type']) && $_GET['order_type'] !="" ){
		$orders_ids_array = get_orders_ids_by_product_id_custom($_GET['order_type'],$order_status);
		$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
		$result[0]['revenue_type'] = $types[$_GET['order_type']];
	}
	$args = array(
					'post__in' 			=> $orders_ids_array ,
					'post_type' 			=> 'shop_order' ,
					'post_status'=>$order_status,
					'posts_per_page'         => '-1',
					//'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
					//'date_query' => array(array('year' => date('Y')))
					);
	if(isset($_GET['order_city']) && $_GET['order_city'] !="" ){
		$args['meta_query'][] =    array(
					'key'   => '_billing_city',
					'value'   => $_GET['order_city'],
					'compare'   => 'like',
		
				);	
				 
		$result[0]['order_city'] = $_GET['order_city'];
	}
	if(isset($_GET['order_state']) && $_GET['order_state'] !="" ){
		$args['meta_query'][] =    array(
					'key'   => '_billing_state',
					'compare'   => 'like',
					'value'   => $_GET['order_state'],
		
				);	
		$result[0]['order_state'] = $_GET['order_state'];
	}
	if(isset($_GET['order_zip_code']) && $_GET['order_zip_code'] !="" ){
		$args['meta_query'][] =    array(
					'key'   => '_billing_postcode',
					'compare'   => 'LIKE',
					'value'   => $_GET['order_zip_code'],
		
				);	
		$result[0]['order_zip_code'] = $_GET['order_zip_code'];
	}			
	if(isset($_GET['mishaDateFrom']) && $_GET['mishaDateFrom'] !="" && $_GET['mishaDateTo'] ==""){
		$args['date_query'] = array(
			array(
				'after' => $_GET['mishaDateFrom'],
				'inclusive' => false,
			)
		);	
	
	}elseif($_GET['mishaDateFrom'] =="" && isset($_GET['mishaDateTo']) && $_GET['mishaDateTo'] !=""){
		$args['date_query'] = array(
			array(
				'before' => $_GET['mishaDateTo'],
				'inclusive' => false,
			)
		);
	}elseif(isset($_GET['mishaDateFrom']) && $_GET['mishaDateFrom'] !="" && isset($_GET['mishaDateTo']) && $_GET['mishaDateTo'] !=""){
		
		$args['date_query'] = array(
			array(
				'after' => $_GET['mishaDateFrom'],
				'before' => $_GET['mishaDateTo'],
				'inclusive' => false,
			)
		);
		
	}
	//print_r($args);
	//$orders_ids_array = get_orders_ids_by_product_id( $product_id );
	
$product_query = new WP_Query( $args );
$count = $product_query->found_posts;
	
if($count > 0){
	$posts = $product_query->posts;
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Sales.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('Order #','Date','Status','Total','Type'));
	foreach($posts as $post){
		$record = array();
		$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
		$order = wc_get_order($post->ID);
		$order_ref =  get_post_meta($post->ID, 'order_ref_#', true );
		$record['Order #'] = $order_ref; 
		$record['Date'] = $post->post_title; 
		$order_status  = $order->get_status();
		$record['Status'] = $order_status; 
		$record['Total'] = "$".$order->get_total(); 
		foreach ($order->get_items() as $item_key => $item ):
			$item_name    = $item->get_name();
			$product_id   = $item->get_product_id();
			$record['Type'] = $types[$product_id]; 
		endforeach;
		fputcsv($output,$record);
		
	}
	
}
exit;
?>
