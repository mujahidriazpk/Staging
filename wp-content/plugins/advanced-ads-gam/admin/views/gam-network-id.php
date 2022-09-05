<?php
/**
 * Ad Manager ID setting
 */

$has_token     = Advanced_Ads_Network_Gam::get_instance()->is_account_connected();
$gam_option    = Advanced_Ads_Network_Gam::get_option();
$connect_nonce = wp_create_nonce( 'gam-connect' );
$has_soap      = Advanced_Ads_Gam_Admin::has_soap();
$has_soap_key  = empty( get_option( AAGAM_API_KEY_OPTION ) );
$soap_class    = ! $has_soap && $has_soap_key ? 'nosoapkey' : '';

?>
<div>
	<?php if ( $has_token ) : ?>
		<button class="button-secondary preventDefault"
				id="advads-gam-revoke"><?php esc_html_e( 'Revoke access', 'advanced-ads-gam' ); ?></button>
		<p class="desciption">
			<code>[
			<?php
				echo esc_html( $gam_option['account']['networkCode'] );
			?>
				]
				<?php
				if ( $gam_option['account']['isTest'] ) {
					echo ' (' . esc_html__( 'Test account', 'advanced-ads-gam' ) . ')';
				}
				?>
				</code>
			<strong><?php echo esc_html( $gam_option['account']['displayName'] ); ?></strong>
		</p>
	<?php else : ?>
		<?php if ( Advanced_Ads_Gam_Admin::has_valid_license() ) : ?>
		<button class="preventDefault button-primary <?php echo esc_attr( $soap_class ); ?>" data-nonce="<?php echo esc_attr( $connect_nonce ); ?>"
				id="advads-gam-connect"><?php esc_html_e( 'Connect account', 'advanced-ads-gam' ); ?></button>
		<p class="description"><?php esc_html_e( 'Connect your Google Ad Manager account', 'advanced-ads-gam' ); ?></p>
		<?php else : ?>
			<button class="preventDefault button disabled"><?php esc_html_e( 'Connect account', 'advanced-ads-gam' ); ?></button>
			<?php
			printf(
				esc_html__(
					'%1$sPlease activate %2$syour license%3$s to connect your account.%4$s',
					'advanced-ads-gam'
				),
				'<p class="advads-error-message">',
				'<a href="' . admin_url( 'admin.php?page=advanced-ads-settings#top#licenses' ) . '">',
				'</a>',
				'</p>'
			);
			?>
		<?php endif; ?>
	<?php endif; ?>
</div>
<?php if ( $has_token ) : ?>
	<?php
	// ad units list is empty, check if we can get it (API is enabled).
	if ( empty( $gam_option['ad_units'] ) ) {
		echo '<input type="hidden" value="' . esc_attr( $connect_nonce ) . '" id="gamlistisempty" />';
	}
	echo '<p class="advads-error-message" id="gamapi-not-enabled">' . esc_attr__( 'Please enable the API option in your Ad Manager account and reload this page.', 'advanced-ads-gam' ) . '&nbsp;<a href="'
		 . esc_url( ADVADS_URL ) . 'manual/google-ad-manager-integration-manual/#Enable_the_API_in_GAM">' . esc_attr__( 'Manual', 'advanced-ads-gam' ) . '</a></p>';

	echo '<p class="advads-error-message" id="gamapi-too-much-ads">' . sprintf( esc_html__( 'We found more than %d ads in your account. The connection might time out.', 'advanced-ads-gam' ), Advanced_Ads_Gam_Admin::MAX_UNIT_COUNT ) . ' ' . esc_html__( 'Please reach out to us for alternative solutions.', 'advanced-ads-gam' ) . '</p>';

	if ( ! Advanced_Ads_Gam_Admin::has_valid_license() ) {
		printf(
			esc_html__(
				'%1$sPlease re-activate %2$syour license%3$s to update the ad unit list.%4$s',
				'advanced-ads-gam'
			),
			'<p class="advads-error-message">',
			'<a href="' . admin_url( 'admin.php?page=advanced-ads-settings#top#licenses' ) . '">',
			'</a>',
			'</p>'
		);
	}

	Advanced_Ads_Gam_Importer::import_button();

	?>
<?php endif; ?>
<div id="gam-settings-overlay"><div></div></div>
