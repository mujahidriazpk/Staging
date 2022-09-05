"use strict"

jQuery(document).ready(function($) {

    /**
     * Global variables.
     */
    // will hold all the available dimension
    var wp_analytify_cd_available = [];
    // will hold all the selected 
    var wp_analytify_cd_selected = [];
    // will count all the available dimensions
    var wp_analytify_cd_total = 0;

    // will create the wp_analytify_cd_available array
    for (let key in wpAnalytifyDimensionOptions) {
        if (wpAnalytifyDimensionOptions.hasOwnProperty(key)) {
            wp_analytify_cd_total++;
            let element = wpAnalytifyDimensionOptions[key];
            wp_analytify_cd_available[key] = element['title'];
        }
    }

    wp_analytify_cd_repopulate_selected();

    wp_analytify_cd_hideshow_selected();

    wp_analytify_cd_check_add_button();

    // checks all the selected options value and moves them to wp_analytify_cd_selected
    function wp_analytify_cd_repopulate_selected() {
        wp_analytify_cd_selected = [];
        $('table#wp-analytify-dimension-table > tbody > tr.single_dimension').each(function() {
            let CurrentValue = $(this).find('select.select-dimension option:selected').val();
            wp_analytify_cd_selected.push(CurrentValue);
        });
    }

    // recheck all hide/show option based on wp_analytify_cd_selected
    function wp_analytify_cd_hideshow_selected() {
        $('table#wp-analytify-dimension-table > tbody > tr.single_dimension select.select-dimension option:not(:selected)').each(function() {
            if (wp_analytify_cd_selected.includes($(this).val())) { $(this).hide(); } else { $(this).show(); }
        });
    }

    // check if all the dimension has been used and then hide the add button
    function wp_analytify_cd_check_add_button() {
        if (wp_analytify_cd_selected.length == wp_analytify_cd_total) {
            $('.wp-analytify-add-dimension').hide();
        } else {
            $('.wp-analytify-add-dimension').show();
        }
    }

    // action: add a new dimension
    $(document).on('click', '.wp-analytify-add-dimension', function(e) {

        e.preventDefault();
        e.stopPropagation();

        wp_analytify_cd_repopulate_selected();

        // used in the input name
        let count = Math.random().toString(36).substr(2, 9);

        // html code that contains <tr>, <td>, and select that will be appended
        let html = '';
        html += '<tr class="single_dimension">';
        html += '<td>';
        html += '<select class="select-dimension" name="wp-analytify-custom-dimensions[analytiy_custom_dimensions][' + count + '][type]">';

        let has_selected = true;
        for (let key in wp_analytify_cd_available) {
            html += '<option value="' + key + '" ';
            if (wp_analytify_cd_selected.includes(key)) {
                html += 'style="display:none;"';
            } else if (has_selected) {
                html += 'selected';
                has_selected = false;
            }
            html += '> ' + wp_analytify_cd_available[key] + ' </option>';
        }

        html += '</select>';
        html += '</td>';
        html += '<td>';
        html += '<input type="number" class="dimension-id" name="wp-analytify-custom-dimensions[analytiy_custom_dimensions][' + count + '][id]" required>';
        html += '</td>';
        html += '<td><span class="wp-analytify-rmv-dimension"></span></td>';
        html += '</tr>';

        // append the select and input html
        $('#wp-analytify-dimension-table tbody').append(html);

        // focus on the last inserted input
        $('table#wp-analytify-dimension-table > tbody > tr.single_dimension:last-child').find('input.dimension-id').focus();

        wp_analytify_cd_repopulate_selected();

        // recheck all the selected option, show/hide based on that
        wp_analytify_cd_hideshow_selected();

        // check whether to show the add button or not
        wp_analytify_cd_check_add_button();

    });

    // action: remove the added dimension
    $(document).on('click', '.wp-analytify-rmv-dimension', function(e) {

        // remove the <tr>
        $(this).closest('tr').remove();

        // repopulate wp_analytify_cd_selected
        wp_analytify_cd_repopulate_selected();

        // recheck all the selected option, show/hide based on that
        wp_analytify_cd_hideshow_selected();

        // check whether to show the add button or not
        wp_analytify_cd_check_add_button();

    });

    // action: select option changed
    $(document).on('change', 'table#wp-analytify-dimension-table > tbody > tr.single_dimension select.select-dimension', function(e) {

        // repopulate wp_analytify_cd_selected
        wp_analytify_cd_repopulate_selected();

        // recheck all the selected option, show/hide based on that
        wp_analytify_cd_hideshow_selected();

    });

    // License activation script. 
    var doing_license_registration_ajax = false;
    var admin_url = ajaxurl.replace('/admin-ajax.php', ''),
        spinner_url = admin_url + '/images/spinner';

    if (2 < window.devicePixelRatio) {
        spinner_url += '-2x';
    }
    spinner_url += '.gif';

    var ajax_spinner = '<img src="' + spinner_url + '" alt="" class="ajax-spinner general-spinner" />';

    $(document).on('click', "#analytify_dimensions_license_activate", function(e) {

        e.preventDefault();

        if (doing_license_registration_ajax) {
            return;
        }

        $('#dimensions-license-status').removeClass('notification-message error-notice');

        var license_key = $.trim($("#analytify_dimensions_license_key").val());

        if ('' === license_key) {
            $('#dimensions-license-status').addClass('notification-message error-notice');
            $('#dimensions-license-status').html(wpanalytify_strings.enter_license_key);
            return;
        }

        $('#dimensions-license-status').empty().removeClass('success-notice');
        doing_license_registration_ajax = true;
        $('#analytify_dimensions_license_activate').after('<img src="' + spinner_url + '" alt="" class="register-license-ajax-spinner general-spinner" />');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                action: 'dimensions_activate_license',
                dimensions_license_key: license_key,
                nonce: wpanalytify_data.nonces.activate_license,
                context: 'license'
            },
            error: function(jqXHR, textStatus, errorThrown) {
                doing_license_registration_ajax = false;
                $('.register-license-ajax-spinner').remove();
                $('#dimensions-license-status').html(wpanalytify_strings.register_license_problem);
            },
            success: function(data) {

                doing_license_registration_ajax = false;
                $('.register-license-ajax-spinner').remove();


                if ('undefined' !== typeof data.error) {

                    $('#dimensions-license-status').addClass('notification-message error-notice');
                    $('#dimensions-license-status').html(data.error);

                } else if (data == '0') {

                    $('#dimensions-license-status').addClass('notification-message error-notice');
                    $('#dimensions-license-status').html(wpanalytify_strings.register_license_problem);
                } else {
                    $('#dimensions-license-status').html(wpanalytify_strings.license_registered).delay(5000).fadeOut(1000);
                    $('#dimensions-license-status').addClass('notification-message success-notice');
                    $('#analytify_dimensions_license_key, #analytify_dimensions_license_activate').remove();
                    $('.dimensions-license-row').prepend(data.masked_license);

                }
            }
        });
    });
    // License activation script end. 

});