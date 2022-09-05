<?php $start_time = microtime(TRUE);
$client_address = preg_replace('/\s/', ' ',"this is my             address");
$client_address = preg_replace('/[\s$@_*]+/', ' ', $client_address);
echo $client_address;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type='text/javascript' src='https://woocommerce-642855-2098160.cloudwaysapps.com/wp-includes/js/jquery/jquery.min.js?ver=3.6.0' id='jquery-core-js'></script>
<script type='text/javascript' src='https://woocommerce-642855-2098160.cloudwaysapps.com/wp-content/plugins/woocommerce-simple-auctions/js/jquery.countdown.min.js?ver=1.2.27' id='simple-auction-countdown-js'></script>
<script type='text/javascript' id='simple-auction-countdown-language-js-extra'>
/* <![CDATA[ */
var countdown_language_data = {"labels":{"Years":"Years","Months":"Months","Weeks":"Weeks","Days":"Days","Hours":"Hours","Minutes":"Minutes","Seconds":"Seconds"},"labels1":{"Year":"Year","Month":"Month","Week":"Week","Day":"Day","Hour":"Hour","Minute":"Minute","Second":"Second"},"compactLabels":{"y":"y","m":"m","w":"w","d":"d"}};
/* ]]> */
</script>
<script type='text/javascript' src='https://woocommerce-642855-2098160.cloudwaysapps.com/wp-content/plugins/woocommerce-simple-auctions/js/jquery.countdown.language.js?ver=1.2.27' id='simple-auction-countdown-language-js'></script>
<script type='text/javascript' id='simple-auction-frontend-js-extra'>
/* <![CDATA[ */
var data = {"finished":"Auction has finished!","gtm_offset":"-8","started":"Auction has started! Please refresh your page.","no_need":"No need to bid. Your bid is winning!","compact_counter":"no","outbid_message":"","interval":"1"};
var SA_Ajax = {"ajaxurl":"\/auction-5417\/removal-1-tooth-bone-graft-8\/?wsa-ajax","najax":"1","last_activity":"1636519428","focus":"yes"};
/* ]]> */
</script>
<link rel='stylesheet'  href='https://woocommerce-642855-2098160.cloudwaysapps.com/wp-content/plugins/woocommerce-simple-auctions/css/frontend.css?ver=5.8.1' type='text/css' media='all' />
<link rel='stylesheet' id='child-style-css'  href='https://woocommerce-642855-2098160.cloudwaysapps.com/wp-content/themes/dokan-child/style.css?ver=1636456456' type='text/css' media='all' />
<title>Countdown</title>
</head>

<body>
<?php
/*$startdate= date("Y-m-d H:i:s"); 
$enddate='2022-12-31 12:00:00'; 
$diff=strtotime($enddate)-strtotime($startdate); 
//echo "diff in seconds: $diff<br/>\n<br/>\n"; 
$end_time = microtime(TRUE);
$time_taken =($end_time - $start_time) * 1000;
$time_taken = round($time_taken,5);
$diff = $diff + $time_taken;*/
?>
<script type="text/javascript">
//const str = new Date().toLocaleString('en-US', { timeZone: 'America/Los_Angeles' });
//console.log(str);
</script>
<div class="auction-time future" id="countdown">
  <div class="auction-time-countdown future" data-time="<?php echo $diff;?>" data-format="yowdHMS"></div>
</div>
<script >
//time.is/UTC
//var countDownDate = new Date("Jan 21, 2022 15:37:25").getTime();
var countDownDate = Date.parse("march 30, 2022 15:37:25")/1000;
const str = new Date().toLocaleString('en-US', { timeZone: 'America/Los_Angeles' ,hour12: false });
var now = Date.parse(str)/1000;
//var now = new Date(str).getTime();
  // Find the distance between now and the count down date
  var time = countDownDate - now;
  console.log(time);
/*jQuery('.auction-time-countdown').SAcountdown({
			//until:   $.SAcountdown.UTCDate(-(new Date().getTimezoneOffset()),new Date(time*1000)),
			until:   time,
			format: 'yowdHMS',
			compact:  false,
			onTick: null,
			onExpiry: null,
			//expiryText: etext
		});*/
</script>
</body>
</html>
