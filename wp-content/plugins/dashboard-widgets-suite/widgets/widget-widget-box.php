<?php // Dashboard Widgets Suite - Widget Box

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_widget_box() {
	
	global $dws_options_widget_box;
	
	$widget_sidebar = isset($dws_options_widget_box['widget_widget_box_sidebar']) ? $dws_options_widget_box['widget_widget_box_sidebar'] : 'dws-widget-box';
	
	if (is_active_sidebar($widget_sidebar)) {
		
		dynamic_sidebar($widget_sidebar);
		
	} else {
		
		$args = array(
			'before_title'  => '<p class="widgettitle"><strong>', 
			'after_title'   => '</strong></p>',  
		);
		
		$instance = array(
			'title'  => esc_html__('Example Widget', 'dashboard-widgets-suite'), 
			'text'   => esc_html__('To add, remove, and customize your Dashboard Widgets, visit Appearance &rarr; Widgets &rarr; ', 'dashboard-widgets-suite') . dashboard_widgets_suite_sidebar_name($widget_sidebar), 
		);
		
		/*
			
			.widget .widgettitle {}
			.widget div[class*=widget] {}
			.widget ul {}
			
		*/
		
		the_widget('WP_Widget_Text', $instance, $args);
		
	}
	
	do_action('dashboard_widgets_suite_widget_box');
	
}

function dashboard_widgets_suite_sidebar_name($widget_sidebar) {
	
	$sidebar_name = 'Dashboard Widgets Suite';
	
	foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) { 
		
		$id   = isset($sidebar['id'])   ? $sidebar['id']   : '';
		$name = isset($sidebar['name']) ? $sidebar['name'] : '';
		
		if ($id === $widget_sidebar) {
			
			$sidebar_name = $name;
			break;
			
		}
		
	}
	
	return $sidebar_name;
}
