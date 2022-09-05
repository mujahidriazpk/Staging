/**
 * If there is no cache-busting script add this to decrypt TCF privacy encrypted ads.
 */
if ( ! advanced_ads_pro ) {
	var advanced_ads_pro = {
		observers: jQuery.Callbacks()
	};
}

document.addEventListener( 'advanced_ads_privacy', function ( event ) {
	if (
		event.detail.previousState !== 'unknown'
		&& ! ( event.detail.previousState === 'rejected' && event.detail.state === 'accepted' )
	) {
		return;
	}

	if ( event.detail.state === 'accepted' || event.detail.state === 'not_needed' ) {
		var encodedAd = 'script[type="text/plain"][data-tcf="waiting-for-consent"]';

		var decoded_ads = {},
			decode_ad   = function ( node ) {
				if ( typeof node.dataset.noTrack === 'undefined' || node.dataset.noTrack !== 'impressions' ) {
					if ( ! decoded_ads.hasOwnProperty( node.dataset.bid ) ) {
						decoded_ads[node.dataset.bid] = [];
					}
					decoded_ads[node.dataset.bid].push( parseInt( node.dataset.id, 10 ) );
				}
				advads.privacy.decode_ad( node );
			};

		// Find all scripts and decode them.
		document.querySelectorAll( encodedAd ).forEach( function ( node ) {
			decode_ad( node );
		} );

		if ( Object.keys( decoded_ads ).length ) {
			advanced_ads_pro.observers.fire( {event: 'advanced_ads_decode_inserted_ads', ad_ids: decoded_ads} );
		}
	}
} );
