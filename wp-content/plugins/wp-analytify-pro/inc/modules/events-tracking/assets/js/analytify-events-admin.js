jQuery(document).ready(function ($) {

	// Add a new affiliates filed.
    $(document).on('click', '.wp-analytify-add-affiliates', function(e) {

        e.preventDefault();
        e.stopPropagation();

        // Used in the input name.
        var count = Math.random().toString(36).substr(2, 9);

        // Html code that contains <tr>, <td>, and select that will be appended.
        var html = '<tr class="single_affiliates">\
		<td><input type="text" class="affiliates-path" placeholder="/refer/" name="wp-analytify-events-tracking[affiliate_link_path][' + count + '][path]" id="wp-analytify-events-tracking[affiliate_link_path]" value="" required=""></td>\
		<td><input type="text" class="affiliates-label" placeholder="loginpress link" name="wp-analytify-events-tracking[affiliate_link_path][' + count + '][label]" id="wp-analytify-events-tracking[affiliate_link_path]" value="" required=""></td>\
		<td><span class="wp-analytify-rmv-affiliates"></span></td>\
		</tr>';

        // Append html.
        $('#wp-analytify-affiliates-table tbody').append(html);

        // Focus on the last inserted input.
        $('table#wp-analytify-affiliates-table > tbody > tr.single_affiliates:last-child').find('input.affiliates-path').focus();

    });

	// Remove the added affiliate.
	$(document).on('click', '.wp-analytify-rmv-affiliates', function(e) {
		$(this).closest('tr').remove();
	});

});

/**
 * License activation script.
 */
jQuery(document).ready(function ($) {
	var doing_license_registration_ajax = false;
	var admin_url = ajaxurl.replace( '/admin-ajax.php', '' ), spinner_url = admin_url + '/images/spinner';

	if ( 2 < window.devicePixelRatio ) {
		spinner_url += '-2x';
	}
	spinner_url += '.gif';

	var ajax_spinner = '<img src="' + spinner_url + '" alt="" class="ajax-spinner general-spinner" />';

	$( document ).on( 'click', "#analytify_events_tracking_license_activate", function(e) {

		e.preventDefault();

		if ( doing_license_registration_ajax ) {
			return;
		}

		$( '#events-tracking-license-status' ).removeClass( 'notification-message error-notice' );

		var license_key = $.trim( $( "#analytify_events_tracking_license_key" ).val() );

		if ( '' === license_key ) {
			$( '#events-tracking-license-status' ).addClass( 'notification-message error-notice' );
			$( '#events-tracking-license-status' ).html( wpanalytify_strings.enter_license_key );
			return;
		}

		$( '#events-tracking-license-status' ).empty().removeClass( 'success-notice' );
		doing_license_registration_ajax = true;
		$( '#analytify_events_tracking_license_activate' ).after( '<img src="' + spinner_url + '" alt="" class="register-license-ajax-spinner general-spinner" />' );

		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			cache: false,
			data: {
				action: 'google_optimize_activate_license',
				events_tracking_license_key: license_key,
				nonce: wpanalytify_data.nonces.activate_license,
				context: 'license'
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				doing_license_registration_ajax = false;
				$( '.register-license-ajax-spinner' ).remove();
				$( '#events-tracking-license-status' ).html( wpanalytify_strings.register_license_problem );
			},
			success: function( data ) {
				console.log(data);
				
				
				doing_license_registration_ajax = false;
				$( '.register-license-ajax-spinner' ).remove();


				if ( 'undefined' !== typeof data.error ) {

					$( '#events-tracking-license-status' ).addClass( 'notification-message error-notice' );
					$( '#events-tracking-license-status' ).html( data.error );

				} else if ( data == '0' ){

					$( '#events-tracking-license-status' ).addClass( 'notification-message error-notice' );
					$( '#events-tracking-license-status' ).html( wpanalytify_strings.register_license_problem );
				}else {
					$( '#events-tracking-license-status' ).html( wpanalytify_strings.license_registered ).delay( 5000 ).fadeOut( 1000 );
					$( '#events-tracking-license-status' ).addClass( 'notification-message success-notice' );
					$( '#analytify_events_tracking_license_key, #analytify_events_tracking_license_activate' ).remove();
					$( '.events-tracking-license-row' ).prepend( data.masked_license );

				}
			}
		} );
	});	
}); // License activation script end