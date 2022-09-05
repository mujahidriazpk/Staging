function wcmp_admin()
{
	if(typeof wcmp_admin_evaluated != 'undefined') return;
	wcmp_admin_evaluated = true;

	var $ = jQuery;

    // Special Radio
    $( document ).on(
        'mousedown',
        '.wcmp_radio',
        function()
        {
            $(this).data('status', this.checked);
        }
    );

    $( document ).on(
        'click',
        '.wcmp_radio',
        function()
        {
            this.checked = !$(this).data('status');
        }
    );
}

jQuery(wcmp_admin);
jQuery(window).on('load', wcmp_admin);