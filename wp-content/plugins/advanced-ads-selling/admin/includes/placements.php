<?php

/**
 * process ordered placements
 */
class Advanced_Ads_Selling_Admin_Placements {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded() {
		if ( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
			return;
		}

		// show a warning above the 'Publish' button on the ad edit screen
		add_action( 'post_submitbox_start', array( $this, 'show_placement_item_and_warning' ), 11 );
		// add the ad to the ordered placement after publishing
		add_action( 'post_updated', array( $this, 'add_ad_to_placement' ), 10, 2 );
	}

	/**
	 * show a warning above the 'Publish' button on the ad edit screen
	 */
	public function show_placement_item_and_warning(){
		global $post;

		// exit if post status is not 'pending'
		if ( $post->post_type !== Advanced_Ads::POST_TYPE_SLUG || ! in_array( $post->post_status, array( 'pending', 'draft' ) ) ) { return; }

		$order_item = get_post_meta( $post->ID, 'advanced_ads_selling_order_item', true );
		if ( ! $order_item ) { return; }

		$p_slug = wc_get_order_item_meta( $order_item, '_ad_placement' );
		if ( ! $p_slug ) { return; }

		$p_item = self::get_placement_item( $p_slug );
		$warning = '';
		$do_import = false;
		
		$placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();
		$placement_name = isset( $placements[ $p_slug ]['name'] ) ? $placements[ $p_slug ]['name'] : '';
		
		if ( ! isset( $p_item['type'] ) ) {
			$warning = sprintf( __( 'When you press “Publish”, this ad will be published in the <strong>%2$s</strong> placement.', 'advanced-ads-selling' ), $p_item['name'], $placement_name );
			$do_import = true;
		} else if ( $p_item['type'] === 'ad' ) {
			// $item_title = sprintf( '%s (%s)', $p_item['name'], __( 'ad', 'advanced-ads-selling' ) );
			$warning = sprintf( __( 'When you press “Publish”, this ad will be swapped against the currently assigned ad (<strong>%s</strong>) in the <strong>%2$s</strong> placement.', 'advanced-ads-selling' ), $p_item['name'], $placement_name );
			$do_import = true;
		} else if ( $p_item['type'] === 'group' ) {
			if ( ! has_term( $p_item['item_id'], Advanced_Ads::AD_GROUP_TAXONOMY, $post->ID ) ) {
				$warning = sprintf( __( 'When you press “Publish”, this ad will be added to the group <strong>%1$s</strong> in the <strong>%2$s</strong> placement.', 'advanced-ads-selling' ), $p_item['name'], $placement_name );
				$do_import = true;
			}
		}
		
		// show expiry date warning
		if( 'days' === wc_get_order_item_meta( $order_item, '_ad_sales_type' ) && $expiry_days = wc_get_order_item_meta( $order_item, '_ad_pricing_option' ) ){
			$warning .= '<br/>' . sprintf( __( 'This ad is going to <strong>expire %d days</strong> after being published.', 'advanced-ads-selling' ), $expiry_days );
		}

		// $message = sprintf( __( 'Existing placement item: <em>%s</em>', 'advanced-ads-selling' ),  $item_title );

		include( AASA_BASE_PATH . '/admin/views/ad-publish-meta-box.php' );
	}

	/**
	 * add the ad to the ordered placement after publishing
	 *
	 * @param int     $post_ID      Post ID.
	 * @param WP_Post $post_after   Post object following the update.
	 */
	public function add_ad_to_placement( $post_ID, $post_after ) {
		// import only when post status is changed from 'pending' to 'publish'
		if ( empty( $_POST['advads-selling-add-to-placement'] ) || $post_after->post_status !== 'publish' ) {
			return;
		}

		// don’t do this on revisions
		if ( wp_is_post_revision( $post_ID ) ) {
			return;
		}

		$p_slug = $_POST['advads-selling-add-to-placement'];

		$xml_array[] = '<placements type="array">';
		$xml_array[] = '<item key="0" type="array">';
		$xml_array[] = '<item type="string">ad_' . $post_ID . '</item>';
		$xml_array[] = '<key type="string"><![CDATA[' . $p_slug . ']]></key>';
		$xml_array[] = '<use_existing type="boolean">1</use_existing>';
		$xml_array[] = '</item>';
		$xml_array[] = '</placements>';

		$xml = '<advads-export>' . implode( '', $xml_array ) . '</advads-export>';

		Advanced_Ads_Import::get_instance()->import( $xml );
	}

	/**
	 * get placement item by placement slug
	 *
	 * @param string $p_slug placement slug
	 * @return null/array null if item is not set, array with type item_id and name otherwise
	 */
	public static function get_placement_item( $p_slug ){
		$placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();

		if ( empty( $placements[ $p_slug ]['item'] ) ) { return; }

		$_item = explode( '_', $placements[ $p_slug ]['item'] );
		if ( empty( $_item[1] ) ) { return; }

		$item_id = absint( $_item[1] );

		switch ( $_item[0] ) {
			case 'ad':
			case Advanced_Ads_Select::AD :
				$ads = Advanced_Ads::get_instance()->get_model()->get_ads();

				foreach ( $ads as $_ad ) {
					if ( $_ad->ID === $item_id) {
						return array( 'type' => 'ad', 'item_id' => $item_id, 'name' => esc_html( $_ad->post_title ) );
					}
				}
				break;
			case Advanced_Ads_Select::GROUP :
				$groups = Advanced_Ads::get_instance()->get_model()->get_ad_groups( array( 'fields' => 'id=>name' ) );

				if ( isset( $groups[ $item_id ] ) ) {
					return array( 'type' => 'group', 'item_id' => $item_id, 'name' => esc_html( $groups[ $item_id ] ) );
				}
				break;
		}
	}
}