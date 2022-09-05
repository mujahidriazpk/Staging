<?php
/**
 * @copyright 2017  Cloudways  https://www.cloudways.com
 *
 *  This plugin is inspired from WP Speed of Light by JoomUnited.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class Breeze_Configuration {
	public function __construct() {
		global $breeze_network_subsite_settings;
		$breeze_network_subsite_settings = false;

		// Save the tabs settings.
		add_action( 'wp_ajax_save_settings_tab_basic', array( &$this, 'update_options_for_basic' ) );
		add_action( 'wp_ajax_save_settings_tab_file', array( &$this, 'update_options_for_file' ) );
		add_action( 'wp_ajax_save_settings_tab_preload', array( &$this, 'update_options_for_preload' ) );
		add_action( 'wp_ajax_save_settings_tab_advanced', array( &$this, 'update_options_for_advanced' ) );
		add_action( 'wp_ajax_save_settings_tab_heartbeat', array( &$this, 'update_options_for_heartbeat' ) );
		add_action( 'wp_ajax_save_settings_tab_database', array( &$this, 'update_options_for_database' ) );
		add_action( 'wp_ajax_save_settings_tab_cdn', array( &$this, 'update_options_for_cdn' ) );
		add_action( 'wp_ajax_save_settings_tab_tools', array( &$this, 'update_options_for_tools' ) );
		add_action( 'wp_ajax_save_settings_tab_faq', array( &$this, 'update_options_for_faq' ) );
		add_action( 'wp_ajax_save_settings_tab_varnish', array( &$this, 'update_options_for_varnish' ) );
		add_action( 'wp_ajax_save_settings_tab_inherit', array( &$this, 'update_options_for_inherit' ) );
	}

	public function update_options_for_varnish() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );
		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response = array();
		parse_str( $_POST['form-data'], $_POST );

		$varnish = array(
			'auto-purge-varnish'       => ( isset( $_POST['auto-purge-varnish'] ) ? '1' : '0' ),
			'breeze-varnish-server-ip' => preg_replace( '/[^a-zA-Z0-9\-\_\.]*/', '', $_POST['varnish-server-ip'] ),
		);

		breeze_update_option( 'varnish_cache', $varnish, true );

		Breeze_ConfigCache::factory()->write_config_cache();

		// Clear varnish cache after settings
		do_action( 'breeze_clear_varnish' );

		wp_send_json( $response );

	}

	/**
	 * Save the Basic Options settings via Ajax call.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_basic() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );
		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}


		$response = array();
		parse_str( $_POST['form-data'], $_POST );

		$post_activate_cache = isset( $_POST['breeze-admin-cache'] ) ? $_POST['breeze-admin-cache'] : array();
		$all_user_roles      = breeze_all_wp_user_roles();
		$active_cache_users  = array();
		foreach ( $all_user_roles as $usr_role ) {
			$active_cache_users[ $usr_role ] = 0;
			if ( isset( $post_activate_cache[ $usr_role ] ) ) {
				$active_cache_users[ $usr_role ] = 1;
			}
		}

		$iframe_lazy_load = ( isset( $_POST['bz-lazy-load-iframe'] ) ? '1' : '0' );
		$lazy_load        = ( isset( $_POST['bz-lazy-load'] ) ? '1' : '0' );
		if ( false === filter_var( $lazy_load, FILTER_VALIDATE_BOOLEAN ) ) {
			$iframe_lazy_load = '0';
		}

		$basic = array(
			'breeze-active'            => ( isset( $_POST['cache-system'] ) ? '1' : '0' ),
			'breeze-cross-origin'      => ( isset( $_POST['safe-cross-origin'] ) ? '1' : '0' ),
			'breeze-disable-admin'     => $active_cache_users,
			'breeze-gzip-compression'  => ( isset( $_POST['gzip-compression'] ) ? '1' : '0' ),
			'breeze-browser-cache'     => ( isset( $_POST['browser-cache'] ) ? '1' : '0' ),
			'breeze-lazy-load'         => ( isset( $_POST['bz-lazy-load'] ) ? '1' : '0' ),
			'breeze-lazy-load-native'  => ( isset( $_POST['bz-lazy-load-nat'] ) ? '1' : '0' ),
			'breeze-lazy-load-iframes' => $iframe_lazy_load,
			'breeze-desktop-cache'     => '1',
			'breeze-mobile-cache'      => '1',
			'breeze-display-clean'     => '1',
			'breeze-ttl'               => (int) $_POST['cache-ttl'],
		);

		breeze_update_option( 'basic_settings', $basic, true );

		// Storage information to cache pages
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Turn on WP_CACHE to support advanced-cache file
		if ( isset( $_POST['cache-system'] ) ) {
			Breeze_ConfigCache::factory()->toggle_caching( true );
		} else {
			Breeze_ConfigCache::factory()->toggle_caching( false );
		}

		// Reschedule cron events
		if ( isset( $_POST['cache-system'] ) ) {
			Breeze_PurgeCacheTime::factory()->unschedule_events();
			Breeze_PurgeCacheTime::factory()->schedule_events();
		}
		// Add expires header
		self::update_htaccess();

		//delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/**
	 * Save the File optimisation tab settings via Ajax call.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_file() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );

		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response = array();
		parse_str( $_POST['form-data'], $_POST );

		$exclude_css = $this->string_convert_arr( sanitize_textarea_field( $_POST['exclude-css'] ) );
		$exclude_js  = $this->string_convert_arr( sanitize_textarea_field( $_POST['exclude-js'] ) );
		$no_delay_js = $this->string_convert_arr( sanitize_textarea_field( $_POST['no-delay-js-scripts'] ) );
		$delay_js    = $this->string_convert_arr( sanitize_textarea_field( $_POST['delay-js-scripts'] ) );

		if ( ! empty( $exclude_js ) ) {
			$exclude_js = array_unique( $exclude_js );
		}

		if ( ! empty( $no_delay_js ) ) {
			$no_delay_js = array_unique( $no_delay_js );
		}

		if ( ! empty( $delay_js ) ) {
			$delay_js = array_unique( $delay_js );
		}

		if ( ! empty( $exclude_css ) ) {
			$exclude_css = array_unique( $exclude_css );
		}

		$move_to_footer_js = $defer_js = array();

		if ( ! empty( $_POST['move-to-footer-js'] ) ) {
			foreach ( $_POST['move-to-footer-js'] as $url ) {
				if ( trim( $url ) == '' ) {
					continue;
				}
				$url                                              = current( explode( '?', $url, 2 ) );
				$move_to_footer_js[ sanitize_text_field( $url ) ] = sanitize_text_field( $url );
			}
		}

		if ( ! empty( $_POST['defer-js'] ) ) {
			foreach ( $_POST['defer-js'] as $url ) {
				if ( trim( $url ) == '' ) {
					continue;
				}
				$url                                     = current( explode( '?', $url, 2 ) );
				$defer_js[ sanitize_text_field( $url ) ] = sanitize_text_field( $url );
			}
		}

		$file_settings = array(
			'breeze-minify-html'        => ( isset( $_POST['minification-html'] ) ? '1' : '0' ),
			// --
			'breeze-minify-css'         => ( isset( $_POST['minification-css'] ) ? '1' : '0' ),
			'breeze-font-display-swap'  => ( isset( $_POST['font-display'] ) ? '1' : '0' ),
			'breeze-group-css'          => ( isset( $_POST['group-css'] ) ? '1' : '0' ),
			'breeze-exclude-css'        => $exclude_css,
			'breeze-include-inline-css' => ( isset( $_POST['include-inline-css'] ) ? '1' : '0' ),
			// --
			'breeze-minify-js'          => ( isset( $_POST['minification-js'] ) ? '1' : '0' ),
			'breeze-group-js'           => ( isset( $_POST['group-js'] ) ? '1' : '0' ),
			'breeze-include-inline-js'  => ( isset( $_POST['include-inline-js'] ) ? '1' : '0' ),
			'breeze-exclude-js'         => $exclude_js,
			'breeze-move-to-footer-js'  => $move_to_footer_js,
			'breeze-defer-js'           => $defer_js,
			'breeze-enable-js-delay'    => ( isset( $_POST['enable-js-delay'] ) ? '1' : '0' ),
			'breeze-delay-js-scripts'   => $delay_js,
			'no-breeze-no-delay-js'     => $no_delay_js,
			'breeze-delay-all-js'       => ( isset( $_POST['breeze-delay-all-js'] ) ? '1' : '0' ),
		);

		breeze_update_option( 'file_settings', $file_settings, true );

		// Storage information to cache pages.
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/**
	 * Save the Preload option settings via Ajax call.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_preload() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );
		set_as_network_screen();


		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response      = array();
		$preload_fonts = array();
		parse_str( $_POST['form-data'], $_POST );


		if ( isset( $_POST['breeze-preload-font'] ) && ! empty( $_POST['breeze-preload-font'] ) ) {
			foreach ( $_POST['breeze-preload-font'] as $font_url ) {
				if ( '' === trim( $font_url ) ) {
					continue;
				}
				$font_url                                          = current( explode( '?', $font_url, 2 ) );
				$preload_fonts[ sanitize_text_field( $font_url ) ] = sanitize_text_field( $font_url );
			}
		}

		$prefetch_urls = $this->string_convert_arr( sanitize_textarea_field( $_POST['br-prefetch-urls'] ) );
		if ( ! empty( $prefetch_urls ) ) {
			$prefetch_urls = array_unique( $prefetch_urls );
			// ltrim( $current_url, 'https:' )
			foreach ( $prefetch_urls as &$url_prefetch ) {
				//$url_prefetch = ltrim( $url_prefetch, 'https:' );
				$link_schema = parse_url( $url_prefetch );
				if ( isset( $link_schema['host'] ) ) {
					$url_prefetch = '//' . $link_schema['host'];
				} else {
					unset( $url_prefetch );
				}
			}
		}

		$preload = array(
			'breeze-preload-fonts' => $preload_fonts,
			'breeze-preload-links' => ( isset( $_POST['preload-links'] ) ? '1' : '0' ),
			'breeze-prefetch-urls' => $prefetch_urls,
		);

		breeze_update_option( 'preload_settings', $preload, true );

		// Storage information to cache pages.
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/**
	 * Save the Advanced option settings via Ajax call.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_advanced() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );
		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response = array();
		parse_str( $_POST['form-data'], $_POST );

		$exclude_urls    = $this->string_convert_arr( sanitize_textarea_field( $_POST['exclude-urls'] ) );
		$cache_query_str = $this->string_convert_arr( sanitize_textarea_field( $_POST['cache-query-str'] ) );
		if ( ! empty( $exclude_urls ) ) {
			$exclude_urls = array_unique( $exclude_urls );
		}
		if ( ! empty( $cache_query_str ) ) {
			$cache_query_str = array_unique( $cache_query_str );
		}

		$advanced = array(
			'breeze-exclude-urls'  => $exclude_urls,
			'cached-query-strings' => $cache_query_str,
			'breeze-wp-emoji'      => ( isset( $_POST['breeze-wpjs-emoji'] ) ? '1' : '0' ),
		);

		breeze_update_option( 'advanced_settings', $advanced, true );

		// Storage information to cache pages.
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/**
	 *  Save the Heartbeat API settings via Ajax call.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_heartbeat() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );
		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response = array();
		parse_str( $_POST['form-data'], $_POST );

		$heartbeat = array(
			'breeze-control-heartbeat'  => ( isset( $_POST['breeze-control-hb'] ) ? '1' : '0' ),
			'breeze-heartbeat-front'    => sanitize_textarea_field( $_POST['br-heartbeat-front'] ),
			'breeze-heartbeat-postedit' => sanitize_textarea_field( $_POST['br-heartbeat-postedit'] ),
			'breeze-heartbeat-backend'  => sanitize_textarea_field( $_POST['br-heartbeat-backend'] ),
		);

		breeze_update_option( 'heartbeat_settings', $heartbeat, true );

		// Storage information to cache pages.
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/**
	 * Database tab only has actions for now and it will not save anything.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_database() {
		// Does not have anything to save.
	}

	/**
	 * Save the CDN option settings via Ajax call.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_cdn() {
		breeze_is_restricted_access();
		check_ajax_referer( '_breeze_save_options', 'security' );
		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response = array();
		parse_str( $_POST['form-data'], $_POST );

		$cdn_content     = array();
		$exclude_content = array();
		if ( ! empty( $_POST['cdn-content'] ) ) {
			$cdn_content = explode( ',', sanitize_text_field( $_POST['cdn-content'] ) );
			$cdn_content = array_unique( $cdn_content );
		}
		if ( ! empty( $_POST['cdn-exclude-content'] ) ) {
			$exclude_content = explode( ',', sanitize_text_field( $_POST['cdn-exclude-content'] ) );
			$exclude_content = array_unique( $exclude_content );
		}

		$cdn_url = ( isset( $_POST['cdn-url'] ) ? sanitize_text_field( $_POST['cdn-url'] ) : '' );
		if ( ! empty( $cdn_url ) ) {
			$http_schema = parse_url( $cdn_url, PHP_URL_SCHEME );

			$cdn_url = ltrim( $cdn_url, 'https:' );
			$cdn_url = '//' . ltrim( $cdn_url, '//' );

			if ( ! empty( $http_schema ) ) {
				$cdn_url = $http_schema . ':' . $cdn_url;
			}
		}

		$cdn = array(
			'cdn-active'          => ( isset( $_POST['activate-cdn'] ) ? '1' : '0' ),
			'cdn-url'             => $cdn_url,
			'cdn-content'         => $cdn_content,
			'cdn-exclude-content' => $exclude_content,
			'cdn-relative-path'   => ( isset( $_POST['cdn-relative-path'] ) ? '1' : '0' ),
		);

		breeze_update_option( 'cdn_integration', $cdn, true );

		// Storage information to cache pages.
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/**
	 * Tools tab has not options that need save here.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_tools() {
		// Does not have anything to save.
	}

	/**
	 * FAQ does not have any options to save, text information only.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_faq() {
		// Does not have anything to save.
	}

	/**
	 * FAQ does not have any options to save, text information only.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function update_options_for_inherit() {
		breeze_is_restricted_access();
		// Does not have anything to save.
		check_ajax_referer( 'breeze_inherit_settings', 'security' );

		set_as_network_screen();

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$response         = array();
		$inherit_settings = ( ( true === filter_var( $_POST['is-selected'], FILTER_VALIDATE_BOOLEAN ) ) ? '1' : '0' );

		update_option( 'breeze_inherit_settings', $inherit_settings );
		Breeze_ConfigCache::factory()->write();
		Breeze_ConfigCache::factory()->write_config_cache();

		// Delete cache after settings
		do_action( 'breeze_clear_all_cache' );

		wp_send_json( $response );
	}

	/*
	 * function add expires header to .htaccess
	 */
	public static function add_expires_header( $clean = false, $conditional_regex = '' ) {
		$args = array(
			'before' => '#Expires headers configuration added by BREEZE WP CACHE plugin',
			'after'  => '#End of expires headers configuration',
		);

		if ( $clean ) {
			$args['clean'] = true;
		} else {
			$args['content'] = '<IfModule mod_env.c>' . PHP_EOL .
			                   '   SetEnv BREEZE_BROWSER_CACHE_ON 1' . PHP_EOL .
			                   '</IfModule>' . PHP_EOL .
			                   '<IfModule mod_expires.c>' . PHP_EOL .
			                   '   ExpiresActive On' . PHP_EOL .
			                   '   ExpiresDefault "access plus 1 month"' . PHP_EOL .

			                   '   # Assets' . PHP_EOL .
			                   '   ExpiresByType text/css "access plus 1 month"' . PHP_EOL .
			                   '   ExpiresByType application/javascript "access plus 1 month"' . PHP_EOL .
			                   '   ExpiresByType application/x-javascript "access plus 1 month"' . PHP_EOL .
			                   '   ExpiresByType text/javascript "access plus 1 month"' . PHP_EOL .

			                   '   # Media assets ' . PHP_EOL .
			                   '   ExpiresByType audio/ogg "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType image/bmp "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType image/gif "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType image/jpeg "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType image/png "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType image/svg+xml "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType image/webp "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType video/mp4 "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType video/ogg "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType video/webm "access plus 1 year"' . PHP_EOL .
			                   '   # Font assets ' . PHP_EOL .
			                   '   ExpiresByType application/vnd.ms-fontobject "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType font/eot "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType font/opentype "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType application/x-font-ttf "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType application/font-woff "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType application/x-font-woff "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType font/woff "access plus 1 year"' . PHP_EOL .
			                   '   ExpiresByType application/font-woff2 "access plus 1 year"' . PHP_EOL .

			                   '   # Data interchange' . PHP_EOL .
			                   '   ExpiresByType application/xml "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType application/json "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType application/ld+json "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType application/schema+json "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType application/vnd.geo+json "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType text/xml "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType application/rss+xml "access plus 1 hour"' . PHP_EOL .
			                   '   ExpiresByType application/rdf+xml "access plus 1 hour"' . PHP_EOL .
			                   '   ExpiresByType application/atom+xml "access plus 1 hour"' . PHP_EOL .

			                   '   # Manifest files' . PHP_EOL .
			                   '   ExpiresByType application/manifest+json "access plus 1 week"' . PHP_EOL .
			                   '   ExpiresByType application/x-web-app-manifest+json "access plus 0 seconds"' . PHP_EOL .
			                   '   ExpiresByType text/cache-manifest  "access plus 0 seconds"' . PHP_EOL .

			                   '   # Favicon' . PHP_EOL .
			                   '   ExpiresByType image/vnd.microsoft.icon "access plus 1 week"' . PHP_EOL .
			                   '   ExpiresByType image/x-icon "access plus 1 week"' . PHP_EOL .
			                   '   # HTML no caching' . PHP_EOL .
			                   '   ExpiresByType text/html "access plus 0 seconds"' . PHP_EOL .

			                   '   # Other' . PHP_EOL .
			                   '   ExpiresByType application/xhtml-xml "access plus 1 month"' . PHP_EOL .
			                   '   ExpiresByType application/pdf "access plus 1 month"' . PHP_EOL .
			                   '   ExpiresByType application/x-shockwave-flash "access plus 1 month"' . PHP_EOL .
			                   '   ExpiresByType text/x-cross-domain-policy "access plus 1 week"' . PHP_EOL .

			                   '</IfModule>' . PHP_EOL;

			$args['conditions'] = array(
				'mod_expires',
				'ExpiresActive',
				'ExpiresDefault',
				'ExpiresByType',
			);

			if ( ! empty( $conditional_regex ) ) {
				$args['content'] = '<If "' . $conditional_regex . '">' . PHP_EOL . $args['content'] . '</If>' . PHP_EOL;
			};
		}

		return self::write_htaccess( $args );
	}

	/*
	 * function add gzip header to .htaccess
	 */
	public static function add_gzip_htacess( $clean = false, $conditional_regex = '' ) {
		$args = array(
			'before' => '# Begin GzipofBreezeWPCache',
			'after'  => '# End GzipofBreezeWPCache',
		);

		if ( $clean ) {
			$args['clean'] = true;
		} else {
			$args['content'] = '<IfModule mod_env.c>' . PHP_EOL .
			                   '    SetEnv BREEZE_GZIP_ON 1' . PHP_EOL .
			                   '</IfModule>' . PHP_EOL .
			                   '<IfModule mod_deflate.c>' . PHP_EOL .
			                   '	AddType x-font/woff .woff' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/plain' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE image/svg+xml' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/html' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/xml' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/css' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/vtt' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/x-component' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE text/javascript' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/js' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/x-httpd-php' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/x-httpd-fastphp' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/atom+xml' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/json' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/ld+json' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/x-web-app-manifest+json' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/xml' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/xhtml+xml' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/rss+xml' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/javascript' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/x-javascript' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/x-font-ttf' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/vnd.ms-fontobject' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE font/opentype' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE font/ttf' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE font/eot font/otf' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE font/otf' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE font/woff' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/x-font-woff' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE application/font-woff2' . PHP_EOL .
			                   '	AddOutputFilterByType DEFLATE image/x-icon' . PHP_EOL .
			                   '</IfModule>' . PHP_EOL;

			$args['conditions'] = array(
				'mod_deflate',
				'AddOutputFilterByType',
				'AddType',
				'GzipofBreezeWPCache',
			);

			if ( ! empty( $conditional_regex ) ) {
				$args['content'] = '<If "' . $conditional_regex . '">' . PHP_EOL . $args['content'] . '</If>' . PHP_EOL;
			};
		}

		return self::write_htaccess( $args );
	}

	/**
	 * Trigger update to htaccess file.
	 *
	 * @param bool $clean If true, will clear custom .htaccess rules.
	 *
	 * @return bool
	 */
	public static function update_htaccess( $clean = false ) {
		if ( $clean ) {
			self::add_expires_header( $clean );
			self::add_gzip_htacess( $clean );

			return true;
		}

		if ( is_multisite() ) {
			// Multisite setup.
			$supports_conditionals = breeze_is_supported( 'conditional_htaccess' );

			if ( ! $supports_conditionals ) {
				// If Apache htaccess conditional directives not available, inherit network-level settings.
				$config = get_site_option( 'breeze_basic_settings', array() );

				if ( isset( $config['breeze-active'] ) && '1' === $config['breeze-active'] ) {
					self::add_expires_header( ! isset( $config['breeze-browser-cache'] ) || '1' !== $config['breeze-browser-cache'] );
					self::add_gzip_htacess( ! isset( $config['breeze-gzip-compression'] ) || '1' !== $config['breeze-gzip-compression'] );
				} else {
					self::add_expires_header( true );
					self::add_gzip_htacess( true );
				}

				return true;
			}

			$has_browser_cache      = false;
			$browser_cache_sites    = array();
			$no_browser_cache_sites = array();
			$browser_cache_regex    = '';
			$has_gzip_compress      = false;
			$gzip_compress_sites    = array();
			$no_gzip_compress_sites = array();
			$gzip_compress_regex    = '';

			$blogs = get_sites(
				array(
					'fields' => 'ids',
				)
			);

			global $breeze_network_subsite_settings;
			$breeze_network_subsite_settings = true;

			foreach ( $blogs as $blog_id ) {
				switch_to_blog( $blog_id );
				$site_url = preg_quote( preg_replace( '(^https?://)', '', site_url() ) );
				if ( '1' === Breeze_Options_Reader::get_option_value( 'breeze-active' ) ) {
					if ( '1' === Breeze_Options_Reader::get_option_value( 'breeze-browser-cache' ) ) {
						$has_browser_cache     = true;
						$browser_cache_sites[] = $site_url;
					} else {
						$no_browser_cache_sites[] = $site_url;
					}
					if ( '1' === Breeze_Options_Reader::get_option_value( 'breeze-gzip-compression' ) ) {
						$has_gzip_compress     = true;
						$gzip_compress_sites[] = $site_url;
					} else {
						$no_gzip_compress_sites[] = $site_url;
					}
				} else {
					$no_browser_cache_sites[] = $site_url;
					$no_gzip_compress_sites[] = $site_url;
				}
				restore_current_blog();
			}

			$breeze_network_subsite_settings = false;

			$rules = array(
				'browser_cache' => 'add_expires_header',
				'gzip_compress' => 'add_gzip_htacess',
			);
			// Loop through caching type rules.
			foreach ( $rules as $var_name => $method_name ) {
				$has_cache_var = 'has_' . $var_name;
				if ( ! ${$has_cache_var} ) {
					// No sites using rules, clean up.
					self::$method_name( true );
				} else {
					$enabled_sites  = $var_name . '_sites';
					$disabled_sites = 'no_' . $var_name . '_sites';
					$regex_string   = '';

					if ( empty( ${$disabled_sites} ) ) {
						// Rule is active across sites, do not include conditional directives.
						self::$method_name( $clean );
						continue;
					}

					if ( defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL ) {
						// Subdomain sites are matched using host alone.
						$regex_string = '%{HTTP_HOST} =~ m#^(' . implode( '|', ${$enabled_sites} ) . ')#';
					} else {
						// Subdirectory sites are matched using "THE_REQUEST".
						$network_site_url = preg_quote( preg_replace( '(^https?://)', '', untrailingslashit( network_site_url() ) ) );

						// Remove host part from URLs.
						${$enabled_sites} = array_filter(
							array_map(
								function ( $url ) use ( $network_site_url ) {
									$modified = str_replace( $network_site_url, '', $url );

									return empty( $modified ) ? '/' : $modified;
								},
								${$enabled_sites}
							)
						);

						if ( ! empty( ${$enabled_sites} ) ) {
							$regex_string = '%{THE_REQUEST} =~ m#^GET (' . implode( '|', ${$enabled_sites} ) . ')#';
						}

						// Remove main site URL from disabled sites array.
						$network_site_url_index = array_search( $network_site_url, ${$disabled_sites} );
						if ( false !== $network_site_url_index ) {
							unset( ${$disabled_sites[ $network_site_url_index ]} );
						}
						// Remove host part from URLs.
						${$disabled_sites} = array_filter(
							array_map(
								function ( $url ) use ( $network_site_url ) {
									$modified = str_replace( $network_site_url, '', $url );

									return empty( $modified ) ? '/' : $modified;
								},
								${$disabled_sites}
							)
						);
						if ( ! empty( ${$disabled_sites} ) ) {
							if ( ! empty( ${$enabled_sites} ) ) {
								$regex_string .= ' && ';
							}
							$regex_string .= '%{THE_REQUEST} !~ m#^GET (' . implode( '|', ${$disabled_sites} ) . ')#';
						}
					}

					// Add conditional rule.
					self::$method_name( empty( $regex_string ), $regex_string );
				}
			}
		} else {
			// Single-site setup.
			if ( '1' === Breeze_Options_Reader::get_option_value( 'breeze-active' ) ) {
				self::add_expires_header( '1' !== Breeze_Options_Reader::get_option_value( 'breeze-browser-cache' ) );
				self::add_gzip_htacess( '1' !== Breeze_Options_Reader::get_option_value( 'breeze-gzip-compression' ) );
			} else {
				// Caching not activated, clean up.
				self::add_expires_header( true );
				self::add_gzip_htacess( true );

				return true;
			}
		}

		return true;
	}

	/**
	 * Add and remove custom blocks from .htaccess.
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public static function write_htaccess( $args ) {
		$htaccess_path = trailingslashit( ABSPATH ) . '.htaccess';

		if ( ! is_super_admin() ) {
			return false;
		}
		// open htaccess file
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		}
		if ( empty( $htaccess_content ) ) {
			return false;
		}

		// Remove old rules.
		$htaccess_content = preg_replace( "/{$args['before']}[\s\S]*{$args['after']}" . PHP_EOL . '/im', '', $htaccess_content );

		if ( ! isset( $args['clean'] ) ) {
			if ( isset( $args['conditions'] ) ) {
				foreach ( $args['conditions'] as $condition ) {
					if ( strpos( $htaccess_content, $condition ) !== false ) {
						return false;
					}
				}
			}

			$htaccess_content = $args['before'] . PHP_EOL . $args['content'] . $args['after'] . PHP_EOL . $htaccess_content;
		}

		file_put_contents( $htaccess_path, $htaccess_content );

		return true;
	}

	/**
	 * Database optimization actions.
	 * Used to clean the database.
	 *
	 * Changed completely in 2.0.0
	 *
	 * @param string $type Optimization type.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public static function clean_system( $type = '' ) {
		global $wpdb;

		switch ( $type ) {
			case 'revisions':
				/**
				 * Delete all revisions.
				 */
				$all_revisions = $wpdb->get_col(
				/* translators: post type */
					$wpdb->prepare( "SELECT ID FROM `$wpdb->posts` WHERE post_type = %s", 'revision' )
				);
				if ( ! empty( $all_revisions ) ) {
					foreach ( $all_revisions as $post_id ) {
						$post_id = intval( $post_id );
						if ( 0 !== $post_id ) {
							wp_delete_post_revision( $post_id );
						}
					}
				}
				break;
			case 'drafted':
				/**
				 * Delete all draft entries.
				 */
				$all_auto_draft = $wpdb->get_col(
				/* translators: post status */
					$wpdb->prepare( "SELECT ID FROM `$wpdb->posts` WHERE post_status = %s", 'auto-draft' )
				);
				if ( ! empty( $all_auto_draft ) ) {
					foreach ( $all_auto_draft as $post_id ) {
						$post_id = intval( $post_id );
						if ( 0 !== $post_id ) {
							wp_delete_post( $post_id, true );
						}
					}
				}
				break;
			case 'trash':
				/**
				 * Delete all trashed posts/pages.
				 */
				$all_trashed = $wpdb->get_col(
				/* translators: post status */
					$wpdb->prepare( "SELECT ID FROM `$wpdb->posts` WHERE post_status = %s", 'trash' )
				);
				if ( ! empty( $all_trashed ) ) {
					foreach ( $all_trashed as $post_id ) {
						$post_id = intval( $post_id );
						if ( 0 !== $post_id ) {
							wp_delete_post( $post_id, true );
						}
					}
				}
				break;
			case 'comments_trash':
				/**
				 * Delete all trashed posts
				 * @see get_comment_count() comment for all the trashed statuses.
				 */
				$comments_trashed = $wpdb->get_col(
				/* translators: trashed comments status, comments for posts that are in the trash */
					$wpdb->prepare( "SELECT comment_ID FROM `$wpdb->comments` WHERE comment_approved = %s  OR comment_approved = %s", 'trash', 'post-trashed' )
				);
				if ( ! empty( $comments_trashed ) ) {
					foreach ( $comments_trashed as $comment_id ) {
						$comment_id = intval( $comment_id );
						if ( 0 !== $comment_id ) {
							wp_delete_comment( $comment_id, true );
						}
					}
				}
				break;
			case 'comments_spam':
				/**
				 * Delete all spam comments.
				 */
				$comments_spam = $wpdb->get_col(
				/* translators: spam comments status */
					$wpdb->prepare( "SELECT comment_ID FROM `$wpdb->comments` WHERE comment_approved = %s", 'spam' )
				);
				if ( ! empty( $comments_spam ) ) {
					foreach ( $comments_spam as $comment_id ) {
						$comment_id = intval( $comment_id );
						if ( 0 !== $comment_id ) {
							wp_delete_comment( $comment_id, true );
						}
					}
				}
				break;
			case 'trackbacks':
				/**
				 * Delete all Track-back and Ping-back comments.
				 */
				$comments_trackback = $wpdb->get_col(
				/* translators: comment type, comment type */
					$wpdb->prepare( "SELECT comment_ID FROM `$wpdb->comments` WHERE comment_type = %s OR comment_type= %s", 'trackback', 'pingback' )
				);
				if ( ! empty( $comments_trackback ) ) {
					foreach ( $comments_trackback as $comment_id ) {
						$comment_id = intval( $comment_id );
						if ( 0 !== $comment_id ) {
							wp_delete_comment( $comment_id, true );
						}
					}
				}
				break;
			case 'transient':
				/**
				 * Delete all Transients.
				 */
				$all_transients = $wpdb->get_col(
				/* translators: comment type, comment type */
					$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE ( option_name LIKE %s OR option_name LIKE %s ) AND option_name NOT LIKE %s",
						$wpdb->esc_like( '_transient' ) . '%',
						$wpdb->esc_like( '_site_transient_' ) . '%',
						$wpdb->esc_like( '_transient_timeout' ) . '%'
					)
				);
				if ( ! empty( $all_transients ) ) {
					foreach ( $all_transients as $transient ) {
						if ( strpos( $transient, '_site_transient_' ) !== false ) {
							$transient_name = str_replace( '_site_transient_', '', $transient );
							delete_site_transient( $transient_name );
						} else {
							$transient_name = str_replace( '_transient_', '', $transient );
							delete_transient( $transient_name );
						}
					}
				}
				break;
		}

		return true;
	}

	/**
	 * Returns the count of each section in the Database tab.
	 *
	 * Changed completely in 2.0.0
	 *
	 * @param string $type
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public static function get_element_to_clean( $type = '' ) {
		global $wpdb;
		$return = 0;
		switch ( $type ) {
			case 'revisions':
				$return = $wpdb->get_var(
				/* translators: post type */
					$wpdb->prepare( "SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_type = %s", 'revision' )
				);
				break;
			case 'drafted':
				$return = $wpdb->get_var(
				/* translators: post status */
					$wpdb->prepare( "SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_status = %s", 'auto-draft' )
				);
				break;
			case 'trash':
				$return = $wpdb->get_var(
				/* translators: post status */
					$wpdb->prepare( "SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_status = %s", 'trash' )
				);
				break;
			case 'comments_trash':
				$return = $wpdb->get_var(
				/* translators: trashed comments status, comments for posts that are in the trash */
					$wpdb->prepare( "SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_approved = %s  OR comment_approved = %s", 'trash', 'post-trashed' )
				);
				break;
			case 'comments_spam':
				$return = $wpdb->get_var(
				/* translators: spam comments status */
					$wpdb->prepare( "SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_approved = %s", 'spam' )
				);
				break;
			case 'trackbacks':
				$return = $wpdb->get_var(
				/* translators: comment type, comment type */
					$wpdb->prepare( "SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_type = %s OR comment_type= %s", 'trackback', 'pingback' )
				);
				break;
			case 'transient':
				$return = $wpdb->get_var(
				/* translators: comment type, comment type */
					$wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->options WHERE ( option_name LIKE %s OR option_name LIKE %s ) AND option_name NOT LIKE %s",
						$wpdb->esc_like( '_transient' ) . '%',
						$wpdb->esc_like( '_site_transient_' ) . '%',
						$wpdb->esc_like( '_transient_timeout' ) . '%'
					)
				);
				break;
		}

		return (int) $return;
	}

	// Convert string to array
	protected function string_convert_arr( $input ) {
		$output = array();
		if ( ! empty( $input ) ) {
			$input = rawurldecode( $input );
			$input = trim( $input );
			$input = str_replace( ' ', '', $input );
			$input = explode( "\n", $input );

			foreach ( $input as $k => $v ) {
				$output[] = trim( $v );
			}
		}

		return $output;
	}

	//ajax clean cache
	public static function breeze_clean_cache() {
		// Check whether we're clearing the cache for one subsite on the network.
		$is_subsite = is_multisite() && ! is_network_admin();

		// analysis size cache
		$cachepath = untrailingslashit( breeze_get_cache_base_path( is_network_admin() ) );

		$size_cache = breeze_get_directory_size( $cachepath );

		// Analyze minification directory sizes.
		$files_path = rtrim( WP_CONTENT_DIR, '/' ) . '/cache/breeze-minification';
		if ( $is_subsite ) {
			$blog_id    = get_current_blog_id();
			$files_path .= DIRECTORY_SEPARATOR . $blog_id;
		}
		$size_cache += breeze_get_directory_size( $files_path, array( 'index.html' ) );

		$result = self::formatBytes( $size_cache );

		//delete minify file
		Breeze_MinificationCache::clear_minification();
		//delete all cache
		Breeze_PurgeCache::breeze_cache_flush();

		return $result;
	}

	/*
	 *Ajax clean cache
	 *
	 */
	public static function breeze_ajax_clean_cache() {
		breeze_is_restricted_access();
		//check security nonce
		check_ajax_referer( '_breeze_purge_cache', 'security' );
		$result = self::breeze_clean_cache();

		echo json_encode( $result );
		exit;
	}

	/*
	 * Ajax purge varnish
	 */
	public static function purge_varnish_action() {
		breeze_is_restricted_access();
		//check security
		check_ajax_referer( '_breeze_purge_varnish', 'security' );

		do_action( 'breeze_clear_varnish' );

		echo json_encode( array( 'clear' => true ) );
		exit;
	}

	/*
	 * Ajax purge database
	 */
	public static function breeze_ajax_purge_database() {
		breeze_is_restricted_access();
		//check security
		check_ajax_referer( '_breeze_purge_database', 'security' );

		set_as_network_screen();

		$items = array(
			'post_revisions'       => array( 'revisions' ),
			'auto_drafts'          => array( 'drafted' ),
			'trashed_posts'        => array( 'trash' ),
			'trashed_comments'     => array( 'comments_trash' ),
			'spam_comments'        => array( 'comments_spam' ),
			'trackbacks_pingbacks' => array( 'trackbacks' ),
			'all_transients'       => array( 'transient' ),
			'all'                  => array( 'revisions', 'drafted', 'trash', 'comments_trash', 'comments_spam', 'trackbacks', 'transient' ),
		);

		if ( isset( $_POST['action_type'] ) ) {
			$type = $_POST['action_type'];

			if ( 'custom' === $type ) {
				$services = json_decode( stripslashes( $_POST['services'] ), true );

				if ( ! empty( $services ) && is_array( $services ) ) {
					foreach ( $services as $service ) {
						if ( isset( $items[ $service ] ) ) {

							self::optimize_database( $items[ $service ] );
						}
					}
				}
			} else {

				if ( isset( $items[ $type ] ) ) {
					self::optimize_database( $items[ $type ] );
				}
			}

		}
		// $type = array( 'revisions', 'drafted', 'trash', 'comments_trash', 'comments_spam', 'trackbacks', 'transient' );


		echo json_encode( array( 'clear' => true ) );
		exit;
	}

	public static function formatBytes( $bytes, $precision = 2 ) {
		if ( $bytes >= 1073741824 ) {
			$bytes = number_format( $bytes / 1073741824, 2 );
		} elseif ( $bytes >= 1048576 ) {
			$bytes = number_format( $bytes / 1048576, 2 );
		} elseif ( $bytes >= 1024 ) {
			$bytes = number_format( $bytes / 1024, 2 );
		} elseif ( $bytes > 1 ) {
			$bytes = $bytes;
		} elseif ( $bytes == 1 ) {
			$bytes = $bytes;
		} else {
			$bytes = '0';
		}

		return $bytes;
	}

	/**
	 * Perform database optimization.
	 *
	 * @param array $items
	 */
	public static function optimize_database( $items ) {
		set_as_network_screen();

		if ( is_multisite() && is_network_admin() ) {
			$sites = get_sites(
				array(
					'fields' => 'ids',
				)
			);

			foreach ( $sites as $blog_id ) {
				switch_to_blog( $blog_id );
				foreach ( $items as $item ) {
					self::clean_system( $item );
				}
				restore_current_blog();
			}
		} else {
			foreach ( $items as $item ) {
				self::clean_system( $item );
			}
		}
	}

}

//init configuration object
new Breeze_Configuration();
