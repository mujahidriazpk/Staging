<?php

/**
 * this class combines all functions added when the external click tracking feature is enabled
 */
class Advanced_Ads_Tracking_External_Clicks {

    const TABLE_EVENTS_BASENAME = 'advads_events';
    const TABLE_CLIENT_BASENAME = 'advads_clients';
    const SIGNATURE_HASH_METHOD = 'sha256';
    const TRACK_EVENT = 'advanced-ads-tracking-event';
    const DB_VERSION = '1.0';

    public function __construct() {

	global $wpdb;

	$this->event_table =        $wpdb->prefix . self::TABLE_EVENTS_BASENAME;
	$this->client_table =       $wpdb->prefix . self::TABLE_CLIENT_BASENAME;

	if( defined( 'DOING_AJAX' ) ) {
	    add_action('wp_ajax_nopriv_' . self::TRACK_EVENT, array($this, 'track_external_event')); // frontend, not logged in
	} elseif (is_admin()) {
	    $this->create_tables();
	    add_action('advanced-ads-submenu-pages', array($this, 'add_menu_item'));
	} else {
	}

    }

    /**
     * add click tracking submenu item
     *
     * @since 1.2.10
     * @param string $plugin_slug
     */
    public function add_menu_item($plugin_slug = '') {

	$cap = method_exists('Advanced_Ads_Plugin', 'user_cap') ? Advanced_Ads_Plugin::user_cap('advanced_ads_edit_ads') : 'manage_options';

	add_submenu_page(
		$plugin_slug, __('Advertisement Event-Tracking', 'advanced-ads-tracking'), __('Events', 'advanced-ads-tracking'), $cap, $plugin_slug . '-tracking-events', array($this, 'display_events_page')
	);
    }

    protected function display_events_page_preview_client_script( $client_id ) {
	global $wpdb;

	// $template = AAT_BASE_PATH . 'admin/views/event.php?action=' . Advanced_Ads_Tracking_Ajax::TRACK_EVENT;
	$template = AAT_BASE_PATH . 'admin/views/event.php';
	$template = file_get_contents($template);

	$vars = array(
	    '%%API_CLIENT_TOKEN%%' => '%%API_CLIENT_TOKEN%%',
	    '%%API_CLIENT_NAME%%' => '%%API_CLIENT_NAME%%',
	    '%%API_TARGET_URI%%' => admin_url('admin-ajax.php'),
	    '%%API_AGENT%%' => 'advads api client',
	    '%%API_VERSION%%' => '1.0-beta',
	    '%%API_HASH_METHOD%%' => self::SIGNATURE_HASH_METHOD,
	    '%%API_ACTION%%' => self::TRACK_EVENT,
	);

	// look for client
	$client_table = $this->get_client_table();
	$query = "SELECT ct.name AS name, ct.token AS token FROM $client_table ct WHERE ct.id=%d LIMIT 1";
	$query = sprintf($query, $client_id);
	$client = $wpdb->get_row($query, ARRAY_N);
	if (isset($client[1])) {
	    $vars['%%API_CLIENT_TOKEN%%'] = $client[1];
	    $vars['%%API_CLIENT_NAME%%'] = $client[0];
	}

	// render template
	$template = str_replace(array_keys($vars), $vars, $template);

	// display events view
	include AAT_BASE_PATH . 'admin/views/events_client_script.php';
    }

    /**
     * render the dashboard page for event tracking
     *
     * @global type $wpdb
     * @return type
     */
    public function display_events_page() {
	global $wpdb;
	$messages = array(); // response message buffer

	if (isset($_GET['preview_client_id']) && is_numeric($_GET['preview_client_id'])) {
	    return $this->display_events_page_preview_client_script( $_GET['preview_client_id']);
	}

	if ($_POST !== array()) {
	    if (isset($_POST['create_client']) && isset($_POST['client_name'])) {
		$created_client = $this->create_client($_POST['client_name']);
		$messages[] = $created_client ? __( "Client created", 'advanced-ads-tracking' ) : __( "Failed to create client.", 'advanced-ads-tracking' );
	    } else {
		// TODO: handle client edits (especially inactivation)
	    }
	    // -TODO refresh page to get rid of POST context
	}

	// clients can access the event tracking by API
	// they are used to generate a client scripts
	// access is restrictable
	$client_table = $this->get_client_table();
	$event_table = $this->get_event_table();
	$reference_time = time() - DAY_IN_SECONDS; // last 24 hours; mostly
	// query clients
	$query = "SELECT ct.id AS id, ct.name AS name, ct.token AS token, ct.expired AS expired FROM $client_table ct";
	$clients = $wpdb->get_results($query, ARRAY_A);
	$clients_by_id = array();
	foreach ($clients as $client) {
	    $clients_by_id[$client['id']] = $client;
	}
	$clients = $clients_by_id;
	unset($clients_by_id);

	// query stats
	/* $query = "SELECT et.client AS id, COUNT(et.client) AS event_count FROM $event_table et " .
		"WHERE et.time > $reference_time GROUP BY et.client";
	$times = $wpdb->get_results($query, ARRAY_A);
	foreach ($times as $time) {
	    if (isset($clients[$time['id']])) {
		$clients[$time['id']]['event_count'] = $time['event_count'];
	    }
	}*/

	$query = "SELECT et.event_id as event_id, et.client AS client_id, et.event_time AS event_time, et.event_meta AS event_meta FROM $event_table et ORDER BY event_time";
	$events = $wpdb->get_results($query, ARRAY_A);
	foreach ($events as $_event) {
	    if (isset($clients[$_event['client_id']])) {
		$clients[$_event['client_id']]['events'][] = array(
		    'event_time' => $_event['event_time'],
		    'event_id' => $_event['event_id'],
		    'event_meta' => json_decode( $_event['event_meta'] )
		);
	    }
	}

	if (count($messages)) {
	    include AAT_BASE_PATH . 'admin/views/events_messages.php';
	}

	// display events view
	include AAT_BASE_PATH . 'admin/views/events.php';
    }

    /**
     * create tables when missing
     *
     * @since 1.2.10
     * @link http://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function create_tables() {

	global $wpdb;

	$event_table = $this->get_event_table();
	$client_table = $this->get_client_table();
	$charset_collate = $wpdb->get_charset_collate();

	$options = Advanced_Ads_Tracking_Plugin::get_instance()->options();
        if (!is_array($options)) {
            return false;
        }

        $sql = array();
        if ( ! isset( $options['dbversion_events'] ) ) {
            $options['dbversion_events'] = '0';
        }

        // handle diffs incrementally
        switch ($options['dbversion_events']) {
            case '0':
		$sql = array();
		$sql[] = "CREATE TABLE IF NOT EXISTS $event_table (
			`time` int unsigned NOT NULL,
			`client` smallint unsigned NOT NULL,
			`event_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
			`event_time` int unsigned NOT NULL,
			`event_meta` blob NOT NULL,
			INDEX `timestamp` (`time`,`client`, `event_id`)
		    ) ENGINE = MyISAM $charset_collate";
		$sql[] = "CREATE TABLE IF NOT EXISTS $client_table (
			`id` smallint unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
			`token` char(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
			`expired` int unsigned DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `name` (`name`)
		    ) ENGINE = MyISAM $charset_collate";
	}

	// if tables are not created, check if this is the cause:
	// https://make.wordpress.org/core/2015/04/02/the-utf8mb4-upgrade/

	if ($sql !== array()) {
            foreach ($sql as $query) {
                $wpdb->query( $query );
		if( $wpdb->last_error ){
		    error_log( 'Advanced Ads Tracking – Event Tracker Module: ' . $wpdb->last_error );
		}
            }
            // add database version number to options
            $options['dbversion_events'] = self::DB_VERSION;
            Advanced_Ads_Tracking_Plugin::get_instance()->update_options( $options );
        }
    }

    /**
     * Read client detail from database.
     *
     * @param string $name
     * @return stdClass
     */
    public function get_client_by_name($name) {
	global $wpdb;

	$table = $this->get_client_table();
	$query = $wpdb->prepare("SELECT id, token FROM $table WHERE expired IS NULL and name = %s LIMIT 1", $name);

	return $wpdb->get_row($query);
    }

    /**
     * Create a new client - if non exists for that name.
     *
     * @param string $name
     *
     * @return stdClass|false
     */
    public function create_client($name) {
	global $wpdb;

	$table = $this->get_client_table();
	$token = sha1(uniqid(mt_rand(100000, 999999), true));
	$inserted = $wpdb->insert($table, array('name' => $name, 'token' => $token));

	$query = "SELECT count(id) FROM $table WHERE name=%s";
	$query = $wpdb->prepare($query, array($name));

	return $wpdb->get_col($query) > 0;
    }

    /**
     * Track special events (like sales).
     *
     * @param integer $client
     * @param string  $event_id
     * @param integer $event_time
     * @param integer $event_price
     * @param string  $event_price_currency
     * @param integer $event_provision
     * @param string  $event_provision_currency
     */
    public function save_event($client, $event_id, $event_time, $event_price = null, $event_price_currency = null, $event_provision, $event_provision_currency) {
	global $wpdb;
	$now = time();
	$table = $this->get_event_table();

	$meta = array(
	    'price' => $event_price ? sprintf('%.4f', $event_price) : null,
	    'price_currency' => $event_price ? substr(preg_replace('/[^A-Z]/ui', '', mb_strtoupper($event_price_currency)), 0, 3) : null,
	    'provision' => $event_provision ? sprintf('%.4f', $event_provision) : null,
	    'provision_currency' => $event_provision ? substr(preg_replace('/[^A-Z]/ui', '', mb_strtoupper($event_provision_currency)), 0, 3) : null,
	);
	$meta = json_encode($meta);
	$query = $wpdb->prepare(
		"INSERT INTO $table (`time`, `client`, `event_id`, `event_time`, `event_meta`) VALUES (%d, %d, %s, %d, %s)", $now, $client, $event_id, $event_time, $meta
	);

	return $wpdb->query($query) > 0;
    }

    public function get_event_table() {
	return $this->event_table;
    }

    public function get_client_table() {
	return $this->client_table;
    }

    /**
     * finally track the external click event
     *
     * @global type $wpdb
     */
    public function track_external_event() {

	// set some headers to avoid caching and other
	// hoping wordpress does not break them (which is not ensured to be avoidable)
	$headers = array(
	    'X-Content-Type-Options: nosniff',
	    'Cache-Control: no-cache, must-revalidate, max-age=0, smax-age=0', // HTTP/1.1
	    'Expires: Sat, 26 Jul 1997 05:00:00 GMT', // deprecated
	    'X-Accel-Expires: 0',
	);
	foreach ($headers as $header) {
	    @header($header, true);
	}

	// ensure wp is muted
	ob_start();
	// the remainder without browser interaction
	ignore_user_abort(true); // do not stop when user ended the connection
	set_time_limit(60); // try to avoid bad conditions

	// process base data
	$payload = isset($_POST['data']) ? stripslashes($_POST['data']) : null;
	$source = isset($_POST['source']) ? $_POST['source'] : null;
	$signature = isset($_POST['signature']) ? $_POST['signature'] : null;
	$time = isset($_POST['time']) ? $_POST['time'] : null;

	// process payload
	$event = $payload ? json_decode($payload) : array();

	$id = isset($event->id) ? $event->id : null;
	$price = isset($event->price) ? $event->price : null;
	$priceCurrency = isset($event->priceCurrency) ? $event->priceCurrency : null;
	$provision = isset($event->provision) ? $event->provision : null;
	$provisionCurrency = isset($event->provisionCurrency) ? $event->provisionCurrency : null;

	$client = $this->get_client_by_name($source);

	if (!$id || !$time || !$source || !$signature || !$client) {
	    header("HTTP/1.0 400 Bad Request");
	    exit(1);
	}

	// validate signature
	$expectedSignature = hash_hmac(
		self::SIGNATURE_HASH_METHOD, "$source $time $payload", $client->token
	);
    
	// compare expected and retrieved signature
	if ( ! $this->check_hashes($expectedSignature, $signature)) {
	    header("HTTP/1.0 401 Unauthorized");
	    exit(0);
	}

	// track event
	$success = $this->save_event($client->id, $id, $time, $price, $priceCurrency, $provision, $provisionCurrency);
	ob_end_clean();

	// try to respond using headers (not ensured to work)
	if (!$success) {
	    global $wpdb;
	    $error = $wpdb->last_error;
	    error_log("Failed to track event with message: ${error}", E_WARNING);

	    echo '{ "error": "Failed to track event. Try again later." }';
	    header("HTTP/1.0 503 Service Unavailable");
	    exit(2);
	}

	// success
	header("HTTP/1.0 202 Accepted");
	exit(0);
    }

    /**
     *  Implementation of hash_equals for max PHP version compatibility.
     *  
     *  @param str $str1 known string
     *  @param str $str2 user string
     *  
     *  @return boolean
     */
    public function check_hashes($str1, $str2) {
		if (strlen($str1) != strlen($str2)) {
		    return false;
		} else {
		    $res = $str1 ^ $str2;
		    $ret = 0;
		    for ($i = strlen($res) - 1; $i >= 0; $i--)
			$ret |= ord($res[$i]);
		    return !$ret;
		}
    }
}
