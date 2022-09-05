<?php
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
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch);
    return $result;
}

$path = "https://api.stripe.com/v1/subscriptions/sub_1Kyz58FEG1D3TdUzq4hrzQzX";
 
$data = array("at_period_end"=>"true");
$curl_del = curl_del($path,$data);
echo "<pre>";
print_r($curl_del);
echo "</pre>";

/*


$headers = ['Accepts: application/json','X-CMC_PRO_API_KEY: 72ee99cf-5475-4356-a803-e283b16a2a44'];
$request = "{$url}";
$curl = curl_init(); // Get cURL resource
// Set cURL options
curl_setopt_array($curl, array(
  CURLOPT_URL => $request,            // set the request URL
  CURLOPT_HTTPHEADER => $headers,     // set the headers 
  CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
));
$response = curl_exec($curl); // Send the request, save the response
//$BTRST_JSON = json_decode($response); // print json decoded response
curl_close($curl);
print_r($response);
/* $token_id = 11584;
 print_r($BTRST_JSON);
$BTRST_current_price_today = $BTRST_JSON->data->$token_id->quote->USD->price;*/
die;
?>


<script type="text/javascript">
/* var time = new Date();
var seconds = time.getSeconds();
alert(seconds);*/
</script>
<?php
die;
/* $fourRandomDigit = rand(1000,9999);
    echo $fourRandomDigit;
	die;*/
define('WP_USE_THEMES', true);
require($_SERVER['DOCUMENT_ROOT'].'wp-load.php');
/*wp_mail('mujahidriazpk@gmail.com',"test",'<div class="buttons" id="not_print"><a href="javascript:window.print();" class="button button-primary print" style="font-size:18px;">Printable Version</a></div>');*/
define("HTML_EMAIL_HEADERS", array('Content-Type: text/html; charset=UTF-8'));
  	$subject = 'Test';
	$message = 'Dear ,<br /><br />
				Your Credit / Debit card was declined. Please <a href="'. site_url( 'my-account/payment-methods/').'">update your card on file</a>.<div class="buttons" id="not_print"><a href="javascript:" class="button button-primary print" onclick="window.print();" style="font-size:18px;">Printable Version</a></div><br /><br />
				Thank you,<br /><a onclick="window.print()">Print this page</a>
				ShopADoc®';
	$heading = '';
	$mailer = WC()->mailer();
	$wrapped_message = $mailer->wrap_message($heading, $message);
	$wc_email = new WC_Email;
	$html_message = $wc_email->style_inline($wrapped_message);
	wp_mail('mujahidriazpk@gmail.com', $subject, $html_message, HTML_EMAIL_HEADERS );
die;
//print_r($_SERVER);
/*$req = curl_init('https://www.google-analytics.com/collect');

curl_setopt_array($req, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_POSTFIELDS =>
    'v=1&t=event&tid=UA-166289038-1&cid=GA1.2.670092755.1629466143&ec=Advanced%20Ads&ea=submit&el=Feedback%20Form%20Submission'
));
// Send the request
$response = curl_exec($req);
print_r($response);*/
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
				foreach($US_state as $key=>$val){
					//echo '"'.$val .'" => "'.$key.'",<br />';
				}
die;
/*
define('WP_USE_THEMES', true);
require($_SERVER['DOCUMENT_ROOT'].'wp-load.php');
global $wpdb;
$tempArray = maybe_unserialize('a:14:{s:7:"visitor";a:0:{}s:8:"visitors";a:0:{}s:6:"output";a:6:{s:8:"image_id";s:4:"1975";s:8:"position";s:0:"";s:6:"margin";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:10:"wrapper-id";s:0:"";s:13:"wrapper-class";s:0:"";s:11:"custom-code";s:0:"";}s:4:"type";s:5:"image";s:3:"url";s:20:"http://grossiweb.com";s:5:"width";i:300;s:6:"height";i:250;s:10:"conditions";a:0:{}s:11:"expiry_date";i:0;s:11:"description";s:0:"";s:5:"layer";a:7:{s:7:"enabled";i:0;s:7:"trigger";s:0:"";s:6:"offset";s:0:"";s:10:"background";s:0:"";s:5:"close";a:5:{s:7:"enabled";s:0:"";s:5:"where";s:0:"";s:4:"side";s:0:"";s:15:"timeout_enabled";b:0;s:7:"timeout";i:0;}s:6:"effect";s:4:"show";s:8:"duration";i:0;}s:6:"sticky";a:4:{s:7:"enabled";i:0;s:4:"type";s:0:"";s:9:"assistant";s:0:"";s:8:"position";a:0:{}}s:8:"weekdays";a:2:{s:7:"enabled";b:0;s:11:"day_indexes";a:0:{}}s:8:"tracking";a:10:{s:7:"enabled";s:7:"default";s:16:"impression_limit";i:0;s:11:"click_limit";i:0;s:11:"public-name";s:0:"";s:6:"target";s:7:"default";s:8:"nofollow";s:7:"default";s:12:"report-recip";s:0:"";s:13:"report-period";s:10:"last30days";s:16:"report-frequency";s:5:"never";s:9:"public-id";s:48:"MeT0lWTBAEHCtdoxmQB87TA6iez4cr15jBjRHvGizlQtgUVV";}}');
print_r($tempArray);die;

function setInterval($f, $milliseconds)
{
       $seconds=(int)$milliseconds/1000;
       while(true)
       {
           //$f();
		   echo "hi!\n";
           sleep($seconds);
       }
}
setInterval('', 6000);
die;

/*define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
$args = array('post_type' 			=> 'mapsvg','posts_per_page'         => '-1','orderby' => 'ID', 'order'   => 'ASC',);
$product_query = new WP_Query( $args );
$count = $product_query->found_posts;
$posts = $product_query->posts;
foreach($posts as $post){
	if($post->ID=='4922'){
		$content = json_decode($post->post_content);
		$events = (array) $content->events;
		$events['mouseout.region'] ='function (e, mapsvg){
											  // var region = this;
											  // console.log(region);
											  var id =jQuery(this).attr("id");
												if(jQuery(".sm_state_"+id).hasClass("mapsvg-region-active")){
												   jQuery("svg path#"+id).attr("style","fill: #12A94C;");
												}
											}';
		$events_new  = (object) $events;
		 $content->events = $events_new;
		 //print_r($content);
		 $data = array('ID' => $post->ID,'post_content' => json_encode($content));
		// wp_update_post( $data );
		break;
	}
}
die;*/
/*
$order_status = array('wc-processing','wc-on-hold','wc-completed');
$orders_ids_array = array();
$args = array('post_type' 			=> 'shop_order' ,'post_status'=>$order_status,'posts_per_page'         => '-1','orderby' => 'ID', 'order'   => 'ASC',);
$product_query = new WP_Query( $args );
$count = $product_query->found_posts;
$posts = $product_query->posts;
foreach($posts as $post){
	$order = wc_get_order( $post->ID );
	$order_data = $order->get_data();
	$items = $order->get_items();
	
	global $wpdb;
	$user = wp_get_current_user();
	$user_id = $post->post_author;
	foreach ( $items as $item ) {
		//print_r($order_data);
		//$product_name = $item->get_name();
		$product_id = $item->get_product_id();
		//$product_variation_id = $item->get_variation_id();
		//$item_meta_data = $item->get_meta_data();
		$Auction_id = $item->get_meta('Auction #');
		
		$user_id = $order_data['customer_id'];
		//$user = get_user_by('id', $user_id);
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

		if($product_id=='126' || $product_id == '1642'){
			$type = 'seller';
		}else{
			$type = 'dentist';
		}
		$data = array('user_id' =>$user_id, 'order_id' => $post->ID, 'product_id' => $product_id, 'cost' => $item->get_total(), 'auction_id' =>$Auction_id_store, 'service' =>$service, 'city' => $order_data['billing']['city'], 'state' => $order_data['billing']['state'], 'zip' => $order_data['billing']['postcode'], 'type' => $type, 'date' =>date('Y-m-d',strtotime($post->post_date)), 'source' =>'import');
		$format = array('%d','%d','%d','%f','%d','%s','%s','%s','%s','%s','%s','%s');
		//print_r($data);
		$wpdb->insert($table,$data,$format);
		//$my_id = $wpdb->insert_id;

	}
	//break;
}
*/
die;
/*
define('WP_USE_THEMES', true);
require('/home/shopadoc/public_html/wp-load.php');
/*$args = array(
				'post_type'             => 'advanced_ads',
				'post_status'         => array('publish','pending'),
				'posts_per_page'      => -1,
				//'auction_archive'     => TRUE,
				//'show_past_auctions'  => TRUE,
              );
$query = new WP_Query($args );
$posts = $query->posts;
function change_weight(){
		
		global $wpdb;
		$weights = get_option('advads-ad-weights', array() );
		$weights_array = $weights;
		foreach($weights as $key=>$val){
			ksort($val);
			foreach($val as $post_id=> $weight){
				//$weights_array[$key][$post_id] = 0;
			}
		}
		$weights_array = array();
		//print_r($weights);
		//print_r($weights[101]);
		arsort($weights[101]);
		$keys = array_keys($weights[101]);
		
		//echo $keys[1]; // chocolate
		//echo $weights[101][$keys[1]]; // 20
		$weights_array[101][$keys[0]] = 1;
		unset($weights[101][$keys[0]]);
		//print_r($weights[101]);
		$i = 10;
		$j = 1;
		foreach($weights[101] as $key=>$val){
			$weights_array[101][$keys[$j]] = $i;
			$i--;
			$j++;
			
		}
		print_r($weights_array);
		
		
		
		/*$weights_array2 = array();
		arsort($weights_array[101]);
		$keys = array_keys($weights_array[101]);
		
		//echo $keys[1]; // chocolate
		//echo $weights_array[$keys[1]]; // 20
		$weights_array2[101][$keys[0]] = 1;
		unset($weights_array[101][$keys[0]]);
		//print_r($weights_array);
		$i = 10;
		$j = 1;
		foreach($weights_array[101] as $key=>$val){
			$weights_array2[101][$keys[$j]] = $i;
			$i--;
			$j++;
			
		}
		print_r($weights_array2);
		
		
		$weights_array3 = array();
		arsort($weights_array2[101]);
		$keys = array_keys($weights_array2[101]);
		
		//echo $keys[1]; // chocolate
		//echo $weights_array[$keys[1]]; // 20
		$weights_array3[101][$keys[0]] = 1;
		unset($weights_array2[101][$keys[0]]);
		//print_r($weights_array);
		$i = 10;
		$j = 1;
		foreach($weights_array2[101] as $key=>$val){
			$weights_array3[101][$keys[$j]] = $i;
			$i--;
			$j++;
			
		}
		print_r($weights_array3);
		//update_option('advads-ad-weights', $weights_array );
}
//change_weight();
/*for($i=0; $i <= 10 ; $i++){
	change_weight();
	sleep(6);
}*/
?>