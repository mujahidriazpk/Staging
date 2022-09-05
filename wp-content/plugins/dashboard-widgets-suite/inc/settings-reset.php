<?php // Dashboard Widgets Suite - Reset Settings

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_admin_notice() {
	
	$screen = get_current_screen();
	
	if ($screen->id === 'settings_page_dashboard_widgets_suite') {
		
		if (isset($_GET['reset-options'])) {
			
			if ($_GET['reset-options'] === 'true') : ?>
				
				<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('Default options restored.', 'dashboard-widgets-suite'); ?></strong></p></div>
				
			<?php else : ?>
				
				<div class="notice notice-info is-dismissible"><p><strong><?php esc_html_e('No changes made to options.', 'dashboard-widgets-suite'); ?></strong></p></div>
				
			<?php endif;
			
		} elseif (isset($_GET['delete-notes'])) {
			
			if ($_GET['delete-notes'] === 'true') : ?>
				
				<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('All User Notes deleted.', 'dashboard-widgets-suite'); ?></strong></p></div>
				
			<?php else : ?>
				
				<div class="notice notice-info is-dismissible"><p><strong><?php esc_html_e('No changes made to User Notes.', 'dashboard-widgets-suite'); ?></strong></p></div>
				
			<?php endif;
			
		}
		
	}
	
}

function dashboard_widgets_suite_reset_options() {
	
	if (isset($_GET['reset-options-verify']) && wp_verify_nonce($_GET['reset-options-verify'], 'dws_reset_options')) {
		
		if (!current_user_can('manage_options')) exit;
		
		$update_general     = update_option('dws_options_general',     Dashboard_Widgets_Suite::options_general());
		$update_notes_user  = update_option('dws_options_notes_user',  Dashboard_Widgets_Suite::options_notes_user());
		$update_feed_box    = update_option('dws_options_feed_box',    Dashboard_Widgets_Suite::options_feed_box());
		$update_social_box  = update_option('dws_options_social_box',  Dashboard_Widgets_Suite::options_social_box());
		$update_list_box    = update_option('dws_options_list_box',    Dashboard_Widgets_Suite::options_list_box());
		$update_system_info = update_option('dws_options_system_info', Dashboard_Widgets_Suite::options_system_info());
		$update_log_debug   = update_option('dws_options_log_debug',   Dashboard_Widgets_Suite::options_log_debug());
		$update_log_error   = update_option('dws_options_log_error',   Dashboard_Widgets_Suite::options_log_error());
		$update_widget_box  = update_option('dws_options_widget_box',  Dashboard_Widgets_Suite::options_widget_box());
		
		$result = 'false';
		
		if (
			$update_general     || 
			$update_notes_user  || 
			$update_feed_box    || 
			$update_social_box  || 
			$update_list_box    || 
			$update_system_info || 
			$update_log_debug   || 
			$update_log_error   || 
			$update_widget_box
			
		) $result = 'true';
		
		$location = admin_url('options-general.php?page=dashboard_widgets_suite&reset-options='. $result);
		wp_redirect($location);
		exit;
		
	}
	
}

function dashboard_widgets_suite_delete_notes() {
	
	if (isset($_GET['delete-notes-verify']) && wp_verify_nonce($_GET['delete-notes-verify'], 'dws_delete_notes')) {
		
		$result = false;
		
		if (current_user_can('manage_options')) {
			
			$result = delete_option('dws_notes_user_data');
			
		}
		
		$result = $result ? 'true' : 'false';
		
		$location = admin_url('options-general.php?page=dashboard_widgets_suite&tab=tab2&delete-notes='. $result);
		wp_redirect($location);
		exit;
		
	}
	
}
