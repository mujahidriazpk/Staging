<?php defined('ABSPATH') or die("No script kiddies please!");?>
<?php
$si_id = intval($atts['id']);
global $wpdb;
$table_name = $table_name = $wpdb->prefix . "aps_social_icons";
$icon_sets = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE si_id=%d",array( $si_id ))); 
if (!empty($icon_sets)) {
    $icon_set = $icon_sets[0];
    $icon_extra = unserialize($icon_set->icon_extra);
    $icon_details = unserialize($icon_set->icon_details);
    $icon_position_class = ($icon_set->icon_display == 'horizontal') ? 'aps-group-horizontal' : 'aps-group-vertical';
    $icon_opacity = (isset($icon_set->opacity_hover) && $icon_set->opacity_hover != '')?esc_attr($icon_set->opacity_hover):'';
    include('icon-group-list.php');
}//if close
