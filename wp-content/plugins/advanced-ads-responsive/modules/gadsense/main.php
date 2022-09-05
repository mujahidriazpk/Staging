<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('AAR_ADSENSE_PATH', plugin_dir_path(__FILE__));
define('AAR_ADSENSE_URL', plugin_dir_url(__FILE__));

if (defined('DOING_AJAX') && DOING_AJAX) {
	// Need to include it because ad parameters meta box content is also loaded by ajax
	require_once(AAR_ADSENSE_PATH . 'includes/hooking.php');
	new Aaabs_Adsense_Param_Hooking();
} else {
	if (is_admin()) {
		// Include the class responsible of hooking into the ad params meta box
		require_once(AAR_ADSENSE_PATH . 'includes/hooking.php');
		new Aaabs_Adsense_Param_Hooking();

		// Include other admin functions
		require_once(AAR_ADSENSE_PATH . 'admin/admin.php');
		new Aaabs_Adsense_Admin();
	}
}
// load in any case, because it could be an ad loaded by ajax
require_once(AAR_ADSENSE_PATH . 'public/public.php');
new Aaabs_Adsense_Public_Facing();
