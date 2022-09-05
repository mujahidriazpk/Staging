<?php
/**
 * Template to show the ad.
 *
 * @var string $placement_content content of the placement.
 * @var string $public_slug public slug or placement ID send as $_GET['p'] to allow placement-specific usage of the hooks.
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex,nofollow">
		<?php do_action( 'advanced-ads-pro-ad-server-template-head', $public_slug ); ?>
	</head>
	<body style="margin: 0;" >
	<?php do_action( 'advanced-ads-pro-ad-server-template-after-opening-body', $public_slug ); ?>
	<?php
        // phpcs:ignore
		echo $placement_content;
	?>
	<?php do_action( 'advanced-ads-pro-ad-server-template-before-closing-body', $public_slug ); ?>
	</body>
</html>
