<?php

class Advanced_Ads_Pro_Placement_Tests {
	/**
	 * Instance of this class.
	 * 
	 * @var      object
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	protected $placement_tests;

	/**
	 * delivered placement tests when cache-busting is not used
	 * contains pairs placement_id => test_id
	 * 
	 * @var array 
	 */
	public $delivered_tests = array();

	/**
	 * contains placement IDs, that can not be delivered using cache-busting
	 * they can not be randomly selected using JavaScript
	 *
	 * @var array
	 */
	public $no_cb_fallbacks = array();

	protected $random_placements;

	private function __construct() {
		if ( is_admin() ) {
			// display weight header in placement table
			add_action( 'advanced-ads-placements-list-column-header', array( $this, 'display_placement_weight_header' ) );
			// display weight selector in placement table
			add_action( 'advanced-ads-placements-list-column', array( $this, 'display_placement_weight_selector' ), 10, 2 );
			// display button in placement table
			add_filter( 'advanced-ads-placements-list-buttons', array( $this, 'display_save_new_test_button' ) );
			// display placement tests table
			add_action( 'advanced-ads-placements-list-before', array( $this, 'display_placement_tests' ) );
			// update placements and placement tests based on form submission
			add_filter( 'advanced-ads-update-placements', array( $this, 'update_placements_and_tests' ) );

			add_action( 'advanced-ads-export', array( $this, 'export' ), 10, 2 );
			add_action( 'advanced-ads-import', array( $this, 'import' ), 10, 3 );
		}
		// check if placement can be displayed
		add_action( 'advanced-ads-can-display-placement', array( $this, 'placement_can_display' ), 13, 2 );
		// add ad select arguments: inject test_id
		add_filter( 'advanced-ads-ad-select-args', array( $this, 'additional_ad_select_args' ), 9, 3 );
		// send emails using CRON
		add_action( 'advanced-ads-placement-tests-emails', array( $this, 'send_emails' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return obj Advanced_Ads_Pro_Placement_Tests a single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * display weight header in placement table
	 */
	public function display_placement_weight_header() { ?> 
		<th></th><?php
	}	

	/**
	 * display weight selector in placement table
	 */
	public function display_placement_weight_selector( $_placement_slug, $_placement ) { 
		$placement_types = Advanced_Ads_Placements::get_placement_types();
		$show = ! isset( $placement_types[ $_placement['type'] ]['options']['placement-cache-busting'] ) || $placement_types[ $_placement['type'] ]['options']['placement-cache-busting'];
		include plugin_dir_path(__FILE__) . '/views/setting_placement_test_weight.php';
	}	

	/**
	 * display button in placement table
	 */
	public function display_save_new_test_button() { ?>
		<button type="button" title="<?php _e( 'Save New Test', 'advanced-ads-pro' ); ?>" id="advads-save-placement-test-button" class="button-secondary" disabled="disabled"><?php
		_e( 'Save New Test', 'advanced-ads-pro' ); ?></button><?php
	}

	/**
	 * display placement tests table
	 *
	 */
	public function display_placement_tests( $placements ) {
		$placement_tests = $this->get_placement_tests_array();
		$placements = Advanced_Ads::get_ad_placements_array();
		$adsense_limit = Advanced_Ads_AdSense_Data::get_instance()->get_limit_per_page();

		include plugin_dir_path(__FILE__) . '/views/placement_tests.php';
	}

	/**
	 * update placements and placement tests based on form submission
	 *
	 */
	public function update_placements_and_tests( $success ) {
		// create new test
		if ( isset( $_POST['advads']['placement_test'] ) && is_array( $_POST['advads']['placement_test'] ) && check_admin_referer( 'advads-placement-test', 'advads_placement_test' ) ) {
			$placement_tests = $this->get_placement_tests_array();
			$placements = Advanced_Ads::get_ad_placements_array();
			// sort by weights
			arsort( $_POST['advads']['placement_test'] ); 
			$new_placements = array();
			$test_id = 'pt_' . md5( uniqid( time(), true ) );

			foreach ( $_POST['advads']['placement_test'] as $placement_id => $placement_weight ) {
				if ( isset( $placements[ $placement_id ] ) ) {
					$new_placements[ $placement_id ] = $placement_weight;
					$placements[ $placement_id ]['options']['test_id'] = $test_id;
				}
			}
			// save test if it contains > 1 placements
			if ( count( $new_placements ) > 1 ) {
				$placement_tests[ $test_id ] = array(
					'user_id' => get_current_user_id(),
					'placements' => $new_placements,
				);

				$this->update_placement_tests_array( $placement_tests );

				update_option( 'advads-ads-placements', $placements );
				$success = true;
			}
		}

		// update tests
		if ( isset( $_POST['advads']['placement_tests'] ) && check_admin_referer( 'advads-placement-test', 'advads_placement_test' ) ) {
			$placement_tests = $this->get_placement_tests_array();
			$placements = Advanced_Ads::get_ad_placements_array();

			foreach ( (array) $_POST['advads']['placement_tests'] as $placement_test_id => $placement_test ) {
				if ( isset( $placement_tests[ $placement_test_id ] ) && is_array( $placement_tests[ $placement_test_id ] ) ) {
					// delete test
					if ( isset( $placement_test['delete'] ) ) {
						if ( isset( $placement_tests[ $placement_test_id ]['placements'] ) && is_array( $placement_tests[ $placement_test_id ]['placements'] ) ) {
							foreach( $placement_tests[ $placement_test_id ]['placements'] as $placement_id => $placement_name ) {
								// detach placements from this test
								unset( $placements[ $placement_id ]['options']['test_id'] );
							}
						}

						unset( $placement_tests[ $placement_test_id ] );
						continue;
					}

					$placement_tests[ $placement_test_id ]['expiry_date'] = $this->extract_expiry_date( $placement_test );
				}
			}

			update_option( 'advads-ads-placements', $placements );
			$this->update_placement_tests_array( $placement_tests );
			$success = true;
		}	

		// save placements
		if ( isset($_POST['advads']['placements']) && check_admin_referer( 'advads-placement', 'advads_placement' )){
			$placement_items = $_POST['advads']['placements'];

			$placements = Advanced_Ads::get_ad_placements_array();
			$placement_tests = $this->get_placement_tests_array();
			$need_update = false;
			
			foreach ( $placement_items as $_placement_slug => $_placement ) {
				if ( isset( $_placement['delete'] ) ) {
					foreach ( $placement_tests as $k => &$placement_test ) {
						// if this placement exist in a test
						if ( isset( $placement_test['placements'] ) && is_array( $placement_test['placements'] ) && array_key_exists( $_placement_slug, $placement_test['placements'] ) ) {
							$need_update = true;
							// remove placement from test
							unset( $placement_test['placements'][ $_placement_slug ] );

							// remove test if it contains < 2 placements
							$placement_count = count( $placement_test['placements'] );

							if ( $placement_count === 0 ) {
								unset( $placement_tests[ $k ] );
							} elseif ( $placement_count === 1 ) {
								$last_placement_key = array_keys( $placement_test['placements'] );
								$last_placement_key = $last_placement_key[0];
								unset( $placements[ $last_placement_key ]['options']['test_id'] );
								unset( $placement_tests[ $k ] );
							}
						}
					}
				}

			}


			if ( $need_update ) {
				update_option( 'advads-ads-placements', $placements );
				$this->update_placement_tests_array( $placement_tests );				
			}

			$success = true;
		}

		Advanced_Ads_Pro::get_instance()->enable_placement_test_emails();

		return $success;
	}


	/**
	 * add ad select arguments: inject test_id
	 *
	 * @param array $args
	 * @return array $args
	 */
	public function additional_ad_select_args( $args, $method = null, $id = null ) {
		 if ( $method === 'placement' ) {
			$placements = Advanced_Ads::get_ad_placements_array();

			if ( isset( $placements[ $id ]['options']['test_id'] ) ) {
				$args['test_id'] = $placements[ $id ]['options']['test_id'];
			}
		}

		return $args;
	}


	/**
	 * check if placement can be displayed
	 *
	 * @param bool $return
	 * @param int $placement_id placement id
	 * @return bool false, if
	 * - cache-busting is not used and the placement belongs to a test, and was not randomly selected by weight
	 * - 1 placement was already delivered when 'no cache-busting' fallback is used
	 */
	public function placement_can_display( $return, $placement_id = 0 ) {
		$placements = Advanced_Ads::get_ad_placements_array();

		if ( isset( $placements[ $placement_id ]['options']['test_id'] ) ) {
			$pro_options = Advanced_Ads_Pro::get_instance()->get_options();
			$cache_busting_enabled = ! empty( $pro_options['cache-busting']['enabled'] );

			$placement = $placements[ $placement_id ];
			$test_id = $placement['options']['test_id'];
			$cb_off = ! $cache_busting_enabled
				|| ( isset( $placement['options']['cache-busting'] ) && $placement['options']['cache-busting'] === Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF );


			if ( ( $cb_off && ! in_array( $placement_id, $this->get_random_placements() ) )
				|| ( in_array( $test_id, $this->delivered_tests ) && ! array_key_exists( $placement_id, $this->delivered_tests ) )
			) {
				return false;
			}
		}

		return $return;
	}

	/**
	 * update the array with placement tests
	 *
	 * @param array
	 */
	public function update_placement_tests_array( $placement_tests ) {
		if ( is_array( $placement_tests ) ) {
			$this->placement_tests = $placement_tests;
			update_option( 'advads-ads-placement-tests', $placement_tests );
		}
	}

	/**
	 * get the array with placement tests
	 *
	 * @return array
	 */
	public function get_placement_tests_array(){
		if ( ! isset( $this->placement_tests ) ) {
			$this->placement_tests = get_option( 'advads-ads-placement-tests', array() );

			// load default array if not saved yet
			if ( ! is_array( $this->placement_tests ) ){
				$this->placement_tests = array();
			}
		}

		return $this->placement_tests;
	}

	/**
	 * get random placements from tests based on placement weight in a test (used without cache-busting)
	 *
	 * @return array
	 */
	public function get_random_placements() {
		if ( ! isset( $this->random_placements ) ) {
			$placement_tests = $this->get_placement_tests_array();
			$this->random_placements = array();

			foreach ( $placement_tests as $placement_test_id => $placement_test ) {
				if ( isset( $placement_test['placements'] ) && is_array( $placement_test['placements'] ) ) {
					if ( $random_placement_id = $this->get_random_placement_from_test( $placement_test['placements'] ) ) {
						$this->random_placements[] = $random_placement_id;
					};
				}
			}
		}

		return $this->random_placements;
	}

	/**
	 * get random placement by placement weight
	 *
	 * @param array $placement_weights e.g. array(A => 2, B => 3, C => 5)
	 * @source applied with fix for order http://stackoverflow.com/a/11872928/904614
	 */
	private function get_random_placement_from_test( array $placement_weights ) {
		// placements might have a weight of zero (0); to avoid mt_rand fail assume that at least 1 is set.
		$max = array_sum( $placement_weights );
		if ( $max < 1 ) {
			return;
		}

		$rand = mt_rand( 1, $max );

		foreach ( $placement_weights as $placement_id => $_weight ) {
			$rand -= $_weight;
			if ( $rand <= 0 ) {
				return $placement_id;
			}
		}
	}

	/**
	 * get names of placements for the test
	 *
	 * @param array $placement_test
	 * @return array $placements_names
	 */
	public function get_placement_names( $placement_test ) {
		$placement_names = array();
		$placements = Advanced_Ads::get_ad_placements_array();

		if ( isset( $placement_test['placements'] ) && is_array( $placement_test['placements'] ) ) {
			foreach ( $placement_test['placements'] as $placement_id => $placement_weight ) {
				if ( isset( $placements[ $placement_id ]['name'] ) ) {
					$placement_names[] = sprintf( '%s <em>(%d)</em>', $placements[ $placement_id ]['name'], $placement_weight );
				}
			}
		}

		return $placement_names;

	}

	/**
	 * return DateTime for timestamp or current time
	 * @uses Advanced_Ads_Admin::timezone_get_name, Advanced_Ads_Admin::get_wp_timezone
	 * @return obj DateTime
	 */
	public static function get_exp_time( $timestamp = null ) {
		$utc_ts = $timestamp ? $timestamp : time();
		
		$utc_time = date_create( '@' . $utc_ts );
		$tz_option = get_option( 'timezone_string' );
		$exp_time = clone  $utc_time;

		if ( $tz_option ) {
			$exp_time->setTimezone( Advanced_Ads_Admin::get_wp_timezone() );
		} else {
			$tz_name = Advanced_Ads_Admin::timezone_get_name( Advanced_Ads_Admin::get_wp_timezone() );
			$tz_offset = substr( $tz_name, 3 );
			$off_time = date_create( $utc_time->format( 'Y-m-d\TH:i:s' ) . $tz_offset );
			$offset_in_sec = date_offset_get( $off_time );
			$exp_time = date_create( '@' . ( $utc_ts + $offset_in_sec ) );
		}

		return $exp_time;

	}

	/**
	 * output expiry date form on placement page
	 * @uses Advanced_Ads_Admin::timezone_get_name, Advanced_Ads_Admin::get_wp_timezone
	 */
	public function output_expiry_date_form( $slug, $timestamp = null ) {
		if ( method_exists( 'Advanced_Ads_Admin', 'timezone_get_name' ) && method_exists( 'Advanced_Ads_Admin', 'get_wp_timezone' ) ) {
			global $wp_locale;
			$enabled = $timestamp ? true : false;
			$exp_time = $this->get_exp_time( $timestamp );

			list( $curr_year, $curr_month, $curr_day, $curr_hour, $curr_minute ) = explode( '-', $exp_time->format( 'Y-m-d-H-i' ) );
			$TZ = Advanced_Ads_Admin::timezone_get_name( Advanced_Ads_Admin::get_wp_timezone() );

			include plugin_dir_path(__FILE__) . '/views/settings_test_expiry_date.php';			
		}
	}

	/**
	* extract expire date from array ($_POST)
	*
	* @param array $test_data
	* @return Unix timestamp for the date, 0 otherwise
	*/
	public function extract_expiry_date( $test_data ) {
		// prepare expiry date
		if ( isset( $test_data['expiry_date']['enabled'] ) ) {
			$year   = absint( $test_data['expiry_date']['year'] );
			$month  = absint( $test_data['expiry_date']['month'] );
			$day    = absint( $test_data['expiry_date']['day'] );
			$hour   = absint( $test_data['expiry_date']['hour'] );
			$minute = absint( $test_data['expiry_date']['minute'] );

			$expiration_date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $year, $month, $day, $hour, $minute, '00' );
			$valid_date = wp_checkdate( $month, $day, $year, $expiration_date );

			if ( ! $valid_date ) {
				return 0;
			} else {
				$_gmDate = date_create( $expiration_date, Advanced_Ads_Admin::get_wp_timezone() );
				$_gmDate->setTimezone( new DateTimeZone( 'UTC' ) );
				$gmDate = $_gmDate->format( 'Y-m-d-H-i' );
				list( $year, $month, $day, $hour, $minute ) = explode( '-', $gmDate );
				return gmmktime($hour, $minute, 0, $month, $day, $year);
			}
		} 

		return 0;
	}


	/**
	 * send email to user if at least 1 placement test is expired
	 */
	public function send_emails() {
		$placement_tests = $this->get_placement_tests_array();
		$expiry_date_format = get_option( 'date_format' ). ', ' . get_option( 'time_format' );
		$combined_tests = array();

		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$from_email = $this->get_default_sender_email();

		$from = "From: \"$blogname\" <$from_email>";
		$message_headers = "$from\n"
		. "Content-Type: text/html; charset=\"" . get_option( 'blog_charset' ) . "\"\n";
		$message_subject = _x( 'Expired placement tests', 'placement tests', 'advanced-ads-pro' );

		foreach ( $placement_tests as $placement_test_id => $placement_test ) {
			if ( 
				! empty( $placement_test['user_id'] ) && 
				! empty( $placement_test['placements'] ) && is_array( $placement_test['placements'] ) && count( $placement_test['placements'] ) > 1 && 
				! empty ( $placement_test['expiry_date'] )
			) {
				$expiry_date = (int) $placement_test['expiry_date'];
				if ( $expiry_date <= 0 || $expiry_date > time() ) {
					continue;
				}

				if ( ! ( $user = get_user_by( 'ID', $placement_test['user_id'] ) ) || ! is_email( $user->user_email ) ) {
					continue;
				}
				// combine tests, that belong to given user id
				$combined_tests [ $placement_test['user_id']  ][ $placement_test_id ] = $placement_test;
			}
		}
		

		foreach ( $combined_tests as $user_id => $tests ) {
			$message_body = '';

			foreach ( $tests as $test_id => $test ) {
				$expiry_date_formatted = $this->get_exp_time( $test['expiry_date'] );
				$expiry_date_formatted = $expiry_date_formatted->format( $expiry_date_format );

				$message_body .= implode( _x( ' vs ', 'placement tests', 'advanced-ads-pro' ), $this->get_placement_names( $test ) ) .
					' - ' . $expiry_date_formatted . "<br />";
			}

			$message_body .= '<br />' 
			. sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=advanced-ads-placements' ) ),
				_x( 'Placement page', 'placement tests', 'advanced-ads-pro' ) );


			$user = get_user_by( 'ID', $user_id );

			if ( wp_mail( $user->user_email, $message_subject, $message_body, $message_headers ) ) {
				foreach ( $tests as $test_id => $test ) {
					unset( $placement_tests[ $test_id ]['expiry_date'] );
				}
				// do not send this email after
				$this->update_placement_tests_array( $placement_tests );
			}
		}

	}

	/**
	 * Generate default sender email.
	 */
	private function get_default_sender_email(){
		if( isset( $_SERVER['SERVER_NAME'] ) && $_SERVER['SERVER_NAME'] ){
			return 'noreply@' . preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );
		} else {
			return get_bloginfo( 'admin_email' );
		}

	}

	/**
	* return placements tests that can be randomly selected by JavaScript
	*
	* @return string
	*/
	public function get_placement_tests_js( $json_encode = true ) {
		// exclude tests, that was already delivered without cache-busting (cb: off, 'no cache-busting' fallback)
		$js_tests = array_diff_key( $this->get_placement_tests_array(), array_flip( $this->delivered_tests ) );
		$cb_off = Advanced_Ads_Pro_Module_Cache_Busting::OPTION_OFF;

		// exclude placements without cache-busting, so that JavaScript can not randomly select it based on weight
		$placements = Advanced_Ads::get_ad_placements_array();
		foreach ( $js_tests as &$js_test ) {
			if ( isset( $js_test['placements'] ) && is_array( $js_test['placements'] ) ) {

				foreach ( $js_test['placements'] as $placement_id => $placement_weight ) {
					if ( isset( $placements[ $placement_id ]['options']['cache-busting'] )
						&& ( $placements[ $placement_id ]['options']['cache-busting'] === $cb_off || in_array( $placement_id, $this->no_cb_fallbacks ) )
					) {
						unset( $js_test['placements'][ $placement_id ] );
					}
				}
			}
		}

		return $json_encode ? json_encode( $js_tests ) : $js_tests;

		return json_encode( $js_tests );
	}

	/**
	* export tests
	*
	* @param $items array requested items (ads, groups, etc.)
	* @param $export array array to encode to XML
	*/
	public function export( $items, &$export ) {
		if ( in_array( 'placements', $items ) ) {
			$placement_tests = $this->get_placement_tests_array();

			foreach ( $placement_tests as &$placement_test ) {
				if ( empty( $placement_test['user_id'] )
					|| ! isset( $placement_test['placements'] )
					|| ! is_array( $placement_test['placements'] )
					|| count( $placement_test['placements'] ) < 2
				) { continue; }

				// prevent nodes starting with number
				$placement_array = array();
				foreach ( $placement_test['placements']  as $placement_id => $placement_weight ) {
					$placement_array[] = array( 'placement_id' => $placement_id, 'weight' => $placement_weight );
				}
				$placement_test['placements'] = $placement_array;
			}

			if ( $placement_tests ) {
				$export['placement_tests'] = $placement_tests;
			}
		}
	}

	/**
	* import tests
	*
	* @param $decoded array decoded XML
	* @param $imported_data array imported data mapped with previous data, e.g. ids [ $old_ad_id => $new_ad_id ]
	* @param $messages array status messages
	*/
	public function import( $decoded, $imported_data, $messages ) {
		if ( isset( $decoded['placement_tests'] ) && is_array( $decoded['placement_tests'] ) ) {
			$existing_tests = $updated_tests = $this->get_placement_tests_array();

			foreach ( $decoded['placement_tests'] as $placement_test_id => $placement_test ) {
				if ( empty( $placement_test['user_id'] )
					|| ! isset( $placement_test['placements'] )
					|| ! is_array( $placement_test['placements'] )
					|| count( $placement_test['placements'] ) < 2
				) { continue; }

				if ( isset( $existing_tests[ $placement_test_id ] ) ) {
					$count = 1;

					while ( isset( $existing_tests[ $placement_test_id . '_' . $count] ) ) {
					    $count++;
					}

					$new_test_id = $placement_test_id . '_' . $count;
				} else {
					$new_test_id = $placement_test_id;
				}

				$new_test = array_diff_key( $placement_test , array( 'placements' => true ) );

				foreach ( $placement_test['placements'] as $placements_of_test ) {
					if ( empty( $placements_of_test['placement_id'] ) || empty( $placements_of_test['weight'] ) ) {
						continue;
					}

					$placement_id = $placements_of_test['placement_id'];
					$placement_key_uniq = $placement_id;

					if ( isset( $imported_data['placements'][ $placement_id ] ) && $imported_data['placements'][ $placement_id ] !== $placement_id ) {
						$placement_key_uniq = $imported_data['placements'][ $placement_id ];
					}

					$new_test['placements'][ $placement_key_uniq ] = $placements_of_test['weight'];
				}

				if ( count( $new_test['placements'] ) > 1 ) {
					$placement_names = $this->get_placement_names( $new_test );
					$placement_names = implode( _x( ' vs ', 'placement tests', 'advanced-ads-pro' ), $placement_names );
					$messages[] = array( 'update', sprintf( __( 'Placement test <em>%s</em> created', 'advanced-ads-pro' ), $placement_names ) );

					$updated_tests[ $new_test_id ] = $new_test;
				}

			}

			if ( $existing_tests !== $updated_tests ) {
				$this->update_placement_tests_array( $updated_tests );
			}
		}
	}
}
