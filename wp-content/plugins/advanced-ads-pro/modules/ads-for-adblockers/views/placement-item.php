<div class="advanced-ads-inputs-dependent-on-cb" <?php if ( $cb_off ) { echo 'style="display:none;"'; } ?>>
<select id="advads-placements-item-adblocker-<?php echo $_placement_slug; ?>" name="advads[placements][<?php echo $_placement_slug; ?>][options][item_adblocker]">
	<option value=""><?php _e( '--not selected--', 'advanced-ads' ); ?></option>
	<?php if ( isset( $items['ads'] ) ) : ?>
	<optgroup label="<?php _e( 'Ads', 'advanced-ads' ); ?>">
	<?php foreach ( $items['ads'] as $_item_id => $_item_title ) : ?>
		<option value="<?php echo $_item_id; ?>" <?php if ( isset( $_placement['options']['item_adblocker'] ) ) {
			selected( $_item_id, $_placement['options']['item_adblocker'] ); } ?>><?php echo $_item_title; ?></option>
	<?php endforeach; ?>
	</optgroup>
	<?php endif; ?>
</select>
	<?php if ( $messages ) : ?>
		<?php foreach ( $messages as $_message) : ?>
			<p class="advads-notice-inline advads-error">
				<?php echo esc_html( $_message ); ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<p class="advads-notice-inline advads-idea" <?php if ( ! $cb_off ) { echo 'style="display:none;"'; } ?>><?php _e( 'Works only with cache-busting enabled', 'advanced-ads-pro' ); ?></p>

