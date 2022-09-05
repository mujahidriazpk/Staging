<?php
if ( ! defined( 'WPINC' ) || ! ( $order_id ) ) {
	die;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>><head>
	<meta charset="<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>">
	<title><?php echo esc_html( bloginfo( 'name' ) ); ?> | <?php esc_html_e( 'Ad Setup', 'advanced-ads-selling' ); ?></title>
	<meta name="robots" content="noindex, nofollow" />
	<script type="text/javascript" src="<?php echo esc_url( includes_url( '/js/jquery/jquery.js' ) ); ?>"></script>
	<script type="text/javascript" src="<?php echo esc_url( AASA_BASE_URL . 'public/assets/js/ad-setup.js' ); ?>"></script>
	<script>
		AdvancedAdSelling = { 'maxFileSize': <?php echo apply_filters( 'advanced-ads-selling-upload-file-size', 1048576 ); ?> }
	</script>
	<link rel="stylesheet" href="<?php echo esc_url( AASA_BASE_URL . 'public/assets/css/ad-setup.css' ); ?>" />
	<script>
	var advads_selling_ajax_url = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
	</script>
</head>
	<body class="advanced-ads-selling-setup-page">
	<h1><?php echo esc_html( bloginfo( 'name' ) ); ?></h1>
	<?php require 'ad-setup-form.php'; ?>
	</body>
</html>
