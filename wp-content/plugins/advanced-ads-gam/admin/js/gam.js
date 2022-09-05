/**
 * Google Ad Manager network JS for dashboard
 */

class AdvanvedAdsNetworkGam extends AdvancedAdsAdNetwork {
	/**
	 * @param id string representing the id of this network. has to match the identifier of the PHP class
	 */
	constructor(id) {
		super('gam');
		this.name = 'Google Ad Manager';
		this.loaded = false;
		this.domReadyEventCount = 0;
	}

	jsonParse(str) {
		try {
			return JSON.parse(str);
		} catch (e) {
			return null;
		}
	}

	onBlur() {
		console.log('AdvanvedAdsNetworkGam >> onBlur');
	}

	/**
	 * will be called when an ad network is selected (ad type in edit ad)
	 */
	onSelected() {
		this.updateSelected();
	}

	/**
	 * opens the selector list containing the external ad units
	 */
	openSelector() {
		console.log('AdvanvedAdsNetworkGam >> openSelector');
	}

	/**
	 * returns the network specific id of the currently selected ad unit
	 */
	getSelectedId() {}

	/**
	 * will be called when an external ad unit has been selected from the selector list
	 * @param slotId string the external ad unit id
	 */
	selectAdFromList(slotId) {
		console.log('AdvanvedAdsNetworkGam >> selectAdFromList');
	}

	/**
	 * return the POST params that you want to send to the server when requesting a refresh of the external ad units
	 * (like nonce and action and everything else that is required)
	 */
	getRefreshAdsParameters() {
		console.log('AdvanvedAdsNetworkGam >> getRefreshAdsParameters');
	}

	/**
	 * return the jquery objects for all the custom html elements of this ad type
	 */
	getCustomInputs() {
		console.log('AdvanvedAdsNetworkGam >> getCustomInputs');
	}

	base64Decode(str) {
		try {
			return atob(str);
		} catch (ex) {
			return null;
		}
	}

	updateSelected() {
		var adData = jQuery('input[name="advanced_ad\[content\]"]').val();
		var that = this;
		if (adData) {
			var jsString = this.base64Decode(adData);
			var parsed = this.jsonParse(jsString);
			if (parsed && 'undefined' != typeof parsed.networkCode && 'undefined' != typeof parsed.id) {
				var theRow = jQuery('#advads-gam-table tbody tr[data-unitid="' + parsed.networkCode + '_' + parsed.id + '"]');
				if (theRow.length) {
					if (theRow.attr('data-unitdata') == jsString && !theRow.hasClass('changed')) {
						jQuery('#advads-gam-table tbody tr').removeClass('selected changed');
						theRow.addClass('selected');
						jQuery('#advads-gam-current-unit-updated').addClass('hidden');
					} else {
						jQuery('#advads-gam-table tbody tr').removeClass('selected changed');
						theRow.addClass('changed');
						jQuery('#advads-gam-current-unit-updated').removeClass('hidden');
						jQuery('input[name="advanced_ad\[content\]"]').val(btoa(theRow.attr('data-unitdata')));
					}
				}
			}
		}
	}

	prependSelected() {
		var theRow = jQuery('#advads-gam-table tbody tr.selected,#advads-gam-table tbody tr.changed');
		if (theRow.length) {
			theRow.parent().prepend(theRow);
		}
	}

	/**
	 * Generate a table for the Ad sizes parameter from the ad content
	 */
	loadAdSizes() {
		// get the data from the ad unit
		var postContent = this.jsonParse(this.base64Decode(jQuery('input[name="advanced_ad\[content\]"]').val()));

		var adData = false;

		if (postContent && postContent.networkCode && postContent.id) {
			adData = this.jsonParse(jQuery('#advads-gam-table tbody tr[data-unitid="' + postContent.networkCode + '_' + postContent.id + '"]').attr('data-unitdata'));
			adData = this.appendFluidSize(adData);
		}

		if (!adData || !adData.adUnitSizes) {
			// hide the sizes option and return
			jQuery('#advads-gam-ad-sizes').addClass('hidden');
			jQuery('.advads-gam-ad-sizes-notice-missing-sizes').removeClass('hidden');
			return;
		}
		jQuery('#advads-gam-ad-sizes').removeClass('hidden');
		jQuery('.advads-gam-ad-sizes-notice-missing-sizes').addClass('hidden');

		// get the template.
		// todo: known issue: throws JS warning when a unit is selected before DOM is ready
		var gam_ad_sizes_table = wp.template('gam-ad-sizes-table');
		var gam_ad_sizes_table_row = wp.template('gam-ad-sizes-table-row');

		var adUnitSizes = this.getAdUnitSizes(adData);

		if (!adUnitSizes) {
			return;
		}

		// fill the template and load it in the reserved place.
		var data = {
			header: adUnitSizes,
		}
		jQuery('#advads-gam-ad-sizes .advads-gam-ad-sizes-table-container').html(gam_ad_sizes_table(data));

		// fill the rows
		var data = {
			rows: this.getAdUnitSizesRows(adData),
		}
		jQuery('#advads-gam-ad-sizes .advads-gam-ad-sizes-table-container tbody').append(gam_ad_sizes_table_row(data));

		// AMP sizes.

		if (advads_gam_amp.hasAMP ) {
			var ampSizesTmpl = wp.template( 'gam-amp-ad-sizes' );
			var ampSizes     = adUnitSizes;
			var ampChecked   = {};

			for ( var i in ampSizes ) {
				// For new ads, checks all sizes on AMP. Look into settings otherwise.
				if ( document.location.href.indexOf( 'post-new.php' ) !== - 1 || advads_gam_amp.sizes.indexOf( ampSizes[i] ) !== - 1 ) {
					ampChecked[ampSizes[i]] = true;
				}
			}

			var ampArgs = {
				sizes:   ampSizes,
				checked: ampChecked
			};

			jQuery( '#advads-gam-ad-sizes .advads-gam-ad-sizes-table-container table' ).append( jQuery( ampSizesTmpl( ampArgs ) ) );
		}
	}

	/**
	 * Load a new line into the Ad sizes options
	 * we just copy the previous line including the options
	 *
	 * @var string el clicked element so that we know where to add it
	 */
	loadAdSizesRow(el) {
		var currentRow = jQuery(el).closest('tr');
		// get this row’s width
		var currentIndex = currentRow.find('td:first-of-type input').val();
		// next weight is just by 1 px higher, duplicates will be removed after saving
		var newWeight = parseInt(currentIndex) + 1;
		var nextIndex = newWeight;

		// create a copy of the existing row
		var newRow = currentRow.clone();
		// update index and weight of the name attributes
		newRow.find('[name]').each(function () {
			jQuery(this).attr('name', jQuery(this).attr('name').replace('\[ad-sizes\]\[' + currentIndex + '\]', '\[ad-sizes\]\[' + nextIndex + '\]'));
		});
		// update weight value
		newRow.find('td:first-of-type input').val(newWeight);

		// clone the current row below the previous one
		currentRow.after(newRow);
		// set focus to the min width field of the next line
		currentRow.next('tr').find('.advads-ad-parameters-option-list-min-width input').focus();
	}

	/**
	 * Append fluid size to regular sizes list of the ad data in a way that looks like Google's data. Creates that list if needed.
	 *
	 * @param [object] adData the ad data.
	 * @return modified data with fluid size.
	 */
	appendFluidSize(adData) {
		if (!adData) {
			return;
		}
		if ('undefined' != typeof adData.hasFluidSize) {
			return adData;
		}
		if (adData.isFluid) {

			// The ad also have regular fixed size
			if (adData.adUnitSizes) {

				// Only one regular size
				if (adData.adUnitSizes.fullDisplayString) {
					var size = adData.adUnitSizes.size;
					var fullString = adData.adUnitSizes.fullDisplayString;
					delete (adData.adUnitSizes.fullDisplayString);
					adData.adUnitSizes = [{
							'size': size,
							'fullDisplayString': fullString,
						}, {
							'size': {},
							'fullDisplayString': 'fluid',
						}
					];
				} else {

					// add 'fluid' to other regular sizes
					adData.adUnitSizes.push({
						size: {},
						fullDisplayString: 'fluid',
					});
				}
			} else {

				// just fluid, no regular sizes
				adData.adUnitSizes = {
					size: {},
					fullDisplayString: 'fluid',
				};
			}
			adData['hasFluidSize'] = true;
		}

		return adData;
	}

	/**
	 * Load sizes of the ad unit into an array
	 *
	 * @var object adData
	 * @return array
	 */
	getAdUnitSizes(adData) {
		adData = this.appendFluidSize(adData);
		if (!adData.adUnitSizes) {
			return [];
		}

		var adUnitSizes = [];

		// handle ad units with just one size and those with more than one
		if (adData.adUnitSizes.fullDisplayString) {
			adUnitSizes.push(adData.adUnitSizes.fullDisplayString);
		} else {
			adData.adUnitSizes.forEach(
				function (size) {
				if (size.fullDisplayString) {
					adUnitSizes.push(size.fullDisplayString);
				}
			});
		}

		return adUnitSizes;
	}

	/**
	 * Load the rows for the Ad Sizes option with ad units
	 *
	 * @var object adData
	 * @return object
	 */
	getAdUnitSizesRows(adData) {

		// iterate through the rows based on the stored values

		var adUnitSizes = this.getAdUnitSizes(adData);

		if (!adUnitSizes) {
			return [];
		}

		// If the ad was never saved with Ad sizes before, we enable all checkboxes.
		var enableAll = "undefined" === typeof advads_gam_stored_ad_sizes_json || jQuery.isEmptyObject(advads_gam_stored_ad_sizes_json);

		// Load the stored values or use a default that set up a new line.
		var savedSizes = !enableAll ? advads_gam_stored_ad_sizes_json : {
			0: {
				width: 0
			}
		};

		var rows = {};
		for (var width in savedSizes) {
			// Object.entries( savedSizes ).forEach( function( width, savedSize ){
			rows[width] = {};
			adUnitSizes.forEach(
				function (adSizeString) {
				// is true if the option exists and was set before or if it wasn’t saved yet
				rows[width][adSizeString] = savedSizes[width]['sizes'] && savedSizes[width]['sizes'].includes(adSizeString) || enableAll;
			})
		}

		return rows;
	}

	/**
	 * what to do when the DOM is ready
	 */
	onDomReady() {
		if ( this.domReadyEventCount !== 0 ) {
			return;
		}
		this.domReadyEventCount++;
		if (!parseInt(jQuery('#advads-gam-table').attr('data-adcount'), 10)) {
			this.refershAdsList();
		}

		var that = this;

		if (jQuery('#advads-gam-table tbody tr').length) {
			this.updateSelected();
			this.prependSelected();

			// load ad size table. we need to wait until the DOM is completely ready and wp-util available (with wp.template)
			jQuery(document).ready(function () {
				that.loadAdSizes();
			})

			this.updateTableStyle();
		}

		jQuery(document).on('click', '#advads-gam-table .dashicons-update,#advads-gam-table-head .dashicons-update,.gam-update-icon', function () {
			if (('undefined' !== gamAdvancedAdsJS && 'no' == gamAdvancedAdsJS.hasGamLicense) || jQuery(this).hasClass('disabled')) {
				return;
			}
			that.refershAdsList();
		});

		jQuery(document).on('click', '#advads-gam-table tbody tr', function () {
			that.updateAdContent(btoa(jQuery(this).attr('data-unitdata')));
			// reload the option with responsive ad sizes after the ad content was updated and before the `select` class was moved
			if (!jQuery(this).hasClass('selected')) {
				that.loadAdSizes();
			}
			that.onSelected();
		});

		jQuery(window).resize(function () {
			that.updateTableFixedHead();
		});

		// add a new row to the Ad sizes form
		jQuery( document ).on( 'click', '#advads-gam-ad-sizes .advads-row-new', function () {
			that.loadAdSizesRow( this );
			// remove the (+) icon to prevent adding multiple rows with the same index
			jQuery( this ).hide();
		} );

		// show the + icon when the last line was removed.
		jQuery(document).on('click', '#advads-gam-ad-sizes .advads-tr-remove', function () {
			setTimeout(function () {
				jQuery('#advads-gam-ad-sizes table tr:last-of-type .advads-row-new').show();
			}, 100);
		});
	}

	updateTableFixedHead() {
		var head = jQuery('#advads-gam-table-head');
		if (head.is(':visible')) {
			var firstRow = jQuery('#advads-gam-table tbody tr').first();
			for (var i = 0; i < 3; i++) {
				head.find('span').eq(i).outerWidth(
					firstRow.find('td').eq(i).outerWidth() - 2);
			}
		}
	}

	updateTableStyle() {
		if (jQuery('#advads-gam-table tbody tr').length < 2) {
			return;
		}
		$ = jQuery;
		var hmax = $('#advads-gam-table tbody tr').first().outerHeight() * 10;
		if (hmax < $('#advads-gam-table tbody').height()) {

			$('#advads-gam-table thead').css('display', 'none');
			$('#advads-gam-table-head').css('display', 'block');
			$('#advads-gam-table-wrap').css({
				paddingBottom: hmax,
				overflowY: 'auto',
			});
			$('#advads-gam-table').css({
				position: 'absolute',
				top: 0,
			});

			this.updateTableFixedHead();

		} else {

			$('#advads-gam-table thead').css('display', 'table-header-group');
			$('#advads-gam-table-head').css('display', 'none');
			$('#advads-gam-table-wrap').css({
				paddingBottom: 0,
				overflowY: 'visible',
			});
			$('#advads-gam-table').css({
				position: 'relative',
				top: 0,
			});

		}
	}

	refershAdsList() {
		var nonce = jQuery('#advads-gam-table').attr('data-nonce');
		var action = 'aagam_reload_ads_list';
		this.loading();
		var that = this;
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				nonce: nonce,
				action: action,
			},
			success: function (resp, status, XHR) {
				that.loading(false);
				if (resp.status && resp.markup) {
					jQuery('#advads-gam-table tbody').html(resp.markup);
					var count = jQuery('#advads-gam-table tbody tr').length;
					jQuery('#advads-gam-table').attr('data-adcount', count);
					that.updateSelected();
					that.updateTableStyle();
					if (resp.updateAgeString) {
						jQuery('#advads-gam-list-update-time span:first-of-type').text(resp.updateAgeString);
						jQuery('#advads-gam-list-update-time .gam-update-icon').remove();
					}
				}
			},
			error: function (req, status, err) {
				that.loading(false);
			},
		});
	}

	updateAdContent(ID) {
		if (jQuery('#advads-gam-netcode-mismatch:visible')) {
			jQuery('#advads-gam-netcode-mismatch:visible').hide();
		}
		jQuery('input[name="advanced_ad\[content\]"]').val(ID);
	}

	loading(show) {
		if ('undefined' == typeof show) {
			show = true;
		}
		if (show) {
			jQuery('#advads-gam-ads-list-overlay').css('display', 'block');
		} else {
			jQuery('#advads-gam-ads-list-overlay').css('display', 'none');
		}
	}

	/**
	 * when you need custom behaviour for ad networks that support manual setup of ad units, override this method
	 */
	onManualSetup() {
		//no console logging. this is optional
		console.log('AdvanvedAdsNetworkGam >> onManualSetup');
	}
}

var AdvancedAdsGamConnect = function () {
	this.$modal = jQuery('#advads-gam-modal');
	this.$currentContent = this.$modal.find('.advads-gam-modal-content-inner').first();

	this.init();

	return this;
};

AdvancedAdsGamConnect.prototype = {

	constructor: AdvancedAdsGamConnect,

	changeContent: function (id) {
		this.$modal.find('.advads-gam-modal-content-inner').css('display', 'none');
		this.$currentContent = this.$modal.find('.advads-gam-modal-content-inner[data-content="' + id + '"]');
		this.$currentContent.css('display', 'block');
	},

	/**
	 * Initialization tasks (on DOM ready)
	 */
	init: function () {
		var $ = window.jQuery;
		var that = this;

		$(document).on('click', '#advads-gam-modal .dashicons-dismiss', function () {
			that.hide();
		});
		$(document).on('click', '#advads-gam-connect', function () {
			if ($(this).hasClass('nosoapkey')) {
				$('#gam-settings-overlay').css('display', 'block');
				$.ajax({
					url: ajaxurl,
					type: 'post',
					data: {
						action: 'advads_gamapi_get_key',
						nonce: $(this).attr('data-nonce'),
					},
					success: function (response) {
						$('#gam-settings-overlay').css('display', 'none');
						if (response.status) {
							that.openGoogle();
						} else {
							console.log('>> API key error');
							console.log(response);
						}
					},
					error: function (error) {
						console.error(error);
					},
				});
			} else {
				that.openGoogle();
			}
		});

		$(document).on('click', '#getAllNet', function () {
			that.getNet.apply(that);
		})

		$(document).on('click', '#advads-gam-revoke', function () {
			that.revokeAccess.apply(that);
		});

		$(document).on('click', '#gam-selected-network', function () {
			that.accountSelected.apply(that);
		});

		$(document).on('paramloaded', '#advanced-ads-ad-parameters', function () {
			that.onParamloaded.apply(that);
		});

		$(document).on('click', '#gam input[type="radio"], #gam input[type="checkbox"]', function () {
			$('#gam .submit input[type="submit"]').focus();
		});

		if ($('#advads-gam-oauth-code').length) {
			this.show();
			this.submitConfirmCode($('#advads-gam-oauth-code').val());
		}

	},

	onParamloaded: function () {
		var $ = jQuery;
		var adType = $('input[name="advanced_ad\[type\]"]:checked').val();

		if ( 'gam' === adType ) {
			$( '#advanced-ads-ad-parameters' ).next( '.advads-option-list' ).css( 'display', 'none' );
			if ( $( '#advads-gam-ad-sizes' ).html().indexOf( 'advads-loader' ) !== - 1 ) {
				AdvancedAdsAdmin.AdImporter.isSetup = false;
				AdvancedAdsAdmin.AdImporter.setup( AdvancedAdsAdmin.AdImporter.adNetwork );
			}
		} else {
			$( '#advanced-ads-ad-parameters' ).next( '.advads-option-list' ).css( 'display', 'block' );
		}
	},

	/**
	 * Internal debugging function
	 */
	getNet: function () {
		data = {
			action: 'advads_gamapi_getnet'
		};
		this.ajax(data, function (resp) {
			console.log(resp)
		});
	},

	/**
	 * Show/hide loading overlay
	 */
	overlay: function () {
		var $overlay = this.$currentContent.find('.advads-gam-overlay');
		if ($overlay.is(':visible')) {
			$overlay.css('display', 'none');
		} else {
			$overlay.css('display', 'block');
		}
	},

	/**
	 * Send an ajax request
	 *
	 * @param [object] data the form data.
	 * @param [function] success callback on success.
	 * @param [function] error callback function on error.
	 */
	ajax: function (data, onSuccess, onError) {
		if ('undefined' == typeof data || 'undefined' == typeof data.action) {
			console.error('AJAX call needs at least an "action" field');
			return;
		}
		data.nonce = this.$modal.attr('data-nonce');
		this.overlay();
		var that = this;
		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			data: data,
			success: function (response, status, XHR) {
				if ('function' == typeof onSuccess) {
					onSuccess.apply(that, [response, status, XHR]);
				} else {
					console.log(response);
				}
			},
			error: function (request, status, error) {
				if ('function' == typeof onError) {
					onError.apply(that, [request, status, error]);
				} else {
					console.error(error);
				}
			},
		});

	},

	accountSelected: function () {
		var extraData = jQuery('#gam-account-list-data').val(),
		action = 'advads_gamapi_account_selected',
		that = this,
		index = jQuery('#gam-account-list').val();

		this.ajax({
			extra_data: extraData,
			action: action,
			index: index,
		},
			function (response) {
			if (response.status) {
				that.hide();
			}
		});
	},

	revokeAccess: function () {
		var that = this;
		jQuery('#advads-gam-page-overlay').css('display', 'block');
		this.ajax({
			action: 'advads_gamapi_revoke'
		}, function (response) {
			if (response.status) {
				document.location.reload();
			} else {
				jQuery('#advads-gam-page-overlay').css('display', 'none');
				that.overlay();
			}
		});
	},

	submitConfirmCode: function (code) {
		action = 'advads_gamapi_confirm_code';

		if ('string' != typeof code || '' === code.trim()) {
			return;
		}
		this.ajax({
			action: action,
			code: code
		}, this.confirmCodeResponse);

	},

	confirmCodeResponse: function (response) {
		var that = this;
		if (response.token_data) {
			this.overlay();
			this.ajax({
				action: 'advads_gamapi_getnet',
				token_data: response.token_data,
			},
				function (resp2) {
				if (resp2.status) {
					that.hide();
				} else {
					if (resp2.error_id) {
						that.overlay();

						if ('select_account' == resp2.error_id) {
							$select = jQuery('#gam-account-list');
							for (var i in resp2.networks) {
								$select.append(jQuery('<option value="' + i + '">[' + resp2.networks[i]['networkCode'] + '] ' + resp2.networks[i]['displayName'] + '</option>'));
							}
							jQuery('#gam-account-list-data').val(JSON.stringify({
									token_data: resp2.token_data,
									networks: resp2.networks,
								}));
						}

						that.changeContent(resp2.error_id);
						if (resp2.msg && that.$currentContent.find('.advads-error-message').length) {
							that.$currentContent.find('.advads-error-message').text(resp2.msg);
						}
					} else {
						that.overlay();
						console.error(resp2);
					}
				}
			});
		} else {
			console.error(response);
		}
	},

	/**
	 * Show the modal frame
	 */
	show: function () {
		this.$modal.css('display', 'block');
	},

	/**
	 * Hide the modal frame
	 */
	hide: function () {
		window.location.href = this.$modal.attr('data-gamsettings');
	},

	/**
	 * Redirect to Google for authorization.
	 */
	openGoogle: function () {
		var AUTH_URL = decodeURIComponent(this.$modal.attr('data-url'));
		window.location.href = AUTH_URL;
	},

};

window.jQuery(function () {
	new AdvanvedAdsNetworkGam('gam');

	if (-1 !== window.location.href.indexOf('&new_ad_type=gam') && jQuery('#advanced-ad-type-gam').length) {
		jQuery('#advanced-ad-type-gam').trigger('click');
	}

	if (window.jQuery('#advads-gam-modal').length) {
		new AdvancedAdsGamConnect();
		if (jQuery('#gamlistisempty').length) {
			jQuery('#gam-settings-overlay').css('display', 'block');
			jQuery.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					action: 'advads_gamapi_test_the_api',
					nonce: jQuery('#gamlistisempty').val()
				},
				success: function (response) {
					jQuery('#gam-settings-overlay').css('display', 'none');
					if (response.msg) {
						if ('TOO_MUCH_ADUNITS' === response.msg) {
							jQuery('#gamapi-too-much-ads').css('display', 'block');
							jQuery('#gamapi-new-ad').css('display', 'none');
						}
						if (-1 != response.msg.indexOf('NOT_WHITELISTED_FOR_API_ACCESS')) {
							jQuery('#gamapi-not-enabled').css('display', 'block');
							jQuery('#gamapi-new-ad').css('display', 'none');
						}
					} else {
						if (response.count && '0' !== response.count) {
							jQuery('#wpwrap').trigger('advads-gam-api-can-import')
						}
					}
				},
				error: function (request) {
					jQuery('#gam-settings-overlay').css('display', 'none');
				},
			});
		}
	}
});
