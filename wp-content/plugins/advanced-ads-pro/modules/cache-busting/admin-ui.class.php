<?php
class Advanced_Ads_Pro_Module_Cache_Busting_Admin_UI {

	// Not ajax, is admin.
	public function __construct() {
		add_action( 'advanced-ads-placement-options-after', array( $this, 'admin_placement_options' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'advanced-ads-ad-params-after', array( $this, 'check_ad' ), 9, 2 );
		//add_filter( 'advanced-ads-save-options', array( $this, 'save_options' ), 10, 2 );
		add_filter( 'advanced-ads-ad-notices', array($this, 'ad_notices'), 10, 3 );
		add_action( 'wp_ajax_advads-reset-vc-cache', array( $this, 'reset_vc_cache' ) );
	}

	/**
	 * Update visitor consitions cache.
	 */
	public function reset_vc_cache(){
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			return;
		}

		check_ajax_referer( 'advads-pro-reset-vc-cache-nonce', 'security' );
		$time = time();

		$options = get_option( 'advanced-ads-pro' );
		$options['cache-busting']['vc_cache_reset'] = $time;
		update_option( 'advanced-ads-pro', $options );
		echo $time;
		exit;
	}

	/**
	 * add placement options on placement page
	 *
	 * @param string $placement_slug
	 * @param array  $placement
	 */
	public function admin_placement_options( $placement_slug, $placement ) {
		$placement_types = Advanced_Ads_Placements::get_placement_types();
		if ( isset( $placement_types[ $placement['type'] ]['options']['placement-cache-busting'] ) && ! $placement_types[ $placement['type'] ]['options']['placement-cache-busting'] ) {
			return;
		}

		// l10n
		$values = array(
			Advanced_Ads_Pro_Module_Cache_Busting::OPTION_ON => _x( 'AJAX', 'setting label', 'advanced-ads-pro' ),
			Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF => _x( 'off', 'setting label', 'advanced-ads-pro' ),
			Advanced_Ads_Pro_Module_Cache_Busting::OPTION_AUTO => _x( 'auto', 'setting label', 'advanced-ads-pro' ),
		);

		// options
		$value = isset( $placement['options']['cache-busting'] ) ? $placement['options']['cache-busting'] : null;
		$value = $value === Advanced_Ads_Pro_Module_Cache_Busting::OPTION_ON ? Advanced_Ads_Pro_Module_Cache_Busting::OPTION_ON : ( $value === Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF ? Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF : Advanced_Ads_Pro_Module_Cache_Busting::OPTION_AUTO );

		ob_start();
		foreach ( $values as $k => $l ) {
			$selected = checked( $value, $k, false );
			echo '<label><input' . $selected . ' type="radio" name="advads[placements]['.
				$placement_slug.'][options][cache-busting]" value="'.$k.'" id="advads-placement-'.
				$placement_slug.'-cache-busting-'.$k.'"/>'.$l.'</label>';
		}
		$option_content = ob_get_clean();
		
		if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
			Advanced_Ads_Admin_Options::render_option( 
				'placement-cache-busting', 
				_x( 'Cache-busting', 'placement admin label', 'advanced-ads-pro' ),
				$option_content );
		}
	}

	/**
	 * enqueue scripts for validation the ad
	 */
	public function enqueue_admin_scripts() {
		$screen = get_current_screen();
		$uriRelPath = plugin_dir_url( __FILE__ );
		if ( isset( $screen->id ) && $screen->id === 'advanced_ads' ) { //ad edit page
			wp_register_script( 'krux/prescribe', $uriRelPath . 'inc/prescribe.js', array( 'jquery' ), '1.1.3' );
			wp_enqueue_script( 'advanced-ads-pro/cache-busting-admin', $uriRelPath . 'inc/admin.js', array( 'krux/prescribe' ), AAP_VERSION );
		} elseif( Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			wp_enqueue_script( 'advanced-ads-pro/cache-busting-admin', $uriRelPath . 'inc/admin.js', array(), AAP_VERSION );
		}
	}

	/**
	 * add validation for cache-busting
	 *
	 * @param obj $ad ad object
	 * @param arr $types ad types
	 */
	public function check_ad( $ad, $types = array()  ) {
		$options = $ad->options();
		include dirname( __FILE__ ) . '/views/settings_check_ad.php';
	}

	// public function save_options( $options = array(), $ad = 0 ) {
	// 	if ( isset( $_POST['advanced_ad']['cache-busting']['possible'] ) ) {
	// 		$options['cache-busting']['possible'] = ('true' === $_POST['advanced_ad']['cache-busting']['possible'] ) ? true : false;
	// 	}
	// 	return $options;
	// }
	
	/**
	 * show cache-busting specific ad notices
	 * 
	 * @since 1.13.1
	 */
	public function ad_notices( $notices, $box, $post ){
	    
	    $ad = new Advanced_Ads_Ad( $post->ID );
	    
	    // $content = json_decode( stripslashes( $ad->content ) );
	    
	    switch ($box['id']){
		case 'ad-parameters-box' :
			// show hint that for ad-group ad type, cache-busting method will only be AJAX or off
			if( 'group' === $ad->type ){
			    $notices[] = array(
				    'text' => __( 'The <em>Ad Group</em> ad type can only use AJAX or no cache-busting, but not passive cache-busting.', 'advanced-ads-pro' ),
				    // 'class' => 'advads-ad-notice-pro-ad-group-cache-busting',
			    );
			}
		    break;
	    }
	    
	    
	    return $notices;
	}
}
