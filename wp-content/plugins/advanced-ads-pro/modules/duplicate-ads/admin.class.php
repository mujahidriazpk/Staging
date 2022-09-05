<?php

/**
 * Class Advanced_Ads_Pro_Module_Duplicate_Ads_Admin
 * Admin logic to duplicate an existing ads.
 */
class Advanced_Ads_Pro_Module_Duplicate_Ads_Admin {

	/**
	 * Advanced_Ads_Pro_Module_Duplicate_Ads_Admin constructor.
	 */
	public function __construct() {
		// stop, if main plugin doesn’t exist.
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_action_advanced_ads_duplicate_ad', array( $this, 'duplicate_ad' ) );

	}

	/**
	 * On admin init
	 */
	public function admin_init() {

		// add Duplicate link to ad overview list.
		add_filter( 'post_row_actions', array( $this, 'render_duplicate_link' ), 10, 2 );
		// add Duplicate link to post submit box.
		add_action( 'post_submitbox_start', array( $this, 'render_duplicate_link_in_submit_box' ) );

	}

	/**
	 * Add the link to action list for post_row_actions
	 *
	 * @param array  $actions list of existing actions.
	 * @param object $post Post object.
	 *
	 * @return array with actions.
	 */
	public function render_duplicate_link( $actions, $post ) {

		if ( isset( $post->post_type )
		    && Advanced_Ads::POST_TYPE_SLUG === $post->post_type
		    && current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads' ) ) ) {
			$actions['copy-ad'] = self::get_duplicate_link( $post->ID );
		}

		return $actions;
	}

	/**
	 * Add the link to the submit box on the ad edit screen.
	 */
	public function render_duplicate_link_in_submit_box() {

		global $post;
		if ( isset( $post->post_type )
			 && 'edit' === $post->filter // only for already saved ads.
		     && Advanced_Ads::POST_TYPE_SLUG === $post->post_type
		     && current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads' ) ) ) {
			?>
			<div>
				<?php
				echo self::get_duplicate_link( $post->ID );
				?>
			</div>
			<?php
		}
	}

	/**
	 * Build the duplicate URL
	 *
	 * @param int $ad_id ID of the ad.
	 *
	 * @return string
	 */
	public static function get_duplicate_link( $ad_id ) {

		$action = '?action=advanced_ads_duplicate_ad&amp;ad_id=' . $ad_id;
		$url    = wp_nonce_url( admin_url( 'admin.php' . $action ), 'duplicate-ad-' . $ad_id );

		return '<a href="' . $url . '" title="' . esc_attr__( 'Create a copy of this ad', 'advanced-ads-pro' ) . '">' . esc_html__( 'Duplicate', 'advanced-ads-pro' ) . '</a>';
	}

	/**
	 * Save a copy of an ad using the same status as the original ad.
	 */
	public function duplicate_ad() {

		if (
			! isset( $_GET['action'] )
			|| 'advanced_ads_duplicate_ad' !== $_GET['action']
			|| ! isset( $_GET['ad_id'] )
			|| ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads' ) )
		) {
			return;
		}

		check_admin_referer( 'duplicate-ad-' . absint( wp_unslash( $_GET['ad_id'] ) ) );

		$ad_id = absint( wp_unslash( $_GET['ad_id'] ) );

		$ad = get_post( $ad_id );

		// copy the ad.
		if ( isset( $ad ) && null !== $ad ) {
			// add copy logic.
			$new_id = $this->create_copy( $ad );

			// redirect to the ad edit page of the new ad.
			wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		}
	}

	/**
	 * Create the copy of an ad.
	 *
	 * @param WP_POST $ad ad object.
	 *
	 * @return mixed
	 */
	public function create_copy( $ad ) {

		// return original ad ID if we are not using the correct post type.
		if ( empty( $ad->post_type ) || Advanced_Ads::POST_TYPE_SLUG !== $ad->post_type ) {
			return $ad->ID;
		}

		$new_ad = array();

		$new_ad['post_type']   = $ad->post_type;
		$new_ad['post_status'] = isset( $ad->post_status ) ? $ad->post_status : 'draft';

		// create a new title by adding "(copy)".
		$copy_suffix          = ' (' . _x( 'copy', 'noun', 'advanced-ads-pro' ) . ')';
		$new_ad['post_title'] = isset( $ad->post_title ) ? $ad->post_title . $copy_suffix : $copy_suffix;

		// use current user as author – not really needed though.
		$new_ad_author         = wp_get_current_user();
		$new_ad['post_author'] = $new_ad_author->ID;

		// copy content.
		$new_ad['post_content'] = $ad->post_content;

		// save the ad. WordPress will handle missing fields.
		$new_ad_id = wp_insert_post( wp_slash( $new_ad ) );

		/**
		 * Handle post meta of the ad
		 */
		/**
		 * Copy the meta information of a post to another post
		 */
		$post_meta_keys = get_post_custom_keys( $ad->ID );
		if ( empty( $post_meta_keys ) ) {
			return;
		}

		// handle exceptions for post meta key that should not be copied.
		$meta_blacklist[] = '_edit_lock';                       // edit lock.
		$meta_blacklist[] = '_edit_last';                       // edit last.
		$meta_blacklist[] = 'slide_template';                   // unknown plugin.
		$meta_blacklist[] = 'tps_options';                      // unknown plugin.
		$meta_blacklist[] = 'isc_post_images';                  // Image Source Control plugin.
		$meta_blacklist[] = '_wp_old_slug';                     // WooCommerce.
		$meta_blacklist[] = '_vc_post_settings';                // Visual Composer.
		$meta_blacklist[] = 'post_views_count';                 // unknown plugin.
		$meta_blacklist[] = 'advanced_ads_selling_order';       // Advanced Ads Selling Ads add-on (order data).
		$meta_blacklist[] = 'advanced_ads_selling_order_item';  // Advanced Ads Selling Ads add-on (order item data).

		// allow other plugins to filter the list.
		$meta_blacklist = apply_filters( 'advanced_ads_pro_duplicate_meta_blacklist', $meta_blacklist );

		$meta_keys = array_diff( $post_meta_keys, $meta_blacklist );

		// get values and add them to the ad.
		foreach ( $meta_keys as $meta_key ) {
			$meta_values = get_post_custom_values( $meta_key, $ad->ID );

			foreach ( $meta_values as $meta_value ) {
				$meta_value = maybe_unserialize( $meta_value );

				// Tracking add-on: remove value for public tracking ID because it needs to be unique per ad.
				if ( 'advanced_ads_ad_options' === $meta_key && isset( $meta_value['tracking']['public-id'] ) ) {
					unset( $meta_value['tracking']['public-id'] );
				}

				add_post_meta( $new_ad_id, $meta_key, $meta_value );
			}
		}

		return $new_ad_id;
	}

}

