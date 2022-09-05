<?php
if (!defined('WPINC')) {
	die;
}

class Aaabs_Adsense_Admin
{
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded(){

		if( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
			// no need to display an admin notice here, because the main plugin already handles them
			return;
		}

		add_filter('advanced-ads-gadsense-ad-param-script', array($this, 'ad_param_script'));
		add_action('admin_print_scripts', array($this, 'print_scripts'));
	}

	/**
	 * Enqueue additional script (.js) files when on the new/edit ad page
	 *
	 * @param arr $scripts, array of scripts file to enqueue
	 */
	public function ad_param_script($scripts) {
		// Enqueue styling files. This function is called within the admin_enqueue_script hook by the base plugin (advanced-ads)
		wp_enqueue_style('gadsense-responsive-manual-css', AAR_ADSENSE_URL . 'admin/assets/css/admin.css', array(), null);

		$scripts['gadsense-respad-js'] = array(
			'path' => AAR_ADSENSE_URL . 'admin/assets/js/new-ad.js',
			'dep' => array('jquery'),
			'version' => null,
		);
		return $scripts;
	}

	/**
	 * Print script in the <head /> section of admin page
	 */
	public function print_scripts() {
		global $pagenow, $post_type, $post;
		if (
				('post-new.php' == $pagenow && Advanced_Ads::POST_TYPE_SLUG == $post_type) ||
				('post.php' == $pagenow && Advanced_Ads::POST_TYPE_SLUG == $post_type && isset($_GET['action']) && 'edit' == $_GET['action'])
		) {

            //  fix for #108
            //  NEVER create a json object inside of JS from a string/obj without encoding it!!!
            $json_object = json_decode($post->post_content);
            $json_string = (empty($json_object))
                ? 'false'
                : json_encode($json_object);
			?>
			<script type="text/javascript">
				var respAdsAdsense = {
					msg : {
						removeRule : '<?php esc_attr_e('Remove this rule', 'advanced-ads-responsive'); ?>',
						remove : '<?php esc_attr_e('remove', 'advanced-ads-responsive'); ?>',
						notDisplayed : '<?php esc_attr_e('Not Displayed', 'advanced-ads-responsive'); ?>',
					},
                    currentAd: <?php echo $json_string; ?>
				};
			</script>
			<?php
		}
	}
}
