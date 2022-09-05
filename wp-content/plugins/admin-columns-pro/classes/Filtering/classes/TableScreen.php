<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 4.0
 */
class ACP_Filtering_TableScreen {

	public function __construct() {
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_action( 'ac/admin_footer', array( $this, 'add_indicator' ) );
		add_action( 'ac/admin_head', array( $this, 'maybe_hide_default_dropdowns' ) );
		add_action( 'wp_ajax_acp_update_filtering_cache', array( $this, 'ajax_update_dropdown_cache' ) );
		add_action( 'ac/table/list_screen', array( $this, 'init' ) ); // Before sorting
	}

	public function scripts() {
		wp_enqueue_style( 'acp-filtering-table', acp_filtering()->get_plugin_url() . 'assets/css/table.css', array(), acp_filtering()->get_version() );
		wp_enqueue_script( 'acp-filtering-table', acp_filtering()->get_plugin_url() . 'assets/js/table.js', array( 'jquery', 'jquery-ui-datepicker' ), acp_filtering()->get_version() );
	}

	/**
	 * Colors the column label orange on the listing screen when it is being filtered
	 *
	 * @param AC_ListScreen $list_screen
	 */
	public function add_indicator( AC_ListScreen $list_screen ) {
		$class_names = array();

		foreach ( $this->get_models( $list_screen ) as $model ) {
			if ( ! $model->is_active() ) {
				continue;
			}

			if ( ! $model->get_filter_value() ) {
				continue;
			}

			$class_names[] = $model->get_column_class_attr();

			// Used by sortable headers
			$class_names[] = $model->get_column_class_attr() . ' > a span:first-child';
		}

		if ( ! $class_names ) {
			return;
		}
		?>

		<style>
			<?php echo implode( ', ', $class_names ) .  '{ font-weight: bold; position: relative; }'; ?>
		</style>

		<?php
	}

	/**
	 * @since 3.8
	 *
	 * @param $list_screen AC_ListScreen
	 */
	public function maybe_hide_default_dropdowns( AC_ListScreen $list_screen ) {
		$disabled = array();

		foreach ( $this->get_models( $list_screen ) as $model ) {
			if ( $model instanceof ACP_Filtering_Model_Delegated && ! $model->is_active() ) {
				$disabled[] = '#' . $model->get_dropdown_attr_id();
			}
		}

		if ( ! $disabled ) {
			return;
		}

		?>

		<style>
			<?php echo implode( ', ', $disabled ) . '{ display: none; }'; ?>
		</style>

		<?php
	}

	/**
	 * @since 3.6
	 */
	public function ajax_update_dropdown_cache() {
		check_ajax_referer( 'ac-ajax' );

		$input = (object) filter_input_array( INPUT_POST, array(
			'list_screen' => FILTER_SANITIZE_STRING,
			'layout'      => FILTER_SANITIZE_STRING,
		) );

		$list_screen = AC()->get_list_screen( $input->list_screen );

		if ( ! $list_screen ) {
			wp_die();
		}

		$list_screen->set_layout_id( $input->layout );

		wp_send_json_success( $this->get_html_dropdowns( $list_screen ) );
	}

	/**
	 * @param AC_ListScreen $list_screen
	 *
	 * @return ACP_Filtering_Model[]
	 */
	private function get_models( AC_ListScreen $list_screen ) {
		$models = array();

		foreach ( $list_screen->get_columns() as $column ) {
			if ( $model = acp_filtering()->get_filtering_model( $column ) ) {
				$models[] = $model;
			}
		}

		return $models;
	}

	/**
	 * Init hooks for columns screen
	 *
	 * @since 1.0
	 */
	public function init( AC_ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ACP_Filtering_ListScreen ) {
			return;
		}

		foreach ( $this->get_models( $list_screen ) as $model ) {
			if ( ! $model->is_active() ) {
				continue;
			}

			// Render filter markup
			$list_screen->filtering( $model )->render_markup_hook();

			// Handle filtering request
			if ( false !== $model->get_filter_value() ) {
				$list_screen->filtering( $model )->handle_request();
			}

			// Hide the default date filter dropdown in WordPress
			if ( $model instanceof ACP_Filtering_Model_Post_Date && $model->hide_default_date_dropdown() ) {
				add_filter( 'disable_months_dropdown', '__return_true' );
			}
		}
	}

	/**
	 * @since 4.0
	 *
	 * @param AC_ListScreen $list_screen
	 *
	 * @return string Filtering HTML dropdowns
	 */
	protected function get_html_dropdowns( AC_ListScreen $list_screen ) {
		ob_start();

		foreach ( $this->get_models( $list_screen ) as $model ) {
			if ( $model instanceof ACP_Filtering_Model_Delegated ) {
				continue;
			}

			if ( ! $model->is_active() ) {
				continue;
			}

			if ( $model->is_ranged() ) {
				continue;
			}

			$model->update_filtering_data_cache();
			$model->render_markup();
		}

		return ob_get_clean();
	}

}
