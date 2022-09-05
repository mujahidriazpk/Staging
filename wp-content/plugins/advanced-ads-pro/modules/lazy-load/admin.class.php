<?php

class Advanced_Ads_Pro_Module_Lazy_Load_Admin {

	public function __construct() {
		add_action( 'advanced-ads-settings-init', array( $this, 'settings_init'), 10, 1 );

		// Render lazy load option.
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( empty( $options['lazy-load']['enabled'] ) ) {
			return;
		}
		add_action( 'advanced-ads-placement-options-after', array( $this, 'render_lazy_load_option' ), 10, 2 );
	}

	public function settings_init($hook) {
		$admin = Advanced_Ads_Admin::get_instance();
		$hook = $admin->plugin_screen_hook_suffix;

		// add new section
		add_settings_field(
			'module-lazy-load',
			__( 'Lazy Load', 'advanced-ads-pro' ),
			array( $this, 'render_settings' ),
			Advanced_Ads_Pro::OPTION_KEY . '-settings',
			Advanced_Ads_Pro::OPTION_KEY . '_modules-enable'
		);
	}

	public function render_settings() {
		include dirname( __FILE__ ) . '/views/settings.php';
	}

	/**
	 * Render lazy load option.
	 *
	 * @param string $_placement_slug id of the placement
	 * @param array $placement
	 */
	public function render_lazy_load_option( $_placement_slug, $placement ) {
		$placement_types = Advanced_Ads_Placements::get_placement_types();
		$options = Advanced_Ads_Pro::get_instance()->get_options();

		if ( ! empty( $placement_types[ $placement['type'] ]['options']['show_lazy_load'] ) ) {
			$checked = ( isset( $placement['options']['lazy_load'] ) && $placement['options']['lazy_load'] === 'enabled' ) ? 'enabled' : 'disabled';
			$cb_off = empty( $options['cache-busting']['enabled'] ) 
			|| ( isset( $placement['options']['cache-busting'] ) && $placement['options']['cache-busting'] === Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF );

			ob_start();
			require dirname( __FILE__ ) . '/views/setting_lazy_load.php';
			$option_content = ob_get_clean();

			if ( class_exists( 'Advanced_Ads_Admin_Options' ) ) {
				Advanced_Ads_Admin_Options::render_option(
					'placement-lazy-load',
					__( 'lazy load', 'advanced-ads-pro' ),
					$option_content
				);
			}
		}
	}
}
