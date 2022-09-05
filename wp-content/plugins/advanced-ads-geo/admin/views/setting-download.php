<?php

if ( ! $correct_databases ) :
	?><p class="advads-error-message"><?php _e( "Geo Databases not found.", 'advanced-ads-geo' ); ?></p>
	<p><?php _e( "In order to use Geo Targeting, please download the geo location databases by clicking on the button below.", 'advanced-ads-geo' ); ?></p><?php
else:
	if( $last_update ) :
		?><p><?php printf( __( 'Last update: %s', 'advanced-ads-geo' ), date_i18n( get_option( 'date_format' ), $last_update ) ); ?></p><?php
	endif;
endif;


if ( $this->is_update_available() || ! $correct_databases ) : ?>
<button type="button" id="download_geolite" class="button-secondary"><?php _e( 'Update geo location databases', 'advanced-ads-geo' ); ?> (~66MB)</button>
<span class="advads-loader" id="advads-geo-loader" style="display: none;"></span>
<p class="advads-error-message hidden" id="advads-geo-upload-error"></p>
<p class="advads-success-message hidden" id="advads-geo-upload-success"></p>
<script>
    jQuery('#download_geolite').on('click', function (e) {

	var el = jQuery(this);
	el.blur();
	el.attr('disabled', 'disabled');
	
	var data = {
	    action: 'advads_download_geolite_database',
	    license_key: jQuery( '#advanced-ads-geo-maxmind-licence' ).val(),
	    nonce: '<?php echo wp_create_nonce( "advanced-ads-admin-ajax-nonce" ); ?>'
	};
	
	jQuery('#advads-geo-loader').show();

	jQuery.post( ajaxurl, data )
	.done(function( result ) {
		if ( ! jQuery.isPlainObject( result ) ) {
			return;
		}
		if ( result.success ) {
			jQuery('#advads-geo-upload-error').hide();
			jQuery('#advads-geo-upload-success').html( result.data ).show();
		} else {
			jQuery('#advads-geo-upload-error').html( result.data ).show();
			jQuery('#advads-geo-upload-success').hide();
			el.attr( 'disabled', false );
		}
	})
	.fail(function (jqXHR, errormessage, errorThrown) {
	    jQuery('#advads-geo-upload-error').html( errormessage ).show();
		jQuery('#advads-geo-upload-success').hide();
		el.attr( 'disabled', false );
	})
	.always( function() {
		jQuery( '#advads-geo-loader' ).hide();
	} );
	
    });
</script>
<?php else :
    ?><p><?php printf(__( 'Next possible update on %s.', 'advanced-ads-geo' ), date_i18n( get_option( 'date_format' ), $next_update )); ?></p>
    <p class="description"><?php _e( 'The databases are updated on the first Tuesday (midnight, GMT) of each month.', 'advanced-ads-geo' ); ?></p>
	<?php
endif;
