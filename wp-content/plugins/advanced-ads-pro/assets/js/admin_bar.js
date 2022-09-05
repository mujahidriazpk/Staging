/**
 * Advanced ads cache-busting admin bar.
 */

var advanced_ads_pro_admin_bar;

if ( ! advanced_ads_pro_admin_bar ) {
	advanced_ads_pro_admin_bar = {
		offset: 0,
		adminBar: null,
		bufferedAds: [],

		/**
		 * Observe ads inserted using Cache Busting
		 *
		 * @param {object} event Cache Busting event.
		 */
		observe: function ( event ) {
			var ad, that = advanced_ads_pro_admin_bar, ref;
			if ( event.event === 'hasAd' && event.ad && event.ad.title && event.ad.cb_type !== 'off' ) {
				if ( ! that.adminBar ) {
					// No admin-bar yet: buffer.
					that.bufferedAds.push( event.ad );
				} else {
					// Flush buffer if not empty.
					if ( that.bufferedAds.length > 0 ) {
						that.flush();
					}
					// Inject current ad.
					that.inject( event.ad );
				}
			}
		},

		/**
		 * Flush earlier collected items.
		 */
		flush: function() {
			var that = advanced_ads_pro_admin_bar, i = 0;
			for (i = that.bufferedAds.length - 1; i >= 0; i-- ) {
				that.inject( that.bufferedAds[i] );
			}
			that.bufferedAds = [];
		},

		/**
		 * Inject an ad.
		 *
		 * @param {object} ad An ad to inject.
		 */
		inject: function ( ad ) {
			var that = advanced_ads_pro_admin_bar;
			if ( that.offset === 0 ) {
				// Remove 'No Ads found' `<li>`.
				jQuery( '#wp-admin-bar-advads_no_ads_found' ).remove();
			}

			that.adminBar.append( '<li id="wp-admin-bar-advads_current_ad_' + that.offset + '"><div class="ab-item ab-empty-item">' + ad.title + ' (' + ad.type + ')</div></li>' );
			that.offset += 1;
		}
	};
}

if ( typeof advanced_ads_pro !== 'undefined' ) {
	advanced_ads_pro.observers.add( advanced_ads_pro_admin_bar.observe );
}

( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {
	advanced_ads_pro_admin_bar.adminBar = jQuery( '#wp-admin-bar-advads_current_ads-default' );
	advanced_ads_pro_admin_bar.flush();

	if ( window.advads_admin_bar_items ) {
		// Append items that do not use cache-busting.
		window.advads_admin_bar_items.forEach( advanced_ads_pro_admin_bar.inject )
	}
} );
