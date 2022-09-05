<?php
defined('ABSPATH') or die("No script kiddies please!");
global $wpdb;
$si_id = intval(sanitize_text_field($_GET['si_id']));
$table_name = $table_name = $wpdb->prefix . "aps_social_icons";
$icon_sets = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE si_id=%d",array( $si_id ))); 
if(!empty($icon_sets)){
$icon_set = $icon_sets[0];
foreach($icon_set as $key=>$val){
    $$key = $val;
}
$icon_set_name .=' -copy';
$wpdb->insert(
$table_name,
array(
		'icon_set_name' => $icon_set_name,
        'icon_display'=>$icon_display,
        'num_rows' => $num_rows,
        'icon_margin'=>$icon_margin,
        'icon_tooltip'=>$icon_tooltip,
        'tooltip_background'=>$tooltip_background,
        'tooltip_text_color'=> $tooltip_text_color,
        'opacity_hover'=>$opacity_hover,
        'icon_animation'=>$icon_animation,
        'icon_details'=>$icon_details,
        'icon_extra'=>$icon_extra
	),
	array(
		'%s',
        '%s',
        '%s',
        '%s',
        '%d',
		'%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
	)
 );
 wp_redirect(admin_url().'admin.php?page=aps-social&message=2');
 exit;
 }else{
   die(__('No icons found for this icon id','accesspress-social-icons'));
 }
