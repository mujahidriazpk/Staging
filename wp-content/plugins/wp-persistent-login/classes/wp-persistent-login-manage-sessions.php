<?php


class WP_Persistent_Login_Manage_Sessions extends WP_User_Meta_Session_Tokens {
				
    // rebuild constructor
    public function __construct($user_id) {
        $this->user_id = $user_id;
    }
    
    // allow us to update sessions by verifier instead of unhashed token
    public function persistent_login_update_session($verifier, $session = null) {
        $this->update_session($verifier, $session);
    }
    
    // allow us to get a session by verifier instead of unhashed token
    public function persistent_login_get_session($verifier) {
        $this->get_session($verifier);
    }
    
    
}

?>