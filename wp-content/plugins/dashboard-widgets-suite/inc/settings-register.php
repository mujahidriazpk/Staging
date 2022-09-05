<?php // Dashboard Widgets Suite - Register Settings

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_register_settings() {
	
	
	// register_setting( $option_group, $option_name, $sanitize_callback )
	register_setting('dws_options_general', 'dws_options_general', 'dashboard_widgets_suite_validate_general');
	
	// add_settings_section( $id, $title, $callback, $page )
	add_settings_section('settings_general', __('General Settings', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_general', 'dws_options_general');
	
	// add_settings_field( $id, $title, $callback, $page, $section, $args )
	add_settings_field('dashboard_columns',    __('Dashboard Columns', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_general', 'settings_general', array('id' => 'dashboard_columns',    'section' => 'general', 'label' => esc_html__('Number of columns for the Dashboard (use 0 for WP defaults)', 'dashboard-widgets-suite')));
	add_settings_field('widget_control_panel', __('Control Panel',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_general', 'settings_general', array('id' => 'widget_control_panel', 'section' => 'general', 'label' => esc_html__('Enable the Control Panel Widget (lets you enable/disable widgets directly from the Dashboard)', 'dashboard-widgets-suite')));
	add_settings_field('widget_control_view',  __('View Role',         'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_general', 'settings_general', array('id' => 'widget_control_view',  'section' => 'general', 'label' => esc_html__('Minimum user role required to view the Control Panel', 'dashboard-widgets-suite')));
	add_settings_field('null_reset_options',   __('Reset Options',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_reset',    'dws_options_general', 'settings_general', array('id' => 'null_reset_options',   'section' => 'general', 'label' => esc_html__('Restore default options', 'dashboard-widgets-suite')));
	add_settings_field('null_rate_plugin',     __('Support Plugin',    'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_rate',     'dws_options_general', 'settings_general', array('id' => 'null_rate_plugin',     'section' => 'general', 'label' => esc_html__('Show support with a 5-star rating &raquo;', 'dashboard-widgets-suite')));
	
	// user notes
	register_setting('dws_options_notes_user', 'dws_options_notes_user', 'dashboard_widgets_suite_validate_notes_user');
	
	add_settings_section('settings_notes_user', __('User Notes', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_notes_user', 'dws_options_notes_user');
	
	add_settings_field('widget_notes_user',       __('Enable Widget',    'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_user',       'section' => 'notes_user', 'label' => esc_html__('Enable the User Notes Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_user_notes_front', __('Enable Frontend',  'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_user_notes_front', 'section' => 'notes_user', 'label' => esc_html__('Enable the User Notes on the frontend (via shortcode)', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_username',   __('Enable Username',  'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_username',   'section' => 'notes_user', 'label' => esc_html__('Automatically use the logged-in username for notes', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_count',      __('Number of Notes',  'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_count',      'section' => 'notes_user', 'label' => esc_html__('Number of notes to display', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_height',     __('Note Height',      'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_height',     'section' => 'notes_user', 'label' => esc_html__('Height of each note in pixels (use 0 for auto-height)', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_text_size',  __('Text Size',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_text_size',  'section' => 'notes_user', 'label' => esc_html__('Font size for note text (you must include the "px" or other unit (default: 12px)', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_edit',       __('Edit Role',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_edit',       'section' => 'notes_user', 'label' => esc_html__('Minimum user role required to add/edit/delete notes', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_view',       __('View Role',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_view',       'section' => 'notes_user', 'label' => esc_html__('Default minimum role to view notes (can be overridden when adding new notes)', 'dashboard-widgets-suite')));
	add_settings_field('widget_notes_message',    __('Custom Message',   'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_notes_user', 'settings_notes_user', array('id' => 'widget_notes_message',    'section' => 'notes_user', 'label' => esc_html__('Custom message when there are no notes', 'dashboard-widgets-suite')));
	add_settings_field('null_delete_notes',       __('Delete Notes',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_delete',   'dws_options_notes_user', 'settings_notes_user', array('id' => 'null_delete_notes',       'section' => 'notes_user', 'label' => esc_html__('Delete all User Notes', 'dashboard-widgets-suite')));
	
	// feed box
	register_setting('dws_options_feed_box', 'dws_options_feed_box', 'dashboard_widgets_suite_validate_feed_box');
	
	add_settings_section('settings_feed_box', __('Feed Box', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_feed_box', 'dws_options_feed_box');
	
	add_settings_field('widget_feed_box',         __('Enable Widget',   'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box',         'section' => 'feed_box', 'label' => esc_html__('Enable the Feed Box Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_feed_box_front',   __('Enable Frontend', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box_front',   'section' => 'feed_box', 'label' => esc_html__('Enable the Feed Box Widget on the frontend (via shortcode)', 'dashboard-widgets-suite')));
	add_settings_field('widget_feed_box_excerpt', __('Feed Excerpt',    'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box_excerpt', 'section' => 'feed_box', 'label' => esc_html__('Include excerpts for each feed item', 'dashboard-widgets-suite')));
	add_settings_field('widget_feed_box_length',  __('Item Length',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box_length',  'section' => 'feed_box', 'label' => esc_html__('Number of characters for each excerpt', 'dashboard-widgets-suite')));
	add_settings_field('widget_feed_box_limit',   __('Number of Items', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box_limit',   'section' => 'feed_box', 'label' => esc_html__('Number of feed items to display', 'dashboard-widgets-suite')));
	add_settings_field('widget_feed_box_view',    __('View Role',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box_view',    'section' => 'feed_box', 'label' => esc_html__('Minimum user role required to view the Feed Box', 'dashboard-widgets-suite')));
	add_settings_field('widget_feed_box_feed',    __('Feed URL',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_feed_box', 'settings_feed_box', array('id' => 'widget_feed_box_feed',    'section' => 'feed_box', 'label' => esc_html__('URL of the feed that you want to display', 'dashboard-widgets-suite')));
	
	
	// social box
	register_setting('dws_options_social_box', 'dws_options_social_box', 'dashboard_widgets_suite_validate_social_box');
	
	add_settings_section('settings_social_box', __('Social Box', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_social_box', 'dws_options_social_box');
	
	add_settings_field('widget_social_box',        __('Enable Widget',   'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box',        'section' => 'social_box', 'label' => esc_html__('Enable the Social Box Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_front',  __('Enable Frontend', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_front',  'section' => 'social_box', 'label' => esc_html__('Enable the Social Box on the frontend (via shortcode)', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_size',   __('Icon Size',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_size',   'section' => 'social_box', 'label' => esc_html__('Size of icon box (in pixels)', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_font',   __('Font Size',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_font',   'section' => 'social_box', 'label' => esc_html__('Size of icon (in pixels)', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_radius', __('Icon Radius',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_radius', 'section' => 'social_box', 'label' => esc_html__('Radius of icons (in pixels)', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_space',  __('Icon Spacing',    'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_space',  'section' => 'social_box', 'label' => esc_html__('Space between icons (in pixels)', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_view',   __('View Role',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_view',   'section' => 'social_box', 'label' => esc_html__('Minimum user role required to view the Social Box', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_twit1',  __('Twitter',         'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_twit1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_face1',  __('Facebook',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_face1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_pint1',  __('Pinterest',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_pint1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_lnkd1',  __('LinkedIn',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_lnkd1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_feed1',  __('RSS Feed',        'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_feed1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_skyp1',  __('Skype',           'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_skyp1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_yout1',  __('YouTube',         'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_yout1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_vime1',  __('Vimeo',           'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_vime1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_inst1',  __('Instagram',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_inst1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_word1',  __('WordPress',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_word1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_tumb1',  __('Tumblr',          'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_tumb1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_four1',  __('Foursquare',      'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_four1',  'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_eml1',   __('Email',           'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_eml1',   'section' => 'social_box', 'label' => esc_html__('For example: mailto:email@example.com or https://example.com/contact/', 'dashboard-widgets-suite')));
	add_settings_field('widget_social_box_link',   __('Link',            'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_social_box', 'settings_social_box', array('id' => 'widget_social_box_link',   'section' => 'social_box', 'label' => esc_html__('', 'dashboard-widgets-suite')));
	
	
	// list box
	register_setting('dws_options_list_box', 'dws_options_list_box', 'dashboard_widgets_suite_validate_list_box');
	
	add_settings_section('settings_list_box', __('List Box', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_list_box', 'dws_options_list_box');
	
	add_settings_field('widget_list_box',      __('Enable Widget', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_list_box', 'settings_list_box', array('id' => 'widget_list_box',      'section' => 'list_box', 'label' => esc_html__('Enable the List Box Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_list_box_view', __('View Role',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_list_box', 'settings_list_box', array('id' => 'widget_list_box_view', 'section' => 'list_box', 'label' => esc_html__('Minimum user role required to view the List Box', 'dashboard-widgets-suite')));
	add_settings_field('widget_list_box_menu', __('Menu/List',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_list_box', 'settings_list_box', array('id' => 'widget_list_box_menu', 'section' => 'list_box', 'label' => esc_html__('Menu (List) to display in the List Box Widget', 'dashboard-widgets-suite')));
	
	// widget box
	register_setting('dws_options_widget_box', 'dws_options_widget_box', 'dashboard_widgets_suite_validate_widget_box');
	
	add_settings_section('settings_widget_box', __('Widget Box', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_widget_box', 'dws_options_widget_box');
	
	add_settings_field('widget_widget_box',         __('Enable Widget', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_widget_box', 'settings_widget_box', array('id' => 'widget_widget_box',         'section' => 'widget_box', 'label' => esc_html__('Enable the Widget Box', 'dashboard-widgets-suite')));
	add_settings_field('widget_widget_box_view',    __('View Role',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_widget_box', 'settings_widget_box', array('id' => 'widget_widget_box_view',    'section' => 'widget_box', 'label' => esc_html__('Minimum user role required to view the Widget Box', 'dashboard-widgets-suite')));
	add_settings_field('widget_widget_box_sidebar', __('Sidebar',       'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_widget_box', 'settings_widget_box', array('id' => 'widget_widget_box_sidebar', 'section' => 'widget_box', 'label' => esc_html__('Sidebar (Widget Area) to display in Widget Box', 'dashboard-widgets-suite')));
	
	
	// system info
	register_setting('dws_options_system_info', 'dws_options_system_info', 'dashboard_widgets_suite_validate_system_info');
	
	add_settings_section('settings_system_info', __('System Info', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_system_info', 'dws_options_system_info');
	
	add_settings_field('widget_system_info',      __('Enable Widget', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_system_info', 'settings_system_info', array('id' => 'widget_system_info',      'section' => 'system_info', 'label' => esc_html__('Enable the System Info Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_system_info_adv',  __('Advanced Info', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_system_info', 'settings_system_info', array('id' => 'widget_system_info_adv',  'section' => 'system_info', 'label' => esc_html__('Display advanced server information', 'dashboard-widgets-suite')));
	add_settings_field('widget_system_info_view', __('View Role',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_system_info', 'settings_system_info', array('id' => 'widget_system_info_view', 'section' => 'system_info', 'label' => esc_html__('Minimum user role required to view System Info', 'dashboard-widgets-suite')));
	
	
	// debug log
	register_setting('dws_options_log_debug', 'dws_options_log_debug', 'dashboard_widgets_suite_validate_log_debug');
	
	add_settings_section('settings_log_debug', __('Debug Log Widget', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_log_debug', 'dws_options_log_debug');
	
	add_settings_field('widget_log_debug',        __('Enable Widget',  'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_log_debug', 'settings_log_debug', array('id' => 'widget_log_debug',        'section' => 'log_debug', 'label' => esc_html__('Enable the WP Debug Log Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_debug_limit',  __('Max Errors',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_log_debug', 'settings_log_debug', array('id' => 'widget_log_debug_limit',  'section' => 'log_debug', 'label' => esc_html__('Max number of errors to display', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_debug_length', __('Max Characters', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_log_debug', 'settings_log_debug', array('id' => 'widget_log_debug_length', 'section' => 'log_debug', 'label' => esc_html__('Max number of characters for each error', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_debug_height', __('Log Height',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_log_debug', 'settings_log_debug', array('id' => 'widget_log_debug_height', 'section' => 'log_debug', 'label' => esc_html__('Height of log display (in pixels)', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_debug_view',   __('View Role',      'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_log_debug', 'settings_log_debug', array('id' => 'widget_log_debug_view',   'section' => 'log_debug', 'label' => esc_html__('Minimum user role required to view the Debug Log', 'dashboard-widgets-suite')));
	
	
	// error log
	register_setting('dws_options_log_error', 'dws_options_log_error', 'dashboard_widgets_suite_validate_log_error');
	
	add_settings_section('settings_log_error', __('Error Log Widget', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_section_log_error', 'dws_options_log_error');
	
	add_settings_field('widget_log_error',        __('Enable Widget',  'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_checkbox', 'dws_options_log_error', 'settings_log_error', array('id' => 'widget_log_error',        'section' => 'log_error', 'label' => esc_html__('Enable the Error Log Widget', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_error_limit',  __('Max Errors',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_log_error', 'settings_log_error', array('id' => 'widget_log_error_limit',  'section' => 'log_error', 'label' => esc_html__('Max number of errors to display', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_error_length', __('Max Characters', 'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_log_error', 'settings_log_error', array('id' => 'widget_log_error_length', 'section' => 'log_error', 'label' => esc_html__('Max number of characters for each error', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_error_height', __('Log Height',     'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_number',   'dws_options_log_error', 'settings_log_error', array('id' => 'widget_log_error_height', 'section' => 'log_error', 'label' => esc_html__('Height of log display (in pixels)', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_error_view',   __('View Role',      'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_select',   'dws_options_log_error', 'settings_log_error', array('id' => 'widget_log_error_view',   'section' => 'log_error', 'label' => esc_html__('Minimum user role required to view the Error Log', 'dashboard-widgets-suite')));
	add_settings_field('widget_log_error_path',   __('Log Location',   'dashboard-widgets-suite'), 'dashboard_widgets_suite_callback_text',     'dws_options_log_error', 'settings_log_error', array('id' => 'widget_log_error_path',   'section' => 'log_error', 'label' => esc_html__('Enter the absolute path to your error log (ask host if unsure)', 'dashboard-widgets-suite')));
	
	
	// 
	
}
