<?php
class Analytify_GDPR_Compliance {

	function __construct() {

		$this->hooks();
	}

	function hooks() {
		
		// CookieYes | GDPR Cookie Consent & Compliance Notice (CCPA Ready) By WebToffee.
		add_filter( 'wt_cli_third_party_scripts', array( $this, 'cookie_law_info_blocking' ) );
		add_filter( 'wt_cli_plugin_integrations', array( $this, 'cookie_law_info_integration' ) );
		add_action( 'init', array( $this, 'cookie_law_info_add_settings' ), 9 );
	}

	/**
	 * Add Analytify in CookieYes blocking scripts settings.
	 *
	 * @return void
	 */
	function cookie_law_info_add_settings() {
		
		global $wt_cli_integration_list;

		$wt_cli_integration_list['wp-analytify'] = array(
			'identifier'  => 'WP_Analytify',
			'label'       => 'Analytify - Google Analytics Dashboard',
			'status'      => 'yes',
			'description' => 'Google Analytics Dashboard Plugin for WordPress by Analytify',
			'category'    => 'analytics',
			'type'        => 1,
		);
	}

	/**
	 * Add Analytify auto blocking scripts for CookieYes.
	 *
	 * @param array $tags
	 * @return array
	 */
	function cookie_law_info_blocking( $tags ) {

		try {
			global $wpdb ;

			$script_table	= $wpdb->prefix . 'cli_scripts';
			$status			= false;

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$script_table'" ) == $script_table ) {
				$script_data = $wpdb->get_results( "select * from {$script_table}", ARRAY_A );
				
				foreach ( $script_data as $key => $data ) {
					if ( 'wp-analytify' === $data['cliscript_key'] && ( 'yes' === $data['cliscript_status'] || '1' === $data['cliscript_status'] ) ) {
						$status = true;
					}
				}
			}

			if ( $status ) {
				$tags['wp-analytify'] = array(
					'www.google-analytics.com/analytics.js',
					'www.googletagmanager.com/gtag/js',
					'wp-analytify/assets/default/js/scrolldepth.js',
					'wp-analytify-forms/assets/js/tracking.js',
					'wp-analytify-pro/assets/js/script.js'
				);
			}
		} catch (\Throwable $th) {
			return $tags;
		}
		
		return $tags;
	}
	
	/**
	 * Add Analytify compatibility integration with CookieYes.
	 *
	 * @param array $integration
	 * @return array
	 */
	function cookie_law_info_integration( $integration ) {
		
		$integration['wp-analytify'] = array(
			'identifier'  => 'WP_Analytify',
			'label'       => 'Analytify - Google Analytics Dashboard',
			'status'      => 'yes',
			'description' => 'Google Analytics Dashboard Plugin for WordPress by Analytify',
			'category'    => 'analytics',
			'type'        => 1,
		);

		return $integration;
	}

	/**
	 * Check if GDPR plugins are blocing scripts. 
	 *
	 * @return bool
	 */
	public static function is_gdpr_compliance_blocking() {

		// Cookie Notice & Compliance for GDPR / CCPA By Hu-manity.co
		if ( function_exists( 'cn_cookies_accepted' ) && ! cn_cookies_accepted() ) {
			return true;
		}

		return false;
	}
	
}