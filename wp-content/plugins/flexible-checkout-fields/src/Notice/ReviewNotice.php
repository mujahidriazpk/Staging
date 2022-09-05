<?php

namespace WPDesk\FCF\Free\Notice;

use FcfVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;

/**
 * Notice about review.
 */
class ReviewNotice implements Notice {

	const ACTIVATION_OPTION_NAME = 'plugin_activation_%s';
	const NOTICE_OPTION_NAME     = 'notice_review_%s';
	const NOTICE_NAME            = 'notice_review';

	/**
	 * @var AbstractPlugin
	 */
	private $plugin;

	public function __construct( AbstractPlugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_notice_name(): string {
		return self::NOTICE_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_active(): bool {
		$option_notice = sprintf( self::NOTICE_OPTION_NAME, $this->plugin->get_plugin_file_path() );
		$notice_date   = strtotime( get_option( $option_notice, false ) );
		$min_date      = strtotime( current_time( 'mysql' ) );

		if ( ( basename( $_SERVER['PHP_SELF'] ) !== 'index.php' ) // phpcs:ignore
			|| ( ( $notice_date !== false ) && ( $notice_date > $min_date ) ) ) {
			return false;
		}

		$option_activation = sprintf( self::ACTIVATION_OPTION_NAME, $this->plugin->get_plugin_file_path() );
		$activation_date   = strtotime( get_option( $option_activation, current_time( 'mysql' ) ) );
		$min_date          = strtotime( current_time( 'mysql' ) . ' -7 days' );

		return ( $activation_date <= $min_date );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_template_path(): string {
		return 'notices/review';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_vars_for_view(): array {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_notice_as_hidden( bool $is_permanently ) {
		$option_name = sprintf( self::NOTICE_OPTION_NAME, $this->plugin->get_plugin_file_path() );
		$notice_time = strtotime( current_time( 'mysql' ) . ( ( $is_permanently ) ? ' +10 years' : ' +1 month' ) );
		$notice_date = gmdate( 'Y-m-d H:i:s', $notice_time );

		update_option( $option_name, $notice_date, true );
	}
}
