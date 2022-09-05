<?php
if( !defined( 'WCMP_PLUGIN_URL' ) ) { echo 'Direct access not allowed.';  exit; }

// include resources
wp_enqueue_style( 'wcmp-admin-style', plugin_dir_url(__FILE__).'../../css/style.admin.css', array(), '5.0.34' );
wp_enqueue_script( 'wcmp-admin-js', plugin_dir_url(__FILE__).'../../js/admin.js' );

$wcmp_js = array(
	'File Name' 		=> __( 'File Name', 'music-player-for-woocommerce' ),
	'Choose file' 		=> __( 'Choose file', 'music-player-for-woocommerce' ),
	'Delete' 			=> __( 'Delete', 'music-player-for-woocommerce' ),
	'Select audio file'	=> __( 'Select audio file', 'music-player-for-woocommerce' ),
	'Select Item'		=> __( 'Select Item', 'music-player-for-woocommerce' )
);
wp_localize_script('wcmp-admin-js', 'wcmp', $wcmp_js);

global $post;
$enable_player 	= $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_enable_player', false );
$show_in 		= $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_show_in', 'all' );
$player_style 	= $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_player_layout', WCMP_DEFAULT_PLAYER_LAYOUT );
$player_controls= $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_player_controls', WCMP_DEFAULT_PLAYER_CONTROLS );
$player_title	= intval( $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_player_title',1 ) );
$merge_grouped	= intval( $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_merge_in_grouped',0 ) );
$play_all		= intval( $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr(
						$post->ID,
						'_wcmp_play_all',
						// This option is only for compatibility with versions previous to 1.0.28
						$GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr(
							$post->ID,
							'play_all',
							0
						)
					)
				);
$preload		= $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr(
					$post->ID,
					'_wcmp_preload',
					$GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr(
						$post->ID,
						'preload',
						'none'
					)
				);
$on_cover 		= intval( $GLOBALS[ 'WooCommerceMusicPlayer' ]->get_product_attr( $post->ID, '_wcmp_on_cover', 0 ) );
?>
<div class="dokan-edit-row wcmp-section">
	<input type="hidden" name="wcmp_nonce" value="<?php echo wp_create_nonce('wcmp_updating_product'); ?>" />
	<div class="dokan-section-heading"><h2><?php _e('Music Player Settings', 'music-player-for-woocommerce'); ?></h2></div>
	<div class="dokan-section-content">
		<div class="wcmp-highlight-box">
		<?php
		_e(
			'<p>The player uses the audio files associated to the product.</b></p>',
			'music-player-for-woocommerce'
		);
		?>
		</div>
		<div class="wcmp-highlight-box">
			<div id="wcmp_tips_header">
				<div style="margin-top:2px;margin-bottom:5px;cursor:pointer;font-weight:bold;" onclick="jQuery('#wcmp_tips_body').toggle();">
					<?php _e('[+|-] Tips', 'music-player-for-woocommerce'); ?>
				</div>
			</div>
			<div id="wcmp_tips_body">
				<div class="wcmp-highlight-box">
					<a class="wcmp-tip"href="javascript:void(0);" onclick="jQuery(this).next('.wcmp-tip-text').toggle();">
					<?php _e('[+|-] Using the audio files stored on Google Drive', 'music-player-for-woocommerce'); ?>
					</a>
					<div class="wcmp-tip-text">
					<ul>
						<li>
							<p> -
							<?php
								_e(
									'Go to Drive, press the right click on the file to use, and select the option: <b>"Get Shareable Link"</b>',
									'music-player-for-woocommerce'
								);
							?>
							</p>
							<p>
							<?php
								_e(
									'The previous action will generate an url with the structure: <b>https://drive.google.com/open?id=FILE_ID</b>',
									'music-player-for-woocommerce'
								);
							?>
							</p>
						</li>
						<li>
							<p> -
								<?php
								_e(
									'Knowing the FILE_ID, extracted from the previous URL, enter the URL below, into the WooCommerce product, to allow the Music Player accessing to it:',
									'music-player-for-woocommerce'
								);
							?>
							</p>
							<p>
								<b>https://drive.google.com/uc?export=download&id=FILE_ID&.mp3</b>
							</p>
							<p>
								<?php
								_e(
									'<b>Note:</b> Pay attention to the use of the fake parameter: <b>&.mp3</b> as the last one in the URL',
									'music-player-for-woocommerce'
								);
								?>
							</p>
						</li>
					</div>
				</div>
				<div class="wcmp-highlight-box">
					<a class="wcmp-tip"href="javascript:void(0);" onclick="jQuery(this).next('.wcmp-tip-text').toggle();">
					<?php _e('[+|-] Using the audio files stored on DropBox', 'music-player-for-woocommerce'); ?>
					</a>
					<div class="wcmp-tip-text">
					<ul>
						<li>
							<p> -
							<?php
								_e(
									'Sign in to <a href="https://www.dropbox.com/login" target="_blank">dropbox.com </a>',
									'music-player-for-woocommerce'
								);
							?>
							</p>
						</li>
						<li>
							<p> -
							<?php
								_e(
									"Hover your cursor over the file or folder you'd like to share and click <b>Share</b> when it appears.",
									'music-player-for-woocommerce'
								);
							?>
							</p>
						</li>
						<li>
							<p> -
							<?php
								_e(
									"If a link hasn't been created, click Create a link. (If a link was already created, click Copy link.",
									'music-player-for-woocommerce'
								);
							?>
							</p>
							<p>
							<?php
								_e(
									'The link structure would be similar to:<br> https://www.dropbox.com/s/rycvgn8iokfedmo/file.mp3?dl=0',
									'music-player-for-woocommerce'
								);
							?>
							</p>
						</li>
						<li>
							<p> -
							<?php
								_e(
									'Enter the URL into the WooCommerce product with the following structure:<br> https://www.dropbox.com/s/rycvgn8iokfedmo/file.mp3?dl=1&.mp3',
									'music-player-for-woocommerce'
								);
							?>
							</p>
							<p>
								<?php
								_e(
									'<b>Note:</b> Pay attention to the use of the fake parameter: <b>&.mp3</b> as the last one in the URL. Furthermore, the parameter <b>dl=0</b>, has been modified as <b>dl=1</b>',
									'music-player-for-woocommerce'
								);
								?>
							</p>
						</li>
					</div>
				</div>
			</div>
		</div>
		<div>
			<div class="wcmp-dokan-attr">
				<label class="wcmp-dokan-attr-label">
					<input aria-label="<?php print esc_attr(__('Enable player','music-player-for-woocommerce')); ?>" type="checkbox" name="_wcmp_enable_player" <?php echo (( $enable_player ) ? 'checked' : '' ); ?> title="<?php print esc_attr(__('The player is shown only if the product is "downloadable" with at least an audio file between the "Downloadable files", or you have selected your own audio files', 'music-player-for-woocommerce')); ?>" /> <?php _e( 'Include music player', 'music-player-for-woocommerce' ); ?>
				</label>
			</div>
			<div class="wcmp-dokan-attr">
				<label for="_wcmp_show_in" class="wcmp-dokan-attr-label"><?php _e( 'Include in', 'music-player-for-woocommerce' ); ?></label>
				<div>
					<label><input aria-label="<?php print esc_attr(__('Include on products pages only','music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_show_in" value="single" <?php echo (( $show_in == 'single' ) ? 'checked' : '' ); ?> />
					<?php _e('single-entry pages <i>(Product\'s page only)</i>', 'music-player-for-woocommerce'); ?></label>

					<label><input aria-label="<?php print esc_attr(__('Include on multiple-entry pages','music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_show_in" value="multiple" <?php echo (( $show_in == 'multiple' ) ? 'checked' : '' ); ?> />
					<?php _e('multiple entries pages <i>(Shop pages, archive pages, but not in the product\'s page)</i>', 'music-player-for-woocommerce'); ?></label>

					<label><input aria-label="<?php print esc_attr(__('Include on products and multiple-entry pages', 'music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_show_in" value="all" <?php echo (( $show_in == 'all' ) ? 'checked' : '' ); ?> />
					<?php _e('all pages <i>(with single or multiple-entries)</i>', 'music-player-for-woocommerce'); ?></label>
				</div>
			</div>
			<div class="wcmp-dokan-attr">
				<label class="wcmp-dokan-attr-label"><?php _e( 'Merge in grouped products', 'music-player-for-woocommerce' ); ?>
				<input aria-label="<?php print esc_attr(__('Merge in grouped products', 'music-player-for-woocommerce')); ?>" type="checkbox" name="_wcmp_merge_in_grouped" <?php echo (( $merge_grouped ) ? 'checked' : '' ); ?> /></label>
				<div><em><?php _e( 'In grouped products, display the "Add to cart" buttons and quantity fields in the players rows', 'music-player-for-woocommerce' ); ?></em></div>
			</div>
			<div class="wcmp-dokan-attr">
				<label class="wcmp-dokan-attr-label"><?php _e( 'Player layout', 'music-player-for-woocommerce' ); ?></label>
				<table border="0" width="100%">
					<tr>
						<td><input aria-label="<?php print esc_attr(__('Skin 1', 'music-player-for-woocommerce')); ?>" name="_wcmp_player_layout" type="radio" value="mejs-classic" <?php echo (($player_style == 'mejs-classic') ? 'checked' : '') ;?> /></td>
						<td><img src="<?php print esc_url(WCMP_PLUGIN_URL); ?>/views/assets/skin1.png" /></td>
					</tr>

					<tr>
						<td><input aria-label="<?php print esc_attr(__('Skin 2', 'music-player-for-woocommerce')); ?>" name="_wcmp_player_layout" type="radio" value="mejs-ted" <?php echo (($player_style == 'mejs-ted') ? 'checked' : '') ;?> /></td>
						<td><img src="<?php print esc_url(WCMP_PLUGIN_URL); ?>/views/assets/skin2.png" /></td>
					</tr>

					<tr>
						<td><input aria-label="<?php print esc_attr(__('Skin 3','music-player-for-woocommerce')); ?>" name="_wcmp_player_layout" type="radio" value="mejs-wmp" <?php echo (($player_style == 'mejs-wmp') ? 'checked' : '') ;?> /></td>
						<td><img src="<?php print esc_url(WCMP_PLUGIN_URL); ?>/views/assets/skin3.png" /></td>
					</tr>
				</table>
			</div>
			<div class="wcmp-dokan-attr">
				<label for="_wcmp_preload" class="wcmp-dokan-attr-label"><?php _e( 'Preload', 'music-player-for-woocommerce' ); ?></label>
				<div>
					<label><input aria-label="<?php print esc_attr(__('Preload - none', 'music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_preload" value="none" <?php if($preload == 'none') echo 'CHECKED'; ?> /> None</label>
					<label><input aria-label="<?php print esc_attr(__('Preload - metadata', 'music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_preload" value="metadata" <?php if($preload == 'metadata') echo 'CHECKED'; ?> /> Metadata</label>
					<label><input aria-label="<?php print esc_attr(__('Preload - auto', 'music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_preload" value="auto" <?php if($preload == 'auto') echo 'CHECKED'; ?> /> Auto</label>
				</div>
			</div>
			<div class="wcmp-dokan-attr">
				<label><?php _e( 'Play all', 'music-player-for-woocommerce' ); ?> <input aria-label="<?php print esc_attr(__('Play all', 'music-player-for-woocommerce')); ?>" type="checkbox" name="_wcmp_play_all" <?php if(!empty($play_all)) echo 'CHECKED'; ?> /></label>
			</div>
			<div class="wcmp-dokan-attr">
				<label class="wcmp-dokan-attr-label"><?php _e( 'Player controls', 'music-player-for-woocommerce' ); ?></label>
				<div>
					<label><input aria-label="<?php print esc_attr(__('Play/pause button', 'music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_player_controls" value="button" <?php echo (( $player_controls == 'button' ) ? 'checked' : ''); ?> /> <?php _e( 'the play/pause button only', 'music-player-for-woocommerce' ); ?></label>
					<label><input aria-label="<?php print esc_attr(__('All controls','music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_player_controls" value="all" <?php echo (( $player_controls == 'all' ) ? 'checked' : ''); ?> /> <?php _e( 'all controls', 'music-player-for-woocommerce' ); ?></label>
					<label><input aria-label="<?php print esc_attr(__('Depending on context','music-player-for-woocommerce')); ?>" type="radio" name="_wcmp_player_controls" value="default" <?php echo (( $player_controls == 'default' ) ? 'checked' : ''); ?> /> <?php _e( 'the play/pause button only, or all controls depending on context', 'music-player-for-woocommerce' ); ?></label>
					<div class="wcmp-on-cover" style="margin-top:10px;">
						<label><input aria-label="<?php print esc_attr(__('Player on cover','music-player-for-woocommerce')); ?>" type="checkbox" name="_wcmp_player_on_cover" value="default" <?php
						echo ((!empty($on_cover) && ( $player_controls == 'button' ||  $player_controls == 'default')) ? 'checked' : '');
						?> /> <?php _e( 'for play/pause button players display them on cover images.', 'music-player-for-woocommerce' ); ?></label>
						<div><em><?php _e('(This feature is experimental, and will depend on the theme active on the website.)', 'music-player-for-woocommerce'); ?></em></div>
					</div>
				</div>
			</div>
			<div class="wcmp-dokan-attr">
				<label><?php _e( 'Display the player\'s title', 'music-player-for-woocommerce' ); ?> <input aria-label="<?php print esc_attr(__('Display player title','music-player-for-woocommerce')); ?>" type="checkbox" name="_wcmp_player_title" <?php echo (( !empty($player_title) ) ? 'checked' : ''); ?> /></label>
			</div>
		</div>
	</div>
</div>
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
	coverSection();
});</script>