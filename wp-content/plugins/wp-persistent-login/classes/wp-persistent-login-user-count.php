<?php


// If this file is called directly, abort.
defined( 'WPINC' ) || die( 'Well, get lost.' );

/**
 * Class WP_Persistent_Login_User_Count
 *
 * @since 2.0.0
 */
class WP_Persistent_Login_User_Count extends WP_Persistent_Login_Admin {


    private $hide_dashboard_stats;


    /**
	 * Initialize the class and set its properties.
	 *
	 * We register all our common hooks here.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {

        // add a minutely WP Cront schedule for when the count is running
        add_filter( 'cron_schedules', array($this, 'cron_add_minutely') ); 

        // start the user count
        add_action( 'persistent_login_user_count', array($this, 'start_count') );

        // update and end the user count
        add_action( 'persistent_login_update_count', array($this, 'update_count') );

        // show the login count on the dashboard
        $settings = new WP_Persistent_Login_Settings();
        $this->hide_dashboard_stats = $settings->get_dashboard_stats();
        if( $this->hide_dashboard_stats === '0' ) {
            add_action( 'activity_box_end', array($this, 'display_login_count_dashboard_stats') );
        }
       
    }

    	
	/**
	 * cron_add_minutely
     * 
     * Adds a Cron schedule that runs every minute.
	 *
     * @since 2.0.0
	 * @param  mixed $schedules
	 * @return array
	 */
	public function cron_add_minutely( $schedules ) {

		// Adds once weekly to the existing schedules.
        if( !isset($schedules['minutely']) ) {
            $schedules['minutely'] = array(
                'interval' => 60,
                'display' => __( 'Once every minute' )
            );
        }

		return $schedules;
	
    }


    
    /**
     * get_allowed_roles
     * 
     * Gets all of the allowed Persistent login roles. By default, all roles are allowed.
     *
     * @since 2.0.0
     * @return array
     */
    private function get_allowed_roles() {
    
        $roles = [];
        $wp_roles = wp_roles();
        
        foreach( $wp_roles->roles as $key => $value ) {
            array_push($roles, $key);
        }
             
        return $roles;
    
    }


    /**
	 * is_user_count_running
	 * 
	 * Checks to see if the user count is currently running.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function is_user_count_running() {
		
		$is_count_running = get_transient('persistent_login_user_count_running') ?: NULL;

		return $is_count_running;

	}

    
    /**
     * get_current_counting_role
     * 
     * Gets the current user role being counted
     *
     * @return string
     */
    private function get_current_counting_role() {
        
        $current_role = get_transient('persistent_login_user_count_current_role') ?: NULL;

		if( $current_role ) {
            return $current_role;
        } else {
            return false;
        }

    }



    /**
	 * output_current_counting_role
     * 
     * Outputs the user role currently being counted and returns it as a string.
	 *
	 * @return string
	 */
	public function output_current_counting_role() {

		$current_role = $this->get_current_counting_role();

		if( $current_role ) {

			$current_role = str_replace('_', ' ', ucfirst($current_role));

            return sprintf(
                __('User count is currently running. Figures below will update once the count has completed. The %s role is being counted now.', WPPL_TEXT_DOMAIN),
                $current_role
            );
		
        } else {

            return false;

        }

	}


    /**
	 * get_next_count_time
	 * 
	 * Returns the timestamp of the next user count.
	 *
	 * @since 2.0.0
	 * @return int
	 */
	private function get_next_count_time() {

		$next_check = wp_next_scheduled( 'persistent_login_user_count' );
		
        return $next_check;
	
    }



    /**
	 * get_next_count_difference
	 * 
	 * Returns the difference in hours until the next user count.
	 *
	 * @since 2.0.0
	 * @return int
	 */
	private function get_next_count_time_difference() {
		
		$time_now = time();
		$next_check = $this->get_next_count_time();
		$difference = $next_check - $time_now;
		$difference_in_hours = round($difference / 60 / 60, 1);

		return $difference_in_hours;

	}


    /**
	 * get_user_count
	 * 
	 * gets the number of users currently logged in
	 *
	 * @since 2.0.0
	 * @return int
	 */
	private function get_user_count() {
			
		$user_count = 0;
		$roles = $this->get_user_count_breakdown();
		
		if( isset($roles) && !empty($roles) ) :
			foreach( $roles as $role ) :
				$user_count += $role;
			endforeach;
		endif;
		
		return $user_count;			
	}


    /**
	 * output_loggedin_user_count
	 * 
	 * Outputs a string with the number of currently logged in users
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function output_loggedin_user_count() {

		$user_count = $this->get_user_count(); 

		$plural_user = ($user_count === 1) ? __('user', WPPL_TEXT_DOMAIN) : __('users', WPPL_TEXT_DOMAIN);
		$is_or_are = ($user_count === 1) ? __('is ', WPPL_TEXT_DOMAIN) : __('are ', WPPL_TEXT_DOMAIN);

		return sprintf( 
			__(
				'<strong>%d %s</strong> %s being kept logged into your website.', 
				WPPL_TEXT_DOMAIN
			), 
			$user_count,
			$plural_user,
			$is_or_are
		); 

	}



    /**
	 * output_last_loggedin_user_count
	 * 
	 * Outputs a string with the number of last counted logged in users
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function output_last_loggedin_user_count() {

		$user_count = $this->get_last_login_count(); 

		return sprintf( 
			__(
				'User count is currently running. The previous loggged in user count was %d.', 
				WPPL_TEXT_DOMAIN
			), 
			$user_count
		); 

	}




    /**
	 * get_user_count_breakdown
	 * 
	 * gets an array of logged in users, by user Role
	 *
	 * @since 2.0.0
	 * @return array|bool
	 */
	private function get_user_count_breakdown() {
				
		$user_count = get_option('persistent_login_user_count');
        if( $user_count ) {
            return $user_count;
        } else {
            return false;
        }
		
	
	}
	
    
    /**
     * update_user_count_breakdown
     *
     * @since 2.0.0
     * @param  array $user_count
     * @return bool
     */
    private function update_user_count_breakdown($user_count) {
        
        $update_roles = update_option('persistent_login_user_count', $user_count);
        return $update_roles;

    }


	/**
	 * output_user_count_breakdown
	 * 
	 * Outputs HTML to display the number of users logged in, 
	 * broken down by their Role.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function output_user_count_breakdown() {

		$breakdown = $this->get_user_count_breakdown();
	
		if( $breakdown && !empty($breakdown) ) :

			foreach( $breakdown as $key=>$value ) : ?>
				<div style="width: 250px; max-width: 45%; float: left; display: block; float: left;">
					<?php echo str_replace(['_', '-'], ' ', ucfirst($key)); ?>: 
					<strong><?php echo $value; ?></strong>
				</div>
			<?php endforeach; ?>
			<div style="clear: both; display: block;"></div>

		<?php else : ?>
		
			<p>
				<em>
					<?php _e('Logins not counted yet.', WPPL_TEXT_DOMAIN); ?>
				</em>
			</p>
		
		<?php endif;

	}


    
    /**
     * get_last_login_count
     *
     * @return string|bool
     */
    private function get_last_login_count() {

        $last_count = (int) get_transient('persistent_login_last_count');

        if( $last_count ) {
            return $last_count;
        } else {
            return false;
        }

    }


    /**
	 * output_last_login_count
	 * 
	 * Gets the last user count figure.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function output_last_login_count() {

		$last_count = $this->get_last_login_count();

        if( $last_count !== false )  {

            return sprintf( 
                __(
                    'The previous logged in user count was: %d', WPPL_TEXT_DOMAIN
                ), 
                $last_count
            ); 
        
        } 
		
	}



    /**
	 * output_next_count
	 * 
	 * Outputs HTML to display when the next user count is being run. 
	 * Notifies the user if the count is currently running. 
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function output_next_count() {
		
		$is_count_running = $this->is_user_count_running();

		if( !$is_count_running ) :

			$next_count = $this->get_next_count_time_difference();
			if( $next_count ) : 
				?>

				<p style="color: #9e9e9e;">
					<?php 
					printf( 
						__('Next automated logged in count: Approximately %d hours', WPPL_TEXT_DOMAIN),
						$next_count
					); ?>
				</p>
			    
                <?php
            endif;
            
        endif;
	}



    
    /**
     * display_login_count_dashboard_stats
     *
     * @return void
     */
    public function display_login_count_dashboard_stats() {
	 		
        $logged_in_users = $this->output_loggedin_user_count();
    
        if ( persistent_login()->is_not_paying() ) : 
            $button = 
            '<a href="'. persistent_login()->get_upgrade_url() .'" class="button button-primary">
                View Upgrade Options
            </a>';
            $title = ' - Free Forever Plan';
        else :
            $button = '';
            $title = ' - Premium Plan';
        endif;
        
        echo (
            sprintf(
                '<hr/><h3><strong>WordPress Persistent Login %s</strong></h3>
                <p>%s</p>
                <p>
                    <a href="'.WPPL_SETTINGS_PAGE.'" class="button">Manage Settings</a>
                    &nbsp; %s
                </p><hr/>', 
                $title,
                $logged_in_users, 
                $button
            )
        );
       
   }
   



    
        
    /**
     * start_count
     *
     * @since 2.0.0
     * @return void
     */
    public function start_count() {

        // Check if the count is currently running, if it's not, start it. 
        $is_count_running = $this->is_user_count_running();
        
        if( !$is_count_running ) {

            // set a transient so we know the count has started
            set_transient( 'persistent_login_user_count_running', 1, 0 );

            // get the current user count and store it as the last count
            $last_count = $this->get_user_count();
            set_transient( 'persistent_login_last_count', $last_count, 0 );
        

            // get the allowed user roles to count
            $roles = [];
            $allowed_roles = $this->get_allowed_roles();

            if( is_array($allowed_roles) && !empty($allowed_roles) ) {

                // store the roles we're allowed to count
                set_transient('persistent_login_allowed_roles_reference', $allowed_roles, 0);

                // set the count to 0 for each role and update the count
                foreach( $allowed_roles as $key => $value ) {
                    $roles[$value] = 0;
                }
                set_transient('persistent_login_user_count_temporary', $roles);
            
            
                // set the current role to the first role in the $allowed_roles array
                $current_role = $allowed_roles[0];

                // set the current role being counted
                set_transient( 'persistent_login_user_count_current_role', $current_role, 0 );

                // set the current count offset to 0 because we're just starting the count now
                set_transient('persistent_login_user_count_offset', 0, 0);
            
                // check if the count is scheduled to update
                $timestamp = wp_next_scheduled( 'persistent_login_update_count' );
                            
                // If $timestamp == false, schedule the count now since it hasn't been started yet
                if( $timestamp == false ) {
                    // Schedule the event for right now, then to repeat daily using the hook 'persistent_login_update_count'
                    wp_schedule_event( time(), 'minutely', 'persistent_login_update_count');
                }

            }

        }
        
        
    }



        
    /**
     * update_count
     *
     * @since 2.0.0
     * @return void
     */
    public function update_count() {

        // set the block size to count in, allow users to filter it
        $block_size = apply_filters( 'wp_persistent_login_count_block_size', 300 );

        // get the current user count offset, or default to 0 if not defined
        $offset = get_transient('persistent_login_user_count_offset') ?: 0;

        // get the current role being counted
        $role = $this->get_current_counting_role();

        // count the next block of users
        $args = array(
            'role' => $role,
            'meta_key' => 'session_tokens',
            'meta_compare' => 'EXISTS',
            'fields' => array('ID'),
            'count_total' => false,
            'offset' => $offset,
            'number' => $block_size
        ); 
        $users = count(get_users($args));

        // update the user count with this block
        $user_count = get_transient('persistent_login_user_count_temporary');
        $user_count[$role] += $users;
        set_transient( 'persistent_login_user_count_temporary', $user_count);

        // if there are less users than the block size, this role has been counted completely.
        if( $users < $block_size ) {

            // fetch the allowed roles so we can move to the next role
            $allowed_roles = get_transient('persistent_login_allowed_roles_reference');

            if( is_array($allowed_roles) ) {
                foreach( $allowed_roles as $key => $value ) {

                    // stop on the currently counted role
                    if( $role === $value ) {

                        // increment the key by one, to get the next role
                        $next_role = $key+1;
                        
                        // if the next role exists, set this as the currently counted role and set the offset to 0
                        if( isset($allowed_roles[$next_role]) ) {

                            set_transient('persistent_login_user_count_current_role', $allowed_roles[$next_role], 0);
                            set_transient('persistent_login_user_count_offset', 0, 0);

                        // if the next role doesn't exist, the count is finished, stop the count.
                        } else {

                            $this->stop_count();
                        
                        }
                        
                        break;

                    }
                } 
            }
            
        // if there are more users to count in the current role, increase the offset and let update_count run again
        } else {

            // set the current offset in a transient
            $new_offset = $offset + $users;
            set_transient('persistent_login_user_count_offset', $new_offset, 0);
        
        }

    }


    
    /**
     * stop_count
     *
     * @since 2.0.0
     * @return void
     */
    private function stop_count() {

        // move the temporary count to the main count
        $temporary_user_count = get_transient('persistent_login_user_count_temporary');
        $this->update_user_count_breakdown($temporary_user_count);

        // we're done, clean up transients
        delete_transient('persistent_login_user_count_current_role');
        delete_transient('persistent_login_user_count_offset');
        delete_transient('persistent_login_user_count_running');
        delete_transient('persistent_login_allowed_roles_reference');
        delete_transient('persistent_login_last_count');
        delete_transient('persistent_login_user_count_temporary');
    
        // stop the minutely count task so we stop counting users
        wp_clear_scheduled_hook('persistent_login_update_count'); 
    
    }

   
    
}