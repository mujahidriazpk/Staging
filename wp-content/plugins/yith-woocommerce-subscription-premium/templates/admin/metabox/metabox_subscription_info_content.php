<?php

/**
 * Metabox for Subscription Info Content
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


$billing_address = $subscription->get_address_fields( 'billing', true );
$shipping_address = $subscription->get_address_fields( 'shipping', true );
$billing_fields = ywsbs_get_order_fields_to_edit( 'billing' );
$shipping_fields = ywsbs_get_order_fields_to_edit( 'shipping' );
?>

<div id="subscription-data" class="panel">

    <h2><?php printf(__('Subscription #%d details', 'yith-woocommerce-subscription'), $subscription->id) ?> <span class="status <?php echo $subscription->status ?>"><?php echo $subscription->status ?></span></h2>
    <p class="subscription_number"> <?php echo $subscription->product_name ?> </p>
    <p class="subscription_number"> <?php echo $subscription->get_formatted_recurring() ?> </p>

    <div class="subscription_data_column_container">
        <div class="subscription_data_column">
            <h3>General Details</h3>

            <p class="form-field form-field-wide"><label><?php  _e( '<strong>Started date</strong>:', 'yith-woocommerce-subscription') ?></label>
                <?php echo ( $subscription->start_date ) ? date_i18n( wc_date_format(), $subscription->start_date ).' '.date_i18n(__( wc_time_format()), $subscription->start_date ) : ''; ?>
            </p>

            <p class="form-field form-field-wide"><label><?php  _e( '<strong>Expired date</strong>:', 'yith-woocommerce-subscription') ?></label>
                <?php echo ( $subscription->expired_date ) ? date_i18n( wc_date_format(), $subscription->expired_date ).' '.date_i18n(__( wc_time_format()), $subscription->expired_date ) : ''; ?>
            </p>

            <p class="form-field form-field-wide"><label><?php  _e( '<strong>Payment due date</strong>:', 'yith-woocommerce-subscription') ?></label>
                <?php echo ( $subscription->payment_due_date ) ? date_i18n( wc_date_format(), $subscription->payment_due_date ).' '.date_i18n(__( wc_time_format()), $subscription->payment_due_date ) : ''; ?>
            </p>

            <?php if( $subscription->cancelled_date != ''): ?>
            <p class="form-field form-field-wide"><label><?php  _e( '<strong>Cancelled date</strong>:', 'yith-woocommerce-subscription') ?></label>
                <?php echo ( $subscription->cancelled_date ) ? date_i18n( wc_date_format(), $subscription->cancelled_date ).' '.date_i18n(__( wc_time_format()), $subscription->cancelled_date ) : ''; ?>
            </p>
            <?php endif ?>

            <?php if( $subscription->end_date != ''): ?>
                <p class="form-field form-field-wide"><label><?php  _e( '<strong>End date</strong>:', 'yith-woocommerce-subscription') ?></label>
                    <?php echo ( $subscription->end_date ) ? date_i18n( wc_date_format(), $subscription->end_date ).' '.date_i18n(__( wc_time_format()), $subscription->end_date ) : ''; ?>
                </p>
            <?php endif ?>

            <?php if( $subscription->payed_order_list != ''): ?>
                <p class="form-field form-field-wide"><label><?php  _e( '<strong>List of paid orders</strong>:', 'yith-woocommerce-subscription') ?></label>
                   <?php foreach ( $subscription->payed_order_list as $order ) : ?>
                       <a href="<?php echo admin_url( 'post.php?post=' . $order . '&action=edit' ) ?>">#<?php echo $order ?></a>
                   <?php endforeach;
                   ?>
                </p>
            <?php endif ?>

            <?php if( $renew_order = $subscription->has_a_renew_order() ):
	            $renew_order_id = yit_get_order_id( $renew_order );
				?>
                <p class="form-field form-field-wide"><label><?php  _e( '<strong>Renew Order</strong>:', 'yith-woocommerce-subscription') ?></label>
                        <a href="<?php echo admin_url( 'post.php?post=' . $renew_order_id . '&action=edit' ) ?>">#<?php echo $renew_order_id ?></a>
                </p>
            <?php endif ?>

            <p class="form-field form-field-wide"><label><?php  _e( '<strong>Payment Method</strong>:', 'yith-woocommerce-subscription') ?></label>
                <?php echo $subscription->payment_method_title  ?>
            </p>
            <?php if( $subscription->transaction_id != ''): ?>
            <p class="form-field form-field-wide"><label><?php  _e( '<strong>Transaction ID</strong>:', 'yith-woocommerce-subscription') ?></label>
                <?php echo $subscription->transaction_id  ?>
            </p>
            <?php endif ?>
            <?php
                woocommerce_wp_hidden_input( array( 'id' => 'user_id', 'value' =>$subscription->user_id, 'class' =>'customer_id') );

            ?>
        </div>
        <div class="subscription_data_column">
            <h3>
		        <?php esc_html_e( 'Billing', 'yith-woocommerce-subscription' ); ?>
                <a href="#" class="edit_address"><?php esc_html_e( 'Edit', 'yith-woocommerce-subscription' ); ?></a>
                <span>
                    <a href="#" class="load_customer_billing load_customer_info" data-from='billing' data-to="billing"
                                   style="display:none;"><?php esc_html_e( 'Load billing address', 'yith-woocommerce-subscription' ); ?></a>
				</span>
            </h3>

            <div class="address">
                <?php

                $formatted_address = WC()->countries->get_formatted_address( $billing_address );
                if ( $formatted_address ) {
	                echo '<p>' . wp_kses( $formatted_address, array( 'br' => array() ) ) . '</p>';
                } else {
	                echo '<p class="none_set"><strong>' . __( 'Address:', 'yith-woocommerce-subscription' ) . '</strong> ' . __( 'No billing address set.', 'yith-woocommerce-subscriptions' ) . '</p>';
                }


                foreach ( $billing_fields as $key => $field ) {
	                if ( isset( $field['show'] ) && false === $field['show'] ) {
		                continue;
	                }

	                $field_name  = 'billing_' . $key;
	                $field_value = $billing_address[ $key ];

	                if ( 'billing_phone' === $field_name ) {
		                $field_value = wc_make_phone_clickable( $field_value );
	                } else {
		                $field_value = make_clickable( esc_html( $field_value ) );
	                }

	                echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p>';
                }
                ?>

            </div>
           <div class="edit_address">
               <?php

               foreach ( $billing_fields as $key => $field ) {

	               if ( ! isset( $field['type'] ) ) {
		               $field['type'] = 'text';
	               }
	               $field['value'] = $billing_address[ $key ];
	               if ( ! isset( $field['id'] ) ) {
		               $field['id'] = '_billing_' . $key;
	               }
	               switch ( $field['type'] ) {
		               case 'select' :
			               woocommerce_wp_select( $field );
			               break;
		               default :
			               woocommerce_wp_text_input( $field );
			               break;
	               }
               }
               ?> 
           </div>
            <?php
                do_action( 'ywcsb_admin_subscription_data_after_billing_address', $subscription )
            ?>
        </div>

        <div class="subscription_data_column">
            <h3>
		        <?php esc_html_e( 'Shipping', 'yith-woocommerce-subscription' ); ?>
                <a href="#" class="edit_address"><?php esc_html_e( 'Edit', 'yith-woocommerce-subscription' ); ?></a>
                <span>
                    <a href="#" class="load_customer_shipping load_customer_info" data-from='shipping' data-to="shipping" style="display:none;"><?php esc_html_e( 'Load shipping address', 'yith-woocommerce-subscription' ); ?></a>
                    <a href="#" class="billing-same-as-shipping load_customer_info" data-from='billing' data-to="shipping" style="display:none;"><?php esc_html_e( 'Copy billing address', 'yith-woocommerce-subscription' ); ?></a>
                </span>
            </h3>
            <div class="address">
		        <?php

		        $formatted_address = WC()->countries->get_formatted_address( $shipping_address );
		        if ( $formatted_address ) {
			        echo '<p>' . wp_kses( $formatted_address, array( 'br' => array() ) ) . '</p>';
		        } else {
			        echo '<p class="none_set"><strong>' . __( 'Address:', 'yith-woocommerce-subscription' ) . '</strong> ' . __( 'No shipping address set.', 'yith-woocommerce-subscriptions' ) . '</p>';
		        }

		        foreach ( $shipping_fields as $key => $field ) {
			        if ( isset( $field['show'] ) && false === $field['show'] ) {
				        continue;
			        }

			        $field_name = 'shipping_' . $key;
			        $field_value = $shipping_address[$key];

			        echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
		        }

		        if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) ) && $subscription->get_customer_order_note() ) {
			        echo '<p><strong>' . __( 'Customer provided note:', 'woocommerce' ) . '</strong> ' . nl2br( esc_html( $subscription->get_customer_order_note() ) ) . '</p>';
		        }
		        ?>

            </div>
            <div class="edit_address">
		        <?php

		        foreach ($shipping_fields as $key => $field ) {

			        if ( ! isset( $field['type'] ) ) {
				        $field['type'] = 'text';
			        }
			        $field['value'] = isset( $shipping_address[$key] ) ? $shipping_address[$key] : '';
			        if ( ! isset( $field['id'] ) ) {
				        $field['id'] = '_shipping_' . $key;
			        }
			        switch ( $field['type'] ) {
				        case 'select' :
					        woocommerce_wp_select( $field );
					        break;
				        default :
					        woocommerce_wp_text_input( $field );
					        break;
			        }
		        }

		        if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) )  ) { ?>
		            <p class="form-field form-field-wide"><label for="excerpt"><?php _e( 'Customer provided note', 'yith-woocommerce-subscription' ) ?>:</label>
                  <textarea rows="1" cols="40" name="customer_note" tabindex="6" id="excerpt" placeholder="<?php esc_attr_e( 'Customer notes about the order', 'yith-woocommerce-subscription' ); ?>"><?php echo wp_kses_post( $subscription->get_customer_order_note() ); ?></textarea></p>
               <?php
		        }

		        ?>
            </div>

	        <?php
	        do_action( 'ywcsb_admin_subscription_data_after_shipping_address', $subscription )
	        ?>
        </div>
    </div>
    <div class="clear"></div>
</div>