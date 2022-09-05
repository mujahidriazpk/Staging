<?php
/**
 * Dashboard class for GAM
 */

class Advanced_Ads_Gam_Admin {

	/**
	 * The unique instance of this class
	 *
	 * @var Advanced_Ads_Gam_Admin
	 */
	private static $instance;

	/**
	 * Link to plugin page
	 *
	 * @const
	 */
	const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/google-ad-manager/';

	/**
	 * Where to redirect users after they Authorize the application.
	 */
	const API_REDIRECT_URI = 'https://gam-connect.wpadvancedads.com/oauth.php';

	/**
	 * Google API version
	 *
	 * @const
	 */
	const API_VERSION = 'v202105';

	/**
	 * Maximum supported ad count.
	 *
	 * @var integer.
	 */
	const MAX_UNIT_COUNT = 1500;

	/**
	 * All GAM ads.
	 *
	 * @var array
	 */
	private $all_gam_ads;

	/**
	 * Private constructor
	 */
	private function __construct() {
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'wp_ajax_advads_gamapi_confirm_code', array( $this, 'ajax_confirm_api_code' ) );
		add_action( 'wp_ajax_advads_gamapi_revoke', array( $this, 'ajax_revoke_tokken' ) );
		add_action( 'wp_ajax_advads_gamapi_account_selected', array( $this, 'ajax_account_selected' ) );

		add_action( 'wp_ajax_advads_gamapi_test_the_api', array( $this, 'test_if_api_enabled' ) );
		add_action( 'wp_ajax_advads_gamapi_get_key', array( $this, 'get_api_key' ) );

		add_action( 'wp_ajax_advads_gamapi_getnet', array( $this, 'ajax_get_all_networks' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );

		add_action( 'advanced-ads-settings-init', array( $this, 'settings_init' ), 10, 1 );

		add_filter( 'plugin_action_links_' . AAGAM_BASE, array( $this, 'add_plugin_link' ) );

		add_action( 'advanced-ads-submenu-pages', array( $this, 'add_submenu_link' ) );

		add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );

		add_action( 'save_post_advanced_ads', array( $this, 'save_ad' ) );

	}

	/**
	 * Save key value targeting post meta
	 *
	 * @param int $post_id the current post ID.
	 */
	public function save_ad( $post_id ) {
		$post_vars = wp_unslash( $_POST );
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads' ) ) // only use for ads, no other post type.
			 || ! isset( $post_vars['post_type'] )
			 || Advanced_Ads::POST_TYPE_SLUG != $post_vars['post_type']
			 || ! isset( $post_vars['advanced_ad']['type'] )
			 || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$the_ad     = new Advanced_Ads_Ad( $post_id );
		$ad_options = $the_ad->options();
		if ( isset( $post_vars['advanced_ad']['gam'] ) && isset( $post_vars['advanced_ad']['gam']['key'] ) ) {
			$keyval = array();
			foreach ( $post_vars['advanced_ad']['gam']['key'] as $key => $value ) {
				if ( isset( $post_vars['advanced_ad']['gam']['value'] ) && is_array( $post_vars['advanced_ad']['gam']['value'] ) ) {
					$keyval[] = array(
						'type'       => $post_vars['advanced_ad']['gam']['type'][ $key ],
						'key'        => $value,
						'value'      => $post_vars['advanced_ad']['gam']['value'][ $key ],
						'onarchives' => $post_vars['advanced_ad']['gam']['onarchives'][ $key ],
					);
				}
			}
			$ad_options['gam-keyval'] = $keyval;

			Advanced_Ads_Ad::save_ad_options( $post_id, $ad_options );
		} else {
			if ( isset( $ad_options['gam-keyval'] ) ) {
				unset( $ad_options['gam-keyval'] );
				Advanced_Ads_Ad::save_ad_options( $post_id, $ad_options );
			}
		}
	}

	/**
	 * Get key values types with their name and the selector markup
	 *
	 * @return array $kvs all key values types.
	 */
	public function get_key_values_types() {
		$kvs = array(
			'custom' => array(
				'name' => esc_html__( 'Custom key', 'advanced-ads-gam' ),
				'html' => '<input type="text" id="advads-gam-kv-value-input" />',
			),
		);

		$kvs['post_types'] = array(
			'name' => esc_html__( 'Post types', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . sprintf( esc_html( 'Post type slug. E.g., %s or %s.', 'advanced-ads-gam' ), '<code>post</code>', '<code>page</code>' ) . '</span>',
		);

		$kvs['page_slug'] = array(
			'name' => esc_html__( 'Page slug', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . esc_html( 'Slug of the current post, page, or archive.', 'advanced-ads-gam' ) . '</span>',
		);

		$kvs['page_type'] = array(
			'name' => esc_html__( 'Page type', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . sprintf(
				esc_html( '%s, %s, %s (if the front page lists your posts), or %s (on the blog page).', 'advanced-ads-gam' ),
				'<code>single</code>',
				'<code>archive</code>',
				'<code>home</code>',
				'<code>blog</code>'
			) . '</span>',
		);

		$kvs['page_id'] = array(
			'name' => esc_html__( 'Page ID', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . esc_html__( 'ID of the current post or page.', 'advanced-ads-gam' ) . '</span>',
		);

		$kvs['placement_id'] = array(
			'name' => esc_html__( 'Placement ID', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . esc_html__( 'ID of the Advanced Ads placement.', 'advanced-ads-gam' ) . '</span>',
		);

		$kvs['postmeta'] = array(
			'name' => esc_html__( 'Post meta', 'advanced-ads-gam' ),
			'html' => '<input type="text" id="advads-gam-kv-value-input" /><br><span class="description">' . esc_html__( 'Enter the post meta "meta_key" as a value.', 'advanced-ads-gam' ) . '</span>',
		);

		$kvs['usermeta'] = array(
			'name' => esc_html__( 'User meta', 'advanced-ads-gam' ),
			'html' => '<input type="text" id="advads-gam-kv-value-input" /><br><span class="description">' . esc_html__( 'Enter the user meta "meta_key" as a value.', 'advanced-ads-gam' ) . '</span>',
		);

		$kvs['taxonomy'] = array(
			'name' => esc_html__( 'Taxonomy', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . esc_html__( 'Taxonomy of archive pages.', 'advanced-ads-gam' ) . '</span>',
		);

		$kvs['terms'] = array(
			'name' => esc_html__( 'Categories/Tags/Terms', 'advanced-ads-gam' ),
			'html' => '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' . esc_html__( 'Any terms on single pages, including categories and tags.', 'advanced-ads-gam' ) . '</span>' .
						'<p class="description"><input type="checkbox" class="onarchives" name="advanced_ad[gam][onarchives][]" value="1">' . esc_html__( 'send also on archive pages', 'advanced-ads-gam' ) . '</span>',
		);

		return $kvs;
	}

	/**
	 * Add links to the plugins list
	 *
	 * @param array $links array of links for the plugins, adapted when the current plugin is found.
	 *
	 * @return array $links.
	 */
	public function add_plugin_link( $links ) {
		if ( ! is_array( $links ) ) {
			return $links;
		}

		// add link to GAM settings.
		if ( ! Advanced_Ads_Network_Gam::get_instance()->is_account_connected() ) {
			$connect_link = '<a href="' . admin_url( 'admin.php?page=advanced-ads-settings#top#gam' ) . '">' . esc_html__( 'Connect to GAM', 'advanced-ads-gam' ) . '</a>';
			array_unshift( $links, $connect_link );
		}

		return $links;
	}

	/**
	 * Add menu link to connect to GAM when the connection was not made yet.
	 *
	 * @param $plugin_Slug slug used by WordPress to recognize the plugin.
	 */
	public function add_submenu_link( $plugin_Slug ) {
		if ( Advanced_Ads_Network_Gam::get_instance()->is_account_connected() ) {
			return;
		}

		global $submenu;
		if ( current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			// phpcs:ignore
			$submenu['advanced-ads'][] = array(
				__( 'Connect to GAM', 'advanced-ads-gam' ), // title.
				Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ), // capability.
				admin_url( 'admin.php?page=advanced-ads-settings#top#gam' ),
				__( 'Connect to GAM', 'advanced-ads-gam' ), // not sure what this is, but it is in the API.
			);
		}
	}

	/**
	 * Get API key if the plugin does not have one yet (or access has been revoked)
	 */
	public function get_api_key() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		$post_vars = wp_unslash( $_POST );
		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}
		$url  = 'https://gam-connect.wpadvancedads.com/api/getAPIKey.php';
		$site = site_url();

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'site' => $site,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json(
				array(
					'status' => false,
					'msg'    => 'error while getting api key',
					'raw'    => $response->get_error_message(),
				)
			);

		} else {
			$results = json_decode( $response['body'], true );
			if ( $results && isset( $results['key'] ) ) {
				update_option( AAGAM_API_KEY_OPTION, $results['key'] );
				wp_send_json( array( 'status' => true ) );
			} else {
				wp_send_json(
					array(
						'status' => false,
						'msg'    => 'incorrect response while getting api key',
						'raw'    => $response['body'],
					)
				);
			}
		}
	}

	/**
	 * Enqueue script on admin pages
	 *
	 * @param string $hook Current page hook.
	 */
	public function enqueue_script( $hook ) {
		$screen = get_current_screen();
		if ( Advanced_Ads::POST_TYPE_SLUG === $screen->id ) {
			// If on the ad edit page.
			wp_enqueue_style( 'advanced-ads-gam-ad-edit', AAGAM_BASE_URL . 'admin/css/ad-edit.css', array(), AAGAM_VERSION );
			wp_enqueue_script( 'advanced-ads-gam-key-value', AAGAM_BASE_URL . 'admin/js/key-value.js', array( 'jquery' ), AAGAM_VERSION, true );

			$i18n = array(
				'type'               => esc_html__( 'Type', 'advanced-ads-gam' ),
				'key'                => esc_html__( 'Key', 'advanced-ads-gam' ),
				'value'              => esc_html__( 'Value', 'advanced-ads-gam' ),
				'termsOnArchives'    => esc_html__( 'Any terms, including categories and tags. Enabled on single and archive pages.', 'advanced-ads-gam' ),
				'termsNotOnArchives' => esc_html__( 'Any terms, including categories and tags. Enabled on single pages.', 'advanced-ads-gam' ),
			);
			wp_add_inline_script( 'advanced-ads-gam-key-value', 'var advadsGamKvsi18n = ' . wp_json_encode( $i18n ) . ';' );
		}

		if ( Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			wp_enqueue_style( 'advanced-ads-gam/settings', AAGAM_BASE_URL . 'admin/css/settings.css', array(), AAGAM_VERSION );
			wp_enqueue_script( 'advanced-ads-gam/settings', AAGAM_BASE_URL . 'admin/js/ad-importer.js', array( 'jquery' ), AAGAM_VERSION, true );
		}
	}

	/**
	 * Get working access token
	 */
	public function get_access_token() {
		$option = Advanced_Ads_Network_Gam::get_option();
		if ( time() - 5 > $option['tokens']['expires'] ) {
			// Access token expired, renew it first.
			$new_token = $this->renew_access_token();
			if ( isset( $new_token['access_token'] ) ) {
				return $new_token['access_token'];
			} else {
				return false;
			}
		}

		// Token still valid.
		return $option['tokens']['access_token'];
	}

	// Renew access token.
	public function renew_access_token() {
		$creds         = self::get_api_creds();
		$cid           = $creds['id'];
		$cs            = $creds['secret'];
		$gam_option    = Advanced_Ads_Network_Gam::get_option();
		$refresh_token = $gam_option['tokens']['refresh_token'];
		$url           = 'https://www.googleapis.com/oauth2/v4/token';

		$args = array(
			'body' => array(
				'refresh_token' => $refresh_token,
				'client_id'     => $cid,
				'client_secret' => $cs,
				'grant_type'    => 'refresh_token',
			),
		);

		$response = wp_remote_post( $url, $args );
		$account  = $gam_option['account']['displayName'] . ' [' . $gam_option['account']['networkCode'] . ']';

		if ( is_wp_error( $response ) ) {
			return array(
				'status' => false,
				'msg'    => sprintf( esc_html__( 'Error while renewing access token for "%s"', 'advanced-ads-gam' ), $account ),
				'raw'    => $response->get_error_message(),
			);
		} else {
			$tokens = json_decode( $response['body'], true );
			if ( null !== $tokens && isset( $tokens['expires_in'] ) ) {
				$expires                              = time() + absint( $tokens['expires_in'] );
				$gam_option['tokens']['access_token'] = $tokens['access_token'];
				$gam_option['tokens']['expires']      = $expires;
				Advanced_Ads_Network_Gam::update_option( $gam_option );

				return array(
					'status'       => true,
					'access_token' => $tokens['access_token'],
				);
			} else {
				return array(
					'status' => false,
					'msg'    => sprintf( esc_html__( 'Invalid response received while renewing access token for "%s"', 'advanced-ads-gam' ), $account ),
					'raw'    => $response['body'],
				);
			}
		}
	}

	/**
	 * Get all networks in an account using SoapClient
	 *
	 * @param string $token access token.
	 */
	private function soap_get_all_networks( $token ) {
		$post_vars    = wp_unslash( $_POST );
		$http_headers = array(
			'http' => array(
				'protocol_version' => 1.1,
				'header'           => 'Authorization:Bearer ' . $token . "\r\n",
			),
		);

		$context = stream_context_create( $http_headers );
		$params  = array(
			'stream_context' => $context,
		);

		$wsdl = 'https://ads.google.com/apis/ads/publisher/' . self::API_VERSION . '/NetworkService?wsdl';
		$ns   = 'https://www.google.com/apis/ads/publisher/' . self::API_VERSION;

		$client = new SoapClient( $wsdl, $params );
		$req_h  = array(
			'ns1:applicationName' => AAGAM_APP_NAME,
		);

		$soap_var = new SoapVar( $req_h, SOAP_ENC_OBJECT, 'SoapRequestHeader', $ns );

		$soap_header = new SoapHeader(
			'https://www.google.com/apis/ads/publisher/' . self::API_VERSION,
			'RequestHeader',
			$soap_var
		);

		try {
			$net       = $client->__soapCall( 'getAllNetworks', array(), array(), array( $soap_header ) );
			$net_array = json_decode( json_encode( $net ), true );

			if ( empty( $net_array ) ) {
				// Empty account.
				$response = array(
					'status'   => false,
					'msg'      => esc_html__( 'No Ad Manager network found in this Google account', 'advanced-ads-gam' ),
					'raw'      => $net,
					'error_id' => 'empty_account',
				);
				return $response;
			} else {
				if ( isset( $post_vars['token_data'] ) ) {
					// From connect form.
					if ( isset( $net->rval ) && isset( $net->rval->networkCode ) ) {
						// Single network in account.
						$network = array(
							'id'                    => $net->rval->id,
							'networkCode'           => $net->rval->networkCode,
							'displayName'           => $net->rval->displayName,
							'currencyCode'          => $net->rval->currencyCode,
							'isTest'                => (bool) $net->rval->isTest,
							'effectiveRootAdUnitId' => $net->rval->effectiveRootAdUnitId,
						);

						$this->save_tokens( $post_vars['token_data'] );
						$gam_option            = Advanced_Ads_Network_Gam::get_option();
						$gam_option['account'] = $network;
						Advanced_Ads_Network_Gam::update_option( $gam_option );

						return array( 'status' => true );
					} else {
						if ( isset( $net->rval ) && is_array( $net->rval ) ) {
							// Multiple networks in account.
							$networks = array();
							foreach ( $net->rval as $network ) {
								$networks[] = array(
									'id'           => $network->id,
									'networkCode'  => $network->networkCode,
									'displayName'  => $network->displayName,
									'currencyCode' => $network->currencyCode,
									'isTest'       => (bool) $network->isTest,
									'effectiveRootAdUnitId' => $network->effectiveRootAdUnitId,
								);
							}

							return array(
								'status'     => false,
								'error_id'   => 'select_account',
								'token_data' => $post_vars['token_data'],
								'networks'   => $networks,
							);
						} else {
							// Unknown format.
							return array(
								'status'   => false,
								'networks' => $net,
							);
						}
					}
				} else {
					// Debug purpose.
					return (array) $net;
				}
			}
		} catch ( Exception $e ) {
			return array(
				'status' => false,
				'msg'    => $e->getMessage(),
			);
		}
	}

	/**
	 * Get all networks via the REST API
	 *
	 * @param array $token_data all token data from Google authorization screen.
	 */
	private function nosoap_get_all_networks( $token_data ) {
		$url    = 'https://gam-connect.wpadvancedads.com/api/getAllNetworks.php';
		$apikey = get_option( AAGAM_API_KEY_OPTION );
		$args   = array(
			'body' => array(
				'token_data' => $token_data,
				'apikey'     => $apikey,
			),
		);

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			wp_send_json(
				array(
					'status' => false,
					'msg'    => 'error while getting networks list',
					'raw'    => $response->get_error_message(),
				)
			);
		} else {
			$results = json_decode( $response['body'], true );
			if ( $results ) {

				if ( ! $results['status'] ) {
					wp_send_json( $results );
				} else {
					if ( isset( $results['network'] ) ) {
						$this->save_tokens( $token_data );

						$gam_option            = Advanced_Ads_Network_Gam::get_option();
						$gam_option['account'] = $results['network'];
						Advanced_Ads_Network_Gam::update_option( $gam_option );

						if ( isset( $results['apikey'] ) ) {
							update_option( AAGAM_API_KEY_OPTION, $results['apikey'] );
						}

						return array( 'status' => true );
					} elseif ( isset( $results['networks'] ) ) {
						if ( isset( $results['apikey'] ) ) {
							update_option( AAGAM_API_KEY_OPTION, $results['apikey'] );
						}
						return $results;
					}
				}
			} else {
				wp_send_json(
					array(
						'status' => false,
						'msg'    => 'unknown response format - getting networks list',
						'raw'    => $response['body'],
					)
				);
			}
		}
	}

	/**
	 * Get all networks associated with the access token
	 *
	 * Will use SOAP directly if supported or rely on the REST API.
	 */
	public function ajax_get_all_networks() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		$post_vars = wp_unslash( $_POST );
		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		if ( isset( $post_vars['token_data'] ) ) {
			$token = $post_vars['token_data']['access_token'];
		} else {
			$token = $this->get_access_token();
		}

		if ( false === $token ) {
			header( 'Content-Type: application/json' );
			echo wp_json_encode(
				array(
					'status' => false,
					'msg'    => 'no access token',
				)
			);
			die;
		}

		if ( self::has_soap() ) {
			$result = $this->soap_get_all_networks( $token );
			wp_send_json( $result );
		} else {
			$result = $this->nosoap_get_all_networks( $post_vars['token_data'] );
			wp_send_json( $result );
		}
	}

	/**
	 * Try to get ad units to check if API is not disabled in the user's account
	 */
	public function test_if_api_enabled() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}
		$post_vars = wp_unslash( $_POST );

		if ( isset( $post_vars['nonce'] ) && false === wp_verify_nonce( $post_vars['nonce'], 'gam-selector' ) && false === wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		$token = $this->get_access_token();
		if ( is_string( $token ) ) {
			if ( self::has_soap() ) {
				wp_send_json( $this->soap_test_the_api( $token ) );
			} else {
				wp_send_json( $this->nosoap_test_the_api( $token ) );
			}
		} else {
			wp_send_json(
				array(
					'status' => false,
					'msg'    => 'no access token',
				)
			);
		}
	}

	/**
	 * Test if API access is enabled using our REST API
	 *
	 * @param string $token the access token.
	 */
	private function nosoap_test_the_api( $token ) {
		$url = 'https://gam-connect.wpadvancedads.com/api/testTheAPI.php';

		$apikey     = get_option( AAGAM_API_KEY_OPTION );
		$gam_option = Advanced_Ads_Network_Gam::get_option();
		$network    = $gam_option['account']['networkCode'];
		$args       = array(
			'body' => array(
				'access_token' => $token,
				'network'      => $network,
				'apikey'       => $apikey,
			),
		);

		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			return array(
				'status' => false,
				'msg'    => 'error while testing the API',
				'raw'    => $response->get_error_message(),
			);
		} else {
			$results = json_decode( $response['body'], true );
			if ( $results && is_array( $results ) ) {
				return $results;
			} else {
				return ( array(
					'status' => false,
					'msg'    => 'unknown response format - testing the API',
					'raw'    => $response,
				) );
			}
		}
	}

	/**
	 * Test if API enabled using SoapClient
	 *
	 * @param string $token the access token.
	 */
	private function soap_test_the_api( $token ) {
		$http_headers = array(
			'http' => array(
				'protocol_version' => 1.1,
				'header'           => 'Authorization:Bearer ' . $token . "\r\n",
			),
		);

		$context = stream_context_create( $http_headers );
		$params  = array(
			'stream_context' => $context,
		);

		$gam_option = Advanced_Ads_Network_Gam::get_option();

		$location = 'https://ads.google.com/apis/ads/publisher/' . self::API_VERSION . '/InventoryService';
		$ns       = 'https://www.google.com/apis/ads/publisher/' . self::API_VERSION;

		$params['location'] = $location;
		$params['uri']      = $ns;
		$client             = new SoapClient( null, $params );
		$req_h              = array(
			'ns1:applicationName' => AAGAM_APP_NAME,
			'ns1:networkCode'     => $gam_option['account']['networkCode'],
		);

		$soap_header_var = new SoapVar( $req_h, SOAP_ENC_OBJECT, 'RequestHeader', $ns );
		$soap_header     = new SoapHeader(
			'https://www.google.com/apis/ads/publisher/' . self::API_VERSION,
			'RequestHeader',
			$soap_header_var
		);

		$units = array();
		$total = PHP_INT_MAX;

		$per_page = 200;

		$statement     = array( 'ns1:query' => 'LIMIT ' . $per_page . ' OFFSET 0' );
		$statement_var = new SoapVar( $statement, SOAP_ENC_OBJECT, 'Statement', $ns );

		try {
			$_units = $client->__soapCall(
				'getAdUnitsByStatement',
				array( new SoapParam( $statement_var, 'ns1:filterStatement' ) ),
				array(),
				array( $soap_header )
			);

			if ( self::MAX_UNIT_COUNT < $_units->totalResultSetSize ) {
				return array(
					'status' => false,
					'msg'    => 'TOO_MUCH_ADUNITS',
					'count'  => $_units->totalResultSetSize,
				);
			} else {
				return array(
					'status' => true,
					'count'  => $_units->totalResultSetSize,
				);
			}
		} catch ( Exception $e ) {
			return array(
				'status' => false,
				'msg'    => $e->getMessage(),
			);
		}

		return array(
			'status' => true,
			'count'  => $_units->totalResultSetSize,
		);
	}

	/**
	 * Get all ad units data using SoapClient
	 *
	 * @param string $token the access token.
	 */
	private function soap_get_ad_units( $token ) {
		$http_headers = array(
			'http' => array(
				'protocol_version' => 1.1,
				'header'           => 'Authorization:Bearer ' . $token . "\r\n",
			),
		);

		$context = stream_context_create( $http_headers );
		$params  = array(
			'stream_context' => $context,
			'trace'          => true,
		);

		$gam_option = Advanced_Ads_Network_Gam::get_option();
		$location   = 'https://ads.google.com/apis/ads/publisher/' . self::API_VERSION . '/InventoryService';
		$ns         = 'https://www.google.com/apis/ads/publisher/' . self::API_VERSION;

		$params['location'] = $location;
		$params['uri']      = $ns;
		$client             = new SoapClient( null, $params );

		$req_h = array(
			'ns1:applicationName' => AAGAM_APP_NAME,
			'ns1:networkCode'     => $gam_option['account']['networkCode'],
		);

		$soap_header_var = new SoapVar( $req_h, SOAP_ENC_OBJECT, 'RequestHeader', $ns );
		$soap_header     = new SoapHeader(
			'https://www.google.com/apis/ads/publisher/' . self::API_VERSION,
			'RequestHeader',
			$soap_header_var
		);

		$units    = array();
		$total    = PHP_INT_MAX;
		$offset   = 0;
		$per_page = 200;

		do {

			$statement = array( 'ns1:query' => 'LIMIT ' . $per_page . ' OFFSET ' . $offset );

			$statement_var = new SoapVar( $statement, SOAP_ENC_OBJECT, 'Statement', $ns );

			try {
				$_units = $client->__soapCall(
					'getAdUnitsByStatement',
					array( new SoapParam( $statement_var, 'ns1:filterStatement' ) ),
					array(),
					array( $soap_header )
				);

				if ( $total > absint( $_units->totalResultSetSize ) ) {
					$total = $_units->totalResultSetSize;
				}

				foreach ( $_units->results as $unit ) {

					if ( $gam_option['account']['effectiveRootAdUnitId'] != $unit->id && isset( $unit->parentPath ) ) {
						// excludes the root ad unit and the test network.
						$units[ $unit->id ] = $unit;
					} else {
						$total --;
					}
				}
			} catch ( Exception $e ) {
				return array(
					'status' => false,
					'msg'    => $e->getMessage(),
				);
			}

			$offset += $per_page;

		} while ( $total > count( $units ) );

		return array(
			'status' => true,
			'units'  => $units,
			'count'  => count( $units ),
		);
	}

	/**
	 * Get all ad units data via the REST API
	 *
	 * @param string $token access token.
	 */
	private function nosoap_get_ad_units( $token ) {

		$gam_option = Advanced_Ads_Network_Gam::get_option();
		$network    = $gam_option['account'];
		$apikey     = get_option( AAGAM_API_KEY_OPTION );

		$url = 'https://gam-connect.wpadvancedads.com/api/getUnits.php';

		$args = array(
			'body' => array(
				'access_token' => $token,
				'network'      => $network,
				'apikey'       => $apikey,
			),
		);

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return array(
				'status' => false,
				'msg'    => 'error while getting ad unit list',
				'raw'    => $response->get_error_message(),
			);
		} else {
			$results = json_decode( $response['body'], true );
			if ( $results && is_array( $results ) ) {
				if ( isset( $results['apikey'] ) ) {
					update_option( AAGAM_API_KEY_OPTION, $results['apikey'] );
				}
				return $results;
			} else {
				return array(
					'status' => false,
					'msg'    => 'unknown response format - getting ad units list',
					'raw'    => $response,
				);
			}
		}

	}

	/**
	 * Get all ad units data
	 *
	 * Will use SoapClient directly if available, or rely on REST API otherwise.
	 */
	public function ajax_get_ad_units() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			return;
		}
		$post_vars = wp_unslash( $_POST );
		if ( isset( $post_vars['nonce'] ) &&
				(
					false !== wp_verify_nonce( $post_vars['nonce'], 'gam-selector' ) ||
					false !== wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) ||
					false !== wp_verify_nonce( $post_vars['nonce'], 'gam-importer' )
				)
		) {
			$token = $this->get_access_token();
			if ( is_string( $token ) ) {
				if ( self::has_soap() ) {
					return $this->soap_get_ad_units( $token );
				} else {
					return $this->nosoap_get_ad_units( $token );
				}
			} else {
				return array(
					'status' => false,
					'msg'    => 'no access token',
				);
			}
		}
	}

	/**
	 * Store network data on multi-network account and store token data
	 */
	public function ajax_account_selected() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		$post_vars = wp_unslash( $_POST );
		if ( wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) === false ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		$account_index = $post_vars['index'];
		$extra_data    = json_decode( $post_vars['extra_data'], true );
		$token_data    = $extra_data['token_data'];
		$networks      = $extra_data['networks'];

		$gam_option = Advanced_Ads_Network_Gam::get_option();

		$gam_option['account'] = $networks[ $account_index ];
		Advanced_Ads_Network_Gam::update_option( $gam_option );
		$this->save_tokens( $token_data );

		wp_send_json( array( 'status' => true ) );
	}

	/**
	 * Submit authorization code on access request, and store token on success.
	 */
	public function ajax_confirm_api_code() {
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		$post_vars = wp_unslash( $_POST );
		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		$creds      = self::get_api_creds();
		$code       = $post_vars['code'];
		$cid        = $creds['id'];
		$cs         = $creds['secret'];
		$code_url   = 'https://www.googleapis.com/oauth2/v4/token';
		$grant_type = 'authorization_code';

		$args = array(
			'timeout' => 10,
			'body'    => array(
				'code'          => $code,
				'client_id'     => $cid,
				'client_secret' => $cs,
				'redirect_uri'  => self::API_REDIRECT_URI,
				'grant_type'    => $grant_type,
			),
		);

		$response = wp_remote_post( $code_url, $args );

		if ( is_wp_error( $response ) ) {
			wp_send_json(
				array(
					'status' => false,
					'msg'    => 'error while submitting code',
					'raw'    => $response->get_error_message(),
				)
			);
		} else {
			$token = json_decode( $response['body'], true );
			if ( null !== $token && isset( $token['refresh_token'] ) ) {
				$token_data = array(
					'expires'       => time() + absint( $token['expires_in'] ),
					'access_token'  => $token['access_token'],
					'refresh_token' => $token['refresh_token'],
				);
				wp_send_json(
					array(
						'status'     => true,
						'token_data' => $token_data,
					)
				);
			} else {
				wp_send_json(
					array(
						'status'        => false,
						'response_body' => $response['body'],
					)
				);
			}
		}
	}

	/**
	 * Save tokens (access and refresh) in options
	 *
	 * @param array $tokens Token data from Google authorization screen.
	 */
	public function save_tokens( $tokens ) {
		$option           = Advanced_Ads_Network_Gam::get_option();
		$option['tokens'] = $tokens;
		Advanced_Ads_Network_Gam::update_option( $option );
	}

	/**
	 *  Revoke a refresh token. Also reset options and delete API key if any.
	 */
	public function ajax_revoke_tokken() {
		$post_vars = wp_unslash( $_POST );
		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			wp_send_json_error( 'Not authorized', 403 );
		}

		if ( ! isset( $post_vars['nonce'] ) || false === wp_verify_nonce( $post_vars['nonce'], 'gam-connect' ) ) {
			wp_send_json_error( 'Bad request', 400 );
		}

		$gam_option = Advanced_Ads_Network_Gam::get_option();
		$url        = 'https://accounts.google.com/o/oauth2/revoke?token=' . $gam_option['tokens']['refresh_token'];
		$args       = array(
			'timeout' => 5,
			'header'  => array( 'Content-type' => 'application/x-www-form-urlencoded' ),
		);

		$response = wp_remote_post( $url, $args );
		if ( ! is_wp_error( $response ) ) {
			$gam_option['account']  = array();
			$gam_option['tokens']   = array();
			$gam_option['ad_units'] = array();

			Advanced_Ads_Network_Gam::update_option( $gam_option );
			delete_option( AAGAM_API_KEY_OPTION );
			wp_send_json( array( 'status' => true ) );
		}

		wp_send_json( array( 'status' => false ) );
	}

	/**
	 * Print admin footer scripts
	 */
	public function admin_footer() {
		if ( Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			require_once AAGAM_BASE_PATH . 'admin/views/gam-connect.php';
		}
	}

	/**
	 * Get all GAM ads
	 *
	 * @param bool $include_trash (optional) include trashed ads.
	 * @return array array with post objects.
	 */
	public function get_all_gam_ads( $include_trash = false ) {
		if ( $this->all_gam_ads === null ) {
			global $wpdb;
			$ad_model          = new Advanced_Ads_Model( $wpdb );
			$this->all_gam_ads = $ad_model->get_ads(
				array(
					'post_status' => array( 'publish', 'future', 'draft', 'pending', 'trash' ),
					'meta_query'  => array(
						array(
							'key'     => 'advanced_ads_ad_options',
							'value'   => 's:4:"type";s:3:"gam"',
							'compare' => 'LIKE',
						),
					),
				)
			);
		}
		if ( $include_trash !== true ) {
			$all_ads = array();
			foreach ( $this->all_gam_ads as $key => $value ) {
				if ( 'trash' != $value->post_status ) {
					$all_ads[ $key ] = $value;
				}
			}
			return $all_ads;
		}
		return $this->all_gam_ads;
	}

	/**
	 * Get API connection credentials
	 *
	 * @return array
	 */
	public static function get_api_creds() {
		return array(
			'id'     => '473832510505-vfvg0fonh9uippvk73m8pv4uom42glon.apps.googleusercontent.com',
			'secret' => 'nplF-khrk1gggcdVkFmURnSr',
		);
	}

	/**
	 * Check if SOAP extension is enabled
	 *
	 * @return bool
	 */
	public static function has_soap() {
		return class_exists( 'SoapClient' );
	}

	/**
	 * Returns or construct the singleton
	 *
	 * @return Advanced_Ads_Gam_Admin
	 */
	final public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Check if there is a valid license for the add-on
	 *
	 * @return bool TRUE if there is a valid license.
	 */
	public static function has_valid_license() {
		return (bool) Advanced_Ads_Admin_Licenses::get_instance()->get_license_status( 'advanced-ads-gam' );
	}

	/**
	 * Add settings to settings page
	 *
	 * @param string $hook settings page hook.
	 */
	public function settings_init( $hook ) {
		// Add license key field to license section.
		add_settings_field(
			'gam-license',
			__( 'Google Ad Manager Integration', 'advanced-ads-gam' ),
			array( $this, 'render_settings_license_callback' ),
			'advanced-ads-settings-license-page',
			'advanced_ads_settings_license_section'
		);
	}

	/**
	 * Render license key section
	 */
	public function render_settings_license_callback() {
		$licenses       = get_option( ADVADS_SLUG . '-licenses', array() );
		$license_key    = isset( $licenses['gam'] ) ? $licenses['gam'] : '';
		$license_status = get_option( AAGAM_SETTINGS . '-license-status', false );
		$index          = 'gam';
		$plugin_name    = AAGAM_PLUGIN_NAME;
		$options_slug   = AAGAM_SETTINGS;
		$plugin_url     = self::PLUGIN_LINK;

		// Template in main plugin.
		include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
	}

	/**
	 * Register plugin for the auto updater in the base plugin
	 *
	 * @param array $plugins plugins that are already registered for auto updates.
	 *
	 * @return array $plugins
	 */
	public function register_auto_updater( array $plugins = array() ) {
		$plugins['gam'] = array(
			'name'         => AAGAM_PLUGIN_NAME,
			'version'      => AAGAM_VERSION,
			'path'         => AAGAM_BASE_PATH . 'advanced-ads-gam.php',
			'options_slug' => AAGAM_SETTINGS,
		);

		return $plugins;
	}
}

if ( is_admin() ) {
	Advanced_Ads_Gam_Admin::get_instance();
}
