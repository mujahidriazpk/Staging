<?php
/**
 * PDF invoice template body.
 *
 * This template can be overridden by copying it to youruploadsfolder/woocommerce-pdf-invoices/templates/invoice/simple/yourtemplatename/body.php.
 *
 * HOWEVER, on occasion WooCommerce PDF Invoices will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  Bas Elbers
 * @package WooCommerce_PDF_Invoices/Templates
 * @version 0.0.1
 */

$templater                      = WPI()->templater();
$order                          = $templater->order;
$invoice                        = $templater->invoice;
$line_items                     = $order->get_items( 'line_item' );
$formatted_shipping_address     = $order->get_formatted_shipping_address();
$formatted_billing_address      = $order->get_formatted_billing_address();
$columns                        = $invoice->get_columns();
$color                          = $templater->get_option( 'bewpi_color_theme' );
$terms                          = $templater->get_option( 'bewpi_terms' );
?>

<div class="title">
	<div>
		<h2><?php echo esc_html( $templater->get_option( 'bewpi_title' ) ); ?></h2>
	</div>
	<div class="watermark">
		<?php
		if ( $templater->get_option( 'bewpi_show_payment_status' ) && $order->is_paid() ) {
			printf( '<h2 class="green">%s</h2>', esc_html__( 'Paid', 'woocommerce-pdf-invoices' ) );
		}

		do_action( 'wpi_watermark_end', $order, $invoice );
		?>
	</div>
</div>
<table cellpadding="0" cellspacing="0">
	<tr class="information">
		<td width="50%">
			<?php
			/**
			 * Invoice object.
			 * 
			 * @var BEWPI_Invoice $invoice.
			 */
			foreach ( $invoice->get_invoice_info() as $id => $info ) {
				printf( '<span class="%1$s">%2$s %3$s</span>', esc_attr( $id ), esc_html( $info['title'] ), esc_html( $info['value'] ) );
				echo '<br>';
			}
			?>
		</td>

		<td>
		
			<?php
	       		printf( '<strong>%s</strong><br />', esc_html__( 'Bill to:', 'woocommerce-pdf-invoices' ) );
			echo $formatted_billing_address;
			?>
		</td>

		<td>
			<?php
			if ( $templater->get_option( 'bewpi_show_ship_to' ) && ! WPI()->has_only_virtual_products( $order ) ) {
				printf( '<strong>%s</strong><br />', esc_html__( 'Ship to:', 'woocommerce-pdf-invoices' ) );
				echo $formatted_shipping_address;
			}
			?>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr class="heading" bgcolor="<?php echo esc_attr( $color ); ?>;">
			<?php
			foreach ( $columns as $key => $data ) {
				$templater->display_header_recursive( $key, $data );
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $invoice->get_columns_data() as $index => $row ) {
		echo '<tr class="item">';

		// Display row data.
		foreach ( $row as $column_key => $data ) {
			$templater->display_data_recursive( $column_key, $data );
		}

		echo '</tr>';
	}
	?>

	<tr class="spacer">
		<td></td>
	</tr>

	</tbody>
</table>

<table cellpadding="0" cellspacing="0">
	<tbody>

	<?php
	foreach ( $invoice->get_order_item_totals() as $key => $total ) {
		$class = str_replace( '_', '-', $key );
		?>

		<tr class="total">
			<td width="50%">
				<?php do_action( 'wpi_order_item_totals_left', $key, $invoice ); ?>
            </td>

			<td width="25%" align="left" class="border <?php echo esc_attr( $class ); ?>">
				<?php echo $total['label']; ?>
			</td>

			<td width="25%" align="right" class="border <?php echo esc_attr( $class ); ?>">
				<?php echo str_replace( '&nbsp;', '', $total['value'] ); ?>
			</td>
		</tr>

	<?php } ?>
	</tbody>
</table>
<?php $_billing_full_name = $order->get_meta('_billing_full_name');
$date = $order->order_date;
?>
<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
	<tr>
		<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
			<p>I authorize ShopADoc® to contact me as needed and permit contact info be provided to auction winner for appointment scheduling. I have read and accept user agreement and privacy policy/HIPAA policy. I certify the x-ray(s) I am uploading have been taken within 30 days of the auction.  I authorize $9.99 be charged to the above card/acct. for payment of ShopADoc® auction fee.</p>
            <p>Full Legal Name: <?php echo $_billing_full_name;?></p>
            <p>Dated: <?php echo date_i18n( get_option( 'date_format' ),  strtotime($date));?></p>
		</td>
	</tr>
</table>
<table class="notes" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<?php
			// Customer notes.
			if ( $templater->get_option( 'bewpi_show_customer_notes' ) ) {
				// Note added by customer.
				$customer_note = BEWPI_WC_Order_Compatibility::get_customer_note( $order );
				if ( $customer_note ) {
					printf( '<strong>' . __( 'Note from customer: %s', 'woocommerce-pdf-invoices' ) . '</strong><br />', nl2br( $customer_note ) );
				}

				// Notes added by administrator on 'Edit Order' page.
				foreach ( $order->get_customer_order_notes() as $custom_order_note ) {
					printf( '<strong>' . __( 'Note to customer: %s', 'woocommerce-pdf-invoices' ) . '</strong><br />', nl2br( $custom_order_note->comment_content ) );
				}
			}
			?>
		</td>
	</tr>

	<tr>
		<td>
			<?php
			// Zero Rated VAT message.
			if ( 'true' === $templater->get_meta( '_vat_number_is_valid' ) && count( $order->get_tax_totals() ) === 0 ) {
				_e( 'Zero rated for VAT as customer has supplied EU VAT number', 'woocommerce-pdf-invoices' );
				printf( '<br />' );
			}
			?>
		</td>
	</tr>
</table>

<?php if ( $terms ) { ?>
	<!-- Using div to position absolute the block. -->
	<div class="terms">
		<table>
			<tr>
				<td style="border: 1px solid #000;">
					<?php echo nl2br( $terms ); ?>
				</td>
			</tr>
		</table>
	</div>
<?php }
