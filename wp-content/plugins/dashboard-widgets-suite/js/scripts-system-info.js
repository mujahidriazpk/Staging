/* Dashboard Widgets Suite - System Info */

jQuery(document).ready(function($) {
	
	$('#dws-system-info ul li').on('click', 'a', function() {
		
		if ($('#'+ $(this).data('rel')).is(':hidden')) $(this).addClass('selected');
		else $(this).removeClass('selected');
		
		$('#'+ $(this).data('rel')).toggle(0);
		
		return false;
		
	});
	
});
