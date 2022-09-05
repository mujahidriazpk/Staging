<?php
defined( 'WPINC' ) || exit;

define( 'AAR_AMP_PATH', plugin_dir_path( __FILE__ ) );
define( 'AAR_AMP_URL', plugin_dir_url( __FILE__ ) );

if ( is_admin() ) {
	require_once( AAR_AMP_PATH . 'admin/admin.php' );
	require_once( AAR_AMP_PATH . 'admin/admin_adsense.php' );
	new Advanced_Ads_Responsive_Amp_Admin;
}

require_once( AAR_AMP_PATH . 'public/public.php' );
new Advanced_Ads_Responsive_Amp;
