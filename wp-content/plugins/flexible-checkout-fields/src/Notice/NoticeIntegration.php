<?php

namespace WPDesk\FCF\Free\Notice;

use FcfVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FcfVendor\WPDesk\PluginBuilder\Plugin\HookablePluginDependant;
use FcfVendor\WPDesk\PluginBuilder\Plugin\PluginAccess;
use FcfVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FcfVendor\WPDesk\View\Resolver\DirResolver;

/**
 * Supports ability to display notice and its management.
 */
class NoticeIntegration implements Hookable, HookablePluginDependant {

	use PluginAccess;

	/**
	 * @var Notice
	 */
	private $notice;

	/**
	 * @var SimplePhpRenderer
	 */
	private $renderer;

	/**
	 * Class constructor.
	 */
	public function __construct( Notice $notice ) {
		$this->notice   = $notice;
		$this->renderer = new SimplePhpRenderer( new DirResolver( dirname( dirname( __DIR__ ) ) . '/templates' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_filter( 'admin_init', [ $this, 'init_admin_notice' ] );
		add_action( 'wp_ajax_fcf_close_' . $this->notice->get_notice_name(), [ $this, 'hide_admin_notice' ] );
	}

	/**
	 * @return void
	 * @internal
	 */
	public function init_admin_notice() {
		if ( ! $this->notice->is_active() ) {
			return;
		}

		add_filter( 'admin_notices', [ $this, 'load_admin_notice_template' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_styles_for_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts_for_notice' ] );
	}

	/**
	 * @return void
	 * @internal
	 */
	public function load_admin_notice_template() {
		echo $this->renderer->render( // phpcs:ignore
			$this->notice->get_template_path(),
			array_merge( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				[
					'ajax_url'    => esc_attr( admin_url( 'admin-ajax.php' ) ),
					'ajax_action' => esc_attr( 'fcf_close_' . $this->notice->get_notice_name() ),
				],
				$this->notice->get_vars_for_view()
			)
		);
	}

	/**
	 * @return void
	 * @internal
	 */
	public function load_styles_for_notice() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.css' : '.min.css';

		wp_register_style(
			'fcf-notice',
			trailingslashit( $this->plugin->get_plugin_assets_url() ) . 'css/admin-notice' . $suffix,
			[],
			( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : $this->plugin->get_script_version()
		);
		wp_enqueue_style( 'fcf-notice' );
	}

	/**
	 * @return void
	 * @internal
	 */
	public function load_scripts_for_notice() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';

		wp_register_script(
			'fcf-notice',
			trailingslashit( $this->plugin->get_plugin_assets_url() ) . 'js/admin-notice' . $suffix,
			[],
			( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : $this->plugin->get_script_version(),
			true
		);
		wp_enqueue_script( 'fcf-notice' );
	}

	/**
	 * @return void
	 * @internal
	 */
	public function hide_admin_notice() {
		$is_permanently = ( isset( $_POST['is_permanently'] ) && $_POST['is_permanently'] ); // phpcs:ignore
		$this->notice->set_notice_as_hidden( $is_permanently );
	}
}
