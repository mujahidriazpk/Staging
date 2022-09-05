<p class="advads-error-message"><?php printf( __( 'These settings are deprecated. Sticky ads are now managed through <a href="%s">placements</a>. Please convert your settings as soon as possible.', 'advanced-ads-sticky' ), admin_url( 'admin.php?page=advanced-ads-placements' ) ); ?></p>
<div>
    <label><input type="checkbox" name="advanced_ad[sticky][enabled]" id="advanced_ad_sticky_type" value="1" onclick="advads_toggle_box(this, '#advads-sticky-ads');" <?php checked( $enabled, 1 ); ?>/><?php _e( 'Stick ad to a specific position on the screen', 'advanced-ads-sticky' ); ?></label>
    <div id="advads-sticky-ads"<?php if ( ! $enabled ) : ?> style="display:none;"<?php endif; ?>>
        <div id="advads-sticky-ads-assistant">
            <p><label><input type="radio" name="advanced_ad[sticky][type]" id="advanced_ad_sticky_type_assistant" value="assistant" <?php checked( $type, 'assistant' ); ?>/><?php _e( 'Select a position', 'advanced-ads-sticky' ); ?></label></p>
            <p class="description"><?php _e( 'Choose a position on the screen.', 'advanced-ads-sticky' ); ?></p>
            <div class="advads-sticky-assistant">
                <table>
                    <tr>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'top left', 'advanced-ads-sticky' ); ?>" value="topleft" <?php checked( $assistant, 'topleft' ); ?>/></td>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'top center', 'advanced-ads-sticky' ); ?>" value="topcenter" <?php checked( $assistant, 'topcenter' ); ?>/></td>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'top right', 'advanced-ads-sticky' ); ?>" value="topright" <?php checked( $assistant, 'topright' ); ?>/></td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'center left', 'advanced-ads-sticky' ); ?>" value="centerleft" <?php checked( $assistant, 'centerleft' ); ?>/></td>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'center', 'advanced-ads-sticky' ); ?>" value="center" <?php checked( $assistant, 'center' ); ?>/></td>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'center right', 'advanced-ads-sticky' ); ?>" value="centerright" <?php checked( $assistant, 'centerright' ); ?>/></td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'bottom left', 'advanced-ads-sticky' ); ?>" value="bottomleft" <?php checked( $assistant, 'bottomleft' ); ?>/></td>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'bottom center', 'advanced-ads-sticky' ); ?>" value="bottomcenter" <?php checked( $assistant, 'bottomcenter' ); ?>/></td>
                        <td><input type="radio" name="advanced_ad[sticky][assistant]" title="<?php _e( 'bottom right', 'advanced-ads-sticky' ); ?>" value="bottomright" <?php checked( $assistant, 'bottomright' ); ?>/></td>
                    </tr>
                </table>
                <label class="description"><?php _e( 'Enter banner width to correctly center the ad.', 'advanced-ads-sticky' ); ?><br/>
                    <input type="number" name="advanced_ad[sticky][position][width]" title="<?php _e( 'banner width', 'advanced-ads-sticky' ); ?>" value="<?php echo $width; ?>"/>px</label>
            </div>
        </div>
        <div id="advads-sticky-ads-absolute">
            <p><label><input type="radio" name="advanced_ad[sticky][type]" id="advanced_ad_sticky_type_absolute" value="absolute" <?php checked( $type, 'absolute' ); ?>/><?php _e( 'Define position manually', 'advanced-ads-sticky' ); ?></label></p>
            <p class="description"><?php _e( 'Use numbers in every field you want to be considered for positioning (top, left, right, bottom of the page). Leave a field empty to not set a position for this side. The number is considered to be in pixels.', 'advanced-ads-sticky' ); ?></p>
            <table class="advads-sticky-numbers">
                <tr>
                    <td></td>
                    <td><input type="number" name="advanced_ad[sticky][position][top]" title="<?php _e( 'top', 'advanced-ads-sticky' ); ?>" value="<?php echo $top; ?>"/></td>
                    <td></td>
                </tr>
                <tr>
                    <td><input type="number" name="advanced_ad[sticky][position][left]" title="<?php _e( 'left', 'advanced-ads-sticky' ); ?> "value="<?php echo $left; ?>"/></td>
                    <td></td>
                    <td><input type="number" name="advanced_ad[sticky][position][right]" title="<?php _e( 'right', 'advanced-ads-sticky' ); ?>" value="<?php echo $right; ?>"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="number" name="advanced_ad[sticky][position][bottom]" title="<?php _e( 'bottom', 'advanced-ads-sticky' ); ?>" value="<?php echo $bottom; ?>"/></td>
                    <td></td>
                <tr>
            </table>
        </div>
        <div class='clear'></div>
    </div>
</div>
<style>
#advads-sticky-ads > div { float: left; width: 50%; min-width: 200px; }
.advads-sticky-numbers input { width: 5em;}
.advads-sticky-assistant table, .advads-sticky-numbers { border: 1px solid #ddd; }
.advads-sticky-assistant td { width: 3em; height: 2em; text-align: center; }
#advads-sticky-ads div.clear { content: ' '; display: block; float: none; clear: both; }
</style>
<script>
    jQuery('#advanced_ad_sticky_type_assistant').change(function(){
        advads_toggle_box_enable(this, '.advads-sticky-assistant');
        advads_toggle_box_enable(document.getElementById('advanced_ad_sticky_type_absolute'), '.advads-sticky-numbers');
    })
    jQuery('#advanced_ad_sticky_type_absolute').change(function(){
        advads_toggle_box_enable(this, '.advads-sticky-numbers');
        advads_toggle_box_enable(document.getElementById('advanced_ad_sticky_type_assistant'), '.advads-sticky-assistant');
    })
    // disable/enable on load
    advads_toggle_box_enable(document.getElementById('advanced_ad_sticky_type_absolute'), '.advads-sticky-numbers');
    advads_toggle_box_enable(document.getElementById('advanced_ad_sticky_type_assistant'), '.advads-sticky-assistant');
</script>