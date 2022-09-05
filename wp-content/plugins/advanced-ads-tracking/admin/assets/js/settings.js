;(function($){
    "use strict";
    var spinner = null;
    var keyUpTimeout = null;
    var TIMEOUT = 3000;
    var isChecking = false;
    var lastChecked;
    var nonce;
    
    // avoid troubles when asking availability of a slug
    function reverseInputsState() {
        $( 'input,textarea,button' ).each(function(){
            var s = $( this ).prop( 'disabled' );
            $( this ).prop( 'disabled', !s );
        });
    }
    
    function checkSlug( title ) {
        if ( undefined === title || '' === title ) return;
        if ( isChecking ) return;
        if ( lastChecked === title ) return;
        
        if ( null !== keyUpTimeout ) {
            clearTimeout( keyUpTimeout );
            keyUpTimeout = null;
        }
            
        $( '#public-stats-spinner32' ).append( spinner );
        
        lastChecked = title;
        isChecking = true;
        
        var formData = {
            nonce : nonce,
            action : 'advads-tracking-check-slug',
            title: title,
        };
        
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: formData,
            success: function ( resp, textStatus, XHR ) {
                $( '#public-stats-spinner32' ).empty();
                if ( undefined === resp.status ) {
                    displayResult();
                } else {
                    if ( ! resp.status ) {
                        displayResult({
                            error: true,
                            msg: resp.msg || trackingSettingsLocale.unknownError,
                        });
                        $( '#public-stat-base' ).val( resp.slug );
                    } else {
                        displayResult({
                            error: false,
                            msg: trackingSettingsLocale.linkAvailable,
                        });
                        $( '#public-stat-base' ).val( resp.slug );
                    }
                }
                isChecking = false;
                reverseInputsState();
            },
            error: function ( request, textStatus, err ) {
                $( '#public-stats-spinner32' ).empty();
                displayResult({
                    error: true,
                    msg: trackingSettingsLocale.serverFail,
                });
                isChecking = false;
                reverseInputsState();
            }
        });
        reverseInputsState();
        $( '#immediate-report-notice' ).empty();
    }
    
    function displayResult( res ) {
        if ( undefined === res ) {
            res = {
                error: true,
                msg: trackingSettingsLocale.unknownError,
            };
        }
        $( '#public-stat-notice' ).empty();
        if ( res.error ) {
            $( '#public-stat-notice' ).append( $( '<span style="color:red;">' + res.msg + '</span>' ) );
        } else {
            $( '#public-stat-notice' ).append( $( '<span style="color:#50CE61;">' + res.msg + '</span>' ) );
        }
    }
    
    $(function(){
        spinner = $( $( '#advads-track-admin-spinner' ).html() );
        nonce = advadsTrackingAjaxNonce;
        
        // change on public stat link base
        $( document ).on( 'change', '#public-stat-base', function(){
            checkSlug( $( this ).val() );
        } );
        
        // keyup on public stat link base
        $( document ).on( 'keyup', '#public-stat-base', function(){
            var elem = $( this );
            if ( null !== keyUpTimeout ) {
                clearTimeout( keyUpTimeout );
                keyUpTimeout = null;
            }
            keyUpTimeout = setTimeout(function(){
                checkSlug( elem.val() );
            }, TIMEOUT);
        } );
        
        // immedaite email report
        $( document ).on( 'click', '#send-immediate-report', function (ev) {
            ev.preventDefault();
            $( '#immediate-report-notice' ).empty();
            $( '#send-email-spinner-spinner' ).append( spinner );
            var formData = {
                nonce : nonce,
                action : 'advads-tracking-immediate-report',
            };
            
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: formData,
                success: function ( resp, textStatus, XHR ) {
                    $( '#send-email-spinner-spinner' ).empty();
                    if ( undefined !== resp.status && resp.status ) {
                        $( '#immediate-report-notice' ).html( '<span style="color:green">' + trackingSettingsLocale.emailSent + '</span>' );
                    } else {
                        $( '#immediate-report-notice' ).html( '<span style="color:red">' + trackingSettingsLocale.emailNotSent + '</span>' );
                    }
                    reverseInputsState();
                },
                error: function ( request, textStatus, err ) {
                    $( '#send-email-spinner-spinner' ).empty();
                    $( '#immediate-report-notice' ).html( '<span style="color:red">' + trackingSettingsLocale.serverFail + '</span>' );
                    reverseInputsState();
                }
            });
            
            reverseInputsState();
            
        } );
        
    });
    
})(jQuery);
