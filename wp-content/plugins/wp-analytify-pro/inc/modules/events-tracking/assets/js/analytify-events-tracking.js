"use strict";

const eventsTrackingMode = Analytify_Event.tracking_mode;

var AnalytifyEventTracking = function () {

	var category = '';
	var action = '';
	var label = '';

	window.addEventListener("load", function () {
		document.body.addEventListener("click", analyftify_track_event, false);
	}, false);

	function analytify_track_event_send(category, action, label) {

		if ('gtag' === eventsTrackingMode) {
			gtag('event', action, {
				'event_category': category,
				'event_label': label
			});
		} else {
			ga('send', 'event', category, action, label);
		}

	}

	function analyftify_track_event(event) {
		if (Analytify_Event.is_track_user != '1') {
			return;
		}

		event = event || window.event;

		var target = event.target || event.srcElement;

		// If link is not define. Get the parent link.
		while (target && (typeof target.tagName == 'undefined' || target.tagName.toLowerCase() != 'a' || !target.href)) {
			target = target.parentNode;
		}

		// if its links
		if (target && target.href) {
			var type = get_tracking_type(target);

			if (type == 'outbound') {
				category = target.getAttribute('data-vars-ga-category') || 'outbound-link';
				action = target.getAttribute('data-vars-ga-action') || target.href;
				label = target.getAttribute('data-vars-ga-label') || target.title || target.innerText || target.href;
				analytify_track_event_send(category, action, label);
			} else if (type == 'download') {
				category = target.getAttribute('data-vars-ga-category') || 'download';
				action = target.getAttribute('data-vars-ga-action') || target.href;
				label = target.getAttribute('data-vars-ga-label') || target.title || target.innerText || target.href;
				analytify_track_event_send(category, action, label);
			} else if (type == 'tel') {
				category = target.getAttribute('data-vars-ga-category') || 'tel';
				action = target.getAttribute('data-vars-ga-action') || target.href;
				label = target.getAttribute('data-vars-ga-label') || target.title || target.innerText || target.href;
				analytify_track_event_send(category, action, label);
			} else if (type == 'external') {
				category = target.getAttribute('data-vars-ga-category') || 'external';
				action = target.getAttribute('data-vars-ga-action') || target.href;
				label = target.getAttribute('data-vars-ga-label') || target.title || target.innerText || target.href;
				analytify_track_event_send(category, action, label);
			} else if (type == 'mailto') {
				category = target.getAttribute('data-vars-ga-category') || 'mail';
				action = target.getAttribute('data-vars-ga-action') || target.href;
				label = target.getAttribute('data-vars-ga-label') || target.title || target.innerText || target.href;
				analytify_track_event_send(category, action, label);
			}

		}
	}

	function trim_link_string(x) {
		return x.replace(/^\s+|\s+$/gm, '');
	}

	function get_affiliate_links() {
		if (typeof Analytify_Event.affiliate_link !== 'undefined') {
			return Analytify_Event.affiliate_link;
		} else {
			return [];
		}
	}

	function get_tracking_type(el) {
		var type = 'unknown';
		var link = el.href;
		var extension = '';
		var hostname = el.hostname;
		var protocol = el.protocol;
		var pathname = el.pathname;
		var download_extension = Analytify_Event.download_extension;
		var currentdomain = Analytify_Event.root_domain;
		var affiliate_links = get_affiliate_links();
		var index, len;

		if (link.match(/^javascript\:/i)) { // if its a JS link, it's internal
			type = 'internal';
		} else if (trim_link_string(protocol) == 'tel' || trim_link_string(protocol) == 'tel:') { // Track telephone event.
			type = 'tel';
		} else if (trim_link_string(protocol) == 'mailto' || trim_link_string(protocol) == 'mailto:') { // Track mail event.
			type = 'mailto';
		} else if (hostname.length > 0 && currentdomain.length > 0 && !hostname.endsWith(currentdomain)) { // Track external links.
			type = 'external';
		} else if (affiliate_links.length > 0 && pathname.length > 0) { // Track outbound links.
			for (index = 0, len = affiliate_links.length; index < len; ++index) {
				if (affiliate_links[index]['path'].length > 0 && pathname.startsWith(affiliate_links[index]['path'])) {
					label = affiliate_links[index]['label'];
					type = 'outbound';
					break;
				}
			}

		}

		// Track download files.
		if (type === 'unknown' && download_extension.length > 0) {
			try {
				var regExp = new RegExp(".*\\.(" + download_extension + ")(\\?.*)?$");
			} catch (e) {
				console.log('Analytify Event Error: Invalid RegExp');
			}

			if (typeof regExp != 'undefined' && el.href.match(regExp)) {
				type = 'download';
			}
		}

		if (type === 'unknown') {
			type = 'internal';
		}

		return type;
	}

	var prevHash = window.location.hash;

	function analytify_track_hash() {
		/* if hash tracking is enabled. */
		if (Analytify_Event.anchor_tracking == 'on' && prevHash != window.location.hash) {
			prevHash = window.location.hash;

			if ('gtag' === eventsTrackingMode) {
				gtag('config', trackingCode, { 'page_path': location.pathname + location.search + location.hash });
			} else {
				ga('set', 'page', location.pathname + location.search + location.hash);
				ga('send', 'pageview');
			}
		}
	}
	
	window.addEventListener('hashchange', analytify_track_hash, false);

}

var AnalytifyEventTrackingObject = new AnalytifyEventTracking();