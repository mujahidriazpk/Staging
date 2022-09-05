<?php
if ( ! defined( 'WCMP_PLUGIN_URL' ) ) {
	echo 'Direct access not allowed.';
	exit; }

// include resources
wp_enqueue_style( 'wcmp-admin-style', plugin_dir_url( __FILE__ ) . '../css/style.admin.css', array(), '1.0.175' );
wp_enqueue_script( 'wcmp-admin-js', plugin_dir_url( __FILE__ ) . '../js/admin.js', array(), '1.0.175' );

$ffmpeg_system_path = defined( 'PHP_OS' ) && strtolower( PHP_OS ) == 'linux' ? @shell_exec( 'which ffmpeg' ) : '';

$troubleshoot_default_extension = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_default_extension', false );
$force_main_player_in_title     = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_force_main_player_in_title', 1 );
$ios_controls                   = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_ios_controls', false );
$troubleshoot_onload            = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_onload', false );
$include_main_player_hook       = trim( $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_main_player_hook', '' ) );
$main_player_hook_title         = trim( $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_main_player_hook_title', 1 ) );
$include_all_players_hook       = trim( $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_all_players_hook', '' ) );

$enable_player   = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_enable_player', false );
$show_in         = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_show_in', 'all' );
$players_in_cart = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_players_in_cart', false );
$player_style    = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_player_layout', WCMP_DEFAULT_PLAYER_LAYOUT );
$volume          = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_player_volume', WCMP_DEFAULT_PLAYER_VOLUME );
$player_controls = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_player_controls', WCMP_DEFAULT_PLAYER_CONTROLS );
$player_title    = intval( $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_player_title', 1 ) );
$merge_grouped   = intval( $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_merge_in_grouped', 0 ) );
$preload         = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr(
	'_wcmp_preload',
	// This option is only for compatibility with versions previous to 1.0.28
					$GLOBALS['WooCommerceMusicPlayer']->get_global_attr( 'preload', 'none' )
);
$play_simultaneously = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_play_simultaneously', 0 );
$play_all            = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr(
	'_wcmp_play_all',
	// This option is only for compatibility with versions previous to 1.0.28
					$GLOBALS['WooCommerceMusicPlayer']->get_global_attr( 'play_all', 0 )
);
$on_cover              = intval( $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_on_cover', 0 ) );
$analytics_integration = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_analytics_integration', 'ua' );
$analytics_property    = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_analytics_property', '' );
$analytics_api_secret  = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_analytics_api_secret', '' );
$registered_only       = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_registered_only', 0 );
$fade_out              = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_fade_out', 1 );
$purchased_times_text  = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_purchased_times_text', '- purchased %d time(s)' );
?>
<h1><?php esc_html_e( 'Music Player for WooCommerce - Global Settings', 'music-player-for-woocommerce' ); ?></h1>
<div style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">
<?php
_e(
	'For reporting any issue or to request a customization, <a href="https://wordpress.dwbooster.com/contact-us" target="_blank">CLICK HERE</a><br />
	For testing the premium version of the plugin, visit the online demo:<br/> <a href="https://demos.dwbooster.com/music-player-for-woocommerce/wp-login.php" target="_blank">Administration area: Click to access the administration area demo</a><br/>
	<a href="https://demos.dwbooster.com/music-player-for-woocommerce/" target="_blank">Public page: Click to visit the WooCommerce Store</a>',
	'music-player-for-woocommerce'
);
?>
</div>
<form method="post">
	<div class="postbox">
		<h2 class="hndle" style="padding:5px;color:#DDDDDD;"><?php esc_html_e( 'Registering of Plugin', 'music-player-for-woocommerce' ); ?></h2>
		<div class="inside">
			<label style="color:#DDDDDD;"><?php esc_html_e( 'Enter the email address of buyer', 'music-player-for-woocommerce' ); ?>:</label>
			<br><input aria-label="<?php esc_attr_e( 'Buyer email', 'music-player-for-woocommerce' ); ?>" type="text" disabled> <input value="<?php esc_attr_e( 'Register', 'music-player-for-woocommerce' ); ?>" disabled class="button-primary">
		</div>
	</div>
</form>
<form method="post">
<input type="hidden" name="wcmp_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wcmp_updating_plugin_settings' ) ); ?>" />
<table class="widefat" style="border-left:0;border-right:0;border-bottom:0;padding-bottom:0;">
	<tr>
		<td>
			<div style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">
			<?php
			_e(
				'<p>The player uses the audio files associated to the product. If you want protecting the audio files for selling, tick the checkbox: <b>"Protect the file"</b>, in whose case the plugin will create a truncated version of the audio files for selling to be used for demo. The size of audio files for demo is based on the number entered through the attribute: <b>"Percent of audio used for protected playbacks"</b>.</p><p><b>Protecting the files prevents that malicious users can access to the original audio files without pay for them.</b></p>',
				'music-player-for-woocommerce'
			);
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<table class="widefat" style="border:1px solid #e1e1e1;margin-bottom:20px;">
				<tr>
					<td colspan="2"><h2><?php esc_html_e( 'General Settings', 'music-player-for-woocommerce' ); ?></h2></td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Include the players only for registered users', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Include the players only for registered users', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_registered_only" <?php print( ( $registered_only ) ? 'CHECKED' : '' ); ?> /></td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Apply fade out to playing audio when possible', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Apply fade out to playing audio when possible', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_fade_out" <?php print( ( $fade_out ) ? 'CHECKED' : '' ); ?> /></td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Text for product purchased times in playlists', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Purchased times text', 'music-player-for-woocommerce' ); ?>" type="text" name="_wcmp_purchased_times_text" value="<?php print esc_attr( $purchased_times_text ); ?>" style="width:100%;" /><br>
					<i><?php esc_html_e( 'Texts to display when the playlist shortcode includes the purchased_times attribute.<br>Ex.', 'music-player-for-woocommerce' ); ?>[wcmp-playlist products_ids="*" controls="track" purchased_times="1"]</i>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="color:red;"><?php esc_html_e( 'The general settings affect only the PRO version of the plugin', 'music-player-for-woocommerce' ); ?>. <a target="_blank" href="https://wcmp.dwbooster.com"><?php esc_html_e( 'CLICK HERE TO GET THE PRO VERSION OF THE PLUGIN', 'music-player-for-woocommerce' ); ?></a></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD;"><?php esc_html_e( 'For buyers, play the purchased audio files instead of the truncated files for demo', 'music-player-for-woocommerce' ); ?></td>
					<td style="color:#DDDDDD;">
						<input aria-label="<?php esc_attr_e( 'For buyers, play the purchased audio files instead of the truncated files for demo', 'music-player-for-woocommerce' ); ?>" type="checkbox" DISABLED />
						<?php esc_html_e( 'Reset the files', 'music-player-for-woocommerce' ); ?>
						<select aria-label="<?php esc_attr_e( 'Reset files interval', 'music-player-for-woocommerce' ); ?>" DISABLED>
							<option><?php esc_html_e( 'daily', 'music-player-for-woocommerce' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr /></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD;"><?php esc_html_e( 'Truncate the audio files for demo with ffmpeg', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Truncate the audio files for demo with ffmpeg', 'music-player-for-woocommerce' ); ?>" type="checkbox" DISABLED /></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD;"><?php esc_html_e( 'ffmpeg path', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'ffmpeg path', 'music-player-for-woocommerce' ); ?>" type="text" value="<?php print esc_attr( ! empty( $ffmpeg_system_path ) ? $ffmpeg_system_path : '' ); ?>" DISABLED style="width:100%;" /></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD"><?php esc_html_e( 'Watermark audio', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Watermark audio', 'music-player-for-woocommerce' ); ?>" type="text" DISABLED style="width: calc( 100% - 60px );"/><input type="button" class="button-secondary" value="<?php esc_attr_e( 'Select', 'music-player-for-woocommerce' ); ?>" style="float:right;" DISABLED /><br />
						<i style="color:#DDDDDD;"><?php esc_html_e( 'Select an audio file if you want to apply a watermark to the audio files for demos. The watermark will be applied to the protected demos (Experimental feature).', 'music-player-for-woocommerce' ); ?></i>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr /></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD;"><?php esc_html_e( 'Delete the demo files generated previously', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Delete the demo files generated previously', 'music-player-for-woocommerce' ); ?>" type="checkbox" DISABLED /></td>
				</tr>
				<tr>
					<td colspan="2"><hr /></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD;"><?php esc_html_e( 'Store demo files on Google Drive', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Store demo files on Google Drive', 'music-player-for-woocommerce' ); ?>" type="checkbox" disabled /></td>
				</tr>
				<tr>
					<td width="30%" style="color:#DDDDDD;"><?php esc_html_e( 'Import a JSON Key file', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'JSON Key file', 'music-player-for-woocommerce' ); ?>" type="file" disabled />
						<br /><br />
						<div style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">
							<h3>Turn on the Drive API</h3>
							<p>
								<ol>
									<li>
										Use <a href="javascript:void(0);" target="_blank" class="gc-analytics-event" data-category="Quickstart" data-action="Enable" data-label="Drive API, PHP">this wizard</a> to create or select a project in the Google Developers Console and automatically turn on the API. Click <strong>Continue</strong>, then <strong>Go to credentials</strong>.
									</li>
									<li>
										On the <strong>Add credentials to your project</strong> page, click the <strong>Cancel</strong> button.
									</li>
									<li>
										At the top of the page, select the <strong>OAuth consent screen</strong> tab. Select an
										<strong>Email address</strong>, enter a <strong>Product name</strong> if not already set, and click the <strong>Save</strong> button.
									</li>
									<li>
										Select the <strong>Credentials</strong> tab, click the <strong>Create credentials</strong> button and select <strong>OAuth client ID</strong>.
									</li>
									<li>Press the <strong>Configure consent screen</strong> button, enter the name <strong>WooCommerce Music Player</strong> in the attribute: <strong>Product name shown to users</strong>, and click the <strong>Save</strong> button.</li>
									<li>
										Select the application type <strong>Web application</strong>, enter the URL below as the <strong>Authorized redirect URIs</strong>:<br><br>
										.........
										<br><br>
										and click the <strong>Create</strong> button.
									</li>
									<li>
										Click <strong>OK</strong> to dismiss the resulting dialog.
									</li>
									<li>
										Click the <span style="display:inline-block;width:14px;height:14px;"><svg viewBox="0 0 14 14"><g><rect x="1" y="11" width="12" height="2"></rect><polygon points="7 5 12 10 2 10 " transform="translate(7.000000, 7.500000) scale(1, -1) translate(-7.000000, -7.500000) "></polygon><rect x="5" y="1" width="4" height="4"></rect></g></svg></span> (Download JSON) button to the right of the client ID.
									</li>
									<li>
										Select the file through the <strong>"Import a JSON Key file"</strong> attribute.
									</li>
								</ol>
							</p>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr /></td>
				</tr>
				<tr>
					<td colspan="2"><h2><?php esc_html_e( 'Troubleshoot Area', 'music-player-for-woocommerce' ); ?></h2></td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'On iPads and iPhones, use native controls', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'On iPads and iPhones, use native controls', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_ios_controls" <?php if ( $ios_controls ) {
							print 'CHECKED';} ?>/>
						<?php esc_html_e( 'tick the checkbox if the players do not work properly on iPads or iPhones', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'Loading players in the onload event', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Loading placeholder in the onload event', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_onload" <?php if ( $troubleshoot_onload ) {
							print 'CHECKED';} ?>/>
						<?php esc_html_e( 'tick the checkbox if the players are not being loaded properly', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'For files whose extensions cannot be determined', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'For files whose extensions cannot be determined, handle them as mp3 files', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_default_extension" <?php if ( $troubleshoot_default_extension ) {
							print 'CHECKED';} ?>/>
						<?php esc_html_e( 'handle them as mp3 files', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'For the WooCommerce Gutenberg Blocks, include the main player in the products titles', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'For the WooCommerce Gutenberg Blocks, include the main player in the products titles', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_force_main_player_in_title" <?php if ( $force_main_player_in_title ) {
							print 'CHECKED';} ?>/>
						<?php esc_html_e( 'Includes the main player in front of products titles', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'WooCommerce hook used to display the players in the shop pages', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'WooCommerce hook used to display the players in the shop pages', 'music-player-for-woocommerce' ); ?>" type="text" name="_wcmp_main_player_hook" value="<?php print esc_attr( $include_main_player_hook ); ?>" style="width:100%" /><br />
						<?php _e( 'The plugin uses by default the <b>woocommerce_shop_loop_item_title</b> hook. If the player is not being displayed, enter the hook used by the theme active on your website.', 'music-player-for-woocommerce' ); ?><br>
						<input type="checkbox" name="_wcmp_main_player_hook_title" aria-label="<?php esc_attr_e( 'Force the player in the title', 'music-player-for-woocommerce' ); ?>" <?php if ( $main_player_hook_title ) {
							print 'checked';} ?>> <?php esc_html_e( 'Forces the audio player to be displayed in the product title.', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'WooCommerce hook used to display the players in the products pages', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'WooCommerce hook used to display the players in the products pages', 'music-player-for-woocommerce' ); ?>" type="text" name="_wcmp_all_players_hook" value="<?php print esc_attr( $include_all_players_hook ); ?>" style="width:100%" /><br />
						<?php _e( 'The plugin uses by default the <b>woocommerce_single_product_summary</b> hook. If the player is not being displayed, enter the hook used by the theme active on your website.', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php _e( 'Click on the <a href="https://docs.woocommerce.com/wc-apidocs/hook-docs.html" target="_blank">THIS LINK</a> for the list of available <a href="https://docs.woocommerce.com/wc-apidocs/hook-docs.html" target="_blank" style="font-weight:bold;font-size:1.3em;">WooCommerce Hooks</a>', 'music-player-for-woocommerce' ); ?></td>
				</tr>
			</table>
			<table class="widefat wcmp-player-settings" style="border:1px solid #e1e1e1;">
				<tr>
					<td width="30%"><?php esc_html_e( 'Include music player in all products', 'music-player-for-woocommerce' ); ?></td>
					<td><div class="wcmp-tooltip"><span class="wcmp-tooltiptext"><?php esc_html_e( 'The player is shown only if the product is "downloadable" with at least an audio file between the "Downloadable files", or you have selected your own audio files', 'music-player-for-woocommerce' ); ?></span><input aria-label="<?php esc_attr_e( 'Enable player', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_enable_player" <?php echo ( ( $enable_player ) ? 'checked' : '' ); ?> /></div></td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Include in', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Products pages only', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_show_in" value="single" <?php echo ( ( 'single' == $show_in ) ? 'checked' : '' ); ?> />
						<?php _e( 'single-entry pages <i>(Product\'s page only)</i>', 'music-player-for-woocommerce' ); ?><br />

						<input aria-label="<?php esc_attr_e( 'Multiple-entry pages', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_show_in" value="multiple" <?php echo ( ( 'multiple' == $show_in ) ? 'checked' : '' ); ?> />
						<?php _e( 'multiple entries pages <i>(Shop pages, archive pages, but not in the product\'s page)</i>', 'music-player-for-woocommerce' ); ?><br />

						<input aria-label="<?php esc_attr_e( 'Product and multiple-entry pages', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_show_in" value="all" <?php echo ( ( 'all' == $show_in ) ? 'checked' : '' ); ?> />
						<?php _e( 'all pages <i>(with single or multiple-entries)</i>', 'music-player-for-woocommerce' ); ?>
					</td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Include players in cart', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Include players in cart', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_players_in_cart" <?php echo ( ( $players_in_cart ) ? 'checked' : '' ); ?> />
					</td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Merge in grouped products', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Merge in grouped products', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_merge_in_grouped" <?php echo ( ( $merge_grouped ) ? 'checked' : '' ); ?> /><br /><em><?php esc_html_e( 'In grouped products, display the "Add to cart" buttons and quantity fields in the players rows', 'music-player-for-woocommerce' ); ?></em></td>
				</tr>
				<tr>
					<td valign="top" width="30%"><?php esc_html_e( 'Player layout', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<table>
							<tr>
								<td><input aria-label="<?php esc_attr_e( 'Skin 1', 'music-player-for-woocommerce' ); ?>" name="_wcmp_player_layout" type="radio" value="mejs-classic" <?php echo ( ( 'mejs-classic' == $player_style ) ? 'checked' : '' ); ?> /></td>
								<td><img alt="<?php esc_attr_e( 'Skin 1', 'music-player-for-woocommerce' ); ?>" src="<?php print esc_url( WCMP_PLUGIN_URL ); ?>/views/assets/skin1.png" /></td>
							</tr>

							<tr>
								<td><input aria-label="<?php esc_attr_e( 'skin 2', 'music-player-for-woocommerce' ); ?>" name="_wcmp_player_layout" type="radio" value="mejs-ted" <?php echo ( ( 'mejs-ted' == $player_style ) ? 'checked' : '' ); ?> /></td>
								<td><img alt="<?php esc_attr_e( 'Skin 2', 'music-player-for-woocommerce' ); ?>" src="<?php print esc_url( WCMP_PLUGIN_URL ); ?>/views/assets/skin2.png" /></td>
							</tr>

							<tr>
								<td><input aria-label="<?php esc_attr_e( 'Skin 3', 'music-player-for-woocommerce' ); ?>" name="_wcmp_player_layout" type="radio" value="mejs-wmp" <?php echo ( ( 'mejs-wmp' == $player_style ) ? 'checked' : '' ); ?> /></td>
								<td><img alt="<?php esc_attr_e( 'Skin 3', 'music-player-for-woocommerce' ); ?>" src="<?php print esc_url( WCMP_PLUGIN_URL ); ?>/views/assets/skin3.png" /></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'Preload', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<label><input aria-label="<?php esc_attr_e( 'Preload - none', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_preload" value="none" <?php if ( 'none' == $preload ) {
							echo 'CHECKED';} ?> /> None</label><br />
						<label><input aria-label="<?php esc_attr_e( 'Preload - metadata', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_preload" value="metadata" <?php if ( 'metadata' == $preload ) {
							echo 'CHECKED';} ?> /> Metadata</label><br />
						<label><input aria-label="<?php esc_attr_e( 'Preload - auto', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_preload" value="auto" <?php if ( 'auto' == $preload ) {
							echo 'CHECKED';} ?> /> Auto</label><br />
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'Play all', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Play all', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_play_all" <?php if ( $play_all ) {
							echo 'CHECKED';} ?> />
					</td>
				</tr>
				<tr>
					<td width="30%">
						<?php esc_html_e( 'Allow multiple players to play simultaneously', 'music-player-for-woocommerce' ); ?>
					</td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Allow multiple players to play simultaneously', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_play_simultaneously" <?php if ( $play_simultaneously ) {
							echo 'CHECKED';} ?> /><br />
						<i><?php
							esc_html_e( 'By default, only one player would be playing at once. By pressing the play button of a player, the other players would stop. By ticking the checkbox, multiple players could play simultaneously.', 'music-player-for-woocommerce' );
						?></i>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Player volume (from 0 to 1)', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Player volume', 'music-player-for-woocommerce' ); ?>" type="number" name="_wcmp_player_volume" min="0" max="1" step="0.01" value="<?php echo esc_attr( $volume ); ?>" />
					</td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Player controls', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Play/pause button', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_player_controls" value="button" <?php echo ( ( 'button' == $player_controls ) ? 'checked' : '' ); ?> /> <?php esc_html_e( 'the play/pause button only', 'music-player-for-woocommerce' ); ?><br />
						<input aria-label="<?php esc_attr_e( 'All controls', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_player_controls" value="all" <?php echo ( ( 'all' == $player_controls ) ? 'checked' : '' ); ?> /> <?php esc_html_e( 'all controls', 'music-player-for-woocommerce' ); ?><br />
						<input aria-label="<?php esc_attr_e( 'Depending on context', 'music-player-for-woocommerce' ); ?>" type="radio" name="_wcmp_player_controls" value="default" <?php echo ( ( 'default' == $player_controls ) ? 'checked' : '' ); ?> /> <?php esc_html_e( 'the play/pause button only, or all controls depending on context', 'music-player-for-woocommerce' ); ?>
						<div class="wcmp-on-cover" style="margin-top:10px;">
							<input aria-label="<?php esc_attr_e( 'On cover', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_player_on_cover" value="default" <?php
							echo ( ( ! empty( $on_cover ) && ( 'button' == $player_controls || 'default' == $player_controls ) ) ? 'checked' : '' );
							?> />
							<?php esc_html_e( 'for play/pause button players display them on cover images.', 'music-player-for-woocommerce' ); ?>
							<i>
							<?php
							esc_html_e( '(This feature is experimental, and will depend on the theme active on the website.)', 'music-player-for-woocommerce' );
							?>
							</i>
						</div>
					</td>
				</tr>
				<tr>
					<td width="30%"><?php esc_html_e( 'Display the player\'s title', 'music-player-for-woocommerce' ); ?></td>
					<td>
						<input aria-label="<?php esc_attr_e( 'Display the player title', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_player_title" <?php echo ( ( ! empty( $player_title ) ) ? 'checked' : '' ); ?> />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="color:red;"><?php esc_html_e( 'The security feature is only available in the PRO version of the plugin', 'music-player-for-woocommerce' ); ?>. <a target="_blank" href="https://wcmp.dwbooster.com"><?php esc_html_e( 'CLICK HERE TO GET THE PRO VERSION OF THE PLUGIN', 'music-player-for-woocommerce' ); ?></a></td>
				</tr>
				<tr>
					<td style="color:#DDDDDD;" width="30%"><?php esc_html_e( 'Protect the file', 'music-player-for-woocommerce' ); ?></td>
					<td><input aria-label="<?php esc_attr_e( 'Protect the file', 'music-player-for-woocommerce' ); ?>" type="checkbox" DISABLED /></td>
				</tr>
				<tr valign="top">
					<td style="color:#DDDDDD;" width="30%"><?php esc_html_e( 'Percent of audio used for protected playbacks', 'music-player-for-woocommerce' ); ?></td>
					<td style="color:#DDDDDD;">
						<input aria-label="<?php esc_attr_e( 'Percent of audio used for protected playbacks', 'music-player-for-woocommerce' ); ?>" type="number" DISABLED /> % <br />
						<em><?php esc_html_e( 'To prevent unauthorized copying of audio files, the files will be partially accessible', 'music-player-for-woocommerce' ); ?></em>
					</td>
				</tr>
				<tr valign="top">
					<td style="color:#DDDDDD;" width="30%">
						<?php esc_html_e( 'Text to display beside the player explaining that demos are partial versions of the original files', 'music-player-for-woocommerce' ); ?>
					<td style="color:#DDDDDD;">
						<textarea aria-label="<?php esc_attr_e( 'Explaining that demos are partial versions of the original files', 'music-player-for-woocommerce' ); ?>" style="width:100%;" rows="4" disabled></textarea>
					</td>
				</tr>

			</table>
		</td>
	</tr>
</table>
<table class="widefat" style="border:0;">
	<tr>
		<td>
			<table class="widefat" style="border:1px solid #e1e1e1;">
				<tr>
					<td>
						<div><?php esc_html_e( 'Scope', 'music-player-for-woocommerce' ); ?></div>
						<div><div class="wcmp-tooltip"><span class="wcmp-tooltiptext"><?php esc_html_e( 'Ticking the checkbox the previous settings are applied to all products, even if they have a player enabled.', 'music-player-for-woocommerce' ); ?></span><input aria-label="<?php esc_attr_e( 'Apply the previous settings to all products', 'music-player-for-woocommerce' ); ?>" type="checkbox" name="_wcmp_apply_to_all_players" /></div> <?php esc_html_e( 'Apply the previous settings to all products pages in the website.', 'music-player-for-woocommerce' ); ?></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table class="widefat" style="border:0;">
	<tr>
		<td>
			<table class="widefat" style="border:1px solid #e1e1e1;">
				<tr>
					<td><h2><?php esc_html_e( 'Analytics', 'music-player-for-woocommerce' ); ?></h2></td>
				</tr>
				<tr>
					<td>
						<p><?php esc_html_e( 'Allows the integration with Google Analytics for registering new events when the songs are played. The event information would include: the URL to the audio file as the event label and the product\'s id as its value.', 'music-player-for-woocommerce' ); ?></p>
						<p style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;"><b><?php esc_html_e( 'Note', 'music-player-for-woocommerce' ); ?></b>: <?php esc_html_e( 'If the preload option is configured as Metadata or Auto in the players settings, the event would be registered when the audio file is loaded by the player and not exactly when they are playing.', 'music-player-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr>
					<td>
						<label><input type="radio" name="_wcmp_analytics_integration" value="ua" <?php print 'ua' == $analytics_integration ? 'CHECKED' : ''; ?>> <?php esc_html_e( 'Universal Analytics', 'music-player-for-woocommerce' ); ?></label>
						<label style="margin-left:30px;"><input type="radio" name="_wcmp_analytics_integration" value="g" <?php print 'g' == $analytics_integration ? 'CHECKED' : ''; ?>> <?php esc_html_e( 'Measurement Protocol (Google Analytics 4)', 'music-player-for-woocommerce' ); ?></label>
					</td>
				</tr>
				<tr>
					<td>
						<div><?php esc_html_e( 'Measurement Id', 'music-player-for-woocommerce' ); ?></div>
						<div><input aria-label="<?php esc_attr_e( 'Measurement Id', 'music-player-for-woocommerce' ); ?>" type="text" name="_wcmp_analytics_property" value="<?php print esc_attr( $analytics_property ); ?>" style="width:100%;" placeholder=""></div>
					</td>
				</tr>
				<tr class="wcmp-analytics-g4" style="display:<?php print esc_attr( 'ua' == $analytics_integration ? 'none' : 'table-row' ); ?>;">
					<td style="width:100%;">
						<div><?php esc_html_e( 'API Secret', 'music-player-for-woocommerce' ); ?></div>
						<div><input aria-label="<?php esc_attr_e( 'API Secret', 'music-player-for-woocommerce' ); ?>" type="text" name="_wcmp_analytics_api_secret" value="<?php print esc_attr( $analytics_api_secret ); ?>" style="width:100%;"></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table class="widefat" style="border:0;">
	<tr>
		<td>
			<table class="widefat" style="border:1px solid #e1e1e1;">
				<tr>
					<td colspan="2"><h2><?php esc_html_e( 'Add ons', 'music-player-for-woocommerce' ); ?></h2></td>
				</tr>
				<?php do_action( 'wcmp_addon_general_settings' ); ?>
			</table>
		</td>
	</tr>
</table>
<div style="margin-top:20px;"><input type="submit" value="<?php esc_attr_e( 'Save settings', 'music-player-for-woocommerce' ); ?>" class="button-primary" /></div>
</form>
<script>jQuery(window).on('load', function(){
	var $ = jQuery;
	function coverSection()
	{
		var v = $('[name="_wcmp_player_controls"]:checked').val(),
			c = $('.wcmp-on-cover');
		if(v == 'default' || v == 'button') c.show();
		else c.hide();
	};
	$(document).on('change', '[name="_wcmp_player_controls"]', function(){
		coverSection();
	});
	$(document).on('change', '[name="_wcmp_analytics_integration"]', function(){
		var v = $('[name="_wcmp_analytics_integration"]:checked').val();
		$('.wcmp-analytics-g4').css('display', v == 'g' ? 'table-row' : 'none');
		$('[name="_wcmp_analytics_property"]').attr('placeholder', v == 'g' ? 'G-XXXXXXXX' : 'UA-XXXXX-Y');
	});
	$('[name="_wcmp_analytics_integration"]:eq(0)').change();
	coverSection();
});</script>
<style>.wcmp-player-settings tr td:first-child{width:225px;}</style>
