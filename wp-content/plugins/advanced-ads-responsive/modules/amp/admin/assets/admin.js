jQuery( document ).ready( function( $ ) {
	$( document ).on( 'click', '#advads-amp-add-prop', function( e ) {
		var copy = $( '.advads-amp-prop-row' ).last().clone();
		copy.find( 'input, textarea' ).val( '' ).end().insertAfter( $( '.advads-amp-prop-row' ).last() );
	});

	$( document ).on( 'click', '.advads-amp-delete-prop', function( e ) {
		if ( $( '.advads-amp-prop-row' ).length > 1 ) {
			$( this ).parents( 'tr' ).remove()
		} else {
			$( this ).parents( 'tr' ).find( 'input, textarea' ).val( '' );
		}
	});


	/**
	 * Settings for AMP version of Adsense.
	 */
	function handle_adsense() {
		/**
		 * Check if a type of adsense ad is supported.
		 *
		 * @return bool
		 */
		function is_supported_adsense_type() {
			var unit_type = jQuery( '#unit-type' ).val();
			if ( advanced_ads_amp_admin.supported_adsense_types.indexOf( unit_type ) !== -1 ) {
				return true;
			}
			return false;
		}

		$( '#advanced-ads-ad-parameters' ).on( 'paramloaded', showAdsenseAmpOptions );

		$( document ).on( 'gadsenseUnitChanged', function() {
			showAdsenseAmpOptions();
		});

		$( document ).on( 'change', '#ad-resize-type', function( ev ) {
			ev.preventDefault();
			showAdsenseAmpOptions();
		} );

		/**
		 * Show controls or warning if a non-AMP compatible option is selected.
		 */
		function showAdsenseAmpOptions() {
			var $controls = $( '#advads-adsense-responsive-amp-inputs' ).prev().addBack();
			var $warning = $( '.advanced-ads-adsense-amp-warning' );

			if ( ! $controls.length ) {
				return;
			}

			var is_supported = is_supported_adsense_type();

			if ( is_supported !== false ) {
				$controls.css( 'display', 'block' );
				$warning.hide();
			} else  {
				$controls.css( 'display', 'none' );
				$warning.show();
			}
		}

	}
	handle_adsense();

} );
