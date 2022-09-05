<?php // Dashboard Widgets Suite - Control Panel Widget

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_control_panel() {
	
	global $dws_options_general, $dws_options_notes_user, $dws_options_feed_box, $dws_options_social_box, 
	$dws_options_log_debug, $dws_options_log_error, $dws_options_system_info, $dws_options_list_box, $dws_options_widget_box;
	
	$control_panel = (isset($dws_options_general     ['widget_control_panel']) && $dws_options_general     ['widget_control_panel']) ? $dws_options_general     ['widget_control_panel'] : false;
	$notes_user    = (isset($dws_options_notes_user  ['widget_notes_user'])    && $dws_options_notes_user  ['widget_notes_user'])    ? $dws_options_notes_user  ['widget_notes_user']    : false;
	$feed_box      = (isset($dws_options_feed_box    ['widget_feed_box'])      && $dws_options_feed_box    ['widget_feed_box'])      ? $dws_options_feed_box    ['widget_feed_box']      : false;
	$social_box    = (isset($dws_options_social_box  ['widget_social_box'])    && $dws_options_social_box  ['widget_social_box'])    ? $dws_options_social_box  ['widget_social_box']    : false;
	$list_box      = (isset($dws_options_list_box    ['widget_list_box'])      && $dws_options_list_box    ['widget_list_box'])      ? $dws_options_list_box    ['widget_list_box']      : false;
	$widget_box    = (isset($dws_options_widget_box  ['widget_widget_box'])    && $dws_options_widget_box  ['widget_widget_box'])    ? $dws_options_widget_box  ['widget_widget_box']    : false;
	$system_info   = (isset($dws_options_system_info ['widget_system_info'])   && $dws_options_system_info ['widget_system_info'])   ? $dws_options_system_info ['widget_system_info']   : false;
	$log_debug     = (isset($dws_options_log_debug   ['widget_log_debug'])     && $dws_options_log_debug   ['widget_log_debug'])     ? $dws_options_log_debug   ['widget_log_debug']     : false;
	$log_error     = (isset($dws_options_log_error   ['widget_log_error'])     && $dws_options_log_error   ['widget_log_error'])     ? $dws_options_log_error   ['widget_log_error']     : false;
	
	$fields = array(
		'dws_options_general'     => array($control_panel, 'widget_control_panel', esc_html__('Control Panel', 'dashboard-widgets-suite')), 
		'dws_options_notes_user'  => array($notes_user,    'widget_notes_user',    esc_html__('User Notes',    'dashboard-widgets-suite')), 
		'dws_options_feed_box'    => array($feed_box,      'widget_feed_box',      esc_html__('Feed Box',      'dashboard-widgets-suite')), 
		'dws_options_social_box'  => array($social_box,    'widget_social_box',    esc_html__('Social Box',    'dashboard-widgets-suite')), 
		'dws_options_list_box'    => array($list_box,      'widget_list_box',      esc_html__('List Box',      'dashboard-widgets-suite')), 
		'dws_options_widget_box'  => array($widget_box,    'widget_widget_box',    esc_html__('Widget Box',    'dashboard-widgets-suite')), 
		'dws_options_system_info' => array($system_info,   'widget_system_info',   esc_html__('System Info',   'dashboard-widgets-suite')),	
		'dws_options_log_debug'   => array($log_debug,     'widget_log_debug',     esc_html__('Debug Log',     'dashboard-widgets-suite')), 
		'dws_options_log_error'   => array($log_error,     'widget_log_error',     esc_html__('Error Log',     'dashboard-widgets-suite')), 
	); ?>
	
	<div id="dws-control-panel" class="dws-dashboard-widget dws-control-panel">
		<form method="post" action="">
			
			<?php if (current_user_can('manage_options')) : ?>
			
			<p>
				<span class="fa fa-cog"></span> 
				<a href="<?php echo admin_url('options-general.php?page=dashboard_widgets_suite'); ?>"><?php esc_html_e('Customize Widgets &rsaquo;', 'dashboard-widgets-suite'); ?></a>
			</p>
			
			<?php endif; ?>
			
			<ul>
				
				<?php foreach ($fields as $key => $value) : 
					
					$enable  = isset($value[0]) ? $value[0] : false;
					$widget  = isset($value[1]) ? $value[1] : null;
					$label   = isset($value[2]) ? $value[2] : '';
					
					$name    = isset($key) ? $key .'['. $widget .']' : '';
					
					$checked = checked($enable, 1, false);
					
					if (isset($value) && $value) : ?>
						
						<li>
							<input name="<?php echo $name; ?>" value="1" <?php echo $checked; ?> type="checkbox"> 
							<label for="<?php echo $name; ?>"><?php echo $label; ?></label>
						</li>
						
					<?php endif;
					
				endforeach; ?>
				
			</ul>
			
			<input class="button button-secondary" type="submit" value="<?php esc_attr_e('Save Changes', 'dashboard-widgets-suite'); ?>">
			
			<?php wp_nonce_field('dws-control-panel-nonce', 'dws-control-panel[nonce]', false); ?>
			
		</form>
	</div>
	
	<?php do_action('dashboard_widgets_suite_control_panel');
	
}
