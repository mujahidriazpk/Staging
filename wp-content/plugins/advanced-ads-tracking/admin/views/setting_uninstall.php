<label><input type="checkbox" <?php checked( $uninstall, '1' ); ?> value="1" name="<?php echo $this->plugin->options_slug; ?>[uninstall]" /><?php _e( 'delete data', 'advanced-ads-tracking' ); ?></label>
<p class="description"><?php _e( 'Clean up all database entries related to tracking when removing the Tracking add-on.', 'advanced-ads-tracking' ); ?></p>
