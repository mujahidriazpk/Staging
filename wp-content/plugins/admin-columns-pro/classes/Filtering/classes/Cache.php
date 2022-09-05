<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ACP_Filtering_Cache {

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @param string $key
	 */
	public function __construct( $key ) {
		$this->set_key( $key );
	}

	/**
	 * Set Cache id. Max length for site_transient name is 40 characters,
	 *
	 * @param string $name
	 * @source https://core.trac.wordpress.org/ticket/15058
	 */
	private function set_key( $key ) {
		$this->key = md5( $key );
	}

	/**
	 * @param mixed       $data
	 * @param null|string $expiration
	 */
	public function put( $data, $expiration = null ) {
		update_site_option( 'ac_cache_data_' . $this->key, $data );

		$this->set_expiration( $expiration );
	}

	/**
	 * @return string|false
	 */
	public function get() {
		return get_site_option( 'ac_cache_data_' . $this->key );
	}

	/**
	 * @return bool
	 */
	public function is_expired() {
		$expired = get_site_option( 'ac_cache_expires_' . $this->key );

		return ! $expired || time() > $expired;
	}

	/**
	 * @param null|int $expiration Expiration in seconds
	 */
	public function set_expiration( $expiration = null ) {
		if ( null === $expiration || ! preg_match( '/^[1-9][0-9]*$/', $expiration ) ) {
			$expiration = 10;
		}

		update_site_option( 'ac_cache_expires_' . $this->key, time() + $expiration );
	}

	public function delete() {
		delete_site_option( 'ac_cache_data_' . $this->key );
		delete_site_option( 'ac_cache_expires_' . $this->key );
	}

}
