;(function($){
	"use strict";
	var HOST = 'https://www.google-analytics.com';
	var BATCH_PATH = '/batch';
	var COLLECT_PATH = '/collect';
	var CLICK_TIMEOUT = 1000; // timeout before aborting click post request to Google
	var CLICK_TIMER = null;

	var clickReqObj = null;

	function abortAndRedirect( url ) {
		if ( null !== CLICK_TIMER ) {
			clearTimeout( CLICK_TIMER );
			CLICK_TIMER = null;
		}
		if ( null !== clickReqObj ) {
			clickReqObj.abort();
			clickReqObj == null;
		}
		 window.open(url, '_blank').focus();
		//window.location = url;
	}

	var advadsTracker = function( name, blogId, UID ) {
		this.name = name;
		this.blogId = blogId
		this.cid = false;
		this.UID = UID;
		this.analyticsObject = null;
		var that = this;
		this.normalTrackingDone = false;

		/**
		 * check if someone has already requested the analytics.js and created a GoogleAnalyticsObject
		 */
		this.analyticsObject = ( 'string' == typeof( GoogleAnalyticsObject ) && 'function' == typeof( window[GoogleAnalyticsObject] ) )? window[GoogleAnalyticsObject] : false;

		if ( false === this.analyticsObject ) {
			
			// No one has requested analytics.js at this point. Require it
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','_advads_ga');

			_advads_ga( 'create', this.UID, 'auto', this.name );
			if( advads_gatracking_anonym ) {
				_advads_ga( 'set', 'anonymizeIp', true );
			}
			_advads_ga(function(){
				var tracker = _advads_ga.getByName( that.name );
				that.readyCB( tracker );
			});
			
		} else {
			
			// The variable has already been created a variable, use it to avoid conflicts.
			
			if ( '_advads_ga' !== GoogleAnalyticsObject ) {
				
				// if the variable has been created by another script, log it.
				console.log( "Advanced Ads Analytics >> using other's variable named `" + GoogleAnalyticsObject + "`" );
				
			}
			
			window[GoogleAnalyticsObject]( 'create', this.UID, 'auto', this.name );
			
			if( advads_gatracking_anonym ) {
				window[GoogleAnalyticsObject]( 'set', 'anonymizeIp', true );
			}
			
			window[GoogleAnalyticsObject](function(){
				var tracker = window[GoogleAnalyticsObject].getByName( that.name );
				that.readyCB( tracker );
			});
			
		}
		
		return this;
	}

	advadsTracker.prototype = {
		contructor: advadsTracker,

		hasCid: function(){
			return ( this.cid && '' !== this.cid );
		},

		readyCB: function( tracker ){
			var that = this;
			this.cid = tracker.get('clientId');
			$( document ).on( 'advadsGADeferedTrack', function( args ){
				that.trackImpressions( false );
			} );
			$( document ).on( 'advadsGADelayedTrack', function(){
				that.trackImpressions( true );
			} );
			this.trackImpressions();
		},

		trackImpressions: function( delayed ){
			if ( 'undefined' == typeof delayed ) {
				delayed = false;
			}
			var trackedAds = [];
			
			/**
			 *  Normal (not deferred) tracking
			 */
			if (
				!this.normalTrackingDone &&
				advads_tracking_utils(
					'hasAd',
					advads_tracking_utils( 'adsByBlog', advads_tracking_ads, this.blogId )
				)
			) {
				trackedAds = trackedAds.concat( advads_tracking_ads[this.blogId] );
			}
			
			if ( 'frontend' == advads_tracking_methods[this.blogId] ) {
				// means parallel tracking. ads ID-s will be sent at the same time as the normal ajax tracking call
				trackedAds = [];
			}
			
			if ( delayed ) {
				if (
					'undefined' != typeof advadsGATracking.delayedAds &&
					advads_tracking_utils(
						'hasAd',
						advads_tracking_utils(
							'adsByBlog',
							advadsGATracking.delayedAds,
							this.blogId
						)
					)
				) {
					// append deferred ads
					trackedAds = trackedAds.concat( advadsGATracking.delayedAds[this.blogId] );
					// empty set deferedAds
					advadsGATracking.delayedAds[this.blogId] = [];
				}
			} else {
				if ( 
					'undefined' != typeof advadsGATracking.deferedAds &&
					advads_tracking_utils(
						'hasAd',
						advads_tracking_utils(
							'adsByBlog',
							advadsGATracking.deferedAds,
							this.blogId
						)
					)
				) {
					// append deferred ads
                    
					trackedAds = trackedAds.concat( advadsGATracking.deferedAds[this.blogId] );
					// set deferedAds to an empty array
					advadsGATracking.deferedAds[this.blogId] = [];
				}
			}
			
			if ( !trackedAds.length ) {
				// no ads to track
				return;
			}
			if ( ! this.hasCid() ) {
				console.log( ' Advads Tracking >> no clientID. aborting ...' );
				return;
			}
			
			var trackBaseData = {
				v: 1,
				tid: this.UID,
				cid: this.cid,
				t: 'event',
				ni: 1,
				ec: 'Advanced Ads',
				ea: advadsGALocale.Impressions,
				dl: document.location.origin + document.location.pathname,
				dp: document.location.pathname,
			};
			
			var payload = "";
			
			for ( var i in trackedAds ) {
				if ( undefined !== advads_gatracking_allads[this.blogId][trackedAds[i]] ) {
					//console.log(advads_gatracking_allads[this.blogId][trackedAds[i]]);
					//Mujahid Code below for Advertiser
					//el: '[' + trackedAds[i] + ']'+'[Advertiser-'+advads_gatracking_allads[this.blogId][trackedAds[i]]['ad_user']+'] ' + advads_gatracking_allads[this.blogId][trackedAds[i]]['title'],
					var adInfo = {
						el: '[' + trackedAds[i] + '] ' + advads_gatracking_allads[this.blogId][trackedAds[i]]['title'],
					};
					//console.log(adInfo);
					var adParam = $.extend( {}, trackBaseData, adInfo );
					payload += $.param( adParam ) + "\n";
				}
			}
			if ( payload.length && 1==2) {
				$.post(
					HOST + BATCH_PATH,
					payload
				);
			}
			
			// set the normaltrackingDone flag if not set yet
			if ( !this.normalTrackingDone ) this.normalTrackingDone = true;

		},

		trackClick: function( id, serverSide, ev, el ){
			if ( ! this.hasCid() ) {
				console.log( ' Advads Tracking >> no clientID. aborting ...' );
				return;
			}
			if ( undefined === serverSide ) serverSide = true;
			//Mujahid Code
			//el: '[' + id + '][Advertiser-'+advads_gatracking_allads[this.blogId][id]['ad_user']+'] '+ advads_gatracking_allads[this.blogId][id]['title'],
			var title = advads_gatracking_allads[this.blogId][id]['title'];
			var trackData = {
				v: 1,
				tid: this.UID,
				cid: this.cid,
				t: 'event',
				ni: 1,
				ec: 'Advanced Ads',
				ea: advadsGALocale.Clicks,
				el: '[' + id + '] '+  title.replace("&nbsp;"," "),
				dl: document.location.origin + document.location.pathname,
				dp: document.location.pathname,
			};
			var payload = $.param( trackData );
			var url = advadsGATracking.adTargets[this.blogId][id];
            if ( 'undefined' != typeof advadsGATracking.postContext ) {
                url = url.replace( '[CAT_SLUG]', advadsGATracking.postContext.cats );
                url = url.replace( '[POST_ID]', advadsGATracking.postContext.postID );
                url = url.replace( '[POST_SLUG]', advadsGATracking.postContext.postSlug );
            }
            url = url.replace( '[AD_ID]', id );
			//Mujahid Code
			var url_old = 'https://woocommerce-642855-2866716.cloudwaysapps.com/linkout/'+id;
			if ( serverSide ) {
				url = $( el ).attr( 'data-href' );
			}
			var newTab = ( $( el ).attr( 'target' ) )? true : false;
			if ( newTab ) {
				// the url is opened in a new tab/window
				$.post( HOST + COLLECT_PATH, payload );
				if ( !serverSide ) {
					// no server side tracking, change the link to the real target before the browser opens a new tab
					$( el ).attr( 'data-href', url );
				}
			} else {
				// intercept the default click event behavior
				ev.preventDefault();
				if ( null === CLICK_TIMER && null === clickReqObj ) {
					CLICK_TIMER = setTimeout( function(){
						abortAndRedirect( url, newTab );
					}, CLICK_TIMEOUT );
					clickReqObj = $.post(
						HOST + COLLECT_PATH,
						payload,
						function(){
							clearTimeout( CLICK_TIMER );
							CLICK_TIMER = null;
							clickReqObj = null;
							//Mujahid Code
							abortAndRedirect( url_old );
						}
					);
				}

			}
		},

	}

	$(function(){
		
		for ( var bid in advads_tracking_methods ) {
			
			if ( advads_tracking_utils( 'blogUseGA', bid ) ) {
				var tracker = new advadsTracker( 'advadsTracker_' + bid, bid, advads_gatracking_uids[bid] );
				( function( _bid, _tracker ){
					
                    var base = advads_tracking_linkbases[_bid];
                    var baseSelector = 'a[data-href^="' + advads_tracking_linkbases[_bid] + '"]';
                    
                    // if using default permalinks.
                    if ( -1 == base.indexOf( '://' ) ) {
                        baseSelector = 'a[data-href*="' + base + '="]';
                    }
                    //alert(baseSelector);
					$( document ).on( 'click', baseSelector + '[data-bid="' + _bid + '"]', function( ev ){
						
						// send click event to Google
                        var id = 0;
                        
                        if ( -1 == base.indexOf( '://' ) ) {
                            // if using default permalinks.
                            var regex = new RegExp( base + '=(\\d+)' );
                            var link = $( this ).attr( 'data-href' );
                            var M = link.match( regex );
                            if ( M && 'undefined' != typeof M[1] ) {
                                id = M[1];
                                id = parseInt( id );
                            }
                        } else {
                            id = $( this ).attr( 'data-href' ).split( advads_tracking_linkbases[_bid] );
                            id = parseInt( id[1] );
                        }
						if ( 'undefined' != typeof advads_gatracking_allads[_bid][id] && advadsGATracking.adTargets[_bid][id] ) {
							
							// clicks on this ad should be tracked
							var serverSide = true;
							if ( 'ga' == advads_tracking_methods[_bid] ) {
								// not parallel tracking, i.e. analytics only
								serverSide = false;
							}
							//Mujahid code and replace href with data-href
							jQuery.confirm({
								title: '',
								columnClass: 'col-md-4 col-md-offset-4',
								closeIcon: true, // hides the close icon.
								content: '<span style="font-size:14px;font-weight:bold;">LEAVING SHOPADOC®</span><br />',
								buttons: {
									Yes: {
										text: 'Confirm',
										btnClass: 'yes_btn',
										action: function(){
													
												_tracker.trackClick( id, serverSide, ev, this );
										}
									},
									No: {
										text: 'Cancel',
										btnClass: 'btn-blue no_btn',
										action: function(){
											//jQuery("#plan_deactive").removeAttr('checked');
										}
									}
								}
							});

						}

					} );
					
				} )( bid, tracker );
			}
			
		}
		
	});

})(jQuery);