<?php

    spl_autoload_register( function( $class_name ) {

        // only load WP_Persistent_Login classes
        if( strpos($class_name, 'WP_Persistent_Login') !== false ) {

            // if the class is a premium class, look in the premium folder
            if( strpos($class_name, 'Premium') !== false ) {
                $class_folder = 'classes/premium';
            } else {
                $class_folder = 'classes';
            }

            // format class name
            $class_file_name = str_replace('_', '-', strtolower($class_name) );

            // build the class file name 
            $file_path = WPPL_PLUGIN_PATH . $class_folder . '/'. $class_file_name . '.php';

            if( file_exists($file_path) ) {
                include $file_path;
            }
            
       
        }
    });

?>