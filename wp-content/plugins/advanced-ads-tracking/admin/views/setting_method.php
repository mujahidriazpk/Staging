<label><input name="<?php echo $this->plugin->options_slug; ?>[method]" type="radio" value="onrequest" <?php
    checked( 'onrequest', $method); ?>/><?php _e('track on load', 'advanced-ads-tracking'); ?></label>
<p class="description"><?php _e('Tracks an impression if the ad is queried from the database. Might count more impressions than ad networks.', 'advanced-ads-tracking'); ?></p>
<label><input name="<?php echo $this->plugin->options_slug; ?>[method]" type="radio" value="frontend" <?php
    checked( 'frontend', $method ); ?>/><?php _e('track with JavaScript (AJAX)', 'advanced-ads-tracking'); ?></label>
<p class="description"><?php _e('Tracks an impression after it was loaded in the frontend. Might count fewer impressions than visible in the frontend.', 'advanced-ads-tracking'); ?></p>
<label><input name="<?php echo $this->plugin->options_slug; ?>[method]" type="radio" value="shutdown" <?php
    checked( 'shutdown', $method ); ?>/><?php _e('track after page load completed', 'advanced-ads-tracking'); ?></label>
<p class="description"><?php _e('Tracks all impressions together after the entire page was printed out.', 'advanced-ads-tracking'); ?></p>
<label><input name="<?php echo $this->plugin->options_slug; ?>[method]" type="radio" value="ga" <?php
    checked( 'ga', $method ); ?>/><?php _e( 'track using Google Analytics', 'advanced-ads-tracking' ); ?></label>
<p class="description"><?php _e('Tracks impressions and clicks in Google Analytics.', 'advanced-ads-tracking'); ?></p>
<?php if ( 'ga' != $method && isset( $options['ga-UID'] ) && ( !defined( 'ADVANCED_ADS_TRACKING_FORCE_ANALYTICS' ) || !ADVANCED_ADS_TRACKING_FORCE_ANALYTICS ) ) : ?>
<input type="hidden" name="<?php echo $this->plugin->options_slug; ?>[ga-UID]" value="<?php echo esc_attr( $options['ga-UID'] ); ?>" />
<?php endif;
// when Pro AddOn and its cache-busting Module are active inform the user that only on-load tracking is supported
if ( class_exists( 'Advanced_Ads_Pro', false ) ) :
    $proOptions = Advanced_Ads_Pro::get_instance()->get_options();
    $isCacheBustingEnabled = isset( $proOptions['cache-busting']['enabled'] ) ? $proOptions['cache-busting']['enabled'] : false;
    if ( $isCacheBustingEnabled ) : ?>
<p class="warning"><?php _e('Ads that use cache-busting will always be counted using AJAX, if the Analytics method is not selected.', 'advanced-ads-tracking'); ?></p>
<?php
    endif;
endif;
?>