<?php do_action( 'dokan_stripe_express_vendor_settings_before', $user_id ); ?>

<div id="dokan-stripe-express-payment">
    <div class="dokan-alert dokan-alert-success dokan-text-middle signup-message" id="dokan-stripe-express-signup-message"></div>
    <div class="dokan-alert dokan-alert-danger dokan-text-middle signup-message" id="dokan-stripe-express-signup-error"></div>

    <?php if ( ! empty( $stripe_account ) ) : ?>
        <?php if ( $stripe_account->charges_enabled ) : ?>
            <div class="dokan-alert dokan-alert-success dokan-text-middle">
                <?php
                    printf(
                        /* translators: 1) line break <br> tag, 2) merchant id, 3) line break <br> tag, */
                        esc_html__( 'Your account is connected with Stripe Express.%1$sMerchant ID: %2$s.%3$sYou can visit your Stripe Express dashboard to track your payments and transactions.', 'dokan' ),
                        '<br>',
                        "<strong>{$stripe_account->id}</strong>",
                        '<br>'
                    );
                ?>
            </div>

            <div id="dokan-stripe-express-vendor-signup-message"></div>

            <button class="dokan-btn"
                id="dokan-stripe-express-dashboard-login"
                data-user="<?php echo esc_attr( $user_id ); ?>">
                <?php esc_html_e( 'Visit Express Dashboard', 'dokan' ); ?>
            </button>

            <button class="dokan-btn dokan-btn-danger"
                id="dokan-stripe-express-account-disconnect"
                data-user="<?php echo esc_attr( $user_id ); ?>">
                <?php esc_html_e( 'Disconnect', 'dokan' ); ?>
            </button>
        <?php else : ?>
            <div class="dokan-alert dokan-alert-warning dokan-text-middle">
                <?php esc_html_e( 'Your have not completed the onboarding for Stripe Express. You can complete the process by clicking the button below.', 'dokan' ); ?>
            </div>

            <div id="dokan-stripe-express-vendor-signup-message"></div>

            <button class="dokan-btn"
                id="dokan-stripe-express-account-connect"
                data-user="<?php echo esc_attr( $user_id ); ?>">
                <?php esc_html_e( 'Complete Onboarding', 'dokan' ); ?>
            </button>
        <?php endif; ?>
    <?php else : ?>
        <div class="dokan-alert dokan-alert-warning dokan-text-left" id="dokan-stripe-express-account-notice">
            <?php esc_html_e( 'Your account is not connected with Stripe Express. Click on the button below to sign up.', 'dokan' ); ?>
        </div>

        <div id="dokan-stripe-express-account-connect"
            data-user="<?php echo esc_attr( $user_id ); ?>">
            <img src="<?php echo esc_url_raw( DOKAN_STRIPE_EXPRESS_ASSETS . 'images/connect-button-slate.svg' ); ?>"
                alt="<?php esc_attr_e( 'Connect with Stripe', 'dokan' ); ?>">
        </div>
    <?php endif; ?>
</div>

<?php do_action( 'dokan_stripe_express_vendor_settings_after', $user_id ); ?>
