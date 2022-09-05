<?php


if ( ! class_exists( 'WP_Analytify_Pro' ) ) {

	/**
	 * Main WP_Analytify class
	 *
	 * @since       1.0.0
	 */
	class WP_Analytify_Pro extends WP_Analytify_Pro_Base {

		/**
		 * @var         WP_Analytify $instance The one true WP_Analytify
		 * @since       1.2.2
		 */
		private static $instance;

		// protected $settings;
		// protected $transient_timeout;

		public $token  = false;
		public $client = null;

		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.2.2
		 * @return      object self::$instance The one true WP_Analytify
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Analytify_Pro();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}
			return self::$instance;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       2.0.16
		 * @return      void
		 */
		private function setup_constants() {

			//Setting Global Values

			$this->define( 'ANALYTIFY_PRO_ID', 10 );
		}

		/**
		 * Define constant if not already set
		 *
		 * @since 1.2.4
		 * @param  string      $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @since 1.2.4
		 * @param string $type ajax, frontend or admin
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
				return is_admin();
				case 'ajax' :
				return defined( 'DOING_AJAX' );
				case 'cron' :
				return defined( 'DOING_CRON' );
				case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.2.2
		 * @return      void
		 */
		private function includes() {

			if ( ! class_exists( 'ANALYTIFY_SL_Plugin_Updater' ) ) {
				// load plugin updater
				include_once( ANALYTIFY_PRO_ROOT_PATH . '/lib/ANALYTIFY_SL_Plugin_Updater.php' );
			}
			if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				include_once( ANALYTIFY_PRO_ROOT_PATH . '/lib/EDD_SL_Plugin_Updater.php' );
			}

			include_once WP_PLUGIN_DIR . '/wp-analytify-pro/classes/class.upgrade-pro.php';
			include_once WP_PLUGIN_DIR . '/wp-analytify-pro/classes/class-analytify-pro-update-routine.php';
			include_once WP_PLUGIN_DIR . '/wp-analytify-pro/classes/analytifypro_ajax.php';
			include_once WP_PLUGIN_DIR . '/wp-analytify-pro/classes/class-analytify-pro-blocks.php';
			include_once WP_PLUGIN_DIR . '/wp-analytify-pro/inc/modules/analytify-modules.php';
			include_once WP_PLUGIN_DIR . '/wp-analytify-pro/inc/wpa-core-functions.php';
		}

		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.2.2
		 * @return      void
		 */
		private function hooks() {

			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ), 10, 1 );
			add_action( 'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ), 10, 1 );

			add_action( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_action( 'admin_init', array( $this, '_save_version' ) );

			add_action( 'admin_init', array( $this, '_plugin_updater' ), 0 );
			add_action( 'admin_notices', array( $this, '_admin_notices') );

			// initiating ShortCodes
			add_action( 'admin_head', array( $this, 'analytify_shortcode_button' ) );

			add_filter( 'mce_css', array( $this, 'analytify_mce_css' ) );

			add_shortcode( 'analytify-stats', array( $this, 'analytify_stats_shortcode' ) );

			add_shortcode( 'analytify-worldmap', array( $this, 'analytify_worldmap_shortcode' ) );

			add_action( 'wp_ajax_analytify_advanced_shortcode', array( $this, 'analytify_shortcode_view' ) );

			add_action( 'admin_init', array( $this, 'wp_analytify_register_option' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// if( get_option( 'analytify_disable_front') == 0 ) {

			$_disable_front = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'disable_front_end', 'wp-analytify-front', '' );
			//var_dump($_disable_front);
			if ( ! empty( $_disable_front ) && 'off' == $_disable_front ) {

				add_filter( 'the_content', array( $this, 'get_single_front_analytics' ) );
			}

			add_action( 'wp_ajax_pa_get_online_data', array( $this, 'pa_realtime_data_get' ) );
			/*
             *   Grab online visitors for frontend widget
			 */
			add_action( 'wp_ajax_nopriv_pa_get_online_data', array( $this, 'pa_realtime_data_get' ) );

			// Show links at post rows
			// add_filter('post_row_actions', array( $this, 'post_rows_stats' ), 10, 2);
			// add_filter('page_row_actions', array( $this, 'post_rows_stats' ), 10, 2);
			// add_action('post_submitbox_minor_actions', array( $this, 'post_submitbox_stats_action' ), 10, 1);

			add_action( 'wp_analytify_load_mobile_stats', array( $this, 'load_mobile_stats' ), 10, 3 );
			add_filter( 'wp_analytify_tabs', array( $this, 'wp_analytify_pro_tabs' ) );
			add_action( 'wp_analytify_license_tab', array( $this, 'license_tab_content' ) );

			// Add after_plugin_row... action for pro plugin
			// add_action( 'after_plugin_row_wp-analytify-pro/wp-analytify-pro.php', array( $this, 'wpa_plugin_row'), 11, 2 );
			add_filter( 'wp_analytify_pro_setting_tabs' , array( $this, 'wp_analytify_pro_setting_tabs' ), 10 , 1 );
			add_filter( 'wp_analytify_pro_accordion_setting' , array( $this, 'wp_analytify_pro_accordion_setting' ), 10 , 1 );
			add_filter( 'wp_analytify_pro_setting_fields', array( $this, 'wp_analytify_pro_setting_fields' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'pa_front_styles' ) );
			add_action( 'wp_analytify_stats_under_post' , array( $this, 'wp_analytify_stats_under_post' ), 10 , 4 );
			add_action( 'wp_ajax_wpanalytifypro_activate_license', array( $this, 'ajax_activate_license' ) );
			add_action( 'wp_ajax_wpanalytifypro_check_license', array( $this, 'ajax_check_license' ) );
			add_action( 'wp_ajax_analytify_license_deactivate', array( $this, 'analytify_license_deactivate' ) );
			add_action( 'anlytify_pro_support_tab', array( $this, 'anlytify_pro_support' ) );
			add_action( 'load-analytify_page_analytify-settings', array( $this, 'load_pro_settings_assets' ) );
			add_filter( 'wpanalytify_data', array( $this, '_wpanalytify_data' ) , 10, 1 );
			add_filter( 'free-pro-features', array( $this, 'pro_feature_box' ),  10, 1 );

			add_action( 'wp_analytify_view_miscellaneous_error', array( $this, 'load_error_stats' ) , 10 , 3 );

			add_action( 'wp_analytify_view_real_time_stats', array( $this, 'include_realtime_stats' ) );
			add_action( 'wp_analytify_view_compare_stats', array( $this, 'include_compare_stats' ), 10, 4 );

			add_action( 'wp_analytify_view_ajax_error', array( $this, 'include_ajax_error'), 10, 5 );
			add_action( 'wp_analytify_view_404_error', array( $this, 'include_404_error'), 10, 5 );
			add_action( 'wp_analytify_view_javascript_error', array( $this, 'include_javascript_error'), 10, 5 );

			add_action( 'show_detail_dashboard_content', array( $this, 'get_detail_dashboard_content' ) );
			add_action( 'analytify_dashboad_dropdown', array( $this, 'dashboad_dropdown' ) );

			add_filter( 'analytify_admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 10, 1);
			add_action( 'init', array( $this, 'generate_csv' ) );
			add_action( 'analytify_after_top_page_text', array( $this, 'top_page_text' ) );
			add_action( 'analytify_after_top_keyword_text', array( $this, 'top_keyword_text' ) );
			add_action( 'analytify_after_top_social_media_text', array( $this, 'top_social_media_text' ) );
			add_action( 'analytify_after_top_reffers_text', array( $this, 'top_reffers_text' ) );
			add_action( 'analytify_after_top_page_stats_text', array( $this, 'top_page_stats_text' ) );
			add_action( 'analytify_after_top_country_text', array( $this, 'after_top_country_text' ) );
			add_action( 'analytify_after_top_city_text', array( $this, 'after_top_city_text' ) );
			add_action( 'analytify_after_top_browser_text', array( $this, 'after_top_browser_text' ) );
			add_action( 'analytify_after_top_operating_system_text', array( $this, 'after_top_operating_system_text' ) );
			add_action( 'analytify_after_top_mobile_device_text', array( $this, 'after_top_mobile_device_text' ) );
		}
		
		/**
		 * Display notices
		 *
		 * @since 1.3
		 */
		function _admin_notices() {

			// Return if current page is not analytify page.
			$screen = get_current_screen();
			if ( 'analytify-dashboard' !== $screen->parent_base ) { return; }

			$profile_id     =   get_option( "pt_webprofile" );
			$acces_token    =   get_option( "post_analytics_token" );

			// Add notices script.
			add_action( 'admin_enqueue_scripts', 'pa_notices_scripts' );

			if ( ! $this->get_license_key() &&  ! $this->get_license_status() ) {
				// echo sprintf( esc_html__( '%1$s %2$s %3$s Activate Your License %4$s &mdash; Please %5$senter your license key%6$s to enable support and plugin updates. %7$s %8$s', 'wp-analytify-pro' ), '<div class="error notice is-dismissible">', '<p>', '<b>', '</b>', '<a style="text-decoration:none" class="wp-analytify-license-notice" href=' . menu_page_url ( 'analytify-settings', false ) . '#wp-analytify-license>', '</a>','</p>', '</div>' );

				$message = sprintf( esc_html__( '%1$s Activate Your License %2$s &mdash; Please %3$senter your license key%4$s to enable support and plugin updates.', 'wp-analytify-pro' ), '<b>', '</b>', '<a style="text-decoration:none" class="wp-analytify-license-notice" href="' . menu_page_url ( 'analytify-settings', false ) . '#wp-analytify-license">', '</a>' );
				
				wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
			}

			else if ( ! $this->get_license_status() ) {
				echo sprintf( esc_html__( '%1$s %2$s %3$s Activate Your License %4$s &mdash; Please click on %5$s Activate License button %6$s to validate your license. %7$s %8$s', 'wp-analytify-pro' ), '<div class="error notice is-dismissible">', '<p>', '<b>', '</b>', '<a style="text-decoration:none" class="wp-analytify-license-notice" href="' . menu_page_url ( 'analytify-settings', false ) . '#wp-analytify-license">', '</a>','</p>', '</div>' );

				$message = sprintf( esc_html__( '%1$s Activate Your License %2$s &mdash; Please click on %3$s Activate License button key%4$s to validate your license.', 'wp-analytify-pro' ), '<b>', '</b>', '<a style="text-decoration:none" class="wp-analytify-license-notice" href="' . menu_page_url ( 'analytify-settings', false ) . '#wp-analytify-license">', '</a>' );
				
				wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
			}

			else if ( $this->is_license_expired() ) {
				// echo sprintf( esc_html__( '%1$s %2$s %3$s License Expired %4$s &mdash; Upgrade your license to enable support and plugin updates. Would you please checkout %5$s what we have improved %6$s so far ? %7$s %8$s', 'wp-analytify-pro' ), '<div class="error notice is-dismissible">', '<p>', '<b>', '</b>', '<a style="text-decoration:none" target="_blank" href="https://analytify.io/changelog/?utm_campaign=WPAnalytifyPro+licensing&utm_medium=link&utm_source=WPAnalytifyPro+LicenseExpired&utm_content=activate-license-notice">', '</a>','</p>', '</div>' );

				$message = sprintf( esc_html__( '%1$s License Expired %2$s &mdash; Upgrade your license to enable support and plugin updates. Would you please checkout %3$s what we have improved %4$s so far ?', 'wp-analytify-pro' ), '<b>', '</b>', '<a style="text-decoration:none" target="_blank" href="https://analytify.io/changelog/?utm_campaign=WPAnalytifyPro+licensing&utm_medium=link&utm_source=WPAnalytifyPro+LicenseExpired&utm_content=activate-license-notice">', '</a>' );
				
				wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
			}

			else if ( $this->get_license_status() === 'invalid' ) {
				// echo sprintf( esc_html__( '%1$s %2$s %3$s Invalid License %4$s &mdash; Please %5$senter a valid license key%6$s to enable support and plugin updates. %7$s %8$s', 'wp-analytify-pro' ), '<div class="error notice is-dismissible">', '<p>', '<b>', '</b>', '<a style="text-decoration:none" class="wp-analytify-license-notice" href=' . menu_page_url ( 'analytify-settings', false ) . '#wp-analytify-license>', '</a>','</p>', '</div>' );

				$message = sprintf( esc_html__( '%1$s Invalid License %2$s &mdash; Please %3$senter a valid license key%4$s to enable support and plugin updates.', 'wp-analytify-pro' ), '<b>', '</b>', '<a style="text-decoration:none" class="wp-analytify-license-notice" href="' . menu_page_url ( 'analytify-settings', false ) . '#wp-analytify-license">', '</a>' );
				
				wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
			}

			if ( $this->get_license_status() !== 'valid' ) {
				echo $this->is_update_available();
			}

		}


		function dashboad_dropdown() { ?>

			<div class="analytify_selected_dashboard_field"><?php _e( 'Dashboards', 'wp-analytify-pro' ) ?></div>
			<ul class="analytify_dashboards_list">

				<li><a href="<?php echo admin_url( 'admin.php?page=analytify-dashboard&show=detail-realtime' ) ?>"><?php _e( 'REAL TIME', 'wp-analytify-pro' ) ?></a></li>
				<li><a href="<?php echo admin_url( 'admin.php?page=analytify-dashboard&show=detail-demographic' ) ?>"><?php _e( 'Demographics', 'wp-analytify-pro' ) ?></a></li>
				<?php do_action( 'analytify_dashboad_dropdown_option' ) ?>
				<li><a href="<?php echo admin_url( 'admin.php?page=analytify-dashboard&show=search-terms' ) ?>"><?php _e( 'Search Terms', 'wp-analytify-pro' ) ?></a></li>

			</ul>

		<?php }


		/**
		 * Show RealTime Stats on Dashboard Page
		 *
		 * @return output
		 */
		function include_realtime_stats() {

			$wp_analytify = $GLOBALS['WP_ANALYTIFY'];

			// Include RealTime Stats
			include ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/realtime-stats.php';
			fetch_realtime_stats( $wp_analytify );
		}


		function include_compare_stats( $start_date, $end_date, $compare_start_date, $compare_end_date ) {
			// Include Visitors and Views Comparison Statistics

			$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			$dashboard_profile_ID = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
			$year_dimensions = 'ga:yearMonth';
			$is_three_month = false;

			$this_month_start_date         = $start_date;
			$this_month_end_date           = $end_date;

			$previous_month_start_date     = $compare_start_date;
			$previous_month_end_date       = $compare_end_date;

			$this_year_start_date          =  $start_date ;
			$this_year_end_data            =  $end_date ;

			$previous_year_start_date     = date( 'Y-m-d', strtotime( $start_date . ' -1 year') );
			$previous_year_end_date       = date( 'Y-m-d', strtotime( $end_date . ' -1 year') );


			$date1 = date_create( $this_year_start_date );
			$date2 = date_create( $this_year_end_data );
			$diff  = date_diff( $date2, $date1 );

			// If difference less than 3 months, get data on date base.
			if ( $diff->format( '%a' ) < 90 ) {
				$year_dimensions = 'ga:date';
				$is_three_month = true;
			}

			$this_month_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:users,ga:pageviews', $this_month_start_date, $this_month_end_date, 'ga:date', false, false, 1000, 'show-this-month-stats' );

			$previous_month_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:users,ga:pageviews', $previous_month_start_date, $previous_month_end_date, 'ga:date', false, false, 1000, 'show-previous-month-stats' );

			$this_year_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:users,ga:pageviews', $this_year_start_date, $this_year_end_data, $year_dimensions, false, false, 10000, 'show-this-year-stats' );

			$previous_year_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:users,ga:pageviews', $previous_year_start_date, $previous_year_end_date, $year_dimensions, false, false, 10000, 'show-previous-year-stats' );

			if ( isset( $this_month_stats['rows'] ) ) {
				include ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/main-comparison-stats.php';
				fetch_visitors_views_comparison( $wp_analytify, $this_month_stats, $previous_month_stats, $this_year_stats, $previous_year_stats, $is_three_month, $this_month_start_date, $this_month_end_date, $previous_month_start_date, $previous_month_end_date, $this_year_start_date, $this_year_end_data, $previous_year_start_date, $previous_year_end_date  );
			}


		}

		/**
		 * Add file for details dashboard.
		 *
		 * @since 2.0.0
		 */
		function get_detail_dashboard_content() {

			if ( 'detail-realtime' === $_GET['show'] ) {
				include_once ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/detail-realtime-stats.php';
				pa_include_detail_realtime();
			} elseif ( 'detail-demographic' === $_GET['show'] ) {
				include_once ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/detail-demographic-stats.php';
			} elseif ( 'search-terms' === $_GET['show'] ) {
				include_once ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/detail-search-terms-stats.php';
			}

		}

		function include_ajax_error( $start_date, $end_date, $dashboard_profile_ID, $report_url, $report_date_range ) {

				if( 'on' === $this->settings->get_option( 'ajax_error_track', 'wp-analytify-advanced' ) ):
				?>

				<div class="analytify_general_status analytify_status_box_wraper">
					<div class="analytify_status_header">
						<h3>
							<?php esc_html_e( 'Top Ajax Errors', 'wp-analytify-pro' ); ?>
							<?php $ajax_url = 'https://analytics.google.com/analytics/web/#/report/content-event-events/' ; ?>
							<a href="<?php echo $ajax_url . $report_url . $report_date_range . '&explorer-segmentExplorer.segmentId=analytics.eventLabel&_r.drilldown=analytics.eventCategory:Ajax%20Error&explorer-table.plotKeys=%5B%5D/' ?>" target="_blank" class="analytify_tooltip"><span class="analytify_tooltiptext"><?php _e( 'View All Ajax Errors', 'wp-analytify' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
						</h3>
						<span class="analytify_top_page_detials analytify_tp_btn">
							<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-ajax">
								<span class="analytify_tooltiptext">Export Top Ajax Errors</span>
							</a>
							<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
						</span>
					</div>
					<div class="analytify_status_body stats_loading">
						<script>
						//<![CDATA[

							jQuery( function($) {
								$.get(ajaxurl, { action:'analytify_load_default_ajax_error', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

									$('.analytify_ajax_error_boxes_wraper').html(data).parent().removeClass("stats_loading");

								});
							});
						//]]>
						</script>
						<div class="analytify_ajax_error_boxes_wraper"> </div>
						</div>
						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php esc_html_e( 'Ajax errors', 'wp-analytify-pro' ); ?></span>
						</div>

				</div>

				<?php
				endif;
		}

		public function include_404_error( $start_date, $end_date, $dashboard_profile_ID, $report_url, $report_date_range ) {
			if( 'on' === $this->settings->get_option( '404_page_track', 'wp-analytify-advanced' ) ):
			?>

			<div class="analytify_general_status analytify_status_box_wraper">
				<div class="analytify_status_header">
					<h3>
						<?php esc_html_e( 'Top 404 Pages Errors', 'wp-analytify-pro' ); ?>
						<?php $four_o_four_url = 'https://analytics.google.com/analytics/web/#/report/content-event-events/' ; ?>
						<a href="<?php echo $four_o_four_url . $report_url . $report_date_range . 'explorer-segmentExplorer.segmentId=analytics.eventLabel&_r.drilldown=analytics.eventCategory:404%20Error&explorer-table.plotKeys=%5B%5D&explorer-segmentExplorer.segmentId=analytics.eventLabel/' ?>" target="_blank" class="analytify_tooltip"><span class="analytify_tooltiptext"><?php _e( 'View All 404 Errors', 'wp-analytify' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
					</h3>
					<span class="analytify_top_page_detials analytify_tp_btn">
						<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-404">
							<span class="analytify_tooltiptext">Export Top 404 Errors</span>
						</a>
						<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
					</span>
				</div>
				<div class="analytify_status_body stats_loading">
					<script>
					//<![CDATA[

						jQuery( function($) {
							$.get(ajaxurl, { action:'analytify_load_default_404_error', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

								$('.analytify_404_error_boxes_wraper').html(data).parent().removeClass("stats_loading");

							});
						});
					//]]>
					</script>
					<div class="analytify_404_error_boxes_wraper"> </div>
					</div>
					<div class="analytify_status_footer">
						<span class="analytify_info_stats"><?php esc_html_e( '404 errors', 'wp-analytify-pro' ); ?></span>
					</div>
			</div>

			<?php
			endif;
		}

		public function include_javascript_error( $start_date, $end_date, $dashboard_profile_ID, $report_url, $report_date_range ) {

			if ( 'on' == $this->settings->get_option( 'javascript_error_track', 'wp-analytify-advanced' )  ) :
				?>

				<div class="analytify_general_status analytify_status_box_wraper">
					<div class="analytify_status_header">
						<h3>
							<?php esc_html_e( 'Top Javascript Errors', 'wp-analytify-pro' ); ?>
							<?php $referral_url = 'https://analytics.google.com/analytics/web/#/report/content-event-events/' ; ?>
							<a href="<?php echo $referral_url . $report_url . $report_date_range . '&explorer-segmentExplorer.segmentId=analytics.eventAction&_r.drilldown=analytics.eventCategory:JavaScript%20Error&explorer-table.plotKeys=%5B%5D&explorer-table.secSegmentId=analytics.eventLabel' ?>" target="_blank" class="analytify_tooltip"><span class="analytify_tooltiptext"><?php _e( 'View All JavaScript Errors', 'wp-analytify' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
						</h3>
						<span class="analytify_top_page_detials analytify_tp_btn">
							<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-js-error">
								<span class="analytify_tooltiptext">Export Top JavaScript Errors</span>
							</a>
							<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
						</span>
					</div>
					<div class="analytify_status_body stats_loading">
						<script>
						//<![CDATA[

							jQuery( function($) {
								$.get(ajaxurl, { action:'analytify_load_default_javascript_error', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

									$('.analytify_javascript_error_boxes_wraper').html(data).parent().removeClass("stats_loading");

								});
							});
						//]]>
						</script>
						<div class="analytify_javascript_error_boxes_wraper"> </div>
					</div>
						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php esc_html_e( 'JavaScript errors', 'wp-analytify-pro' ); ?></span>
						</div>
					</div>
				<?php
			endif;

		}

		/**
		 * Load Errors Stats
		 *
		 * @param   $start_date
		 * @param   $end_date
		 * @param   $dashboard_profile_ID
		 * @return     error box widgets
		 */
		public function load_error_stats( $start_date, $end_date, $dashboard_profile_ID ) {

			// Show Ajax Error
			if ( 'on' == $this->settings->get_option( 'ajax_error_track', 'wp-analytify-advanced' )  ) {

				?>

				<div id="wp-analytify-ajax-error-stats-box"><img class="dashboard-loader"  class="dashboard-loader" src="<?php ANALYTIFY_PLUGIN_URL . 'assets/images/loading.gif' ?>"></div>
				<script>
				//<![CDATA[

					jQuery( function($) {
						$.get(ajaxurl, { action:'analytify_load_ajax_error', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

							$('#wp-analytify-ajax-error-stats-box').html(data);
						});
					});
				//]]>
				</script>

				<?php
			}

			// 404
			if ( 'on' == $this->settings->get_option( '404_page_track', 'wp-analytify-advanced' )  ) {

				?>

				<div id="wp-analytify-404-page-error-stats-box"><img class="dashboard-loader"  class="dashboard-loader" src="<?php ANALYTIFY_PLUGIN_URL . 'assets/images/loading.gif' ?>"></div>
				<script>
				//<![CDATA[

					jQuery( function($) {
						$.get(ajaxurl, { action:'analytify_load_404_error', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

							$('#wp-analytify-404-page-error-stats-box').html(data);
						});
					});
				//]]>
				</script>

				<?php
			}

			// javascript Errors
			if ( 'on' == $this->settings->get_option( 'javascript_error_track', 'wp-analytify-advanced' )  ) {

				?>

				<div id="wp-analytify-javascript-error-stats-box"><img class="dashboard-loader"  class="dashboard-loader" src="<?php ANALYTIFY_PLUGIN_URL . 'assets/images/loading.gif' ?>"></div>
				<script>
				//<![CDATA[

					jQuery( function($) {
						$.get(ajaxurl, { action:'analytify_load_javascript_error', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

							$('#wp-analytify-javascript-error-stats-box').html(data);
						});
					});
				//]]>
				</script>

				<?php

			}

		}

		/**
		 * Add sidebar on settings page
		 *
		 * @param  string $inner_html
		 * @since  2.0
		 */
		function pro_feature_box( $inner_html ) {

			$inner_html = '
                            <div class="postbox-container side">
                                    <div class="metabox-holder">

                                        <div class="grids_auto_size wpa_side_box" style="width: 100%;">
                                        <div class="grid_title cen"> ' . __( 'Thank you!', 'wp-analytify-pro' ) . ' </div>

                                            <div class="grid_footer cen" style="background-color:white;">
																							' . __( 'We appreciate you to upgrade to Pro version and supporting the development of this product.', 'wp-analytify-pro' ) . '
                                            </div>
                                        </div>
                                        <div class="grids_auto_size wpa_side_box" style=" width: 100%;">
                                            <div class="grid_footer cen">
																						' . __( 'made with ♥ by', 'wp-analytify-pro' ) . '
                                             <a href="http://wpbrigade.com" title="WPBrigade | A Brigade of WordPress Developers." />WPBrigade</a>
                                            </div>
                                        </div>
                                    </div>
							</div>';

			return $inner_html;
		}

		/**
		 * This filter is to hook in localize data to use in JS
		 *
		 * @param  [array] $data data strings array
		 * @return [array]       merge array and return new one
		 */
		function _wpanalytify_data( $data ) {

			$pro_data = array(
							'has_license' => esc_html( $this->get_license_key() == '' ? '0' : '1' ),
						);

			return array_merge( $data, $pro_data );
		}

		/**
		 * Loading scripts js for the Pro at the backend
		 */
		public function admin_scripts( $page ) {

			wp_enqueue_script( 'wp-analytify-pro', plugins_url( 'assets/js/wp-analytify-pro.js', dirname( __FILE__ ) ), false, ANALYTIFY_PRO_VERSION );

			wp_localize_script( 'wp-analytify-pro', 'Analytify', array(
				 'ajaxurl' => admin_url( 'admin-ajax.php' ),
				 'exportUrl' => add_query_arg( array( 'action' => 'analytify_export' ), admin_url( 'admin-ajax.php' ) ),
				 'export_nonce' => wp_create_nonce( 'analytify_export_nonce' ),
		 	) );

		}


		/**
		 * Loading scripts js for the Pro at the frontend
		 */
		public function front_scripts( $page ) {

			// Google Map Api
    	wp_register_script( 'jsapi', 'https://www.google.com/jsapi', null, null, true );

			// if( get_option( 'analytify_disable_front') == 0 ) {
			if ( 'off' == $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'disable_front_end', 'wp-analytify-front', '' ) ) {

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'analytify-script', plugins_url( 'assets/js/script.js', dirname( __FILE__ ) ), array('jquery'), ANALYTIFY_PRO_VERSION );
				wp_localize_script( 'analytify-script', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

			}

		}

		/**
		 * Tab content in Help Tab for Pro users.
		 *
		 * @since 2.0
		 */
		public function anlytify_pro_support() {

			include_once ANALYTIFY_PRO_ROOT_PATH . '/views/help.php';
		}

		public function pa_front_styles( $page ) {


			if( 'off' == $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'disable_front_end', 'wp-analytify-front', '' ) ) {
				// wp_enqueue_style( 'analytify-font-awesome', plugins_url('assets/css/font-awesome.min.css', __FILE__),false,ANALYTIFY_PRO_VERSION);

				wp_enqueue_style( 'front-end-style', plugins_url('assets/css/frontend_styles.css', dirname( __FILE__ )  ),false,ANALYTIFY_PRO_VERSION);

			}
		}

		function load_mobile_stats( $start_date, $end_date, $dashboard_profile_ID ) {

			?>

			<div id="wp-analytify-mobile-stats-box"><img class="dashboard-loader"  class="dashboard-loader" src="<?php ANALYTIFY_PLUGIN_URL . 'assets/images/loading.gif' ?>"></div>
			<script>
			//<![CDATA[

				jQuery( function($) {
					$.get(ajaxurl, { action:'analytify_load_mobile_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

						$('#wp-analytify-mobile-stats-box').html(data);
					});
				});
			//]]>
			</script>

			<?php

			// include_once WP_PLUGIN_DIR . '/wp-analytify-pro/views/load-mobile-stats.php';
		}


		/**
		* [wp_analytify_pro_tabs Managing tabs for Pro]
		* @param  array     $tabs   Tabs
		* @return array             List of modofied tabs
		*/
		function wp_analytify_pro_tabs( $tabs ) {

			$front_tab = array(
					'front'    => __( 'Front', 'wp-analytify-pro' ),
					);

			$license_tab = array(
					'license'  => __( 'License', 'wp-analytify-pro' ),
					);

			$first_tab  = array_slice( $tabs, 0, 2 );
			$second_tab = array_slice( $tabs, 2 );

			$first_tab  = array_merge( $first_tab, $front_tab );

			$second_tab = array_merge( $first_tab, $second_tab );
			$tabs       = array_merge( $second_tab, $license_tab );

			return $tabs;
		}

		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.2.2
		 * @return      void
		 */
		public function load_textdomain() {

			// $plugin_dir = basename( dirname(__FILE__) );
			// load_plugin_textdomain( 'wp-analytify', false , $plugin_dir . '/lang/');
		}


		/**
		 * Save version number of the plugin and show a custom message for users
		 *
		 * @since 1.3
		 */

		public function _save_version() {

			global $current_user ;
			$user_id = $current_user->ID;

			/*
            delete_option( 'WP_ANALYTIFY_PLUGIN_VERSION' );
            delete_user_meta( $user_id, 'wp_analytify_notice_1_3_0' );
			delete_option( 'WP_ANALYTIFY_NEW_LOGIN', 'yes' );*/

			// notice to displaya notice message Just for 1.3 update for easy authentication.
			// if ( get_option( 'pa_google_token' ) && get_option( 'WP_ANALYTIFY_NEW_LOGIN' ) != 'yes' ) {
			//
			// 	if ( filter_input( INPUT_GET, 'wp_analytify_notice_1_3_0' ) === '0' ) {
			//
			// 		add_user_meta( $user_id, 'wp_analytify_notice_1_3_0', 'true', true );
			// 	}
			//
			// 	if ( ! get_user_meta( $user_id, 'wp_analytify_notice_1_3_0', true ) ) {
			//
	    //         	echo sprintf( esc_html__( '%1$s%2$s%3$s Important note from WP Analytify: %4$s We are Introducing new way to connect Analytify with your Google Analytics account. It requires you to re-authenticate the plugin. We apologize for inconvenience. %5$s %6$s Re-connect now %7$s %9$s %10$s', 'wp-analytify' ), '<div class="updated notice">', '<p>', '<b>', '</b>', '<br/><br/>', '<a style="text-decoration:none" href="' . menu_page_url( 'analytify-settings', false ) . '&tab=authentication&wp_analytify_notice_1_3_0=0">', '</a>', '<a style="text-decoration:none" href="' . menu_page_url( 'analytify-settings', false ) . '&tab=authentication&wp_analytify_notice_1_3_0=0">', '</p>', '</div>' );
			//
			// 	}
			// }


			if ( ANALYTIFY_PRO_VERSION != get_option( 'WP_ANALYTIFY_PRO_PLUGIN_VERSION' ) ) {

				update_option( 'WP_ANALYTIFY_PRO_PLUGIN_VERSION_OLD', get_option( 'WP_ANALYTIFY_PRO_PLUGIN_VERSION' ), '2.0.0' );  // saving old plugin version

				update_option( 'WP_ANALYTIFY_PRO_PLUGIN_VERSION', ANALYTIFY_PRO_VERSION );
			}

		}


		/**
		 * Show Analytics of single post/page in wp-admin under EDIT screen.
		 */
		public static function show_admin_single_analytics() {

			global $post;

			// // Don't show statistics on posts which are not published.
			// if ( get_post_status ( $post->ID ) != 'publish' ) {
			// esc_html_e( 'Statistics will be loaded after you publish this content.', 'wp-analytify' );
			// return false;
			// }
			$back_exclude_posts = explode( ',', get_option( 'post_analytics_exclude_posts_back' ) );

			if ( is_array( $back_exclude_posts ) ) {

				if ( in_array( $post->ID, $back_exclude_posts ) ) {

					analytify_e( 'This post is excluded and will NOT show Analytics.', 'wp-analytify' );

					return;
				}
			}

			$urlPost = '';
			$wp_analytify  = $GLOBALS['WP_ANALYTIFY'];
			$urlPost = parse_url( get_permalink( $post->ID ) );

			if ( get_the_time( 'Y', $post->ID ) < 2005 ) {

				$start_date = '2005-01-01';
			} else {

				$start_date = get_the_time( 'Y-m-d', $post->ID );
			}

			$end_date = date( 'Y-m-d' );

			$is_access_level = get_option( 'post_analytics_access_back' );

			if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {  ?>

			<div class="pa-filter">
				<table cellspacing="0" cellpadding="0" width="400">
					<tbody>
						<tr>
							<td width="0">
								<input type="text" id="start_date" name="start_date" value="<?php echo $start_date;?>">
							</td>
							<td width="0">
								<input type="text" id="end_date" name="end_date" value="<?php echo $end_date;?>">
							</td>
							<input type="hidden" name="urlpost" id="urlpost" value="<?php echo $urlPost['path']; ?>">
							<td width="0">
								<input type="button" id="view_analytics" name="view_analytics" value="View Stats" class="button button-primary button-large">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="loading" style="display:none">
				<img src="<?php echo plugins_url( 'images/loading.gif', __FILE__ );?>">
			</div>
			<div class="show-hide">
				<?php $wp_analytify->get_single_admin_analytics( $start_date, $end_date, $post->ID, 0 ); ?>
			</div>
			<?php
			} else {
				analytify_e( 'You are not allowed to see stats', 'wp-analytify' );
			}
		}


		/**
		 *  Show license tab content
		 *
		 *  @since  2.0
		 */
		function license_tab_content() {

			// var_dump(get_option( 'analytify_license_key' ));
			// var_dump(get_option( 'analytify_license_status' ));
			// var_dump(delete_option( 'analytify_license_key' ));
			// var_dump(delete_option( 'analytify_license_status' ));
			$license  = get_option( 'analytify_license_key' );
			$status   = get_option( 'analytify_license_status' );

			?>

			<div class="wrap">
				<form method="post" action="">

					<p class="inside" for="analytify_license_key"><?php esc_html_e( 'Enter your license key(s). It is important to have a valid license key for automatic plugin updates and support. After adding a license key, press the \'Activate License\' button.', 'wp-analytify-pro' ); ?></p>
					<table class="form-table">
						<tbody>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php esc_html_e( 'Analytify PRO (License Key):', 'wp-analytify-pro' ); ?>
								</th>

								<?php  if ( 'valid' === $status ) : ?>

									<td class="pro-license-row">

									<?php echo $this->get_formatted_masked_license( $license ); ?>

									</td>

								<?php else : ?>

										<td class="pro-license-row">
											<input id="analytify_license_key" name="analytify_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />

												<input type="submit" class="button-secondary" id="analytify_license_activate" name="analytify_license_activate" value="<?php esc_html_e( 'Activate License', 'wp-analytify-pro' ); ?>"/>

											<br /><p id="pro-license-status"><?php if ( $status ) { echo $status; } ?></p>

										</td>

								<?php
								endif; ?>

									</tr>

									<?php do_action( 'edd_license_key' ); ?>
									<?php do_action( 'woocommerce_license_key' ); ?>
									<?php do_action( 'wp_analytify_email_license_key' ); ?>
									<?php do_action( 'wp_analytify_campaigns_license_key' ); ?>
									<?php do_action( 'wp_analytify_goals_license_key' ); ?>
									<?php do_action( 'wp_analytify_forms_license_key' ); ?>
									<?php do_action( 'wp_analytify_authors_license_key' ); ?>

								</tbody>
							</table>

						</form>
					</div>

				<?php

		}

		/**
		 * @return NULL nothing
		 */
		function analytify_shortcode_button() {

			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard', array( 'Administrator' ) );
			// Return, if not have dashboard access.
			if ( ! $wp_analytify->pa_check_roles( $is_access_level )  ) {
				return;
			}

			if ( get_user_option( 'rich_editing' ) == 'true' ) {

				add_filter( 'mce_external_plugins', array( $this, 'analytify_stats_js' ) );
				add_filter( 'mce_buttons',          array( $this, 'register_analytify_button' ) );
			}
		}

		/**
		 * @param  [Array]
		 * @return [Array]
		 */
		function analytify_stats_js( $plugin_array ) {

			$plugin_array['analytifystats'] = plugins_url( 'assets/js/shortcode.js', dirname( __FILE__ )  );
			return $plugin_array;
		}


		function register_analytify_button( $buttons ) {

			array_push( $buttons, '|', 'analytifystats' );
			return $buttons;
		}


		/**
		 * Shortcodes implementation for front end World Map.
		 */
		public function analytify_worldmap_shortcode( $atts ) {

			global $post;
			$content = '';

			if ( empty( $post ) || get_the_time( 'Y', $post->ID ) < 2005 ) {

				$start_date = '2005-01-01';
			} else {
				$start_date = get_the_time( 'Y-m-d', $post->ID );
			}

			$end_date = date( 'Y-m-d' );

			extract( shortcode_atts( array(
				'start_date' 		   => $start_date,
				'end_date' 			   => $end_date,
				'analytics_for' 	 => 'current',
				),
			$atts ));

			if ( $analytics_for == 'current' ) {

				$u_post = parse_url( get_permalink( $post->ID ) );
				$filter = 'ga:pagePath==' . $u_post['path'] . '';
			} else {
				$filter = false;
			}

			/* Use Transients API for cache management to reduce the API requests */
			$worldmap_stats = get_transient( md5( 'show-worldmap-front' . $start_date . $end_date ) );

			if ( $worldmap_stats == false ) {

				$worldmap_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', $filter, 15 );
				set_transient( md5( 'show-worldmap-front' . $start_date . $end_date ) , $worldmap_stats, 60 * 60 * 24 );

			}

			if ( isset( $worldmap_stats->totalsForAllResults ) ) {
				ob_start();

				include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/world-map-stats.php';
				pa_include_worldmap( $this, $worldmap_stats );

				$content .= ob_get_contents();
				ob_get_clean();
			}

			return $content;

		}

		/**
		 *  Shortcodes implementation for front end
		 */
		public function analytify_stats_shortcode( $atts, $content = null ) {

			if ( ! wp_style_is( 'front-end-style', 'enqueued' ) ) {

				wp_enqueue_style( 'front-end-style', plugins_url( 'assets/css/frontend_styles.css', dirname( __FILE__ ) ), false, ANALYTIFY_PRO_VERSION );
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'analytify-script', plugins_url( 'assets/js/script.js', dirname( __FILE__ ) ), array('jquery'), ANALYTIFY_PRO_VERSION );
				wp_localize_script( 'analytify-script', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
			
			// Google Analytics release date as default.
			$default_start_date = '2005-01-01';
			
			global $post;
			$filter = '';
			$return = '';
			
			if ( ! isset( $post ) && is_object( $post ) ) {
				$default_start_date = get_the_time( 'Y-m-d', $post->ID );
			}

			extract( shortcode_atts( array(
				'metrics'         => 'ga:sessions',
				'dimensions'      => '',
				'start_date'      => $default_start_date,
				'end_date'        => date( 'Y-m-d' ),
				'sort'            => '',
				'filter'          => $filter,
				'max_results'     => 6,
				'analytics_for'   => '',
				'custom_page_id'  => 0,
				'date_type'       => 'custom',
				'permission_view' => '',
				'front'		      => '',
				),
			$atts ));

			// ShortCode to show All stats at frontend [analytify-stats front='all']
			if ( $front == 'all' ) {
				return $this->get_shortcode_front_analytics( $custom_page_id );
			}

			/**
			 * Handing ShortCode custom dates
			 *
			 */
			if ( $date_type != 'custom' ) {

				if ( $date_type == 'today' ) { // Today
					$start_date = date( 'Y-m-d' );
				}

				if ( $date_type == 'year-to-date' ) { // Yesterday
					$start_date = date( 'Y-m-d', strtotime(  date( 'Y' ) . '-01-01' ) );
					$end_date = date( 'Y-m-d' );
				}

				if ( $date_type == '- 7 days' ) { // Last week
					$start_date = date( 'Y-m-d', strtotime( $date_type ) );
				}

				if ( $date_type == '- 15 days' ) { // Last 15 days
					$start_date = date( 'Y-m-d', strtotime( $date_type ) );
				}

				if ( $date_type == '- 30 days' ) { // Last 30 days
					$start_date = date( 'Y-m-d', strtotime( $date_type ) );
				}

				if ( $date_type == 'year-to-date' ) { // This year
					$start_date = date( 'Y-01-01' );
				}

				if ( $date_type == '- 365 days' ) { // Last Year
					$start_date = date( 'Y-m-d', strtotime( $date_type ) );
				}

				if ( $date_type == '- 1 days' ) { // Yesterday
					$start_date = date( 'Y-m-d', strtotime( $date_type ) );
					$end_date = date( 'Y-m-d', strtotime( $date_type ) );
				}
			}
			
			if ( "current" == $analytics_for ) {
				global $wp;

				// Default is homepage link as fallback.
				$permalink = get_site_url();

				// Change permalink based on current page.
				if ( is_archive() ) {
					$permalink = get_site_url() . '/' . $wp->request;
				} else if ( isset( $post ) && is_object( $post ) ) {
					$permalink = get_permalink( $post->ID );
				}

				// Parse URL
				$u_post = parse_url( $permalink );

				// If path not found in parsed array, grab from wp object.
				$url_path = isset( $u_post['path'] ) ? $u_post['path'] : $wp->request;

				$filters = 'ga:pagePath==' . $url_path ;

				// Page path filter for site that use domain mapping.
				$filters = apply_filters( 'analytify_page_path_filter', $filters, $u_post );

				if ( $filter ) {
					$filters = ','. $filter;
				}
				
			} else if ( "page_id" == $analytics_for ) {
				$u_post = parse_url( get_permalink( $custom_page_id ) );
				$filters = 'ga:pagePath==' . $u_post['path'] ;
				
				// Page path filter for site that use domain mapping.
				$filters = apply_filters( 'analytify_page_path_filter', $filters, $u_post );

				if ( $filter ) {
					$filters = ','. $filter;
				}
			} else {
				$filters = $filter;
			}

			if ( ! empty( $dimensions ) ) {
				$is_dimensions = $dimensions;
			} else {
				$is_dimensions = false;
			}

			$all_metrics    = explode( ',', $metrics );
			$all_dimensions = explode( ',', $dimensions );

			if ( empty( $sort ) ) {
				$is_sort = $all_metrics[0];
			} else {
				$is_sort = $sort;
			}

			// If post year is less than 2005, make it to start from 2005.
			if ( date( 'Y', strtotime( $start_date ) ) < 2005 ) {
				$start_date = '2005-01-01';
			}

			/**
			*  Making transient string for cache.
			*/
			$transient_value = $metrics . '_' . $dimensions . '_' . $date_type . '_' . $start_date . '_' . $end_date . '_' . $sort . '_' . $filters . '_' . $max_results . '_' . $analytics_for . '_' . $permission_view . '_' . $custom_page_id;

				$stats = get_transient( md5( $transient_value ) );

				if ( $stats === false ) {

					$stats = $this->wpa_get_analytics( 'single', $metrics, $start_date, $end_date, $is_dimensions, $is_sort, $filters, $max_results );

					set_transient( md5( $transient_value ) , $stats, 60 * 60 * 2 );
				}

				$total          = count( $all_metrics ) + count( $all_dimensions );
				$current_user   = wp_get_current_user();
				$roles          = $current_user->roles;
				$role_to_show   = explode( ',', $permission_view );

				if ( $current_user->ID == 0 ) {
					$current_role   = 'everyone';
				} else {
					$current_role   = $roles[0];
				}

				// Comment of start and end date for analytics.
				$return .= sprintf( esc_html__( '%1$s Fetching Analytics from %2$s to %3$s %4$s', 'wp-analytify-pro' ), '<!--', $start_date, $end_date, ' !-->' );

				if ( ! empty( $stats['rows'] ) ) {

					if ( empty( $dimensions ) ) {

						if ( $permission_view == '' || in_array( $current_role, $role_to_show ) ) {

							for ( $i = 0; $i < count( $all_metrics ); $i++ ) {

								$title         = $all_metrics[ $i ];
								$matrics_title = explode( ':', $title );

								if ( ! is_numeric( $stats->totalsForAllResults[ $all_metrics[ $i ] ] ) ) {
									$return       .= $stats->totalsForAllResults[ $all_metrics[ $i ] ];
								} else {
									$return       .= number_format( $stats->totalsForAllResults[ $all_metrics[ $i ] ] ) ;
								}

							}

						}
					} else {

						if ( $permission_view == '' || in_array( $current_role, $role_to_show ) ) {

							$return             .= "<div class='shortcode-table'><table> ";
							$query_dimensions    = explode( ',', $stats['query']->dimensions );

							for ( $i = 0; $i < count( $query_dimensions ); $i++ ) {

								if ( $query_dimensions[ $i ] == 'ga:userType' ) {
										// Change texts here for dimensions
										// $query_dimensions[ $i ] = 'Players Type';
								}

								$return .= '<th>' . str_replace( 'ga:', '', $query_dimensions[ $i ] ) . '</th> ';
							}

							for ( $i = 0; $i < count( $all_metrics ); $i++ ) {

								if ( $stats['query']->metrics[ $i ] == 'ga:users' ) {
										// Change texts here for metrics
										// $stats['query']->metrics[ $i ] = 'Players';
								}

								$return .= '<th>' . str_replace( 'ga:', '', $stats['query']->metrics[ $i ] ) . '</th> ';
							}

							$return .= "<tr rowspan='" . $max_results . "'>";

							for ( $i = 0; $i < $total; $i++ ) {

								foreach ( $stats['rows'] as $b_stat ) {

									$return .= "<tr rowspan='" . $max_results . "'>";

									for ( $i = 0; $i < count( $b_stat ); $i++ ) {

												// Add Strings here for dimension rows
												// $b_stat[ $i ] = str_replace(array('New Visitor', 'Returning Visitor'), array('New Player','Returning Player'), $b_stat[ $i ]) ;
										if ( ! is_numeric( $b_stat[ $i ] ) ) {
											$return .= '<td>' . $b_stat[ $i ] . '</td>'; } else {
											$return .= '<td>' . number_format( $b_stat[ $i ] ) . '</td>'; }
									}
									$return .= '</tr>';
								}
							}

							$return .= '</table></div>';

						}
					}
				}

			return $return;
		}

		function analytify_mce_css( $mce_css ) {

			if ( ! empty( $mce_css ) ) {

				$mce_css .= ',';
			}

			$mce_css .= plugins_url( 'assets/css/wp-analytify-editor.css', dirname( __FILE__ ) );
			return $mce_css;
		}

		/**
		* View of Shortcode window
		*/
		public function analytify_shortcode_view() {
			include_once( ANALYTIFY_PRO_ROOT_PATH . '/inc/shortcode.php' );
			wp_die();
		}


		/**
		 * Add a link to the settings page to the plugins list
		 *
		 * @since 2.0
		 */
		public function plugin_action_links( $links ) {

			$settings_link = sprintf( esc_html__( '%1$s Settings %2$s | %3$s Dashboard %4$s | %5$s Help %6$s', 'wp-analytify-pro' ), '<a href="' . admin_url( 'admin.php?page=analytify-settings' ) . '">', '</a>', '<a href="' . admin_url( 'admin.php?page=analytify-dashboard' ) . '">', '</a>', '<a href="' . admin_url( 'index.php?page=wp-analytify-getting-started' ) . '">', '</a>' );
			array_unshift( $links, $settings_link );

			return $links;
		}

		/**
		 * Plugin row meta links
		 *
		 * @since 1.1
		 *
		 * @param array  $input already defined meta links
		 * @param string $file plugin file path and name being processed
		 * @return array $input
		 */
		function plugin_row_meta( $input, $file ) {

			if ( 'wp-analytify-pro/wp-analytify-pro.php' !== $file ) {
				return $input; }

			$links = array(

				sprintf( esc_html__( '%1$s Getting Started %2$s', 'wp-analytify-pro' ), '<a href="' . admin_url( 'index.php?page=wp-analytify-getting-started' ) . '">', '</a>' ),
				sprintf( esc_html__( '%1$s Add Ons %2$s', 'wp-analytify-pro' ), '<a href="http://analytify.io/add-ons/">', '</a>' ),
			);

			$input = array_merge( $input, $links );

			return $input;
		}


		/**
		 * Display warning if profiles are not selected.
		 */
		public function pa_check_warnings() {

			add_action( 'admin_footer', array(
				&$this,
				'profile_warning',
			));
		}

		/**
		 * Get profiles from user Google Analytics account profiles.
		 */

		// public function pt_get_analytics_accounts() {

		// 	try {

		// 		if ( get_option( 'pa_google_token' ) != '' ) {
		// 			$profiles = $this->service->management_profiles->listManagementProfiles( '~all', '~all' );
		// 			return $profiles;
		// 		} else {
		// 			echo '<br /><p class="description">' . esc_html__( 'You must authenticate to access your web profiles.', 'wp-analytify' ) . '</p>';
		// 		}
		// 	} catch (Exception $e) {
		// 		echo sprintf( esc_html__( '%1$s %2$s oOps, Something went wrong!%3$s %4$s Try to %5$s Reset %6$s Authentication.', 'wp-analytify' ), '<br />', '<strong>', '</strong>', '<br /><br />', '<a href=\'?page=analytify-settings&tab=authentication\' title="Reset">', '</a>' );
		// 	}

		// }

		// public function pa_setting_url() {

		// 	return admin_url( 'admin.php?page=analytify-settings' );

		// }


		// public function pt_save_data( $key_google_token ) {

		// 	try {

		// 		update_option( 'post_analytics_token', $key_google_token );
		// 		if ( $this->pa_connect() ) { return true; }
		// 	} catch (Exception $e) {

		// 		echo $e->getMessage();
		// 	}

		// }

		/**
		 * Warning messages.
		 */
		public function profile_warning() {

			$profile_id     = get_option( 'pt_webprofile' );
			$acces_token    = get_option( 'post_analytics_token' );

			if ( ! isset( $acces_token ) || empty( $acces_token ) ) {

				echo "<div id='message' class='error'><p><strong>" . sprintf( esc_html__( 'Analytify is not active. Please %1$sAuthenticate%2$s in order to get started using this plugin.', 'wp-analytify-pro' ) , '<a href="' . menu_page_url( 'analytify-settings', false ) . '">', '</a>' ) . '</p></div>';
			} else {

				if ( ! isset( $profile_id ) || empty( $profile_id ) ) {
					echo sprintf( esc_html__( '%1$s Google Analytics Profile is not set. Set the %2$s Profile %3$s' , 'wp-analytify-pro' ), '<div class="error"><p><strong>', '<a href="' . menu_page_url( 'analytify-settings', false ) . '&tab=profile">', '</a></p></div>' );
				}
			}
		}

		/**
		 * Show Analytics at front-end.
		 */
		public function get_single_front_analytics( $content ) {

			global $post, $wp_analytify;

			if ( is_single() || is_page() ) {

				$post_type = get_post_type( $post->ID );

				if ( ! in_array( $post_type, $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_post_types_front_end','wp-analytify-front', array() ) ) ) {

					return $content;
				}


				$front_exclude_posts_arr = explode( ',', $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'exclude_pages_front_end','wp-analytify-front' ) );

				if ( is_array( $front_exclude_posts_arr ) ) {

					if ( in_array( get_the_ID(), $front_exclude_posts_arr ) ) {

						return $content;
					}
				}

				// show stats to only selected roles

				$front_access = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_roles_front_end','wp-analytify-front' , array() );

				if ( $GLOBALS['WP_ANALYTIFY']->pa_check_roles( $front_access ) ) {

					$post_analytics_settings_front = array();
					$post_analytics_settings_front = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_panels_front_end','wp-analytify-front' );

					$urlPost = parse_url( get_permalink( $post->ID ) );

					if ( $urlPost['host'] == 'localhost' ) {
						$filter = 'ga:pagePath==/';
					} else {
						$filter = 'ga:pagePath==' . $urlPost['path'] . '';
					}

					if ( get_the_time( 'Y', $post->ID ) < 2005 ) {

						$start_date = '2005-01-01';
					} else {

						$start_date = get_the_time( 'Y-m-d', $post->ID );
					}

					$end_date = date( 'Y-m-d' );

					ob_start();

					include( ANALYTIFY_PRO_ROOT_PATH . '/inc/front-menus.php' );

					if ( ! empty( $post_analytics_settings_front ) ) {

						if ( is_array( $post_analytics_settings_front ) ) {

							if ( in_array( 'show-overall-front', $post_analytics_settings_front ) ) {

								$stats = get_transient( md5( 'show-overall-front' . $filter ) );
								if ( $stats == false ) {
									$stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users',$start_date, $end_date, false, false, $filter );
									set_transient( md5( 'show-overall-front' . $filter ) , $stats, 60 * 60 * 20 );
								}

								if ( isset( $stats->totalsForAllResults ) ) {

									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/general-stats.php';
									pa_include_general( $GLOBALS['WP_ANALYTIFY'] , $stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-country-front', $post_analytics_settings_front ) ) {

								$country_stats = get_transient( md5( 'show-country-front' . $filter ) );
								if ( $country_stats == false ) {
									$country_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', $filter,5 );
									set_transient( md5( 'show-country-front' . $filter ) , $country_stats, 60 * 60 * 20 );
								}

								if ( isset( $country_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/country-stats.php';
									pa_include_country( $GLOBALS['WP_ANALYTIFY'], $country_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-city-front', $post_analytics_settings_front ) ) {

								$city_stats = get_transient( md5( 'show-city-front' . $filter ) );
								if ( $city_stats == false ) {
									$city_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:city', '-ga:sessions', $filter, 5 );
									set_transient( md5( 'show-city-front' . $filter ) , $city_stats, 60 * 60 * 20 );
								}

								if ( isset( $city_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/city-stats.php';
									pa_include_city( $GLOBALS['WP_ANALYTIFY'], $city_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-keywords-front', $post_analytics_settings_front ) ) {

								$keyword_stats = get_transient( md5( 'show-keywords-front' . $filter ) );
								if ( $keyword_stats == false ) {
									$keyword_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions',$filter,10 );
									set_transient( md5( 'show-keywords-front' . $filter ) , $keyword_stats, 60 * 60 * 20 );
								}

								if ( isset( $keyword_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/keywords-stats.php';
									pa_include_keywords( $GLOBALS['WP_ANALYTIFY'] , $keyword_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-social-front', $post_analytics_settings_front ) ) {

								$social_stats = get_transient( md5( 'show-social-front' . $filter ) );
								if ( $social_stats == false ) {
									$social_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions',$filter, 10 );
									set_transient( md5( 'show-social-front' . $filter ) , $social_stats, 60 * 60 * 20 );
								}

								if ( isset( $social_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/social-stats.php';
									pa_include_social( $GLOBALS['WP_ANALYTIFY'], $social_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-browser-front', $post_analytics_settings_front ) ) {

								$browser_stats = get_transient( md5( 'show-browser-front' . $filter ) );
								if ( $browser_stats == false ) {
									$browser_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:browser,ga:operatingSystem', '-ga:sessions',$filter,5 );
									set_transient( md5( 'show-browser-front' . $filter ) , $browser_stats, 60 * 60 * 20 );
								}

								if ( isset( $browser_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/browser-stats.php';
									pa_include_browser( $GLOBALS['WP_ANALYTIFY'], $browser_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-os-front', $post_analytics_settings_front ) ) {

								$os_stats = get_transient( md5( 'show-os-front' . $filter ) );
								if ( $os_stats == false ) {
									$os_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:operatingSystem,ga:operatingSystemVersion', '-ga:sessions', $filter, 5 );
									set_transient( md5( 'show-os-front' . $filter ) , $os_stats, 60 * 60 * 20 );
								}

								if ( isset( $os_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/os-stats.php';
									pa_include_operating( $GLOBALS['WP_ANALYTIFY'], $os_stats );

								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-mobile-front', $post_analytics_settings_front ) ) {

								$mobile_stats = get_transient( md5( 'show-mobile-front' . $filter ) );
								if ( $mobile_stats == false ) {
									$mobile_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:mobileDeviceInfo', '-ga:sessions', $filter, 5 );
									set_transient( md5( 'show-mobile-front' . $filter ) , $mobile_stats, 60 * 60 * 20 );
								}

								if ( isset( $mobile_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/mobile-stats.php';
									pa_include_mobile( $GLOBALS['WP_ANALYTIFY'], $mobile_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-referrer-front', $post_analytics_settings_front ) ) {

								$referr_stats = get_transient( md5( 'show-referrer-front' . $filter ) );
								if ( $referr_stats == false ) {
									$referr_stats = $GLOBALS['WP_ANALYTIFY']->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions',$filter,10 );
									set_transient( md5( 'show-referrer-front' . $filter ) , $referr_stats, 60 * 60 * 20 );
								}

								if ( isset( $referr_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/referrers-stats.php';
									pa_include_referrers( $GLOBALS['WP_ANALYTIFY'], $referr_stats );
								}
							}
						}
					}

					$content .= ob_get_contents();
					ob_get_clean();
				}
			}

			return $content;

		}

		/**
		 * Show Analytics at front-end using ShortCode
		 */
		public function get_shortcode_front_analytics( $post_id = '' ) {

			global $post, $wp_analytify;

			$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			$front_access = $wp_analytify->settings->get_option( 'show_analytics_roles_front_end','wp-analytify-front', array() );

			if ( is_single() || is_page() ) {

				$post_type = get_post_type( $post->ID );

				if ( 'off' == $wp_analytify->settings->get_option( 'disable_front_end', 'wp-analytify-front' ) ) {
					return ;
				}

				if ( ! in_array( $post_type, $wp_analytify->settings->get_option( 'show_analytics_post_types_front_end', 'wp-analytify-front', array() ) ) ) {
					return ;
				}

				$front_exclude_posts_arr = explode( ',', $wp_analytify->settings->get_option( 'exclude_pages_front_end', 'wp-analytify-front' ) );

				if ( is_array( $front_exclude_posts_arr ) ) {

					if ( in_array( get_the_ID(), $front_exclude_posts_arr ) ) {

						return ;
					}
				}

				// Showing stats to guests
				if ( $wp_analytify->pa_check_roles( $front_access ) ) {

					$post_analytics_settings_front = $wp_analytify->settings->get_option( 'show_panels_front_end', 'wp-analytify-front', array() );

					if  ( $post_id ) {
						$urlPost = parse_url( get_permalink( $post_id ) );
					} else {
						$urlPost = parse_url( get_permalink( $post->ID ) );
					}

					if ( $urlPost['host'] == 'localhost' ) {
						$filter = 'ga:pagePath==/'; // .$u_post['path'];
					} else {
						$filter = 'ga:pagePath==' . $urlPost['path'] . '';
					}

					if ( get_the_time( 'Y', $post->ID ) < 2005 ) {
						$start_date = '2005-01-01';
					} else {
						$start_date = get_the_time( 'Y-m-d', $post->ID );
					}

					$end_date =	 date( 'Y-m-d' );

					$content = '';
					ob_start();
					include( ANALYTIFY_PRO_ROOT_PATH . '/inc/front-menus.php' );

					if ( ! empty( $post_analytics_settings_front ) ) {

						if ( is_array( $post_analytics_settings_front ) ) {

							if ( in_array( 'show-overall-front', $post_analytics_settings_front ) ) {

								$stats = get_transient( md5( 'show-overall-front' . $filter ) );
								if ( $stats === false ) {
									$stats = $this->wpa_get_analytics( 'single', 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users',$start_date, $end_date, false, false, $filter );
									set_transient( md5( 'show-overall-front' . $filter ) , $stats, 60 * 60 * 20 );
								}

								if ( isset( $stats->totalsForAllResults ) ) {

									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/general-stats.php';
									pa_include_general( $wp_analytify, $stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-country-front', $post_analytics_settings_front ) ) {

								$country_stats = get_transient( md5( 'show-country-front' . $filter ) );
								if ( $country_stats === false ) {
									$country_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', $filter,5 );
									set_transient( md5( 'show-country-front' . $filter ) , $country_stats, 60 * 60 * 20 );
								}

								if ( isset( $country_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old//front/country-stats.php';
									pa_include_country( $wp_analytify, $country_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-city-front', $post_analytics_settings_front ) ) {

								$city_stats = get_transient( md5( 'show-city-front' . $filter ) );
								if ( $city_stats === false ) {
									$city_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:city', '-ga:sessions', $filter, 5 );
									set_transient( md5( 'show-city-front' . $filter ) , $city_stats, 60 * 60 * 20 );
								}

								if ( isset( $city_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/city-stats.php';
									pa_include_city( $wp_analytify, $city_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-keywords-front', $post_analytics_settings_front ) ) {

								$keyword_stats = get_transient( md5( 'show-keywords-front' . $filter ) );
								if ( $keyword_stats === false ) {
									$keyword_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions',$filter,10 );
									set_transient( md5( 'show-keywords-front' . $filter ) , $keyword_stats, 60 * 60 * 20 );
								}

								if ( isset( $keyword_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/keywords-stats.php';
									pa_include_keywords( $wp_analytify, $keyword_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-social-front', $post_analytics_settings_front ) ) {

								$social_stats = get_transient( md5( 'show-social-front' . $filter ) );
								if ( $social_stats === false ) {
									$social_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions',$filter, 10 );
									set_transient( md5( 'show-social-front' . $filter ) , $social_stats, 60 * 60 * 20 );
								}

								if ( isset( $social_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/social-stats.php';
									pa_include_social( $wp_analytify, $social_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-browser-front', $post_analytics_settings_front ) ) {

								$browser_stats = get_transient( md5( 'show-browser-front' . $filter ) );
								if ( $browser_stats === false ) {
									$browser_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:browser,ga:operatingSystem', '-ga:sessions',$filter,5 );
									set_transient( md5( 'show-browser-front' . $filter ) , $browser_stats, 60 * 60 * 20 );
								}

								if ( isset( $browser_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/browser-stats.php';
									pa_include_browser( $wp_analytify, $browser_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-os-front', $post_analytics_settings_front ) ) {

								$os_stats = get_transient( md5( 'show-os-front' . $filter ) );
								if ( $os_stats === false ) {
									$os_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:operatingSystem,ga:operatingSystemVersion', '-ga:sessions', $filter, 5 );
									set_transient( md5( 'show-os-front' . $filter ) , $os_stats, 60 * 60 * 20 );
								}

								if ( isset( $os_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/os-stats.php';
									pa_include_operating( $wp_analytify, $os_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-mobile-front', $post_analytics_settings_front ) ) {

								$mobile_stats = get_transient( md5( 'show-mobile-front' . $filter ) );
								if ( $mobile_stats === false ) {
									$mobile_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:mobileDeviceInfo', '-ga:sessions', $filter, 5 );
									set_transient( md5( 'show-mobile-front' . $filter ) , $mobile_stats, 60 * 60 * 20 );
								}

								if ( isset( $mobile_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/mobile-stats.php';
									pa_include_mobile( $wp_analytify, $mobile_stats );
								}
							}
						}

						if ( is_array( $post_analytics_settings_front ) ) {
							if ( in_array( 'show-referrer-front', $post_analytics_settings_front ) ) {

								$referr_stats = get_transient( md5( 'show-referrer-front' . $filter ) );
								if ( $referr_stats === false ) {
									$referr_stats = $this->wpa_get_analytics( 'single', 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions',$filter,10 );
									set_transient( md5( 'show-referrer-front' . $filter ) , $referr_stats, 60 * 60 * 20 );
								}

								if ( isset( $referr_stats->totalsForAllResults ) ) {
									include_once ANALYTIFY_PRO_ROOT_PATH . '/views/old/front/referrers-stats.php';
									pa_include_referrers( $wp_analytify, $referr_stats );
								}
							}
						}
					}
				}
			}

			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}


		/**
		 * Show Stats on under Post/Page.
		 *
		 * @since 2.0.0
		 */
		public function wp_analytify_stats_under_post( $show_settings, $s_date, $e_date, $filter ) {

			$wp_analytify =  $GLOBALS['WP_ANALYTIFY'];

			if ( is_array( $show_settings ) ) {
				if ( in_array( 'show-geographic-dashboard', $show_settings ) ) {
					?>
					<div class="analytify_general_status analytify_status_box_wraper">
						<div class="analytify_status_header">
							<h3><?php analytify_e( 'Geographic', 'wp-analytify' ); ?></h3>
						</div>
						<div class="analytify_status_body">
							<?php

							$country_stats = $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date , 'ga:country' , '-ga:sessions' , 'ga:country!=(not set);'.$filter , false, 'analytify-single-country-stats' );
							$cities_stats 		= $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date , 'ga:city,ga:country' , '-ga:sessions' , 'ga:city!=(not set);ga:country!=(not set);'.$filter , 5, 'analytify-single-city-stats' );

							include ANALYTIFY_ROOT_PATH . '/views/default/admin/geographic-stats.php';
							fetch_geographic_stats( $wp_analytify, $country_stats, $cities_stats, false );

							?>
						</div>

						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php _e( 'Listing statistics of top countries and cities.', 'wp-analytify-pro' ); ?></span>
						</div>
					</div>
					<?php
				}
			}


			if ( is_array( $show_settings ) ) {
				if ( in_array( 'show-system-stats', $show_settings ) ) { ?>
					<div class="analytify_general_status analytify_status_box_wraper">
						<div class="analytify_status_header">
							<h3><?php analytify_e( 'System Stats', 'wp-analytify' ); ?></h3>
						</div>
						<div class="">
							<?php


							$browser_stats 	= $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date , 'ga:browser,ga:operatingSystem' , '-ga:sessions' , 'ga:browser!=(not set);ga:operatingSystem!=(not set);'.$filter, 5, 'analytify-single-browser-stats' );

							$os_stats 			= $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date , 'ga:operatingSystem,ga:operatingSystemVersion' , '-ga:sessions' , 'ga:operatingSystemVersion!=(not set);'.$filter, 5, 'analytify-single-operating-system-stats' );


							$mobile_stats 	= $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date , 'ga:mobileDeviceBranding,ga:mobileDeviceModel' , '-ga:sessions' , 'ga:mobileDeviceModel!=(not set);ga:mobileDeviceBranding!=(not set);'.$filter, 5, 'analytify-single-mobile-stats' );

							include ANALYTIFY_ROOT_PATH . '/views/default/admin/system-stats.php';
							fetch_system_stats( $wp_analytify, $browser_stats, $os_stats, $mobile_stats );
							?>
						</div>
					</div>
					<?php
				}
			}


				if ( is_array( $show_settings ) ) {
					if ( in_array( 'show-keywords-dashboard', $show_settings ) ) {
						?>
						<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3><?php analytify_e( 'How people are finding you (keywords)', 'wp-analytify' ); ?></h3>
								<div class="analytify_status_header_value keywords_total">
									<span class="analytify_medium_f"><?php analytify_e( 'Total Visits', 'wp-analytify' ); ?></span>
								</div>
							</div>
							<div class="analytify_status_body">
								<?php
								$keyword_stats = $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date, 'ga:keyword', '-ga:sessions', $filter, 8, 'analytify-single-keyword-stats' );

								include ANALYTIFY_ROOT_PATH . '/views/default/admin/keywords-stats.php';
								$_keyword_dashboard = json_decode( fetch_keywords_stats( $wp_analytify, $keyword_stats, true ), true);
								echo $_keyword_dashboard['body']  ;
								?>
							</div>
							<div class="analytify_status_footer">
								<span class="analytify_info_stats"><?php _e( 'Listing your ranked keywords', 'wp-analytify-pro' ); ?></span>
							</div>
						</div>
						<?php
					}
				}


				if ( is_array( $show_settings ) ) {
					if ( in_array( 'show-social-dashboard', $show_settings ) ) {
						?>
						<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3><?php analytify_e( 'Social Media', 'wp-analytify' ); ?></h3>
								<div class="analytify_status_header_value social_total">
									<span class="analytify_medium_f"><?php analytify_e( 'Total Visits', 'wp-analytify' ); ?></span>
								</div>
							</div>
							<div class="analytify_status_body">
								<?php

								$social_stats = $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date, 'ga:socialNetwork', '-ga:sessions', 'ga:socialNetwork!=(not set);'.$filter, 7, 'analytify-single-social-media-stats' );
								include ANALYTIFY_ROOT_PATH . '/views/default/admin/socialmedia-stats.php';
								$_socialmedia_dashboard = json_decode( fetch_socialmedia_stats( $wp_analytify, $social_stats, true ), true);
								echo $_socialmedia_dashboard['body'];
								?>

							</div>
							<div class="analytify_status_footer">
								<span class="analytify_info_stats"><?php _e( 'See how many users are coming to your site from Social media', 'wp-analytify-pro' ); ?></span>
							</div>
						</div>
						<?php
					}

				}

				if ( is_array( $show_settings ) ) {
					if ( in_array( 'show-referrer-dashboard', $show_settings ) ) {
						?>
						<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3><?php analytify_e( 'Top Referrers', 'wp-analytify' ); ?></h3>
								<div class="analytify_status_header_value  reffers_total">
									<span class="analytify_medium_f"><?php analytify_e( 'Total Visits', 'wp-analytify' ); ?></span>
								</div>
							</div>
							<div class="analytify_status_body">
								<?php
								$referr_stats = $wp_analytify->pa_get_analytics( 'ga:sessions', $s_date, $e_date, 'ga:source,ga:medium', '-ga:sessions', $filter, 7,'analytify-single-reffer-stats' );
								include ANALYTIFY_ROOT_PATH . '/views/default/admin/referrers-stats.php';
								$_refferrers_dashboard = json_decode( fetch_referrers_stats( $wp_analytify, $referr_stats, true ), true);
								echo $_refferrers_dashboard['body'];
								?>
							</div>
							<div class="analytify_status_footer">
								<span class="analytify_info_stats"><?php _e( 'Who are the top Referrers to your site? See above', 'wp-analytify-pro' ); ?></span>
							</div>
						</div>
						<?php
					}

				}

				if ( is_array( $show_settings ) ) {
					if ( in_array( 'show-what-happen-stats', $show_settings ) ) {
						?>
						<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3><?php _e( 'What\'s happening when users come to your page.', 'wp-analytify-pro' ); ?></h3>
							</div>
							<div class="analytify_status_body">
								<?php
								$what_happen = $wp_analytify->pa_get_analytics( 'ga:entrances,ga:exits,ga:entranceRate,ga:exitRate', $s_date, $e_date, 'ga:pagePath', '-ga:entrances', $filter, 1, 'analytify-single-what-happen-stats' );
								include ANALYTIFY_PRO_ROOT_PATH . '/views/default/admin/single-what-happen.php';
								$_what_happen = json_decode( analytify_single_what_happen( $wp_analytify, $what_happen ), true);
								echo $_what_happen['body'];
								?>
							</div>
							<div class="analytify_status_footer">
							</div>
						</div>
						<?php
					}
				}

		}


		/**
		 * Update your plugin from our own server.
		 *
		 * @since 1.0
		 */
		function _plugin_updater() {

			// retrieve our license key from the DB
			$wpa_license_key = trim( get_option( 'analytify_license_key' ) );

			// setup the updater
			if ( class_exists( 'ANALYTIFY_SL_Plugin_Updater' ) ) {

				$edd_updater = new ANALYTIFY_SL_Plugin_Updater( ANALYTIFY_STORE_URL, ANALYTIFY_PRO_UPGRADE_PATH, array(
						'version'   => ANALYTIFY_PRO_VERSION,               // current version number
						'license'   => $wpa_license_key,        // license key (used get_option above to retrieve from DB)
						'item_id' 	=> ANALYTIFY_PRO_ID,    // name of this plugin
						'author'    => 'Muhammad Adnan',// author of this plugin
						'beta'		=> false
						)
				);
			}
		}

		function wp_analytify_register_option() {
			// creates our settings in the options table
			register_setting( 'analytify-settings', 'analytify_license_key', array( $this, 'analytify_sanitize_license' ) );
		}


		function analytify_sanitize_license( $new ) {

			$old = get_option( 'analytify_license_key' );

			if ( $old && $old != $new ) {

				delete_option( 'analytify_license_status' ); // new license has been entered, so must reactivate

			}
			return $new;
		}


		/**
		 * *********************************
		 * Check the license key
		 *************************************/


		/**
		 * AJAX handler for checking a license.
		 *
		 * @return string (JSON)
		 */
		function ajax_check_license() {

			$this->check_ajax_referer( 'check-license' );

			$key_rules = array(
				'action'  => 'key',
				'license' => 'string',
				'context' => 'key',
				'nonce'   => 'key',
			);
			$this->set_post_data( $key_rules );

			$license          = ( empty( $this->state_data['license'] ) ? $this->get_license_key() : $this->state_data['license'] );
			$decoded_response = $this->check_license( $license );
			$context          = ( empty( $this->state_data['context'] ) ? null : $this->state_data['context'] );
			// var_dump($decoded_response);
			// wp_die('Okie');
			if ( false == $license ) {

				$decoded_response           = array( 'error' => array() );
				$decoded_response['error'] = array( sprintf( '<div class="notification-message warning-notice inline-message invalid-license">%s</div>', $this->get_license_status_message() ) );
			} elseif ( 'expired' === $decoded_response->license ) {
					$decoded_response           = array( 'error' => array() );
					$decoded_response['error'] = array( sprintf( '<div class="notification-message warning-notice inline-message invalid-license">%s</div>', 'License Expired' ) );
			}

			$response = json_encode( $decoded_response );

			$result = $this->end_ajax( $response );

			return $result;
		}

		/**
		 * *********************************
		 * Activate the license key for Pro
		 * *********************************
		 *
		 * @since 1.0.0
		 */

		public function ajax_activate_license() {

			$this->check_ajax_referer( 'activate-license' );

			$key_rules = array(
				'action'      => 'key',
				'license_key' => 'string',
				'context'     => 'key',
				'nonce'       => 'key',
			);
			$this->set_post_data( $key_rules );

			$api_params = array(
				'edd_action' 	=> 'activate_license',
				'license'   	=> $this->state_data['license_key'],
				'item_id' 		=> ANALYTIFY_PRO_ID,
				'url'       	=> home_url(),
			);

			$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, ANALYTIFY_STORE_URL ) ), array( 'timeout' => 15, 'sslverify' => false ) );
			//var_dump($response);
			//wp_die('activate debug');

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {

				return false;
			}

			$license_response = json_decode( wp_remote_retrieve_body( $response ) );

			if ( 'valid' === $license_response->license ) {

				$this->set_license_key( $this->state_data['license_key'] );
        $license_response->masked_license = $this->get_formatted_masked_license( $this->state_data['license_key'] );

        $this->remove_expiry_transient();

			} else {

				if ( 'invalid' === $license_response->license ) {

					set_site_transient( 'wpanalytify_license_response', $license_response, $this->transient_timeout );
					$license_response->error = $this->get_license_status_message( $license_response, $this->state_data['context'] );

				}
			}

			// $license_response->license will be either "valid" or "invalid"
			update_option( 'analytify_license_status', $license_response->license );
      update_option( 'analytify_license_key' , $this->state_data['license_key'] );

			$result = $this->end_ajax( json_encode( $license_response ) );

			return $result;
		}


		// function analytify_activate_license() {
		// listen for our activate button to be clicked
		// if( isset( $_POST['analytify_license_activate'] ) ) {
		// run a quick security check
		// if( ! check_admin_referer( 'analytify_nonce', 'analytify_nonce' ) )
		// return; // get out if we didn't click the Activate button
		// retrieve the license from the database
		// $license = trim( get_option( 'analytify_license_key' ) );
		// data to send in our API request
		// $api_params = array(
		// 'edd_action'=> 'activate_license',
		// 'license'   => $license,
		// 'item_name' => urlencode( ANALYTIFY_PRODUCT_NAME ), // the name of our product in EDD
		// 'url'       => home_url()
		// );
		// Call the custom API.
		// $response = wp_remote_get( esc_url_raw(add_query_arg( $api_params, ANALYTIFY_STORE_URL )), array( 'timeout' => 15, 'sslverify' => false ) );
		// make sure the response came back okay
		// if ( is_wp_error( $response ) )
		// return false;
		// decode the license data
		// $license_data = json_decode( wp_remote_retrieve_body( $response ) );
		// print_r($license_data);
		// if( $license_data->license === 'valid')
		// delete_site_transient( 'wp_analytify_check_license_expiration' );
		// $license_data->license will be either "valid" or "invalid"
		// echo $license_data->license ;
		// update_option( 'analytify_license_status', $license_data->license );
		// }
		// }
		/**
		 * ********************************************
		 * Illustrates how to deactivate a license key.
		 * This will descrease the site count
		 ***********************************************/

		// public function analytify_license_deactivate(){
		// retrieve the license from the database
		// $license = trim( get_option( 'analytify_license_key' ) );
		// data to send in our API request
		// $api_params = array(
		// 'edd_action'=> 'deactivate_license',
		// 'license'   => $license,
		// 'item_name' => urlencode( ANALYTIFY_PRODUCT_NAME ), // the name of our product in EDD
		// 'url'       => home_url()
		// );
		// Call the custom API.
		// $response = wp_remote_get( esc_url_raw(add_query_arg( $api_params, ANALYTIFY_STORE_URL )), array( 'timeout' => 15, 'sslverify' => false ) );
		// make sure the response came back okay
		// if ( is_wp_error( $response ) )
		// return false;
		// decode the license data
		// $license_data = json_decode( wp_remote_retrieve_body( $response ) );
		// $license_data->license will be either "deactivated" or "failed"
		// if( $license_data->license == 'deactivated' ){
		// delete_option( 'analytify_license_key' );
		// unset($license);
		// delete_option( 'analytify_license_status' );
		// }
		// wp_die();
		// }
		// function analytify_deactivate_license() {
		// listen for our activate button to be clicked
		// if( isset( $_POST['analytify_license_deactivate'] ) ) {
		// run a quick security check
		// if( ! check_admin_referer( 'analytify_nonce', 'analytify_nonce' ) )
		// return; // get out if we didn't click the Activate button
		// retrieve the license from the database
		// $license = trim( get_option( 'analytify_license_key' ) );
		// data to send in our API request
		// $api_params = array(
		// 'edd_action'=> 'deactivate_license',
		// 'license'   => $license,
		// 'item_name' => urlencode( ANALYTIFY_PRODUCT_NAME ), // the name of our product in EDD
		// 'url'       => home_url()
		// );
		// Call the custom API.
		// $response = wp_remote_get( esc_url_raw(add_query_arg( $api_params, ANALYTIFY_STORE_URL )), array( 'timeout' => 15, 'sslverify' => false ) );
		// make sure the response came back okay
		// if ( is_wp_error( $response ) )
		// return false;
		// decode the license data
		// $license_data = json_decode( wp_remote_retrieve_body( $response ) );
		// $license_data->license will be either "deactivated" or "failed"
		// if( $license_data->license == 'deactivated' ){
		// delete_option( 'analytify_license_key' );
		// unset($license);
		// delete_option( 'analytify_license_status' );
		// }
		// }
		// }
		/**
		 *	Check and Dismiss review message.
		 *
		 *	@since 1.3
		 */
		private function review_dismissal() {

			// delete_site_option( 'wp_analytify_review_dismiss' );
			if ( ! is_admin() ||
				! current_user_can( 'manage_options' ) ||
				! isset( $_GET['_wpnonce'] ) ||
				! wp_verify_nonce( $_GET['_wpnonce'], 'analytify-review-nonce' ) ||
				! isset( $_GET['wp_analytify_review_dismiss'] ) ) {

				return;
			}

			add_site_option( 'wp_analytify_review_dismiss', 'yes' );
		}


		/**
		 * Display a license activation message to let users know
		 * that they can get Automatic Updates and Support
		 *
		 * @since  1.3.3
		 * @return void
		 */
		function wpa_plugin_row( $plugin_path, $plugin_data ) {

			$plugin_title     = $plugin_data['Name'];
			$plugin_slug      = sanitize_title( $plugin_title );
			$license          = $this->get_license_key();
			$license_response = $this->is_license_expired();
			$license_problem  = isset( $license_response['errors'] );

			if ( ! ANALYTIFY_PRO_VERSION ) {
				$installed_version = '0';
			} else {
				$installed_version = ANALYTIFY_PRO_VERSION;
			}

			$latest_version = $this->get_latest_version( $license );

			$new_version = '';
			if ( version_compare( $installed_version, $latest_version, '<' ) ) {
				$new_version = sprintf( __( 'There is a new version of %s available.', 'wp-analytify-pro' ), $plugin_title );
				$new_version .= ' <a class="thickbox" title="' . $plugin_title . '" href="plugin-install.php?tab=plugin-information&plugin=' . rawurlencode( $plugin_slug ) . '&TB_iframe=true&width=640&height=808">';
				$new_version .= sprintf( __( 'View version %s details', 'wp-analytify-pro' ), $latest_version ) . '</a>.';
			}

			if ( ! $new_version && ! empty( $license ) ) {
				return;
			}

			if ( empty( $license ) ) {
				$settings_link = sprintf( '<a href="%s">%s</a>', network_admin_url( $this->plugin_base ) . '#settings', _x( 'Settings', 'Plugin configuration and preferences', 'wp-analytify-pro' ) );
				if ( $new_version ) {
					$message = sprintf( __( 'To update, go to %1$s and enter your license key. If you don\'t have a license key, you may <a href="%2$s">purchase one</a>.', 'wp-analytify-pro' ), $settings_link, 'http://analytify.io/pricing/' );
				} else {
					$message = sprintf( __( 'To finish activating %1$s, please go to %2$s and enter your license key. If you don\'t have a license key, you may <a href="%3$s">purchase one</a>.', 'wp-analytify-pro' ), $this->plugin_title, $settings_link, 'http://analytify.io/pricing/' );
				}
			} elseif ( $license_problem ) {
				$message = array_shift( $license_response['errors'] ) . sprintf( ' <a href="#" class="check-my-license-again">%s</a>', __( 'Check my license again', 'wp-analytify-pro') );
			} else {
				return;
			}

			?>

			<tr class="plugin-update-tr wpanalytifypro-custom">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<span class="wpanalytify-new-version-notice"><?php var_dump( $license ); ?></span>
						<span class="wpanalytify-license-error-notice"><?php // echo $this->get_license_status_message( null, 'update' ); ?></span>
					</div>
				</td>
			</tr>

			<?php if ( $new_version ) { // removes the built-in plugin update message
				?>
				<script type="text/javascript">
					(function( $ ) {
						var wpanalytify_row = jQuery( '#<?php echo $plugin_slug; ?>' ),
							next_row = wpanalytify_row.next();

						// If there's a plugin update row - need to keep the original update row available so we can switch it out
						// if the user has a successful response from the 'check my license again' link
						if ( next_row.hasClass( 'plugin-update-tr' ) && !next_row.hasClass( 'wpanalytifypro-custom' ) ) {
							var original = next_row.clone();
							original.add;
							next_row.html( next_row.next().html() ).addClass( 'wpanalytifypro-custom-visible' );
							next_row.next().remove();
							next_row.after( original );
							original.addClass( 'wpanalytify-original-update-row' );
						}
					})( jQuery );
				</script>
				<?php
			} ?>

			<style type="text/css">

/*			 .plugin-update-tr .update-message {
			 	font-size: 13px;
			 	font-weight: 400;
			 	margin: 0 10px 8px 31px;
			 	padding: 6px 12px 8px 40px;
			 	background-color: #f7f7f7;
			 	background-color: rgba(0,0,0,.03);
			 }
			 .plugin-update .update-message {
			 	background-color: #fcf3ef;
			 }*/

			</style>
<!-- 			 <tr class="plugin-update-tr wpanalytifypro-custom">
			 		<td colspan="3" class="plugin-update">
			 			<div class="update-message">
			 				<span class=""></span>
			 			</div>
			 		</td>
			 	</tr> -->

				<?php
		}

		function is_update_available() {

			if ( ! ANALYTIFY_PRO_VERSION ) {
				$installed_version = '0';
			} else {
				$installed_version = ANALYTIFY_PRO_VERSION;
			}

			$latest_version = get_site_transient( 'wp_analytify_check_latest_version' );

			if ( false === $latest_version or empty( $latest_version )) {
				$latest_version = $this->get_latest_version( '8a97ba2fd7460564b494427811ade113' );
				set_site_transient( 'wp_analytify_check_latest_version', $latest_version, 60 * 60 * 24 );
			}

			if ( version_compare( $installed_version, $latest_version, '<' ) ) {

				// echo sprintf( esc_html__( '%1$s %2$s %3$s Update Available %4$s &mdash; %9$s %10$s is now available. You currently have %11$s installed.  %5$s Changelog %6$s  %7$s %8$s', 'wp-analytify-pro' ), '<div class="notice notice-warning">', '<p>', '<b>', '</b>', '<a style="text-decoration:none" target="_blank" href="https://analytify.io/changelog/?utm_campaign=WPAnalytifyPro+UpdateAvailable&utm_medium=link&utm_source=WPAnalytifyPro+UpdateAvailable&utm_content=update-available-notice">', '</a>','</p>', '</div>', 'WP Analytify Pro', $latest_version, $installed_version );
						
				$message = sprintf( esc_html__( '%1$s Update Available %2$s &mdash; %5$s %6$s is now available. You currently have %7$s installed. %3$s Changelog %4$s', 'wp-analytify-pro' ), '<b>', '</b>', '<a style="text-decoration:none" target="_blank" href="https://analytify.io/changelog/?utm_campaign=WPAnalytifyPro+UpdateAvailable&utm_medium=link&utm_source=WPAnalytifyPro+UpdateAvailable&utm_content=update-available-notice">', '</a>', 'WP Analytify Pro', $latest_version, $installed_version );
					
				wp_analytify_pro_notice(  $message, 'wp-analytify-success' );
			}
					
			// $update_url = wp_nonce_url( network_admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( plugin_basename( __FILE__ ) ) ), 'upgrade-plugin_' . plugin_basename( __FILE__ ) );

		}


		public function wp_analytify_pro_setting_tabs( $old_tabs ) {

			$pro_tabs = array(
				array(
					'id' => 'wp-analytify-front',
					'title' => __( 'Front', 'wp-analytify-pro' ),
					'desc' => __( 'Following are the settings for front-end side. Google Analytics will appear under the posts, custom post types or pages at front-end.', 'wp-analytify-pro' ),
					'priority' => '15',
				),
				array(
					'id' => 'wp-analytify-dashboard',
					'title' => __( 'Dashboard', 'wp-analytify-pro' ),
					'desc' => 'Following settings will take effect statistics on Analytify Dashboard(s) reports only.',
					'priority' => '25',
				),
				array(
					'id' => 'wp-analytify-license',
					'title' => __( 'License', 'wp-analytify-pro' ),
					'priority' => '40'
				)
			);

			return array_merge( $old_tabs,$pro_tabs );

		}


		public function wp_analytify_pro_accordion_setting( $old_tabs ){
			
			$setting_tabs = apply_filters( 'wp_analytify_pro_setting_tabs', $old_tabs );

		}


		public function wp_analytify_pro_setting_fields( $old_fields ) {

			$pro_fields = array(
				'wp-analytify-front' => array(
					array(
						'name'              => 'disable_front_end',
						'label'             => __( 'Disable front-end', 'wp-analytify-pro' ),
						'desc'              => __( 'Enable this if you don\'t want Analytics to load at all. You can still use shortcodes.', 'wp-analytify-pro' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'show_analytics_roles_front_end',
						'label'             => analytify__( 'Show Analytics to (roles)', 'wp-analytify' ),
						'desc'              => analytify__( 'Show analytics to the above selected user roles only.', 'wp-analytify' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => WP_Analytify_Settings::get_current_roles(),
					),

					array(
						'name'              => 'show_analytics_post_types_front_end',
						'label'             => __( 'Enable analytics on post types', 'wp-analytify-pro' ),
						'desc'              => __( 'Show analytics below these post types only.', 'wp-analytify-pro' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => WP_Analytify_Settings::get_current_post_types(),
					),

					array(
						'name'              => 'show_panels_front_end',
						'label'             => __( 'Front-end analytics panels', 'wp-analytify-pro' ),
						'desc'              => __( 'Select which statistic panels you want to display on the front-end.', 'wp-analytify-pro' ),
						'type'              => 'Chosen',
						'default' 			=> array(),
						'options' => array(
							'show-overall-front'  => analytify__( 'General Stats', 'wp-analytify' ),
							'show-country-front'  => __( 'Country Stats', 'wp-analytify-pro' ),
							'show-keywords-front' => analytify__( 'Keywords Stats', 'wp-analytify' ),
							'show-social-front'   => analytify__( 'Social Media Stats', 'wp-analytify' ),
							'show-browser-front'  =>  __( 'Browser Stats', 'wp-analytify-pro' ),
							'show-referrer-front' => analytify__( 'Referrers Stats', 'wp-analytify' ),
							'show-mobile-front'   =>  __( 'Mobile devices Stats', 'wp-analytify-pro' ),
							'show-os-front'       =>  __( 'Operating System Stats', 'wp-analytify-pro' ),
							'show-city-front'     =>  __( 'City Stats', 'wp-analytify-pro' ),
						),
					),
					array(
						'name'              => 'exclude_pages_front_end',
						'label'             => analytify__( 'Exclude analytics on specific pages', 'wp-analytify' ),
						'desc'              => __( 'Enter a comma separated list of the post/page ID\'s you do not want to show analytics for. Example: 21,44,66', 'wp-analytify-pro' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),

				'wp-analytify-dashboard' => array(
					array(
						'name'              => 'delete_dashboard_cache',
						'label'             => __( 'Delete cache', 'wp-analytify-pro' ),
						'desc'              => __( 'The Analytify dashboard (except real-time statistics) saves results for 24 hours. Enable this if you want to retrieve the current statistics.', 'wp-analytify-pro' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'show_analytics_roles_dashboard',
						'label'             => analytify__( 'Show analytics to (roles)', 'wp-analytify' ),
						'desc'              => __( 'Show the Analytify dashboard to the above-selected user roles only.', 'wp-analytify-pro' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => WP_Analytify_Settings::get_current_roles(),
					),

					array(
						'name'              => 'show_analytics_panels_dashboard',
						'label'             => __( 'Dashboard analytics panels', 'wp-analytify-pro' ),
						'desc'              => __( 'Choose the panels to display in Analytify dashboard.', 'wp-analytify-pro' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => array(
							'show-real-time'             =>  __( 'RealTime Stats', 'wp-analytify-pro' ),
							'show-compare-stats' 				 =>  __( 'Comparison Stats', 'wp-analytify-pro' ),
							'show-overall-dashboard'     => analytify__( 'General Stats', 'wp-analytify' ),
							'show-top-pages-dashboard'   => __( 'Top Pages Stats', 'wp-analytify-pro' ),
							'show-geographic-dashboard'  => analytify__( 'Geographic Stats', 'wp-analytify' ),
							'show-system-stats'          => analytify__( 'System Stats', 'wp-analytify' ),
							'show-keywords-dashboard'    => analytify__( 'Keywords Stats', 'wp-analytify' ),
							'show-social-dashboard'      => analytify__( 'Social Media Stats', 'wp-analytify' ),
							'show-referrer-dashboard'    => analytify__( 'Referrers Stats', 'wp-analytify' ),
							'show-page-stats-dashboard'  => __( 'Page entrance and exit stats', 'wp-analytify-pro' ),
						),
					),
				),
				// 'wp-analytify-license' => array(
				// array(
				// 'name'    => 'license_key_pro',
				// 'label'   => __( 'Analytify PRO (License Key):', 'wp-analytify' ),
				// 'desc'    => __( 'Text input description', 'wp-analytify' ),
				// 'type'    => 'text',
				// 'default' => 'Enter your license key'
				// ),
				// array(
				// 'name'    => 'btn_pro_activate',
				// 'label'   => __( '', 'wp-analytify' ),
				// 'desc'    => __( '', 'wp-analytify' ),
				// 'type'    => 'button',
				// 'default' => 'Activate'
				// )
				// ),
				);

				return array_merge( $old_fields, $pro_fields );
		}

		/**
		 * Check for wpanalytify-remove-license and related nonce
		 * if found cleanup routines related to licensed product
		 *
		 * @since 2.0
		 *
		 * @return void
		 */
		function http_remove_license() {

			if ( isset( $_GET['wpanalytify-remove-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-license' ) ) {

				delete_option( 'analytify_license_key' );
				delete_option( 'analytify_license_status' );
				$this->load_settings['license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}

			if ( isset( $_GET['wpanalytify-remove-woo-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-woo-license' ) ) {

				delete_option( 'analytify_woo_license_key' );
				delete_option( 'analytify_woo_license_status' );
				$this->load_settings['woo_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_woo_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}

			if ( isset( $_GET['wpanalytify-remove-edd-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-edd-license' ) ) {

				delete_option( 'analytify_edd_license_key' );
				delete_option( 'analytify_edd_license_status' );
				$this->load_settings['edd_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_edd_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}

			if ( isset( $_GET['wpanalytify-remove-email-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-email-license' ) ) {

				delete_option( 'analytify_email_license_key' );
				delete_option( 'analytify_email_license_status' );
				$this->load_settings['email_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_email_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}

			if ( isset( $_GET['wpanalytify-remove-campaigns-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-campaigns-license' ) ) {

				delete_option( 'analytify_campaigns_license_key' );
				delete_option( 'analytify_campaigns_license_status' );
				$this->load_settings['campaigns_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_campaigns_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}

			// Removes/Reset Goals addon license key
			if ( isset( $_GET['wpanalytify-remove-goals-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-goals-license' ) ) {

				delete_option( 'analytify_goals_license_key' );
				delete_option( 'analytify_goals_license_status' );
				$this->load_settings['goals_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_goals_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}

			// Removes/Reset Forms addon license key
			if ( isset( $_GET['wpanalytify-remove-forms-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-forms-license' ) ) {

				delete_option( 'analytify_forms_license_key' );
				delete_option( 'analytify_forms_license_status' );
				$this->load_settings['forms_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_forms_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}
			
			// Removes/Reset Authors addon license key
			if ( isset( $_GET['wpanalytify-remove-authors-license'] ) && wp_verify_nonce( $_GET['nonce'], 'wpanalytify-remove-authors-license' ) ) {

				delete_option( 'analytify_authors_license_key' );
				delete_option( 'analytify_authors_license_status' );
				$this->load_settings['authors_license'] = '';
				update_site_option( 'wpanalytify_settings', $this->settings );
				// delete these transients as they contain information only valid for authenticated license holders
				delete_site_transient( 'update_plugins' );
				delete_site_transient( 'wpanalytify_upgrade_data' );
				delete_site_transient( 'wpanalytify_authors_license_response' );
				// redirecting here because we don't want to keep the query string in the web browsers address bar
				wp_redirect( network_admin_url( $this->plugin_settings_base . '#wp-analytify-license' ) );
				exit;
			}			

		}

		function load_pro_settings_assets() {
			$this->http_remove_license();
    	}
	
		
		/**
		 * Delete the license expiry transient
		 * This will remove the notice for licnes expiry.
		 * 
		 */
		private function remove_expiry_transient() {
			$license_expire_trans = get_site_transient( 'wp_analytify_check_license_expiration' );

			if( false !== $license_expire_trans || ! empty( $license_expire_trans ) ) {
				delete_site_transient( 'wp_analytify_check_license_expiration' );
			}
		}


		/**
		* Add option in admin bar.
		* @param [array] $menus.
		*
		* @since 2.0.9
		*/
		function add_admin_bar_menu( $menus ) {

			$menus['analytify-dashboard&show=detail-realtime'] = __( 'Real Time', 'wp-analytify-pro' ) ;
			$menus['analytify-dashboard&show=search-terms'] = __( 'Search Terms', 'wp-analytify-pro' );
			$menus['analytify-dashboard&show=detail-demographic'] = __( 'Demographics', 'wp-analytify-pro' );

			return $menus;
		}

		/**
		 * Add Top Pages Export Icon.
		 *
		 * @since 2.0.17
		 */
		function top_page_text() {
			?>
			<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-pages">
				<span class="analytify_tooltiptext"><?php _e( 'Export Top Pages', 'wp-analytify-pro' ) ?></span>
			</a>
			<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			<?php
		}

		/**
		 * Add Top Keywords Export Icon.
		 *
		 * @since 2.0.17
		 */
		function top_keyword_text() {
			?>
			<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-keywords">
				<span class="analytify_tooltiptext"><?php _e( 'Export Top Keywords', 'wp-analytify-pro' ) ?></span>
			</a>
			<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			<?php
		}

		/**
		 * Add Top Social Media Export Icon.
		 *
		 * @since 2.0.17
		 */
		function top_social_media_text() {
			?>
			<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-social-media">
				<span class="analytify_tooltiptext"><?php _e( 'Export Top Social Media', 'wp-analytify-pro' ) ?></span>
			</a>
			<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			<?php
		}

		/**
		 * Add Top Reffers Export Icon.
		 *
		 * @since 2.0.17
		 */
		function top_reffers_text() {
			?>
			<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-reffers">
				<span class="analytify_tooltiptext"><?php _e( 'Export Top Reffers', 'wp-analytify-pro' ) ?></a>
			<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			<?php
		}

		/**
		 * Add What Happens Export Icon.
		 *
		 * @since 2.0.17
		 */
		function top_page_stats_text() {
			?>
			<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="what-happen">
				<span class="analytify_tooltiptext"><?php _e( 'Export Stats', 'wp-analytify-pro' ) ?></a>
			<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			<?php
		}

		/**
		 * Add Top Mobiles Export Icon.
		 *
		 * @since 2.0.17
		 */
		function after_top_mobile_device_text() {
			?>
			<span class="analytify_top_geographic_detials analytify_tp_btn">
				<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-mobile-device">
					<span class="analytify_tooltiptext"><?php _e( 'Export Top Mobile Device', 'wp-analytify-pro' ) ?></span>
				</a>
				<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			</span>
			<?php
		}

		/**
		 * Add Top Operating System Export Icon.
		 *
		 * @since 2.0.17
		 */
		function after_top_operating_system_text() {
			?>
			<span class="analytify_top_geographic_detials analytify_tp_btn">
				<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-operating-system">
					<span class="analytify_tooltiptext"><?php _e( 'Export Top Operating System', 'wp-analytify-pro' ) ?></span>
				</a>
				<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			</span>
			<?php
		}

		/**
		 * Add Top Browsers Export Icon.
		 *
		 * @since 2.0.17
		 */
		function after_top_browser_text() {
			?>
			<span class="analytify_top_geographic_detials analytify_tp_btn">
				<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-browsers">
					<span class="analytify_tooltiptext"><?php _e( 'Export Top Browsers', 'wp-analytify-pro' ) ?></span>
				</a>
				<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			</span>
			<?php
		}

		/**
		 * Add Top Cities Export Icon.
		 *
		 * @since 2.0.17
		 */
		function after_top_city_text() {
			?>
			<span class="analytify_top_geographic_detials analytify_tp_btn">
				<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-cities">
					<span class="analytify_tooltiptext"><?php _e( 'Export Top Cities', 'wp-analytify-pro' ) ?></span>
				</a>
				<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			</span>
			<?php
		}

		/**
		 * Add Top Countries Export Icon.
		 *
		 * @since 2.0.17
		 */
		function after_top_country_text() {
			?>
			<span class="analytify_top_geographic_detials analytify_tp_btn">
				<a href="#" class="analytify-export-data analytify_tooltip" data-stats-type="top-countries">
					<span class="analytify_tooltiptext"><?php _e( 'Export Top Countries', 'wp-analytify-pro' ) ?></span>
				</a>
				<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class='analytify-export-loader' style="display:none">
			</span>
			<?php
		}

		/**
		 * Generate CSV.
		 *
		 * @since 2.0.17
		 * @return CSV
		 */
		function generate_csv() {

			// check security nounce.
			if ( isset( $_GET ['security'] ) &&  wp_verify_nonce( $_GET['security'], 'analytify_export_nonce' ) && 'analytify_export' == $_GET ['action'] ) {

				$name = $_GET['report_type'] . '-' . $_GET['start_date'] . '|' . $_GET['end_date'] . '.csv';
				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="'. $name .'"' );

				// do not cache the file
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				// create a file pointer connected to the output stream
				$file = fopen( 'php://output', 'w' );

				$data = get_option( 'analytify_csv_data' );

				foreach ( $data as $value) {
					fputcsv( $file, $value );
				}
				exit();
			}
		}

	} // End if class_exists check

}

/**
 * Accordions settings callback.
 *
 */
function wp_analytify_tracking_accordion_options( $accordion_id ) {	?>
	
	<form method="post" action="options.php">
		
		<?php
		settings_fields( $accordion_id );
		$GLOBALS['WP_ANALYTIFY']->settings->do_settings_sections( $accordion_id ); ?>
		
		<div style="padding-left: 10px">
			<?php submit_button(); ?>
		</div>
	</form>
	
	<?php
}

add_action( 'wp_analytify_tracking_accordion_options', 'wp_analytify_tracking_accordion_options' );
