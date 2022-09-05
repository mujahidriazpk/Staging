<?php
$analytify_modules = get_option( 'wp_analytify_modules' );

foreach ( $analytify_modules as $module ) {
	if ( 'active' === $module['status'] ) {
		include_once ANALYTIFY_PRO_ROOT_PATH . '/inc/modules/'.$module['slug'].'/classes/'.$module['slug'].'.php';
	}
}
