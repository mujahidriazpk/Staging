<?php

namespace ACP\Editing\Model\Media;

use AC;
use ACP\Editing\Model;

class MetaData extends Model {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @param AC\Column $column
	 * @param string    $meta_key
	 */
	public function __construct( AC\Column $column, $meta_key ) {
		parent::__construct( $column );

		$this->meta_key = $meta_key;
	}

	public function get_edit_value( $id ) {
		$raw = get_post_meta( $id, '_wp_attachment_metadata', true );

		return isset( $raw[ $this->meta_key ] )
			? $raw[ $this->meta_key ]
			: false;
	}

	public function save( $id, $value ) {
		$new_value = get_post_meta( $id, '_wp_attachment_metadata', true );
		if ( ! $new_value ) {
			$new_value = [];
		}

		$new_value[ $this->meta_key ] = $value;

		return (bool) update_post_meta( $id, '_wp_attachment_metadata', $new_value );
	}

}