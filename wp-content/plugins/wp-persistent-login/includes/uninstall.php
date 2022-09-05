<?php

function persistent_login_uninstall_cleanup()
{
    // remove database options
    $options = array( 'persistent_login_db_version', 'persistent_login_options', 'persistent_login_user_count' );
    foreach ( $options as $option ) {
        delete_option( $option );
    }
    // unschedule cron event
    wp_clear_scheduled_hook( 'persistent_login_user_count' );
}
