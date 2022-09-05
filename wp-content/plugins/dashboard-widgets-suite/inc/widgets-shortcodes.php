<?php // Dashboard Widgets Suite - Widget Shortcodes

if (!defined('ABSPATH')) exit;

/*	
	Frontend User Notes Shortcode
		
		Todo:
			
			Add shortcode attributes for options
			Add template for custom markup
	
*/
function dashboard_widgets_suite_user_notes_frontend() {
	
	global $dws_options_notes_user;
	
	$user_notes = '';
	
	if (isset($dws_options_notes_user['widget_user_notes_front']) && $dws_options_notes_user['widget_user_notes_front']) {
		
		require_once DWS_DIR .'widgets/widget-notes-user.php';
		
		$user_notes .= dashboard_widgets_suite_notes_user_content();
		
	}
	
	$user_notes = apply_filters('dashboard_widgets_suite_notes_user_frontend_data', $user_notes);
	
	do_action('dashboard_widgets_suite_notes_user_frontend', $user_notes);
	
	return $user_notes;
	
}

/*
	
	Frontend Feed Box Shortcode
		
		Todo:
			
			Add shortcode attributes for various options
	
*/
function dashboard_widgets_suite_feed_box_frontend() {
	
	global $dws_options_feed_box;
	
	$feed_box = '';
	
	if (isset($dws_options_feed_box['widget_feed_box_front']) && $dws_options_feed_box['widget_feed_box_front']) {
		
		$display_feed_box = isset($dws_options_feed_box['widget_feed_box_view']) ? $dws_options_feed_box['widget_feed_box_view'] : null;
		
		if (dashboard_widgets_suite_check_role($display_feed_box)) {
			
			require_once DWS_DIR .'widgets/widget-feed-box.php';
			
			$feed_box = dashboard_widgets_suite_feed_box_content();
			
		}
		
	}
	
	$feed_box = apply_filters('dashboard_widgets_suite_feed_box_frontend_data', $feed_box);
	
	do_action('dashboard_widgets_suite_feed_box_frontend', $feed_box);
	
	return $feed_box;
		
}

/*
	
	Frontend Social Box Shortcode
		
		Todo:
			
			Add shortcode attributes for various options
	
*/
function dashboard_widgets_suite_social_box_frontend() {
	
	global $dws_options_social_box;
	
	$social_box = '';
	
	if (isset($dws_options_social_box['widget_social_box_front']) && $dws_options_social_box['widget_social_box_front']) {
		
		$display_social_box = isset($dws_options_social_box['widget_social_box_view']) ? $dws_options_social_box['widget_social_box_view'] : null;
		
		if (dashboard_widgets_suite_check_role($display_social_box)) {
			
			require_once DWS_DIR .'widgets/widget-social-box.php';
			
			$social_box = dashboard_widgets_suite_social_box_content();
			
		}
		
	}
	
	$social_box = apply_filters('dashboard_widgets_suite_social_box_frontend_data', $social_box);
	
	do_action('dashboard_widgets_suite_social_box_frontend', $social_box);
	
	return $social_box;
	
}
