<?php
/**
 * Google Ad Manager Ad Type
 */

class Advanced_Ads_Gam_Ad_Type extends Advanced_Ads_Ad_Type_Abstract {

	/**
	 * ID - internal type of the ad type
	 *
	 * @var string
	 */
	public $ID = 'gam';

	/**
	 * Responsive form class
	 *
	 * @var string
	 */
	public $responsive_form = 'Advanced_Ads_Gam_Admin_Responsive_Form';

	/**
	 * Set basic attributes
	 */
	public function __construct() {
		$this->title       = __( 'Google Ad Manager', 'advanced-ads-gam' );
		$this->description = __( 'Use ad units from your Google Ad Manager account', 'advanced-ads-gam' );
		$this->parameters  = array(
			'content' => '',
		);
	}

	/**
	 * Output for the ad parameters metabox
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 */
	public function render_parameters( $ad ) {

		$ad_content       = ( $ad->content ) ? trim( $ad->content ) : '';
		$network          = Advanced_Ads_Network_Gam::get_instance();
		$responsive_sizes = isset( $ad->options()['output']['responsive-sizes'] );

		require_once AAGAM_BASE_PATH . 'admin/views/ad-parameters.php';

	}

	/**
	 * Get ad sizes from the ad object
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @return array|bool false if the GAM ad does not have sizes.
	 */
	public static function get_ad_unit_sizes( $ad ) {
		if ( empty( $ad->content ) ) {
			return false;
		}

		// the `true` argument converts the object into an array.
		return self::get_ad_unit_sizes_from_ad_content( json_decode( base64_decode( $ad->content ), true ) );
	}

	/**
	 * Generate a json string for the ad sizes selected for this ad so that we can load it using JS in the frontend.
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @return string
	 */
	public static function get_ad_sizes_json_string( $ad ) {

		$ad_options = $ad->options( 'output' );
		if ( ! isset( $ad_options['ad-sizes'] ) ) {
			return '{}';
		}

		return wp_json_encode( $ad_options['ad-sizes'] );
	}

	/**
	 * Get ad sizes from the ad unit data ($ad->content)
	 *
	 * @param array $ad_unit ad unit content.
	 * @return array|bool false if the GAM ad does not have sizes.
	 */
	public static function get_ad_unit_sizes_from_ad_content( $ad_unit ) {
		if ( empty( $ad_unit ) ) {
			return false;
		}

		$ad_unit = self::append_fluid_to_sizes( $ad_unit );

		/**
		 * Ads with just one size have a simple array in `adUnitSizes` while those with multiple sizes store them in a multidimensional array
		 * we convert the version with just one size to a multidimensional array to later be able to handle them equally
		 */
		$ad_unit_sizes = array();
		if ( ! empty( $ad_unit['adUnitSizes'] ) && is_array( $ad_unit['adUnitSizes'] ) ) {
			if ( isset( $ad_unit['adUnitSizes']['size'] ) ) {
				$ad_unit_sizes = array( $ad_unit['adUnitSizes'] );
			} else {
				$ad_unit_sizes = $ad_unit['adUnitSizes'];
			}
		}

		// we are using the `fullDisplayString` value as an index to be able to identify the sizes
		$ad_unit_with_keys = array();
		foreach ( $ad_unit_sizes as $_ad_unit ) {
			if ( isset( $_ad_unit['fullDisplayString'] ) ) {
				$ad_unit_with_keys[ $_ad_unit['fullDisplayString'] ] = $_ad_unit;
			}
		}

		return array() !== $ad_unit_with_keys ? $ad_unit_with_keys : false;
	}

	/**
	 * Build ad path using the parent Path
	 *
	 * @param array $ad_unit The ad unit data.
	 * @return string the path string.
	 */
	public function build_slot_path( $ad_unit ) {
		$path = '/' . $ad_unit['networkCode'] . '/';

		if ( ! isset( $ad_unit['parentPath']['adUnitCode'] ) ) {
			// another parent except the effective root ad unit.
			foreach ( $ad_unit['parentPath'] as $parent ) {
				if ( $parent['id'] == $ad_unit['effectiveRootAdUnitId'] ) {
					continue;
				}
				$path .= $parent['adUnitCode'] . '/';
			}
		}

		$path .= $ad_unit['adUnitCode'];
		return $path;

	}

	/**
	 * Append 'fluid' size into 'adUnitSizes' field in a way that resemble to Google's data. Create the field if needed
	 *
	 * @param [array] $ad_unit the original ad unit array.
	 * @return [array] the modified unit
	 */
	public static function append_fluid_to_sizes( $ad_unit ) {
		if ( isset( $ad_unit['isFluid'] ) && $ad_unit['isFluid'] ) {
			// Do not append it more than once.
			if ( isset( $ad_unit['hasFluidSize'] ) ) {
				return $ad_unit;
			}

			if ( isset( $ad_unit['adUnitSizes'] ) ) {
				if ( isset( $ad_unit['adUnitSizes']['fullDisplayString'] ) ) {
					// single size
					$single_size              = $ad_unit['adUnitSizes'];
					$ad_unit['adUnitSizes']   = array();
					$ad_unit['adUnitSizes'][] = $single_size;
					$ad_unit['adUnitSizes'][] = array(
						'size'              => array(),
						'fullDisplayString' => 'fluid',
					);
				} else {
					$ad_unit['adUnitSizes'][] = array(
						'size'              => array(),
						'fullDisplayString' => 'fluid',
					);
				}
			} else {
				$ad_unit['adUnitSizes'] = array(
					'size'              => array(),
					'fullDisplayString' => 'fluid',
				);
			}
			$adUnit['hasFluidSize'] = true;
		}
		return $ad_unit;
	}

	/**
	 * Build size string parameter for an ad unit
	 *
	 * @param array $ad_unit The ad unit data.
	 * @return string the size string.
	 */
	public function build_size_string( $ad_unit ) {

		$ad_unit = self::append_fluid_to_sizes( $ad_unit );

		if ( ! isset( $ad_unit['adUnitSizes'] ) ) {
			return '';
		}

		$ad_size_string = '';

		if ( isset( $ad_unit['adUnitSizes']['size'] ) ) {
			// single size element.
			if ( ! empty( $ad_unit['adUnitSizes']['size'] ) ) {
				$ad_size_string = '[' . $ad_unit['adUnitSizes']['size']['width'] . ',' . $ad_unit['adUnitSizes']['size']['height'] . ']';
			} else {
				$ad_size_string = "['fluid']";
			}
		} else {
			$size_array = array();
			foreach ( $ad_unit['adUnitSizes'] as $size ) {
				if ( ! empty( $size['size'] ) ) {
					$size_array[] = '[' . $size['size']['width'] . ',' . $size['size']['height'] . ']';
				} else {
					$size_array[] = "'fluid'";
				}
			}
			$ad_size_string = '[' . implode( ',', $size_array ) . ']';
		}

		return $ad_size_string;
	}

	/**
	 * Build a string that returns an array with the ad unit sizes that match the screen width
	 * if the appropriate option is enabled in the ad settings
	 *
	 * If the Ad sizes option that makes the ad fully responsive is enabled, our size string is
	 * a dynamic function filtering the available sizes by available width
	 *
	 * @param string          $ad_size_string JS array with the ad sizes.
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @param string          $container_id ID of the container in the frontend.
	 *
	 * @return string the string that filters the size or the original string if the option was not selected
	 */
	public function maybe_build_responsive_size_string( $ad_size_string, $ad, $container_id ) {

		// Do not use it on AMP pages.
		if ( advads_is_amp() ) {
			return $ad_size_string;
		}

		if ( ! isset( $ad->options( 'output' )['responsive-sizes'] )
		|| '[]' === $ad_size_string ) {
			return $ad_size_string;
		}

		/**
		 * Manipulate the string to become an array, if there is just one element
		 * e.g., [300,250] => [[300,250]]
		 * we are just checking if the string has only a single comma
		 */
		if ( 1 === substr_count( $ad_size_string, ',' ) ) {
			$ad_size_string = "[$ad_size_string]";
		}

		return $ad_size_string . ".filter( el => el[0] <= document.querySelector( '#$container_id').clientWidth || 'fluid' == el )";

	}

	/**
	 * Build sizeMapping object.
	 *
	 * @link https://developers.google.com/doubleclick-gpt/guides/ad-sizes#responsive_ads
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @param string          $container_id ID of the container in the frontend.
	 * @return string the sizeMapping array. returns an empty string if not set
	 */
	public function build_size_mapping_object( $ad, $container_id ) {

		/**
		 * Load the size mapping options from the ad unit.
		 */
		$ad_options = $ad->options( 'output' );
		$ad_sizes   = isset( $ad_options['ad-sizes'] ) ? $ad_options['ad-sizes'] : null;

		/**
		 * Load the sizes set up with the ad in the GAM account.
		 */
		$ad_unit_sizes = self::get_ad_unit_sizes( $ad );

		if ( ! $ad_unit_sizes ) {
			return '';
		}

		/**
		 * Sanitize Ad sizes option on output
		 * remove the options when none or all of the possible options are selected
		 * this prevents the output in the code, which is not needed in this case
		 */
		if ( is_array( $ad_sizes ) && count( $ad_sizes ) && $ad_unit_sizes ) {
			$rows           = count( $ad_sizes );
			$max_checkboxes = $rows * count( $ad_unit_sizes );

			// collect all selected options
			$selected_checkboxes = 0;
			foreach ( $ad_sizes as $_option ) {
				if ( isset( $_option['sizes'] ) && is_array( $_option['sizes'] ) ) {
					$selected_checkboxes += count( $_option['sizes'] );
				}
			}

			if ( ! $selected_checkboxes || $max_checkboxes === $selected_checkboxes ) {
				return '';
			}
		}

		if ( ! $ad_sizes ) {
			return '';
		}

		/**
		 * We are iterating through the sizeMapping options (ad sizes) from the backend
		 * and compare them to the available ad unit sizes stored in the GAM ad unit code
		 * only ad sizes in the ad unit code will be considered in the output
		 */
		$size_strings = array();
		// order options starting with highest minimum width.
		krsort( $ad_sizes );
		foreach ( $ad_sizes as $_min_width => $_saved_sizes ) {
			// match our saved options with the sizes of the ad
			$ad_unit_sizes_for_output = array();
			// if the sizes option is missing then it might mean that the ad code should stay empty
			if ( ! isset( $_saved_sizes['sizes'] ) ) {
				$ad_unit_sizes_for_output[] = '[]';
			} else {
				foreach ( $ad_unit_sizes as $_ad_size ) {
					if ( isset( $_ad_size['fullDisplayString'] )
						&& ( ( isset( $_ad_size['size']['width'] ) && isset( $_ad_size['size']['height'] ) ) || 'fluid' === $_ad_size['fullDisplayString'] )
						&& in_array( $_ad_size['fullDisplayString'], $_saved_sizes['sizes'], true ) ) {
							// sanitize output
						if ( 'fluid' === $_ad_size['fullDisplayString'] ) {
							$ad_unit_sizes_for_output[] = "'fluid'";
						} else {
							$ad_width  = absint( $_ad_size['size']['width'] );
							$ad_height = absint( $_ad_size['size']['height'] );
							// build the object with all sizes
							$ad_unit_sizes_for_output[] = "[{$ad_width}, {$ad_height}]";
						}
					}
				}
			}
			if ( count( $ad_unit_sizes_for_output ) ) {
				$min_width            = absint( $_min_width );
				$ad_unit_sizes_string = 1 === count( $ad_unit_sizes_for_output ) ? $ad_unit_sizes_for_output[0] : '[' . implode( ', ', $ad_unit_sizes_for_output ) . ']';
				$ad_unit_sizes_string = $this->maybe_build_responsive_size_string( $ad_unit_sizes_string, $ad, $container_id );
				$size_strings[]       = "addSize([{$min_width}, 0], {$ad_unit_sizes_string}).";
			}
		}

		// build the output string if the array is not empty.
		if ( empty( $size_strings ) ) {
			return '';
		}

		return 'var mapping = googletag.sizeMapping().'
			   . "\n"
			   . implode( "\n", $size_strings )
			   . "\n"
			   . 'build();'
			   . "\n";
	}

	/**
	 * Return the key value targeting output (if any)
	 *
	 * @param [Advanced_Ads_Ad] $ad the current ad object.
	 * @return [string] the front end output (JS code).
	 */
	private function get_key_values_output( $ad ) {
		$js      = '';
		$options = $ad->options();

		if ( isset( $options['gam-keyval'] ) && is_array( $options['gam-keyval'] ) ) {

			$custom_key = array();
			$postmeta   = array();
			$usermeta   = array();

			foreach ( $options['gam-keyval'] as $kv ) {

				switch ( $kv['type'] ) {
					case 'categories':
						if ( is_single() || is_category() ) {
							$_categories = get_the_category( get_the_ID() );
							if ( is_category() ) {
								$query_obj = get_queried_object();
								$js       .= ".setTargeting( '" . $kv['key'] . "', '" . $query_obj->slug . "' )";
								break;
							}
							$categories = array();
							if ( ! empty( $_categories ) ) {
								foreach ( $_categories as $term ) {
									$categories[] = $term->slug;
								}
							}
							if ( ! empty( $categories ) ) {
								if ( 1 == count( $categories ) ) {
									// Category page OR single post that belongs to only one category.
									$js .= ".setTargeting( '" . $kv['key'] . "', '" . $categories[0] . "' )";
								} else {
									// Single post in multiple categories.
									$js .= ".setTargeting( '" . $kv['key'] . "', ['" . implode( '\', \'', $categories ) . "'] )";
								}
							}
						}
						break;
					case 'post_types':
						$post_type = false;
						if ( is_singular() || is_post_type_archive() ) {
							$post_type = get_post_type( get_the_ID() );
						}

						// custom taxonomy archive (like product category page of WooCommerce)
						if ( is_archive() ) {
							$pt        = get_post_type( get_the_ID() );
							$post_type = $pt;
						}

						if ( $post_type ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $post_type . "' )";
						}
						break;
					case 'page_slug':
						if ( is_category() ) {
							// Category slug of the current category page.
							$query_obj = get_queried_object();
							$js       .= ".setTargeting( '" . $kv['key'] . "', '" . $query_obj->slug . "' )";
						}
						// custom taxonomy archive (like product category page of WooCommerce)
						if ( is_archive() ) {
							$post_type = get_post_type( get_the_ID() );
							if ( ! in_array( $post_type, array( 'post', 'page', 'attachment' ) ) ) {
								$query_obj = get_queried_object();
								if ( ! empty( $query_obj ) ) {
									if ( isset( $query_obj->term_id ) ) {
										$term = get_term( $query_obj->term_id );
										if ( ! empty( $term ) ) {
											if ( is_array( $term ) ) {
												$term = $term[0];
											}
											$js .= ".setTargeting( '" . $kv['key'] . "', '" . $term->slug . "' )";
										}
									}
								}
							}
						}
						if ( is_singular() ) {
							// Single post or page.
							$post = get_post();
							$js  .= ".setTargeting( '" . $kv['key'] . "', '" . $post->post_name . "' )";
						}
						break;
					case 'page_type':
						$front = get_option( 'show_on_front' );
						if ( 'posts' == $front && is_front_page() ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'home' )";
							break;
						} else {
							if ( is_home() ) {
								$js .= ".setTargeting( '" . $kv['key'] . "', 'blog' )";
								break;
							}
						}
						if ( is_archive() ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'archive' )";
							break;
						} else {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'single' )";
							break;
						}
					case 'placement_id':
						if ( isset( $options['output'] ) && isset( $options['output']['placement_id'] ) ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $options['output']['placement_id'] . "' )";
						}
						break;
					case 'page_id':
						if ( is_singular() ) {
							$post = get_post();
							$js  .= ".setTargeting( '" . $kv['key'] . "', '" . $post->ID . "' )";
						}
						break;
					case 'usermeta':
						$user = wp_get_current_user();
						if ( ! empty( $user->ID ) ) {
							$meta = get_user_meta( $user->ID, $kv['value'], false );
							if ( is_array( $meta ) && 1 < count( $meta ) ) {
								if ( ! isset( $usermeta[ $kv['key'] ] ) ) {
									$usermeta[ $kv['key'] ] = array();
								}
								$usermeta[ $kv['key'] ][] = $meta;
							} else {
								if ( is_array( $meta ) && ! empty( $meta ) ) {
									$meta = $meta[0];
									if ( ! isset( $usermeta[ $kv['key'] ] ) ) {
										$usermeta[ $kv['key'] ] = array();
									}
									$usermeta[ $kv['key'] ][] = $meta;
								}
							}
						}
						break;
					case 'postmeta':
						$post_id = get_the_ID();
						$meta    = get_post_meta( $post_id, $kv['value'], false );
						if ( is_array( $meta ) && 1 < count( $meta ) ) {
							if ( ! isset( $postmeta[ $kv['key'] ] ) ) {
								$postmeta[ $kv['key'] ] = array();
							}
							$postmeta[ $kv['key'] ][] = $meta;
						} else {
							if ( is_array( $meta ) && ! empty( $meta ) ) {
								$meta = $meta[0];
								if ( ! isset( $postmeta[ $kv['key'] ] ) ) {
									$postmeta[ $kv['key'] ] = array();
								}
								$postmeta[ $kv['key'] ][] = $meta;
							}
						}
						break;
					case 'taxonomy':
						if ( is_archive() ) {
							$query_obj = get_queried_object();
							if ( ! empty( $query_obj ) && isset( $query_obj->taxonomy ) ) {
								$js .= ".setTargeting( '" . $kv['key'] . "', '" . $query_obj->taxonomy . "' )";
							}
						}
						break;
					case 'term':
						if ( is_home() ) {
							break;
						}
						if ( is_archive() ) {
							$query_obj = get_queried_object();
							if ( ! empty( $query_obj ) && isset( $query_obj->term_taxonomy_id ) && isset( $query_obj->slug ) ) {
								$js .= ".setTargeting( '" . $kv['key'] . "', '" . $query_obj->slug . "' )";
							}
						}
						if ( is_single() ) {
							$post_type = get_post_type();
							if ( 'post' == $post_type ) {
								$_categories = get_the_category();
								$categories  = array();
								if ( ! empty( $_categories ) ) {
									foreach ( $_categories as $term ) {
										$categories[] = $term->slug;
									}
								}
								if ( ! empty( $categories ) ) {
									if ( 1 == count( $categories ) ) {
										// Post that belongs to only one category.
										$js .= ".setTargeting( '" . $kv['key'] . "', '" . $categories[0] . "' )";
									} else {
										// Single post in multiple categories.
										$js .= ".setTargeting( '" . $kv['key'] . "', ['" . implode( '\', \'', $categories ) . "'] )";
									}
								}
							}
						}
						break;
					case 'terms':
						if ( is_home() ) {
							break;
						}
						if ( is_archive() && $kv['onarchives'] ) {
							$query_obj = get_queried_object();
							if ( ! empty( $query_obj ) && isset( $query_obj->term_taxonomy_id ) && isset( $query_obj->slug ) ) {
								$js .= ".setTargeting( '" . $kv['key'] . "', '" . $query_obj->slug . "' )";
							}
						} elseif ( is_single() ) {

							$the_post   = get_post();
							$taxonomies = get_object_taxonomies( $the_post, 'names' );
							$terms      = array();

							foreach ( $taxonomies as $taxo ) {
								$_terms = get_the_terms( $the_post, $taxo );
								if ( $_terms ) {
									foreach ( $_terms as $_term ) {
										$terms[] = $_term->slug;
									}
								}
							}
							if ( ! empty( $terms ) ) {
								if ( 1 < count( $terms ) ) {
									$js .= ".setTargeting( '" . $kv['key'] . "', [\"" . implode( '", "', $terms ) . '"] )';
								} else {
									$js .= ".setTargeting( '" . $kv['key'] . "', \"" . $terms[0] . '" )';
								}
							}
						}
						break;
					case 'custom':
						if ( ! isset( $custom_key[ $kv['key'] ] ) ) {
							$custom_key[ $kv['key'] ] = array();
						}
						$custom_key[ $kv['key'] ][] = $kv['value'];
						break;
					default:
				}
			}

			foreach ( $custom_key as $key => $value ) {
				if ( 1 < count( $value ) ) {
					$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $value ) . '"] )';
				} else {
					$js .= ".setTargeting( '" . $key . "', \"" . $value[0] . '" )';
				}
			}

			foreach ( $postmeta as $key => $value ) {
				if ( 1 < count( $value ) ) {
					$meta_values = array();
					foreach ( $value as $_value ) {
						if ( is_array( $_value ) ) {
							$meta_values += $_value;
						} else {
							$meta_values[] = $_value;
						}
					}
					$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $meta_values ) . '"] )';
				} else {
					if ( is_array( $value[0] ) ) {
						$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $value[0] ) . '"] )';
					} else {
						$js .= ".setTargeting( '" . $key . "', \"" . $value[0] . '" )';
					}
				}
			}

			foreach ( $usermeta as $key => $value ) {
				if ( 1 < count( $value ) ) {
					$meta_values = array();
					foreach ( $value as $_value ) {
						if ( is_array( $_value ) ) {
							$meta_values += $_value;
						} else {
							$meta_values[] = $_value;
						}
					}
					$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $meta_values ) . '"] )';
				} else {
					if ( is_array( $value[0] ) ) {
						$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $value[0] ) . '"] )';
					} else {
						$js .= ".setTargeting( '" . $key . "', \"" . $value[0] . '" )';
					}
				}
			}
		}
		return $js;
	}

	/**
	 * Prepare the ads frontend output
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 *
	 * @return string $content ad content prepared for frontend output.
	 */
	public function prepare_output( $ad ) {

		if ( empty( $ad->content ) ) {
			return '';
		}

		$ad_unit = json_decode( base64_decode( $ad->content ), true );

		if ( ! isset( $ad_unit['networkCode'] ) || ! isset( $ad_unit['adUnitCode'] ) ) {
			return '';
		}

		// we are mimicking the container IDs that GAM builds since they are not retrievable through the API.
		$p1 = mt_rand( intval( pow( 10, 5 ) ), intval( pow( 10, 6 ) ) - 1 );
		$p2 = mt_rand( intval( pow( 10, 6 ) ), intval( pow( 10, 7 ) ) - 1 );

		// GAM seems to add `-0` to all container IDs so let’s do this as well.
		$div_id = 'gpt-ad-' . $p1 . $p2 . '-0';

		// Load general GAM plugin settings.
		$setting = Advanced_Ads_Network_Gam::get_setting();
		$path    = $this->build_slot_path( $ad_unit );
		$size    = $this->build_size_string( $ad_unit );
		$size    = $this->maybe_build_responsive_size_string( $size, $ad, $div_id );

		// Output for sizeMapping (responsive ad units)
		$size_mapping_object = $this->build_size_mapping_object( $ad, $div_id );
		$size_mapping        = ( $size_mapping_object ) ? '.defineSizeMapping(mapping)' : '';

		// Output for the collapse option.
		$empty_div = '';
		if ( 'collapse' === $setting['empty-div'] ) {
			$empty_div = '.setCollapseEmptyDiv(true)';
		} elseif ( 'fill' === $setting['empty-div'] ) {
			$empty_div = '.setCollapseEmptyDiv(true,true)';
		}

		$key_values = $this->get_key_values_output( $ad );

		ob_start();

		if ( advads_is_amp() ) {
			$size = '[]';
			if ( isset( $ad->output['amp-ad-sizes'] ) ) {
				$size_array = array();
				foreach ( $ad->output['amp-ad-sizes'] as $ad_size ) {
					$size_array [] = $ad_size === 'fluid' ? "'fluid'" : '[' . str_replace( 'x', ',', $ad_size ) . ']';
				}
				$size = '[' . implode( ',', $size_array ) . ']';
			} elseif ( ! isset( $ad->output['amp-has-sizes'] ) ) {
				$size = $this->build_size_string( $ad_unit );
			}
			require AAGAM_BASE_PATH . 'includes/amp-output.php';
		} else {
			require AAGAM_BASE_PATH . 'includes/ad-output.php';
		}

		return ob_get_clean();

	}

	/**
	 * Sanitize ad options on save
	 * - use value for "width" as index
	 * - order lines by index/width starting with the lowest
	 * - prevent saving a completely empty row if there is only one
	 *
	 * @param array $options ad options.
	 * @return array sanitized ad options.
	 */
	public function sanitize_options( $options = array() ) {

		// remove the option when there is just one line and no size was selected so that it is recreated with all boxes selected
		if ( isset( $options['output']['ad-sizes'] )
			 && is_array( $options['output']['ad-sizes'] )
			 && 1 === count( $options['output']['ad-sizes'] ) ) {
			// get first array since we don’t have a static index.
			$first = reset( $options['output']['ad-sizes'] );

			if ( ! isset( $first['sizes'] ) ) {
				unset( $options['output']['ad-sizes'] );
			}
		}

		/**
		 * Sanitize Ad sizes
		 * - use the "width" input field as an index
		 * - order lines
		 * - removes duplicates by design
		 */
		if ( isset( $options['output']['ad-sizes'] ) && is_array( $options['output']['ad-sizes'] ) ) {
			$sanitized_codes = array();

			foreach ( $options['output']['ad-sizes'] as $_index => $_codes ) {
				// use value for "width" as the index
				$new_index                     = isset( $_codes['width'] ) ? absint( $_codes['width'] ) : absint( $_index );
				$sanitized_codes[ $new_index ] = $_codes;
			}

			// order lines by index/width starting with the lowest
			ksort( $sanitized_codes );

			$options['output']['ad-sizes'] = $sanitized_codes;
		}

		return $options;

	}

}
