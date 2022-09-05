<?php
$options = isset( $placement['options']['layer_placement']['auto_close'] ) ? $placement['options']['layer_placement']['auto_close'] : array();
$option_name = 'advads[placements][' . $placement_slug . '][options][layer_placement][auto_close]';
$auto_close_trigger = isset( $options['trigger'] ) ? $options['trigger'] : '';
$auto_close_delay   = isset( $options['delay'] ) ? absint( $options['delay'] ) : 5;
?>
<ul>
    <li><label><input type="checkbox" name="<?php echo $option_name; ?>[trigger]" value="timeout" <?php checked( $auto_close_trigger, 'timeout' ); ?>/><?php
    printf( __( 'close after %s seconds', 'advanced-ads-layer' ), '</label><label><input type="number" name="' . $option_name . '[delay]" value="' . $auto_close_delay . '"/>'); ?></label>
    </li>
</ul>
