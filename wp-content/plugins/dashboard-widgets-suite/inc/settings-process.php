<?php // Dashboard Widgets Suite - Process Settings
	
if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_control_panel_submit() {
	
	global $dws_options_general, $dws_options_notes_user, $dws_options_feed_box, $dws_options_social_box, 
	$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_list_box, $dws_options_widget_box;
	
	if (isset($_POST['dws-control-panel']['nonce']) && wp_verify_nonce($_POST['dws-control-panel']['nonce'], 'dws-control-panel-nonce')) {
		
		// control panel
		
		$control_panel = isset($_POST['dws_options_general']['widget_control_panel']) ? boolval($_POST['dws_options_general']['widget_control_panel']) : false;
		
		$dws_options_general['widget_control_panel'] = $control_panel;
		
		update_option('dws_options_general', $dws_options_general);
		
		// user notes
		
		$notes_user = isset($_POST['dws_options_notes_user']['widget_notes_user']) ? boolval($_POST['dws_options_notes_user']['widget_notes_user']) : false;
		
		$dws_options_notes_user['widget_notes_user'] = $notes_user;
		
		update_option('dws_options_notes_user', $dws_options_notes_user);
		
		// feed box
		
		$feed_box = isset($_POST['dws_options_feed_box']['widget_feed_box']) ? boolval($_POST['dws_options_feed_box']['widget_feed_box']) : false;
		
		$dws_options_feed_box['widget_feed_box'] = $feed_box;
		
		update_option('dws_options_feed_box', $dws_options_feed_box);
		
		// social box
		
		$social_box = isset($_POST['dws_options_social_box']['widget_social_box']) ? boolval($_POST['dws_options_social_box']['widget_social_box']) : false;
		
		$dws_options_social_box['widget_social_box'] = $social_box;
		
		update_option('dws_options_social_box', $dws_options_social_box);
		
		// list box
		
		$list_box = isset($_POST['dws_options_list_box']['widget_list_box']) ? boolval($_POST['dws_options_list_box']['widget_list_box']) : false;
		
		$dws_options_list_box['widget_list_box'] = $list_box;
		
		update_option('dws_options_list_box', $dws_options_list_box);
		
		// widget box
		
		$widget_box = isset($_POST['dws_options_widget_box']['widget_widget_box']) ? boolval($_POST['dws_options_widget_box']['widget_widget_box']) : false;
		
		$dws_options_widget_box['widget_widget_box'] = $widget_box;
		
		update_option('dws_options_widget_box', $dws_options_widget_box);
		
		// system info
		
		$system_info = isset($_POST['dws_options_system_info']['widget_system_info']) ? boolval($_POST['dws_options_system_info']['widget_system_info']) : false;
		
		$dws_options_system_info['widget_system_info'] = $system_info;
		
		update_option('dws_options_system_info', $dws_options_system_info);
		
		// debug log
		
		$log_debug = isset($_POST['dws_options_log_debug']['widget_log_debug']) ? boolval($_POST['dws_options_log_debug']['widget_log_debug']) : false;
		
		$dws_options_log_debug['widget_log_debug'] = $log_debug;
		
		update_option('dws_options_log_debug', $dws_options_log_debug);
		
		// error log
		
		$log_error = isset($_POST['dws_options_log_error']['widget_log_error']) ? boolval($_POST['dws_options_log_error']['widget_log_error']) : false;
		
		$dws_options_log_error['widget_log_error'] = $log_error;
		
		update_option('dws_options_log_error', $dws_options_log_error);
		
	}
	
}

function dashboard_widgets_suite_notes_user_submit() {
	
	global $dws_options_notes_user;
	
	$required_role = isset($dws_options_notes_user['widget_notes_edit']) ? $dws_options_notes_user['widget_notes_edit'] : null;
	
	if (!dashboard_widgets_suite_check_role($required_role)) return false;
	
	if (isset($_POST['dws-notes-user']['nonce']) && wp_verify_nonce($_POST['dws-notes-user']['nonce'], 'dws-notes-user-nonce')) {
		
		$query = null;
		
		if     (isset($_POST['dws-notes-user']['add']))    $query = 'add';
		elseif (isset($_POST['dws-notes-user']['edit']))   $query = 'edit';
		elseif (isset($_POST['dws-notes-user']['delete'])) $query = 'delete';
		
		$id     = isset($_POST['dws-notes-user']['id'])     ? intval($_POST['dws-notes-user']['id']) : '';
		$name   = isset($_POST['dws-notes-user']['name'])   ? sanitize_text_field($_POST['dws-notes-user']['name']) : '';
		$role   = isset($_POST['dws-notes-user']['role'])   ? sanitize_text_field($_POST['dws-notes-user']['role']) : '';
		$title  = isset($_POST['dws-notes-user']['title'])  ? sanitize_text_field(stripslashes_deep($_POST['dws-notes-user']['title'])) : '';
		$format = isset($_POST['dws-notes-user']['format']) ? sanitize_text_field($_POST['dws-notes-user']['format']) : 'text';
		$note   = isset($_POST['dws-notes-user']['note'])   ? stripslashes_deep($_POST['dws-notes-user']['note']) : '';
		
		list($date, $time) = dashboard_widgets_suite_get_date();
		
		$data = get_option('dws_notes_user_data');
		
		$data = apply_filters('dashboard_widgets_suite_notes_user_data_get', $data);
		
		$update = false;
		
		if ($query === 'add') {
			
			$id = dashboard_widgets_suite_get_id($data);
			
			$add = array(
				array(
					'date'   => $date, 
					'id'     => $id, 
					'name'   => $name, 
					'note'   => $note, 
					'role'   => $role, 
					'time'   => $time, 
					'title'  => $title, 
					'format' => $format,
				)
			);
			
			$add = apply_filters('dashboard_widgets_suite_notes_user_data_add', $add);
			
			if (is_array($data)) $data = array_merge($data, $add);
			else $data = $add;
			
			$update = update_option('dws_notes_user_data', $data);
			
		} elseif ($query === 'edit') {
			
			foreach ($data as $key => $value) {
				
				$note_id = isset($value['id']) ? intval($value['id']) : null;
				
				if ($note_id === $id) {
					
					$data[$key]['date'] = $date;
					$data[$key]['note'] = $note;
					$data[$key]['time'] = $time;
					
					$data = apply_filters('dashboard_widgets_suite_notes_user_data_edit', $data);
					
					$update = update_option('dws_notes_user_data', $data);
					
					break;
					
				}
				
			}
			
		} elseif ($query === 'delete') {
			
			foreach ($data as $key => $value) {
				
				$note_id = isset($value['id']) ? intval($value['id']) : null;
				
				if ($note_id === $id) {
					
					unset($data[$key]);
					
					$data = apply_filters('dashboard_widgets_suite_notes_user_data_delete', $data);
					
					$update = update_option('dws_notes_user_data', $data);
					
					break;
					
				}
				
			}
			
		}
		
		do_action('dashboard_widgets_suite_notes_user_submit', $update, $data);
		
	}
	
}
