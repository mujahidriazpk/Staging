<?php
/**
 * Collapse empty div setting
 */

$setting = Advanced_Ads_Network_Gam::get_setting();

$choices = array(
	'default'  => esc_html__( 'Do not collapse', 'advanced-ads-gam' ),
	'collapse' => esc_html__( 'Collapse if empty', 'advanced-ads-gam' ),
	'fill'     => esc_html__( 'Fill space when ad is loaded', 'advanced-ads-gam' ),
);

?>
<?php foreach ( $choices as $key => $value ) : ?>
<p><label title="<?php echo esc_attr( $value ); ?>">
	<input type="radio" name="<?php echo esc_attr( AAGAM_SETTINGS ); ?>[empty-div]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $setting['empty-div'], $key ); ?> />
	<?php echo $value; ?>
</label></p>
<?php endforeach; ?>
