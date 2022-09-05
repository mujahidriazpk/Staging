<?php
$options                 = Advanced_Ads_Pro::get_instance()->get_options();
$module_enabled          = ! empty( $options['cache-busting']['enabled'] );
$method                  = ( isset( $options['cache-busting']['default_auto_method'] ) && $options['cache-busting']['default_auto_method'] === 'ajax' ) ? 'ajax' : 'passive';
$fallback_method         = ( isset( $options['cache-busting']['default_fallback_method'] ) && $options['cache-busting']['default_fallback_method'] === 'disable' ) ? 'disable' : 'ajax';
$passive_all             = ! empty( $options['cache-busting']['passive_all'] );
$vc_cache_reset          = ! empty( $options['cache-busting']['vc_cache_reset'] ) ? (int) $options['cache-busting']['vc_cache_reset'] : 0;
$vc_cache_reset_on_login = ! empty( $options['cache-busting']['vc_cache_reset_actions']['login'] );
?>
<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][enabled]" id="advanced-ads-pro-cache-busting-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> class="advads-has-sub-settings" />
<label for="advanced-ads-pro-cache-busting-enabled" class="description"><?php esc_html_e( 'Activate module.', 'advanced-ads-pro' ); ?></label>

<div class="advads-sub-settings">
    <h4><?php esc_html_e( 'Default option', 'advanced-ads-pro' ); ?></h4>
	<p class="description"><?php esc_html_e( 'Choose which method to use when cache-busting for a placement is set to “auto”.', 'advanced-ads-pro' ); ?></p>
	<label>
		<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][default_auto_method]" type="radio" value="passive"
								<?php
								checked( $method, 'passive' );
								?>
		/><?php esc_html_e( 'passive', 'advanced-ads-pro' ); ?>
	</label>
	<label>
		<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][default_auto_method]" type="radio" value="ajax"
								<?php
								checked( $method, 'ajax' );
								?>
		/><?php esc_html_e( 'AJAX', 'advanced-ads-pro' ); ?>
	</label>
    <p><label>
        <input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][passive_all]" type="checkbox" value="1"
			<?php
			checked( $passive_all, 1 );
			?>
		/><?php esc_html_e( 'Force passive cache-busting', 'advanced-ads-pro' ); ?>
    </label></p>
	<p class="description">
		<?php
		esc_html_e( 'By default, cache-busting only works through placements.', 'advanced-ads-pro' );
		echo '&nbsp;';
		esc_html_e( 'Enable passive cache-busting for all ads and groups which are not delivered through a placement, if possible.', 'advanced-ads-pro' );
		?>
	</p>

    <h4><?php esc_html_e( 'Fallback option', 'advanced-ads-pro' ); ?></h4>
	<p class="description"><?php esc_html_e( 'Choose the fallback if “passive“ cache-busting is not possible.', 'advanced-ads-pro' ); ?></p>
	<label>
		<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][default_fallback_method]" type="radio" value="ajax"
								<?php
								checked( $fallback_method, 'ajax' );
								?>
		/><?php esc_html_e( 'Use AJAX', 'advanced-ads-pro' ); ?>
	</label>
	<label>
		<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][default_fallback_method]" type="radio" value="disable"
								<?php
								checked( $fallback_method, 'disable' );
								?>
		/><?php esc_html_e( 'No cache-busting', 'advanced-ads-pro' ); ?>
	</label>

	<input id="advads-pro-vc-hash" name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][vc_cache_reset]" type="hidden" value="
													<?php
													echo esc_attr( $vc_cache_reset );
													?>
		" />
	<h4><?php esc_html_e( 'Visitor profile', 'advanced-ads-pro' ); ?></h4>
	<p class="description">
	<?php
	esc_html_e( 'Advanced Ads stores some user information in the user’s browser to limit the number of AJAX requests for cache-busting.', 'advanced-ads-pro' );
	?>
		&nbsp;
		<?php
		printf(
			wp_kses(
				// translators: $1%s is an opening a tag; $2%s is the closing one
				__( 'Learn more about this %1$shere%2$s.', 'advanced-ads-pro' ),
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'<a href="' . esc_url( ADVADS_URL ) . 'manual/cache-busting/?utm_source=advanced-ads&utm_medium=link&utm_campaign=visitor-profile#Visitor_profile" target="_blank">',
			'</a>'
		);
		?>
	</p>
	<br/><button type="button" id="advads-pro-vc-hash-change" class="button-secondary"><?php esc_html_e( 'Update visitor profile', 'advanced-ads-pro' ); ?></button>
	<p id="advads-pro-vc-hash-change-ok" class="advads-success-message" style="display:none;"><?php esc_html_e( 'Updated', 'advanced-ads-pro' ); ?>
		<span class="description"><?php esc_html_e( 'You might need to update the page cache if you are using one.', 'advanced-ads-pro' ); ?></span>
	</p>
	<p id="advads-pro-vc-hash-change-error" class="advads-notice-inline advads-error" style="display:none;"><?php esc_html_e( 'An error occured', 'advanced-ads-pro' ); ?></p>
		<input type="hidden" id="advads-pro-reset-vc-cache-nonce" value="<?php echo esc_attr( wp_create_nonce( 'advads-pro-reset-vc-cache-nonce' ) ); ?>" />
	<p><label>
		<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[cache-busting][vc_cache_reset_actions][login]" type="checkbox" value="1"
								<?php
								checked( $vc_cache_reset_on_login, 1 );
								?>
	 />
		<?php esc_html_e( 'Update visitor profile when user logs in or out', 'advanced-ads-pro' ); ?>
		</label></p>
</div>
