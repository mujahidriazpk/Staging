    <label><input type="radio" name="<?php echo $option_name; ?>[effect]" value="show" <?php checked( $effect, 'show' ); ?>/><?php _e( 'Show', 'advanced-ads-layer' ); ?></label>
    <label><input type="radio" name="<?php echo $option_name; ?>[effect]" value="fadein" <?php checked( $effect, 'fadein' ); ?>/><?php _e( 'Fade in', 'advanced-ads-layer' ); ?></label>
    <label<?php if ( $this->fancybox_is_enabled ) : ?> style="display:none;" <?php endif; ?>><input type="radio" name="<?php echo $option_name; ?>[effect]" value="slide" <?php checked( $effect, 'slide' ); ?>/><?php _e( 'Slide', 'advanced-ads-layer' ); ?></label>
<p class="description"><?php _e( 'Type of effect when the ad is being displayed', 'advanced-ads-layer' ); ?></p>
<br/>
<input type="number" name="<?php echo $option_name; ?>[duration]" value="<?php echo $duration; ?>"/>
<p class="description"><?php _e( 'Duration of the effect (in milliseconds).', 'advanced-ads-layer' ); ?></p>
