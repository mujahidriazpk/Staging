jQuery( document ).ready(
	function ($) {
		function advads_selling_toggle_details_section() {

			// iterate through all ads.
			$( '.advanced-ads-selling-setup-ad-type:checked' ).each(
				function (key, el) {
					// get active sections.
					var ad_type = $( el ).val();

					// get parent container.
					var parent_container = $( el ).parents( '.advanced-ads-selling-setup-ad-details' );

					// highlight correct form fields.
					if ('image' === ad_type ) {
						parent_container.find( '.advanced-ads-selling-setup-ad-details-upload-label, .advanced-ads-selling-setup-ad-details-image-upload, .advanced-ads-selling-setup-ad-details-url, .advanced-ads-selling-setup-ad-details-url-input' ).show();
						parent_container.find( '.advanced-ads-selling-setup-ad-details-html-label, .advanced-ads-selling-setup-ad-details-html' ).hide();
					} else { // HTML is fallback.
						parent_container.find( '.advanced-ads-selling-setup-ad-details-html-label, .advanced-ads-selling-setup-ad-details-html' ).show();
						parent_container.find( '.advanced-ads-selling-setup-ad-details-upload-label, .advanced-ads-selling-setup-ad-details-image-upload, .advanced-ads-selling-setup-ad-details-url, .advanced-ads-selling-setup-ad-details-url-input' ).hide();
					}
				}
			);

		}

		advads_selling_toggle_details_section();
		// trigger, when selection is changed.
		$( '.advanced-ads-selling-setup-ad-type' ).click( advads_selling_toggle_details_section );
		// submit frontend ad form.
		$( '.advanced-ads-selling-setup-ad-details-submit' ).on(
			'click',
			function (e) {
				var uploadforms = document.querySelectorAll( '.advanced-ads-selling-setup-ad-details-upload-input' );
				uploadforms.forEach( function( el ) {
					// skip check if the upload form is not visible
					var style = window.getComputedStyle( el );
					if ( el.offsetParent === null ) {
						return;
					}

					var file = el.files[0];
					if ( file && file.size > AdvancedAdSelling.maxFileSize ) { // default is 1,000,000 bytes = 1 MB
						// Prevent default and display error
						e.preventDefault();
						alert( 'File size too large' );
						return false;
					}
				});
			}
		);

		// update prices dynamically.
		if ('object' == typeof AdvancedAdSelling) {
			var price_array = AdvancedAdSelling.product_prices;
		}
		jQuery( '#advads_selling_option_ad_price input' ).on( 'change', advads_selling_update_price );

		function advads_selling_update_price() {
			// when ad expiry is given.
			var total_price = 0;
			if (jQuery( '#advads_selling_option_ad_price input:checked' ).length) {
				var price_index = jQuery( '#advads_selling_option_ad_price input' ).length - jQuery( this ).parents( 'li' ).index() - 1; // needed to be reversed.
				total_price     = parseFloat( price_array[price_index]['price'] );
			}

			total_price = total_price.toFixed( 2 );
			total_price = total_price.toString();
			total_price = total_price.replace( '.', AdvancedAdSelling.woocommerce_price_decimal_sep );

			// write price into frontend.
			var selector = jQuery( '.price .woocommerce-ad-price' );
			var noRemove = selector.find( '.woocommerce-Price-currencySymbol' );
			jQuery( '.price .woocommerce-ad-price' ).html( noRemove );

			// place new price based on currency symbol position.
			switch ( AdvancedAdSelling.woocommerce_currency_position ) {
				case 'right' :
					jQuery( '.price .woocommerce-ad-price .woocommerce-Price-currencySymbol' ).before( total_price );
					break;
				case 'right_space' :
					jQuery( '.price .woocommerce-ad-price .woocommerce-Price-currencySymbol' ).before( total_price + '&nbsp;' );
					break;
				case 'left_space' :
					jQuery( '.price .woocommerce-ad-price .woocommerce-Price-currencySymbol' ).after( '&nbsp;' + total_price );
					break;
				default :
					jQuery( '.price .woocommerce-ad-price .woocommerce-Price-currencySymbol' ).after( total_price );
			}
		}
	}
);
