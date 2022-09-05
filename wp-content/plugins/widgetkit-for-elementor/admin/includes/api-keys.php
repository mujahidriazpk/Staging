<?php 
    class WKFE_Dashboard_API_Keys{
        private static $instance; 

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->wkfe_dashboard_api_keys_content();
        }
        public function wkfe_dashboard_api_keys_content(){
            ?>
            <div class="wk-padding-remove">

                <h2>
                    <?php 
                        esc_html_e("MailChimp", "widgetkit-for-elementor");
                    ?> 
                </h2>

                <div class="wk-padding-small wk-background-muted wk-mailchimp-card">
                    <div class="wk-card">

                        <div class="wk-card-body">
                            <div class="response wk-margin-bottom" ></div>
                            
                            <div class="mailchimp-api-key-wrapper">
                                <input 
                                class="wk-input" 
                                placeholder="<?php echo esc_attr('API key'); ?>" 
                                type="text" 
                                name="mailchimp-api-key" 
                                id="mailchimp-api-key" 
                                value="<?php echo get_option('wkfe_mailchimp_api_key') ?: ''; ?>"
                                >

                                <input 
                                class="wk-input" 
                                placeholder="<?php echo esc_attr('List ID'); ?>" 
                                type="text" 
                                name="mailchimp-list-id" 
                                id="mailchimp-list-id" 
                                value="<?php echo get_option('wkfe_mailchimp_list_id') ?: ''; ?>"
                                >

                                <?php echo wp_nonce_field('wkfe_security_nonce'); ?>
                                
                                <div class="action-button-wrapper">
                                    <?php if( get_option('wkfe_mailchimp_api_key') || get_option('wkfe_mailchimp_list_id') ): ?>  
                                        <button class="deactivate-mailchimp wk-button wk-button-primary">Update</button>
                                    <?php else: ?>  
                                        <button class="activate-mailchimp wk-button wk-button-primary">Activate</button>
                                    <?php endif;?>  
                                </div>
                            </div>


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