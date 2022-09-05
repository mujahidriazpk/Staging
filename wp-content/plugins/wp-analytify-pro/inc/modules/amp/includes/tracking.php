<?php
function analytify_amp_rest_add_userid( $amp_template ) {	?>
	<meta name="amp-google-client-id-api" content="googleanalytics">
	<meta name="analytify-version" content="<?php echo esc_attr( ANALYTIFY_VERSION ); ?>">
	<meta name="analytify-amp-version" content="<?php echo esc_attr( ANALYTIFY_AMP_VERSION );?>">
	
	<?php
	if ( function_exists( 'analytify_is_track_user' ) && analytify_is_track_user() ) { ?>
		<meta name="analytify-tracking-user" content="true">
	<?php } else { ?>
		<meta name="analytify-tracking-user" content="false">
	<?php }
}
add_action( 'amp_post_template_head', 'analytify_amp_rest_add_userid', 12 );

// Amp canonical pages setup.
function analytify_amp_rest_add_userid_native() {
	if ( ! function_exists( 'amp_is_canonical' ) || ! amp_is_canonical() ) {
		return;
	} ?>

	<meta name="amp-google-client-id-api" content="googleanalytics">
	<meta name="analytify-version" content="<?php echo esc_attr( ANALYTIFY_VERSION ); ?>">
	<meta name="analytify-amp-version" content="<?php echo esc_attr( ANALYTIFY_AMP_VERSION );?>">
	
	<?php
	if ( function_exists( 'analytify_is_track_user' ) && analytify_is_track_user() ) { ?>
		<meta name="analytify-tracking-user" content="true">
	<?php } else { ?>
		<meta name="analytify-tracking-user" content="false">
	<?php }
}
add_action( 'wp_head', 'analytify_amp_rest_add_userid_native' );

// If amp pages are not bering tracked.
function analytify_not_tracking_amp() {
	if ( function_exists( 'analytify_is_track_user' ) && ! analytify_is_track_user() ) {
		echo '<!-- Note: Analytify is not tracking this page as you are either a logged in administrator or a disabled user group. -->';
	}
}
add_filter( 'amp_post_template_footer', 'analytify_not_tracking_amp' );

// If amp pages are not bering tracked for canonical pages setup.
function analytify_not_tracking_amp_native() {
	if ( ! function_exists( 'amp_is_canonical' ) || ! amp_is_canonical() ) {
			return;
	}

	if ( function_exists( 'analytify_is_track_user' ) && ! analytify_is_track_user() ) {
		echo '<!-- Note: Analytify is not tracking this page as you are either a logged in administrator or a disabled user group. -->';
	}
}
add_action( 'wp_footer', 'analytify_not_tracking_amp_native', 9 );

function analytify_amp_add_analytics( $analytics ) {
	// if Yoast is outputting analytics
	// if ( isset( $analytics['yst-googleanalytics'] ) ) {
	// 	return $analytics;
	// }
	
	if ( function_exists( 'analytify_is_track_user' ) && ! analytify_is_track_user() ) {
		return $analytics;
	}

	// $ua = analytify_get_ua_to_output( array( 'amp' => true ) );
	$UA = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
	
	// if there's no UA code set
	if ( empty( $UA ) ) {
		return $analytics;
	}

	$site_url = str_replace( array( 'http:', 'https:'), '',  site_url() );
	$analytics['analytify-googleanalytics'] = array(
		'type' => 'googleanalytics',
		'attributes'  => array(),
		'config_data' => array(
			'vars' => array(
				'account' => $UA,
			),
			'triggers' => array(
				'trackPageview' => array(
					'on'      => 'visible',
					'request' => 'pageview',
				),
			),
		),
	);

	// // Dimensions Addon Integration
	if ( class_exists( 'Analytify_Dimensions_Tracking' ) ) {
		$track_dimensions				= false;
		$options						= $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytiy_custom_dimensions','wp-analytify-custom-dimensions' );
		$Analytify_Dimensions_Tracking	= new Analytify_Dimensions_Tracking();

		foreach ( $options as $key => $value ) {
			$type  = $value['type'];
			$id    = $value['id'];

			switch ( $type ) {
				case 'logged_in' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_logged_in();
				break;
				
				case 'user_id' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_user_id();
				break;
				
				case 'post_type' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_post_type();
				break;
				
				case 'author' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_author();
				break;
				
				case 'category' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_category();
				break;
				
				case 'tags' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_tags();
				break;

				case 'published_at' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_published_at();
				break;

				case 'seo_score' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_seo_score();
				break;

				case 'focus_keyword' :
					$tracking_val = $Analytify_Dimensions_Tracking->track_focus_keyword();
				break;

				default:
				break;
			}

			if ( ! empty( $tracking_val ) ) {
				if ( ! $track_dimensions ) {
					$track_dimensions = true;
				}

				$analytics['analytify-googleanalytics']['config_data']['triggers']['trackPageview']['vars']['cd'.$id] = $tracking_val;
				
				if ( isset( $analytics['analytify-googleanalytics']['config_data']['requests'] ) ) {
					$analytics['analytify-googleanalytics']['config_data']['requests']['pageviewWithCDs'] = $analytics['analytify-googleanalytics']['config_data']['requests']['pageviewWithCDs'] . '&cd' . $id . '=${cd' . $id . '}';
				} else {
					$analytics['analytify-googleanalytics']['config_data']['requests']['pageviewWithCDs'] = '${pageview}' . '&cd' . $id . '=${cd' . $id . '}';
				}
			}
		}

		if ( $track_dimensions ) {
			$analytics['analytify-googleanalytics']['config_data']['triggers']['trackPageview']['request'] = 'pageviewWithCDs';
		}		
	}

	// Track Downloads
	$analytics['analytify-googleanalytics']['config_data']['triggers']['downloadLinks']['on'] = 'click';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['downloadLinks']['selector'] = '.analytify-amp-download';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['downloadLinks']['request'] = 'event';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['downloadLinks']['vars'] = array(
		'eventCategory' => '${category}',
		'eventAction'   => '${action}',
		'eventLabel'    => '${label}',
	);

	// Track Internal as Outbound
	$analytics['analytify-googleanalytics']['config_data']['triggers']['internalAsOutboundLinks']['on'] = 'click';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['internalAsOutboundLinks']['selector'] = '.analytify-internal-as-outbound';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['internalAsOutboundLinks']['request'] = 'event';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['internalAsOutboundLinks']['vars'] = array(
		'eventCategory' => '${category}',
		'eventAction'   => '${action}',
		'eventLabel'    => '${label}',
	);

	// Track External
	$analytics['analytify-googleanalytics']['config_data']['triggers']['outboundLinks']['on'] = 'click';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['outboundLinks']['selector'] = '.analytify-outbound-link';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['outboundLinks']['request'] = 'event';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['outboundLinks']['vars'] = array(
		'eventCategory' => '${category}',
		'eventAction'   => '${action}',
		'eventLabel'    => '${label}',
	);

	// Track Tel
	$analytics['analytify-googleanalytics']['config_data']['triggers']['telLinks']['on'] = 'click';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['telLinks']['selector'] = '.analytify-amp-tel';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['telLinks']['request'] = 'event';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['telLinks']['vars'] = array(
		'eventCategory' => '${category}',
		'eventAction'   => '${action}',
		'eventLabel'    => '${label}',
	);

	// Track Mailto
	$analytics['analytify-googleanalytics']['config_data']['triggers']['mailtoLinks']['on'] = 'click';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['mailtoLinks']['selector'] = '.analytify-amp-mail';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['mailtoLinks']['request'] = 'event';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['mailtoLinks']['vars'] = array(
		'eventCategory' => '${category}',
		'eventAction'   => '${action}',
		'eventLabel'    => '${label}',
	);

	// Track Custom Links
	$analytics['analytify-googleanalytics']['config_data']['triggers']['customLinks']['on'] = 'click';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['customLinks']['selector'] = '.analytify-link';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['customLinks']['request'] = 'event';
	$analytics['analytify-googleanalytics']['config_data']['triggers']['customLinks']['vars'] = array(
		'eventCategory' => '${category}',
		'eventAction'   => '${action}',
		'eventLabel'    => '${label}',
	);

	$analytics = apply_filters( 'analytify_amp_add_analytics', $analytics );

	return $analytics;
}
add_filter( 'amp_post_template_analytics', 'analytify_amp_add_analytics' );
add_filter( 'amp_analytics_entries', 'analytify_amp_add_analytics' );

function analytify_amp_rename_config_data( $analytics ) {
	$analytics['analytify-googleanalytics']['config'] = $analytics['analytify-googleanalytics']['config_data'];
	unset( $analytics['analytify-googleanalytics']['config_data'] );
	$analytics['analytify-googleanalytics']['config'] = wp_json_encode($analytics['analytify-googleanalytics']['config'] );
	return $analytics;
}
add_filter( 'analytify_amp_add_analytics', 'analytify_amp_rename_config_data' );

/**
 * Add our own sanitizer to the array of sanitizers
 *
 * @param array $sanitizers
 *
 * @return array
 */
function analytify_amp_add_sanitizer( $sanitizers ) {
	require_once ANALYTIFY_PRO_ROOT_PATH . '/inc/modules/amp/classes/analytify-amp-sanitizer.php';
	$sanitizers['Analytify_AMP_Sanitizer'] = array();

	return $sanitizers;
}
add_filter( 'amp_content_sanitizers', 'analytify_amp_add_sanitizer' );

// If Yoast SEO Glue is active, turn off our integration hosted in their plugin and use
// the more advanced one from this addon.
// remove_class_filter( 'amp_post_template_analytics', 'YoastSEO_AMP_Frontend', 'analytics' );

// // Remove the submenu page so users do not get confused as much.
// function analytify_amp_remove_analytics_submenu() {
// 	if ( class_exists( 'AMP_Options_Manager' ) ) {
// 		remove_submenu_page( AMP_Options_Manager::OPTION_NAME, 'amp-analytics-options' );
// 	}
// }
// add_action( 'admin_menu', 'analytify_amp_remove_analytics_submenu', 999 );

// function analytify_amp_remove_analytics_code() {
// 	if ( ! function_exists( 'is_amp_endpoint' ) || ! is_amp_endpoint() ) {
// 		return;
// 	}

// 	// Core
// 	remove_action( 'wp_head', 'analytify_tracking_script', 6 );
// 	remove_action( 'template_redirect', 'analytify_events_tracking', 6 );

// 	// Ads
// 	remove_action( 'analytify_tracking_after_analytics', 'analytify_ads_output_after_script_old' );

// 	// Dimensions
// 	remove_class_filter( 'analytify_frontend_tracking_options_analytics_before_pageview', 'analytify_Frontend_Custom_Dimensions', 'output_custom_dimensions' );

// 	// Forms
// 	// @todo: track form impressions
// 	remove_action( 'wp_head', 'analytify_forms_output_after_script', 15 );

// 	// Admin bar scripts.
// 	remove_action( 'wp_enqueue_scripts', 'analytify_frontend_admin_bar_scripts' );

// 	// Scroll tracking.
// 	remove_action( 'wp_footer', 'analytify_scroll_tracking_output_after_script', 11 );

// 	// eCommerce
// 	remove_class_action( 'analytify_load_plugins', 'analytify_eCommerce', 'init', 99 );
// }
// add_action( 'template_redirect', 'analytify_amp_remove_analytics_code', 2 );

// // Remove Admin not tracked notice in AMP
// remove_action( 'wp_footer', 'analytify_administrator_tracking_notice', 300 );