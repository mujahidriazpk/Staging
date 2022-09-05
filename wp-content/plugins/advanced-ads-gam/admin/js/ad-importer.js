(function ($) {
	"use strict";

	var DEFAULT_BODY = null;
	var DEFAULT_FOOTER = null;

	/**
	 * Disable/enable action buttons on the modal's footer.
	 */
	function primaryButtonsEnabled(enabled) {
		if ('undefined' == typeof enabled) {
			enabled = true;
		}
		$('#modal-gam-import .advads-modal-footer button.button').prop('disabled', !enabled);
	}

	// Open importer button.
	$(document).on('click', '#gam-open-importer', function () {

		if (null === DEFAULT_BODY) {
			DEFAULT_BODY = $('#modal-gam-import .advads-modal-body').html();
			DEFAULT_FOOTER = $('#modal-gam-import .advads-modal-footer').html();
		} else {
			$('#modal-gam-import .advads-modal-body').html(DEFAULT_BODY);
			$('#modal-gam-import .advads-modal-footer').html(DEFAULT_FOOTER);
		}
		primaryButtonsEnabled(false);
		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				nonce: $('#gam-open-importer').attr('data-nonce'),
				action: 'gam_importable_list',
			},
			success: function (resp) {
				primaryButtonsEnabled(true);
				if (resp.status) {
					if (resp.html) {
						$('#modal-gam-import .advads-modal-body').html(resp.html);
					}
				}
			},
			error: function () {
				primaryButtonsEnabled(true);
			},
		});

	});

	// Update external ad unit lists (after successful account connection) then show the import button.
	$(document).on('advads-gam-api-can-import', function () {
		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				nonce: $('#gam-late-import-button').attr('data-nonce'),
				action: 'advads_gam_import_button',
			},
			success: function (resp) {
				if (resp.status && resp.html) {
					$('#gam-late-import-button').replaceWith($(resp.html));
					prepareImportButton();
				}
				$('#gam-settings-overlay').css('display', 'none');
			},
			error: function () {
				$('#gam-settings-overlay').css('display', 'none');
			},
		});
	});

	// After a click on a close importer button/icon: Restore URL fragments.
	$(document).on('click', '#modal-gam-import .advads-modal-close, #modal-gam-import .advads-modal-close-background', function () {
		$('#modal-gam-import .advads-modal-body').html(DEFAULT_BODY);
		setTimeout(function () {
			window.location.hash = '#top#gam';
		}, 300);
	});

	/**
	 * Change on table body checkboxes
	 *
	 * @param [jQuery] ckb the checkbox
	 */
	function bodyCheckbox(ckb) {
		if (ckb.prop('checked')) {
			ckb.closest('tr').addClass('checked');
		} else {
			ckb.closest('tr').removeClass('checked');
		}
		if (!$('#modal-gam-import tbody input[type="checkbox"]:not(:checked)').length) {
			// All ticked
			$('#modal-gam-import thead input[type="checkbox"]').prop('checked', true);
		} else {
			// Not all ticked
			$('#modal-gam-import thead input[type="checkbox"]').prop('checked', false);
		}
		if (!$('#modal-gam-import tbody input[type="checkbox"]:checked').length) {
			// All unticked
			$('#gam-import-form .import').prop('disabled', true);
		} else {
			// At least one ticked
			$('#gam-import-form .import').prop('disabled', false);
		}
	}

	// Click on tbody checkbox
	$(document).on('change', '#modal-gam-import tbody input[type="checkbox"]', function () {
		bodyCheckbox($(this));
	});

	// Change checkbox values on click on the containing row
	$(document).on('click', '#modal-gam-import tbody tr', function (ev) {
		if ($(ev.target).attr('type') && 'checkbox' === $(ev.target).attr('type')) {
			return;
		}
		var ckb = $(this).find('input[type="checkbox"]');
		bodyCheckbox(ckb.prop('checked', !ckb.prop('checked')));
	});

	// Click on thead checkbox
	$(document).on('change', '#modal-gam-import thead input[type="checkbox"]', function () {
		var ckb = $(this);
		if (ckb.prop('checked')) {
			$('#modal-gam-import tbody input[type="checkbox"]').prop('checked', true).trigger('change');
		} else {
			$('#modal-gam-import tbody input[type="checkbox"]').prop('checked', false).trigger('change')
			$('#modal-gam-import-form .import').prop('disabled', true);
		}
	});

	/**
	 * Handle response from the import AJAX call
	 */
	function importResponse(resp) {
		if (resp.status) {
			if (resp.html) {
				// Markup provided, show it in the modal frame.
				primaryButtonsEnabled(true);
				$('#modal-gam-import .advads-modal-body').html(resp.html);
				$('#modal-gam-import .advads-modal-footer .tablenav').html(resp.footer);
			} else if (resp.resend && resp.form_data) {
				// The resend field is set and TRUE, perform the next AJAX call.
				$.ajax({
					type: 'post',
					url: ajaxurl,
					data: resp.form_data,
					success: function (resp) {
						importResponse(resp);
					},
					error: function (req, status, err) {
						console.log(err);
					}
				});
			}
		}
	}

	// Launch the import process
	$(document).on('click', '#modal-gam-import #gam-start-import', function (ev) {
		ev.preventDefault();
		var ids = $('#gam-import-form').serialize();
		var nonce = $('#gam-open-importer').attr('data-nonce');

		$('#modal-gam-import .advads-modal-body').html(DEFAULT_BODY);
		primaryButtonsEnabled(false);
		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				nonce: nonce,
				ids: ids,
				action: 'gam_import_ads',
			},
			success: function (resp) {
				importResponse(resp);
			},
			error: function (req, status, err) {
				primaryButtonsEnabled(true);
				console.log(err);
			}
		});
	});

	function prepareImportButton(){
		// Move the importer modal content outside of any FORM.
		$('#wpwrap').append($('#modal-gam-import'));

		// Add the import button.
		$('#modal-gam-import .advads-modal-footer .tablenav').append($('<button />').attr({
			'class': 'button button-primary',
			id: 'gam-start-import',
		}).text($('#gam-open-importer').attr('data-import-text')));
	}

	/**
	 * On DOM ready.
	 */
	$(function () {
		if ($('#modal-gam-import').length) {
			prepareImportButton();
		}
	});

})(window.jQuery);
