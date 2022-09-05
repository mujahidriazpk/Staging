<label><?php _e( 'Your Tracking ID', 'advanced-ads-tracking' ); ?></label><br />
<input type="text" name="<?php echo $this->plugin->options_slug; ?>[ga-UID]" value="<?php echo esc_attr( $UID ); ?>" />
<p class="description"><?php _e( 'The Google Analytics property you want the data to be sent to', 'advanced-ads-tracking' ); ?>&nbsp;<code>(UA-123456-1)</code></p>