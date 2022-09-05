<?php
$options = isset( $placement['options']['layer_placement']['close'] ) ? $placement['options']['layer_placement']['close'] : array();

$close_enabled         = isset( $options['enabled'] ) ? $options['enabled'] : 0;
$close_where           = isset( $options['where'] ) ? $options['where'] : 'inside';
$close_side            = isset( $options['side'] ) ? $options['side'] : 'right';
$close_timeout_enabled = isset( $options['timeout_enabled'] ) ? $options['timeout_enabled'] : false;
$close_timeout         = isset( $options['timeout'] ) ? absint( $options['timeout'] ) : 0;

$option_name = "advads[placements][$placement_slug][options][layer_placement][close]";

?><p><label><input type="checkbox" name="<?php echo $option_name; ?>[enabled]" value="1" <?php
checked( $close_enabled, 1 ); ?> onclick="advads_toggle_box(this, '#advads-close-button-<?php echo $placement_slug; ?>');"/><?php
_e( 'add close button', 'advanced-ads-layer' ); ?></label></p>
<div id="advads-close-button-<?php echo $placement_slug; ?>" <?php if ( ! $close_enabled ) : ?> style="display:none;"<?php endif; ?>>

    <div <?php if ( $this->fancybox_is_enabled ) : ?> style="display:none;"<?php endif; ?>>
        <p><?php _e( 'Position', 'advanced-ads-layer' ); ?></p>
        <label><input type="radio" name="<?php echo $option_name; ?>[where]" value="outside" <?php checked($close_where, 'outside'); ?>/><?php _e( 'outside', 'advanced-ads-layer' ); ?></label>
        <label><input type="radio" name="<?php echo $option_name; ?>[where]" value="inside" <?php checked($close_where, 'inside'); ?>/><?php _e( 'inside', 'advanced-ads-layer' ); ?></label>
        <br/>
        <label><input type="radio" name="<?php echo $option_name; ?>[side]" value="left" <?php checked($close_side, 'left'); ?>/><?php _e( 'left', 'advanced-ads-layer' ); ?></label>
        <label><input type="radio" name="<?php echo $option_name; ?>[side]" value="right" <?php checked($close_side, 'right'); ?>/><?php _e( 'right', 'advanced-ads-layer' ); ?></label>
        <br/>
        <br/>
    </div>

    <p><label><input type="checkbox" name="<?php echo $option_name; ?>[timeout_enabled]" onclick="advads_toggle_box(this, '#advads-timeout-<?php echo $placement_slug; ?>');" value="true" <?php checked($close_timeout_enabled, 'true'); ?>/><?php _e( 'enable timeout', 'advanced-ads-layer' ); ?></label></p>
    <div id="advads-timeout-<?php echo $placement_slug; ?>" <?php if (!$close_timeout_enabled) : ?> style="display:none;"<?php endif; ?>>
	<p><?php _e( 'close the ad for …', 'advanced-ads-layer' ); ?></p>
	<input type="number" name="<?php echo $option_name; ?>[timeout]" value="<?php echo $close_timeout; ?>"/>
	<span class="description"><?php _e( 'days, 0 = after current session', 'advanced-ads-layer' ); ?></span>
    </div>
</div>