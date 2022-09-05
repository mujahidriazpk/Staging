<?php

$sums = Advanced_Ads_Tracking_Util::get_instance()->get_sums();

$impr = ( isset( $sums['impressions'][$ad_id] ) )? $sums['impressions'][$ad_id] : 0;
$clicks = ( isset( $sums['clicks'][$ad_id] ) )? $sums['clicks'][$ad_id] : 0;

$ad = new Advanced_Ads_Ad( $ad_id );
$ad_options = $ad->options();
$tracking_options = Advanced_Ads_Tracking_Plugin::get_instance()->options();
$target = Advanced_Ads_Tracking_Util::get_link( $ad );
global $post;
$published = ( 'publish' == $post->post_status )? true : false;
?>
<ul>
    <li><strong><?php _e( 'Impressions', 'advanced-ads-tracking' ); ?>:</strong>&nbsp;<?php echo $impr; ?></li>
    <?php if ( $target ) : ?>
    <?php if ( 'enabled' == $ad_options['tracking']['enabled'] || ( ( isset( $tracking_options['everything'] ) && 'true' == $tracking_options['everything'] || !isset( $tracking_options['everything'] ) ) && 'default' == $ad_options['tracking']['enabled'] ) ) : ?>
    <li><strong><?php _e( 'Clicks', 'advanced-ads-tracking' ); ?>:</strong>&nbsp;<?php echo $clicks; ?></li>
    <?php endif; ?>
    <li>
        <strong><?php _e( 'Target url', 'advanced-ads-tracking' ); ?>:</strong>&nbsp;<div class="target-link-div">
            <div class="target-link-text"><a href="<?php echo esc_url( $target ); ?>" target="_blank"><?php echo $target; ?></a></div>
            <a href="<?php echo esc_url( $target ); ?>" target="_blank"><?php _e( 'show', 'advanced-ads-tracking' ); ?></a>
        </div>
    </li>
    <?php endif; ?>
</ul>
<?php if ( $published ) : // avoid admin stats for non published ads ?>
<div class="row-actions">
    <a target="blank" href="<?php echo Advanced_Ads_Tracking_Admin::admin_30days_stats_url( $ad_id ); ?>"><?php _e( 'Statistics for the last 30 days', 'advanced-ads-tracking' ); ?></a>
</div>
<?php endif; ?>
