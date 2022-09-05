<?php
/**
 * Advanced Visitor Conditions module.
 *
 * -TODO should use a constant for option key as it is shared at multiple positions
 */
class Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions {

	protected $options = array();
	protected $is_ajax;

	// Note: hard-coded in JS
	const REFERRER_COOKIE_NAME = 'advanced_ads_pro_visitor_referrer';

	// page impression counter
	const PAGE_IMPRESSIONS_COOKIE_NAME = 'advanced_ads_page_impressions';

	// ad impression cookie name basis
	const AD_IMPRESSIONS_COOKIE_NAME = 'advanced_ads_ad_impressions';

	public function __construct() {
		// load options (and only execute when enabled)
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( isset( $options['advanced-visitor-conditions'] ) ) {
			$this->options = $options['advanced-visitor-conditions'];
		}

		// only execute when enabled
		if ( ! isset( $this->options['enabled'] ) || ! $this->options['enabled'] ) {
			return ;
		}

		$is_admin = is_admin();
		$this->is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		add_filter( 'advanced-ads-visitor-conditions', array( $this, 'visitor_conditions' ) );
		// action after ad output is created; used for js injection
		add_filter( 'advanced-ads-ad-output', array( $this, 'after_ad_output' ), 10, 2 );
		if ( $is_admin ) {
			// add referrer check to visitor conditions
			// add_action( 'advanced-ads-visitor-conditions-after', array( $this, 'referrer_check_metabox' ), 10, 2 );

			/*if ( $this->is_ajax ) {
				add_action( 'advanced-ads-ajax-ad-select-init', array( $this, 'ajax_init_ad_select' ) );
			}*/
		// wp ajax is admin but this will allow other ajax callbacks to avoid setting the referrer
		} elseif ( ! $this->is_ajax ) {
			// save referrer url in session for visitor referrer url feature
			$this->save_first_referrer_url();
			// count page impression
			$this->count_page_impression();

			// register js script to set cookie for cached pages
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

			// enable common frontend logic
			// $this->init_common_frontend();
		}
	}

	/**
	 * Specially prepare for ajax ad select calls.
	 *
	 */
	public function ajax_init_ad_select() {
		$this->init_common_frontend();
	}

	/**
	 * Init for any frontend action (including ajax ad select calls)
	 *
	 */
	public function init_common_frontend() {
		// check the url referrer condition
		// add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_url_referrer' ), 10, 2 );
	}

	/**
	 * Add scripts to non-ajax frontend calls.
	 */
	public function enqueue_scripts() {
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return;
		}
		// add dependency to manipulate cookies easily
		/*wp_enqueue_script( 'jquery' );
		wp_enqueue_script(
			'js.cookie',
			'//cdnjs.cloudflare.com/ajax/libs/js-cookie/1.5.1/js.cookie.min.js',
			array( 'jquery' ),
			'1.5.1',
			true
		);*/

		// add own code
		wp_register_script(
			'advanced_ads_pro/visitor_conditions',
			sprintf( '%sinc/conditions%s.js', plugin_dir_url( __FILE__ ), defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ),
			array( ADVADS_SLUG . '-advanced-js' ),
			AAP_VERSION
		);

		// 1 year by default
		$referrer_exdays = ( defined( 'ADVANCED_ADS_PRO_REFERRER_EXDAYS' ) && absint( ADVANCED_ADS_PRO_REFERRER_EXDAYS ) > 0 ) ? absint( ADVANCED_ADS_PRO_REFERRER_EXDAYS ) : 365;
		// 10 years by default
		$page_impressions_exdays = ( defined( 'ADVANCED_ADS_PRO_PAGE_IMPR_EXDAYS' ) && absint( ADVANCED_ADS_PRO_PAGE_IMPR_EXDAYS ) > 0 ) ? absint( ADVANCED_ADS_PRO_PAGE_IMPR_EXDAYS ) : 3650;

		wp_localize_script( 'advanced_ads_pro/visitor_conditions', 'advanced_ads_pro_visitor_conditions', array(
			'referrer_cookie_name' => self::REFERRER_COOKIE_NAME,
			'referrer_exdays' => $referrer_exdays,
			'page_impr_cookie_name' => self::PAGE_IMPRESSIONS_COOKIE_NAME,
			'page_impr_exdays' => $page_impressions_exdays
		));

		wp_enqueue_script( 'advanced_ads_pro/visitor_conditions' );
	}

	/**
	 * add visitor condition
	 *
	 * @since 1.0.1
	 * @param arr $conditions visitor conditions of the main plugin
	 * @return arr $conditions new global visitor conditions
	 */
	public function visitor_conditions( $conditions ){

		// referrer url
		$conditions['referrer_url'] = array(
			'label' => __( 'referrer url', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the referrer url.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_string' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_referrer_url' ) // callback for frontend check
		);

		// user_agent
		$conditions['user_agent'] = array(
			'label' => __( 'user agent', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the user agent.', 'advanced-ads-pro' ) . ' <a href="'. ADVADS_URL .'manual/display-ads-based-on-browser-or-device/#utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-user-agent" target="_blank">' . __( 'Manual', 'advanced-ads-pro' ) . '</a>',
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_string' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_user_agent' ) // callback for frontend check
		);

		// capabilities
		$conditions['capability'] = array(
			'label' => __( 'user can (capabilities)', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the user’s capabilities.', 'advanced-ads-pro' ) . ' <a href="'. ADVADS_URL .'manual/display-ads-based-on-user-capabilities/#utm_source=advanced-ads&utm_medium=link&utm_campaign=condition-user-capabilities" target="_blank">' . __( 'Manual', 'advanced-ads-pro' ) . '</a>',
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_capabilities' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_capabilities' ), // callback for frontend check
			'passive_info' => array( 'hash_fields' => 'value', 'remove' => 'login', 'function' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'get_passive_capability' ) )
		);
		$conditions['role'] = array(
			'label' => __( 'user role', 'advanced-ads-pro' ),
			'description' => sprintf( __( 'Display ads based on the user’s roles. See <a href="%s" target="_blank">List of roles in WordPress</a>.', 'advanced-ads-pro' ),
				'https://codex.wordpress.org/Roles_and_Capabilities' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_roles' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_roles' ), // callback for frontend check
			'passive_info' => array( 'hash_fields' => 'value', 'remove' => 'login', 'function' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'get_passive_role' ) )
		);

		// browser lang
		$conditions['browser_lang'] = array(
			'label' => __( 'browser language', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the visitors browser language.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_browser_lang' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_browser_lang' ) // callback for frontend check
		);

		// has cookie
		$conditions['cookie'] = array(
			'label' => __( 'cookie', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the value of a cookie.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_cookie' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_cookie' ) // callback for frontend check
		);

		// page impressions
		$conditions['page_impressions'] = array(
			'label' => __( 'page impressions', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the number of page impressions the user already made (before the current on).', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_number' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_page_impressions' ) // callback for frontend check
		);
		// page impressions in given time frame
		$conditions['ad_impressions'] = array(
			'label' => __( 'max. ad impressions', 'advanced-ads-pro' ),
			'description' => __( 'Display the ad only for a few impressions in a given period per user.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_ad_impressions' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_ad_impressions' ) // callback for frontend check
		);
		// new visitor
		$conditions['new_visitor'] = array(
			'label' => __( 'new visitor', 'advanced-ads-pro' ),
			'description' => __( 'Display ads to new or returning visitors only.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_is_or_not' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_new_visitor' ) // callback for frontend check
		);

		return $conditions;
	}

	/**
	 * save the first referrer url submitted. Cookies is set using JavaScript
	 *
	 * @since 1.1.0
	 */
	protected function save_first_referrer_url(){
		if ( ! isset( $_COOKIE[ self::REFERRER_COOKIE_NAME ] ) ) {
			if ( isset( $_SERVER['HTTP_REFERER'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
				// make cookies directly available to current request
				$_COOKIE[ self::REFERRER_COOKIE_NAME ] = $_SERVER['HTTP_REFERER'];
			}
		}
	}


	/**
	 * save page impressions in cookie. Cookies is set using JavaScript
	 *
	 * @since 1.1.0
	 */
	protected function count_page_impression(){
		if ( $this->is_ajax ) {
			return;
		}

		// Make cookies directly available to current request.
		$impressions = isset( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] )
			? absint( self::extract_cookie_data( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) )
			: 0;
		$_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] = $impressions + 1;
	}

	/**
	 * callback to display the "capabilities" condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_capabilities( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

	    // options
	    $value = isset( $options['value'] ) ? $options['value'] : '';
	    $operator = isset( $options['operator'] ) ? $options['operator'] : 'can';

	    // load capabilities
	    global $wp_roles;
	    $roles = $wp_roles->roles;

	    // loop through all roles in order to get registered capabilities
	    $capabilities = array();
	    foreach ( $roles as $_role ){
		    if( isset( $_role['capabilities'] )){
			$capabilities += $_role['capabilities'];
		    }
	    }

	    // sort keys by alphabet
	    ksort( $capabilities );

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<select name="<?php echo $name; ?>[operator]">
		    <option value="can" <?php selected( 'can', $operator ); ?>><?php _e( 'can', 'advanced-ads-pro' ); ?></option>
		    <option value="can_not" <?php selected( 'can_not', $operator ); ?>><?php _e( 'can not', 'advanced-ads-pro' ); ?></option>
		</select>
		<div class="advads-conditions-select-wrap"><select name="<?php echo $name; ?>[value]">
			<option><?php _e( '-- choose one --', 'advanced-ads-pro' ); ?></option>
			<?php foreach( $capabilities as $cap => $_val ) : ?>
				<option value="<?php echo $cap; ?>" <?php selected( $cap, $value ); ?>><?php echo $cap; ?></option>
			<?php endforeach; ?>
		</select></div>
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}

	/**
	 * Callback to display the "roles" condition.
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_roles( $options, $index = 0, $form_name = '' ) {

		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

		$type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

		// options
		$value = isset( $options['value'] ) ? $options['value'] : '';
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is';

		global $wp_roles;
		$roles = $wp_roles->get_names();

		?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<select name="<?php echo $name; ?>[operator]">
			<option value="is" <?php selected( 'is', $operator ); ?>><?php _e( 'is', 'advanced-ads-pro' ); ?></option>
			<option value="is_not" <?php selected( 'is_not', $operator ); ?>><?php _e( 'is not', 'advanced-ads-pro' ); ?></option>
		</select>
		<div class="advads-conditions-select-wrap"><select name="<?php echo $name; ?>[value]">
			<option><?php _e( '-- choose one --', 'advanced-ads-pro' ); ?></option>
			<?php foreach( $roles as $_role => $_display_name ) : ?>
			<option value="<?php echo $_role; ?>" <?php selected( $_role, $value ); ?>><?php echo $_display_name; ?></option>
			<?php endforeach; ?>
		</select></div>
		<p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}

	/**
	 * callback to display the "browser language" condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_browser_lang( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

	    // options
	    $operator = isset( $options['operator'] ) ? $options['operator'] : 'is';
	    $value = isset( $options['value'] ) ? $options['value'] : '';

	    // load browser languages
	    include plugin_dir_path( __FILE__ ) . 'inc/browser_langs.php';
	    if( isset( $advads_browser_langs )){
		asort( $advads_browser_langs );
	    }

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<select name="<?php echo $name; ?>[operator]">
			<option value="is" <?php selected( 'is', $operator ); ?>><?php _e( 'is', 'advanced-ads-pro' ); ?></option>
			<option value="is_not" <?php selected( 'is_not', $operator ); ?>><?php _e( 'is not', 'advanced-ads-pro' ); ?></option>
	    </select>
		<select name="<?php echo $name; ?>[value]">
			<option><?php _e( '-- choose one --', 'advanced-ads-pro' ); ?></option>
			<?php if( isset( $advads_browser_langs )) :
			    foreach( $advads_browser_langs as $_key => $_title ) : ?>
				<option value="<?php echo $_key; ?>" <?php selected( $_key, $value ); ?>><?php echo $_title; ?></option>
			<?php endforeach;
			endif; ?>
		</select>
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}


	/**
	 * callback to display the "cookie" condition
	 *
	 * @param arr    $options Options of the condition.
	 * @param int    $index Index of the conditionA.
	 * @param string $form_name Name of the form, falls back to class constant.
	 */
	static function metabox_cookie( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

		$operator = isset( $options['operator'] ) ? self::maybe_replace_cookie_operator( $options['operator'] ) : 'contain';

	    // options
		$cookie = isset( $options['cookie'] ) ? $options['cookie'] : ''; // Cookie name.
		// the value may be slashed if displayed in placement, we also need to convert to htmlentities to display `"`
		$value = isset( $options['value'] ) ? htmlentities( wp_unslash( $options['value'] ) ) : '';

		ob_start();
		if ( 0 <= version_compare( ADVADS_VERSION, '1.9.1' ) ) {
			include( ADVADS_BASE_PATH . 'admin/views/ad-conditions-string-operators.php' );
		}
		$operatoroption = ob_get_clean();

		$cookieoption = '<input type="text" name="' . $name . '[cookie]" value="' . $cookie . '" placeholder="' . __( 'Cookie Name', 'advanced-ads-pro' ) . '"/>';
		$valueoption = '<input type="text" name="' . $name . '[value]" value="' . $value . '" placeholder="' . __( 'Cookie Value', 'advanced-ads-pro' ) . '"/>';

		?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>[type]" value="<?php echo esc_attr( $options['type'] ); ?>"/>
		<div class="advads-condition-line-wrap">
		<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		/* 1: Cookie Name, 2: Operator, 3: Cookie Value */
		printf( '%1$s %2$s %3$s', $cookieoption, $operatoroption, $valueoption );
		// phpcs:enable
		?>
		</div>
		<div class="clear"></div>
		<p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?> <?php esc_html_e( 'Set the operator to “match/does not match” and leave the value empty to check only the existence of the cookie.', 'advanced-ads-pro' ); ?></p><?php
	}

	/**
	 * callback to display the condition for ad impressions in a specific time frame
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_ad_impressions( $options, $index = 0, $form_name = '' ) {

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
		$name = self::get_form_name_with_index( $form_name, $index );

	    // options
	    $value = isset( $options['value'] ) ? absint( $options['value'] ) : 0;
	    $timeout = isset( $options['timeout'] ) ? absint( $options['timeout'] ) : 0;

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<input type="number" required="required" min="0" name="<?php echo $name; ?>[value]" value="<?php echo absint( $value ); ?>"/>
		<?php
		$impressions_field = '<input type="number" required="required" min="0" name="' . $name . '[timeout]" value="' . $timeout . '"/>';
		printf( __( 'within %s seconds', 'advanced-ads-pro' ), $impressions_field ); ?>
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}


	/**
	 * check referrer url in frontend
	 *
	 * @since 1.0.1
	 * @param array $options Options of the condition.
	 * @return bool true if ad can be displayed
	 */
	static function check_referrer_url( $options = array() ){

		// check if session variable is set
		if ( ! isset( $_COOKIE[ self::REFERRER_COOKIE_NAME ] ) ) {
			return false;
		}
		$referrer = self::extract_cookie_data( $_COOKIE[ self::REFERRER_COOKIE_NAME ] );

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $referrer, $options );
	}

	/**
	 * check user agent in frontend
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_user_agent( $options = array() ){

		// check if session variable is set
		$user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? $_SERVER[ 'HTTP_USER_AGENT' ] : '';

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $_SERVER[ 'HTTP_USER_AGENT' ], $options );
	}

	/**
	 * check user capabilities in frontend
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_capabilities( $options = array() ){

		if ( ! isset( $options['value'] ) || '' === $options['value'] || ! isset( $options['operator'] ) ){
			return true;
		}

		switch ( $options['operator'] ){
		    case 'can' :
			    return ( current_user_can( $options['value'] ) );
			    break;
		    case 'can_not' :
			    return ( ! current_user_can( $options['value'] ) );
		}

		return true;
	}

	/**
	 * Check user roles in frontend.
	 *
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_roles( $options = array() ){
		if ( ! isset( $options['value'] ) || '' === $options['value'] || ! isset( $options['operator'] ) ){
			return true;
		}

		$user = wp_get_current_user();
		if ( ! is_array( $user->roles ) ) {
			return false;
		}

		switch ( $options['operator'] ) {
			case 'is' :
				return ( in_array( $options['value'], $user->roles, true ) );
				break;
			case 'is_not' :
				return ! ( in_array( $options['value'], $user->roles, true ) );
		}

		return true;
	}

	/**
	 * check browser language
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_browser_lang( $options = array() ){

		if ( ! isset( $options['value'] ) || '' === $options['value'] ){
			return true;
		}

		if ( ! isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) || '' === $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) {
			return false;
		}

		// check if the browser lang is within the accepted language string
		$regex = "@\b" . $options['value'] . "\b@i"; // \b checks for "whole words"
		$result = preg_match( $regex, $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) === 1;

		if ( isset( $options['operator'] ) && $options['operator'] === 'is_not' ) {
			return ! $result;
		} else {
			return $result;
		}
	}

	/**
	 * check cookie value in frontend
	 *
	 * @param array $options Options of the condition.
	 *
	 * @return bool true if ad can be displayed
	 * @since 1.1.1
	 */
	public static function check_cookie( $options = array() ) {
		if ( isset( $options['operator'] ) ) {
			$options['operator'] = self::maybe_replace_cookie_operator( $options['operator'] );
		}

		$must_be_set = ! isset( $options['operator'] ) || 'match_not' !== $options['operator'];

		// Check if cookie option exists.
		if ( empty( $options['cookie'] ) || empty( $options['value'] ) ) {
			return $must_be_set;
		}

		// check if there are cookies.
		if ( empty( $_SERVER['HTTP_COOKIE'] ) ) {
			return ! $must_be_set;
		}

		// Get the raw cookie keys and values; the superglobal $_COOKIE holds manipulated keys and values.
		$raw_cookies = array_reduce( explode( ';', $_SERVER['HTTP_COOKIE'] ), static function( $carry, $item ) {
			$cookie_pair = explode( '=', $item, 2 );
			if ( count( $cookie_pair ) !== 2 ) {
				return $carry;
			}
			$carry[ trim( $cookie_pair[0] ) ] = urlencode( urldecode( wp_unslash( trim( $cookie_pair[1] ) ) ) );

			return $carry;
		}, [] );

		// check if the cookie exists.
		if ( ! isset( $raw_cookies[ $options['cookie'] ] ) ) {
			return ! $must_be_set;
		}

		// de- and then encode the value, this catches values the user entered decoded and encoded.
		$options['value'] = urlencode( urldecode( wp_unslash( $options['value'] ) ) );

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $raw_cookies[ $options['cookie'] ], $options );
	}

	/**
	 * check page_impressions in frontend
	 *
	 * @since 1.1.1
	 * @param array $options Options of the condition.
	 * @return bool true if ad can be displayed
	 */
	static function check_page_impressions( $options = array() ){
	    if ( ! isset( $options['operator'] ) || ! isset( $options['value'] ) ) {
			return true;
	    }

	    $impressions = 0;
	    if ( isset( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) ) {
			$impressions = absint( self::extract_cookie_data( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) );
	    } else {
			return false;
	    }

	    $value = absint( $options['value'] );

	    switch ( $options['operator'] ){
		    case 'is_equal' :
			    if ( $value !== $impressions ) { return false; }
			    break;
		    case 'is_higher' :
			    if ( $value > $impressions ) { return false; }
			    break;
		    case 'is_lower' :
			    if ( $value < $impressions ) { return false; }
			    break;
	    }

	    return true;
	}

	/**
	 * check ad impressions limit for the ad in frontend
	 *
	 * @since 1.2.4
	 * @param arr $options options of the condition
	 * @param obj $ad Advanced_Ads_Ad
	 * @return bool true if ad can be displayed
	 */
	static function check_ad_impressions( $options = array(), $ad = false ){

	    if ( ! $ad instanceof Advanced_Ads_Ad || ! isset( $options['value'] ) || ! isset( $options['timeout'] ) ) {
			return true;
	    }

	    $value = absint( $options['value'] );
	    $impressions = 0;
	    $cookie_name = self::AD_IMPRESSIONS_COOKIE_NAME . '_' . $ad->id;
	    $cookie_timeout_name = $cookie_name . '_timeout';

	    if ( isset( $_COOKIE[ $cookie_name ] ) && isset( $_COOKIE[ $cookie_timeout_name ] )) {
		$impressions = absint( $_COOKIE[ $cookie_name ]  );
		if ( $value <= $impressions ) { return false; }
	    }

	    return true;
	}

	/**
	 * check new_visitor in frontend
	 *
	 * @since 1.1.1
	 * @param array $options Options of the condition.
	 * @return bool true if ad can be displayed
	 */
	static function check_new_visitor( $options = array() ){
	    if ( ! isset( $options['operator'] ) ) {
			return true;
	    }

	    $impressions = 0;
	    if ( isset( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) ) {
			$impressions = absint( self::extract_cookie_data( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) );
	    }

	    switch ( $options['operator'] ){
		    case 'is' :
			    return 1 === $impressions;
			    break;
		    case 'is_not' :
			    return 1 < $impressions;
			    break;
	    }

	    return true;
	}

	/**
	 * Get capability information to use in passive cache-busting.
	 */
	static function get_passive_capability( $options = array() ) {
		if ( ! isset( $options['value'] ) ) {
			return;
		}
		$userdata = get_userdata( get_current_user_id() );
		if ( ! empty( $userdata->allcaps ) && is_array( $userdata->allcaps ) && ! empty( $userdata->allcaps[ $options['value'] ] ) ) {
			return $options['value'];
		}
	}

	/**
	 * Get role information to use in passive cache-busting.
	 */
	static function get_passive_role( $options = array() ) {
		if ( ! isset( $options['value'] ) ) {
			return;
		}
		$user = wp_get_current_user();
		if ( ! empty( $user->roles ) && is_array( $user->roles ) && in_array( $options['value'], $user->roles ) ) {
			return $options['value'];
		}
	}

	/**
	 * Inject ad output and js code.
	 *
	 * @since 1.2.4
	 * @param string          $content Ad content.
	 * @param Advanced_Ads_Ad $ad Ad object.
	 */
	public function after_ad_output( $content = '', Advanced_Ads_Ad $ad ) {
		// Do not enqueue on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return $content;
		}
		$options = $ad->options( 'visitors' );
		if( is_array( $options )) foreach( $options as $_visitor_condition ){
			if( isset( $_visitor_condition['type'] )){
				switch( $_visitor_condition['type'] ){
					// set limit and timeout for max_ad_impressions visitor condition
					case 'ad_impressions' :
					    $limit = isset( $_visitor_condition['value'] ) ? $_visitor_condition['value'] : '';
					    $timeout = isset( $_visitor_condition['timeout'] ) ? $_visitor_condition['timeout'] : '';
					    $timeout = ( $timeout ) ? $timeout : '""';
					    // cookie names
					    $cookie_name = self::AD_IMPRESSIONS_COOKIE_NAME . '_' . $ad->id;
					    $cookie_timeout_name = $cookie_name . '_timeout';
					    // get current count, if timeout not reached yet
					    $count = ( isset( $_COOKIE[ $cookie_name ] ) && isset( $_COOKIE[ $cookie_timeout_name ] ) ) ? $_COOKIE[ $cookie_name ] : 1;

					    $content .= '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {'
						    . 'if( advads.get_cookie( "' . $cookie_timeout_name . '" ) ) {'
						    . 'if( advads.get_cookie( "' . $cookie_name . '" ) ) {'
						    . 'var ' . $cookie_name . ' = parseInt( advads.get_cookie( "' . $cookie_name . '" ) ) + 1;'
						    . '} else { var ' . $cookie_name . ' = 1; }'
						    . '} else {'
						    . 'var ' . $cookie_name . ' = 1;'
						    . 'advads.set_cookie_sec("' . $cookie_timeout_name . '", "true", ' . $timeout . ' );'
						    . '}'
						    . 'advads.set_cookie_sec("' . $cookie_name . '", ' . $cookie_name . ', ' . $timeout . ' );';
					    $content .= '});</script>';
					    break;
				}
			}
		}
		return $content;
	}

	/**
	 * Helper function to the name of a form field.
	 * falls back to default
	 *
	 * @param string $form_name form name if submitted.
	 * @param int    $index index of the condition.
	 *
	 * @return string
	 */
	public static function get_form_name_with_index( $form_name = '', $index = 0 ) {
		// form name basis
		if ( method_exists( 'Advanced_Ads_Visitor_Conditions', 'get_form_name_with_index' ) ) {
			return Advanced_Ads_Visitor_Conditions::get_form_name_with_index( $form_name, $index );
		} else {
			return Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';
		}
	}

	/**
	 * Replace operator name to ensure backward compatibility.
	 *
	 * @param string $operator Operator name.
	 * @return string $operator Operator name.
	 */
	private static function maybe_replace_cookie_operator( $operator ) {
		$replace = array(
			'show' => 'match',
			'hide' => 'match_not'
		);
		return isset( $replace[ $operator ] ) ? $replace[ $operator ] : $operator;
	}

	/**
	 * Extract cookie data from a stringified cookie.
	 *
	 * @param string $cookie {
	 *     A stringified cookie.
	 *
	 *     @type string $data Cookie data.
	 *     @type string $expire Expiration time.
	 * }
	 * @return mixed The data field on success, original stringified cookie on error.
	 */
	private static function extract_cookie_data( $cookie ) {
		$cookie_array = json_decode( wp_unslash( $cookie ), true );

		if (
			! is_array( $cookie_array )
			|| ! array_key_exists( 'data', $cookie_array )
		) {
			return $cookie;
		}

		return $cookie_array['data'];
	}
}
