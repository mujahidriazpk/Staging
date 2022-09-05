<?php
/**
 * GAM ad importer.
 *
 * Import ad units from a connected Google Ad Manager account into WordPress.
 */

class Advanced_Ads_Gam_Importer {

	/**
	 * The unique instance of this class
	 *
	 * @var Advanced_Ads_Gam_Importer.
	 */
	private static $instance;

	/**
	 * All GAM ids (in the format "networkCode_id"). FALSE if not set yet.
	 *
	 * @var array.
	 */
	private $all_gam_ids;

	/**
	 * Private constructor
	 */
	private function __construct() {
		add_action( 'wp_ajax_gam_importable_list', array( $this, 'get_importable_list_markup' ) );
		add_action( 'wp_ajax_gam_import_ads', array( $this, 'launch_import' ) );
		add_action( 'wp_ajax_advads_gam_import_button', array( $this, 'ajax_import_button' ) );
	}

	/**
	 * Get all GAM ad unique IDs from AA ad list (in the format "networkCode_id")
	 *
	 * @param bool $include_trash include trashed ads.
	 *
	 * @return array
	 */
	public function get_all_gam_ids( $include_trash = false ) {
		if ( $this->all_gam_ids === null ) {
			$all_gam_ads = Advanced_Ads_Gam_Admin::get_instance()->get_all_gam_ads( $include_trash );
			foreach ( $all_gam_ads as $_ad ) {
				$ad_content          = json_decode( base64_decode( $_ad->post_content ), true );
				$this->all_gam_ids[] = $ad_content['networkCode'] . '_' . $ad_content['id'];
			}
		}
		return $this->all_gam_ids;
	}

	/**
	 * Launch the successive post insertion
	 */
	public function launch_import() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		$post_vars = wp_unslash( $_POST );
		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-importer' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		$max_time = time() + ( .75 * absint( ini_get( 'max_execution_time' ) ) );
		$ad_ids   = array();

		if ( is_array( $post_vars['ids'] ) ) {
			// A sub call, ad IDs already in array format.
			$ad_ids = $post_vars['ids'];
		} else {
			// The initial call, parse the serialized ad IDs.
			parse_str( $post_vars['ids'], $id_arr );
			if ( isset( $id_arr['gam-ad-id'] ) && is_array( $id_arr['gam-ad-id'] ) ) {
				$ad_ids = $id_arr['gam-ad-id'];
			}
		}

		// Grab the original ad IDs list if it's a sub call.
		$original_ids      = isset( $post_vars['original_ids'] ) ? $post_vars['original_ids'] : $ad_ids;
		$time_up           = false;
		$imported_ad_count = isset( $post_vars['imported_ad_count'] ) ? absint( $post_vars['imported_ad_count'] ) : 0;

		while ( isset( $ad_ids[0] ) ) {
			// If we consumed more than 75% of PHP's max_execution_time, go for another AJAX call.
			if ( time() > $max_time ) {
				$time_up = true;
				break;
			}
			$imported = $this->import_single_ad( $ad_ids[0] );
			array_shift( $ad_ids );
			if ( $imported !== 0 && ! is_wp_error( $imported ) ) {
				$imported_ad_count++;
			}
		}

		// Exited the WHILE loop because of time restriction.
		if ( $time_up ) {
			// Send all the form data needed for the next AJAX call.
			wp_send_json(
				array(
					'status'    => true,
					'resend'    => true,
					'form_data' => array(
						'nonce'             => wp_create_nonce( 'gam-importer' ),
						'ids'               => $ad_ids,
						'original_ids'      => $original_ids,
						'action'            => 'gam_import_ads',
						'imported_ad_count' => $imported_ad_count,
					),
				)
			);
		} else {
			// All ads have been processed. Send the final markup.
			$html   = '<h2>' . sprintf( _n( 'One ad imported.', '%s ads imported.', $imported_ad_count, 'advanced-ads-gam' ), number_format_i18n( $imported_ad_count ) ) . '</h2>';
			$footer = '<a class="button-primary" href="' . esc_url( admin_url( 'edit.php?post_type=advanced_ads' ) ) . '">' . esc_html__( 'Open ad overview', 'advanced-ads-gam' ) . '</a><button class="button advads-modal-close">' . esc_html__( 'Close', 'advanced-ads-gam' ) . '</button>';

			wp_send_json(
				array(
					'status' => true,
					'html'   => $html,
					'footer' => $footer,
				)
			);
		}
	}

	/**
	 * Insert a new ad in the DB
	 *
	 * @param string $gam_id ad unit id in the current GAM network.
	 */
	protected function import_single_ad( $gam_id ) {
		$external_ads = Advanced_Ads_Network_Gam::get_instance()->get_external_ad_units();
		$ad_unit      = false;
		foreach ( $external_ads as $ad ) {
			if ( $gam_id === $ad['id'] ) {
				$ad_unit = $ad;
				break;
			}
		}

		// Somehow the ad unit is not in the current network.
		if ( ! $ad_unit ) {
			return false;
		}

		$post_content = base64_encode( json_encode( $ad_unit ) );
		$post_title   = 'GAM: ' . wp_strip_all_tags( $ad_unit['name'] );

		/**
		 * Allow ad user to change the name of the imported ad.
		 *
		 * @param string $post_title Default name (GAM: $ad_unit['name']).
		 * @param array $ad_unit The ad unit data.
		 */
		$post_title = apply_filters( 'advanced-ads-gam-ad-import-title', $post_title, $ad_unit );

		$post_id = wp_insert_post(
			array(
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_status'  => 'publish',
				'post_type'    => Advanced_Ads::POST_TYPE_SLUG,
				'meta_input'   => array(
					Advanced_Ads_Ad::$options_meta_field => array( 'type' => 'gam' ),
				),
			)
		);

		return $post_id;
	}

	/**
	 * Get importable ads markup
	 */
	public function get_importable_list_markup() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}
		$post_vars = wp_unslash( $_POST );

		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-importer' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		$external_ads = Advanced_Ads_Network_Gam::get_instance()->get_external_ad_units();
		if ( empty( $external_ads ) ) {
			Advanced_Ads_Network_Gam::get_instance()->update_external_ad_units();
			$external_ads = Advanced_Ads_Network_Gam::get_instance()->get_external_ad_units();
		}

		$importable_ads = array();
		$all_gam_ids    = $this->get_all_gam_ids( true );
		foreach ( $external_ads as $ad ) {
			if ( ! in_array( $ad['networkCode'] . '_' . $ad['id'], $all_gam_ids ) ) {
				$importable_ads[] = $ad;
			}
		}

		ob_start();
		require_once AAGAM_BASE_PATH . 'admin/views/importer/import-table.php';
		$output = ob_get_clean();

		wp_send_json(
			array(
				'status' => true,
				'html'   => $output,
			)
		);
	}

	/**
	 * Check if there is something that can be imported
	 *
	 * @return mixed.
	 */
	public function has_importable() {
		$external_ads = Advanced_Ads_Network_Gam::get_instance()->get_external_ad_units();

		$all_gam_ids = $this->get_all_gam_ids( true ) ?: array();
		$ad_count    = array(
			'imported'   => 0,
			'importable' => 0,
		);

		foreach ( $external_ads as $ad ) {
			if ( ! in_array( $ad['networkCode'] . '_' . $ad['id'], $all_gam_ids ) ) {
				$ad_count['importable']++;
			} else {
				$ad_count['imported']++;
			}
		}
		if ( 0 === $ad_count['importable'] ) {
			return false;
		}
		return $ad_count;
	}

	/**
	 * Prints the import ads button
	 */
	public static function import_button() {
		// Don't show the button if there is no valid license.
		if ( ! Advanced_Ads_Gam_Admin::has_valid_license() ) {
			return;
		}

		$external_ads = Advanced_Ads_Network_Gam::get_instance()->get_external_ad_units();
		// $external_ads is empty right after the account connection and can be fetched only using AJAX to blocking the page loading. But we know that we have an access token.
		if ( empty( $external_ads ) ) {
			echo '<span id="gam-late-import-button" data-nonce="' . esc_attr( wp_create_nonce( 'gam-importer' ) ) . '"><span>';
		} else {
			// There are external ad units that can be imported (and the external ad list has been updated at least once. Otherwise "has_importable" will always return false).
			if ( self::get_instance()->has_importable() ) {
				require_once AAGAM_BASE_PATH . 'admin/views/importer/base-frame.php';
				require ADVADS_BASE_PATH . 'admin/views/modal.php';
			}
		}
	}

	/**
	 * Update external ad units, then return the markup for the import button.
	 *
	 * @return void
	 */
	public function ajax_import_button() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		$post_vars = wp_unslash( $_POST );
		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-importer' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		Advanced_Ads_Network_Gam::get_instance()->update_external_ad_units();
		$ads = Advanced_Ads_Network_Gam::get_instance()->get_external_ad_units();
		if ( ! empty( $ads ) ) {
			ob_start();
			self::import_button();
			$html = ob_get_clean();
			wp_send_json(
				array(
					'status' => true,
					'html'   => $html,
				)
			);
		}
		wp_send_json( array( 'status' => false ) );
	}

	/**
	 * Returns or construct the singleton
	 *
	 * @return Advanced_Ads_Gam_Importer.
	 */
	final public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

if ( is_admin() ) {
	Advanced_Ads_Gam_Importer::get_instance();
}
