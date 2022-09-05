<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$demo_listing;
$to = 'mujahidriazpk@gmail.com';
$subject = 'The subject';
$body = 'The email body content'.date("Y-m-d H:i:s");
$headers = array('Content-Type: text/html; charset=UTF-8','From: ShopAdoc <support@shopadoc.com>');
wp_mail( $to, $subject, $body, $headers );
?>