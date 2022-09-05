<?php

/**
 * Callbacks for GamiPress visitor conditions.
 */
class Advanced_Ads_Pro_Module_GamiPress {
	/**
	 * Check if current user meets points visitor condition.
	 *
	 * @param array $condition Array of visitor condition options.
	 *
	 * @return bool
	 */
	public static function check_points_visitor_condition( $condition ) {
		$points  = gamipress_get_user_points(
			get_current_user_id(),
			// dynamically get the post_name from the WP_Post::ID, post_name can change, while ID is immutable.
			get_post_field( 'post_name', (int) $condition['points'] )
		);
		$compare = (int) $condition['value'];
		switch ( $condition['operator'] ) {
			case 'is_higher':
				return $points >= $compare;
			case 'is_lower':
				return $points <= $compare;
			case 'is_equal':
			default:
				return $points === $compare;
		}
	}

	/**
	 * Check if current user meets rank visitor condition.
	 *
	 * @param array $condition Array of visitor condition options.
	 *
	 * @return bool
	 */
	public static function check_rank_visitor_condition( $condition ) {
		$condition_rank = gamipress_get_rank_priority( (int) $condition['value'] );
		$user_rank      = gamipress_get_rank_priority( gamipress_get_user_rank(
			get_current_user_id(),
			gamipress_get_post_type( (int) $condition['value'] )
		) );

		switch ( $condition['operator'] ) {
			case 'is_higher':
				return $user_rank >= $condition_rank;
			case 'is_lower':
				return $user_rank <= $condition_rank;
			case 'is_equal':
			default:
				return $user_rank === $condition_rank;
		}
	}

	/**
	 * Check if current user meets achievement visitor condition.
	 *
	 * @param array $condition Array of visitor condition options.
	 *
	 * @return bool
	 */
	public static function check_achievement_visitor_condition( $condition ) {
		$achieved = gamipress_has_user_earned_achievement( (int) $condition['value'], get_current_user_id() );
		if ( $condition['operator'] === 'is_not' ) {
			return ! $achieved;
		}

		return $achieved;
	}
}
