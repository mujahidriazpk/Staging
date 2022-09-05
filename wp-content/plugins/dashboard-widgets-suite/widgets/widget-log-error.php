<?php // Dashboard Widgets Suite - Error Log Widget

if (!defined('ABSPATH')) die();

function dashboard_widgets_suite_log_error() {
	
	global $dws_options_log_error;
	
	$options = $dws_options_log_error;
	
	$log_path = (isset($options['widget_log_error_path']) && !empty($options['widget_log_error_path'])) ? realpath($options['widget_log_error_path']) : '';
	
	$log_file      = apply_filters('dashboard_widgets_suite_log_error_path', $log_path);
	$user_level    = apply_filters('dashboard_widgets_suite_log_error_level', current_user_can('manage_options'));
	
	$error_limit   = isset($options['widget_log_error_limit'])  ? $options['widget_log_error_limit']  : 20;
	$error_length  = isset($options['widget_log_error_length']) ? $options['widget_log_error_length'] : 350;
	$error_height  = isset($options['widget_log_error_height']) ? $options['widget_log_error_height'] : 300;
	
	$clear_file    = false;
	$errors        = '';
	
	// clear log file
	
	if ($user_level && isset($_GET['dws_error_log_clear']) && $_GET['dws_error_log_clear'] === 'true') {
		
		$nonce = isset($_GET['dws_error_log_nonce']) ? $_GET['dws_error_log_nonce'] : null;
		
		if (wp_verify_nonce($nonce, 'dws_error_log_nonce')) {
			
			if (is_writable($log_file)) {
				
				$handle = fopen($log_file, 'w');
				fclose($handle);
				$clear_message = esc_html__('Log file cleared.', 'dashboard-widgets-suite');
				$clear_message .= ' <a href="'. esc_url(admin_url()) .'">'. esc_html__('Reload page', 'dashboard-widgets-suite') .'</a> <span class="fa fa-redo"></span>';
				
			} else {
				
				$permissions = substr(sprintf('%o', fileperms($log_file)), -4);
				$clear_message  = esc_html__('The log file could not be cleared because it is not writable by the server. ', 'dashboard-widgets-suite');
				$clear_message .= esc_html__('Current file permissions = ', 'dashboard-widgets-suite') . $permissions;
				
			}
			
			$clear_message = apply_filters('dashboard_widgets_suite_log_error_clear', $clear_message);
			
			$clear_file = true;
			
		}
		
	}
	
	// display log file
	
	echo '<div id="dws-log-error" class="dws-dashboard-widget">';
	
	if (file_exists($log_file)) {
		
		$errors = dashboard_widgets_suite_get_log($log_file, $error_limit);
		$errors = apply_filters('dashboard_widgets_suite_log_error_errors', $errors);
		
		$count  = count($errors);
		$lines = dashboard_widgets_suite_get_lines($log_file);
		
		$nonce  = wp_create_nonce('dws_error_log_nonce');
		$href   = admin_url('?dws_error_log_clear=true&dws_error_log_nonce='. $nonce);
		
		if ($clear_file) echo '<p><em>'. $clear_message .'</em></p>';
		
		if ($errors) {
			
			$item_count = ($count < $error_limit) ? $count : $error_limit;
			
			echo '<p><span class="fa fa-search"></span> '. esc_html__('Showing the latest ', 'dashboard-widgets-suite') . $item_count . esc_html__(' of ', 'dashboard-widgets-suite') . sprintf(_n('%s error.', '%s errors.', $lines, 'dashboard-widgets-suite'), $lines);
			
			if ($user_level) echo ' <a href="'. $href .'" onclick="return confirm(\'Are you sure?\');">'. esc_html__('Clear log file', 'dashboard-widgets-suite') .'</a> &#8634;'; // fa-undo
			
			echo '</p>';
			
			echo '<div id="dws-log-error" class="dws-log" style="height:'. $error_height .'px;">';
			echo '<ol>';
			
			$i = 0;
			foreach ($errors as $error) {
				
				if ($i < $error_limit) {
					
					echo '<li>';
					
					$error = preg_replace("/\<a([^>]*)\>([^<]*)\<\/a\>/i", "$2", $error);
					
					$error = esc_html($error);
					
					if (strlen($error) > $error_length) {
						
						echo substr($error, 0, $error_length) .' [...]';
						
					} else {
						
						echo $error;
						
					}
					
					echo '</li>';
					
					$i++;
					
				} else {
					
					break;
				}
			}
			
			echo '</ol>';
			echo '</div>';
			
		} else {
			
			if (!empty($error_limit)) {
				
				echo '<p><span class="fa fa-check"></span> '. esc_html__('Congrats, error log is empty.', 'dashboard-widgets-suite') .'</p>';
				
			} else {
				
				echo '<p><span class="fa fa-check"></span> '. esc_html__('Error display disabled via widget settings.', 'dashboard-widgets-suite') .'</p>';
				
			}
			
		}
		
	} else {
		
		echo '<p><span class="fa fa-exclamation-circle"></span> <em>'. esc_html__('There was a problem reading the log file.', 'dashboard-widgets-suite') .'</em></p>';
		
	}
	
	echo '</div>';
	
	do_action('dashboard_widgets_suite_log_error', $errors);
	
}
