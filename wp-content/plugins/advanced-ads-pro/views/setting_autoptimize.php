<?php $optimizers = "Autoptimize, WP Rocket";
?><input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[autoptimize-support-disabled]" type="checkbox" value="1" <?php checked( $autoptimize_support_disabled, 1 ); ?>"/>
<p class="description"><?php 
/*
 * translators: %s is a list of supported optimizer plugins
 */
echo sprintf(__( 'Advanced Ads Pro disables optimizers ( %s ) for displaying ads per default. Enable this option to allow optimizers to change the ad code. Especially JavaScript ads might stop working then.', 'advanced-ads-pro' ), $optimizers); ?></p>