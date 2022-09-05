<select name="<?php echo Advanced_Ads_Selling_Plugin::OPTION_KEY; ?>[setup-page-id]">
    <?php if( $pages ) : ?>
    <option value="" <?php selected( false, $public_page_id ); ?>><?php _e( '(default)', 'advanced-ads-selling' ); ?></option>
    <?php foreach( $pages as $_page ) : ?>
    <option value="<?php echo $_page->ID ?>" <?php selected( $_page->ID, $public_page_id ); ?>><?php echo $_page->post_title; ?></option>
    <?php endforeach; ?>
    <?php endif; ?>
</select>
<p class="description"><?php _e( 'Choose the page on which you want to show the ad setup for the client after the purchase. Leave blank for the default layout.', 'advanced-ads-selling' ); ?></p>