<?php
/**
 * Ad importer modal frame
 */

$importable_count = Advanced_Ads_Gam_Importer::get_instance()->has_importable();

$description = __( 'You can choose which ad units to import.', 'advanced-ads-gam' );
if ( 0 !== $importable_count['imported'] && 0 !== $importable_count['importable'] ) {
	$_description = sprintf( _n( 'One ad unit is not yet imported.', '%s ad units are not yet imported.', $importable_count['importable'], 'advanced-ads-gam' ), number_format_i18n( $importable_count['importable'] ) );
	if ( 1 === $importable_count['importable'] ) {
		$description = $_description;
	} else {
		$description = $_description . ' ' . $description;
	}
}

?>
<br />
<p><label for="gam-open-importer"><strong><?php esc_attr_e( 'Import ad units from your account', 'advanced-ads-gam' ); ?></strong></label></p>
<p class="description"><?php echo esc_html( $description ); ?></p>
<a href="#modal-gam-import" id="gam-open-importer" data-import-text="<?php esc_attr_e( 'Import', 'advanced-ads-gam' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'gam-importer' ) ); ?>" class="button"><?php esc_html_e( 'Import', 'advanced-ads-gam' ); ?></a>
<?php

$modal_slug    = 'gam-import';
$modal_content = '<p class="centered"><img alt="..." src="' . esc_url( ADVADS_BASE_URL . 'admin/assets/img/loader.gif' ) . '" /></p>';
