<select name="<?php echo $this->plugin->options_slug; ?>[email-stats-period]">
<option value="last30days" <?php selected( $period, 'last30days' ); ?>><?php _e( 'last 30 days', 'advanced-ads-tracking' ); ?></option>
<option value="lastmonth" <?php selected( $period, 'lastmonth' ); ?>><?php _e( 'last month', 'advanced-ads-tracking' ); ?></option>
<option value="last12months" <?php selected( $period, 'last12months' ); ?>><?php _e( 'last 12 months', 'advanced-ads-tracking' ); ?></option>
</select>
<p class="description"><?php _e( 'The period for which stats will be included.', 'advanced-ads-tracking' ); ?></p>
