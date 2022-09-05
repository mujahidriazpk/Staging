// ads loaded by passive cache busting
window.advadsGAAjaxAds = {};

// ads loaded by ajax cache busting
window.advadsGAPassiveAds = {};

( function( $ ) {
    /**
     * If cache-busting module is enabled.
     * With 'document.ready' this function will be called after 'document.ready'
     * from cache-busting when 'defer' attribute is added to scripts.
     * It is too late, hence do not use 'document.ready'.
     */
    if ( typeof advanced_ads_pro !== 'undefined' ) {
        advanced_ads_pro.observers.add( function( event ) {
            // waiting for the moment when all passive cache-busting ads will be inserted into html
            if ( event.event === 'inject_passive_ads' ) {
				
				var server = 'all';
				if ( $.isArray( event.ad_ids ) && !event.ad_ids.length ) {
					event.ad_ids = {};
				}
				advadsGAPassiveAds = advads_tracking_utils( 'concat', advadsGAPassiveAds, event.ad_ids );
				var filteredIds = removeDelayedAdId( event.ad_ids );
				
                var advads_ad_ids;
                if ( advadsTracking.method === 'frontend' ) {
                    // cache-busting: off + cache-busting: passive
                    advads_ad_ids = advads_tracking_utils( 'concat', advads_tracking_ads, filteredIds );
                    // clean cache-busting: off
                    advads_tracking_ads = [];
                } else {
                    // select only passive cache-busting ads
                    advads_ad_ids = filteredIds;
					server = 'passive';
                }
				advads_track_ads( advads_ad_ids, server );
				
            }
            if ( event.event === 'inject_ajax_ads' ) {
				
				if ( $.isArray( event.ad_ids ) && !event.ad_ids.length ) {
					event.ad_ids = {};
				}
				
				advadsGAAjaxAds = advads_tracking_utils( 'concat', advadsGAAjaxAds,  event.ad_ids );
				
				var filteredIds = removeDelayedAdId( event.ad_ids );
				// ajax ads
				advads_track_ads( filteredIds, 'analytics' );
				
			}
        } );
    }
}( jQuery ) );

/**
 * remove delayed ad ids
 *
 * @param [arr] ids, the original array of ids
 * @return [arr] ids, the filtered array
 */
function removeDelayedAdId( ids ) {
	if ( jQuery( '[data-delayedgatrackid]' ).length ) {
		jQuery( '[data-delayedgatrackid]' ).each(function(){
			var id = parseInt( jQuery( this ).attr( 'data-delayedgatrackid' ) );
			var bid = parseInt( jQuery( this ).attr( 'data-delayedgabid' ) );
			if ( advads_tracking_utils( 'hasAd', ids ) ) {
				if ( 'undefined' != typeof ids[bid] ) {
					var index = ids[bid].indexOf( id );
					if ( -1 != index ) {
						ids[bid].splice( index, 1 );
					}
				}
			}
		});
	}
	return ids;
}

/**
 * on DOM ready 
 */
jQuery(document).ready(function($){
	
	if ( 'undefined' == typeof advads_tracking_ads ) return;
	advads_tracking_ads = removeDelayedAdId( advads_tracking_ads );
	
    if ( typeof advanced_ads_pro === 'undefined' ) {
		if ( advads_tracking_utils( 'hasAd', advads_tracking_ads ) ) {
			for ( var bid in advads_tracking_ads ) {
				if ( 'frontend' == advads_tracking_methods[bid] ) {
					// cache-busting: off
					advads_track_ads( advads_tracking_ads );
					// clean cache-busting: off
					advads_tracking_ads = {1:[]};
				}
			}
		}
    }
	
});

jQuery( document ).on( 'advads_track_ads', function( e, ad_ids ) {
    advads_track_ads( ad_ids );
});

/**
 * on layer ad shown 
 */
jQuery( document ).on( 'advads-layer-trigger', function( e ) {
	if ( 'undefined' == typeof advadsGATracking ) {
		return;
	}
	advads_delayed_track_event( e );
} )

/**
 * on sticky ad shown 
 */
jQuery( document ).on( 'advads-sticky-trigger', function( e ) {
	if ( 'undefined' == typeof advadsGATracking ) {
		return;
	}
	advads_delayed_track_event( e );
} );

/**
 *  delayed tracking event
 */
function advads_delayed_track_event( ev ) {
	
	/**
	 * Retrieve the node with ad ids, can be the sticky/layer placement wrapper, or the ads themselves in case of groups 
	 */
	var $el = jQuery( ev.target );
	var $vector = [];
	if ( $el.attr( 'data-delayedgatrackid' ) ) {
		$vector = $el;
	} else {
		$vector = $el.find( '[data-delayedgatrackid]' );
	}
	
	if ( $vector.length ) {
		var ids = {};
		
		// collect ad ids
		$vector.each(function(){
			var bid = parseInt( jQuery( this ).attr( 'data-delayedgabid' ) );
			if ( 'undefined' == typeof ids[bid] ) {
				ids[bid] = [];
			}
			ids[bid].push( parseInt( jQuery( this ).attr( 'data-delayedgatrackid' ) ) );
		});
		if ( 'undefined' == typeof advadsGATracking.delayedAds ) {
			advadsGATracking.delayedAds = {};
		}
		
		// then send the delayed tracking request
		advadsGATracking.delayedAds = advads_tracking_utils( 'concat', advadsGATracking.delayedAds, ids );
		advads_track_ads( advadsGATracking.delayedAds, 'delayed' );
	}
}

function advads_tracking_utils() {
	if ( !arguments.hasOwnProperty( 0 ) ) return;
	var fn = arguments[0];
	var args = Array.prototype.slice.call( arguments, 1 );
	
	var utils = {
		hasAd: function( data ) {
			for ( var i in data ) {
				if ( jQuery.isArray( data[i] ) ) {
					if ( data[i].length ) {
						return true;
					}
				}
			}
			return false;
		},
		concat: function() {
			var result = {};
			for ( var i in args ) {
				for ( var j in args[i] ) {
					if ( 'undefined' == typeof result[j] ) {
						result[j] = args[i][j];
					} else {
						result[j] = result[j].concat( args[i][j] );
					}
				}
			}
			return result;
		},
		blogUseGA: function( bid ) {
			if ( 'ga' != advads_tracking_methods[bid] && false === advads_tracking_parallel[bid] ) {
				return false;
			}
			if ( '' == advads_gatracking_uids[bid] ) {
				return false;
			}
			return true;
		},
		adsByBlog: function( ads, bid ) {
			var result = {};
			if ( 'undefined' != typeof ads[bid] ) {
				result[bid] = ads[bid];
				return result;
			}
			return {};
		},
	};
	if ( 'function' == typeof utils[fn] ) {
		return utils[fn].apply( null, args );
	}
}

/**
 * track ads
 *
 * @param {arr} advads_ad_ids
 * @param {str} server, to which server the tracking request should be sent all|local|analytics
 */
function advads_track_ads( advads_ad_ids, server ) {
    if ( !advads_tracking_utils( 'hasAd', advads_ad_ids ) ) return; // do not send empty array
	if ( 'undefined' == typeof server ) server = 'all';
    
	for ( var bid in advads_ad_ids ) {
		var data = {
			ads: advads_ad_ids[bid],
		};
        
		if ( advads_tracking_utils( 'blogUseGA', bid ) ) {
			// send tracking data to Google
			if ( 'undefined' == typeof advadsGATracking ) {
				window.advadsGATracking = {};
			}
			if ( 'undefined' == typeof advadsGATracking.deferedAds ) {
				window.advadsGATracking.deferedAds = {};
			}
			if ( 'local' != server ) {
				// ads ID-s already collected and will be sent automatically once the Analytics tracker is ready
				advadsGATracking.deferedAds = advads_tracking_utils(
					'concat',
					advadsGATracking.deferedAds,
					advads_tracking_utils( 'adsByBlog', advads_ad_ids, bid )
				);
				if ( 'delayed' == server ) {
					
					// "Delayed" tracking. Explicitly defined for placements that initially hide ads (timeout/scroll)
					jQuery( document ).trigger( 'advadsGADelayedTrack' );
					var passiveDelayed = {};
					passiveDelayed[bid] = [];
					// also track locally if needed ( passive cache busting )
					if ( -1 == ['frontend','ga'].indexOf( advads_tracking_methods[bid] ) ) {
						
						if ( advads_tracking_utils( 'hasAd', advads_tracking_utils( 'adsByBlog', advadsGAPassiveAds, bid ) ) ) {
							for ( var i in advads_ad_ids[bid] ) {
								if ( -1 != advadsGAPassiveAds[bid].indexOf( advads_ad_ids[bid][i] ) ) {
									passiveDelayed[bid].push( advads_ad_ids[i] );
								}
							}
						}
						if ( passiveDelayed[bid].length ) {
							for( var j in passiveDelayed[bid] ) {
								advadsGAPassiveAds[bid].splice( advadsGAPassiveAds[bid].indexOf( passiveDelayed[j] ), 1 );
							}
							jQuery.post( advads_tracking_urls[bid], {ads:passiveDelayed[bid]}, function(response) {} );
						}
					}
					
				} else {
					
					// normal passive cache busting in parallel tracking
					if (
						'passive' == server &&
						advads_tracking_utils( 'hasAd', advads_tracking_utils( 'adsByBlog', advads_ad_ids, bid ) ) &&
						-1 != ['onrequest','shutdown'].indexOf( advads_tracking_methods[bid] )
					) {
						jQuery.post( advads_tracking_urls[bid], data, function(response) {} );
					}
					
					// the "usual" deferred tracking (once the GA tracker is ready)
					jQuery( document ).trigger( 'advadsGADeferedTrack' );
					
				}
				
			}
			
			if ( advads_tracking_parallel[bid] && 'analytics' != server && advads_tracking_methods[bid] == 'frontend' ) {
				// if concurrent tracking, also send data to the server
				if ( advads_tracking_utils( 'hasAd', advads_tracking_utils( 'adsByBlog', advadsGAAjaxAds, bid ) ) ) {
					
					// remove first all ajax ads (already tracked)
					var removed = [];
					for ( var i in advadsGAAjaxAds[bid] ) {
						var index = data.ads.indexOf( advadsGAAjaxAds[bid][i] );
						if ( -1 != index ) {
							data.ads.splice( index, 1 );
							removed.push( advadsGAAjaxAds[bid][i] );
						}
					}
					if ( removed.length ) {
						for ( var j in removed ) {
							index = advadsGAAjaxAds[bid].indexOf( removed[j] );
							advadsGAAjaxAds[bid].splice( index, 1 );
						}
					}
				}
				
				if ( data.ads.length ) {
					jQuery.post( advads_tracking_urls[bid], data, function(response) {} );
				}
			}
		} else {
			
			if ( 'analytics' != server ) {
				// just send tracking data to the server
				jQuery.post( advads_tracking_urls[bid], {ads:data.ads}, function(response) {} );
			}
		}
		
	}
	
	
}
