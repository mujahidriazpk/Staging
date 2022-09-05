<input type="checkbox" name="<?php echo $option_name; ?>[background]" value="1" <?php 
checked( $background, 1 ); ?> onclick="advads_toggle_box( this, '#advads-close-background-<?php echo $placement_slug; ?>' );"/>
<p class="description"><?php _e( 'Put a half-transparent overlay over the background of the page if this ad is displayed.', 'advanced-ads-layer' ); ?></p>
<div id="advads-close-background-<?php echo $placement_slug; ?>" <?php if ( ! $background ) { echo 'style="display:none;"';} ?>>
<input type="checkbox" name="<?php echo $option_name; ?>[background_click_close]" value="1" <?php checked( $background_click_close, 1 ); ?> />
<p class="description"><?php _e( 'close all ads with click on the background', 'advanced-ads-layer' ); ?></p>
</div>
