<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

$wp_analytify   = $GLOBALS['WP_ANALYTIFY'];

$dashboard_profile_ID = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
$is_access_level =  $GLOBALS['WP_ANALYTIFY']->settings->get_option('show_analytics_roles_dashboard','wp-analytify-dashboard');

$acces_token  = get_option( "post_analytics_token" );

$start_date_val = strtotime( '-1 month' );
$end_date_val   = strtotime( 'now' );
$start_date     = date( 'Y-m-d', $start_date_val );
$end_date       = date( 'Y-m-d', $end_date_val );

if ( isset( $_POST['analytify_date_start'] ) && ! empty( $_POST['analytify_date_start'] ) && isset( $_POST['analytify_date_end'] ) && ! empty( $_POST['analytify_date_end'] ) ) {
	$start_date	= sanitize_text_field( wp_unslash( $_POST['analytify_date_start'] ) );
	$end_date	= sanitize_text_field( wp_unslash( $_POST['analytify_date_end'] ) );
}

$_analytify_profile = get_option( 'wp-analytify-profile' );

$demogragphic_stats = $GLOBALS['WP_ANALYTIFY']->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date,'ga:userAgeBracket,ga:userGender', false, false, 20, 'show-demographic-stats' );
$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION; ?>

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
					<div class="wpa-tab-wrapper"><?php echo $GLOBALS['WP_ANALYTIFY']->dashboard_navigation(); ?></div>
					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
								
						<div class="analytify_wraper">
							<div class="analytify_main_title_section">
								<div class="analytify_dashboard_title">
									<h1 class="analytify_pull_left analytify_main_title"><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?> </h1>

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
							
							<?php 
							if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {

								// Check with roles assigned at dashboard settings.
								$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );
								
								// Show dashboard to admin incase of empty access roles.
								if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }

								if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {
									if ( $acces_token ) { ?>
										
										<div class="analytify_general_status analytify_status_box_wraper">
											<div class="analytify_status_header">
												<h3><?php _e( 'Overview' , 'wp-analytify-pro' ) ?></h3>
											</div>
											<div class="analytify_status_body">
												<table class="analytify_data_tables">
													<thead>
														<tr>
															<th class="analytify_num_row">#</th>
															<th class="analytify_txt_left"><?php esc_html_e( 'Age', 'wp-analytify-pro' ); ?></th>
															<th class="analytify_txt_left"><?php esc_html_e( 'Gender', 'wp-analytify-pro' ); ?></th>
															<th class="analytify_value_row"><?php esc_html_e( 'Total Views', 'wp-analytify-pro' ); ?></th>
														</tr>
													</thead>
													<tbody>
														<?php
														if ( isset( $demogragphic_stats['rows'] ) && $demogragphic_stats['rows'] > 0 ) {
															$i = 1;
															foreach ( $demogragphic_stats['rows'] as $demographic ){ ?>
																<tr>
																	<td class="analytify_txt_center"><?php echo $i; ?></td>
																	<td><?php echo $demographic[0]; ?></td>
																	<td><?php echo $demographic[1]; ?></td>
																	<td class="analytify_txt_center"><?php echo WPANALYTIFY_Utils::pretty_numbers( $demographic[2] ); ?></td>
																</tr>
																<?php
																$i++;
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
												<div class="analytify_status_footer">
													<span class="analytify_info_stats"><?php esc_html_e( 'Demographic Stats', 'wp-analytify-pro' ); ?></span>
												</div>
											</div>
										</div>
								
									<?php
									} else {
										esc_html_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-analytify' );
									}
								} else {
									esc_html_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify' );
								}
							} ?>

						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
