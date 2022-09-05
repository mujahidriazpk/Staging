<?php
class Advanced_Ads_Pro_Compatibility {
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

		// Set WPML Language.
		// Note: the "Language filtering for AJAX operations" feature of WPML does not work
		// because it sets cookie later then our ajax requests are sent.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX
			&& defined( 'ICL_SITEPRESS_VERSION' )
			&& ! empty( $_REQUEST[ 'wpml_lang' ] ) ) {
			do_action( 'wpml_switch_language', $_REQUEST[ 'wpml_lang' ] );
		}
	}

	/**
	 * After the theme is loaded.
	 */
	public function after_setup_theme() {
		// Newspaper theme
		if ( defined( 'TD_THEME_NAME' ) && 'Newspaper' === TD_THEME_NAME ) {
			$options = get_option( 'td_011' );
			// Check if lazy load is enabled (non-existent key or '').
			if ( empty( $options['tds_animation_stack'] ) ) {
				add_filter( 'advanced-ads-ad-image-tag-style', array( $this, 'newspaper_theme_disable_lazy_load' ) );
			}
		}
	}

	/**
	 * Newspaper theme: disable lazy load of the theme to prevent conflict with
	 * cache-busting/lazy-load of the Pro add-on.
	 *
	 * @param str $style
	 * @return str $style
	 */
	public function newspaper_theme_disable_lazy_load( $style ) {
		$style .= 'opacity: 1 !important;';
		return $style;
	}
}

