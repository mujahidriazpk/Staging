<?php 

class WKFE_Appsero_Init{
    private static $instance = null;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        $this->appsero_tracker_init();
    }
    /**
     * Initialize the appsero plugin tracker
     *
     * @return void
     */
    protected function appsero_tracker_init() {

        if ( ! class_exists( 'Appsero\Client' ) ) {
        require_once WK_PATH . '/vendor/appsero/client/src/Client.php';
        }

        $client = new Appsero\Client( '91bf222e-d4ce-4fdb-97dc-30a95fa0bff7', 'Widgetkit For Elementor', WK_FILE );

        $client->insights()->init();

    }   

}

?>