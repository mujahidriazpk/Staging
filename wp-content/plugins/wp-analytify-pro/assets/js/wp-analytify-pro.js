/**
 * All JS management for Pro version.
 */
(function($) {

    var doing_license_registration_ajax = false;
    var checked_license = false;
    var fade_duration = 400;
    var admin_url = ajaxurl.replace('/admin-ajax.php', ''),
        spinner_url = admin_url + '/images/spinner';

    if (2 < window.devicePixelRatio) {
        spinner_url += '-2x';
    }
    spinner_url += '.gif';

    var ajax_spinner = '<img src="' + spinner_url + '" alt="" class="ajax-spinner general-spinner" />';


    $(document).ready(function() {

        if (typeof(localStorage) != 'undefined') {

            activetab = localStorage.getItem("activetab");
            //console.info(activetab);
            if (activetab == '#wp-analytify-help') {

                if (false === checked_license && '1' === wpanalytify_data.has_license && 'analytify_page_analytify-settings' === pagenow) {
                    check_license();
                }
            }

            $('.nav-tab-wrapper a').click(function(e) {
                if ($(this).attr('id') == 'wp-analytify-help-tab')
                    if (false === checked_license && '1' === wpanalytify_data.has_license) {
                        check_license();
                    }
            });
        }


        function check_license(license) {

            checked_license = true;

            $('.support-content.full').hide();
            $('.support-content.check').show().html('<p>Fetching license details, please wait...</p>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    action: 'wpanalytifypro_check_license',
                    license: license,
                    context: 'all',
                    nonce: wpanalytify_data.nonces.check_license
                },
                error: function(jqXHR, textStatus, errorThrown) {

                    alert(wpanalytify_strings.license_check_problem);
                },
                success: function(data) {

                    //console.log(data);
                    //return;

                    var $support_content = $('.support-content.full');
                    var $license_content = $('.support-content.check');
                    var license_msg, support_msg;

                    if ('undefined' !== typeof data.error) {

                        var msg = '';
                        for (var key in data.error) {
                            msg += data.error[key];
                        }
                        support_msg = msg;


                        $license_content.stop().fadeOut(fade_duration, function() {
                            $(this)
                                .empty()
                                .html(support_msg)
                                .stop()
                                .fadeIn(fade_duration);
                        });

                    } else {
                        if ('undefined' !== typeof license)
                            $('.support-content.full form').attr('action', 'https://analytify.io/plugin-support-form/?action=priority-support&product=analytifypro&key=' + license);
                        $('.support-content.full #analytify-support-email').append($('<option></option>').val(data.customer_email).html(data.customer_email));
                        $('.support-content.check').stop().fadeOut();
                        $support_content.stop().fadeOut(fade_duration, function() {
                            $(this)
                                .stop()
                                .fadeIn(fade_duration);
                        });

                    }

                    // $license_content.stop().fadeOut( fade_duration, function() {
                    // 	$( this )
                    // 		.empty()
                    // 		.html( license_msg )
                    // 		.stop()
                    // 		.fadeIn( fade_duration );
                    // } );
                    // $support_content.stop().fadeOut( fade_duration, function() {
                    // 	$( this )
                    // 		.empty()
                    // 		.html( support_msg )
                    // 		.stop()
                    // 		.fadeIn( fade_duration );
                    // } );

                }
            });
        }

        function enable_pro_license(data, license_key) {

            $('#analytify_license_key, #analytify_license_activate').remove();
            $('.pro-license-row').prepend(data.masked_license);
            $('.support-content.check').empty().html('<p>' + wpanalytify_strings.fetching_license + '<img src="' + spinner_url + '" alt="" class="ajax-spinner general-spinner" /></p>');
            $('.support-content.check').delay(5000).fadeOut(1000).remove();
            $('.support-content.full').show();

            check_license(license_key);

        }

        $(document).on('click', "#analytify_license_activate", function(e) {

            e.preventDefault();

            if (doing_license_registration_ajax) {
                return;
            }

            $('#pro-license-status').removeClass('notification-message error-notice');

            var license_key = $.trim($("#analytify_license_key").val());

            if ('' === license_key) {
                $('#pro-license-status').addClass('notification-message error-notice');
                $('#pro-license-status').html(wpanalytify_strings.enter_license_key);
                return;
            }

            $('#pro-license-status').empty().removeClass('success-notice');
            doing_license_registration_ajax = true;
            $('#analytify_license_activate').after('<img src="' + spinner_url + '" alt="" class="register-license-ajax-spinner general-spinner" />');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                data: {
                    action: 'wpanalytifypro_activate_license',
                    license_key: license_key,
                    nonce: wpanalytify_data.nonces.activate_license,
                    context: 'license'
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    doing_license_registration_ajax = false;
                    $('.register-license-ajax-spinner').remove();
                    $('#pro-license-status').html(wpanalytify_strings.register_license_problem);
                },
                success: function(data) {
                    doing_license_registration_ajax = false;
                    $('.register-license-ajax-spinner').remove();


                    if ('undefined' !== typeof data.error) {

                        $('#pro-license-status').addClass('notification-message error-notice');
                        $('#pro-license-status').html(data.error);

                    } else {
                        $('#pro-license-status').html(wpanalytify_strings.license_registered).delay(5000).fadeOut(1000);
                        $('#pro-license-status').addClass('notification-message success-notice');
                        enable_pro_license(data, license_key);

                    }
                }
            });
        });

        $(document).on('click', "#analytify_woo_license_activate", function(e) {

            e.preventDefault();

            if (doing_license_registration_ajax) {
                return;
            }

            $('#woo-license-status').removeClass('notification-message error-notice');

            var license_key = $.trim($("#analytify_woo_license_key").val());

            if ('' === license_key) {
                $('#woo-license-status').addClass('notification-message error-notice');
                $('#woo-license-status').html(wpanalytify_strings.enter_license_key);
                return;
            }

            $('#woo-license-status').empty().removeClass('success-notice');
            doing_license_registration_ajax = true;
            $('#analytify_woo_license_activate').after('<img src="' + spinner_url + '" alt="" class="register-license-ajax-spinner general-spinner" />');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                data: {
                    action: 'wpanalytifywoo_activate_license',
                    woo_license_key: license_key,
                    nonce: wpanalytify_data.nonces.activate_license,
                    context: 'license'
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    doing_license_registration_ajax = false;
                    $('.register-license-ajax-spinner').remove();
                    $('#woo-license-status').html(wpanalytify_strings.register_license_problem);
                },
                success: function(data) {
                    doing_license_registration_ajax = false;
                    $('.register-license-ajax-spinner').remove();


                    if ('undefined' !== typeof data.error) {

                        $('#woo-license-status').addClass('notification-message error-notice');
                        $('#woo-license-status').html(data.error);

                    } else if (data == '0') {

                        $('#woo-license-status').addClass('notification-message error-notice');
                        $('#woo-license-status').html(wpanalytify_strings.register_license_problem);
                    } else {
                        $('#woo-license-status').html(wpanalytify_strings.license_registered).delay(5000).fadeOut(1000);
                        $('#woo-license-status').addClass('notification-message success-notice');
                        $('#analytify_woo_license_key, #analytify_woo_license_activate').remove();
                        $('.woo-license-row').prepend(data.masked_license);

                    }
                }
            });
        });

        /**
         * [Ajax processing on license management]
         */
        $(document).on('click', "#analytify_license_deactivate", function(event) {
            event.preventDefault();

            $.ajax({
                    url: Analytify.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'analytify_license_deactivate',
                    }
                })
                .done(function(response) {

                    $("#return-message").html(' <span style="color:green;">Deactive</span>');
                    $("#analytify_license_deactivate").attr('id', 'analytify_license_activate').val('Activate License');

                });
        });

    });

    $(document).on('click', '.analytify-export-data', function(e) {
        e.preventDefault();
        var _this = $(this);
        var start_date = $("#analytify_start").val();
        start_date = moment(start_date, 'MMM DD, YYYY').format("YYYY-MM-DD");

        var end_date = $("#analytify_end").val();
        end_date = moment(end_date, 'MMM DD, YYYY').format("YYYY-MM-DD");

        $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'analytify_export_csv',
                    stats_type: _this.data('stats-type'),
                    start_date: start_date,
                    end_date: end_date,
                    security: Analytify.export_nonce
                },
                beforeSend: function() {
                    _this.siblings('.analytify-export-loader').show();
                }
            })
            .done(function(res) {
                _this.siblings('.analytify-export-loader').hide();
                window.location.href = Analytify.exportUrl + '&export_type=csv&report_type=' + _this.data('stats-type') + '&security=' + Analytify.export_nonce + '&start_date=' + start_date + '&end_date=' + end_date;
            });

		});
		
})(jQuery);