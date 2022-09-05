<p>
<?php if ( false !== $width ) : ?>
    <label><?php _e( 'width', 'advanced-ads-sticky'  ); ?>
    <input type="number" value="<?php echo $width; ?>" name="advads[placements][<?php echo $placement_slug; ?>][options][placement_width]">px</label>
<?php endif; ?>&nbsp;
<?php if ( false !== $height ) : ?>
    <label><?php _e( 'height', 'advanced-ads-sticky'  ); ?>
    <input type="number" value="<?php echo $height; ?>" name="advads[placements][<?php echo $placement_slug; ?>][options][placement_height]">px</label>
<?php endif; ?>
</p>
<p class="description"><?php _e( 'Needed in case the ad does not center correctly', 'advanced-ads-sticky' ); ?></p>