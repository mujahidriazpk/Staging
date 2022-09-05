<div class="advads-buttonset">
<input type="radio" name="<?php echo ADVADS_SLUG . '[' . AAR_SLUG . '][show-tooltip]' ?>" value="1" id="advads-responsive-tooltip-setting-on" <?php
    checked( $show_tooltip ); ?> /><label for="advads-responsive-tooltip-setting-on">&nbsp;<?php _e('on', 'advanced-ads-responsive'); ?></label>
<input type="radio" name="<?php echo ADVADS_SLUG . '[' . AAR_SLUG . '][show-tooltip]' ?>" value="0" id="advads-responsive-tooltip-setting-off" <?php
    checked( !$show_tooltip ); ?> /><label for="advads-responsive-tooltip-setting-off">&nbsp;<?php _e('off', 'advanced-ads-responsive'); ?></label>
</div>
<p class="description"><?php _e( 'Display the sizes of the ads, ad containers and window in the frontend. Only logged in admin users will see it.', 'advanced-ads-responsive' ); ?></p>