<?php /*echo strtotime('2022-01-19 02:44:45');die;*/
$start_time = microtime(TRUE);
?>
<?php
$time = strtotime(date("Y-m-d H:i:s"));
$end_time = microtime(TRUE);
$time_taken =($end_time - $start_time)*1000;
$time_taken = round($time_taken,5);
echo $time + $time_taken;
//echo 'Page generated in '.$time_taken.' seconds.';