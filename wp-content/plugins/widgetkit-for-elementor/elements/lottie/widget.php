<?php

use Elementor\Widgets\Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class widgetkit_for_elementor_lottie_animation {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
        $this->add_actions();
        add_filter('upload_mimes',[$this,'upload_mimes']);
        add_filter('wp_check_filetype_and_ext',[$this,'wkfe_wp_check_filetype_and_ext'],10,4);
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
    }
    
    /**
	 * Allowed Json file upload
	 *
	 */
	public function upload_mimes( $mimes  ) {
        $mimes['json'] = 'application/json';
        return $mimes;
    }
    
    public function wkfe_wp_check_filetype_and_ext( $data, $file, $filename, $mimes  ) {
        if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
            return $data;
        }
      
        $filetype = wp_check_filetype( $filename, $mimes );
      
        if ( 'json' === $filetype['ext'] ) {
            $data['ext'] = 'json';
            $data['type'] = 'application/json';
        }
      
        return $data;
    }

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		require_once WK_PATH  . '/elements/lottie/template/config.php';
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new wkfe_lottie_animation() );
	}
}

new widgetkit_for_elementor_lottie_animation();


