<?php
class Advanced_Ads_Tracking_Ajax {
    const TRACK_IMPRESSION = 'advanced-ads-tracking-track';

    public function __construct() {
        // register callback
        add_action( 'wp_ajax_' . self::TRACK_IMPRESSION, array( $this, 'track' ) ); // logged in users
        add_action( 'wp_ajax_nopriv_' . self::TRACK_IMPRESSION, array( $this, 'track' ) ); // frontend, not logged in

        add_action( 'wp_ajax_advads-tracking-check-slug', array( $this, 'check_slug' ) );
        add_action( 'wp_ajax_advads-tracking-immediate-report', array( $this, 'immedate_report' ) );
        add_action( 'wp_ajax_advads_load_stats', array( $this, 'load_stats' ) );
        add_action( 'wp_ajax_advads_load_stats_file', array( $this, 'load_stats_file' ) );
		add_action( 'wp_ajax_advads_stats_file_info', array( $this, 'get_stats_file_info' ) );

    }

	public function get_stats_file_info() {
        $nonce = ( isset( $_POST['nonce'] ) )? $_POST['nonce'] : '';
        if ( false === wp_verify_nonce( $nonce, 'advads-stats-page' ) ) die;
		$id = absint( $_POST['id'] );
		$data = Advanced_Ads_Tracking_Util::get_instance()->parse_CSV( $id );
		$result = array(
			'status' => false
		);
		if ( isset( $data['firstdate'] ) ) {
			$result = array(
				'status' => true,
				'firstdate' => $data['firstdate'],
				'lastdate' => $data['lastdate'],
				'ads' => implode( '-', array_keys( $data['ads'] ) ),
			);
		}
        header( 'Content-Type: application/json' );
        echo json_encode( $result );
		die;
	}

	/**
	 *  load stats from file for a given period
	 */
	public function load_stats_file(){
        $nonce = ( isset( $_POST['nonce'] ) )? $_POST['nonce'] : '';
        if ( false === wp_verify_nonce( $nonce, 'advads-stats-page' ) ) die;
        $result = array( 'status' => false );
        parse_str( $_POST['args'], $args );
		$id = absint( $args['file'] );
		$data = Advanced_Ads_Tracking_Util::get_instance()->parse_CSV( $id );
		if ( isset( $data['status'] ) && $data['status'] ) {
			$result = $this->prepare_stats_from_file( $data, $args['period'], $args['from'], $args['to'], $args['groupby'] );
		}
        header( 'Content-Type: application/json' );
        echo json_encode( $result );
		die;
	}

	public static function split_date( $d ) {
		return substr($d,0,4) . "-" . substr($d,4,2) . "-" . substr($d,6,2);
	}
	
	/**
	 *  prepare data from CSV before sending it back to the browser
	 */
	private function prepare_stats_from_file( $data, $period, $from, $to, $groupby ) {
		$result = array( 'status' => true, 'stats' => array() );
		$_from = intval( str_replace( array( '-', '/' ), array( '', '' ), $from ) );
		$_to = intval( str_replace( array( '-', '/' ), array( '', '' ), $to ) );

		$periodstart = '';
		$periodend = '';

		// define the timetsamp for the first and last record to return
		switch( $period ){
			case 'firstmonth':
				$firstdate = date_create( $data['firstdate'] );
				$_from = intval( $firstdate->format( 'Ym01' ) );
				$_to = intval( $firstdate->format( 'Ymt' ) );
				$periodstart = $firstdate->format( 'Y-m-01' );
				$periodend = $firstdate->format( 'Y-m-t' );
				break;
			case 'latestmonth':
				$lastdate = date_create( $data['lastdate'] );
				$_from = intval( $lastdate->format( 'Ym01' ) );
				$_to = intval( $lastdate->format( 'Ymd' ) );
				$periodstart = $lastdate->format( 'Y-m-01' );
				$periodend = $lastdate->format( 'Y-m-t' );
				break;
			default: // custom
				$periodstart = $from;
				$periodend = $to;
		}
		$imprs = array();
		$clicks = array();
		$adIDs = array_keys( $data['ads'] );
		$date = null;
		$group_clicks = array();
		$group_imprs = array();

		end( $data['impressions'] );
		$last_ts = key( $data['impressions'] );
		reset( $data['impressions'] );

		foreach ( $data['impressions'] as $ts => $_imprs ) {
			switch ( $groupby ) {
				case 'month':
					if ( $ts >= $_from && $ts <= $_to ) {
						$_date = date_create( self::split_date( $ts ) );
						if ( null === $date ) {
							$date = $_date->format( 'Y-m' );
						}
						if ( $ts == $last_ts || $date != $_date->format( 'Y-m' ) ) {
							if ( $ts == $last_ts ) {
								foreach ( $adIDs as $ad_id ) {
									if ( !isset( $group_imprs[$ad_id] ) ) {
										$group_imprs[$ad_id] = 0;
									}
									if ( !isset( $group_clicks[$ad_id] ) ) {
										$group_clicks[$ad_id] = 0;
									}
									$group_imprs[$ad_id] += intval( $_imprs[$ad_id] );
									if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
										$group_clicks[$ad_id] += intval( $data['clicks'][$ts][$ad_id] );
									}
								}
							}
							$imprs[$date] = $group_imprs;
							$clicks[$date] = $group_clicks;

							$date = $_date->format( 'Y-m' );
							$group_clicks = array();
							$group_imprs = array();
						}
						foreach ( $adIDs as $ad_id ) {
							if ( !isset( $group_imprs[$ad_id] ) ) {
								$group_imprs[$ad_id] = 0;
							}
							if ( !isset( $group_clicks[$ad_id] ) ) {
								$group_clicks[$ad_id] = 0;
							}
							$group_imprs[$ad_id] += intval( $_imprs[$ad_id] );
							if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
								$group_clicks[$ad_id] += intval( $data['clicks'][$ts][$ad_id] );
							}
						}
					} else if ( $ts > $_to && !empty( $group_imprs ) ) {
						foreach ( $adIDs as $ad_id ) {
							if ( !isset( $group_imprs[$ad_id] ) ) {
								$group_imprs[$ad_id] = 0;
							}
							if ( !isset( $group_clicks[$ad_id] ) ) {
								$group_clicks[$ad_id] = 0;
							}
							$group_imprs[$ad_id] += intval( $_imprs[$ad_id] );
							if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
								$group_clicks[$ad_id] += intval( $data['clicks'][$ts][$ad_id] );
							}
						}
						$imprs[$date] = $group_imprs;
						$clicks[$date] = $group_clicks;
						$group_clicks = array();
						$group_imprs = array();
					}
					break;
				case 'week':
					if ( $ts >= $_from && $ts <= $_to ) {
						$_date = date_create( self::split_date( $ts ) );
						if ( null === $date ) {
							$date = $_date->format( 'o-\WW' );
						}
						if ( $ts == $last_ts || $date != $_date->format( 'o-\WW' ) || $ts > $_to ) {
							if ( $ts == $last_ts ) {
								foreach ( $adIDs as $ad_id ) {
									if ( !isset( $group_imprs[$ad_id] ) ) {
										$group_imprs[$ad_id] = 0;
									}
									if ( !isset( $group_clicks[$ad_id] ) ) {
										$group_clicks[$ad_id] = 0;
									}
									$group_imprs[$ad_id] += intval( $_imprs[$ad_id] );
									if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
										$group_clicks[$ad_id] += intval( $data['clicks'][$ts][$ad_id] );
									}
								}
							}
							$imprs[$date] = $group_imprs;
							$clicks[$date] = $group_clicks;

							$date = $_date->format( 'o-\WW' );
							$group_clicks = array();
							$group_imprs = array();
						}
						foreach ( $adIDs as $ad_id ) {
							if ( !isset( $group_imprs[$ad_id] ) ) {
								$group_imprs[$ad_id] = 0;
							}
							if ( !isset( $group_clicks[$ad_id] ) ) {
								$group_clicks[$ad_id] = 0;
							}
							$group_imprs[$ad_id] += intval( $_imprs[$ad_id] );
							if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
								$group_clicks[$ad_id] += intval( $data['clicks'][$ts][$ad_id] );
							}
						}
					} else if ( $ts > $_to && !empty( $group_imprs ) ) {
						foreach ( $adIDs as $ad_id ) {
							if ( !isset( $group_imprs[$ad_id] ) ) {
								$group_imprs[$ad_id] = 0;
							}
							if ( !isset( $group_clicks[$ad_id] ) ) {
								$group_clicks[$ad_id] = 0;
							}
							$group_imprs[$ad_id] += intval( $_imprs[$ad_id] );
							if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
								$group_clicks[$ad_id] += intval( $data['clicks'][$ts][$ad_id] );
							}
						}
						$imprs[$date] = $group_imprs;
						$clicks[$date] = $group_clicks;
						$group_clicks = array();
						$group_imprs = array();
					}
					break;
				default: // day
					$date = self::split_date( $ts );
					if ( $ts >= $_from && $ts <= $_to ) {
						if ( !isset( $imprs[$date] ) ) {
							$imprs[$date] = array();
						}
						if ( !isset( $clicks[$date] ) ) {
							$clicks[$date] = array();
						}
						foreach ( $adIDs as $ad_id ) {
							if ( isset( $_imprs[$ad_id] ) ) {
								$imprs[$date][$ad_id] = $_imprs[$ad_id];
							}
							if ( isset( $data['clicks'][$ts][$ad_id] ) ) {
								$clicks[$date][$ad_id] = $data['clicks'][$ts][$ad_id];
							}
						}
					}

			}
		}

		if ( $imprs ) {
			// prepare jqplot and datatable variables that depend on date of first record ( if any record is found )
			$formatstring = "%b&nbsp;%#d";
			$firstdate = key( $imprs );

			switch ( $groupby ) {
				case 'month' :
					$formatstring = "%B";
					$firstdate = '';
					break;
				case 'week':
					$formatstring = _x( 'from %b&nbsp;%#d', 'format for week group in stats table', 'advanced-ads-tracking' );
					$firstdate = date( 'Y-m-d', strtotime( $firstdate . ' -1 week') );
					break;
				default: // day
					$firstdate = date( 'Y-m-d', strtotime( $firstdate . ' -1 day' ) );
			}
			$result['stats']['xAxisThickformat'] = $formatstring;
			$result['stats']['firstDate'] = $firstdate;
			$result['stats']['impr'] = $imprs;
			$result['stats']['click'] = $clicks;
			$result['stats']['periodEnd'] = $periodend;
			$result['stats']['periodStart'] = $periodstart;
			$result['stats']['ads'] = $data['ads'];
		}
		return $result;
	}

    /**
     *  load stats for a given period
     */
    public function load_stats() {
        $nonce = ( isset( $_POST['nonce'] ) )? $_POST['nonce'] : '';
        if ( false === wp_verify_nonce( $nonce, 'advads-stats-page' ) ) die;
        $result = array( 'status' => false );
        parse_str( $_POST['args'], $args );

        if ( !empty( $args['period'] ) ) {

            $util = Advanced_Ads_Tracking_Util::get_instance();
            $admin = new Advanced_Ads_Tracking_Admin();
            $result['status'] = true;
            $result['stats'] = array();

            /**
             *  prepare all locale dependant and groupby dependant variables needed jqplot and datatable
             */

            $dateFormat = 'Y-m-d';
            $groupFormat = 'Y-m-d';

            // groupby-s formating
            $groupby = $args['groupby'];
            $groupbys = array(
                // group format, axis label, value conversion for graph
                'day' => array('Y-m-d', __('day', 'advanced-ads-tracking'), _x('Y-m-d', 'date format on stats page', 'advanced-ads-tracking')),
                'week' => array('o-\WW', __('week', 'advanced-ads-tracking'), _x('Y-m-d', 'date format on stats page', 'advanced-ads-tracking')),
                'month' => array('Y-m', __('month', 'advanced-ads-tracking'), _x('Y-m', 'date format on stats page', 'advanced-ads-tracking')),
            );

            if ( !isset( $groupbys[$groupby] ) ) {
                $groupby = null;
            } else {
                $groupFormat = $groupbys[$groupby][0];
                $dateFormat = $groupbys[$groupby][2];
                if ( 'week' == $groupby ) {
                    // $groupFormat = 'Y-m-d';
                }
            }

            /**
             *  load result from DB
             */
            $sql_args = array(
                'period' => $args['period'],
                'groupby' => $args['groupby'],
                'ad_id' => explode( '-', $_POST[ 'ads' ] ),
                'groupFormat' => $groupFormat,
            );


            if ( 'custom' == $args['period'] ) {
                $sql_args['from'] = $args['from'];
                $sql_args['to'] = $args['to'];
            }

            $impr = $admin->load_stats( $sql_args, $util->get_impression_table() );
            $clicks = $admin->load_stats( $sql_args, $util->get_click_table() );
            $firstdate = '';
            if ( $impr ) {
                $result['stats']['click'] = $clicks;
                $result['stats']['impr'] = $impr;

                $time = time();
                $today = date_create( '@' . $time );

                /**
                 *  get the real start of period, in case it is anterior to the first stat found in order to keep stats length in comparison
                 */
                switch ( $sql_args['period'] ) {
                    case 'custom':
                        $result['stats']['periodStart'] = $sql_args['from'];
                        $result['stats']['periodEnd'] = $sql_args['to'];
                        break;

                    case 'today':
                        $result['stats']['periodStart'] = get_date_from_gmt( $today->format( 'Y-m-d H:i:s' ), 'Y-m-d' );
                        $result['stats']['periodEnd'] = $result['stats']['periodStart'];
                        break;

                    case 'yesterday':
                        $yesterday = date_create( '@' . ( $time - ( 24 * 3600 ) ) );
                        $result['stats']['periodStart'] = get_date_from_gmt( $yesterday->format( 'Y-m-d H:i:s' ), 'Y-m-d' );
                        $result['stats']['periodEnd'] = $result['stats']['periodStart'];
                        break;

                    case 'lastmonth' :
                        /**
                         *  get next month start without using DateInterval for PHP 5.2
                         */
                        $year = intval( $today->format( 'Y' ) );
                        $month = intval( $today->format( 'm' ) );
                        $decr_year = ( 1 > $month - 1 )? 1 : 0;
                        $last_month = ( 1 > $month - 1 )? 12 - $month - 1 : $month - 1;
						$days_count = cal_days_in_month( CAL_GREGORIAN, $last_month , ( $year - $decr_year ) );
                        $result['stats']['periodStart'] = get_date_from_gmt( ( $year - $decr_year ) . '-' . $last_month . '-1 ' . $today->format( 'H:i:s' ), 'Y-m-d' );
                        $result['stats']['periodEnd'] = get_date_from_gmt( ( $year - $decr_year ) . '-' . $last_month . '-' . $days_count . ' ' . $today->format( 'H:i:s' ), 'Y-m-d' );
                        break;

                    case 'thismonth':
                        $result['stats']['periodStart'] = get_date_from_gmt( $today->format( 'Y-m-1 H:i:s' ), 'Y-m-d' );
                        /**
                         *  get next month start without using DateInterval for PHP 5.2
                         */
                        $year = intval( $today->format( 'Y' ) );
                        $month = intval( $today->format( 'm' ) );
						$days_count = cal_days_in_month( CAL_GREGORIAN, $month , $year );
                        $result['stats']['periodEnd'] = get_date_from_gmt( $today->format( 'Y-m-' . $days_count . ' H:i:s' ), 'Y-m-d' );
                        break;

                    case 'thisyear':
                        $result['stats']['periodStart'] = get_date_from_gmt( $today->format( 'Y-1-1 H:i:s' ), 'Y-m-d' );
                        $result['stats']['periodEnd'] = get_date_from_gmt( $today->format( 'Y-12-31 H:i:s' ), 'Y-m-d' );
                        break;

                    case 'lastyear':
                        $result['stats']['periodEnd'] = get_date_from_gmt( ( intval( $today->format( 'Y' ) ) - 1 ) . $today->format( '-12-31 H:i:s' ), 'Y-m-d' );
                        $result['stats']['periodStart'] = get_date_from_gmt( ( intval( $today->format( 'Y' ) ) - 1 ) . $today->format( '-01-01 H:i:s' ), 'Y-m-d' );
                        break;

                    default: // last 7 days
                        $last7days = $time - ( 7 * 24 * 3600 );
                        $D_last7days = date_create( '@' . $last7days );
                        $yesterday = date_create( '@' . ( $time - ( 24 * 3600 ) ) );
                        $result['stats']['periodStart'] = get_date_from_gmt( $D_last7days->format( 'Y-m-d H:i:s' ), 'Y-m-d' );
                        $result['stats']['periodEnd'] = get_date_from_gmt( $yesterday->format( 'Y-m-d H:i:s' ), 'Y-m-d' );
                }
            }
            /**
             *  prepare jqplot and datatable variables that depend on date of first record ( if any record is found )
             */
            if ( $impr ) {
                $formatstring = "%b&nbsp;%#d";
                reset( $impr );
                $firstdate = key( $impr );
                switch( $args['groupby'] ){
                    case 'week' :
                        $formatstring = _x( 'from %b&nbsp;%#d', 'format for week group in stats table', 'advanced-ads-tracking' );
                        $firstdate = date( 'Y-m-d', strtotime( $firstdate . ' -1 week') );
                        break;
                    case 'month' :
                        $formatstring = "%B";
                        $firstdate = '';
                        break;
                    default :
                        $firstdate = date( 'Y-m-d', strtotime( $firstdate . ' -1 day' ) );
                }

                $result['stats']['xAxisThickformat'] = $formatstring;
                $result['stats']['firstDate'] = $firstdate;
            }
        }
        if ( !empty( $firstdate ) && intval( str_replace( '-', '', $firstdate ) ) < 20100101 ) {
			// an invalid date has been found in the records
			$result = array(
				'status' => false,
				'msg' => 'invalid-record',
			);
		}
        header( 'Content-Type: application/json' );
        echo json_encode( $result );
        die;
    }

    /**
     *  send immediately an email report
     */
    public function immedate_report() {
        $nonce = ( isset( $_POST['nonce'] ) )? $_POST['nonce'] : '';
        if ( false === wp_verify_nonce( $nonce, 'advads-tracking-public-stats' ) ) die;
        $result = array(
            'status' => false,
        );
        $result = Advanced_Ads_Tracking_Util::get_instance()->send_email_report();
        header( 'Content-Type: application/json' );
        echo json_encode( $result );
        die;
    }

    /**
     *  check if a slug is taken
     *
     *  @since N/A
     */
    public function check_slug() {
        $nonce = ( isset( $_POST['nonce'] ) )? $_POST['nonce'] : '';
        if ( false === wp_verify_nonce( $nonce, 'advads-tracking-public-stats' ) ) die;
        $result = array(
            'status' => false,
        );
        $title = ( isset( $_POST['title'] ) && ! empty( $_POST['title'] ) )? stripslashes( $_POST['title'] ) : false;

        if ( $title ) {
            $to_slug = sanitize_title( $title );

            $category = get_term_by( 'slug', $to_slug, 'category' );
            $tag = get_term_by( 'slug', $to_slug, 'post_tag' );
            $link = get_term_by( 'slug', $to_slug, 'link_category' );
            $posts = new WP_Query( array( 'post_type' => 'any', 'name' => $to_slug ) );

            if ( $posts->have_posts() ) {
                $result['msg'] = __( 'This base name collides with an existing WordPress content (blog post, page or any public custom content)', 'advanced-ads-tracking' );
            } else if ( false !== $link ) {
                $result['msg'] = __( 'This base name collides with an existing link category', 'advanced-ads-tracking' );
            } else if ( false !== $tag ) {
                $result['msg'] = __( 'This base name collides with an existing blog post tag', 'advanced-ads-tracking' );
            } else if ( false !== $category ) {
                $result['msg'] = __( 'This base name collides with an existing blog post category', 'advanced-ads-tracking' );
            } else {
                // all clear
                $result['status'] = true;
            }
            $result['slug'] = $to_slug;
            $result['title'] = $title;
        }

        header( 'Content-Type:  application/json' );
        echo json_encode( $result );
        die;
    }

    /**
     * track impressions.
     * Does not provide any output.
     */
    public function track() {
        // set some headers to avoid caching and other
        $headers = array(
            'X-Content-Type-Options: nosniff',
            'Cache-Control: no-cache, must-revalidate, max-age=0, smax-age=0', // HTTP/1.1
            'Expires: Sat, 26 Jul 1997 05:00:00 GMT', // deprecated
            'X-Accel-Expires: 0',
        );
        foreach ($headers as $header) {
            @header($header, true);
        }
        // ensure headers are send (and not later modified by WP or other plugins)
        flush();

        // the remainder without browser interaction
        ignore_user_abort(true); // do not stop when user ended the connection
		
		if ( strpos( @ini_get( 'disable_functions' ), 'set_time_limit' ) === false ) {
			@set_time_limit(60); // try to avoid bad conditions
		}

	// do nothing if called without payload
        if ( ! isset( $_POST['ads'] ) ) {
            die();
        }

        // iterate through ad ids
        echo 1;
        if ( is_array( $_POST['ads'] ) && $_POST['ads'] !== array() ) {
            $util = Advanced_Ads_Tracking_Util::get_instance();

            foreach ( $_POST['ads'] as $_ad_id_attr ) {
                $args = array( 'ad_id' => $_ad_id_attr );
                $util->track_impression( $args );
            };
        }

        // no message intended
        die();
    }
}
