<?php // Dashboard Widgets Suite - Display Settings

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_menu_pages() {
	
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
	add_options_page('Widgets Suite', 'Dashboard Widgets', 'manage_options', 'dashboard_widgets_suite', 'dashboard_widgets_suite_display_settings');
	
}

function dashboard_widgets_suite_get_tabs() {
	
	$tabs = array(
		'tab1' => esc_html__('General Settings', 'dashboard-widgets-suite'), 
		'tab2' => esc_html__('User Notes',       'dashboard-widgets-suite'), 
		'tab3' => esc_html__('Feed Box',         'dashboard-widgets-suite'), 
		'tab4' => esc_html__('Social Box',       'dashboard-widgets-suite'), 
		'tab5' => esc_html__('List Box',         'dashboard-widgets-suite'), 
		'tab6' => esc_html__('Widget Box',       'dashboard-widgets-suite'), 
		'tab7' => esc_html__('System Info',      'dashboard-widgets-suite'), 
		'tab8' => esc_html__('Debug Log',        'dashboard-widgets-suite'), 
		'tab9' => esc_html__('Error Log',        'dashboard-widgets-suite'),
	);
	
	return $tabs;
	
}

function dashboard_widgets_suite_display_settings() { 
	
	$tab_active = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'tab1'; 
	
	$tab_href = admin_url('options-general.php?page=dashboard_widgets_suite');
	
	$tab_names = dashboard_widgets_suite_get_tabs();
	
	?>
	
	<div class="wrap wrap-<?php echo $tab_active; ?>">
		<h1><span class="fa fa-pad fa-th"></span> <?php echo DWS_NAME; ?> <span class="dws-version"><?php echo DWS_VERSION; ?></span></h1>
		<h2 class="nav-tab-wrapper">
			
			<?php 
				
				foreach ($tab_names as $key => $value) {
					
					$active = ($tab_active === $key) ? ' nav-tab-active' : '';
					
					echo '<a href="'. $tab_href .'&tab='. $key .'" class="nav-tab nav-'. $key . $active .'">'. $value .'</a>';
					
				}
				
			?>
			
		</h2>
		<form method="post" action="options.php">
			
			<?php
				
				if ($tab_active === 'tab1') {
					
					settings_fields('dws_options_general');
					do_settings_sections('dws_options_general');
				
				} elseif ($tab_active === 'tab2') {
					
					settings_fields('dws_options_notes_user');
					do_settings_sections('dws_options_notes_user');
					
				} elseif ($tab_active === 'tab3') {
					
					settings_fields('dws_options_feed_box');
					do_settings_sections('dws_options_feed_box');
					
				} elseif ($tab_active === 'tab4') {
					
					settings_fields('dws_options_social_box');
					do_settings_sections('dws_options_social_box');
					dashboard_widgets_suite_section_social_box_note();
					
				} elseif ($tab_active === 'tab5') {
					
					settings_fields('dws_options_list_box');
					do_settings_sections('dws_options_list_box');
					
				} elseif ($tab_active === 'tab6') {
					
					settings_fields('dws_options_widget_box');
					do_settings_sections('dws_options_widget_box');
					
				} elseif ($tab_active === 'tab7') {
					
					settings_fields('dws_options_system_info');
					do_settings_sections('dws_options_system_info');
					
				} elseif ($tab_active === 'tab8') {
					
					settings_fields('dws_options_log_debug');
					do_settings_sections('dws_options_log_debug');
					
				} elseif ($tab_active === 'tab9') {
					
					settings_fields('dws_options_log_error');
					do_settings_sections('dws_options_log_error');
				
				}
				
				submit_button();
				
			?>
			
			<?php
				
				if ($tab_active !== 'tab1') :
					
					$info1 = esc_attr__('Banhammer WordPress security plugin',                  'dashboard-widgets-suite');
					$info2 = esc_attr__('USP Pro - Unlimited Front-end forms',                  'dashboard-widgets-suite');
					$info3 = esc_attr__('WordPress Themes In Depth - PDF/eBook',                'dashboard-widgets-suite');
					$info4 = esc_attr__('Wizard&rsquo;s SQL Recipes for WordPress - PDF/eBook', 'dashboard-widgets-suite');
					
					?>
					
					<hr>
					<div class="dws-recommended">
						<h2 class="dws-noicon"><span class="fa fa-pad fa-coffee"></span> <?php esc_html_e('WP Resources', 'dashboard-widgets-suite'); ?></h2>
						<p><?php esc_html_e('Check out more WordPress resources from this developer:', 'dashboard-widgets-suite'); ?></p>
						<div class="dws-resources">
							<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/banhammer/" title="<?php echo $info1; ?>">
								<img src="<?php echo DWS_URL .'img/250x250-banhammer-pro.jpg'; ?>" width="125" height="125" alt="<?php echo $info1; ?>">
							</a> 
							<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro/" title="<?php echo $info2; ?>">
								<img src="<?php echo DWS_URL .'img/250x250-usp-pro.jpg'; ?>" width="125" height="125" alt="<?php echo $info2; ?>">
							</a> 
							<a target="_blank" rel="noopener noreferrer" href="https://wp-tao.com/wordpress-themes-book/" title="<?php echo $info3; ?>">
								<img src="<?php echo DWS_URL .'img/250x250-wp-themes-in-depth.jpg'; ?>" width="125" height="125" alt="<?php echo $info3; ?>">
							</a>
							<a target="_blank" rel="noopener noreferrer" href="https://books.perishablepress.com/downloads/wizards-collection-sql-recipes-wordpress/" title="<?php echo $info4; ?>">
								<img src="<?php echo DWS_URL .'img/250x250-wizards-sql.jpg'; ?>" width="125" height="125" alt="<?php echo $info4; ?>">
							</a>
						</div>
					</div>
					
					<?php 
					
				endif;
				
			?>
			
		</form>
	</div>
	
<?php }
