<?php
/**
 * Reduce the number of AJAX calls: during the first AJAX call, save the data (in cookies),
 * that cannot be checked using only JS. Later, the passive cache-busting can check that data.
 *
 * There are 2 ways to update the data array:
 * 1. Define the 'ADVANCED_ADS_PRO_USER_COOKIE_MAX_AGE' constant (in seconds).
 * An ajax requests will be initiated from time to time to update the expired conditions of the ads on the page.
 * 2. Use the "Update visitor conditions cache in the user&#8217;s browsers" option (Settings > Pro > Cache Busting ) and update the page cache.
 * An ajax request will be initiated to update all the conditions of the ads on the page.
 */
class Advanced_Ads_Pro_Cache_Busting_Server_Info {

	public function __construct( $cache_busting, $options ) {
		$this->cache_busting = $cache_busting;
		$this->options = $options;

		$this->server_info_duration = defined( 'ADVANCED_ADS_PRO_USER_COOKIE_MAX_AGE' ) ? absint( ADVANCED_ADS_PRO_USER_COOKIE_MAX_AGE ) : MONTH_IN_SECONDS;
		$this->vc_cache_reset = ! empty( $this->options['vc_cache_reset'] ) ? absint( $this->options['vc_cache_reset'] ) : 0;

		$this->is_ajax = ! empty( $cache_busting->is_ajax );
		new Advanced_Ads_Pro_Cache_Busting_Server_Info_Cookie( $this );
	}

	/**
	 * Get ajax request that will be used in case required cookies do not exist.
	 *
	 * @param array $ads An array of Advanced_Ads_Ad objects.
	 * @param array $args Ad arguments
	 * @return null/string $elementid Id of the html wrapper.
	 */
	public function get_ajax_for_passive_placement( $ads, $args, $elementid ) {
		if ( ! $this->server_info_duration ) {
			return;
		}

		if ( ! is_array( $ads ) ) {
			$ads = array( $ads );
		}

		$server_c = array();
		foreach ( $ads as $ad ) {
			$ad_server_c = $this->get_server_conditions( $ad );
			if ( $ad_server_c ) {
				$server_c = array_merge( $server_c, $ad_server_c );
			}
		}

		if ( ! $server_c ) { return; }

		$query = Advanced_Ads_Pro_Module_Cache_Busting::build_js_query( $args);

		return array(
			'ajax_query' => Advanced_Ads_Pro_Module_Cache_Busting::get_instance()->get_ajax_query( array_merge( $query, array(
				'elementid' => $elementid,
				'server_conditions' => $server_c
			) ) ),
			'server_info_duration' => $this->server_info_duration,
			'server_conditions' => $server_c,
		);

	}

	/**
	 * Get server conditions of the ad.
	 *
	 * @param $ad Advanced_Ads_Ad
	 * @return array
	 */
	private function get_server_conditions( Advanced_Ads_Ad $ad ) {
		$ad_options = $ad->options();
		$visitors = ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) ) ? array_values( $ad_options['visitors'] ) : array();
		$result = array();
		foreach ( $visitors as $k => $visitor ) {
			if ( $info = $this->get_server_condition_info( $visitor ) ) {
				$visitor_to_add = array_intersect_key( $visitor, array( 'type' => true, $info['hash_fields'] => true ) );
				$result[ $info['hash'] ] = $visitor_to_add;
			}

		}
		return $result;
	}

	/**
	 * Get info about the server condition.
	 *
	 * @param array $visitor Visitor condition.
	 * @return array/null info about server condition.
	 */
	public function get_server_condition_info( $visitor ) {
		if ( ! isset( $visitor['type'] ) ) {
			return;
		}

		$conditions = $this->get_all_server_conditions();
		if ( ! isset( $conditions[ $visitor['type'] ]['passive_info']['function'] ) ) {
			// It's not a server condition.
			return;
		}
		$info = $conditions[ $visitor['type'] ]['passive_info'];

		$hash = $visitor['type'];

		// Add unique fields set on the Ad edit page.
		// This allows us to to have several conditions of the same type.
		if ( isset( $info['hash_fields'] ) && isset( $visitor[ $info['hash_fields'] ] ) ) {
			$hash .= '_' . $visitor[ $info['hash_fields'] ];
		}
		// Allow the administrator to remove all cookies in the user's browsers.
		$hash .= '_' . $this->vc_cache_reset;

		$hash = substr( md5( $hash ), 0, 10 );
		return array( 'hash' => $hash, 'function' => $info['function'], 'hash_fields' => $info['hash_fields'] );
	}

	/**
	 * Get all server conditions.
	 */
	public function get_all_server_conditions() {
		if ( ! $this->server_info_duration ) {
			return array();
		}
		if ( ! did_action( 'init' ) ) {
			// All conditions should be ready.
			trigger_error( sprintf( '%1$s was called incorrectly', 'Advanced_Ads_Pro_Cache_Busting_Server_Info::get_all_server_conditions' ) );
		}
		$r = array();
		foreach ( Advanced_Ads_Visitor_Conditions::get_instance()->conditions as $name => $condition ) {
			if ( isset( $condition['passive_info'] ) ) {
				$r[ $name ] = $condition;
			}
		}
		return $r;
	}

}

class Advanced_Ads_Pro_Cache_Busting_Server_Info_Cookie {
	// Note: hard-coded in JS.
	const SERVER_INFO_COOKIE_NAME = 'advanced_ads_pro_server_info';

	public function __construct( $server_info ) {
		$this->server_info = $server_info;

		if ( ! $this->can_set_cookie() ) {
			// Remove cookie.
			if ( $this->parse_existing_cookies() ) {
				$this->set_cookie( '' );
			}
			return;
		}

		if ( $this->server_info->is_ajax ) {
			add_action( 'init', array( $this, 'add_server_info' ) );
		}

		if ( ! empty( $this->server_info->options['vc_cache_reset_actions']['login'] ) ) {
			add_action( 'wp_logout', array( $this, 'log_in_out' ) );
			add_action( 'set_auth_cookie', array( $this, 'log_in_out' ) );
		}
	}

	/**
	 * Create cookies during AJAX requests.
	 */
	public function add_server_info() {
		if ( ! isset( $_REQUEST['deferedAds'] ) ) {
			return;
		}

		$e_cookie = $n_cookie = $this->parse_existing_cookies();

		// Parse ajax request.
		foreach ( (array) $_REQUEST['deferedAds'] as $ajax_query ) {
			if ( ! isset( $ajax_query['ad_method'] ) || $ajax_query['ad_method'] !== 'placement'
				|| empty( $ajax_query['server_conditions'] ) ) {
				// The query does not have server conditions.
				continue;
			}

			// Prepare new cookies to save.
			$n_cookie = $this->prepare_new_cookies( $ajax_query['server_conditions'], $n_cookie );
		}

		$n_cookie['vc_cache_reset'] = $this->server_info->vc_cache_reset;

		if ( $n_cookie !== $e_cookie ) {
			$this->set_cookie( $n_cookie );
		}
	}

	/**
	 * Get correct and not obsolete conditions.
	 */
	private function parse_existing_cookies() {
		$n_cookie = array();

		if ( isset( $_COOKIE[ self::SERVER_INFO_COOKIE_NAME ] ) ) {
			$e_cookie = $_COOKIE[ self::SERVER_INFO_COOKIE_NAME ];
			$e_cookie = wp_unslash( $e_cookie );
			$e_cookie = json_decode( $e_cookie, true);

			if ( isset( $e_cookie['vc_cache_reset'] ) && absint( $e_cookie['vc_cache_reset'] ) < $this->server_info->vc_cache_reset ) {
				// The cookie has been reset on the Settings page.
				return $n_cookie;
			}
			if ( empty( $e_cookie['conditions'] ) || ! is_array( $e_cookie['conditions'] ) ) {
				return $n_cookie;
			}

			foreach ( $e_cookie['conditions'] as $cond_name => $hashes ) {
				foreach ( (array) $hashes as $hash => $item ) {
					// Do not add outdated conditions.
					if ( isset( $item['time'] ) && ( absint( $item['time'] ) + $this->server_info->server_info_duration ) > time() ) {
						$n_cookie['conditions'][ $cond_name ][ $hash ] = $item;
					}
				}
			}
		}
		return $n_cookie;
	}

	/**
	 * Prepare new conditions to save.
	 *
	 * @param array $visitors New visitor conditions to add to cookie.
	 * @param array $n_cookie Existing visitor conditions from cookie.
	 * @return array $n_cookie New cookie.
	 */
	public function prepare_new_cookies( $visitors, $n_cookie = array() ) {
		foreach ( (array) $visitors as $visitor ) {
			$info = $this->server_info->get_server_condition_info( $visitor );
			if ( ! $info ) { continue; }
			if ( isset( $n_cookie['conditions'][ $visitor['type'] ][ $info['hash'] ] ) ) { continue; }

			$n_cookie['conditions'][ $visitor['type'] ][ $info['hash'] ] = array(
				'data' => call_user_func( $info['function'], $visitor ),
				'time' => time(),
			);
		}
		return $n_cookie;
	}

	/**
	 * Check if the cookie can be set.
	 */
	public function can_set_cookie() {
		$has_duration = $this->server_info->server_info_duration;

		//$consent_given = ! class_exists( 'Advanced_Ads_Privacy' )
			//|| ! method_exists( Advanced_Ads_Privacy::get_instance(), 'can_set_cookie' )
			//|| Advanced_Ads_Privacy::get_instance()->can_set_cookie();


		//return $has_duration && $consent_given;
		return $has_duration;

	}

	/**
	 * Set cookie.
	 */
	public function set_cookie( $cookie ) {
		if ( ! $cookie ) {
			setrawcookie( self::SERVER_INFO_COOKIE_NAME, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
			return;
		}

		$cookie = json_encode( $cookie );
		$cookie = rawurlencode( $cookie );

		if ( strlen( $cookie ) > 4096 ) {
			Advanced_Ads::log( 'The cookie size is too large' );
			return;
		}

		// Prevent spaces from being converted to '+'
		setrawcookie( self::SERVER_INFO_COOKIE_NAME, $cookie, time() + $this->server_info->server_info_duration, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Remove server info on log in/out.
	 */
	public function log_in_out() {
		$server_conditions = $this->server_info->get_all_server_conditions();

		$n_cookie = $this->parse_existing_cookies();
		if ( isset( $n_cookie['conditions'] ) ) {
			foreach ( (array) $n_cookie['conditions'] as $cond_name => $cond ) {
				if ( isset( $server_conditions[ $cond_name ]['passive_info']['remove'] )
					&& $server_conditions[ $cond_name ]['passive_info']['remove'] === 'login' ) {
					unset ( $n_cookie['conditions'][ $cond_name ] );
				}
			}
		}

		$this->set_cookie( $n_cookie );
	}

}
