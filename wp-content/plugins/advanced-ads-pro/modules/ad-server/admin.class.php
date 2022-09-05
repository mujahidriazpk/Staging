<?php

/**
 * Allow serving ads on external URLs.
 *
 * Class Advanced_Ads_Pro_Module_Ad_Server_Admin
 */
class Advanced_Ads_Pro_Module_Ad_Server_Admin {

	/**
	 * Advanced_Ads_Pro_Module_Ad_Server_Admin constructor.
	 */
	public function __construct() {
		// Stop, if main plugin doesn’t exist.
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return;
		}

		// Add settings section to allow module enabling.
		add_action( 'advanced-ads-settings-init', array( $this, 'settings_init' ), 10, 1 );

		// Check if the module was enabled.
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( empty( $options['ad-server']['enabled'] ) ) {
			return;
		}

		// Add server placement.
		add_action( 'advanced-ads-placement-types', array( $this, 'add_placement' ) );
		// Content of server placement.
		add_action( 'advanced-ads-placement-options-after', array( $this, 'placement_options' ), 10, 2 );
		// Show usage information under "show all options".
		add_filter( 'advanced-ads-placement-options-after-advanced', array( $this, 'add_placement_setting' ), 10, 2 );
	}

	/**
	 * Option to enable the Ad Server module.
	 */
	public function settings_init() {
		// Add new section.
		add_settings_field(
			'module-ad-server',
			__( 'Ad Server', 'advanced-ads-pro' ),
			array( $this, 'render_settings' ),
			Advanced_Ads_Pro::OPTION_KEY . '-settings',
			Advanced_Ads_Pro::OPTION_KEY . '_modules-enable'
		);
	}

	/**
	 * Render Ad Server module option.
	 */
	public function render_settings() {

		$options           = Advanced_Ads_Pro::get_instance()->get_options();
		$module_enabled    = isset( $options['ad-server']['enabled'] ) && $options['ad-server']['enabled'];
		$embedding_url     = isset( $options['ad-server']['embedding-url'] ) ? $options['ad-server']['embedding-url'] : '';
		$block_no_referrer = ! empty( $options['ad-server']['block-no-referrer'] ); // True if option is set.

		include dirname( __FILE__ ) . '/views/module-settings.php';
	}

	/**
	 * Register the placement in Advanced Ads
	 *
	 * @param array $types existing placement types.
	 *
	 * @return mixed
	 */
	public function add_placement( $types ) {
		$types['server'] = array(
			'title'       => __( 'Ad Server', 'advanced-ads-pro' ),
			'description' => __( 'Display ads on external websites.', 'advanced-ads-pro' ),
			'image'       => AAP_BASE_URL . 'modules/ad-server/assets/img/server.png',
			'options'     => array(
				'placement-cache-busting'      => false,
				'placement-display-conditions' => false,
				'placement-visitor-conditions' => false,
				'placement-item-alternative'   => false,
				'placement-tests'              => false,
			),
		);
		return $types;
	}

	/**
	 * Register placement options.
	 *
	 * @param string $placement_slug placement ID.
	 * @param array  $placement placement options.
	 */
	public function placement_options( $placement_slug = '', $placement = array() ) {
		if ( 'server' !== $placement['type'] ) {
			return;
		}
	}

	/**
	 * Show usage information for the ad server
	 *
	 * @param string $_placement_slug placement ID.
	 * @param array  $_placement placement options.
	 */
	public function add_placement_setting( $_placement_slug, $_placement ) {

		if ( 'server' !== $_placement['type'] ) {
			return;
		}

		// Publically visible name of the placement. Defaults to the placement slug.
		$public_slug = ! empty( $_placement['options']['ad-server-slug'] ) ? sanitize_title( $_placement['options']['ad-server-slug'] ) : $_placement_slug;

		ob_start();
		include dirname( __FILE__ ) . '/views/placement-settings.php';
		$slug_content = ob_get_clean();

		Advanced_Ads_Admin_Options::render_option(
			'ad-server-usage',
			__( 'Public string', 'advanced-ads-pro' ),
			$slug_content
		);

		$options = Advanced_Ads_Pro::get_instance()->get_options();
		// Static URL used for the placement to deliver the content.
		$url = admin_url( 'admin-ajax.php' ) . '?action=aa-server-select&p=' . $public_slug;

		ob_start();
		include dirname( __FILE__ ) . '/views/placement-usage.php';
		$usage_content = ob_get_clean();

		Advanced_Ads_Admin_Options::render_option(
			'ad-server-usage',
			__( 'Usage', 'advanced-ads-pro' ),
			$usage_content
		);
	}


}

