jQuery(document).ready( function($) {
    var wptp_popup_id = jQuery('#wp-terms-popup').attr('data-wptp-popup-id');
    var wptp_cookie = 'wptp_terms_' + wptp_popup_id;

    if (wptp_popup_id != undefined && wptp_read_cookie(wptp_cookie) != 'accepted') {
        // Load Popup CSS
        jQuery.ajax({
            type: 'POST',
            url: wptp_ajax_object.ajaxurl,
            data: {

                action: 'wptp_ajaxhandler_css',
                termspageid: wptp_popup_id,
                wptp_nonce: wptp_ajax_object.ajax_nonce,

            }, 
            dataType: 'json',

            success: function(data, textStatus, XMLHttpRequest) {
                jQuery('#wptp-css').html('');
                jQuery('#wptp-css').append(data.css);
            },

            error: function(MLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        // Load Popup HTML
        jQuery.ajax({
            type: 'POST',
            url: wptp_ajax_object.ajaxurl,
            data: {
                action: 'wptp_ajaxhandler_popup',
                termspageid: wptp_popup_id,
                wptp_nonce: wptp_ajax_object.ajax_nonce,
            }, 
            dataType: 'json',

            success: function(data, textStatus, XMLHttpRequest) {
                jQuery('#wptp-popup').html('');
                jQuery('#wptp-popup').append(data.popup);

                jQuery('#wptp-container').css('background-image', 'none');
            },

            error: function(MLHttpRequest, textStatus, errorThrown) {
                jQuery('#wptp-form').css('display', 'block');
                console.log(errorThrown);
            }
        });
    } else {
        if ($('.tdarkoverlay').length ) {
            // jQuery('.tdarkoverlay').remove();
        }

        if ($('.tbrightcontent').length ) {
            // jQuery('.tbrightcontent').remove();
        }
    }
    
    function wptp_read_cookie(cookie_name) {
        var nameEQ = cookie_name + "=";
        var ca = document.cookie.split(';');

        for (var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }

        return null;
    }
});