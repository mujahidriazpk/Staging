<?php

// Create a helper function for easy SDK access.
function persistent_login()
{
    global  $persistent_login ;
    
    if ( !isset( $persistent_login ) ) {
        // Include Freemius SDK.
        require_once dirname( __FILE__ ) . '/freemius/start.php';
        $persistent_login = fs_dynamic_init( array(
            'id'             => '1917',
            'slug'           => 'wp-persistent-login',
            'type'           => 'plugin',
            'public_key'     => 'pk_2f0822b0db5884898e4f60e4b1d48',
            'is_premium'     => false,
            'premium_suffix' => '',
            'has_addons'     => false,
            'has_paid_plans' => true,
            'trial'          => array(
            'days'               => 7,
            'is_require_payment' => true,
        ),
            'menu'           => array(
            'slug'    => 'wp-persistent-login',
            'contact' => false,
            'support' => false,
            'account' => false,
            'parent'  => array(
            'slug' => 'users.php',
        ),
        ),
            'is_live'        => true,
        ) );
    }
    
    return $persistent_login;
}

// Init Freemius.
persistent_login();
// Signal that SDK was initiated.
do_action( 'persistent_login_loaded' );