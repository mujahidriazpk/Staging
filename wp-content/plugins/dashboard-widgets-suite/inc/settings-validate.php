<?php // Dashboard Widgets Suite - Validate Settings

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_validate_general($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (isset($input['dashboard_columns'])) $input['dashboard_columns'] = intval($input['dashboard_columns']);
	
	if (!isset($input['widget_control_panel'])) $input['widget_control_panel'] = null;
	$input['widget_control_panel'] = ($input['widget_control_panel'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_control_view'])) $input['widget_control_view'] = null;
	if (!array_key_exists($input['widget_control_view'], $user_roles)) $input['widget_control_view'] = null;
	
	return $input;
	
}

function dashboard_widgets_suite_validate_notes_user($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (!isset($input['widget_notes_user'])) $input['widget_notes_user'] = null;
	$input['widget_notes_user'] = ($input['widget_notes_user'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_user_notes_front'])) $input['widget_user_notes_front'] = null;
	$input['widget_user_notes_front'] = ($input['widget_user_notes_front'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_notes_username'])) $input['widget_notes_username'] = null;
	$input['widget_notes_username'] = ($input['widget_notes_username'] == 1 ? 1 : 0);
	
	if (isset($input['widget_notes_count'])) $input['widget_notes_count'] = intval($input['widget_notes_count']);
	
	if (isset($input['widget_notes_height'])) $input['widget_notes_height'] = intval($input['widget_notes_height']);
	
	if (isset($input['widget_notes_text_size'])) $input['widget_notes_text_size'] = sanitize_text_field($input['widget_notes_text_size']);
	
	if (!isset($input['widget_notes_edit'])) $input['widget_notes_edit'] = null;
	if (!array_key_exists($input['widget_notes_edit'], $user_roles)) $input['widget_notes_edit'] = null;
	
	if (!isset($input['widget_notes_view'])) $input['widget_notes_view'] = null;
	if (!array_key_exists($input['widget_notes_view'], $user_roles)) $input['widget_notes_view'] = null;
	
	if (isset($input['widget_notes_message'])) $input['widget_notes_message'] = sanitize_text_field($input['widget_notes_message']);
	
	return $input;
	
}

function dashboard_widgets_suite_validate_feed_box($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (!isset($input['widget_feed_box'])) $input['widget_feed_box'] = null;
	$input['widget_feed_box'] = ($input['widget_feed_box'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_feed_box_front'])) $input['widget_feed_box_front'] = null;
	$input['widget_feed_box_front'] = ($input['widget_feed_box_front'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_feed_box_excerpt'])) $input['widget_feed_box_excerpt'] = null;
	$input['widget_feed_box_excerpt'] = ($input['widget_feed_box_excerpt'] == 1 ? 1 : 0);
	
	if (isset($input['widget_feed_box_limit'])) $input['widget_feed_box_limit'] = intval($input['widget_feed_box_limit']);
	
	if (isset($input['widget_feed_box_length'])) $input['widget_feed_box_length'] = intval($input['widget_feed_box_length']);
	
	if (isset($input['widget_feed_box_feed'])) $input['widget_feed_box_feed'] = esc_url(trim($input['widget_feed_box_feed']));
	
	if (!isset($input['widget_feed_box_view'])) $input['widget_feed_box_view'] = null;
	if (!array_key_exists($input['widget_feed_box_view'], $user_roles)) $input['widget_feed_box_view'] = null;
	
	return $input;
	
}

function dashboard_widgets_suite_validate_social_box($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (!isset($input['widget_social_box'])) $input['widget_social_box'] = null;
	$input['widget_social_box'] = ($input['widget_social_box'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_social_box_front'])) $input['widget_social_box_front'] = null;
	$input['widget_social_box_front'] = ($input['widget_social_box_front'] == 1 ? 1 : 0);
	
	if (isset($input['widget_social_box_size'])) $input['widget_social_box_size'] = intval($input['widget_social_box_size']);
	
	if (isset($input['widget_social_box_font'])) $input['widget_social_box_font'] = intval($input['widget_social_box_font']);
	
	if (isset($input['widget_social_box_radius'])) $input['widget_social_box_radius'] = intval($input['widget_social_box_radius']);
	
	if (isset($input['widget_social_box_space'])) $input['widget_social_box_space'] = intval($input['widget_social_box_space']);
	
	if (!isset($input['widget_social_box_view'])) $input['widget_social_box_view'] = null;
	if (!array_key_exists($input['widget_social_box_view'], $user_roles)) $input['widget_social_box_view'] = null;
	
	if (isset($input['widget_social_box_twit1'])) $input['widget_social_box_twit1'] = esc_url(trim($input['widget_social_box_twit1']));
	if (isset($input['widget_social_box_face1'])) $input['widget_social_box_face1'] = esc_url(trim($input['widget_social_box_face1']));
	if (isset($input['widget_social_box_pint1'])) $input['widget_social_box_pint1'] = esc_url(trim($input['widget_social_box_pint1']));
	if (isset($input['widget_social_box_lnkd1'])) $input['widget_social_box_lnkd1'] = esc_url(trim($input['widget_social_box_lnkd1']));
	if (isset($input['widget_social_box_feed1'])) $input['widget_social_box_feed1'] = esc_url(trim($input['widget_social_box_feed1']));
	if (isset($input['widget_social_box_skyp1'])) $input['widget_social_box_skyp1'] = esc_url(trim($input['widget_social_box_skyp1']));
	if (isset($input['widget_social_box_yout1'])) $input['widget_social_box_yout1'] = esc_url(trim($input['widget_social_box_yout1']));
	if (isset($input['widget_social_box_vime1'])) $input['widget_social_box_vime1'] = esc_url(trim($input['widget_social_box_vime1']));
	if (isset($input['widget_social_box_inst1'])) $input['widget_social_box_inst1'] = esc_url(trim($input['widget_social_box_inst1']));
	if (isset($input['widget_social_box_word1'])) $input['widget_social_box_word1'] = esc_url(trim($input['widget_social_box_word1']));
	if (isset($input['widget_social_box_tumb1'])) $input['widget_social_box_tumb1'] = esc_url(trim($input['widget_social_box_tumb1']));
	if (isset($input['widget_social_box_four1'])) $input['widget_social_box_four1'] = esc_url(trim($input['widget_social_box_four1']));
	if (isset($input['widget_social_box_eml1']))  $input['widget_social_box_eml1']  = esc_url(trim($input['widget_social_box_eml1']));
	if (isset($input['widget_social_box_link']))  $input['widget_social_box_link']  = esc_url(trim($input['widget_social_box_link']));
	
	return $input;
	
}

function dashboard_widgets_suite_validate_list_box($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	$menu_list = dashboard_widgets_suite_menu_list();
	
	if (!isset($input['widget_list_box'])) $input['widget_list_box'] = null;
	$input['widget_list_box'] = ($input['widget_list_box'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_list_box_view'])) $input['widget_list_box_view'] = null;
	if (!array_key_exists($input['widget_list_box_view'], $user_roles)) $input['widget_list_box_view'] = null;
	
	if (!isset($input['widget_list_box_menu'])) $input['widget_list_box_menu'] = null;
	if (!array_key_exists($input['widget_list_box_menu'], $menu_list)) $input['widget_list_box_menu'] = null;
	
	return $input;
	
}

function dashboard_widgets_suite_validate_widget_box($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	$widget_sidebars = dashboard_widgets_suite_widget_sidebars();
	
	if (!isset($input['widget_widget_box'])) $input['widget_widget_box'] = null;
	$input['widget_widget_box'] = ($input['widget_widget_box'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_widget_box_view'])) $input['widget_widget_box_view'] = null;
	if (!array_key_exists($input['widget_widget_box_view'], $user_roles)) $input['widget_widget_box_view'] = null;
	
	if (!isset($input['widget_widget_box_sidebar'])) $input['widget_widget_box_sidebar'] = null;
	if (!array_key_exists($input['widget_widget_box_sidebar'], $widget_sidebars)) $input['widget_widget_box_sidebar'] = null;
	
	return $input;
	
}

function dashboard_widgets_suite_validate_system_info($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (!isset($input['widget_system_info'])) $input['widget_system_info'] = null;
	$input['widget_system_info'] = ($input['widget_system_info'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_system_info_adv'])) $input['widget_system_info_adv'] = null;
	$input['widget_system_info_adv'] = ($input['widget_system_info_adv'] == 1 ? 1 : 0);
	
	if (!isset($input['widget_system_info_view'])) $input['widget_system_info_view'] = null;
	if (!array_key_exists($input['widget_system_info_view'], $user_roles)) $input['widget_system_info_view'] = null;
	
	return $input;
}

function dashboard_widgets_suite_validate_log_debug($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (!isset($input['widget_log_debug'])) $input['widget_log_debug'] = null;
	$input['widget_log_debug'] = ($input['widget_log_debug'] == 1 ? 1 : 0);
	
	if (isset($input['widget_log_debug_limit'])) $input['widget_log_debug_limit'] = intval($input['widget_log_debug_limit']);
	
	if (isset($input['widget_log_debug_length'])) $input['widget_log_debug_length'] = intval($input['widget_log_debug_length']);
	
	if (isset($input['widget_log_debug_height'])) $input['widget_log_debug_height'] = intval($input['widget_log_debug_height']);
	
	if (!isset($input['widget_log_debug_view'])) $input['widget_log_debug_view'] = null;
	if (!array_key_exists($input['widget_log_debug_view'], $user_roles)) $input['widget_log_debug_view'] = null;
	
	return $input;
	
}

function dashboard_widgets_suite_validate_log_error($input) {
	
	$user_roles = dashboard_widgets_suite_user_roles();
	
	if (!isset($input['widget_log_error'])) $input['widget_log_error'] = null;
	$input['widget_log_error'] = ($input['widget_log_error'] == 1 ? 1 : 0);
	
	if (isset($input['widget_log_error_limit'])) $input['widget_log_error_limit'] = intval($input['widget_log_error_limit']);
	
	if (isset($input['widget_log_error_length'])) $input['widget_log_error_length'] = intval($input['widget_log_error_length']);
	
	if (isset($input['widget_log_error_height'])) $input['widget_log_error_height'] = intval($input['widget_log_error_height']);
	
	if (isset($input['widget_log_error_path'])) $input['widget_log_error_path'] = sanitize_text_field($input['widget_log_error_path']);
	
	if (!isset($input['widget_log_error_view'])) $input['widget_log_error_view'] = null;
	if (!array_key_exists($input['widget_log_error_view'], $user_roles)) $input['widget_log_error_view'] = null;
	
	return $input;
	
}
