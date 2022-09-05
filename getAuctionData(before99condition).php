<?php
$servername = "localhost";
$database = "gddwwykpfm";
$username = "gddwwykpfm";
$password = "8cK5GTxtnS";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='checkBidUpdate'){
	$return = null;
	 if ( isset( $_REQUEST['product_id'] ) && $_REQUEST['product_id']  !="" ) {
			$result = mysqli_query($conn,"SELECT meta_value FROM wp_postmeta where post_id='".$_REQUEST['product_id']."' and meta_key = '_auction_current_bid' limit 1");
			$row = mysqli_fetch_row($result);
			 mysqli_free_result($result);
			$_auction_current_bid = $row[0];
			$curent_bid = '';
			if ($_auction_current_bid){
				$curent_bid = $_auction_current_bid;
			}else{
				$result = mysqli_query($conn,"SELECT meta_value FROM wp_postmeta where post_id='".$_REQUEST['product_id']."' and meta_key = '_auction_start_price' limit 1");
				$row = mysqli_fetch_row($result);
				 mysqli_free_result($result);
				$curent_bid = $row[0];
			}
			$result = mysqli_query($conn,"SELECT meta_value FROM wp_postmeta where post_id='".$_REQUEST['product_id']."' and meta_key = '_auction_current_bider' limit 1");
			$row = mysqli_fetch_row($result);
			 mysqli_free_result($result);
			 $current_bider = $row[0];
			 
			 $result_auction_start_price = mysqli_query($conn,"SELECT meta_value FROM wp_postmeta where post_id='".$_REQUEST['product_id']."' and meta_key = '_auction_start_price' limit 1");
			$row_auction_start_price = mysqli_fetch_row($result_auction_start_price);
			 mysqli_free_result($result_auction_start_price);
			$_auction_start_price = $row_auction_start_price[0];
			 
			 $result_auction_maximum_travel_distance = mysqli_query($conn,"SELECT meta_value FROM wp_postmeta where post_id='".$_REQUEST['product_id']."' and meta_key = '_auction_maximum_travel_distance' limit 1");
			$row_auction_maximum_travel_distance = mysqli_fetch_row($result_auction_maximum_travel_distance);
			 mysqli_free_result($row_auction_maximum_travel_distance);
			$_auction_maximum_travel_distance = $row_auction_maximum_travel_distance[0];
			
			if($curent_bid !=$_REQUEST['current_bid'] && $current_bider !=""){
				define('WP_USE_THEMES', true);
				require($_SERVER['DOCUMENT_ROOT'].'wp-load.php');
				global $wpdb;
				$posts_id = $_REQUEST['product_id'];
				$product_data = wc_get_product($_REQUEST['product_id']);
				$current_user = wp_get_current_user();
				$_auction_max_current_bider = get_post_meta($_REQUEST['product_id'], '_auction_max_current_bider', true );
				$_auction_dates_extend = get_post_meta($_REQUEST['product_id'], '_auction_dates_extend', true );
				if($current_user->ID==$_auction_max_current_bider){
					$return['winner_screen']       = 'yes';
				}else{
					$return['winner_screen']       = 'no';
				}
				$return['curent_bid']       = $product_data->get_price_html();
				
				$return['curent_bid_value'] = $product_data->get_curent_bid();
				
				$return['curent_bider']     = $product_data->get_auction_current_bider();
				
				$return['curent_id']     	= $current_user->ID;
				
				$return['bid_value']        = $product_data->bid_value();
				
				$return['add_to_cart_text'] = $product_data->add_to_cart_text();
				
				$return['response_mode'] = 'update_auction';
				
				/*$auctionend = new DateTime($product_data->get_auction_dates_to());
				$auctionendformat = $auctionend->format('Y-m-d H:i:s');
				$time = current_time( 'timestamp' );
				$timeplus5 = date('Y-m-d H:i:s', strtotime('+5 minutes', $time));*/
				// if ($timeplus5 > $auctionendformat) {
					if($_auction_dates_extend =='yes'){
					$return['auction_dates_extend'] = 'yes';
				 }else{
					 $return['auction_dates_extend'] = 'no';
				}
				
				 wp_send_json($return);
				 die();
			}
	 }
	 if(str_replace(",","",$_auction_start_price) !=$_REQUEST['price'] && $_REQUEST['seller_screen'] != 'yes'){
		 $return['price'] = number_format(round(str_replace(",","",$_auction_start_price)),0,",",",");
		 $return['add_to_cart_text'] = 'update_price';
		 $return['response_mode'] = 'update_price';
		 echo json_encode($return);
		 die();
	 }
	// echo $_auction_maximum_travel_distance."==".$_REQUEST['distance'];
	 if($_auction_maximum_travel_distance !=$_REQUEST['distance'] && $_REQUEST['distance'] != 'NaN'){
		// define('WP_USE_THEMES', true);
		//require($_SERVER['DOCUMENT_ROOT'].'wp-load.php');
		 $return['distance'] = $_auction_maximum_travel_distance;
		 $return['add_to_cart_text'] = 'update_distance';
		 $return['response_mode'] = 'update_distance';
		 echo json_encode($return);
		 die();
	 }
	//wp_send_json( apply_filters( 'simple_auction_get_price_for_auctions', $return ) );
}
echo 'null';
mysqli_close($conn);
?>