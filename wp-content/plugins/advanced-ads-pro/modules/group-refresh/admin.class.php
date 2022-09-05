<?php
class Advanced_Ads_Pro_Group_Refresh_Admin {

	public function __construct() {
		add_action( 'advanced-ads-group-form-options', array( $this, 'add_group_refresh_options' ) );
	}

	/**
	 * Render group refresh options
	 *
	 * @param obj $group Advanced_Ads_Group
	 */
	public function add_group_refresh_options( Advanced_Ads_Group $group ) {
		$options = Advanced_Ads_Pro::get_instance()->get_options();

		$cb_module_enabled = ! empty( $options['cache-busting']['enabled'] );
		$enabled = Advanced_Ads_Pro_Group_Refresh::is_enabled( $group );
		$interval = ! empty( $group->options['refresh']['interval'] ) ? Advanced_Ads_Pro_Utils::absint( $group->options['refresh']['interval'], 1 ) : 2000;
		$show_warning = false;

		if ( $cb_module_enabled && $enabled && method_exists( 'Advanced_Ads_Placements', 'get_placements_by' ) ) {
			$show_warning = true;
			$placements = Advanced_Ads_Placements::get_placements_by( 'group', $group->id );

			foreach( $placements as $placement ) {
				if ( ! isset( $placement['options']['cache-busting'] )
					|| $placement['options']['cache-busting'] !== Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF
				) {
					$show_warning = false;
					break;
				}
			}
		}

		ob_start();
		include dirname( __FILE__ ) . '/views/settings_group_refresh.php';
		$option_content = ob_get_clean();
		
		if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
			Advanced_Ads_Admin_Options::render_option( 
			    'group-pro-refresh advads-group-type-default advads-group-type-ordered', 
			    __( 'Refresh interval', 'advanced-ads-pro' ),
			    $option_content );
		}
	}
}
