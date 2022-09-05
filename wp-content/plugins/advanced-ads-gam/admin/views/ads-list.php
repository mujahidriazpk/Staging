<?php
/**
 * Ad unit list in the ad parameters meta box
 */

$network    = Advanced_Ads_Network_Gam::get_instance();
$ads_list   = $network->get_external_ad_units();
$gam_option = Advanced_Ads_Network_Gam::get_option();
global $post;
$ad_unit_data = false;

if ( $post && $post->post_content ) {
	$ad_unit_data = $network->post_content_to_adunit( $post->post_content );
}

?>

<?php if ( ! $network->is_account_connected() ) : ?>
	<?php if ( $ad_unit_data ) : ?>
	<table class="widefat" id="advads-gam-notconnected-table">
		<thead>
			<th><?php esc_html_e( 'Name', 'advanced-ads-gam' ); ?></th>
			<th><?php esc_html_e( 'Description', 'advanced-ads-gam' ); ?></th>
			<th><?php esc_html_e( 'Ad Unit Code', 'advanced-ads-gam' ); ?></th>
		</thead>
		<tbody>
			<tr>
				<td><?php echo esc_html( $ad_unit_data['name'] ); ?></td>
				<td><p class="description"><?php echo esc_html( $ad_unit_data['description'] ); ?></p></td>
				<td><p class="description"><?php echo esc_html( $ad_unit_data['adUnitCode'] ); ?></p></td>
			</tr>
		</tbody>
	</table>
	<div id="advads-gam-notconnected-notice">
		<p><?php esc_html_e( 'You need to connect to a Google Ad Manager account', 'advanced-ads-gam' ); ?></p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=advanced-ads-settings#top#gam' ) ); ?>" class="button-primary"><?php esc_html_e( 'Connect', 'advamced-ads-gam' ); ?></a>
	</div>
	<?php else : ?>
	<p>
		<em><?php esc_html_e( 'You need to connect to a Google Ad Manager account', 'advanced-ads-gam' ); ?></em>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=advanced-ads-settings#top#gam' ) ); ?>" class="button-primary"><?php esc_html_e( 'Connect', 'advamced-ads-gam' ); ?></a>
	</p>
	<?php endif; ?>
<?php else : ?>
<div id="advads-gam-table-head">
	<span><?php esc_html_e( 'Name', 'advanced-ads-gam' ); ?></span>
	<span><?php esc_html_e( 'Description', 'advanced-ads-gam' ); ?></span>
	<span><?php esc_html_e( 'Ad Unit Code', 'advanced-ads-gam' ); ?></span>
	<span class="update-icon nopadding"><i class="dashicons dashicons-update 
	<?php
	if ( ! Advanced_Ads_Gam_Admin::has_valid_license() ) {
		echo 'disabled';}
	?>
	"></i></span>
</div>
<div id="advads-gam-table-wrap">
	<table id="advads-gam-table" class="widefat striped" data-nonce="<?php echo esc_attr( wp_create_nonce( 'gam-selector' ) ); ?>" data-adcount="<?php echo count( $ads_list ); ?>">
		<thead>
			<th><?php esc_html_e( 'Name', 'advanced-ads-gam' ); ?></th>
			<th><?php esc_html_e( 'Description', 'advanced-ads-gam' ); ?></th>
			<th><?php esc_html_e( 'Ad Unit Code', 'advanced-ads-gam' ); ?></th>
			<th class="update-icon"><i class="dashicons dashicons-update 
			<?php
			if ( ! Advanced_Ads_Gam_Admin::has_valid_license() ) {
				echo 'disabled';}
			?>
			"></i></th>
		</thead>
		<tbody><?php require_once AAGAM_BASE_PATH . 'admin/views/ads-list-table-body.php'; ?></tbody>
	</table>
	<p id="advads-gam-current-unit-updated" class="advads-error-message hidden">
		<?php esc_html_e( 'The selected ad unit has changed in your GAM account. Please re-save this ad to apply the new changes.', 'advanced-ads-gam' ); ?>
		<button class="button-primary"><?php esc_html_e( 'Update ad', 'advanced-ads-gam' ); ?></button>
	</p>
</div>
	<?php if ( isset( $ad_unit_data['networkCode'] ) && $ad_unit_data['networkCode'] != $gam_option['account']['networkCode'] ) : ?>
<div id="advads-gam-netcode-mismatch">
	<p><?php esc_html_e( 'The selected ad is not from the currently connected account. You can still use it though.', 'advanced-ads-gam' ); ?></p>
	<p><strong><?php esc_html_e( 'Network code', 'advanced-ads-gam' ); ?>:</strong>&nbsp;<code><?php echo esc_html( $ad_unit_data['networkCode'] ); ?></code>
	<strong><?php esc_html_e( 'Ad unit name', 'advanced-ads-gam' ); ?>:</strong>&nbsp;<code><?php echo esc_html( $ad_unit_data['name'] ); ?></code></p>
</div>
	<?php endif; ?>
<div id="advads-gam-ads-list-overlay"><div><div><div><img alt="loading" src="<?php echo esc_url( AAGAM_BASE_URL . 'admin/img/loader.gif' ); ?>" /></div></div></div></div>
<?php endif; ?>
