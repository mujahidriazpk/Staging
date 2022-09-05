<?php 

class WKFE_PRO_Init{
    private static $instance = null;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        add_filter('plugin_action_links', array($this, 'widgetkit_pro_link'),10, 2);
    }
    public function widgetkit_pro_link($links, $file) {
        static $this_plugin;
     
        if (!$this_plugin) {
            $this_plugin = plugin_basename(WK_FILE);
        }
        
        $settings_links = sprintf( '<a href="admin.php?page=widgetkit-settings#widgetkit-elements">' . __( 'Settings', 'widgetkit-for-elementor' ) . '</a>' );

        if(! class_exists( 'WidgetKit_Pro' ) ) {
            // check to make sure we are on the correct plugin
            if ($file == $this_plugin) {

                $plugin_links['WidgetKit_Pro'] = sprintf( '<a href="https://themesgrove.com/widgetkit-for-elementor/" target="_blank" style="color:#39a700eb; font-weight: bold;">' . __( 'Get Pro', 'widgetkit-for-elementor' ) . '</a>' );
        
                foreach($plugin_links as $link) {
                    array_unshift($links,$settings_links, $link);
                }
                
            }
        }
     
        return $links;
    }

}


?>
