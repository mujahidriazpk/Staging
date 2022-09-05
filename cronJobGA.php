<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$demo_listing;
/*$query = "SELECT * FROM `wp_advads_impressions` where count != count_tracked ORDER BY `timestamp` DESC";
$results = $wpdb->get_results($query, OBJECT);
$trackBaseData = "v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced Ads&ea=Impressions&dl=https://woocommerce-642855-2866716.cloudwaysapps.com&dp=https://woocommerce-642855-2866716.cloudwaysapps.com";
$request_str = '';
foreach($results as $row){
	//$post_title  = _wp_specialchars( get_post_field( 'post_title', $row->ad_id), ENT_QUOTES, 'UTF-8', true);'ev' => 0,
	$remain_count = $row->count - $row->count_tracked;
	if($remain_count>0){
		$ad_ga = '['.$row->ad_id.'] '.str_replace("&nbsp;"," ",htmlspecialchars_decode(get_the_title($row->ad_id)));
		//$request_str .= $trackBaseData."&el=".$ad_ga."&ev=".$remain_count."\n";
		for($i=0; $i<$remain_count; $i++){
			$request_str .= $trackBaseData."&el=".$ad_ga."\n";
		}
		$wpdb->query($wpdb->prepare("UPDATE wp_advads_impressions SET count_tracked ='".$row->count."' WHERE ad_id='".$row->ad_id."' and timestamp= '".$row->timestamp."'"));	
	}
}*/

//echo  $request_str;
$request_str = 'v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced Ads&ea=Impressions&dl=https://woocommerce-642855-2866716.cloudwaysapps.com&dp=https://woocommerce-642855-2866716.cloudwaysapps.com&el=[6123] DTest Client D10 03/01/22 – 12/31/22';

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.google-analytics.com/collect",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 1000,
  CURLOPT_TIMEOUT => 30000,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $request_str,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
 echo $response;
 var_dump(json_decode($response, true));
/*if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}*/

?>