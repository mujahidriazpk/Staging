<?php
class Advanced_Ads_Pro_Module_Placement_Conditions {
	public function __construct() {
		add_filter( 'advanced-ads-ad-select-args', array( $this, 'append_placement_conditions' ), 10, 2 );
	}

	/**
	 * Read visitor and display condition of a placement and append them to conditions of ads.
	 *
	 * @param array $args Arguments passed to ads.
	 * @return string $method Ad select method.
	 */
	public function append_placement_conditions( $args, $method ) {
		// Check if we are about to request an ad.
		if ( $method !== Advanced_Ads_Select::AD ) {
			return $args;
		}

		if ( ! empty( $args['placement_conditions']['visitors'] )  && is_array( $args['placement_conditions']['visitors'] ) ) {
			// Get placement visitor conditions.
			$placement_visitors = array_values( $args['placement_conditions']['visitors'] );
			if ( ! array( $placement_visitors[0] ) ) {
				$placement_visitors[0] = array();
			}
			// We append placement conditions to ad conditions using the 'AND' connector.
			$placement_visitors[0]['connector'] = 'and';

			if ( ! isset ( $args['change-ad']['visitors'] ) || ! is_array( $args['change-ad']['visitors'] ) ) {
				$args['change-ad']['visitors'] = array();
			}

			//Merge those conditions that the user may add using shortcode attributes.
			//For example: `[the_ad id="1" change-ad__visitors__0__type="loggedin change-ad__visitors__0__operator="is_not"]`
			$args['change-ad']['visitors'] = array_merge( $placement_visitors, $args['change-ad']['visitors'] );
		}
		if ( ! empty( $args['placement_conditions']['display'] ) && is_array( $args['placement_conditions']['display'] ) ) {
			// Get placement display conditions.
			$placement_display = array_values( $args['placement_conditions']['display'] );

			if ( ! array( $placement_display[0] ) ) {
				$placement_display[0] = array();
			}
			$placement_display[0]['connector'] = 'and';

			if ( ! isset ( $args['change-ad']['conditions'] ) || ! is_array( $args['change-ad']['conditions'] ) ) {
				$args['change-ad']['conditions'] = array();
			}

			$args['change-ad']['conditions'] = array_merge( $placement_display, $args['change-ad']['conditions'] );
		}


		// The actual merging of 'change-ad' arguments with ad options takes place in the `Advanced_Ads_Ad::options()` method.
		return $args;
	}
}
