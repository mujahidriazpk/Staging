<?php
/**
 * Placement conditions administration.
 */
class Advanced_Ads_Pro_Module_Placement_Conditions_Admin {
	public function __construct() {
		add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'render_conditions_for_placements' ), 10, 2 );
	}

	/**
	 * Render display and visitor condition for placement.
	 *
	 * @param string $placement_slug Slug of the placement.
	 * @param array $placement Placement data.
	 */
	function render_conditions_for_placements( $placement_slug, $placement ) {
		if ( ! class_exists( 'Advanced_Ads_Admin_Options' )
			|| ! method_exists( 'Advanced_Ads_Display_Conditions', 'render_condition_list' )
			|| ! method_exists( 'Advanced_Ads_Visitor_Conditions', 'render_condition_list' )
		) {
			return;
		}

		$placement_types = Advanced_Ads_Placements::get_placement_types();

		if ( ! isset( $placement_types[ $placement['type'] ]['options']['placement-display-conditions'] ) || $placement_types[ $placement['type'] ]['options']['placement-display-conditions'] ) {
			$set_conditions = isset( $placement['options']['placement_conditions']['display'] ) ? $placement['options']['placement_conditions']['display'] : array();

			$list_target = 'advads-placement-condition-list-' . $placement_slug;
			$form_name   = 'advads[placements][' . $placement_slug . '][options][placement_conditions][display]';

			ob_start();

			if ( ! empty( $placement_types[ $placement['type'] ]['options']['placement-display-conditions'] ) ) {
				// Render only specific conditions.
				$options['in'] = $placement_types[ $placement['type'] ]['options']['placement-display-conditions'];
			} else {
				$options['in'] = 'global';
			}

			Advanced_Ads_Display_Conditions::render_condition_list( $set_conditions, $list_target, $form_name, $options );
			$conditions = ob_get_clean();

			Advanced_Ads_Admin_Options::render_option(
				'placement-display-conditions',
				__( 'Display Conditions', 'advanced-ads' ),
				$conditions
			);
		}



		if ( ! isset( $placement_types[ $placement['type'] ]['options']['placement-visitor-conditions'] ) || $placement_types[ $placement['type'] ]['options']['placement-visitor-conditions'] ) {

			$set_conditions = isset( $placement['options']['placement_conditions']['visitors'] ) ? $placement['options']['placement_conditions']['visitors'] : array();

			$list_target = 'advads-placement-condition-list-visitor' . $placement_slug;
			$form_name = 'advads[placements][' . $placement_slug . '][options][placement_conditions][visitors]';

			ob_start();
			Advanced_Ads_Visitor_Conditions::render_condition_list( $set_conditions, $list_target, $form_name );
			$conditions = ob_get_clean();

			Advanced_Ads_Admin_Options::render_option(
				'placement-visitor-conditions',
				__( 'Visitor Conditions', 'advanced-ads' ),
				$conditions
			);
		}
	}


}
