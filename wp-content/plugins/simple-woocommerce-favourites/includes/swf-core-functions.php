<?php

/**
 * Core Favouries Functions
 */

    /*
        Get Favouries List
    */
    function swf_get_favourites( $user_id = false ){
        if(!$user_id){
            $user_id = get_current_user_id();
        }
        $favourites = get_user_meta( $user_id, '_simple_favourites_string', true );
        if( empty( $favourites ) ){
            $favourites = array();
        }
        $favourites = swf_check_favourites_products ($favourites );
        return $favourites;
    }

    /*
        Check Favourites List
    */
    function swf_check_favourites_products( $favourites ){
        $flag = false;
        foreach( $favourites as $key => $id ){
            if( false === get_post_status($id) || 'trash' === get_post_status( $id ) ){
                unset( $favourites[$key] );
                $flag = true;
            }
        }
        if($flag){
            swf_update_favourites( $favourites );
        }
        return $favourites;
    }

    /* 
        Add Product to Favourites
    */
    function swf_add_favourite( $product_id, $favourites, $update = false ){
        if( ! in_array( $product_id, $favourites ) ){
            array_push( $favourites, $product_id );
            if( $update ){
                swf_update_favourites( $favourites );
            }
        }
        return $favourites;
    }

    /*
        Remove Product from Favourites
    */
    function swf_remove_favourite( $product_id ){
        $user_id    = get_current_user_id();
        $favourites = get_user_meta( $user_id, '_simple_favourites_string', true );
        if( ($key = array_search( $product_id, $favourites ) ) !== false ){
            unset( $favourites[$key] );
        }
        swf_update_favourites( $favourites, $user_id );
    }

    /*
        Update Favourites List
    */
    function swf_update_favourites( $favourites, $user_id = false ){
        if( !$user_id ){
            $user_id = get_current_user_id();
        }
        update_user_meta( $user_id, '_simple_favourites_string', $favourites );
    }