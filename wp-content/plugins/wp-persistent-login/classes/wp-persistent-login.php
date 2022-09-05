<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die( 'Process terminated.' );

/**
 * Class WP_Persistent_login
 *
 * @since 2.0.0
 */
class WP_Persistent_Login {


	public $expiration;


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

		$this->expiration = YEAR_IN_SECONDS;
		
		// set the expiration time when a user logs in
		add_filter( 'auth_cookie_expiration', array( $this, 'set_login_expiration' ), 10, 3 );

		// increase the cookie time when a user revisits
		add_action( 'auth_cookie_valid', array( $this, 'update_auth_cookie' ), 10, 2 );

		// set user meta if the user want to be remembered
		add_filter( 'secure_signon_cookie', array( $this, 'remember_me_meta' ), 20, 2 ); 

		// pre-check the remember me box
		add_action( 'wp_footer', array( $this, 'precheck_remember_me' ) );
		add_filter( 'login_footer', array( $this, 'precheck_remember_me' ) );
		
		// logout management
		add_action( 'clear_auth_cookie', array( $this, 'logout' ) );

		// woocommerce auto remember users on register
		add_filter( 'woocommerce_login_credentials', array( $this, 'woocommerce_remember_on_login' ), 20, 1 );

	}


	/**
	 * set_login_expiration
	 * 
	 * Adjust the login expiration time if the user selected to be remembered.
	 *
	 * @param  int $expiration
	 * @param  int $user_id
	 * @param  bool $remember
	 * @return int $expiration
	 */
	public function set_login_expiration( $expiration, $user_id, $remember ) {
			
		// the the user wants to be remembered, set the expiration time to 1 year
		if( $remember ) :
						
			// default expiration time to 1 year
			$expiration = $this->expiration;
												
		endif;
	  
		/**
		 * Filter hook to change the expiration time manually
		 *
		 * @param int $expiration Expiration time in seconds.
		 * @param int $user_id The current Users ID.
		 * @param bool $remember Boolean value for if the user selected to be remembered.
		 *
		 * @since 1.4.0
		 */
		return apply_filters( 'wp_persistent_login_auth_cookie_expiration', $expiration, $user_id, $remember );
	  
	}



	
	/**
	 * remember_me_meta
	 * 
	 * Adds meta data to the user. If set, extends their login cookie every time they login.
	 *
	 * @param  bool $secure_cookie
	 * @param  array $credentials
	 * @return bool
	 */
	public function remember_me_meta( $secure_cookie, $credentials ) { 
				
		$user = get_user_by('login', $credentials['user_login']);

		if ( !empty( $user ) ) {			
		
			if( $credentials['remember'] === true ) {
				update_user_meta( $user->ID, 'persistent_login_remember_me', 'true');
			}
			
			if( $credentials['remember'] === false ) {
				delete_user_meta( $user->ID, 'persistent_login_remember_me', 'true');
			}
		
		}
		
		return $secure_cookie; 

	} 


		
	/**
	 * update_auth_cookie
	 * 
	 * Reset authentication cookie expiry to keep the user logged in
	 *
	 * @since  1.0.0
	 * @param  array $cookieElements
	 * @param  object $user
	 * @return void
	 */
	public function update_auth_cookie( $cookieElements, $user ) {					
				
		if( $user ) :

			// set users local cookie again - checks if they should be remembered
			$remember_user_check = get_user_meta( $user->ID, 'persistent_login_remember_me', true );
						
			if( $remember_user_check === 'true' ) :
				
				// get the session verifier from the token
					$sessionToken = $cookieElements['token'];
						
					if ( function_exists( 'hash' ) ) :
						$verifier = hash( 'sha256', $sessionToken );
					else :
						$verifier = sha1( $sessionToken );
					endif;						
						
				// update the login time, expires time
					$sessions = get_user_meta( $user->ID, 'session_tokens', true );
					$sessions[$verifier]['login'] = time();
					$sessions[$verifier]['expiration'] = time()+$this->expiration;
					$sessions[$verifier]['ip'] = $_SERVER["REMOTE_ADDR"];
						
				// update the token with new data
					$wp_session_token = WP_Session_Tokens::get_instance( $user->ID );
					$wp_session_token->update( $sessionToken, $sessions[$verifier] );
				
				// apply filter for allowing duplicate sessions, default false
					$currentOptions = get_option( 'persistent_login_options' );
					$allowDuplicateSessions = $currentOptions['duplicateSessions'];
						
						
				// remove any exact matches to this session
					if( $allowDuplicateSessions === '0' ) :

						foreach( $sessions as $key => $session ) :
							if( $key !== $verifier ) : 
																												
									// if we're on the same user agent and same IP, we're probably on the same device
									// delete the duplicate session
									if( 
										($session['ip'] === $sessions[$verifier]['ip']) &&
										($session['ua'] === $sessions[$verifier]['ua'])
									) :
										$updateSession = new WP_Persistent_Login_Manage_Sessions( $user->ID );
										$updateSession->persistent_login_update_session( $key );
									endif;
																			
							endif; // if key is different to identifier 
						endforeach;

					endif;
				
				// if the user should be remembered, reset the cookie so the cookie time is reset
					wp_set_auth_cookie( $user->ID, true, is_ssl(), $sessionToken );
								

			endif; // end if remember me is set	

		endif; // endif user
	}





	/**
	 * precheck_remember_me
	 * 
	 * Pre-check the Remember me checkbox on login forms
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return NULL
	 */
	public function precheck_remember_me() {
			
				
		$script = '<script>';
		$script .= "

			var wppl_precheck_remember_me = function() {

				// check remember me by default
				var forms = document.querySelectorAll('form'); 						
				if (forms) {

					var rememberMeNames = ['rememberme', 'remember', 'rcp_user_remember'];
					var rememberArray = [];

					// loop through each remember me name and see if there's a field that matches
					for( z = 0; z < rememberMeNames.length; z++ ) {
						var input = document.getElementsByName( rememberMeNames[z] );
						if( input.length ) {
							rememberArray.push(input);
						}
					}
					
					// if there are remember me inputs
					if( rememberArray.length ) { 	
					
						// 'check' the inputs so they're active		
							for (i = 0; i < rememberArray.length; i++) {
								for (x = 0; x < rememberArray[i].length; x++) {
								  rememberArray[i][x].checked = true;
								}
							}
					
					}

					
					// test for Ultimate Member Plugin forms
						
						// find the UM checkboxes
						var UmCheckboxIcon = document.querySelectorAll('.um-icon-android-checkbox-outline-blank');
						var UmCheckboxLabel = document.querySelectorAll('.um-field-checkbox');
						
						if( UmCheckboxIcon.length && UmCheckboxLabel.length ) {
							
							// loop through UM checkboxes
							for (i = 0; i < UmCheckboxLabel.length; i++) {
								
								// find the UM input element
								var UMCheckboxElement = UmCheckboxLabel[i].children;
								var UMCheckboxElementName = UMCheckboxElement[0].getAttribute('name');
								
								// check if UM input element is remember me box
								if( UMCheckboxElementName === 'remember' || UMCheckboxElementName === 'rememberme' ) {
									
									// activate the UM checkbox if it is a remember me box
									UmCheckboxLabel[i].classList.add('active');
									
									// swap out UM classes to show the active state
									UmCheckboxIcon[i].classList.add('um-icon-android-checkbox-outline');
									UmCheckboxIcon[i].classList.remove('um-icon-android-checkbox-outline-blank');
									
								} // endif
							
							} // end for

						} // endif UM
						
						
						
					// test for AR Member
						
						var ArmRememberMeCheckboxContainer = document.querySelectorAll('.arm_form_input_container_rememberme');
						
						if( ArmRememberMeCheckboxContainer.length ) {
							
							for (i = 0; i < ArmRememberMeCheckboxContainer.length; i++) {
								
								var ArmRememberMeCheckbox = ArmRememberMeCheckboxContainer[i].querySelectorAll('md-checkbox');
								
								if( ArmRememberMeCheckbox.length ) {
									// loop through AR Member checkboxes
									for (x = 0; x < ArmRememberMeCheckbox.length; x++) {
										if( ArmRememberMeCheckbox[x].classList.contains('ng-empty') ) {
											ArmRememberMeCheckbox[x].click();
										}
									}
								}
								
							}
							
						} // end if AR Member
							
							
			
				} // endif forms

			}

			document.addEventListener('DOMContentLoaded', function(event) {
				wppl_precheck_remember_me();
			});

			";
		$script .= '</script>';

		echo $script;

	}

	
	/**
	 * logout
	 *
	 * deletes the user meta to re-login automatically when they visit
	 * 
	 * @return void
	 */
	public function logout() {
		delete_user_meta( get_current_user_id(), 'persistent_login_remember_me', 'true' );
	}
	
	
	
	/**
	 * woocommerce_remember_on_login
	 *
	 * @param  array $credentials
	 * @return array
	 */
	public function woocommerce_remember_on_login( $credentials ) {

		$credentials['remember'] = true;
		return $credentials;

	}


}

?>