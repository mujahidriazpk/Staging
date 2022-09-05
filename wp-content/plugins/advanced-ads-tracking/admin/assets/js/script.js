/**
 * check if there is a link in the content field and a tracking url given
 */
jQuery(document).ready(function(){
	jQuery('#advanced-ads-ad-parameters textarea#advads-ad-content-plain').keyup(advads_tracking_check_link);
	jQuery('#advads-url').keyup(advads_tracking_check_link);
	if (Advanced_Ads_Admin.editor && Advanced_Ads_Admin.editor.codemirror)
		Advanced_Ads_Admin.editor.codemirror.on("keyup", advads_tracking_check_link);
	advads_tracking_display_click_limit_field( '' );
});

/**
 * exchange link in ad code with the %link% placeholder.
 * updated to work with CodeMirror in WordPress 4.9
 */
jQuery( document ).on( 'click', '#advads-tracking-link-exchange', function( ev ){
	ev.preventDefault();
	var url = jQuery( '#advads-url' ).val();
	var tval = Advanced_Ads_Admin.get_ad_source_editor_text();
	if ( ! tval || ! url ) { return; };
	var $tval = jQuery( '<p />' ).html( tval );
	if ( $tval.find( 'a[href="' + url + '"]' ).length ) {
		$tval.find( 'a[href="' + url + '"]' ).attr( 'href', '%link%' );
		Advanced_Ads_Admin.set_ad_source_editor_text($tval.html());
		jQuery('.advads-ad-notice-tracking-link-placeholder-missing').hide();
	}	
} );

/**
 * display click tracking limitation fields based on ad type
 * 
 * @param {string} ad_type
 */
function advads_tracking_display_click_limit_field( ad_type ){
	// get current ad type if not given
	if( ! ad_type ){
		ad_type = jQuery('#advanced-ad-type input:checked').val();
	}
	// display / hide click tracking row
	if( 0 <= advads_tracking_clickable_ad_types.indexOf( ad_type ) ) {
		jQuery( '.advads-tracking-click-limit-row' ).show();
	} else {
		jQuery( '.advads-tracking-click-limit-row' ).hide();
	}
	
	// hide target, url and nofollow field for adsense
	var div = jQuery( 'input[name="advanced_ad[tracking][nofollow]"],input[name="advanced_ad[tracking][target]"],#advads-url' ).closest( 'div' );
	var label = div.prev();
	var hr = div.next();
	if ( 'adsense' == ad_type ) {
		div.add( label ).add( hr ).css( 'display', 'none' );
	} else {
		div.add( label ).add( hr ).css( 'display', 'block' );
	}
}
jQuery( document ).on('change', '#advanced-ad-type input', function () {
	var ad_type = jQuery( this ).val()
	advads_tracking_display_click_limit_field( ad_type );
});

/**
 * check if there is a link attribute in the content field that is not %link%
 * @param {obj} contentfield field selector
 * @returns {undefined}
 */
function advads_tracking_check_link(){
	// check if url is given and not empty
    if( ! jQuery('#advads-url').length || '' === jQuery('#advads-url').val() ){
    	return;
    }
    // fetch the contents of the source editor via our global function
    var text = Advanced_Ads_Admin.get_ad_source_editor_text();
    // search for href attribute
    var errormessage = jQuery('.advads-ad-notice-tracking-link-placeholder-missing');
    if( text.search(' href=') > 0 && text.search('%link%') < 0 ){
	    if( errormessage.is(':hidden') ){
		    errormessage.show();
	    }
    } else {
	    // hide error message
	    errormessage.hide();
    }
}

/**
 *  draw the graph
 */
(function($){
	
	$(function(){
		
		// no stats to show yet or not relevant for this ad type (e.g., Analytics tracking method used)
		if ( 'undefined' === typeof advads_stats || false === advads_stats.impressions ) return;
		
		var imprs = [];
		for ( var date in advads_stats.impressions ) {
			var val = advads_stats.impressions[date][advads_stats.ID] || 0;
			imprs.push( [date, parseInt( val ) ] );
		}
		var clicks = [];
		for ( var date in advads_stats.clicks ) {
			var val =  advads_stats.clicks[date][advads_stats.ID] || 0;
			clicks.push( [date, parseInt( val )] );
		}
		
		var graphOptions = {
			axes:{
				xaxis:{
					tickOptions: {},
					tickInterval: '',
				},
				yaxis:{
					min:0,
					formatString:'$%.2f',
					autoscale:true,
					label: '',
				},
				y2axis:{
					min:0,
					autoscale:true,
					label: '',
				}
			},
			highlighter: {
				show: true,
				sizeAdjust: 7.5
			},
			cursor: {
				show: false
			},
			title: {
				show: true
			},
		};
		
		graphOptions.axes.xaxis.renderer = $.jqplot.DateAxisRenderer;
		graphOptions.axes.xaxis.tickInterval = '1 day';
		graphOptions.axes.xaxis.tickOptions.formatString = '%b&nbsp;%#d';
		graphOptions.axes.yaxis.label = advadsStatsLocale.impressions;
		graphOptions.axes.y2axis.label = advadsStatsLocale.clicks;
		
		graphOptions['series'] = [
			{
				color: '#4b5de4',
				highlighter: {
					formatString: '%s, %d ' + advadsStatsLocale.impressions,
				},
				lineWidth: 1,
				markerOptions: {
					size: 5,
					style: 'circle',
				},
			},
			{
				color: '#EA7228',
				highlighter: {
					formatString: '%s, %d ' + advadsStatsLocale.clicks,
				},
				linePattern: 'dashed',
				lineWidth: 2,
				markerOptions: {
					size: 5,
					style: 'filledSquare',
				},
				yaxis: 'y2axis',
			},
		];
		var lines = [imprs, clicks];
		var ticks = [];
    	for (var i in imprs){
    		var x = imprs[i];
    		ticks.push(x[0]);
    	}
    	graphOptions.axes.xaxis.ticks = ticks;
		$.jqplot( 'stats-jqplot', lines, graphOptions );
		
	});
	
})(jQuery);
