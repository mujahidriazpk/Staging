<ul>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="" <?php checked( $trigger, '' ); ?>/><?php _e( 'right away', 'advanced-ads-layer' ); ?></label></li>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="stop" <?php checked( $trigger, 'stop' ); ?>/><?php _e( 'when user stops scrolling', 'advanced-ads-layer' ); ?></label></li>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="half" <?php checked( $trigger, 'half' ); ?>/><?php _e( 'after user scrolled to second half of the page', 'advanced-ads-layer' ); ?></label></li>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="custom" <?php checked( $trigger, 'custom' ); ?>/><?php
    printf( __( 'after user scrolled %s px', 'advanced-ads-layer' ), '</label><label><input type="number" name="' . $option_name . '[offset]" value="'.$offset.'"/>'); ?></label>
    </li>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="exit" <?php checked( $trigger, 'exit' ); ?>/><?php _e( 'when a user wants to leave the page', 'advanced-ads-layer' ); ?></label></li>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="delay" <?php checked( $trigger, 'delay' ); ?>/><?php
    printf( __( 'after %s seconds', 'advanced-ads-layer' ), '</label><label><input type="number" name="' . $option_name . '[delay_sec]" value="' . $delay_sec . '"/>'); ?></label>
    </li>
</ul>
