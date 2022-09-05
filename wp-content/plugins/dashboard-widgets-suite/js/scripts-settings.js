/* Dashboard Widgets Suite - Admin JavaScript */

jQuery(document).ready(function($) {
	
	$('.wp-admin .wrap-tab1 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-cog"></span> ');
	$('.wp-admin .wrap-tab2 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-file-text"></span> ');
	$('.wp-admin .wrap-tab3 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-rss"></span> ');
	$('.wp-admin .wrap-tab4 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-share-alt"></span> ');
	$('.wp-admin .wrap-tab5 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-list-ul"></span> ');
	$('.wp-admin .wrap-tab6 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-list-alt"></span> ');
	$('.wp-admin .wrap-tab7 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-info-circle"></span> ');
	$('.wp-admin .wrap-tab8 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-search"></span> ');
	$('.wp-admin .wrap-tab9 form h2:not(.dws-noicon)').prepend('<span class="fa fa-pad fa-search"></span> ');
	
	$('.dws-reset-options').on('click', function(e) {
		e.preventDefault();
		$('.dws-modal-dialog').dialog('destroy');
		var link = this;
		var button_names = {}
		button_names[dws_settings.reset_true]  = function() { window.location = link.href; }
		button_names[dws_settings.reset_false] = function() { $(this).dialog('close'); }
		$('<div class="dws-modal-dialog">'+ dws_settings.reset_message +'</div>').dialog({
			title: dws_settings.reset_title,
			buttons: button_names,
			modal: true,
			width: 350,
			closeText: ''
		});
	});
	
	$('.dws-delete-notes').on('click', function(e) {
		e.preventDefault();
		$('.dws-modal-dialog').dialog('destroy');
		var link = this;
		var button_names = {}
		button_names[dws_settings.delete_true]  = function() { window.location = link.href; }
		button_names[dws_settings.delete_false] = function() { $(this).dialog('close'); }
		$('<div class="dws-modal-dialog">'+ dws_settings.delete_message +'</div>').dialog({
			title: dws_settings.delete_title,
			buttons: button_names,
			modal: true,
			width: 350,
			closeText: ''
		});
	});
	
});
