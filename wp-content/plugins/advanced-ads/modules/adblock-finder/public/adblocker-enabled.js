/**
 * Check if an ad blocker is enabled.
 *
 * @param {function} callback A callback function that is executed after the check has been done.
 *                            The 'is_enabled' (bool) variable is passed as the callback's first argument.
 */
window.advanced_ads_check_adblocker = ( function ( callback ) {
	var pending_callbacks = [];
	var is_enabled        = null;

	function RAF( RAF_callback ) {
		var fn = window.requestAnimationFrame
			|| window.mozRequestAnimationFrame
			|| window.webkitRequestAnimationFrame
			|| function ( RAF_callback ) {
				return setTimeout( RAF_callback, 16 );
			};

		fn.call( window, RAF_callback );
	}

	RAF( function () {
		// Create a bait.
		var ad       = document.createElement( 'div' );
		ad.innerHTML = '&nbsp;';
		ad.setAttribute( 'class', 'ad_unit ad-unit text-ad text_ad pub_300x250' );
		ad.setAttribute( 'style', 'width: 1px !important; height: 1px !important; position: absolute !important; left: 0px !important; top: 0px !important; overflow: hidden !important;' );
		document.body.appendChild( ad );

		RAF( function () {
			var styles      = window.getComputedStyle && window.getComputedStyle( ad );
			var moz_binding = styles && styles.getPropertyValue( '-moz-binding' );

			is_enabled = ( styles && styles.getPropertyValue( 'display' ) === 'none' )
				|| ( typeof moz_binding === 'string' && moz_binding.indexOf( 'about:' ) !== - 1 );

			// Call pending callbacks.
			for ( var i = 0, length = pending_callbacks.length; i < length; i ++ ) {
				pending_callbacks[i]( is_enabled );
			}
			pending_callbacks = [];
		} );
	} );

	return function ( callback ) {
		if ( is_enabled === null ) {
			pending_callbacks.push( callback );
			return;
		}
		// Run the callback immediately
		callback( is_enabled );
	};
}() );
