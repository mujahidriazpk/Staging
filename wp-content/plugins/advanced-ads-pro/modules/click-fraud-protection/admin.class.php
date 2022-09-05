<?php
class Advanced_Ads_Pro_Module_CFP_Admin
{
	
    public function __construct() {
        add_action( 'advanced-ads-settings-init', array( $this, 'settings_init'), 10, 1 );
    }

    /**
     * settings init (Advanced Ads settings page) 
     */
    public function settings_init($hook) {
       $admin = Advanced_Ads_Admin::get_instance();
       $hook = $admin->plugin_screen_hook_suffix;

        // add new section
        add_settings_field(
            'module-cfp',
            __( 'Click Fraud Protection', 'advanced-ads-pro' ),
            array( $this, 'render_settings' ),
            Advanced_Ads_Pro::OPTION_KEY . '-settings',
            Advanced_Ads_Pro::OPTION_KEY . '_modules-enable'
        );
    }

    /**
     * settings callback 
     */
    public function render_settings() {
        include dirname( __FILE__ ) . '/views/settings.php';
    }

}