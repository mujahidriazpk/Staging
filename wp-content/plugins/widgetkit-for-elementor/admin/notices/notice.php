<?php 

header('Content-type:application/json;charset=utf-8');
$notice = array(
	'id' => '101',
	'message' => 'If you like this plugin, please leave a review about it at wordpress.org or social media. Your opinion will help others discover our plugin.
	Thank you!',

);
echo json_encode($notice);