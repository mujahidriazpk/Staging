<?php
/**
 * Render position option for the BuddyPress Content placement
 *
 * @var string $placement_slug slug of the placement
 * @var array $buddypress_positions available positions (hooks)
 * @var string $current currently selected position
 * @var int $index value of index option
 * @var string $activity_type Activity Type
 * @var bool $hook_repeat Whether to repeat the hook
 */
?><div class="advads-option">
<span><?php esc_html_e( 'position', 'advanced-ads-pro' ); ?></span>
	<div>
	<?php if ( Advanced_Ads_Pro_Module_BuddyPress::is_legacy_theme() ) : ?>
		<select name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][buddypress_hook]">
			<?php foreach ( $buddypress_positions as $_group => $_positions ) : ?>
					<optgroup label="<?php echo esc_html( $_group ); ?>">
					<?php foreach ( $_positions as $_position_key => $_position_title ) : ?>
						<option value="<?php echo esc_attr( $_position_key ); ?>" <?php selected( $_position_key, $current ); ?>><?php echo esc_html( $_position_title ); ?></option>
					<?php endforeach; ?>
					</optgroup>
			<?php endforeach; ?>
		</select>
	<?php endif; ?>
		<p>
		<?php
		printf(
			// translators: %s is an HTML input element.
			esc_html__( 'Inject after %s. entry', 'advanced-ads-pro' ),
			'<input type="number" required="required" min="1" name="advads[placements][' . esc_attr( $placement_slug ) . '][options][pro_buddypress_pages_index]" value="' . absint( $index ) . '"/>'
		);
		?>
		</p>

		<p>
		<label><input type="checkbox" name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][hook_repeat]" value="1" <?php checked( $hook_repeat ); ?>><?php esc_html_e( 'repeat the position', 'advanced-ads-pro' ); ?></label>
		</p>
	</div>
</div>

<?php if ( ! Advanced_Ads_Pro_Module_BuddyPress::is_legacy_theme() ) : ?>
<div class="advads-option">
	<span><?php esc_html_e( 'Stream', 'advanced-ads-pro' ); ?></span>
	<div class="advads-buddypress-placement-activity">
		<label><input name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][activity_type]" value="any" type="radio" <?php checked( $activity_type, 'any' ); ?>><?php esc_html_e( 'any', 'advanced-ads-pro' ); ?></label>
		<label><input name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][activity_type]" value="sitewide" type="radio" <?php checked( $activity_type, 'sitewide' ); ?>><?php esc_html_e( 'activity stream', 'advanced-ads-pro' ); ?></label>
		<label><input name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][activity_type]" value="group" type="radio" <?php checked( $activity_type, 'group' ); ?>><?php esc_html_e( 'group feed', 'advanced-ads-pro' ); ?></label>
		<label><input name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][activity_type]" value="member" type="radio" <?php checked( $activity_type, 'member' ); ?>><?php esc_html_e( 'member timeline', 'advanced-ads-pro' ); ?></label>
	</div>
</div>
<?php endif; ?>
