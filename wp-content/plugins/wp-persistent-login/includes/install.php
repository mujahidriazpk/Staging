<?php

/**
 * Run activation function to setup 
 */
function persistent_login_activate()
{
    // add db version for future reference
    update_option( 'persistent_login_db_version', WPPL_DATABASE_VERSION );
    // setup CRON to check how many users are logged in
    // Use wp_next_scheduled to check if the event is already scheduled
    $timestamp = wp_next_scheduled( 'persistent_login_user_count' );
    // If $timestamp == false schedule daily backups since it hasn't been done previously
    if ( $timestamp == false ) {
        // Schedule the event for right now, then to repeat daily using the hook 'persistent_login_user_count'
        wp_schedule_event( time(), 'twicedaily', 'persistent_login_user_count' );
    }
    // set detaults for permissions - all roles are available for persistent login by default
    // free options
    
    if ( !get_option( 'persistent_login_options' ) ) {
        $defaultOptions = array(
            'duplicateSessions' => '0',
            'limitActiveLogins' => '0',
            'limitActiveLogins' => '0',
        );
        update_option( 'persistent_login_options', $defaultOptions );
    }

}
