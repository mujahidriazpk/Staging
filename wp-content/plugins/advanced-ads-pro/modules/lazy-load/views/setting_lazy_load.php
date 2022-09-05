<div class="advanced-ads-inputs-dependent-on-cb" <?php if ( $cb_off ) { echo 'style="display:none;"'; } ?>>
<label title="<?php _e( 'enabled', 'advanced-ads-pro' ); ?>">
<input type="radio" name="advads[placements][<?php echo $_placement_slug; ?>][options][lazy_load]" value="enabled" <?php
	checked( $checked, 'enabled' ); ?> /><?php _e( 'enabled', 'advanced-ads-pro' ); ?>
</label>
<label title="<?php _e( 'disabled', 'advanced-ads-pro' ); ?>">
<input type="radio" name="advads[placements][<?php echo $_placement_slug; ?>][options][lazy_load]" value="disabled" <?php
	checked( $checked, 'disabled' ); ?> /><?php _e( 'disabled', 'advanced-ads-pro' ); ?>
</label>
</div>
<p class="advads-notice-inline advads-idea" <?php if ( ! $cb_off ) { echo 'style="display:none;"'; } ?>><?php _e( 'Works only with cache-busting enabled', 'advanced-ads-pro' ); ?></p>
