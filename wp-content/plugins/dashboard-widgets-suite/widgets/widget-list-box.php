<?php // Dashboard Widgets Suite - List Box

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_list_box() {
	
	global $dws_options_list_box;
	
	$menu = isset($dws_options_list_box['widget_list_box_menu']) ? $dws_options_list_box['widget_list_box_menu'] : false;
	
	if (!$menu) {
		
		$menu_object = wp_get_nav_menu_object('Dashboard Widgets Suite');
		
		$menu = (isset($menu_object->term_id) && is_int($menu_object->term_id)) ? $menu_object->term_id : 1;
		
	}
	
	$menu = apply_filters('dashboard_widgets_suite_list_box_menu_name', $menu);
	
	$menu_args = array(
		'menu'       => (int) $menu, 
		'menu_class' => 'dws-list-box-menu', 
	);
	
	$dws_menu = wp_nav_menu($menu_args);
	
	do_action('dashboard_widgets_suite_list_box', $dws_menu);
	
}
