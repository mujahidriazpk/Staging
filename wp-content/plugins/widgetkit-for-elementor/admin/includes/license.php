<?php 
    class WKFE_Dashboard_License{
        private static $instance; 

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->wkfe_dashboard_license_content();
        }
        public function wkfe_dashboard_license_content(){
            ?>
            <div class="wk-padding-remove">

                <h2>
                    <?php 
                        get_option("wk_pro_license_key") 
                            ? esc_html_e("Deactivate", "widgetkit-for-elementor") 
                            : esc_html_e("Activate", "widgetkit-for-elementor"); 
                        esc_html_e(" Your License", "widgetkit-for-elementor");
                    ?> 
                </h2>

                <div class="wk-padding-small wk-background-muted wk-license-card">
                    <div class="wk-card">
                        <?php if(! get_option("wk_pro_license_key")): ?>
                        <div class="wk-card-header"><?php echo esc_html__('Enter your license key here, to activate Widgetkit Pro, and get feature updates, premium support and unlimited access to the template library.', 'widgetkit-for-elementor');?></div>
                        <?php endif; ?>

                        <div class="wk-card-body">
                            <?php if(! get_option("wk_pro_license_key")): ?>
                            <ol>
                                <li> <?php echo esc_html__('Log in to your account to get your license key.', 'widgetkit-for-elementor') ?> </li>
                                <li> <?php echo esc_html__('If you don\'t yet have a license key, get Widgetkit Pro now.', 'widgetkit-for-elementor') ?> </li>
                                <li> <?php echo esc_html__('Copy the license key from your account and paste it below.', 'widgetkit-for-elementor') ?> </li>
                            </ol>
                            <?php endif; ?>
                            <div class="license-checker-wrapper">
                                <?php //wp_nonce_field( 'wk-pro-license' ); ?>
                                <input class="wk-input" type="text" name="license-input" id="license-input" value="<?php echo get_option('wk_pro_license_key') ?: ''; ?>">
                                <?php if( get_option('wk_pro_license_key') ): ?>  
                                    <button class="deactivate-license wk-button wk-button-primary">Deactivate</button>
                                <?php else: ?>  
                                    <button class="activate-license wk-button wk-button-primary">Activate</button>
                                <?php endif;?>  
                            </div>
                            <?php if(! get_option("wk_pro_license_key")): ?>
                            <div class="demo-text">
                                <?php echo esc_html__('Your license key should look something like this: ', 'widgetkit-for-elementor');?>
                                <code>fb351f05958872E193feb37a505a84be</code>
                            </div>
                            <?php endif; ?>

                            <div class="response"></div>
                        </div>
                    </div>
                    <div class="wk-card">
                        <div class="wk-card-body">

                        </div>
                    </div>
                </div>
                </div>
            <?php 
        }
    }
?>