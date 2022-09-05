<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACP_Filtering_Strategy_User extends ACP_Filtering_Strategy {

	/**
	 * @var bool
	 */
	private static $has_button = false;

	public function handle_request() {
		add_action( 'pre_get_users', array( $this, 'handle_filter_requests' ), 1 );
	}

	public function render_markup_hook() {
		add_action( 'restrict_manage_users', array( $this, 'render_markup' ) );
		add_action( 'restrict_manage_users', array( $this, 'render_button' ), 99 );
	}

	/**
	 * Handle filter request
	 *
	 * @since 3.5
	 *
	 * @param WP_User_Query $user_query
	 */
	public function handle_filter_requests( $user_query ) {
		if ( ! isset( $_GET['acp_filter_action'] ) ) {
			return;
		}

		$user_query->query_vars = $this->model->get_filtering_vars( $user_query->query_vars );
	}

	public function get_values_by_db_field( $user_field ) {
		global $wpdb;

		$user_field = sanitize_key( $user_field );
		$limit = absint( $this->get_model()->get_limit() );

		$values = $wpdb->get_col( "
			SELECT DISTINCT {$user_field}
			FROM {$wpdb->users}
			WHERE {$user_field} <> ''
			ORDER BY 1
			LIMIT {$limit}
		" );

		if ( ! $values || is_wp_error( $values ) ) {
			return array();
		}

		return $values;
	}

	/**
	 * Run once for users
	 */
	public function render_markup() {
		remove_action( 'restrict_manage_users', array( $this, 'render_markup' ) );

		parent::render_markup();
	}

	/**
	 * @since 3.5
	 */
	public function render_button() {
		if ( self::$has_button ) {
			return;
		}
		self::$has_button = true;
		?>
		<input type="submit" name="acp_filter_action" class="button" value="<?php echo esc_attr( __( 'Filter', 'codepress-admin-columns' ) ); ?>">
		<?php
	}

}
