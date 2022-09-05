<?php
/**
 * Advanced Ads – Ad Tracking
 *
 * Plugin Name:       Advanced Ads – Ad Tracking
 * Plugin URI:        https://wpadvancedads.com/add-ons/tracking/
 * Description:       Track ad impressions and clicks.
 * Version:           1.8.15
 * Author:            Thomas Maier
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-tracking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die; // -TODO use proper header
}

// load basic path and url to the plugin
define( 'AAT_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AAT_BASE_URL', plugin_dir_url( __FILE__) );
define( 'AAT_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) );
// used as prefix for wp options; used as gettext domain; used as script/ admin namespace
define( 'AAT_SLUG', 'advads-tracking' );
define( 'AAT_VERSION', '1.8.15' );

define( 'AAT_PLUGIN_URL', 'https://wpadvancedads.com' );
define( 'AAT_PLUGIN_NAME', 'Tracking' );

add_action( 'advanced-ads-plugin-loaded', 'advanced_ads_tracking_init_plugin' );
function advanced_ads_tracking_init_plugin () {
    if ( defined( 'AAT_IMP_SHORTCODE' ) ) {
        return ;
    }
    
    // impressions shortcode
    define( 'AAT_IMP_SHORTCODE', 'the_ad_impressions' );

    // autoload
    require_once AAT_BASE_PATH . '/vendor/autoload_52.php';

    $is_admin = is_admin();
    $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

    if ( $is_ajax ) {
        new Advanced_Ads_Tracking_Ajax;
    }

	if ( $is_admin && current_user_can( advanced_ads_tracking_db_cap() ) ) {
		require_once AAT_BASE_PATH . '/classes/db-operations.php';
		$dbop = Advanced_Ads_Tracking_Dbop::get_instance();
		add_action( 'admin_notices', array( $dbop, 'admin_notices' ) );
	}

    $public_class = new Advanced_Ads_Tracking( $is_admin, $is_ajax );

     // external click tracking feature is enabled
    if( defined('ADVANCED_ADS_TRACK_EXTERNAL_EVENTS') ){
	new Advanced_Ads_Tracking_External_Clicks;
    }

    // only admin, not ajax (which is always admin)
    if ( $is_admin && !$is_ajax ) {
        $advads_tracking_admin = new Advanced_Ads_Tracking_Admin();

        register_deactivation_hook( __FILE__, array( 'Advanced_Ads_Tracking', 'deactivate' ) );
    }

}

/**
 *  activation hook
 */
function advanced_ads_tracking_activation() {
	require_once plugin_dir_path( __FILE__ ) . '/admin/admin.php';
	Advanced_Ads_Tracking_Admin::create_tables();
}
register_activation_hook( __FILE__, 'advanced_ads_tracking_activation' );

/**
 *  Automatic compression
 */
function advanced_ads_auto_compress() {
	if ( !class_exists( 'Advanced_Ads_Tracking_Dbop', false ) ) {
		require_once AAT_BASE_PATH . '/classes/db-operations.php';
		Advanced_Ads_Tracking_Dbop::get_instance();
		Advanced_Ads_Tracking_Dbop::incr_compress();
	}
}
add_action( 'advanced_ads_auto_comp', 'advanced_ads_auto_compress' );
 
/**
 *  listen to timezone_string option. Need to be hooked asap ( before "advanced-ads-plugin-loaded" action )
 */
function advanced_ads_timezone_changed( $new, $old ) {
    if ( $new != $old && !empty( $new ) ) {
		$TZ = new DateTimeZone( $new );
		$now = time();
		$now += ( 24 * 60 * 60 );
		$date = date_create( '@' . $now );
		
		$local_now = date_create( 'now', $TZ );
		$offset = $local_now->getOffset();
		
		// next day at 00:15 AM UTC
		$_00h15 = date_create( $date->format( "Y-m-dT00:15:00" ), new DateTimeZone( 'UTC' ) );
		$_00h15 = intval( $_00h15->format( 'U' ) ) - $offset;
		
		// admin reports
		$admin_report = wp_get_schedule( 'advanced_ads_daily_email' );
		if ( false !== $admin_report ) {
			wp_clear_scheduled_hook( 'advanced_ads_daily_email' );
			wp_schedule_event( $_00h15, 'daily', 'advanced_ads_daily_email' );
		}
		
		// individual ad report
		$individual_report = wp_get_schedule( 'advanced_ads_daily_report' );
		if ( false !== $individual_report ) {
			wp_clear_scheduled_hook( 'advanced_ads_daily_report' );
			wp_schedule_event( $_00h15, 'daily', 'advanced_ads_daily_report' );
		}
    }
    return $new;
}
add_filter( 'pre_update_option_timezone_string', 'advanced_ads_timezone_changed', 10, 2 );

/**
 *  listen to gmt_offset option. Need to be hooked asap ( before "advanced-ads-plugin-loaded" action )
 */
function advanced_ads_gmt_offset_changed( $new, $old ) {
    if ( $new != $old && !empty( $new ) ) {
		$admin_report = wp_get_schedule( 'advanced_ads_daily_email' );
		$individual_report = wp_get_schedule( 'advanced_ads_daily_report' );
		
		if ( false !== $admin_report || false !== $individual_report ) {
			
			// fallback timezone ( WP's default )
			$TZ = new DateTimeZone( 'UTC' );
            $pattern = '/(-|\+)?((\d+)(:\d\d)?)/';
            preg_match( $pattern, $new, $result );
            if ( $result ) {
                $zero = ( 1 == strlen( $result[3] ) )? '0' : '';
                $sign = ( isset( $result[1] ) && !empty( $result[1] ) )? $result[1] : '+';
                $gmt = $sign . $zero . $result[2];
                if ( !isset( $result[4] ) || empty($result[4]) ) $gmt .= ':00';
                
                $TZ = date_create( '2015-11-01T12:00:00' . $gmt )->getTimezone();
            }
			$now = time();
			$now += ( 24 * 60 * 60 );
			$date = date_create( '@' . $now );
			
			$local_now = date_create( 'now', $TZ );
			$offset = $local_now->getOffset();
		
			$date->setTimeZone( $TZ );
			
			// next day at 00:15 AM UTC
			$_00h15 = date_create( $date->format( "Y-m-dT00:15:00" ), new DateTimeZone( 'UTC' ) );
			$_00h15 = intval( $_00h15->format( 'U' ) ) - $offset;
			
			if ( false !== $admin_report ) {
				wp_clear_scheduled_hook( 'advanced_ads_daily_email' );
				wp_schedule_event( $_00h15, 'daily', 'advanced_ads_daily_email' );
			}
			
			if ( false !== $individual_report ) {
				wp_clear_scheduled_hook( 'advanced_ads_daily_report' );
				wp_schedule_event( $_00h15, 'daily', 'advanced_ads_daily_report' );
			}
		}
    }
    return $new;
}
add_filter( 'pre_update_option_gmt_offset', 'advanced_ads_gmt_offset_changed', 10, 2 );

/**
 *  wrapper for translation from other domains
 */
function advads__( $text, $domain = 'default' ) {
	return translate( $text, $domain );
}

/**
 *  wrapper for translation from other domain with context
 */
function advads_x( $text, $context, $domain = 'default' ) {
	return translate_with_gettext_context( $text, $context, $domain );
}

function advads_n( $single, $plural, $number, $domain = 'default' ) {
    $translations = get_translations_for_domain( $domain );
    $translation  = $translations->translate_plural( $single, $plural, $number );
 
    return apply_filters( 'ngettext', $translation, $single, $plural, $number, $domain );
}

/**
 *  echo translation from other domains
 */
function advads_e( $text, $domain = 'default' ) {
	echo advads__( $text, $domain );
}

/**
 *  capability needed for database operations (compress/export etc)
 */
function advanced_ads_tracking_db_cap(){
	$default_cap = 'manage_options';
	return apply_filters( 'advanced-ads-tracking-dbop-capability', $default_cap );
}
