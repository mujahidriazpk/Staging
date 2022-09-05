<?php
/**
 * Responsive Thickbox Settings Functions
 *
 * @package     responsive-thickbox
 * @subpackage  Includes
 * @copyright   Copyright (c) 2016, Lyquidity Solutions Limited
 * @License:	GNU Version 2 or Any Later Version
 * @since       1.0
 */

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Use this filter to let WordPress know it's OK for someone with responsive_thickbox rights to save options
add_filter( "option_page_capability_responsive_thickbox_settings", '\lyquidity\responsive_thickbox\responsive_thickbox_settings_capability' );
function responsive_thickbox_settings_capability( $capability )
{
	return RESPONSIVE_THICKBOX_CAPABILITY;
}

function responsive_thickbox_settings()
{
	// To use this functionality, the user must have been given rights and 
	// the user's email address must belong to one of the groups.
	settings_errors( 'responsive-thickbox-notices' );

	$tabs = responsive_thickbox_get_settings_tabs();
	$tabKeys = array_keys( $tabs );

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET[ 'tab' ] : reset( $tabKeys );

	ob_start();
?>

	<div class="wrap">
		<div id="tab_container">
			<input type="hidden" name="responsive_thickbox_action" value="responsive_thickbox_download_commands">
			<?php 
				wp_nonce_field( "responsivethickbox", "responsivethickbox-nonce" ); 
				advert( 'responsive-thickbox', RESPONSIVE_THICKBOX_VERSION, function() use( $active_tab ) {
				?>
					<table class="form-table">
					<?php 
					if ( isset( $_REQUEST['message'] ) ) { 
						echo "<th/><td><p class=\"message\">{$_REQUEST['message']}</p></td>";
					}
					do_settings_fields( 'responsive_thickbox_settings_' . $active_tab, 'responsive_thickbox_settings_' . $active_tab );
					?>
					</table>
				<?php
				});
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve settings tabs
 *
 * @since 1.0
 * @return array $tabs
 */
function responsive_thickbox_get_settings_tabs() {

	$current_user = wp_get_current_user();
	$test = $current_user && $current_user->get('user_email');

	$tabs = array();
	$tabs['responsivethickbox']			= __( 'General', 'responsive-thickbox' );

	return apply_filters( 'responsive_thickbox_settings_tabs', $tabs );
}

?>