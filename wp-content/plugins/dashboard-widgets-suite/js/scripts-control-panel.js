jQuery(document).ready(function($) {
	
	var enableSortable = function() {
		
		$('.empty-container').removeClass('empty-container');
		
	};
	
	enableSortable();
	
	$('input[name="screen_columns"]').on('change', enableSortable);
	
	$('.meta-box-sortables').on('sortactivate', function(event, ui) {
		
		enableSortable();
		
	});
	
});
