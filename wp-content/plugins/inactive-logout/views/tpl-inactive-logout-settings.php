<?php
/**
 * Template for Settings page.
 *
 * @package inactive-logout
 */
?>

<h1><?php esc_html_e( 'Inactive User Logout Settings', 'inactive-logout' ); ?></h1>

<?php if ( ! \Codemanas\InactiveLogout\Helpers::get_option( 'ina_dismiss_like_notice' ) ) { ?>
    <div id="message" class="notice notice-warning ina-logout-like-dismiss-wrapper">
        <p>
			<?php
			// translators: anchor tag.
			printf( esc_html__( 'Please consider giving a %s if you found this useful at wordpress.org.', 'inactive-logout' ), '<a href="https://wordpress.org/support/plugin/inactive-logout/reviews/#new-post">5 star thumbs up</a>' );
			?>
            <a href="javascript:void(0);" id="ina-logout-like-dismiss">Already Rated. Don't show this message again.</a>
        </p>
    </div>
<?php } ?>

<div class="message">
	<?php
	$message = self::get_message();
	if ( isset( $message ) && ! empty( $message ) ) {
		echo $message;
	}
	?>
</div>

<h2 class="nav-tab-wrapper">
    <a href="?page=inactive-logout&tab=ina-basic" class="nav-tab <?php echo ( 'ina-basic' === $active_tab ) ? esc_attr( 'nav-tab-active' ) : ''; ?>">
		<?php esc_html_e( 'Basic Management', 'inactive-logout' ); ?>
    </a>
    <a href="?page=inactive-logout&tab=ina-advanced" class="nav-tab <?php echo ( 'ina-advanced' === $active_tab ) ? esc_attr( 'nav-tab-active' ) : ''; ?>">
		<?php esc_html_e( 'Role Based Timeout', 'inactive-logout' ); ?>
    </a>
	<?php do_action( 'ina_settings_page_tabs_before' ); ?>
    <a href="?page=inactive-logout&tab=ina-support" class="nav-tab <?php echo ( 'ina-support' === $active_tab ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Support', 'inactive-logout' ); ?></a>
	<?php if ( !\Codemanas\InactiveLogout\Helpers::is_pro_version_active() ) { ?>
        <a href="http://inactive-logout.com/" target="_blank" class="nav-tab"><?php esc_html_e( 'Go Pro', 'inactive-logout' ); ?> <span class="dashicons dashicons-star-filled"></span></a>
	<?php } ?>
	<?php do_action( 'ina_settings_page_tabs_after' ); ?>
</h2>