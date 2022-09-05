<?php 
    class WKFE_Dashboard_Content{
        private static $instance; 
        
        private $widgetkit_get_settings;
        private $pro_integration_data;

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct($all_option_data){
            $this->widgetkit_get_settings = $all_option_data['widgetkit_get_settings'];
            $this->pro_integration_data = $all_option_data['pro_integration_data'];
            $this->wkfe_dashboard__content();
        }
        public function wkfe_dashboard__content(){
            ?>
            <div class="wk-main wk-margin wk-padding-small wk-background-default">
                <div class="wk-grid">
                    <?php if (!apply_filters('wkpro_enabled', false)) :?>
                    <div class="wk-width-3-4">
                    <?php else: ?>
                    <div class="wk-width-1-1">
                    <?php endif; ?>
                        <div class="wk-card-small">
                            
                            <ul id="wk-options" class="wk-switcher">
                            
                                <!-- overview -->
                                <li>
                                    <?php 
                                        require WK_PATH . '/admin/includes/overview.php';
                                        WKFE_Dashboard_Overview::init();
                                    ?>
                                </li>

                                <!-- Elements -->
                                <li>
                                    <?php 
                                        require WK_PATH . '/admin/includes/elements.php';
                                        new WKFE_Dashboard_Elements($this->widgetkit_get_settings);
                                    ?>
                                </li>

                                <!-- pro integration -->
                                <li class="pro-integrated-plugins-data">
                                    <?php 
                                        require WK_PATH . '/admin/includes/pro-integration.php';
                                        new WKFE_Dashboard_PRO_Integration($this->pro_integration_data);
                                    ?>
                                    
                                </li>

                                <!-- pro panel for free -->
                                <?php if (!apply_filters('wkpro_enabled', false)) :?>
                                <li>
                                    <?php 
                                        require WK_PATH . '/admin/includes/upgrade-to-pro.php';
                                        WKFE_Dashboard_Upgrade_to_PRO::init();
                                    ?>
                                </li>
                                <?php endif;?>

                                <!-- change log -->
                                <li class="wk-changelog">
                                    <?php 
                                        require WK_PATH . '/admin/includes/changelog.php';
                                        WKFE_Dashboard_Changelog::init();
                                    ?>
                                </li>

                                <!-- license -->
                                <?php if (apply_filters('wkpro_enabled', false)) :?>
                                    <li class="wk-pro-license">
                                        <?php 
                                            require WK_PATH . '/admin/includes/license.php';
                                            WKFE_Dashboard_License::init();
                                        ?>
                                    </li>
                                <?php endif; ?>

                                <!-- API Keys -->
                                <li class="wk-api-keys">
                                    <?php 
                                        require WK_PATH . '/admin/includes/api-keys.php';
                                        WKFE_Dashboard_API_Keys::init();
                                    ?>
                                </li>
                                
                            </ul>
                        </div>
                    </div>
                    
                    <?php if (!apply_filters('wkpro_enabled', false)) :?>
                    <div class="wk-width-1-4 pro-sidebar">
                        <?php 
                            require WK_PATH . '/admin/includes/sidebar.php';
                            WKFE_Dashboard_Sidebar::init();
                        ?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <?php 
        }
    }
?>