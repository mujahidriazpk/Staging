<?php // Dashboard Widgets Suite - Uninstall Remove Options

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

delete_option('dws_options_feed_box');
delete_option('dws_options_general');
delete_option('dws_options_list_box');
delete_option('dws_options_log_debug');
delete_option('dws_options_log_error');
delete_option('dws_options_notes_user');
delete_option('dws_options_social_box');
delete_option('dws_options_system_info');
delete_option('dws_options_widget_box');

// For User Notes: dws_notes_user_data (can delete via plugin settings)