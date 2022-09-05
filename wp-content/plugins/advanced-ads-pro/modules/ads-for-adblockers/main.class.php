<?php

class Advanced_Ads_Pro_Module_Ads_For_Adblockers {
	/**
	 * Holds unique identifiers of each chain. It allows to show only one copy of the alternative ad.
	 */
	private $shown_chains = array();

	public function __construct() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( empty( $options['ads-for-adblockers']['enabled'] ) ) {
			return;
		}

		add_filter( 'advanced-ads-pro-ad-needs-backend-request', array( $this, 'ad_needs_backend_request' ), 10, 3 );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_filter( 'advanced-ads-can-display', array( $this, 'can_display' ), 10, 2 );
			add_filter( 'advanced-ads-ad-select-args', array( $this, 'save_chain_id' ), 10, 1 );
			add_filter( 'advanced-ads-ad-select-override-by-ad', array( $this, 'override_ad_select_by_ad' ), 10, 3 );
		}
	}

    /**
     * Enable cache-busting if there is an ad for adblocker.
     *
     * @param string $return.
     * @param obj Advanced_Ads_Ad.
     * @return string $return
     */
	public function ad_needs_backend_request( $return, Advanced_Ads_Ad $ad, $fallback ) {
		$ad_for_adblocker = Advanced_Ads_Pro_Module_Ads_For_Adblockers::get_ad_for_adblocker( $ad->args );

		if ( ! $ad_for_adblocker ) {
			return $return;
		}

		if ( $return === $fallback ) {
			return $fallback;
		}

		if ( 'plain' === $ad_for_adblocker->type && ( ! isset( $ad_for_adblocker->output['allow_php'] ) || $ad_for_adblocker->output['allow_php'] ) ) {
			// AJAX or no cache-busting if PHP is enabled for ad for adblocker.
			return $fallback;
		}

		return 'passive';
	}

	/**
	 * Save chain id.
	 *
	 * @param array $args
	 * @return array $args
	 */
	public function save_chain_id( $args ) {
		if ( ! isset( $args['chain_id'] ) ) {
			$args['chain_id'] = mt_rand();
		};
		return $args;
	}

	/**
	 * Check if the ad can be displayed.
	 *
	 * @param bool $can_display
	 * @return obj $ad Advanced_Ads_Ad
	 * @return bool $can_display
	 */
	public function can_display( $can_display, Advanced_Ads_Ad $ad ) {
		if ( ! $can_display ) {
			return $can_display;
		}
		if ( in_array( $ad->args['chain_id'], $this->shown_chains ) ) {
			return false;
		}
		return true;
	}

	public function override_ad_select_by_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
		if ( ! $ad->can_display() ) {
			return $overriden_ad;
		}

		if ( ! empty( $args['adblocker_active'] ) && ! empty( $args['item_adblocker'] ) ) {
			$_item = explode( '_', $args['item_adblocker'] );

			if ( $_item[0] === Advanced_Ads_Select::AD
				&& ! empty( $_item[1] ) 
			) {
				$ad = new Advanced_Ads_Ad( (int) $_item[1], $args );
				$overriden_ad = $ad->output();
				$this->shown_chains[] = $args['chain_id'];
			}
		}

		return $overriden_ad;
	}

    /**
     * Get an ad that is delivered to users with an ad blocker enabled.
     *
     * @param $args Optional arguments passed to ads.
     * @return mixed bool false; Advanced_Ads_Ad $ad
     */
    public static function get_ad_for_adblocker( $args ) {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( empty( $options['ads-for-adblockers']['enabled'] ) ) {
			return false;
		}

        if ( ! empty( $args['item_adblocker'] ) ) {
            $_item = explode( '_', $args['item_adblocker'] );

            if ( ! empty( $_item[1] ) && $_item[0] === Advanced_Ads_Select::AD ) {
                $ad = new Advanced_Ads_Ad( $_item[1], $args );
                if ( ! empty( $ad->is_ad ) ) {
                    return $ad;
                }
            }
        }

        return false;
    }


}
