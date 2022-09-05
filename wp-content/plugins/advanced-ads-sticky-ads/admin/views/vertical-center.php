<label><input type="checkbox" name="advads[placements][<?php echo $placement_slug; ?>][options][sticky_center_vertical]" value="1" <?php
if ( isset($placement['options']['sticky_center_vertical']) ) { checked( $placement['options']['sticky_center_vertical'], 1 ); }
?>/><?php _e( 'center vertically', 'advanced-ads-sticky' ); ?></label>