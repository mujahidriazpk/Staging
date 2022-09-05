<?php

/**
 * persistent_login_update_db_check
 *
 * @return void
 */
function persistent_login_update_db_check()
{
    // check db version
    $persistent_login_db_version = WPPL_DATABASE_VERSION;
    $current_persistent_login_db_version = get_option( 'persistent_login_db_version' );
    // test db version number against plugin
    if ( $current_persistent_login_db_version !== $persistent_login_db_version ) {
        // if different, run the update function
        $persistent_login_db_update = persistent_login_update_db( $current_persistent_login_db_version );
    }
}

add_action( 'plugins_loaded', 'persistent_login_update_db_check' );
/**
 * persistent_login_update_db
 *
 * @param  mixed $persistent_login_db_version
 * @return void
 */
function persistent_login_update_db( $persistent_login_db_version )
{
    // multi-device support
    
    if ( $persistent_login_db_version === '1.1.3' ) {
        // load required global vars
        global  $wpdb ;
        $tableRef = WPPL_DATABASE_NAME;
        // set table name
        $table = $wpdb->prefix . $tableRef;
        // fetch charset for db
        $charset_collate = $wpdb->get_charset_collate();
        // run query
        $table_update = $wpdb->query( "\r\n                    ALTER TABLE {$table} \r\n                    ADD `ip` INT(11) NOT NULL AFTER `login_key`,\r\n                    ADD `user_agent` varchar(255) NOT NULL AFTER `ip`\r\n                " );
        // update db version option
        update_option( 'persistent_login_db_version', '1.1.3' );
        $persistent_login_db_version = '1.1.3';
    }
    
    // 1.1.3 update
    // timestamps
    
    if ( $persistent_login_db_version === '1.1.3' ) {
        // load required global vars
        global  $wpdb ;
        $tableRef = WPPL_DATABASE_NAME;
        // set table name
        $table = $wpdb->prefix . $tableRef;
        // fetch charset for db
        $charset_collate = $wpdb->get_charset_collate();
        // run query
        $table_update = $wpdb->query( "\r\n                    ALTER TABLE {$table} \r\n                    ADD `timestamp` CHAR(19) NOT NULL AFTER `user_agent`\r\n                " );
        // update db version option
        update_option( 'persistent_login_db_version', '1.2.3' );
        $persistent_login_db_version = '1.2.3';
    }
    
    // 1.2.3 update
    // remove db, no longer needed
    
    if ( $persistent_login_db_version === '1.2.3' ) {
        // remove all existing logins
        global  $wpdb ;
        $tableRef = WPPL_DATABASE_NAME;
        $table = $wpdb->prefix . $tableRef;
        // drop the table, we don't need it anymore!
        $sql = "DROP TABLE IF EXISTS {$table};";
        $drop = $wpdb->query( $sql );
        
        if ( $drop ) {
            // update db version option
            update_option( 'persistent_login_db_version', '1.3.0' );
            $persistent_login_db_version = '1.3.0';
            return true;
        } else {
            return false;
        }
    
    }
    
    // 1.3.0 update
    // fixing options in options table
    
    if ( $persistent_login_db_version === '1.3.0' ) {
        // fetching the current settings, which we don't need any more!
        $current_settings = get_option( 'persistent_login_options_user_access' );
        
        if ( $current_settings ) {
            // now delete the old free option, not needed anymore
            delete_option( 'persistent_login_options_user_access' );
            // update db version option
            update_option( 'persistent_login_db_version', '1.3.10' );
            $persistent_login_db_version = '1.3.10';
            return true;
        }
    
    }
    
    // 1.3.10 update
    
    if ( $persistent_login_db_version === '1.3.10' ) {
        // Use wp_next_scheduled to check if the event is already scheduled
        $timestamp = wp_next_scheduled( 'persistent_login_user_count' );
        // If $timestamp == false schedule daily backups since it hasn't been done previously
        if ( $timestamp == false ) {
            // Schedule the event for right now, then to repeat daily using the hook 'persistent_login_user_count'
            wp_schedule_event( time(), 'twicedaily', 'persistent_login_user_count' );
        }
        // update db version option
        update_option( 'persistent_login_db_version', '1.3.12' );
        $persistent_login_db_version = '1.3.12';
        return true;
    }
    
    // 1.3.12 update
    
    if ( $persistent_login_db_version === '1.3.12' ) {
        $options = get_option( 'persistent_login_options' );
        if ( !isset( $options['limitActiveLogins'] ) ) {
            $options['limitActiveLogins'] = '0';
        }
        if ( !isset( $options['duplicateSessions'] ) ) {
            $options['duplicateSessions'] = '0';
        }
        update_option( 'persistent_login_options', $options );
        // update db version option
        update_option( 'persistent_login_db_version', '2.0.0' );
        $persistent_login_db_version = '2.0.0';
        return true;
    }
    
    // 2.0.0 update
}
