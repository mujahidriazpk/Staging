<?php
/**
 * Templace for the Group display condition.
 *
 * @var array $groups List of BuddyBoss groups.
 * @var array $values Selected groups.
 * @var string $rand Unique identifier of the current metabox.
 * @var string $name Form name attribute.
 */
?>
<input type="hidden" name="<?php echo esc_attr( $name ); ?>[type]" value="<?php echo esc_attr( $options['type'] ); ?>"/>
<div class="advads-conditions-single advads-buttonset">
<?php
foreach ( $groups as $_group_id => $_group_name ) {
	$_val   = in_array( $_group_id, $values, true ) ? 1 : 0;
	$_label = sprintf( '%s (%s)', $_group_name, $_group_id );

	$field_id = "advads-conditions-$_group_id-$rand";
	?>
	<label class="button" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $_label ); ?></label><input
		type="checkbox"
		id="<?php echo esc_attr( $field_id ); ?>"
		name="<?php echo esc_attr( $name ); ?>[value][]" <?php checked( $_val, 1 ); ?>
		value="<?php echo esc_attr( $_group_id ); ?>">
	<?php
}
