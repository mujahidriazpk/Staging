<?php
/**
 * Admin related functions
 *
 * @package Dokan
 * @subpackage Subscription
 */
class DPS_Admin {

    function __construct() {

        $this->response = '';

        add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );

        // add product area in admin panel
        add_filter( 'product_type_selector', array( $this, 'add_product_type' ), 1 );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'general_fields' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'general_fields_save' ), 99 );

        add_action( 'dokan_admin_menu', array( $this, 'add_submenu_in_dokan_dashboard' ) );

        // settings section
        add_filter( 'dokan_settings_sections', array( $this, 'add_new_section_admin_panael' ) );
        add_filter( 'dokan_settings_fields', array( $this, 'add_new_setting_field_admin_panael' ), 12, 1 );

        //add dropdown field with subscription packs
        add_action( 'dokan_seller_meta_fields', array( $this, 'add_subscription_packs_dropdown' ), 10, 1 );

        //save user meta
        add_action( 'dokan_process_seller_meta_fields', array( $this, 'save_meta_fields' ) );

        if ( isset( $_POST['admin_subs_cancel'] ) && current_user_can( 'manage_options' ) ) {
            $result = $this->handle_cancel_subscription_pack();

            if ( $result ) {
                $this->response = __( 'Successfully Cancelled Subscription', 'dps' );
            } else {
                $this->response = __( 'Sorry Something wrong please try again', 'dps' );
            }
        }
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'dps-custom-style', DPS_URL . '/assets/css/style.css', false, date( 'Ymd' ) );
        wp_enqueue_script( 'dps-custom-admin-js', DPS_URL . '/assets/js/admin-script.js', array('jquery'), false, true );

        wp_localize_script( 'dps-custom-admin-js', 'dps', array(
            'ajaxurl'             => admin_url( 'admin-ajax.php' ),
            'subscriptionLengths' => DPS_Manager::get_subscription_ranges()
        ) );
    }

    /**
     * Add woocommerce extra product type
     *
     * @param array   $types
     * @param array   $product_type
     */
    function add_product_type( $types ) {

        $types['product_pack'] = __( 'Dokan Subscription', 'dps' );

        return $types;
    }

    /**
     * Add extra custom field in woocommerce product type
     */
    function general_fields() {
        global $woocommerce, $post;

        echo '<div class="options_group show_if_product_pack">';

        woocommerce_wp_text_input(
            array(
                'id'                => '_no_of_product',
                'label'             => __( 'Number of Products', 'dps' ),
                'placeholder'       => 10,
                'description'       => __( 'Enter the no of product you want to give this package.', 'dps' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            )
        );

        woocommerce_wp_text_input(
            array(
                'id'                => '_pack_validity',
                'label'             => __( 'Pack Validity', 'dps' ),
                'placeholder'       => 30,
                'description'       => __( 'Enter no of validity days you want to give this pack ', 'dps' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            )
        );

        woocommerce_wp_text_input(
            array(
                'id'                => '_vendor_commission',
                'label'             => __( 'Vendor Commission', 'dps' ),
                'placeholder'       => '',
                'description'       => __( 'How much (%) a vendor will get from each order, Leave empty ( not "0" ) if you don\'t apply any ovverride', 'dps' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            )
        );

        echo '<p class="form-field _vendor_allowed_categories">';
        $categories = get_terms( 'product_cat', array( 'orderby' => 'name' ) );
        $selected_cat = get_post_meta( $post->ID, '_vendor_allowed_categories', true );
        echo '<label for="_vendor_allowed_categories">' . __( 'Allowed categories', 'dps' ) .'</label>';
        echo '<select multiple="multiple" data-placeholder=" '. __( 'Select categories&hellip;', 'dps' ) .'" class="wc-enhanced-select" id="_vendor_allowed_categories" name="_vendor_allowed_categories[]" style="width: 350px;">';
            $r = array();
            $r['pad_counts']    = 1;
            $r['hierarchical']  = 1;
            $r['hide_empty']    = 1;
            $r['value']         = 'id';
            $r['selected']      = $selected_cat;

            include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );

            echo wc_walk_category_dropdown_tree( $categories, 0, $r );
        echo '</select>';
        echo '<span class="description">' . __( 'Select specific product category for this package. Leave empty to select all categories.', 'dps' ) . '</span>';

        echo '</p>';

        woocommerce_wp_checkbox(
            array(
                'id'          => '_enable_recurring_payment',
                'label'       => __( 'Recurring Payment', 'dps' ),
                'description' => __( 'Please check this if you want to enable recurring payment system', 'dps' ),
            )
        );
        echo '</div>';

        // Set month as the default billing period
        if ( !$subscription_period = get_post_meta( $post->ID, '_subscription_period', true ) )
            $subscription_period = 'month';

        echo '<div class="options_group subscription_pricin subscription_pricing">';
        // Subscription Period Interval
        echo '<div class="dokan-billing-cycle-wrap">';
        woocommerce_wp_select( array(
            'id'      => '_subscription_period_interval',
            'class'   => 'wc_input_subscription_period_interval',
            'label'   => __( 'Billing cycle', 'dps' ),
            'options' => DPS_Manager::get_subscription_period_interval_strings(),
        ) );

        // Billing Period
        woocommerce_wp_select( array(
            'id'          => '_subscription_period',
            'class'       => 'wc_input_subscription_period',
            'label'   => __( '', 'dps' ),
            /*'value'       => $subscription_period,*/
            'options'     => DPS_Manager::get_subscription_period_strings(),
        ) );

        echo '</div>';

        echo '<div class="dokan-billing-cyle-clear"></div>';

        // Subscription Length
        woocommerce_wp_select( array(
            'id'          => '_subscription_length',
            'class'       => 'wc_input_subscription_length',
            'label'       => __( 'Billing cycle stop', 'dps' ),
            'options'     => DPS_Manager::get_subscription_ranges( $subscription_period ),

        ) );

        echo '</div>';
    }


    /**
     * Manupulate custom filed meta data in post meta
     *
     * @param integer $post_id
     */
    function general_fields_save( $post_id ) {

        if ( ! isset( $_POST['product-type'] ) || $_POST['product-type'] != 'product_pack' ) {
            return;
        }

        update_post_meta( $post_id, '_virtual', 'yes' );
        update_post_meta( $post_id, '_sold_individually', 'yes' );

        // WC 3.0+ compatibility
        $visibility_term = array( 'exclude-from-search', 'exclude-from-catalog' );
        wp_set_post_terms( $post_id, $visibility_term, 'product_visibility', false );
        update_post_meta( $post_id, '_visibility', 'hidden' );

        $woocommerce_no_of_product_field = $_POST['_no_of_product'];

        if ( ! empty( $woocommerce_no_of_product_field ) ) {
            update_post_meta( $post_id, '_no_of_product', $woocommerce_no_of_product_field );
        }

        $woocommerce_pack_validity_field = $_POST['_pack_validity'];

        if ( ! empty( $woocommerce_pack_validity_field ) ) {
            update_post_meta( $post_id, '_pack_validity', $woocommerce_pack_validity_field );
        }

        $woocommerce_vendor_commission_field = $_POST['_vendor_commission'];

        if ( ! empty( $woocommerce_vendor_commission_field ) ) {
            update_post_meta( $post_id, '_vendor_commission', $woocommerce_vendor_commission_field );
        }

        if ( ! empty( $_POST['_vendor_allowed_categories'] ) ) {
            update_post_meta( $post_id, '_vendor_allowed_categories', $_POST['_vendor_allowed_categories'] );
        } else {
            delete_post_meta( $post_id, '_vendor_allowed_categories' );
        }


        $woocommerce_enable_recurring_field = isset( $_POST['_enable_recurring_payment'] ) ? 'yes' : 'no';

        if ( ! empty( $woocommerce_enable_recurring_field ) ) {
            update_post_meta( $post_id, '_enable_recurring_payment', $woocommerce_enable_recurring_field );
        }

        $woocommerce_subscription_period_interval_field = $_POST['_subscription_period_interval'];

        if ( ! empty( $woocommerce_enable_recurring_field ) ) {
            update_post_meta( $post_id, '_subscription_period_interval', $woocommerce_subscription_period_interval_field );
        }

        $woocommerce_subscription_period_field = $_POST['_subscription_period'];

        if ( ! empty( $woocommerce_enable_recurring_field ) ) {
            update_post_meta( $post_id, '_subscription_period', $woocommerce_subscription_period_field );
        }

        $woocommerce_subscription_length_field = $_POST['_subscription_length'];

        if ( ! empty( $woocommerce_enable_recurring_field ) ) {
            update_post_meta( $post_id, '_subscription_length', $woocommerce_subscription_length_field );
        }
    }


    /**
     * Add new Section in admin dokan settings
     *
     * @param array   $sections
     */
    function add_new_section_admin_panael( $sections ) {
        $sections['dokan_product_subscription'] = array(
            'id'    => 'dokan_product_subscription',
            'title' => __( 'Product Subscription', 'dps' )
        );

        return $sections;
    }

    /**
     * Get all Pages
     *
     * @param string  $post_type
     * @return array
     */
    function get_post_type( $post_type ) {

        $pages_array = array( '-1' => __( '- select -', 'dps' ) );
        $pages = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1 ) );

        if ( $pages ) {
            foreach ( $pages as $page ) {
                $pages_array[$page->ID] = $page->post_title;
            }
        }

        return $pages_array;
    }

    /**
     * Add new Settings field in admin dashboard for selection product
     * subscription page
     *
     * @param array   $settings_fields
     * @return array
     */
    function add_new_setting_field_admin_panael( $settings_fields ) {
        $pages_array = $this->get_post_type( 'page' );

        $settings_fields['dokan_product_subscription'] = array(
            array(
                'name'    => 'subscription_pack',
                'label'   => __( 'Subscription', 'dps' ),
                'type'    => 'select',
                'options' => $pages_array
            ),
            array(
                'name'  => 'enable_pricing',
                'label' => __( 'Enable Product Subscription', 'dps' ),
                'desc'  => __( 'Enable product subscription for vendor', 'dps' ),
                'type'  => 'checkbox'
            ),
            array(
                'name'    => 'enable_subscription_pack_in_reg',
                'label' => __( 'Enable Subscription in registration form', 'dps' ),
                'desc'  => __( 'Enable Subscription pack in registration form for new vendor', 'dps' ),
                'type'  => 'checkbox',
                'default' => 'on'
            ),
            array(
                'name'  => 'notify_by_email',
                'label' => __( 'Enable Email Notification', 'dps' ),
                'desc'  => __( 'Enable notification by email for vendor during end of the package expiration', 'dps' ),
                'type'  => 'checkbox'
            ),
            array(
                'name'    => 'no_of_days_before_mail',
                'label'   => __( 'No. of Days', 'dps' ),
                'desc'    => __( 'Before an email will be sent to the vendor', 'dps' ),
                'type'    => 'text',
                'size'    => 'midium',
                'default' => '2'
            ),
            array(
                'name'    => 'product_status_after_end',
                'label'   => __( 'Product Status', 'dps' ),
                'desc'    => __( 'Product status when vendor pack validity will expire', 'dps' ),
                'type'    => 'select',
                'default' => 'draft',
                'options' => array(
                    'publish' => __( 'Published', 'dps' ),
                    'pending' => __( 'Pending Review', 'dps' ),
                    'draft'   => __( 'Draft', 'dps' )
                )
            ),
            array(
                'name'  => 'email_subject',
                'label' => __( 'Email Subject', 'dps' ),
                'desc'  => __( 'Enter Subject text for email notification', 'dps' ),
                'type'  => 'text'
            ),
            array(
                'name'  => 'email_body',
                'label' => __( 'Email body', 'dps' ),
                'desc'  => __( 'Enter body text for email notification', 'dps' ),
                'type'  => 'textarea'
            )
        );

        if ( Dokan_Product_Subscription::is_dokan_plugin() ) {
            unset( $settings_fields['dokan_product_subscription'][0] );
        }

        return $settings_fields;
    }

    /**
     * Hanlde Delete Subscription form Admin panel by Admin
     *
     * @return boolean
     */
    function handle_cancel_subscription_pack() {
        $status = get_terms( 'shop_order_status' );

        if ( is_wp_error( $status ) ) {
            register_taxonomy( 'shop_order_status', array( 'shop_order' ), array( 'rewrite' => false ) );
        }

        $user_id    = (int) $_POST['user_id'];
        $order_id   = get_user_meta( $user_id, 'product_order_id', true );

        if ( get_user_meta( $user_id, '_customer_recurring_subscription', true ) == 'active' ) {
            dokan_dps_log( 'Subscription cancel check: Admin has canceled Subscription of User #' . $user_id . ' on order #' . $order_id );
            DPS_PayPal_Standard_Subscriptions::cancel_subscription_with_paypal( $order_id, $user_id );
            return true;

        } else {
            dokan_dps_log( 'Subscription cancel check: Admin has canceled Subscription of User #' . $user_id . ' on order #' . $order_id );
            Dokan_Product_Subscription::delete_subscription_pack( $user_id, $order_id );
            return true;
        }

        return false;
    }

    /**
     * Add submenu page in dokan Dashboard
     */
    function add_submenu_in_dokan_dashboard() {
        add_submenu_page( 'dokan', __( 'Dokan Subscription', 'dps' ), __( 'Subscription', 'dps' ), 'activate_plugins' , 'dokan-subscription', array( $this, 'admin_user_list' ) );
    }

    /**
     * Call back function for showing user subscriptions in admin panel
     *
     * @return none
     */
    function admin_user_list() {

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2><?php _e( 'Subscription User List', 'dps' ); ?></h2>

            <?php if ( $this->response ): ?>
                <div class="updated settings-error">
                   <p><strong><?php echo $this->response; ?></strong></p>
                </div>
            <?php endif?>

            <table class="widefat fixed wp_payme_table">
                <thead>
                    <tr>
                        <th><?php _e( 'Username', 'dps' ); ?> </th>
                        <th><?php _e( 'Subscription Pack', 'dps' ); ?> </th>
                        <th><?php _e( 'Start date', 'dps' ); ?> </th>
                        <th><?php _e( 'End date', 'dps' ); ?> </th>
                        <th><?php _e( 'Status', 'dps' ); ?> </th>
                        <th><?php _e( 'Action', 'dps' ); ?> </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><?php _e( 'Username', 'dps' ); ?> </th>
                        <th><?php _e( 'Subscription Pack', 'dps' ); ?> </th>
                        <th><?php _e( 'Start date', 'dps' ); ?> </th>
                        <th><?php _e( 'End date', 'dps' ); ?> </th>
                        <th><?php _e( 'Status', 'dps' ); ?> </th>
                        <th><?php _e( 'Action', 'dps' ); ?> </th>
                    </tr>
                </tfoot>
                <tbody>
                <?php
                $user_query = new WP_User_Query( array(
                    'role' => 'seller',
                    'meta_query' => array(
                        array(
                            'key'   => 'can_post_product',
                            'value' => '1'
                        ),
                        array(
                            'key'   => 'dokan_enable_selling',
                            'value' => 'yes'
                        ),
                    )
                ) );

                $users = $user_query->get_results();

                if ( $users ) {
                    foreach ( $users as $user ) {
                        ?>
                        <tr>
                            <td><a href="<?php echo get_edit_user_link( $user->ID ); ?>"><?php echo $user->data->user_nicename; ?></a></td>
                            <td><a href="<?php echo get_edit_post_link( get_user_meta( $user->ID, 'product_package_id', true ) ); ?>"><?php echo get_the_title( get_user_meta( $user->ID, 'product_package_id', true ) ); ?></a></td>
                            <td><?php echo date( 'F j, Y', strtotime( get_user_meta( $user->ID, 'product_pack_startdate', true ) ) ); ?></td>
                            <td><?php echo date( 'F j, Y', strtotime( get_user_meta( $user->ID, 'product_pack_enddate', true ) ) ); ?></td>
                            <td><?php echo ( get_user_meta( $user->ID, 'can_post_product', true ) == 1 )? __( 'Active', 'dps' ) : __( 'Cancelled', 'dps' ) ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>">
                                    <input type="submit" name="admin_subs_cancel" value="<?php _e( 'Cancel', 'dps' ); ?>" class="button button-primary">
                                </form>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">
                            <?php _e( 'No users with subscription found!', 'dps' ); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Add subscription packs in drowpdown to let admin select a pack for the seller
     */
    public function add_subscription_packs_dropdown( $user ){

        $users_assigned_pack = get_user_meta( $user->ID, 'product_package_id', true );
        $vendor_allowed_categories = get_user_meta( $user->ID, 'vendor_allowed_categories', true );

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'product_pack',
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_enable_recurring_payment',
                    'value' => 'no',
                )
            )
        );
        $sub_packs = get_posts( $args );
        ?>
        <tr>
            <td>
                <h3><?php _e( 'Dokan Subscription', 'dps' ); ?> </h3>
            </td>
        </tr>

        <?php if ( $users_assigned_pack ) : ?>
            <tr>
                <td><?php _e( 'Currently Activated Pack', 'dps' ); ?></td>
                <td> <?php echo get_the_title( $users_assigned_pack ); ?> </td>
            </tr>
            <tr>
                <td><?php _e( 'Start Date :' ) ;?></td>
                <td><?php echo date( get_option( 'date_format' ), strtotime( get_user_meta( $user->ID, 'product_pack_startdate', true ) ) ); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'End Date :' ) ;?></td>
                <td><?php echo date( get_option( 'date_format' ), strtotime( get_user_meta( $user->ID, 'product_pack_enddate', true ) ) ); ?></td>
            </tr>
        <?php endif; ?>

        <tr>
             <?php if ( $users_assigned_pack  && get_user_meta( $user->ID, '_customer_recurring_subscription', true ) == 'active' ) : ?>
                <td colspan="2"><?php  _e( '<i>This user already has recurring pack assigned. Are you sure to assign a new normal pack to the user? If you do so, the existing recurring plan will be replaced with the new one<i>', 'dps' ); ?></td>
            <?php endif; ?>
        </tr>

        <tr>
            <td><?php _e( 'Allowed categories', 'dps' ); ?></td>
            <td>
                <?php
                    $categories = get_terms( 'product_cat', array( 'orderby' => 'name' ) );
                    $selected_cat = !empty( $vendor_allowed_categories ) ? $vendor_allowed_categories : get_post_meta( $users_assigned_pack, '_vendor_allowed_categories', true );
                    echo '<select multiple="multiple" data-placeholder=" '. __( 'Select categories&hellip;', 'dps' ) .'" class="wc-enhanced-select" id="vendor_allowed_categories" name="vendor_allowed_categories[]" style="width: 350px;">';
                    $r = array();
                    $r['pad_counts']    = 1;
                    $r['hierarchical']  = 1;
                    $r['hide_empty']    = 1;
                    $r['value']         = 'id';
                    $r['selected']      = ! empty( $selected_cat ) ? $selected_cat : array();

                    include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );

                    echo wc_walk_category_dropdown_tree( $categories, 0, $r );
                    echo '</select>';
                ?>
                <p class="description"><?php _e( 'You can override allowed categories for this user. If empty then the predefined category for this pack will be selected', 'dps' ); ?></p>
            </td>
        </tr>

        <tr class="dps_assign_pack">
            <td><?php _e( 'Assign Subscription Pack', 'wedevs' ); ?></td>
            <td>
                <select name="_dokan_user_assigned_sub_pack">
                    <option value="" <?php selected( $users_assigned_pack, '' ); ?>><?php _e( '-- Select a pack --', 'dps' ); ?></option>
                    <?php foreach ( $sub_packs as $pack ) : ?>
                        <option value="<?php echo $pack->ID;?>" <?php selected( $users_assigned_pack, $pack->ID ); ?>><?php echo $pack->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php _e( 'You can only assign non-recurring packs', 'dps' ); ?></p>
            </td>
        </tr>
    <?php
    }

    public function save_meta_fields( $user_id ){

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! isset( $_POST['dokan_enable_selling'] ) ) {
            return;
        }

        if ( ! isset( $_POST['_dokan_user_assigned_sub_pack'] ) ) {
            return;
        }

        $pack_id = intval( $_POST['_dokan_user_assigned_sub_pack'] );

        if ( !$pack_id || empty( $pack_id ) ) {
            return;
        }

        //cancel paypal if current pack is recurring
        if( get_user_meta( $user_id, '_customer_recurring_subscription', true ) == 'active' ) {
            $order_id = get_user_meta( $user_id, 'product_order_id', true );

            if ( $order_id ) {
                dokan_dps_log( 'Subscription cancel check: On assign pack by admin cancel Recurring Subscription of User #' . $user_id . ' on order #' . $order_id );
                DPS_PayPal_Standard_Subscriptions::cancel_subscription_with_paypal( $order_id , $user_id );
            }
        }

        $pack_validity = get_post_meta( $pack_id, '_pack_validity', true );
        $vendor_commission = get_post_meta( $pack_id, '_vendor_commission', true );
        update_user_meta( $user_id, 'product_package_id', $pack_id );
        update_user_meta( $user_id, 'product_order_id', '' );
        update_user_meta( $user_id, 'product_no_with_pack' , get_post_meta( $pack_id, '_no_of_product', true ) ); //number of products
        update_user_meta( $user_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
        update_user_meta( $user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+$pack_validity days" ) ) );
        update_user_meta( $user_id, 'can_post_product' , 1 );
        update_user_meta( $user_id, '_customer_recurring_subscription', '' );

        if ( !empty( $_POST['vendor_allowed_categories'] ) ) {
            $allowed_cat = array_map( 'intval', $_POST['vendor_allowed_categories'] );
            update_user_meta( $user_id, 'vendor_allowed_categories', $allowed_cat );
        } else {
            delete_user_meta( $user_id, 'vendor_allowed_categories' );
        }

        if ( !empty( $vendor_commission ) ) {
            update_user_meta( $user_id, 'dokan_seller_percentage', $vendor_commission );
        } else {
            update_user_meta( $user_id, 'dokan_seller_percentage', '' );
        }
    }
}

new DPS_Admin();