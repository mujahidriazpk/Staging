<?php 
    class WKFE_Dashboard_Overview{
        private static $instance; 

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->wkfe_dashboard_overview_content();
        }
        public function wkfe_dashboard_overview_content(){
            ?>
            <div class="wk-grid wk-child-width-1-3 wk-grid-match" wk-grid>
                <div>
                    <div class="wk-card wk-card-default wk-card-body">
                        <h3 class="wk-card-title wk-margin-remove-top">Documentation</h3>
                        <p>It’s highly recommended to check out documentation and FAQ before using this plugin. <a class="wk-alert-primary" target="_blank" href="https://themesgrove.com/support/"><code class="wk-alert-primary">Click Here</code></a> for more details.</p>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-secondary wk-card-body">
                        <h3 class="wk-card-title wk-margin-remove-top"><?php echo  __( 'Need Any Help?');?></h3>
                        <p>If you need help just shoot us an email <code>help@themesgrove.com</code>.</p>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-body">
                        <h3 class="wk-card-title wk-margin-remove-top"><?php echo  __( 'Social Community');?></h3>
                        <p>Feel free to join us in our <a target="_blank" href="https://www.facebook.com/groups/widgetkitcommunity/"><code class="wk-alert-primary">Official Facebook Group</code></a> for discussion, support and chill.
                    </div>
                </div>
                <div class="wk-width-1-1">
                    <div class="wk-card wk-card-primary wk-card-body">
                        <h3 class="wk-card-title wk-margin-remove-top"><?php echo  __( 'Show your Love?');?></h3>
                        <p>We love to have you in Themesgrove family. We are making WidgetKit more awesome everyday. Take your 2 minutes to review the plugin and spread the love to encourage us to keep it going.</p>
                        <a href="https://wordpress.org/support/plugin/widgetkit-for-elementor/reviews/" target="_blank" class="wk-button wk-button-default"><span class="wk-margin-small-right" wk-icon="icon: heart"></span> Leave a Review</a>
                    </div>
                </div>
            </div>
            <?php 
        }
    }
?>