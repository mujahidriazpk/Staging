<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\PaymentMethods;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\PaymentMethod;

/**
 * Gateway handler class for Stripe Credit/Debit Cards.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\PaymentMethods
 */
class Ideal extends PaymentMethod {

    /**
     * Stores Stripe ID.
     *
     * @since 3.6.1
     *
     * @var string
     */
    const STRIPE_ID = 'ideal';

    /**
     * Strores label for the method.
     *
     * @since 3.6.1
     *
     * @var string
     */
    const LABEL = 'iDEAL';

    /**
     * Constructor for iDEAL payment method.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->stripe_id            = self::STRIPE_ID;
        $this->title                = apply_filters( 'dokan_stripe_express_payment_method_title', __( 'Pay with iDEAL', 'dokan' ), self::STRIPE_ID );
        $this->is_reusable          = true;
        $this->supported_currencies = [ 'EUR' ];
        $this->label                = Helper::get_method_label( self::STRIPE_ID );
        $this->description          = __(
            'iDEAL is a Netherlands-based payment method that allows customers to complete transactions online using their bank credentials.',
            'dokan'
        );
    }
}
