<?php

if ( ! class_exists( 'GamiPress' ) ) {
	return;
}

// the callbacks for the visitor conditions are registered in admin.
new Advanced_Ads_Pro_Module_GamiPress_Admin();
