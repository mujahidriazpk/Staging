<?php if( count( $draft_ads ) ) : ?>
<h3><?php _e('New ad purchases', 'advanced-ads-selling'); ?></h3>
    <ul>
    <?php foreach( $draft_ads as $_draft_ad ) : ?>
	<li><a href="<?php echo get_edit_post_link( $_draft_ad->ID );?>"><?php echo $_draft_ad->post_title; ?></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif;

if( count( $pending_ads ) ) : ?>
<h3><?php _e('Pending ad purchases', 'advanced-ads-selling'); ?></h3>
    <ul>
    <?php foreach( $pending_ads as $_pending_ad ) : ?>
	<li><a href="<?php echo get_edit_post_link( $_pending_ad->ID );?>"><?php echo $_pending_ad->post_title; ?></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif;

if( $entries === 0 ) : ?>
    <p><?php _e( 'No ad purchases to review', 'advanced-ads-selling' ); ?></p>
<?php endif;