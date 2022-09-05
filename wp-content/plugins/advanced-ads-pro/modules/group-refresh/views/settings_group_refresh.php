<fieldset <?php if ( ! $cb_module_enabled ) { echo 'style="display:none;"'; } ?>>
    <label><input type="checkbox" name="advads-groups[<?php echo $group->id; ?>][options][refresh][enabled]" value="1" <?php checked( $enabled, 1 ); ?>><?php _e( 'Enabled', 'advanced-ads-pro' ); ?></label>
    <br>
    <label><input type="number" min="1" required="required" name="advads-groups[<?php echo $group->id; ?>][options][refresh][interval]" value="<?php echo $interval; ?>"> <?php _e( 'milliseconds', 'advanced-ads-pro' ); ?></label>
</fieldset>
<p class="description"><?php _e( 'Refresh ads on the same spot. Works when cache-busting is used.', 'advanced-ads-pro' ); ?></p>

<?php if ( $show_warning ): ?>
<p class="advads-notice-inline advads-error"><?php _e( 'Please use a placement to deliver this group using cache-busting.', 'advanced-ads-pro' ); ?></p>
<?php endif; ?>
