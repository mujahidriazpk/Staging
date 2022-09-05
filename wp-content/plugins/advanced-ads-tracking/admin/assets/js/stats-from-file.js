;(function($){
    "use strict";
    var spinner = $( '<img src="" alt="loading" class="ajax-spinner" />' );
	
    // limit for stats points
    var MAX_POINTS = 150;
	
	/**
	 * convert date string from date picker (m/d/Y) to the more common Y-m-d format
	 */
	function datePickerToYmd( str ) {
		var split = str.split( '/' );
		if ( 1 == split.length ) {
			split = str.split( '-' );
		}
		return $.zeroise( split[2], 2 ) + '/' + $.zeroise( split[0], 2 ) + '/' + $.zeroise( split[1], 2 );
	}
	
	/**
	 * retrieve info about stats file ( attachment ID ) and adjust the ciew according to te response
	 */
	function getFileInfo( id ) {
		var nonce = window.advadsStatPageNonce;
		var formData = {
			action: 'advads_stats_file_info',
			id: id,
			nonce: nonce,
		};
		$( '#load-stats-from-file' ).prop( 'disabled', false );
		$.statDisplay._reverseDisabled( 'wpbody-content' );
		$.statDisplay.reverseDisableCompareBtn();
		$( '#file-spinner' ).append( spinner );
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: formData,
			success: function ( resp, textStatus, XHR ) {
				$.statDisplay._reverseDisabled( 'wpbody-content' );
				$.statDisplay.reverseDisableCompareBtn();
				$( '.ajax-spinner' ).remove();
				if ( resp.status ) {
					if ( '' != resp.ads && 2 < resp.firstdate.length && 2 < resp.lastdate.length ) {
						var s = new Date( resp.firstdate );
						var l = new Date( resp.lastdate );
						var start = $.formatDate( s, wpDateFormat );
						var last = $.formatDate( l, wpDateFormat );
						$( '#stats-file-description' ).text( statsFileLocale.statsFrom.replace( '%1$s', start ).replace( '%2$s', last ) );
						$( '#stats-attachment-id' ).val( id );
						$( '#stats-attachment-firstdate' ).val( resp.firstdate );
						$( '#stats-attachment-lastdate' ).val( resp.lastdate );
						$( '#stats-attachment-adIDs' ).val( resp.ads );
						$.statDisplay.reset();
						$( '#load-stats-from-file' ).prop( 'disabled', false );
					} else {
						$( '#stats-file-description' ).html( '<span style="color:red">' + statsFileLocale.statsNotFoundInFile + '</span>' );
						$( '#load-stats-from-file' ).prop( 'disabled', true );
					}
				} else {
					$( '#stats-file-description' ).html( '<span style="color:red">' + statsFileLocale.unknownError + '</span>' );
					$( '#load-stats-from-file' ).prop( 'disabled', true );
				}
			},
			error: function ( request, textStatus, err ) {
				$.statDisplay._reverseDisabled( 'wpbody-content' );
				$.statDisplay.reverseDisableCompareBtn();
				$( '.ajax-spinner' ).remove();
				$( '#stats-file-description' ).html( '<span style="color:red">' + statsFileLocale.unknownError + '</span>' );
			}
		});
	}
	
	// get period length
	function getPeriodLenth( val1, val2 ) {
		var max = 0;
		var t1 = ( new Date( val1 ) ).getTime();
		var t2 = ( new Date( val2 ) ).getTime();
		return {
			days: Math.abs( t1 - t2 ) / ( 1000 * 3600 * 24 ),
			weeks: Math.abs( t1 - t2 ) / ( 1000 * 3600 * 24 * 7 ),
		}
	}
	
	// load stats a from file
	$( document ).on( 'click', '#load-stats-from-file', function(){
		if ( ! $( '#stats-attachment-id' ).val() ) return;
		var period = $( '#stats-file-period' ).val();
		var from = ( 'custom' == period )? $( '#stats-file-from' ).val() : '';
		var to = ( 'custom' == period )? $( '#stats-file-to' ).val() : '';
		if ( 'custom' == period ) {
			if ( ! $.advadsIsConsistentPeriod( from, to ) ) {
				$( '#period-td' ).html( $( '<span style="color:red;">' + statsFileLocale.periodNotConsistent + '</span>' ) );
				return false;
			}
			var len = getPeriodLenth( from, to );
			if ( len.weeks > 24 ) {
				// approx 66% of a year
				$( 'select[name="advads-stats[groupby]"]' ).val( 'month' );
			} else if ( len.days > MAX_POINTS ) {
				// approx 40% of a year - 150 points
				$( 'select[name="advads-stats[groupby]"]' ).val( 'week' );
			}
		}
		// convert the date picker date format to Y/m/d for loading stats
		from = datePickerToYmd( from );
		to = datePickerToYmd( to );
		var groupby = $( 'select[name="advads-stats[groupby]"]' ).val();
		var args = {
			period: period,
			from: from,
			to: to,
			file: $( '#stats-attachment-id' ).val(),
			groupby: groupby,
		};
		// set the last buton clicked flag
		$.statDisplay.lastBtn = 'file';
		
		// empty group filter and hide the input
		$( '#display-filter-list .display-filter-group' ).remove();
		$( '#group-filter-wrap' ).css( 'display', 'none' );
		
		// get data
		$.statDisplay.getSinglePeriod( args );
	} );
	
	// open the media frame for selecting a stats file
	$( document ).on( 'click', '#select-file', function( ev ) {
		ev.preventDefault();
		$.advadsMediaFrame({
			notice: $( '#stats-file-description' ),
			context: 'stats-file',
		});
	} );
	
	// on valid file selected ( according to mime info )
	$( document ).on( 'advadsHasValidFile', function( ev, context, id ){
		if ( 'stats-file' == context && id ) {
			getFileInfo( id );
		}
	} );
	
	// hooking into $.statDisplay._updateComparablePeriods()
	$( document ).on( 'advadsTrackComparablePeriods', function( ev, data ){
		var now = new Date();
		
		/**
		 * We do not use WP's time zone offset in this function because we are not querying the DB (which has records in WP local time)
		 */
		var clientTZ = ( now.getTimezoneOffset() * 60 * 1000 );
		
		
		switch ( data.argsA.period ) {
			case 'custom':
				var len = getPeriodLenth( data.argsA.from, data.argsA.to );
				if ( data.argsB ) {
					var prevStartDate = new Date( data.lastArgs['from'] );
					var prevEndDate = new Date( data.lastArgs['to'] );
					prevEndDate.setTime( prevEndDate.getTime() - ( ( len.days + 1 ) * 24 * 3600000 ) );
					prevStartDate.setTime( prevStartDate.getTime() - ( ( len.days + 1 ) * 24 * 3600000 ) );
					var nextStartDate = new Date( data.lastArgs['from'] );
					var nextEndDate = new Date( data.lastArgs['to'] );
					nextEndDate.setTime( nextEndDate.getTime() + ( ( len.days + 1 )  * 24 * 3600000 ) );
					nextStartDate.setTime( nextStartDate.getTime() + ( ( len.days + 1 )  * 24 * 3600000 ) );
					data.result = {
						prev: {
							'from': prevStartDate.getFullYear() + '/' + ( prevStartDate.getMonth() + 1 ) + '/' + prevStartDate.getDate(),
							to: prevEndDate.getFullYear() + '/' + ( prevEndDate.getMonth() + 1 ) + '/' + prevEndDate.getDate(),
							label: statsFileLocale['prev%dDays'].replace( '%d', len.days ),
							disabled: false,
						},
						next: {
							'from': nextStartDate.getFullYear() + '/' + ( nextStartDate.getMonth() + 1 ) + '/' + nextStartDate.getDate(),
							to: nextEndDate.getFullYear() + '/' + ( nextEndDate.getMonth() + 1 ) + '/' + nextEndDate.getDate(),
							label: statsFileLocale['next%dDays'].replace( '%d', len.days ),
							disabled: false,
						},
					};
					if ( ! $.statDisplay.statsB ) {
						if ( 'prev' == $.statDisplay.lastBtn ) {
							data.result.prev.disabled = true;
						} else {
							data.result.next.disabled = true;
						}
					};
				} else {
					var startDate = new Date( data.argsA['from'] );
					startDate.setTime( startDate.getTime() - ( ( len.days + 1 ) * 24 * 3600000 ) );
					var endDate = new Date( data.argsA['to'] );
					endDate.setTime( endDate.getTime() - ( ( len.days + 1 ) * 24 * 3600000 ) );
					data.result = {
						prev: {
							'from': startDate.getFullYear() + '/' + ( startDate.getMonth() + 1 ) + '/' + startDate.getDate(),
							to: endDate.getFullYear() + '/' + ( endDate.getMonth() + 1 ) + '/' + endDate.getDate(),
							label: statsFileLocale['prev%dDays'].replace( '%d', len.days ),
							disabled: false,
						}
					};
					
					startDate = new Date( data.argsA['from'] );
					startDate.setTime( startDate.getTime() + ( ( len.days + 1 ) * 24 * 3600000 ) );
					endDate = new Date( data.argsA['to'] );
					endDate.setTime( endDate.getTime() + ( ( len.days + 1 ) * 24 * 3600000 ) );
					
					data.result.next = {
						'from': startDate.getFullYear() + '/' + ( startDate.getMonth() + 1 ) + '/' + startDate.getDate(),
						to: endDate.getFullYear() + '/' + ( endDate.getMonth() + 1 ) + '/' + endDate.getDate(),
						label: statsFileLocale['next%dDays'].replace( '%d', len.days ),
						disabled: false,
					};
				}
				break;
			case 'firstmonth':
				var firstDateTS = ( new Date( $( '#stats-attachment-firstdate' ).val() ).getTime() ) - clientTZ;
				if ( data.argsB ) {
					var startDate = new Date( firstDateTS );
					startDate.setMonth( data.argsB.offset + startDate.getMonth() + 1, 1 );
					var endDate = new Date( firstDateTS );
					endDate.setMonth( data.argsB.offset + endDate.getMonth() + 2 );
					endDate.setDate( 0 );
					
					data.result.next = {
						'from': startDate.getFullYear() + '/' + ( startDate.getMonth() + 1 ) + '/' + startDate.getDate(),
						to: endDate.getFullYear() + '/' + ( endDate.getMonth() + 1 ) + '/' + endDate.getDate(),
						label: statsFileLocale['nextMonth'],
						disabled: false,
					};
					if ( 1 < data.argsB.offset ) {
						startDate = new Date( firstDateTS );
						startDate.setMonth( data.argsB.offset + startDate.getMonth() - 1, 1 );
						endDate = new Date( firstDateTS );
						endDate.setMonth( data.argsB.offset + endDate.getMonth() );
						endDate.setDate( 0 );
						data.result.prev = {
							'from': startDate.getFullYear() + '/' + ( startDate.getMonth() + 1 ) + '/' + startDate.getDate(),
							to: endDate.getFullYear() + '/' + ( endDate.getMonth() + 1 ) + '/' + endDate.getDate(),
							label: statsFileLocale['prevMonth'],
							disabled: false,
						};
					} else {
						data.result.prev = {
							'from': false,
							to: false,
							label: statsFileLocale['prevMonth'],
							disabled: true,
						};
					}
				} else {
					var startDate = new Date( firstDateTS );
					startDate.setMonth( startDate.getMonth() + 1, 1 );
					var endDate = new Date( firstDateTS );
					endDate.setMonth( endDate.getMonth() + 2 );
					endDate.setDate( 0 );
					
					data.result = {
						prev: {
							'from': false,
							to: false,
							label: statsFileLocale['prevMonth'],
							disabled: true,
						},
						next: {
							'from': startDate.getFullYear() + '/' + ( startDate.getMonth() + 1 ) + '/' + startDate.getDate(),
							to: endDate.getFullYear() + '/' + ( endDate.getMonth() + 1 ) + '/' + endDate.getDate(),
							label: statsFileLocale['nextMonth'],
							disabled: false,
						}
					}
				}
				break;
			default: // latestmonth
				var lastDateTS = ( new Date( $( '#stats-attachment-lastdate' ).val() ).getTime() ) - clientTZ;
				if ( data.argsB ) {
					
					var prevStartDate = new Date( lastDateTS );
					
					prevStartDate.setMonth( data.argsB.offset + prevStartDate.getMonth() - 1, 1 );
					
					var prevEndDate = new Date( lastDateTS );
					prevEndDate.setMonth( data.argsB.offset + prevEndDate.getMonth(), 0 );
					
					
					data.result.prev = {
						'from': prevStartDate.getFullYear() + '/' + ( prevStartDate.getMonth() + 1 ) + '/' + prevStartDate.getDate(),
						to: prevEndDate.getFullYear() + '/' + ( prevEndDate.getMonth() + 1 ) + '/' + prevEndDate.getDate(),
						label: statsFileLocale['prevMonth'],
						disabled: false,
					};
					if ( -2 < data.argsB.offset ) {
						data.result.next = {
							'from': false,
							to: false,
							label: statsFileLocale['nextMonth'],
							disabled: true,
						};
					} else {
						prevStartDate.setMonth( prevStartDate.getMonth() + 2 );
						prevEndDate.setMonth( prevEndDate.getMonth() + 1, 0 );
						data.result.next = {
							'from': prevStartDate.getFullYear() + '/' + ( prevStartDate.getMonth() + 1 ) + '/' + prevStartDate.getDate(),
							to: prevEndDate.getFullYear() + '/' + ( prevEndDate.getMonth() + 1 ) + '/' + prevEndDate.getDate(),
							label: statsFileLocale['nextMonth'],
							disabled: false,
						};
					}
				} else {
					var startDate = new Date( lastDateTS );
					startDate.setMonth( startDate.getMonth() - 1, 1 );
					var endDate = new Date( lastDateTS );
					endDate.setDate( 0 );
					
					data.result = {
						prev: {
							'from': startDate.getFullYear() + '/' + ( startDate.getMonth() + 1 ) + '/' + startDate.getDate(),
							to: endDate.getFullYear() + '/' + ( endDate.getMonth() + 1 ) + '/' + endDate.getDate(),
							label: statsFileLocale['prevMonth'],
							disabled: false,
						},
						next: {
							'from': false,
							to: false,
							label: statsFileLocale['nextMonth'],
							disabled: true,
						}
					}
				}
		}
	} );
	
	// on DOM ready
	$(function(){
        spinner.attr( 'src', adminUrl + 'images/spinner.gif' );
	});
	
})(jQuery);
