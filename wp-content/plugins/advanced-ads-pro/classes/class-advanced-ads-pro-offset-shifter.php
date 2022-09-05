<?php
/**
 * Calculates new offsets based on amount of words between ads.
 */
class Advanced_Ads_Pro_Offset_Shifter {
	/**
	 * Default options.
	 *
	 * @var array
	 */
	protected $default_options = array(
		// Required amount of words between ads.
		'words_between_repeats'        => 20,
		// Whether to check the required amount of words before the first item.
		'require_before_first'         => false,
		// Whether to check the required amount of words after the last item.
		'require_after_last'           => false,
		'debug'                        => false,
		'before'                       => false,
		'paragraph_select_from_bottom' => false,
	);

	/**
	 * Amount of words between items.
	 *
	 * @var array
	 */
	protected $words_between = array();

	/**
	 * Previous offset.
	 *
	 * @var false/int
	 */
	protected $previous_offset = false;

	const START_EXISTING_AD = 'advads_amount_of_words_s';
	const END_EXISTING_AD   = 'advads_amount_of_words_e';
	const INSERTION_POINT   = 'advads_amount_of_words_i';
	const SPLIT_REGEXP      = '/(advads_amount_of_words_.)/';
	const PREV_WORDS        = 'prev_words';
	const PREV_NUMBER       = 'prev_number';
	const NEXT_WORDS        = 'next_words';
	const NEXT_NUMBER       = 'next_number';
	const PREV_IS_AD        = 'prev_is_ad';
	const NEXT_IS_AD        = 'next_is_ad';

	/**
	 * Create an object of the class.
	 *
	 * @param string $html HTML string.
	 * @param array  $options Options.
	 * @return object Object of this class.
	 */
	public static function from_html( $html, $options ) {
		$libxml_previous_state = libxml_use_internal_errors( true );
		$dom                   = new DOMDocument( '1.0', 'UTF-8' );
		$success               = $dom->loadHtml( '<!DOCTYPE html><html><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><body>' . $html );
		libxml_clear_errors();
		libxml_use_internal_errors( $libxml_previous_state );

		$xpath = new DOMXPath( $dom );

		return new static( $dom, $xpath, $options );
	}

	/**
	 * Constructor.
	 *
	 * @param DOMDocument $dom DOMDocument object.
	 * @param DOMXPath    $xpath DOMXpath object.
	 * @param array       $options Options.
	 */
	public function __construct( DOMDocument $dom, DOMXPath $xpath, array $options ) {
		$this->dom     = $dom;
		$this->xpath   = $xpath;
		$this->options = array_merge( $this->default_options, $options );
	}

	/**
	 * Prepare HTML for parsing.
	 *
	 * @param array $items Existing selected items.
	 * @return string $r String with injected patterns.
	 */
	private function prepare_for_parsing( $items ) {
		$expr     = $this->get_expression_for_existing_ads();
		$existing = $this->xpath->query( $expr );

		$created = array();
		// Excel existing ads.
		foreach ( $existing as $node ) {
			$start = $this->dom->createTextNode( self::START_EXISTING_AD );
			$node->parentNode->insertBefore( $start, $node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$created[] = $start;

			$end = $this->dom->createTextNode( self::END_EXISTING_AD );
			$node->parentNode->insertBefore( $end, $node->nextSibling ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$created[] = $end;
		}

		// Excel items, e.g. paragraphs.
		foreach ( $items as $node ) {
			$point     = $this->dom->createTextNode( self::INSERTION_POINT );
			$created[] = $point;

			if ( $this->options['before'] ) {
				$node->parentNode->insertBefore( $point, $node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			} else {
				$node->parentNode->insertBefore( $point, $node->nextSibling ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}

		// Select all text nodes.
		$nodes = $this->xpath->query( '//text()[not(parent::script or parent::style)]' );
		$r     = '';
		foreach ( $nodes as $n ) {
			$r .= $n->data;
		}

		foreach ( $created as $node ) {
			$node->parentNode->removeChild( $node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		return $r;
	}

	/**
	 * Prepare the array that contains calculated amount of words between items.
	 *
	 * @param str $parts Post text splited by regexp.
	 * @return $r Word amounts between items.
	 */
	protected function prepare_words_between( $parts ) {
		$r          = array();
		$prev_is_ad = false;
		$doing_ad   = 0;
		$prev_text  = array();
		$prev_ip    = false;

		foreach ( $parts as $part ) {
			if ( self::START_EXISTING_AD === $part ) {
				if ( $prev_ip ) {
					$r       = $this->add_after_insertion_point( $r, $prev_text, true );
					$prev_ip = false;
				}
				$prev_text  = array();
				$prev_is_ad = true;
				$doing_ad++;
				continue;
			} elseif ( self::END_EXISTING_AD === $part ) {
				$doing_ad--;
				continue;
			} elseif ( $doing_ad ) {
				continue;
			} elseif ( self::INSERTION_POINT === $part ) {
				if ( $prev_ip ) {
					$r       = $this->add_after_insertion_point( $r, $prev_text );
					$prev_ip = false;
				}

				$data = array(
					self::PREV_NUMBER => self::calc_words( $prev_text ),
					self::PREV_IS_AD  => $prev_is_ad,
				);
				if ( $this->options['debug'] ) {
					$data[ self::PREV_WORDS ] = $prev_text;
				}
				$r[] = $data;

				$prev_text  = array();
				$prev_is_ad = false;
				$prev_ip    = true;
			} else {
				$prev_text[] = self::prepare_text( $part );

			}
		}
		if ( $prev_ip ) {
			$r = $this->add_after_insertion_point( $r, $prev_text );

			$prev_ip = false;
		}
		return $r;
	}

	/**
	 * Add words after insertion points.
	 *
	 * @param array $r Word amounts between items.
	 * @param array $prev_text Word amounts between items.
	 * @param bool  $next_is_ad Whether or an ad is placed after the point.
	 * @return $r Word amounts between items.
	 */
	private function add_after_insertion_point( $r, $prev_text, $next_is_ad = false ) {
		$el = array_pop( $r );

		$el[ self::NEXT_NUMBER ] = self::calc_words( $prev_text );
		$el[ self::NEXT_IS_AD ]  = $next_is_ad;

		if ( $this->options['debug'] ) {
			$el[ self::NEXT_WORDS ] = $prev_text;
		}

		$r[] = $el;
		return $r;
	}

	/**
	 * Get amount of words between items.
	 *
	 * @param string/array $items An array of `DOMElement`s or `XPath` query.
	 * @return array
	 */
	public function get_words_between( $items ) {
		if ( is_string( $items ) ) {
			$items = iterator_to_array( $this->xpath->query( $items ) );
		}

		$text                = $this->prepare_for_parsing( $items );
		$parts               = preg_split( self::SPLIT_REGEXP, $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		$this->words_between = $this->prepare_words_between( $parts );
		return $this->words_between;
	}

	/**
	 * Calculate new offsets.
	 *
	 * They may be shifted to provide the minimum amount of words before and after or deleted,
	 * if shifting is not possible
	 *
	 * @param array        $offsets Existing offsets.
	 * @param string/array $items An array of `DOMElement`s or `XPath` query.
	 * @return array $offsets New offsets.
	 */
	public function calc_offsets( array $offsets, $items ) {
		$words_between = $this->get_words_between( $items );

		$from_bottom = $this->options['paragraph_select_from_bottom'];

		if ( $from_bottom ) {
			rsort( $offsets );
		} else {
			sort( $offsets );
		}

		$new_offsets           = array();
		$this->previous_offset = false;

		$this->debug( '///// debug start: ' . implode( ', ', $offsets ) . ' /////' );

		foreach ( $offsets as $k => $offset ) {

			if ( $from_bottom ) {
				$new_offset = $this->calc_offset_from_bottom( $offset );
			} else {
				$new_offset = $this->calc_offset( $offset );
			}

			if ( false === $new_offset ) {
				break;
			}

			$new_offsets[] = $new_offset;
		}

		$this->debug( '///// debug end /////' );

		return $new_offsets;
	}

	/**
	 * Calculate offset.
	 *
	 * @param int $offset Offset.
	 * @return int/false New shifted offset or false.
	 */
	private function calc_offset( $offset ) {
		$l = count( $this->words_between );

		if ( false !== $this->previous_offset ) {
			$offset = max( $offset, $this->previous_offset + 1 );
		}

		$this->debug( "///// offset: $offset /////" );

		for ( $i = $offset; $i < $l; $i++ ) {

			if ( ! isset( $this->words_between[ $i ] ) ) {
				continue;
			}
			$prev_num = 0;
			$next_num = 0;

			$prev_has_ad = false;
			for ( $j = $i; $j >= 0; $j-- ) {
				if ( $this->options['debug'] ) {
					$this->debug( implode( ' / ', $this->words_between[ $j ][ self::PREV_WORDS ] ) );
				}

				$prev_num += $this->words_between[ $j ][ self::PREV_NUMBER ];

				if ( $this->words_between[ $j ][ self::PREV_IS_AD ] ) {
					$prev_has_ad = true;
					break;
				}
			}

			$this->debug( $i . ': prev: ' . $prev_num );

			if ( $prev_num < $this->options['words_between_repeats']
				// Check if this is the first item and there are no ads before it.
				&& ( $this->options['require_before_first'] || $prev_has_ad || false !== $this->previous_offset ) ) {
					continue;
			}

			$next_has_ad = false;
			for ( $k = $i; $k < $l; $k++ ) {
				$next_num += $this->words_between[ $k ][ self::NEXT_NUMBER ];
				if ( $this->words_between[ $k ][ self::NEXT_IS_AD ] ) {
					$next_has_ad = true;
					break;
				}
			}

			if ( $next_num < $this->options['words_between_repeats']
				// Check if there are no ads after the last item.
				&& ( $this->options['require_after_last'] || $next_has_ad ) ) {
				continue;
			}

			$this->previous_offset = $i;

			$this->words_between[ $i ][ self::NEXT_IS_AD ] = true;
			if ( isset( $this->words_between[ $i + 1 ] ) ) {
				$this->words_between[ $i + 1 ][ self::PREV_IS_AD ] = true;
			}
			$this->debug( "found $i" );
			return $i;
		}
		return false;
	}

	/**
	 * Calculate offset from bottom.
	 *
	 * @param int $offset Offset.
	 * @return int/false New shifted offset or false.
	 */
	private function calc_offset_from_bottom( $offset ) {
		$l = count( $this->words_between );

		if ( false !== $this->previous_offset ) {
			$offset = min( $offset, $this->previous_offset - 1 );
		}

		$this->debug( "///// offset: $offset /////" );

		for ( $i = $offset; $i >= 0; $i-- ) {

			if ( ! isset( $this->words_between[ $i ] ) ) {
				continue;
			}
			$prev_num = 0;
			$next_num = 0;

			$prev_has_ad = false;
			for ( $j = $i; $j < $l; $j++ ) {
				if ( $this->options['debug'] ) {
					$this->debug( implode( ' / ', $this->words_between[ $j ][ self::NEXT_WORDS ] ) );
				}

				$prev_num += $this->words_between[ $j ][ self::NEXT_NUMBER ];

				if ( $this->words_between[ $j ][ self::NEXT_IS_AD ] ) {
					$prev_has_ad = true;
					break;
				}
			}

			$this->debug( $i . ': prev: ' . $prev_num );

			if ( $prev_num < $this->options['words_between_repeats']
				// Check if this is the first item and there are no ads before it.
				&& ( $this->options['require_before_first'] || $prev_has_ad || false !== $this->previous_offset ) ) {
					continue;
			}

			$next_has_ad = false;
			for ( $k = $i; $k >= 0; $k-- ) {
				$next_num += $this->words_between[ $k ][ self::NEXT_NUMBER ];
				if ( $this->words_between[ $k ][ self::PREV_IS_AD ] ) {
					$next_has_ad = true;
					break;
				}
			}

			if ( $next_num < $this->options['words_between_repeats']
				// Check if there are no ads after the last item.
				&& ( $this->options['require_after_last'] || $next_has_ad ) ) {
				continue;
			}

			$this->previous_offset = $i;

			$this->words_between[ $i ][ self::PREV_IS_AD ] = true;
			if ( isset( $this->words_between[ $i - 1 ] ) ) {
				$this->words_between[ $i - 1 ][ self::NEXT_IS_AD ] = true;
			}
			$this->debug( "found $i" );
			return $i;
		}
		return false;
	}

	/**
	 * Get xpath expression for selecting existing ads.
	 *
	 * @return string XPath expression.
	 */
	protected function get_expression_for_existing_ads() {
		$expr = array(
			// The assumption is that a `div` that has a class starting with the frontend prefix is ad.
			"//div[@class and contains(concat(' ', normalize-space(@class), ' '), ' %s')]",
			// Waiting for consent ads (Privacy module): `<script type="text/plain" data-tcf="waiting-for-consent" data-id="..." data-bid="..."`
			"//comment()[contains(.,'data-tcf=\"waiting-for-consent')]"
		);

		return sprintf(
			implode( ' | ', $expr ),
			sanitize_html_class( Advanced_Ads_Plugin::get_instance()->get_frontend_prefix() )
		);

	}

	/**
	 * Prepare text for counting words.
	 *
	 * @param str $text Text.
	 * @return str $text Text.
	 */
	protected static function prepare_text( $text ) {
		$text = normalize_whitespace( $text );
		$text = str_replace( "\n", ' ', $text );
		// Replace punctuation.
		$text = preg_replace( '/[.(),;:!?%#$¿\'"_+=\\/-]+/', '', $text );
		return $text;
	}

	/**
	 * Calculate words.
	 *
	 * @param str $text Text.
	 * @return int Word count.
	 */
	protected static function calc_words( $text ) {
		if ( is_array( $text ) ) {
			$text = implode( ' ', $text );
		}

		$r = count( preg_split( '/\ +/', $text, -1, PREG_SPLIT_NO_EMPTY ) );
		return $r;
	}

	/**
	 * Print info for debugging.
	 *
	 * @param str $str String.
	 */
	protected function debug( $str ) {
		if ( $this->options['debug'] ) {
			echo "\n" . esc_html( $str );
		}
	}

	/**
	 * Check if 'Before Content' placement can be used.
	 *
	 * @return bool
	 */
	public function can_inject_before_content_placement() {
		$query    = '(' . $this->get_expression_for_existing_ads() . ')[1]';
		$existing = $this->xpath->query( $query );
		$existing = iterator_to_array( $existing );

		if ( $existing ) {
			$last = end( $existing );
			// Select all text before the first ad.
			$texts = $this->xpath->query( './/preceding::text()[not(parent::script or parent::style)]', $last );
		} else {
			// Select all text.
			$texts = $this->xpath->query( '//text()[not(parent::script or parent::style)]' );
		}

		$texts = iterator_to_array( $texts );

		$l = 0;
		foreach ( $texts as $text ) {
			$l += $this->calc_words( $this->prepare_text( $text->data ) );
		}
		return $l >= $this->options['words_between_repeats'];
	}

	/**
	 * Check if 'After Content' placement can be used.
	 *
	 * @return bool
	 */
	public function can_inject_after_content_placement() {
		// Insert a node to guarantee that there is a following node after the last ad.
		if ( isset( $this->dom->documentElement ) && isset( $this->dom->documentElement->lastChild ) ) {
			$last_node = $this->dom->createTextNode( '/' );
			$this->dom->documentElement->lastChild->appendChild( $last_node );
		}

		// Select following nodes of the ads.
		$query    = $this->get_expression_for_existing_ads() . '/following::node()[1]';
		$existing = $this->xpath->query( $query );
		$existing = iterator_to_array( $existing );

		if ( $existing ) {
			$last  = end( $existing );
			// Select all text before the first ad.
			$texts = $this->xpath->query( './/following::text()[not(parent::script or parent::style)]', $last );
			$texts = iterator_to_array( $texts );
			if ( $last instanceof DOMText ) {
				array_unshift( $texts, $last );
			}
		} else {
			// Select all text.
			$texts = $this->xpath->query( '//text()[not(parent::script or parent::style)]' );
			$texts = iterator_to_array( $texts );
		}

		$l = 0;
		foreach ( $texts as $text ) {
			$l += $this->calc_words( $this->prepare_text( $text->data ) );
		}
		return $l >= $this->options['words_between_repeats'];
	}

}

