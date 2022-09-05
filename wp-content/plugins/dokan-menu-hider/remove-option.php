<?php 

/*
Plugin Name: Dokan Menu Hider
Plugin URI: https://github.com/nayemDevs/Dokan-tab-remover
Description: Remove seller dashboard menu/tab easily - Dokan
Author: Nayem
Version: 2.5.3
Author URI: https://nayemdevs.com
License: GPL2
Tested up to: 4.9.7
TextDomain: dokan
*/


// Admin menu in Dokan settings -> Selling option

add_filter( 'dokan_settings_fields', 'wlr_tab_remover', 10);


function wlr_tab_remover($settings_fields){

        $settings_fields ['dokan_selling']['remove_tab'] = array(
            'name'    => 'remove_tab',
            'label'   => __( 'Hide Vendor Dashboard Menu', 'dokan' ),
            'desc'    => __( 'Select the dashboard menu to hide from seller', 'dokan' ),
            'type'    => 'multicheck',
            'default' => array( 'products' => 'Products'),
            'options' => array(
                'products' => 'Products',
                'orders'   => 'Orders',
                'withdraw' => 'Withdraw',
                'coupons'  => 'Coupons', 
                'reviews'  => 'Reviews',
                'reports'  => 'Reports',
                'store'    => 'Store', 
                'payment'  => 'Payment',
                'shipping' => 'Shipping',
                'social'   => 'Social',
                'seo'      => 'SEO',
                'staffs'   => 'Staff',
                'return-request' =>'Return Request',
                'rma'     =>   'RMA (Vendor sub-settings)'

                ),
            );


        return $settings_fields;



}

// Dashbaord menu removing function 

add_filter( 'dokan_get_dashboard_nav','wlr_dashbaord_nav', 16);

function wlr_dashbaord_nav($urls){

    $menus = dokan_get_option('remove_tab','dokan_selling');

   if ( ! empty( $menus ) ) {
       foreach ( $menus as $key => $value ) {
           if ( isset( $urls[ $value ] ) ) {
               unset( $urls[ $value ] );
           }
       }
   }

   return $urls;
}



add_filter( 'dokan_get_dashboard_settings_nav','wlrs_dashbaord_settings_nav',15);

function wlrs_dashbaord_settings_nav($sub_settins){

 $menus = dokan_get_option('remove_tab','dokan_selling');
    
     if ( ! empty( $menus ) ) {
       foreach ( $menus as $key => $value ) {
           if ( isset( $sub_settins[ $value ] ) ) {
               unset( $sub_settins[ $value ] );
           }
       }
   }

    return $sub_settins;
}

// function author_admin_notice(){
//     global $pagenow;
//     if ( $pagenow == 'index.php' ) {
//     $user = wp_get_current_user();
//     if ( in_array( 'administrator', (array) $user->roles ) ) {
//     echo '<div class="notice notice-info is-dismissible">
//           <h2> WooCommerce Toolkit </h2>
//           <p> Get more orders from your customer</p>
//           <p> <a href="https://wpdoctor.press/product/login-redirect-pro/"> <img src="https://i0.wp.com/wpdoctor.press/wp-content/uploads/2019/01/icon-128x128.png?resize=230%2C300&ssl=1" width="100px" alt="" target="_blank"></a></p>
//           <p>Download this plugin from <a href="https://wordpress.org/plugins/woo-toolkit">here</a></p>
//          </div>';
//     }
// }

// }
// add_action('admin_notices', 'author_admin_notice');




?>
