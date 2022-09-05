<?php // Dashboard Widgets Suite - Enqueue Resources

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_enqueue_resources_frontend() {
	
	// Feed Box
	
	if (dashboard_widgets_suite_display_feed_box_frontend()) {
		
		wp_enqueue_style('dws-feed-box', DWS_URL .'css/styles-feed-box.css', array(), DWS_VERSION);
		
	}
	
	// Social Box
	
	if (dashboard_widgets_suite_display_social_box_frontend()) {
		
		wp_enqueue_style('dws-social-box', DWS_URL .'css/styles-social-box.css', array(), DWS_VERSION);
		
		wp_enqueue_script('dws-social-box', DWS_URL .'js/scripts-social-box.js', array('jquery'), DWS_VERSION);
		
		list($social_box_size, $social_box_font, $social_box_radius, $social_box_space, $data) = dashboard_widgets_suite_get_social_box_vars();
		
		wp_localize_script('dws-social-box', 'dws_social_box', $data);
		
	}
	
	// User Notes
	
	if (dashboard_widgets_suite_display_user_notes_frontend()) {
		
		wp_enqueue_style('dws-user-notes', DWS_URL .'css/styles-user-notes.css', array(), DWS_VERSION);
		
		wp_enqueue_script('dws-user-notes', DWS_URL .'js/scripts-user-notes.js', array('jquery'), DWS_VERSION);
		
		$data = dashboard_widgets_suite_get_user_notes_vars();
		
		wp_localize_script('dws-user-notes', 'dws_user_notes', $data);
		
	}
	
}

function dashboard_widgets_suite_enqueue_resources_admin() {
	
	$screen = get_current_screen();
	
	if (!property_exists($screen, 'id')) return;
	
	if ($screen->id === 'settings_page_dashboard_widgets_suite') {
		
		wp_enqueue_style('dws-font-icons', DWS_URL .'css/styles-font-icons.css', array(), DWS_VERSION);
		
		wp_enqueue_style('dws-settings', DWS_URL .'css/styles-settings.css', array(), DWS_VERSION);
		
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		$js_deps = array('jquery', 'jquery-ui-core', 'jquery-ui-dialog');
		
		wp_enqueue_script('dws-settings', DWS_URL .'js/scripts-settings.js', $js_deps, DWS_VERSION);
		
		$data = dashboard_widgets_suite_get_settings_vars();
		
		wp_localize_script('dws-settings', 'dws_settings', $data);
		
	} elseif ($screen->id === 'dashboard') {
		
		wp_enqueue_style('dws-dashboard', DWS_URL .'css/styles-dashboard.css', array(), DWS_VERSION);
		
		wp_enqueue_style('dws-font-icons', DWS_URL .'css/styles-font-icons.css', array(), DWS_VERSION);
		
		
		// Control Panel
		
		if (dashboard_widgets_suite_display_control_panel()) {
			
			wp_enqueue_script('dws-control-panel', DWS_URL .'js/scripts-control-panel.js', array('jquery'), DWS_VERSION);
			
		}
		
		// Debug & Error Logs
		
		if (dashboard_widgets_suite_display_log_widgets()) {
			
			wp_enqueue_style('dws-log-widgets', DWS_URL .'css/styles-log-widgets.css', array(), DWS_VERSION);
			
		}
		
		// Feed Box
		
		if (dashboard_widgets_suite_display_feed_box()) {
			
			wp_enqueue_style('dws-feed-box', DWS_URL .'css/styles-feed-box.css', array(), DWS_VERSION);
			
		}
		
		// Social Box
		
		if (dashboard_widgets_suite_display_social_box()) {
			
			wp_enqueue_style('dws-social-box', DWS_URL .'css/styles-social-box.css', array(), DWS_VERSION);
			
			wp_enqueue_script('dws-social-box', DWS_URL .'js/scripts-social-box.js', array('jquery'), DWS_VERSION);
			
			list($social_box_size, $social_box_font, $social_box_radius, $social_box_space, $data) = dashboard_widgets_suite_get_social_box_vars();
			
			wp_localize_script('dws-social-box', 'dws_social_box', $data);
			
		}
		
		// System Info
		
		if (dashboard_widgets_suite_display_system_info()) {
			
			wp_enqueue_style('dws-system-info', DWS_URL .'css/styles-system-info.css', array(), DWS_VERSION);
			
			wp_enqueue_script('dws-system-info', DWS_URL .'js/scripts-system-info.js', array('jquery'), DWS_VERSION);
			
		}
		
		// User Notes
		
		if (dashboard_widgets_suite_display_user_notes()) {
			
			wp_enqueue_style('dws-user-notes', DWS_URL .'css/styles-user-notes.css', array(), DWS_VERSION);
			
			wp_enqueue_script('dws-user-notes', DWS_URL .'js/scripts-user-notes.js', array('jquery'), DWS_VERSION);
			
			wp_add_inline_style('dws-user-notes', dashboard_widgets_suite_note_styles());
			
			$data = dashboard_widgets_suite_get_user_notes_vars();
			
			wp_localize_script('dws-user-notes', 'dws_user_notes', $data);
			
		}
		
	}
	
}

function dashboard_widgets_suite_get_settings_vars() {
	
	$data = array(
		'reset_title'    => __('Confirm Reset',            'dashboard-widgets-suite'),
		'reset_message'  => __('Restore default options?', 'dashboard-widgets-suite'),
		'reset_true'     => __('Yes, make it so.',         'dashboard-widgets-suite'),
		'reset_false'    => __('No, abort mission.',       'dashboard-widgets-suite'),
		
		'delete_title'   => __('Confirm Delete',           'dashboard-widgets-suite'),
		'delete_message' => __('Delete all User Notes?',   'dashboard-widgets-suite'),
		'delete_true'    => __('Yes, make it so.',         'dashboard-widgets-suite'),
		'delete_false'   => __('No, abort mission.',       'dashboard-widgets-suite'),
	);
	
	return $data;
	
}

function dashboard_widgets_suite_get_social_box_vars() {
	
	global $dws_options_social_box;
	
	$social_box_size   = isset($dws_options_social_box['widget_social_box_size'])   ? $dws_options_social_box['widget_social_box_size']   : 50;
	$social_box_font   = isset($dws_options_social_box['widget_social_box_font'])   ? $dws_options_social_box['widget_social_box_font']   : 24;
	$social_box_radius = isset($dws_options_social_box['widget_social_box_radius']) ? $dws_options_social_box['widget_social_box_radius'] : 0;
	$social_box_space  = isset($dws_options_social_box['widget_social_box_space'])  ? $dws_options_social_box['widget_social_box_space']  : 10;
	
	$data = array(
		'size'   => $social_box_size   . 'px',
		'font'   => $social_box_font   . 'px',
		'radius' => $social_box_radius . 'px',
		'space'  => $social_box_space  . 'px',
	);
	
	return array($social_box_size, $social_box_font, $social_box_radius, $social_box_space, $data);
	
}

function dashboard_widgets_suite_get_user_notes_vars() {
	
	$data = array(
		'open'    => __('Cancel',            'dashboard-widgets-suite'),
		'close'   => __('Add Note',          'dashboard-widgets-suite'),
		'confirm' => __('Delete this note?', 'dashboard-widgets-suite'),
	);
	
	return $data;
	
}

function dashboard_widgets_suite_display_control_panel() {
	
	global $dws_options_general;
	
	$display_control_panel = false;
	
	if (isset($dws_options_general['widget_control_panel']) && $dws_options_general['widget_control_panel']) {
		
		$control_panel = isset($dws_options_general['widget_control_view']) ? $dws_options_general['widget_control_view'] : null;
		
		$display_control_panel = dashboard_widgets_suite_check_role($control_panel);
		
	}
	
	return ($display_control_panel) ? true : false;
	
}

function dashboard_widgets_suite_display_log_widgets() {
	
	global $dws_options_log_debug, $dws_options_log_error;
	
	$display_log_debug = false;
	$display_log_error = false;
	
	if (isset($dws_options_log_debug['widget_log_debug']) && $dws_options_log_debug['widget_log_debug']) {
		
		$log_debug = isset($dws_options_log_debug['widget_log_debug_view']) ? $dws_options_log_debug['widget_log_debug_view'] : null;
		
		$display_log_debug = dashboard_widgets_suite_check_role($log_debug);
		
	}
	
	if (isset($dws_options_log_error['widget_log_error']) && $dws_options_log_error['widget_log_error']) {
		
		$log_error = isset($dws_options_log_error['widget_log_error_view']) ? $dws_options_log_error['widget_log_error_view'] : null;
		
		$display_log_error = dashboard_widgets_suite_check_role($log_error);
		
	}
	
	return ($display_log_debug || $display_log_error) ? true : false;
	
}

function dashboard_widgets_suite_display_feed_box() {
	
	global $dws_options_feed_box;
	
	$display_feed_box = false;
	
	if (isset($dws_options_feed_box['widget_feed_box']) && $dws_options_feed_box['widget_feed_box']) {
		
		$feed_box = isset($dws_options_feed_box['widget_feed_box_view']) ? $dws_options_feed_box['widget_feed_box_view'] : null;
		
		$display_feed_box = dashboard_widgets_suite_check_role($feed_box);
		
	}
	
	return ($display_feed_box) ? true : false;
	
}

function dashboard_widgets_suite_display_feed_box_frontend() {
	
	global $dws_options_feed_box;
	
	$display_feed_box = false;
	
	if (isset($dws_options_feed_box['widget_feed_box_front']) && $dws_options_feed_box['widget_feed_box_front']) {
		
		$feed_box = isset($dws_options_feed_box['widget_feed_box_view']) ? $dws_options_feed_box['widget_feed_box_view'] : null;
		
		$display_feed_box = dashboard_widgets_suite_check_role($feed_box);
		
	}
	
	return ($display_feed_box) ? true : false;
	
}

function dashboard_widgets_suite_display_social_box() {
	
	global $dws_options_social_box;
	
	$display_social_box = false;
	
	if (isset($dws_options_social_box['widget_social_box']) && $dws_options_social_box['widget_social_box']) {
		
		$social_box = isset($dws_options_social_box['widget_social_box_view']) ? $dws_options_social_box['widget_social_box_view'] : null;
		
		$display_social_box = dashboard_widgets_suite_check_role($social_box);
		
	}
	
	return ($display_social_box) ? true : false;
	
}

function dashboard_widgets_suite_display_social_box_frontend() {
	
	global $dws_options_social_box;
	
	$display_social_box = false;
	
	if (isset($dws_options_social_box['widget_social_box_front']) && $dws_options_social_box['widget_social_box_front']) {
		
		$social_box = isset($dws_options_social_box['widget_social_box_view']) ? $dws_options_social_box['widget_social_box_view'] : null;
		
		$display_social_box = dashboard_widgets_suite_check_role($social_box);
		
	}
	
	return ($display_social_box) ? true : false;
	
}

function dashboard_widgets_suite_display_system_info() {
	
	global $dws_options_system_info;
	
	$display_system_info = false;
	
	if (isset($dws_options_system_info['widget_system_info']) && $dws_options_system_info['widget_system_info']) {
		
		$display_system_info = true;
		
	}
	
	return ($display_system_info) ? true : false;
	
}

function dashboard_widgets_suite_display_user_notes() {
	
	global $dws_options_notes_user;
	
	$display_notes_user = false;
	
	if (isset($dws_options_notes_user['widget_notes_user']) && $dws_options_notes_user['widget_notes_user']) {
		
		$display_notes_user = true;
		
	}
	
	return ($display_notes_user) ? true : false;
	
}

function dashboard_widgets_suite_display_user_notes_frontend() {
	
	global $dws_options_notes_user;
	
	$display_notes_user = false;
	
	if (isset($dws_options_notes_user['widget_user_notes_front']) && $dws_options_notes_user['widget_user_notes_front']) {
		
		$display_notes_user = true;
		
	}
	
	return ($display_notes_user) ? true : false;
	
}
