<?php

/**
 * Captcha field.
 *
 * @package    WPFormsCaptcha
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Captcha_Field extends WPForms_Field {

	/**
	 * Min & max values to participate in equation and operators.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $math;

	/**
	 * Questions to ask.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $qs;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Captcha', 'wpforms-captcha' );
		$this->type  = 'captcha';
		$this->icon  = 'fa-question-circle';
		$this->order = 300;
		$this->group = 'fancy';
		$this->math  = apply_filters(
			'wpforms_math_captcha',
			array(
				'min' => 1,
				'max' => 15,
				'cal' => array( '+', '*' ),
			)
		);
		$this->qs    = array(
			1 => array(
				'question' => esc_html__( 'What is the fourth letter of the alphabet?', 'wpforms-captcha' ),
				'answer'   => esc_html__( 'D', 'wpforms-captcha' ),
			),
			2 => array(
				'question' => '',
				'answer'   => '',
			),
		);

		// Form frontend javascript.
		add_action( 'wpforms_frontend_js', array( $this, 'frontend_js' ) );

		// Admin form builder enqueues.
		add_action( 'wpforms_builder_enqueues', array( $this, 'admin_builder_enqueues' ) );

		// Remove the field from saved data.
		add_filter( 'wpforms_process_after_filter', array( $this, 'process_remove_field' ), 10, 3 );

		// Set field to default to required.
		add_filter( 'wpforms_field_new_required', array( $this, 'field_default_required' ), 10, 2 );

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_captcha', array( $this, 'field_properties' ), 5, 3 );
	}

	/**
	 * Enqueue frontend field js.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms Forms on the current page.
	 */
	public function frontend_js( $forms ) {

		if (
			wpforms()->frontend->assets_global() ||
			true === wpforms_has_field_type( 'captcha', $forms, true )
		) {

			$min = wpforms_get_min_suffix();

			wp_enqueue_script(
				'wpforms-captcha',
				plugin_dir_url( __FILE__ ) . "assets/js/wpforms-captcha{$min}.js",
				array( 'jquery', 'wpforms' ),
				WPFORMS_CAPTCHA_VERSION,
				true
			);

			$strings = array(
				'max'      => $this->math['max'],
				'min'      => $this->math['min'],
				'cal'      => $this->math['cal'],
				'errorMsg' => esc_html__( 'Incorrect answer.', 'wpforms-captcha' ),
			);
			wp_localize_script( 'wpforms-captcha', 'wpforms_captcha', $strings );
		}
	}

	/**
	 * Enqueues for the admin form builder.
	 *
	 * @since 1.0.0
	 */
	public function admin_builder_enqueues() {

		$min = wpforms_get_min_suffix();

		// JavaScript.
		wp_enqueue_script(
			'wpforms-builder-custom-captcha',
			plugin_dir_url( __FILE__ ) . "assets/js/admin-builder-captcha{$min}.js",
			array( 'jquery', 'wpforms-builder' ),
			WPFORMS_CAPTCHA_VERSION
		);

		// CSS.
		wp_enqueue_style(
			'wpforms-builder-custom-captcha',
			plugin_dir_url( __FILE__ ) . "assets/css/admin-builder-captcha{$min}.css",
			array(),
			WPFORMS_CAPTCHA_VERSION
		);
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.3.8
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		$field_id = absint( $field['id'] );
		$format   = ! empty( $field['format'] ) ? $field['format'] : 'math';

		// Input Primary: adjust name.
		$properties['inputs']['primary']['attr']['name'] = "wpforms[fields][{$field_id}][a]";

		// Input Primary: adjust class.
		$properties['inputs']['primary']['class'][] = 'a';

		// Input Primary: type dat attr.
		$properties['inputs']['primary']['data']['rule-wpf-captcha'] = $format;

		return $properties;
	}

	/**
	 * @inheritdoc
	 */
	public function is_dynamic_population_allowed( $properties, $field ) {

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function is_fallback_population_allowed( $properties, $field ) {

		return false;
	}

	/**
	 * Field should default to being required.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $required Required status, true is required.
	 * @param array $field    Field settings.
	 *
	 * @return bool
	 */
	public function field_default_required( $required, $field ) {

		if ( 'captcha' === $field['type'] ) {
			return true;
		}

		return $required;
	}

	/**
	 * Don't store captcha fields since it's for validation only.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    Field settings.
	 * @param array $entry     Form $_POST.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function process_remove_field( $fields, $entry, $form_data ) {

		foreach ( $fields as $id => $field ) {
			// Remove captcha from saved data.
			if ( 'captcha' === $field['type'] ) {
				unset( $fields[ $id ] );
			}
		}

		return $fields;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field settings.
	 */
	public function field_options( $field ) {

		// Defaults.
		$format = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'math';
		$qs     = ! empty( $field['questions'] ) ? $field['questions'] : $this->qs;

		// Field is always required.
		$this->field_element(
			'text',
			$field,
			array(
				'type'  => 'hidden',
				'slug'  => 'required',
				'value' => '1',
			)
		);

		/*
		 * Basic field options.
		 */

		// Options open markup.
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'open',
			)
		);

		// Label.
		$this->field_option( 'label', $field );

		// Format.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'format',
				'value'   => esc_html__( 'Type', 'wpforms_math_captcha' ),
				'tooltip' => esc_html__( 'Select type of captcha to use.', 'wpforms_math_captcha' ),
			),
			false
		);
		$fld = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'format',
				'value'   => $format,
				'options' => array(
					'math' => esc_html__( 'Math', 'wpforms-captcha' ),
					'qa'   => esc_html__( 'Question and Answer', 'wpforms-captcha' ),
				),
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'format',
				'content' => $lbl . $fld,
			)
		);

		// Questions.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'questions',
				'value'   => esc_html__( 'Questions and Answers', 'wpforms-captcha' ),
				'tooltip' => esc_html__( 'Add questions to ask the user. Questions are randomly selected.', 'wpforms-captcha' ),
			),
			false
		);
		$fld = sprintf(
			'<ul data-next-id="%s" data-field-id="%d" data-field-type="%s">',
			max( array_keys( $qs ) ) + 1,
			esc_attr( $field['id'] ),
			esc_attr( $this->type )
		);
		foreach ( $qs as $key => $value ) {
			$fld .= '<li data-key="' . absint( $key ) . '">';
			$fld .= sprintf( '<input type="text" name="fields[%s][questions][%s][question]" value="%s" class="question" placeholder="%s">', $field['id'], $key, esc_attr( $value['question'] ), esc_html__( 'Question', 'wpforms-captcha' ) );
			$fld .= '<a class="add" href="#"><i class="fa fa-plus-circle"></i></a><a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>';
			$fld .= sprintf( '<input type="text" name="fields[%s][questions][%s][answer]" value="%s" class="answer" placeholder="%s">', $field['id'], $key, esc_attr( $value['answer'] ), esc_html__( 'Answer', 'wpforms-captcha' ) );
			$fld .= '</li>';
		}
		$fld .= '</ul>';
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'questions',
				'content' => $lbl . $fld,
				'class'   => 'math' === $format ? 'wpforms-hidden' : '',
			)
		);

		// Description.
		$this->field_option( 'description', $field );

		// Options close markup.
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'close',
			)
		);

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$this->field_option(
			'advanced-options',
			$field,
			array(
				'markup' => 'open',
			)
		);

		// Size.
		$this->field_option(
			'size',
			$field,
			array(
				'class' => 'math' === $format ? 'wpforms-hidden' : '',
			)
		);

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Hide Label.
		$this->field_option( 'label_hide', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$this->field_option(
			'advanced-options',
			$field,
			array(
				'markup' => 'close',
			)
		);
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field settings.
	 */
	public function field_preview( $field ) {

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
		$format      = ! empty( $field['format'] ) ? $field['format'] : 'math';
		$num1        = wp_rand( $this->math['min'], $this->math['max'] );
		$num2        = wp_rand( $this->math['min'], $this->math['max'] );
		$cal         = $this->math['cal'][ wp_rand( 0, count( $this->math['cal'] ) - 1 ) ];
		$qs          = ! empty( $field['questions'] ) ? $field['questions'] : $this->qs;

		// Label.
		$this->field_preview_option( 'label', $field );
		?>

		<div class="format-selected-<?php echo $format; ?> format-selected">

			<span class="wpforms-equation"><?php echo "$num1 $cal $num2 = "; ?></span>

			<p class="wpforms-question"><?php echo $qs[1]['question']; ?></p>

			<input type="text" placeholder="<?php echo $placeholder; ?>" class="primary-input" disabled>

		</div>

		<?php
		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field settings.
	 * @param array $deprecated Deprecated array.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$field_id = absint( $field['id'] );
		$primary  = $field['properties']['inputs']['primary'];
		$format   = $form_data['fields'][ $field_id ]['format'];

		if ( 'math' === $format ) {
			// Math Captcha.
			?>
			<div class="wpforms-captcha-math">
				<span class="wpforms-captcha-equation">
					<span class="n1"></span>
					<span class="cal"></span>
					<span class="n2"></span>
					<span class="e">=</span>
				</span>
				<?php
				printf(
					'<input type="text" %s %s>',
					wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
					$primary['required']
				);
				?>
				<input type="hidden" name="wpforms[fields][<?php echo $field_id; ?>][cal]" class="cal">
				<input type="hidden" name="wpforms[fields][<?php echo $field_id; ?>][n2]" class="n2">
				<input type="hidden" name="wpforms[fields][<?php echo $field_id; ?>][n1]" class="n1">
			</div>
			<?php
		} else {
			// Question and Answer captcha.
			$qid = $this->random_question( $field, $form_data );
			$q   = esc_html( $form_data['fields'][ $field_id ]['questions'][ $qid ]['question'] );
			$a   = esc_attr( $form_data['fields'][ $field_id ]['questions'][ $qid ]['answer'] );
			?>
			<p class="wpforms-captcha-question"><?php echo $q; ?></p>
			<?php
			$primary['data']['a'] = $a;
			printf(
				'<input type="text" %s %s>',
				wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
				$primary['required']
			);
			?>
			<input type="hidden" name="wpforms[fields][<?php echo $field_id; ?>][q]" value="<?php echo $qid; ?>">
			<?php
		} // End if().
	}

	/**
	 * Select a random question.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field     Field settings.
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool|int
	 */
	public function random_question( $field, $form_data ) {

		if ( empty( $form_data['fields'][ $field['id'] ]['questions'] ) ) {
			return false;
		}

		$index = array_rand( $form_data['fields'][ $field['id'] ]['questions'] );

		if (
			empty( $form_data['fields'][ $field['id'] ]['questions'][ $index ]['question'] ) ||
			empty( $form_data['fields'][ $field['id'] ]['questions'][ $index ]['answer'] )
		) {
			$index = $this->random_question( $field, $form_data );
		}

		return $index;
	}

	/**
	 * Validates field on form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		// Math captcha.
		if ( 'math' === $form_data['fields'][ $field_id ]['format'] ) {

			// All calculation fields are required.
			if (
				( empty( $field_submit['a'] ) && '0' !== $field_submit['a'] ) ||
				empty( $field_submit['n1'] ) ||
				empty( $field_submit['cal'] ) ||
				empty( $field_submit['n2'] )
			) {
				wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = wpforms_get_required_label();

				return;
			}

			$n1  = $field_submit['n1'];
			$cal = $field_submit['cal'];
			$n2  = $field_submit['n2'];
			$a   = (int) trim( $field_submit['a'] );
			$x   = false;

			switch ( $cal ) {
				case '+':
					$x = ( $n1 + $n2 );
					break;
				case '-':
					$x = ( $n1 - $n2 );
					break;
				case '*':
					$x = ( $n1 * $n2 );
					break;
			}

			if ( $x !== $a ) {
				wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Incorrect answer', 'wpforms-captcha' );

				return;
			}
		}

		if ( 'qa' === $form_data['fields'][ $field_id ]['format'] ) {

			// All fields are required.
			if ( empty( $field_submit['q'] ) || empty( $field_submit['a'] ) ) {
				wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = wpforms_get_required_label();

				return;
			}

			if ( strtolower( trim( $field_submit['a'] ) ) !== strtolower( trim( $form_data['fields'][ $field_id ]['questions'][ $field_submit['q'] ]['answer'] ) ) ) {
				wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Incorrect answer', 'wpforms-captcha' );

				return;
			}
		}
	}
}

new WPForms_Captcha_Field();
