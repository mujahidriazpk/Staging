/* Dashboard Widgets Suite - Social Box */

jQuery(document).ready(function($) {
	
	$('#dws-social-box li').css({ 
		
		'margin' : '0 '+ dws_social_box.space +' '+ dws_social_box.space +' 0' 
		
	});
	
	$('#dws-social-box li a').css({
		 
		'width'                 : dws_social_box.size, 
		'height'                : dws_social_box.size, 
		'line-height'           : dws_social_box.size, 
		'font-size'             : dws_social_box.font, 
		'-webkit-border-radius' : dws_social_box.radius, 
		'-moz-border-radius'    : dws_social_box.radius,
		'border-radius'         : dws_social_box.radius
		
	});
	
});
