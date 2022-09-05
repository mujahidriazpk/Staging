(function() {
	if ( typeof advanced_ads_pro_visitor_conditions !== 'object' ) {
		return;
	}

	/**
	 * The cookie storage object.
	 *
	 * Since we cannot read expiration times of cookies, we use our own `expires` field to save expiration times.
	 * This allows us to update cookies without updating their expiration times, i.e. without prolonging them.
	 *
	 * @param {string} name   The cookie name.
	 * @param {int}    exdays The number of days before the cookie expires.
	 */
	function cookie_storage( name, exdays ) {
		this.name = name;
		this.exdays = exdays;
		this.data = undefined;
		this.expires = 0;

		var cookie = advads.get_cookie( name );

		if ( ! cookie ) {
			this.data = cookie;
			return;
		}

		try {
			var cookie_obj = JSON.parse( cookie );
		} catch ( e ) {
			this.data = cookie;
			return;
		}

		if ( typeof cookie_obj !== 'object' ) {
			this.data = cookie;
			return;
		}

		this.data = cookie_obj.data;
		this.expires = parseInt( cookie_obj.expires, 10 );
	}

	/**
	 * Check if the cookie data exists.
	 */
	cookie_storage.prototype.exists = function() {
		return typeof this.data !== 'undefined';
	};

	/**
	 * Save the cookie data.
	 *
	 * @param {mixed} data The cookie data.
	 */
	cookie_storage.prototype.save = function( data ) {
		this.data = data;

		get_unix_time_in_seconds = function() {
			return Math.round( ( new Date() ).getTime() / 1000 );
		}

		var remaining_time = this.expires - get_unix_time_in_seconds();

		// Check if the cookie is expired.
		if ( remaining_time <= 0 ) {
			remaining_time = ( this.exdays * 24 * 60 * 60 );
			this.expires = get_unix_time_in_seconds() + remaining_time;
		}

		advads.set_cookie_sec(
			this.name,
			JSON.stringify( {
				expires: this.expires,
				data: this.data,
			} ),
			remaining_time
		);
	};

	advanced_ads_pro_visitor_conditions.cookie_storage = cookie_storage;

	// set cookie for referrer visitor condition.
	var cookie = new cookie_storage( advanced_ads_pro_visitor_conditions.referrer_cookie_name, advanced_ads_pro_visitor_conditions.referrer_exdays );
	if ( ! cookie.exists() && document.referrer !== '' ) {
		cookie.save( document.referrer );
	}

	// Set cookie with page impressions.
	var cookie = new cookie_storage( advanced_ads_pro_visitor_conditions.page_impr_cookie_name, advanced_ads_pro_visitor_conditions.page_impr_exdays );
	if ( ! cookie.exists() ) {
		cookie.save( 1 );
	} else {
		cookie.save( parseInt( cookie.data, 10 ) + 1 || 1 );
	}
} )();
