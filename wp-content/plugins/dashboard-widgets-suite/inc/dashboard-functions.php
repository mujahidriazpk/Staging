<?php // Dashboard Widgets Suite - Dashboard Functions

if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_dashboard_columns() {
	
	global $dws_options_general;
	
	$cols = isset($dws_options_general['dashboard_columns']) ? intval($dws_options_general['dashboard_columns']) : 2;
	
	return empty($cols) ? false : $cols;
	
}

function dashboard_widgets_suite_dashboard_columns_max($columns) {
	
	$cols = dashboard_widgets_suite_dashboard_columns();
	
	if (!empty($cols)) $columns['dashboard'] = $cols;
	
	return $columns;
	
}

function dashboard_widgets_suite_dashboard_columns_style() { 
	
	$cols = dashboard_widgets_suite_dashboard_columns(); 
	
	if (!empty($cols)) : ?>
	
	<style>
		.columns-prefs { display: none !important; }
		@media only screen and (max-width: 799px) { .postbox-container { width: 100% !important; min-width: 100% !important; } }
	</style>
	
	<?php endif; 
	
	if ($cols === 1) : ?>
	
	<style>
		.postbox-container { width: 100% !important; min-width: 100% !important; }
	</style>
	
	<?php elseif ($cols === 2) : ?>
	
	<style>
		@media only screen and (max-width: 9999px) and (min-width: 800px) { .postbox-container { width: 50% !important; } }
	</style>
	
	<?php elseif ($cols === 3) : ?>
	
	<style>
		@media only screen and (max-width: 9999px) and (min-width: 1000px) { .postbox-container { width: 33.3% !important; } }
		@media only screen and (max-width: 999px)  and (min-width: 800px)  { .postbox-container { width: 50% !important; } }
	</style>
	
	<?php elseif ($cols === 4) : ?>
	
	<style>
		@media only screen and (max-width: 9999px) and (min-width: 1300px) { .postbox-container { width: 25% !important; } }
		@media only screen and (max-width: 1299px) and (min-width: 1000px) { .postbox-container { width: 33.3% !important; } }
		@media only screen and (max-width: 999px)  and (min-width: 800px)  { .postbox-container { width: 50% !important; } }
	</style>
	
	<?php endif;
	
}
