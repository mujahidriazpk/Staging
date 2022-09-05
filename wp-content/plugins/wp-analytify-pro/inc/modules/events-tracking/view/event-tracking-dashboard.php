<?php
/**
* Analytify Event Tracking Dashboard File.
*
* @package WP_Analytify
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
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

$date1 = date_create( $start_date );
$date2 = date_create( $end_date );
$diff  = date_diff( $date2, $date1 );

$compare_start_date = strtotime( $start_date . $diff->format( '%R%a days' ) );
$compare_start_date = date( 'Y-m-d', $compare_start_date );
$compare_end_date   = $start_date;

$dashboard_profile_ID = $wp_analytify->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
$fetch_fresh          = $wp_analytify->settings->get_option( 'delete_dashboard_cache', 'wp-analytify-dashboard' );

$acces_token = get_option( 'post_analytics_token' );

if ( ! $acces_token ) {
	return;
} else {
	if ( WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection( 'Analytify' ) ) { return; }
}

// Check with roles assigned at dashboard settings.
$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard' );
$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION;
$_analytify_profile		= get_option( 'wp-analytify-profile' );

// Show dashboard to admin incase of empty access roles.
if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }

if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

	if ( $acces_token ) {
		// dequeue event calendar js
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
											<h1 class="analytify_pull_left analytify_main_title"><?php _e( 'Events Tracking Dashboard', 'wp-analytify-pro' ); ?></h1>
																						
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
										<div class="analytify_main_setting_bar">
											<div class="analytify_pull_right analytify_setting">
												<div class="analytify_select_date">

													<?php 
													if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {
														if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {
															if ( $acces_token ) {
																if ( method_exists( 'WPANALYTIFY_Utils', 'date_form' )  ) {
																	WPANALYTIFY_Utils::date_form( $start_date, $end_date );
																} 
															}
														}
													} ?>

												</div>
											</div>
										</div>
										<!-- <div class="analytify_select_dashboard analytify_pull_right"><?php // do_action( 'analytify_dashboad_dropdown' ); ?></div> -->
									</div>

									<?php $external_links = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventCategory,ga:eventLabel,ga:eventAction', '-ga:totalEvents', 'ga:eventCategory==external', 100, 'show-default-external-links' ); ?>

									<div class="analytify_external_links_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php _e( 'External Links', 'wp-analytify-pro' ); ?></h3>
										</div>

										<div class="analytify_status_body">
											<table class="analytify_data_tables wp_analytify_paginated" data-product-per-page="10">
												<thead>
													<tr>
														<th class="wd_1">#</th>
														<th style="text-align:left"><?php _e( 'Link Text', 'wp-analytify-pro' ); ?></th>
														<th style="text-align:left"><?php _e( 'Link', 'wp-analytify-pro' ); ?></th>
														<th><?php _e( 'Total Click', 'wp-analytify-pro' ); ?></th>
													</tr>
												</thead>
												<tbody>

													<?php
													if ( ! empty( $external_links['rows'] ) ) {
														$i = 0;
														foreach ( $external_links['rows'] as $external_link ) {
															$i++;
															?>

															<tr>
																<td><?php echo $i; ?></td>
																<td><?php echo $external_link[1]; ?></td>
																<td><?php echo $external_link[2]; ?></td>
																<td class="analytify_txt_center"><?php echo number_format( $external_link[3] ); ?></td>
															</tr>

															<?php
														}
													} else { ?>

														<tr>
															<td  class="analytify_td_error_msg" colspan="4">
																<?php $GLOBALS['WP_ANALYTIFY']->no_records(); ?>
															</td>
														</tr>

													<?php } ?>

												</tbody>
											</table>
										</div>
										<div class="analytify_status_footer">
											<div class="wp_analytify_pagination"></div>
										</div>
									</div>
									
									<?php $outbound_links = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventCategory,ga:eventLabel,ga:eventAction', '-ga:totalEvents', 'ga:eventCategory==outbound-link', 100, 'show-default-outbound-link' ); ?>

									<div class="analytify_outbound_links_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php _e( 'Affiliate Links', 'wp-analytify-pro' ); ?></h3>
										</div>

										<div class="analytify_status_body">
											<table class="analytify_data_tables wp_analytify_paginated" data-product-per-page="10">
												<thead>
													<tr>
														<th class="wd_1">#</th>
														<th style="text-align:left"><?php _e( 'Label', 'wp-analytify-pro' ); ?></th>
														<th style="text-align:left"><?php _e( 'Link', 'wp-analytify-pro' ); ?></th>
														<th><?php _e( 'Total Click', 'wp-analytify-pro' ); ?></th>
													</tr>
												</thead>
												<tbody>

													<?php
													if ( ! empty( $outbound_links['rows'] ) ) {
														$i = 0;

														foreach ( $outbound_links['rows'] as $outbound_link ) {
															$i++; ?>

															<tr>
																<td><?php echo $i; ?></td>
																<td><?php echo $outbound_link[1]; ?></td>
																<td><?php echo $outbound_link[2]; ?></td>
																<td class="analytify_txt_center"><?php echo number_format( $outbound_link[3] ); ?></td>
															</tr>

															<?php
														}
													} else { ?>

														<tr>
															<td  class="analytify_td_error_msg" colspan="4">
																<?php $GLOBALS['WP_ANALYTIFY']->no_records(); ?>
															</td>
														</tr>

													<?php } ?>

												</tbody>
											</table>
										</div>
										<div class="analytify_status_footer">
											<div class="wp_analytify_pagination"></div>
										</div>
									</div>

									<?php $download_links = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventCategory,ga:eventLabel,ga:eventAction', '-ga:totalEvents', 'ga:eventCategory==download', 100, 'show-default-download-links' ); ?>

									<div class="analytify_outbound_links_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php _e( 'Download Links', 'wp-analytify-pro' ); ?></h3>
										</div>
										<div class="analytify_status_body">
											<table class="analytify_data_tables wp_analytify_paginated" data-product-per-page="10">
												<thead>
													<tr>
														<th class="wd_1">#</th>
														<th style="text-align:left"><?php _e( 'Label', 'wp-analytify-pro' ); ?></th>
														<th style="text-align:left"><?php _e( 'Links', 'wp-analytify-pro' ); ?></th>
														<th><?php _e( 'Click', 'wp-analytify-pro' ); ?></th>
													</tr>
												</thead>
												<tbody>
												
													<?php
													if ( ! empty( $download_links['rows'] ) ) {
														$i = 0;
														foreach ( $download_links['rows'] as $download_link ) {
															$i++; ?>
															<tr>
																<td><?php echo $i; ?></td>
																<td><?php echo $download_link[1]; ?></td>
																<td><?php echo $download_link[2]; ?></td>
																<td class="analytify_txt_center"><?php echo number_format( $download_link[3] ); ?></td>
															</tr>
															<?php
														}
													} else { ?>
														<tr>
															<td class="analytify_td_error_msg" colspan="4">
																<?php $GLOBALS['WP_ANALYTIFY']->no_records(); ?>
															</td>
														</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
										<div class="analytify_status_footer">
											<div class="wp_analytify_pagination"></div>
										</div>
									</div>

									<?php $tel_links = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventCategory,ga:eventLabel,ga:eventAction', '-ga:totalEvents', 'ga:eventCategory==tel', 100, 'show-default-tel-links' ); ?>

									<div class="analytify_outbound_links_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php _e( 'Tel Links', 'wp-analytify-pro' ); ?></h3>
										</div>
										<div class="analytify_status_body">
											<table class="analytify_data_tables wp_analytify_paginated" data-product-per-page="10">
												<thead>
													<tr>
														<th class="wd_1">#</th>
														<th style="text-align:left"><?php _e( 'Label', 'wp-analytify-pro' ); ?></th>
														<th style="text-align:left"><?php _e( 'Links', 'wp-analytify-pro' ); ?></th>
														<th><?php _e( 'Click', 'wp-analytify-pro' ); ?></th>
													</tr>
												</thead>
												<tbody>
												
													<?php
													if ( ! empty( $tel_links['rows'] ) ) {
														$i = 0;
														foreach ( $tel_links['rows'] as $tel_link ) {
															$i++; ?>

															<tr>
																<td><?php echo $i; ?></td>
																<td><?php echo $tel_link[1]; ?></td>
																<td><?php echo $tel_link[2]; ?></td>
																<td class="analytify_txt_center"><?php echo number_format( $tel_link[3] ); ?></td>
															</tr>
															
															<?php
														}
													} else { ?>

														<tr>
															<td class="analytify_td_error_msg" colspan="4">
																<?php $GLOBALS['WP_ANALYTIFY']->no_records(); ?>
															</td>
														</tr>

													<?php } ?>

												</tbody>
											</table>
										</div>
										<div class="analytify_status_footer">
											<div class="wp_analytify_pagination"></div>
										</div>
									</div>

									<?php $mail_links = $wp_analytify->pa_get_analytics_dashboard( 'ga:totalEvents', $start_date, $end_date, 'ga:eventCategory,ga:eventLabel,ga:eventAction', '-ga:totalEvents', 'ga:eventCategory==mail', 100, 'show-default-tel-links' ); ?>

									<div class="analytify_outbound_links_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php _e( 'Mail Links', 'wp-analytify-pro' ); ?></h3>
										</div>
										<div class="analytify_status_body">
											<table class="analytify_data_tables wp_analytify_paginated" data-product-per-page="10">
												<thead>
													<tr>
														<th class="wd_1">#</th>
														<th style="text-align:left"><?php _e( 'Label', 'wp-analytify-pro' ); ?></th>
														<th style="text-align:left"><?php _e( 'Links', 'wp-analytify-pro' ); ?></th>
														<th><?php _e( 'Click', 'wp-analytify-pro' ); ?></th>
													</tr>
												</thead>
												<tbody>
												
													<?php
													if ( ! empty( $mail_links['rows'] ) ) {
														$i = 0;
														foreach ( $mail_links['rows'] as $mail_link ) {
															$i++; ?>

															<tr>
																<td><?php echo $i; ?></td>
																<td><?php echo $mail_link[1]; ?></td>
																<td><?php echo $mail_link[2]; ?></td>
																<td class="analytify_txt_center"><?php echo number_format( $mail_link[3] ); ?></td>
															</tr>
															
															<?php
														}
													} else { ?>

														<tr>
															<td class="analytify_td_error_msg" colspan="4">
																<?php $GLOBALS['WP_ANALYTIFY']->no_records(); ?>
															</td>
														</tr>

													<?php } ?>

												</tbody>
											</table>
										</div>
										<div class="analytify_status_footer">
											<div class="wp_analytify_pagination"></div>
										</div>
									</div>

									<script type="text/javascript">
										jQuery(document).ready(function($) {
											wp_analytify_paginated();
										});
									</script>

								</div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	} else {
		_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-analytify-pro' );
	}
} else {
	_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify-pro' );
}