<?php // Dashboard Widgets Suite - Widget Helper Functions

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_check_role($role) {
	
	$core = false;
	
	if ($role === 'administrator') {
		
		$role = 'manage_options';
		$core = true;
		
	} elseif ($role === 'editor') {
		
		$role = 'moderate_comments';
		$core = true;
		
	} elseif ($role === 'author') {
		
		$role = 'publish_posts';
		$core = true;
		
	} elseif ($role === 'contributor') {
		
		$role = 'edit_posts';
		$core = true;
		
	} elseif ($role === 'subscriber') {
		
		$role = 'read';
		$core = true;
		
	}
	
	if ($role === 'all') {
		
		return true;
		
	} else {
		
		if ($core) {
			
			return current_user_can($role) ? true : false;
			
		} else {
			
			$roles = get_role($role);
			
			if (is_object($roles) && !empty($roles)) {
				
				$caps = $roles->capabilities;
				
				if (is_array($caps)) {
					
					foreach ($caps as $key => $val) {
						
						if ($val) {
							
							if (current_user_can($key)) continue;
							
							else return false;
							
						}
						
					}
					
				}
				
			}
			
			return true;
			
		}
		
	}
	
}

function dashboard_widgets_suite_get_date() {
	
	$date = date_i18n(get_option('date_format'), current_time('timestamp'));
	$time = date_i18n(get_option('time_format'), current_time('timestamp'));
	
	$date_time = array($date, $time);
	$date_time = apply_filters('dashboard_widgets_suite_get_date', $date_time);
	
	return $date_time;
	
}

function dashboard_widgets_suite_get_id($data) {
	
	$id = 1;
	
	$array = array();
	
	if (isset($data[0])) $array = $data[0];
	
	if (array_key_exists('id', $array)) {
		
		$ids = array_column($data, 'id');
		$max = max($ids);
		$id  = $max + 1;
		
	}
	
	return $id;
}

// array_column fallback for PHP < 5.5
if (!function_exists('array_column')) {
	
	function array_column(array $input, $columnKey, $indexKey = null) {
		
		$array = array();
		
		foreach ($input as $value) {
			
			if (!array_key_exists($columnKey, $value)) {
				
				trigger_error('Key "'. $columnKey .'" does not exist in array');
				
				return false;
				
			}
			
			if (is_null($indexKey)) {
				
				$array[] = $value[$columnKey];
				
			} else {
				
				if (!array_key_exists($indexKey, $value)) {
					
					trigger_error('Key "'. $indexKey .'" does not exist in array');
					
					return false;
					
				}
				
				if (!is_scalar($value[$indexKey])) {
					
					trigger_error('Key "'. $indexKey .'" does not contain scalar value');
					
					return false;
					
				}
				
				$array[$value[$indexKey]] = $value[$columnKey];
				
			}
			
		}
		
		return $array;
		
	}
    
}

function dashboard_widgets_suite_get_ip() {
	
	if (isset($_SERVER)) {
		
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
			
		elseif (isset($_SERVER['HTTP_CLIENT_IP'])) $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		
		elseif (isset($_SERVER['REMOTE_ADDR'])) $ip_address = $_SERVER['REMOTE_ADDR'];
		
	} else {
		
		if (getenv('HTTP_X_FORWARDED_FOR')) $ip_address = getenv('HTTP_X_FORWARDED_FOR');
			
		elseif (getenv('HTTP_CLIENT_IP')) $ip_address = getenv('HTTP_CLIENT_IP');
		
		elseif (getenv('REMOTE_ADDR')) $ip_address = getenv('REMOTE_ADDR');
		
	}
	
	return sanitize_text_field($ip_address);
	
}

function dashboard_widgets_suite_register_widget_box() {
	
	$widget_args = array(
		
		'name'          => esc_html__('Dashboard Widgets Suite', 'dashboard-widgets-suite'), 
		'description'   => esc_html__('Add widgets to the WordPress Dashboard', 'dashboard-widgets-suite'), 
		'id'            => 'dws-widget-box', 
		'class'         => 'dws-widget-box', 
		'before_widget' => '<div id="%1$s" class="widget %2$s">', 
		'after_widget'  => '</div>', 
		'before_title'  => '<p class="widgettitle"><strong>', 
		'after_title'   => '</strong></p>', 
		
	);
	
	register_sidebar($widget_args);
	
}

function dashboard_widgets_suite_register_list_box() {
	
	$menu = 'Dashboard Widgets Suite';
	
	$menu_exists = wp_get_nav_menu_object($menu);
	
	if (!$menu_exists) {
		
		$menu_id = wp_create_nav_menu($menu);
		
		$menu_item_1 = array(
			'menu-item-attr-title'  => esc_attr__('Home Page', 'dashboard-widgets-suite'), 
			'menu-item-classes'     => 'dws-list-box-menu-item', 
			'menu-item-description' => esc_html__('This is an example link, edit as desired or delete if not needed.', 'dashboard-widgets-suite'), 
			'menu-item-status'      => 'publish', 
			'menu-item-target'      => '_blank', 
			'menu-item-title'       => esc_html__('Home Page', 'dashboard-widgets-suite'), 
			'menu-item-url'         => home_url('/'), 
		);
		
		wp_update_nav_menu_item($menu_id, 0, $menu_item_1);
		
		$menu_item_2 = array(
			'menu-item-attr-title'  => esc_attr__('Example Page', 'dashboard-widgets-suite'), 
			'menu-item-classes'     => 'dws-list-box-menu-item', 
			'menu-item-description' => esc_html__('This is an example link, edit as desired or delete if not needed.', 'dashboard-widgets-suite'), 
			'menu-item-status'      => 'publish', 
			'menu-item-target'      => '_blank', 
			'menu-item-title'       =>  esc_html__('Example Page', 'dashboard-widgets-suite'), 
			'menu-item-url'         => 'https://example.com/', 
		);
		
		wp_update_nav_menu_item($menu_id, 0, $menu_item_2);
		
		$menu_item_3 = array(
			'menu-item-attr-title'  => esc_attr__('Custom Page', 'dashboard-widgets-suite'), 
			'menu-item-classes'     => 'dws-list-box-menu-item', 
			'menu-item-description' => esc_html__('This is an example link, edit as desired or delete if not needed.', 'dashboard-widgets-suite'), 
			'menu-item-status'      => 'publish', 
			'menu-item-target'      => '_blank', 
			'menu-item-title'       =>  esc_html__('Custom Page', 'dashboard-widgets-suite'), 
			'menu-item-url'         => 'https://example.com/custom/', 
		);
		
		wp_update_nav_menu_item($menu_id, 0, $menu_item_3);
		
	}
	
}

// Source: https://m0n.co/u
function dashboard_widgets_suite_get_log($path, $limit, $block_size = 512) {
	
	$lines = array();
	
	$leftover = '';
	
	$fh = fopen($path, 'r');
	fseek($fh, 0, SEEK_END);
	
	do {
		
		$can_read = $block_size;
		
		if (ftell($fh) <= $block_size) $can_read = ftell($fh);
		
		if (empty($can_read)) break;
		
		fseek($fh, - $can_read, SEEK_CUR);
		
		$data  = fread($fh, $can_read);
		$data .= $leftover;
		
		fseek($fh, - $can_read, SEEK_CUR);
		
		$split_data = array_reverse(explode("\n", $data));
		$new_lines  = array_slice($split_data, 0, - 1);
		$lines      = array_merge($lines, $new_lines);
		$leftover   = $split_data[count($split_data) - 1];
		
	} while (count($lines) <= $limit && ftell($fh) != 0);
	
	if (ftell($fh) == 0) $lines[] = $leftover;
	
	fclose($fh);
	
	$lines = array_filter($lines);
	
	return array_slice($lines, 0, $limit);
	
}

function dashboard_widgets_suite_get_lines($path) {
	
	$lines = count(file($path));
	
	return $lines;
	
}

function dashboard_widgets_suite_allowed_tags() {

	$allowed_tags = array(
		'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'abbr' => array(
			'title' => array(),
		),
		'b' => array(),
		'blockquote' => array(
			'cite'  => array(),
		),
		'cite' => array(
			'title' => array(),
		),
		'code' => array(),
		'pre' => array(),
		'del' => array(
			'datetime' => array(),
			'title' => array(),
		),
		'dd' => array(),
		'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'i' => array(),
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li' => array(
			'class' => array(),
		),
		'ol' => array(
			'class' => array(),
		),
		'p' => array(
			'class' => array(),
		),
		'q' => array(
			'cite' => array(),
			'title' => array(),
		),
		'span' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'ul' => array(
			'class' => array(),
		),
	);
	
	$allowed_tags = apply_filters('dashboard_widgets_suite_allowed_tags', $allowed_tags);
	
	return $allowed_tags;
}

if (!function_exists('boolval')) {
	
	function boolval($val) {
		
		return (bool) $val;
		
	}
	
}

function dashboard_widgets_suite_note_styles() {
	
	global $dws_options_notes_user;
	
	$size = isset($dws_options_notes_user['widget_notes_text_size']) ? $dws_options_notes_user['widget_notes_text_size'] : '12px';
	
	$css = '#dws-notes-user .dws-notes-user textarea, #dws-notes-user .dws-notes-user-note { font-size: '. esc_html($size) .'; }';
	
	$css = apply_filters('dashboard_widgets_suite_note_styles', $css);
	
	return $css;
	
}
