<h3><?php esc_html_e( 'Stripe', 'dokan' ); ?></h3>
<p><?php esc_html_e( 'Stripe works by adding credit card fields on the checkout and then sending the details to Stripe for verification.', 'dokan' ); ?></p>
<p>
    <?php
    echo wp_kses(
        sprintf(
            /* translators: 1) payment settings url, 2) stripe dashboard url */
            __( 'Set your authorize redirect uri <code>%1$s</code> in your Stripe <a href="%2$s" target="_blank">application settings</a> for Redirects.', 'dokan' ),
            $dashboard_url,
            'https://dashboard.stripe.com/account/applications/settings'
        ),
        [
            'a'    => [
                'href'   => true,
                'target' => true,
            ],
            'code' => [],
        ]
    )
    ?>
</p>

<table class="form-table">
    <?php $gateway->generate_settings_html(); ?>
</table>
