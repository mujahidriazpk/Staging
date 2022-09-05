(function($){
	"use strict";
	
	$.advadsIsConsistentPeriod = function( from, to ){
		if ( undefined === from || undefined === to || '' == from || '' == to ) return false;
		var start = from.split( '/' );
		var end = to.split( '/' );
		if ( parseInt( start[2] + start[0] + start[1] ) > parseInt( end[2] + end[0] + end[1] ) ) return false;
		return true;
	}
	
	$( document ).on( 'change', '.advads-period', function(){
		if ( 'custom' == $( this ).val() ) {
			$( this ).siblings( 'input' ).show();
		} else {
			$( this ).siblings( 'input' ).hide();
		}
	} );
	
	$(function(){
		$( '.advads-datepicker' ).datepicker({dateFormat: 'mm/dd/yy'});
	});
	
})(jQuery);
