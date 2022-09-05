jQuery().ready(function() {
	jQuery('#fttb_settings').validate({
		rules: {
			fttb_topdistance: {
				required: true,
				digits: true
			},
			fttb_topspeed: {
				required: true,
				digits: true
			},
			fttb_animationinspeed: {
				required: true,
				digits: true
			},
			fttb_animationoutspeed: {
				required: true,
				digits: true
			},
			fttb_opacity_out: {
				required: true,
				digits: true,
				min: 0,
				max: 99
			},
			fttb_opacity_over: {
				required: true,
				digits: true,
				min: 0,
				max: 99
			},
			fttb_zindex: {
				required: true,
				digits: true,
				min: 0,
				max: 9999999999
			},					
		},
		messages: {
			fttb_topdistance: fttb_strings.topdistance,
			fttb_topspeed: fttb_strings.topspeed,
			fttb_animationinspeed: fttb_strings.animationinspeed,
			fttb_animationoutspeed: fttb_strings.animationoutspeed,
			fttb_opacity_out: fttb_strings.opacity_out,
			fttb_opacity_over: fttb_strings.opacity_over,
			fttb_zindex_over: fttb_strings.zindex
		}
	});
});