<?php
/**
 * Dokan Update class
 *
 * Performas license validation and update checking
 *
 * @package Dokan
 */
class Dokan_Auction_Update {

    const base_url     = 'https://wedevs.com/';
    const api_endpoint = 'http://api.wedevs.com/';
    private $product_id;
    const option       = 'dokan_auction_license';
    const slug         = 'dokan-auction';

    function __construct( $plan ) {

        $this->product_id = $plan;

        // bail out if it's a local server
        if ( $this->is_local_server() ) {
            return;
        }

        add_action( 'dokan_update_license_wrap', array( $this, 'plugin_update' ), 80 );

        if ( is_multisite() ) {
            if ( is_main_site() ) {
                add_action( 'admin_notices', array($this, 'license_enter_notice') );
                add_action( 'admin_notices', array($this, 'license_check_notice') );
            }
        } else {
            add_action( 'admin_notices', array($this, 'license_enter_notice') );
            add_action( 'admin_notices', array($this, 'license_check_notice') );
        }

        add_filter( 'pre_set_site_transient_update_plugins', array($this, 'check_update') );
        add_filter( 'pre_set_transient_update_plugins', array($this, 'check_update') );
        add_filter( 'plugins_api', array($this, 'check_info'), 10, 3 );
    }

    /**
     * Check if the current server is localhost
     *
     * @return boolean
     */
    private function is_local_server() {
        return in_array( dokan_get_client_ip(), array( '127.0.0.1', '::1' ) );
    }

    /**
     * Get license key
     *
     * @return array
     */
    function get_license_key() {
        return get_option( self::option, array() );
    }

    /**
     * Prompts the user to add license key if it's not already filled out
     *
     * @return void
     */
    function license_enter_notice() {
        if ( $key = $this->get_license_key() ) {
            return;
        }
        ?>
        <div class="error">
            <p><?php printf( __( 'Please <a href="%s">enter</a> your <strong>Dokan Auction</strong> plugin license key to get regular update and support.', 'dokan-auction' ), admin_url( 'admin.php?page=dokan_updates' ) ); ?></p>
        </div>
        <?php
    }

    /**
     * Check activation every 12 hours to the server
     *
     * @return void
     */
    function license_check_notice() {
        if ( !$key = $this->get_license_key() ) {
            return;
        }

        $error = __( 'Please activate your copy', 'dokan-auction' );
        $license_status = get_option( 'dokan_auction_license_status' );

        if ( $license_status && $license_status->activated ) {

            $status = get_transient( self::option );
            if ( false === $status ) {
                $status   = $this->activation();
                $duration = 60 * 60 * 12; // 12 hour

                set_transient( self::option, $status, $duration );
            }

            if ( $status && $status->success ) {

                // notice if validity expires
                if ( isset( $status->update ) ) {
                    $update = strtotime( $status->update );

                    if ( time() > $update ) {
                        echo '<div class="error">';
                        echo '<p>Your <strong>Dokan Auction</strong> License has been expired. Please <a href="https://wedevs.com/account/" target="_blank">renew your license</a>.</p>';
                        echo '</div>';
                    }
                }
                return;
            }

            // may be the request didn't completed
            if ( !isset( $status->error )) {
                return;
            }

            $error = $status->error;
        }
        ?>
        <!--<div class="error">
            <p><strong><?php _e( 'Dokan Auction Error:', 'dokan-auction' ); ?></strong> <?php echo $error; ?></p>
        </div>-->
        <?php
    }

    /**
     * Activation request to the plugin server
     *
     * @return object
     */
    function activation( $request = 'check' ) {
        global $wp_version;

        if ( ! $option = $this->get_license_key() ) {
            return;
        }

        $params = array(
            'timeout'    => ( ( defined( 'DOING_CRON' ) && DOING_CRON ) ? 30 : 3 ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body'       => array(
                'request'     => $request,
                'email'       => $option['email'],
                'licence_key' => $option['key'],
                'product_id'  => $this->product_id,
                'instance'    => home_url()
            )
        );

        $response = wp_remote_post( self::api_endpoint . 'activation', $params );
        $update   = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            if ( is_wp_error( $response ) ) {
                echo '<div class="error"><p><strong>Dokan Activation Error:</strong> ' . $response->get_error_message() . '</p></div>';
                return false;
            }

            if ( $response['response']['code'] != 200 ) {
                echo '<div class="error"><p><strong>Dokan Activation Error:</strong> ' . $response['response']['code'] .' - ' . $response['response']['message'] . '</p></div>';
                return false;
            }

            printf('<pre>%s</pre>', print_r( $response, true ) );
        }

        return json_decode( $update );
    }

    /**
     * Integrates into plugin update api check
     *
     * @param object $transient
     * @return object
     */
    function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $remote_info = $this->get_info();

        if ( !$remote_info ) {
            return $transient;
        }

        list( $plugin_name, $plugin_version) = $this->get_current_plugin_info();

        if ( version_compare( $plugin_version, $remote_info->latest, '<' ) ) {

            $obj              = new stdClass();
            $obj->slug        = self::slug;
            $obj->new_version = $remote_info->latest;
            $obj->url         = self::base_url;

            if ( isset( $remote_info->latest_url ) ) {
                $obj->package = $remote_info->latest_url;
            }

            $basefile = plugin_basename( DOKAN_AUCTION_DIR . '/dokan-auction.php' );
            $transient->response[$basefile] = $obj;
        }

        return $transient;
    }

    /**
     * Plugin changelog information popup
     *
     * @param type $false
     * @param type $action
     * @param type $args
     * @return \stdClass|boolean
     */
    function check_info( $false, $action, $args ) {

        if ( 'plugin_information' != $action ) {
            return $false;
        }

        if ( self::slug == $args->slug ) {

            $remote_info = $this->get_info();

            $obj              = new stdClass();
            $obj->slug        = self::slug;
            $obj->new_version = $remote_info->latest;

            if ( isset( $remote_info->latest_url ) ) {
                $obj->download_link = $remote_info->latest_url;
            }

            $obj->sections = array(
                'description' => $remote_info->msg
            );

            return $obj;
        }

        return $false;
    }

    /**
     * Collects current plugin information
     *
     * @return array
     */
    function get_current_plugin_info() {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';

        $plugin_data    = get_plugin_data( DOKAN_AUCTION_DIR . '/dokan-auction.php' );
        $plugin_name    = $plugin_data['Name'];
        $plugin_version = $plugin_data['Version'];

        return array($plugin_name, $plugin_version);
    }

    /**
     * Get plugin update information from server
     *
     * @global string $wp_version
     * @global object $wpdb
     * @return boolean
     */
    function get_info() {
        global $wp_version, $wpdb;

        list( $plugin_name, $plugin_version) = $this->get_current_plugin_info();

        if ( is_multisite() ) {
            $wp_install = network_site_url();
        } else {
            $wp_install = home_url( '/' );
        }

        $license = $this->get_license_key();

        $params = array(
            'timeout'    => 15,
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body' => array(
                'name'              => $plugin_name,
                'slug'              => self::slug,
                'type'              => 'plugin',
                'version'           => $plugin_version,
                'wp_version'        => $wp_version,
                'php_version'       => phpversion(),
                'site_url'          => $wp_install,
                'license'           => isset( $license['key'] ) ? $license['key'] : '',
                'license_email'     => isset( $license['email'] ) ? $license['email'] : '',
                'product_id'        => $this->product_id
            )
        );

        $response = wp_remote_post( self::api_endpoint . 'update_check', $params );
        $update   = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            return false;
        }

        return json_decode( $update );
    }

    /**
     * Plugin license enter admin UI
     *
     * @return void
     */
    function plugin_update() {
        $errors = array();
        if ( isset( $_POST['dokan-auction-license-submit'] ) ) {
            if ( empty( $_POST['email'] ) ) {
                $errors[] = __( 'Empty email address', 'dokan-auction' );
            }

            if ( empty( $_POST['license_key'] ) ) {
                $errors[] = __( 'Empty license key', 'dokan-auction' );
            }

            if ( !$errors ) {
                update_option( self::option, array('email' => $_POST['email'], 'key' => $_POST['license_key']) );
                delete_transient( self::option );

                $license_status = get_option( 'dokan_auction_license_status' );

                if ( !isset( $license_status->activated ) || $license_status->activated != true ) {
                    $response = $this->activation( 'activation' );

                    if ( $response && isset( $response->activated ) && $response->activated ) {
                        update_option( 'dokan_auction_license_status', $response );
                    }
                }


                echo '<div class="updated"><p>' . __( 'Settings Saved', 'dokan-auction' ) . '</p></div>';
            }
        }

        if ( isset( $_POST['delete_auction_license'] ) ) {
            delete_option( self::option );
            delete_transient( self::option );
            delete_option( 'dokan_auction_license_status' );
        }

        $license = $this->get_license_key();
        $email   = $license ? $license['email'] : '';
        $key     = $license ? $license['key'] : '';
        ?>
        <h3><?php _e( 'Dokan Auction', 'dokan-auction' ); ?></h3>
        <hr>
            <?php

            $license_status = get_option( 'dokan_auction_license_status' );
            if ( !isset( $license_status->activated ) || $license_status->activated != true ) {
                ?>

                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th><?php _e( 'E-mail Address', 'dokan-auction' ); ?></th>
                            <td>
                                <input type="email" name="email" class="regular-text" value="<?php echo esc_attr( $email ); ?>" required>
                                <span class="description"><?php _e( 'Enter your purchase Email address', 'dokan-auction' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'License Key', 'dokan-auction' ); ?></th>
                            <td>
                                <input type="text" name="license_key" class="regular-text" value="<?php echo esc_attr( $key ); ?>">
                                <span class="description"><?php _e( 'Enter your license key', 'dokan-auction' ); ?></span>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button( __( 'Save & Activate', 'dokan-auction' ), 'primary', 'dokan-auction-license-submit' ); ?>
                </form>
            <?php } else {

                if ( isset( $license_status->update ) ) {
                    $update = strtotime( $license_status->update );
                    $expired = false;

                    if ( time() > $update ) {
                        $string = __( 'has been expired %s ago', 'dokan-auction' );
                        $expired = true;
                    } else {
                        $string = __( 'will expire in %s', 'dokan-auction' );
                    }
                    // $expired = true;
                    ?>
                    <div class="updated <?php echo $expired ? 'error' : ''; ?>">
                        <p>
                            <strong><?php _e( 'Validity:', 'dokan-auction' ); ?></strong>
                            <?php printf( 'Your Dokan Auction license %s.', sprintf( $string, human_time_diff( $update, time() ) ) ); ?>
                        </p>

                        <?php if ( $expired ) { ?>
                            <p><a href="https://wedevs.com/account/" target="_blank" class="button-primary"><?php _e( 'Renew License', 'dokan-auction' ); ?></a></p>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>

                <div class="updated">
                    <p><?php _e( 'Dokan Auction Plugin is activated', 'dokan-auction' ); ?></p>
                </div>

                <form method="post" action="">
                    <?php submit_button( __( 'Delete Dokan Auction License', 'dokan-auction' ), 'delete', 'delete_auction_license' ); ?>
                </form>

            <?php }
    }

}