<?php 
    class WKFE_Dashboard_Sidebar{
        private static $instance; 

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->wkfe_dashboard_sidebar_content();
        }
        public function wkfe_dashboard_sidebar_content(){
            ?>
            <div wk-sticky="offset: 40">
                <div class="wk-card wk-card-default wk-card-body  wk-background-small wk-text-center">
                    <img class="wk-margin-small-top" src="<?php echo plugins_url('../assets/images/widgetkit-pro.svg', __FILE__)?>" width="150" wk-svg>
                    <p class="wk-text-muted">Get the pro version of <strong>WidgetKit</strong> for more stunning elements and customization options.</p>
                    <a href="https://themesgrove.com/widgetkit-for-elementor/?utm_campaign=widgetkit-pro&utm_medium=wp-admin&utm_source=pro-feature-button" target="_blank" class="wk-button wk-button-primary wk-padding-remove-vertical wk-padding-small"><span class="wk-icon wk-margin-small-right" wk-icon="unlock"></span>Upgrade to Pro</a>
                </div>
            </div>
            <?php 
        }
    }
?>