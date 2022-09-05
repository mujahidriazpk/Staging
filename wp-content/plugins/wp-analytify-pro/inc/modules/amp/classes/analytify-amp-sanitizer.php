<?php

// If file directly access or AMP is not activated.
if ( ! defined( 'ABSPATH' ) || ! defined( 'AMP__FILE__' ) ) {
	die;
}

require_once AMP__DIR__ . '/includes/sanitizers/class-amp-base-sanitizer.php';

class Analytify_AMP_Sanitizer extends AMP_Base_Sanitizer {

	/**
	 * Sanitize the HTML contained in the DOMDocument received by the constructor
	 *
	 * @since 1.0.0
	 */
	public function sanitize() {
		$this->options = array(
			'outbound_link'       => $this->get_outbound_links(),
			'download_extensions' => $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'file-extension', 'wp-analytify-file-download', 'zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*' ),
		);

		$body = $this->get_body_node();
		$this->parse_nodes_recursive( $body );
	}

	/**
	 * Passes through the DOM and removes stuff that shouldn't be there.
	 *
	 * @param DOMNode $node
	 *
	 * @since 1.0.0
	 */
	private function parse_nodes_recursive( $node ) {
		if ( $node->nodeType !== XML_ELEMENT_NODE ) {
			return;
		}
		if ( $node->nodeName === 'a' ) {
			$node_name = $node->nodeName;
			$this->parse_href( $node );
		}
		foreach ( $node->childNodes as $child_node ) {
			$this->parse_nodes_recursive( $child_node );
		}
	}

	/**
	 * Sanitizes anchor attributes
	 *
	 * @param DOMNode $node
	 * @param object  $attribute
	 *
	 * @since 1.0.0
	 */
	private function parse_href( $node ) {
		$href  = $node->getAttribute( 'href' );
		$hrefe = esc_attr( $href );
		$label = $this->get_label( $node );
		$category = ! empty( $node->getAttribute('data-vars-ga-category') ) ? esc_attr( $node->getAttribute('data-vars-ga-category') ) : '';
		$action   = ! empty( $node->getAttribute('data-vars-ga-action') )   ? esc_attr( $node->getAttribute('data-vars-ga-action') )   : $hrefe;

		// if has Javascript in link
		if ( substr( $href, 0, strlen( 'javascript:' ) ) === 'javascript:' ) {
			return;
		}

		$title = esc_attr( $node->getAttribute( 'title' ) );
		$class = esc_attr( $node->getAttribute( 'class' ) );
		if ( ! empty( $class ) ) {
			$class = $class . ' ';
		}

		if ( empty( $title ) ) {
			$title = esc_attr( $node->nodeValue );
		}

		if ( ! empty( $category ) ) {
			$node->setAttribute( 'class', $class . 'analytify-link' );
			$node->setAttribute( 'data-vars-category', $category ); // type of link
			$action = $action ? $action : 'click';
			$node->setAttribute( 'data-vars-action', $action );  // href
			$label = $label ? $label : $title;
			$node->setAttribute( 'data-vars-label', $label ); // Link text
			return;
		}

		// for telephone.
		if ( substr( $href, 0, strlen( 'tel:' ) ) === 'tel:' ) {
			$node->setAttribute( 'class', $class . 'analytify-amp-tel' );
			$node->setAttribute( 'data-vars-ga-category', 'tel' );
			$node->setAttribute( 'data-vars-ga-action', $hrefe );
			$node->setAttribute( 'data-vars-ga-label', $label );
			return;
		}

		// for mail.
		if ( substr( $href, 0, strlen( 'mailto:' ) ) === 'mailto:' ) {
			$node->setAttribute( 'class', $class . 'analytify-amp-mail' );
			$node->setAttribute( 'data-vars-ga-category', 'mail' );
			$node->setAttribute( 'data-vars-ga-action', $hrefe );
			$node->setAttribute( 'data-vars-ga-label', $label );
			return;
		}

		$url = wp_parse_url( $href );

		// for download.
		if ( ! empty( $url['path'] ) && ! empty( $this->options['download_extensions'] ) ) {
			if ( preg_match( '/' . $this->options['download_extensions'] . '/', $url['path'] ) ) {
				$node->setAttribute( 'class', $class . 'analytify-amp-download' );
				$node->setAttribute( 'data-vars-ga-category', 'download' );
				$node->setAttribute( 'data-vars-ga-action', $hrefe );
				$node->setAttribute( 'data-vars-ga-label', $label );
				return;
			}
		}

		// for internal as outbound
		if ( ! empty( $this->options['outbound_link'] ) ) {
			foreach ( $this->options['outbound_link'] as $path ) {
				if ( $this->_string_starts_with( $url['path'], $path ) ) {
					$node->setAttribute( 'class', $class . 'analytify-internal-as-outbound' );
					$node->setAttribute( 'data-vars-ga-category', 'outbound-link' );
					$node->setAttribute( 'data-vars-ga-action', $hrefe );
					$node->setAttribute( 'data-vars-ga-label', $label );
					return;
				}
			}
		}

		// for external link.
		$current_url = home_url();
		$current_url = str_replace( 'www.', '', $current_url );
		if ( ! $this->_string_ends_with( $url['host'], $current_url ) ) {
			$node->setAttribute( 'class', $class . 'analytify-outbound-link' );
			$node->setAttribute( 'data-vars-category', 'outbound-link' );
			$node->setAttribute( 'data-vars-action', $hrefe );
			$node->setAttribute( 'data-vars-label', $label );
			return;
		}

	}

	/**
	 * Helper function to check external link.
	 *
	 * @since 1.0.0
	 */
	private function _string_ends_with( $string, $ending ) {
		$strlen    = strlen( $string );
		$endinglen = strlen( $ending );
		if ( $endinglen > $strlen ) {
			return false;
		}
		return substr_compare( $string, $ending, $strlen - $endinglen, $endinglen ) === 0;
	}

	/**
	 * Helper function to check outbound link.
	 *
	 * @since 1.0.0
	 */
	private function _string_starts_with( $string, $start ) {
		return substr( $string, 0, strlen( $start ) ) === $start;
	}

	/**
	 * Return Outbound option.
	 *
	 * @since 1.0.0
	 */
	private function get_outbound_links() {
		$links = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'affiliate-link-path', 'wp-analytify-affiliate-link' );
		return explode( ',', $links );
	}

	/**
	 * Get Label from link.
	 *
	 * @since 1.0.0
	 */
	private function get_label( $node ) {
		if ( $node->hasAttribute( 'data-vars-ga-label' ) ) {
			return $node->getAttribute( 'data-vars-ga-label' );
		} elseif ( $node->hasAttribute( 'title' ) ) {
			return $node->getAttribute( 'title' );
		} elseif ( $node->textContent ) {
			return $node->textContent;
		} else {
			return $node->getAttribute( 'href' );
		}
	}

}
