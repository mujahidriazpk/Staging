<?php
/*$user_id = 403;
wp_set_current_user( $user_id );
wp_set_auth_cookie( $user_id );*/
global $US_state,$US_State_2,$today_date_time,$monday,$thursday,$auction_expired_date_time,$flash_cycle_start,$flash_cycle_end,$radius_distance,$today_date_time_seconds,$demo_listing;
$demo_listing = 3977;
//echo date('Y-m-d g:i A');
//echo get_the_modified_date("Y-m-d H:i:s",60);
date_default_timezone_set('America/Los_Angeles');
$today_date_time = date('Y-m-d g:i A');
$today_date_time_seconds = date('Y-m-d H:i:s');
$monday = date("Y-m-d",strtotime( "monday next week" ))." 08:30";
$thursday = date('Y-m-d', strtotime( 'thursday next week' ) )." 13:00";
$auction_expired_date_time = date('Y-m-d', strtotime( 'saturday next week' ) )." 10:30";
$flash_cycle_start = date('Y-m-d', strtotime( 'friday next week' ) )." 08:30";
$flash_cycle_end = date('Y-m-d', strtotime( 'friday next week' ) )." 10:30";
$radius_distance = 50;
$US_state = array("Alabama" => "AL",
				"Alaska" => "AK",
				"Arizona" => "AZ",
				"Arkansas" => "AR",
				"California" => "CA",
				"Colorado" => "CO",
				"Connecticut" => "CT",
				"Delaware" => "DE",
				"District of Columbia" => "DC",
				"Florida" => "FL",
				"Georgia" => "GA",
				"Hawaii" => "HI",
				"Idaho" => "ID",
				"Illinois" => "IL",
				"Indiana" => "IN",
				"Iowa" => "IA",
				"Kansas" => "KS",
				"Kentucky" => "KY",
				"Louisiana" => "LA",
				"Maine" => "ME",
				"Maryland" => "MD",
				"Massachusetts" => "MA",
				"Michigan" => "MI",
				"Minnesota" => "MN",
				"Mississippi" => "MS",
				"Missouri" => "MO",
				"Montana" => "MT",
				"Nebraska" => "NE",
				"Nevada" => "NV",
				"New Hampshire" => "NH",
				"New Jersey" => "NJ",
				"New Mexico" => "NM",
				"New York" => "NY",
				"North Carolina" => "NC",
				"North Dakota" => "ND",
				"Ohio" => "OH",
				"Oklahoma" => "OK",
				"Oregon" => "OR",
				"Pennsylvania" => "PA",
				"Rhode Island" => "RI",
				"South Carolina" => "SC",
				"South Dakota" => "SD",
				"Tennessee" => "TN",
				"Texas" => "TX",
				"Utah" => "UT",
				"Vermont" => "VT",
				"Virginia" => "VA",
				"Washington" => "WA",
				"West Virginia" => "WV",
				"Wisconsin" => "WI",
				"Wyoming" => "WY",
				//"Armed Forces Americas" => "AA",
				//"Armed Forces Europe" => "AE",
				//"Armed Forces Pacific" => "AP",
				);
$US_State_2 = array(
				"AL" => "Alabama",
				"AK" => "Alaska",
				"AZ" => "Arizona",
				"AR" => "Arkansas",
				"CA" => "California",
				"CO" => "Colorado",
				"CT" => "Connecticut",
				"DE" => "Delaware",
				"DC" => "District of Columbia",
				"FL" => "Florida",
				"GA" => "Georgia",
				"HI" => "Hawaii",
				"ID" => "Idaho",
				"IL" => "Illinois",
				"IN" => "Indiana",
				"IA" => "Iowa",
				"KS" => "Kansas",
				"KY" => "Kentucky",
				"LA" => "Louisiana",
				"ME" => "Maine",
				"MD" => "Maryland",
				"MA" => "Massachusetts",
				"MI" => "Michigan",
				"MN" => "Minnesota",
				"MS" => "Mississippi",
				"MO" => "Missouri",
				"MT" => "Montana",
				"NE" => "Nebraska",
				"NV" => "Nevada",
				"NH" => "New Hampshire",
				"NJ" => "New Jersey",
				"NM" => "New Mexico",
				"NY" => "New York",
				"NC" => "North Carolina",
				"ND" => "North Dakota",
				"OH" => "Ohio",
				"OK" => "Oklahoma",
				"OR" => "Oregon",
				"PA" => "Pennsylvania",
				"RI" => "Rhode Island",
				"SC" => "South Carolina",
				"SD" => "South Dakota",
				"TN" => "Tennessee",
				"TX" => "Texas",
				"UT" => "Utah",
				"VT" => "Vermont",
				"VA" => "Virginia",
				"WA" => "Washington",
				"WV" => "West Virginia",
				"WI" => "Wisconsin",
				"WY" => "Wyoming",
				);				
function wptutsplus_admin_styles() {
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin'){
		wp_register_style( 'wptuts_admin_stylesheet', get_template_directory_uri() . '-child/admin_style.css');
		wp_enqueue_style( 'wptuts_admin_stylesheet' );
	}
	if($user->roles[0]=='administrator'){
		wp_register_style( 'wptuts_admin_stylesheet', get_template_directory_uri() . '-child/super_admin_style.css');
		wp_enqueue_style( 'wptuts_admin_stylesheet' );
	}
}
add_action( 'admin_enqueue_scripts', 'wptutsplus_admin_styles' );
function my_theme_enqueue_styles() {
    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',get_stylesheet_directory_uri() . '/style.css',array( $parent_style ),wp_get_theme()->get('Version'));
	//wp_enqueue_style( 'print',get_stylesheet_directory_uri() . '/print.css',array( $parent_style ),false,'print');
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function count_auctions_in_area(){
			global $post,$demo_listing,$radius_distance;
			$pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
			$post_statuses = array('publish');
			if(isset($_GET['auction_status']) && $_GET['auction_status']=='past'){}else{
			$args = array(
					'post__not_in' => array($demo_listing),
					'post_status'         => $post_statuses,
					'ignore_sticky_posts' => 1,
					//'orderby'             => 'post_date',
					'meta_key' => '_auction_dates_from',
					'orderby' => 'meta_value',
					'order'               => 'asc',
					'posts_per_page'      => -1,
					'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
					//'meta_query' => array(array('key' => '_auction_closed','compare' => 'NOT EXISTS',)),
					'auction_archive'     => TRUE,
					'show_past_auctions'  => TRUE,
					'paged'               => $pagenum
				);
			}
			
			if ( isset( $_GET['post_status'] ) && in_array( $_GET['post_status'], $post_statuses ) ) {
				$args['post_status'] = $_GET['post_status'];
			}
			
			// $original_post = $post;
			$product_query = new WP_Query( $args );
			$count = $product_query->found_posts;
			$user_id    = get_current_user_id();
			$i = 0;
			if ( $product_query->have_posts() ) {
				while ($product_query->have_posts()) {
					$product_query->the_post();
					$auction_location = get_post_meta($post->ID, '_auction_location',true);
					$dentist_office_address = getDentistAddress();
					$Distance = 0;
					if(trim($dentist_office_address) !="" && trim($auction_location) !=""){
						$Distance = get_driving_information($dentist_office_address,$auction_location);
					}
					if($Distance <= $radius_distance){
						$i++;
					}
				} 
			}else{ 
					$i=0;
			}
		return $i;
}
function my_phone_or_tablet() {
	 $device = '';
    if( false !== strpos( $_SERVER['HTTP_USER_AGENT'],'iPad' ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'],'tablet' ) ){
        $device = "tab";
    }
    return $device;
}
function getActiveAds($type){
	global $wpdb;
	$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '% ".$type."%' ORDER BY ID ASC";
	$results = $wpdb->get_results($query, OBJECT);
	$ads_count = $wpdb->num_rows;
	return $ads_count;
}
function wc_get_price_decimals_mujahid(){
	return 0;
}
function wc_price_mujahid($price){
	return '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'.custom_number_format($price).'</span></span>';
}
function wc_price_ask_mujahid($price){
	return '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'.custom_number_format($price).'</span></span>';
}
function custom_number_format($number){
	return number_format(round(str_replace(",","",$number)),0,",",",");
}
function check_first_auction($auction_id){
	global $wpdb;
	$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$product_id));
}
function getKeyStatus($key){
	return 'not-expire';
}
function getAuctionId($auction_id){
	$temp = explode("-",$auction_id);
	$orginal_id = $temp[4];
	return $orginal_id;
}
function makeFlashLive() {
	global $wpdb,$today_date_time;
	$args = array(
					'post_status'         => array('publish','pending'),
					'posts_per_page'      => -1,
					'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
					'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
				  );
			$query = new WP_Query($args );
			$posts = $query->posts;
	foreach($posts as $post) {
	global $today_date_time;
	$_flash_cycle_start = get_post_meta($post->ID, '_flash_cycle_start' , TRUE);
	$_flash_cycle_end = get_post_meta($post->ID, '_flash_cycle_end' , TRUE);
	if(strtotime($today_date_time) >= strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
	//if(strtotime($today_date_time) > strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
			$product_id = $post->ID;
			update_post_meta( $product_id, '_auction_dates_from', $_flash_cycle_start);
			update_post_meta( $product_id, '_auction_dates_to', $_flash_cycle_end);
			update_post_meta( $product_id, '_flash_status','yes');
			delete_post_meta($product_id, '_auction_fail_email_sent', 1);
			delete_post_meta($product_id, '_auction_finished_email_sent', 1);
			delete_post_meta($product_id, '_auction_fail_reason', 1);
			delete_post_meta($product_id, '_auction_closed', 1);
		}
	}
	$this_saturday = date('Y-m-d', strtotime( 'saturday this week' ) )." 10:31";
	if(strtotime($today_date_time) >  strtotime($this_saturday)){
		$url = home_url('cronJob_archive_bid.php');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		
		$url = home_url('cronJob_archive_no_bid.php');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
	}
}
function getAdLinkNew($ad_id){
	//include("/home/401140.cloudwaysapps.com/qcqkjmndhe/public_html/wp-content/plugins/advanced-ads/classes/ad.php");
	
	$ad = new Advanced_Ads_Ad($ad_id);
	$options = $ad->options();
	$ad_options = isset( $options['tracking'] ) ? $options['tracking'] : array();
	$public_id = ( isset( $ad_options['public-id'] ) && ! empty( $ad_options['public-id'] ) )? $ad_options['public-id'] : false;
	$public_stats_slug = ( isset( $tracking_options['public-stats-slug'] ) )? $tracking_options['public-stats-slug'] : 'ad-stats';
	if( $public_id==""){
		$public_id=base64_encode($ad_id);
	}
	$public_link = $public_id ? site_url( '/ad-statistics/?id=' . $public_id) : false; 
	return $public_link;
}
function woocommerce__simple_auctions_winning_bid_message_custom( $product_id ) {

	global $product, $woocommerce;



	if (!(method_exists( $product, 'get_type') && $product->get_type() == 'auction'))

					return '';

	if ($product->is_closed())

				return '';

	$current_user = wp_get_current_user();



	if (!$current_user-> ID)

					return '';



	if ($product->get_auction_sealed() == 'yes')

					return '';



	if ($current_user->ID == $product->get_auction_current_bider() &&  wc_notice_count () == 0   ) {
		$message = '<!--<div class="woocommerce-notices-wrapper" id="no_bid_msg">
						<div class="woocommerce-message" role="alert">'.__('Your bid is registered.', 'wc_simple_auctions').'</div>
					</div>--><script type="text/javascript"> jQuery(\'#no_bid_msg\').show();  jQuery(document).ready(function(){ setTimeout("jQuery(\'#no_bid_msg\').hide(\'slow\');",2000); });</script>';
		return $message;

	}else{
		return '';
	}

	

}
function getTimeZone_Custom(){
	global $wpdb;
	if (is_user_logged_in()){
		$user = wp_get_current_user();
		$user_id = $user->ID;
		if($user->roles[0]=='seller'){
			$zip_code = get_user_meta( $user_id, 'client_zip_code', true);
		}else{
			$zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true);
		}
		$timeZone = $wpdb->get_var("SELECT timezone FROM timezonebyzipcode WHERE zip = '".$zip_code."' LIMIT 1");
		if($timeZone !=""){
			return $timeZone;
		}else{
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$pageurl = 'https://ipapi.co/'.$user_ip.'/timezone';
			$cURL=curl_init();
			curl_setopt($cURL,CURLOPT_URL,$pageurl);
			curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
			$timeZone=trim(curl_exec($cURL));
			curl_close($cURL);
			if(strpos($timeZone,'error') > 0){
				$pageurl = 'http://dentalassets.com/timezone.php?REMOTE_ADDR='.$user_ip;
				$cURL=curl_init();
				curl_setopt($cURL,CURLOPT_URL,$pageurl);
				curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
				$timeZone=trim(curl_exec($cURL));
				curl_close($cURL);
				if(strpos($timeZone,'error') > 0 || $timeZone==''){
					$pageurl = 'https://ladeedastudio.com/timezone.php?REMOTE_ADDR='.$user_ip;
					$cURL=curl_init();
					curl_setopt($cURL,CURLOPT_URL,$pageurl);
					curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
					$timeZone=trim(curl_exec($cURL));
					curl_close($cURL);
				}
			}
			return $timeZone;
		}
		
	}
	//$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$post->ID));
	
}
function getDentistAddress(){
	if (is_user_logged_in()){
		$user = wp_get_current_user();
		if($user->roles[0]=='seller'){
			$user_id = dokan_get_current_user_id();
			$client_street = get_user_meta( $user_id, 'client_street', true);
			$client_apt_no = get_user_meta( $user_id, 'client_apt_no', true);
			$client_city = get_user_meta( $user_id, 'client_city', true);
			$client_state = get_user_meta( $user_id, 'client_state', true);
			$client_zip_code = get_user_meta( $user_id, 'client_zip_code', true);
			$address = $client_street." ".$client_apt_no." ".$client_city." ".$client_state." ".$client_zip_code;
		}else{
			$user_id = dokan_get_current_user_id();
			$dentist_office_street = get_user_meta( $user_id, 'dentist_office_street', true);
			$dentist_office_apt_no = get_user_meta( $user_id, 'dentist_office_apt_no', true);
			$dentist_office_city = get_user_meta( $user_id, 'dentist_office_city', true);
			$dentist_office_state = get_user_meta( $user_id, 'dentist_office_state', true);
			$dentist_office_zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true);
			$address = $dentist_office_street." ".$dentist_office_apt_no." ".$dentist_office_city." ".$dentist_office_state." ".$dentist_office_zip_code;
		}
	}else{
		$user_ip = getenv('REMOTE_ADDR');
		$pageurl = "http://www.geoplugin.net/php.gp?ip=$user_ip";
		$cURL=curl_init();
		curl_setopt($cURL,CURLOPT_URL,$pageurl);
		curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
		$geo=unserialize(trim(curl_exec($cURL)));
		curl_close($cURL);
		$country = $geo["geoplugin_countryName"];
		$city = $geo["geoplugin_city"];
		$address = $city." ".$country;
	}
	return $address;
}
function get_plan_dates($product_id){
	$user_id = dokan_get_current_user_id();
	$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($user_id);
	$active_cancelled_flag = 'no';
	if(count($subscriptions_users) > 0){
		foreach($subscriptions_users as $row){
			$plan_id = get_post_meta($row->ID,'product_id',true);
			$status = get_post_meta($row->ID,'status',true);
			$expired_date = get_post_meta($row->ID,'expired_date',true);
			if($status=='active'){
				if($plan_id != 1141 && $product_id != $plan_id){
					if($plan_id==948 && $product_id==942){
						$payment_due_date = get_post_meta( $row->ID, 'payment_due_date', true );
						if($payment_due_date){
							$deactive_date = strtotime("+4 week", $payment_due_date);
							$active_cancelled_flag = 'yes';
							$this_monday = date('Y-m-d',strtotime("monday next week",$deactive_date))." 08:30";
							$this_friday = date('Y-m-d',strtotime("friday next week",$deactive_date))." 10:30";
						}
					}
				}
			}else{
			}
		}
	}else{
		$this_monday = date("Y-m-d",strtotime( "monday this week" ))." 08:30";
		$this_friday = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
	}
	if($this_monday==""){
		$this_monday = date("Y-m-d",strtotime( "monday this week" ))." 08:30";
		$this_friday = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
	}
	return $this_monday."##".$this_friday;
}
function pay_for_plan($auction_id,$type){
	global $woocommerce;
	$custom_data = array();
	$woocommerce->cart->empty_cart();
	// select ID
	if($type=='single'){
		$product_id = 942;
		if($auction_id==''){
			$auction_id = $product_id;
		}	
	}elseif($type=='relist'){
		$product_id = 1642;	
	}elseif($type=='register'){
		$product_id = 1141;	
	}else{
		$product_id = 948;
		if($auction_id==''){
			$auction_id = $product_id;
		}
	}
	$custom_data['custom_data']['auction_id'] = $auction_id;
	$custom_data['custom_data']['plan_id'] = $product_id; 
	
	$user_id = dokan_get_current_user_id();
	$custom_data['custom_data']['user_id'] = $user_id; 
	$quantity = 0;
	foreach(WC()->cart->get_cart() as $key => $val ) {
		$_product = $val['data'];
        if($product_id == $_product->get_id()) {
			$quantity = $val['quantity'];
		}
    }
	if($quantity==0){
		WC()->cart->add_to_cart($product_id, '1', '0', array(), $custom_data); 
	}
	//echo "here";die;
	//check if product already in cart
	//if ( WC()->cart->get_cart_contents_count() == 0 ) {
		// if no products in cart, add it
		//WC()->cart->add_to_cart( $product_id );	  
	//}
	
	$url = get_permalink(48);
	if($auction_id==46){
		$url .="/?mode=reactive";
	}elseif($auction_id=='' && $type=='register'){
		$url .="/?mode=register";
	}elseif($type=='monthly' || $type=='single'){
		$url .="/?mode=change-plan";
	}
	wp_redirect($url);
	exit();
}
function pay_for_register_fee($auction_id,$type){
	$custom_data = array();

	// select ID
	if($type=='single'){
		$product_id = 942;	
	}else{
		$product_id = 948;
	}
	$custom_data['custom_data']['auction_id'] = $auction_id;
	$custom_data['custom_data']['plan_id'] = $product_id; 
	$user_id = dokan_get_current_user_id();
	$custom_data['custom_data']['user_id'] = $user_id; 
	WC()->cart->add_to_cart($product_id, '1', '0', array(), $custom_data); 
	//echo "here";die;
	//check if product already in cart
	//if ( WC()->cart->get_cart_contents_count() == 0 ) {
		// if no products in cart, add it
		//WC()->cart->add_to_cart( $product_id );	  
	//}
	$url = get_permalink(48);
	if($auction_id==46){
		$url .="/?mode=reactive";
	}
	wp_redirect($url);
	exit();
}
function pay_for_auction($auction_id,$mode){
	$custom_data = array();
	// select ID
	if($mode=='discount'){
		$product_id = 1642;	
	}else{
		$product_id = 126;	
	}
	$custom_data['custom_data']['auction_id'] = $auction_id; 
	$user_id = dokan_get_current_user_id();
	$custom_data['custom_data']['user_id'] = $user_id; 
	WC()->cart->empty_cart();
	WC()->cart->add_to_cart($product_id, '1', '0', array(), $custom_data); 
	//check if product already in cart
	if ( WC()->cart->get_cart_contents_count() == 0 ) {
		// if no products in cart, add it
		//WC()->cart->add_to_cart( $product_id );	  
	}
	$url = get_permalink(48);
	if($mode=='discount'){
		$url =$url."?mode=relist";
	}
	wp_redirect($url);
}
function pay_for_auction_multi($auction_id,$mode){
	$custom_data = array();
	// select ID
	if($mode=='discount'){
		$product_id = 1642;	
	}else{
		$product_id = 126;	
	}
	$product_id = 1642;
	$custom_data['custom_data']['auction_id'] = $auction_id; 
	$user_id = dokan_get_current_user_id();
	$custom_data['custom_data']['user_id'] = $user_id; 
	//WC()->cart->empty_cart();
	WC()->cart->add_to_cart($product_id, '1', '0', array(), $custom_data); 
	//check if product already in cart
	if ( WC()->cart->get_cart_contents_count() == 0 ) {
		// if no products in cart, add it
		//WC()->cart->add_to_cart( $product_id );	  
	}
	
}
function get_CURLDATA($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
function get_latlong($street_address){
	///buy/wp-content/themes/dokan-child/functions.php
	//wp-content/plugins/dokan-pro/includes/wc-function.php
	//wp-content/plugins/dokan-lite/includes/wc-function.php
	//wp-content/plugins/dokan-lite/classes//template-settings.php
	$street_address = str_replace(" ", "+", $street_address); //google doesn't like spaces in urls, but who does?
	$url 			= 'https://nominatim.openstreetmap.org/search?q='.$street_address.'&format=json&polygon=1&addressdetails=1'; 
	$google_api_response = get_CURLDATA($url); 
	
	$results = json_decode($google_api_response); //grab our results from Google
		
	$results = (array) $results; //cast them to an array

	$latitude = $results[0]->lat;
	$longitude = $results[0]->lon;
	$return = array('latitude'  => $latitude,'longitude' => $longitude);
	return $return;
}
function curl_request($sURL,$sQueryString=null){
	//echo $sURL.'?'.$sQueryString."<br />";
	$cURL=curl_init();
	curl_setopt($cURL,CURLOPT_URL,$sURL.'?'.$sQueryString);
	curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
	$cResponse=trim(curl_exec($cURL));
	curl_close($cURL);
	return $cResponse;
}
function get_driving_information($start, $finish, $raw = false){
	$pageurl = "https://www.distance-cities.com/search?from=".urlencode($start)."&to=".urlencode($finish)."&fromId=0&toId=0&flat=0&flon=0&tlat=0&tlon=0";
	$cURL=curl_init();
	curl_setopt($cURL,CURLOPT_URL,$pageurl);
	curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
	$html=trim(curl_exec($cURL));
	curl_close($cURL);
	$html = str_replace('&','&amp;',$html);
	$doc = new DOMDocument();
	@$doc->loadHTML($html);

	if(trim($doc->getElementById('sud')->nodeValue) =="" || $doc->getElementById('sud')->nodeValue ==NULL){
		//echo $doc->getElementById('p#results')->nodeValue;
		return str_replace("mi","",str_replace(",","",$doc->getElementById('kmslinearecta')->nodeValue));
	}else{
		return str_replace("mi","",str_replace(",","",$doc->getElementById('sud')->nodeValue));
	}
}
function getUserRole() {

	global $current_user;

	if (is_user_logged_in() ){

		$role = $current_user->roles[0];
		return $role;
	}
	return "";
}
function get_dentist_active_auction(){
	global $wpdb;
	$user_id  = get_current_user_id();
	$postids = array();
	$userauction	 = $wpdb->get_results("SELECT DISTINCT auction_id FROM ".$wpdb->prefix."simple_auction_log WHERE userid = $user_id ",ARRAY_N );
	if(isset($userauction) && !empty($userauction)){
		foreach ($userauction as $auction) {
			$postids[]= $auction[0];
		}
	}
	$args = array('post__in' 			=> $postids ,
					'post_type' 		=> 'product',
					'posts_per_page' 	=> '-1',
                    'order'		=> 'ASC',
                    'orderby'	=> 'meta_value',
                    //'meta_key' 	=> '_auction_dates_to',
					'tax_query' 		=> array(
						array(
							'taxonomy' => 'product_type',
							'field' => 'slug',
							'terms' => 'auction'
						)
					),
					'meta_query' => array(
								array(
									'key'     => '_auction_closed',
									'compare' => 'NOT EXISTS',
								)
					   ),
					'auction_arhive' => TRUE,      
					'show_past_auctions' 	=>  FALSE,      
				);
	$activeloop = new WP_Query( $args );
	if ($activeloop->have_posts()&&!empty($postids)) {
		return 'active';
	}else{
		return 'inactive';
	}
}
function my_active_auction_status(){
	global $wpdb,$today_date_time;
	$query = "SELECT wp_posts.* FROM wp_posts LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) LEFT JOIN wp_posts AS p2 ON (wp_posts.post_parent = p2.ID) WHERE 1=1 AND ( wp_term_relationships.term_taxonomy_id IN (17) ) AND wp_posts.post_author IN (".dokan_get_current_user_id().") AND wp_posts.post_type = 'product' AND (((wp_posts.post_status = 'publish' OR wp_posts.post_status = 'draft' OR wp_posts.post_status = 'pending') OR (wp_posts.post_status = 'inherit' AND (p2.post_status = 'publish' OR p2.post_status = 'draft' OR p2.post_status = 'pending')))) GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
	$results = $wpdb->get_results($query, OBJECT);
	$active_auction = array();
	foreach($results as $post){
		$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$post->ID));
		$_auction_dates_to = get_post_meta($post->ID, '_auction_dates_to', true );
		$_flash_cycle_start = get_post_meta( $post->ID, '_flash_cycle_start' , TRUE);
		$_flash_cycle_end = get_post_meta( $post->ID, '_flash_cycle_end' , TRUE);
		if(strtotime($_auction_dates_to) > strtotime($today_date_time) && strtotime($today_date_time) < strtotime($_flash_cycle_end) && $bid_count != 0){
			array_push($active_auction,'active');
		}else{
			array_push($active_auction,'inactive');
		}
	}
	if(in_array("active",$active_auction)){
		return 'active';
	}else{
		return 'inactive';
	}
}
function get_plan_orderid(){
	$plan_order_id= '';
	if (is_user_logged_in() ){
		$current_user = wp_get_current_user();
		$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
		foreach($subscriptions_users as $row){
			$plan_id = get_post_meta($row->ID,'product_id',true);
			if($plan_id != 1141){
				$metas = get_post_meta($row->ID);
				$payment_due_date = get_post_meta($row->ID,'payment_due_date',true);
				$status = get_post_meta($row->ID,'status',true);
				$expired_date = get_post_meta($row->ID,'expired_date',true);
				if($expired_date ==""){
					if($status=='active'){
						$plan_order_id = $row->ID;
					}
				}else{
					$curDateTime = date("Y-m-d H:i:s");
					$myDate = date("Y-m-d H:i:s",$expired_date);
					if($myDate > $curDateTime){
						if($status=='active'){
							$plan_order_id = $row->ID;
						}
					}
				}
			}
		}
	}
	return $plan_order_id;
}
function get_plan_status(){
	if (is_user_logged_in() ){
		$current_user = wp_get_current_user();
		$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
		$plan = 'inactive';
		foreach($subscriptions_users as $row){
			$plan_id = get_post_meta($row->ID,'product_id',true);
			if($plan_id != 1141){
				$metas = get_post_meta($row->ID);
				$status = get_post_meta($row->ID,'status',true);
				if($status=='active'){
					$payment_due_date = get_post_meta($row->ID,'payment_due_date',true);
					$expired_date = get_post_meta($row->ID,'expired_date',true);
					$start_date = get_post_meta($row->ID,'start_date',true);
					if($expired_date ==""){
						if($status=='active'){
							$plan = 'active';
							break;
						}else{
							$plan = 'inactive';
						}
					}else{
						//date_default_timezone_set('Asia/Kolkata');
		
						/*$curDateTime = date("Y-m-d H:i:s");
						$expired_date = date("Y-m-d H:i:s",$expired_date);
						$start_date = date("Y-m-d H:i:s",$start_date);*/
						$curDateTime = date("Y-m-d");
						$expired_date = date("Y-m-d",$expired_date);
						$start_date = date("Y-m-d",$start_date);
						if(isset($_GET['mode'])){
							//echo $expired_date.' > '.$curDateTime;
						}
						if($start_date <=  $curDateTime && $expired_date >= $curDateTime){
							if($status=='active'){
								$plan = "active";
								break;
							}else{
								$plan = "inactive";
							}
						}else{
							$plan = "inactive";
						}
					}
				}
			}
			//echo date("Y-m-d",$payment_due_date);
			
			/*foreach($myvals as $key=>$val){
				echo $key . ' : ' . $val[0] . '<br/>';
			}*/
		}
		return $plan;
	}else{
		return true;
	}
}

add_action( 'woocommerce_payment_complete', 'so_payment_complete' );
function so_payment_complete( $order_id ){
    $subscription = ywsbs_get_subscription_by_order($order_id);
	if($subscription){
		$product_id = get_post_meta($subscription->id,'product_id',true);
		/*
		if($product_id==948){
			$order_subscription_id = $subscription->id;
			$subscriptions = YWSBS_Subscription_Helper()->get_subscriptions_by_user( get_current_user_id() );
			$status = ywsbs_get_status();
			$flag = false;
			foreach ( $subscriptions as $subscription_post ) :
				$subscription = ywsbs_get_subscription( $subscription_post->ID );
				if(($status[$subscription->status]=='active' || $status[$subscription->status] == 'expired') && $subscription->product_id != 1141):
					$flag = true;
					$active_plan = $subscription->product_id;
				endif;
			endforeach;
			if($flag):
			if($active_plan == 942){
				$next_monday = date("Y-m-d",strtotime( "monday next week" ));
				$payment_due_date = date("Y-m-d", strtotime("+1 month", strtotime( "monday next week" )));
				update_post_meta($order_subscription_id,'start_date',strtotime($next_monday));
				update_post_meta($order_subscription_id,'payment_due_date',strtotime($payment_due_date));
			}
			endif;
		}
		if(!$flag):
			//cancel all previouse subscription except current one
			$user_id = get_post_meta($subscription->id,'user_id',true);
			$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($user_id);
			foreach($subscriptions_users as $row){
				$plan_id = get_post_meta($row->ID,'product_id',true);
				if($plan_id != 1141 && $product_id != $plan_id){
					update_post_meta ($row->ID,'status','cancelled');
					update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
					update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));
				}
			}
		endif;
		*/
		//cancel all previouse subscription except current one
		//important
		$user_id = get_post_meta($subscription->id,'user_id',true);
		$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($user_id);
		$active_cancelled_flag = 'no';
		foreach($subscriptions_users as $row){
			$plan_id = get_post_meta($row->ID,'product_id',true);
			if($plan_id != 1141 && $product_id != $plan_id){
				if($plan_id==948 && $product_id==942){
					//first deactive the recurring subscription
					$payment_due_date = get_post_meta( $row->ID, 'payment_due_date', true );
					if($payment_due_date){
						$deactive_date = strtotime("+4 week", $payment_due_date);
						update_post_meta ($row->ID,'cancelled_date',$deactive_date);
						update_post_meta ($row->ID,'end_date',$deactive_date);
						update_post_meta ($row->ID,'_plan_status',"active_cancelled");
						$status = get_post_meta($row->ID,'status',true);
						if($status=='active'){
							$active_cancelled_flag = 'yes';
							//calculate subscription for next week plan
							$next_monday_after_deactive = date('Y-m-d',strtotime("monday next week",$deactive_date))." 08:30";
							$next_friday_after_deactive = date('Y-m-d',strtotime("friday next week",$deactive_date))." 10:30";
						}
					}
					/*echo $payment_due_date."<br />";		
					echo $next_monday_after_deactive."<br />";
					echo $next_monday_after_deactive."<br />";
					die;*/
				}else{
					update_post_meta ($row->ID,'status','cancelled');
					update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
					update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));
				}
			}
			
			/**/
		}
		/*****************************************/
		if($product_id==942){
			if($active_cancelled_flag=='yes'){
				$this_monday =$next_monday_after_deactive;
				$this_thursday =$next_friday_after_deactive;
			}else{
				$this_monday = date("Y-m-d",strtotime( "monday this week" ))." 08:30";
				$this_thursday = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
			}
			update_post_meta($subscription->id,'start_date',strtotime($this_monday));
			update_post_meta($subscription->id,'payment_due_date',strtotime($this_thursday));
			update_post_meta($subscription->id,'expired_date',strtotime($this_thursday));
		}
		if($product_id==1141){
			update_user_meta ( get_current_user_id(),'dentist_account_status','active');
			update_user_meta(get_current_user_id(), 'deactivate_CD','No');
		}
		
	}
	$order = wc_get_order( $order_id );
	$order_data = $order->get_data();
	$items = $order->get_items();
	global $wpdb;
	$user = wp_get_current_user();
	$user_id = $user->ID;
	foreach ( $items as $item ) {
		//$product_name = $item->get_name();
		$product_id = $item->get_product_id();
		//$product_variation_id = $item->get_variation_id();
		//$item_meta_data = $item->get_meta_data();
		$Auction_id = $item->get_meta('Auction #');
		//print_r($item_meta_data);
		
		//Insert Order Record for Stats Table
		$table = 'wp_order_stats';
		if($Auction_id){
			$Auction_id_store = getAuctionId($Auction_id);
			$service = $item->get_meta('Service');
		}else{
			$Auction_id_store = 0;
			$service = '';
		}
		if($user->roles[0]=='seller'){
			$type = 'seller';
		}else{
			$type = 'dentist';
		}
		$data = array('user_id' =>$user_id, 'order_id' => $order_id, 'product_id' => $product_id, 'cost' => $item->get_total(), 'auction_id' =>$Auction_id_store, 'service' =>$service, 'city' => $order_data['billing']['city'], 'state' => $order_data['billing']['state'], 'zip' => $order_data['billing']['postcode'], 'type' => $type, 'date' =>date('Y-m-d'), 'source' =>'woocommerce');
		$format = array('%d','%d','%d','%f','%d','%s','%s','%s','%s','%s','%s','%s');
		$wpdb->insert($table,$data,$format);
		//$my_id = $wpdb->insert_id;
		/*****************************************/
		
		if($Auction_id){
			$Auction_id = getAuctionId($Auction_id);
			$postData = array('ID' =>$Auction_id, 'post_status' =>'publish');
			wp_update_post( $postData );
		}
	}
	if(isset($_POST['wc-yith-stripe-payment-token'])  && $_POST['wc-yith-stripe-payment-token'] =='new'){
		if(isset($_POST['card-number']) && $_POST['card-number']!=""){
			$number_array = explode(" ",$_POST['card-number']);
			//$last_part = array_pop($number_array);
			//$card_number = implode(" ",$number_array)." XXXX";
			update_post_meta($order_id,'_credit_card_number',end($number_array));
		}
	}
	if(!isset($_POST['wc-yith-stripe-payment-token'])){
		if(isset($_POST['card-number']) && $_POST['card-number']!=""){
			$number_array = explode(" ",$_POST['card-number']);
			//$last_part = array_pop($number_array);
			//$card_number = implode(" ",$number_array)." XXXX";
			update_post_meta($order_id,'_credit_card_number',end($number_array));
		}
	}
	if(isset($_POST['wc-yith-stripe-payment-token'])  && $_POST['wc-yith-stripe-payment-token'] !='new'){
		//if(isset($_POST['card-number-save']) && $_POST['card-number-save']!=""){
			$token_id = $_POST['wc-yith-stripe-payment-token'];
			update_post_meta($order_id,'_credit_card_number',$_POST['card-number-save-'.$token_id]);
		//}
	}
	
}
add_filter( 'woocommerce_payment_gateway_get_saved_payment_method_option_html', 'custom_card_number', 10, 3 );
function custom_card_number( $html, $token, $that ){
	// filter...
	$label = esc_html( $token->get_display_name() );
	$tmp = explode("ending in ",$label);
	$tmp2 = explode(" (",$tmp[1]);
	if($tmp2[0] !=""){
		$html .= '<input type="hidden" name="card-number-save-'.$token->get_id().'" value="'.$tmp2[0].'"/>';
	}
	return $html;
}
add_filter('woocommerce_email_recipient_customer_processing_order', 'wh_OrderProcessRecep', 10, 2);
function wh_OrderProcessRecep($recipient, $order){
    $order_id = $order->get_order_number();
	if(isset($_POST['wc-yith-stripe-payment-token'])  && $_POST['wc-yith-stripe-payment-token'] =='new'){
		if(isset($_POST['card-number']) && $_POST['card-number']!=""){
			$number_array = explode(" ",$_POST['card-number']);
			//$last_part = array_pop($number_array);
			//$card_number = implode(" ",$number_array)." XXXX";
			update_post_meta($order_id,'_credit_card_number',end($number_array));
		}
	}
	if(isset($_POST['wc-yith-stripe-payment-token'])  && $_POST['wc-yith-stripe-payment-token'] !='new'){
		//if(isset($_POST['card-number-save']) && $_POST['card-number-save']!=""){
			$token_id = $_POST['wc-yith-stripe-payment-token'];
			update_post_meta($order_id,'_credit_card_number',$_POST['card-number-save-'.$token_id]);
		//}
	}
    return $recipient;
}
add_action('gform_user_registered','add_dokan_address', 10, 4 ); 
function add_dokan_address( $user_id, $feed, $entry, $user_pass ) {
	  $shopname = rgar( $entry, '4' );
	  $phone = rgar( $entry, '9' );
	  $dokan_settings = array(
            'store_name'     => sanitize_text_field( wp_unslash($shopname)),
            'social'         => array(),
            'payment'        => array(),
            'phone'          => '',
			'address'        => array(),
            'show_email'     => 'no',
            'location'       => '',
            'find_address'   => '',
            'dokan_category' => '',
            'banner'         => 0,
        );
	update_user_meta( $user_id, 'dokan_enable_selling', 'yes' );
	update_user_meta( $user_id, 'dokan_profile_settings', $dokan_settings );
	update_user_meta( $user_id, 'dokan_store_name', $shopname);
}
add_filter('gform_us_states', 'us_states');
function us_states( $states ) {
    $new_states = array();
    foreach ( $states as $state ) {
        $new_states[ GF_Fields::get( 'address' )->get_us_state_code( $state ) ] = $state;
    }
    return $new_states;
}

add_action('um_registration_complete','after_registration_complete', 10, 2 );
function after_registration_complete($user_id, $args) {
	if($_POST['form_id']==103){
		global $US_state;
		$shopname = $_POST['user_login-103'];
		$phone = $_POST['phone_number-103'];
		$dokan_settings = array(
			'store_name'     => sanitize_text_field( wp_unslash($shopname)),
			'social'         => array(),
			'payment'        => array(),
			'phone'          => sanitize_text_field( wp_unslash($phone) ),
			'address'        => array(
							"street_1"=>strip_tags($_POST['client_street-103']),
							"street_2"=>strip_tags($_POST['client_apt_no-103']),
							"city"=>strip_tags($_POST['client_city-103']),
							"state"=>strip_tags($US_state[$submitted['client_state']]),
							"zip"=>strip_tags($_POST['client_zipcode_new-103']),
							"country"=>'US'),
			'show_email'     => 'no',
			'location'       => '',
			'find_address'   => '',
			'dokan_category' => '',
			'banner'         => 0,
		);
		update_user_meta( $user_id, 'dokan_enable_selling', 'yes' );
		update_user_meta( $user_id, 'dokan_profile_settings', $dokan_settings );
		update_user_meta( $user_id, 'dokan_store_name', $shopname);
	}
    // your code here
}
add_action( 'um_user_after_updating_profile', 'my_user_after_updating_profile', 10, 1 );
function my_user_after_updating_profile( $submitted ) {
		global $US_state;
		$user = wp_get_current_user();
		if($user->roles[0]=='seller'){
			$user_id = $user->ID;
			$shopname = $user->user_login;
			$phone = $submitted['phone_number'];
			$dokan_settings = array(
				'store_name'     => sanitize_text_field( wp_unslash($shopname)),
				'social'         => array(),
				'payment'        => array(),
				'phone'          => sanitize_text_field( wp_unslash($phone) ),
				'address'        => array(
								"street_1"=>strip_tags($submitted['client_street']),
								"street_2"=>strip_tags($submitted['client_apt_no']),
								"city"=>strip_tags($submitted['client_city']),
								"state"=>strip_tags($US_state[$submitted['client_state']]),
								"zip"=>strip_tags($submitted['client_zipcode_new']),
								"country"=>'US'),
				'show_email'     => 'no',
				'location'       => '',
				'find_address'   => '',
				'dokan_category' => '',
				'banner'         => 0,
			);
			update_user_meta( $user_id, 'dokan_profile_settings', $dokan_settings );
		}
}
add_action( 'dokan_edit_auction_product_content_before', 'dokan_edit_auction_product_content_before_custom');
function dokan_edit_auction_product_content_before_custom(){
	if ( isset( $_GET['message'] ) && $_GET['message'] == 'success') {
		WC()->cart->empty_cart();
		$custom_data = array();
		// select ID
		$product_id = 126;	
		$custom_data['custom_data']['auction_id'] = $_GET['product_id']; 
		$user_id = dokan_get_current_user_id();
		$custom_data['custom_data']['user_id'] = $user_id; 
		WC()->cart->add_to_cart($product_id, '1', '0', array(), $custom_data); 
		//check if product already in cart
		if ( WC()->cart->get_cart_contents_count() == 0 ) {
			// if no products in cart, add it
			//WC()->cart->add_to_cart( $product_id );	  
		}
		$url = get_permalink(48);
		wp_redirect($url);
		exit;
	}
}

function iconic_add_engraving_text_to_cart_item($cart_item_data, $product_id, $variation_id ) {
	$cart_item_data['auction_id'] = $cart_item_data['custom_data']['auction_id'];
	$cart_item_data['user_id'] = $cart_item_data['custom_data']['user_id'];
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'iconic_add_engraving_text_to_cart_item', 10, 3 );
function iconic_display_engraving_text_cart( $item_data, $cart_item ) {
	if ( empty( $cart_item['auction_id'] ) ) {
		return $item_data;
	}
	
	$item_data = array();
	
	if($cart_item['product_id']==942 || $cart_item['product_id']==948){
		/*$item_data[] = array(
		'key'     => __( 'Auction #', 'iconic' ),
		'value'   =>  $cart_item['auction_id'],
			'display' => '',
	
		);*/
		if($cart_item['product_id']==942){
			/*$this_monday = date("Y-m-d",strtotime( "monday this week" ))." 08:30";
			$this_thursday = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";*/
			$dates = explode("##",get_plan_dates(942));
			$this_monday = $dates[0];
			$this_thursday = $dates[1];
			$item_data[] = array(
			'key'     => __( 'Auction Cycle Starts', 'iconic' ),
			'value'   =>  date_i18n( get_option( 'date_format' ),  strtotime($this_monday)).' '.date('g:i A',strtotime($this_monday)).' PT',
			'display' => '',
	
			);
			$item_data[] = array(
				'key'     => __( 'Auction Cycle Ends', 'iconic' ),
				'value'   =>  date_i18n( get_option( 'date_format' ),  strtotime( $this_thursday )).' '.date('g:i A',strtotime($this_thursday)).' PT',
				'display' => '',
		
			);
		}
	}else{
		$auction_no = get_post_meta($cart_item['auction_id'], 'auction_#',true);
		$item_data[] = array(
		'key'     => __( 'Auction #', 'iconic' ),
		'value'   =>  $auction_no,
			'display' => '',
	
		);
		if($cart_item['auction_id'] != 46){
			//die;
			if (defined('DOING_AJAX') && DOING_AJAX) {
			}else{
				if(isset($_GET['lang']) && $_GET['lang']=='es'){
					$btn_back_text = 'atrás';
				}else{
					$btn_back_text = 'Back';
				}
				echo '<script> jQuery("#back_btn_div").html("<a href=\"'.home_url('auction-activity/auction/?product_id='.$cart_item['auction_id'].'&action=edit').'\" style=\"font-size:17px !important;\" class=\"dokan-btn dokan-btn-theme btn-primary\" title=\"'.$btn_back_text .'\">'.$btn_back_text .'</a>");</script>';
			}
			$product = wc_get_product( $cart_item['auction_id'] );
			//$product = new WC_Product($cart_item['auction_id']);
			$product_cats_ids = wc_get_product_term_ids($cart_item['auction_id'] , 'product_cat' );
			$sub_title = '';
			if(in_array(119,$product_cats_ids)){
				$sub_title = '&nbsp;-&nbsp;<i>locators & retrofit service only</i>';
			}
			if(in_array(76,$product_cats_ids)){
				$sub_title = '&nbsp;-&nbsp;<i>abutments & denture only</i>';
			}
			if(in_array(77,$product_cats_ids)){
				$sub_title = '&nbsp;-&nbsp;<i>abutments & dentures only</i>';
			}
			$item_data[] = array(
				'key'     => __( 'Service', 'iconic' ),
				'value'   =>  str_replace("*","",$product->get_name()).$sub_title,
				'display' => '',
		
			);
			
			$item_data[] = array(
				'key'     => __( 'Auction Begins', 'iconic' ),
				'value'   =>  date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_start_time() )).' '.date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_start_time() )),
				'display' => '',
		
			);
			$item_data[] = array(
				'key'     => __( 'Auction Ends', 'iconic' ),
				'value'   =>  date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_end_time() )).' '.date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_end_time() )),
				'display' => '',
		
			);
			$_flash_cycle_start = get_post_meta($cart_item['auction_id'], '_flash_cycle_start',true);
			$_flash_cycle_end = get_post_meta($cart_item['auction_id'], '_flash_cycle_end',true );
			$item_data[] = array(
				'key'     => __( '<span class="no_translate">Flash Bid Cycle<span class="TM_flash" style="position:relative;top: -5px;">®</span></span> <small>(if needed)</small>', 'iconic' ),
				'value'   =>  date_i18n(get_option('date_format'),strtotime($_flash_cycle_start)).' @ '.date("g:i A",strtotime($_flash_cycle_start)).' to '.date_i18n(str_replace("@ ","",get_option('time_format')),strtotime($_flash_cycle_end)),
				'display' => '',
		
			);
		}
	}
	$item_data[] = array(
		'key'     => __( 'User', 'iconic' ),
		'value'   => $cart_item['user_id'] ,
		'display' => '',

		);
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'iconic_display_engraving_text_cart', 10, 2 );
function iconic_add_engraving_text_to_order_items( $item, $cart_item_key, $values, $order ) {
	if ( empty( $values['auction_id'] ) ) {
		return;
	}
	if($values['product_id']==942 || $values['product_id']==948){
		if($values['product_id']==942){
			/*$this_monday = date("Y-m-d",strtotime( "monday this week" ))." 08:30";
			$this_thursday = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";*/
			$dates = explode("##",get_plan_dates(942));
			$this_monday = $dates[0];
			$this_thursday = $dates[1];
			$item->add_meta_data( __( 'Auction Cycle Starts', 'iconic' ),date_i18n( get_option( 'date_format' ),  strtotime( $this_monday )).' '.date('g:i A',strtotime($this_monday)).' PT');
		$item->add_meta_data( __( 'Auction Cycle Ends', 'iconic' ),date_i18n( get_option( 'date_format' ),  strtotime( $this_thursday )).' '.date('g:i A',strtotime($this_thursday)).' PT');
		}
		
	}else{
		//print_r($values);
		$product = wc_get_product( $values['auction_id'] );
		$auction_no = get_post_meta($values['auction_id'], 'auction_#',true);
		$item->add_meta_data( __( 'Auction #', 'iconic' ), $auction_no );
		if($values['auction_id'] != 46){
			$product_cats_ids = wc_get_product_term_ids($values['auction_id'] , 'product_cat' );
			$sub_title = '';
			if(in_array(119,$product_cats_ids)){
				$sub_title = '&nbsp;-&nbsp;<i>locators & retrofit service only</i>';
			}
			if(in_array(76,$product_cats_ids)){
				$sub_title = '&nbsp;-&nbsp;<i>abutments & denture only</i>';
			}
			if(in_array(77,$product_cats_ids)){
				$sub_title = '&nbsp;-&nbsp;<i>abutments & dentures only</i>';
			}
			$item->add_meta_data( __( 'Service', 'iconic' ),str_replace("*","",$product->get_name()).$sub_title );
			$item->add_meta_data( __( 'Auction Begins', 'iconic' ), date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_start_time() )).' '.date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_start_time() )) );
			$item->add_meta_data( __( 'Auction Ends', 'iconic' ),date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_end_time() )).' '.date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_end_time() )) );
			
			$_flash_cycle_start = get_post_meta($values['auction_id'], '_flash_cycle_start',true);
			$_flash_cycle_end = get_post_meta($values['auction_id'], '_flash_cycle_end',true );
			$item->add_meta_data( __( '<span class="no_translate">Flash Bid Cycle<span class="TM_flash" style="position:relative;top: -5px;">®</span></span> <small>(if needed)</small>', 'iconic' ),str_replace(" PT","",date_i18n(get_option('date_format'),strtotime($_flash_cycle_start))).' @ '.date("g:i A",strtotime($_flash_cycle_start)).' to '.date_i18n(str_replace("@ ","",get_option('time_format')),strtotime($_flash_cycle_end)));
		}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'iconic_add_engraving_text_to_order_items', 10, 4 );
add_action( 'woocommerce_order_status_completed', 'my_woocommerce_order_status_completed', 10, 1 );
function my_woocommerce_order_status_completed( $order_id ) {
	global $wpdb;
    $order       = wc_get_order( $order_id );
	$customer_id = $order->get_customer_id();
	$order = new WC_Order($order_id);
	//print_r($order);
	$Auction_id = '';
	$order_item = $order->get_items();
	foreach($order_item as $item_id => $item ){
		$Auction_id = $item->get_meta('Auction #');
		if($Auction_id > 0 && $Auction_id !=""){
			$Auction_id = getAuctionId($Auction_id);
			 $post   = get_post( $Auction_id );
			 $seller = get_user_by( 'id', $post->post_author );
			 do_action( 'dokan_pending_product_published_notification', $post, $seller );
			
			$postData = array('ID' =>$Auction_id, 'post_status' =>'publish');
			wp_update_post( $postData );
		}
	}
	
}


add_action( 'woocommerce_edit_account_form', 'my_woocommerce_edit_account_form' );
function my_woocommerce_edit_account_form() {
  global $US_state;
  $user_id = get_current_user_id();
  $user = get_userdata( $user_id );
  if ( !$user )
    	return;
  $url = $user->user_url;
  if($user->roles[0]=='customer'){
		/*$new_auction_notification = get_user_meta( $user_id, 'new_auction_notification', true );
		$checked = '';
		if($new_auction_notification=='yes'){
			$checked = ' checked="checked" ';
		}
		echo '<a name="new_auction_notification">&nbsp;</a><br /><br />';
		echo '<input type="checkbox" name="new_auction_notification" value="yes" '.$checked.'/>&nbsp;<strong>Get notified immediately when new auctions are available in your area</strong><br /><br />';*/
		
		$key = 'designation';
		$value = get_user_meta( $user_id, 'designation', true );
		$options = array('DDS'=>'DDS','DMD'=>'DMD','DDS, MD'=>'DDS, MD','DMD, MD'=>'DMD, MD');
		echo '<p class="form-row form-row-thirds">
			  <label for="street">Designation</label>';
			  echo $value;
			 echo '<select name="'.$key.'" class="input-text hide" ><option value="">Please Select</option>';
			  
		foreach($options as $k => $v){
			$selected = '';
			if($value==$k){
				$selected = ' selected="selected"';
			}?>
				<option value="<?php echo esc_attr($k); ?>" <?php echo $selected; ?>><?php echo esc_attr( $k ); ?></option>
			
		<?php }
		 echo '</select></p>';
			 
	}
  if($user->roles[0]=='customer'){
	$disable_array = array('dentist_office_street','dentist_office_apt_no','dentist_office_city','dentist_office_state','dentist_office_zip_code','dentist_office_email','dentist_personal_cell','state_dental_license_no');
  	$field_array = array('<span class="tooltip_New">Change office address where treatment is administered&nbsp;<span class="tooltips" title="Please <span>contact</span> ShopADoc® admin to request any change to office address where treatment is rendered.">i</span>' => 'label',
					 'dentist_office_street'=>'Street',
					'dentist_office_apt_no'=>'Suite #' ,
					 'dentist_office_city'=>'City',
					 'dentist_office_state'=>'State',
					 'dentist_office_zip_code'=>'Zip Code',
					 'dentist_office_email'=>'Office email',
					 'dentist_personal_cell'=>'Office <i class="fa fa-phone icon">&nbsp;</i>',
					 'Licensed Dentist\'s address on file with the State Board of Dentistry'=>'label',
					 'dentist_home_street'=>'Street',
					 'dentist_home_apt_no'=>'Suite #',
					 'dentist_home_city'=>'City',
					 'dentist_home_state'=>'State',
					 'dentist_home_zip'=>'Zip Code',
					 'state_dental_license_no'=>'State Dental License #',);
	
  }elseif($user->roles[0]=='seller'){
	  $active_status = my_active_auction_status();
	  if($active_status=='active'){
		  $disable_array = array('client_street','client_apt_no','client_city','client_state','client_zip_code');
	  }else{
		  $disable_array = array();
	  }
	  $field_array = array(
	  				 'Change Address' => 'label',
					 'client_street'=>'Street',
					'client_apt_no'=>'Suite #' ,
					 'client_city'=>'City',
					 'client_state'=>'State',
					 'client_zip_code'=>'Zip Code',
					 'Change Phone Number' => 'label',
					 'client_cell_ph'=>'Mobile <i class="fa fa-phone icon">&nbsp;</i>',
					 'client_home_ph'=>'Home <i class="fa fa-phone icon">&nbsp;</i>',);
  }elseif($user->roles[0]=='advanced_ads_user'){
	  
	  $field_array = array(
	  				 'Change Address' => 'label',
					 'billing_address_1'=>'Street',
					'billing_address_2'=>'Suite #' ,
					 'billing_city'=>'City',
					 'billing_state'=>'State',
					 'billing_postcode'=>'Zip Code',
					 'Change Phone Number' => 'label',
					 'billing_phone'=>'Cell <i class="fa fa-phone icon">&nbsp;</i>',);
		 $field_array =array();
 }else{
	  $field_array =array();
  }
	echo '<fieldset>';				
	
	foreach ($field_array as $key => $val){		
		if($val=='label'){
			echo '<legend class="blue_text">'.$key.'</legend>';
		}elseif($val=='State'){
			$value = get_user_meta( $user_id, $key, true );
			echo '<p class="form-row form-row-thirds">
                  <label for="street">'.$val.'</label>';
				  if(in_array($key,$disable_array)){
					  echo $value;
				  	echo '<select name="'.$key.'" class="input-text hide" disabled="disabled" readonly="readonly" >';
				  }else{
					  echo '<select name="'.$key.'" class="input-text" >';
				  }
			foreach($US_state as $k => $v){
				$selected = '';
				if($value==$k){
					$selected = ' selected="selected"';
				}?>
                	<option value="<?php echo esc_attr($k); ?>" <?php echo $selected; ?>><?php echo esc_attr( $k ); ?></option>
                
			<?php }
			 echo '</select></p>';
			?>
			
		<?php }elseif($val=='Designation'&& 1==2){}else{
			$value = get_user_meta( $user_id, $key, true );	
		  ?>
			<p class="form-row form-row-thirds">
			  <label for="street"><?php echo $val;?></label>
              <?php if(in_array($key,$disable_array)){?>
			  	<input type="text" name="<?php echo $key;?>" value="<?php echo esc_attr( $value ); ?>" class="input-text" disabled="disabled" readonly="readonly"/>
              <?php }else{?>
			  	<input type="text" name="<?php echo $key;?>" value="<?php echo esc_attr( $value ); ?>" class="input-text" />
              <?php }?>
			</p>
		  <?php
		}
	}
	if($user->roles[0]=='customer'){
		if(isset($_GET['mode']) && ($_GET['mode']=='active' || $_GET['mode']=='de-active' || $_GET['mode']== 'unsubscribe' || $_GET['mode']== 'de-active-sub-reg' || $_GET['mode']== 'de-active-sub-reg-intial')){
			//get_user_meta( $user_id, 'dentist_account_status', true );
			//if(!add_user_meta($user_id,'dentist_account_status',$_GET['mode'])) {
				update_user_meta ($user_id,'dentist_account_status',$_GET['mode']);
				if($_GET['mode']=='de-active' &&1==2){
					/* global $current_user;
					$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
					foreach($subscriptions_users as $row){
						$plan_id = get_post_meta($row->ID,'product_id',true);
						if($plan_id != 1141){
							update_post_meta ($row->ID,'status','cancelled');
							update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
							update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));
						}
					}*/
				}
				
				if($_GET['mode']=='unsubscribe'){
					 global $current_user;
					$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
					foreach($subscriptions_users as $row){
						$plan_id = get_post_meta($row->ID,'product_id',true);
						if($plan_id == 1141 || 1==1){
							update_post_meta ($row->ID,'status','cancelled');
							update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
							update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));
						}
					}
				}
				if($_GET['mode']=='de-active-sub-reg-intial'){
					global $current_user,$wpdb;
					$entry_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_wpforms_entries WHERE user_id = %d and form_id='895' and status = 'completed' and type='payment' LIMIT 1",$current_user->ID));
					$stripe_subscription_id = json_decode($entry_row->meta)->payment_subscription;
					$payment_due_date = date("Y-m-d H:i:s", strtotime('+1 years', strtotime($entry_row->date)));
					update_user_meta ($current_user->ID,'register_sub_end_date',strtotime($payment_due_date));
					$path = "https://api.stripe.com/v1/subscriptions/".$stripe_subscription_id;
					$data = array("at_period_end"=>"true");
					curl_del($path,$data);
					
				}
				if($_GET['mode']=='de-active-sub-reg'){
					global $current_user;
					$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
					foreach($subscriptions_users as $row){
						//print_r($row);
						$plan_id = get_post_meta($row->ID,'product_id',true);
						$status = get_post_meta($row->ID,'status',true);
						if($plan_id == 1141 && $status =='active'){
							$stripe_subscription_id = get_post_meta($row->ID,'stripe_subscription_id',true);
							$payment_due_date = get_post_meta($row->ID,'payment_due_date',true);
							//update_post_meta ($row->ID,'register_sub_end_date',$payment_due_date);
							update_post_meta ($row->ID,'status','cancelled');
							update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
							update_post_meta ($row->ID,'expired_date',$payment_due_date);
							update_post_meta ($row->ID,'end_date',$payment_due_date);
							update_user_meta ($user_id,'register_sub_end_date',$payment_due_date);
							//echo $row->ID."==".$stripe_subscription_id."<br />";
							$path = "https://api.stripe.com/v1/subscriptions/".$stripe_subscription_id;
 							$data = array("at_period_end"=>"true");
							curl_del($path,$data);
						}
					}
				}
			//}
		}
		$dentist_account_status = get_user_meta( $user_id, 'dentist_account_status', true );
		//echo '<a name="dentist_account_status">&nbsp;</a><br /><br />';
		echo '<style type="text/css">.woocommerce-EditAccountForm button.woocommerce-Button{margin-top:-50px !important;}</style>';
		if($dentist_account_status=='de-active' && 1==2){
			echo '<p style="float:right;">Your account is currently deactived.&nbsp;';
			echo '<a href="'.home_url('/my-account/edit-account/?mode=active').'" class="bid_on" style="float:right;">Activate Account</a></p>';
			?>
           <!--<p style="float:right;clear:both;"> <a href="javascript:" class="example2 woocommerce-Button button">unsubscribe from all emails</a></p>-->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
				<script type="text/javascript">
                                jQuery('.example2').on('click', function(){
                                    jQuery.confirm({
                                        title: 'Please Confirm',
										columnClass: 'col-md-6 col-md-offset-3',
                                        content: 'Are you sure you want to unsubscribe from all future emails?',
                                        buttons: {
											No: {
                                                text: 'No, keep me subscribed',
                                                //btnClass: 'btn-blue',
                                                /*keys: [
                                                    'enter',
                                                    'shift'
                                                ],*/
                                                /*action: function(){
                                                    //this.jQuerycontent // reference to the content
                                                    jQuery.alert('No');
                                                }*/
                                            },
                                            Yes: {
                                                text: 'Yes, please unsubscribe me',
                                                btnClass: 'btn-blue pull-left',
                                                /*keys: [
                                                    'enter',
                                                    'shift'
                                                ],*/
                                                action: function(){
                                                   // this.jQuerycontent // reference to the content
                                                    //jQuery.alert('Yes');
													window.location.replace("<?php echo home_url('/my-account/edit-account/?mode=unsubscribe');?>");
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
			<?php
		}elseif($dentist_account_status =='unsubscribe' || $dentist_account_status=='de-active' || $dentist_account_status =='de-active-sub-reg' || $dentist_account_status =='de-active-sub-reg-intial'){
			echo '<a href="'.get_site_url().'/?action=add_to_cart&type=register&auction_id=" class="" style="float:right;">&nbsp;</a>';
		}/*elseif($dentist_account_status =='de-active-sub-reg'){
			echo '<a href="'.get_site_url().'/?action=add_to_cart&type=register&auction_id=" class="" style="float:right;">&nbsp;</a>';
		}elseif($dentist_account_status =='de-active-sub-reg-intial'){
			echo '<a href="'.get_site_url().'/?action=add_to_cart&type=register&auction_id=" class="" style="float:right;">&nbsp;</a>';
		}*/elseif(($dentist_account_status =='active' || $dentist_account_status =="")){
			$dentist_auction_status = get_dentist_active_auction();
			
			global $current_user;
			$intial_register = 'yes';
			$de_active_url = '/my-account/edit-account/?mode=de-active-sub-reg-intial';
			$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
			foreach($subscriptions_users as $row){
				//print_r($row);
				$plan_id = get_post_meta($row->ID,'product_id',true);
				$status = get_post_meta($row->ID,'status',true);
				if($plan_id == 1141 && $status =='active'){
					$intial_register = 'no';
					$de_active_url = '/my-account/edit-account/?mode=de-active-sub-reg';
					break;
				}
			}
			
			//echo '<a href="javascript:" class="example2" style="float:right;">Deactivate auto-renewal</a>';
			echo '<a href="javascript:" class="example2" style="float:right;">&nbsp;</a>';
			?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

				<script type="text/javascript">
								<?php if($dentist_auction_status=='active'){?>
                               jQuery('.example2').on('click', function(){
                                    jQuery.alert({
                                        title: 'Alert!',
										columnClass: 'col-md-6 col-md-offset-3',
                                        content: 'Auction(s) in progress, you cannot deactivate at this time.',
                                    });
                                });
                            	<?php }else{?>
                                jQuery('.example2').on('click', function(){
                                    jQuery.confirm({
                                        title: 'Please Confirm',
										columnClass: 'col-md-8 col-md-offset-3',
                                        content: 'Are you sure you want to deactivate your registration auto renewal feature?<br />Deactivation will require you to make a manual registration payment to continue to have access after your anniversary date.',
                                        buttons: {
											Yes: {
                                                text: 'Yes',
                                                action: function(){
                                                	//window.location.replace("<?php echo home_url('/my-account/edit-account/?mode=de-active');?>");
													window.location.replace("<?php echo home_url($de_active_url);?>");
                                                }
                                            },
											No: {
                                                text: 'No',
												btnClass: 'btn-blue',
                                            },
                                            
                                        }
                                    });
                                });
								<?php }?>
                            </script>
			<?php
		}
	}
	echo " </fieldset>";
 
}
function curl_del($path,$data)
{
    // Add your key
    $headers = array('Authorization:Bearer sk_test_414XDvtyKG9n5QUJI8Fadzuh');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
   // curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    //$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch);
	//return true;
    //return $result;
}

/*$path = "https://api.stripe.com/v1/subscriptions/sub_1Kyz58FEG1D3TdUzq4hrzQzX";
 
$data = array("at_period_end"=>"true");
$curl_del = curl_del($path,$data);
echo "<pre>";
print_r($curl_del);
echo "</pre>";*/
add_action('woocommerce_save_account_details','my_woocommerce_save_account_details',10,1);
function my_woocommerce_save_account_details( $user_id ) {
 global $US_state;
 $user = get_userdata( $user_id );
  if ( !$user )
    	return;
 if($user->roles[0]=='customer'){
	 $disable_array = array('dentist_office_street','dentist_office_apt_no','dentist_office_city','dentist_office_state','dentist_office_zip_code','dentist_office_email','dentist_personal_cell','state_dental_license_no');
  	$field_array = array('new_auction_notification'=>'New Auction Notification',
					 'designation'=>'Designation',
					 'Office address' => 'label',
					 'dentist_office_street'=>'Street',
					'dentist_office_apt_no'=>'Suite #' ,
					 'dentist_office_city'=>'City',
					 'dentist_office_state'=>'State',
					 'dentist_office_zip_code'=>'Zip Code',
					 'dentist_office_email'=>'Office email',
					 'dentist_personal_cell'=>'Personal cell',
					 'Home address'=>'label',
					 'dentist_home_street'=>'Street',
					 'dentist_home_apt_no'=>'Suite #',
					 'dentist_home_city'=>'City',
					 'dentist_home_state'=>'State',
					 'dentist_home_zip'=>'Zip Code',
					 'state_dental_license_no'=>'State Dental License #',);
	
  }elseif($user->roles[0]=='seller'){
	  $active_status = my_active_auction_status();
	  if($active_status=='active'){
		  $disable_array = array('client_street','client_apt_no','client_city','client_state','client_zip_code');
	  }else{
		  $disable_array = array();
	  }
	  $field_array = array(
					 'client_street'=>'Street',
					'client_apt_no'=>'Suite #' ,
					 'client_city'=>'City',
					 'client_state'=>'State',
					 'client_zip_code'=>'Zip Code',
					 'client_cell_ph'=>'Client cell ph.',
					 'client_home_ph'=>'Client home ph.',);
					 
	$shopname = $user->user_login;
	$submitted = $_POST['wpforms']['fields'];
	//print_r($submitted);die;
	$phone = $_POST['client_home_ph'];
	$dokan_settings = array(
		'store_name'     => sanitize_text_field( wp_unslash($shopname)),
		'social'         => array(),
		'payment'        => array(),
		'phone'          => sanitize_text_field( wp_unslash($phone) ),
		'address'        => array(
						"street_1"=>strip_tags($_POST['client_street']),
						"street_2"=>strip_tags($_POST['client_apt_no']),
						"city"=>strip_tags($_POST['client_city']),
						"state"=>strip_tags($US_state[$_POST['client_state']]),
						"zip"=>strip_tags($_POST['client_zip_code']),
						"country"=>'US'),
		'show_email'     => 'no',
		'location'       => '',
		'find_address'   => '',
		'dokan_category' => '',
		'banner'         => 0,
	);
	update_user_meta( $user_id, 'dokan_profile_settings', $dokan_settings );				 
  }elseif($user->roles[0]=='advanced_ads_user'){
	  $field_array = array(
	  				 'Change Address' => 'label',
					 'billing_address_1'=>'Street',
					'billing_address_2'=>'Suite #' ,
					 'billing_city'=>'City',
					 'billing_state'=>'State',
					 'billing_postcode'=>'Zip Code',
					 'Change Phone Number' => 'label',
					 'billing_phone'=>'Cell <i class="fa fa-phone icon">&nbsp;</i>',);
	 $field_array =array();
 }else{
	  $field_array =array();
  }
  foreach($field_array as $key => $val){
	if(!in_array($key,$disable_array)){				 
  		update_user_meta( $user_id,$key, htmlentities( $_POST[ $key ] ) );
	}
  }
}
function my_user_after_updating_profile_custom( $user_id, $fields, $form_data, $userdata ) {
	global $US_state;
	//$user_id = get_current_user_id();
	$user = get_userdata( $user_id );
	if($user->roles[0]=='seller'){
		$shopname = $user->user_login;
		$submitted = $_POST['wpforms']['fields'];
		//print_r($submitted);die;
		$phone = $submitted['28'];
		$dokan_settings = array(
			'store_name'     => sanitize_text_field( wp_unslash($shopname)),
			'social'         => array(),
			'payment'        => array(),
			'phone'          => sanitize_text_field( wp_unslash($phone) ),
			'address'        => array(
							"street_1"=>strip_tags($submitted['22']),
							"street_2"=>strip_tags($submitted['23']),
							"city"=>strip_tags($submitted['24']),
							"state"=>strip_tags($US_state[$submitted['25']]),
							"zip"=>strip_tags($submitted['26']),
							"country"=>'US'),
			'show_email'     => 'no',
			'location'       => '',
			'find_address'   => '',
			'dokan_category' => '',
			'banner'         => 0,
		);
		update_user_meta( $user_id, 'dokan_profile_settings', $dokan_settings );
		//Auto Login Code
		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id);
		//wp_redirect(get_permalink(15));
		wp_redirect(dokan_get_navigation_url('new-auction-product'));
		exit;
	}else{
		//Auto Login Code
		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id);
		//wp_redirect(home_url('/my-account/edit-account/'));
		//exit;
	}

}
add_action( 'wpforms_user_registered', 'my_user_after_updating_profile_custom', 50, 4 );
add_action( 'wpforms_frontend_output_before', 'check_if_login', 10, 3 );
function check_if_login($form_data,$form){
	if($form->ID==154 || $form->ID==185){
		if (is_user_logged_in()){
			wp_redirect(wc_customer_edit_account_url());
		}
	}
}
add_filter ( 'woocommerce_account_menu_items', 'misha_remove_my_account_links',100,1 );
function misha_remove_my_account_links( $menu_links ){
 
	unset( $menu_links['edit-address'] ); // Addresses
 	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	//unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	unset( $menu_links['orders'] ); // Remove Orders
	unset( $menu_links['downloads'] ); // Disable Downloads
	unset( $menu_links['auctions-endpoint'] );
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	//unset( $menu_links['customer-logout'] ); // Remove Logout link
	 //$menu_links['my-auctions'] = __( 'My Auction', 'iconic' );
 //print_r($menu_links);
 	$auctions_item = array('dashboard' => __( 'Dashboard', 'woocommerce' ),'my-auctions' => __( 'My Auction', 'woocommerce' ) );
    // Remove 'subscriptions' key / label pair from original $items array
    unset( $menu_links['my-auctions'] );
	unset( $menu_links['dashboard'] );

    // merging arrays
    $menu_links = array_merge( $auctions_item, $menu_links );
	
	return $menu_links;
 
}
function iconic_add_my_auctions_endpoint() {
    add_rewrite_endpoint( 'my-auctions', EP_PAGES );
	if(isset($_POST['wptp_popup_id'])&& $_POST['wptp_popup_id'] !=""){
		$current_user = wp_get_current_user();
		if ($current_user->ID){
			add_option('termpopup_'.$current_user->ID,'accepted', '', 'yes' );
		}
	}
}
add_action( 'init', 'iconic_add_my_auctions_endpoint' );
function iconic_my_auctions_endpoint_content() {
    //echo 'Your new content';
	echo do_shortcode('[woocommerce_simple_auctions_my_auctions]');
	// [woocommerce_simple_auctions_my_auctions]
}
 
add_action( 'woocommerce_account_my-auctions_endpoint', 'iconic_my_auctions_endpoint_content' );


add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['shipping'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['seller'] );  	// Remove the additional information tab
	unset($tabs['more_seller_product']);
	unset($tabs['simle_auction_history']);
    return $tabs;
}
add_action( 'template_redirect','auction_handle_relist', 11 );
function auction_handle_relist(){
        if ( !is_user_logged_in() ) {
            return;
        }
		//Remoe persistent Items From Cart
		$user_id = dokan_get_current_user_id();
		foreach(WC()->cart->get_cart() as $key => $val ) {
			$user_id_cart_item = $val['custom_data']['user_id'];
			$_product = $val['data'];
			if($user_id_cart_item != $user_id){
			   if ($key) WC()->cart->remove_cart_item($key);
			}
		}
		if(isset($_GET['mode'])&& $_GET['mode']=='de-active-sub'){
			global $current_user;
			$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
			foreach($subscriptions_users as $row){
				$plan_id = get_post_meta($row->ID,'product_id',true);
				if($plan_id != 1141){
					$status = get_post_meta($row->ID,'status',true );
					if($status=='active'){
						/*update_post_meta ($row->ID,'status','cancelled');
						update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
						update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));*/
						$payment_due_date = get_post_meta( $row->ID, 'payment_due_date', true );
						$deactive_date = strtotime("+4 week", $payment_due_date);
						update_post_meta ($row->ID,'cancelled_date',$deactive_date);
						update_post_meta ($row->ID,'end_date',$deactive_date);
						update_post_meta ($row->ID,'_plan_status',"active_cancelled");
						/*
						$this->set( 'end_date', $this->payment_due_date );
						$this->set( 'payment_due_date', '' );
						$this->set( 'cancelled_date', current_time( 'timestamp' ) );
						$this->set( 'status', $new_status );
						*/
					}
				}
			}
		}
		global $woocommerce;
		if( is_cart() && WC()->cart->cart_contents_count == 0){
			wp_safe_redirect(home_url('/auction-activity/new-auction-product/'));
			exit();
		}
		if ( isset($_REQUEST['action'])&&$_REQUEST['action'] == 'clear_cart') {
			global $woocommerce;
			$woocommerce->cart->empty_cart();
		}
		if ( isset($_REQUEST['action'])&&$_REQUEST['action'] == 'add_to_cart') {
			pay_for_plan($_GET['auction_id'],$_GET['type']);
		}
        if ( !dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }
        $errors = array();
        global $woocommerce_auctions;
		if ( isset($_GET['action'])&&$_GET['action'] == 'delete_list') {
			$my_post = array('ID' =>base64_decode($_GET['product_id']),'post_status'   => 'private',);
			wp_update_post( $my_post );
			wp_redirect(dokan_get_navigation_url( 'auction' ));
		}
		if ( isset($_GET['action'])&&$_GET['action'] == 'multi-relist') {
			if ( ! current_user_can( 'dokan_add_auction_product' ) ) {
                return;
            }
			
			$product_ids = explode(",",$_GET['product_id']);
			//print_r($product_ids);die;
			global $wpdb,$monday,$thursday,$flash_cycle_start,$flash_cycle_end,$auction_expired_date_time;
			
			$user_id = dokan_get_current_user_id();
			global $US_state;
			$postcode = get_user_meta($user_id, 'client_zip_code', true );
			$billing_state = get_user_meta($user_id, 'client_state', true );
			WC()->cart->empty_cart();
			foreach($product_ids as $product_id){
				//$product_id = trim( $_GET['product_id'] );
				$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$product_id));
				$_auction_dates_to = get_post_meta($product_id, '_auction_dates_to', true );
				if(strtotime($_auction_dates_to) <= strtotime(date('Y-m-d H:i')) && $bid_count == 0){
					$timezone = getTimeZone_Custom();
					date_default_timezone_set($timezone);
					$today_date_time = date('Y-m-d H:i');
					$monday_auction_by = date("Y-m-d",strtotime("monday this week"))." 08:00";
					$this_monday = date("Y-m-d",strtotime("monday this week"))." 08:30";
					$this_saturday = date('Y-m-d', strtotime( 'saturday this week' ) )." 10:30";
					if ($today_date_time < $monday_auction_by) {
						$monday = $this_monday;
						$auction_expired_date_time = $this_saturday;
					}
					
					update_post_meta( $product_id, '_auction_dates_from', $monday );
					update_post_meta( $product_id, '_auction_dates_from_org', $monday );
					update_post_meta( $product_id, '_auction_dates_to', $thursday );
					update_post_meta( $product_id, '_flash_cycle_start', $flash_cycle_start );
					update_post_meta( $product_id, '_flash_cycle_end', $flash_cycle_end );
					update_post_meta( $product_id, '_auction_expired_date_time', $auction_expired_date_time);
					delete_post_meta($product_id, '_auction_fail_email_sent', 1);
					delete_post_meta($product_id, '_auction_finished_email_sent', 1);
					delete_post_meta($product_id, '_auction_fail_reason', 1);
					delete_post_meta($product_id, '_auction_closed', 1);
					update_post_meta( $product_id, '_auction_relist_expire','no');
					update_post_meta( $product_id, '_flash_status','no');
					update_post_meta( $product_id, '_auction_has_started',0);
					
					$fourRandomDigit = rand(1000,9999);
					$auction_no = $US_state[$billing_state].$postcode.'-'.date('Y-md',strtotime($monday))."-".$fourRandomDigit."-".str_pad($product_id,4,'0', STR_PAD_LEFT);
					update_post_meta( $product_id, 'auction_#', $auction_no);					
					$my_post = array('ID'           => $product_id,'post_status'   => 'pending',);
								 
					// Update the post into the database
					  wp_update_post( $my_post );
					if(isset($_GET['mode'])&&$_GET['mode'] == 'discount'){
						pay_for_auction_multi($product_id,'discount');
					}else{
						pay_for_auction_multi($product_id,'normal');
					}
					
					
				}else{
					wp_redirect(add_query_arg( array('product_id' =>$product_id, 'action' => 'relist_error' ), dokan_get_navigation_url( 'auction' ) ) );
					return;
				}
			}
			$url = get_permalink(48);
			if($mode=='discount'){
				$url =$url."?mode=relist";
			}
			wp_redirect($url);
		}
        if ( isset($_GET['action'])&&$_GET['action'] == 'relist') {
            if ( ! current_user_can( 'dokan_add_auction_product' ) ) {
                return;
            }
			
            $product_id = trim( $_GET['product_id'] );
			global $wpdb,$monday,$thursday,$flash_cycle_start,$flash_cycle_end,$auction_expired_date_time;
			$user_id = dokan_get_current_user_id();
			global $US_state;
			$postcode = get_user_meta($user_id, 'client_zip_code', true );
			$billing_state = get_user_meta($user_id, 'client_state', true );
			
			$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$product_id));
			$_auction_dates_to = get_post_meta($product_id, '_auction_dates_to', true );
			if(strtotime($_auction_dates_to) <= strtotime(date('Y-m-d H:i')) && $bid_count == 0){
				$timezone = getTimeZone_Custom();
				date_default_timezone_set($timezone);
				$today_date_time = date('Y-m-d H:i');
				$monday_auction_by = date("Y-m-d",strtotime("monday this week"))." 08:00";
				$this_monday = date("Y-m-d",strtotime("monday this week"))." 08:30";
				$this_saturday = date('Y-m-d', strtotime( 'saturday this week' ) )." 10:30";
				if ($today_date_time < $monday_auction_by) {
					$monday = $this_monday;
					$auction_expired_date_time = $this_saturday;
				}
				
				update_post_meta( $product_id, '_auction_dates_from', $monday );
				update_post_meta( $product_id, '_auction_dates_from_org', $monday );
				update_post_meta( $product_id, '_auction_dates_to', $thursday );
				update_post_meta( $product_id, '_flash_cycle_start', $flash_cycle_start );
				update_post_meta( $product_id, '_flash_cycle_end', $flash_cycle_end );
				update_post_meta( $product_id, '_auction_expired_date_time', $auction_expired_date_time);
				delete_post_meta($product_id, '_auction_fail_email_sent', 1);
				delete_post_meta($product_id, '_auction_finished_email_sent', 1);
				delete_post_meta($product_id, '_auction_fail_reason', 1);
				delete_post_meta($product_id, '_auction_closed', 1);
				update_post_meta( $product_id, '_auction_relist_expire','no');
				update_post_meta( $product_id, '_flash_status','no');
				update_post_meta( $product_id, '_auction_has_started',0);
				$fourRandomDigit = rand(1000,9999);
				$auction_no = $US_state[$billing_state].$postcode.'-'.date('Y-md',strtotime($monday))."-".$fourRandomDigit."-".str_pad($product_id,4,'0', STR_PAD_LEFT);
				update_post_meta( $product_id, 'auction_#', $auction_no);
				$my_post = array('ID'           => $product_id,'post_status'   => 'pending',);
							 
				// Update the post into the database
				  wp_update_post( $my_post );
				if(isset($_GET['mode'])&&$_GET['mode'] == 'discount'){
					pay_for_auction($product_id,'discount');
				}else{
					pay_for_auction($product_id,'normal');
				}
			}else{
				wp_redirect(add_query_arg( array('product_id' =>$product_id, 'action' => 'relist_error' ), dokan_get_navigation_url( 'auction' ) ) );
                return;
				//$errors[] = __( 'You can not relist this auction.', 'dokan-auction' );
				//$errors = apply_filters( 'dokan_add_auction_product', $errors );
			}
            
        }
		if ( isset($_POST['action'])&&$_POST['action'] == 'update_price') {
			if ( ! current_user_can( 'dokan_add_auction_product' ) ) {
                return;
            }
			
            $product_id = trim( $_POST['product_id'] );
			$_auction_start_price = get_post_meta( $product_id, '_auction_start_price',TRUE);
			if($_POST['_new_price'] < $_auction_start_price){
				wp_redirect(add_query_arg( array('product_id' =>$product_id, 'action' => 'update_error' ), dokan_get_navigation_url( 'auction' ) ) );
            	return;
			}
			global $wpdb;
			global $today_date_time;
			$_flash_cycle_start = get_post_meta( $product_id , '_flash_cycle_start' , TRUE);
			$_flash_cycle_end = get_post_meta( $product_id , '_flash_cycle_end' , TRUE);
			
			update_post_meta( $product_id, '_auction_start_price', $_POST['_new_price']);
			update_post_meta( $product_id, '_auction_dates_from', $_flash_cycle_start);
			update_post_meta( $product_id, '_auction_dates_to', $_flash_cycle_end);
			update_post_meta( $product_id, '_flash_status','yes');
			delete_post_meta($product_id, '_auction_fail_email_sent', 1);
			delete_post_meta($product_id, '_auction_finished_email_sent', 1);
			delete_post_meta($product_id, '_auction_fail_reason', 1);
			delete_post_meta($product_id, '_auction_closed', 1);
			wp_redirect(add_query_arg( array('product_id' =>$product_id, 'action' => 'update_success' ), dokan_get_navigation_url( 'auction' ) ) );
            return;
		}
}
//add_action( 'woocommerce_simple_auctions_before_place_bid', 'play_sound' );
//add_filter('woocommerce_simple_auctions_placed_bid_message', 'play_sound',10,2 );
function play_sound($message,$product_id){
	echo $message;
	if(strpos($message,"placed bid for") > 0){
		$attr = array(
					'src'      => home_url('/applause-4.mp3'),
					'loop'     => '',
					'autoplay' => 'yes',
					'preload'  => 'none'
				);
		$message .= wp_audio_shortcode( $attr );
		
	}
	return $message;
}
add_action( 'woocommerce_before_single_product', 'play_sound2', 1 );
function play_sound2($product_id){
	global $product, $post,$today_date_time;
	$product_id = $post->ID;
	//
	$today = date("Y-m-d G:i",strtotime($today_date_time));
	//echo date("Y-m-d G:i",strtotime($today_date_time)).'=='.$product->get_auction_end_time();
	$_auction_current_bid = get_post_meta($product_id, '_auction_current_bid', true );
	$_auction_closed = get_post_meta($product_id, '_auction_closed', true );
	if (($product->get_auction_closed() == '2' && !$product->get_auction_payed())){
		$attr = array(
					'src'      => home_url('wp-content/uploads/sounds/auction_sucess.mp3'),
					'loop'     => '0',
					'autoplay' => 'yes',
					'preload'  => 'none',
					'class' => 'hide'
				);
		if(strtotime($today)==strtotime($product->get_auction_end_time())){
				echo '<audio id="myAudio" controls="false" volume=".2" style="display:none;"></audio>';
				echo wp_audio_shortcode( $attr );
		}
	}else{
		if(!$_auction_current_bid && $_auction_closed==1){
			$attr = array(
					'src'      => home_url('wp-content/uploads/sounds/auction_failure.mp3'),
					'loop'     => '0',
					'autoplay' => 'yes',
					'preload'  => 'none',
					'class' => 'hide'
				);
				if(strtotime($today)==strtotime($product->get_auction_end_time())){
					echo '<audio id="myAudio" controls="false" volume=".2" style="display:none;"></audio>';
					echo wp_audio_shortcode( $attr );
				}
		}else{
			$product = get_product($product_id);
			$auctionend = new DateTime($product->get_auction_dates_to());
			$auctionendformat = $auctionend->format('Y-m-d H:i:s');
			$time = current_time( 'timestamp' );
			$timeplus5 = date('Y-m-d H:i:s', strtotime('+5 minutes', $time));
			/*/
			if ($timeplus5 > $auctionendformat) {
				echo '<script type="text/javascript">jQuery( document ).ready(function() { playAudioLoop("'.home_url('wp-content/uploads/sounds/087168277-helicopter-sound-effect.mp3').'"); });</script><input type="hidden" id="play_snipping" value="yes" />';
			}else{
			
			}
			*/
		/*$attr = array(
					'src'      => home_url('wp-content/uploads/sounds/033581166-energetic-auctioneer-cattle-li.mp3'),
					'loop'     => 'yes',
					'autoplay' => 'yes',
					'preload'  => 'none',
					'class' => 'hide'
				);*/
		}
	}
		
		
		//echo do_shortcode('[audio src="'.home_url('/roller.mp3').'" autoplay="true"]');
		//echo do_shortcode('[sc_embed_player_template1 autoplay=true loops="true" volume="100" fileurl="'.home_url('wp-content/uploads/sounds/087168277-helicopter-sound-effect.mp3').'"]');
		
	//return $message;
}
add_filter( 'wp_audio_shortcode', 'filter_function_name_4420', 10, 5 );
function filter_function_name_4420( $html, $atts, $audio, $post_id, $library ){
	// filter...
	$html = str_replace("<audio",'<audio controlsList="nodownload" ',$html);
	return $html;
}
function title_filter( $where, &$wp_query){
	global $wpdb;
	// 2. pull the custom query in here:
	if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
	}
	return $where;
}
function getActiveAD_id($searchTerm,$UserID){
		$args = array(
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => 'post_date',
			'search_prod_title' =>$searchTerm,
			//'meta_query' => array(array('key' => 'ad_user',get_current_user_id(),'compare' => 'EQUAL')),
			 'meta_query' => array(
											array(
												'key'     => 'ad_user',
												'value'   => array($UserID),
												'compare' => 'IN',
											),
										),
			'post_type' =>'advanced_ads',
			'order'               => 'DESC',
			'posts_per_page'      => -1,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		$wp_query = new WP_Query($args);
		remove_filter( 'posts_where', 'title_filter', 10 );
		return $wp_query->found_posts;
}
function dokan_header_user_menu_custom() {
	$cart_total = WC()->cart->total;
    ?>
    
    <ul class="nav navbar-nav navbar-right">
        <?php if ( is_user_logged_in() ) { ?>

            <?php
            global $current_user;
            
            
            $is_seller = false;
            
            $user_id = $current_user->ID;
            if(  function_exists( 'dokan_is_user_seller' )){
                $is_seller = dokan_is_user_seller( $user_id );
            }
			
              if(isset($_GET['mode'])&& $_GET['mode']=='popup'){?>
				  <li class="dropdown"><a href="javascript:" class="menu-login-deactive" style="color:#fff !important;text-decoration:none !important;">hello!</a></li>
             <?php }else{               
				$deactivate_advertiser = get_user_meta($current_user->ID, 'deactivate_advertiser',true);
				if($deactivate_advertiser=='Yes'){?>
					<li class="dropdown"><a href="<?php echo home_url('/?customer-logout=true');?>" class="menu-login-deactive" style="color:#fff !important;text-decoration:none !important;">Log out</a></li>
				<?php }else{
				if($is_seller){
					$display_name = '<span style="text-transform:lowercase">hello!</span> <span class="no_translate">'.esc_html( $current_user->first_name).'</span>';
				}else{
					$display_name = '<span style="text-transform:lowercase">hello!</span> <span class="no_translate">'.esc_html( $current_user->first_name).'</span>';
					$designation = get_user_meta( $user_id, 'designation', true );
					if($designation!=""){
						//$display_name .=', '.$designation;
					}
				}
				
				
				if($current_user->roles[0]=='advanced_ads_user' || $current_user->roles[0]=='ad_demo'){
						$display_name = '<span style="text-transform:lowercase">hello!</span>';
				}
				?>
				<li class="dropdown">
					<a href="#" class="menu-login dropdown-toggle" data-toggle="dropdown"><?php echo $display_name;?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php if($is_seller){?>
						<li><a href="<?php echo dokan_get_navigation_url('auction'); ?>"><?php _e( 'ShopADoc® Auction Activity', 'dokan-theme' ); ?></a></li>
						<li><a href="<?php echo dokan_get_navigation_url( 'new-auction-product' ); ?>"><?php _e( 'List My Service', 'dokan-theme' ); ?></a></li>
						<li><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e( 'Edit Contact Info', 'dokan-theme' ); ?></a></li>	
						<?php }else{?>
						<?php if($current_user->roles[0]=='advanced_ads_user'){?>
						<?php 
							$dentist_ad = getActiveAds('D');
							$client_ad =  getActiveAds('C');
							global $wpdb;
						/*$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '% ".$type."%' ORDER BY ID ASC";
						$results = $wpdb->get_results($query, OBJECT);
						$ads_count = $wpdb->num_rows;*/
						//$ads = $wpdb->get_results( "SELECT post_id as ID FROM {$wpdb->prefix}postmeta WHERE meta_key = 'ad_user' and meta_value=".get_current_user_id(), OBJECT );
						/*$args = array(
											'post_status'         => 'publish',
											'ignore_sticky_posts' => 1,
											'orderby'             => 'post_date',
											'post_type' =>'advanced_ads',
											'order'               => 'DESC',
											'posts_per_page'      => -1,
											'meta_query' => array(array('key' => 'ad_user',get_current_user_id(),'compare' => 'EQUAL')),
										);
						$query = new WP_Query($args );*/
						//$count = $query->found_posts;
						/*$user_id    = get_current_user_id();
						$favourites = swf_get_favourites( $user_id) ;
						global $radius_distance;
						$i = 0;
                        if ( $product_query->have_posts() ) {
                            while ($product_query->have_posts()) {*/
							$client_ad = getActiveAD_id("C ",get_current_user_id());
							$dentist_ad = getActiveAD_id("D ",get_current_user_id());
							if($client_ad==0 ){
								$client_class = 'availability_popup';
							}else{
								$client_class = '';
							}
							if($dentist_ad==0){
								$dentist_class = 'availability_popup';
							}else{
								$dentist_class = '';
							}
						
				
						if($deactivate_advertiser=='Yes'){?>
						<?php }else{
								/*$current_date_option_label = "_".date('m_y');
								$rotations_client = array();
								for($i=1;$i<= 10;$i++){		
									$rotaion_array = array();
									for($j=1;$j<= 4;$j++){
										$postion = 'position'.$i.'_col'.$j.'_client'.$current_date_option_label;
										$postion_value = get_option($postion);
										if($postion_value){
											$rotaion_array[] = $postion_value;
										}
									}
									if(!empty($rotaion_array)){
										$rotations_client[] = implode(",",$rotaion_array); 
									}
								}
								
								$rotations_dentist = array();
								for($i=1;$i<= 10;$i++){
									
									$rotaion_array = array();
									for($j=1;$j<= 4;$j++){
										$postion = 'position'.$i.'_col'.$j.'_dentist'.$current_date_option_label;
										$postion_value = get_option($postion);
										if($postion_value){
											$rotaion_array[] = $postion_value;
										}
									}
									if(!empty($rotaion_array)){
										$rotations_dentist[] = implode(",",$rotaion_array); 
									}
								}
								if(!empty($rotations_client)){
									$client_class = 'availability_popup';
								}else{
									$client_class = '';
								}
								if(!empty($rotations_dentist)){
									$dentist_class = 'availability_popup';
								}else{
									$dentist_class = '';
								}*/
							?>
						   <li><a href="/" ><?php _e( 'ShopADoc® homepage', 'dokan-theme' ); ?></a></li>
							<li><a href="<?php echo site_url(); ?>/auction-3977/demo-auction/?screen=client" class="<?php echo $client_class;?>" ><?php _e( 'Client Ads', 'dokan-theme' ); ?></a></li>
							<li><a href="<?php echo site_url(); ?>/auction-3977/demo-auction/" class="<?php echo $dentist_class;?>"><?php _e( 'Dentist Ads', 'dokan-theme' ); ?></a></li>
							<li><a href="#" class="ad-analytics-img_adver"><?php _e( 'Analytics', 'dokan-theme' ); ?></a></li>
                       		<li><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e( 'Change Password', 'dokan-theme' ); ?></a></li>	
							<li><a href="<?php echo site_url(); ?>/contact-support/"><?php _e( 'Contact', 'dokan-theme' ); ?></a></li>
						<?php }?>
						<?php }elseif($current_user->roles[0]=='ad_demo'){?>
						<li><a href="/"><?php _e( 'ShopADoc® homepage', 'dokan-theme' ); ?></a></li>
						<li><a href="<?php echo site_url(); ?>/auction-3977/demo-auction/?screen=client" ><?php _e( 'Client Ads', 'dokan-theme' ); ?></a></li>
						<li><a href="<?php echo site_url(); ?>/auction-3977/demo-auction/" ><?php _e( 'Dentist Ads', 'dokan-theme' ); ?></a></li>
						<li><a href="#" class="ad-analytics-img"><?php _e( 'Analytics', 'dokan-theme' ); ?></a></li>
					   <!-- <li><a href="<?php echo site_url(); ?>/contact-support-demo/"><?php _e( 'Contact', 'dokan-theme' ); ?></a></li>-->
						<li><a href="<?php echo site_url(); ?>/contact-support/"><?php _e( 'Contact', 'dokan-theme' ); ?></a></li>
						<?php }else{
							$count_auctions_in_area = count_auctions_in_area();
						?>
						<li><a href="<?php echo site_url(); ?>/shopadoc-auction-activity/"><?php _e( 'ShopADoc® Auction Activity', 'dokan-theme' ); ?></a></li>
	<!--                    <li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>/my-auctions/"><?php _e( 'ShopADoc® Auction History', 'dokan-theme' ); ?></a></li>
	-->				<?php if($count_auctions_in_area==0){?>
							<li><a href="#" class="payment_option_no_auction" onclick="jQuery('.dropdown').removeClass('open');"><?php _e( 'Auction Participation Options', 'dokan-theme' ); ?></a></li>
						<?php }else{ ?>	
							<li><a href="#" class="bid_on" onclick="jQuery('.dropdown').removeClass('open');"><?php _e( 'Auction Participation Options', 'dokan-theme' ); ?></a></li>
						<?php }?>
						<li><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e( 'Edit Contact Info', 'dokan-theme' ); ?></a></li>	
						<!--<li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' )); ?>"><?php _e( 'Change Payment Plan', 'dokan-theme' ); ?></a></li>-->
						<li><a href="<?php echo wc_get_endpoint_url( 'payment-methods','', get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php _e( 'Update Card on File', 'dokan-theme' ); ?></a></li>	
						<?php }?>
						<?php }?>
						<li ><a href="<?php echo home_url('/?customer-logout=true'); ?>"><?php _e( 'Log out', 'dokan-theme' ); ?></a></li>
					</ul>
				</li>
				<?php }?>
            <?php }?>
        <?php } else { ?>
            <li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>" id="login_txt"><?php _e( 'Auction Sign-in', 'dokan-theme' ); ?></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle register" data-toggle="dropdown"><?php _e( 'Register', 'dokan-theme' ); ?> <!--<b class="caret"></b>--></a>
            </li>
        <?php } ?>
    </ul>
    <?php
}
add_action('wp_logout','ps_redirect_after_logout');
function ps_redirect_after_logout(){
         wp_redirect(home_url('/'));
         exit();
}
add_filter( 'login_url', 'my_login_page', 10, 3 );
function my_login_page( $login_url, $redirect, $force_reauth ) {
	$redirect_text = '';
	if($redirect !=""){
		$redirect_text = '?redirect_to='.$redirect;
	}
    return home_url( '/my-account/'.$redirect_text);
}
add_action('wp_trash_post', 'restric_from_trash',10,1);
function restric_from_trash($id){
	global $demo_listing;
	if($id==126 || $id==942 || $id==948 || $id==1141 || $id==1642 || $id==$demo_listing){
		wp_redirect( admin_url( 'edit.php?post_type=product' ) );
		exit;
	}
}


add_filter( 'dokan_get_dashboard_nav', 'dokan_add_help_menu');
function dokan_add_help_menu( $urls ) {
	unset($urls['dashboard']);
    /*$urls['help'] = array(
        'title' => __( 'Help', 'dokan'),
        'icon'  => '<i class="fa fa-user"></i>',
        'url'   => dokan_get_navigation_url( 'help' ),
        'pos'   => 51
    );*/
    return $urls;
}
/*add_filter( 'dokan_query_var_filter', 'dokan_load_document_menu' );
function dokan_load_document_menu( $query_vars ) {
    $query_vars['help'] = 'help';
    return $query_vars;
}
add_action( 'dokan_load_custom_template', 'dokan_load_template' );
function dokan_load_template( $query_vars ) {
    if ( isset( $query_vars['help'] ) ) {
        require_once dirname( __FILE__ ). '/help.php';
        exit();
    }
}*/
add_filter('woocommerce_checkout_fields', 'readdonly_billing_country_select_field');
function readdonly_billing_country_select_field( $fields ) {
    // Set billing and shipping country to AU
    //WC()->customer->set_billing_country('AU');
	global $current_user,$US_state;
	$user_id = $current_user->ID;	
	$user = wp_get_current_user();
	if($user->roles[0]=='seller'){
		$client_state = get_user_meta( $user_id, 'client_state', true);
	}else{
		$client_state = get_user_meta( $user_id, 'dentist_office_state', true);
	}
	if($client_state !=''){
    	// Make billing country field read only
    	$fields['billing']['billing_state']['custom_attributes'] = array( 'disabled' => 'disabled' );
	}

    return $fields;
}
add_action('woocommerce_after_order_notes', 'billing_countryand_state_hidden_field');
function billing_countryand_state_hidden_field($checkout){
	global $current_user,$US_state;
    $user = wp_get_current_user();
    $user_id = $current_user->ID;
	if($user->roles[0]=='seller'){
		$client_state = get_user_meta( $user_id, 'client_state', true);
	}else{
		$client_state = get_user_meta( $user_id, 'dentist_office_state', true);
	}
	if($client_state !=''){
   		echo '<input type="hidden" class="input-hidden" name="billing_state"  value="'.$US_state[$client_state].'">';
	}

}
add_filter('woocommerce_checkout_get_value', function($input, $key ) {
    global $current_user,$US_state;
	$user_id = $current_user->ID;	
	$user = wp_get_current_user();
	if($user->roles[0]=='seller'){
		$user_id = dokan_get_current_user_id();
		$client_street = get_user_meta( $user_id, 'client_street', true);
		$client_apt_no = get_user_meta( $user_id, 'client_apt_no', true);
		$client_city = get_user_meta( $user_id, 'client_city', true);
		$client_state = get_user_meta( $user_id, 'client_state', true);
		$client_zip_code = get_user_meta( $user_id, 'client_zip_code', true);
		$client_cell_ph = get_user_meta( $user_id, 'client_cell_ph', true );
		$client_state = $US_state[$client_state];
	}else{
		$user_id = dokan_get_current_user_id();
		$client_street = get_user_meta( $user_id, 'dentist_office_street', true);
		$client_apt_no = get_user_meta( $user_id, 'dentist_office_apt_no', true);
		$client_city = get_user_meta( $user_id, 'dentist_office_city', true);
		$client_state = get_user_meta( $user_id, 'dentist_office_state', true);
		$client_zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true);
		$client_cell_ph = get_user_meta( $user_id, 'dentist_personal_cell', true );
		$client_state = $US_state[$client_state];
	}
	/*$client_street = get_user_meta( $user_id, 'client_street', true );
	$client_apt_no = get_user_meta( $user_id, 'client_apt_no', true );
	$client_city = get_user_meta( $user_id, 'client_city', true );
	$client_state = get_user_meta( $user_id, 'client_state', true );
	$client_zip_code = get_user_meta( $user_id, 'client_zip_code', true );
	$client_cell_ph = get_user_meta( $user_id, 'client_cell_ph', true );
	$client_home_ph = get_user_meta( $user_id, 'client_home_ph', true );
	$client_state = $US_state[$client_state];*/
    switch ($key) :
	    case 'billing_first_name':
        case 'shipping_first_name':
            return $current_user->first_name;
        break;

        case 'billing_last_name':
        case 'shipping_last_name':
            return $current_user->last_name;
        break;
		case 'billing_address_1':
            return $client_street;
        break;
		case 'billing_address_2':
            return $client_apt_no;
        break;
		case 'billing_city':
            return $client_city;
        break;
		case 'billing_state':
            return $client_state;
        break;
		case 'billing_postcode':
            return $client_zip_code;
        break;
        case 'billing_email':
            return $current_user->user_email;
        break;
        case 'billing_phone':
            return $client_cell_ph;
        break;
    endswitch;
}, 10, 2);

add_filter( 'dokan_register_scripts', 'dokan_register_scripts_custom');
function dokan_register_scripts_custom() {
	wp_deregister_script( 'dokan-script' );
	$scripts = array('dokan-script' => array(
                'src'       => get_template_directory_uri(). '-child/dokan/dokan.js',
                'deps'      => array( 'imgareaselect', 'customize-base', 'customize-model', 'dokan-i18n-jed' ),
                //'version'   => get_template_directory_uri() . '-child/dokan/dokan.js',
            ));
	foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : true;
            $version   = isset( $script['version'] ) ? $script['version'] : DOKAN_PLUGIN_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }		
}
// Removes Order Notes Title - Additional Information & Notes Field
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
// Remove Order Notes Field
add_filter( 'woocommerce_checkout_fields' , 'remove_order_notes' );
function remove_order_notes( $fields ) {
     unset($fields['order']['order_comments']);
     return $fields;
}
add_action( 'init', function() {
	
	add_shortcode( 'site_url', function( $atts = null, $content = null ) {
		return home_url("/");
	} );
	if (is_user_logged_in()){
		global $wp;
		$current_url =  home_url( $wp->request );
		$user = wp_get_current_user();
		if($user->roles[0]=='advanced_ads_user' && strpos($_SERVER['REQUEST_URI'],"logout")===false){
			//deactivate_advertiser
			$deactivate_advertiser = get_user_meta($user->ID, 'deactivate_advertiser',true);
			if(strpos($_SERVER['REQUEST_URI'],"/ad-analytics")===false && $deactivate_advertiser=='Yes' ){
				wp_redirect(home_url('/ad-analytics'));
				exit;
			}
		}
	}

} );

function my_test_shortcode(){
	$year = date('Y');
	echo $year;
}
add_shortcode('my-test', 'my_test_shortcode');
//remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
//add_action( 'woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form' );
//add_action( 'advanced-ads-can-display', 'advads_show_ads_on_posts_only',10,2);
function advads_show_ads_on_posts_only($can_display, $ad){
    global $post;
	$ad_id = $ad->id;
	$ad_location = get_post_meta($ad_id, 'ad_location',true);
	$dentist_office_address = getDentistAddress();
	if(trim($dentist_office_address) !="" && trim($ad_location) !=""){
		$Distance = get_driving_information($dentist_office_address,$ad_location);
		if($Distance > 50){
			return false;
		}
	}
    //if( ! isset( $post->post_type ) || 'post' !== $post->post_type ){
 		//return false;
    //}
    return $can_display;
}
function theme_slug_filter_the_content( $content ) {
	//8,46,48,91,99
	global $wp;
	$pageIDS = array(8,48,91,99);
	$current_url =  home_url( $wp->request );
	if(strpos($current_url,"my-account/edit-account") > 0){
		 /*$content .= '<style type="text/css">#post-46 .entry-content .woocommerce-MyAccount-content{
	background: url('.home_url().'/wp-content/themes/dokan-child/watermark.png) no-repeat;
	background-size: cover;
	min-height:1000px;
}</style>';*/
	}
	if(in_array(get_the_ID(),$pageIDS) || strpos($current_url,"my-account/edit-account") > 0){
    $custom_content = '<div class="watermark"><img src="'.get_site_url().'/wp-content/themes/dokan-child/watermark-100.png" width="68" title="watermark" alt="watermark" /></div>';
    $custom_content .= $content;
    return $custom_content;
	}else{
		return $content;
	}
}
add_filter( 'the_content', 'theme_slug_filter_the_content' );

add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
function change_default_checkout_country() {
  return 'US'; // country code
}
//add_action('woocommerce_simple_auction_finished','woocommerce_simple_auction_finished_custom',50);
function woocommerce_simple_auction_finished_custom($product_id){
	 $product = get_product($product_id);
	 if ('auction' === $product->get_type() ) {
		 $_auction_dates_extend = get_post_meta($product_id, '_auction_dates_extend',TRUE);
		 $_auction_extend_counter = get_post_meta($product_id, '_auction_extend_counter',TRUE);
		 if($_auction_dates_extend=='yes'){
			  	$auctionend = new DateTime($product->get_auction_dates_to());
				$auctionendformat = $auctionend->format('Y-m-d H:i:s');
				//$time = current_time( 'timestamp' );
				$timeplus5 = date('Y-m-d H:i:s');
				if ($timeplus5 > $auctionendformat) {
					return TRUE;
				}else{
					return false;
				}
		}
	}
}
/* Auto Extend Auction by 2 min when a bid is placed within the last 5mins */
add_action( 'woocommerce_simple_auctions_place_bid', 'woocommerce_simple_auctions_extend_time', 50 );
add_action( 'woocommerce_simple_auctions_outbid', 'woocommerce_simple_auctions_extend_time', 50 );
function woocommerce_simple_auctions_extend_time($data) {
	global $wpdb,$today_date_time;
    $product = get_product( $data['product_id'] );
    if ('auction' === $product->get_type() ) {
        $auctionend = new DateTime($product->get_auction_dates_to());
        $auctionendformat = $auctionend->format('Y-m-d H:i:s');
        $time = current_time( 'timestamp' );
        $timeplus5 = date('Y-m-d H:i:s', strtotime('+5 minutes', $time));
		if ($timeplus5 > $auctionendformat) {
			$timeminus5 = date('Y-m-d H:i:s', strtotime('-5 minutes',strtotime($auctionend->format('Y-m-d H:i:s'))));
			//$bid_count = $wpdb->get_var("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = '".$data['product_id']."' and (date BETWEEN '".$timeminus5."' AND '".$auctionend->format('Y-m-d H:i:s')."') LIMIT 1");
			//if($bid_count >=2){
				update_post_meta( $data['product_id'], '_extend_time_start', $auctionend->format('Y-m-d H:i:s') );
				//$auctionend->add(new DateInterval('PT5S'));
				//update_post_meta( $data['product_id'], '_auction_dates_to', $auctionend->format('Y-m-d H:i:s') );
				update_post_meta($data['product_id'], '_auction_dates_extend', 'yes' );
				update_post_meta($data['product_id'], '_auction_extend_counter', 'yes' );
				$_auction_extend_first_time = get_post_meta($data['product_id'],'_auction_extend_first_time', true );
				if($_auction_extend_first_time==""){
					update_post_meta($data['product_id'], '_auction_extend_first_time', 'yes' );
				}else{
					update_post_meta($data['product_id'], '_auction_extend_first_time', 'no' );
				}
			//}
        }
    }
}
add_action('woocommerce_thankyou', 'enroll_student', 10, 1);
function enroll_student( $order_id ) {

    if ( ! $order_id )
        return;

    // Getting an instance of the order object
    $order = wc_get_order( $order_id );

    // iterating through each order items (getting product ID and the product object) 
    // (work for simple and variable products)
    foreach ( $order->get_items() as $item_id => $item ) {
		//print_r($item->get_meta_data( __( 'Auction #', 'iconic' )));
		$auction_id = wc_get_order_item_meta( $item_id,  'Auction #', true );
		$auction_id = getAuctionId($auction_id);
        if( $item['variation_id'] > 0 ){
            $product_id = $item['variation_id']; // variable product
        } else {
            $product_id = $item['product_id']; // simple product
        }
		//$auction_id = $item['auction_id'];
        // Get the product object
        //$product = wc_get_product( $product_id );

    }
	if($product_id==942 || $product_id==948 || $product_id==1141){
		
		echo '<p style="width:100%;float:left;" >';
		if($auction_id){
			echo '<a href="'.get_permalink( $auction_id ).'" class="bid_button button alt" >Return to Auction Listing</a>&nbsp;';
		}
    	echo '<a href="'.site_url().'/shopadoc-auction-activity/" class="bid_button button alt proceed_to_auction" id="not_print">Proceed to Auction</a><span id="checkout_tooltip" ><span class="tooltip_New checkout"><span class="tooltips custom_m_bubble"  style="float:left !important;">&nbsp;</span><span class="tooltip_text">Please check your Inbox, Spam, Junk, & Promotions tab for receipts & correspondence <br class="only_print">from ShopADoc.</span></span></span>';
		echo "</p>";
	}
	$user = wp_get_current_user();
	if($user->roles[0]=='seller'){
		echo '<p style="width:100%;float:left;" >';
		echo '<a href="'.get_permalink( $auction_id ).'" style="padding:15px 30px;font-weight:bold;" class="bid_button button alt proceed_to_auction not_print"  id="not_print" >Proceed to Auction</a><span id="checkout_tooltip"><span class="tooltip_New checkout"><span class="tooltips custom_m_bubble"  style="float:left !important;">&nbsp;</span><span class="tooltip_text">Please check your Inbox, Spam, Junk, & Promotions tab for receipts & correspondence <br class="only_print">from ShopADoc.</span></span></span><!--&nbsp;<a href="'.dokan_get_navigation_url('auction').'" class="bid_button button alt" >Auction Activity</a>-->';
			echo "</p>";
	}

}
function ea_wpforms_description_above_field( $properties ) {
	if(in_array("wpforms-field-text",$properties['container']['class'])){
		$properties['description']['position'] = 'before';
	}
	return $properties;
}

if(isset($_REQUEST['mode'])&& ($_REQUEST['mode'] =='reactive' || $_REQUEST['mode'] =='register')){
	add_filter( 'woocommerce_checkout_fields' , 'bbloomer_add_field_and_reorder_fields' );
	function bbloomer_add_field_and_reorder_fields( $fields ) {
		$fields['billing']['page_mode'] = array(
		'default'=>$_GET['mode'],
		'priority' => 51,
		'class'     => array('hide'),
		 );
		return $fields;
	}
}
//add_filter('the_title','some_callback');
function some_callback($title){
    global $post,$wp;
	$types = array('post', 'page');
	if (in_array($post->post_type,$types)){
		if($post->ID == 46 /*&& $title =='Registration Info'*/){
			$current_url =  home_url( $wp->request );
			if(strpos($current_url,"my-account/edit-account") > 0){
				$user = wp_get_current_user();
				if($user->roles[0]=='seller'){
					$title = "Edit Client Contact";
				}else{
					$title = "Edit Registration Info";
				}
			}elseif(strpos($current_url,"my-account/my-auctions") > 0){
				$title = "ShopADoc® Auction History";
			}elseif(strpos($current_url,"my-account/payment-methods") > 0){
				$title = "Update Card on File";
			}elseif(strpos($current_url,"my-account/add-payment-method") > 0){
				$title = "Add Credit/Debit Card Info";
			}else{
				$title = "Change Payment Plan";
			}
			
		}
	}
	return $title;
}
global $title_right;
$title_right ="<span id='not_print' class='right-align-txt'>ShopADoc<span class='TM_title'>®</span></span>";
function wpse309151_title_update( $title, $id = null ) {
	global $post,$wp,$title_right;
    if ( ! is_admin() && ! is_null( $id ) ) {
        $post = get_post( $id );
		$current_url =  home_url( $wp->request );
        if ( $post instanceof WP_Post && ( $post->post_type == 'post' || $post->post_type == 'page' ) ) {
			if($post->ID == 48 || $post->ID == 54 || $post->ID == 56 || $post->ID == 60 || $post->ID == 62 || $post->ID == 66 || $post->ID == 68 || $post->ID == 91|| $post->ID == 99 || $post->ID == 1735 || $post->ID == 1325){
				$title = $post->post_title.$title_right;
				
			}
            if($post->ID == 46 /*&& $title =='Registration Info'*/){
			
			if(strpos($current_url,"my-account/edit-account") > 0){
				$user = wp_get_current_user();
				if($user->roles[0]=='seller'){
					$title = "Edit Client Contact";
				}else{
					$title = "Edit Registration Info";
				}
			}elseif(strpos($current_url,"my-account/my-auctions") > 0){
				$title = "ShopADoc® Auction History";
			}elseif(strpos($current_url,"my-account/my-auctions") > 0){
				$title = "ShopADoc® Auction History";
			}elseif(strpos($current_url,"my-account/payment-methods") > 0){
				$title = "Update Card on File";
			}elseif(strpos($current_url,"my-account/add-payment-method") > 0){
				$title = "Add Credit/Debit Card Info";
			}else{
				$title = "Change Payment Plan";
			}
			if (!is_user_logged_in()){
				$title = "Sign-in".$title_right;
			}
			}
			
			$title = $post->post_title.$title_right;
			if($post->ID == 606){
				$title = $post->post_title;
			}
			if(strpos($current_url,"checkout/order-received") > 0){
				$order_id = str_replace('checkout/order-received/','',$wp->request);
				$order = wc_get_order( $order_id );
				$items = $order->get_items();
				$product_ids = array();
				foreach ( $items as $item ) {
					array_push($product_ids,$item->get_product_id());
				}
				if(in_array('1141',$product_ids)){
					$title ="ShopADoc<span class='TM_title'>®</span>&nbsp;&nbsp;Annual Registration Renewal";
				}else{
					$title ="ShopADoc<span class='TM_title'>®</span>&nbsp;&nbsp;Auction Participation Receipt";
				}
			}
        }
    }
    return $title;
}
add_filter( 'the_title', 'wpse309151_title_update', 10, 2 );

function wpse309151_remove_title_filter_nav_menu( $nav_menu, $args ) {
    // we are working with menu, so remove the title filter
    remove_filter( 'the_title', 'wpse309151_title_update', 10, 2 );
    return $nav_menu;
}
// this filter fires just before the nav menu item creation process
add_filter( 'pre_wp_nav_menu', 'wpse309151_remove_title_filter_nav_menu', 10, 2 );

function wpse309151_add_title_filter_non_menu( $items, $args ) {
    // we are done working with menu, so add the title filter back
    add_filter( 'the_title', 'wpse309151_title_update', 10, 2 );
    return $items;
}
// this filter fires after nav menu item creation is done
add_filter( 'wp_nav_menu_items', 'wpse309151_add_title_filter_non_menu', 10, 2 );

add_action( 'woocommerce_review_order_before_submit', 'bbloomer_add_checkout_per_product_terms', 9 );
   
function bbloomer_add_checkout_per_product_terms() {
 
//Single auction condition
$product_id_1 = 942;
$quantity = 0;
foreach(WC()->cart->get_cart() as $key => $val ) {
	$_product = $val['data'];
	if($product_id_1 == $_product->get_id()) {
		$quantity = $val['quantity'];
	}
}
//Monthly Auction Condition
$product_id_2 = 948;
$quantity_2 = 0;
foreach(WC()->cart->get_cart() as $key => $val ) {
	$_product = $val['data'];
	if($product_id_2 == $_product->get_id()) {
		$quantity_2 = $val['quantity'];
	}
}
//Registerration fee condition
$product_id_3 = 1141;
$quantity_3 = 0;
foreach(WC()->cart->get_cart() as $key => $val ) {
	$_product = $val['data'];
	if($product_id_3 == $_product->get_id()) {
		$quantity_3 = $val['quantity'];
	}
}
	
//$product_cart_id_1 = WC()->cart->generate_cart_id( $product_id_1 );
//$in_cart_1 = WC()->cart->find_product_in_cart($product_cart_id_1);
if($quantity > 0){    
?>
    <p class="form-row terms wc-terms-and-conditions">
    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox container_my">
    <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms-new" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms-new'] ) ), true ); ?> id="terms-new"> <span class="checkmark_my"></span><span>I authorize the above amount be charged to the card listed below for payment of the ShopADoc® Auction Participation Fee.</span> <span class="required">*</span>
    </label>
    <input type="hidden" name="terms-new-field" value="true">
    <?php 
		$term_text = 'I authorize the above amount be charged to the card listed below for payment of the ShopADoc® Auction Participation Fee.';
	?>
    </p>
<?php }elseif($quantity_2 > 0){?>
<p class="form-row terms wc-terms-and-conditions">
    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox container_my">
    <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms-new" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms-new'] ) ), true ); ?> id="terms-new"> <span class="checkmark_my"></span><span>I authorize the above amount be charged to the card listed below as a recurring charge every 4 weeks for the payment of the ShopADoc® Auction Participation Fee. I may deactivate this subscription payment method at anytime with 4 weeks notice.</span> <span class="required"> *</span>
    </label>
    <input type="hidden" name="terms-new-field" value="true">
    <?php 
		$term_text = 'I authorize the above amount be charged to the card listed below as a recurring charge every 4 weeks for the payment of the ShopADoc® Auction Participation Fee. I may deactivate this subscription payment method at anytime with 4 weeks notice.';
	?>
    </p>
<?php }elseif($quantity_3 > 0){?>
<p class="form-row terms wc-terms-and-conditions">
    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox container_my">
    <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms-new" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms-new'] ) ), true ); ?> id="terms-new"> <span class="checkmark_my"></span><span>I authorize ShopADoc The Dentist Marketplace Inc. to charge my credit/debit card, listed below, an annual registration fee in the amount of $99.99.<br />
The annual registration fee will be a recurring charge to this credit/debit card on your anniversary date of registration.<br /><br /> Under penalty of law, I certify I hold an active unrestricted license to practice dentistry and I am without pending investigation for disciplinary/ administrative action(s) against me. Should my status change, I agree to notify ShopADoc The Dentist Marketplace Inc. immediately by email to <a href="<?php echo home_url('/contact/'); ?>" title="Contact" target="_blank">ShopADoc1@gmail.com</a> and refrain from further participation on this site until reinstatement by the State Board of Dentistry. I have read and accept the <a href="<?php echo home_url('/user-agreement/'); ?>" title="User Agreement" target="_blank">User Agreement</a>, <a href="<?php echo home_url('/privacy-policy/'); ?>" title="Privacy Policy" target="_blank">Privacy Policy</a>, and <a href="<?php echo home_url('/house-rules/'); ?>" title="House Rules" target="_blank">House Rules</a>.</span> <span class="required">*</span>
    </label>
    <input type="hidden" name="terms-new-field" value="true">
    <?php 
		$term_text = 'I authorize ShopADoc The Dentist Marketplace Inc. to charge my credit/debit card, listed below, an annual registration fee in the amount of $99.99.<br />
The annual registration fee will be a recurring charge to this credit/debit card on your anniversary date of registration.<br /><br /> Under penalty of law, I certify I hold an active unrestricted license to practice dentistry and I am without pending investigation for disciplinary/ administrative action(s) against me. Should my status change, I agree to notify ShopADoc The Dentist Marketplace Inc. immediately by email to ShopADoc1@gmail.com and refrain from further participation on this site until reinstatement by the State Board of Dentistry. I have read and accept the User Agreement, Privacy Policy, and House Rules.';
	?>
    </p>
<?php }else{?>
<p class="form-row terms wc-terms-and-conditions">
    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox container_my">
    <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms-new" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms-new'] ) ), true ); ?> id="terms-new"> <span class="checkmark_my"></span><span>ShopADoc® may contact me as needed. I permit ShopADoc® to provide my contact information to the Dentist following a successful auction pairing for appointment scheduling. I have read and accept the <a href="<?php echo home_url('/user-agreement/'); ?>" title="User Agreement" target="_blank">User Agreement</a>, <a href="<?php echo home_url('/privacy-policy/'); ?>" title="Privacy Policy" target="_blank">Privacy Policy</a>, and <a href="<?php echo home_url('/house-rules/'); ?>" title="House Rules" target="_blank">House Rules</a>. I certify the x-ray(s) I am uploading have been taken within 30 days of the auction. I authorize the above amount be charged to the card listed below for payment of the ShopADoc® auction fee.</span> <span class="required">*</span>
    </label>
    <input type="hidden" name="terms-new-field" value="true">
    <?php 
		$term_text = 'ShopADoc® may contact me as needed. I permit ShopADoc® to provide my contact information to the Dentist following a successful auction pairing for appointment scheduling. I have read and accept the User Agreement, Privacy Policy, and House Rules. I certify the x-ray(s) I am uploading have been taken within 30 days of the auction. I authorize the above amount be charged to the card listed below for payment of the ShopADoc® auction fee.';
	?>
    </p>
<?php }?>
 <input type="hidden" name="_order_terms_text" value="<?php echo $term_text;?>">
<?php
// Show Terms 2
/*
$product_id_2 = 2152;
$product_cart_id_2 = WC()->cart->generate_cart_id( $product_id_2 );
$in_cart_2 = WC()->cart->find_product_in_cart( $product_cart_id_2 ); 
if ( $in_cart_2 ) {		  
	?>
        <p class="form-row terms wc-terms-and-conditions">
        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
        <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms-2" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms-2'] ) ), true ); ?> id="terms-2"> <span>I authorize ShopADoc The Dentist Marketplace charge my credit/debit card listed below<br />
The amount of $12.99 for auction participation. I accept the User Agreement, Privacy Policy, and House Rules.</span> <span class="required">*</span>
        </label>
        <input type="hidden" name="terms-2-field" value="true">
        </p>
	<?php
	}
	*/
}
// Show notice if customer does not tick either terms
add_action( 'woocommerce_checkout_process', 'bbloomer_not_approved_terms_1' );
function bbloomer_not_approved_terms_1() {
	
    if ( $_POST['terms-new-field'] == true ) {
      if ( empty( $_POST['terms-new'] ) ) {
           wc_add_notice( __( 'Please read and accept the terms and conditions to proceed with your order.' ), 'error' );         
      }
   }
}
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta',20,1);
function custom_checkout_field_update_order_meta($order_id){
	if (!empty($_POST['_order_terms_text'])) {
		update_post_meta($order_id, '_order_terms_text',sanitize_text_field($_POST['_order_terms_text']));
	}
}
/*
add_action( 'woocommerce_checkout_process', 'bbloomer_not_approved_terms_2' ); 
function bbloomer_not_approved_terms_2() {
   if ( $_POST['terms-2-field'] == true ) {
      if ( empty( $_POST['terms-2'] ) ) {
         wc_add_notice( __( 'Please agree to terms-2' ), 'error' );         
      }
   }
}
*/
add_action( 'woocommerce_after_single_product_summary', 'custom_single_product_banner', 12 );
function custom_single_product_banner() {
	/*$output = '';
	if(is_user_logged_in()){
		$user = wp_get_current_user();
		$output = '<link rel="stylesheet"  href="'.home_url('/wp-content/themes/dokan-child/slider/css/lightslider.css').'"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="'.home_url('/wp-content/themes/dokan-child/slider/js/lightslider.js').'"></script> 
		<script>
			 $(document).ready(function() {
				
			});
		</script>';
		$output ='';
		if($user->roles[0]=='seller'){
				$output .= the_ad_group(59).the_ad_group(95).the_ad_group(100).the_ad_group(101);
				//$output .= '<div class="ad-zone" id="c57b5c52aff270f681f700f516b58055e"></div><div id="ca513a235a820d3d19f372ac7d04ca19a"></div>';
			}else{
				$output .= the_ad_group(59).the_ad_group(95).the_ad_group(100).the_ad_group(101);
			}
	}
    echo $output;*/
	
}
add_action( 'init', function() {
	add_shortcode( 'ad_section', function( $atts = null, $content = null ) {
		$output = '';
		
		$default_add_sets = array(1 =>array('Red','Blue','Purple','Green'),2 =>array('Blue','Purple','Green','Red'),3 =>array('Purple','Green','Red','Blue'),4 =>array('Green','Red','Blue','Purple'),5 =>array('Red','Purple','Blue','Green'),6 =>array('Blue','Green','Purple','Red'),7 =>array('Purple','Blue','Red','Green'),8 =>array('Green','Purple','Blue','Red'),9 =>array('Red','Purple','Green','Blue'),10 =>array('Blue','Red','Purple','Green'));
		if(is_user_logged_in()){
			$user = wp_get_current_user();
			/*$output = '<link rel="stylesheet"  href="'.home_url('/wp-content/themes/dokan-child/slider/css/lightslider.css').'"/>
							<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
							<script src="'.home_url('/wp-content/themes/dokan-child/slider/js/lightslider.js').'"></script>';
		
			$output ='';
			if($user->roles[0]=='seller'){
				$output .= '<div class="ads_div"><div class="advads-main" id="c2c9ba4709f26cc69919b8fe14985a2d2"></div><div class="advads-main" id="cecf361cf5be9652ac6d85a8b942bb08d"></div><div class="advads-main" id="c2bc5e0ae81103c57086052a71f001371"></div><div class="advads-main" id="caac99a31e29aff4f5d89c961a064e962"></div></div>';
			
			}else{
				$output .= '<div class="ads_div"><div class="advads-main" id="c57b5c52aff270f681f700f516b58055e"></div><div class="advads-main" id="c9eb37b12dbbbc5b3cad9932fa4c3e8b3"></div><div class="advads-main" id="c7a0c95665efa3a5a052e90fe1da5fa16"></div><div class="advads-main" id="cf977d95931f6a6aaa7a23a58ad8ae277"></div></div>';
			}*/
		}
		$current_date_option_label = "_".date('m_y');
		$rotations_client = array();
		for($i=1;$i<= 10;$i++){		
			$rotaion_array = array();
			for($j=1;$j<= 4;$j++){
				$postion = 'position'.$i.'_col'.$j.'_client'.$current_date_option_label;
				$postion_value = get_option($postion);
				if($postion_value){
					$rotaion_array[] = $postion_value;
				}else{
					$rotaion_array[] = "";
				}
			}
			if(!empty($rotaion_array)){
				$rotations_client[] = implode(",",$rotaion_array); 
			}
		}
		
		$rotations_dentist = array();
		for($i=1;$i<= 10;$i++){
			
			$rotaion_array = array();
			for($j=1;$j<= 4;$j++){
				$postion = 'position'.$i.'_col'.$j.'_dentist'.$current_date_option_label;
				$postion_value = get_option($postion);
				if($postion_value){
					$rotaion_array[] = $postion_value;
				}else{
					$rotaion_array[] = "";
				}
			}
			if(!empty($rotaion_array)){
				$rotations_dentist[] = implode(",",$rotaion_array); 
			}
		}
		
		if($user->roles[0]=='seller'){
			$rotations_client = $rotations_client;
		}else{
			if($user->roles[0]=='ad_demo'){
				if(isset($_GET['screen']) && $_GET['screen']=='client'){
					$rotations_client = $rotations_client;
				}else{
					$rotations_client = $rotations_dentist;
				}
			}elseif($user->roles[0]=='advanced_ads_user'){
				if(isset($_GET['screen']) && $_GET['screen']=='client'){
					$rotations_client = $rotations_client;
				}else{
					$rotations_client = $rotations_dentist;
				}
			}else{
				$rotations_client = $rotations_dentist;
			}
		}
		$rotation_output = '<div class="rotation_main">';
		$expired_listing = 'no';
		if(is_product()){
			 global $post,$demo_listing,$today_date_time;
			$_auction_expired_date_time =  get_post_meta($post->ID, '_auction_expired_date_time', true );
			if(strtotime($today_date_time) >  strtotime($_auction_expired_date_time)  && $post->ID != $demo_listing){
				$expired_listing = 'yes';
				$rotation_output .='<div class="rotation_set" id="rotation_set_1" >
											  <div class="rotation_ad" >
												<div class="advads-adaa63980ddea7e6c9e42adcf1a0fe2d" id="advads-adaa63980ddea7e6c9e42adcf1a0fe2d"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											  <div class="rotation_ad" >
												<div class="advads-e29f3dd5c4592b9586597ff8a280d6ac" id="advads-e29f3dd5c4592b9586597ff8a280d6ac"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											  <div class="rotation_ad" >
												<div class="advads-72c08c851c089dd0e3deae69be61e5c2" id="advads-72c08c851c089dd0e3deae69be61e5c2"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											  <div class="rotation_ad">
												<div class="advads-8f6e191e483989ae92a8d006924325b9" id="advads-8f6e191e483989ae92a8d006924325b9"><a class="my-ad" data-bid="1"data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											</div>
											';
					$rotation_output .= '</div>';
					echo $rotation_output;
					
			}
		}
		global $wp;
		$current_url =  home_url($wp->request);
		if(is_404() && strpos($current_url,"/auction-") > 0){
			$expired_listing = 'yes';
			$rotation_output .='<div class="rotation_set" id="rotation_set_1" >
										  <div class="rotation_ad" >
											<div class="advads-adaa63980ddea7e6c9e42adcf1a0fe2d" id="advads-adaa63980ddea7e6c9e42adcf1a0fe2d"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										  <div class="rotation_ad" >
											<div class="advads-e29f3dd5c4592b9586597ff8a280d6ac" id="advads-e29f3dd5c4592b9586597ff8a280d6ac"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										  <div class="rotation_ad" >
											<div class="advads-72c08c851c089dd0e3deae69be61e5c2" id="advads-72c08c851c089dd0e3deae69be61e5c2"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										  <div class="rotation_ad">
											<div class="advads-8f6e191e483989ae92a8d006924325b9" id="advads-8f6e191e483989ae92a8d006924325b9"><a class="my-ad" data-bid="1"data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										</div>
										';
				$rotation_output .= '</div>';
				echo $rotation_output;
		}
		if($expired_listing=='no'){
						$i = 1;
						foreach($rotations_client as $rotation_str){
								if($i == 1 && 1==2){
									$style =' style = "display:block;"'; 
								}else{
									$style =' style = "display:none;"';
								}
								$rotation_output .= '<div class="rotation_set" id="rotation_set_'.$i.'" '.$style.'>';
								$set_ads = array();
								$set_ads_ga = array();
								$ads = explode(",",$rotation_str);
								$j =0;
								foreach($ads as $ad){
									//track_ad_custom($ad,'wp_advads_impressions');
									$rotation_output .= '<div class="rotation_ad" style="position:relative;">';							
									if(get_ad($ad)!=""){
										$advanced_ads_ad_options = maybe_unserialize(get_post_meta($ad,'advanced_ads_ad_options',TRUE));
										//print_r($advanced_ads_ad_options);
										$image_id = $advanced_ads_ad_options['output']['image_id'];
										$url = $advanced_ads_ad_options['url'];
										$width = $advanced_ads_ad_options['width'];
										$height = $advanced_ads_ad_options['height'];
										$image = wp_get_attachment_image_src($image_id, 'full' );
										if ( $image ) {
											list( $image_url, $image_width, $image_height ) = $image;
										}
										$target = '';
										if($advanced_ads_ad_options['tracking']['target']=='new'){
											$target = ' target="_new"';
										}
										//$rotation_output .= '<div class="ad" ><a href="'.home_url('/linkout/'.$ad).'">';
										$role_array = array("C"=>"Client","D"=>"Dentist");
										$temp = explode(" ",htmlspecialchars_decode(get_the_title($ad)));
										$temp2 = explode("&nbsp;",$temp[2]);
										$rotation_output .= "<span style='position: absolute; color: #fff;font-size:15px;font-weight: bold;margin-left: 10px;left:0;'>".$role_array[$temp[0]]." ".$temp2[0]."</span>";
										$rotation_output .= get_ad($ad);
										$ad_ga = '['.$ad.'] '.str_replace("–","-",str_replace("&nbsp;"," ",get_the_title($ad)));
										array_push($set_ads_ga,$ad_ga);
										array_push($set_ads,$ad);
									}else{
										$adSRC = $default_add_sets[$i][$j];
										$rotation_output .='<span style="position: absolute; color: #fff;font-size:15px;font-weight: bold;margin-left: 10px;left:0;">&nbsp;</span>
																	  <div class="advads-82e83803fbd666ef7042b8f56e49dc69" id="advads-82e83803fbd666ef7042b8f56e49dc69"><a data-bid="1" data-href="#" rel="nofollow" target="_blank"><img loading="lazy" src="/wp-content/uploads/2022/03/'.$adSRC.'.gif" alt="" width="300" height="250"></a></div>';
									}
									$rotation_output .= '</div>';
									$j++;
								}
								$rotation_output .='<input type="hidden" id="set_ads_ga_'.$i.'" value="'.implode(',',$set_ads_ga).'" />';
								$rotation_output .='<input type="hidden" id="set_ads_'.$i.'" value="'.implode(',',$set_ads).'" />';
								$rotation_output .= '</div>';
								$i++;
						}
						$rotation_output .='<script type="text/javascript">//startSlide('.get_option("current_rotation").');</script>';
						$rotation_output .='<script type="text/javascript">startSlide(1);</script>';
						$rotation_output .= '</div>';
						echo $rotation_output;
		}
    	//echo $output;
	} );
	
	add_shortcode( 'ad_section_old', function( $atts = null, $content = null ) {
		$output = '';
		if(is_user_logged_in()){
			$user = wp_get_current_user();
			/*$output = '<link rel="stylesheet"  href="'.home_url('/wp-content/themes/dokan-child/slider/css/lightslider.css').'"/>
							<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
							<script src="'.home_url('/wp-content/themes/dokan-child/slider/js/lightslider.js').'"></script>';
		
			$output ='';
			if($user->roles[0]=='seller'){
				$output .= '<div class="ads_div"><div class="advads-main" id="c2c9ba4709f26cc69919b8fe14985a2d2"></div><div class="advads-main" id="cecf361cf5be9652ac6d85a8b942bb08d"></div><div class="advads-main" id="c2bc5e0ae81103c57086052a71f001371"></div><div class="advads-main" id="caac99a31e29aff4f5d89c961a064e962"></div></div>';
			
			}else{
				$output .= '<div class="ads_div"><div class="advads-main" id="c57b5c52aff270f681f700f516b58055e"></div><div class="advads-main" id="c9eb37b12dbbbc5b3cad9932fa4c3e8b3"></div><div class="advads-main" id="c7a0c95665efa3a5a052e90fe1da5fa16"></div><div class="advads-main" id="cf977d95931f6a6aaa7a23a58ad8ae277"></div></div>';
			}*/
		}
		
		if($user->roles[0]=='seller'){
			$rotations_client = maybe_unserialize(get_option('rotations_client'));
		}else{
			if($user->roles[0]=='ad_demo'){
				if(isset($_GET['screen']) && $_GET['screen']=='client'){
					$rotations_client = maybe_unserialize(get_option('rotations_client'));
				}else{
					$rotations_client = maybe_unserialize(get_option('rotations_dentist'));
				}
			}elseif($user->roles[0]=='advanced_ads_user'){
				if(isset($_GET['screen']) && $_GET['screen']=='client'){
					$rotations_client = maybe_unserialize(get_option('rotations_client'));
				}else{
					$rotations_client = maybe_unserialize(get_option('rotations_dentist'));
				}
			}else{
				$rotations_client = maybe_unserialize(get_option('rotations_dentist'));
			}
		}
		$rotation_output = '<div class="rotation_main">';
		$expired_listing = 'no';
		if(is_product()){
			 global $post,$demo_listing,$today_date_time;
			$_auction_expired_date_time =  get_post_meta($post->ID, '_auction_expired_date_time', true );
			if(strtotime($today_date_time) >  strtotime($_auction_expired_date_time)  && $post->ID != $demo_listing){
				$expired_listing = 'yes';
				$rotation_output .='<div class="rotation_set" id="rotation_set_1" >
											  <div class="rotation_ad" >
												<div class="advads-adaa63980ddea7e6c9e42adcf1a0fe2d" id="advads-adaa63980ddea7e6c9e42adcf1a0fe2d"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											  <div class="rotation_ad" >
												<div class="advads-e29f3dd5c4592b9586597ff8a280d6ac" id="advads-e29f3dd5c4592b9586597ff8a280d6ac"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											  <div class="rotation_ad" >
												<div class="advads-72c08c851c089dd0e3deae69be61e5c2" id="advads-72c08c851c089dd0e3deae69be61e5c2"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											  <div class="rotation_ad">
												<div class="advads-8f6e191e483989ae92a8d006924325b9" id="advads-8f6e191e483989ae92a8d006924325b9"><a class="my-ad" data-bid="1"data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
											  </div>
											</div>
											';
					$rotation_output .= '</div>';
					echo $rotation_output;
					
			}
		}
		global $wp;
		$current_url =  home_url($wp->request);
		if(is_404() && strpos($current_url,"/auction-") > 0){
			$expired_listing = 'yes';
			$rotation_output .='<div class="rotation_set" id="rotation_set_1" >
										  <div class="rotation_ad" >
											<div class="advads-adaa63980ddea7e6c9e42adcf1a0fe2d" id="advads-adaa63980ddea7e6c9e42adcf1a0fe2d"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										  <div class="rotation_ad" >
											<div class="advads-e29f3dd5c4592b9586597ff8a280d6ac" id="advads-e29f3dd5c4592b9586597ff8a280d6ac"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										  <div class="rotation_ad" >
											<div class="advads-72c08c851c089dd0e3deae69be61e5c2" id="advads-72c08c851c089dd0e3deae69be61e5c2"><a class="my-ad" data-bid="1" data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										  <div class="rotation_ad">
											<div class="advads-8f6e191e483989ae92a8d006924325b9" id="advads-8f6e191e483989ae92a8d006924325b9"><a class="my-ad" data-bid="1"data-href="#"><img src="/wp-content/uploads/2019/12/Red.gif" alt="" width="300" height="250"></a></div>
										  </div>
										</div>
										';
				$rotation_output .= '</div>';
				echo $rotation_output;
		}
		if($expired_listing=='no'){
						$i = 1;
						foreach($rotations_client as $rotation_str){
								if($i == 1){
									$style =' style = "display:block;"'; 
								}else{
									$style =' style = "display:none;"';
								}
								$rotation_output .= '<div class="rotation_set" id="rotation_set_'.$i.'" '.$style.'>';
								$set_ads = array();
								$ads = explode(",",$rotation_str);
								foreach($ads as $ad){
									//track_ad_custom($ad,'wp_advads_impressions');
									$advanced_ads_ad_options = maybe_unserialize(get_post_meta($ad,'advanced_ads_ad_options',TRUE));
									//print_r($advanced_ads_ad_options);
									$image_id = $advanced_ads_ad_options['output']['image_id'];
									$url = $advanced_ads_ad_options['url'];
									$width = $advanced_ads_ad_options['width'];
									$height = $advanced_ads_ad_options['height'];
									$image = wp_get_attachment_image_src($image_id, 'full' );
									if ( $image ) {
										list( $image_url, $image_width, $image_height ) = $image;
									}
									$target = '';
									if($advanced_ads_ad_options['tracking']['target']=='new'){
										$target = ' target="_new"';
									}
									//$rotation_output .= '<div class="ad" ><a href="'.home_url('/linkout/'.$ad).'">';
									$rotation_output .= '<div class="rotation_ad" >';
									$rotation_output .= get_ad($ad);
										$rotation_output .= '</div>';
									array_push($set_ads,$ad);
								}
								$rotation_output .='<input type="hidden" id="set_ads_'.$i.'" value="'.implode(',',$set_ads).'" />';
								$rotation_output .= '</div>';
								$i++;
						}
						$rotation_output .='<script type="text/javascript">startSlide('.get_option("current_rotation").'); </script>';
						$rotation_output .= '</div>';
						echo $rotation_output;
		}
    	//echo $output;
	} );

} );
function get_timestamp_custom( $timestamp = null, $fixed = false ) {
	    if ( ! isset( $timestamp ) || empty( $timestamp ) ) {
			$timestamp = time();
	    }

	    // -TODO using bitmap would be more efficient for database
	    // .. format using 5 6bit might be most useful for fast operations
	    $ts = gmdate( 'Y-m-d H:i:s', (int) $timestamp ); // time in UTC

        $week = absint( get_date_from_gmt( $ts, 'W' ) );
        $month = absint( get_date_from_gmt( $ts, 'm' ) );

        if ( 52 <= $week && 1 == $month ) {
            /**
             *  Fix for the new year inconsistency
             */
            $ts = get_date_from_gmt( $ts, 'ym01dH' );
        } elseif ( 12 === $month && in_array( $week, array( 1, 53 ) ) ) {
            $ts = get_date_from_gmt( $ts, 'ym52dH' );
        } else {
            $ts = get_date_from_gmt( $ts, 'ymWdH' ); // ensure wp local time
        }

		if ( $fixed ) {
			$ts = substr( $ts, 0, strlen( $ts ) - 2 );
			$ts .= '06';
		}

	    return $ts;
	}
	function track_ad_custom($id, $table) {
	    global $wpdb;
	    $timestamp =get_timestamp_custom( null, true );
		//echo "INSERT INTO $table (`ad_id`, `timestamp`, `count`) VALUES ($id, $timestamp, 1) ON DUPLICATE KEY UPDATE `count` = `count`+ 1";
	    $success = $wpdb->query( "INSERT INTO $table (`ad_id`, `timestamp`, `count`) VALUES ($id, $timestamp, 1) ON DUPLICATE KEY UPDATE `count` = `count`+ 1" );
		//do_action( 'advanced-ads-tracking-after-writing-into-db', $id, $table, $timestamp, $success );
	}
function my_slider_adjustments( $settings ) {
$settings['animation'] = "'fade'"; // change animation to fade
//$settings['speed'] = "'100'";
return $settings;
}
add_filter( 'advanced-ads-slider-settings', 'my_slider_adjustments' );
function my_function( $wrapper_options, $ad ){
    $wrapper_options['class'][] = 'my-ad';
    return $wrapper_options;
}
//add_filter( 'advanced-ads-output-wrapper-options', 'my_function', 10, 2 );
function my_ad_custom( $output, $ad ){
    //$output = str_replace("href","data-href",str_replace("<a",'<a class="my-ad" ',$output));
	 $output = str_replace("href","data-href",$output);
    return $output;
}
add_filter( 'advanced-ads-output-inside-wrapper', 'my_ad_custom', 30, 2 );

add_action( 'init', function() {
	add_shortcode( 'service_list', function( $atts = null, $content = null ) {
		$categories = get_categories( array(
							'taxonomy' => 'product_cat',
							'hide_empty' => false,
							'exclude'          => '15,46,47',
							'orderby' => 'term_id',
							'order'   => 'ASC'
						));
		echo '';
		$categories =  get_categories('parent=0&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_id&order=asc');
		$i = 0;
		list($array1, $array2) = array_chunk($categories, ceil(count($categories) / 2));
		echo '<div class="dokan-form-group dokan-auction-category"><div class="content-half-part half-part-1">';
		foreach($array1 as $cat){
				$categories_level_2 =  get_categories('parent='.$cat->term_id.'&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_order&order=asc');	?>
				<ul><strong><?php echo $cat->name;?></strong>
				<?php foreach($categories_level_2 as $cat_level_2){?>
					<li><?php echo $cat_level_2->name;?></li>
				 <?php }?>
				</ul>
		<?php }
		echo '</div><div class="content-half-part half-part-2">';
		foreach($array2 as $cat){
				$categories_level_2 =  get_categories('parent='.$cat->term_id.'&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_order&order=asc');	?>
				<ul><strong><?php echo $cat->name;?></strong>
				<?php foreach($categories_level_2 as $cat_level_2){?>
					<li><?php echo $cat_level_2->name;?></li>
				 <?php }?>
				</ul>
        <?php }
		echo "<ul class='custom_service'><li>Six Month Smiles®, Invisalign®, & NTI-TSS®  are not registered trademarks of ShopADoc, Inc.</li></ul>";
		echo '</div></div>';
	} );

} );
add_action( 'init', function() {
	add_shortcode( 'confirm_page', function( $atts = null, $content = null ) {
		

		// Load entry details.
		$entry = wpforms()->entry->get( absint( $_GET['entry_id'] ) );

		// Double check that we found a real entry.
		if ( empty( $entry ) ) {
			return;
		}

		// Get form details.
		$form_data = wpforms()->form->get(
			$entry->form_id,
			array(
				'content_only' => true,
			)
		);

		// Double check that we found a valid entry.
		if ( empty( $form_data ) ) {
			return;
		}

		?>
	<!--	<link rel="stylesheet" href="<?php echo WPFORMS_PLUGIN_URL; ?>assets/css/wpforms-preview.css" type="text/css">-->
        <style type="text/css">
			.site{text-align:left;}
			.only_print{
				visibility:hidden;
				display:none;
			}
			@media print {
				body{
					background-repeat:no-repeat !important;
					background-attachment:scroll !important;
				}
					#not_print,#colophon{
					visibility: hidden;
					display:none;
				  }
				  .only_print{
					visibility:visible;
					display:block;
					text-align:center;
					 width:100%;
				  }
				  table.only_print{
					  width:100%;
					  text-align:center;
					  display:block !important;
					  float:left;
				  }
				  /*body * {
					visibility: hidden;
				  }
				  #section-to-print, #print * {
					visibility: visible;
				  }
				  #print{
					position: absolute;
					left: 0;
					top: 0;
				  }*/
				}
		</style>
		<script>
		jQuery(function($){
					$('body').toggleClass('compact');
				// Print page.
				$(document).on('click', '.print', function(e) {
					e.preventDefault();
					window.print();
				});
		});
		</script>
			<div class="wpforms-preview" id="print" style="float:left;width:100%;">
					<div class="buttons" id="not_print">
						<a href="javascript:" class="button button-primary print" style="font-size:18px;"><?php esc_html_e( 'Printable Version', 'wpforms-lite' ); ?></a>
					</div>
                <?php 
					$fields = apply_filters( 'wpforms_entry_single_data', wpforms_decode( $entry->fields ), $entry, $form_data );
					//print_r($fields);
					$email =$fields[3]['value'];
					$dated = date('l, F j, Y',strtotime($fields[33]['value']));
					$name =$fields[1]['value'];
					$street1 =$fields[17]['value'];
					$street2 =$fields[18]['value'];
					$city =$fields[19]['value'];
					$state =$fields[20]['value'];
					$zip =$fields[22]['value'];
					$phone =$fields[15]['value'];
					$card = str_replace("Visa","",str_replace("X","",$fields[30]['value']));
					$order_no = date('Y-md-Hi')."-".str_pad($_GET['entry_id'],4,'0', STR_PAD_LEFT);
				?>
                <style type="text/css">
				.woocommerce .woocommerce-customer-details :last-child, .woocommerce .woocommerce-order-details :last-child, .woocommerce .woocommerce-order-downloads :last-child{
					font-size:16px;
				}
				.woocommerce ul.order_details li{
					font-size:12px;
				}
				.entry-title{
					font-family:'Open Sans', sans-serif;
				}
				.site-footer{position:relative !important;}
				</style>
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">Thank you. Your payment has processed.<br />
                  You now have access to engage weekly auction listings within your service area as they populate.<br />
                  Your registration will auto-renew the last day of your annual cycle.</p>
                <!-- <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
                    <li class="woocommerce-order-overview__order order"> Order number: <strong><?php echo $order_no;?></strong> </li>
                    <li class="woocommerce-order-overview__date date"> Date: <strong><?php echo $dated;?></strong> </li>
                    <li class="woocommerce-order-overview__email email"> Email: <strong><?php echo $email;?></strong> </li>
                    <li class="woocommerce-order-overview__total total"> Total: <strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><?php echo $fields[29]['amount'];?></span></strong> </li>
                    <li class="woocommerce-order-overview__payment-method method"> Payment method: <strong>Card ending in <?php echo $card;?></strong> </li>
                  </ul>-->
                <section class="woocommerce-order-details"> 
                  <!-- <h2 class="woocommerce-order-details__title">Order details</h2>-->
                  <table border="0" width="100%">
                    <tr>
                      <td ><strong>Order #: <?php echo $order_no;?></strong></td>
                    </tr>
                    <tr>
                      <td ><strong><?php echo $dated;?></strong></td>
                    </tr>
                    <tr>
                      <td ><strong>Email: <?php echo $email;?></strong></td>
                    </tr>
                    <tr>
                      <td ><strong>Payment Method: Card ending in <?php echo $card;?></strong></td>
                    </tr>
                  </table>
                  <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">&nbsp;
                  </p>
                  <table class="woocommerce-table woocommerce-table--order-details shop_table order_details" width="100%;">
                    <thead>
                      <tr>
                        <th class="woocommerce-table__product-name product-name">Product</th>
                        <th class="woocommerce-table__product-table product-total">Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="woocommerce-table__line-item order_item">
                        <td class="woocommerce-table__product-name product-name"> Annual Registration <strong class="product-quantity">× 1</strong></td>
                        <td class="woocommerce-table__product-total product-total"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><?php echo $fields[29]['amount'];?></span></td>
                      </tr>
                    </tbody>
                    <!--<tfoot>
                                      <tr>
                                        <th scope="row">Subtotal:</th>
                                        <td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><?php echo $fields[29]['amount'];?></span></td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Payment method:</th>
                                        <td>Card ending in <?php echo $card;?></td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Total:</th>
                                        <td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><?php echo $fields[29]['amount'];?></span></td>
                                      </tr>
                                    </tfoot>-->
                  </table>
                </section>
                <section class="woocommerce-customer-details">
                  <h2 class="woocommerce-column__title">Billing address</h2>
                  <address>
                  <?php echo $name;?><br />
                  <?php echo $street1." ".$street2;?><br />
                  <?php echo $city.", ".$state." ".$zip?><br />
                  <p class="woocommerce-customer-details--phone"><?php echo $phone;?></p>
                  </address>
                </section>
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received only_print" id="only_print">&nbsp;</p>
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received only_print" id="only_print">&nbsp;</p>
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received only_print" id="only_print" style="float:left;width:100%;text-align:center;">ShopADoc® The Dentist Marketplace</p>
				<?php
				/*

				if ( empty( $fields ) ) {

					// Whoops, no fields! This shouldn't happen under normal use cases.
					echo '<p class="no-fields">' . esc_html__( 'This entry does not have any fields', 'wpforms-lite' ) . '</p>';

				} else {

					echo '<div class="fields">';
					unset($fields[4]);
					unset($fields[31]);
					// Display the fields and their values.
					foreach ( $fields as $key => $field ) {

						$field_value  = apply_filters( 'wpforms_html_field_value', wp_strip_all_tags( $field['value'] ), $field, $form_data, 'entry-single' );
						$field_class  = sanitize_html_class( 'wpforms-field-' . $field['type'] );
						$field_class .= empty( $field_value ) ? ' empty' : '';

						echo '<div class="field ' . $field_class . '">';

							echo '<p class="field-name">';
								echo ! empty( $field['name'] ) ? wp_strip_all_tags( $field['name'] ) : sprintf( esc_html__( 'Field ID #%d', 'wpforms-lite' ), absint( $field['id'] ) );
							echo '</p>';

							echo '<p class="field-value">';
								echo ! empty( $field_value ) ? nl2br( make_clickable( $field_value ) ) : esc_html__( 'Empty', 'wpforms-lite' );
							echo '</p>';

						echo '</div>';
					}

					echo '</div>';
				}
				*/
				?>
			</div>
            <span id="not_print">
            <h1>You now have access to view and bid on all auctions within your service area.</h1>
            <h1>Best of Luck!</h1>
            <div class="wpforms-confirmation-container-full wpforms-confirmation-scroll" id="wpforms-confirmation-154">
<div class="buttons" id="not_print"><a href="<?php echo site_url(); ?>/shopadoc-auction-activity/ " title="View Auctions" class="button button-primary proceed_to_auction"><?php esc_html_e( 'Proceed to Auction', 'wpforms-lite' ); ?></a><span id="checkout_tooltip" class="not_print"><span class="tooltip_New checkout"><span class="tooltips custom_m_bubble"  style="float:left !important;">&nbsp;</span><span class="tooltip_text">Please check your Inbox, Spam, Junk, & Promotions tab for receipts & correspondence from ShopADoc.</span></span></span></div>
<?php
		//print_r($_SERVER);
		
		$user = get_user_by( 'email',$email);
		$_email_send = get_user_meta($user->ID, '_email_send',true); 
		//if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'new-dentist') > 0 || 1==1){
		if($_email_send !="Yes"){
			$price = get_post_meta(1141,"_regular_price",true);
			$price =number_format($price, 2, '.', '');
			$email_content = array();
			/*
			$email_content['subject']  = 'Order Confirmation';
			$email_content['message'] = "Greetings! Your payment has processed.<br />";
			$email_content['message'] .= "You now have access to engage weekly auction listings within your service area as they populate.<br />Your registration will auto-renew the last day of your annual cycle.<br />";
			$email_content['message'] .= '
			<table border="0" style="color:#000000;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Order #: '.$order_no.'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>'.$dated.'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Email: <a style="color:#000000 !important;">'.$email.'</a></strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Payment Method: Card ending in '.$card.'</strong></td> </tr></table>
			<table cellspacing="0" cellpadding="6" border="1" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <thead><tr> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Product</th> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Total</th></tr> </thead> <tbody><tr> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"> Annual Registration </td> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"><span><span>$</span><span>'.$price.'</span></span></td></tr> </tbody></table>
			';
			*/
			$password = get_user_meta($user->ID, '_plain_pass',true); 
			$email_content['subject']  = 'Annual Registration Confirmation';
			$email_content['message'] = "<p style='color:#000 !important;'>Welcome to ShopADoc®</p>";
			$email_content['message'] .= "<p style='color:#000 !important;'>You’ve successfully completed the registration process.<br /></p>";
			$email_content['message'] .= '<p style="color:#000 !important;">User name: <a style="color:#000 !important;">'.$email  .'</a></p>';
			$email_content['message'] .= '<p style="color:#000 !important;">Password: '.base64_decode($password) . '<br /></p>';
			$email_content['message'] .= '
			<table border="0" style="color:#000000;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Order #: '.$order_no.'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>'.$dated.'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Email: <a style="color:#000 !important;font-weight:bold;">'.$email.'</a></strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Payment Method: Card ending in '.$card.'</strong></td> </tr></table>
			<table cellspacing="0" cellpadding="6" border="1" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <thead><tr> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Product</th> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Total</th></tr> </thead> <tbody><tr> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"> Annual Registration </td> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"><span><span>$</span><span>'.$price.'</span></span></td></tr> </tbody></table>
			';
			$email_content['message'] .= "<p style='color:#000 !important;'>We invite you to <a href='".wp_login_url()."' title='Login' style='color:blue !important;'><strong>log in</strong></a> to ShopADoc® and follow all auctions within your service area.<br /></p>";
			$start = date("F j, Y");
			$end = date("F j, Y", strtotime('+1 years'));
			$end = date("F j, Y", strtotime('+1 years'));
			$end = date("F j, Y", strtotime('-1 days', strtotime($end)));
			$email_content['message'] .='<p style="color:#000 !important;">Your registration is effective '.$start.' - '.$end.'.</p>';
			$email_content['message'] .='<p style="color:#000 !important;">Renewal will process via auto-pay on your anniversary date.</p>';
			
			$emails = new WPForms_WP_Emails;
			$emails->send($email, $email_content['subject'], $email_content['message'] );
			update_user_meta($user->ID, '_email_send','Yes');
			//Insert Order Record for Stats Table
			global $wpdb;
			$table = 'wp_order_stats';
			$Auction_id_store = 0;
			$service = '';
			$type = 'dentist';
			$data = array('user_id' =>get_current_user_id(), 'order_id' =>$_GET['entry_id'], 'product_id' =>1141, 'cost' =>$price, 'auction_id' =>$Auction_id_store, 'service' =>$service, 'city' => $city, 'state' => $state, 'zip' =>$zip, 'type' => $type, 'date' =>date('Y-m-d'), 'source' =>'wpform');
			$format = array('%d','%d','%d','%f','%d','%s','%s','%s','%s','%s','%s','%s');
			$wpdb->insert($table,$data,$format);
			//$my_id = $wpdb->insert_id;
			/*****************************************/
		}
?>
<?php /*?>
<p><h3>Upcoming Auctions</h3>
<?php echo do_shortcode('[future_auctions per_page="4" columns="4" orderby="date" order="desc"]'); ?>

<p><h3>Active Auctions</h3>
<?php echo do_shortcode('[ending_soon_auctions per_page="12" columns="4" order="asc"]'); ?>
</p>
</p>
<?php */?>
</div>
</span>
<?php //echo do_shortcode('[ad_section]');?>
		<?php
		//exit();
	} );

} );
add_filter('wpforms_field_properties_text', 'wpforms_field_properties_text_custom', 10, 3 );
function wpforms_field_properties_text_custom($properties, $field, $form_data){
	$$properties_new = array();
	$properties_new['container'] = $properties['container'];
	$properties_new['label'] = $properties['label'];
	$properties_new['description'] = $properties['description'];
	$properties_new['inputs'] = $properties['inputs'];
	$properties_new['error'] = $properties['error'];
	$properties = array();
	$properties = $properties_new;
	return $properties_new;
}
add_action( 'wpforms_display_field_before','field_label_custom', 16, 2 );
function field_label_custom( $field, $form_data ) {

	$label = $field['properties']['label'];
	if(strpos($field['css'],"mytooltip") > 0){
		// If the label is empty or disabled don't proceed.
		if ( empty( $label['value'] ) || $label['disabled'] ) {
			return;
		}
	
		$required = $label['required'] ? wpforms_get_field_required_label() : '';
	
		printf( '<label %s>%s%s</label>',
			wpforms_html_attributes( $label['id'], $label['class']." customTooltip", $label['data'], $label['attr'] ),
			$label['value'],
			$required
		);
	}
}
function wc_get_gallery_image_html_override( $attachment_id, $main_image = false ) {
	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	$alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
	$image             = wp_get_attachment_image(
		$attachment_id,
		$image_size,
		false,
		apply_filters(
			'woocommerce_gallery_image_html_attachment_image_params',
			array(
				'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-src'                => esc_url( $full_src[0] ),
				'data-large_image'        => esc_url( $full_src[0] ),
				'data-large_image_width'  => esc_attr( $full_src[1] ),
				'data-large_image_height' => esc_attr( $full_src[2] ),
				'class'                   => 'wp-post-image',
			),
			$attachment_id,
			$image_size,
			$main_image
		)
	);

	return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="woocommerce-product-gallery__image">' . $image . '</div>';
}
//add_action('woocommerce_product_thumbnails','report_abuse_link',11);
function report_abuse_link(){
	echo '<div class="abuse"><a href="'.home_url('/report-abuse/').'" title="Report Abuse">Report Abuse</a></div>';
}
function isa_order_received_text( $text, $order ) {
    $new = $text /*. '<span class="checkout_thank">ShopADoc ®</span>'*/;
    return $new;
}
add_filter('woocommerce_thankyou_order_received_text', 'isa_order_received_text', 10, 2 );
/*add_filter( 'woocommerce_get_price_html', 'wpa83367_price_html', 100, 2 );
function wpa83367_price_html( $price, $product ){
	$html = '<form action="'.get_permalink().'" method="post">
    	<table width="100%" border="0" >
        	<tr><td><i class="fa fa-arrow-up" onclick="updatePrice();">&nbsp;</i></td><td><input name="update_distance" type="submit" value="Update" /></td>/tr>
        </table>
        </form>';
   return $price.$html;
}*/
add_action('profile_update', 'custom_profile_redirect', 12 );
function custom_profile_redirect() {
	if (defined('DOING_AJAX') && DOING_AJAX) {
	}else{
		if (!is_admin() && strpos($_SERVER['HTTP_REFERER'],'/checkout') === false && strpos($_SERVER['HTTP_REFERER'],'/lost-password') === false) {
			$user = wp_get_current_user();
			my_woocommerce_save_account_details($user->ID);
			wp_redirect(home_url('/my-account/edit-account/').'?mode=update');
			exit;
		}
	}
}
function op_bypass_add_to_cart_sold_individually_found_in_cart( $found_in_cart, $product_id ) {
    if ( $found_in_cart ) {
			$product = wc_get_product( $product_id );
			/* translators: %s: product name */
			throw new Exception( );
	}
    return $found_in_cart;
}
add_filter( 'woocommerce_add_to_cart_sold_individually_found_in_cart', 'op_bypass_add_to_cart_sold_individually_found_in_cart', 10, 2 );
add_action("template_redirect", 'redirection_cart_function');
function redirection_cart_function(){
    global $woocommerce;
    if( is_cart() && WC()->cart->cart_contents_count == 0){
        wp_safe_redirect(home_url());
    }
	if( is_cart() && WC()->cart->cart_contents_count > 0){
		 //wp_safe_redirect(home_url('/checkout/'));
	}
}
function custom_shop_page_redirect() {
	global $wp;
    if( is_shop() ){
		if (is_user_logged_in()){
			$user = wp_get_current_user();
			if($user->roles[0]=='seller'){
				wp_redirect( home_url('/auction-activity/auction/') );
			}elseif($user->roles[0]=='advanced_ads_user'){
				$client_ad = getActiveAD_id("C ",get_current_user_id());
				$dentist_ad = getActiveAD_id("D ",get_current_user_id());
				if($client_ad==0 && $dentist_ad > 0){
					wp_redirect(home_url('/auction-3977/demo-auction/'));
				}elseif($dentist_ad==0 && $client_ad > 0){
					wp_redirect(home_url('/auction-3977/demo-auction/?screen=client'));
				}elseif($dentist_ad==0 && $client_ad == 0){
					wp_redirect(home_url('/'));
				}else{
					wp_redirect(home_url('/auction-3977/demo-auction/'));
				}
			}elseif($user->roles[0]=='ad_demo'){
				$dentist_ad = getActiveAds('D');
				$client_ad =  getActiveAds('C');
				if($client_ad==0){
					wp_redirect(home_url('/auction-3977/demo-auction/'));
				}elseif($dentist_ad==0){
					wp_redirect(home_url('/auction-3977/demo-auction/?screen=client'));
				}else{
					wp_redirect(home_url('/auction-3977/demo-auction/'));
				}
			}else{
				wp_redirect( home_url( '/shopadoc-auction-activity/ ' ) );
			}
		}else{
			wp_redirect( home_url( '/shopadoc-auction-activity/ ' ) );
		}
        exit();
    }
	$current_url =  home_url( $wp->request );
	if(strpos($current_url,"settings") > 0 || strpos($current_url,"settings/payment") > 0 || strpos($current_url,"withdraw") > 0 || strpos($current_url,"reports") > 0 || strpos($current_url,"orders") > 0 || strpos($current_url,"products") > 0 || strpos($current_url,"new-product") > 0 || strpos($current_url,"coupons") > 0 || strpos($current_url,"reviews") > 0 || strpos($current_url,"store") > 0){
		wp_redirect( home_url('/auction-activity/auction/') );
		exit();
	}
}
add_action( 'template_redirect', 'custom_shop_page_redirect' );
function year_shortcode(){
	$year = date('Y');
	return $year;
}
add_shortcode('year', 'year_shortcode');
function bid_section_template_func(){
	wc_get_template( 'single-product/bid.php' );
}
add_shortcode('bid_section_template', 'bid_section_template_func');
add_action( 'init', function() {
	add_shortcode( 'list_ads_stats','list_ads_stats_func');
} );
function list_ads_stats_func(){
	if (!is_user_logged_in()){
		wp_redirect( home_url( '/my-account/' ) );
		exit();
	}
	global $wpdb;
	$args = array(
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'orderby'             => 'post_date',
					'post_type' =>'advanced_ads',
					'order'               => 'DESC',
					'posts_per_page'      => -1,
					'meta_query' => array(array('key' => 'ad_user',get_current_user_id(),'compare' => 'EQUAL')),
				);
				$query = new WP_Query($args );
				//echo $wpdb->last_query;
				$ads = $wpdb->get_results( "SELECT post_id as ID FROM {$wpdb->prefix}postmeta WHERE meta_key = 'ad_user' and meta_value=".get_current_user_id(), OBJECT );
				//$ads = $query->posts;?>
                <style type="text/css">
				.my-listing-custom{
					float: left;
					width: 100%;
				}
				.table > thead > tr > th {
					font-size:17px;
				}
				.table > tbody > tr > td {
					font-size:14px;
				}
				tbody.scroll {
					display:block;
					height:167px;
					overflow:auto;
				}
				thead, tbody tr,tfoot {
					display:table;
					width:100%;
					table-layout:fixed;
				}
				thead,tfoot {
					width: calc( 100% - 1em )
				}
				table {
					width:100%;
				}
				@media (max-width: 448px) {
					.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td{
						padding:8px 2px !important;
					}
				}
			</style>
<?php
        	 $periods = array(
				'today' => __('today', 'advanced-ads-tracking'),
				'yesterday' => __('yesterday', 'advanced-ads-tracking'),
				'last7days' => __('last 7 days', 'advanced-ads-tracking'),
				'thismonth' => __('this month', 'advanced-ads-tracking'),
				'lastmonth' => __('last month', 'advanced-ads-tracking'),
				'thisyear' => __('this year', 'advanced-ads-tracking'),
				'lastyear' => __('last year', 'advanced-ads-tracking'),
				// -TODO this is not fully supported for ranges of more than ~200 points; should be reviewed before 2015-09-01
				'custom' => __('custom', 'advanced-ads-tracking'),
			);
			
		?>
        <script src='/wp-includes/js/jquery/ui/datepicker.min.js?ver=1.11.4'></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<table style="width: 100%;">
      	<tbody>
        <tr>
          <td align="right"><form method="get" id="period-form">
              <label>
                <?php _e( 'Period', 'advanced-ads-tracking' ); ?>
                :&nbsp;</label>
              <select id="period" name="period" class="advads-stats-period" onchange="selectDateField();">
              	<option value="">- Please Select -</option>
				<?php foreach($periods as $_period_key => $_period) : ?>
                <option value="<?php echo $_period_key; ?>" <?php if(isset($_GET['period']) && $_GET['period']==$_period_key){ echo 'selected="selected"';}else{}?> ><?php echo $_period; ?></option>
                <?php endforeach; ?>
            </select>
            <?php 	$period = $_GET['period'];
					$from='';
					$to = '';
					if($period=='custom'){
						if(isset($_GET['from'])&& $_GET['from'] !=""){
							$from = $_GET['from'];
						}
						if(isset($_GET['from'])&& $_GET['from'] !=""){
							$to = $_GET['to'];
						}
					}
			?>
             <input type="text" id="from" name="from" class="advads-stats-from advads-datepicker <?php if($period !== 'custom') echo ' hidden'; ?>" value="<?php echo $from; ?>" size="10" maxlength="10" placeholder="<?php _e( 'from', 'advanced-ads-tracking' ); ?>"/>
                        <input type="text" id="to" name="to" class="advads-stats-to<?php
                            if($period !== 'custom') echo ' hidden'; ?>" value="<?php
                            echo $to; ?>" size="10" maxlength="10" placeholder="<?php _e( 'to', 'advanced-ads-tracking' ); ?>"/>
              <input type="submit" class="button button-primary" value="<?php echo esc_attr( __( 'Load', 'advanced-ads-tracking' ) ); ?>" />
            </form></td>
        </tr>
      </tbody>
    </table>
    <script>
	 jQuery( function() {
		jQuery( "#from,#to" ).datepicker({format:"mm/dd/yyyy"});
	  } );
	  function selectDateField(){
		  	var val = jQuery('#period').val();
		  	jQuery('#from,#to').removeClass('hidden');
		  	if(val=='custom'){
				jQuery('#from,#to').removeClass('hidden');
			}else{
				jQuery('#from,#to').addClass('hidden');
				jQuery('#from,#to').val('');
			}
	  }
	</script>
<div class="dokan-dashboard-wrap nano">
  <div class="dokan-dashboard-content dokan-product-listing my-listing-custom">
    <article class="dokan-product-listing-area">
      <div class="table-wrap"> 
        <!--<table class="table table-striped product-listing-table">-->
        
        <table class="table product-listing-table">
          <thead>
            <tr>
              <th class="image_col">Creative</th>
              <th>Title</th>
              <th>Impressions</th>
              <th>Clicks</th>
              <th>CTR</th>
               <th>Action</th>
            </tr>
          </thead>
          <tbody class="scroll">
            <?php
			
			$util = Advanced_Ads_Tracking_Util::get_instance();
			if (isset($_GET['period'])) {
				// time handling; blog time offset in seconds
				$gmt_offset = 3600 * floatval( get_option( 'gmt_offset', 0 ) );
				// day start in seconds
				$now = $util->get_timestamp();
				$today_start = $now - $now % Advanced_Ads_Tracking_Util::MOD_HOUR;
				$start = null;
				$end = null;
				switch ($_GET['period']) {
					case 'today' :
						$start = $today_start;
						break;
					case 'yesterday' :
						$start = $util->get_timestamp( time() - DAY_IN_SECONDS );
						$start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
						$end = $today_start;
						break;
					case 'last7days' :
						// last seven full days // -TODO might do last or current week as well
						$start = $util->get_timestamp( time() - WEEK_IN_SECONDS );
						$start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
						break;
					case 'thismonth' :
						// timestamp from first day of the current month
						$start = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
						break;
					case 'lastmonth' :
						// timestamp from first day of the last month
						$start = $util->get_timestamp( mktime(0, 0, 0, date("m") - 1, 1, date("Y")) );
						$end = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
						break;
					case 'thisyear' :
						// timestamp from first day of the current year
						$start = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
						break;
					case 'lastyear' :
						// timestamp from first day of previous year
						$start = $util->get_timestamp( mktime(0, 0, 0, 1, 1, date('Y') - 1) );
						$end = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
						break;
					case 'custom' :
						$start  = $util->get_timestamp( strtotime( $_GET['from'] ) - $gmt_offset  );
						$end    = $util->get_timestamp( strtotime( $_GET['to'] ) - $gmt_offset + ( 24 * 3600 ) );
						break;
				}
			}
			// TODO limit range (mind groupIncrement/ granularity)
			// values might be null (not set) or false (error in input)
	
			$where = '';
			if (isset($start) && $start) {
				$where .= " AND `timestamp` >= $start";
			}
			if (isset($end) && $end) {
				if ( $where ) {
					$where .= " AND `timestamp` < $end";
				} else {
					$where .= " AND `timestamp` < $end";
				}
			}
			if ( count($ads) > 0) {
				foreach($ads as $ad) {
					$post_title = $wpdb->get_var("SELECT post_title FROM {$wpdb->prefix}posts where ID = '".$ad->ID."'");
					$impression_count = $wpdb->get_var("SELECT SUM(count) as total FROM `wp_advads_impressions` where ad_id='".$ad->ID."'".$where);
					$click_count = $wpdb->get_var("SELECT SUM(count) as total FROM `wp_advads_clicks` where ad_id='".$ad->ID."'".$where);
					$ctr  = ($click_count / $impression_count) * 100;
					$advanced_ads_ad_options = maybe_unserialize(get_post_meta($ad->ID,'advanced_ads_ad_options',TRUE));
					$image_id = $advanced_ads_ad_options['output']['image_id'];
					$image = wp_get_attachment_image_src($image_id, 'thumb' );
					if ( $image ) {
						list( $image_url, $image_width, $image_height ) = $image;
					}
					if($ctr < 1){
						$ctr  =  number_format(($click_count / $impression_count) * 100,7);
					}else{
						$ctr  = number_format(($click_count / $impression_count) * 100,2);
					}
			?>
                    <tr id="row_<?php echo $ad->ID;?>">
                      <td class="post-status"><img src="<?php echo $image_url;?>" alt=""  width="" height="" /></td>
                      <td class="post-status"><?php echo $post_title;?></td>
                      <td class="post-status "><?php echo ($impression_count==0)? '0':$impression_count;?></td>
                      <td class="post-status "><?php echo ($click_count==0)? '0' : $click_count;?></td>
                      <td class="post-status "><?php echo ( 0 == $click_count )? '0.00 %' : $ctr . ' %';?></td>
                      <td class="post-status "><a href="<?php echo getAdLinkNew($ad->ID);?>" title="View State" >View Stats</a></td>
                    </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td colspan="7"><?php _e( 'No Ad found', 'dokan-auction' ); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </article>
  </div>
</div>
<?php //echo do_shortcode("[list_adStats]");?>
<?php
}
add_action( 'init', function() {
	add_shortcode( 'list_ads_stats_demo','list_ads_stats_demo_func');
} );
function list_ads_stats_demo_func(){
	if (!is_user_logged_in()){
		wp_redirect( home_url( '/my-account/' ) );
		exit();
	}
	global $wpdb;
	$args = array(
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'orderby'             => 'post_date',
					'post_type' =>'advanced_ads',
					'order'               => 'DESC',
					'posts_per_page'      => -1,
					'meta_query' => array(array('key' => 'ad_user',get_current_user_id(),'compare' => 'EQUAL')),
				);
				$query = new WP_Query($args );
				//echo $wpdb->last_query;
				$ads = $wpdb->get_results( "SELECT post_id as ID FROM {$wpdb->prefix}postmeta WHERE meta_key = 'ad_user' and meta_value=".get_current_user_id(), OBJECT );
				//$ads = $query->posts;?>
                <style type="text/css">
				.my-listing-custom{
					float: left;
					width: 100%;
				}
				.table > thead > tr > th {
					font-size:17px;
				}
				
				
				tbody.scroll {
					display:block;
					height:167px;
					overflow:auto;
				}
				thead, tbody tr,tfoot {
					display:table;
					width:100%;
					table-layout:fixed;
				}
				thead,tfoot {
					width: 100%;
				}
				table {
					width:100%;
				}
				@media (max-width: 448px) {
					h1.entry-title{font-size:26px !important;}
					#primary{padding-left:5px;padding-right:5px;}
					.my-listing-custom{
						padding-right:0px;
					}
					.table > tbody > tr > td {
						font-size:14px;
					}
					.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td{
						padding:8px 2px !important;
					}
				}
			</style>
<?php
        	 $periods = array(
				'today' => __('today', 'advanced-ads-tracking'),
				'yesterday' => __('yesterday', 'advanced-ads-tracking'),
				'last7days' => __('last 7 days', 'advanced-ads-tracking'),
				'thismonth' => __('this month', 'advanced-ads-tracking'),
				'lastmonth' => __('last month', 'advanced-ads-tracking'),
				'thisyear' => __('this year', 'advanced-ads-tracking'),
				'lastyear' => __('last year', 'advanced-ads-tracking'),
				// -TODO this is not fully supported for ranges of more than ~200 points; should be reviewed before 2015-09-01
				'custom' => __('custom', 'advanced-ads-tracking'),
			);
			
		?>
        <script src='/wp-includes/js/jquery/ui/datepicker.min.js?ver=1.11.4'></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<table style="width: 100%;">
      	<tbody>
        <tr>
          <td align="right"><form method="get" id="period-form">
              <label>
                <?php _e( 'Period', 'advanced-ads-tracking' ); ?>
                :&nbsp;</label>
                <?php if(isset($_GET['mode'])&& $_GET['mode']=='popup'){?>
                	<input type="text"  id="period" name="period" class="advads-stats-period" value="LAST 7 DAYS" readonly="readonly" style="width: 145px;padding: 0px 10px; border: solid 2px #000; border-radius: 3px;"/>
                <?php }else{?>
              <select id="period" name="period" class="advads-stats-period" onchange="selectDateField();">
              	<!--<option value="last7days">- Please Select -</option>-->
				<?php foreach($periods as $_period_key => $_period) : ?>
                <option value="<?php echo $_period_key; ?>" <?php if((isset($_GET['period']) && $_GET['period']==$_period_key)){ echo 'selected="selected"';}else{}?> ><?php echo $_period; ?></option>
                <?php endforeach; ?>
            </select>
            <?php }?>
            <?php 	$period = $_GET['period'];
					$from='';
					$to = '';
					if($period=='custom'){
						if(isset($_GET['from'])&& $_GET['from'] !=""){
							$from = $_GET['from'];
						}
						if(isset($_GET['from'])&& $_GET['from'] !=""){
							$to = $_GET['to'];
						}
					}
			?>
             <input type="text" id="from" name="from" class="advads-stats-from advads-datepicker <?php if($period !== 'custom') echo ' hidden'; ?>" value="<?php echo $from; ?>" size="10" maxlength="10" placeholder="<?php _e( 'from', 'advanced-ads-tracking' ); ?>"/>
                        <input type="text" id="to" name="to" class="advads-stats-to<?php
                            if($period !== 'custom') echo ' hidden'; ?>" value="<?php
                            echo $to; ?>" size="10" maxlength="10" placeholder="<?php _e( 'to', 'advanced-ads-tracking' ); ?>"/>
                            <?php if(isset($_GET['mode'])&& $_GET['mode']=='popup'){?>
									
              					<input type="button" class="button button-primary" <?php echo $disable?> value="<?php echo esc_attr( __( 'Load', 'advanced-ads-tracking' ) ); ?>" />
							<?php }else{?>
								
              					<input type="submit" class="button button-primary" <?php echo $disable?> value="<?php echo esc_attr( __( 'Load', 'advanced-ads-tracking' ) ); ?>" />
							<?php }?>
            </form></td>
        </tr>
      </tbody>
    </table>
    <script>
	 jQuery( function() {
		jQuery( "#from,#to" ).datepicker({format:"mm/dd/yyyy"});
	  } );
	  function selectDateField(){
		  	var val = jQuery('#period').val();
		  	jQuery('#from,#to').removeClass('hidden');
		  	if(val=='custom'){
				jQuery('#from,#to').removeClass('hidden');
			}else{
				jQuery('#from,#to').addClass('hidden');
				jQuery('#from,#to').val('');
			}
	  }
	</script>
<div class="dokan-dashboard-wrap nano">
  <div class="dokan-dashboard-content dokan-product-listing my-listing-custom">
    <article class="dokan-product-listing-area">
      <div class="table-wrap"> 
        <!--<table class="table table-striped product-listing-table">-->
        
        <table class="table product-listing-table" width="100%">
          <thead>
            <tr>
              <th width="30%" align="left">CREATIVE</th>
              <!--<th width="10%" align="left">TITLE</th>-->
              <th width="35%" align="left">IMPRESSIONS</th>
              <th width="20%" align="left">CLICKS</th>
              <th width="15%" align="left">CTR</th>
              <!-- <th width="15%" class="mobile_hide">Action</th>-->
            </tr>
          </thead>
          <tbody >
            <?php
			
			$util = Advanced_Ads_Tracking_Util::get_instance();
			if (isset($_GET['period'])) {
				// time handling; blog time offset in seconds
				$gmt_offset = 3600 * floatval( get_option( 'gmt_offset', 0 ) );
				// day start in seconds
				$now = $util->get_timestamp();
				$today_start = $now - $now % Advanced_Ads_Tracking_Util::MOD_HOUR;
				$start = null;
				$end = null;
				switch ($_GET['period']) {
					case 'today' :
						$start = $today_start;
						break;
					case 'yesterday' :
						$start = $util->get_timestamp( time() - DAY_IN_SECONDS );
						$start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
						$end = $today_start;
						break;
					case 'last7days' :
						// last seven full days // -TODO might do last or current week as well
						$start = $util->get_timestamp( time() - WEEK_IN_SECONDS );
						$start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
						break;
					case 'thismonth' :
						// timestamp from first day of the current month
						$start = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
						break;
					case 'lastmonth' :
						// timestamp from first day of the last month
						$start = $util->get_timestamp( mktime(0, 0, 0, date("m") - 1, 1, date("Y")) );
						$end = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
						break;
					case 'thisyear' :
						// timestamp from first day of the current year
						$start = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
						break;
					case 'lastyear' :
						// timestamp from first day of previous year
						$start = $util->get_timestamp( mktime(0, 0, 0, 1, 1, date('Y') - 1) );
						$end = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
						break;
					case 'custom' :
						$start  = $util->get_timestamp( strtotime( $_GET['from'] ) - $gmt_offset  );
						$end    = $util->get_timestamp( strtotime( $_GET['to'] ) - $gmt_offset + ( 24 * 3600 ) );
						break;
				 	default:
						// last seven full days // -TODO might do last or current week as well
						$start = $util->get_timestamp( time() - WEEK_IN_SECONDS );
						$start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
						break;
				}
			}
			// TODO limit range (mind groupIncrement/ granularity)
			// values might be null (not set) or false (error in input)
	
			$where = '';
			if (isset($start) && $start) {
				$where .= " AND `timestamp` >= $start";
			}
			if (isset($end) && $end) {
				if ( $where ) {
					$where .= " AND `timestamp` < $end";
				} else {
					$where .= " AND `timestamp` < $end";
				}
			}
			if ( count($ads) > 0) {
				$i = 0;
				foreach($ads as $ad) {
					$post_status = $wpdb->get_var("SELECT post_status FROM {$wpdb->prefix}posts where ID = '".$ad->ID."'");
					if($post_status=='publish'){
					$post_title = $wpdb->get_var("SELECT post_title FROM {$wpdb->prefix}posts where ID = '".$ad->ID."'");
					$post_date_gmt = $wpdb->get_var("SELECT post_date_gmt FROM {$wpdb->prefix}posts where ID = '".$ad->ID."'");
					$post_author = $wpdb->get_var("SELECT post_author FROM {$wpdb->prefix}posts where ID = '".$ad->ID."'");
					$impression_count = $wpdb->get_var("SELECT SUM(count) as total FROM `wp_advads_impressions` where ad_id='".$ad->ID."'".$where);
					$click_count = $wpdb->get_var("SELECT SUM(count) as total FROM `wp_advads_clicks` where ad_id='".$ad->ID."'".$where);
					$ctr  = ($click_count / $impression_count) * 100;
					$advanced_ads_ad_options = maybe_unserialize(get_post_meta($ad->ID,'advanced_ads_ad_options',TRUE));
					$image_id = $advanced_ads_ad_options['output']['image_id'];
					$image = wp_get_attachment_image_src($image_id, 'thumb' );
					if ( $image ) {
						list( $image_url, $image_width, $image_height ) = $image;
					}
					if($ctr < 1){
						$ctr  =  number_format(($click_count / $impression_count) * 100,4);
					}else{
						$ctr  = number_format(($click_count / $impression_count) * 100,2);
					}
					$advanced_ads_ad_options = maybe_unserialize(get_post_meta($ad->ID, 'advanced_ads_ad_options', true ));
					$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
					$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
					$src = $img_atts[0];
					$ad_title = "<img src='".$src."' style='height:26px;'><!--<br /><br /><span style='position:absolute;width:100%;margin-top:-5px;'>".$post_title."</span>-->";
					
					$ad_demo_company_name = get_user_meta($post_author, 'ad_demo_company_name',true);
					$post_date_gmt = date("M j, Y",strtotime($post_date_gmt));
					if($advanced_ads_ad_options['expiry_date']){
						$expiry_date = date("M j, Y",$advanced_ads_ad_options['expiry_date']);
					}
					$sub_title = $ad_demo_company_name." ".$post_date_gmt.' - '.$expiry_date;
			?>
                    <tr id="row_<?php echo $ad->ID;?>">
                      <td class="post-status" width="30%" align="left"><?php echo $ad_title;?></td>
                      <td class="post-status " width="35%" align="left"><?php echo ($impression_count==0)? '0':number_format($impression_count);?></td>
                      <td class="post-status " width="20%" align="left"><?php echo ($click_count==0)? '0' : $click_count;?></td>
                      <td class="post-status "  width="15%" align="left"><?php echo ( 0 == $click_count )? '0.00 %' : $ctr . ' %';?></td>
                    </tr>
                    <tr id="row_sub_<?php echo $ad->ID;?>">
                    	<td colspan="4" style="border: none;padding: 0 0 8px 0;"><?php echo $post_title;?></td>
                    </tr>
            <?php $i++;}
				} if($i==0){?>
					<tr>
                      <td colspan="4"><?php _e( 'No Ad found', 'dokan-auction' ); ?></td>
                    </tr>
			<?php }?>
            <?php } else { ?>
            <tr>
              <td colspan="7"><?php _e( 'No Ad found', 'dokan-auction' ); ?></td>
            </tr>
            <?php } ?>
          </tbody>
          <!--<tbody class="scroll">
           <tr>
              <td colspan="4">&nbsp;</td>
            </tr>
          </tbody>-->
        </table>
      </div>
    </article>
  </div>
</div>
<?php //echo do_shortcode("[list_adStats]");?>
<?php
}
function ad_hash_to_id_custom( $hash ) {
	$all_ads = Advanced_Ads::get_ads( array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );
	foreach ( $all_ads as $_ad ) {
		$ad = new Advanced_Ads_Ad( $_ad->ID );
		$options = $ad->options();
		if ( ! isset( $options['tracking'] ) ) continue;
		if ( ! isset( $options['tracking']['public-id'] ) ) continue;
		if ( $hash == $options['tracking']['public-id'] ) return $_ad->ID;
	}
	return false;
}
add_action( 'init', function() {
	add_shortcode( 'list_adStats','list_adStats_func');
} );
function list_adStats_func(){ if(!isset($_GET['id'])||$_GET['id']==""){
		wp_redirect(home_url("/ad-analytics/"));
	}
	$ad_id = ad_hash_to_id_custom($_GET['id']);
	if($ad_id==''){
		$ad_id = base64_decode($_GET['id']);
	}
	$period = ( isset( $_GET['period'] ) && ! empty( $_GET['period'] ) )? stripslashes( $_GET['period'] ) : 'last30days';
	$ad = new Advanced_Ads_Ad( $ad_id );
	$ad_options = $ad->options();
	$ad_name = ( isset( $ad_options['tracking']['public-name'] ) && !empty( $ad_options['tracking']['public-name'] ) )? $ad_options['tracking']['public-name'] : $ad->title;
	$ad = new Advanced_Ads_Ad( $ad_id );
	$ad_options = $ad->options();
	$util = Advanced_Ads_Tracking_Util::get_instance();
	$wptz = Advanced_Ads_Tracking::$WP_DateTimeZone;
        $today = date_create( 'now', $wptz );
        $admin_class = new Advanced_Ads_Tracking_Admin();
        $args = array(
            'ad_id' => array( $ad_id ), // actually no effect
            'period' => 'lastmonth',
            'groupby' => 'day',
            'groupFormat' => 'Y-m-d',
            'from' => null,
            'to' => null,
        );
        
        if ( 'last30days' == $period ) {
            $start_ts = intval( $today->format( 'U' ) );
			// unlike with emails, send the current day, then the last 30 days stops at ( today - 29 days )
            $start_ts = $start_ts - ( 29 * 24 * 60 * 60 );
            
            $start = date_create( '@' . $start_ts, $wptz );
            
            $args['period'] = 'custom';
            $args['from'] = $start->format( 'm/d/Y' );
            
            $end_ts = intval( $today->format( 'U' ) );
            $end = date_create( '@' . $end_ts, $wptz );
            
            $args['to'] = $end->format( 'm/d/Y' );
        }
        
        if ( 'last12months' == $period ) {
			$current_year = intval( $today->format( 'Y' ) );
			$current_month = intval( $today->format( 'm' ) );
			$past_year = $current_year - 1;
			
            $args['period'] = 'custom';
            $args['groupby'] = 'month';
			
            $args['from'] = $today->format( 'm/01/' . $past_year );
            $args['to'] = $today->format( 'm/d/Y' );
        }
        
        $impr_stats = $admin_class->load_stats( $args, 'wp_advads_impressions' );
        $clicks_stats = $admin_class->load_stats( $args, 'wp_advads_clicks' );
        $impr_series = array();
        $clicks_series = array();
        $first_date = false;
        $max_clicks = 0;
        $max_impr = 0;
        
		if( isset( $impr_stats ) && is_array( $impr_stats ) ) {
			foreach ( $impr_stats as $date => $impressions ) {
				if ( ! $first_date ) {
				$first_date = $date;
				}
				$impr = 0;
				if ( isset( $impressions[ $ad_id ] ) ) {
				$impr_series[] = array( $date, $impressions[ $ad_id ] );
				$impr = $impressions[ $ad_id ];
				} else {
				$impr_series[] = array( $date, 0 );
				}
				$clicks = 0;
				if ( isset( $clicks_stats[ $date ] ) && isset( $clicks_stats[ $date ][ $ad_id ] ) ) {
				$clicks_series[] = array( $date, $clicks_stats[ $date ][ $ad_id ] );
				$clicks = $clicks_stats[ $date ][ $ad_id ];
				} else {
				$clicks_series[] = array( $date, 0 );
				}
				if ( $impr > $max_impr ) {
				$max_impr = $impr;
				}
				if ( $clicks > $max_clicks ) {
				$max_clicks = $clicks;
				}
			}
		}
        $lines = array( $impr_series, $clicks_series );
	
	?>
    <style type="text/css">
		.my-listing-custom{
			float: left;
			width: 100%;
		}
		.table > thead > tr > th {
			font-size:17px;
		}
		header.entry-header{display:none;}
		tbody.scroll {
		display:block;
		height:167px;
		overflow:auto;
	}
	thead, tbody tr,tfoot {
		display:table;
		width:100%;
		table-layout:fixed;
	}
	thead,tfoot {
		width: calc( 100% - 1em )
	}
	table {
		width:100%;
	}
		@media (max-width: 448px) {
			.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td{
				padding:8px 2px !important;
			}
		}
	
	</style>
    <table style="width: 100%;">
      <tbody>
        <tr>
          <td><h1 class="entry-title"><?php printf( __( 'Statistics for %s', 'advanced-ads-tracking' ), $ad_name );?><span class="right-align-txt">ShopADoc<span class="TM_title" style="top:10px;">®</span></span></h1></td>
        </tr>
        <tr>
          <td align="right"><form method="get" id="period-form">
              <label>
                <?php _e( 'Period', 'advanced-ads-tracking' ); ?>
                :&nbsp;</label>
              <select name="period">
                <option value="last30days" <?php selected( 'last30days', $period ); ?>>
                <?php _e( 'last 30 days', 'advanced-ads-tracking' ); ?>
                </option>
                <option value="lastmonth" <?php selected( 'lastmonth', $period ); ?>>
                <?php _e( 'last month', 'advanced-ads-tracking' ); ?>
                </option>
                <option value="last12months" <?php selected( 'last12months', $period ); ?>>
                <?php _e( 'last 12 months', 'advanced-ads-tracking' ); ?>
                </option>
              </select>
              <input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
              <input type="submit" class="button button-primary" value="<?php echo esc_attr( __( 'Load', 'advanced-ads-tracking' ) ); ?>" />
            </form></td>
        </tr>
      </tbody>
    </table>
    <div class="dokan-dashboard-wrap nano">
      <div class="dokan-dashboard-content dokan-product-listing my-listing-custom">
        <article class="dokan-product-listing-area">
          <div class="table-wrap"> 
            <!--<table class="table table-striped product-listing-table">-->
            <table class="table product-listing-table">
              <thead>
                <tr>
                  <th class="image_col">Date</th>
                  <th>Impressions</th>
                  <th>Clicks</th>
                  <th>CTR</th>
                  <!--<th>Action</th>--> 
                </tr>
              </thead>
              <tbody class="scroll">
                <?php
                    if( isset( $impr_stats ) && is_array( $impr_stats ) ) :
                    $impr_stats = array_reverse( $impr_stats );
                    $impr_sum = 0; 
                    $click_sum = 0; 
                foreach ( $impr_stats as $date => $all ) : ?>
                <tr>
                  <td><?php 
                                if ( 'last12months' == $period ) {
                                    echo date_i18n( 'M Y', strtotime( $date ) );
                                } else {
                                    echo date_i18n( get_option( 'date_format' ), strtotime( $date ) ); 
                                }
                            ?></td>
                  <td><?php
                                $impr = ( isset( $all[$ad_id] ) )? $all[$ad_id] : 0;
                                $impr_sum += $impr;
                                echo $impr;
                            ?></td>
                  <td><?php
                                $click = ( isset( $clicks_stats[$date] ) && isset( $clicks_stats[$date][$ad_id] ) )? $clicks_stats[$date][$ad_id] : 0;
                                $click_sum += $click;
                                echo $click;
                      ?></td>
                  <td><?php
                                $ctr = 0;
                                if ( 0 != $impr ) {
                                    $ctr = $click / $impr * 100;
                                }
								if($ctr < 1 && $click  > 0){
									echo number_format( $ctr, 7) . ' %';
								}else{
									echo number_format( $ctr, 2 ) . ' %';
								}
                                
                            ?></td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
              <tfoot>
              	<?php 
				if(100 * $click_sum / $impr_sum < 1 && $click_sum > 0){
					$total_ctr = number_format( 100 * $click_sum / $impr_sum, 7);
				}else{
					$total_ctr = number_format( 100 * $click_sum / $impr_sum, 2 ) ;
				}
				?>
              	<tr style="background-color:#f0f0f0;color:#222;font-weight:bold;">
                  <td><?php _e( 'Total', 'advanced-ads-tracking' ); ?></td>
                  <td><?php echo $impr_sum; ?></td>
                  <td><?php echo $click_sum; ?></td>
                  <td><?php echo ( 0 == $click_sum )? '0.00 %' : $total_ctr . ' %'; ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </article>
      </div>
      <script type="text/javascript" src="<?php echo includes_url( '/js/jquery/jquery.js' ); ?>"></script> 
      <script type="text/javascript" src="<?php echo includes_url( '/js/jquery/jquery-migrate.min.js' ); ?>"></script> 
      <script type="text/javascript" src="<?php echo AAT_BASE_URL . 'admin/assets/jqplot/jquery.jqplot.min.js'; ?>"></script> 
      <script type="text/javascript" src="<?php echo AAT_BASE_URL . 'admin/assets/jqplot/plugins/jqplot.dateAxisRenderer.min.js'; ?>"></script> 
      <script type="text/javascript" src="<?php echo AAT_BASE_URL . 'admin/assets/jqplot/plugins/jqplot.highlighter.min.js'; ?>"></script> 
      <script type="text/javascript" src="<?php echo AAT_BASE_URL . 'admin/assets/jqplot/plugins/jqplot.cursor.min.js'; ?>"></script> 
      <script type="text/javascript" src="<?php echo AAT_BASE_URL . 'public/assets/js/public-stats.js'; ?>"></script>
      <link rel="stylesheet" href="<?php echo AAT_BASE_URL . 'admin/assets/jqplot/jquery.jqplot.min.css'; ?>" />
      <link rel="stylesheet" href="<?php echo AAT_BASE_URL . 'public/assets/css/public-stats.css'; ?>" />
      <?php do_action( 'advanced-ads-public-stats-head' ); ?>
      <script type="text/javascript">
                var statsGraphOptions = {
                    axes:{
                        xaxis:{
                            renderer: null,
                            <?php if ( 'last12months' == $period ) : ?>
                            tickOptions: { formatString: '%b %Y' },
                            <?php else : ?>
                            tickOptions: { formatString: '%b%d' },
                            <?php endif; ?>
                            tickInterval: '1 <?php echo $args['groupby']; ?>',
                            min: '<?php echo $first_date; ?>',        
                        },
                        yaxis:{
                            min: 0,
                            max: <?php echo ( intval( $max_impr * 1.1 / 10 ) + 1 ) * 10; ?>,
                            formatString: '$%.2f',
                            label: '<?php _e( 'impressions', 'advanced-ads-tracking' ); ?>',
                        },
                        y2axis:{
                            min: 0,
                            max: <?php echo ( intval( $max_clicks * 1.3 / 10 ) + 1 ) * 10; ?>,
                            label: '<?php _e( 'clicks', 'advanced-ads-tracking' ); ?>',
                        }
                    },
                    highlighter: {
                        show: true,
                    },
                    cursor: {
                        show: false
                    },
                    title: {
                        show: true
                    },
                    series: [
                        {
                            highlighter: {
                                formatString:'%s, %d <?php _e( 'impressions', 'advanced-ads-tracking' ); ?>',
                            },
                            lineWidth: 1,
                            markerOptions: { style: 'circle', size: 5 },
                            color: '#f06050',
                            label: '<?php _e( 'impressions', 'advanced-ads-tracking' ); ?>',
                        }, // impressions
                        {
                            yaxis:'y2axis', 
                            highlighter: {
                                formatString: '%s, %d <?php _e( 'clicks', 'advanced-ads-tracking' ); ?>',
                            },
                            lineWidth: 2,
                            linePattern: 'dashed',
                            markerOptions: { style: 'filledSquare', size: 5 },
                            color: '#4b5de4',
                            label: '<?php _e( 'clicks', 'advanced-ads-tracking' ); ?>',
                        } // clicks
                    ],
                }
                var lines = <?php echo json_encode( $lines ); ?>; 
                </script>
      <div id="public-stat-graph" style="float:left;top:20px;width:100%;font-size:13px;"></div>
      <div id="graph-legend" style="float:left;text-align:center;">
        <div class="legend-item">
          <div id="impr-legend"></div>
          <span class="legend-text">
          <?php _e( 'impressions', 'advanced-ads-tracking' ); ?>
          </span> </div>
        <div class="legend-item">
          <div id="click-legend"></div>
          <span class="legend-text">
          <?php _e( 'clicks', 'advanced-ads-tracking' ); ?>
          </span> </div>
      </div>
    </div>
    <?php
}
add_action( 'init', function() {
	
	add_shortcode( 'list_auctions','list_auctions_func');

} );
function list_auctions_func(){
if (!is_user_logged_in()){
	wp_redirect( home_url( '/my-account/' ) );
	exit();
}
$user = wp_get_current_user();
if($user->roles[0]=='seller'){
	wp_redirect( home_url( '/auction-activity/auction/' ) );
	exit();
}
$dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
if($dentist_account_status =='de-active'){
	wp_redirect( home_url( '/my-account/edit-account/' ) );
	exit();
}
?>
<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/dokan-child/nanoscroller.css">
<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/dokan-child/jquery.nanoscroller.js"></script>
<script type="text/javascript">
function setHeight(){
		var height_header = jQuery(".module__item.header").height();
		var height_ad = jQuery(".ad_section_main").height();
		var height_ad_inner = jQuery(".ad_section_main .rotation_main").height();
		if(height_ad_inner > height_ad){
			var height_ad = jQuery(".ad_section_main .rotation_main").height();
		}
		var height_page = jQuery("#page").height();
		var height_rowHeading = jQuery(".rowHeading").height();
		var height_nano = parseInt(height_page) - (parseInt(height_header) +  parseInt(height_ad) + parseInt(height_rowHeading) + 28);
		//console.log(height_header+"=="+height_top+"=="+height_ad);
		console.log(height_nano);
		jQuery(".details .nano").css('height',height_nano+'px');
		var windowsize = jQuery(window).width();
		if(windowsize > 850){
			jQuery('.nano').nanoScroller({
				preventPageScrolling: true,
				sliderMaxHeight: 27,
				alwaysVisible: true,
				contentClass: 'nano-content',
				//scrollTop: 10
			});
		}else{
			jQuery('.nano').nanoScroller({
				preventPageScrolling: true,
				sliderMaxHeight: 68,
				alwaysVisible: true,
				contentClass: 'nano-content',
				//scrollTop: 10
			});
		}
		
	}
jQuery(function(){
	var windowsize = jQuery(window).width();
	//if(windowsize > 850){
		setTimeout("setHeight()",1000);
		var heightThead = parseInt(jQuery(".my-listing-custom table thead").height());
		//console.log(heightThead);
		/*jQuery('.nano').nanoScroller({
			preventPageScrolling: true,
			sliderMaxHeight: 25,
			alwaysVisible: true,
			contentClass: 'nano-content',
			//scrollTop: 10
		});*/
	//}
});
</script>
<style type="text/css">
.dokan-alert {
	height:auto !important;
}
/*@media only screen and (min-width: 850px) {*/
.nano {
	width: 100%;
	height: 167px;
}
.nano .nano-content {
	padding: 10px;
}
.nano .nano-pane {
	background: #f0f0f0;
}
.nano > .nano-pane > .nano-slider img {
/*display:none;*/
object-fit: cover;
}
.nano > .nano-pane > .nano-slider {
	/*border:solid 2px #444;*/
}

@media only screen and (max-width: 850px) {
.nano > .nano-pane {
	width:5px !important;
}
.nano > .nano-pane > .nano-slider img {
	display:none;
}
.nano > .nano-pane > .nano-slider {
	background-color: rgba(0, 0, 0, .5) !important;
	/*height:68px !important;*/
	border-radius:8px !important;
}
.nano .nano-pane {
	background: transparent;
}
}
.table > thead > tr > th {
	vertical-align: bottom;
	border-bottom: 2px solid #dddddd;
	line-height: 27px;
	font-size:17px;
}
.dokan-dashboard .dokan-dash-sidebar article, .dokan-dashboard .dokan-dashboard-content article {
	overflow:hidden !important;
}
table tbody:nth-of-type(1) tr:nth-of-type(1) td {
	border-top: none !important;
}
table thead th {
	border-top: none !important;
	border-bottom: none !important;
	/*box-shadow: inset 0 2px 0 #dddddd, inset 0 -2px 0 #dddddd;*/
  box-shadow:  inset 0 -2px 0 #dddddd;
	padding: 2px 0;
}
/* and one small fix for weird FF behavior, described in https://stackoverflow.com/questions/7517127/ */
table thead th {
	background-clip: padding-box
}
#posts_table {
	position:relative;
}
.table > thead > tr > th {
	position: sticky;
	position: -webkit-sticky;
	top:0;
	background: #F2F2F2;
}
 @media (max-width: 448px) {
#main .container.content-wrap {
	padding-left:5px;
	padding-right:5px;
}
.image_col {
	display:none !important;
}
.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td {
	padding:8px !important;
}
#content {
	margin-top:7px;
}
}
.dokan-dashboard-wrap * {
	box-sizing: border-box;
}
.newRow {
	height:auto !important;
	margin-right: 0;
	margin-left: 0;
}
.newRow {
	display: table;
	width: 100%;
}
.newRow [class*="col-"] {
	float: none;
	display: table-cell;
	vertical-align: top;
}
.rowHeading [class*="col-"] {
	vertical-align: middle;
	line-height:17px;
}
.newRow .col-md-1 {
	width:10%;
}
.newRow .col-md-3 {
	width:30%;
}
.newRow .col-md-2 {
	width:15%;
}
.newRow .heart_col {
	width:10%;
}
.newRow .date_col {
	width:10%;
}
.newRow .status_col {
	width:25%;
}
.nano-content [class*="col-"] {
	/*height:auto !important;*/
					padding:0 !important;
}
.th {
	vertical-align: bottom;
	border-bottom: 2px solid #dddddd;
	line-height: 27px;
	font-size: 17px;
	padding: 8px 0 !important;
	font-weight:bold;
}
.newRow .image_col img {
	width: auto;
	height: auto;
	max-width: 48px;
	max-height: 48px;
}
.equal {
	display: flex;
	display: -webkit-flex;
	flex-wrap: wrap;
}
.footer-widget {
	border-top:1px solid #dddddd;
	height: 100%;
	width: 100%;
	padding:8px 0 8px 0;
}
.nano {
	/*height: calc(100% - 35px) !important;*/
}
 @media (max-width: 448px) {
.newRow {
	padding-left: 0;
	padding-right: 0;
}
.newRow .col-md-1 {
	width:0;
}
.newRow .col-md-3 {
	width:30%;
}
.newRow .col-md-2 {
	width:15%;
}
.newRow .heart_col {
	width:15%;
}
.newRow .date_col {
	width:15%;
}
.newRow .status_col {
	width:25%;
}
.footer-widget, .footer-widget p {
	line-height:17px;
}
.nano > .nano-pane {
	right:0;
}
.nano {
	height: calc(85% - 20px) !important;
}
}
 @media only screen and (min-width: 448px) and (max-width:850px) {
.newRow {
	padding:0;
}
.footer-widget, .footer-widget p {
	line-height:17px;
}
.nano > .nano-pane {
	right:0;
}
.nano {
	height: calc(90% - 35px) !important;
}
}
</style>
<?php makeFlashLive();?>
<?php //echo do_shortcode('');?>

<div class="dokan-dashboard-wrap dokan-product-listing">
  <div id="no_auction_div"></div>
  <div class="dokan-product-listing-area">
    <div class="row newRow rowHeading">
      <div class="col-12 col-md-1 image_col th">
        <?php _e( 'IMAGE', 'dokan-auction' ); ?>
      </div>
      <div class="col-12 col-md-3 th">
        <?php _e( 'SERVICE', 'dokan-auction' ); ?>
      </div>
      <div class="col-6 col-md-2 ask_fee th center ask_fee">
        <?php _e( 'ASK FEE', 'dokan-auction' ); ?>
      </div>
      <div class="col-6 col-md-2 th center status_col">
        <?php _e( 'STATUS', 'dokan-auction' ); ?>
      </div>
      <div class="col-12 col-md-2 th center date_col">
        <?php _e( 'START DATE', 'dokan-auction' ); ?>
      </div>
      <div class="col-12 col-md-2 th center heart_col"><img src="<?php echo home_url('/wp-content/themes/dokan-child/Heart-Icon-Header-3D.png')?>" alt="" border="0" title="" width="25px"/></div>
    </div>
    <div class="nano">
      <div class="nano-content table-wrap" style="padding:0;">
        <?php
                        global $post,$demo_listing;
						$pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
						$post_statuses = array('publish');
						if(isset($_GET['auction_status']) && $_GET['auction_status']=='past'){}else{
							$args = array(
								'post__not_in' => array($demo_listing),
								'post_status'         => $post_statuses,
								'ignore_sticky_posts' => 1,
								//'orderby'             => 'post_date',
								'meta_key' => '_auction_dates_from',
                   			 	'orderby' => 'meta_value',
								'order'               => 'asc',
								'posts_per_page'      => -1,
								'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
								//'meta_query' => array(array('key' => '_auction_closed','compare' => 'NOT EXISTS',)),
								'auction_archive'     => TRUE,
								'show_past_auctions'  => TRUE,
								'paged'               => $pagenum
							);
						}
						

                        if ( isset( $_GET['post_status'] ) && in_array( $_GET['post_status'], $post_statuses ) ) {
                            $args['post_status'] = $_GET['post_status'];
                        }

                        // $original_post = $post;
                        $product_query = new WP_Query( $args );
						$count = $product_query->found_posts;
						$user_id    = get_current_user_id();
						$favourites = swf_get_favourites( $user_id) ;
						global $radius_distance;
						$i = 0;
                        if ( $product_query->have_posts() ) {
                            while ($product_query->have_posts()) {
                                $product_query->the_post();
                                $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                                $product = dokan_wc_get_product( $post->ID );
								$_auction_start_price = get_post_meta($post->ID, '_auction_start_price',TRUE);
								global $wpdb,$today_date_time;
								$bid_count = $wpdb->get_var("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = '".$post->ID."' and userid = '".dokan_get_current_user_id()."' LIMIT 1");
								$auction_location = get_post_meta($post->ID, '_auction_location',true);
								$dentist_office_address = getDentistAddress();
								$_auction_dates_from =  get_post_meta($post->ID, '_auction_dates_from_org', true );
								$Distance = 0;
								if(trim($dentist_office_address) !="" && trim($auction_location) !=""){
									//echo $dentist_office_address."==".$auction_location."<br />";
									$Distance = get_driving_information($dentist_office_address,$auction_location);
									//if($Distance > 50){
										//return false;
									//}
								}
								$product_cats_ids = wc_get_product_term_ids($post->ID, 'product_cat' );
								$sub_title = '';
								if(in_array(119,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>locators & retrofit service only</i>';
								}
								if(in_array(76,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>abutments & denture only</i>';
								}
								if(in_array(77,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>abutments & dentures only</i>';
								}
								
								if($Distance <= $radius_distance){
									//echo $Distance."==".$dentist_office_address."==".$auction_location."==".$Distance."<br />";
									$i++;
                                ?>
        <div class="row newRow equal">
          <div class="col-12 col-md-1 image_col">
            <div class="footer-widget"><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo $product->get_image(); ?></a></div>
          </div>
          <div class="col-12 col-md-3 service_title">
            <div class="footer-widget">
              <p><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo str_replace("*","",$product->get_title()); ?><?php echo $sub_title;?></a></p>
            </div>
          </div>
          <div class="col-12 col-md-2 post-status ask_fee center">
            <div class="footer-widget">
              <label class="dokan-label">
                <?php
                                        if ( $product->get_price_html() ) {
                                            //echo str_replace("Current bid:","",str_replace("Ask Fee:","",$product->get_price_html()));
											//echo wc_price_mujahid($_auction_start_price);
											echo wc_price_ask_mujahid($_auction_start_price);
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
              </label>
            </div>
          </div>
          <?php 
										$_auction_current_bid = get_post_meta($product->get_id(), '_auction_current_bid', true );
										$_auction_closed = get_post_meta($product->get_id(), '_auction_closed', true );
										$_auction_dates_extend = get_post_meta($product->get_id(), '_auction_dates_extend', true );
										$_auction_extend_counter = get_post_meta($product->get_id(), '_auction_extend_counter', true );
										$_flash_status = get_post_meta($product->get_id(), '_flash_status', true );
										$customer_winner = get_post_meta($product->get_id(),'_auction_current_bider', true);
										//if(!$_auction_current_bid && $_auction_closed==1){
										if($product->is_closed() === TRUE){	
												global $today_date_time;
												$_flash_cycle_start = get_post_meta( $product->get_id() , '_flash_cycle_start' , TRUE);
												$_flash_cycle_end = get_post_meta( $product->get_id() , '_flash_cycle_end' , TRUE);
												if(strtotime($today_date_time) < strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
													if(!$_auction_current_bid){
														$status = '<span>countdown to <span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span></span>';
														$class = " ended";
													}else{
														if($customer_winner == $user_id){
															$status = '<span class="red">✓ Email (Spam)</span>';
														}else{
															$status = '<span>ended</span>';
														}
														$class = " ended";
													}
												}elseif(strtotime($today_date_time) >= strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
													$status = '<span style="color:red;">Flash Bid Cycle<span class="TM_flash">®</span> live</span>';
													$class = " live";
												}else{
													if($customer_winner == $user_id){
														$status = '<span class="red">✓ Email (Spam)</span>';
													}else{
														$status = '<span>ended</span>';
													}
													$class = " ended";
												}
												/*if($customer_winner == $user_id){
													$status = '<span class="red">✓ Email (Spam)</span>';
												}else{
													$status = '<span>ended</span>';
												}*/
												$class = " ended";
										}else{
											if(($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )){
												if($post->post_status=='pending'){
													$status = '<span>countdown to auction</span>';
													$class = " upcoming-pending";
												}else{
													$status = '<span>countdown to auction</span>';
													$class = " upcoming";
												}
											}else{
												if($post->post_status=='pending'){
													$status = '<span>Live: Pending Review</span>';
													$class = " live";
												}else{
													if ($_auction_dates_extend == 'yes' && $_auction_extend_counter == 'no') {
														$status = '<span>extended</span>';
														$class = " extended";
													}else{
														if($_flash_status == 'yes'){
															$status = '<span><span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span> live</span>';
															$class = " live";
														}else{
															$status = '<span style="color:red;">auction live</span>';
															$class = " live";
														}
													}
												}
											}
										}
									?>
          <div class="col-12 col-md-2 post-status status_col center"  id="status_<?php echo $product->get_id();?>">
            <div class="footer-widget">
              <label class="dokan-label <?php echo /*$post->post_status.*/$class; ?>"><?php echo $status;//dokan_get_post_status( $post->post_status ); ?></label>
            </div>
          </div>
          <div class="col-12 col-md-2 post-status date_col center">
            <div class="footer-widget">
              <label class="dokan-label"><?php echo date_i18n('l m/d/Y',strtotime( $_auction_dates_from));?></label>
            </div>
          </div>
          <!--<td class="auction-list-fav"></td>-->
          <div class="col-12 col-md-2 auction-list-fav heart_col center">
            <div class="footer-widget">
              <?php 
									 	if($bid_count >0){?>
              <img class="bid_man" src="<?php echo home_url('/wp-content/themes/dokan-child/Bid-Active-Icon-on.png')?>" alt="" border="0" title="" width="25px"/>
              <?php }else{?>
              <?php //echo do_shortcode("[simple_favourites_button]");?>
              <?php if(in_array($post->ID,$favourites)){ ?>
              <img class="heart_img" src="<?php echo home_url('/wp-content/themes/dokan-child/Heart-Icon-Header.png')?>" alt="" border="0" title="" width="20px" style="margin-left:2px;"/>
              <?php }else{?>
              <img class="heart_img" src="<?php echo home_url('/wp-content/themes/dokan-child/Heart-Icon-Header-empty.png')?>" alt="" border="0" title="" width="20px" style="margin-left:2px;"/>
              <?php }?>
              <?php }?>
            </div>
          </div>
        </div>
        <?php }?>
        <?php } ?>
        <?php } else { ?>
        <div class="row newRow equal">
          <div class="col-12 col-md-12">
           <h2>No auctions as yet in your service area.<br />Please check back frequently.</h2>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php
                wp_reset_postdata();

                $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                if ( $product_query->max_num_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $product_query->max_num_pages,
                        'base'      => add_query_arg( 'pagenum', '%#%' ),
                        'format'    => '',
                        'type'      => 'array',
                        'prev_text' => __( '&laquo; Previous', 'dokan-auction' ),
                        'next_text' => __( 'Next &raquo;', 'dokan-auction' )
                    ) );

                    echo '<ul class="pagination"><li>';
                    echo join("</li>\n\t<li>", $page_links);
                    echo "</li>\n</ul>\n";
                    echo '</div>';
                }
                ?>
  </div>
</div>
</div>
<script type="text/javascript">
	<?php if($i==0){?>
		var html_Text = '<div class="col-12 col-md-12 image_col"><h2>No auctions as yet in your service area.<br />Please check back frequently.</h2></div>';
		jQuery(".nano-content").html(html_Text);
	<?php }?>
</script>
<?php 
}
function list_auctions_func_old(){
if (!is_user_logged_in()){
	wp_redirect( home_url( '/my-account/' ) );
	exit();
}
$user = wp_get_current_user();
if($user->roles[0]=='seller'){
	wp_redirect( home_url( '/auction-activity/auction/' ) );
	exit();
}
$dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
if($dentist_account_status =='de-active'){
	wp_redirect( home_url( '/my-account/edit-account/' ) );
	exit();
}
?>
<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/dokan-child/nanoscroller.css">
<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/dokan-child/jquery.nanoscroller.js"></script>

<!--<script src = "https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>-->
<style type="text/css">
/* width */
.my-listing-custom1::-webkit-scrollbar {
  width: 20px;
}

/* Track */
.my-listing-custom1::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 10px;
}
 
/* Handle */
.my-listing-custom1::-webkit-scrollbar-thumb {
  /*background: red; */
  
  /*background:url(/wp-content/themes/dokan-child/scroll.png) no-repeat;*/
  background: url(/wp-content/themes/dokan-child/scrollTop.png) left top no-repeat,url(/wp-content/themes/dokan-child/scrollBottom.png) left bottom no-repeat;
  background-size:contain;
  background-color:#fff;
  border-radius: 10px;
}

/* Handle on hover */
.my-listing-custom1::-webkit-scrollbar-thumb:hover {
  /*background: #b30000; */
   /*background:url(/wp-content/themes/dokan-child/scroll.png) no-repeat;*/
     background: url(/wp-content/themes/dokan-child/scrollTop.png) left top no-repeat,url(/wp-content/themes/dokan-child/scrollBottom.png) left bottom no-repeat;
  background-size:contain;
  background-color:#fff;
}
/*@media only screen and (min-width: 850px) {*/
.nano {width: 100.7%; height: 167px; }
.nano .nano-content { padding: 10px; }
.nano .nano-pane   { background: #f0f0f0; }
.my-listing-custom::-webkit-scrollbar {
  display: none;
}
.my-listing-custom{
	overflow-x:hidden !important;
}
.nano > .nano-pane > .nano-slider img{
	/*display:none;*/
}
.nano > .nano-pane > .nano-slider{
	border:solid 2px #444;
}

/*}*/
/*@media only screen and (max-width: 448px) {
.nano > .nano-pane{
	background:transparent;
}
}*/
@media only screen and (max-width: 850px) {
.nano > .nano-pane{
	width:5px !important;
}
.nano > .nano-pane > .nano-slider img{display:none;}
.nano > .nano-pane > .nano-slider{
	background-color: rgba(0, 0, 0, .5) !important;
	height:68px !important;
	border-radius:8px !important;
}
.nano .nano-pane {
    background: transparent;
}
/*.my-listing-custom::-webkit-scrollbar {
    -webkit-appearance: none;
}

.my-listing-custom::-webkit-scrollbar:vertical {
    width: 11px;
}

.my-listing-custom::-webkit-scrollbar:horizontal {
    height: 11px;
}

.my-listing-custom::-webkit-scrollbar-thumb {
    border-radius: 8px;
    border: 2px solid white; 
    background-color: rgba(0, 0, 0, .5);
}*/
/*.my-listing-custom{
	padding-right:0 !important;
}*/
}
</style>
<script type="text/javascript">
	jQuery(function(){
	var windowsize = jQuery(window).width();
	//if(windowsize > 850){
		var heightThead = parseInt(jQuery(".my-listing-custom table thead").height());
		var  sliderHeight = 25;
		var heightTheadFinal = heightThead - sliderHeight -10;
		//console.log(heightTheadFinal);
		jQuery('.nano').nanoScroller({
			preventPageScrolling: true,
			sliderMaxHeight: sliderHeight,
			alwaysVisible: true,
			//contentClass: 'my-listing-custom'
			scrollTop: 12,
			//sliderMaxHeight: 200 
		});
	//}
});
/*jQuery(function($){
        $('#posts_table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ], 
            "searching": false, 
            "paging": false,
            "fnInitComplete": function() {
                //$("#posts_table").after(function() {
                  //  return "<div class='nano'><div class='nano-content'><table class='" + this.className + "' id='custom-table'></table></div></div>";
                //});

              //  $('#custom-table').html($('#posts_table').html());
              //  $('#posts_table').remove();
                $(".nano").nanoScroller();
             },
         });
         var $table = $('.nano table');
         $table.floatThead(); 
    });*/
</script>

<style type="text/css">
table tbody:nth-of-type(1) tr:nth-of-type(1) td {
  border-top: none !important;
}

table thead th {
  border-top: none !important;
  border-bottom: none !important;
  /*box-shadow: inset 0 2px 0 #dddddd, inset 0 -2px 0 #dddddd;*/
  box-shadow:  inset 0 -2px 0 #dddddd;
  padding: 2px 0;
}
/* and one small fix for weird FF behavior, described in https://stackoverflow.com/questions/7517127/ */
table thead th {
  background-clip: padding-box
}
#posts_table{position:relative;} 
.table > thead > tr > th {
    position: sticky;
	position: -webkit-sticky;
    top: -10px;
    background: #F2F2F2;
}
.entry-header{display:none !important;}
ul.subsubsub{font-size:15px;margin-top:40px;}
.auction-list-fav .sav-fav-text,.auction-list-fav .fav-text,.auction-list-fav .swf_message{display:none !important;}
.auction-list-fav .swf_container{float:none;text-align:center;}
.my-listing-custom{
	float: left;
	width: 100%;
	height: 167px;
	overflow-y: scroll;
}
.table > thead > tr > th {
	font-size:17px;
}
@media (max-width: 448px) {
	#main .container.content-wrap{
	padding-left:5px;
	padding-right:5px;
}
	.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td{
		padding:8px 2px !important;
	}
}
</style>
<?php makeFlashLive();?>
<?php //echo do_shortcode('');?>
<div class="dokan-dashboard-wrap dokan-product-listing">
	<div id="no_auction_div"></div>
    <div class="dokan-product-listing-area nano">
				<div class="my-listing-custom table-wrap nano-content">
                <table id="posts_table" class="table product-listing-table">
                    <thead>
                        <tr>
                            <th class="image_col" width="10%"><?php _e( 'IMAGE', 'dokan-auction' ); ?></th>
                            <th><?php _e( 'SERVICE', 'dokan-auction' ); ?></th>
                            <th class="center ask_fee" ><?php _e( 'ASK FEE', 'dokan-auction' ); ?></th>
                            <th class="center"><?php _e( 'STATUS', 'dokan-auction' ); ?></th>
                            <th class="center"><?php _e( 'START DATE', 'dokan-auction' ); ?></th>
                            <th width="8%" class="center"><img src="<?php echo home_url('/wp-content/themes/dokan-child/Heart-Icon-Header-3D.png')?>" alt="" border="0" title="" width="25px"/></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        global $post,$demo_listing;
						$pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
						$post_statuses = array('publish');
						if(isset($_GET['auction_status']) && $_GET['auction_status']=='past'){}else{
							$args = array(
								'post__not_in' => array($demo_listing),
								'post_status'         => $post_statuses,
								'ignore_sticky_posts' => 1,
								//'orderby'             => 'post_date',
								'meta_key' => '_auction_dates_from',
                   			 	'orderby' => 'meta_value',
								'order'               => 'asc',
								'posts_per_page'      => -1,
								'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
								//'meta_query' => array(array('key' => '_auction_closed','compare' => 'NOT EXISTS',)),
								'auction_archive'     => TRUE,
								'show_past_auctions'  => TRUE,
								'paged'               => $pagenum
							);
						}
						

                        if ( isset( $_GET['post_status'] ) && in_array( $_GET['post_status'], $post_statuses ) ) {
                            $args['post_status'] = $_GET['post_status'];
                        }

                        // $original_post = $post;
                        $product_query = new WP_Query( $args );
						$count = $product_query->found_posts;
						$user_id    = get_current_user_id();
						$favourites = swf_get_favourites( $user_id) ;
						global $radius_distance;
						$i = 0;
                        if ( $product_query->have_posts() ) {
                            while ($product_query->have_posts()) {
                                $product_query->the_post();
                                $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                                $product = dokan_wc_get_product( $post->ID );
								$_auction_start_price = get_post_meta($post->ID, '_auction_start_price',TRUE);
								global $wpdb,$today_date_time;
								$bid_count = $wpdb->get_var("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = '".$post->ID."' and userid = '".dokan_get_current_user_id()."' LIMIT 1");
								$auction_location = get_post_meta($post->ID, '_auction_location',true);
								$dentist_office_address = getDentistAddress();
								$_auction_dates_from =  get_post_meta($post->ID, '_auction_dates_from_org', true );
								$Distance = 0;
								if(trim($dentist_office_address) !="" && trim($auction_location) !=""){
									//echo $dentist_office_address."==".$auction_location."<br />";
									$Distance = get_driving_information($dentist_office_address,$auction_location);
									//if($Distance > 50){
										//return false;
									//}
								}
								$product_cats_ids = wc_get_product_term_ids($post->ID, 'product_cat' );
								$sub_title = '';
								if(in_array(119,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>locators & retrofit service only</i>';
								}
								if(in_array(76,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>abutments & denture only</i>';
								}
								if(in_array(77,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>abutments & dentures only</i>';
								}
								
								if($Distance <= $radius_distance){
									//echo $Distance."==".$dentist_office_address."==".$auction_location."==".$Distance."<br />";
									$i++;
                                ?>
                                <tr <?php echo $tr_class; ?> id="" >
                                    <td class="image_col"><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo $product->get_image(); ?></a></td>
                                    <td width="30%" class="service_title" ><p><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo str_replace("*","",$product->get_title()); ?><?php echo $sub_title;?></a></p></td>
                                    <td class="post-status" align="center">
                                    <label class="dokan-label">
                                        <?php
                                        if ( $product->get_price_html() ) {
                                            //echo str_replace("Current bid:","",str_replace("Ask Fee:","",$product->get_price_html()));
											//echo wc_price_mujahid($_auction_start_price);
											echo wc_price_ask_mujahid($_auction_start_price);
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                                        </label>
                                    </td>
                                    <?php 
										$_auction_current_bid = get_post_meta($product->get_id(), '_auction_current_bid', true );
										$_auction_closed = get_post_meta($product->get_id(), '_auction_closed', true );
										$_auction_dates_extend = get_post_meta($product->get_id(), '_auction_dates_extend', true );
										$_auction_extend_counter = get_post_meta($product->get_id(), '_auction_extend_counter', true );
										$_flash_status = get_post_meta($product->get_id(), '_flash_status', true );
										$customer_winner = get_post_meta($product->get_id(),'_auction_current_bider', true);
										//if(!$_auction_current_bid && $_auction_closed==1){
										if($product->is_closed() === TRUE){	
												global $today_date_time;
												$_flash_cycle_start = get_post_meta( $product->get_id() , '_flash_cycle_start' , TRUE);
												$_flash_cycle_end = get_post_meta( $product->get_id() , '_flash_cycle_end' , TRUE);
												if(strtotime($today_date_time) < strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
													if(!$_auction_current_bid){
														$status = '<span>countdown to <span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span></span>';
														$class = " ended";
													}else{
														if($customer_winner == $user_id){
															$status = '<span class="red">✓ Email (Spam)</span>';
														}else{
															$status = '<span>ended</span>';
														}
														$class = " ended";
													}
												}elseif(strtotime($today_date_time) >= strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
													$status = '<span style="color:red;">Flash Bid Cycle<span class="TM_flash">®</span> live</span>';
													$class = " live";
												}else{
													if($customer_winner == $user_id){
														$status = '<span class="red">✓ Email (Spam)</span>';
													}else{
														$status = '<span>ended</span>';
													}
													$class = " ended";
												}
												/*if($customer_winner == $user_id){
													$status = '<span class="red">✓ Email (Spam)</span>';
												}else{
													$status = '<span>ended</span>';
												}*/
												$class = " ended";
										}else{
											if(($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )){
												if($post->post_status=='pending'){
													$status = '<span>countdown to auction</span>';
													$class = " upcoming-pending";
												}else{
													$status = '<span>countdown to auction</span>';
													$class = " upcoming";
												}
											}else{
												if($post->post_status=='pending'){
													$status = '<span>Live: Pending Review</span>';
													$class = " live";
												}else{
													if ($_auction_dates_extend == 'yes' && $_auction_extend_counter == 'no') {
														$status = '<span>extended</span>';
														$class = " extended";
													}else{
														if($_flash_status == 'yes'){
															$status = '<span><span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span> live</span>';
															$class = " live";
														}else{
															$status = '<span style="color:red;">auction live</span>';
															$class = " live";
														}
													}
												}
											}
										}
									?>
                                    <td class="post-status status_col" width="30%" align="center">
                                        <label class="dokan-label <?php echo /*$post->post_status.*/$class; ?>"><?php echo $status;//dokan_get_post_status( $post->post_status ); ?></label>
                                    </td>
                                   <td class="post-status date_col" align="center">
                                    <label class="dokan-label"><?php echo date_i18n('l m/d/Y',strtotime( $_auction_dates_from));?></label>
                                    </td>
                                     <!--<td class="auction-list-fav"></td>-->
                                     <td class="auction-list-fav" align="center">
                                     <?php 
									 	if($bid_count >0){?>
                                        	<img class="bid_man" src="<?php echo home_url('/wp-content/themes/dokan-child/Bid-Active-Icon-on.png')?>" alt="" border="0" title="" width="25px"/>
										<?php }else{?>
                                        <?php //echo do_shortcode("[simple_favourites_button]");?>
                                        <?php if(in_array($post->ID,$favourites)){ ?>
												<img class="heart_img" src="<?php echo home_url('/wp-content/themes/dokan-child/Heart-Icon-Header.png')?>" alt="" border="0" title="" width="20px" style="margin-left:2px;"/>
										<?php }else{?>
                                        	<img class="heart_img" src="<?php echo home_url('/wp-content/themes/dokan-child/Heart-Icon-Header-empty.png')?>" alt="" border="0" title="" width="20px" style="margin-left:2px;"/>
                                        <?php }?>
										<?php }?>
                                     </td>
                                </tr>
								<?php }?>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="7"><?php _e( 'No auction found', 'dokan-auction' ); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
				</div>
                <?php
                wp_reset_postdata();

                $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                if ( $product_query->max_num_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $product_query->max_num_pages,
                        'base'      => add_query_arg( 'pagenum', '%#%' ),
                        'format'    => '',
                        'type'      => 'array',
                        'prev_text' => __( '&laquo; Previous', 'dokan-auction' ),
                        'next_text' => __( 'Next &raquo;', 'dokan-auction' )
                    ) );

                    echo '<ul class="pagination"><li>';
                    echo join("</li>\n\t<li>", $page_links);
                    echo "</li>\n</ul>\n";
                    echo '</div>';
                }
                ?>
    </div>
</div>
</div>
<script type="text/javascript">
	<?php if($i==0){?>
		var html_Text = '<tr><td colspan="6"><h2>No auctions as yet in your service area.<br />Please check back frequently.</h2></td></tr>';
		jQuery("table.product-listing-table tbody").html(html_Text);
		//jQuery(".bid_on").addClass("bid_on_new");
		//jQuery(".bid_on_new").removeClass("bid_on");
	<?php }?>
	/*jQuery(document).on('click', ".bid_on", function() {
		jQuery(".sgpb-content.sgpb-content-955").html('Sorry...no active listings in your service area as yet.');
	});*/
</script>

<?php 
}
function change_role_name() {
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
    //You can list all currently available roles like this...
    //$roles = $wp_roles->get_names();


    //You can replace "administrator" with any other role "editor", "author", "contributor" or "subscriber"...
    $wp_roles->roles['seller']['name'] = 'Client';
    $wp_roles->role_names['seller'] = 'Client';   
	$wp_roles->roles['customer']['name'] = 'Dentist';
    $wp_roles->role_names['customer'] = 'Dentist';   
	
/*	$newRoles= array();  
	$newRoles['seller'] = $wp_roles->roles['seller'];
	$newRoles['customer'] = $wp_roles->roles['customer'];
	$wp_roles->roles = $newRoles;
	print_r($wp_roles->roles);*/
	//print_r($wp_roles->roles);
}
add_action('init', 'change_role_name');
add_filter( 'woocommerce_form_field', 'checkout_fields_in_label_error', 10, 4 );

function checkout_fields_in_label_error( $field, $key, $args, $value ) {
   if ( strpos( $field, '</span>' ) !== false && $args['required'] ) {
      $error = '<span class="error" style="display:none">';
      $error .= sprintf( __( 'Billing %s is a required field.', 'woocommerce' ), $args['label'] );
      $error .= '</span>';
      $field = substr_replace( $field, $error, strpos( $field, '</span>' ), 0);
   }
   return $field;
}
function prfx_featured_meta() {
    add_meta_box( 'prfx_meta', __( "We've Updated", 'prfx-textdomain' ), 'prfx_meta_callback', 'page', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'prfx_featured_meta' );
function prfx_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
    //$prfx_stored_meta = get_post_meta( $post->ID );
    ?>
 <p>
    <!--<span class="prfx-row-title"><?php _e( 'Check if content has been updated: ', 'prfx-textdomain' )?></span>-->
    <div class="prfx-row-content">
        <label for="featured-checkbox">
            <input type="checkbox" name="update-checkbox" id="update-checkbox" value="yes" <?php //if ( isset ( $prfx_stored_meta['update-checkbox'] ) ) checked( $prfx_stored_meta['update-checkbox'][0], 'yes' ); ?> /><?php _e( 'Checking this box will force registered users to reaccept the terms pop up', 'prfx-textdomain' )?>
        </label>

    </div>
</p>   
<?php
}
function prfx_meta_save( $post_id ) {
	global $wpdb;
    // Checks save status - overcome autosave, etc.
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
	if($post_id==56 || $post_id==60 || $post_id==62){
		if(isset($_POST['update-checkbox'])){
			//echo "i have update the content";die;
			$query = "SELECT option_id FROM wp_options where option_name like '%termpopup_%'";
			$results = $wpdb->get_results($query, OBJECT);
			foreach($results as $row){
				$wpdb->query('DELETE FROM wp_options WHERE option_id = "'.$row->option_id.'"');
			}
		}
	}
	/*if( isset( $_POST[ 'featured-checkbox' ] ) ) {
		update_post_meta( $post_id, 'update-checkbox', 'yes' );
	} else {
		update_post_meta( $post_id, 'update-checkbox', 'no' );
	}*/

}
add_action( 'save_post', 'prfx_meta_save' );
add_filter( 'woocommerce_get_price_html', 'custom_price_html', 100, 2 );
function custom_price_html( $price, $product ){
	return $price;
    //return str_replace(',', '',$price);
}
//Create admin page 
add_action('admin_menu', 'ad_setting_admin_menu');
function ad_setting_admin_menu() {
    add_options_page('Ad Placement', 'Ad Placement', 'manage_options', __FILE__, 'ad_placement');
	//add_menu_page(__( 'Auctions', 'textdomain' ),'Auctions','shopadoc_admin_cap','?page=auctions-activity','','',6);
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin'){

		add_menu_page(__( 'Home ', 'textdomain' ),'Home','shopadoc_admin_cap','admin.php?page=home_performance','','dashicons-admin-post',1);
		add_menu_page('Performance', 'Performance', 'shopadoc_admin_cap', 'admin.php?page=auction_performance','','dashicons-chart-bar',2);
		
		add_menu_page('Pricing', 'Pricing', 'shopadoc_admin_cap', 'admin.php?page=pricing','','dashicons-chart-bar',3);
		/*
		add_menu_page(__( 'Coupons / Promos', 'textdomain' ),'Coupons / Promos','shopadoc_admin_cap','edit.php?coupon_category=coupon-code&post_type=shop_coupon','','dashicons-megaphone',3);
		add_submenu_page( 'edit.php?coupon_category=coupon-code&post_type=shop_coupon', 'Coupon Codes', 'Coupon Codes','shopadoc_admin_cap', 'edit.php?coupon_category=coupon-code&post_type=shop_coupon');
		add_submenu_page( 'edit.php?coupon_category=coupon-code&post_type=shop_coupon', 'Promo Codes', 'Promo Codes','shopadoc_admin_cap', 'edit.php?coupon_category=promo-code&post_type=shop_coupon');
		add_submenu_page( 'edit.php?coupon_category=coupon-code&post_type=shop_coupon', 'Re-list Discount', 'Re-list Discount','shopadoc_admin_cap', 'post.php?post=1642&action=edit');*/
		
		add_menu_page(__( 'Incoming Contacts', 'textdomain' ),'Incoming Contacts','shopadoc_admin_cap','/admin.php?page=wpforms-entries&view=list&form_id=266','','dashicons-chart-bar',4);
		add_submenu_page( 'admin.php?page=wpforms-entries&view=list&form_id=266', 'Admin', 'Admin','shopadoc_admin_cap', '/admin.php?page=wpforms-entries&view=list&form_id=266');
		add_submenu_page( 'admin.php?page=wpforms-entries&view=list&form_id=266', 'VIP', 'VIP','shopadoc_admin_cap', '/admin.php?page=wpforms-entries&view=list&form_id=4017');
		
		//add_menu_page(__( 'Auction #', 'textdomain' ),'Auction #','shopadoc_admin_cap','edit.php?post_type=product&paged=1','','dashicons-admin-post',5);
		add_menu_page('Auction #', 'Auction #', 'shopadoc_admin_cap', 'admin.php?page=auctions','','dashicons-chart-bar',5);
		add_menu_page('Order #', 'Order #', 'shopadoc_admin_cap', 'admin.php?page=orders','','dashicons-chart-bar',6);
		//add_menu_page(__( 'Orders', 'textdomain' ),'Orders','shopadoc_admin_cap','edit.php?post_type=shop_order','','dashicons-admin-post',6);
		
		add_menu_page('Client / Dentist', 'Client / Dentist', 'shopadoc_admin_cap', 'admin.php?page=CDUSER','','dashicons-chart-bar',7);
		/*add_menu_page(__( 'User', 'textdomain' ),'User','shopadoc_admin_cap','users.php?role=seller','','dashicons-megaphone',7);
		add_submenu_page( 'users.php?role=seller', 'Client', 'Client','shopadoc_admin_cap', 'users.php?role=seller');
		add_submenu_page( 'users.php?role=seller', 'Dentist', 'Dentist','shopadoc_admin_cap', 'users.php?role=customer');
		add_submenu_page( 'users.php?role=seller', 'Advertiser', 'Advertiser','shopadoc_admin_cap', 'users.php?role=advanced_ads_user');*/
		
		//add_menu_page(__( 'AD DEMO', 'textdomain' ),'AD DEMO','shopadoc_admin_cap','users.php?role=ad_demo','','dashicons-megaphone',8);
		add_menu_page('AD DEMO', 'AD DEMO', 'shopadoc_admin_cap', 'admin.php?page=ADDEMO','','dashicons-chart-bar',8);
		
		add_menu_page('ADVERTISERS', 'ADVERTISERS', 'shopadoc_admin_cap', '#','','dashicons-chart-bar',9);
		//add_menu_page('ADVERTISERS', 'ADVERTISERS','shopadoc_admin_cap','admin.php?page=ADVERTISER','','dashicons-chart-bar',9);
		add_submenu_page( '#', 'Current Runs', 'Current Runs','shopadoc_admin_cap', 'admin.php?page=ADVERTISER');
		add_submenu_page( '#', 'Past Runs', 'Past Runs','shopadoc_admin_cap', 'admin.php?page=ADVERTISER_PAST_RUN');
		
		add_menu_page('ADS', 'ADS', 'shopadoc_admin_cap', 'admin.php?page=ADS','','dashicons-chart-bar',10);
		
		/*add_menu_page(__( 'ADS', 'textdomain' ),'ADS','shopadoc_admin_cap','edit.php?post_type=advanced_ads&adtype=image','','dashicons-chart-bar',9);
		add_submenu_page( 'edit.php?post_type=advanced_ads&adtype=image', 'Ads Manager', 'Ads Manager','manage_options', 'edit.php?post_type=advanced_ads&adtype=image');
		add_submenu_page( 'edit.php?post_type=advanced_ads&adtype=image', 'Ads Placement', 'Ads Placement','manage_options', '/options-general.php?page=mnt%2Fdata%2Fhome%2F401140.cloudwaysapps.com%2Fqcqkjmndhe%2Fpublic_html%2Fwp-content%2Fthemes%2Fdokan-child%2Ffunctions.php');*/
		
		/*add_menu_page(__( 'ShopADoc Analytics', 'textdomain' ),'ShopADoc Analytics','shopadoc_admin_cap','/admin.php?page=advanced-ads-stats','','dashicons-chart-bar',9);
		add_submenu_page( 'admin.php?page=advanced-ads-stats', 'Ad Analytics', 'Ad Analytics','shopadoc_admin_cap', '/admin.php?page=advanced-ads-stats');
		add_submenu_page( 'admin.php?page=advanced-ads-stats', 'Google Analytics', 'Google Analytics','shopadoc_admin_cap', '/admin.php?page=analytify-dashboard');*/
		
		add_menu_page(__( 'ShopADoc Analytics', 'textdomain' ),'ShopADoc Analytics','shopadoc_admin_cap','/admin.php?page=advanced-ads-stats','','dashicons-chart-bar',11);
		
		add_menu_page(__( 'Google Analytics', 'textdomain' ),'Google Analytics','shopadoc_admin_cap','/admin.php?page=Analytics','','dashicons-chart-bar',12);
		/*add_submenu_page( '/admin.php?page=analytify-dashboard', 'Dashboard', 'Dashboard','shopadoc_admin_cap', '/admin.php?page=analytify-dashboard');
		add_submenu_page( '/admin.php?page=analytify-dashboard', 'Analytics', 'Analytics','manage_options', 'admin.php?page=Analytics');*/
		//add_submenu_page( '/admin.php?page=analytify-dashboard', '<div id="wpse-66020">Analytics</div>', '<div id="wpse-66020">Analytics </div>','manage_options', 'https://analytics.google.com/analytics/web/#/report/content-event-events/a166289038w232260644p218158068/explorer-segmentExplorer.segmentId=analytics.eventLabel&_r.drilldown=analytics.eventCategory:Advanced%20Ads&explorer-table.plotKeys=%5B%5D');
		
		add_menu_page(__( 'Plug-ins/ Licenses/ IP', 'textdomain' ),'Plug-ins/ Licenses/ IP','shopadoc_admin_cap','upload.php','','dashicons-megaphone',13);
		add_menu_page('<div id="wpse-66024">Updates</div>','<div id="wpse-66024">Updates</div>','shopadoc_admin_cap','/admin.php?page=updates','','dashicons-megaphone',14);
		add_menu_page('<div id="wpse-66022">Staging</div>','<div id="wpse-66022">Staging</div>','shopadoc_admin_cap','https://staging.shopadoc.com/','','dashicons-megaphone',15);
		add_menu_page('<div id="wpse-66023">Storage</div>','<div id="wpse-66023">Storage</div>','shopadoc_admin_cap','https://github.com/ShopADoc-Inc','','dashicons-megaphone',16);
		add_menu_page(__( 'LOG OUT', 'textdomain' ),'LOG OUT','shopadoc_admin_cap',home_url('/?customer-logout=true'),'','dashicons-megaphone',17);
		add_menu_page(__( '<div id="wpse-123">Clear Cache</div>', 'textdomain' ),'<div id="wpse-123">Clear Cache</div>','shopadoc_admin_cap',add_query_arg( '_wpnonce', wp_create_nonce( 'ccfm' ), admin_url() . '?ccfm=1' ),'','dashicons-megaphone',18);
		
		
		
		//add_menu_page(__( 'ShopADoc Ad Analytics', 'textdomain' ),'ShopADoc Ad Analytics','shopadoc_admin_cap','admin.php?page=advanced-ads-stats','','dashicons-chart-bar',3);
		//add_submenu_page( 'performance_auction', 'Total # of Auctions', 'Auction','shopadoc_admin_cap', 'admin.php?page=auction_performance');
		//add_submenu_page( 'performance_auction', 'User', 'User','shopadoc_admin_cap', 'performance_user');
		//add_submenu_page( 'performance_auction', 'Revenue', 'Revenue','shopadoc_admin_cap', 'performance_revenue');
	/*	add_menu_page(__( 'Contact', 'textdomain' ),'VIP Contact','shopadoc_admin_cap','admin.php?page=wpforms-entries&view=list&form_id=4017','','dashicons-chart-bar',4);
		add_submenu_page( 'admin.php?page=wpforms-entries&view=list&form_id=4017', 'Standard', 'Standard','shopadoc_admin_cap', 'admin.php?page=wpforms-entries&view=list&form_id=266');*/
		//add_submenu_page( 'admin.php?page=wpforms-entries&view=list&form_id=266', 'VIP', 'VIP','shopadoc_admin_cap', '/admin.php?page=wpforms-entries&view=list&form_id=4017');
		//add_submenu_page( 'shopadoc_admin_cap', 'Standard', 'Standard','shopadoc_admin_cap', 'admin.php?page=wpforms-entries&view=list&form_id=266');
	
	}
}
add_action( 'admin_menu', function() use ( &$submenu )
{
    $class = 'hide'; // Edit to your needs!
	//print_r($submenu);
    if( ! isset( $submenu['#'][0] ) )
        return;

    if($submenu['#'][0][2] == '#' ) // Append if css class exists
        $submenu['#'][0][4] .= ' ' . $class;
    else                                      
        $submenu['#'][0][4] = $class;

},20);
add_action( 'show_admin_bar', 'show_admin_bar_custom');
function wpforms_manage_cap_custom( $arg ){
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin' || $user->roles[0]=='administrator'){
		  return true;
	}
}
add_filter( 'wpforms_manage_cap', 'wpforms_manage_cap_custom', 10 );
add_action( 'pre_get_posts', 'admin_pre_get_posts_product_query' );
function admin_pre_get_posts_product_query( $query ) {
    global $pagenow,$demo_listing;
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin'){
		// Targeting admin product list
		if( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'product' === $_GET['post_type'] ) {
			$query->set( 'post__not_in',array($demo_listing));
			$query->set( 'product_type','auction'); // Only displays the products created by the current user
			//'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
		}
	}
}
function ad_placement() {
	//print_r($_POST);
	if(isset($_POST['save_ad_setting']) && $_POST['save_ad_setting'] !=""){
		if($_POST['mode']=='dentist'){
			$rotations = array();
			for($i = 1;$i <= 10;$i++){
				$rotation_str = '';
				$option_name = 'position'.$i.'_col1_dentist';
				$new_value = $_POST['position'.$i.'_col1_dentist'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str = $new_value;
				}
				//Col2
				$option_name = 'position'.$i.'_col2_dentist';
				$new_value = $_POST['position'.$i.'_col2_dentist'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str .=",".$new_value;
				}
				//Col3
				$option_name = 'position'.$i.'_col3_dentist';
				$new_value = $_POST['position'.$i.'_col3_dentist'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str .=",".$new_value;
				}
				//Col4
				$option_name = 'position'.$i.'_col4_dentist';
				$new_value = $_POST['position'.$i.'_col4_dentist'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str .=",".$new_value;
				}
				$rotations[$i] .= $rotation_str;
			}
			$rotations_data = maybe_serialize($rotations);
			//print_r($rotations_data);
			$option_name = 'rotations_dentist';
			$new_value = $rotations_data;
			if ( get_option( $option_name ) !== false ) {
				update_option( $option_name, $new_value );
			}else{
				$deprecated = null;
				$autoload = 'yes';
				add_option( $option_name, $new_value, $deprecated, $autoload );
			}
		}else{
			$rotations = array();
			for($i = 1;$i <= 10;$i++){
				$rotation_str = '';
				$option_name = 'position'.$i.'_col1_client';
				$new_value = $_POST['position'.$i.'_col1_client'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str = $new_value;
				}
				//Col2
				$option_name = 'position'.$i.'_col2_client';
				$new_value = $_POST['position'.$i.'_col2_client'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str .=",".$new_value;
				}
				//Col3
				$option_name = 'position'.$i.'_col3_client';
				$new_value = $_POST['position'.$i.'_col3_client'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str .=",".$new_value;
				}
				//Col4
				$option_name = 'position'.$i.'_col4_client';
				$new_value = $_POST['position'.$i.'_col4_client'];
				if($new_value !=""){
					//echo $option_name.'=='.$new_value.'<br />';
					if ( get_option( $option_name ) !== false ) {
						update_option( $option_name, $new_value );
					}else{
						$deprecated = null;
						$autoload = 'yes';
						add_option( $option_name, $new_value, $deprecated, $autoload );
					}
					$rotation_str .=",".$new_value;
				}
				$rotations[$i] .= $rotation_str;
			}
			$rotations_data = maybe_serialize($rotations);
			//print_r($rotations_data);
			$option_name = 'rotations_client';
			$new_value = $rotations_data;
			if ( get_option( $option_name ) !== false ) {
				update_option( $option_name, $new_value );
			}else{
				$deprecated = null;
				$autoload = 'yes';
				add_option( $option_name, $new_value, $deprecated, $autoload );
			}
		}
	}
	?>
    <style type="text/css">
		.notice-error,.error{
			display:none !important;
		}
		ul{float:left;width:100%;margin:0;}
		ul.nav-tabs li{
			float:left;
			width:10%;
			border:solid 1px #888;
			color:#000;
			padding:10px;
			border-radius:5px 5px 0px 0px;
			border-bottom:none;
			text-align:center;
			font-weight:bold;
			margin:0;
		}
		ul.nav-tabs li.active{
			background:#ccc;
		}
		ul.nav-tabs li a{
			color: #000;
			text-decoration:none !important;
		}
		.submit{
			width:100%;
			text-align:right;
		}
		.submit .save-btn {
			padding: 0 28px;
			font-size: 18px;
			font-weight: bold;
		}
	</style>
	<div class="wrap">
    <h2>Ads Placement</h2>
  <div id="poststuff">
    <div id="post-body">
      <form method="post" action="">
        <div class="postbox">
          <!--<h1 class="hndle">
            <label for="title">Ads Placement</label>
          </h1>-->
        
          <?php if(isset($_GET['mode']) && $_GET['mode']== 'dentist'){ ?>
          	  <div class="inside">
            <?php 
				
				global $wpdb;
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '% D%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				$html1 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html1 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				/*
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title not like '% C%' and post_title like '%Col 2%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				//print_r($results);
				$html2 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html2 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title not like '%Client%' and post_title like '%Col 3%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				//print_r($results);
				$html3 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html3 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title not like '%Client%' and post_title like '%Col 4%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				//print_r($results);
				$html4 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html4 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				*/
				?>
              <ul class="nav nav-tabs">
                <li ><a href="<?php echo home_url('wp-admin/options-general.php?page=mnt%2Fdata%2Fhome%2F401140.cloudwaysapps.com%2Fqcqkjmndhe%2Fpublic_html%2Fwp-content%2Fthemes%2Fdokan-child%2Ffunctions.php');?>">Client</a></li>
                <li class="active"><a href="<?php echo home_url('wp-admin/options-general.php?page=mnt%2Fdata%2Fhome%2F401140.cloudwaysapps.com%2Fqcqkjmndhe%2Fpublic_html%2Fwp-content%2Fthemes%2Fdokan-child%2Ffunctions.php&mode=dentist');?>">Dentist</a></li>
              </ul>
              <style type="text/css">
			  .form-table th{padding:4px 0 !important;}
			  .form-table td{padding:5px 10px !important; }
			  .wp-core-ui select{width:75%;}
			  </style>
            <table class="form-table" border="1">
              <thead>
                <tr>
                  <th style="text-align:center" width="10%">ROTATIONS</th>
                  <th style="text-align:center">Column A</th>
                  <th style="text-align:center">Column B</th>
                  <th style="text-align:center">Column C</th>
                  <th style="text-align:center">Column D</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i = 1;$i <= 10;$i++){
					$option_name1 = 'position'.$i.'_col1_dentist';
					$option_name2 = 'position'.$i.'_col2_dentist';
					$option_name3 = 'position'.$i.'_col3_dentist';
					$option_name4 = 'position'.$i.'_col4_dentist';
				?>
                <tr>
                  <td align="center" width="10%"><strong><?php echo $i?></strong></td>
                  <td align="center"><select name="position<?php echo $i?>_col1_dentist" id="position<?php echo $i?>_col1_dentist">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name1) !=""){?><span id="<?php echo $option_name1;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name1).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                  <td align="center"><select name="position<?php echo $i?>_col2_dentist" id="position<?php echo $i?>_col2_dentist">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name2) !=""){?><span id="<?php echo $option_name2;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name2).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                  <td align="center"><select name="position<?php echo $i?>_col3_dentist" id="position<?php echo $i?>_col3_dentist">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name3) !=""){?><span id="<?php echo $option_name3;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name3).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                  <td  align="center"><select name="position<?php echo $i?>_col4_dentist" id="position<?php echo $i?>_col4_dentist">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name4) !=""){?><span id="<?php echo $option_name4;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name4).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                </tr>
                <script type="text/javascript">
						jQuery('#<?php echo $option_name1;?>').change(function(){   
							jQuery("#<?php echo $option_name1;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						jQuery('#<?php echo $option_name2;?>').change(function(){   
							jQuery("#<?php echo $option_name2;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						jQuery('#<?php echo $option_name3;?>').change(function(){   
							jQuery("#<?php echo $option_name3;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						jQuery('#<?php echo $option_name4;?>').change(function(){   
							jQuery("#<?php echo $option_name4;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						document.getElementById('<?php echo $option_name1;?>').value = <?php echo get_option($option_name1);?> ;
						document.getElementById('<?php echo $option_name2;?>').value = <?php echo get_option($option_name2);?> ;
						document.getElementById('<?php echo $option_name3;?>').value = <?php echo get_option($option_name3);?> ;
						document.getElementById('<?php echo $option_name4;?>').value = <?php echo get_option($option_name4);?> ;
				</script>
                <?php 
				
				}?>
              </tbody>
            </table>
            <div class="submit">
              <input type="hidden" name="mode" value="dentist" />
              <input type="submit" class="button-primary save-btn" name="save_ad_setting" value="Save" />
            </div>
          </div>
          <?php }else{?>
         	 <div class="inside">
            <?php 
				
				global $wpdb;
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '% C%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				$html1 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html1 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				/*
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '%Client%' and post_title like '%Col 2%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				//print_r($results);
				$html2 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html2 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '%Client%' and post_title like '%Col 3%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				//print_r($results);
				$html3 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html3 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				$query = "SELECT * FROM wp_posts where post_status = 'publish' and post_type = 'advanced_ads' and post_title like '%Client%' and post_title like '%Col 4%' ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				//print_r($results);
				$html4 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$html4 .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
					}
				}
				*/
				?>
             <ul class="nav nav-tabs">
                <li class="active"><a href="<?php echo home_url('wp-admin/options-general.php?page=mnt%2Fdata%2Fhome%2F401140.cloudwaysapps.com%2Fqcqkjmndhe%2Fpublic_html%2Fwp-content%2Fthemes%2Fdokan-child%2Ffunctions.php');?>">Client</a></li>
                <li ><a href="<?php echo home_url('wp-admin/options-general.php?page=mnt%2Fdata%2Fhome%2F401140.cloudwaysapps.com%2Fqcqkjmndhe%2Fpublic_html%2Fwp-content%2Fthemes%2Fdokan-child%2Ffunctions.php&mode=dentist');?>">Dentist</a></li>
              </ul>
              <style type="text/css">
			  .form-table th{padding:4px 0 !important;}
			  .form-table td{padding:5px 10px !important; }
			  .wp-core-ui select{width:75%;}
			  </style>
            <table class="form-table" border="1">
              <thead>
                <tr>
                  <th style="text-align:center" width="10%">ROTATIONS</th>
                  <th style="text-align:center">Column A</th>
                  <th style="text-align:center">Column B</th>
                  <th style="text-align:center">Column C</th>
                  <th style="text-align:center">Column D</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i = 1;$i <= 10;$i++){ 
					$option_name1 = 'position'.$i.'_col1_client';
					$option_name2 = 'position'.$i.'_col2_client';
					$option_name3 = 'position'.$i.'_col3_client';
					$option_name4 = 'position'.$i.'_col4_client';
					//$value1 = get_option('position'.$i.'_col1_client');
				?>
                <tr>
                  <td align="center" width="10%"><strong><?php echo $i?></strong></td>
                  <td align="center"><select name="position<?php echo $i?>_col1_client" id="position<?php echo $i?>_col1_client">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name1) !=""){?><span id="<?php echo $option_name1;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name1).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                  <td align="center"><select name="position<?php echo $i?>_col2_client" id="position<?php echo $i?>_col2_client">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name2) !=""){?><span id="<?php echo $option_name2;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name2).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                  <td align="center"><select name="position<?php echo $i?>_col3_client" id="position<?php echo $i?>_col3_client">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name3) !=""){?><span id="<?php echo $option_name3;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name3).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                  <td  align="center"><select name="position<?php echo $i?>_col4_client" id="position<?php echo $i?>_col4_client">
                      <option value="">- Please Select Ad -</option>
                      <?php echo $html1;?>
                    </select><?php if(get_option($option_name4) !=""){?><span id="<?php echo $option_name4;?>_view">&nbsp;<a href="<?php echo home_url('/wp-admin/post.php?post='.get_option($option_name4).'&action=edit');?>" target="_blank">View Ad</a></span><?php }?></td>
                </tr>
                <script type="text/javascript">
						jQuery('#<?php echo $option_name1;?>').change(function(){   
							jQuery("#<?php echo $option_name1;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						jQuery('#<?php echo $option_name2;?>').change(function(){   
							jQuery("#<?php echo $option_name2;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						jQuery('#<?php echo $option_name3;?>').change(function(){   
							jQuery("#<?php echo $option_name3;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						jQuery('#<?php echo $option_name4;?>').change(function(){   
							jQuery("#<?php echo $option_name4;?>_view").html('&nbsp;<a href="<?php echo home_url();?>/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit" target="_blank">View Ad</a>');
						});
						document.getElementById('<?php echo $option_name1;?>').value = <?php echo get_option($option_name1);?> ;
						document.getElementById('<?php echo $option_name2;?>').value = <?php echo get_option($option_name2);?> ;
						document.getElementById('<?php echo $option_name3;?>').value = <?php echo get_option($option_name3);?> ;
						document.getElementById('<?php echo $option_name4;?>').value = <?php echo get_option($option_name4);?> ;
				</script>
                <?php 
				
				}?>
              </tbody>
            </table>
            <div class="submit">
              <input type="submit" class="button-primary save-btn" name="save_ad_setting" value="Save" />
            </div>
          </div>
          <?php }?>
        </div>
      </form>
    </div>
  </div>
</div>
<?php 
}

add_filter('wp_generate_attachment_metadata', 'txt_domain_delete_fullsize_image');
function txt_domain_delete_fullsize_image($metadata){
    $upload_dir = wp_upload_dir();
	if(strpos($metadata['file'],'-scaled') !== false){
		$full_image_path = trailingslashit($upload_dir['basedir']) . str_replace("-scaled","",$metadata['file']);
		$deleted = unlink($full_image_path);
	}
    return $metadata;
}
function wpse_240765_unset_images( $sizes ){
    unset( $sizes[ 'thumbnail' ]);
	unset( $sizes[ 'medium' ]);
    unset( $sizes[ 'medium_large' ] );
    unset( $sizes[ 'large' ]);
    unset( $sizes[ 'full' ] );
	
    unset( $sizes[ '1536x1536' ] );
    unset( $sizes[ '2048x2048' ] );
    unset( $sizes[ 'woocommerce_single' ] );
    unset( $sizes[ 'shop_single' ] );
    return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'wpse_240765_unset_images' );

add_filter('woocommerce_login_redirect', 'wc_login_redirect',2,10); 

function wc_login_redirect( $redirect_to, $user ) {
    if(!is_admin()){
		//$user = wp_get_current_user();
		if($user->roles[0]=='seller'){
			$redirect_to = home_url('/auction-activity/auction/');
		}elseif($user->roles[0]=='advanced_ads_user' ){
			global $today_date_time_seconds;
			delete_user_meta($user->ID, '_last_login_session');
			add_user_meta( $user->ID, '_last_login_session', strtotime($today_date_time_seconds));
			/*$redirect_to = home_url('/auction-3977/demo-auction/');
			$dentist_ad = getActiveAds('D');
			$client_ad =  getActiveAds('C');
			if($client_ad==0){
			}elseif($dentist_ad==0){
				$redirect_to .='?screen=client';
			}else{
			}*/
			
			$client_ad = getActiveAD_id("C ",$user->ID);
			$dentist_ad = getActiveAD_id("D ",$user->ID);
			if($client_ad==0 && $dentist_ad > 0){
				$redirect_to = home_url('/auction-3977/demo-auction/');
			}elseif($dentist_ad==0 && $client_ad > 0){
				$redirect_to = home_url('/auction-3977/demo-auction/?screen=client');
			}elseif($dentist_ad==0 && $client_ad == 0){
				$redirect_to =home_url('/');
			}else{
				$redirect_to = home_url('/auction-3977/demo-auction/');
			}
		}elseif($user->roles[0]=='ad_demo'){
			//$timestamp = filter_input( INPUT_POST, 'timestamp' );
			//$timestamp = ( isset( $timestamp ) ) ? $timestamp : null;
			//$timestamp = current_time( 'timestamp' );
			global $today_date_time_seconds;
			//delete_user_meta($user->ID, '__ina_last_active_session');
			//add_user_meta( $user->ID, '__ina_last_active_session', strtotime($today_date_time_seconds));
			delete_user_meta($user->ID, '_last_login_session');
			add_user_meta( $user->ID, '_last_login_session', strtotime($today_date_time_seconds));
			$redirect_to = home_url('/auction-3977/demo-auction/');
			$redirect_to .='?screen=client';
		}elseif($user->roles[0]=='shopadoc_admin'){
			$redirect_to = home_url('wp-admin/admin.php?page=home_performance');
		}elseif(($user->roles[0] == 'administrator')) {
			$redirect_to = home_url('wp-admin');
		}else{
			$redirect_to = home_url('/shopadoc-auction-activity/');
		}
	}
    return $redirect_to;

}
function scriptForLogout() {
	$user = wp_get_current_user();
	$_last_login_session = get_user_meta( $user->ID, '_last_login_session' , true );
	if($user->roles[0]=='ad_demo'){
?>
	<script type="text/javascript">
	function checkLoginDemo(){
             jQuery.ajax({	
                        url:'<?php echo get_site_url();?>/ajax.php',	
                        type:'POST',
                        data:{'mode':'checkLogin','user_id':'<?php echo $user->ID;?>'},
                        beforeSend: function() {},
                        complete: function() {},
                        success:function (data){
							if(data=='logout'){
								 //window.location.href ='<?php echo wp_nonce_url(home_url('/?customer-logout=true'));?>';
								 window.location ='<?php echo home_url('/');?>';
								 return false;
								 //clearTimeout(timer);
							}
						}
                			
                        });
			setTimeout('checkLoginDemo();',1000);
         }
    (function(jQuery) {
         checkLoginDemo();
         //setTimeout('checkLoginDemo();',3000);
    })(jQuery);
    </script>
<?php
	}
	?>
    
	<!-- password_strength -->
	<script type="text/javascript" src="<?php echo home_url('/');?>wp-content/themes/dokan-child/password_strength/PassRequirements.js"></script>
    <!-- <script type="text/javascript" src="password_strength/password_strength.js"></script> -->
    <!-- <script type="text/javascript" src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="<?php echo home_url('/');?>wp-content/themes/dokan-child/password_strength/password_strength.css">
	<script type="text/javascript">
	
	 jQuery(document).ready(function() {
        // jQuery('#wpforms-895-field_4').strength_meter();
		jQuery('.detail_link').bind("click",function(){
			jQuery('.accordion_div').show();
			jQuery('.close_link').show();
			jQuery('body').append('<div class="popup_bg"></div>');
			//setTimeout("jQuery('.accordion_div').hide('slow');jQuery('.close_link').hide();",12000);
		});
		jQuery('.close_link').bind("click",function(){
				jQuery('.accordion_div').hide();
				jQuery('body .popup_bg').remove();
				jQuery('.close_link').hide();
		 });
		
		var tooltip = new jQuery.Zebra_Tooltips(jQuery('.password_tooltip'),{
				'background_color':     '#c9c9c9',
				'color':				'#000000',
				'max_width':  500,
				'opacity':    .95, 
				'position':    'center'
			});
		/*jQuery( "#wpforms-895-field_4,#wpforms-185-field_4" ).keyup(function() {
    		tooltip.show(jQuery('.password_tooltip'), true);
		});*/
		jQuery("#wpforms-895-field_4,#wpforms-185-field_4").focusin(function() { 
               tooltip.show(jQuery('.password_tooltip'), true);
         }); 
		jQuery("#wpforms-895-field_4,#wpforms-185-field_4").focusout(function() { 
			tooltip.hide(jQuery('.password_tooltip'), true);
		}); 
		
		/*jQuery('#wpforms-895-field_4').PassRequirements({
        rules: {
            containSpecialChars: {
                text: "Your input should contain at least minLength special character(s)",
                minLength: 1,
                regex: new RegExp('([^!,%,&,@,#,$,^,*,?,_,~])', 'g')
            },
            containNumbers: {
                text: "Your input should contain at least minLength number(s)",
                minLength: 1,
                regex: new RegExp('[^0-9]', 'g')
            }
        }
    });*/
    });
	</script>
<?php }

add_action( 'wp_footer','scriptForLogout' );
function customize_wc_errors( $error ) {
    if ( strpos( $error, 'Billing ' ) !== false ) {
        $error = str_replace("Billing ", "", $error);
    }
    return $error;
}
add_filter( 'woocommerce_add_error', 'customize_wc_errors' );
// hide coupon field on the checkout page
function disable_coupon_field_on_checkout( $enabled ) {
	if ( is_checkout() ) {
		$flag = 0;
		foreach(WC()->cart->get_cart() as $key => $val ) {
			$_product = $val['data'];
			if($_product->get_id()==1642) {
				$flag = 1;
				break;
			}
		}
		if($flag==1){
			$enabled = false;
		}
	}
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'disable_coupon_field_on_checkout' );
// define the wc_price callback 
function filter_wc_price( $return, $price, $args ) { 
    // make filter magic happen here... 
	$return = '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'.$price.'</span></span>';
    return $return; 
}; 
         
// add the filter 
add_filter( 'wc_price', 'filter_wc_price', 10, 3 );
function wpf_dev_display_submit_before( $form_data, $form ) {
    // Only run on my form with ID = 5
    if ( absint( $form_data['id'] ) !== 1330 ) {
            return;
    } 
    if(isset($_GET['pid']) && $_GET['pid'] !=""){     
    	echo '<div id="back_btn_div"><a href="'.get_permalink($_GET['pid']).'" style="font-size:17px !important;" class="dokan-btn dokan-btn-theme btn-primary" title="Back">Back</a></div>';
	}
}
add_action( 'wpforms_frontend_output_before', 'wpf_dev_display_submit_before', 10, 2 ); 
function dsourc_hide_notices(){
	$user = wp_get_current_user();
	if (!($user->roles[0] == 'administrator')) {
		remove_all_actions( 'admin_notices' );
	}
	?>
    <script type="text/javascript">
        jQuery(document).ready( function($) {   
            $('#wpse-66020').parent().attr('target','_blank');  
			$('#wpse-66021').parent().parent().attr('target','_blank');  
			$('#wpse-66022').parent().parent().attr('target','_blank');  
			$('#wpse-66023').parent().parent().attr('target','_blank');  
			$('#wpse-123').closest("a").attr('id','wp-admin-bar-ccfm-link');  
        });
    </script>
    <?php
}
add_action( 'admin_head', 'dsourc_hide_notices', 1 );
add_filter( 'media_view_strings', 'media_view_strings_custom', 10, 1 );
function media_view_strings_custom($string){
	$string['warnDelete'] = "Delete selected item.\n 'Cancel' to stop, 'OK' to delete.";
	return $string;
	
}
/*function wpb_sender_email() {
    return 'noreply@shopadoc.com';
}
 
// Function to change sender name
function wpb_sender_name() {
    return 'ShopADoc®';
}
 
// Hooking up our functions to WordPress filters 
add_filter('wpforms_email_from_name', 'wpb_sender_email',20);
add_filter('wpforms_email_from_address', 'wpb_sender_name',20);*/

//add_action( 'wpforms_process_entry_save', 'wpf_dev_process_entry_save', 10, 4 );
//add_action( 'wpforms_process_complete', 'wpf_dev_process_complete', 10, 4 );

function wpf_dev_user_registration_user_email( $email ) {
 
    $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
 	$user = get_user_by( 'email', $email['user']->user_login );
	if($user->roles[0]=='seller'){
		$email['subject']  = 'Welcome to ShopADoc®';
	 	$email['message'] = "You’ve successfully completed the registration process.<br /><br />";
		$email['message'] .= sprintf( __( 'User name: %s' ), '<a style="color:#000000 !important;">'.$email['user']->user_login.'</a>' ) . "<br />";
		$email['message'] .= sprintf( __( 'Password: %s' ), $email['password'] ) . "<br /><br />";
		$email['message'] .= "We invite you to <a href='".wp_login_url()."' title='Login' ><strong>log in</strong></a> to ShopADoc®.<br /><br />";
		//$email['message'] .= wp_login_url() . "rnrn";
	}else{
		global $wpdb;
		/*
		$register_id = $wpdb->get_var($wpdb->prepare("SELECT entry_id FROM wp_wpforms_entries WHERE user_id = %d and form_id='895' LIMIT 1",$user->ID));
		
		$entry = wpforms()->entry->get( absint($register_id) );
		$form_data = wpforms()->form->get($entry->form_id,array('content_only' => true,));
		$fields = apply_filters( 'wpforms_entry_single_data', wpforms_decode( $entry->fields ), $entry, $form_data );
		$card = str_replace("Visa","",str_replace("X","",$fields[30]['value']));
		$order_no = date('Y-md-Hi')."-".str_pad($register_id,4,'0', STR_PAD_LEFT);
		*/
/*$data = '';
foreach ($_POST['wpforms']['fields'] as $key=>$value){
    $data .= $key.'-------'.$value;
    $data.= "\n";
}*/
		$email['subject']  = 'Annual Registration Confirmation';
		$email['message'] = "Welcome to ShopADoc®<br />";
		$email['message'] .= "You’ve successfully completed the registration process.<br /><br />";
		$email['message'] .= sprintf( __( 'User name: %s' ), '<a style="color:#000000 !important;">'.$email['user']->user_login ) ."</a><br />";
		$email['message'] .= sprintf( __( 'Password: %s' ), $email['password'] ) . "<br /><br />";
		/*$email['message'] .= '<!--<h2 style="color:#96588a;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">'.date("F j, Y").'</h2>-->
		<table border="0" style="color:#000000;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Order #: '.$order_no.'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>'.date("F j, Y").'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">E<strong>mail: '.$email['user']->user_login.'</strong></td> </tr> <tr><td style="color:#000000;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"><strong>Payment Method: Card ending in '.$card.'</strong></td> </tr></table>
		<table cellspacing="0" cellpadding="6" border="1" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <thead><tr> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Product</th> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Total</th></tr> </thead> <tbody><tr> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"> Annual Registration </td> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"><span><span>$</span><span>99.99</span></span></td></tr> </tbody></table>
		';*/
		$email['message'] .= "We invite you to <a href='".wp_login_url()."' title='Login'>log in</a> to ShopADoc® and follow all auctions within your service area.<br /><br />";
		$start = date("F j, Y");
		$end = date("F j, Y", strtotime('+1 years'));
		$end = date("F j, Y", strtotime('+1 years'));
		$end = date("F j, Y", strtotime('-1 days', strtotime($end)));
		$email['message'] .='<p>Your registration is effective '.$start.' - '.$end.'.</p>';
		$email['message'] .='<p>Renewal will process via auto-pay on your anniversary date.</p>';
		/*$email['message'] .= '<h2 style="color:#96588a;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">'.date("F j, Y").'</h2>
		<table cellspacing="0" cellpadding="6" border="1" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"> <thead><tr> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Product</th> <th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Price</th></tr> </thead> <tbody><tr> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"> Annual Registration </td> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif"><span><span>$</span><span>99.99</span></span></td></tr> </tbody> <tfoot><tr> <th scope="row" style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;border-top-width:4px">Subtotal:</th> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top;border-top-width:4px"><span><span>$</span><span>99.99</span></span></td></tr><tr> <th scope="row" style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top">Total:</th> <td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:top"><span><span>$</span><span>99.99</span></span></td></tr> </tfoot></table>
		';*/
	}
    return $email;
}
add_filter( 'wpforms_user_registration_email_user', 'wpf_dev_user_registration_user_email', 10, 1 );
function custom_wpforms_email_from_address( $from_address, $instance ){ 
   //custom code here
    return 'admin@shopadoc.com';
} 

//add the action 
add_filter('wpforms_email_from_address', 'custom_wpforms_email_from_address', 10, 2);
function custom_wpforms_email_from_name( $wpforms_decode_string, $instance ){ 
   //custom code here
    return 'ShopADoc®';
} 
add_filter('wpforms_email_from_name', 'custom_wpforms_email_from_name', 10, 2);
//add_filter('wpforms_email_from_name', 'custom_wp_mail_from_name' );

add_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name' );
function custom_wp_mail_from_name( $original_email_from ) {
    return 'ShopADoc®';
}

function wpdocs_custom_wp_mail_from( $original_email_address ) {
    return 'admin@shopadoc.com';
}
add_filter( 'wp_mail_from', 'wpdocs_custom_wp_mail_from' );
//add the action 
function update_order_no($post_id, $post, $update) {
    if ($post->post_type == 'shop_order') {
		//$order_no = date('Y-md-Hi')."-".str_pad($post_id,4,'0', STR_PAD_LEFT);
		$billing_state = get_post_meta( $post_id, '_billing_state', true ); 
		$billing_postcode = get_post_meta( $post_id, '_billing_postcode', true ); 
		$fourRandomDigit = rand(1000,9999);
		$order_no = $billing_state.$billing_postcode.'-'.date('Y')."-".$fourRandomDigit."-".str_pad($post_id,4,'0', STR_PAD_LEFT);
        update_post_meta($post_id, 'order_ref_#',$order_no);
    }
}
add_action('wp_insert_post', 'update_order_no', 10, 3 );
// display the extra data in the order admin panel
function kia_display_order_data_in_admin( $order ){  
	$order_ref_no = get_post_meta( $order->id, 'order_ref_#', true ); 
	if($order_ref_no !=""){
?>
    <div class="order_data_column">
        <h4><?php _e( 'Order Ref. #' ); ?></h4>
        <?php 
            echo '<p>' . get_post_meta( $order->id, 'order_ref_#', true ) . '</p>'; ?>
    </div>
    <script type="text/javascript">
		jQuery(".woocommerce-order-data__heading").html('Order #<?php echo get_post_meta( $order->id, 'order_ref_#', true );?> details');
	</script>
<?php 
	}
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'kia_display_order_data_in_admin' );
function filter_woocommerce_display_item_meta( $html, $item, $args ) { 
   	if(strpos($html,'Auction Begins:</strong>') > 0){
		$html = str_replace('Auction Begins:</strong>','Auction Begins:</strong><br />',$html);
	}
	if(strpos($html,'Auction Ends:</strong>') > 0){
		$html = str_replace('Auction Ends:</strong>','Auction Ends:</strong><br />',$html);
	}
	if(strpos($html,'Auction Cycle Starts:</strong>') > 0){
		$html = str_replace('Auction Cycle Starts:</strong>','Auction Cycle Starts:</strong><br />',$html);
	}
	if(strpos($html,'Auction Cycle Ends:</strong>') > 0){
		$html = str_replace('Auction Cycle Ends:</strong>','Auction Cycle Ends:</strong><br />',$html);
	}
	if(strpos($html,'(if needed)</small>:</strong>') > 0){
		$html = str_replace('(if needed)</small>:</strong> <p','(if needed)</small>:</strong> <p style="float:left;width:100%;" ',$html);
	} 
    return $html; 
}; 
         
// add the filter 
add_filter( 'woocommerce_display_item_meta', 'filter_woocommerce_display_item_meta', 10, 3 ); 
function be_wpforms_update_total_field( $fields, $entry, $form_data ) {
	// Only run on my form with ID = 7785
	if( 1330 != $form_data['id'] )
		return $fields;
	$listing_id = $fields[10]['value'];
	$auction_no = get_post_meta($listing_id, 'auction_#',true);
	$fields[8]['value'] = '<a href="'.get_permalink( $listing_id ).'"  >'.$fields[7]['value'].'</a>';
	$fields[9]['value'] = $auction_no;
	unset($fields[7]);
	unset($fields[10]);
	return $fields;
}
add_filter( 'wpforms_process_filter', 'be_wpforms_update_total_field', 10, 3 );
add_filter( 'wpforms_field_properties_phone', 'field_properties_phone_func', 10, 1 );
function field_properties_phone_func($properties){
	$properties['inputs']['primary']['data']['inputmask'] = "'mask': '999-999-9999'";
	return $properties;
}

function pw_loading_scripts_wrong() {
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin'){
	echo '<link rel="stylesheet" href="'.get_site_url().'/wp-content/themes/dokan/assets/css/bootstrap.css"><link rel="stylesheet" href="'.get_site_url().'/wp-content/plugins/wpforms/assets/css/wpforms-full_admin.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script><link rel="stylesheet" href="'.get_site_url().'/wp-content/themes/dokan-child/jQuery-Validation-Engine/css/validationEngine.jquery.css" type="text/css"/></script><script src="'.get_site_url().'/wp-content/themes/dokan-child/jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><script src="'.get_site_url().'/wp-content/themes/dokan-child/jQuery-Validation-Engine/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>';?>
<script type="text/javascript">
function viewUser(user_id,type){
	 //alert("test");
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getAddUserPopup','user_id':user_id,'type':type},
				beforeSend: function() {},
				complete: function() {
					
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-10 col-md-offset-1 no-button popup_grey no-button',
					closeIcon: true, // hides the close icon.
					content: data,
					buttons: {
						Yes: {
							text: "OK",
						   	btnClass: 'btn btn-primary pull-left hide',
							keys: ['enter'],
							action: function(){}
						}
					}

				});
				
				}
				
		
				});
		
		
	}
function addUser(user_id,type){
	 //alert("test");
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getAddUserPopup','user_id':user_id,'type':type},
				beforeSend: function() {},
				complete: function() {
					
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-10 col-md-offset-1 no-button popup_grey',
					closeIcon: true, // hides the close icon.
					content: data,
					buttons: {
						Yes: {
							text: "Save",
						   	btnClass: 'btn btn-primary pull-left',
							keys: ['enter'],
							action: function(){
								/*if(!jQuery("#wpforms-form-ad-demo").validationEngine('validate')){
									return false;
								}*/
								var vars = jQuery("#wpforms-form-ad-demo").serialize();
								jQuery.ajax({	
												url:'<?php echo get_site_url();?>/ajax.php',	
												type:'POST',
												data:{'mode':'submitUser','vars':vars,'user_id':user_id,'type':type},
												beforeSend: function() {},
												complete: function() {
													
												},
												success:function (data){
														//location.reload();
														if(user_id!=""){
															 window.location.replace(window.location.href + "&update=add");
															//window.location.href ='<?php echo get_site_url();?>%2Fwp-admin%2Fadmin.php%3Fpage%3DADDEMO&update=add';
														}else{
															
															 window.location.replace(window.location.href + "&update=add");
															//window.location.href = '<?php echo get_site_url();?>%2Fwp-admin%2Fadmin.php%3Fpage%3DADDEMO&update=edit';
														}
														return true;
												}
								});
								//return true;
								/*if (jQuery("#wpforms-form-ad-demo").validationEngine('validate',{'allrules':{"ajaxUserCallCustom": {
										"url": "<?php echo get_site_url();?>/ajax.php",
										"extraData": "mode=checkUsername",
										//"extraDataDynamic": '',
										//"alertText": "* This user is already taken",
										//"alertTextOk": "All good!",
										"alertTextLoad": "* Validating, please wait"
									}}})) {
								
								}else{
										return false;
								}*/
								
								
							}
						}
					}

				});
				
				}
				
		
				});
		
		
	}
 
 function checkUsername(email){
	// var email = jQuery("#email").val();
	jQuery.ajax({	
			url:'<?php echo get_site_url();?>/ajax.php',	
			type:'POST',
			data:{'mode':'checkEmail','email':email},
			beforeSend: function() {},
			complete: function() {
				
			},
			success:function (data){
					if(data=='exit'){
						jQuery("#email_error").text('This email address is already taken');
						jQuery(".jconfirm-buttons button").attr('disabled','disabled');
						//return true;
					}else{
						jQuery("#email_error").text('');
						jQuery(".jconfirm-buttons button").removeAttr('disabled')
						 //return true;
					}
			}
		});
 }
 function openUserCD(user_id,type,freeze_status){
	 //alert("test");
	 if(freeze_status=='' || freeze_status == 'No'){
		 var label_freeze = 'Suspend Acct';
		 var class_freeze = '';
		 var info_btn = '<img src="/wp-content/themes/dokan-child/icons8-info-26.png" alt="" border="" class="suspend_info" style="margin-left:5px;float:right;margin-top:-3px;" width="25px" height="25px"/>';
	}else{
		var label_freeze = 'Reactivate Acct';
		 var class_freeze = 'btn-blue';
		var info_btn = '<img src="/wp-content/themes/dokan-child/icons8-info-26.png" alt="" border="" class="active_info" style="margin-left:5px;float:right;margin-top:-3px;" width="25px" height="25px"/>';
	}
	if(type=='client'){
		var hideClass =' hide';
	}else{
		var hideClass ='';
	}
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getUserPopup','user_id':user_id,'type':type},
				beforeSend: function() {},
				complete: function() {
					
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-10 col-md-offset-1 no-button popup_grey cd_popup',
					closeIcon: true, // hides the close icon.
					content: data,
					onContentReady: function () {
						//jQuery('.btn.btn-blue.no_bg').before(info_btn);
						jQuery(".tooltips img").closest(".tooltips").css("display", "inline-block");
                    
                        new jQuery.Zebra_Tooltips(jQuery('.tooltips').not('.custom_m_bubble'), {
                            'background_color':     '#c9c9c9',
                            'color':				'#000000',
                            'max_width':  500,
                            'opacity':    .95, 
                            'position':    'center'
                        });
					},
					buttons: {
						Yes: {
							text: "Save",
						   	btnClass: 'btn btn-primary pull-left',
							keys: ['enter'],
							action: function(){
								if(!jQuery("#wpforms-form-895").validationEngine('validate')){
									return false;
								}
								var vars = jQuery("#wpforms-form-895").serialize();
								jQuery.ajax({	
												url:'<?php echo get_site_url();?>/ajax.php',	
												type:'POST',
												data:{'mode':'submitCD','vars':vars,'user_id':user_id,'freeze_status':freeze_status},
												beforeSend: function() {},
												complete: function() {
													
												},
												success:function (data){
														//location.reload();
														//window.location.replace(window.location.href + "&update=add");
														if(user_id!=""){
															//window.location.replace(window.location.href + "&update=add");
															//window.location.href ='<?php echo get_site_url();?>%2Fwp-admin%2Fadmin.php%3Fpage%3DADDEMO&update=add';
														}else{
															
															 //window.location.replace(window.location.href + "&update=add");
															//window.location.href = '<?php echo get_site_url();?>%2Fwp-admin%2Fadmin.php%3Fpage%3DADDEMO&update=edit';
														}
														return true;
												}
								});
							}
						},heyThere: {
							text: info_btn, // With spaces and symbols
							btnClass: 'btn-blue no_bg btn_info'+hideClass,
							action: function () {
								 jQuery.ajax({	
										url:'<?php echo get_site_url();?>/ajax.php',	
										type:'POST',
										data:{'mode':'getSuspendPopup','user_id':user_id,'type':type},
										beforeSend: function() {},
										complete: function() {},
										success:function (data){
											jQuery.confirm({
																title:'<strong>'+label_freeze+'</strong>',
																content: data,
																closeIcon: true,
																columnClass: 'col-md-12 suspendPopUp',
																buttons: {
																	formSubmit: {
																		text: 'Save',
																		btnClass: 'btn-blue hide',
																		action: function () {
																				/*if(!jQuery("#wpforms-form-6724").validationEngine('validate')){
																					return false;
																				}*/
																				var vars = jQuery("#wpforms-form-6724").serialize();
																				jQuery.ajax({	
																								url:'<?php echo get_site_url();?>/ajax.php',	
																								type:'POST',
																								data:{'mode':'submitSuspendReason','vars':vars,'user_id':user_id},
																								beforeSend: function() {},
																								complete: function() {
																									
																								},
																								success:function (data){
																								}
																				});
							
																			
																		}
																	},
																	//cancel: function () {},
																},
																onContentReady: function () {
																	//jQuery('.jconfirm-content-pane').attr('style','height:475px;max-height:100%;overflow-y:scroll;');
																	jQuery('.jconfirm-content-pane').addClass('suspendListArea');
																	jQuery("#submitSuspend").on('click', function (e) {
																		// if the user submits the form by pressing enter in the field.
																		e.preventDefault();
																		var vars = jQuery("#wpforms-form-6724").serialize();
																			jQuery.ajax({	
																					url:'<?php echo get_site_url();?>/ajax.php',	
																					type:'POST',
																					data:{'mode':'submitSuspendReason','vars':vars,'user_id':user_id},
																					beforeSend: function() {},
																					complete: function() {
																						
																					},
																					success:function (data){
																						var tmp = data.split('##');
																						jQuery('#SuspendReason').val(tmp[0]);
																						jQuery('#resultList').html(tmp[1]);
																						//jQuery('.suspendPopUp .jconfirm-content-pane').attr('style','height:100%;max-height:100%;overflow-y:scroll;');
																					}
																			});
																	});
																	/*jQuery.datepicker.setDefaults({
																		dateFormat: "mm/dd/y"
																	});
																	jQuery( '#SuspendDate' ).datepicker();
																	// bind to events
																	jQuery('.wpforms-submit-container').hide();
																	var jc = this;
																	this.jQuerycontent.find('form').on('submit', function (e) {
																		// if the user submits the form by pressing enter in the field.
																		e.preventDefault();
																		jc.jQueryjQueryformSubmit.trigger('click'); // reference the button and click it
																	});*/
																}
															});
										}
									});
							}
						},somethingElse: {
							text:label_freeze ,
							btnClass: 'btn-blue no_bg'+hideClass,
							keys: ['enter', 'shift'],
							action: function(){
								var vars = jQuery("#wpforms-form-895").serialize();
								jQuery.ajax({	
												url:'<?php echo get_site_url();?>/ajax.php',	
												type:'POST',
												data:{'mode':'submitCD_status','user_id':user_id,'type':type,'freeze_status':freeze_status},
												beforeSend: function() {},
												complete: function() {
													
												},
												success:function (data){
														window.location.replace(window.location.href + "&firstname="+jQuery('#firstname').val()+ "&lastname="+jQuery('#lastname').val());
														return true;
												}
								});
							}
						}
					}

				});
				
				}
				
		
				});
		
		
	}
 function openUser(user_id,type){
	 //alert("test");
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getUserPopup','user_id':user_id,'type':type},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-10 col-md-offset-1 no-button popup_grey no-button',
					closeIcon: true, // hides the close icon.
					content: data,
					buttons: {
						Yes: {
							text: "OK",
						   	btnClass: 'btn btn-primary pull-left',
							keys: ['enter'],
							action: function(){
							}
						}
					}

				});
				}
		
				});
		
		
	}
	jQuery(document).ready( function($) {   
			jQuery(".jstree-anchor").click();
        });
</script>
<?php 
	}
}
add_action('admin_head', 'pw_loading_scripts_wrong');
function ac_column_value_usage( $value, $id, AC\Column $column ) {
	// Change the rendered column value
	// $value = 'new value';
	/*
	if ( $column instanceof AC\Column\Post\Shortcodes) {
		$meta_key = $column->get_shortcode();
		//print_r($column);
		echo $meta_key;
	}
	if ( $column instanceof AC\Column\CustomField ) {
		// Custom Field Key
		$meta_key = $column->get_meta_key();
		echo $meta_key;
		// Custom Field Type can be 'excerpt|color|date|numeric|image|has_content|link|checkmark|library_id|title_by_id|user_by_id|array|count'. The default is ''.
		$custom_field_type = $column->get_field_type();
		if (
			'my_hex_color' === $meta_key
			&& 'color' === $custom_field_type
		) {
			$value = sprintf( '<span style="background-color: %1$s">%1$s</span>', $value );
		}
	}
*/
	if($value=='&ndash;'){
		$value = '';
	}

	return $value;
}

add_filter( 'ac/column/value', 'ac_column_value_usage', 10, 3 );
add_action( 'manage_product_posts_custom_column', 'wpso23858236_product_column_offercode', 10, 2 );
function wpso23858236_product_column_offercode( $column, $postid ) {
	include('/public_html/wp-content/plugins/admin-columns-pro/admin-columns/classes/ListScreen/ListScreen.php');
	//echo $column;
	//$new_column = AC_ListScreen::get_column_by_name( $column );
	//print_r($new_column);
	if($column =='60e5c718d8654'){
		global $US_state;
		$auction_state = get_post_meta( $postid, 'auction_state' , TRUE);
		echo $US_state[$auction_state];
	}
	if ( $column == '60390b3f8622a' ) {
		
		$_auction_current_bider = get_post_meta( $postid, '_auction_current_bider' , TRUE);
		if($_auction_current_bider){
		 $seller = get_user_by( 'id', $_auction_current_bider );
		 	//echo $seller->first_name." ".$seller->last_name."<br />".$seller->user_email;
			 echo "<a onclick=\"openUser('".$_auction_current_bider."','dentist');\" href='javascript:'><strong>D</strong></a>";
		}else{
			echo "-";
		}
	}
	if ( $column == '603906d6d0106' ) {
		 $post   = get_post( $postid);
		 $seller = get_user_by( 'id', $post->post_author );
		 //echo $seller->first_name." ".$seller->last_name."<br />".$seller->user_email;
		 echo "<a onclick=\"openUser('".$post->post_author."','client');\" href='javascript:'><strong>C</strong></a>";
	}
	
	if ( $column == '6039002027393' ) {
		$product = dokan_wc_get_product( $postid);
		if($product->is_closed() === TRUE){	
												global $today_date_time;
												$_flash_cycle_start = get_post_meta( $product->get_id() , '_flash_cycle_start' , TRUE);
												$_flash_cycle_end = get_post_meta( $product->get_id() , '_flash_cycle_end' , TRUE);
												if(strtotime($today_date_time) < strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
													if(!$_auction_current_bid){
														$status = '<span>countdown to <span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span></span>';
														$class = " ended";
													}else{
														if($customer_winner == $user_id){
															$status = '<span class="red">✓ Email (Spam)</span>';
														}else{
															$status = '<span>ended</span>';
														}
														$class = " ended";
													}
												}else{
													if($customer_winner == $user_id){
														$status = '<span class="red">✓ Email (Spam)</span>';
													}else{
														$status = '<span>ended</span>';
													}
													$class = " ended";
												}
												/*if($customer_winner == $user_id){
													$status = '<span class="red">✓ Email (Spam)</span>';
												}else{
													$status = '<span>ended</span>';
												}*/
												$class = " ended";
										}else{
											if(($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )){
												if($post->post_status=='pending'){
													$status = '<span>countdown to auction</span>';
													$class = " upcoming-pending";
												}else{
													$status = '<span>countdown to auction</span>';
													$class = " upcoming";
												}
											}else{
												if($post->post_status=='pending'){
													$status = '<span>Live: Pending Review</span>';
													$class = " live";
												}else{
													if ($_auction_dates_extend == 'yes') {
														$status = '<span>extended</span>';
														$class = " extended";
													}else{
														if($_flash_status == 'yes'){
															$status = '<span><span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span> live</span>';
															$class = " live";
														}else{
															$status = '<span style="color:red;">auction live</span>';
															$class = " live";
														}
													}
												}
											}
										}
		echo $status;
    }
}
add_filter( 'woocommerce_products_admin_list_table_filters', 'remove_products_admin_list_table_filters', 10, 1 );
function remove_products_admin_list_table_filters( $filters ){
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin'){
		// Remove "Product type" dropdown filter
		if( isset($filters['product_type']))
			unset($filters['product_type']);
	
		// Remove "Product stock status" dropdown filter
		if( isset($filters['stock_status']))
			unset($filters['stock_status']);
		if( isset($filters['product_category']))
			unset($filters['product_category']);
			
	}

    return $filters;
}
function wpforms_current_user_can_custom( $arg ){
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin' || $user->roles[0]=='administrator'){
		  return true;
	}

  
}
add_filter( 'wpforms_current_user_can', 'wpforms_current_user_can_custom', 10 );
add_action( 'wp_head', 'n8f_add_ios_phone_number_blocker_to_meta');
function n8f_add_ios_phone_number_blocker_to_meta() {
	echo '<meta name="format-detection" content="telephone=no"> <meta name="format-detection" content="email=no">';
}
/*add_filter( 'woocommerce_ajax_loader_url', 'custom_loader_icon', 10, 1 );
function custom_loader_icon($str_replace) {
    $str_replace = home_url('wp-content/themes/dokan-child/woo_loading.gif');
    return $str_replace;
}*/
add_filter( 'authenticate', 'my_login_with_email', 10, 3 );
function my_login_with_email( $user=null, $username, $password ){
    // First, check by username.
    $user = get_user_by( 'login', $username );
 
    // If the username is invalid, check by email.
    if( ! $user ) {
        $user = get_user_by( 'email', $username );  
    }
     
    // Validate the password.
    if( $user ) {
		$pending =  get_user_meta($user->ID, 'wpforms-pending', true );
        if( $pending ) {
    		wp_redirect(home_url('/my-account/?login=approve'));
			exit();
        }
		$deactivate_CD =  get_user_meta($user->ID, 'deactivate_CD', true );
		if($deactivate_CD =='Yes'){
			/*wp_redirect(home_url('/my-account/?login=freeze'));
			exit();*/
		}
    }
     
}
add_filter( 'yith_wcstripe_error_message_order_note', 'yith_wcstripe_error_message_order_note_email_send', 10);
function yith_wcstripe_error_message_order_note_email_send($parm){
	$user = wp_get_current_user();
	$user_id = $user->ID;	
	if($user->roles[0]=='seller'){
		//$user_id = dokan_get_current_user_id();
		$client_street = get_user_meta( $user_id, 'client_street', true);
		$client_apt_no = get_user_meta( $user_id, 'client_apt_no', true);
		$client_city = get_user_meta( $user_id, 'client_city', true);
		$client_state = get_user_meta( $user_id, 'client_state', true);
		$client_zip_code = get_user_meta( $user_id, 'client_zip_code', true);
		$client_cell_ph = get_user_meta( $user_id, 'client_cell_ph', true );
		$client_state = $US_state[$client_state];
	}else{
		//$user_id = dokan_get_current_user_id();
		$client_street = get_user_meta( $user_id, 'dentist_office_street', true);
		$client_apt_no = get_user_meta( $user_id, 'dentist_office_apt_no', true);
		$client_city = get_user_meta( $user_id, 'dentist_office_city', true);
		$client_state = get_user_meta( $user_id, 'dentist_office_state', true);
		$client_zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true);
		$client_cell_ph = get_user_meta( $user_id, 'dentist_personal_cell', true );
		$client_state = $US_state[$client_state];
	}
	define("HTML_EMAIL_HEADERS", array('Content-Type: text/html; charset=UTF-8'));
  	$subject = 'Card Declined';
	$message = 'Dear '.$user->first_name.',<br /><br />
				Your Credit / Debit card was declined. Please <a href="'. site_url( 'my-account/payment-methods/').'">update your card on file</a>.<br /><br />
				Thank you,<br />
				ShopADoc®';
	$heading = '';
	$mailer = WC()->mailer();
	$wrapped_message = $mailer->wrap_message($heading, $message);
	$wc_email = new WC_Email;
	$html_message = $wc_email->style_inline($wrapped_message);
	wp_mail($user->user_email, $subject, $html_message, HTML_EMAIL_HEADERS );
	
	//Admin Email
	$subject_admin = $user->first_name.'  '.$user->last_name.' Card Declined';
	$message_admin = $user->first_name.'  '.$user->last_name.' Credit / Debit card was declined.<br /><br />
			'.$user->user_email.'<br />'.$client_cell_ph;
	$heading = '';
	$mailer = WC()->mailer();
	$wrapped_message_admin = $mailer->wrap_message($heading, $message_admin);
	$wc_email = new WC_Email;
	$html_message_admin = $wc_email->style_inline($wrapped_message_admin);
	$admin_email = get_option( 'admin_email' );
	wp_mail($admin_email, $subject_admin, $html_message_admin, HTML_EMAIL_HEADERS );
	
	return $parm;
}
function wpse_restrict_mimes($mime_types){
    $mime_types = array(
        'jpg|jpeg' => 'image/jpeg',
		'png' =>  'image/png',
		'gif' =>  'image/gif',
		'pdf' =>  'application/pdf',
		'txt' =>  'text/plain',
    );
    return $mime_types;
}

add_filter('upload_mimes', 'wpse_restrict_mimes');
//yith_wcstripe_error_message_order_note
//add_filter( 'woocommerce_email_headers', 'custom_email_notification_headers', 10, 3 );
function custom_email_notification_headers( $headers, $email_id, $order ) {

    // Get the city
  	//$headers[] = 'List-Unsubscribe: <mailto:unsubscribe@shopadoc.com>';
    return $headers;
}
add_filter( 'views_users', 'modify_views_users_so_15295853' );

function modify_views_users_so_15295853( $views ) 
{
    // Manipulate $views
	//print_r($views);
	$views_sort = array();
	$views_sort['all'] = $views['all'];
	$views_sort['seller'] = $views['seller'];
	$views_sort['customer'] = $views['customer'];
	$views_sort['shopadoc_admin'] = $views['shopadoc_admin'];
	$views_sort['ad_demo'] = $views['ad_demo'];
	$views_sort['advanced_ads_user'] = $views['advanced_ads_user'];	
	$views_sort['advanced_ads_admin'] = $views['advanced_ads_admin'];
	
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin'){
		if(isset($_GET['role']) && $_GET['role'] !=""){
			if($_GET['role'] =="seller"){
				unset($views_sort['all']);
				unset($views_sort['customer']);
				unset($views_sort['shopadoc_admin']);
				unset($views_sort['ad_demo']);
				unset($views_sort['advanced_ads_user']);
				unset($views_sort['advanced_ads_admin']);
			}
			if($_GET['role'] =="customer"){
				unset($views_sort['all']);
				unset($views_sort['seller']);
				unset($views_sort['shopadoc_admin']);
				unset($views_sort['ad_demo']);
				unset($views_sort['advanced_ads_user']);
				unset($views_sort['advanced_ads_admin']);
			}
			if($_GET['role'] =="advanced_ads_user"){
				unset($views_sort['all']);
				unset($views_sort['seller']);
				unset($views_sort['customer']);
				unset($views_sort['shopadoc_admin']);
				unset($views_sort['ad_demo']);
				unset($views_sort['advanced_ads_admin']);
			}
			if($_GET['role'] =="ad_demo"){
				unset($views_sort['all']);
				unset($views_sort['seller']);
				unset($views_sort['customer']);
				unset($views_sort['shopadoc_admin']);
				unset($views_sort['advanced_ads_user']);
				unset($views_sort['advanced_ads_admin']);
			}
			
		}
	}
    return $views_sort;
}
add_filter( 'dokan_ensure_vendor_coupon',true);
add_filter( 'woocommerce_coupon_is_valid','woocommerce_coupon_is_valid_custom', 11, 2 );
function woocommerce_coupon_is_valid_custom($valid, $coupon){
	global $checkout;

	// If coupon is invalid already, no need for further checks.
	if ( ! $valid ) {
		return $valid;
	}
	//$coupon_id = ( $this->is_wc_gte_30() ) ? $coupon->get_id() : $coupon->id;
	$coupon_id = $coupon->id;
	$zipcode = get_post_meta( $coupon_id, 'coupon_zipcode', true );
	
	$coupon_state = get_post_meta( $coupon_id, 'coupon_state', true );
	if (empty( $zipcode ) && empty( $coupon_state ) ) {
		return $valid;
	}
	if ( ! empty( $zipcode )){
		$wc_customer  = WC()->customer;
		$current_post_code = ( ! empty( $wc_customer->postcode ) ) ? $wc_customer->postcode : '';
		$post_code = ( ! empty( $current_post_code ) ) ? strtolower( $current_post_code ) : '';
		if ($post_code === $zipcode ) {
						return true;
		}
		throw new Exception( __( 'Coupon is not valid for the Zipcode', 'woocommerce-smart-coupons' )   );
		return false;
	}
	
	//Considtions for State
	/*if (empty( $coupon_state ) ) {
		return $valid;
	}*/
	if ( ! empty( $coupon_state )){
		$wc_customer  = WC()->customer;
		$current_state = ( ! empty( $wc_customer->state ) ) ? $wc_customer->state : '';
		$state = ( ! empty( $current_state ) ) ? strtolower( $current_state ) : '';
		if (strtolower($state) === strtolower($coupon_state)) {
						return true;
		}
		throw new Exception( __( 'Coupon is not valid for the State', 'woocommerce-smart-coupons' )   );
		return false;
	}
	return $valid;

}
function wpf_dev_footer_text( $footer ) {
    $footer = '<strong style="color:#555555">ShopADoc® The Dentist Marketplace</strong>';
    return $footer;
}
add_filter( 'wpforms_email_footer_text', 'wpf_dev_footer_text' );
add_action( 'admin_bar_menu', 'wp_admin_bar_site_menu_custom', 31 );
function wp_admin_bar_site_menu_custom($wp_admin_bar){
	$wp_admin_bar->add_node(
			array(
				'id'     => 'view-site_heading',
				'title'  => ' | Admin Master Control Board',
				'href'   => '#',
			)
		);
	return $wp_admin_bar;
}
//add_filter( 'woocommerce_register_post_type_shop_coupon', 'filter_function_name_7460' );
add_filter( 'woocommerce_register_post_type_shop_coupon', 'filter_woocommerce_register_post_type_shop_coupon', 10, 1 ); 
function filter_woocommerce_register_post_type_shop_coupon( $array ){
	// filter...
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin' ){
		if($_GET['coupon_category'] =="promo-code"){
			$array['labels']['name']='Promo Codes';
			$array['labels']['add_new']='Add Promo';
			$array['labels']['add_new_item']='Add new Promo';
		}else{
			$array['labels']['name']='Coupon Codes';
			$array['labels']['add_new']='Add Coupon';
			$array['labels']['add_new_item']='Add new Coupon';
		}
	}
	return $array;
}
add_action( 'wpforms_entry_list_title', 'wpforms_entry_list_title_custom', 31 );
function wpforms_entry_list_title_custom($form_data){
	$user = wp_get_current_user();
	if($user->roles[0]=='shopadoc_admin' ){
		if($_GET['form_id'] =="4017"){
			echo '<style>.page-title{display:none !important;}
			#toplevel_page_admin-page-wpforms-entries-view-list-form_id-266 a.menu-top{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-wpforms-entries-view-list-form_id-266 a.menu-top:after {
			right: 0;
			border: solid 8px transparent;
			content: " ";
			height: 0;
			width: 0;
			position: absolute;
			pointer-events: none;
			border-right-color: #f0f0f1;
			top: 50%;
			margin-top: -8px;
			}.column-wpforms_field_5{width:27%;}#wpforms-entries-list .wp-list-table .column-date{width:15% !important;}</style><h1 style="padding:15px 0;color:#0A7BE2 !important;">VIP@ShopADoc.com</h1>';
		}
		if($_GET['form_id'] =="266"){
			echo '<style>.page-title{display:none !important;}#toplevel_page_admin-page-wpforms-entries-view-list-form_id-266 a.menu-top{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-wpforms-entries-view-list-form_id-266 a.menu-top:after {
			right: 0;
			border: solid 8px transparent;
			content: " ";
			height: 0;
			width: 0;
			position: absolute;
			pointer-events: none;
			border-right-color: #f0f0f1;
			top: 50%;
			margin-top: -8px;
			}.column-wpforms_field_2{width:40%;}#wpforms-entries-list .wp-list-table .column-date{width:15% !important;}</style><h1 style="padding:15px 0;color:#0A7BE2 !important;">Admin@ShopADoc.com</h1>';
		}
	}
	return $form_data;
}
add_filter( 'wpforms_entries_table_columns', 'wpforms_entries_table_columns_custom', 10, 2 );
function wpforms_entries_table_columns_custom($columns,$form_data){
	if($form_data['id']==4017){
		unset($columns['date']);
		unset($columns['cb']);
		unset($columns['indicators']);
		unset($columns['wpforms_field_0']);
		unset($columns['wpforms_field_1']);
		//wpforms_field_0
		//array_unshift($columns,'Date');
		//array_unshift($columns,'cb');
		//print_r($columns);
		$firstItem = array('cb'=>'<input type="checkbox">','indicators'=>'','date' => 'Date','wpforms_field_0'=>'Name','wpforms_field_1'=>'Email','wpforms_field_4'=>'Company');
		
		//$columns['wpforms_field_4'] = 'Company';
		$columns = $firstItem + $columns;
		$columns['wpforms_field_5'] = 'Message';

	}
	if( $form_data['id']==266){
		unset($columns['date']);
		unset($columns['cb']);
		unset($columns['indicators']);
		//array_unshift($columns,'Date');
		//array_unshift($columns,'cb');
		//print_r($columns);
		$firstItem = array('cb'=>'<input type="checkbox">','indicators'=>'','date' => 'Date');
		$columns = $firstItem + $columns;
		$columns['wpforms_field_2'] = 'Message';
	}
	if( $form_data['id']==266){
		//print_r($columns);
		//$columns['wpforms_field_4'] = 'Describe Abuse';
	}
	if( $form_data['id']==1330){
		//print_r($columns);
		//Describe Abuse
	}
	return $columns;
}
add_filter("wpforms_entry_table_args","wpforms_entry_table_args_custom",10,1);
function wpforms_entry_table_args_custom($args){
	//echo $args['form_id'];
	if($args['form_id']==4017){
		$args['form_id'] = array(523,$args['form_id']);
	}
	if($args['form_id']==266){
		$args['form_id'] = array(1330,$args['form_id']);
	}
	return $args;
}
// Replace WordPress Howdy in Admin bar
add_filter( 'admin_bar_menu', 'replace_wordpress_howdy', 25 );
function replace_wordpress_howdy( $wp_admin_bar ) {
$my_account = $wp_admin_bar->get_node('my-account');
$newtext = str_replace( 'Howdy,', '', $my_account->title );
$wp_admin_bar->add_node( array(
'id' => 'my-account',
'title' => $newtext,
) );
}
function pricing_shortcode($atts){
	$types = array('C Listing Fee'=>'126','D Registration'=>'1141','D Subscription'=>'948','D Weekly Fee'=>'942','Relist Fee'=>'1642',);
	if($types[$atts['type']]){
		
		$price = get_post_meta($types[$atts['type']],"_regular_price",true);
		return '$'.number_format($price, 2, '.', '');
	}
}
add_shortcode('pricing', 'pricing_shortcode');
function Relist_Auctions_shortcode($atts){
	return '';
	 global $wpdb,$post,$today_date_time,$today_date_time_seconds;
	$post_statuses = array( 'publish');
	$args = array(
						'post_status'         => $post_statuses,
						'ignore_sticky_posts' => 1,
						//'orderby'             => 'post_date',
						'meta_key' => '_auction_dates_from',
						'orderby' => 'meta_value',
						'order'               => 'asc',
						'author'              => dokan_get_current_user_id(),
						'posts_per_page'      => -1,
						'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
						//'meta_query' => array(array('key' => '_auction_closed','compare' => 'NOT EXISTS',)),
						'auction_archive'     => TRUE,
						'show_past_auctions'  => TRUE,
						'paged'               => 1
					);
		 $product_query = new WP_Query( $args );
		//_auction_dates_from
		$count = $product_query->found_posts;
		$price_listing = get_post_meta('126',"_regular_price",true);
		$price_relist = get_post_meta('1642',"_regular_price",true);
		$html = '';
		if ( $product_query->have_posts() ) {
			$html1 .='
				<div class="row newRow rowHeading">
							<div class="col-12 col-md-1 th" style="width:20%;" style="text-align:center;text-decoration:underline;padding-bottom:10px;">SELECT</div>
							  <div class="col-12 col-md-5 th" style="width:45%;" style="text-decoration:underline;padding-bottom:10px;">SERVICE</div>
							  <div class="col-6 col-md-2 th center" style="width:35%;" style="text-align:center;text-decoration:underline;padding-bottom:10px;">DISCOUNTED FEE</div>
							</div> 
							<div class="nano">
							<div class="nano-content table-wrap" style="padding:0;">';
		
			//$html .= '<table width="100%" border="0" class="relist_table"><thead><th width="15%" align="center" style="text-align:center;text-decoration:underline;padding-bottom:10px;">SELECT</th><th style="text-decoration:underline;padding-bottom:10px;" width="50%">SERVICE</th><th width="35%" align="center" style="text-align:center;text-decoration:underline;padding-bottom:10px;" width="30%">DISCOUNTED FEE</th></thead><tbody>';
			while ($product_query->have_posts()) {
                    $product_query->the_post();
					$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$post->ID));
					$_auction_dates_to = get_post_meta($post->ID, '_auction_dates_to', true );
					$_flash_cycle_start = get_post_meta( $post->ID, '_flash_cycle_start' , TRUE);
					$_flash_cycle_end = get_post_meta( $post->ID, '_flash_cycle_end' , TRUE);
					if(strtotime($_auction_dates_to) < strtotime($today_date_time_seconds) && strtotime($today_date_time_seconds) > strtotime($_flash_cycle_end) && ($bid_count == '' || $bid_count == 0) || (isset($_GET['mode']) && $_GET['mode']=='test')){
						$newtimestamp = strtotime($_flash_cycle_end.' + 3 minute');
						$to_date = date('Y-m-d H:i:s', $newtimestamp);
						if(strtotime($today_date_time_seconds) > strtotime($to_date)){
						}else{
							//List Auction here
						
							//<input id="plan0" name="plan" type="radio" value="relist" /> [pricing type='Relist Fee'] (Reg. [pricing type='C Listing Fee']) Post my auction again!
							//<label class="checkbox container_my"><input type="checkbox" name="relist_ids[]" id="" value="'.$post->ID.'" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" /><span class="checkmark_my"></span></label>
							$html1 .= '<div class="row newRow equal">
											<div class="col-12 col-md-1" style="width:20%;"><div class="footer-widget" style="text-align:center;"><label class="checkbox container_my_relist"><input type="checkbox" name="relist_ids[]" id="" value="'.$post->ID.'" class="relist_radio woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" /><span class="checkmark_my_relist"></span></label></div></div>
											<div class="col-12 col-md-1" style="width:45%;"><div class="footer-widget"><span class="relist_txt" style="color:#0A79E2;">'.$post->post_title.'</span></div></div>
											<div class="col-12 col-md-1 center" style="width:35%;"><div class="footer-widget" style="color:##444444;"><span style="text-decoration:underline;">$'.$price_relist.'</span>&nbsp;&nbsp;(reg. $'.$price_listing.'</div></div>
							
							</div>';
							//$html .='<tr><td width="15%" align="center"><label class="checkbox container_my_relist"><input type="checkbox" name="relist_ids[]" id="" value="'.$post->ID.'" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" /><span class="checkmark_my_relist"></span></label></td><td width="50%" ><span class="relist_txt" style="color:#0A79E2;">'.$post->post_title.'</span></td><td width="35%" align="center" style="color:##444444;"><span style="text-decoration:underline;">$'.$price_relist.'</span>&nbsp;&nbsp;(reg. $'.$price_listing.')</td></tr>';
						}
						
					}
			}
			//$html .='</tbody></table>';
			$html1 .='</div></div>';
		}
		if((isset($_GET['mode']) && $_GET['mode']=='test') || 1==1){
			$html1 .= '<style>.relist_radio{left:25px !important;margin-left:0 !important;}.checkmark_my_relist{left:15px !important;}.nano {
				width: 100%;
				height: 167px;
				overflow-y: auto;
			}
			.newRow{height:auto !important;margin-right: 0;margin-left: 0;}
			.newRow {
							display: table;
							width: 100%;
						}						
					.newRow [class*="col-"] {
						float: none;
						display: table-cell;
						vertical-align: top;
					}
					.rowHeading [class*="col-"]{
						vertical-align: middle;
						line-height:17px;
					}
					.newRow .col-md-1{
						width:10%;
					}
					.newRow .col-md-5{
						width:42%;
					}
					.newRow .col-md-2{
						width:16%;
					}
				
				.nano-content [class*="col-"] {
					/*height:auto !important;*/
					padding:0 !important;
				}
			.th{
				vertical-align: bottom;
				border-bottom: 2px solid #dddddd;
				line-height: 27px;
				font-size: 17px;
				padding: 8px 0 !important;
				font-weight:bold;
			}
		
			.newRow img {
					width: auto;
					height: auto;
					max-width: 48px;
					max-height: 48px;
				}
				.equal {
				  display: flex;
				  display: -webkit-flex;
				  flex-wrap: wrap;
				}
				.footer-widget {
					border-top:1px solid #dddddd;
					height: 100%;
					width: 100%;
					padding:8px 0 8px 0;
				}</style>';
			return $html1;
		}else{
			return $html;
		}
}
add_shortcode('Relist_Auctions', 'Relist_Auctions_shortcode');
add_filter('auth_cookie_expiration', 'my_expiration_filter', 99, 3);
function my_expiration_filter($seconds, $user_id, $remember){
	$the_user = get_user_by( 'id', $user_id );
	if($user->roles[0]!='ad_demo'){
		//if "remember me" is checked;
		if ( $remember ) {
			//WP defaults to 2 weeks;
			$expiration = 365*24*60*60; //UPDATE HERE;
		} else {
			//WP defaults to 48 hrs/2 days;
			//$expiration = 2*24*60*60; //UPDATE HERE;
			$expiration = 365*24*60*60; //UPDATE HERE;
		}
	}

    //http://en.wikipedia.org/wiki/Year_2038_problem
    /*if ( PHP_INT_MAX - time() < $expiration ) {
        //Fix to a little bit earlier!
        $expiration =  PHP_INT_MAX - time() - 5;
    }*/

    return $expiration;
}
add_filter('dashboard_glance_items', 'dashboard_glance_items_custom', 8,1);
function dashboard_glance_items_custom($param){
	global $current_user;
	echo "mujahid is here";
	if($current_user->roles[0]=='shopadoc_admin'){
		wp_redirect(home_url('/wp-admin/admin.php?page=home_performance'));
         exit();
	}
	//$current_user->roles[0]=='ad_demo'
}
/*
add_action( 'wp', function() {
  if( is_product() && ! is_admin() ) { // restrict page ID '2072' if it's on the front end and if user doesn't have the permissions
    $base = [ // allowed referees
     home_url( '/auction-activity/auction/'), // referee 1
    ];
	//print_r($_SERVER['HTTP_REFERER']);
    if( ! in_array( $_SERVER['HTTP_REFERER'], $base ) ) { // if not in referees
      wp_redirect(home_url( '/auction-activity/auction/')); // redirect in 3 seconds
      exit; // terminate the current script
    };
  };
} );
*/
function custom_search_form( $form ) {
  $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
    <div class="custom-search-form"><label class="screen-reader-text" for="s">' . __( 'Search:' ) . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" />
    <input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
  </div>
  </form>';

  return $form;
}

add_filter( 'get_search_form', 'custom_search_form', 100 );
add_filter('woocommerce_simple_auctions_minimal_bid_value','woocommerce_simple_auctions_minimal_bid_value_custom',10,3);
function woocommerce_simple_auctions_minimal_bid_value_custom($bid_value,$product_date,$current_bid){
	if($bid_value==0){
		return .99;
	}
	return $bid_value;
}
//Dequeue Styles
function project_dequeue_unnecessary_styles() {
    wp_dequeue_style( 'dokan-fontawesome' );
	wp_dequeue_style( 'font-awesome-5-all' );
	wp_dequeue_style( 'font-awesome-4-shim' );
}
add_action( 'wp_print_styles', 'project_dequeue_unnecessary_styles' );
add_filter("advanced-ads-tracking-redirect-url","advanced_ads_tracking_redirect_url_custom",20,1);
function advanced_ads_tracking_redirect_url_custom($url){
	return "//".str_replace("https://","",str_replace("http://","",$url));
}
/*add_filter('wp_mail','custom_mails', 10,1);
function custom_mails($args){
	if(strtolower($args['to']) =='client@shopadoc.com' || strtolower($args['to']) =='dentist@shopadoc.com'){
		//admin@shopadoc.com
		$cc_email = sanitize_email('admin@shopadoc.com');
		if (is_array($args['headers'])){
			$args['headers'][] = 'cc: '.$cc_email;
		}else {
			$args['headers'] .= 'cc: '.$cc_email."\r\n";
		}
	}
	return $args;
}*/
add_filter("loggedin_reached_limit","loggedin_reached_limit_custom",20,3);
function loggedin_reached_limit_custom($reached, $user_id, $count ){
	$maximum = intval( get_option( 'loggedin_maximum', 1 ) );
	$manager = WP_Session_Tokens::get_instance( $user_id );
	$sessions = $manager->get_all();
	$ips = array();
	foreach($sessions as $session){
		$ips['ip'][] = $session['ip'];
	}
	
	$count = count(array_unique($ips['ip']));
	$reached = $count >= $maximum;
	if((isset($_GET['mode']) && $_GET['mode']=='test')){
		$reached='';
	}
	return $reached;
}
?>