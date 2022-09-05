<?php
Class Analytify_Dimensions_Tracking {
	
	/**
	 * Class constructor.
	 * @since 1.0
	*/
	public function __construct() {

		// $this->track_dimension();
		$this->_hooks();
	}

	
	/**
	 * Calling hooks.
	 * @since 1.0
	*/
	public function _hooks() {
		
		// add_action('wp_head', array( $this, 'track_dimension' ) ); // Debuging!
		add_action( 'analytify_tracking_code_before_pageview', array( $this, 'add_dimension_hit_code' ) );
	}
	
	
	/**
	 * Callback for dimensions hit code generator.
	 * @since 1.0
	*/
	public function add_dimension_hit_code() {

		$this->track_dimension();
	}
	

	/**
	 * Track all selected dimensions.
	 * @since 1.0
	*/
	public function track_dimension() {

		$current_dimensions = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytiy_custom_dimensions','wp-analytify-custom-dimensions' );
		$dimensions = array();
		
		if ( empty( $current_dimensions ) || ! is_array( $current_dimensions ) ) {
			return;
		}
		
		foreach ($current_dimensions as $key => $value) {
			$type  = $value['type'];
			$id    = $value['id'];
			
			switch ($type) {
				case 'logged_in' :
					$tracking_val = $this->track_logged_in();
				break;

				case 'user_id' :
					$tracking_val = $this->track_user_id();
				break;

				case 'post_type' :
					$tracking_val = $this->track_post_type();
				break;

				case 'author' :
					$tracking_val = $this->track_author();
				break;

				case 'category' :
					$tracking_val = $this->track_category();
				break;

				case 'tags' :
					$tracking_val = $this->track_tags();
				break;

				case 'published_at' :
					$tracking_val = $this->track_published_at();
				break;

				case 'seo_score' :
					$tracking_val = $this->track_seo_score();
				break;

				case 'focus_keyword' :
					$tracking_val = $this->track_focus_keyword();
				break;

				default:
				break;
			}

			if ( ! empty( $tracking_val ) ) {
				// Debuging!
				// $dimensions[ 'dimension' . $id ] = $this->get_dimension_script( $id, $tracking_val );
				//echo 'console.log('.$this->get_dimension_script( $id, $tracking_val ).');';
				
				if ( 'gtag' === ANALYTIFY_TRACKING_MODE ) {
					echo 'gtag('.$this->get_dimension_script( $id, $tracking_val ).');';
				} else {
					echo 'ga('.$this->get_dimension_script( $id, $tracking_val ).');';
				}
			}
		}

	}
	
	
	/**
	 * Generate dimensions hit script.
	 * @since 1.0
	 * @return string $track_code
	*/
	public function get_dimension_script( $id, $tracking_val ) {

		return "'set', 'dimension" . absint( $id ) . "', '" . esc_js( addslashes( $tracking_val ) ) . "'";
	}
	
	
	/**
	 * Check if user logged in.
	 * @since 1.0
	 * @return string $value
	*/
	public function track_logged_in() {

		return var_export( is_user_logged_in(), true );
	}
	
	
	/**
	 * Get current logged in user Id, returns 0 if not logged in.
	 * @since 1.0
	 * @return numaric
	*/
	public function track_user_id() {

		return is_user_logged_in() ? get_current_user_id() : 0;
	}


	/**
	 * Get the post type of current post.
	 *
	 * @since 1.0
	 * @return string $post_type
	*/
	public function track_post_type() {

		return ( is_singular() ) ? get_post_type( get_the_ID() ) : '';
	}


	/**
	 * Get the post author's name of current post.
	 *
	 * @since 1.0
	 * @return string $value
	*/
	public function track_author() {

		$value = '';
		if ( is_singular() ) {
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
				}
			}
			$value = get_the_author_meta( 'user_login' );
		}
    	return $value;
	}
	  

	/**
	 * Get the comma seperated categories of current post.
	 *
	 * @since 1.0
	 * @return string $value
	*/
	public function track_category() {

		$value = '';
		
		if ( is_single() ) {
			// Check primary category form yoast.
			$main_category = get_post_meta( get_the_ID(), '_yoast_wpseo_primary_category', true );

			if ( ! empty( $main_category ) ) {
				$main_category = get_category( $main_category );
				if ( ! empty( $main_category->name ) ) {
					$value = $main_category->name;
				}
			}

			if ( empty( $value ) ) {
				$categories = get_the_category( get_the_ID() );

				if ( $categories ) {
					foreach ( $categories as $category ) {
						$category_names[] = $category->slug;
					}
					$value =  implode( ',', $category_names );
				}
			}
    	}

		return $value;
	}
	
	
	/**
	 * Get the comma seperated tags of current post.
	 * @since 1.0
	 * @return string $tag_names
	*/
	public function track_tags() {

		$tag_names = '';
		if ( is_single() ) {
			$tag_names = 'untagged';
			$tags = get_the_tags( get_the_ID() );
			if ( $tags ) {
				$tag_names = implode( ',', wp_list_pluck( $tags, 'name' ) );
			}
		}
		return $tag_names;
	}


	/**
	 * Get the publist date and time of current post.
	 *
	 * @since 1.0
	 * @return string $date
	 */
	public function track_published_at() {

		return ( is_singular() ) ? get_the_date( 'c' ) : '';
	}


	/**
	 * Get yoast seo score of current post.
	 *
	 * @since 1.0
	 */
	public function track_seo_score() {

		if ( class_exists( 'WPSEO_Frontend' ) && is_singular() ) {
			$score = WPSEO_Metabox::get_value( 'linkdex', get_the_ID() );
			return $this->translate_score($score);
		}
	}


	/**
	 * Translate yoast seo score to readable value.
	 *
	 * @since 1.0
	 */
	public function translate_score( $score ) {

		if ( class_exists( 'WPSEO_Frontend' ) && method_exists( 'WPSEO_Utils', 'translate_score' ) ) {
			return WPSEO_Utils::translate_score( $score );
		}
		return wpseo_translate_score( $score );
	}


	/**
	 * Get yoast seo focus keywords of current post.
	 *
	 * @since 1.0
	 */
	public function track_focus_keyword() {
		
		$focus_keyword = '';
		
		if ( is_singular() ) {
			$focus_keyword = get_post_meta( get_the_ID(), '_yoast_wpseo_focuskw', true );
			if ( empty( $focus_keyword ) ) {
				$focus_keyword = __( 'keyword_not_set', 'wp-analytify-pro' );
			}
		}
		return $focus_keyword;
	}
	
}

// Init Analytify_Dimensions_Tracking
$Analytify_Dimensions_Tracking = new Analytify_Dimensions_Tracking();
