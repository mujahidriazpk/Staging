<?php

/**
 * Class Advanced_Ads_Gutenberg
 */
class Advanced_Ads_Gutenberg {

    private static $instance;

	private static $css_class;

    private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_scripts' ) );
    }

	/**
	 * Register blocks
	 */
	public function init() {
		if ( !function_exists( 'register_block_type' ) ) {
			// no Gutenberg, Abort
			return;
		}

		register_block_type( 'advads/gblock', array(
			'editor_script' => ADVADS_BASE . '/gutenberg-ad',
			'render_callback' => array( $this, 'render_ad_selector' ),
		) );

		// Removes legacy widget from legacy widget block.
		add_filter( 'widget_types_to_hide_from_legacy_widget_block', function() {
			$widget_types[] = 'advads_ad_widget';

			return $widget_types;
		} );
	}

	/**
	 *  Register back end scripts
	 */
	public function register_scripts() {
		if ( !function_exists( 'register_block_type' ) ) {
			// no Gutenberg, Abort
			return;
		}

		wp_register_script(
			ADVADS_BASE . '/gutenberg-ad',
			ADVADS_BASE_URL . 'modules/gutenberg/js/advanced-ads.block.js',
			array( 'wp-dom-ready', 'wp-blocks', 'wp-element' )
		);

		$model = Advanced_Ads::get_instance()->get_model();

		$all_ads = Advanced_Ads::get_ads( array( 'post_status' => array( 'publish' ), 'orderby' => 'title', 'order' => 'ASC' ) );
		$all_groups = $model->get_ad_groups();

		$ads = array();
		$groups = array();
		$placements = array();

		foreach ( $all_ads as $ad ) {
			$ads[] = array( 'id' => $ad->ID, 'title' => $ad->post_title );
		}

		foreach ( $all_groups as $gr ) {
			$groups[] = array( 'id' => $gr->term_id, 'name' => $gr->name );
		}

		foreach ( Advanced_Ads::get_instance()->get_model()->get_ad_placements_array() as $key => $value ) {
			if ( in_array( $value['type'], array( 'sidebar_widget', 'default' ), true ) ) {
				$placements[] = array( 'id' => $key, 'name' => $value['name'] );
			}
		}

		ksort( $placements );

		if ( empty( $placements ) ) {
			$placements = false;
		}

		$i18n = array(
			'--empty--' => __( '--empty--', 'advanced-ads' ),
			'advads' => __( 'Advanced Ads', 'advanced-ads' ),
			'ads' => __( 'Ads', 'advanced-ads' ),
			'adGroups' => __( 'Ad Groups', 'advanced-ads' ),
			'placements' => __( 'Placements', 'advanced-ads' ),
		);

		$inline_script = wp_json_encode(
			array(
				'ads' => $ads,
				'groups' => $groups,
				'placements' => $placements,
				'editLinks' => array(
					'group' => admin_url( 'admin.php?page=advanced-ads-groups' ),
					'placement' => admin_url( 'admin.php?page=advanced-ads-placements' ),
					'ad' => admin_url( 'post.php?post=%ID%&action=edit' ),
				),
				'i18n' => $i18n
			)
		);

		// put the inline code with the global variable right before the block's JS file
		wp_add_inline_script( ADVADS_BASE . '/gutenberg-ad', 'var advadsGutenberg = ' . $inline_script, 'before' );
		wp_enqueue_script( ADVADS_BASE . '/gutenberg-ad' );
	}

	/**
	 * Server side rendering for single ad block
	 *
	 * @param array $attr Block's attributes.
	 */
	public static function render_ad_selector( $attr ) {
		ob_start();

		if ( !isset( $attr['itemID'] ) ) {
			ob_end_clean();
			return '';
		}

		$output = array(
			'output' => array(
				'class'         => ! empty( $attr['className'] ) ? array_filter( explode( ' ', $attr['className'] ) ) : array(),
				'wrapper_attrs' => array( 'data-fixed_widget' => isset( $attr['fixed_widget'] ) ? $attr['fixed_widget'] : '' ),
			),
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- we can't escape ad output without potentially breaking ads
		if ( 0 === strpos( $attr['itemID'], 'ad_' ) ) {
			echo get_ad( absint( substr( $attr['itemID'], 3 ) ), $output );
		} elseif ( 0 === strpos( $attr['itemID'], 'group_' ) ) {
			echo get_ad_group( substr( $attr['itemID'], 6 ), $output );
		} elseif ( 0 === strpos( $attr['itemID'], 'place_' ) ) {
			echo get_ad_placement( substr( $attr['itemID'], 6 ), $output );
		}
		// phpcs:enable

		return ob_get_clean();
	}

    /**
     * Return the unique instance
     */
    public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
    }

}
