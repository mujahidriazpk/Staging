/* global wpforms_captcha */

/**
 * WPForms Custom Captcha function.
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

			// Populate random equations for math captchas.
			$( '.wpforms-captcha-equation' ).each( function() {

				var $captcha = $( this ).parent(),
					calc     = wpforms_captcha.cal[ Math.floor( Math.random() * wpforms_captcha.cal.length ) ],
					n1       = app.randomNumber( wpforms_captcha.min, wpforms_captcha.max ),
					n2       = app.randomNumber( wpforms_captcha.min, wpforms_captcha.max );

				$captcha.find( 'span.n1' ).text( n1 );
				$captcha.find( 'input.n1' ).val( n1 );
				$captcha.find( 'span.n2' ).text( n2 );
				$captcha.find( 'input.n2' ).val( n2 );
				$captcha.find( 'span.cal' ).text( calc );
				$captcha.find( 'input.cal' ).val( calc );
				$captcha.find( 'input.a' ).attr( {
					'data-cal': calc,
					'data-n1': n1,
					'data-n2': n2
				} );
			});

			// Reload after OptinMonster is loaded.
			$( document ).on( 'OptinMonsterAfterInject', function() {
				app.ready();
			});

			// Load custom validation.
			app.loadValidation();
		},

		/**
		 * Custom captcha validation for jQuery Validation.
		 *
		 * @since 1.1.0
		 */
		loadValidation: function() {

			// Only load if the jQuery validation library exists.
			if ( typeof $.fn.validate !== 'undefined' ) {

				$.validator.addMethod( 'wpf-captcha', function( value, element, param ) {

					var $ele = $( element );

					var a, res;

					if ( 'math' === param ) {
						// Math captcha.
						var n1  = Number( $ele.attr( 'data-n1' ) ),
							n2  = Number( $ele.attr( 'data-n2' ) ),
							cal = $ele.attr( 'data-cal' );

						a   = Number( value );
						res = false;

						switch ( cal ) {
							case '-' :
								res = ( n1 - n2 );
								break;
							case '+' :
								res = ( n1 + n2 );
								break;
							case '*' :
								res = ( n1 * n2 );
								break;
						}
					} else {
						// Question answer captcha.
						a   = $.trim( value.toString().toLowerCase() );
						res = $.trim( $ele.attr( 'data-a' ).toString().toLowerCase() );
					}

					return this.optional( element ) || a === res;

				}, $.validator.format( wpforms_captcha.errorMsg ) );
			}
		},

		/**
		 * Generate random whole number.
		 *
		 * @since 1.0.0
		 *
		 * @param int $min Max number.
		 * @param int $max Max number.
		 *
		 * @return int
		 */
		randomNumber: function( min, max ) {

			return Math.floor( Math.random() * ( Number( max ) - Number( min ) + 1) ) + Number( min );
		}
	};

	// Provide access to public functions/properties.
	return app;

})( document, window, jQuery );

// Initialize.
WPFormsCaptcha.init();