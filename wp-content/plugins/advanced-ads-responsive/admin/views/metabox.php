<h4><?php _e('Display by browser size', 'advanced-ads-responsive'); ?></h4>
    <p style="color: red;"><?php _e( 'The below version of the browser width check is deprecated. Please use the new version of visitor conditions above to replace it.', 'advanced-ads-responsive' ); ?></p>
    <p><label><input type="checkbox" name="advanced_ad[visitor][by-size][enable]" value="1" <?php
    checked($by_size_enable, 1); ?> onclick="advads_toggle_box(this, '#advads-responsive-ads');"/><?php _e('Display ad by browser size', 'advanced-ads-responsive'); ?></label></p>
    <div id="advads-responsive-ads"<?php if(!$by_size_enable) : ?> style="display:none;"<?php endif; ?>>
    <?php printf(__('Display ad in browsers with a size of %s px to %s px', 'advanced-ads-responsive'),
        '<input type="number" name="advanced_ad[visitor][by-size][from]" value="'.$by_size_from.'"/>',
        '<input type="number" name="advanced_ad[visitor][by-size][to]" value="'.$by_size_to.'"/>'); ?>
    <p class="description"><?php _e('On the first page view of a new visitor the browser size can not be determined in advanced. Decide here what to do in this case.', 'advanced-ads-responsive'); ?></p>
<ul id="advanced-ad-visitor-by-size-fallback">
    <li>
        <label><input type="radio" name="advanced_ad[visitor][by-size][fallback]"
               value="display" <?php checked($by_size_fallback, 'display'); ?>/>
        <?php _e('Display the ad anyway', 'advanced-ads-responsive'); ?></label>
        <label><input type="radio" name="advanced_ad[visitor][by-size][fallback]"
               value="hide" <?php checked($by_size_fallback, 'hide'); ?>/>
        <?php _e('Don’t load the ad', 'advanced-ads-responsive'); ?></label>
        <label><input type="radio" name="advanced_ad[visitor][by-size][fallback]"
               value="desktop" <?php checked($by_size_fallback, 'desktop'); ?>/>
        <?php _e('Display on desktop only', 'advanced-ads-responsive'); ?></label>
        <label><input type="radio" name="advanced_ad[visitor][by-size][fallback]"
               value="mobile" <?php checked($by_size_fallback, 'mobile'); ?>/>
        <?php _e('Display on mobile only', 'advanced-ads-responsive'); ?></label>
    </li>
</ul>
    </div>