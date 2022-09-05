<?php

final class Advanced_Ads_Tracking_Util {

	// mod filter for db format timestamps
	const MOD_HOUR  = 100;
	const MOD_DAY   = 10000;
	const MOD_WEEK  = 1000000;
	const MOD_MONTH = 100000000;
	const MOD_YEAR  = 10000000000;

	const DB_VERSION = '1.4';
	const TABLE_BASENAME = 'advads_impressions';
	const TABLE_CLICKS_BASENAME = 'advads_clicks';
	const SUM_TIMEOUT = 60; // default value for sum timeout in minutes
	const SUM_TRANSIENT = 'advads_tracking_sum'; // name of transient option

	const FIXED_HOUR = '06';

	/**
	 * name of the impressions table
	 */
	protected $impressions_table = '';

	/**
	 * name of the clicks table
	 */
	protected $clicks_table = '';

	/**
	 *
	 * @var Advanced_Ads_Tracking_Plugin
	 */
	protected $plugin;

	/**
	 *  Tracking related data for each blog where ads come from
	 */
	protected $blog_data = array(
		'ajaxurls' => array(),
		'gaUIDs' => array(),
		'gaAnonymIP' => array(),
		'methods' => array(),
		'linkbases' => array(),
		'allads' => array(),
		'parallelTracking' => array(),
	);
	
	/**
	 *
	 * @var Advanced_Ads_Tracking_Util
	 */
	private static $_instance;

	private function __construct() {
	    global $wpdb;

	    // load table names
	    $this->impressions_table =  $wpdb->prefix . self::TABLE_BASENAME;
	    $this->clicks_table =       $wpdb->prefix . self::TABLE_CLICKS_BASENAME;

	}

	/**
	 *  Collect data on blog from which ads have been picked
	 */
	public function collect_blog_data() {
		$bid = get_current_blog_id();
		if ( !isset( $this->blog_data['ajaxurls'][$bid] ) ) {
			$this->blog_data['ajaxurls'][$bid] = admin_url( 'admin-ajax.php' ) . '?action=' . Advanced_Ads_Tracking_Ajax::TRACK_IMPRESSION;
		}
		$options = get_option( $this->plugin->options_slug, array() );
		if ( !isset( $this->blog_data['gaUIDs'][$bid] ) ) {
			$this->blog_data['gaUIDs'][$bid] = isset( $options['ga-UID'] ) ? $options['ga-UID'] : '';
		}
		
		if ( !isset( $this->blog_data['gaAnonymIP'][$bid] ) ) {
			$this->blog_data['gaAnonymIP'][$bid] = isset( $options['ga-anonym-IP'] ) && 'on' == $options['ga-anonym-IP'];
		}
		
		if ( !isset( $this->blog_data['methods'][$bid] ) ) {
			$this->blog_data['methods'][$bid] = !empty( $options['method'] )? $options['method'] : 'onrequest';
		}
		
		$this->blog_data['parallelTracking'][$bid] =  (bool)defined( 'ADVANCED_ADS_TRACKING_FORCE_ANALYTICS' ) && ADVANCED_ADS_TRACKING_FORCE_ANALYTICS;
		
		if ( !isset( $this->blog_data['linkbases'][$bid] ) ) {
            
            $permalink = get_option( 'permalink_structure' );
            $linkbase = isset( $options['linkbase'] ) ? $options['linkbase'] : Advanced_Ads_Tracking::CLICKLINKBASE;
            $base = apply_filters( 'advanced-ads-tracking-click-url-base', $linkbase, false );
            
            if ( empty( $permalink ) ) {
                $linkbase = $base;
			} else {
                $linkbase = home_url( '/' . $base . '/' );
            }
            
			$this->blog_data['linkbases'][$bid] = $linkbase;
            
		}
		if ( !isset( $this->blog_data['allads'][$bid] ) ) {
			$ads = Advanced_Ads::get_ads( array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );
			$all_ads = array();
			foreach ( $ads as $ad ) {
				$ad_object = new Advanced_Ads_Ad( $ad->ID );
				$tracking_plugin = Advanced_Ads_Tracking_Plugin::get_instance();
				if ( $tracking_plugin->check_ad_tracking_enabled( $ad_object ) ) {
					$all_ads[(string)$ad->ID] = array();
					$all_ads[(string)$ad->ID]['title'] = $ad->post_title;
					//Mujahid Code
					$all_ads[(string)$ad->ID]['ad_user'] = get_post_meta($ad->ID, 'ad_user',TRUE);
					$ad_options = $ad_object->options();
					// get url
					if( isset($ad_options['tracking']['link']) && $ad_options['tracking']['link'] != '' ){
						$url = $ad_options['tracking']['link'];
					} elseif( isset($ad_options['url']) && $ad_options['url'] != '' ){
						$url = $ad_options['url'];
					} else {
						$url = false;
					}
					$all_ads[(string)$ad->ID]['target'] = $url? $url : false;
				}
			}
			$this->blog_data['allads'][$bid] = $all_ads;
		}
	}
	
	/**
	 *  Return blog data
	 */
	public function get_blog_data() {
		return $this->blog_data;
	}
	
	/**
	 *
	 * @return Advanced_Ads_Tracking_Util
	 */
	public static function get_instance() {
	    if ( self::$_instance === null ) {
		self::$_instance = new self;
	    }

	    return self::$_instance;
	}

	/**
	 *
	 * @param Advanced_Ads_Tracking_Plugin $plugin
	 */
	public function set_plugin( $plugin ) {
	    $this->plugin = $plugin;

	    $options = $plugin->options();

	}

	// -TODO remove test fragments
	public function createTestData($all_ads, $maxDays = 356, $i = 1000 ) {
	    global $wpdb;

	    $ids = array();
	    $variance = 4;
	    $maxHours = $maxDays * 24 - 1;
	    $runs = 1;
	    $numIds = preg_match_all('/(?<="id":)\d+/ui', json_encode($all_ads), $ids);
	    $numIds = (int) max(2, $numIds / $variance);
	    $ids = $ids[0];
	    $baseTime = time();
	    $baseTime -= $baseTime % 3600;
		$count = 0;
	    for ($y = 0; $y < $runs; $y++) {
			$insert = '';
			$insert_clicks = '';
			for ($n = $i; $n>0; $n--) {
				$ts = $baseTime - 3600 * mt_rand(0, $maxHours / $variance / 10) * mt_rand(1, $variance * 10);
				$ts = $this->get_timestamp( $ts, true );
				$subIds = array_rand($ids, mt_rand(2, $numIds));
				foreach ($subIds as $subId) {
				$subId = $ids[$subId];
				$insert[$ts . '_' . $subId] = "$ts, $subId, " . ((mt_rand(0, 10) + mt_rand(0, 10)) * mt_rand(1, 5));
				$insert_clicks[$ts . '_' . $subId] = "$ts, $subId, " . ((mt_rand(0, 2) + mt_rand(0, 3)) * mt_rand(1, 3));
				}
			}
			$wpdb->query("INSERT INTO $this->impressions_table (timestamp, ad_id, count) VALUES (" . implode('), (', $insert) . ") ON DUPLICATE KEY UPDATE count=(count + VALUES(count)) / 2");
			$wpdb->query("INSERT INTO $this->clicks_table (timestamp, ad_id, count) VALUES (" . implode('), (', $insert_clicks) . ") ON DUPLICATE KEY UPDATE count=(count + VALUES(count)) / 2");
	    }
	}

	public function get_impression_table() {
	    return $this->impressions_table;
	}

	public function get_click_table() {
	    return $this->clicks_table;
	}

	/**
	 * resets stats for ads
	 *
	 * @param str/int $ad_id ad id or string "all-ads"
	 * @return string $error message
	 */
	public function reset_stats($ad_id = 0) {
	    global $wpdb;
	    if ( ! $ad_id ) {
		return;
	    }

	    // reset the whole table if all stats should be removed
	    if ( $ad_id === 'all-ads' ){
		$wpdb->query( 'TRUNCATE TABLE ' . $this->impressions_table );
		$wpdb->query( 'TRUNCATE TABLE ' . $this->clicks_table );
		return true;
	    };

	    // reset stats for individual ad
	    $ad_id = (int) $ad_id;
	    if($ad_id > 0){
		// remove impressions
		$query = $wpdb->prepare(
		    "DELETE FROM $this->impressions_table WHERE ad_id = %d",
		    $ad_id
		);
		$affected_rows = $wpdb->query($query);
		// remove clicks
		$query = $wpdb->prepare(
		    "DELETE FROM $this->clicks_table WHERE ad_id = %d",
		    $ad_id
		);
		$affected_rows += $wpdb->query($query);
		return $affected_rows;
	    }
	}

	/**
	 * Get the 'timestamp' (db format).
	 *
	 * @since 1.0.0
	 * @param integer $timestamp reference time (default: now; server time)
	 * @param boolean $fixed, whether to return a fixed hour (on stat per day per ad)
	 *
	 * @return integer  db formated timestamp in wordpress local time
	 */
	public function get_timestamp( $timestamp = null, $fixed = false ) {
	    if ( ! isset( $timestamp ) || empty( $timestamp ) ) {
			$timestamp = time();
	    }

	    // -TODO using bitmap would be more efficient for database
	    // .. format using 5 6bit might be most useful for fast operations
	    $ts = gmdate( 'Y-m-d H:i:s', (int) $timestamp ); // time in UTC

        $week = absint( get_date_from_gmt( $ts, 'W' ) );
        $month = absint( get_date_from_gmt( $ts, 'm' ) );

        if ( 52 <= $week && 1 == $month ) {
            /**
             *  Fix for the new year inconsistency
             */
            $ts = get_date_from_gmt( $ts, 'ym01dH' );
        } elseif ( 12 === $month && in_array( $week, array( 1, 53 ) ) ) {
            $ts = get_date_from_gmt( $ts, 'ym52dH' );
        } else {
            $ts = get_date_from_gmt( $ts, 'ymWdH' ); // ensure wp local time
        }

		if ( $fixed ) {
			$ts = substr( $ts, 0, strlen( $ts ) - 2 );
			$ts .= self::FIXED_HOUR;
		}

	    return $ts;
	}

	/**
	 * Format original date as stored in db for display.
	 *
	 * @param integer $db_time db format time
	 * @param string  $format  date format
	 *
	 * @return string
	 */
	public function get_date_from_db( $db_time, $format ) {
	    $date = array_combine( array( 'year', 'month', 'week', 'day', 'hour' ), str_split( $db_time, 2 ) );
	    // -TODO since month and day have special meaning when `0` this was hot-fixed
	    $time = mktime( (int) $date['hour'], 0, 0, max( $date['month'], 1 ), max( $date['day'], 1 ), (int) $date['year'] );

	    return date( $format, $time );
	}

	/**
	 * add impression to database
	 *
	 * @since 1.0.0
	 */
	public function track_impression($args = array()){
	    $ad_id = isset($args['ad_id']) ? (int) $args['ad_id'] : 0;

	    $tracking_options = $this->plugin->options();
	    $is_bot = Advanced_Ads::get_instance()->is_bot();
	    $is_cache_bot = $this->is_cache_bot();

	    /**
	     * do not track click for bots if the options is not active.
	     * never track cache bots though
	     *
	     * @todo remove optional bot tracking unless we find a good reason that activity by some bots should be tracked
	     */

	    if ( $is_cache_bot
		    || $this->plugin->ignore_logged_in_user()
		    || ( $is_bot && ! isset( $tracking_options['track-bots'] ) ) ) {
			return;
	    }

	    $this->persist( $ad_id, $this->impressions_table );

	    // check if the ad is already in the sum array and if not, remove the array to recreate it on next page load
	    $sums = Advanced_Ads_Tracking_Util::get_instance()->get_sums();
	    if( !isset( $sums['impressions'][ $ad_id ] ) ){
		    self::delete_sums_transient();
	    }
	}

	/**
	 * add click to database
	 *
	 * @since 1.1.0
	 */
	public function track_click($args = array()){
	    $ad_id = isset($args['ad_id']) ? (int) $args['ad_id'] : 0;

	    $tracking_options = $this->plugin->options();
	    $is_bot = Advanced_Ads::get_instance()->is_bot();
	    $is_cache_bot = $this->is_cache_bot();

	    /**
	     * do not track click for bots if the options is not active.
	     * never track cache bots though
	     *
	     * @todo remove optional bot tracking unless we find a good reason that activity by some bots should be tracked
	     */
	    if ( $is_cache_bot
		    || $this->plugin->ignore_logged_in_user()
		    || ( $is_bot && ! isset( $tracking_options['track-bots'] ) ) ) {
			return;
	    }
	    $this->persist($ad_id, $this->clicks_table);
	}

	protected function persist($id, $table) {
	    global $wpdb;
	    $timestamp = $this->get_timestamp( null, true );
	    
	    /**
	     * allow to disable tracking something into the database
	     */
	    $do_tracking = apply_filters( 'advanced-ads-tracking-do-tracking', true, $id, $table );
	    if( ! $do_tracking ){
		    return;
	    }

	    $success = $wpdb->query( "INSERT INTO $table (`ad_id`, `timestamp`, `count`) VALUES ($id, $timestamp, 1) ON DUPLICATE KEY UPDATE `count` = `count`+ 1" );

	    /**
	     * add custom logging if ADVANCED_ADS_TRACKING_DEBUG is enabled
	     * writes events into wp-content/advanced-ads-tracking.csv
	     */
	    if( defined( 'ADVANCED_ADS_TRACKING_DEBUG')
		    &&  ( true === ADVANCED_ADS_TRACKING_DEBUG
		    || $id === ADVANCED_ADS_TRACKING_DEBUG ) ){
		    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		    
		    // if this is AJAX-tracked, get the post ID instead of the request UI
		    $url = '';
		    if( isset( $_POST['deferedAds'][0]['ad_args'] ) ){
			    $args = json_decode( stripslashes($_POST['deferedAds'][0]['ad_args']));
			    if( isset( $args->url_parameter ) ){
				    $url = $args->url_parameter . ' via ajax';
			    }
		    }
		    $url = ( empty( $url ) && isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : $url;
		    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		    $log_content = date_i18n( 'Y-m-d H:i:s' ) . ";$table;$id;$ip;$url;\"$user_agent\""  . "\n";
		    error_log( $log_content, 3, WP_CONTENT_DIR . '/advanced-ads-tracking.csv' );
	    }

	    /**
		 * allow to perform your own action when tracking was performed locally
		 *
		 * @param   $id     ad ID
		 * @param   $table  name of the table, normally {prefix_}advads_impressions or {prefix_}advads_clicks
		 * @param   $timestamp
		 * @param   $success    true if written into the db
		 */
		do_action( 'advanced-ads-tracking-after-writing-into-db', $id, $table, $timestamp, $success );

		$limiter = new Advanced_Ads_Tracking_Limiter( $id );
		$type = 'clicks';
		if ( $this->impressions_table == $table ) {
			$type = 'impressions';
		}
		$limiter->track( 1, $type );
	}

	/**
	 * check if the current user is a bot prepopulating the cache
	 *	currently, only WP Rocket is supported
	 *	ads should be loaded for the bot, because they should show up on the cached site
	 *	but impressions and clicks should never be tracked then
	 */
	protected function is_cache_bot(){

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			// WP Rocket
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'wprocketbot' ) ) {
				return true;
			}

			// WP Super Cache.
			$wp_useragent = apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) );
			if ( $wp_useragent === $_SERVER['HTTP_USER_AGENT'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * load sums of impressions and clicks
	 *
	 * @since 1.2.6
	 * @return arr $sums array with impressions and clicks by ad id
	 */
	public function get_sums(){
		global $wpdb;
		$sums = array(
			'impressions'   => array(),
			'clicks'	    => array()
		);

		$transient_sum = get_transient( self::SUM_TRANSIENT );;
		// if there is an option already saved then use it
		if( $transient_sum ){
			$sums = $transient_sum;
		} else {
			// else, query the value and save it

			// check for the presence of table
			global $wpdb;
			$query = "SHOW TABLES LIKE '$this->impressions_table'";
			$result = $wpdb->query( $query );
			if ( !$result ) {
				return;
			}
			$query = "SELECT SQL_NO_CACHE `ad_id`, SUM(`count`) as `impressions` FROM  $this->impressions_table GROUP BY `ad_id`";
			$numRows = $wpdb->query( $query );

			if ($numRows > 0) {
				$rows = $wpdb->last_result;
				foreach ($rows as $row) {
					$sums['impressions'][$row->ad_id] = $row->impressions;
				}
			}

			$query = "SELECT SQL_NO_CACHE `ad_id`, SUM(`count`) as `clicks` FROM  $this->clicks_table GROUP BY `ad_id`";
			$numRows = $wpdb->query( $query );

			if ($numRows > 0) {
				$rows = $wpdb->last_result;
				foreach ($rows as $row) {
					$sums['clicks'][$row->ad_id] = $row->clicks;
				}
			}

			// save as a transient
			$options = $this->plugin->options();
			$timeout = isset($options['sum-timeout']) ? absint( $options['sum-timeout'] ) : self::SUM_TIMEOUT;
			$timeout_s = 60 * $timeout; // timeout in seconds
			// only save transient if timeout is not empty
			if( $timeout_s ){
				set_transient( self::SUM_TRANSIENT, $sums, $timeout_s );
			}
		}

		return $sums;
	}

	/**
	 * delete the sums transient
	 */
	public static function delete_sums_transient(){
		delete_transient( self::SUM_TRANSIENT );
	}

    /**
     *  draw the email content of ads reports
     */
    public function get_email_report_content( $report_args = array() ) {
		$period = isset( $report_args['period'] ) ? $report_args['period'] : '';
		$ad_id = isset( $report_args['ads'] ) ? $report_args['ads'] : 'all';

		if ( 'all' !== $ad_id ) {
			$ad_id = absint( $ad_id );
		}

        $valid_period = array( 'last30days', 'last12months', 'lastmonth' );
        if ( ! in_array( $period, $valid_period ) ) $period = 'last30days';

        $textual_period = array(
            'last30days' => __( ' the last 30 days', 'advanced-ads-tracking' ),
            'lastmonth' => __( ' the last month', 'advanced-ads-tracking' ),
            'last12months' => __( ' the last 12 months', 'advanced-ads-tracking' ),
        );

        $admin_class = new Advanced_Ads_Tracking_Admin();

		$plugin_options = $this->plugin->options();
		$public_stats_slug = isset( $plugin_options['public-stats-slug'] )? $plugin_options['public-stats-slug'] : Advanced_Ads_Tracking_Admin::PUBLIC_STATS_DEFAULT;

		$wptz = Advanced_Ads_Admin::get_wp_timezone();
        $today = date_create( 'now', $wptz );
        $args = array(
            'ad_id' => array(),
            'period' => 'lastmonth',
            'groupby' => 'day',
            'groupFormat' => 'Y-m-d',
            'from' => null,
            'to' => null,
        );

        if ( 'last30days' == $period ) {

            $start_ts = intval( $today->format( 'U' ) );
            $start_ts = $start_ts - ( 30 * 24 * 60 * 60 );

            $start = date_create( '@' . $start_ts, new DateTimeZone( 'UTC' ) );

            $args['period'] = 'custom';
            $args['from'] = $start->format( 'm/d/Y' );

            $end_ts = intval( $today->format( 'U' ) );
            $end = date_create( '@' . $end_ts, new DateTimeZone( 'UTC' ) );

            $args['to'] = $end->format( 'm/d/Y' );
        }

        if ( 'last12months' == $period ) {

            // $start = $start->sub( new DateInterval( 'P12M' ) );
            $start_ts = intval( $today->format( 'U' ) );
            $start_ts = $start_ts - ( 365 * 24 * 60 * 60 );

            $start = date_create( '@' . $start_ts, new DateTimeZone( 'UTC' ) );

            $args['period'] = 'custom';
            $args['groupby'] = 'month';
            $args['from'] = $start->format( 'm/' ) . '1' . $start->format( '/Y' );

            // fix potential time zone gap

            // $today->add( new DateInterval( 'P1D' ) );
            $end_ts = intval( $today->format( 'U' ) );
            $end_ts = $end_ts + ( 24 * 60 * 60 );
            $end = date_create( '@' . $end_ts, new DateTimeZone( 'UTC' ) );

            $args['to'] = $end->format( 'm/d/Y' );

        }

        $impr_stats = $admin_class->load_stats( $args, $this->impressions_table );
        $clicks_stats = $admin_class->load_stats( $args, $this->clicks_table );

		$ad_name = false;
		$public_stats = false;

		/**
		 *  filter ad ids to allow correct display if no stats for the corresponding ad
		 */
		if ( 'all' !== $ad_id ) {
			$__imprs = array();
			$__clicks = array();
			foreach ( $impr_stats as $date => $impression ) {
				$key = (string)$ad_id;
				if ( array_key_exists( $key, $impression ) ) {
					$__imprs[$date] = array( $key => $impression[$key] );
				} else {
					$__imprs[$date] = array( $key => 0 );
				}
				if ( isset( $clicks_stats[$date] ) ) {
					if ( array_key_exists( $key, $clicks_stats[$date] ) ) {
						$__clicks[$date] = array( $key => absint( $clicks_stats[$date][$key] ) );
					} else {
						$__clicks[$date] = array( $key => 0 );
					}
				} else {
					$__clicks[$date] = array( $key => 0 );
				}
			}
			$impr_stats = $__imprs;
			$clicks_stats = $__clicks;
			$the_ad = new Advanced_Ads_Ad( $ad_id );
			$ad_options = $the_ad->options();

			$ad_name = ( isset( $ad_options['tracking']['public-name'] ) && !empty( $ad_options['tracking']['public-name'] ) )? $ad_options['tracking']['public-name'] : $the_ad->title;

			$public_stats = site_url( '/' . $public_stats_slug . '/' . $ad_options['tracking']['public-id'] . '/' );
		}

        $cell_style = 'style="padding: 0.6em;text-align:right;border:1px solid;"';
        $header_style = 'style="padding: 0.8em;text-align:center;font-size:1.1em;font-weight:bold;"';

		$impr_sum = 0;
		$click_sum = 0;
        ob_start();

        ?>
        <div style="margin-top:0.4em;margin-bottom:0.4em;margin-right:auto;margin-left:auto;position:relative;width:420px;overflow:visible;">
            <h3 style="font-size:1.3em;"><?php echo bloginfo( 'name' ); ?></h3>
			<?php if ( $ad_name ) : ?>
            <h4 style="font-size:1.2em;"><?php printf( __( '%s statistics for %s', 'advanced-ads-tracking' ), '<strong><em>' . $ad_name . '</em></strong>', $textual_period[ $period ] ); ?></h4>
			<?php else : ?>
            <h4 style="font-size:1.2em;"><?php printf( __( 'Ads statistics for %s', 'advanced-ads-tracking' ), $textual_period[ $period ] ); ?></h4>
			<?php endif; ?>
	    <?php do_action( 'advanced-ads-tracking-email-report-below-headline' ); ?>
            <?php if ( ! $impr_stats ) : // no impression stats found ?>
            <p style="font-size:1.1em;"><em><?php _e( 'There is no data for the given period, yet.', 'advanced-ads-tracking' ); ?></em></p>
            <?php else : // there are some stats ?>
            <table style="border:1px solid;border-collapse:collapse;">
            <thead>
                <th <?php echo $header_style; ?>><?php _e( 'date', 'advanced-ads-tracking' ); ?></th>
                <th <?php echo $header_style; ?>><?php _e( 'impressions', 'advanced-ads-tracking' ); ?></th>
                <th <?php echo $header_style; ?>><?php _e( 'clicks', 'advanced-ads-tracking' ); ?></th>
                <th <?php echo $header_style; ?>>
                    <span title="<?php echo esc_attr( __( 'click through rate', 'advanced-ads-tracking' ) ); ?>" style="cursor:help;"><?php _e( 'CTR', 'advanced-ads-tracking' ); ?></span>
                </th>
            </thead>
            <tbody>
            <?php $impr_stats = array_reverse( $impr_stats ); ?>
			<?php foreach( $impr_stats as $date => $impr ) : ?>
            <?php
            $total_impr = ( is_array( $impr ) )? array_sum( $impr ) : 0;
            $total_clicks = ( isset( $clicks_stats[$date] ) && is_array( $clicks_stats[ $date ] ) )? array_sum( $clicks_stats[ $date ] ) : 0;
            $ctr = ( 0 != $total_impr )? number_format( 100 * $total_clicks / $total_impr, 2 ) . '%' : '0.00%';
            /**
             *  Avoid sending the partial stats (if any at the moment the email is sent) for the current day for the "last 30 days".
             */
            if ( 'last30days' == $period && $date == $today->format( 'Y-m-d' ) ) {
                continue;
            }
			/**
			 *  Avoid printing the 13th month (the current month) for last 12 months
			 */
			if ( 'last12months' == $period && $date == $today->format( 'Y-m-01' ) ) {
				continue;
			}
			$impr_sum += $total_impr;
			$click_sum += $total_clicks
            ?>
            <tr>
                <td <?php echo $cell_style; ?>>
                <?php
                    if ( 'last12months' == $period ) {
                        echo date_i18n( 'F Y', strtotime( $date ) );
                    } else {
                        echo date_i18n( get_option( 'date_format' ), strtotime( $date ) );
                    }
                ?>
                </td>
                <td <?php echo $cell_style; ?>><?php echo $total_impr; ?></td>
                <td <?php echo $cell_style; ?>><?php echo $total_clicks; ?></td>
                <td <?php echo $cell_style; ?>><?php echo $ctr; ?></td>
            </tr>
            <?php endforeach; ?>
			<tr style="font-weight:600;">
				<td <?php echo $cell_style ?>><?php _e( 'Total', 'advanced-ads-tracking' ); ?></td>
				<td <?php echo $cell_style ?>><?php echo $impr_sum; ?></td>
				<td <?php echo $cell_style ?>><?php echo $click_sum; ?></td>
				<td <?php echo $cell_style ?>><?php echo ( 0 == $click_sum )? '0.00 %' : number_format( 100 * $click_sum / $impr_sum, 2 ) . ' %'; ?></td>
			</tr>
            </tbody>
            </table>
			<?php if ( $ad_name ) :?>
			<p><a href="<?php echo esc_url( $public_stats ); ?>" target="_blank" style="font-size:1.1em;color:#1fa1d0;text-decoration:none;font-weight:bold;"><?php _e( 'View the live statistics', 'advanced-ads-tracking' ); ?></a></p>
			<?php endif; ?>
            <?php endif;
	    do_action( 'advanced-ads-tracking-email-report-below-content' );
        ?></div><?php
        return ob_get_clean();
    }

	/**
	 *  Retrieve ad ids, period, frequency and report recipient for all ads
	 */
	public function get_ad_reports_params() {
		global $wpdb;
		$metatable = $wpdb->prefix . 'postmeta';
		$posttable = $wpdb->prefix . 'posts';
		$query = "SELECT $posttable.ID FROM $posttable INNER JOIN $metatable ON $posttable.ID = $metatable.post_id WHERE $posttable.post_type = 'advanced_ads' AND $metatable.meta_value LIKE '%report-frequency%'";
		$results = $wpdb->get_results( $query, ARRAY_A );

		// the final result
		$params = array();

		$period_names = array(
			'last30days' => __( 'last 30 days', 'advanced-ads-tracking' ),
			'lastmonth' => __( 'the last month', 'advanced-ads-tracking' ),
			'last12months' => __( 'last 12 months', 'advanced-ads-tracking' ),
		);

		foreach( $results as $row ) {
			$the_ad = new Advanced_Ads_Ad( absint( $row['ID'] ) );
			if ( 'publish' != $the_ad->status ) {
				continue;
			}
			$options = $the_ad->options();
			if ( isset( $options['tracking']['report-frequency'] ) && 'never' != $options['tracking']['report-frequency'] ) {
				$params[ $the_ad->id ] = array(
					'id' => $the_ad->id,
					'frequency' => $options['tracking']['report-frequency'],
					'period' => $options['tracking']['report-period'],
					'recip' => $options['tracking']['report-recip'],
					'title' => get_the_title( $the_ad->id ),
					'period-literal' => $period_names[ $options['tracking']['report-period'] ],
				);
			}
		}
		return $params;
	}

	/**
	 *  send individual ad report
	 */
	public function send_individual_ad_report( $params = array() ) {

		if (
			!isset( $params['subject'] ) ||
			!isset( $params['to'] ) ||
			!isset( $params['id'] ) ||
			!isset( $params['period'] )
		) {
			return false;
		}

		$bcc = explode( ',', $params['to'] );
		$to = array_shift( $bcc );
		
		$options = $this->plugin->options();
		$sender = isset( $options['email-sender-name'] )? $options['email-sender-name'] : 'Advanced Ads';
		$from = isset( $options['email-sender-address'] )? $options['email-sender-address'] : 'noreply@' . $_SERVER['SERVER_NAME'];

		$headers = array(
		    'Content-Type: text/html; charset=UTF-8',
		    'From: ' . $sender . ' <' . $from . '>',
		);
		if ( !empty( $bcc ) ) {
			$headers[] = 'Bcc: ' . implode( ',', $bcc );
		}
		
		ob_start();

		$content = $this->get_email_report_content( array( 'period' => $params['period'], 'ads' => $params['id'] ) );

		$result = wp_mail( $to, $params['subject'], $content, $headers );
		$error = ob_get_clean();

		return array(
		    'status' => $result,
		    'error' => $error,
		);

	}

    /**
     *  send ads reports to admin email
     */
    public function send_email_report() {

        $options = $this->plugin->options();
        if ( empty( $options['email-addresses'] ) ) return false;
        $period = $options['email-stats-period'];
        $content = $this->get_email_report_content( array( 'period' => $period ) );
        if ( !$content ) return false;
		
		$bcc = explode( ',', $options['email-addresses'] );
		
        $to = array_shift( $bcc );
		
        $subject = $options['email-subject'];
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $options['email-sender-name'] . ' <' . $options['email-sender-address'] . '>',
        );
		
		if ( !empty( $bcc ) ) {
			$headers[] = 'Bcc: ' . implode( ',', $bcc );
		}
		
        ob_start();
        $result = wp_mail( $to, $subject, $content, $headers );
        $error = ob_get_clean();

        return array(
            'status' => $result,
            'error' => $error,
        );

    }

	/**
	 *  parse CSV stats file ( compatible PHP < 5.3 )
	 */
	public function parse_CSV( $id ) {
		$file = get_attached_file( $id );
		$result = array(
			'impressions' => array(),
			'clicks' => array(),
			'ads' => array(),
			'status' => true,
		);
		WP_Filesystem();
		global $wp_filesystem;
		$data = $wp_filesystem->get_contents( $file );
		if ( ! $data ) {
			// ureadable file
			return array(
				'status' => false,
				'msg', __( 'unable to read file', 'advanced-ads-tracking' ),
			);
		}
		// remove evntual BOM
		$bom = pack( 'H*','EFBBBF' );
		$data = preg_replace( "/^$bom/", '', $data );

		$lines = explode( "\n", $data );

		$lines = array_slice( $lines, 1 );
		foreach( $lines as $line ) {
			if ( empty( $line ) ) continue;
			$cells = array();
			$_cells = explode( ',', $line );

			if ( 5 < count( $_cells ) ) {
				// some extra commas are present in the ad title
				$pad = 0;
				for ( $i = 0; $i < count( $_cells ); ++$i ) {
					if ( $i < 4 ) {
						$cells[] = $_cells[$i];
					} else {
						$_title = array_slice( $_cells, 4 );
						$cells[] = implode( ',', $_title );
						break;
					}
				}
			} else {
				// no extra commas
				$cells = $_cells;
			}
			// remove enclosing quotes / trim only outermost quotes
			// if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
				// $_trim = function( $elem ) {
					// if ( "\"" == $elem[0] && "\"" == $elem[ strlen( $elem )-1 ] ) {
						// return substr( $elem,1,strlen( $elem ) - 2 );
					// } else {
						// return $elem;
					// }
				// };
			// } else {
				// $_trim = create_function( '$elem', 'if ("\""==$elem[0]&&"\""==$elem[strlen($elem)-1]){return substr($elem,1,strlen($elem)-2);}else{return $elem;}' );
			// }
			
			// $cells = array_map( $_trim, $cells );
			$cells = array_map( array( 'Advanced_Ads_Tracking_Util', 'trim_outer_quotes' ), $cells );
			$ts = intval( str_replace( '-', '', $cells[0] ) );

			// impressions
			if ( !isset( $result['impressions'][$ts] ) ) {
				$result['impressions'][$ts] = array();
			}
			$result['impressions'][$ts][$cells[1]] = absint( $cells[2] );

			// clicks
			if ( !isset( $result['clicks'][$ts] ) ) {
				$result['clicks'][$ts] = array();
			}
			$result['clicks'][$ts][$cells[1]] = absint( $cells[3] );

			// ad title
			if ( !isset( $result['ads'][$cells[1]] ) ) {
				$result['ads'][$cells[1]] = $cells[4];
			}
		}

		$firstdate = key( $result['impressions'] );
		end( $result['impressions'] );
		$lastdate = key( $result['impressions'] );
		reset( $result['impressions'] );
		$result['firstdate'] = substr( $firstdate, 0, 4 ) . '-' . substr( $firstdate, 4, 2 ) . '-' . substr( $firstdate, 6, 2);
		$result['lastdate'] = substr( $lastdate, 0, 4 ) . '-' . substr( $lastdate, 4, 2 ) . '-' . substr( $lastdate, 6, 2);
		return $result;
	}

	public static function trim_outer_quotes( $elem ) {
		if ( "\"" == $elem[0] && "\"" == $elem[ strlen( $elem )-1 ] ) {
			return substr( $elem,1,strlen( $elem ) - 2 );
		} else {
			return $elem;
		}
	}
	
	/**
	 * get the target link
	 *
	 * @param   obj|int	$ad    ID of the ad or the ad object
	 * @return  str|bol	link if given or false if empty
	 */
	static function get_link( $ad ){

		if( ! $ad instanceof Advanced_Ads_Ad ){
			$ad = new Advanced_Ads_Ad( $ad );
		}

		$options = $ad->options();
		$ad_options = isset( $options['tracking'] ) ? $options['tracking'] : array();

		// get url
		if( isset($ad_options['link']) && $ad_options['link'] != '' ){
			return $ad_options['link'];
		} elseif( isset($options['url']) && $options['url'] != '' ){
			return $options['url'];
		} else {
			return false;
		}
	}

	/**
	 * get the target attribute for the link, e.g. ` target="_blank"`
	 *
	 * @param   obj|int	$ad    ID of the ad or the ad object
	 * @return  str		whole target attibute with value
	 */
	static function get_target( $ad ){

		$ad_options = $ad->options();
		$options = Advanced_Ads_Tracking_Plugin::get_instance()->options();
		$general_options = Advanced_Ads::get_instance()->options();

		/**
		 * second line is needed for backward compatibility with Tracking 1.7.2
		 * and below when the general target-blank option was still in this add-on and not in basic
		 */
		$general_target_blank = ( ( isset( $general_options['target-blank'] ) && '1' == $general_options['target-blank'] )
		    || ( isset( $options['target'] ) && '1' == $options['target'] ) ) ? true : false;
		if (
		       ( $general_target_blank && ( !isset( $ad_options['tracking']['target'] ) || 'same' != $ad_options['tracking']['target'] ) )
		       || ( isset( $ad_options['tracking']['target'] ) && 'new' == $ad_options['tracking']['target'] )
                ) {
			return ' target="_blank"';
		}
		return '';
	}
}
