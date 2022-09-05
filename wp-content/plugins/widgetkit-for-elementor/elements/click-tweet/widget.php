<?php

use Elementor\Widgets\Elementor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

class Widgetkit_For_Elementor_Click_To_Tweet{

	public function __construct() {
		$this->widget_register();
	}

	private function widget_register() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );
	}

	public function on_widgets_registered() {
		$this->include_files();
		$this->init_config();
	}

	private function include_files() {
		require_once WK_PATH  . '/elements/click-tweet/template/config.php';
	}

	private function init_config() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new WKFE_Feature_Click_Tweet_Config() );
	}
}

new Widgetkit_For_Elementor_Click_To_Tweet();
