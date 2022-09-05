/* global Stripe, yith_stripe_info, woocommerce_params */

(function ( $ ) {

    var $body = $( 'body' ),
        $form  = $( 'form.checkout, form#order_review, form#add_payment_method' ),
        style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '16px',
                color: '#32325d'
            }
        },
        stripe = Stripe( yith_stripe_info.public_key ),
        elements = stripe.elements(),
        card = elements.create( 'card', { style: style, hidePostalCode: ! yith_stripe_info.show_zip } ),

        // init Stripe Elements fields
        init_elements = function() {
            // Add an instance of the card Element into the `card-element` <div>.
            card.mount( yith_stripe_info.elements_container_id );
        },

        // init error handling
        handle_elements_error = function( response ) {
            var ccForm = $( '#wc-yith-stripe-cc-form, #yith-stripe-cc-form' );

            $( '.woocommerce-error', ccForm ).remove();

            if ( response.error ) {
                // Remove token, if any
                $( '.stripe-token', ccForm ).remove();

                // Show the errors on the form
                if ( response.error.message ) {
                    ccForm.prepend( '<ul class="woocommerce-error"><li>' + response.error.message + '</li></ul>' );
                }
            }
        },

        // init form submit
        handle_form_submit = function( event ){
            var ccForm = $( '#wc-yith-stripe-cc-form, #yith-stripe-cc-form' ),
               $form =  $( 'form.checkout, form#order_review, form#add_payment_method' );

            if ( $form.is('.add-card') || $( 'input#payment_method_yith-stripe' ).is( ':checked' ) && ( ! $( 'input[name="wc-yith-stripe-payment-token"]').length || $( 'input[name="wc-yith-stripe-payment-token"]:checked').val() === 'new' ) ) {
                if ( 0 === $( 'input.stripe-token' ).length ) {
                    var nameInput = $( '#yith-stripe-card-name' ),
                        billing_country_input = $('#billing_country'),
                        billing_city_input = $('#billing_city:visible'),
                        billing_address_1_input = $('#billing_address_1:visible'),
                        billing_address_2_input = $('#billing_address_2:visible'),
                        billing_state_input = $('select#billing_state:visible, input#billing_state:visible'),

                        cardData = {
                            name: nameInput.length ? nameInput.val() : $('#billing_first_name' ).val() + ' ' + $('#billing_last_name' ).val(),
                            address_line1: billing_address_1_input.length ? billing_address_1_input.val() : '',
                            address_line2: billing_address_2_input.length ? billing_address_2_input.val() : '',
                            address_city: billing_city_input.length ? billing_city_input.val() : '',
                            address_state: billing_state_input.length ? billing_state_input.val() : '',
                            address_country: billing_country_input.length ? billing_country_input.val() : '',
                            currency: yith_stripe_info.currency
                        };

                    $form.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });

                    stripe.createToken( card, cardData ).then(function (result) {
                        if (result.error) {
                            // Inform the customer that there was an error.
                            $form.unblock();
                            handle_elements_error(result);
                        } else {
                            // Send the token to your server.
                            ccForm.append('<input type="hidden" class="stripe-token" name="stripe_token" value="' + result.token.id + '"/>');
                            $form.submit();
                        }
                    });

                    return false;
                }
            }

            return event;
        },

        // remove token from DOM
        remove_token = function(){
            $( '.stripe-token' ).remove();
        },

        // handle card selection
        handle_card_selection = function(){
            var $cards = $( '#payment').find( 'div.cards');

            if ( $cards.length ) {
                $cards.siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').hide();

                $( 'body' ).on( 'updated_checkout', function() {
                    $( '#payment').find( 'div.cards').siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').hide();
                });

                $('form.checkout, form#order_review').on( 'change', '#payment input[name="wc-yith-stripe-payment-token"]', function(){
                    var input = $(this),
                        $cards = $( '#payment').find( 'div.cards');

                    // change selected
                    $cards.find('div.card').removeClass('selected');
                    $cards.find('input[name="wc-yith-stripe-payment-token"]:checked').closest('div.card').addClass('selected');

                    if ( input.val() === 'new' ) {
                        $cards.siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').show();
                    } else {
                        $cards.siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').hide();
                    }
                });
            }
        };

    $( document ).on( 'ready ywsbs-auto-renew-opened', function(){

        // exit if there is no container for Elements
        if( 0 === $( yith_stripe_info.elements_container_id ).length ){
            return;
        }

        // handles errors messages
        card.addEventListener( 'change', handle_elements_error );

        // init elements and updates it when checkout is updated
        $body.on( 'updated_checkout', init_elements );

        // handle checkout error
        $body.on( 'checkout_error', remove_token );

        // handle form submit: checkout form
        $( 'form.checkout' ).on( 'checkout_place_order_yith-stripe', handle_form_submit );

        // handle form submit: pay / add card form
        $( 'form#order_review, form#add_payment_method' ).on( 'submit', handle_form_submit );

        // handle change of payment method
        $( 'form.checkout, form#order_review, form#add_payment_method' ).on( 'change', '#wc-yith-stripe-cc-form input, #yith-stripe-cc-form input', function() {
            $( '.stripe-token' ).remove();
        });

        init_elements();
        handle_card_selection();
    } );


})(jQuery);