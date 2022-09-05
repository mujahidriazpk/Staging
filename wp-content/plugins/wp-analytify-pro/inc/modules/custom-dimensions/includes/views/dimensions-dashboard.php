<?php
/**
 * Analytify Dashboard file.
 *
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
$start_date_val = strtotime( '-1 month' );
$end_date_val   = strtotime( 'now' );
$start_date     = date( 'Y-m-d', $start_date_val );
$end_date       = date( 'Y-m-d', $end_date_val );
$selected_stats = $wp_analytify->settings->get_option( 'show_analytics_panels_dashboard', 'wp-analytify-dashboard', array() );

if ( isset( $_POST['analytify_date_diff'] ) && ! empty( $_POST['analytify_date_diff'] ) ) {
	update_option( 'analytify_date_differ', sanitize_text_field($_POST['analytify_date_diff']) );
}

$_differ = get_option( 'analytify_date_differ' );

if ( $_differ ) {
	if ( $_differ == 'last_7_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
	} elseif ( $_differ == 'last_14_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-14 days' ) );
	} elseif ( $_differ == 'last_30_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-1 month' ) );
	} elseif ( $_differ == 'this_month' ) {
		$start_date = date( 'Y-m-01' );
	} elseif ( $_differ == 'last_month' ) {
		$start_date = date( 'Y-m-01', strtotime( '-1 month' ) );
		$end_date   = date( 'Y-m-t', strtotime( '-1 month' ) );
	} elseif ( $_differ == 'last_3_months' ) {
		$start_date = date( 'Y-m-01', strtotime( '-3 month' ) );
		$end_date   = date( 'Y-m-t', strtotime( '-1 month' ) );
	} elseif ( $_differ == 'last_6_months' ) {
		$start_date = date( 'Y-m-01', strtotime( '-6 month' ) );
		$end_date   = date( 'Y-m-t', strtotime( '-1 month' ) );
	} elseif ( $_differ == 'last_year' ) {
		$start_date = date( 'Y-m-01', strtotime( '-1 year' ) );
		$end_date   = date( 'Y-m-t', strtotime( '-1 month' ) );
	}
}

if ( isset( $_POST['view_data'] ) ) {
	$s_date  = sanitize_text_field( wp_unslash( $_POST['st_date'] ) );
	$ed_date = sanitize_text_field( wp_unslash( $_POST['ed_date'] ) );
}

if ( isset( $_POST['analytify_date_start'] ) && ! empty( $_POST['analytify_date_start'] ) && isset( $_POST['analytify_date_end'] ) && ! empty( $_POST['analytify_date_end'] ) ) {
	$start_date	= sanitize_text_field( wp_unslash( $_POST['analytify_date_start'] ) );
	$end_date	= sanitize_text_field( wp_unslash( $_POST['analytify_date_end'] ) );
}

$date1					= date_create( $start_date );
$date2					= date_create( $end_date );
$diff					= date_diff( $date2, $date1 );
$compare_start_date		= strtotime( $start_date . $diff->format( '%R%a days' ) );
$compare_start_date		= date( 'Y-m-d', $compare_start_date );
$compare_end_date		= $start_date;
$dashboard_profile_ID	= $wp_analytify->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
$fetch_fresh			= $wp_analytify->settings->get_option( 'delete_dashboard_cache', 'wp-analytify-dashboard' );
$acces_token			= get_option( 'post_analytics_token' );
$_analytify_profile		= get_option( 'wp-analytify-profile' );
$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard' );

/*
* Check with roles assigned at dashboard settings.
*/
$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION;

wp_dequeue_script( 'tribe-common' ); ?>

<div class="wpanalytify analytify-dashboard-nav">
	<div class="wpb_plugin_wraper">
		<div class="wpb_plugin_header_wraper">
			<div class="graph"></div>
			<div class="wpb_plugin_header">
				<div class="wpb_plugin_header_title"></div>
				<div class="wpb_plugin_header_info">
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">Changelog - v<?php echo $version; ?></a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="<?php echo ANALYTIFY_PLUGIN_URL . '/assets/images/logo.svg'?>" alt="Analytify">
				</div>
			</div>
		</div>
				
		<div class="analytify-dashboard-body-container">
			<div class="wpb_plugin_body_wraper">
				<div class="wpb_plugin_body">
					<div class="wpa-tab-wrapper">
						<?php echo $wp_analytify->dashboard_navigation(); ?>
					</div>

					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
					<div class="analytify_wraper">
						<div class="analytify_main_title_section">
							<div class="analytify_dashboard_title">
								<h1 class="analytify_pull_left analytify_main_title"><?php _e( 'Dimensions Dashboard', 'wp-analytify-pro' ); ?></h1>
																
								<?php
								if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {
									if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {
										if ( $acces_token ) {
											if ( $acces_token && isset( $_analytify_profile['profile_for_dashboard'] ) && ! empty( $_analytify_profile['profile_for_dashboard'] ) ) { ?>
											
												<span class="analytify_stats_of"><a href="<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?>" target="_blank"><?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?></a> (<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'name' ) ?>)</span>
											
											<?php 
											} 
										}
									}
								} ?>


							</div>

							<?php 
							if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {
								if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {
									if ( $acces_token ) {
										if ( method_exists( 'WPANALYTIFY_Utils', 'date_form' )  ) {
											?>
											<div class="analytify_main_setting_bar">
												<div class="analytify_pull_right analytify_setting">
													<div class="analytify_select_date">
														<?php WPANALYTIFY_Utils::date_form( $start_date, $end_date ); ?>
													</div>
												</div>
											</div>
											<?php
										} 
									}
								}
							} ?>

						</div>

						<?php 
						if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {

							/*
							* Check with roles assigned at dashboard settings.
							*/
							$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );
							
							// Show dashboard to admin incase of empty access roles.
							if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }

							$report_url        = WP_ANALYTIFY_FUNCTIONS::get_ga_report_url( $dashboard_profile_ID ) ;
							$report_date_range = WP_ANALYTIFY_FUNCTIONS::get_ga_report_range( $start_date, $end_date, $compare_start_date, $compare_end_date ); 
							if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

								if ( $acces_token ) { ?>

									<?php
									$dimensions = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytiy_custom_dimensions', 'wp-analytify-custom-dimensions' );

									if ( ! empty( $dimensions ) ) {
										$i = 1;

										foreach ( $dimensions as $key => $value ) {
											$type  = $value['type'];
											$id    = $value['id'];
											$label = ucfirst( str_replace('_', ' ', $type) );

											if ( ! empty( $id ) ) {

												if ( 'logged_in' === $type || 'user_id' === $type ) {
													$dimension_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:dimension' . $id, '-ga:sessions', false, '10', 'show-sessions-dimensions-stats-' . $type );
													$total = $dimension_stats['totalsForAllResults']['ga:sessions'];
												} else {
													$dimension_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:pageviews', $start_date, $end_date, 'ga:dimension' . $id, '-ga:pageviews', false, '10', 'show-pageviews-dimensions-stats-' . $type );
													$total = $dimension_stats['totalsForAllResults']['ga:pageviews'];
												}

												$dimension_row = isset( $dimension_stats['rows'] ) ? $dimension_stats['rows'] : false;

												if ( 1 === $i % 2 ) {
													echo '<div class="analytify_column"><div class="analytify_half analytify_left_flow">';
												} else {
													echo '<div class="analytify_half analytify_right_flow">';
												}
												if ( $dimension_row ){
												?>
													<div class="analytify_general_status analytify_status_box_wraper">
														<div class="analytify_status_header analytify_header_adj">
															<h3><?php echo $label; ?></h3>
															<div class="analytify_status_header_value">
																<span class="analytify_medium_f"><?php _e( 'Total', 'wp-analytify-pro' ); ?></span> <?php echo $total; ?>
															</div>
														</div>
														<div class="analytify_status_body">
															<div class="analytify_dimension_pageviews_stats_boxes_wraper">
																<table class="analytify_data_tables">
																	<tbody>
																		<?php
																		foreach ( $dimension_row as $view ){
																		?>
																			<tr>
																				<td><?php echo $view[0]; ?></td>
																				<td class="analytify_txt_center analytify_value_row"><?php echo $view[1]; ?></td>
																			</tr>
																		<?php
																		}
																		?>
																	</tbody>
																</table>
															</div>
														</div>
														<?php /*<div class="analytify_status_footer">
															<span class="analytify_info_stats"><?php // echo $label; ?> views to your website</span>
														</div>*/ ?>
													</div>

												<?php
												}
												$i++;

												if ( 1 === $i%2 ) {
													echo '</div></div>';
												}else{ echo '</div>'; }

											}

										}

									} else {
										echo '<div class="analytify-stats-error-msg">
										<div class="wpb-error-box">
											<span class="blk">
												<span class="line"></span>
												<span class="dot"></span>
											</span>
											<span class="information-txt">'.__('No dimension is set. Please set dimensions from the settings.', 'wp-analytify-pro').'</span>
										</div>
										</div>';
									}
									
								} else {
									_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-analytify-pro' );
								}

							} else {
								_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify-pro' );
							}

						} ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>