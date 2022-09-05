<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$module_enabled = isset( $options['ads-for-adblockers']['enabled'] ) && $options['ads-for-adblockers']['enabled'];
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[ads-for-adblockers][enabled]" id="advanced-ads-pro-ads-for-adblockers-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> />
<label for="advanced-ads-pro-ads-for-adblockers-enabled" class="description"><?php _e( 'Activate <em>ads for ad blockers</em> module.', 'advanced-ads-pro' ); ?></label>
