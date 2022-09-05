<div id="advanced-ads-selling-setup-head">
	<div id="advanced-ads-selling-order-details">
		<h3><?php printf(
				// translators: %1$d is an order ID, %2$s a string with the number of items purchased.
			esc_html__( 'Setup page for order #%1$d, %2$s', 'advanced-ads-selling' ),
			absint( $order_id ),
			sprintf(
					// translators: %d is the number of items purchased.
				esc_html__( '%d item(s)', 'advanced-ads-selling' ),
				count( $items )
			)
		); ?></h3>
		<div class="address">
		<?php
		if ( $order->get_formatted_billing_address() ) {
			echo '<p><strong>' . esc_html__( 'purchased by', 'advanced-ads-selling' ) . ':</strong><br/>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
		}
		?>
		</div>
	</div>
</div>
<div id="advanced-ads-selling-wrapper">
	<?php
	// iterate through all ads.
	$item_count = 1;
	foreach ( $items as $_item_key => $_item ) :
		// check if this is an ad product or not.
		if ( ! isset( $_item['product_id'] ) ) {
			continue;
		}
		$product = wc_get_product( $_item['product_id'] ); // Get product_details.
		if ( 'advanced_ad' !== $product->get_type() ) {
			continue;
		}


		?>
		<div class="advanced-ads-selling-setup-ad-details">
		<h3>
		<?php
		printf(
			// translators: %d is an item ID.
			esc_html__( 'Item #%d', 'advanced-ads-selling' ),
			absint( $item_count ++ )
		);
		?>
			</h3>
		<label><?php esc_html_e( 'Pricing option', 'advanced-ads-selling' ); ?></label>
		<span>
		<?php
		$ad_pricing_label = isset( $_item['ad_pricing_label'] ) ? $_item['ad_pricing_label'] : '';
		echo esc_html( $ad_pricing_label );
		?>
		</span>
		<?php if ( isset( $_item['ad_placement'] ) ) : ?>
		<br/>
			<label><?php esc_html_e( 'Placement', 'advanced-ads-selling' ); ?></label>
		<span><?php echo esc_html( $_item['ad_placement'] ); ?></span>
	<?php endif; ?>
		<br/>
			<label><?php esc_html_e( 'Status', 'advanced-ads-selling' ); ?></label>
		<?php
		$ad_status = get_post_status( Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $_item_key ) );
		if ( 'publish' === $ad_status ) :
			?>
			<p style="color:green;"><?php esc_html_e( 'The content of this ad was already accepted and can no longer be changed.', 'advanced-ads-selling' ); ?></p>
		<?php elseif ( 'pending' === $ad_status ) : ?>
			<p style="color:orange;"><?php esc_html_e( 'This ad is currently in review.', 'advanced-ads-selling' ); ?></p>
		<?php else : ?>
			<p style="color:red;"><?php esc_html_e( 'Please complete the ad details so that we can process your order.', 'advanced-ads-selling' ); ?></p>
			<form enctype="multipart/form-data" method="POST" style="clear: both;">
				<?php
				if ( ! empty( $_POST['errors'] ) ) :
					?>
					<p class="advanced-ads-selling-error" style="color: red;"><?php echo esc_html( $_POST['errors'] ); ?></p>
					<?php
				endif;
				?>
				<input type="hidden" value="advanced-ads-selling-upload-ad" name="advanced-ads-selling-upload-ad">
				<?php $ad_types = isset( $_item['ad_types'] ) ? explode( ', ', $_item['ad_types'] ) : array( 'plain' ); ?>
				<label><?php esc_html_e( 'Ad Type', 'advanced-ads-selling' ); ?></label>
								 <?php
									if ( 1 === count( $ad_types ) ) :
										?>
										<div>
											<label>
												<input type="radio" class="advanced-ads-selling-setup-ad-type" name="advads_selling_ad_type" value="<?php echo esc_attr( trim( $ad_types[0] ) ); ?>" checked="checked" />
												<?php esc_html( $ad_types[0] ); ?>
											</label>
										</div>
										<?php
									elseif ( count( $ad_types ) ) :
										?>
										<div>
										<?php
										foreach ( $ad_types as $_key => $_type ) {
											?>
						<label><input type="radio"
										class="advanced-ads-selling-setup-ad-type" <?php checked( $_key, 0 ); ?>
										name="advads_selling_ad_type"
										value="<?php echo esc_attr( trim( $_type ) ); ?>"/><?php echo esc_html( $_type ); ?></label>
											<?php
										}
										echo '</div>';
									endif;
									?>
				<label class="advanced-ads-selling-setup-ad-details-html-label advanced-ads-selling-setup-ad-details-content"><?php esc_html_e( 'Ad Code', 'advanced-ads-selling' ); ?></label>
				<?php
				if ( in_array( 'plain', $ad_types ) ) {
					?>
					<div class="advanced-ads-selling-setup-ad-details-html advanced-ads-selling-setup-ad-details-content">
						<p><?php esc_html_e( 'Please enter the ad code. HTML, JavaScript, CSS and plain text are allowed.', 'advanced-ads-selling' ); ?></p>
						<textarea name="advads_selling_ad_content"></textarea>
					</div>
					<?php
				}
				if ( in_array( 'image', $ad_types ) ) {
					?>
					<label class="advanced-ads-selling-setup-ad-details-upload-label advanced-ads-selling-setup-ad-details-content"
						   for="advanced-ads-selling-setup-ad-details-upload-input-<?php echo $_item_key; ?>"><?php _e( 'Image Upload', 'advanced-ads-selling' ); ?></label>
					<div class="advanced-ads-selling-setup-ad-details-image-upload advanced-ads-selling-setup-ad-details-content">
						<input id="advanced-ads-selling-setup-ad-details-upload-input-<?php echo esc_attr( $_item_key ); ?>" class="advanced-ads-selling-setup-ad-details-upload-input" type="file"
							   name="advads_selling_ad_image"/>
						<span class="advanced-ads-selling-file-upload-instruction"><?php printf(
								esc_html__( 'Max File Size : %s MB', 'advanced-ads-selling' ),
								number_format_i18n( apply_filters( 'advanced-ads-selling-upload-file-size', 1048576 ) / 1000000, 1 )
							); ?></span>
					</div>
					<label class="advanced-ads-selling-setup-ad-details-url advanced-ads-selling-setup-ad-details-content"
						   for="advanced-ads-selling-setup-ad-details-url-input-<?php echo esc_attr( $_item_key ); ?>"><?php esc_html_e( 'Target URL', 'advanced-ads-selling' ); ?></label>
					<input id="advanced-ads-selling-setup-ad-details-url-input-<?php echo esc_attr( $_item_key ); ?>"
						   class="advanced-ads-selling-setup-ad-details-url-input advanced-ads-selling-setup-ad-details-content" type="url"
						   name="advads_selling_ad_url"/>
					<?php
				}
				do_action( 'advanced-ads-selling-ad-setup-form-types-after', $ad_types, $_item );
				?>
				<?php wp_nonce_field( 'advanced-ads-ad-setup-order-item-' . $_item_key, 'advads_selling_nonce' ); ?>
				<input type="hidden" name="advads_selling_order_item" value="<?php echo esc_attr( $_item_key ); ?>"/>
				<input type="submit" class="advanced-ads-selling-setup-ad-details-submit button button-primary"
					   value="<?php esc_attr_e( 'submit this ad', 'advanced-ads-selling' ); ?>"/>
			</form>
			<p class="advanced-ads-selling-setup-submit-error message"
			   style="color: red; display: none;"><?php esc_html_e( 'The ad could not be submitted. Please try later or contact the site admin.', 'advanced-ads-selling' ); ?></p>
			<p class="advanced-ads-selling-setup-submit-success message"
			   style="color: green; display: none;"><?php esc_html_e( 'The ad was successfully submitted for review.', 'advanced-ads-selling' ); ?></p>
		<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
