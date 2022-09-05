<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

$options_name = 'advanced-ads-tracking';
$impr_table_base = 'advads_impressions';
$clicks_table_base = 'advads_clicks';

if ( class_exists( 'Advanced_Ads_Admin_Licenses' ) ) {
    $addon = 'tracking';
    $plugin_name = 'Tracking';
    $options_slug = 'advanced-ads-tracking';
    $advads_license = Advanced_Ads_Admin_Licenses::get_instance();
    $license_status = $advads_license->get_license_status( $options_slug );
    if ( 'valid' == $license_status ) {
        $advads_license->deactivate_license( $addon, $plugin_name, $options_slug );
    }
}

global $wpdb;
if ( is_multisite() ) {
    $sql1 = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";
    $blog_ids = $wpdb->get_col( $sql1 );
    foreach ( $blog_ids as $id ) {
        switch_to_blog( $id );
        $tracking_options = get_option( $options_name );
        if ( isset( $tracking_options['uninstall'] ) && '1' == $tracking_options['uninstall'] ) {
            $_impr = $wpdb->prefix . $impr_table_base;
            $_clicks = $wpdb->prefix . $clicks_table_base;
            $sql2 = "DROP TABLE IF EXISTS $_impr, $_clicks";
            $wpdb->query( $wpdb->prepare( $sql2 ) );
            delete_option( $options_name );
        }
    }
    restore_current_blog();
} else {
    $tracking_options = get_option( $options_name );
    if ( isset( $tracking_options['uninstall'] ) && '1' == $tracking_options['uninstall'] ) {
        $_impr = $wpdb->prefix . $impr_table_base;
        $_clicks = $wpdb->prefix . $clicks_table_base;
        $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %1$s, %2$s', $_impr, $_clicks ) );
        delete_option( $options_name );
    }
}