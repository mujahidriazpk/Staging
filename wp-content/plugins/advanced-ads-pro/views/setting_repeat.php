<p><label><input type="checkbox" name="advads[placements][<?php echo $_placement_slug; ?>][options][repeat]" value="1" <?php
if ( isset( $_placement['options']['repeat'] ) ) { checked( $_placement['options']['repeat'], 1 ); }
?>/><?php _e( 'repeat the position', 'advanced-ads-pro' ); ?></label></p>
