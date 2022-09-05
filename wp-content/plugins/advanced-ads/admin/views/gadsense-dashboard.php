<?php

$pub_id = Advanced_Ads_AdSense_Data::get_instance()->get_adsense_id();
if ( $pub_id ) {
	Advanced_Ads_Overview_Widgets_Callbacks::adsense_stats_js( $pub_id );
	$arguments = array(
		'type'   => $report_type,
		'filter' => $report_filter,
	);
	$report    = new Advanced_Ads_AdSense_Report( $report_type, $report_filter );

	echo '<div class="advanced-ads-adsense-dashboard" data-arguments="' . esc_js( wp_json_encode( $arguments ) ) . '">';
	echo wp_kses_post( $report->get_markup() );
	echo '</div>';
} else {
	echo esc_html__( 'There is an error in your AdSense setup.', 'advanced-ads' );
}
?>
