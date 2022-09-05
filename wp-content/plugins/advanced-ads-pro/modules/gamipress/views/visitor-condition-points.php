<?php
/**
 * View for GamiPress points visitor condition.
 *
 * @var string $type                 The visitor condition type.
 * @var string $name                 Unique input name.
 * @var string $operator             The comparison operator.
 * @var array  $condition            Details for the condition.
 * @var array  $points               GamiPress points types.
 * @var array  $selected_points_type The currently selected points type.
 */
?>
<input type="hidden" name="<?php echo esc_attr( $name ); ?>[type]" value="<?php echo esc_attr( $type ); ?>"/>
<div class="advads-conditions-single">
	<div class="advads-conditions-select-wrap">
		<select name="<?php echo esc_attr( $name ); ?>[points]">
			<option value="" disabled <?php selected( 0, $selected_points_type ); ?>><?php esc_attr_e( 'Choose Points Type', 'advanced-ads-pro' ); ?></option>
			<?php foreach ( $points as $points_id => $label ) : ?>
				<option value="<?php echo (int) $points_id; ?>" <?php selected( $points_id, $selected_points_type ); ?>><?php echo esc_attr( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<select name="<?php echo esc_attr( $name ); ?>[operator]">
		<option value="is_equal" <?php selected( 'is_equal', $operator ); ?>>
			<?php esc_html_e( 'is equal', 'advanced-ads' ); ?>
		</option>
		<option value="is_higher" <?php selected( 'is_higher', $operator ); ?>>
			<?php esc_html_e( 'is higher or equal', 'advanced-ads' ); ?>
		</option>
		<option value="is_lower" <?php selected( 'is_lower', $operator ); ?>>
			<?php esc_html_e( 'is lower or equal', 'advanced-ads' ); ?>
		</option>
	</select>

	<input type="number" name="<?php echo esc_attr( $name ); ?>[value]" value="<?php echo (int) $value; ?>" min="0">


	<p class="description"><?php echo esc_html( $condition['description'] ); ?></p>
</div>
