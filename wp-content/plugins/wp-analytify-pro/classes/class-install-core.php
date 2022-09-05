<?php 

/**
 * Insatall and activate the core version.
 */
class WP_Analytify_Pro_Core_Installer {

	public function __construct() {
		// Check core Analytify is available.
		$plugins = get_option('active_plugins');	
	
		if ( ! isset( $plugins['wp-analytify/wp-analytify.php'] ) ) {
			$this->ready_analytify_core();
		}
	}

	protected function ready_analytify_core() {
		$basename = 'wp-analytify/wp-analytify.php';
		$is_plugin_installed = $this->get_installed_plugin_data( $basename );
		$plugin_data = $this->get_plugin_data( 'wp-analytify', $basename );

		if ( $is_plugin_installed ) {
			// upgrade plugin - attempt for once
			if ( isset( $plugin_data->version ) && $is_plugin_installed['Version'] != $plugin_data->version ) {
				$this->upgrade_or_install_plugin( $basename );
			}
			
			if ( ! is_plugin_active( $basename ) ) {
				activate_plugin( $this->safe_path( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $basename ), '', false, true );
			}

		} else {
			// install & activate plugin
			$download_link = isset( $plugin_data->download_link ) ? $plugin_data->download_link : ANALYTIFY_PRO_ROOT_PATH . 'lib/wp-analytify.zip';

			$this->upgrade_or_install_plugin( $download_link, false );
		}

		return false;
	}

	protected function get_plugin_data( $slug = '', $basename = '' ){
		if ( empty( $slug ) ) {
			return false;
		}

		$installed_plugin = false;

		if ( $basename ) {
			$installed_plugin = $this->get_installed_plugin_data( $basename );
		}

		if ( $installed_plugin ) {
			return $installed_plugin;
		}

		$args = array(
			'slug' => $slug,
			'fields' => array(
					'version' => false,
			),
		);

		$response = wp_remote_post(
			'http://api.wordpress.org/plugins/info/1.0/',
			array(
				'body' => array(
					'action' => 'plugin_information',
					'request' => serialize((object) $args),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		} else {
			$response = unserialize( wp_remote_retrieve_body( $response ) );

			if( $response ) {
				return $response;
			} else {
				return false;
			}
		}
	}

	public function get_installed_plugin_data( $basename = '' ) {
		if( empty( $basename ) ) {
			return false;
		}
		if( ! function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		return isset( $plugins[ $basename ] ) ? $plugins[ $basename ] : false;
	}

	public function upgrade_or_install_plugin( $basename = '', $upgrade = true ) {
		if ( empty( $basename ) ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

		$skin = new \Automatic_Upgrader_Skin;
		$upgrader = new \Plugin_Upgrader( $skin );

		if ( $upgrade == true ) {
			$upgrader->upgrade( $basename );
		} else {
			$upgrader->install( $basename );
			activate_plugin( $upgrader->plugin_info(), '', false, true );
		}

		return $skin->result;
	}

	public function safe_path( $path ) {
		$path = str_replace(['//', '\\\\'], ['/', '\\'], $path);
		return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
	}
}