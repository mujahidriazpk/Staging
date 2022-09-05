<?php
//woocommerce-401140-1262735.cloudwaysapps.com/ajax_bid_check.php?mode=checkBidUpdate&product_id=4180&current_bid=27.02
global $connection;
function wc_format_decimal($number) {
  $number = number_format($number, 2, '.', '');
  return $number;
}
function get_auction_bid_increment($post_id) {
	//Mujahid code to update increment logic
	global $connection;
	$query = "SELECT MIN(bid) as bid_amount FROM wp_simple_auction_log WHERE auction_id = '".$post_id."' LIMIT 1";
	$result = mysqli_fetch_assoc(mysqli_query($connection,$query)) or die("Query failed : " . mysqli_error());
	$bid_amount = $result['bid_amount'];
	if ($bid_amount > 0){
		$_auction_bid_increment =  ($bid_amount * 3)/100;
	}else{
		$_auction_bid_increment = 0;
	}
	return $_auction_bid_increment;
   // return get_post_meta( $this->get_main_wpml_product_id(), '_auction_bid_increment', true );
}
function getMetaValue_func($post_id,$meta_key){
	
	global $connection;
	$query = "SELECT meta_value FROM wp_postmeta  WHERE meta_key = '".$meta_key."' and post_id ='".$post_id."'";
	$result = mysqli_fetch_assoc(mysqli_query($connection,$query)) or die("Query failed : " . mysqli_error());
	//$num_rows = mysqli_num_rows($result); 
	return $result['meta_value'];
}

if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='checkBidUpdate'){
	$return = null;
	$current_user_ID = $_REQUEST['current_user_ID'];
	 if ( isset( $_REQUEST['product_id'] ) && $_REQUEST['product_id']  !="" ) {
		 	$myfile = fopen($_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/bid_logs/log_'.$_REQUEST['product_id'].'.txt', "r");
		 	$contents = fgets($myfile);
			$content_decode = json_decode($contents);
			if($content_decode->curent_bid != $_REQUEST['current_bid'] && strlen($contents) > 0){
				global $connection;
				$db_server = "localhost";
				$db_user = "gddwwykpfm";		// The user that has access to your database
				$db_password = "8cK5GTxtnS";		// The password for the user that has access to your database
				$db_name = "gddwwykpfm";
				$connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
				if(mysqli_connect_errno()){
				  echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}
				//$product_data = wc_get_product($_REQUEST['product_id']);
				$auction_has_started = getMetaValue_func($_REQUEST['product_id'],'_auction_has_started');
				if($auction_has_started === '1' ){
					$auction_current_bid = getMetaValue_func( $_REQUEST['product_id'], '_auction_current_bid');
				}else{
					$auction_current_bid = getMetaValue_func( $_REQUEST['product_id'], '_auction_start_price');
				}
				$auction_current_bider = getMetaValue_func($_REQUEST['product_id'], '_auction_current_bider');
				
				if($auction_current_bid != $_REQUEST['current_bid'] && $auction_current_bider !=""){
					$auction_bid_increment = get_auction_bid_increment($_REQUEST['product_id']);
					$_auction_max_current_bider = getMetaValue_func($_REQUEST['product_id'], '_auction_max_current_bider');
					if($current_user_ID == $_auction_max_current_bider){
						$return['winner_screen']       = 'yes';
					}else{
						$return['winner_screen']       = 'no';
					}
					$priceHTML = '<span class="auction-price current-bid" data-auction-id="'.$_REQUEST['product_id'].'" data-bid="'.$auction_current_bid.'" data-status="running"><span class="current auction">Current Bid:</span>&nbsp;$'.round(wc_format_decimal($auction_current_bid),2).'</span>';
					$return['curent_bid']       = $priceHTML;
					$return['curent_bid_value'] = $auction_current_bid;
					$return['curent_bider']     = $auction_current_bider;
					$return['curent_id']     	= $current_user_ID;
					$return['bid_value']        = round(wc_format_decimal($auction_current_bid) - wc_format_decimal($auction_bid_increment),2);
					$return['add_to_cart_text'] = '';	
				}	
			}
	 }
	 echo json_encode($return);
	 die;
}