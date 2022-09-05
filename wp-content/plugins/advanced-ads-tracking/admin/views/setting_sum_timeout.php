<input name="<?php echo $this->plugin->options_slug; ?>[sum-timeout]" type="number" value="<?php
    echo $timeout; ?>"/><?php _ex( 'minutes', 'label for sum timeout option', 'advanced-ads-tracking' ); ?>
<p class="description"><?php
    _e('How often the sum of impressions and clicks used for ads with limited impressions or clicks is recalculated. The higher the number, the later the ad might expire, but the better the performance.', 'advanced-ads-tracking'); ?></p>
    <p><?php _e('Set 0 to recalculate on every page impression. 1 hour = 60, 1 day = 1440', 'advanced-ads-tracking'); ?></p>
    <p><?php _e('Resaving these settings forces a recalculation of the sum.', 'advanced-ads-tracking'); ?></p>