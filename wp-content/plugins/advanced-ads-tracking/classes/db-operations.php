<?php
class Advanced_Ads_Tracking_Dbop {
    /**
     * the unique instance of this class
     */
    private static $instance = null;

	private static $row_count_limit = 100000;

	// hour used for compression
	private static $fixed_hour = 06;
	
	private $compress_periods;

	private $remove_periods;

	private $export_periods;

	private $warnings;

	const MIN_DATE = '2010-02-01';

	const MAX_DATE = '2020-02-01';

	const SIZE_TRANS = 'advads_tracking_oversize_row';

	private function __construct() {
		$this->compress_periods = array(
			'except3months' => __( 'all except last 3 months', 'advanced-ads-tracking' ),
			'except6months' => __( 'all except last 6 months', 'advanced-ads-tracking' ),
		);
		$this->remove_periods = array(
			'beforethisyear' => __( 'everything before this year', 'advanced-ads-tracking' ),
			'first6months' => __( 'first 6 months', 'advanced-ads-tracking' ),
		);

		$this->export_periods = array(
			'last12months' => __( 'last 12 months', 'advanced-ads-tracking' ),
			'lastyear' => __( 'last year', 'advanced-ads-tracking' ),
			'thisyear' => __( 'this year', 'advanced-ads-tracking' ),
			'beforethisyear' => __( 'everything before this year', 'advanced-ads-tracking' ),
			'first6months' => __( 'first 6 months', 'advanced-ads-tracking' ),
		);

		$this->warnings = array(
			'row-oversize' => sprintf( __( 'One of the tracking table has more than %1$s entries. Please go to the <a href="%2$s">database management page</a> to fix it.', 'advanced-ads-tracking' ),
				( self::row_count_limit() )? self::row_count_limit() : 150000,
				esc_url( admin_url( 'admin.php?page=advads-tracking-db-page' ) )
			)
		);

		add_filter( 'advanced-ads-tracking-get-period-bounds', array( $this, 'add_compress_periods_bounds' ), 20, 2 );
		add_filter( 'advanced-ads-tracking-get-period-bounds', array( $this, 'add_remove_periods_bounds' ), 25, 2 );

		// AJAX ACTION
		add_action( 'wp_ajax_advads_tracking_remove', array( $this, 'ajax_remove' ) );
		add_action( 'wp_ajax_advads_tracking_export', array( $this, 'ajax_export' ) );
		add_action( 'wp_ajax_advads_tracking_reset', array( $this, 'ajax_reset' ) );
	}

	public function ajax_reset(){
		if ( false !== wp_verify_nonce( $_POST['nonce'], 'advads_tracking_dbop' ) ) {
			$ad = $_POST['ad'];
			$admin_class = new Advanced_Ads_Tracking_Admin();
			$result = $admin_class->reset_stats( $ad );
			header( 'Content-Type: application/json' );
			echo json_encode( $result );
			die;
		}
		die;
	}
	
	public function admin_notices() {
		$screen = get_current_screen();
		// no need to display the notice. We are aleady in the DB operations page
		if ( 'admin_page_advads-tracking-db-page' == $screen->id ) return;

		if ( get_transient( self::SIZE_TRANS ) ) {
			?><div class="notice error">
				<p><?php echo $this->warnings['row-oversize']; ?></p>
			</div><?php
		}
	}

	public function ajax_remove(){
		if ( false !== wp_verify_nonce( $_POST['nonce'], 'advads_tracking_dbop' ) ) {
			$period = ( $_POST['period'] )? $_POST['period'] : false;
			$result = $this->remove( $period );
			header( 'Content-Type: application/json' );
			echo json_encode( $result );
			die;
		}
		die;
	}

	public function ajax_export() {
		if ( false !== wp_verify_nonce( $_GET['nonce'], 'advads_tracking_dbop' ) ) {
			$period = ( $_GET['period'] )? stripslashes( $_GET['period'] ) : false;
			$from = ( isset( $_GET['from'] ) )? $_GET['from'] : '';
			$to = ( isset( $_GET['to'] ) )? $_GET['to'] : '';
			$data = $this->get_export_data( $period, $from, $to );
			if ( false === $data ) {
				echo 'invalid period';
				die;
			} else {
				$file_name = 'Advanced_Ads_Stats';
				if ( !empty( $data['impressions'] ) ) {
					$first_date = key( $data['impressions'] );
					end( $data['impressions'] );
					$end_date = key( $data['impressions'] );
					reset( $data['impressions'] );
					$file_name .= "_{$first_date}_{$end_date}";
				}
				$file_name .= '.csv';

				ob_start();
				$str = "Date,Ad ID,Impressions,Clicks,Ad title\n";
				foreach ( $data['impressions'] as $date => $impr_block ) {
					foreach( $impr_block as $ID => $impr ) {
						$title = 'deleted';
						$clicks = 0;
						$imprs = 0;
						if ( array_key_exists( $ID, $data['ads'] ) ) {
							$title = $data['ads'][$ID];
							// escape " and , ( RFC 4180 )[http://www.rfc-base.org/rfc-4180.html] - shouldn't be EOL in post title
							if ( false !== strpos( $title, ',' ) || false !== strpos( $title, '"' ) ) {
								$title = str_replace( '",', "'',", $title );
								$title = '"' . $title . '"';
							}
						}
						if ( !empty( $impr_block[$ID] ) ) {
							$imprs = $impr_block[$ID];
						}
						if ( isset( $data['clicks'][$date] ) && !empty( $data['clicks'][$date][$ID] ) ) {
							$clicks = $data['clicks'][$date][$ID];
						}
						$str .= "$date,$ID,$imprs,$clicks,$title\n";
					}
				}
				echo $str;
				header( 'Content-Description:FileTransfer' );
				header( 'Content-Type:text/csv;' );
				header( 'Content-Disposition:attachment;filename="' . $file_name . '"' );
				header( 'Expires:0');
				header( 'Cache-Control:must-revalidate');
				header( 'Pragma:public');
				header( 'Content-Length:' . ( ob_get_length() ) );
				ob_end_flush();
			}

		}
		die;
	}

	public function get_compress_periods() {
		return $this->compress_periods;
	}

	public function get_remove_periods() {
		return $this->remove_periods;
	}

	public function get_export_periods() {
		return $this->export_periods;
	}

	/**
	 *  get the period limit to be used in SQL query from the period name. applies filter for future extensions
	 *
	 *  @return arr [startDate, endDate]
	 */
	public function get_period_bounds( $period_name = 'last12months', $from = '', $to = '' ) {
		$result = array();
		$now = date_create( 'now', Advanced_Ads_Admin::get_wp_timezone() );

		switch( $period_name ) {
			case 'custom':
				if ( false !== strpos( $to, '/' ) ) {
					// from and to are from jQuery-ui DatePicker
					$from = explode( '/', $from );
					$to = explode( '/', $to );
					$result = array(
						$from[2] . '-' . $from[0] . '-' . $from[1],
						$to[2] . '-' . $to[0] . '-' . $to[1],
					);
				} else {
					// already converted
					$result = array( $from, $to );
				}
				break;
			case 'lastyear':
				$last_year = ( string )( intval( $now->format( 'Y' ) ) - 1 );
				$result = array( $last_year . '-01-01', $last_year . '-12-31' );
				break;

			case 'thisyear':
				$result = array( $now->format( 'Y' ) . '-01-01', $now->format( 'Y-m-d' ) );
				break;

			default: // last12months
				$last_month = date_create( $now->format( 'Y-' ) . ( intval( $now->format( 'm' ) ) - 1 ) . '-1' );
				$end_date = date_create( $last_month->format( 'Y-m-t' ) );
				$start_date = date_create( ( intval( $now->format( 'Y' ) ) - 1 ) . $now->format( '-m' ). '-01' );
				$result = array( $start_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-d' ) );
		}

		return apply_filters( 'advanced-ads-tracking-get-period-bounds', $result, $period_name );
	}

	/**
	 *  returns the period bounds for data removal
	 */
	public function add_remove_periods_bounds( $result, $period_name ) {
		switch ( $period_name ) {
			case 'beforethisyear':
				$now = date_create( 'now', Advanced_Ads_Admin::get_wp_timezone() );
				$year = intval( $now->format( 'Y' ) ) - 1;
				$result = array( self::MIN_DATE, $year . '-12-31' );
				break;
			case 'first6months':
				$first_date = date_create( $this->get_first_record_date( 'Y-m-d' ) );
				$year = intval( $first_date->format( 'Y' ) );
				$month = intval( $first_date->format( 'm' ) );
				if ( 12 < $month + 6 ) {
					$month = $month + 6 - 12;
					$year = $year + 1;
				} else {
					$month = $month + 6;
				}
				$end_date = date_create( $year . '-' . $month . '-01' );
				return array( $first_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-t' ) );
				break;
			default:
				// nothing to do, return the result as is
		}
		return $result;
	}

	/**
	 *  get date of first record
	 */
	public function get_first_record_date( $format = false ){
		global $wpdb;
		$impressions_table = $wpdb->prefix . 'advads_impressions';
		$util = Advanced_Ads_Tracking_Util::get_instance();
		$date_format = get_option( 'date_format' );
		$query = "SELECT `timestamp` FROM $impressions_table ORDER BY `timestamp` ASC LIMIT 1";
		$result = $wpdb->get_results( $query );
		if ( $result ) {
			$oldest_impression = $util->get_date_from_db( $result[0]->timestamp, 'Y-m-d' );
			$_oldest_impression = date_create( $oldest_impression, Advanced_Ads_Admin::get_wp_timezone() );
			if ( $format ) {
				if ( is_string( $format ) ) {
					return date_i18n( $format, $_oldest_impression->format( 'U' ) );
				} else {
					return date_i18n( $date_format, $_oldest_impression->format( 'U' ) );
				}
			} else {
				return intval( $_oldest_impression->format( 'U' ) );
			}
		}
		return false;
	}

	/**
	 *  returns the bounds of compression periods
	 */
	public function add_compress_periods_bounds( $result, $period_name ) {
		switch ( $period_name ) {
			case 'except6months':
				$now = date_create( 'now', Advanced_Ads_Admin::get_wp_timezone() );
				$month_minus_6 = intval( $now->format( 'm' ) ) - 6 ;
				$year = $now->format( 'Y' );
				if ( 0 > $month_minus_6 ) {
					$month_minus_6 += 12;
					$year = intval( $year ) - 1;
				}
				$end_date = date_create( $year . '-' . $month_minus_6 . '-01' );
				$result = array( self::MIN_DATE, $end_date->format( 'Y-m-t' ) );
				break;

			case 'except3months':
				$now = date_create( 'now', Advanced_Ads_Admin::get_wp_timezone() );
				$month_minus_3 = intval( $now->format( 'm' ) ) - 3 ;
				$year = $now->format( 'Y' );
				if ( 0 > $month_minus_3 ) {
					$month_minus_3 += 12;
					$year = intval( $year ) - 1;
				}
				$end_date = date_create( $year . '-' . $month_minus_3 . '-01' );
				$result = array( self::MIN_DATE, $end_date->format( 'Y-m-t' ) );
				break;

			default:
				// nothing to do, return the result as is
		}
		return $result;
	}

	/**
	 *  load stats groupped by day for export/compression
	 */
	public function load_stats( $period, $from = '', $to = '' ) {
		$bounds = $this->get_period_bounds( $period, $from, $to );
		$util = Advanced_Ads_Tracking_Util::get_instance();
		$admin = new Advanced_Ads_Tracking_Admin();
		if ( 'custom' == $period ) {
			$bounds = array( $from, $to );
		} else {
			$bounds = $this->get_period_bounds( $period, $from, $to );
		}
        $_ads = Advanced_Ads::get_ads( array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );
		$ads = array();
		foreach ( $_ads as $ad ) {
			$ads[] = (string) $ad->ID;
		}
		// SQL query arguments
		$sql_args = array(
			'period' => 'custom',
			'groupby' => 'day',
			'ad_id' => $ads,
			'groupFormat' => 'Y-m-d',
			'from' => $bounds[0],
			'to' => $bounds[1],
		);

		$imprs = $admin->load_stats( $sql_args, $util->get_impression_table() );
		$clicks = $admin->load_stats( $sql_args, $util->get_click_table() );
		return array( $imprs, $clicks );
	}

	/**
	 *  delete records for the give period
	 */
	private function remove( $period ) {
		if ( !array_key_exists( $period, $this->remove_periods ) ) {
			return array( 'status' => false, 'msg' => 'invalid period', 'value' => $period );
		}
		$util = Advanced_Ads_Tracking_Util::get_instance();
		$click_table = $util->get_click_table();
		$impression_table = $util->get_impression_table();
		$bounds = $this->get_period_bounds( $period );
		$start = explode( '-', $bounds[0] );
		$end = explode( '-', $bounds[1] );
        $gmt_offset = 3600 * floatval( get_option( 'gmt_offset', 0 ) );

		$start_ts = $util->get_timestamp( mktime( 0, 0, 1, intval( $start[1] ), intval( $start[2] ), intval( $start[0] ) ) - $gmt_offset );
		$end_ts = $util->get_timestamp( mktime( 23, 0, 1, intval( $end[1] ), intval( $end[2] ), intval( $end[0] ) ) - $gmt_offset );

		$query = "DELETE $click_table, $impression_table FROM $click_table, $impression_table WHERE $click_table.timestamp BETWEEN $start_ts AND $end_ts AND $impression_table.timestamp BETWEEN $start_ts AND $end_ts";
		global $wpdb;

		$result = $wpdb->query( $query );
		if ( false === $result ) {
			return array( 'status' => false );
		} else {
			// OPTIMIZE to retrieve unused space
			$o1 = "OPTIMIZE TABLE $impression_table";
			$o2 = "OPTIMIZE TABLE $click_table";

			$ro1 = $wpdb->query( $o1 );
			$ro2 = $wpdb->query( $o2 );
			$return = array( 'status' => true );
			if ( false === $ro1 || false === $ro2 ) {
				$return['alt-msg'] = 'optimize-failure';
			}
			return $return;
		}
	}

	/**
	 *  compress data within date range
	 */
	private function compress( $period ) {
		if ( !array_key_exists( $period, $this->compress_periods ) ) {
			return array( 'status' => false, 'msg' => 'invalid period', 'value' => $period );
		}
		$util = Advanced_Ads_Tracking_Util::get_instance();
		$stats = $this->load_stats( $period );
		if ( false === $stats ) {
			return array( 'status' => false, 'msg' => 'invalid period', 'value' => $period );
		}
		list( $imprs, $clicks ) = $stats;

		$first_key = key( $imprs );
		end( $imprs );
		$last_key = key( $imprs );
		reset( $imprs );

		$first_date = date_create( $first_key );
		$last_date = date_create( $last_key . 'T23:30' );

		$first_ts = $util->get_timestamp( $first_date->format( 'U' ) );
		$last_ts = $util->get_timestamp( $last_date->format( 'U' ) );

		$d1 = "DELETE FROM " . $util->get_impression_table() . " WHERE `timestamp` BETWEEN $first_ts AND $last_ts";
		$d2 = "DELETE FROM " . $util->get_click_table() . " WHERE `timestamp` BETWEEN $first_ts AND $last_ts";

		global $wpdb;

		$rd1 = $wpdb->query( $d1 );
		$rd2 = $wpdb->query( $d2 );

		if ( false === $rd1 || false === $rd2 ) {
			return array(
				'status' => false,
				'msg' => 'delete-failure',
			);
		}

		// OPTIMIZE to retrieve unused space
		$o1 = "OPTIMIZE TABLE " . $util->get_impression_table();
		$o2 = "OPTIMIZE TABLE " . $util->get_click_table();

		$ro1 = $wpdb->query( $o1 );
		$ro2 = $wpdb->query( $o2 );

		$alt_msg = '';
		if ( false === $ro1 || false === $ro2 ) {
			$alt_msg .= 'optimize-failure';
		}

		$this->_insert_stats( $imprs, $clicks );

		$result = array(
			'status' => true,
		);

		if ( $alt_msg ) {
			$result['alt-msg'] = $alt_msg;
		}
		return $result;
	}

	/**
	 *  incremental compress
	 */
	public static function incr_compress() {
		/**
		 *  STOP AUTOMATIC COMPRESSION
		 */
		return;
		
		if ( !function_exists( 'microtime' ) ) return;
		if ( defined( 'ADVANCED_ADS_TRACKING_DISABLE_COMPRESSION' ) && true == ADVANCED_ADS_TRACKING_DISABLE_COMPRESSION ) return;
		global $wpdb;
		$util = Advanced_Ads_Tracking_Util::get_instance();
		$impr_table = $util->get_impression_table();
		$click_table = $util->get_click_table();
		
		// max time , need to be less than WP_CRON limit ( 1sec )
		$max_exec_time = 0.750;
		
		// how many rows to compress per step
		$step = 50;
		
		// starting time
		$start = microtime( true );
		
		// last compressed row
		$last_comp = get_option( 'advads-track-autocomp' );
		
		if ( isset( $last_comp['end_select'] ) ) {
			$last_comp = $last_comp['end_select'];
		}
		
		$optimize = false;
		$fa = self::$fixed_hour;
		
		// keep a trace in options for debug/troubleshooting purpose
		$tracing = array(
			'impressions' => array(
				'select' => 0,
				'insert' => 0,
			),
			'clicks' => array(
				'select' => 0,
				'insert' => 0,
			),
			'iterations' => 0,
			'start_select' => 0,
			'end_select' => 0,
			'time' => 0,
			'timecost' => 0, 
			'optimize_failure' => false,
		);
		
		while ( $start + $max_exec_time > microtime( true ) ) {
			if ( false === $last_comp ) {
				$_cq1 = "SELECT * FROM `$click_table` ORDER BY `timestamp` ASC LIMIT $step";
				$_iq1 = "SELECT * FROM `$impr_table` ORDER BY `timestamp` ASC LIMIT $step";
			} else {
				// take the fixed hour in account ( otherwise there might be a SQL duplicate key error )
				$last_comp = absint( $last_comp ) - ( absint( $last_comp ) % Advanced_Ads_Tracking_Util::MOD_HOUR );
				$_cq1 = "SELECT * FROM `$click_table` WHERE `timestamp` > $last_comp ORDER BY `timestamp` ASC LIMIT $step";
				$_iq1 = "SELECT * FROM `$impr_table` WHERE `timestamp` > $last_comp ORDER BY `timestamp` ASC LIMIT $step";
			}
			
			$_cr1 = $wpdb->get_results( $_cq1, ARRAY_A );
			$_ir1 = $wpdb->get_results( $_iq1, ARRAY_A );
			
			if ( $step == count( $_ir1 ) ) {
				/**
				 *  Do not touch the data if there is too few rows, or reaching the
				 *  recents stats ( avoid problems with touching fresh data - cache and co )
				 */
				$new_impr = self::_build_compressed_data( $_ir1 );
				$new_click = self::_build_compressed_data( $_cr1 );
				if ( count( $new_impr ) != $step ) {
					// row count reduced, update DB
					$tracing['impressions']['select'] += $step;
					$tracing['impressions']['insert'] += count( $new_impr );
					if ( 0 === $tracing['start_select'] ) $tracing['start_select'] = $_ir1[0]['timestamp'];
					$tracing['end_select'] = $_ir1[ ( count( $_ir1 ) - 1 ) ]['timestamp'];
					
					self::_reinsert_data( $_ir1[0]['timestamp'], $_ir1[ ( count($_ir1) - 1 ) ]['timestamp'], $new_impr, $impr_table );
				}
				if ( count( $new_click ) != $step ) {
					// row count reduced, update DB
					$tracing['clicks']['select'] += $step;
					$tracing['clicks']['insert'] += count( $new_click );
					self::_reinsert_data( $_ir1[0]['timestamp'], $_ir1[ ( count($_ir1) - 1 ) ]['timestamp'], $new_click, $click_table );
				}
				$last_comp = $_ir1[ ( count( $_ir1 ) - 1 ) ]['timestamp'];
				$tracing['iterations'] += 1;
				$tracing['timecost'] = microtime( true ) - $start . ' sec';
				
			}
		}
		
		/**
		 *  optimize table and update tracing option
		 */
		$tracing['time'] = time();
		if ( $tracing['impressions']['select'] != $tracing['impressions']['insert'] ) {
			$o1 = $wpdb->query( "OPTIMIZE TABLE $impr_table" );
			$o2 = $wpdb->query( "OPTIMIZE TABLE $click_table" );
			if ( !$o1 || !$o2 ) {
				$tracing['optimize_failure'] = true;
			}
			update_option( 'advads-track-autocomp', $tracing );
		}
		return $tracing;
	}
	
	/**
	 *  Re-insert compressed data (incremental)
	 *  
	 *  @param [str] $start, starting timestamp (uncompressed)
	 *  @param [str] $end, ending timestamp (uncompressed)
	 *  @param [arr] $compressed, the compressed data (assoc array with timestamp, ad_id and count as fields)
	 */
	private static function _reinsert_data( $start, $end, $compressed, $table ) {
		global $wpdb;
		$dq = "DELETE FROM $table WHERE `timestamp` BETWEEN $start AND $end";
		$wpdb->query( $dq );
		$wpdb->query( "OPTIMIZE TABLE $table" );
		$i = 0;
		$_qbase = "INSERT INTO `$table` (`timestamp`, `ad_id`, `count`) VALUES (%v%) ON DUPLICATE KEY UPDATE `count` = `count` + %c%";
		
		while ( isset( $compressed[$i] ) ) {
			$count = absint( $compressed[$i]['count'] );
			$ts = absint( $compressed[$i]['timestamp'] );
			$ad_id = absint( $compressed[$i]['ad_id'] );
			
			$query = str_replace( array(
				'%v%',
				'%c%',
			), array(
				"$ts,$ad_id,$count",
				$count,
			), $_qbase );
			
			$wpdb->query( $query );
			
			$i++;
		}
	}
	
	/**
	 *  Build the compressed data ( incremental )
	 *  
	 *  @param [assoc array] $db_rows, DB query returned by $wpdb->get_results( $query, ARRAY_A )
	 *  
	 *  @return [assoc array], the compressed data in the same format as $db_rows
	 */
	private static function _build_compressed_data( $db_rows ) {
		$_result = array();
		foreach ( $db_rows as $row ) {
			// timestamp in array format 
			$da = explode( ' ', chunk_split( $row['timestamp'], 2, ' ' ) );
			
			$count = absint( $row['count'] );
			$ad_id = absint( $row['ad_id'] );
			
			// array index composed by the timestamp with the fixed hour + the ad ID (YmdWd{self::$fixed_hour}-{$ad_id})
			// ( ad_id and timestamp are primary keys in the DB )
			
			$index =  $da[0] . $da[1] . $da[2] . $da[3] . zeroise( self::$fixed_hour, 2 ) . '-' . $ad_id ;
			if ( isset( $_result[$index] ) ) {
				// increment count 
				$_result[$index] += $count;
			} else {
				$_result[$index] = $count;
			}
		}
		$result = array();
		foreach ( $_result as $key => $value ) {
			$expl = explode( '-', $key );
			$result[] = array(
				'timestamp' => $expl[0],
				'ad_id' => $expl[1],
				'count' => $value,
			);
		}
		return $result;
	}
	
	/**
	 *  re-insert stats grouped by day in the DB
	 */
	private function _insert_stats( $imprs, $clicks ) {
		global $wpdb;
		$step = 40;
		$i = 0;
		$j = 0;
		$len = count( $imprs );
		$util = Advanced_Ads_Tracking_Util::get_instance();
		$queryI = 'INSERT INTO `' . $util->get_impression_table() . '` (`timestamp`, `ad_id`, `count`) VALUES';
		$queryC = 'INSERT INTO `' . $util->get_click_table() . '` (`timestamp`, `ad_id`, `count`) VALUES';
		$queryIr = array();
		$queryCr = array();

        $gmt_offset = 3600 * floatval( get_option( 'gmt_offset', 0 ) );

		foreach( $imprs as $_date => $impr ) {
			$ts = date_create( $_date );
			$ts = $ts->format( 'U' );
			$ts = $util->get_timestamp( $ts - $gmt_offset );
			$ts = floor( $ts / 100 ) * 100;
			$ts += self::$fixed_hour;
			if ( $step - 1 <= $i || $len - 1 == $j ) {
				// finalize query and execute
				$queryI .= implode( ',', $queryIr );
				$queryC .= implode( ',', $queryCr );

				$empty_clicks = ( empty( $queryCr ) )? true : false;
				$empty_impressions = ( empty( $queryIr ) )? true : false;
				
				if ( !$empty_impressions ) {
					$wpdb->query( $queryI );
				}
				if ( !$empty_clicks ) {
					$wpdb->query( $queryC );
				}

				$queryIr = array();
				$queryCr = array();
				$queryI = 'INSERT INTO `' . $util->get_impression_table() . '` (`timestamp`, `ad_id`, `count`) VALUES';
				$queryC = 'INSERT INTO `' . $util->get_click_table() . '` (`timestamp`, `ad_id`, `count`) VALUES';
				$i = 0;
			}
			foreach ( $impr as $ad_id => $count ) {
				if ( !empty( $count ) ) {
					$queryIr[] = ' (' . $ts . ', ' . $ad_id . ', ' . $count . ')';
					if ( isset( $clicks[ $_date ] ) && isset( $clicks[ $_date ][ $ad_id ] ) && !empty( $clicks[ $_date ][ $ad_id ] ) ) {
						$queryCr[] = ' (' . $ts . ', ' . $ad_id . ', ' . $clicks[ $_date ][ $ad_id ] . ')';
					}
					$i++;
				}
			}
			$j++;
		}
	}

	/**
	 *  get info about db size
	 *
	 *  @return assoc array
	 */
	public function get_db_size() {

		global $wpdb;
		$clicks_table = $wpdb->prefix . 'advads_clicks';
		$impressions_table = $wpdb->prefix . 'advads_impressions';
		$q1 = "SELECT round(((data_length + index_length) / 1024), 2) AS `size` FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "' AND table_name = '$clicks_table'";
		$q2 = "SELECT round(((data_length + index_length) / 1024), 2) AS `size` FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "' AND table_name = '$impressions_table'";

		$clicks_size_results = $wpdb->get_results( $q1 );
		$impressions_size_results = $wpdb->get_results( $q2 );

		$impression_size = '0';
		$click_size = '0';
		if ( is_array( $impressions_size_results ) && isset( $impressions_size_results[0]->size ) ) {
			$impression_size = $impressions_size_results[0]->size;
		}
		if ( is_array( $clicks_size_results ) && isset( $clicks_size_results[0]->size ) ) {
			$click_size = $clicks_size_results[0]->size;
		}

		$q3 = "SELECT COUNT(*) AS count FROM $clicks_table";
		$q4 = "SELECT COUNT(*) AS count FROM $impressions_table";

		$clicks_count_results = $wpdb->get_results( $q3 );
		$impressions_count_results = $wpdb->get_results( $q4 );

		$clicks_row_count = 0;
		$impressions_row_count = 0;

		if ( $impressions_count_results && isset( $impressions_count_results[0]->count ) && !empty( $impressions_count_results[0]->count ) ) {
			$impressions_row_count = intval( $impressions_count_results[0]->count );
		}
		if ( $clicks_count_results && isset( $clicks_count_results[0]->count ) && !empty( $clicks_count_results[0]->count ) ) {
			$clicks_row_count = intval( $clicks_count_results[0]->count );
		}

		$util = Advanced_Ads_Tracking_Util::get_instance();

		$q5 = "SELECT `timestamp` FROM $clicks_table ORDER BY `timestamp` ASC LIMIT 1";
		$q6 = "SELECT `timestamp` FROM $impressions_table ORDER BY `timestamp` ASC LIMIT 1";

		$oldest_click = null;
		$oldest_impression = null;

		$old_click_result = $wpdb->get_results( $q5 );
		$old_impression_result = $wpdb->get_results( $q6 );
		if ( $old_click_result ) {
			$oldest_click = $util->get_date_from_db( $old_click_result[0]->timestamp, 'Y-m-d' );
			$_oldest_click = date_create( $oldest_click );
			$oldest_click = $_oldest_click->format( 'U' );
		}
		if ( $old_impression_result ) {
			$oldest_impression = $util->get_date_from_db( $old_impression_result[0]->timestamp, 'Y-m-d' );
			$_oldest_impression = date_create( $oldest_impression );
			$oldest_impression = $_oldest_impression->format( 'U' );
		}
		return array(
			'impression_row_count' => $impressions_row_count,
			'click_row_count' => $clicks_row_count,
			'first_impression' => $oldest_impression, // UNIX timestamp | NULL
			'first_click' => $oldest_click, // UNIX timestamp | NULL
			'impression_in_kb' => $impression_size,
			'click_in_kb' => $click_size,
		);
	}

	/**
	 *  check DB info then update a transient if needed
	 *
	 *  @param [assoc arr] $db_size, the result from $this->get_db_size()
	 */
	public function db_size_transient( $db_size ) {
		if ( self::row_count_limit() && ( self::row_count_limit() <= $db_size['impression_row_count'] || self::row_count_limit() <= $db_size['click_row_count'] ) ) {
			set_transient( self::SIZE_TRANS, '1', 6 * WEEK_IN_SECONDS );
		} else {
			delete_transient( self::SIZE_TRANS );
		}
	}

	/**
	 *  get warning message
	 *
	 *  @param [str] $id, the id of the message
	 */
	public function get_warning_message( $id = 'row-oversize' ) {
		$msg = '';
		switch( $id ) {
			default: // 'row-oversize'
				$msg = sprintf(
					__( 'One or more database tables have more than %s rows. It is advised to compress old stats data. You can also export old data into a CSV file and then remove them to reduce the database size. ', 'advanced-ads-tracking' ),
					( self::row_count_limit() )? self::row_count_limit() : 110000
				);
		}
		return $msg;
	}

	/**
	 *  retrieve data to be exported ( ads + stats )
	 */
	private function get_export_data( $period, $from = '', $to = '' ) {
		$bounds = $this->get_period_bounds( $period, $from, $to );
		if ( empty( $bounds ) ) {
			return false;
		}
        $_ads = Advanced_Ads::get_ads( array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );
		$stats = $this->load_stats( $period, $bounds[0], $bounds[1] );

		if ( false === $stats[0] ) {
			return false;
		}
		list( $imprs, $clicks ) = $stats;
		$ads = array();
		foreach ( $_ads as $ad ) {
			$ads[$ad->ID] = $ad->post_title;
		}
		return array(
			'ads' => $ads,
			'impressions' => $imprs,
			'clicks' => $clicks,
		);
	}

	/**
	 *  returns the row count limit
	 */
	public static function row_count_limit() {
		return apply_filters( 'advanced-ads-tracking-row-count-limit', self::$row_count_limit );
	}
	
	/**
	 *  output the period selection inputs for exporting and compressing data
	 */
	public static function period_select_inputs( $args = array() ) {
		$default_args = array(
			'period' => array( '', '' ),
			'from' => array( '', '' ),
			'to' => array( '', '' ),
			'custom' => true,
			'period-options' => array(
				'last12months' => __( 'last 12 months', 'advanced-ads-tracking' ),
				'lastyear' => __( 'last year', 'advanced-ads-tracking' ),
				'thisyear' => __( 'this year', 'advanced-ads-tracking' ),
			),
		);
		$_args = $args + $default_args;
		if ( isset( $args['period-options'] ) && is_array( $args['period-options'] ) ) $_args['period-options'] = $args['period-options'];
		?>
		<span class="advads-period-inputs">
		<select <?php echo ( !empty( $_args['period'][0] ) )? 'id="' . $_args['period'][0] . '"' : ''; ?> class="<?php echo $_args['period'][1]; ?> advads-period">
		<?php foreach( $_args['period-options'] as $value => $readable ) : ?>
		<option value="<?php echo esc_attr( $value ); ?>"><?php echo wp_strip_all_tags( $readable ); ?></option>
		<?php endforeach; ?>
		<?php if ( $_args['custom'] ) : ?>
		<option value="custom"><?php _e( 'custom', 'advanced-ads-tracking' ); ?></option>
		<?php endif; ?>
		</select>
		<input style="display:none;width:auto;" type="text" <?php echo ( $_args['from'][0] )? 'id="' . $_args['from'][0] . '"' : ''; ?> class="<?php echo $_args['from'][1]; ?> advads-from advads-datepicker" value="" size="10" maxlength="10" placeholder="<?php _e( 'from', 'advanced-ads-tracking' ); ?>" />
		<input style="display:none;width:auto;" type="text" <?php echo ( $_args['to'][0] )? 'id="' . $_args['to'][0] . '"' : ''; ?> class="<?php echo $_args['to'][1]; ?> advads-to advads-datepicker" value="" size="10" maxlength="10" placeholder="<?php _e( 'to', 'advanced-ads-tracking' ); ?>" />
		</span>
		<?php
	}

    /**
     * return the unique instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
