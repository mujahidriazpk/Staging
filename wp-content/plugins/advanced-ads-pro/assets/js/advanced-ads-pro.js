;(function($){

	// CFP cookies prefix
	var cname = 'advads_procfp';
	var cname_vc = 'advanced_ads_ad_clicks'; // visitor conditions

	// CFP cookies parameter ( domain/path )
	var PATH = null;
	var DOMAIN = null;

	/**
	 * wrapper for JSON.parse
	 */
	function jsonDecode( str ) {
		try {
			var res = JSON.parse( str );
			return res;
		} catch( Ex ) {
			return null;
		}
	}

	// Max ad click conditions/ passive Cache busting.

	$( document ).on( 'advads-passive-cb-conditions', function( e, cbs ) {
		cbs.conditions['ad_clicks'] = 'check_ad_clicks';
		cbs['check_ad_clicks'] = function ( options, ad ) {
			if ( advads.cookie_exists( cname_vc + '_' + ad.id ) ) {
				var C_vc = advads.get_cookie( cname_vc + '_' + ad.id );
				C_vc = jsonDecode( C_vc );
			}

			if ( C_vc ) {
				var now = parseInt( new Date().getTime() / 1000 );
				for ( var i in C_vc ) {

					if ( '_' + options.expiration == i ) {

						// if still valid counter and click limit reached
						if ( C_vc[i]['ttl'] >= now && C_vc[i]['count'] >= parseInt( options.limit ) ) {
							return false;
						}

					}

				}
			}

			return true;
		};
	});

	var cfpTracker = function() {
		this.$elements     = {};
		this.currentIFrame = false;
		this.focusLost     = false;
		this.wrappers      = [
			'.google-auto-placed'
		];
		this.attributes    = {
			'data-anchor-status':   'displayed',
			'data-vignette-loaded': 'true'
		};
		this.lastClick     = 0;
		this.init();
	}

	cfpTracker.prototype = {

		constructor: cfpTracker,

		init: function () {
			const that = this;
			let touchmoved;

			// increment counter on click on link
			$( document ).on( 'click', 'a[data-cfpa]', function () {
				that.onClick( parseInt( $( this ).attr( 'data-cfpa' ) ) );
			} );

			// Increment counter on blur only if an iframe has recently been hovered.
			$( window ).on( 'blur', function ( e ) {

				// Use timeout of 0 ms as a workaround to make sure that the active element has changed correctly.
				setTimeout( function () {
					if ( ! that.currentIFrame ) {
						// Loop parent nodes from the target to the delegation node to recognise iframe clicks.
						for ( let target = document.activeElement; target && target !== this && target !== document; target = target.parentNode ) {
							that.currentIFrame = that.checkWrappers( target );
							if ( that.currentIFrame ) {
								break;
							}
						}
					}

					if ( that.currentIFrame ) {
						that.onClick( that.currentIFrame );
						that.focusLost = true;
						top.focus();
					}
				}, 0 );
			} );

			// mouse passes over an ad that has not yet been initialized (adsense and other distant ads - OR just an ad that contains no link nor iframes)
			$( document ).on( 'mouseenter', 'div[data-cfpa]', function () {
				var id = parseInt( $( this ).attr( 'data-cfpa' ) );
				that.addElement( id );
			} );

			// Detect swipe and click on mobile devices.
			document.addEventListener( 'touchmove', function () {
				touchmoved = true;
			}, false );
			document.addEventListener( 'touchstart', function () {
				touchmoved = false;
			}, false );

			// Detect desktop and mobile clicks.
			['click', 'touchend'].forEach(
				function ( event ) {
					document.addEventListener( event, function ( e ) {
						// Prevent swipes and simultaneous clicks on wrapper and element as well as fast clicks in a row
						if ( touchmoved || ( that.getTimestamp() - that.lastClick ) < 1 ) {
							return;
						}
						let adId = null;
						// Loop parent nodes from the target to the delegation node.
						for ( let target = e.target; target && target !== this && target !== document; target = target.parentNode ) {

							// Loop all predefined wrappers to detect clicks on Google Auto Ads iframes.
							adId = that.checkWrappers( target );
							if ( adId ) {
								that.onClick( adId );
								break;
							}

							// Check if clicked element is a cfp wrapper.
							if ( target.hasAttribute( 'data-cfpa' ) && target.hasAttribute( 'data-cfptl' ) ) {
								adId = parseInt( target.getAttribute( 'data-cfpa' ), 10 );
								that.onClick( adId );
								break;
							}
						}
					} );
				}
			);
		},

		/**
		 * Create current timestamp
		 *
		 * @return {number}
		 */
		getTimestamp: function () {
			return Math.floor( Date.now() / 1000 );
		},

		/**
		 * Check if target element is a predefined wrapper or has Google Auto Ads attributes.
		 *
		 * @param target
		 * @returns {string|null}
		 */
		checkWrappers: function ( target ) {
			for ( let i = 0, wrappersLength = this.wrappers.length, selector = null; i < wrappersLength; i ++ ) {
				selector = this.wrappers[i];
				if ( target.matches && target.matches( selector ) ) {
					return ( selector === '.google-auto-placed' ) ? 'google-auto-placed' : null;
				}
			}
			for ( const [key, value] of Object.entries( this.attributes ) ) {
				if ( target.hasAttribute( key ) && target.getAttribute( key ) === value ) {
					return 'google-auto-placed';
				}
			}
			return null;
		},

		addElement: function( $el ) {
			if ( false === $el instanceof jQuery ) {
				// Select the first ad since there may be multiple copies of the same ad on the page.
				$el = $( 'div[data-cfpa="' + $el + '"]' ).first();
			}

			var hasIframe = $el.find( 'iframe' ).length ? true : false;
			if ( !hasIframe ) {
				if ( !$el.find( 'a' ).length ) {
					// no an anchor and no iframe -- likely and ad that is not yet loaded (adsense or other ad network)
					return;
				}
			}

			var adID = parseInt( $el.attr( 'data-cfpa' ) );

			this.$elements[adID] = $el;

			// remove attribute from the wrapper
			$el.removeAttr( 'data-cfpa' );

			// And then move it to the first anchor or iframe found
			if ( hasIframe ) {
				$el.find( 'iframe' ).first().attr({
					'data-cfpa': adID,
				})
				if ( $el.attr( 'data-cfph' ) ) {
					$el.find( 'iframe' ).first().attr({
						'data-cfph': $el.attr( 'data-cfph' ),
					})
				}
			} else {
				$el.find( 'a' ).not( '.advads-edit-button' ).first().attr({
					'data-cfpa': adID,
				})
				if ( $el.attr( 'data-cfph' ) ) {
					$el.find( 'a' ).not( '.advads-edit-button' ).first().attr({
						'data-cfph': $el.attr( 'data-cfph' ),
					})
				}
			}
			// remove Hours attribute from the wrapper
			$el.removeAttr( 'data-cfph' );

			// update TTL field for all outdated counter (visitor conditions)
			if ( advads.cookie_exists( cname_vc + '_' + adID ) ) {

				var C_vc = advads.get_cookie( cname_vc + '_' + adID );
				C_vc = jsonDecode( C_vc );

				if ( C_vc ) {
					var now = parseInt( new Date().getTime() / 1000 ), cookie_modified = false;
					for ( var i in C_vc ) {
						if ( !C_vc.hasOwnProperty( i ) ) continue;
						if ( 'exp' == i ) continue;
						if ( C_vc[i]['ttl'] < now ) {
							var period = parseFloat( i.substr( 1 ) );
							var newTTL = C_vc[i]['ttl'];
							while ( newTTL < now ) {
								newTTL += period * 60 * 60;
							}
							C_vc[i]['ttl'] = newTTL;
							C_vc[i]['count'] = 0;
							cookie_modified = true;
						}
					}
					if ( cookie_modified ) {
						var expTime = new Date( C_vc['exp'] );
						advads.set_cookie_sec( cname_vc + '_' + adID, JSON.stringify( C_vc, 'false', false ), parseInt( expTime.getTime() / 1000 ), PATH, DOMAIN );
					}
				}

			}
		},

		/**
		 * Ban the visitor
		 */
		_banVisitor: function() {
			var now = new Date();
			var d = new Date();
			d.setTime( d.getTime() + ( advadsCfpBan*24*60*60*1000 ) );
			var ban = ( d.getTime() - now.getTime() ) / 1000;
			advads.set_cookie_sec( 'advads_pro_cfp_ban', 1, ban, PATH, DOMAIN );

			// Select all top level ad wrappers and delete them.
			jQuery( '[data-cfptl]' ).remove();
			// Select Google AdSense Auto Ads and delete them.
			this.wrappers.forEach( function ( wrapper ) {
				jQuery( wrapper ).remove();
			} );
			for ( const [key, value] of Object.entries( this.attributes ) ) {
				jQuery( '[' + key + '="' + value + '"]' ).remove();
			}
		},

		onClick: function( ID ){
			var C          = false,
				C_vc       = false,
				that       = this;
			this.lastClick = this.getTimestamp();

			if ( 'google-auto-placed' !== ID && $( '[data-cfpa="' + ID + '"]' ).attr( 'data-cfph' ) ) {
				// if there are some visitor conditions, use the vc cookie

				if ( advads.cookie_exists( cname_vc + '_' + ID ) ) {
					C_vc = advads.get_cookie( cname_vc + '_' + ID );
					C_vc = jsonDecode( C_vc );
				}

				if ( C_vc ) {
					// Cookie already exists, increment each counter (keep expiration time)
					for ( var h in C_vc ) {
						if ( !C_vc.hasOwnProperty( h ) ) continue;
						if ( 'exp' == h ) continue;
						var count = parseInt( C_vc[h]['count'] );
						C_vc[h]['count'] = count + 1;
					}
					var now = new Date();
					var expiry = new Date( C_vc.exp );
					var expirySecs = parseInt( ( expiry.getTime() - now.getTime() ) / 1000 );
					advads.set_cookie_sec( cname_vc + '_' + ID, JSON.stringify( C_vc, 'false', false ), expirySecs, PATH, DOMAIN );
				} else {
					// create a new cookie
					var H = $( '[data-cfpa="' + ID + '"]' ).attr( 'data-cfph' ).split( '_' );
					var cval = {}, maxHValue = 0;

					var d = new Date();
					var now = new Date();

					for ( var h in H ) {
						if ( parseFloat( H[h] ) > maxHValue ) {
							maxHValue = parseFloat( H[h] );
						}
						cval['_' + H[h]] = {
							count: 1,
							ttl: parseInt( ( ( now.getTime() / 1000 ) + ( parseFloat( H[h] ) * 3600 ) ) ),
						};
					}

					// use the longest hour value for the expiry time
					d.setTime( d.getTime() + ( maxHValue * 60 * 60 * 1000 ) );
					var expires = "expires="+ d.toUTCString();
					var expirySecs = parseInt( ( d.getTime() - now.getTime() ) / 1000 );

					cval['exp'] = expires;
					advads.set_cookie_sec( cname_vc + '_' + ID, JSON.stringify( cval, 'false', false ), expirySecs, PATH, DOMAIN );
				}

			}

			// use the module wide CFP cookie
			if ( advads.cookie_exists( cname + '_' + ID ) ) {
				C = advads.get_cookie( cname + '_' + ID );
				C = jsonDecode( C );
			}
			if ( C ) {
				// Cookie already exists, increment the counter (keep expiration time)

				var count = parseInt( C.count );
				C.count = count +1;
				var now = new Date();
				var expiry = new Date( C.exp );
				var expirySecs = ( expiry.getTime() - now.getTime() ) / 1000;
				advads.set_cookie_sec( cname + '_' + ID, JSON.stringify( C, 'false', false ), expirySecs, PATH, DOMAIN );
				if ( advadsCfpClickLimit <= C.count && 'undefined' != typeof advadsCfpBan ) {
					// CFP module enabled - ban this visitor
					that._banVisitor();
				}
			} else {
				// create a new cookie

				var d = new Date();
				var now = new Date();
				d.setTime( d.getTime() + ( advadsCfpExpHours*60*60*1000 ) );
				var expires = "expires="+ d.toUTCString();
				var expirySecs = ( d.getTime() - now.getTime() ) / 1000;
				advads.set_cookie_sec( cname + '_' + ID, '{"count":1,"exp":"' + expires + '"}', expirySecs, PATH, DOMAIN );
				if ( advadsCfpClickLimit === 1 && 'undefined' != typeof advadsCfpBan ) {
					// CFP module enabled - ban this visitor
					that._banVisitor();
				}
			}
		},
	}

	$( function(){

		/**
		 * Max ad click ( visitor conditions )
		 */
		window.advadsProCfp = new cfpTracker();

		// IFRAME click tracking
		$( document ).on( 'mouseenter', 'iframe[data-cfpa]', function(e){
			var ID                     = parseInt( $( this ).attr( 'data-cfpa' ) );
			advadsProCfp.currentIFrame = ID;
		} ).on( 'mouseenter', '.google-auto-placed', function(e){
			// Use the same ID for all Google AdSense Auto Ads.
			advadsProCfp.currentIFrame = 'google-auto-placed';
		} ).on( 'mouseleave mouseout', '[data-cfpa], .google-auto-placed', function(){
			advadsProCfp.currentIFrame = false;
			if ( advadsProCfp.focusLost ) {
				advadsProCfp.focusLost = false;
				$( window ).trigger( 'focus' );
			}
		} );

		// construct all elements already present in the queue
		for( var i in advadsCfpQueue ) {
			if ( advadsCfpQueue.hasOwnProperty( i ) ) {
				advadsProCfp.addElement( advadsCfpQueue[i] );
			}
		}

		advadsCfpQueue = [];

		/**
		 * Click fraud protection module.
		 */
		if ( 'undefined' == typeof window.advadsCfpPath ) return;

		// get the path/domain parameter to use in cookies
		if ( '' != advadsCfpPath ) {
			PATH = advadsCfpPath;
		}

		if ( '' != advadsCfpDomain ) {
			DOMAIN = advadsCfpDomain;
		}


	} );

})(window.jQuery);
