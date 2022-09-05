//Go Between the Tabs
( function ( $ ){
    "use strict";
	$(".widgetkit-settings-tabs").tabs();
	
	$(document).ready(function(){
		$(".wk-thank-you-notice button.notice-dismiss").on('click', function(e){
			e.preventDefault();
			var url = new URL(location.href);
			url.searchParams.append('dismissed', 1);
			location.href = url;
		})
	});
    
    
    $("a.widgetkit-tab-list-item").on("click", function () {
        var tabHref = $(this).attr('href');
        window.location.hash = tabHref;
        $("html , body").scrollTop(tabHref);
    });
    
    $(".widgetkit-checkbox").on("click", function(){
       if($(this).prop("checked") == true) {
           $(".widgetkit-elements-table input").prop("checked", 1);
       }else if($(this).prop("checked") == false){
           $(".widgetkit-elements-table input").prop("checked", 0);
       }
    });
   
    /**
	 * dashboard settings save
	 */
    $( 'form#widgetkit-settings' ).on( 'submit', function(e) {
		e.preventDefault();
		$('form#widgetkit-settings button[type="submit"]').attr( 'disabled', true );
		$.ajax( {
			url: settings.ajaxurl,
			type: 'post',
			data: {
				action: 'widgetkit_save_admin_addons_settings',
				fields: $( 'form#widgetkit-settings' ).serialize(),
				security: settings.security_nonce
			},
			beforeSend: function() {
                $('form#widgetkit-settings .loading-ring').css('visibility', 'visible');
            },
            success: function( response ) {
				$('form#widgetkit-settings button[type="submit"]').attr( 'disabled', false );
				$('form#widgetkit-settings .loading-ring').css('visibility', 'hidden');
				swal({
					title: 'Settings Saved!',
					text: 'Click OK to continue',
					type: 'success',
					confirmButtonColor: '#ed485f'
				});
			},
			error: function() {
				swal({
					type: 'error',
					title: 'Bad Request',
					text: 'No dirty business please !',
				});
				$('form#widgetkit-settings .loading-ring').css('visibility', 'hidden');
			}
		} );

	} );
	/**
	 * mailchimp activation and deactivation
	 */
	$('.mailchimp-api-key-wrapper button').on('click', function(e){
		e.preventDefault();

		var apiKey = $( '.mailchimp-api-key-wrapper input#mailchimp-api-key' ).val();
		var listID = $( '.mailchimp-api-key-wrapper input#mailchimp-list-id' ).val();

		var inputObj = {
			'apiKey': apiKey,
			'listID': listID,
		};
		$.ajax( {
			url: settings.ajaxurl,
			type: 'post',
			data: {
				action: 'wkfe_mailchimp_api_keys',
				fields: inputObj,
				security: settings.security_nonce
			},
            success: function( response ) {
				if(response.data.success){
					$('.wk-mailchimp-card .response').text(response.data.message);
				}
			},
			error: function(error) {
				console.log('error', error.responseJSON);
				$('.wk-mailchimp-card .response').text(error.responseJSON.data);
			}
		} );
	})

	/**
	 * activate license
	 */
	$( '.wk-pro-license .license-checker-wrapper button.activate-license' ).on( 'click', function(e) {
		
		e.preventDefault();
		var wkProLicenseKey = $('.wk-pro-license .license-checker-wrapper input').val();
		$('.wk-pro-license .response').empty();

		if(wkProLicenseKey === ''){
			alert('please enter a license');
			return;
		}
		$(this).text('Activating');
		
		$.ajax( {
			url: settings.ajaxurl,
			type: 'post',
			data: {
				action: 'wk_pro_activate_license_key',
				license: wkProLicenseKey,
			},
            success: function( response ) {
				if('true' == response){
					$('.wk-pro-license .license-checker-wrapper button.activate-license').text('Activated');
					location.reload(true);
				}else{
					$('.wk-pro-license .license-checker-wrapper button.activate-license').text('Activate');
					$('.wk-pro-license .response').empty();
					$('.wk-pro-license .response').append(response);
				}
				
			},
			error: function(error) {
				console.log(error);
			}
		} );

	} );
	/**
	 * deactivate license
	 */
	$( '.wk-pro-license .license-checker-wrapper button.deactivate-license' ).on( 'click', function(e) {
		e.preventDefault();
		$(this).text('Deactivating');
		$('.wk-pro-license .response').empty();
		$.ajax( {
			url: settings.ajaxurl,
			type: 'post',
			data: {
				action: 'wk_pro_deactivate_license',
			},
            success: function( response ) {
				if('true' == response ){
					$('.wk-pro-license .license-checker-wrapper button.deactivate-license').text('Deactivated');
					location.reload(true);
				}else{
					$('.wk-pro-license .license-checker-wrapper button.deactivate-license').text('Deactivate');
					$('.wk-pro-license .response').text(response);
				}
			},
			error: function(error) {
				console.log(error);
			}
		} );

	} );
    
} )(jQuery);


// function url_content(url){
//     return jQuery.get(url);
// }


// url_content("http://child.demo").success(function(data){ 
//   console.log(jQuery(data).find('.elementor-image-box-img img')[0]);
// });

// function url_content(url){
//     return jQuery.get(url);
// }


// url_content("http://child.demo").success(function(data){ 
//   jQuery('#wpfooter').append(jQuery(data).find('.elementor-image-box-img img')[0]);
// });