<?php

namespace WPDesk\FCF\Free\Validator\Error;

use WPDesk\FCF\Free\Settings\Option\RegexMessageOption;

/**
 * {@inheritdoc}
 */
class InvalidRegexError extends ErrorAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message(): string {
		return sprintf(
			esc_html( wpdesk__( $this->field_data[ RegexMessageOption::FIELD_NAME ], 'flexible-checkout-fields' ) ),
			sprintf( '<strong>%s</strong>', strip_tags( $this->field_data['label'] ) )
		);
	}
}
