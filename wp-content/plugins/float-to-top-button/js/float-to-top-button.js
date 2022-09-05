/* ---------------------------------------------------
 *
 *	Initialize the scrollUp object
 *
 *	http://markgoodyear.com/2013/01/scrollup-jquery-plugin/
 *
 *	Options
 *  scrollName: 'scrollUp',      // Element ID
 *  scrollDistance: 300,         // Distance from top/bottom before showing element (px)
 *  scrollFrom: 'top',           // 'top' or 'bottom'
 *  scrollSpeed: 300,            // Speed back to top (ms)
 *  easingType: 'linear',        // Scroll to top easing (see http://easings.net/)
 *  animation: 'fade',           // Fade, slide, none
 *  animationSpeed: 200,         // Animation in speed (ms)
 *  scrollTrigger: false,        // Set a custom triggering element. Can be an HTML string or jQuery object
 *  scrollTarget: false,         // Set a custom target element for scrolling to. Can be element or number
 *  scrollText: 'Scroll to top', // Text for element, can contain HTML
 *  scrollTitle: false,          // Set a custom <a> title if required. Defaults to scrollText
 *  scrollImg: false,            // Set true to use image
 *  activeOverlay: false,        // Set CSS color to display scrollUp active point, e.g '#00FFFF'
 *  zIndex: 2147483647           // Z-Index for the overlay
 *
 * -------------------------------------------------*/	
jQuery(document).ready(function() {

	/* v2.3 - BUTTON HIDDEN ON THIS POST / PAGE? */
	if (hide_fttb === 'Y') return;
	
	var fttb_img = new Image();

	/* v2.0.8 */
	fttb_img.src = fttb.arrow_img_url == '' ? fttb.imgurl+fttb.arrow_img : fttb.arrow_img_url;
	
	jQuery("#scrollUp").width(fttb_img.width);
	jQuery("#scrollUp").height(fttb_img.height);

	/* CREATE THE SCROLLUP INSTANCE */
	jQuery.scrollUp({
		// v2.2
		scrollDistance: fttb.topdistance,			// DISTANCE FROM TOP BEFORE SHOWING ELEMENT (PX)
		// v2.2
		scrollSpeed: fttb.topspeed,					// SPEED BACK TO TOP (MS)
		animation: fttb.animation,					// FADE, SLIDE, NONE
		animationInSpeed: fttb.animationinspeed,	// ANIMATION IN SPEED (MS)
		animationOutSpeed: fttb.animationoutspeed,	// ANIMATION OUT SPEED (MS)
		scrollText: fttb.scrolltext,				// TEXT FOR THE IMAGE
		scrollTitle: fttb.scrolltext,				// TITLE FOR THE IMAGE
		zIndex: fttb.zindex							// Z-INDEX FOR THE OVERLAY
	});
		
	/* SET THE 'TO TOP' IMAGE TO THE SELECTED IMAGE */
	if(fttb.arrow_img_url == '')
		/* STANDARD IMAGE */
		jQuery("#scrollUp").css({"background-image":"url("+fttb.imgurl+fttb.arrow_img+")"});
	else
		/* CUSTOM IMAGE URL */
		jQuery("#scrollUp").css({"background-image":"url("+fttb.arrow_img_url+")"});
	/* Z-INDEX OF THE BUTTON */
	jQuery("#scrollUp").css('z-index', fttb.zindex);

	if(fttb.position == 'lowerleft') {
		jQuery("#scrollUp").css('left', fttb.spacing_horizontal);
		jQuery("#scrollUp").css('bottom', fttb.spacing_vertical);		
	} else if(fttb.position == 'lowerright') {
		jQuery("#scrollUp").css('right', fttb.spacing_horizontal);
		jQuery("#scrollUp").css('bottom', fttb.spacing_vertical);			
	} else if(fttb.position == 'upperleft') {
		jQuery("#scrollUp").css('left', fttb.spacing_horizontal);
		jQuery("#scrollUp").css('top', fttb.spacing_vertical);
	} else if(fttb.position == 'upperright') {
		jQuery("#scrollUp").css('right', fttb.spacing_horizontal);
		jQuery("#scrollUp").css('top', fttb.spacing_vertical);		
	}
	
	/* SET THE OPACITY OF THE 'TO TOP' IMAGE (FROM THE SETTINGS) */
	setOpacity(fttb.opacity_out);
	
	jQuery("#scrollUp").mouseover(function() {
		setOpacity(fttb.opacity_over);
	});
	
	jQuery("#scrollUp").mouseout(function() {
		setOpacity(fttb.opacity_out);
	});
});		

function setOpacity(opac) {
	jQuery("#scrollUp").css({"-khtml-opacity":"."+opac});
	jQuery("#scrollUp").css({"-moz-opacity":"."+opac});
	jQuery("#scrollUp").css({"-ms-filter":'"alpha(opacity='+opac+')"'});
	jQuery("#scrollUp").css({"filter":"alpha(opacity="+opac+")"});
	// v2.1 - IE 9 issue
    jQuery("#scrollUp").css({"filter":"progid:DXImageTransform.Microsoft.Alpha(opacity="+opac+")"});
	jQuery("#scrollUp").css({"opacity":"."+opac});		
} // setOpacity()