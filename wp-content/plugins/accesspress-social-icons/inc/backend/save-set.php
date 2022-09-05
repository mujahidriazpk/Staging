<?php defined('ABSPATH') or die("No script kiddies please!");
foreach($_POST as $key=>$val)
{
    if($key=='icons')
    {
        $$key = $val;
    }
    else
    {
        $$key = sanitize_text_field($val);

    }
}
foreach($icons as $key=>$val)
{
    $icon_detail_array = array();
    foreach($val as $k=>$v)
    {
        if($k=='image' || $k=='link')
        {
            $icon_detail_array[$k] = esc_url_raw($v);
        }
        else
        {
            $icon_detail_array[$k] = sanitize_text_field($v);
        }
    }
$icons[$key] = $icon_detail_array;
}
$icon_extra = array('icon_set_type'=>$icon_set_type,
                    'icon_theme_id'=>$icon_theme_id,
                    'num_columns'=>$num_columns,
                    'tooltip_position'=>$tooltip_position);
$icon_extra = serialize($icon_extra);
global $wpdb;
$icons = serialize($icons);
$table_name = $wpdb->prefix . "aps_social_icons";
if(isset($si_id))
{
    $wpdb->update(
	$table_name,
	array(
		'icon_set_name' => $set_name,
        'icon_display'=>$display,
        'num_rows' => $num_rows,
        'icon_margin'=>$margins,
        'icon_tooltip'=>$tooltip,
        'tooltip_background'=>$tooltip_bg,
        'tooltip_text_color'=> $tooltip_text_color,
        'opacity_hover'=>$opacity_hover,
        'icon_animation'=>$icon_animation,
        'icon_details'=>$icons,
        'icon_extra'=>$icon_extra
	),
    array('si_id'=>intval($si_id)),
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
	),
     array('%d')
);
}
else
{
    $wpdb->insert(
	$table_name,
array(
		'icon_set_name' => $set_name,
        'icon_display'=>$display,
        'num_rows' => $num_rows,
        'icon_margin'=>$margins,
        'icon_tooltip'=>$tooltip,
        'tooltip_background'=>$tooltip_bg,
        'tooltip_text_color'=> $tooltip_text_color,
        'opacity_hover'=>$opacity_hover,
        'icon_animation'=>$icon_animation,
        'icon_details'=>$icons,
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
}
if(isset($_POST['current_page']))
{
    wp_redirect(sanitize_text_field($_POST['current_page']).'&message=1');
}
else
{
  wp_redirect(admin_url().'admin.php?page=aps-social&message=1');
}
exit;
