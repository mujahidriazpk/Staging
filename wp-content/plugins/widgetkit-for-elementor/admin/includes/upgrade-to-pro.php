<?php 
    class WKFE_Dashboard_Upgrade_to_PRO{
        private static $instance; 

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->wkfe_dashboard_upgrade_to_pro_content();
        }
        public function wkfe_dashboard_upgrade_to_pro_content(){
            ?>
            <div class="wk-card wk-card-default wk-grid-collapse wk-child-width-1-2@s wk-margin" wk-grid>
                <div class="wk-card-media-left wk-cover-container">
                    <img src="https://themesgrove.com/wp-content/uploads/2018/12/wigetkit-banner-bg.png" alt="" wk-cover>
                    <canvas width="100" height="120"></canvas>
                </div>
                <div>
                    <div class="wk-card-body">
                        <h3 class="wk-card-title">Upgrade to WidgetKit Pro!</h3>
                        <p>Seems to be convinced, You need more to empower your Elementor capabilities.</p>
                    </div>
                </div>
            </div>
            <h3 class="wk-text-center wk-h2"><?php echo esc_html__('Awesome Post Widgets','widgetkit-for-elementor');?></h3>
            <div class="wk-child-width-1-3@m wk-grid-match" wk-grid>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/post-grid-slider.jpg', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Ajax based grid slider</h4>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/post-tabs.jpg', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Posts tab with ajax</h4>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/post-smart-list.jpg', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Smart list widget</h4>
                    </div>
                </div>
            </div>
            <h3 class="wk-text-center wk-h2"><?php echo esc_html__('Premium WooCommerce Widgets','widgetkit-for-elementor');?></h3>
            <div class="wk-child-width-1-3@m wk-grid-match" wk-grid>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/woo-smart-products.jpg', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Woo smart products</h4>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/woo-smart-cat.jpg', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Woo smart categories</h4>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/woo-ajax-cart.jpg', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Ajax add to cart</h4>
                    </div>
                </div>
            </div>
            <h3 class="wk-text-center wk-h2"><?php echo esc_html__('LearnDash Widgets','widgetkit-for-elementor');?></h3>
            <div class="wk-child-width-1-3@m wk-grid-match" wk-grid>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/ld1.png', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Course List Style</h4>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/ld2.png', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Course Tab Style</h4>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-text-center">
                        <img width="100%" src="<?php echo plugins_url('../assets/images/premium/ld3.png', __FILE__)?>" alt="">
                        <h4 class="wk-margin-small-top wk-text-light">Course Carousel Style</h4>
                    </div>
                </div>
                <div class="wk-width-1-1 wk-text-center">
                    <div><a href="https://themesgrove.com/widgetkit-for-elementor/?utm_campaign=widgetkit-pro&utm_medium=wp-admin&utm_source=pro-feature-button" target="_blank" class="wk-button wk-button-primary">And Many More <span wk-icon="icon: arrow-right"></span></a></div>
                </div>
            </div>
            <?php 
        }
    }
?>