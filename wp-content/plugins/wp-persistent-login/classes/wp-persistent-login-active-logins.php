<?php

/**
 * Class WP_Persistent_Login_Active_Logins
 *
 * @since 2.0.0
 */
class WP_Persistent_Login_Active_Logins {

    protected $limit;

	/**
	 * __construct
	 *
     * @since  2.0.0
	 * @return void
	 */
	public function __construct() {

        if( !isset($this->limit) ) {
            $this->limit = 1;
        }
        
		// Use password check filter.
		add_filter( 'check_password', array( $this, 'validate_block_logic' ), 20, 4 );

	}



	/**
     * get_human_readable_login_duration
     * 
     * Gets the amount of time since a session was last active.
     *
     * @param  int $seconds_ago
     * @return string
     */
    protected function get_human_readable_login_duration($login_time) {

        $seconds_ago = (time() - $login_time);

        $time_breaks = array(
            31536000 => __(' years ago', WPPL_TEXT_DOMAIN),
            2419200 => __(' months ago', WPPL_TEXT_DOMAIN),
            86400 => __(' days ago', WPPL_TEXT_DOMAIN),
            3600 => __(' hours ago', WPPL_TEXT_DOMAIN),
            60 => __(' mins ago', WPPL_TEXT_DOMAIN),
            0 => __('Active now', WPPL_TEXT_DOMAIN)
        );

        foreach( $time_breaks as $key => $value ) {

            if( $key === 0 ) {

                return $value;
            
            } elseif( $seconds_ago >= $key ) {
            
                $login_duration = intval( $seconds_ago / $key ) . $value;
                return $login_duration;
            
            }
        
        }

    }
    


    /**
     * get_user_device
     * 
     * Takes the user agent and returns the device type and a description of the device.
     *
     * @param  string $user_agent
     * @return string
     */
    protected function get_user_device($user_agent) {

        $device = new WhichBrowser\Parser($user_agent);
        $device_type = ucfirst($device->device->type);
        $browser = $device->browser->name;
        $os = $device->os->toString();
        $the_device = $device->device->toString().' ';

        return $browser .' '. __('on', WPPL_TEXT_DOMAIN) .' ' . $the_device . $os .' ('. $device_type .')';

    }

    
    /**
     * get_user_location
     *
     * @param  string $remote_address
     * @return array|bool
     */
    protected function get_user_location($remote_address) {

        $ip_data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$remote_address));
        $city = $ip_data['geoplugin_city'];
        $region = $ip_data['geoplugin_regionName'];
        $country = $ip_data['geoplugin_countryName'];

        if( $city !== null || $region !== null || $country !== null ) {

            $output = array();

            if( $city !== null ) {
                $output[] = $city;
            }
            if( $region !== null ) {
                $output[] = $region;
            }
            if( $country !== null ) {
                $output[] = $country;
            }

            return $output;

        } else {
            
            return false;
        
        }
        

    }



	/**
     * get_session_verifier
     *
     * @return string
     */
    private function get_session_verifier() {

        $sessionToken = wp_get_session_token();
        
        if ( function_exists( 'hash' ) ) {
            $verifier = hash('sha256', $sessionToken);
        } else {
            $verifier = sha1( $sessionToken);
        }

        return $verifier;
    
    }


       
    /**
     * get_user_sessions
     *
     * @since  2.0.0
     * @param  object $user
     * @param  mixed $sort_order
     * @return array|bool
     */
    protected function get_user_sessions($user, $sort_order = SORT_DESC) {

        $sessions = get_user_meta($user->ID, 'session_tokens', true);

        if( is_array($sessions) ) {

            // fetch the login time column from the array
            $login_times = array_column($sessions, 'login');

            // sort the sessions by login times (newest first)
            array_multisort( $login_times, $sort_order, $sessions );

            return $sessions;

        } else {

            return false;
        
        }

    }



	/**
     * get_session_data
     * 
     * Takes a users sessions and outputs the required data for the session management table.
     *
     * @param  array $sessions
     * @return array
     */
    public function get_session_data($user) {

        $sessions = $this->get_user_sessions($user);
        
        if( $sessions !== false ) {

            $data = array();

            foreach( $sessions as $key => $session ) {

                $device = $this->get_user_device($session['ua']);
                $ip_address = $session['ip'];
                $login_time = $this->get_human_readable_login_duration($session['login']);
                $session_key = $key;

                if ( $session_key === $this->get_session_verifier() )  :
                    $current_device = true;
                else :
                    $current_device = false;
                endif;

                $data[] = array(
                    'device' => $device,
                    'ip' => $ip_address,
                    'login_time' => $login_time,
                    'session_key' => $session_key,
                    'current_device' => $current_device
                );

            }

            return $data;

        } else {

            return false;

        }
        

    }


	/**
	 * Validate if the maximum active logins limit reached.
	 *
	 * This check happens only after authentication happens
	 *
	 * @param boolean $check    Whether the passwords match..
	 * @param string  $password Plaintext user's password.
	 * @param string  $hash     Hash of the user's password to check against.
	 * @param int     $user_id  User ID.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function validate_block_logic( $check, $password, $hash, $user_id ) {

        // If the validation failed already, bail.
		if ( ! $check ) {
			return false;
		}
        $settings = new WP_Persistent_Login_Settings();
        $limit_active_logins = $settings->get_limit_active_logins();
        
        // if we should limit the number of active logins, 
        if( $limit_active_logins === '1' ) {

            $user = get_user_by('id', $user_id);

            // if the limit is exceeded, start ending sessions
            if ( $this->reached_limit( $user ) ) {
                $this->end_excess_logins( $user );
            }
        }

        return true;

        
	}

    
    /**
     * end_excess_logins
     *
     * @param  mixed $user_id
     * @return void
     */
    protected function end_excess_logins($user) {

        $user_id = $user->ID;

        // setup a session manager
        $session_manager = new WP_Persistent_Login_Manage_Sessions( $user_id );

        // count the current sessions
        $session_count = $this->get_login_count( $user_id );

        // invalid sessions = current sessions, plus this login, minus the limit
        $invalid_sessions = $this->get_invalid_session_count( $user_id );

        // if there are invalid sessions remove them
        if( $invalid_sessions > 0 ) {

            // get all users sessions, oldest first
            $sessions = $this->get_user_sessions( $user, SORT_ASC );

            $session_tokens = array_keys( $sessions );
            
            // remove all invalid sessions, leaving only the limit
            for( $i = 0; $i < $invalid_sessions; $i++ ) {
                $session_token = $session_tokens[$i];
                $session_manager->persistent_login_update_session( $session_token );
            }
        
        }   

    }

    
    protected function get_login_count($user_id) {

        // Sessions token instance.
		$manager = WP_Session_Tokens::get_instance( (int) $user_id );

		// Count sessions.
		$login_count = count( $manager->get_all() );

        return $login_count;

    }


    /**
     * get_invalid_session_count
     *
     * @param  int $user_id
     * @return int
     */
    protected function get_invalid_session_count($user_id) {

        $user = get_user_by('ID', $user_id);

        // get all users sessions, oldest first
        $sessions = $this->get_user_sessions($user, SORT_ASC);

        // count the current sessions
        $session_count = $this->get_login_count($user_id);

        // invalid sessions = current sessions, plus this login, minus the limit
        $invalid_sessions = ($session_count+1) - $this->limit;

        return $invalid_sessions;

    }


	/**
	 * Check if the current user is allowed for another login.
	 *
	 * Count all the active logins for the current user annd
	 * check if that exceeds the maximum login limit set.
	 *
	 * @param int $user_id User ID.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return boolean Limit reached or not
	 */
	protected function reached_limit( $user_id ) {

		// Count sessions.
		$login_count = $this->get_login_count( $user_id );

		// Check if limit reached.
		$limit_reached = $login_count >= $this->limit;

		/**
		 * Filter hook to change the limit condition.
		 *
		 * @param bool $limit_reached
		 * @param int  $user_id
		 * @param int  $login_count
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wppl_loggedin_limit_reached', $limit_reached, $user_id, $login_count );
	}



}


?>