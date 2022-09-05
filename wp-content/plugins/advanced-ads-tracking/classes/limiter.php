<?php
class Advanced_Ads_Tracking_Limiter
{
	// meta key for internal records
	const metakey = 'advanced_ads_limiter';
	
	// ad ID
	public $id = 0;
	
	// is click tracking allowed for the ad type
	private $use_clicks = true;
	
	// limit value
	private $limit = array( 'impressions' => 0, 'clicks' => 0 );
	
	// sums from the Util class
	private $sum = array( 'impressions' => 0, 'clicks' => 0 );
	
	// ad options
	private $options = null;
	
	// internal tracking record
	private $records = array();
	
	public function __construct( $ad_id ) {
		$this->id = absint( $ad_id );
		$options = get_post_meta( absint( $ad_id ), Advanced_Ads_Ad::$options_meta_field, true );
		$type = isset( $options['type'] ) ? $options['type'] : '';
		
		$this->use_clicks = in_array( $type, Advanced_Ads_Tracking_Plugin::$types_using_click_tracking );
		
		if ( !empty( $options ) ) {
			$this->options = $options;
			if ( !empty( $this->options['tracking']['impression_limit'] ) ) {
				$this->limit['impressions'] = $this->options['tracking']['impression_limit'];
			}
			if ( $this->use_clicks ) {
				if ( !empty( $this->options['tracking']['click_limit'] ) ) {
					$this->limit['clicks'] = $this->options['tracking']['click_limit'];
				}
			}
		}
		if ( !$this->is_empty() ) {
			$sums = Advanced_Ads_Tracking_Util::get_instance()->get_sums();
			$this->sum = array(
				'impressions' => isset( $sums['impressions'][$this->id] )? absint( $sums['impressions'][$this->id] ) : 0,
			);
			if ( $this->use_clicks ) {
				$this->sum['clicks'] = isset( $sums['clicks'][$this->id] )? absint( $sums['clicks'][$this->id] ) : 0;
			}
		}
		$records = get_post_meta( $this->id, self::metakey, true );
		if ( !empty( $records ) ) {
			$this->records = $records;
			if ( !isset( $records['options'] ) || !isset( $records['options']['expiry_date'] ) || !isset( $records['options']['impression_limit'] ) || ( $this->use_clicks && !isset( $records['options']['click_limit'] ) ) ) {
				$this->get_hourly_pace( true );
			} else {
				if ( $this->limit['impressions'] != $recors['options']['impression_limit'] || ( $this->use_clicks && $this->limit['clicks'] != $recors['options']['click_limit'] ) || $this->options['expiry_date'] != $recors['options']['expiry_date'] ) {
					// one of the limits or the expiry date has changed, recalculate the pace and update the postmeta
					$this->get_hourly_pace( true );
				}
			}
		}
	}
	
	/**
	 *  check if a limit is set
	 */
	public function is_empty() {
		if ( $this->use_clicks ) {
			return 0 == $this->limit['impressions'] && 0 == $this->limit['clicks'];
		} else {
			return 0 == $this->limit['impressions'];
		}
	}
	
	/**
	 *  get the hourly limit
	 */
	public function get_hourly_pace( $update = false ) {
		if ( empty( $this->options['expiry_date'] ) || time() > absint( $this->options['expiry_date'] ) || $this->is_empty() ) {
			/**
			 *  no expiry date, expired ad or no limitations
			 */
			return false;
		}
		$type = $this->options['type'];
		$using_clicks = Advanced_Ads_Tracking_Plugin::$types_using_click_tracking;
		
		$pace = array();
		if ( $this->limit['impressions'] ) {
			$pace['impressions'] = ceil( ( $this->limit['impressions'] - $this->sum['impressions'] ) / ceil( ( $this->options['expiry_date'] - time() ) / 3600 ) );
		}
		if ( $this->use_clicks && $this->limit['clicks'] ) {
			$pace['clicks'] = ceil( ( $this->limit['clicks'] - $this->sum['clicks'] ) / ceil( ( $this->options['expiry_date'] - time() ) / 3600 ) );
		}
		if ( !isset( $this->records['pace'] ) || true === $update ) {
			// set new pace and update metadata
			$this->records['pace'] = $pace;
			update_post_meta( $this->id, self::metakey, $this->records );
			return $pace;
		} else {
			$ts = (string)Advanced_Ads_Tracking_Util::get_instance()->get_timestamp();
			if ( isset( $this->records['count'][$ts] ) ) {
				// return the current pace ( current hour )
				return $this->records['pace'];
			} else {
				// update pace for the current hour and return the new value
				$this->records['pace'] = $pace;
				update_post_meta( $this->id, self::metakey, $this->records );
				return $pace;
			}
		}
	}
	
	/**
	 *  Internal tracking
	 */
	public function track( $count, $type ) {
		if ( defined( 'ADVANCED_ADS_TRACKING_NO_HOURLY_LIMIT' ) && ADVANCED_ADS_TRACKING_NO_HOURLY_LIMIT ) {
			// feature disabled
			return;
		}
		if ( false === $this->get_hourly_pace() ) {
			// no limitation, don't track internally
			return;
		}
		if ( empty( $this->records ) ) {
			// no data yet
			$this->records = array(
				'pace' => $this->get_hourly_pace(),
				'count' => array(),
			);
		}
		$ts = (string)Advanced_Ads_Tracking_Util::get_instance()->get_timestamp();
		if ( isset( $this->records['count'][$ts] ) ) {
			if ( isset( $this->records['count'][$ts][$type] ) ) {
				$this->records['count'][$ts][$type] += absint( $count );
			} else {
				$this->records['count'][$ts][$type] = absint( $count );
			}
			update_post_meta( $this->id, self::metakey, $this->records );
		} else {
			$this->records['count'][$ts][$type] = absint( $count );
			update_post_meta( $this->id, self::metakey, $this->records );
		}
	}
	
	/**
	 *  Check if the ad can ad displayed in the front end
	 */
	public function can_display( $can_display, $ad ) {
		if ( defined( 'ADVANCED_ADS_TRACKING_NO_HOURLY_LIMIT' ) && ADVANCED_ADS_TRACKING_NO_HOURLY_LIMIT ) {
			// feature disabled
			return true;
		}
		$ts = (string)Advanced_Ads_Tracking_Util::get_instance()->get_timestamp();
		$pace = $this->get_hourly_pace();
		if ( false === $pace ) {
			return true;
		}
		if ( isset( $pace['impressions'] ) && isset( $this->records['count'][$ts] ) && isset( $this->records['count'][$ts]['impressions'] ) ) {
			if ( $this->records['count'][$ts]['impressions'] >= $pace['impressions'] ) {
				return false;
			}
		}
		if ( isset( $pace['clicks'] ) && isset( $this->records['count'][$ts] ) && isset( $this->records['count'][$ts]['clicks'] ) ) {
			if ( $this->records['count'][$ts]['clicks'] >= $pace['clicks'] ) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * returns records for this ad 
	 */
	public function get_records() {
		return $this->records;
	}
	
	/**
	 *  delete meta data
	 */
	public static function delete_records( $ad_id ) {
		if ( empty( $ad_id ) ) {
			return;
		}
		delete_post_meta( absint( $ad_id ), self::metakey );
	}
	
}