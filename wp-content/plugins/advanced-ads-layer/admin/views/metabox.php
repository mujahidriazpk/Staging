<?php 
if (! isset($layer_class)){
	$layer_class = Advanced_Ads_Layer::get_layer_class();
}
?>
<p class="advads-error-message"><?php printf( __( 'These settings are deprecated. Layer ads are now managed through <a href="%s">placements</a>. Please convert your settings as soon as possible.', 'advanced-ads-layer' ), admin_url( 'admin.php?page=advanced-ads-placements' ) ); ?></p>
<div>
    <label><input type="checkbox" name="advanced_ad[layer][enabled]" value="1" onclick="advads_toggle_box(this, '#<?php echo $layer_class; ?>-ads');" <?php checked($enabled, 1); ?>/><?php _e( 'Show this ad in a PopUp', 'advanced-ads-layer' ); ?></label>
    <div id="<?php echo $layer_class; ?>-ads"<?php if ( ! $enabled ) : ?> style="display:none;"<?php endif; ?>>
        <p class="description"><?php _e( 'Choose when the ad should show up.', 'advanced-ads-layer' ); ?></p>
        <ul>
            <li><label><input type="radio" name="advanced_ad[layer][trigger]" value="" <?php checked( $trigger, '' ); ?>/><?php _e( 'right away', 'advanced-ads-layer'); ?></label></li>
            <li><label><input type="radio" name="advanced_ad[layer][trigger]" value="stop" <?php checked( $trigger, 'stop' ); ?>/><?php _e( 'when a user stops scrolling', 'advanced-ads-layer'); ?></label></li>
            <li><label><input type="radio" name="advanced_ad[layer][trigger]" value="half" <?php checked( $trigger, 'half' ); ?>/><?php _e( 'after a user scrolled half of the page', 'advanced-ads-layer'); ?></label></li>
            <li><label><input type="radio" name="advanced_ad[layer][trigger]" value="custom" <?php checked( $trigger, 'custom' ); ?>/><?php
            printf( __( 'after a user scrolled %s px', 'advanced-ads-layer' ), '</label><label><input type="number" name="advanced_ad[layer][offset]" value="' . $offset . '"/>'); ?></label></li>
        </ul>
        <p class="description"><?php _e( 'Put a half-transparent overlay over the background of the page if this ad is displayed.', 'advanced-ads-layer' ); ?></p>
        <p><label><input type="checkbox" name="advanced_ad[layer][background]" value="1" <?php checked( $background, 1 ); ?>/><?php _e( 'Enable background', 'advanced-ads-layer' ); ?></label></p>
        <h4><?php _e( 'Effects', 'advanced-ads-layer' ); ?></h4>
        <p class="description"><?php _e( 'Type of effect when the ad is being displayed', 'advanced-ads-layer' ); ?></p>
            <label><input type="radio" name="advanced_ad[layer][effect]" value="show" <?php checked( $effect, 'show' ); ?>/><?php _e( 'Show', 'advanced-ads-layer' ); ?></label>
            <label><input type="radio" name="advanced_ad[layer][effect]" value="fadein" <?php checked( $effect, 'fadein' ); ?>/><?php _e( 'Fade in', 'advanced-ads-layer' ); ?></label>
            <label><input type="radio" name="advanced_ad[layer][effect]" value="slide" <?php checked( $effect, 'slide' ); ?>/><?php _e( 'Slide', 'advanced-ads-layer' ); ?></label>
        <p class="description"><?php _e( 'Duration of the effect (in milliseconds).', 'advanced-ads-layer' ); ?></p>
        <input type="number" name="advanced_ad[layer][duration]" value="<?php echo $duration; ?>"/>
    </div>

    <br/>
    <h4><?php _e( 'Close-Button', 'advanced-ads-layer' ); ?></h4>
    <p><label><input type="checkbox" name="advanced_ad[layer][close][enabled]" value="1" <?php
    checked( $close_enabled, 1 ); ?> onclick="advads_toggle_box(this, '#advads-close-ads');"/><?php
    _e( 'Add close button', 'advanced-ads-layer' ); ?></label></p>
    <div id="advads-close-ads"<?php if ( ! $close_enabled ) : ?> style="display:none;"<?php endif; ?>>
	    <p class="description"><?php _e( 'Allow visitors to remove this ad.', 'advanced-ads-layer' ); ?></p>
        
        <div <?php if ( $this->fancybox_is_enabled ) : ?> style="display:none;"<?php endif; ?>>
            <p><?php _e( 'Where to display the close button', 'advanced-ads-layer' ); ?></p>
            <label><input type="radio" name="advanced_ad[layer][close][where]" value="outside" <?php checked($close_where, 'outside'); ?>/><?php _e( 'outside', 'advanced-ads-layer' ); ?></label>
            <label><input type="radio" name="advanced_ad[layer][close][where]" value="inside" <?php checked($close_where, 'inside'); ?>/><?php _e( 'inside', 'advanced-ads-layer' ); ?></label>
            <br/>
            <label><input type="radio" name="advanced_ad[layer][close][side]" value="left" <?php checked($close_side, 'left'); ?>/><?php _e( 'left', 'advanced-ads-layer' ); ?></label>
            <label><input type="radio" name="advanced_ad[layer][close][side]" value="right" <?php checked($close_side, 'right'); ?>/><?php _e( 'right', 'advanced-ads-layer' ); ?></label>
            <br/>
        </div>

	    <p><label><input type="checkbox" name="advanced_ad[layer][close][timeout_enabled]" onclick="advads_toggle_box(this, '#<?php echo $layer_class; ?>-timeout');" value="true" <?php checked( $close_timeout_enabled, 'true' ); ?>/><?php _e( 'enable timeout', 'advanced-ads-layer' ); ?></label></p>
	
        <div id="<?php echo $layer_class; ?>-timeout" <?php if ( ! $close_timeout_enabled ) : ?> style="display:none;"<?php endif; ?>>
    	    <p class="description"><?php _e( 'How long should visitors, that closed the ad, not see it again.', 'advanced-ads-layer' ); ?></p>
    	    <input type="number" name="advanced_ad[layer][close][timeout]" value="<?php echo $close_timeout; ?>"/>
    	    <span class="description"><?php _e( 'in days, 0 = current session', 'advanced-ads-layer' ); ?></span>
    	</div>

    </div>
</div>
