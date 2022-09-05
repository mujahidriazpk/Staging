<?php

class Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions_Admin {

    public function __construct() {
        add_action( 'advanced-ads-settings-init', array( $this, 'settings_init'), 10, 1 );
    }

    public function settings_init($hook) {
       $admin = Advanced_Ads_Admin::get_instance();
       $hook = $admin->plugin_screen_hook_suffix;

        // add new section
        add_settings_field(
            'module-advanced-visitor-conditions',
            __('Advanced visitor conditions', 'advanced-ads-pro'),
            array($this, 'render_settings'),
            Advanced_Ads_Pro::OPTION_KEY . '-settings',
            Advanced_Ads_Pro::OPTION_KEY . '_modules-enable'
        );
    }

    public function render_settings() {
        include dirname( __FILE__ ) . '/views/settings.php';
    }
}
