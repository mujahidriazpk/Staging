jQuery(document).ready(function($){
	$('.advads-ad-group-form').each(function(){
		if( 'slider' === $(this).find('.advads-ad-group-type input:checked').val()){
			$(this).find('.advads-option-group-number').val('all').hide();
		}
	});
});