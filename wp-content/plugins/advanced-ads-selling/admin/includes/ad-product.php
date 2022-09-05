<?php
/**
 * logic to initialize the ad product type and ad product page
 */
class Advanced_Ads_Selling_Admin_Ad_Product {
    
	/**
	 * @since     1.0.0
	 */
	public function __construct() {

	    add_action('plugins_loaded', array($this, 'wp_admin_plugins_loaded'));
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded() {

	    if (!class_exists('Advanced_Ads_Admin', false)) {
		return;
	    }

	    // show price and other product data fields
	    add_action( 'admin_footer', array( $this, 'ad_product_custom_js' ) );
	    // hide some product data tabs for ad product type
	    add_filter( 'woocommerce_product_data_tabs', array( $this, 'hide_ad_data_panel' ) );
	    // add custom product tab for ad type and all the needed logic
	    add_filter( 'woocommerce_product_data_tabs', array( $this, 'custom_product_tab' ) );
	    add_action( 'woocommerce_product_data_panels', array( $this, 'ad_options_product_tab_content' ) );
	    add_action( 'woocommerce_process_product_meta_advanced_ad', array( $this, 'save_ad_option_fields' ) );	
	}
	
	/**
	 * hide some product data panels for ad type
	 */
	public function hide_ad_data_panel( $tabs) {

		// Other default values for 'attribute' are; general, inventory, shipping, linked_product, variations, advanced
		$tabs['attribute']['class'][] = 'hide_if_advanced_ad';
		$tabs['shipping']['class'][] = 'hide_if_advanced_ad';
		// $tabs['linked_product']['class'][] = 'hide_if_advanced_ad'; // allow Upsells and Cross-sells
		$tabs['advanced']['class'][] = 'hide_if_advanced_ad';

		return $tabs;

	}

	/**
	 * handle product data fields for ad product.
	 */
	public function ad_product_custom_js() {

		if ( 'product' != get_post_type() ) :
			return;
		endif;

		?><script type='text/javascript'>
			jQuery( document ).ready( function() {
				jQuery( '.options_group.pricing' ).addClass( 'show_if_advanced_ad' ).show();
			});
		</script><?php
	}

	/**
	 * add a custom product tab for ad product type
	 */
	public function custom_product_tab( $tabs) {
		$tabs['ad_options'] = array(
			'label'		=> __( 'Ad Options', 'advanced-ads-selling' ),
			'target'	=> 'ad_options',
			'class'		=> array( 'show_if_advanced_ad'  ),
		);
		return $tabs;
	}

	/**
	 * show content of custom product tab in admin area
	 */
	public function ad_options_product_tab_content() {
		global $post;

		?><div id='ad_options' class='panel woocommerce_options_panel'><?php
			?><div class='options_group'><?php
				/**
				 * selectable ad types
				 * keys must match an Advanced Ads ad type
				 */
				$ad_types = apply_filters( 'advanced-ads-selling-product-tab-ad-types', array(
					    'plain' => _x( 'html', 'ad type', 'advanced-ads-selling' ), 
					    'image' => _x( 'image', 'ad type', 'advanced-ads-selling' )
					), $post
				);
			
				$this->woocommerce_wp_multiselect( array(
					'id' 		=> '_ad_types',
					'name' 		=> '_ad_types[]',
					'label' 	=> __( 'Available ad types', 'advanced-ads-selling' ),
					'options' 	=> $ad_types
				) );
				woocommerce_wp_select( array(
					'id' 		=> '_ad_sales_type',
					'label' 	=> __( 'Customer can buy per …', 'advanced-ads-selling' ),
					'options' 	=> Advanced_Ads_Selling_Plugin::get_instance()->sale_types,
				) );
				woocommerce_wp_textarea_input( array(
					'id' 		=> '_ad_prices',
					'label' 	=> __( 'Price options', 'advanced-ads-selling' ),
					'placeholder' 	=> __( 'define label, value and price separated by |, e.g. 100.000 impressions|100000|20.00', 'advanced-ads-selling' ),
					'desc_tip'		=> 'true',
					'description'	=> __( 'define label, value and price separated by |, e.g. 100.000 impressions|100000|20.00', 'advanced-ads-selling' ),
				) );
				$this->woocommerce_wp_multiselect( array(
					'id' 		=> '_ad_placements',
					'name' 		=> '_ad_placements[]',
					'label' 	=> __( 'Available placements', 'advanced-ads-selling' ),
					'options' 	=> $this->_get_placements_for_select(),
				) );
			?></div>

		</div><?php
	}

	/**
	 * Output for a multiselect field since WooCommerce does not have it
	 * 
	 * derrived from /woocommerce/includes/admin/wc-meta-box-functions.php::woocommerce_wp_select version 2.6.2
	 * 
	 * @param arr $field
	 */
	private function woocommerce_wp_multiselect( $field ) {
		    global $thepostid, $post;

		    $thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

		    // Custom attribute handling
		    $custom_attributes = array();

		    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . ' multiple="multiple">';

		    foreach ( $field['options'] as $key => $value ) {
			    //echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
			    echo '<option value="' . esc_attr( $key ) . '" ' . ( is_array( $field['value'] ) && in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
		    }

		    echo '</select> ';

		    if ( ! empty( $field['description'] ) ) {

			    if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				    echo wc_help_tip( $field['description'] );
			    } else {
				    echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			    }
		    }
		    echo '</p>';
	    }

	/**
	 * flatten multidimensional placements array for select
	 * 
	 * @return arr $placements as a one-dimensional array
	 */
	private function _get_placements_for_select(){
		$model = Advanced_Ads::get_instance()->get_model();
		$placements_raw = $model->get_ad_placements_array();
		$placements = array();
		foreach( $placements_raw as $_key => $_placement ){
		    $placements[$_key] = $_placement['name'];
		}

		return $placements;
	}

	/**
	 * save custom fields.
	 */
	public function save_ad_option_fields( $post_id ) {

		$ad_type_option = isset( $_POST['_ad_types'] ) ? $_POST['_ad_types'] : array( 'plain' );
		update_post_meta( $post_id, '_ad_types', $ad_type_option );

		$sales_type_option = isset( $_POST['_ad_sales_type'] ) ? $_POST['_ad_sales_type'] : 'flat';
		update_post_meta( $post_id, '_ad_sales_type', $sales_type_option );

		if ( isset( $_POST['_ad_prices'] ) ) {
			update_post_meta( $post_id, '_ad_prices', esc_attr( $_POST['_ad_prices'] ) );
		}

		if ( isset( $_POST['_ad_placements'] ) ) {
			update_post_meta( $post_id, '_ad_placements', $_POST['_ad_placements'] );
		} else {
			delete_post_meta( $post_id, '_ad_placements' );
		}
		
		/**
		 * the next line forces the product to be virtual.
		 * Simple products have an option for this, but we don’t need a choice here, since ads are always virtual
		 * at the time we added this, we didn’t find a programmatic way, e.g., when registering the ad type to accomplish this
		 * ads that were created before this change have to be resaved
		 */
		update_post_meta( $post_id, '_virtual', true );
		
		// override product price with first price
		$prices = Advanced_Ads_Selling_Plugin::get_prices( $post_id );
		$first_price = current( $prices );
		update_post_meta( $post_id, '_price', $first_price['price'] );
	}
}