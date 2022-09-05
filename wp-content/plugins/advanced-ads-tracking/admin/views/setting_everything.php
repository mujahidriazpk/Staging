<select name="<?php echo $this->plugin->options_slug; ?>[everything]">
	<option value="true" <?php selected( $method, 'true' ); ?>><?php _e('impressions & clicks', 'advanced-ads-tracking'); ?></option>
	<option value="false" <?php selected( $method, 'false' ); ?>><?php _e('don’t track anything', 'advanced-ads-tracking'); ?></option>
	<option value="impressions" <?php selected( $method, 'impressions' ); ?>><?php _e('impressions only', 'advanced-ads-tracking'); ?></option>
	<option value="clicks" <?php selected( $method, 'clicks' ); ?>><?php _e('clicks only', 'advanced-ads-tracking'); ?></option>
</select>
<p class="description"><?php _e('You can change this setting individually for each ad on the ad edit page.', 'advanced-ads-tracking'); ?></p>