<?php
/**
 * Public AdSense functionality.
 */
class Advanced_Ads_Pro_AdSense_Public {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( ! empty( $options['cfp']['enabled'] ) ) {
			add_filter( 'advanced-ads-gadsense-page-level-code', array( $this, 'overwrite_page_level_code' ), 10, 2 );
		}
	}

	/**
	 * Overwrite the page-level code of the base plugin.
	 *
	 * @param string $code Existing code.
	 * @param array $parameters Parameters of the AdSense code.
	 * @return string $code New code.
	 */
	public function overwrite_page_level_code( $code, $parameters ) {
		ob_start();
		require_once __DIR__ . '/views/page-level.php';
		return ob_get_clean();
	}
}
