<?php
$options = isset( $placement['options']['close'] ) ? $placement['options']['close'] : array();

$close_enabled = isset($options['enabled']) ? $options['enabled'] : 0;
$close_where = isset($options['where']) ? $options['where'] : 'inside';
$close_side = isset($options['side']) ? $options['side'] : 'right';
$close_timeout_enabled = isset($options['timeout_enabled']) ? $options['timeout_enabled'] : false;
$close_timeout = isset($options['timeout']) ? absint($options['timeout']) : 0;

$option_name = "advads[placements][$placement_slug][options][close]";

?><p><label><input type="checkbox" name="<?php echo $option_name; ?>[enabled]" value="1" <?php
checked($close_enabled, 1); ?> onclick="advads_toggle_box(this, '#advads-close-button-<?php echo $placement_slug; ?>');"/><?php
_e('add close button', 'advanced-ads-sticky'); ?></label></p>
<div id="advads-close-button-<?php echo $placement_slug; ?>" <?php if(!$close_enabled) : ?> style="display:none;"<?php endif; ?>>
    <p><?php _e('Position', 'advanced-ads-sticky'); ?></p>
    <label><input type="radio" name="<?php echo $option_name; ?>[where]" value="outside" <?php checked($close_where, 'outside'); ?>/><?php _e('outside', 'advanced-ads-sticky'); ?></label>
    <label><input type="radio" name="<?php echo $option_name; ?>[where]" value="inside" <?php checked($close_where, 'inside'); ?>/><?php _e('inside', 'advanced-ads-sticky'); ?></label>
    <br/>
    <label><input type="radio" name="<?php echo $option_name; ?>[side]" value="left" <?php checked($close_side, 'left'); ?>/><?php _e('left', 'advanced-ads-sticky'); ?></label>
    <label><input type="radio" name="<?php echo $option_name; ?>[side]" value="right" <?php checked($close_side, 'right'); ?>/><?php _e('right', 'advanced-ads-sticky'); ?></label>
    <br/><br/><p><label><input type="checkbox" name="<?php echo $option_name; ?>[timeout_enabled]" onclick="advads_toggle_box(this, '#advads-timeout-<?php echo $placement_slug; ?>');" value="true" <?php checked($close_timeout_enabled, 'true'); ?>/><?php _e('enable timeout', 'advanced-ads-sticky'); ?></label></p>
    <div id="advads-timeout-<?php echo $placement_slug; ?>" <?php if(!$close_timeout_enabled) : ?> style="display:none;"<?php endif; ?>>
	<p><?php _e('close the ad for …', 'advanced-ads-sticky'); ?></p>
	<input type="number" name="<?php echo $option_name; ?>[timeout]" value="<?php echo $close_timeout; ?>"/>
	<span class="description"><?php _e('days, 0 = after current session', 'advanced-ads-sticky'); ?></span>
    </div>
</div>