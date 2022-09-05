<p>
    <label><?php _e( 'width', 'advanced-ads-layer'  ); ?>
    <input type="number" value="<?php echo $width; ?>" name="advads[placements][<?php
    echo $placement_slug; ?>][options][placement_width]">px</label>&nbsp;

    <label><?php _e( 'height', 'advanced-ads-layer'  ); ?>
    <input type="number" value="<?php echo $height; ?>" name="advads[placements][<?php
    echo $placement_slug; ?>][options][placement_height]">px</label>
</p>
<p class="description"><?php _e( 'Needed sometimes to center the ad correctly', 'advanced-ads-layer' ); ?></p>