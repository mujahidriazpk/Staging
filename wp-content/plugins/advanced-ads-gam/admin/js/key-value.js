/**
 * Key value targeting UI
 *
 */
(function ($) {
	"use strict";

	/**
	 * Sanitize value field
	 *
	 * @link https://support.google.com/admanager/answer/9796369#name-versus-display-name
	 *
	 * @param [string] str the original value.
	 */
	function sanitizeValue(str) {

		// case insensitive.
		str = str.toLowerCase();

		// only alphanumeric chars|can not contain space (actually allowed but makes front end implantation simpler)
		str = str.replace(/[^0-9a-z_]/ig, '_');

		return str;
	}

	/**
	 * Sanitize post meta value field (meta_key)
	 *
	 * @link https://support.google.com/admanager/answer/9796369#name-versus-display-name
	 *
	 * @param [string] str the original value.
	 */
	function sanitizeMeta(str) {

		// case insensitive.
		str = str.toLowerCase();

		// only alphanumeric chars|can not contain space (actually allowed but makes front end implantation simpler)
		str = str.replace(/[^0-9a-z_-\s]/ig, '_');

		return str;
	}

	/**
	 * Sanitize key field
	 *
	 * @link https://support.google.com/admanager/answer/9796369#name-versus-display-name
	 *
	 * @param [string] str the original key value.
	 */
	function sanitizeKey(str) {

		// case insensitive.
		str = str.toLowerCase();

		// only alphanumeric chars|can not contain space.
		str = str.replace(/[^0-9a-z_]/ig, '_');

		// can not start with a number.
		if (-1 != ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'].indexOf(str.charAt(0))) {
			str = '_' + str;
		}

		return str;
	}

	/**
	 * Add a row of input
	 *
	 * @param [string] type the key value type.
	 * @param [string] key the key (non sanitized).
	 * @param [string] value the value (non sanitized).
	 */
	function addKVPair(type, key, value, archives) {
		var html = '<tr>';
		if ('undefined' == typeof value) {
			value = false;
		}
		html += '<td data-th="' + advadsGamKvsi18n.type + '">' +
		gamAdvancedAdsJS.kvTypes[type].name +
		'<input type="hidden" name="advanced_ad[gam][type][]" value="' + type + '">' +
		'</td>' +
		'<td data-th="' + advadsGamKvsi18n.key + '"><code>' + sanitizeKey(key) + '</code>' +
		'<input type="hidden" name="advanced_ad[gam][key][]" value="' + sanitizeKey(key) + '">' +
		'</td>';
		if (value) {
			html += '<td data-th="' + advadsGamKvsi18n.value + '">' +
			'<code>' + value + '</code>' +
			'<input type="hidden" name="advanced_ad[gam][value][]" value="' + value + '">' +
			'</td>';
		} else {
			if (-1 != gamAdvancedAdsJS.kvTypes[type].html.indexOf('onarchives')) {
				var mkup = gamAdvancedAdsJS.kvTypes[type].html;
				if (archives) {
					mkup = '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' +
						advadsGamKvsi18n['termsOnArchives'] + '</span>' +
						'<input type="hidden" name="advanced_ad[gam][onarchives][]" value="1" />';
				} else {
					mkup = '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' +
						advadsGamKvsi18n['termsNotOnArchives'] + '</span>' +
						'<input type="hidden" name="advanced_ad[gam][onarchives][]" value="0" />';
				}
				html += '<td data-th="' + advadsGamKvsi18n.value + '">' + mkup + '</td>';
			} else {
				html += '<td data-th="' + advadsGamKvsi18n.value + '">' + gamAdvancedAdsJS.kvTypes[type].html + '<input type="hidden" name="advanced_ad[gam][onarchives][]" value="0" /></td>';
			}
		}
		html += '<td><i class="dashicons dashicons-dismiss" title="' + gamAdvancedAdsJS.i18n['remove'] + '"></i></td></tr>';
		$('#advads-gam-keyvalue-table tbody').append($(html));
		if (-1 == ['custom', 'postmeta', 'usermeta'].indexOf(type)) {
			$('#advads-gam-kv-type option[value="' + type + '"]').remove();
		}
		$('#advads-gam-kv-type').val('custom').trigger('change');
		enableKVPairBtn(false);
	}

	/**
	 * Enable (or disable) the add key-value pair button (on duplicate)
	 *
	 * @param [bool] enable Whether to enable or disable the button.
	 */
	function enableKVPairBtn(enable) {
		if ('undefined' == typeof enable) {
			enable = true;
		}
		if (false === enable) {
			$('#advads-gam-add-kvpair').removeClass('button-primary');
			$('#advads-gam-add-kvpair').addClass('button-secondary');
			$('#advads-gam-add-kvpair').prop('disabled', true);
		} else {
			$('#advads-gam-add-kvpair').addClass('button-primary');
			$('#advads-gam-add-kvpair').removeClass('button-secondary');
			$('#advads-gam-add-kvpair').prop('disabled', false);
		}
	}

	/** Events binding **/

	// "enter" key event
	$(document).on('keydown', '#advads-gam-kv-key-input, #advads-gam-kv-value-input', function (ev) {
		var keycode = (ev.keyCode ? ev.keyCode : ev.which);
		if (keycode == '13') {
			ev.preventDefault();
			if ('' == $(this).val()) {
				return;
			}
			$('#advads-gam-add-kvpair').trigger('click');
		}
	});

	// remove a key value pair.
	$(document).on('click', '#advads-gam-keyvalue-div table tbody tr .dashicons-dismiss', function () {
		var type = $(this).closest('tr').find('input[name="advanced_ad\[gam\]\[type\]\[\]"]').val();
		if (-1 == ['custom', 'postmeta', 'usermeta'].indexOf(type)) {
			$('#advads-gam-kv-type').append('<option value="' + type + '">' + gamAdvancedAdsJS.kvTypes[type].name + '</option>');
		}
		$(this).closest('tr').remove();
	});

	// add key button.
	$(document).on('click', '#advads-gam-add-kvpair', function (ev) {
		ev.preventDefault();
		var key = $('#advads-gam-kv-key-input').val();
		if (!key) {
			return
		}
		var value = null;
		if ($('#advads-gam-kv-value-input').length) {
			value = $('#advads-gam-kv-value-input').val();
			if (!value) {
				return;
			}
			if (-1 != ['postmeta', 'usermeta'].indexOf($('#advads-gam-kv-type').val())) {
				value = sanitizeMeta(value);
			} else {
				value = sanitizeValue(value);
			}
			$('#advads-gam-kv-value-input').val('');
		}
		$('#advads-gam-kv-key-input').val('');
		var archives = false;
		if ($('#advads-gam-kv-value-td .onarchives').length && $('#advads-gam-kv-value-td .onarchives').prop('checked')) {
			archives = true;
		}
		addKVPair($('#advads-gam-kv-type').val(), key, value, archives);
	});

	// key type change.
	$(document).on('change', '#advads-gam-kv-type', function () {
		var type = $(this).val();
		var valueColumn = gamAdvancedAdsJS.kvTypes[type].html;
		$('#advads-gam-kv-value-td').html(valueColumn);
	});

	// key input change.
	$(document).on('keyup', '#advads-gam-kv-key-input', function () {
		var key = $('#advads-gam-kv-key-input').val();
		var safeKey = sanitizeKey(key);
		if ('' == safeKey) {
			enableKVPairBtn(false);
		} else {
			enableKVPairBtn();
			if (key != safeKey) {
				$(this).siblings('p').remove();
				$(this).after($('<p class="description">' + gamAdvancedAdsJS.i18n['willBeCreatedAs'] + ' <code>' + safeKey + '</code></p>'));
			} else {
				$(this).siblings('p').remove();
			}
		}
	})

	// custom key value change.
	$(document).on('keyup', '#advads-gam-kv-value-input', function () {
		$('#advads-gam-add-kvpair').prop('disabled', true);
		var value = $('#advads-gam-kv-value-input').val();
		var safeValue = sanitizeValue(value);
		if (-1 != ['postmeta', 'usermeta'].indexOf($('#advads-gam-kv-type').val())) {
			safeValue = sanitizeMeta(value);
		}
		if ('' == safeValue) {
			enableKVPairBtn(false);
		} else {
			enableKVPairBtn();
			if (value != safeValue) {
				$(this).siblings('p').remove();
				$(this).after($('<p class="description">' + gamAdvancedAdsJS.i18n['willBeCreatedAs'] + ' <code>' + safeValue + '</code></p>'));
			} else {
				$(this).siblings('p').remove();
			}
		}
	})

	/**
	 * On DOM ready.
	 */
	$(function () {});

})(window.jQuery);
