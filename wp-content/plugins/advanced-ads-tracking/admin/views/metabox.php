<?php
$has_limits_message = 	( !empty( $impression_limit ) || !empty( $click_limit ) ); 
?><style type="text/css">
#tracking-ads-box .form-group {
    margin: 8px;
    padding: 6px;
}
#tracking-ads-box .form-group label {
    display: block;
    font-weight: bold;
    margin: 6px 0 8px 0;
}
</style>
<?php if( $warnings ) : ?>
<ul id="tracking-ads-box-notices" class="advads-metabox-notices">
<?php foreach( $warnings as $_warning ) :
	$warning_class = isset( $_warning['class'] ) ? $_warning['class'] : '';
	echo '<li class="'. $warning_class . '">';
	echo $_warning['text'];
	echo '</li>';
endforeach;
endif;
// hide options if Google Analytics tracking method is used
if( 'ga' !== $this->plugin->get_tracking_method() ) :
?></ul>
<div class="advads-option-list">
<?php
    global $post, $wpdb;
    $admin_ad_title = $post->post_title;
	
	$to = date_create( 'today' );
	$from = date_create( '14 days ago' );
	
	$clicks_stats = $this->load_stats(
		array(
			'ad_id' => array( $post->ID ),
			'period' => 'custom',
			'groupby' => 'day',
			'from' => $from->format( 'm/d/Y' ),
			'to' => $to->format( 'm/d/Y' ),
		),
		$wpdb->prefix . 'advads_clicks'
	);
	
	$impressions_stats = $this->load_stats(
		array(
			'ad_id' => array( $post->ID ),
			'period' => 'custom',
			'groupby' => 'day',
			'from' => $from->format( 'm/d/Y' ),
			'to' => $to->format( 'm/d/Y' ),
		),
		$wpdb->prefix . 'advads_impressions'
	);
	
	/**
	 * Fill data with empty values until today if missing
	 */
	if ( false !== $impressions_stats ) {
		
		$_impressions_fill = array();
		$_clicks_fill = array();
		$_to = $to;
		
		while( !isset( $impressions_stats[ $_to->format('Y-m-d') ] ) ) {
			$_impressions_fill[ $_to->format('Y-m-d') ] = array( $post->ID => null );
			if ( !isset( $clicks_stats[ $_to->format('Y-m-d') ] ) ) {
				$clicks_fill[ $_to->format('Y-m-d') ] = array( $post->ID => null );
			}
			$_to = date_create( '@' . ( absint( $_to->format( 'U' ) ) - ( 3600 * 24 ) ) );
		}
		
		$impressions_stats += array_reverse( $_impressions_fill );
		
		if ( is_array( $clicks_stats ) ) {
			$clicks_stats += array_reverse( $_clicks_fill );
		}
	}
	
	$_stats = array(
		'ID' => $post->ID,
		'impressions' => $impressions_stats,
		'clicks' => $clicks_stats,
	);
	
	$public_link = $public_id ? site_url( '/' . $public_stats_slug .'/' . $public_id . '/' ) : false; 
    
    $permalink = get_option( 'permalink_structure' );
    
    if ( empty( $permalink ) && $public_link ) {
        $public_link = site_url( '/?' . $public_stats_slug . '=' . $public_id );
    }
    
?>
	<script type="text/javascript">
		var advads_stats = <?php echo json_encode( $_stats ); ?>;
	</script>
	<div id="stats-jqplot"></div>
	<?php if ( false !== $impressions_stats ) : ?>
	<hr />
	<?php endif; ?>
	<span class="label"><?php _e( 'Stats pages', 'advanced-ads-tracking' ); ?></span>
    <div>
		<b>
			<a href="<?php echo Advanced_Ads_Tracking_Admin::admin_30days_stats_url( $post->ID ); ?>"><?php _e( 'Dashboard', 'advanced-ads-tracking' ); ?></a>
			<?php if ( !defined( 'ADVANCED_ADS_TRACKING_NO_PUBLIC_STATS' ) ) : ?>
				<?php if ( $public_id ) : ?>
				<a href="<?php echo esc_url( $public_link ); ?>" style="margin-left:1.5rem;"><?php _e( 'Shareable Link', 'advanced-ads-tracking' ); ?></a>
				<?php else : ?>
				<i class="dashicons dashicons-info" style="color:#ff9800;margin-left:.5em;font-size:1.75em;cursor:pointer;" title="<?php echo esc_attr( __( 'The public stats url for this ad will be generated the next time it is saved.', 'advanced-ads-tracking' ) ); ?>"></i>
				<?php endif; ?>
			<?php endif; ?>
		</b>
	</div>
    <hr />
    <span class="label"><?php _e( 'Public name', 'advanced-ads-tracking' ); ?></span>
	<div>
        <input type="text" name="advanced_ad[tracking][public-name]" value="<?php echo $public_name; ?>" />
        <p class="description"><?php _e( 'Will be used as ad name instead of the internal ad title', 'advanced-ads-tracking' ); 
        ?>&nbsp;<?php echo ( ! empty( $admin_ad_title ) )? '(' . $admin_ad_title .')' : '' ; ?></p>
    </div>
	<hr />
	<span class="label"><?php _e( 'limits', 'advanced-ads-tracking' ); ?></span>
	<div>
    <table id="advads-ad-stats" class="table widefat">
	<thead>
	    <tr class="alternate">
		<th></th>
		<th><strong><?php _e( 'current', 'advanced-ads-tracking' ); ?></strong></th>
		<th><strong><?php _e( 'limit', 'advanced-ads-tracking' ); ?></strong></th>
	    </tr>
	</thead>
	<tbody>
	    <tr>
		<th><strong><?php _e( 'impressions', 'advanced-ads-tracking' ); ?></strong></th>
		<td><?php echo isset( $sums['impressions'][ $post->ID ] ) ? $sums['impressions'][ $post->ID ] : 0; ?></td>
		<td><input name="advanced_ad[tracking][impression_limit]" type="number" value="<?php echo $impression_limit; ?>"/></td>
	    </tr>
	    <tr class="advads-tracking-click-limit-row" style="<?php echo $clicks_display; ?>">
		<th><strong><?php _e( 'clicks', 'advanced-ads-tracking' ); ?></strong></th>
		<td><?php echo isset( $sums['clicks'][ $post->ID ] ) ? $sums['clicks'][ $post->ID ] : 0; ?></td>
		<td><input name="advanced_ad[tracking][click_limit]" type="number" value="<?php echo $click_limit; ?>"/></td>
	    </tr>
	</tbody>
    </table>
    <p class="description"><?php _e('Set a limit if you want to expire the ad after a specific amount of impressions or clicks.', 'advanced-ads-tracking'); ?></p>
	</div>
	<hr />
	<?php if ( !defined( 'ADVANCED_ADS_TRACKING_NO_HOURLY_LIMIT' ) || !ADVANCED_ADS_TRACKING_NO_HOURLY_LIMIT ) : ?>
		<?php if ( $has_limits_message ) : ?>
			<?php 
			$use_clicks = in_array( $ad->type, Advanced_Ads_Tracking_Plugin::$types_using_click_tracking );
			$limits_type = 'impressions';
			$__limits_type = __( 'impressions', 'advanceds-ads-tracking' );
			if ( $use_clicks && !empty( $click_limit ) ) {
				$__limits_type = __( 'clicks', 'advanceds-ads-tracking' );
				$limits_type = 'clicks';
				if ( !empty( $impression_limit ) ) {
					$__limits_type = __( 'impressions or clicks', 'advanceds-ads-tracking' );
					$limits_type = 'all';
				}
			}
			if ( empty( $options['expiry_date'] ) ) : 
			/**
			 *  There is no expiry date
			 */
			 ?>
			<p class="description" style="color:#e48901"><?php
				printf(
					__( 'The ad %s will be delivered as soon as possible. Set an expiry date in the <em>Publish</em> meta box to spread impressions over a period.', 'advanced-ads-tracking'),
					$__limits_type
				); 
			
			?></p>
			<?php else : $limiter = new Advanced_Ads_Tracking_Limiter( $post->ID ); $pace = $limiter->get_hourly_pace(); ?>
			<p class="description" style="color:#e48901"><?php
				/**
				 *  Expiry date is set
				 */
				 if ( time() <= $options['expiry_date'] ) {
					/**
					 *  The ad has not yet expired
					 */
					if ( isset( $pace['impressions'] ) && 0 == $pace['impressions'] ) {
						_e( 'The impressions goal for the current hour has been reached.', 'advanceds-ads-tracking' );
						echo ' '; _e( 'Impressions will resume on the next hour', 'advanced-ads-tracking' );
					} elseif ( isset( $pace['clicks'] ) && 0 == $pace['clicks'] ) {
						_e( 'The clicks goal for the current hour has been reached.', 'advanced-ads-tracking' );
						echo ' '; _e( 'Impressions will resume on the next hour', 'advanced-ads-tracking' );
					} else {
						$timeleft = $options['expiry_date'] - time();
						$timeleft_dh_days = floor( $timeleft / 86400 );
						$timeleft_dh_hours = floor( ( $timeleft - ( $timeleft_dh_days * 86400 ) ) / 3600 );
						$timeleft_hm_hours = floor( $timeleft / 3600 );
						$timeleft_hm_mins = floor( ( $timeleft - ( $timeleft_hm_hours * 3600 ) ) / 60 );
						
						$timeleft_str = '';
						
						if ( 0 != $timeleft_dh_days ) {
							$timeleft_str .= sprintf( advads_n( '%s day', '%s days', $timeleft_dh_days ), $timeleft_dh_days );
							if ( 0 < $timeleft_dh_hours ) {
								$timeleft_str .= ' ' . sprintf( advads_n( '%s hour', '%s hours', $timeleft_dh_hours ), $timeleft_dh_hours );
							}
						} else {
							if ( 0 != $timeleft_hm_hours ) {
								$timeleft_str .= sprintf( advads_n( '%s hour', '%s hours', $timeleft_hm_hours ), $timeleft_hm_hours );
							}
							if ( 0 != $timeleft_hm_mins && !empty( $timeleft_str ) ) {
								$timeleft_str .= ' ' . sprintf( advads_n( '%s minute', '%s minutes', $timeleft_hm_mins ), $timeleft_hm_mins );
							}
						}
						
						if ( 'all' != $limits_type ) {
							printf(
								__( 'The %1$s are spread equally through %2$s currently with a limit of %3$d %1$s per hour.', 'advanced-ads-tracking' ),
								$__limits_type, // impressions or clicks
								$timeleft_str, // time left
								$pace[$limits_type] // pace
							);
						} else {
							printf(
								__( 'The %1$s are spread equally through %2$s currently with a limit of %3$d impressions or %4$d clicks per hour.', 'advanced-ads-tracking' ),
								$__limits_type, // impressions and clicks
								$timeleft_str, // time left
								$pace['impressions'], // impression pace
								$pace['clicks'] // click pace
							);
						}
					}
				} else {
					/**
					 *  Ad has already expired
					 */
					_e( 'This ad expired already.', 'advanced-ads-tracking' );
				}
			?></p>
			<?php endif; // empty( $options['expiry_date'] ) ?>
		<hr />
		<?php endif; // if ( $has_limits_message ) ?>
	<?php endif; ?>
    <?php if ( $public_id ) : ?>
		<input type="hidden" name="advanced_ad[tracking][public-id]" value="<?php echo esc_attr( $public_id ); ?>" />
    <?php else : ?>
		<input type="hidden" name="advanced_ad[tracking][public-id]" value="<?php echo wp_generate_password( $hash_length, false ); ?>" />
    <?php endif; ?>
	<span class="label"><?php _e( 'report recipient', 'advanced-ads-tracking' ); ?></span>
	<div>
		<?php if ( $billing_email ) : ?>
		<input type="hidden" name="advanced_ad[tracking][report-recip]" value="" />
		<input type="text" style="width:66%;" disabled value="<?php echo esc_attr( $billing_email ); ?>"/>
		<?php else : ?>
		<input type="text" style="width:66%;" name="advanced_ad[tracking][report-recip]" value="<?php echo esc_attr( $report_recip ); ?>" />
		<?php endif; ?>
		<p class="description"><?php _e( 'Email address to send the performance report for this ad', 'advanced-ads-tracking' ); 
		?>.&nbsp;<?php _e( 'Separate multiple emails with commas', 'advanced-ads-tracking' ); ?></p>
	</div>
	<hr>
	<span class="label"><?php _e( 'report period', 'advanced-ads-tracking' ); ?></span>
	<div>
		<select name="advanced_ad[tracking][report-period]">
			<option value="last30days" <?php selected( $report_period, 'last30days' ); ?>><?php _e( 'last 30 days', 'advanced-ads-tracking' ); ?></option>
			<option value="lastmonth" <?php selected( $report_period, 'lastmonth' ); ?>><?php _e( 'last month', 'advanced-ads-tracking' ); ?></option>
			<option value="last12months" <?php selected( $report_period, 'last12months' ); ?>><?php _e( 'last 12 months', 'advanced-ads-tracking' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Period used to calculate the stats for the report', 'advanced-ads-tracking' ); ?></p>
	</div>
	<hr>
	<span class="label"><?php _e( 'report frequency', 'advanced-ads-tracking' ); ?></span>
	<div>
		<select name="advanced_ad[tracking][report-frequency]">
			<option value="never" <?php selected( $report_frequency, 'never' ); ?>><?php _e( 'never', 'advanced-ads-tracking' ); ?></option>
			<option value="daily" <?php selected( $report_frequency, 'daily' ); ?>><?php _e( 'daily', 'advanced-ads-tracking' ); ?></option>
			<option value="weekly" <?php selected( $report_frequency, 'weekly' ); ?>><?php _e( 'weekly', 'advanced-ads-tracking' ); ?></option>
			<option value="monthly" <?php selected( $report_frequency, 'monthly' ); ?>><?php _e( 'monthly', 'advanced-ads-tracking' ); ?></option>
		</select>
		<p class="description"><?php _e( 'How often to send email reports', 'advanced-ads-tracking' ); ?></p>
	</div>
	<hr>
</div>
<?php endif;