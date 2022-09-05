<?php $start_time = microtime(TRUE);?>
<html>
<head>
<title>Test Count</title>
</head>
<body>
<p id="demo"></p>
<?php
date_default_timezone_set('America/Los_Angeles');
$today_date_time = date('Y-m-d H:i:s');
$thursday = "2022-01-30 13:00:00";
//echo $today_date_time;
$date1 = strtotime($today_date_time);
$date2 = strtotime($thursday);
 
 // Formulate the Difference between two dates
$diff = abs($date2 - $date1);

$end_time = microtime(TRUE);
$time_taken =($end_time - $start_time)*1000;
$time_taken = round($time_taken,3);
//$diff = $diff + $time_taken;
?>
<script>
const str = new Date().toLocaleString('en-US', { timeZone: 'America/Los_Angeles' });
var distance = parseInt('<?php echo $diff;?>');
// Set the date we're counting down to

// Update the count down every 1 second
var x = setInterval(function() {
  // If the count down is over, write some text 
  if (distance <= 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "EXPIRED";
  }else{
	 
	var day = 86400;
	var hour = 3600;
	var minute = 60;
	
	
	var daysout = Math.floor(distance / day);
	var hoursout = Math.floor((distance - daysout * day)/hour);
	var minutesout = Math.floor((distance - daysout * day - hoursout * hour)/minute);
	var secondsout = distance - daysout * day - hoursout * hour - minutesout * minute;
	  document.getElementById("demo").innerHTML = daysout + "d " + hoursout + "h "
	  + minutesout + "m " + secondsout + "s ";
	  distance--;
 }
}, 1000);
</script>
<?php echo $diff;?>
</body>
</html>
