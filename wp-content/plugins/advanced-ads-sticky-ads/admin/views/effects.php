<?php
$effect     = isset( $options['effect'] ) ? $options['effect'] : 'show';
$duration   = isset( $options['duration'] ) ? absint( $options['duration'] ) : 0;

?>
<label><input type="radio" name="<?php echo $option_name; ?>[effect]" value="show" <?php checked( $effect, 'show' ); ?>/><?php _e( 'Show', 'advanced-ads-sticky' ); ?></label>
<label><input type="radio" name="<?php echo $option_name; ?>[effect]" value="fadein" <?php checked( $effect, 'fadein' ); ?>/><?php _e( 'Fade in', 'advanced-ads-sticky' ); ?></label>
<label><input type="radio" name="<?php echo $option_name; ?>[effect]" value="slidedown" <?php checked( $effect, 'slidedown' ); ?>/><?php _e( 'Slide in', 'advanced-ads-sticky' ); ?></label>
<p class="description"><?php _e( 'Type of effect when the ad is being displayed', 'advanced-ads-sticky' ); ?></p>
<br/>
<input type="number" name="<?php echo $option_name; ?>[duration]" value="<?php echo $duration; ?>"/>
<p class="description"><?php _e( 'Duration of the effect (in milliseconds).', 'advanced-ads-sticky' ); ?></p>
