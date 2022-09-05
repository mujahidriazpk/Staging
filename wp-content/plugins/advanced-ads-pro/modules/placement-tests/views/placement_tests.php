<?php 
if ( count( $placement_tests ) ) : ?>
	<h2><?php _e( 'Placement tests', 'advanced-ads-pro' ); ?></h2>
	<form method="POST" action="">
		<table class="widefat advads-placement-tests-table striped">
			<thead>
				<tr>
					<th><?php _e( 'Author', 'advanced-ads-pro' ); ?></th>
					<th><?php _e( 'Expiry date', 'advanced-ads-pro' ); ?></th>
					<th><?php _e( 'Placements', 'advanced-ads-pro' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			foreach ( $placement_tests as $slug => $placement_test ) :
				$placement_names = $this->get_placement_names( $placement_test );
				$is_empty_test = count( $placement_names ) < 2;
				$user_login = ( isset( $placement_test['user_id'] ) && ( $user = get_user_by( 'ID', $placement_test['user_id'] ) ) ) ? $user->user_login : '';
				?>
			<tr>
				<td>
					<?php echo $user_login; ?>
				</td>
				<td>
					<?php
					if ( ! $is_empty_test ) {
						$expiry_date = isset( $placement_test['expiry_date'] ) ? $placement_test['expiry_date'] : false;
						$this->output_expiry_date_form( $slug, $expiry_date );
					}
					?>
				</td>			
				<td>
					<?php
					if ( ! $is_empty_test ) {
						echo implode( _x( ' vs ', 'placement tests', 'advanced-ads-pro' ), $placement_names );
					} else { ?>
						<span class="advads-notice-inline advads-error"><?php _ex( 'empty', 'placement tests', 'advanced-ads-pro' ); ?> </span>
						<?php
					}
					?>
				</td>
				<td>
					<label><input type="checkbox" name="advads[placement_tests][<?php echo $slug; ?>][delete]" value="1" /> <?php _ex( 'delete', 'checkbox to remove placement test', 'advanced-ads-pro' ); ?></label>
				</td>
			<tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php wp_nonce_field( 'advads-placement-test', 'advads_placement_test', true ) ?>
		<input type="submit" class="button button-primary" value="<?php _e( 'Save Tests', 'advanced-ads-pro' ); ?>"/>
	</form>

	<?php if ( $adsense_limit ) : ?>
	<p class="advads-notice-inline advads-error"><?php printf(__( 'Due to technical restrictions, when testing AdSense units, the limit in the plugin settings does not work. Please disable it <a href="%s">here</a>.', 'advanced-ads-pro' ), admin_url('admin.php?page=advanced-ads-settings#top#adsense') ); ?></p>
	<?php endif; ?>
<?php endif; ?>



<form id="advads-placements-new-test-form" method="POST" action="" style="display: none;" tabindex="-1">
	<?php wp_nonce_field( 'advads-placement-test', 'advads_placement_test', true ) ?>
</form>


<script>
jQuery( document ).ready( function( $ ) {
	function maybe_allow_create_test() {
		var $inputs = jQuery( '.advads-add-to-placement-test' ).filter( function() {
			return this.value != '';
		}); 

		if ( $inputs.length > 1 ) {
			jQuery( '#advads-save-placement-test-button' ).addClass( 'button-primary' ).prop( 'disabled', false );
			jQuery( '#advads-save-placements-button' ).removeClass( 'button-primary' );
		} else {
			jQuery( '#advads-save-placement-test-button' ).removeClass( 'button-primary' ).prop( 'disabled', true )
			jQuery( '#advads-save-placements-button' ).addClass( 'button-primary' );
		}
	}

	jQuery( '.advads-add-to-placement-test' ).on( 'change', function() {
		maybe_allow_create_test();
	} );

	maybe_allow_create_test(); // handle 'Tab Discarding' in Chrome

	jQuery( '#advads-save-placement-test-button' ).on( 'click', function() {
		var $inputs = jQuery( '.advads-add-to-placement-test' ).filter( function() {
			return this.value != '';
		});

		var new_inputs = '';
		$inputs.each(function( index ) {
			new_inputs += '<input type="hidden" name="advads[placement_test][' + jQuery( this).data( 'slug' ) + ']" value="' + jQuery( this ).val() + '" />';
		});
		jQuery( '#advads-placements-new-test-form' ).append( new_inputs ).trigger( 'submit' );

	});
});

</script>
