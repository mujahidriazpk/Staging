/* Dashboard Widgets Suite - User Notes */

jQuery(document).ready(function($) {
	
	$('input[name="dws-notes-user[delete]"]').on('click', function() {
		
		if (confirm(dws_user_notes.confirm)) {
			return true;
		} else {
			return false;
		}
		
	});
	
	$('div.dws-notes-user-button-add a').click(function(e) {
		
		e.preventDefault();
		
		var txt = $(e.target).text();
		
		$('#dws-notes-user-add').slideToggle(100);
		
		if (txt == dws_user_notes.open) {
			
			$(this).text(dws_user_notes.close);
			$('.dws-notes-user-button-add .fa').attr('class', 'fa fa-plus-circle');
			
		} else {
			
			$(this).text(dws_user_notes.open);
			$('.dws-notes-user-button-add .fa').attr('class', 'fa fa-minus-circle');
			
		}
		
	});
	
	$('.dws-notes-user div[data-key]').on('dblclick', function() {
		
		var id         = $(this).attr('data-key');
		var height     = $(this).innerHeight();
		var min_height = $(this).inlineStyle('min-height');
		var textarea   = $('textarea[name="dws-notes-user[note]"][data-key="'+ id +'"]');
		
		if (min_height) textarea.css('min-height', height);
		
		$(this).hide();
		textarea.show().css('display', 'block').focus().scrollTop(0).selectRange(0, 0);
		$(this).siblings('.dws-notes-user-buttons').show();
		
	});
	
	$('input[name="dws-notes-user[cancel]"]').on('click', function(e) {
		
		e.stopPropagation();
		
		var id = $(this).attr('data-key');
		
		$(this).closest('div.dws-notes-user-buttons').hide();
		$('textarea[name="dws-notes-user[note]"][data-key="'+ id +'"]').hide();
		$('div.dws-notes-user-note[data-key="'+ id +'"]').show();
		
		return false;
		
	});
	
	$('div.dws-notes-user').each(function(index, value) {
		
		var id = 'textarea[name="dws-notes-user[note]"][data-key="'+ index +'"]';
		
		$(document).one('focus.textarea', id, function() {
			
			var savedValue = this.value;
			this.value = '';
			this.baseScrollHeight = this.scrollHeight;
			this.value = savedValue;
			
		}).on('input.textarea', id, function() {
			
			var minRows = this.getAttribute('data-rows') | 0, rows;
			this.rows = minRows;
			
			rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 17);
			this.rows = minRows + rows;
			
		});
		
	});
	
	$('textarea[name="dws-notes-user[note]"]').each(function() {
		
		prepareString($(this), $(this).val());
		
	}).keyup(function() {
		
		prepareString($(this), $(this).val());
		
	});
	
	$('div.dws-notes-user-note').each(function() {
		
		prepareString($(this), $(this).html());
		
	});
	
});

function prepareString(el, val) {
	
	var key = jQuery(el).data('key');
	var el = 'div.dws-notes-user-note[data-key="'+ key +'"]';
	
	if (key !== 0) filterString(el, val);
	
}

function filterString(el, val) {
	
	var js  = /<(java)?script(.*)?>(.*)?(<\/(java)?script>)?/gi;
	var br  = /(\r\n|\n\r|\r|\n)/g;
	var lb  = /(&lt;br&gt;)/gi;
	var php = /(\<\!--\?php)/gi;
	var lt  = /\</g;
	var gt  = /\>/g;
	
	if (jQuery(el).parents('.dws-notes-user-format-code').length) {
		
		jQuery(el).html(val.replace(br, '<br>').replace(lt, '&lt;').replace(gt, '&gt;').replace(lb, '<br>'));
		
	} else if (jQuery(el).parents('.dws-notes-user-format-text').length) {
		
		jQuery(el).html(val.replace(br, '<br>').replace(js, ''));
		
	} else {
		
		jQuery(el).html(val.replace(js, ''));
		
	}
	
}

jQuery.fn.inlineStyle = function(prop) {
	
	return this.prop('style')[jQuery.camelCase(prop)];
	
};

jQuery.fn.selectRange = function(start, end) {
	
	if (!end) end = start;
	
	return this.each(function() {
		
		if (this.setSelectionRange) {
			
			this.focus();
			this.setSelectionRange(start, end);
			
		} else if (this.createTextRange) {
			
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', end);
			range.moveStart('character', start);
			range.select();
			
		}
		
	});
	
};
