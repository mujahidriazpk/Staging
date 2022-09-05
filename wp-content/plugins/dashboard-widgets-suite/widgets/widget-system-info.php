<?php // Dashboard Widgets Suite - System Infos

if (!defined('ABSPATH')) exit;

/*
	
	Thanks to:
		
		johnregan3's Send System Info @ https://wordpress.org/plugins/send-system-info/
		cstrosser's TPC! Memory Usage @ https://wordpress.org/plugins/tpc-memory-usage/
	
*/

function dashboard_widgets_suite_system_info() {
	
	global $dws_options_system_info;
	
	$advanced = isset($dws_options_system_info['widget_system_info_adv']) ? $dws_options_system_info['widget_system_info_adv'] : false;
	
	do_action('dashboard_widgets_suite_system_info', $dws_options_system_info);
	
	echo '<div id="dws-system-info" class="dws-dashboard-widget">';
	
	if ($advanced) {
		
		echo '<ul class="dws-system-info-menu">';
		
		echo '<li><a href="#dws-system-info-overview" data-rel="dws-system-info-overview" class="selected">'. esc_html__('Overview', 'dashboard-widgets-suite') .'</a></li>';
		echo '<li><a href="#dws-system-info-wordpress" data-rel="dws-system-info-wordpress">'. esc_html__('WordPress', 'dashboard-widgets-suite') .'</a></li>';
		echo '<li><a href="#dws-system-info-client"    data-rel="dws-system-info-client">'.    esc_html__('Client',    'dashboard-widgets-suite') .'</a></li>';
		echo '<li><a href="#dws-system-info-server"    data-rel="dws-system-info-server">'.    esc_html__('Server',    'dashboard-widgets-suite') .'</a></li>';
		echo '<li><a href="#dws-system-info-database"  data-rel="dws-system-info-database">'.  esc_html__('Database',  'dashboard-widgets-suite') .'</a></li>';
		echo '<li><a href="#dws-system-info-php"       data-rel="dws-system-info-php">'.       esc_html__('PHP',       'dashboard-widgets-suite') .'</a></li>';
		echo '<li><a href="#dws-system-info-security"  data-rel="dws-system-info-security">'.  esc_html__('Security',  'dashboard-widgets-suite') .'</a></li>';
		
		echo '</ul>';
		
	}
	
	echo '<div class="dws-system-info" id="dws-system-info-overview">';
	dashboard_widgets_suite_get_overview();
	echo '</div>';
	
	if ($advanced) {
		
		echo '<div class="dws-system-info dws-hidden" id="dws-system-info-wordpress">';
		dashboard_widgets_suite_get_wordpress_infos();
		echo '</div>';
		
		echo '<div class="dws-system-info dws-hidden" id="dws-system-info-client">';
		dashboard_widgets_suite_get_client_infos();
		echo '</div>';
		
		echo '<div class="dws-system-info dws-hidden" id="dws-system-info-server">';
		dashboard_widgets_suite_get_server_infos();
		echo '</div>';
		
		echo '<div class="dws-system-info dws-hidden" id="dws-system-info-database">';
		dashboard_widgets_suite_get_database_infos();
		echo '</div>';
		
		echo '<div class="dws-system-info dws-hidden" id="dws-system-info-php">';
		dashboard_widgets_suite_get_php_info();
		echo '</div>';
		
		echo '<div class="dws-system-info dws-hidden" id="dws-system-info-security">';
		dashboard_widgets_suite_check_security();
		echo '</div>';
		
	}
	
	echo '</div>';
	
}





function dashboard_widgets_suite_get_overview() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('System Overview', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('WP Version: ',          'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_version(); ?></li>
		<li><?php esc_html_e('PHP Version: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_version(); ?></li>
		<li><?php esc_html_e('Database Version: ',    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_database_version(); ?></li>
		<li><?php esc_html_e('Client IP Address: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_ip(); ?></li>
		<li><?php esc_html_e('Server IP Address: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_address(); ?></li>
		<li><?php esc_html_e('Server Load: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_load(); ?></li>
		<li><?php esc_html_e('Server Load Average: ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_load_average(); ?></li>
		<li><?php esc_html_e('PHP Memory Usage: ',    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_memory_usage(); ?></li>
	</ul>
	
	<?php
}





/*
	
	WORDPRESS INFOS
	
*/

function dashboard_widgets_suite_get_wordpress_infos() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('WordPress Info', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('WP Version: ',             'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_version(); ?></li>
		<li><?php esc_html_e('Active Theme: ',           'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_theme_info(); ?></li>
		<li><?php esc_html_e('WP Memory Limit: ',        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_memory_limit(); ?></li>
		<li><?php esc_html_e('WP Remote Post: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_remote_post(); ?></li>
		<li><?php esc_html_e('WP Debug Mode: ',          'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_debug_mode(); ?></li>
		<li><?php esc_html_e('WP Debug Log: ',           'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_debug_log(); ?></li>
		<li><?php esc_html_e('WP Debug Display: ',       'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_debug_display(); ?></li>
		<li><?php esc_html_e('WP Debug Script: ',        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_script_debug(); ?></li>
		<li><?php esc_html_e('Query Logging: ',          'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_save_queries(); ?></li>
		<li><?php esc_html_e('Disallow File Edit: ',     'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_file_edit(); ?></li>
		<li><?php esc_html_e('Allow Core Auto Update: ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_core_update(); ?></li>
		<li><?php esc_html_e('WP DB Hostname: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_db_hostname(); ?></li>
		<li><?php esc_html_e('WP DB Name: ',             'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_db_name(); ?></li>
		<li><?php esc_html_e('Active Plugins: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_active_plugins(); ?></li>
		<li><?php esc_html_e('WP Language: ',            'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_language(); ?></li>
		<li><?php esc_html_e('Advanced Caching: ',       'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_advanced_cache(); ?></li>
		<li><?php esc_html_e('External Object Cache: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_object_cache(); ?></li>
		<li><?php esc_html_e('WordPress Time: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_time(); ?></li>
		<li><?php esc_html_e('Update Method: ',          'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_wp_update_methods(); ?></li>
	</ul>
	
	<?php
}

function dashboard_widgets_suite_get_wp_version() {
	
	return get_bloginfo('version');
	
}

function dashboard_widgets_suite_get_theme_info() {
	
	$theme_data = wp_get_theme();
	
	$theme_info = $theme_data->Name . esc_html__(', Version ', 'dashboard-widgets-suite') . $theme_data->Version;
	
	return $theme_info;
		
}

function dashboard_widgets_suite_get_wp_memory_limit() {
	
	$v   = WP_MEMORY_LIMIT;
	
	$l   = substr($v, -1);
	$ret = substr($v, 0, -1);
	
	switch (strtoupper($l)) {
		case 'P':
		case 'T':
		case 'G':
		case 'M':
		case 'K': $ret *= 1024;
		break;
		default:
		break;
	}
	
	return ($ret / 1024) .' MB';
}

function dashboard_widgets_suite_check_remote_post() {
	
	$test_url = 'https://www.example.com/';
	
	$params = array(
		'method'    => 'POST',
		'sslverify' => true,
		'timeout'   => 60,
		'body'      => array('test'),
	);
	
	$response = wp_remote_post($test_url, $params);
	
	if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
		
		$result = esc_html__('Enabled', 'dashboard-widgets-suite');
		
	} else {
		
		$result = esc_html__('Disabled', 'dashboard-widgets-suite');
		
	}
	
	return $result;
	
}

function dashboard_widgets_suite_get_wp_debug_mode() {
	
	return defined('WP_DEBUG') && WP_DEBUG ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_debug_log() {
	
	$enabled  = esc_html__('Enabled', 'dashboard-widgets-suite');
	$disabled = esc_html__('Disabled', 'dashboard-widgets-suite');
	
	if (is_string(WP_DEBUG_LOG)) $enabled .= ' @ '. WP_DEBUG_LOG;
	
	return defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? $enabled : $disabled;
	
}

function dashboard_widgets_suite_get_wp_debug_display() {
	
	return defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_script_debug() {
	
	return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_save_queries() {
	
	return defined('SAVEQUERIES') && SAVEQUERIES ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_file_edit() {
	
	return defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_core_update() {
	
	return defined('WP_AUTO_UPDATE_CORE') && WP_AUTO_UPDATE_CORE ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_db_hostname() {
	
	return sanitize_text_field(DB_HOST);
	
}

function dashboard_widgets_suite_get_wp_db_name() {
	
	return sanitize_text_field(DB_NAME);
	
}

function dashboard_widgets_suite_get_wp_active_plugins() {
	
	return count(get_option('active_plugins'));
	
}

function dashboard_widgets_suite_get_wp_language() {
	
	return defined('WPLANG') && WPLANG !== '' ? WPLANG : esc_html__('English', 'dashboard-widgets-suite') .' / '. get_locale();
	
}

function dashboard_widgets_suite_get_wp_advanced_cache() {
	
	return defined('WP_CACHE') && WP_CACHE ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_object_cache() {
	
	global $_wp_using_ext_object_cache;
	
	return $_wp_using_ext_object_cache ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_wp_time() {
	
	return sanitize_text_field(current_time('mysql'));
	
}

function dashboard_widgets_suite_get_wp_update_methods() {
	
	$filesystem_method = get_filesystem_method(array());
	$filesystem_message = $filesystem_method !== 'direct' ? esc_html__('FTP/SSH access only', 'dashboard-widgets-suite') : esc_html__('Direct access allowed', 'dashboard-widgets-suite');
	
	return $filesystem_message;
	
}





/*
	
	CLIENT INFOS
	
*/

function dashboard_widgets_suite_get_client_infos() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('Client Info', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('Platform: ',        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_platform(); ?></li>
		<li><?php esc_html_e('Browser: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_browser(); ?></li>
		<li><?php esc_html_e('IP Address: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_ip(); ?></li>
		<li><?php esc_html_e('User Agent: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_user_agent(); ?></li>
		<li><?php esc_html_e('Hostname: ',        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_host_name(); ?></li>
		<li><?php esc_html_e('Client Port: ',     'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_client_port(); ?></li>
	</ul>
	
	<?php
}

function dashboard_widgets_suite_get_platform() {
	
	$user_agent  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'n/a';
	
	$os_platform = esc_html__('Unknown OS Platform', 'dashboard-widgets-suite');
	
	$os_array = array(
		
		'/windows nt 10.0/i'    =>  'Windows 10',
		'/windows nt 6.2/i'     =>  'Windows 8',
		'/windows nt 6.1/i'     =>  'Windows 7',
		'/windows nt 6.0/i'     =>  'Windows Vista',
		'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		'/windows nt 5.1/i'     =>  'Windows XP',
		'/windows xp/i'         =>  'Windows XP',
		'/windows nt 5.0/i'     =>  'Windows 2000',
		'/windows me/i'         =>  'Windows ME',
		'/win98/i'              =>  'Windows 98',
		'/win95/i'              =>  'Windows 95',
		'/win16/i'              =>  'Windows 3.11',
		'/macintosh|mac os x/i' =>  'Mac OS X',
		'/mac_powerpc/i'        =>  'Mac OS 9',
		'/linux/i'              =>  'Linux',
		'/ubuntu/i'             =>  'Ubuntu',
		'/iphone/i'             =>  'iPhone',
		'/ipod/i'               =>  'iPod',
		'/ipad/i'               =>  'iPad',
		'/android/i'            =>  'Android',
		'/blackberry/i'         =>  'BlackBerry',
		'/webos/i'              =>  'Mobile'
		
	);
	
	$bit_array = array(
		
		'/wow64/i' => '64 bit',
		'/win64/i' => '64 bit'
		
	);

	foreach ($os_array as $regex => $value) {
		
		if (preg_match($regex, $user_agent)) {
			
			$os_platform = $value;
			
			break;
			
		}
	
	}
	
	foreach ($bit_array as $regex => $value) {
		
		if (preg_match($regex, $user_agent)) {
			
			$os_platform .= ' : '. $value;
			
			break;
			
		}
		
	}
	
	return $os_platform;
	
}

function dashboard_widgets_suite_get_browser() {
	
	$user_agent    = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'n/a';
	
	$browser       = esc_html__('Unknown Browser', 'dashboard-widgets-suite');
	
	$browser_array = array(
		
		'/msie/i'      =>  'Internet Explorer',
		'/firefox/i'   =>  'Firefox',
		'/chrome/i'    =>  'Chrome',
		'/safari/i'    =>  'Safari',
		'/opera/i'     =>  'Opera',
		'/netscape/i'  =>  'Netscape',
		'/maxthon/i'   =>  'Maxthon',
		'/konqueror/i' =>  'Konqueror',
		'/mobile/i'    =>  'Handheld Browser'
		
	);
	
	foreach ($browser_array as $regex => $value) {
		
		if (preg_match($regex, $user_agent)) {
			
			$browser = $value;
			
			break;
			
		}
		
	}
	
	return $browser;
	
}

function dashboard_widgets_suite_get_user_agent() {
	
	return isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'n/a';
	
}

function dashboard_widgets_suite_get_host_name() {
	
	return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(gethostbyaddr($_SERVER['REMOTE_ADDR'])) : 'n/a';
	
}

function dashboard_widgets_suite_get_client_port() {
	
	return isset($_SERVER['REMOTE_PORT']) ? sanitize_text_field($_SERVER['REMOTE_PORT']) : 'n/a';
	
}





/*
	
	SERVER INFOS
	
*/

function dashboard_widgets_suite_get_server_infos() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('Server Info', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('OS/Server: ',        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_name(); ?></li>
		<li><?php esc_html_e('Server Software: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_software(); ?></li>
		<li><?php esc_html_e('Server Version: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_version(); ?></li>
		<li><?php esc_html_e('Server Address: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_address(); ?></li>
		<li><?php esc_html_e('Server Port: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_port(); ?></li>
		<li><?php esc_html_e('Document Root: ',    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_document_root(); ?></li>
		<li><?php esc_html_e('Server Name: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_hostname(); ?></li>
		<li><?php esc_html_e('Server Load: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_load(); ?></li>
		<li><?php esc_html_e('Load Average: ',     'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_load_average(); ?></li>
		<li><?php esc_html_e('Server Signature: ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_serversignature(); ?></li>
		<li><?php esc_html_e('Apache Modules: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_apache_modules(); ?></li>
		<li><?php esc_html_e('Server Protocol: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_protocol(); ?></li>
		<li><?php esc_html_e('HTTP Connection: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_http_connection(); ?></li>
		<li><?php esc_html_e('Server Gateway: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_gateway(); ?></li>
		<li><?php esc_html_e('Server Time: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_server_time(); ?></li>
	</ul>
	
	<?php
}

function dashboard_widgets_suite_get_server_name() {
	
	return sanitize_text_field(php_uname());
	
}

function dashboard_widgets_suite_get_server_software() {
	
	return isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field($_SERVER['SERVER_SOFTWARE']) : 'n/a';
	
}

function dashboard_widgets_suite_get_server_version() {
	
	return sanitize_text_field(PHP_INT_SIZE * 8) . esc_html__('Bit', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_server_address() {
	
	return isset($_SERVER['SERVER_ADDR']) ? sanitize_text_field($_SERVER['SERVER_ADDR']) : 'n/a';
	
}

function dashboard_widgets_suite_get_server_port() {
	
	return isset($_SERVER['SERVER_PORT']) ? sanitize_text_field($_SERVER['SERVER_PORT']) : 'n/a';
	
}

function dashboard_widgets_suite_get_document_root() {
	
	return isset($_SERVER['DOCUMENT_ROOT']) ? sanitize_text_field($_SERVER['DOCUMENT_ROOT']) : 'n/a';
	
}

function dashboard_widgets_suite_get_server_load_average() {
	
	if (stristr(PHP_OS, 'win')) {
		
		if (!class_exists('COM')) return;
		
		$wmi = new COM("Winmgmts://");
		$server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
		
		$cpu_num = 0;
		$load_total = 0;
		
		foreach($server as $cpu) {
			$cpu_num++;
			$load_total += $cpu->loadpercentage;
		}
		
		$load = round($load_total / $cpu_num);
		
	} else {
		
		$sys_loadavg = sys_getloadavg();
		$sys_load = (is_array($sys_loadavg)) ? $sys_loadavg : array(0);
		$load = array_sum($sys_load) / count($sys_load);
		
		// $load = $sys_load[0];
		
	}
	
	return round($load, 3);
	
}

function dashboard_widgets_suite_get_server_load() {
	
	if (!function_exists('sys_getloadavg')) return 'n/a';
	
	$avgs = sys_getloadavg();
	
	if (!is_array($avgs)) return 'n/a';
	
	$load = '';
	
	foreach ($avgs as $avg) {
		
		$class = 'dws-color-good';
		
		if     ($avg >= 2 && $avg < 3) $class = 'dws-color-fair';
		elseif ($avg >= 3)             $class = 'dws-color-poor';      
		
		$load .= ' <span class="'. $class .'">'. $avg .'</span>, ';
		
	}
	
	$load = rtrim(trim($load), ',');
	
	return $load;
	
}

function dashboard_widgets_suite_get_hostname() {
	
	return sanitize_text_field($_SERVER['SERVER_NAME']);
	
}

function dashboard_widgets_suite_get_serversignature() {
	
	$disabled  = esc_html__('Disabled', 'dashboard-widgets-suite');
	$signature = $disabled;
	
	if (isset($_SERVER['SERVER_SIGNATURE'])) {
		
		$signature = trim($_SERVER['SERVER_SIGNATURE']) !== '' ? $_SERVER['SERVER_SIGNATURE'] : $disabled;
		
	}
	
	return sanitize_text_field($signature);
	
}

function dashboard_widgets_suite_get_apache_modules() {
	
	$modules = function_exists('apache_get_modules') ? implode(', ', (array) apache_get_modules()) : 'n/a';
	
	return sanitize_text_field($modules);
}

function dashboard_widgets_suite_get_server_protocol() {
	
	return isset($_SERVER['SERVER_PROTOCOL']) ? sanitize_text_field($_SERVER['SERVER_PROTOCOL']) : 'n/a';
	
}

function dashboard_widgets_suite_get_http_connection() {
	
	return isset($_SERVER['HTTP_CONNECTION']) ? sanitize_text_field($_SERVER['HTTP_CONNECTION']) : 'n/a';
	
}

function dashboard_widgets_suite_get_server_gateway() {
	
	return isset($_SERVER['GATEWAY_INTERFACE']) ? $_SERVER['GATEWAY_INTERFACE'] : 'n/a';
	
}

function dashboard_widgets_suite_get_server_time() {
	
	return date('Y-m-d H:i:s');
	
}





/*
	
	DATABASE INFOS
	
*/

function dashboard_widgets_suite_get_database_infos() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('Database Info', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('Database: ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_database_name(); ?></li>
		<li><?php esc_html_e('Version: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_database_version(); ?></li>
		<li><?php esc_html_e('Uptime: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_database_uptime(); ?></li>
		<li><?php esc_html_e('Hostname: ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_database_hostname(); ?></li>
		<li><?php esc_html_e('Charset: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_database_charset(); ?></li>
	</ul>
	
	<?php
}

if (!function_exists('mysql_get_client_info')) {
	
	function mysql_get_client_info() {
		
		// need replacement for PHP 7.0+
		
		echo 'n/a';
		
	}
	
}

function dashboard_widgets_suite_get_database_vars() {
	
	global $wpdb;
	
	if (!$results = $wpdb->get_results('SHOW GLOBAL VARIABLES')) return false;
	
	$mysql_vars = array();
	
	foreach ($results as $result) {
		
		$mysql_vars[$result->Variable_name] = $result->Value;
		
	}
	
	return $mysql_vars;
	
}

function dashboard_widgets_suite_get_database_status() {
	
	global $wpdb;
	
	if (!$results = $wpdb->get_results('SHOW GLOBAL STATUS')) return false;
	
	$mysql_status = array();
	
	foreach ($results as $result) {
		
		$mysql_status[$result->Variable_name] = $result->Value;
		
	}
	
	return $mysql_status;
	
}

function dashboard_widgets_suite_get_database_name() {
	
	$vars = dashboard_widgets_suite_get_database_vars();
	
	return (isset($vars['version_comment']) && !empty($vars['version_comment'])) ? sanitize_text_field($vars['version_comment']) : 'n/a';
	
}

function dashboard_widgets_suite_get_database_version() {
	
	$vars = dashboard_widgets_suite_get_database_vars();
	
	return (isset($vars['version']) && !empty($vars['version'])) ? sanitize_text_field($vars['version']) : 'n/a';
	
}

function dashboard_widgets_suite_get_database_uptime() {
	
	$mysql_uptime = dashboard_widgets_suite_get_database_status();
	
	$uptime = (isset($mysql_uptime['Uptime']) && !empty($mysql_uptime['Uptime'])) ? $mysql_uptime['Uptime'] : 0;
	
	$uptime_seconds = $uptime % 60;
	$uptime_minutes = (int) (($uptime % 3600) / 60);
	$uptime_hours   = (int) (($uptime % 86400) / 3600);
	$uptime_days    = (int) ($uptime / 86400);
	
	if ($uptime_days > 0) {
		
		$uptime_string = "{$uptime_days} days, {$uptime_hours} hours, {$uptime_minutes} minutes, {$uptime_seconds} seconds";
		
	} elseif ($uptime_hours > 0) {
		
		$uptime_string = "{$uptime_hours} hours, {$uptime_minutes} minutes, {$uptime_seconds} seconds";
		
	} elseif ($uptime_minutes > 0) {
		
		$uptime_string = "{$uptime_minutes} minutes, {$uptime_seconds} seconds";
		
	} else {
		
		$uptime_string = "{$uptime_seconds} seconds";
		
	}
	
	return sanitize_text_field($uptime_string);
	
}

function dashboard_widgets_suite_get_database_hostname() {
	
	$vars = dashboard_widgets_suite_get_database_vars();
	
	return (isset($vars['hostname']) && !empty($vars['hostname'])) ? sanitize_text_field($vars['hostname']) : 'n/a';
	
}

function dashboard_widgets_suite_get_database_charset() {
	
	return defined('DB_CHARSET') ? sanitize_text_field(DB_CHARSET) : 'n/a';
	
}





/*
	
	PHP INFOS
	
*/

function dashboard_widgets_suite_get_php_info() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('PHP Info', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('PHP Version: ',                         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_version(); ?></li>
		<li><?php esc_html_e('Zend Engine: ',                         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_zend_engine(); ?></li>
		<li><?php esc_html_e('PHP Memory Limit (runtime / server): ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_memory(); ?></li>
		<li><?php esc_html_e('PHP Memory Usage: ',                    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_memory_usage(); ?></li>
		<li><?php esc_html_e('PHP Peak Memory Usage: ',               'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_peak_memory_usage(); ?></li>
		<li><?php esc_html_e('PHP Post Max Size: ',                   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_post_max_size(); ?></li>
		<li><?php esc_html_e('PHP Upload Max File Size: ',            'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_upload_max_size(); ?></li>
		<li><?php esc_html_e('PHP Execution Time Limit: ',            'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_exec_limit(); ?></li>
		<li><?php esc_html_e('PHP Input Time Limit: ',                'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_input_limit(); ?></li>
		<li><?php esc_html_e('PHP Max Input Vars: ',                  'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_max_vars(); ?></li>
		<li><?php esc_html_e('PHP Include Path: ',                    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_include_path(); ?></li>
		<li><?php esc_html_e('PHP Allow URL File Open: ',             'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_file_open(); ?></li>
		<li><?php esc_html_e('PHP File Uploads: ',                    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_file_uploads(); ?></li>
		<li><?php esc_html_e('Session: ',                             'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_session_info(); ?></li>
		<li><?php esc_html_e('Session Name: ',                        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_session_name(); ?></li>
		<li><?php esc_html_e('Cookie Path: ',                         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_cookie_path(); ?></li>
		<li><?php esc_html_e('Save Path: ',                           'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_save_path(); ?></li>
		<li><?php esc_html_e('Use Cookies: ',                         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_use_cookies(); ?></li>
		<li><?php esc_html_e('Use Only Cookies: ',                    'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_only_cookies(); ?></li>
		<li><?php esc_html_e('Loaded Extensions: ',                   'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_loaded_extensions(); ?></li>
		<li><?php esc_html_e('open_basedir: ',                        'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_open_basedir(); ?></li>
		<li><?php esc_html_e('fsockopen: ',                           'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_fsockopen(); ?></li>
		<li><?php esc_html_e('cURL: ',                                'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_curl(); ?></li>
		<li><?php esc_html_e('SOAP Client: ',                         'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_soap_client(); ?></li>
		<li><?php esc_html_e('Short Open Tag: ',                      'dashboard-widgets-suite'); echo dashboard_widgets_suite_get_php_short_open_tag(); ?></li>
	</ul>
	
	<?php
}

function dashboard_widgets_suite_get_php_version() {
	
	return defined('PHP_VERSION') ? sanitize_text_field(PHP_VERSION) : 'n/a';
	
}

function dashboard_widgets_suite_get_php_zend_engine() {
	
	return sanitize_text_field(zend_version());
	
}

/*
	
	ini_get()     : returns runtime config value (i.e., values set by ini_set(), .htaccess, local php.ini file, and other functions at runtime)
	get_cfg_var() : returns strictly the server php.ini
	
*/
function dashboard_widgets_suite_get_php_memory() {
	
	$memory_runtime = ini_get('memory_limit');
	$memory_server  = get_cfg_var('memory_limit');
	
	return sanitize_text_field($memory_runtime .' / '. $memory_server);
		
}

function dashboard_widgets_suite_get_memory_usage() {
	
	$load = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0;
	
	$limit = (int) ini_get('memory_limit'); // get_cfg_var('memory_limit');
	
	$usage = round($load / $limit * 100, 0);
	
	return $usage .'% <span class="dws-color-grey">('. $load .'M '.  esc_html__('of ', 'dashboard-widgets-suite') . $limit .'M)</span>'; 
	
}

function dashboard_widgets_suite_get_peak_memory_usage() {
	
	$mem = function_exists('memory_get_peak_usage') ? memory_get_peak_usage(true) : 0;
	
	if ($mem < 1024) {
		
		$$memory = $mem .'B'; 
		
	} elseif ($mem < 1048576) {
		
		$memory = round($mem / 1024, 2) .'K';
		
	} else {
		
		$memory = round($mem / 1048576, 2) .'M';
		
	}
	
	return $memory;
	
}

function dashboard_widgets_suite_get_php_post_max_size() {
	
	return sanitize_text_field(ini_get('post_max_size'));
	
}

function dashboard_widgets_suite_get_php_upload_max_size() {
	
	return sanitize_text_field(ini_get('upload_max_filesize'));
	
}

function dashboard_widgets_suite_get_php_exec_limit() {
	
	return sanitize_text_field(ini_get('max_execution_time')) .'s';
		
}

function dashboard_widgets_suite_get_php_input_limit() {
	
	return sanitize_text_field(ini_get('max_input_time')) .'s';
	
}

function dashboard_widgets_suite_get_php_max_vars() {
	
	return sanitize_text_field(ini_get('max_input_vars'));
	
}

function dashboard_widgets_suite_get_php_include_path() {
	
	return sanitize_text_field(ini_get('include_path'));
	
}

function dashboard_widgets_suite_get_php_file_open() {
	
	return ini_get('allow_url_fopen') ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_file_uploads() {
	
	return ini_get('file_uploads') ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_session_info() {
	
	return isset($_SESSION) ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_session_name() {
	
	return sanitize_text_field(ini_get('session.name'));
	
}

function dashboard_widgets_suite_get_php_cookie_path() {
	
	return sanitize_text_field(ini_get('session.cookie_path'));
	
}

function dashboard_widgets_suite_get_php_save_path() {
	
	return sanitize_text_field(ini_get('session.save_path'));
	
}

function dashboard_widgets_suite_get_php_use_cookies() {
	
	return sanitize_text_field(ini_get('session.use_cookies')) ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_only_cookies() {
	
	return sanitize_text_field(ini_get('session.use_only_cookies')) ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_loaded_extensions() {
	
	return sanitize_text_field(implode(', ', get_loaded_extensions()));
	
}

function dashboard_widgets_suite_get_php_open_basedir() {
	
	return sanitize_text_field(ini_get('open_basedir'));
	
}

function dashboard_widgets_suite_get_php_fsockopen() {
	
	return function_exists('fsockopen') ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_curl() {
	
	return function_exists('curl_init') ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_soap_client() {
	
	return class_exists('SoapClient') ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}

function dashboard_widgets_suite_get_php_short_open_tag() {
	
	return ini_get('short_open_tag') ? esc_html__('Enabled', 'dashboard-widgets-suite') : esc_html__('Disabled', 'dashboard-widgets-suite');
	
}





/*
	
	SECURITY INFOS
	
*/

function dashboard_widgets_suite_check_security() { ?>
	
	<p><span class="fa fa-info-circle"></span> <strong><?php esc_html_e('Security Info', 'dashboard-widgets-suite'); ?></strong></p>
	<ul>
		<li><?php esc_html_e('Register Globals: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_register_globals(); ?></li>
		<li><?php esc_html_e('Safe Mode: ',         'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_safe_mode(); ?></li>
		<li><?php esc_html_e('Display Errors: ',    'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_display_errors(); ?></li>
		<li><?php esc_html_e('allow_url_include: ', 'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_url_include(); ?></li>
		<li><?php esc_html_e('allow_url_fopen: ',   'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_url_fopen(); ?></li>
		<li><?php esc_html_e('Server Signature: ',  'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_serversignature(); ?></li>
		<li><?php esc_html_e('WP Unique Keys: ',    'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_uniquekeys(); ?></li>
		<li><?php esc_html_e('mod_security: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_modsecurity(); ?></li>
		<li><?php esc_html_e('open_basedir: ',      'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_openbasedir(); ?></li>
		<li><?php esc_html_e('upload_tmp_dir: ',    'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_upload_tmp_dir(); ?></li>
		<li><?php esc_html_e('expose_php: ',        'dashboard-widgets-suite'); echo dashboard_widgets_suite_check_expose_php(); ?></li>
	</ul>
	
	<?php	
}

function dashboard_widgets_suite_check_register_globals() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('register_globals') ? $enabled_poor : $disabled_good;
	
}

function dashboard_widgets_suite_check_safe_mode() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('safe_mode') ? $enabled_poor : $disabled_good;
	
}

function dashboard_widgets_suite_check_display_errors() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('display_errors') ? $enabled_poor : $disabled_good;
	
}

function dashboard_widgets_suite_check_url_include() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('allow_url_include') ? $enabled_poor : $disabled_good;
	
}

function dashboard_widgets_suite_check_url_fopen() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('allow_url_fopen') ? $enabled_poor : $disabled_good;
	
}

function dashboard_widgets_suite_check_serversignature() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return isset($_SERVER['SERVER_SIGNATURE']) && trim($_SERVER['SERVER_SIGNATURE']) !== '' ? $enabled_poor : $disabled_good;
	
}

function dashboard_widgets_suite_check_uniquekeys() {
	
	$enabled_good  = '<span class="dws-color-good">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_poor = '<span class="dws-color-poor">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	$keys = array('AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY');
	
	foreach ($keys as $key) {
		
		if (defined($key)) {
			
			if ('put your unique phrase here' == constant($key)) return $disabled_poor;
			
		} else {
			
			return $disabled_poor;
			
		}
		
	}
	
	return $enabled_good;
}

function dashboard_widgets_suite_check_modsecurity() {
	
	$enabled_good  = '<span class="dws-color-good">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_poor = '<span class="dws-color-poor">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	$unknown_warn  = '<span class="dws-color-warn">'. esc_html__('Not Installed', 'dashboard-widgets-suite') .'</span>';
	
	if (function_exists('apache_get_modules')) {
		
		$apache_mods = apache_get_modules(); // note: apache_get_modules() does not work if running PHP as CGI
		
		$mod_security = in_array('mod_security', $apache_mods) || in_array('mod_security2', $apache_mods) ? $enabled_good : $disabled_poor;
		
		if (!$mod_security && in_array('security2_module', $apache_mods)) $mod_security = $unknown_warn;
		
	} else {
		
		$mod_security = $unknown_warn;
		
	}
	
	return $mod_security;
	
}

function dashboard_widgets_suite_check_openbasedir() {
	
	$enabled_good  = '<span class="dws-color-good">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_poor = '<span class="dws-color-poor">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('open_basedir') && trim(ini_get('open_basedir')) !== '' ? $enabled_good : $disabled_poor;
	
}

// php.ini : upload_tmp_dir = /tmp
function dashboard_widgets_suite_check_upload_tmp_dir() {
	
	$dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : null;
	
	if (function_exists('sys_get_temp_dir')) {
		
		$dir = sys_get_temp_dir();
		
	} else {
		
		$vars = array('TMP', 'TMPDIR', 'TEMP');
		
		foreach ($vars as $var) {
			
			$tmp = getenv($var);
			
			if (!empty($tmp)) {
				
				$dir = realpath($tmp);
				
			}
			
		}
		
	}
	
	$result = $dir ? '<span class="dws-color-good">'. $dir .'</span>' : '<span class="dws-color-poor">n/a</span>';
	
	return $result;
	
}

// php.ini : expose_php = Off
function dashboard_widgets_suite_check_expose_php() {
	
	$enabled_poor  = '<span class="dws-color-poor">'. esc_html__('Enabled', 'dashboard-widgets-suite') .'</span>';
	$disabled_good = '<span class="dws-color-good">'. esc_html__('Disabled', 'dashboard-widgets-suite') .'</span>';
	
	return ini_get('expose_php') ? $enabled_poor : $disabled_good;
	
}


