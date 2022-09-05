/**
 * WPForms Custom Captcha admin builder function.
 *
 * @since 1.1.0
*/
var WPFormsCaptcha = window.WPFormsCaptcha || ( function( document, window, $ ) {

	'use strict';

	/**
	 * Public functions and properties.
	 *
	 * @since 1.1.0
	 *
	 * @type object
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.1.0
		 */
		init: function() {

			$( document ).ready( app.ready );
		},

		/**
		 * Initialize once the DOM is fully loaded.
		 *
		 * @since 1.1.0
		 */
		ready: function() {

			// Cache builder element.
			var $builder = $( '#wpforms-builder') ;

			// Type (format) toggle.
			$builder.on( 'change', '.wpforms-field-option-captcha .wpforms-field-option-row-format select', function() {

				var $this = $( this ),
					value = $this.val(),
					id    = $this.parent().data( 'field-id' );

				if ( value === 'math') {
					$( '#wpforms-field-option-row-'+id+'-questions' ).hide();
					$( '#wpforms-field-option-row-'+id+'-size' ).hide();
				} else {
					$( '#wpforms-field-option-row-'+id+'-questions' ).show();
					$( '#wpforms-field-option-row-'+id+'-size' ).show();
				}
			});

			// Add new captcha question.
			$builder.on( 'click', '.wpforms-field-option-row-questions .add', function( event ) {

				event.preventDefault();

				var $this     = $( this ),
					$parent   = $this.parent(),
					fieldID   = $this.closest( '.wpforms-field-option-row-questions' ).data( 'field-id' ),
					id        = $parent.parent().attr( 'data-next-id' ),
					$question  = $parent.clone().insertAfter( $parent );

				$question.attr( 'data-key', id );
				$question.find( 'input.question' ).val( '' ).attr( 'name', 'fields['+fieldID+'][questions]['+id+'][question]' );
				$question.find( 'input.answer' ).val( '' ).attr( 'name', 'fields['+fieldID+'][questions]['+id+'][answer]' );
				id++;
				$parent.parent().attr( 'data-next-id', id );
			});

			// Remove captcha question.
			$builder.on( 'click', '.wpforms-field-option-row-questions .remove', function( event ) {

				event.preventDefault();

				var $this = $( this ),
					$list = $this.parent().parent(),
					total = $list.find( 'li' ).length;

				if ( total === 1 ) {
					$.alert({
						title:   false,
						content: wpforms_builder.error_choice,
						icon:   'fa fa-exclamation-circle',
						type:   'orange',
						buttons: {
							confirm: {
								text:      wpforms_builder.ok,
								btnClass: 'btn-confirm',
								keys:     [ 'enter' ]
							}
						}
					});
				} else {
					$this.parent().remove();
				}
			});

			// Captch questions sample question
			$builder.on( 'input', '.wpforms-field-option-row-questions li:first-of-type .question', function() {

				var $this   = $ ( this ),
					fieldID = $this.parent().parent().data( 'field-id' );

				$( '#wpforms-field-'+fieldID ).find( '.wpforms-question' ).text( $this.val() );
			});
		}
	};

	// Provide access to public functions/properties.
	return app;

})( document, window, jQuery );

// Initialize.
WPFormsCaptcha.init();