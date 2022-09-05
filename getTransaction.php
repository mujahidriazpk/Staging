<?php
$url = 'https://api.etherscan.io/api?module=account&action=txlist&address=0x438e6416fe63863c434e4d6ee0c39d8f96880186&startblock=0&endblock=99999999&page=1&offset=1&sort=desc&apikey=2KNWQQTN2U5DG63V34BBX2QQMXM95MXDK4';
$ch = curl_init();
$request_headers = array("Content-Type: application/json; charset=utf-8",);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
$data = curl_exec($ch);
curl_close($ch);
$response = json_decode($data);
if($response->status == 1){
	$date = date("Y-m-d H:i:s",$response->result[0]->timeStamp);
	echo $date;
}
?>