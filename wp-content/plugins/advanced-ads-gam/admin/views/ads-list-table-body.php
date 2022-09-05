<?php
/**
 * Table body markup for the ad lists.
 */

$network  = Advanced_Ads_Network_Gam::get_instance();
$ads_list = $network->get_external_ad_units();

?>

<?php if ( empty( $ads_list ) ) : ?>
<tr><td colspan="4"><p class="description text-center"><?php esc_html_e( 'Your account looks empty', 'advanced-ads-gam' ); ?></p></td></tr>
<?php else : ?>
	<?php

	foreach ( $ads_list as $ad_id => $unit ) :

		$unit_data = json_encode( $unit );
		$unit_id   = $unit['networkCode'] . '_' . $unit['id'];

		?>
	<tr data-unitid="<?php echo esc_attr( $unit_id ); ?>" data-unitdata="<?php echo esc_attr( json_encode( $unit ) ); ?>">
		<td><?php echo esc_html( $unit['name'] ); ?></td>
		<td><p class="description"><?php echo esc_html( $unit['description'] ); ?></p></td>
		<td><p class="description"><?php echo esc_html( $unit['adUnitCode'] ); ?></p></td>
		<td></td>
	</tr>
	<?php endforeach; ?>
<?php endif; ?>
