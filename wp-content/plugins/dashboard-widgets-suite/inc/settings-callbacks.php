<?php // Dashboard Widgets Suite - Settings Callbacks

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_section_general() {
	
	echo '<p>'. esc_html__('Thank you for using the free version of Dashboard Widgets Suite. May your Dashboard serve you well.', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_notes_user() {
	
	echo '<p>'. esc_html__('This widget enables you to post notes for specific user levels. ', 'dashboard-widgets-suite');
	echo esc_html__('Double-click on any note on the Dashboard to edit or delete.', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_feed_box() {
	
	echo '<p>'. esc_html__('This widget enables you to view a custom RSS Feed on the Dashboard. ', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_social_box() {
	
	echo '<p>'. esc_html__('This widget enables you to link up your social media profiles. Leave any URL blank to disable. ', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_social_box_note() {
	
	echo '<p>'. esc_html__('Want more icons/options?', 'dashboard-widgets-suite');
	echo ' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/support/#contact">'. esc_html__('Let me know', 'dashboard-widgets-suite') .'</a>. ';
	echo esc_html__('You can find a list of available icons', 'dashboard-widgets-suite');
	echo ' <a target="_blank" rel="noopener noreferrer" href="https://github.com/ericakfranz/socicon/">'. esc_html__('here', 'dashboard-widgets-suite') .'</a> :)</p>';
	
}

function dashboard_widgets_suite_section_list_box() {
	
	echo '<p>'. esc_html__('This widget enables you to display a custom list on the Dashboard. ', 'dashboard-widgets-suite');
	echo esc_html__('Visit Appearance &rarr; Menus &rarr; &ldquo;Dashboard Widgets Suite&rdquo; to customize the list. ', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_widget_box() {
	
	echo '<p>'. esc_html__('This widget enables you to display any widget on the Dashboard. ', 'dashboard-widgets-suite');
	echo esc_html__('Visit Appearance &rarr; Widgets &rarr; &ldquo;Dashboard Widgets Suite&rdquo; to add, remove, and customize widgets. ', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_system_info() {
	
	echo '<p>'. esc_html__('This widget displays information about your server and WordPress. Displayed only to Admins by default. ', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_section_log_debug() {
	
	echo '<p>'. esc_html__('This widget displays your WP Debug Log when WP_DEBUG is enabled.', 'dashboard-widgets-suite');
	echo ' <a target="_blank" rel="noopener noreferrer" href="https://codex.wordpress.org/Debugging_in_WordPress">';
	echo esc_html__('Learn how to enable WP_DEBUG&nbsp;&rsaquo;', 'dashboard-widgets-suite') .'</a></p>';
	
}

function dashboard_widgets_suite_section_log_error() {
	
	echo '<p>'. esc_html__('This widget displays your server&rsquo;s Error Log. Consult your hosting docs for more info.', 'dashboard-widgets-suite') .'</p>';
	
}

function dashboard_widgets_suite_user_roles() {
	
	$roles = array();
	
	$all_roles = wp_roles()->roles;
	
	$editable_roles = apply_filters('dashboard_widgets_suite_editable_roles', $all_roles);
	
	foreach ($editable_roles as $role_name => $role_info) {
		
		$roles[$role_name] = array(
			'value' => $role_name,
			'label' => ucfirst($role_name)
		);
		
	}
	
	$all = array('all' => array(
			'value' => 'all',
			'label' => esc_html__('Any Role', 'dashboard-widgets-suite')
		)
	);
	
	$roles = array_merge($all, $roles);
	
	return $roles;
	
}

function dashboard_widgets_suite_widget_sidebars() {
	
	$roles = array();
	
	foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
		
		$role_id   = isset($sidebar['id'])   ? $sidebar['id']   : '';
		$role_name = isset($sidebar['name']) ? $sidebar['name'] : '';
		
		$roles[$role_id] = array(
			'value' => $role_id,
			'label' => $role_name,
		);
		
	}
	
	return $roles;
	
}

function dashboard_widgets_suite_menu_list() {
	
	$roles = array();
	
	$menus = get_terms('nav_menu', array('hide_empty' => true));
	
	foreach ($menus as $menu) {
		
		$role_id   = isset($menu->term_id) ? $menu->term_id : '';
		$role_name = isset($menu->name)    ? $menu->name    : '';
		
		$roles[$role_id] = array(
			'value' => $role_id,
			'label' => $role_name,
		);
		
	}
	
	return $roles;
	
}

function dashboard_widgets_suite_callback_select($args) {
	
	$id      = isset($args['id'])      ? $args['id']      : '';
	$label   = isset($args['label'])   ? $args['label']   : '';
	$section = isset($args['section']) ? $args['section'] : '';
	
	$setting = 'dws_options_'. $section;
	
	global $dws_options_feed_box, $dws_options_notes_user, $dws_options_social_box, $dws_options_general, $dws_options_list_box, 
			$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_widget_box;
	
	$options = ${$setting};
	
	$value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';
	
	$options_array = array();
	
	if (
		
		$id === 'widget_notes_edit'       || 
		$id === 'widget_notes_view'       || 
		$id === 'widget_control_view'     || 
		$id === 'widget_log_debug_view'   || 
		$id === 'widget_log_error_view'   || 
		$id === 'widget_feed_box_view'    || 
		$id === 'widget_system_info_view' || 
		$id === 'widget_social_box_view'  || 
		$id === 'widget_list_box_view'    || 
		$id === 'widget_widget_box_view'
		
	) {
		
		$options_array = dashboard_widgets_suite_user_roles();
		
	} elseif ($id === 'widget_widget_box_sidebar') {
		
		$options_array = dashboard_widgets_suite_widget_sidebars();
		
	} elseif ($id === 'widget_list_box_menu') {
		
		$options_array = dashboard_widgets_suite_menu_list();
		
	}
	
	echo '<select name="'. $setting .'['. $id .']">';
	
	foreach ($options_array as $option) {
		echo '<option '. selected($option['value'], $value, false) .' value="'. $option['value'] .'">'. $option['label'] .'</option>';
	}
	echo '</select> <label class="dws-label inline-block" for="'. $setting .'['. $id .']">'. $label .'</label>';
	
}

function dashboard_widgets_suite_callback_text($args) {
	
	$id      = isset($args['id'])      ? $args['id']      : '';
	$label   = isset($args['label'])   ? $args['label']   : '';
	$section = isset($args['section']) ? $args['section'] : '';
	
	$setting = 'dws_options_'. $section;
	
	global $dws_options_feed_box, $dws_options_notes_user, $dws_options_social_box, $dws_options_general, $dws_options_list_box, 
			$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_widget_box;
	
	$options = ${$setting};
	
	$value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';
	
	if ($id === 'widget_notes_text_size') {
		
		$class_input = 'dws-input-text dws-input-text-short';
		$class_label = 'dws-label inline-block';
		
	} else {
		
		$class_input = 'dws-input-text';
		$class_label = 'dws-label';
		
	}
	
	echo '<input name="'. $setting .'['. $id .']" class="'. $class_input .'" type="text" size="40" value="'. $value .'"> ';
	echo '<label  for="'. $setting .'['. $id .']" class="'. $class_label .'">'. $label .'</label>';
	
}

function dashboard_widgets_suite_callback_textarea($args) {
	
	$id      = isset($args['id'])      ? $args['id']      : '';
	$label   = isset($args['label'])   ? $args['label']   : '';
	$section = isset($args['section']) ? $args['section'] : '';
	
	$setting = 'dws_options_'. $section;
	
	global $dws_options_feed_box, $dws_options_notes_user, $dws_options_social_box, $dws_options_general, $dws_options_list_box, 
			$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_widget_box;
	
	$options = ${$setting};
	
	$allowed_tags = wp_kses_allowed_html('post');
	
	$value = isset($options[$id]) ? wp_kses(stripslashes_deep($options[$id]), $allowed_tags) : '';
	
	echo '<textarea name="'. $setting .'['. $id .']" rows="3" cols="50">'. $value .'</textarea> ';
	echo '<label for="'. $setting .'['. $id .']" class="dws-label" >'. $label .'</label>';
	
}

function dashboard_widgets_suite_callback_checkbox($args) {
	
	$id      = isset($args['id'])      ? $args['id']      : '';
	$label   = isset($args['label'])   ? $args['label']   : '';
	$section = isset($args['section']) ? $args['section'] : '';
	
	$setting = 'dws_options_'. $section;
	
	global $dws_options_feed_box, $dws_options_notes_user, $dws_options_social_box, $dws_options_general, $dws_options_list_box, 
			$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_widget_box;
	
	$options = ${$setting};
	
	$checked = isset($options[$id]) ? checked($options[$id], 1, false) : '';
	
	echo '<input name="'. $setting .'['. $id .']" value="1" type="checkbox" '. $checked .'> ';
	echo '<label for="'. $setting .'['. $id .']" class="dws-label inline-block">'. $label .'</label>';
	
}

function dashboard_widgets_suite_callback_number($args) {
	
	$id      = isset($args['id'])      ? $args['id']      : '';
	$label   = isset($args['label'])   ? $args['label']   : '';
	$section = isset($args['section']) ? $args['section'] : '';
	
	$setting = 'dws_options_'. $section;
	
	global $dws_options_feed_box, $dws_options_notes_user, $dws_options_social_box, $dws_options_general, $dws_options_list_box, 
			$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_widget_box;
	
	$options = ${$setting};
	
	$value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';
	
	$min = 0;
	$max = ($id === 'dashboard_columns') ? 4 : 999;
	
	echo '<input name="'. $setting .'['. $id .']" type="number" class="small-text" min="'. $min .'" max="'. $max .'" value="'. $value .'"> ';
	echo '<label for="'. $setting .'['. $id .']" class="dws-label inline-block">'. $label .'</label>';
	
}

function dashboard_widgets_suite_callback_reset($args) {
	
	$nonce = wp_create_nonce('dws_reset_options');
	$url   = admin_url('options-general.php?page=dashboard_widgets_suite');
	$href  = esc_url(add_query_arg(array('reset-options-verify' => $nonce), $url));
	
	echo '<a class="dws-reset-options" href="'. $href .'">'. esc_html__('Restore default plugin options', 'dashboard-widgets-suite') .'</a>';
	
}

function dashboard_widgets_suite_callback_delete($args) {
	
	$nonce = wp_create_nonce('dws_delete_notes');
	$url   = admin_url('options-general.php?page=dashboard_widgets_suite');
	$href  = esc_url(add_query_arg(array('delete-notes-verify' => $nonce), $url));
	
	echo '<a class="dws-delete-notes" href="'. $href .'">'. esc_html__('Delete all User Notes', 'dashboard-widgets-suite') .'</a>';
	
}

function dashboard_widgets_suite_callback_rate($args) {
	
	$href  = 'https://wordpress.org/support/plugin/'. DWS_SLUG .'/reviews/?rate=5#new-post';
	$title = esc_attr__('Help keep Dashboard Widgets Suite going strong! A huge THANK YOU for your support!', 'dashboard-widgets-suite');
	$text  = isset($args['label']) ? $args['label'] : esc_html__('Show support with a 5-star rating&nbsp;&raquo;', 'dashboard-widgets-suite');
	
	echo '<a target="_blank" rel="noopener noreferrer" class="dws-rate-plugin" href="'. $href .'" title="'. $title .'">'. $text .'</a>';
	
}
