<?php

class Advanced_Ads_Geo {
	/**
	 * Holds plugin base class
	 *
	 * @var Advanced_Ads_Geo_Plugin
	 * @since 1.0.0
	 */
	protected $plugin;

	/**
	 * Initialize the plugin
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->plugin = Advanced_Ads_Geo_Plugin::get_instance();

		// register events when all plugins are loaded.
		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * Load actions and filters
	 */
	public function wp_admin_plugins_loaded() {

	}

	/**
	 * Look at the options and check whether they contain valid values for a lat/lon distance check
	 */
	public static function check_for_valid_lat_lon_options( $options = array() ) {
		return isset( $options['lat'] ) && isset( $options['lon'] ) && isset( $options['distance_condition'] ) && isset( $options['distance'] ) && isset( $options['distance_unit'] )
		       && is_numeric( $options['lat'] ) && is_numeric( $options['lon'] ) && is_numeric( $options['distance'] ) && '' !== $options['distance_condition'] && '' !== $options['distance_unit'];
	}

	/**
	 * Computes the distance between the coordinates and returns the result
	 */
	public static function calculate_distance( $lat1, $lon1, $lat2, $lon2, $unit = 'km' ) {
		$lat1 = deg2rad( $lat1 );
		$lon1 = deg2rad( $lon1 );
		$lat2 = deg2rad( $lat2 );
		$lon2 = deg2rad( $lon2 );

		$dLon = $lon2 - $lon1;
		$a    = pow( cos( $lat2 ) * sin( $dLon ), 2 ) + pow( cos( $lat1 ) * sin( $lat2 ) - sin( $lat1 ) * cos( $lat2 ) * cos( $dLon ), 2 );
		$b    = sin( $lat1 ) * sin( $lat2 ) + cos( $lat1 ) * cos( $lat2 ) * cos( $dLon );

		$rad = atan2( sqrt( $a ), $b );
		if ( $unit == 'mi' ) {
			return $rad * 3958.755865744; //6371.0 / 1.609344;.
		} else {
			return $rad * 6371.0;
		}
	}

	/**
	 * Check geo visitor condition
	 *
	 * @param array $options visitor condition options.
	 *
	 * @return bool true if ad can be displayed.
	 * @since 1.0.0
	 */
	public static function check_geo( $options = array() ) {
		$method = Advanced_Ads_Geo_Plugin::get_current_targeting_method();

		if ( ( ! isset( $options['country'] ) && ! isset( $options['region'] ) && ! isset( $options['city'] ) )
		     || ( '' === $options['country'] && '' === $options['region'] && '' === $options['city'] ) ) {
			// maybe we got a valid lat/lon condition.
			// TODO: this check should also take place when the user creates the condition and raise warnings etc.
			$hasValidLatLonOptions = self::check_for_valid_lat_lon_options( $options );
			if ( ! $hasValidLatLonOptions || $method === 'sucuri' ) {
				// TODO: right now the lat/lon check is not supported for sucuri.
				return true;
			}
		}

		// switch by method.
		switch ( $method ) :
			case 'sucuri':
				return self::check_geo_sucuri( $options );
				break;
			default:
				return self::check_geo_default( $options );
		endswitch;
	}

	/**
	 * Check geo visitor condition for Sucuri header method
	 *
	 * @since 1.2
	 */
	static function check_geo_sucuri( $options = array() ) {

		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is';
		$country  = isset( $options['country'] ) ? trim( $options['country'] ) : '';

		$api          = Advanced_Ads_Geo_Api::get_instance();
		$country_code = Advanced_Ads_Geo_Plugin::get_sucuri_country();

		if ( 'is_not' === $operator ) {
			// check EU.
			if ( 'EU' === $country ) {
				return ! $api->is_eu_state( $country_code );
			}

			// check country.
			return $country !== $country_code;
		} else {
			// check EU.
			if ( 'EU' === $country ) {
				return $api->is_eu_state( $country_code );
			}

			// check country.
			return $country === $country_code;
		}

		return false;

	}

	/**
	 * Check geo visitor condition
	 *
	 * @since 1.2
	 */
	public static function check_geo_default( $options = array() ) {

		$geo_mode              = isset( $options['geo_mode'] ) ? $options['geo_mode'] : 'classic';
		$hasValidLatLonOptions = self::check_for_valid_lat_lon_options( $options );
		$operator              = isset( $options['operator'] ) ? $options['operator'] : 'is';
		$country               = isset( $options['country'] ) ? trim( $options['country'] ) : '';
		$region                = isset( $options['region'] ) ? trim( $options['region'] ) : '';
		$city                  = isset( $options['city'] ) ? trim( $options['city'] ) : '';

		$lat                = isset( $options['lat'] ) ? $options['lat'] : null;
		$lon                = isset( $options['lon'] ) ? $options['lon'] : null;
		$distance           = isset( $options['distance'] ) ? $options['distance'] : null;
		$distance_condition = isset( $options['distance_condition'] ) ? $options['distance_condition'] : null;
		$distance_unit      = isset( $options['distance_unit'] ) ? $options['distance_unit'] : 'km';

		$api            = Advanced_Ads_Geo_Api::get_instance();
		$ip             = $api->get_real_IP_address();
		$country_code   = '';
		$visitor_city   = '';
		$visitor_region = '';

		// get locale.
		$options = Advanced_Ads_Geo_Plugin::get_instance()->options();
		$locale  = isset( $options[ AAGT_SLUG ]['locale'] ) ? $options[ AAGT_SLUG ]['locale'] : 'en';

		// reuse already existing location information to save db requests on the same page impression.
		if ( ! $ip ) {
			if ( 'is_not' === $operator ) {
				return true;
			} else {
				return false;
			}
		} elseif ( $api->used_city_reader && $city && $api->current_city ) {
			$continent_code = $api->current_continent;
			$country_code   = $api->current_country;
			$visitor_city   = $api->current_city;
		} elseif ( $api->used_city_reader && $region && $api->current_region ) {
			$continent_code = $api->current_continent;
			$country_code   = $api->current_country;
			$visitor_region = $api->current_region;
		} elseif ( ! $city && ! $region && $api->current_country ) {
			$continent_code = $api->current_continent;
			$country_code   = $api->current_country;
		} else {
			try {
				// get correct reader.
				if ( $city || $region || $hasValidLatLonOptions ) {
					$reader                = $api->get_GeoIP2_city_reader();
					$api->used_city_reader = true;
				} else {
					$reader = $api->get_GeoIP2_country_reader();
				}

				if ( $reader ) {
					// Look up the IP address.
					if ( $city || $region || $hasValidLatLonOptions ) {
						try {
							$record = $reader->city( $ip );
						} catch ( Exception $e ) {
						}
					} else {
						try {
							$record = $reader->country( $ip );
						} catch ( Exception $e ) {
						}
					}

					if ( ! empty( $record ) ) {
						$api->current_country   = $country_code = $record->country->isoCode;
						$api->current_continent = $continent_code = $record->continent->code;
						if ( $city ) {

							$api->current_city = $visitor_city = isset( $record->city->name ) ? $record->city->name : __( '(unknown city)', 'advanced-ads-geo' );
							if ( isset( $record->city->names[ $locale ] ) && $record->city->names[ $locale ] ) {
								$api->current_city = $visitor_city = $record->city->names[ $locale ];
							}
						}
						if ( $region ) {
							$api->current_region = $visitor_region = isset( $record->subdivisions[0]->name ) ? $record->subdivisions[0]->name : __( '(unknown region)', 'advanced-ads-geo' );
							if ( isset( $record->subdivisions[0]->names[ $locale ] ) && $record->subdivisions[0]->names[ $locale ] ) {
								$api->current_region = $visitor_region = $record->subdivisions[0]->names[ $locale ];
							}
						}
						if ( isset( $record->location ) && isset( $record->location->latitude ) && isset( $record->location->longitude ) ) {
							$api->current_lat = $record->location->latitude;
							$api->current_lon = $record->location->longitude;
						}
					}
				} else {
					error_log( 'Advanced Ads Geo: ' . __( 'Geo Databases not found', 'advanced-ads-geo' ) );
				}

			} catch ( \GeoIp2\Exception\AddressNotFoundException $e ) {
				if ( defined( 'ADVANCED_ADS_GEO_CHECK_DEBUG' ) ) {
					$log_content = sprintf( __( "Address not found: %s", 'advanced-ads-geo' ), $e->getMessage() ) . "\n";
					error_log( $log_content, 3, WP_CONTENT_DIR . '/geo-check.log' );
				}

				return false;
			}
		}

		// convert to lower case.
		if ( function_exists( 'mb_strtolower' ) ) {
			$city           = mb_strtolower( $city, 'utf-8' );
			$region         = mb_strtolower( $region, 'utf-8' );
			$visitor_city   = mb_strtolower( $visitor_city, 'UTF-8' );
			$visitor_region = mb_strtolower( $visitor_region, 'UTF-8' );
		}

		if ( defined( 'ADVANCED_ADS_GEO_CHECK_DEBUG' ) ) {
			$log_content = "GEO CHECK (setting|visitor): COUNTRY {$country}|{$country_code} – REGION {$region}|{$visitor_region} – CITY {$city}|{$visitor_city}" . "\n";
			error_log( $log_content, 3, WP_CONTENT_DIR . '/geo-check.log' );
		}

		// set up data for continent search.
		if ( 0 === strpos( $country, 'CONT_' ) ) {
			$country_code = 'CONT_' . $continent_code;
		}

		if ( 'latlon' === $geo_mode ) {
			if ( $hasValidLatLonOptions ) {
				$dst = self::calculate_distance( $api->current_lat, $api->current_lon, $lat, $lon, $distance_unit );
				if ( 'gt' === $distance_condition ) {
					return $dst > $distance;
				}

				return $dst <= $distance;
			}

			return true;
		} elseif ( 'is_not' === $operator ) {
			// check city.
			if ( $city ) {
				return $city !== $visitor_city;
			} elseif ( $region ) { // check region.
				return $region !== $visitor_region;
			}
			// check EU.
			if ( 'EU' === $country ) {
				return ! $api->is_eu_state( $country_code );
			}

			// check country.
			return $country !== $country_code;
		} else {
			// check city.
			if ( $city ) {
				return $city === $visitor_city;
			} elseif ( $region ) {
				return $region === $visitor_region;
			}
			// check EU.
			if ( 'EU' === $country ) {
				return $api->is_eu_state( $country_code );
			}

			// check country.
			return $country === $country_code;
		}

		return false;
	}

	/**
	 * Get geo information to use in passive cache-busting.
	 */
	public static function get_passive() {
		$method = Advanced_Ads_Geo_Plugin::get_current_targeting_method();

		// switch by method.
		switch ( $method ) :
			case 'sucuri':
				return self::get_passive_sucuri();
				break;
			default:
				return self::get_passive_default();
		endswitch;
	}

	/**
	 * Get geo information to use in passive cache-busting.
	 * Sucuri header method
	 *
	 * @since 1.2
	 */
	public static function get_passive_sucuri() {
		$api          = Advanced_Ads_Geo_Api::get_instance();
		$country_code = Advanced_Ads_Geo_Plugin::get_sucuri_country();

		return array(
			'country_code' => $country_code,
			'is_eu_state'  => $api->is_eu_state( $country_code ),
			'is_sucuri'    => true,
		);
	}

	/**
	 * Get geo information to use in passive cache-busting.
	 * Default method.
	 */
	public static function get_passive_default( $options = array() ) {
		$api = Advanced_Ads_Geo_Api::get_instance();
		$ip  = $api->get_real_IP_address();

		// get locale.
		$options = Advanced_Ads_Geo_Plugin::get_instance()->options();
		$locale  = isset( $options[ AAGT_SLUG ]['locale'] ) ? $options[ AAGT_SLUG ]['locale'] : 'en';

		$r = array();

		if ( ! $ip ) {
			return $r;
		}
		// reuse already existing location information to save db requests on the same page impression.
		if ( ! $api->used_city_reader || ! $api->current_city || ! $api->current_region || ! $api->current_country ) {
			try {
				$reader                = $api->get_GeoIP2_city_reader();
				$api->used_city_reader = true;

				if ( $reader ) {
					try {
						$record = $reader->city( $ip );
					} catch ( Exception $e ) {
						return $r;
					}

					if ( ! empty( $record ) ) {
						$api->current_country   = $record->country->isoCode;
						$api->current_continent = $record->continent->code;

						$api->current_city = isset( $record->city->name ) ? $record->city->name : __( '(unknown city)', 'advanced-ads-geo' );
						if ( isset( $record->city->names[ $locale ] ) && $record->city->names[ $locale ] ) {
							$api->current_city = $record->city->names[ $locale ];
						}
						$api->current_region = isset( $record->subdivisions[0]->name ) ? $record->subdivisions[0]->name : __( '(unknown region)', 'advanced-ads-geo' );
						if ( isset( $record->subdivisions[0]->names[ $locale ] ) && $record->subdivisions[0]->names[ $locale ] ) {
							$api->current_region = $record->subdivisions[0]->names[ $locale ];
						}
						if ( isset( $record->location ) && isset( $record->location->latitude ) && isset( $record->location->longitude ) ) {
							$api->current_lat = $record->location->latitude;
							$api->current_lon = $record->location->longitude;
						}
					}
				} else {
					return $r;
				}
			} catch ( \GeoIp2\Exception\AddressNotFoundException $e ) {
				return $r;
			}
		}

		$r['visitor_city']   = $api->current_city;
		$r['visitor_region'] = $api->current_region;
		$r['country_code']   = $api->current_country;
		$r['continent_code'] = $api->current_continent;
		$r['is_eu_state']    = $api->is_eu_state( $api->current_country );
		$r['current_lat']    = $api->current_lat;
		$r['current_lon']    = $api->current_lon;

		return $r;
	}
}
