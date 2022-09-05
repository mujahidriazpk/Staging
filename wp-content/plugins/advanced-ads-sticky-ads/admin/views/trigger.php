<?php
//$enabled    = isset( $options['enabled'] ) ? $options['enabled'] : false;
$trigger    = isset( $options['trigger'] ) ? $options['trigger'] : '';
$delay     = isset( $options['delay'] ) ? absint( $options['delay'] ) : 0;

?><ul>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="" <?php checked( $trigger, '' ); ?>/><?php _e( 'right away', 'advanced-ads-sticky' ); ?></label></li>
    <li><label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="effect" <?php checked( $trigger, 'effect' ); ?>/><?php _e( 'right away with effect', 'advanced-ads-sticky' ); ?></label></li>
    <li>
	<label><input type="radio" name="<?php echo $option_name; ?>[trigger]" value="timeout" <?php checked( $trigger, 'timeout' ); ?>/><?php 
	printf( __( 'after %s seconds', 'advanced-ads-sticky' ), '<input type="number" name="' . $option_name . '[delay]" value="'.$delay.'"/>'); ?></label>
    </li>
</ul>
