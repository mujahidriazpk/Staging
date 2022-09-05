/**
 * Advanced Ads – Responsive Ads.
 *
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      http://webgilde.com
 * @copyright 2013-2015 Thomas Maier, webgilde GmbH
 */
;(function( $ ){
	"use strict";
	
	var tooltipModel = $( '<div class="advads-tooltip">' + advadsRespLocalize.adSize + '&nbsp;:&nbsp;<span class="tooltip-w"></span>&nbsp;x&nbsp;<span class="tooltip-h"></span><br />' + 
    advadsRespLocalize.containerWidth + '&nbsp;:&nbsp;<span class="tooltip-cw"></span></div>' );
	
	// on DOM ready
	$( function(){
		
		// if cache busting in pro is enabled, use a decent timeout
		if( typeof advanced_ads_pro_admin_bar !== 'undefined' ){
		    setTimeout(function() {
			    $( document ).find('.advads-size-tooltip-h' ).each( function(){
				    $( this ).prepend( tooltipModel.clone() );
				    setToolTipValues( $( this ) );
			    } );		    
		    },1000);
		} else {
		    $( '.advads-size-tooltip-h' ).each( function(){
			    $( this ).prepend( tooltipModel.clone() );
			    setToolTipValues( $( this ) );
		    } );
		}
		
        // add window size tooltip
		$( 'html body' ).each( function( index, elem ){
			if ( 0 != index ) return; // Do not append in iframe
			$( elem ).append( $( '<div class="advads-window-size">' + advadsRespLocalize.windowWidth + ' : <span class="advads-ww"></span> px</div>' ) );
			setWindowWidthTip();
		} );
		
		// Refresh all size tooltips
		window.onresize = function( ev ) {
			$( '.advads-size-tooltip-h' ).each( function(){
				setToolTipValues( $( this ) );
			} );
			setWindowWidthTip();
		};
		
	} );
	
    // Set window width tooltip info on resize
	function setWindowWidthTip() {
		$( '.advads-ww' ).text( $( window ).width() );
	}
	
    // Set ad tooltip values
	function setToolTipValues( hProbe ) {
		var h = hProbe.height();
		var cw = hProbe.width();
		var w = hProbe.find( '.advads-size-tooltip-w' ).width();
		hProbe.find( '.advads-tooltip .tooltip-w' ).text( w );
		hProbe.find( '.advads-tooltip .tooltip-h' ).text( h );
		hProbe.find( '.advads-tooltip .tooltip-cw' ).text( cw );
	}
	
})( jQuery );
