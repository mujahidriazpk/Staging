<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class ACP_Filtering_Model extends ACP_Model {

	/**
	 * @var bool
	 */
	private $ranged;

	/**
	 * Get the query vars to filter on
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	abstract public function get_filtering_vars( $vars );

	/**
	 * Return the data required to generate the filtering gui on a list screen
	 *
	 * @return array
	 */
	abstract public function get_filtering_data();

	/**
	 * @param bool $is_ranged
	 */
	public function set_ranged( $is_ranged ) {
		$this->ranged = (bool) $is_ranged;
	}

	/**
	 * @return bool
	 */
	public function is_ranged() {
		if ( null === $this->ranged ) {
			$setting = $this->column->get_setting( 'filter' );
			$is_ranged = $setting instanceof ACP_Filtering_Settings_Ranged && $setting->is_ranged();

			$this->set_ranged( $is_ranged );
		}

		return $this->ranged;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		$setting = $this->column->get_setting( 'filter' );

		if ( ! $setting instanceof ACP_Filtering_Settings ) {
			return false;
		}

		return $setting->is_active();
	}

	/**
	 * Register column settings
	 */
	public function register_settings() {
		$this->column->add_setting( new ACP_Filtering_Settings( $this->column ) );
	}

	/**
	 * @return string|array
	 */
	public function get_filter_value() {
		if ( $this->is_ranged() ) {

			$value = false;

			$min = $this->get_request_var( 'min' );
			$max = $this->get_request_var( 'max' );

			if ( false !== $min || false !== $max ) {
				$value = array(
					'min' => $min,
					'max' => $max,
				);
			}

			return $value;

		} else {
			return $this->get_request_var();
		}
	}

	/**
	 * Validate a value: can it be used to filter results?
	 *
	 * @param string|integer $value
	 * @param string         $filters Options: all, serialize, length and empty. Use a | to use a selection of filters e.g. length|empty
	 *
	 * @return bool
	 */
	protected function validate_value( $value, $filters = 'all' ) {
		$available = array( 'serialize', 'length', 'empty' );

		switch ( $filters ) {
			case 'all':
				$applied = $available;

				break;
			default:
				$applied = array_intersect( $available, explode( '|', $filters ) );

				if ( empty( $applied ) ) {
					$applied = $available;
				}
		}

		foreach ( $applied as $filter ) {
			switch ( $filter ) {
				case 'serialize':
					if ( is_serialized( $value ) ) {
						return false;
					}

					break;
				case 'length':
					if ( strlen( $value ) > 1024 ) {
						return false;
					}

					break;
				case 'empty':
					if ( empty( $value ) && 0 !== $value ) {
						return false;
					}

					break;
			}
		}

		return true;
	}

	/**
	 * @param string $label
	 *
	 * @return array
	 */
	protected function get_empty_labels( $label = '' ) {
		if ( ! $label ) {
			$label = strtolower( $this->column->get_label() );
		}

		return array(
			sprintf( __( "Without %s", 'codepress-admin-columns' ), $label ),
			sprintf( __( "Has %s", 'codepress-admin-columns' ), $label ),
		);
	}

	/**
	 * Return the maximum amount of results in a dropdown
	 *
	 * @return int
	 */
	public function get_limit() {
		return 5000;
	}

	protected function use_cache() {
		return apply_filters( 'acp/filtering/use_cache', true );
	}

	protected function get_cache_key() {
		return $this->column->get_list_screen()->get_storage_key() . $this->column->get_name();
	}

	/**
	 * Create the cache
	 */
	public function update_filtering_data_cache() {
		if ( ! $this->use_cache() ) {
			return;
		}

		$cache = new ACP_Filtering_Cache( $this->get_cache_key() );

		if ( $cache->is_expired() ) {
			$cache->put( $this->get_filtering_data() );
		}
	}

	protected function get_filtering_data_from_cache() {
		$cache = new ACP_Filtering_Cache( $this->get_cache_key() );
		$data = $cache->get();

		if ( ! $data ) {
			$data = array(
				'options' => array(
					ACP_Filtering_Markup_Dropdown::get_disabled_prefix() . 'loading' => __( 'Loading values', 'codepress-admin-columns' ) . ' ...',
				),
			);
		}

		return $data;
	}

	/**
	 * Display dropdown markup
	 */
	public function render_markup() {
		// Check column
		if ( $this instanceof ACP_Filtering_Model_Delegated ) {
			return;
		}

		// Check filter
		$filter_setting = $this->column->get_setting( 'filter' );

		if ( ! $filter_setting instanceof ACP_Filtering_Settings ) {
			return;
		}

		// Get label
		$label = $filter_setting->get_filter_label();

		if ( ! $label ) {
			$label = $filter_setting->get_filter_label_default();
		}

		// Get name
		$name = $this->column->get_name();

		// Range inputs or select dropdown
		if ( $this->is_ranged() ) {
			$min = $this->get_request_var( 'min' );
			$max = $this->get_request_var( 'max' );

			switch ( $this->get_data_type() ) {
				case 'date':
					$markup = new ACP_Filtering_Markup_Ranged_Date( $name, $label, $min, $max );

					break;
				case 'numeric':
					$markup = new ACP_Filtering_Markup_Ranged_Number( $name, $label, $min, $max );

					break;
				default:
					return;
			}
		} else {
			$markup = new ACP_Filtering_Markup_Dropdown( $this->column->get_name() );
			$markup->set_value( $this->get_request_var() )
			       ->set_label( $label );

			if ( $this->use_cache() ) {
				$data = $this->get_filtering_data_from_cache();
			} else {
				$data = $this->get_filtering_data();
			}

			$defaults = array(
				'order'        => false,
				'options'      => array(),
				'empty_option' => false,
			);

			$data = array_merge( $defaults, $data );

			$data = apply_filters( 'acp/filtering/dropdown_args', $data, $this->column );

			// backwards compatible for the acp/filtering/dropdown_args filter
			if ( isset( $data['label'] ) ) {
				$markup->set_label( $data['label'] );
			}

			// backwards compatible for the acp/filtering/dropdown_args filter
			if ( is_array( $data['options'] ) ) {
				if ( count( $data['options'] ) >= $this->get_limit() ) {
					$data['options'] = array_slice( $data['options'], 0, $this->get_limit(), true );
					$data['options'][ $markup::get_disabled_prefix() . 'limit' ] = '───── ' . sprintf( __( 'Limited to %s items' ), $this->get_limit() ) . ' ─────';
				}

				$markup->set_options( $data['options'] );
			}

			// backwards compatible for the default options, this should be done using an array as well
			if ( true === $data['empty_option'] ) {
				$markup->set_empty()
				       ->set_nonempty();
			} elseif ( is_array( $data['empty_option'] ) ) {
				$markup->set_empty( $data['empty_option'][0] )
				       ->set_nonempty( $data['empty_option'][1] );
			}

			$markup->set_order( $data['order'] );
		}

		echo $markup->render();
	}

	/**
	 * Get a request var for all columns
	 *
	 * @param string $suffix
	 *
	 * @return string|false
	 */
	private function get_request_var( $suffix = '' ) {
		$key = 'acp_filter';

		if ( $suffix ) {
			$key .= '-' . ltrim( $suffix, '-' );
		}

		$values = filter_input( INPUT_GET, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! isset( $values[ $this->column->get_name() ] ) ) {
			return false;
		}

		$value = $values[ $this->column->get_name() ];

		if ( ! is_scalar( $value ) ) {
			return false;
		}

		if ( mb_strlen( $value ) < 1 ) {
			return false;
		}

		return $value;
	}

	/**
	 * Column header class attribute. Used for styling the column header based on filter vars.
	 *
	 * @return string
	 */
	public function get_column_class_attr() {
		return 'thead tr th.column-' . $this->column->get_name();
	}

	/**
	 * @deprecated 4.2
	 */
	protected function get_date_options_relative( $format ) {
		_deprecated_function( __METHOD__, '4.2', 'acp_filtering_helper()->get_date_options_relative()' );

		return acp_filtering_helper()->get_date_options_relative( $format );
	}

	/**
	 * @deprecated 4.2
	 */
	protected function get_date_options( array $dates, $display, $format = 'Y-m-d', $key = null ) {
		_deprecated_function( __METHOD__, '4.2', 'acp_filtering_helper()->get_date_options()' );

		return acp_filtering_helper()->get_date_options( $dates, $display, $format, $key );
	}

}
