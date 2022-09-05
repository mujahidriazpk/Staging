<?php
/**
 * Ad parameter meta box markup
 *
 * @var string $ad_content content of the ad.
 * @var array $ad_sizes_header headers for the Ad sizes table.
 * @var array $ad_sizes_rows options for the Ad sizes table.
 */
$ad_content = ( $ad->content ) ? trim( $ad->content ) : '';
$network    = Advanced_Ads_Network_Gam::get_instance();
$update_age = $network->get_list_update_age();

// fluid size in ad unit data.
$has_fluid_size = false;

// Position left or right.
$is_floated = false;
$ad_sizes   = self::get_ad_unit_sizes( $ad );

$amp_ad_sizes = array();

if ( isset( $ad->options()['output']['amp-ad-sizes'] ) ) {
	$amp_ad_sizes = $ad->options()['output']['amp-ad-sizes'];
} elseif ( ! isset( $ad->options()['output'] ) || ! isset( $ad->options()['output']['amp-has-sizes'] ) ) {
	$amp_ad_sizes = is_array( $ad_sizes ) ? array_keys( $ad_sizes ) : array();
}

if ( isset( $ad->options()['output'] ) ) {
	if ( isset( $ad->options()['output']['ad-sizes'] ) && isset( $ad->options()['output']['ad-sizes'][0]['sizes'] ) ) {
		$has_fluid_size = in_array( 'fluid', $ad->options()['output']['ad-sizes'][0]['sizes'] );
	}
	$is_floated = isset( $ad->options()['output']['position'] ) && in_array( $ad->options()['output']['position'], array( 'left', 'right' ) );
}

?>
<input type="hidden" name="advanced_ad[content]" value="<?php echo esc_attr( $ad_content ); ?>" />
<?php $network->print_external_ads_list(); ?>
<script type="text/javascript">
var AAGAM = new AdvanvedAdsNetworkGam( 'gam' );
AdvancedAdsAdmin.AdImporter.setup( AAGAM )
</script>
<p id="advads-gam-list-update-time">
	<span><?php echo esc_html( $network->get_last_update_string() ); ?></span>
	<?php if ( false === $update_age || ( is_int( $update_age ) && 60 < $update_age ) ) : ?>
	<span class="gam-update-icon
		<?php
		if ( ! Advanced_Ads_Gam_Admin::has_valid_license() ) {
			echo 'disabled';}
		?>
	"><i class="dashicons dashicons-update"></i></span>
	<?php endif; ?>
<p>
<?php
if ( ! Advanced_Ads_Gam_Admin::has_valid_license() ) {
	printf(
		esc_html__(
			'%1$sPlease re-activate %2$syour license%3$s to update the ad unit list.%4$s',
			'advanced-ads-gam'
		),
		'<p class="advads-error-message">',
		'<a href="' . admin_url( 'admin.php?page=advanced-ads-settings#top#licenses' ) . '" target="_blank">',
		'</a>',
		'</p>'
	);
}
?>
<div class="advads-option-list">
	<span class="label"><?php esc_html_e( 'Ad sizes', 'advanced-ads-gam' ); ?></span>
	<div id="advads-gam-ad-sizes" class="advads-ad-parameters-option-list">
		<div class="advads-gam-ad-sizes-table-container"><span class="advads-loader"></span></div>
	<?php require AAGAM_BASE_PATH . 'admin/views/ad-sizes/responsive-sizes.php'; ?>
	</div>
	<p class="advads-gam-ad-sizes-notice-missing-sizes hidden"><?php esc_html_e( 'This ad unit does not have any ad sizes', 'advanced-ads-gam' ); ?></p>
	<?php require AAGAM_BASE_PATH . 'admin/views/ad-sizes/table.php'; ?>
	<?php require AAGAM_BASE_PATH . 'admin/views/ad-sizes/table-row.php'; ?>
	<?php require AAGAM_BASE_PATH . 'admin/views/ad-sizes/amp.php'; ?>
	<script>
		advads_gam_stored_ad_sizes_json = <?php echo $this->get_ad_sizes_json_string( $ad ); ?>;
		advads_gam_amp =
		<?php
		echo wp_json_encode(
			array(
				'sizes'  => $amp_ad_sizes,
				'hasAMP' => Advanced_Ads_Checks::active_amp_plugin(),
			)
		);
		?>
		;
	</script>
</div>
<?php if ( $has_fluid_size && $is_floated ) : ?>
<p class="advads-error-message clear"><?php esc_html_e( 'Fluid sizes cannot be aligned left or right. Please choose another option for Position.', 'advanced-ads-gam' ); ?></p>
<?php endif; ?>
<?php require_once AAGAM_BASE_PATH . 'admin/views/key-value.php'; ?>
<hr/>
