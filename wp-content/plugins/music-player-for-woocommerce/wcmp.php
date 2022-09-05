<?php
/*
Plugin Name: Music Player for WooCommerce
Plugin URI: https://wcmp.dwbooster.com
Version: 1.0.177
Text Domain: music-player-for-woocommerce
Author: CodePeople
Author URI: https://wcmp.dwbooster.com
Description: Music Player for WooCommerce includes the MediaElement.js music player in the pages of the products with audio files associated, and in the store's pages, furthermore, the plugin allows selecting between multiple skins.
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

require_once 'banner.php';
$codepeople_promote_banner_plugins['codepeople-music-player-for-woocommerce'] = array(
	'plugin_name' => 'Music Player for WooCommerce',
	'plugin_url'  => 'https://wordpress.org/support/plugin/music-player-for-woocommerce/reviews/#new-post',
);

// CONSTANTS

define( 'WCMP_WEBSITE_URL', get_home_url( get_current_blog_id(), '', is_ssl() ? 'https' : 'http' ) );
define( 'WCMP_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'WCMP_DEFAULT_PLAYER_LAYOUT', 'mejs-classic' );
define( 'WCMP_DEFAULT_PLAYER_VOLUME', 1 );
define( 'WCMP_DEFAULT_PLAYER_CONTROLS', 'default' );
define( 'WCMP_DEFAULT_PlAYER_TITLE', 1 );
define( 'WCMP_REMOTE_TIMEOUT', 120 );

// Load widgets
require_once 'widgets/playlist_widget.php';

add_filter( 'option_sbp_settings', array( 'WooCommerceMusicPlayer', 'troubleshoot' ) );
if ( ! class_exists( 'WooCommerceMusicPlayer' ) ) {
	class WooCommerceMusicPlayer {

		// ******************** ATTRIBUTES ************************

		private $_products_attrs  = array();
		private $_global_attrs    = array();
		private $_player_layouts  = array( 'mejs-classic', 'mejs-ted', 'mejs-wmp' );
		private $_player_controls = array( 'button', 'all', 'default' );
		private $_files_directory_path;
		private $_files_directory_url;
		private $_enqueued_resources = false;
		private $_insert_player      = true;

		private $_force_hook_title = 0;

		/**
		 * WCMP constructor
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			 $this->_createDir();
			register_activation_hook( __FILE__, array( &$this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'deactivation' ) );

			add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ), 99 );
		} // End __constructor

		public function activation() {
			$this->_deleteDir( $this->_files_directory_path );
			$this->_createDir();
		}

		public function deactivation() {
			$this->_deleteDir( $this->_files_directory_path );
		}

		public function plugins_loaded() {
			if ( ! class_exists( 'woocommerce' ) ) {
				return;
			}
			load_plugin_textdomain( 'music-player-for-woocommerce', false, basename( dirname( __FILE__ ) ) . '/languages/' );

			add_filter( 'the_title', array( &$this, 'include_main_player_filter' ), 11, 2 );
			$this->_init_force_in_title();
			$this->_load_addons();

			// Integration with the content editors
			require_once dirname( __FILE__ ) . '/pagebuilders/builders.php';
			WCMP_BUILDERS::run();
		}

		public function get_product_attr( $product_id, $attr, $default = false ) {
			if ( ! isset( $this->_products_attrs[ $product_id ] ) ) {
				$this->_products_attrs[ $product_id ] = array();
			}
			if ( ! isset( $this->_products_attrs[ $product_id ][ $attr ] ) ) {
				if ( metadata_exists( 'post', $product_id, $attr ) ) {
					$this->_products_attrs[ $product_id ][ $attr ] = get_post_meta( $product_id, $attr, true );
				} else {
					$this->_products_attrs[ $product_id ][ $attr ] = $this->get_global_attr( $attr, $default );
				}
			}
			return apply_filters( 'wcmp_product_attr', $this->_products_attrs[ $product_id ][ $attr ], $product_id, $attr );

		} // End get_product_attr

		public function get_global_attr( $attr, $default = false ) {
			if ( empty( $this->_global_attrs ) ) {
				$this->_global_attrs = get_option( 'wcmp_global_settings', array() );
			}
			if ( ! isset( $this->_global_attrs[ $attr ] ) ) {
				$this->_global_attrs[ $attr ] = $default;
			}
			return apply_filters( 'wcmp_global_attr', $this->_global_attrs[ $attr ], $attr );

		} // End get_global_attr

		// ******************** WordPress ACTIONS **************************

		public function init() {
			// Check if WooCommerce is installed or not
			if ( ! class_exists( 'woocommerce' ) ) {
				add_shortcode(
					'wcmp-playlist',
					function( $atts ) {
						return '';
					}
				);
				return; }
			$_current_user_id = get_current_user_id();
			if (
				$this->get_global_attr( '_wcmp_registered_only', 0 ) &&
				0 == $_current_user_id
			) {
				$this->_insert_player = false;
			}

			if ( ! is_admin() ) {
				// Define the shortcode for the playlist_widget
				add_shortcode( 'wcmp-playlist', array( &$this, 'replace_playlist_shortcode' ) );
				$this->_preview();
				if ( isset( $_REQUEST['wcmp-action'] ) && 'play' == $_REQUEST['wcmp-action'] ) {
					if ( isset( $_REQUEST['wcmp-product'] ) ) {
						$product_id = @intval( $_REQUEST['wcmp-product'] );
						if ( ! empty( $product_id ) ) {
							$product = wc_get_product( $product_id );
							if ( false !== $product && isset( $_REQUEST['wcmp-file'] ) ) {
								$files = $this->_get_product_files(
									array(
										'product' => $product,
										'file_id' => sanitize_key( $_REQUEST['wcmp-file'] ),
									)
								);

								if ( ! empty( $files ) ) {
									$file_url = $files[ sanitize_key( $_REQUEST['wcmp-file'] ) ]['file'];
									$this->_tracking_play_event( $product_id, $file_url );
									$this->_output_file( array( 'url' => $file_url ) );
								}
							}
						}
					}
					exit;
				} else {
					// To allow customize the hooks
					$include_main_player_hook = trim( $this->get_global_attr( '_wcmp_main_player_hook', '' ) );
					$include_all_players_hook = trim( $this->get_global_attr( '_wcmp_all_players_hook', '' ) );

					if ( empty( $include_main_player_hook ) ) {
						$include_main_player_hook = 'woocommerce_shop_loop_item_title';
					}
					if ( empty( $include_all_players_hook ) ) {
						$include_all_players_hook = 'woocommerce_single_product_summary';
					}

					if ( 0 == $this->_force_hook_title ) {
						add_action( $include_main_player_hook, array( &$this, 'include_main_player' ), 11 );
					}

					add_action( $include_all_players_hook, array( &$this, 'include_all_players' ), 11 );

					// Allows to call the players directly by themes
					add_action( 'wcmp_main_player', array( &$this, 'include_main_player' ), 11 );
					add_action( 'wcmp_all_players', array( &$this, 'include_all_players' ), 11 );

					// Integration with woocommerce-product-table by barn2media
					add_filter( 'wc_product_table_data_name', array( &$this, 'product_table_data_name' ), 11, 2 );

					$players_in_cart = $this->get_global_attr( '_wcmp_players_in_cart', false );
					if ( $players_in_cart ) {
						add_action( 'woocommerce_after_cart_item_name', array( &$this, 'player_in_cart' ), 11, 2 );
					}

					// Add product id to audio tag
					add_filter( 'wcmp_audio_tag', array( &$this, 'add_data_product' ), 99, 4 );

					// Add class name to the feature image of product
					add_filter( 'woocommerce_product_get_image', array( &$this, 'add_class_attachment' ), 99, 6 );
					add_filter( 'woocommerce_single_product_image_thumbnail_html', array( &$this, 'add_class_single_product_image' ), 99, 2 );

					// Include players with the titles
					if (
						$this->get_global_attr( '_wcmp_force_main_player_in_title', 1 ) &&
						! empty( $_SERVER['REQUEST_URI'] )
						/*
						 ! empty( $_SERVER['REQUEST_URI'] ) &&
						stripos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'wc/store' ) !== false */
					) {
						add_filter( 'woocommerce_product_title', array( &$this, 'woocommerce_product_title' ), 10, 2 );

						add_filter( 'esc_html', array( &$this, 'esc_html' ), 10, 2 );
					}

					// For accepting the <source> tags
					add_filter( 'wp_kses_allowed_html', array( &$this, 'allowed_html_tags' ), 10, 2 );
				}
			} else {
				add_action( 'admin_menu', array( &$this, 'menu_links' ), 10 );
			}

		} // End init

		public function admin_init() {
			// Check if WooCommerce is installed or not
			if ( ! class_exists( 'woocommerce' ) ) {
				return;
			}

			add_meta_box( 'wcmp_woocommerce_metabox', __( 'Music Player for WooCommerce', 'music-player-for-woocommerce' ), array( &$this, 'woocommerce_player_settings' ), $this->_get_post_types(), 'normal' );
			add_action( 'save_post', array( &$this, 'save_post' ), 10, 3 );
			add_action( 'delete_post', array( &$this, 'delete_post' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'help_link' ) );
		} // End admin_init

		public function help_link( $links ) {
			array_unshift(
				$links,
				'<a href="https://wordpress.org/support/plugin/music-player-for-woocommerce/#new-post" target="_blank">' . __( 'Help' ) . '</a>'
			);
			return $links;
		} // End help_link

		public function menu_links() {
			add_options_page( 'Music Player for WooCommerce', 'Music Player for WooCommerce', 'manage_options', 'music-player-for-woocommerce-settings', array( &$this, 'settings_page' ) );
		} // End menu_links

		public function settings_page() {
			if (
				isset( $_POST['wcmp_nonce'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcmp_nonce'] ) ), 'wcmp_updating_plugin_settings' )
			) {
				$_REQUEST = stripslashes_deep( $_REQUEST );
				// Save the player settings
				$registered_only                = ( isset( $_REQUEST['_wcmp_registered_only'] ) ) ? 1 : 0;
				$fade_out                       = ( isset( $_REQUEST['_wcmp_fade_out'] ) ) ? 1 : 0;
				$purchased_times_text           = sanitize_text_field( isset( $_REQUEST['_wcmp_purchased_times_text'] ) ? wp_unslash( $_REQUEST['_wcmp_purchased_times_text'] ) : '' );
				$troubleshoot_default_extension = ( isset( $_REQUEST['_wcmp_default_extension'] ) ) ? true : false;
				$force_main_player_in_title     = ( isset( $_REQUEST['_wcmp_force_main_player_in_title'] ) ) ? 1 : 0;
				$ios_controls                   = ( isset( $_REQUEST['_wcmp_ios_controls'] ) ) ? true : false;
				$troubleshoot_onload            = ( isset( $_REQUEST['_wcmp_onload'] ) ) ? true : false;
				$include_main_player_hook       = ( isset( $_REQUEST['_wcmp_main_player_hook'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_main_player_hook'] ) ) : '';
				$main_player_hook_title         = ( isset( $_REQUEST['_wcmp_main_player_hook_title'] ) ) ? 1 : 0;
				$include_all_players_hook       = ( isset( $_REQUEST['_wcmp_all_players_hook'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_all_players_hook'] ) ) : '';

				$enable_player    = ( isset( $_REQUEST['_wcmp_enable_player'] ) ) ? 1 : 0;
				$show_in          = ( isset( $_REQUEST['_wcmp_show_in'] ) && in_array( $_REQUEST['_wcmp_show_in'], array( 'single', 'multiple' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_show_in'] ) ) : 'all';
				$players_in_cart  = ( isset( $_REQUEST['_wcmp_players_in_cart'] ) ) ? true : false;
				$player_style     = (
						isset( $_REQUEST['_wcmp_player_layout'] ) &&
						in_array( $_REQUEST['_wcmp_player_layout'], $this->_player_layouts )
					) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_player_layout'] ) ) : WCMP_DEFAULT_PLAYER_LAYOUT;
				 $player_controls = (
						isset( $_REQUEST['_wcmp_player_controls'] ) &&
						in_array( $_REQUEST['_wcmp_player_controls'], $this->_player_controls )
					) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_player_controls'] ) ) : WCMP_DEFAULT_PLAYER_CONTROLS;

				 $on_cover = ( ( 'button' == $player_controls || 'default' == $player_controls ) && isset( $_REQUEST['_wcmp_player_on_cover'] ) ) ? 1 : 0;

				 $player_title        = ( isset( $_REQUEST['_wcmp_player_title'] ) ) ? 1 : 0;
				 $merge_grouped       = ( isset( $_REQUEST['_wcmp_merge_in_grouped'] ) ) ? 1 : 0;
				 $play_all            = ( isset( $_REQUEST['_wcmp_play_all'] ) ) ? 1 : 0;
				 $play_simultaneously = ( isset( $_REQUEST['_wcmp_play_simultaneously'] ) ) ? 1 : 0;
				 $volume              = ( isset( $_REQUEST['_wcmp_player_volume'] ) && is_numeric( $_REQUEST['_wcmp_player_volume'] ) ) ? floatval( $_REQUEST['_wcmp_player_volume'] ) : 1;
				 $preload             = (
						isset( $_REQUEST['_wcmp_preload'] ) &&
						in_array( $_REQUEST['_wcmp_preload'], array( 'none', 'metadata', 'auto' ) )
					) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_preload'] ) ) : 'none';

				 $global_settings = array(
					 '_wcmp_registered_only'            => $registered_only,
					 '_wcmp_fade_out'                   => $fade_out,
					 '_wcmp_purchased_times_text'       => $purchased_times_text,
					 '_wcmp_enable_player'              => $enable_player,
					 '_wcmp_show_in'                    => $show_in,
					 '_wcmp_players_in_cart'            => $players_in_cart,
					 '_wcmp_player_layout'              => $player_style,
					 '_wcmp_player_volume'              => $volume,
					 '_wcmp_player_controls'            => $player_controls,
					 '_wcmp_player_title'               => $player_title,
					 '_wcmp_merge_in_grouped'           => $merge_grouped,
					 '_wcmp_play_all'                   => $play_all,
					 '_wcmp_play_simultaneously'        => $play_simultaneously,
					 '_wcmp_preload'                    => $preload,
					 '_wcmp_on_cover'                   => $on_cover,
					 '_wcmp_default_extension'          => $troubleshoot_default_extension,
					 '_wcmp_force_main_player_in_title' => $force_main_player_in_title,
					 '_wcmp_ios_controls'               => $ios_controls,
					 '_wcmp_onload'                     => $troubleshoot_onload,
					 '_wcmp_main_player_hook'           => $include_main_player_hook,
					 '_wcmp_main_player_hook_title'     => $main_player_hook_title,
					 '_wcmp_all_players_hook'           => $include_all_players_hook,
					 '_wcmp_analytics_integration'      => ( isset( $_REQUEST['_wcmp_analytics_integration'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_analytics_integration'] ) ) : 'ua',
					 '_wcmp_analytics_property'         => ( isset( $_REQUEST['_wcmp_analytics_property'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_analytics_property'] ) ) : '',
					 '_wcmp_analytics_api_secret'       => ( isset( $_REQUEST['_wcmp_analytics_api_secret'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_analytics_api_secret'] ) ) : '',
				 );

				 $apply_to_all_players = ( isset( $_REQUEST['_wcmp_apply_to_all_players'] ) ) ? 1 : 0;
				 if ( $apply_to_all_players ) {
					 $this->_deleteDir( $this->_files_directory_path );

					 $products_ids = array(
						 'post_type'     => $this->_get_post_types(),
						 'numberposts'   => -1,
						 'post_status'   => array( 'publish', 'pending', 'draft', 'future' ),
						 'fields'        => 'ids',
						 'cache_results' => false,
					 );

					 $products = get_posts( $products_ids );
					 foreach ( $products as $product_id ) {
						 update_post_meta( $product_id, '_wcmp_enable_player', $enable_player );
						 update_post_meta( $product_id, '_wcmp_show_in', $show_in );
						 update_post_meta( $product_id, '_wcmp_player_layout', $player_style );
						 update_post_meta( $product_id, '_wcmp_player_controls', $player_controls );
						 update_post_meta( $product_id, '_wcmp_player_volume', $volume );
						 update_post_meta( $product_id, '_wcmp_player_title', $player_title );
						 update_post_meta( $product_id, '_wcmp_merge_in_grouped', $merge_grouped );
						 update_post_meta( $product_id, '_wcmp_play_all', $play_all );
						 update_post_meta( $product_id, '_wcmp_preload', $preload );
						 update_post_meta( $product_id, '_wcmp_on_cover', $on_cover );
					 }
				 }

				 update_option( 'wcmp_global_settings', $global_settings );
				 $this->_global_attrs = $global_settings;
				 do_action( 'wcmp_save_setting' );
			} // Save settings

			print '<div class="wrap">'; // Open Wrap
			include_once dirname( __FILE__ ) . '/views/global_options.php';
			print '</div>'; // Close Wrap
		} // End settings_page

		public function save_post( $post_id, $post, $update ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( empty( $_POST['wcmp_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcmp_nonce'] ) ), 'wcmp_updating_product' ) ) {
				return;
			}
			$post_types = $this->_get_post_types();
			if ( ! isset( $post ) || ! in_array( $post->post_type, $post_types ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$this->delete_post( $post_id );

			// Save the player options
			$enable_player = ( isset( $_REQUEST['_wcmp_enable_player'] ) ) ? 1 : 0;
			$show_in       = ( isset( $_REQUEST['_wcmp_show_in'] ) && in_array( $_REQUEST['_wcmp_show_in'], array( 'single', 'multiple' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_show_in'] ) ) : 'all';
			$player_style  = (
					isset( $_REQUEST['_wcmp_player_layout'] ) &&
					in_array( $_REQUEST['_wcmp_player_layout'], $this->_player_layouts )
				) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_player_layout'] ) ) : WCMP_DEFAULT_PLAYER_LAYOUT;

			$player_controls = (
					isset( $_REQUEST['_wcmp_player_controls'] ) &&
					in_array( $_REQUEST['_wcmp_player_controls'], $this->_player_controls )
				) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_player_controls'] ) ) : WCMP_DEFAULT_PLAYER_CONTROLS;

			$player_title  = ( isset( $_REQUEST['_wcmp_player_title'] ) ) ? 1 : 0;
			$merge_grouped = ( isset( $_REQUEST['_wcmp_merge_in_grouped'] ) ) ? 1 : 0;
			$play_all      = ( isset( $_REQUEST['_wcmp_play_all'] ) ) ? 1 : 0;
			$volume        = ( isset( $_REQUEST['_wcmp_player_volume'] ) && is_numeric( $_REQUEST['_wcmp_player_volume'] ) ) ? floatval( $_REQUEST['_wcmp_player_volume'] ) : 1;
			$preload       = (
					isset( $_REQUEST['_wcmp_preload'] ) &&
					in_array( $_REQUEST['_wcmp_preload'], array( 'none', 'metadata', 'auto' ) )
				) ? sanitize_text_field( wp_unslash( $_REQUEST['_wcmp_preload'] ) ) : 'none';

			$on_cover = ( ( 'button' == $player_controls || 'default' == $player_controls ) && isset( $_REQUEST['_wcmp_player_on_cover'] ) ) ? 1 : 0;

			add_post_meta( $post_id, '_wcmp_enable_player', $enable_player, true );
			add_post_meta( $post_id, '_wcmp_show_in', $show_in, true );
			add_post_meta( $post_id, '_wcmp_player_layout', $player_style, true );
			add_post_meta( $post_id, '_wcmp_player_volume', $volume, true );
			add_post_meta( $post_id, '_wcmp_player_controls', $player_controls, true );
			add_post_meta( $post_id, '_wcmp_player_title', $player_title, true );
			add_post_meta( $post_id, '_wcmp_merge_in_grouped', $merge_grouped, true );
			add_post_meta( $post_id, '_wcmp_preload', $preload, true );
			add_post_meta( $post_id, '_wcmp_play_all', $play_all, true );
			add_post_meta( $post_id, '_wcmp_on_cover', $on_cover, true );
		} // End save_post

		public function delete_post( $post_id ) {
			$post       = get_post( $post_id );
			$post_types = $this->_get_post_types();
			if ( ! isset( $post ) || ! in_array( $post->post_type, $post_types ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Delete truncated version of the audio file
			$this->_delete_truncated_files( $post_id );

			delete_post_meta( $post_id, '_wcmp_enable_player' );
			delete_post_meta( $post_id, '_wcmp_show_in' );
			delete_post_meta( $post_id, '_wcmp_merge_in_grouped' );
			delete_post_meta( $post_id, '_wcmp_player_layout' );
			delete_post_meta( $post_id, '_wcmp_player_volume' );
			delete_post_meta( $post_id, '_wcmp_player_controls' );
			delete_post_meta( $post_id, '_wcmp_player_title' );
			delete_post_meta( $post_id, '_wcmp_preload' );
			delete_post_meta( $post_id, '_wcmp_play_all' );
			delete_post_meta( $post_id, '_wcmp_on_cover' );
		} // End delete_post

		public function esc_html( $safe_text, $text ) {
			if ( strpos( $safe_text, 'wcmp-player-container' ) !== false ) {
				return $text;
			}
			return $safe_text;
		} // End esc_html

		public function enqueue_resources() {
			if ( $this->_enqueued_resources ) {
				return;
			}
			$this->_enqueued_resources = true;

			if ( function_exists( 'wp_add_inline_script' ) ) {
				wp_add_inline_script( 'wp-mediaelement', 'try{if(mejs && mejs.i18n && "undefined" == typeof mejs.i18n.locale) mejs.i18n.locale={};}catch(mejs_err){if(console) console.log(mejs_err);};' );
			}

			// Registering resources
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_style( 'wp-mediaelement-skins', 'https://cdnjs.cloudflare.com/ajax/libs/mediaelement/2.23.5/mejs-skins.min.css', array(), '1.0.177' );
			wp_enqueue_style( 'wcmp-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '1.0.177' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'wcmp-script', plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery', 'wp-mediaelement' ), '1.0.177' );

			$play_all = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr(
				'_wcmp_play_all',
				// This option is only for compatibility with versions previous to 1.0.28
				$GLOBALS['WooCommerceMusicPlayer']->get_global_attr( 'play_all', 0 )
			);

			$play_simultaneously = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_play_simultaneously', 0 );

			if ( function_exists( 'is_product' ) && is_product() ) {
				global $post;
				$post_types = $this->_get_post_types();
				if ( ! empty( $post ) && in_array( $post->post_type, $post_types ) ) {
					$play_all = $GLOBALS['WooCommerceMusicPlayer']->get_product_attr(
						$post->ID,
						'_wcmp_play_all',
						// This option is only for compatibility with versions previous to 1.0.28
						$GLOBALS['WooCommerceMusicPlayer']->get_product_attr(
							$post->ID,
							'play_all',
							$play_all
						)
					);
				}
			}

			wp_localize_script(
				'wcmp-script',
				'wcmp_global_settings',
				array(
					'fade_out'            => $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_fade_out', 1 ),
					'play_all'            => intval( $play_all ),
					'play_simultaneously' => intval( $play_simultaneously ),
					'ios_controls'        => $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_ios_controls', false ),
					'onload'              => $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_onload', false ),
				)
			);
		} // End enqueue_resources

		/**
		 * Replace the shortcode to display a playlist with all songs.
		 */
		public function replace_playlist_shortcode( $atts ) {
			if ( ! class_exists( 'woocommerce' ) ) {
				return '';
			}

			$get_times = function( $product_id, $products_list ) {
				if ( ! empty( $products_list ) ) {
					foreach ( $products_list as $product ) {
						if ( $product->product_id == $product_id ) {
							return $product->times;
						}
					}
				}
				return 0;
			};

			global $post;

			$output = '';
			if ( ! $this->_insert_player ) {
				return $output;
			}

			if ( ! is_array( $atts ) ) {
				$atts = array();
			}
			$post_types = $this->_get_post_types();
			if (
				empty( $atts['products_ids'] ) &&
				empty( $atts['purchased_products'] ) &&
				! empty( $post ) &&
				in_array( $post->post_type, $post_types )
			) {
				try {
					ob_start();
					$this->include_all_players( $post->ID );
					$output = ob_get_contents();
					ob_end_clean();

					$class = esc_attr( isset( $atts['class'] ) ? $atts['class'] : '' );

					return strpos( $output, 'wcmp-player-list' ) !== false ?
						   str_replace( 'wcmp-player-list', $class . ' wcmp-player-list', $output ) :
						   str_replace( 'wcmp-player-container', $class . ' wcmp-player-container', $output );
				} catch ( Exception $err ) {
					$atts['products_ids'] = $post->ID;
				}
			}

			$atts = shortcode_atts(
				array(
					'products_ids'              => '*',
					'purchased_products'        => 0,
					'highlight_current_product' => 0,
					'continue_playing'          => 0,
					'player_style'              => WCMP_DEFAULT_PLAYER_LAYOUT,
					'controls'                  => 'track',
					'layout'                    => 'new',
					'cover'                     => 0,
					'volume'                    => 1,
					'hide_purchase_buttons'     => 0,
					'class'                     => '',
					'loop'                      => 0,
					'purchased_times'           => 0,
				),
				$atts
			);

			$products_ids              = $atts['products_ids'];
			$purchased_products        = $atts['purchased_products'];
			$highlight_current_product = $atts['highlight_current_product'];
			$continue_playing          = $atts['continue_playing'];
			$player_style              = $atts['player_style'];
			$controls                  = $atts['controls'];
			$layout                    = $atts['layout'];
			$cover                     = $atts['cover'];
			$volume                    = $atts['volume'];
			$hide_purchase_buttons     = $atts['hide_purchase_buttons'];
			$class                     = $atts['class'];
			$loop                      = $atts['loop'];
			$purchased_times           = $atts['purchased_times'];

			// Typecasting variables.
			$cover                     = is_numeric( $cover ) ? intval( $cover ) : 0;
			$volume                    = is_numeric( $volume ) ? floatval( $volume ) : 0;
			$purchased_products        = is_numeric( $purchased_products ) ? intval( $purchased_products ) : 0;
			$highlight_current_product = is_numeric( $highlight_current_product ) ? intval( $highlight_current_product ) : 0;
			$continue_playing          = is_numeric( $continue_playing ) ? intval( $continue_playing ) : 0;
			$hide_purchase_buttons     = is_numeric( $hide_purchase_buttons ) ? intval( $hide_purchase_buttons ) : 0;
			$loop                      = is_numeric( $loop ) ? intval( $loop ) : 0;
			$purchased_times           = is_numeric( $purchased_times ) ? intval( $purchased_times ) : 0;

			// get the produts ids
			$products_ids = preg_replace( '/[^\d\,\*]/', '', $products_ids );
			$products_ids = preg_replace( '/(\,\,)+/', '', $products_ids );
			$products_ids = trim( $products_ids, ',' );

			if ( strlen( $products_ids ) == 0 ) {
				return $output;
			}

			// MAIN CODE GOES HERE
			global $wpdb, $post;

			$current_post_id = ! empty( $post ) ? ( is_int( $post ) ? $post : $post->ID ) : -1;

			$query = 'SELECT posts.ID FROM ' . $wpdb->posts . ' AS posts, ' . $wpdb->postmeta . ' as postmeta WHERE posts.post_status="publish" AND posts.post_type IN (' . $this->_get_post_types( true ) . ') AND posts.ID = postmeta.post_id AND postmeta.meta_key="_wcmp_enable_player" AND (postmeta.meta_value="yes" OR postmeta.meta_value="1")';

			if ( ! empty( $purchased_products ) ) {
				// Hide the purchase buttons
				$hide_purchase_buttons = 1;

				// Getting the list of purchased products
				$_current_user_id = get_current_user_id();
				if ( 0 == $_current_user_id ) {
					return $output;
				}

				// GET USER ORDERS (COMPLETED + PROCESSING)
				$customer_orders = get_posts(
					array(
						'numberposts' => -1,
						'meta_key'    => '_customer_user',
						'meta_value'  => $_current_user_id,
						'post_type'   => wc_get_order_types(),
						'post_status' => array_keys( wc_get_is_paid_statuses() ),
					)
				);

				if ( empty( $customer_orders ) ) {
					return $output;
				}

				// LOOP THROUGH ORDERS AND GET PRODUCT IDS
				$products_ids = array();

				foreach ( $customer_orders as $customer_order ) {
					$order = wc_get_order( $customer_order->ID );
					$items = $order->get_items();
					foreach ( $items as $item ) {
						$product_id     = $item->get_product_id();
						$products_ids[] = $product_id;
					}
				}
				$products_ids     = array_unique( $products_ids );
				$products_ids_str = implode( ',', $products_ids );

				$query .= ' AND posts.ID IN (' . $products_ids_str . ')';
				$query .= ' ORDER BY FIELD(posts.ID,' . $products_ids_str . ')';
			} else {
				if ( strpos( '*', $products_ids ) === false ) {
					$query .= ' AND posts.ID IN (' . $products_ids . ')';
					$query .= ' ORDER BY FIELD(posts.ID,' . $products_ids . ')';
				} else {
					$query .= ' ORDER BY posts.post_title ASC';
				}
			}

			$products = $wpdb->get_results( $query );

			if ( ! empty( $products ) ) {
				$product_purchased_times = array();
				if ( $purchased_times ) {
					$products_ids_str = ( is_array( $products_ids ) ) ? implode( ',', $products_ids ) : $products_ids;

					$product_purchased_times = $wpdb->get_results(
						'SELECT order_itemmeta.meta_value product_id, COUNT(order_itemmeta.meta_value) as times
					FROM
						' . $wpdb->prefix . 'posts as orders INNER JOIN ' . $wpdb->prefix . 'woocommerce_order_items as order_items ON (orders.ID=order_items.order_id)
						INNER JOIN ' . $wpdb->prefix . 'woocommerce_order_itemmeta as order_itemmeta ON (order_items.order_item_id=order_itemmeta.order_item_id)
					WHERE orders.post_type="shop_order" AND orders.post_status="wc-completed" AND order_itemmeta.meta_key="_product_id" ' . ( strlen( $products_ids_str ) && false === strpos( '*', $products_ids_str ) ? ' AND order_itemmeta.meta_value IN (' . $products_ids_str . ')' : '' ) . '
					GROUP BY order_itemmeta.meta_value'
					);
				}

				// Enqueue resources

				$this->enqueue_resources();
				wp_enqueue_style( 'wcmp-playlist-widget-style', plugin_dir_url( __FILE__ ) . 'widgets/playlist_widget/css/style.css', array(), '1.0.177' );
				wp_enqueue_script( 'wcmp-playlist-widget-script', plugin_dir_url( __FILE__ ) . 'widgets/playlist_widget/js/public.js', array(), '1.0.177' );
				wp_localize_script(
					'wcmp-playlist-widget-script',
					'wcmp_widget_settings',
					array( 'continue_playing' => $continue_playing )
				);
				$counter = 0;
				$output .= '<div data-loop="' . ( $loop ? 1 : 0 ) . '">';
				foreach ( $products as $product ) {
					$product_obj = wc_get_product( $product->ID );
					$counter++;
					$preload   = $this->get_product_attr( $product->ID, '_wcmp_preload', '' );
					$row_class = 'wcmp-even-product';
					if ( 1 == $counter % 2 ) {
						$row_class = 'wcmp-odd-product';
					}

					$audio_files = $this->get_product_files( $product->ID );
					if ( ! is_array( $audio_files ) ) {
						continue;
					}

					if ( $cover ) {
						$featured_image = get_the_post_thumbnail_url( $product->ID );
					}

					if ( 'new' == $layout ) {
						$price   = $product_obj->get_price();
						$output .= '
							<div class="wcmp-widget-product controls-' . esc_attr( $controls ) . ' ' . esc_attr( $class ) . ' ' . esc_attr( $row_class ) . ' ' . esc_attr( ( $product->ID == $current_post_id && $highlight_current_product ) ? 'wcmp-current-product' : '' ) . '">
								<div class="wcmp-widget-product-header">
									<div class="wcmp-widget-product-title">
										<a href="' . esc_url( get_permalink( $product->ID ) ) . '">' . $product_obj->get_name() . '</a>' .
										(
											$purchased_times ?
											'<span class="wcmp-purchased-times">' .
											sprintf(
												__( $this->get_global_attr( '_wcmp_purchased_times_text', '- purchased %d time(s)' ), 'music-player-for-woocommerce' ),
												$get_times( $product->ID, $product_purchased_times )
											) . '</span>' : ''
										) .
									'</div><!-- product title -->
						';
						if ( 0 != @floatval( $price ) && 0 == $hide_purchase_buttons ) {
							$output .= '<div class="wcmp-widget-product-purchase">
											' . wc_price( $product_obj->get_price(), '' ) . ' <a href="?add-to-cart=' . $product->ID . '"></a>
										</div><!-- product purchase -->
							';
						}
						$output .= '</div>
								<div class="wcmp-widget-product-files">
						';

						if ( ! empty( $featured_image ) ) {
							$output .= '<img src="' . esc_attr( $featured_image ) . '" class="wcmp-widget-feature-image" /><div class="wcmp-widget-product-files-list">';
						}

						foreach ( $audio_files as $index => $file ) {
							$audio_url  = $this->generate_audio_url( $product->ID, $index, $file );
							$duration   = $this->_get_duration_by_url( $file['file'] );
							$audio_tag  = apply_filters(
								'wcmp_widget_audio_tag',
								$this->get_player(
									$audio_url,
									array(
										'player_controls' => $controls,
										'player_style'    => $player_style,
										'media_type'      => $file['media_type'],
										'id'              => $index,
										'duration'        => $duration,
										'preload'         => $preload,
										'volume'          => $volume,
									)
								),
								$product->ID,
								$index,
								$audio_url
							);
							$file_title = esc_html( apply_filters( 'wcmp_widget_file_name', $file['name'], $product->ID, $index ) );
							$output    .= '
								<div class="wcmp-widget-product-file">
									' . $audio_tag . '' . $file_title . '<div style="clear:both;"></div>
								</div><!--product file -->
							';
						}

						if ( ! empty( $featured_image ) ) {
							$output .= '</div>';
						}

						$output .= '
								</div><!-- product-files -->
							</div><!-- product -->
						';
					} else // Load the previous playlist layout
					{
						$output .= '<ul class="wcmp-widget-playlist controls-' . esc_attr( $controls ) . ' ' . esc_attr( $class ) . ' ' . esc_attr( $row_class ) . ' ' . esc_attr( ( $product->ID == $current_post_id && $highlight_current_product ) ? 'wcmp-current-product' : '' ) . '">';

						if ( ! empty( $featured_image ) ) {
							$output .= '<li style="display:table-row;"><img src="' . esc_attr( $featured_image ) . '" class="wcmp-widget-feature-image" /><div class="wcmp-widget-product-files-list"><ul>';
						}

						foreach ( $audio_files as $index => $file ) {
							$audio_url  = $this->generate_audio_url( $product->ID, $index, $file );
							$duration   = $this->_get_duration_by_url( $file['file'] );
							$audio_tag  = apply_filters(
								'wcmp_widget_audio_tag',
								$this->get_player(
									$audio_url,
									array(
										'player_controls' => $controls,
										'player_style'    => $player_style,
										'media_type'      => $file['media_type'],
										'id'              => $index,
										'duration'        => $duration,
										'preload'         => $preload,
										'volume'          => $volume,
									)
								),
								$product->ID,
								$index,
								$audio_url
							);
							$file_title = esc_html( apply_filters( 'wcmp_widget_file_name', ( ( ! empty( $file['name'] ) ) ? $file['name'] : $product->post_title ), $product->ID, $index ) );

							$output .= '<li class="wcmp-widget-playlist-item">' . $audio_tag . '<a href="' . esc_url( get_permalink( $product->ID ) ) . '">' . $file_title . '</a>' .
							(
								$purchased_times ?
								'<span class="wcmp-purchased-times">' .
								sprintf(
									__( $this->get_global_attr( '_wcmp_purchased_times_text', '- purchased %d time(s)' ), 'music-player-for-woocommerce' ),
									$get_times( $product->ID, $product_purchased_times )
								) . '</span>' : ''
							)
							. '<div style="clear:both;"/></li>';
						}
						if ( ! empty( $featured_image ) ) {
							$output .= '</ul></div></li>';
						}
						$output .= '</ul>';
					}
				}
				$output .= '</div>';
			}
			return $output;
		} // End replace_playlist_shortcode

		/**
		 * Used for accepting the <source> tags
		 */
		public function allowed_html_tags( $allowedposttags, $context ) {
			if ( ! in_array( 'source', $allowedposttags ) ) {
				$allowedposttags['source'] = array(
					'src'  => true,
					'type' => true,
				);
			}
			return $allowedposttags;
		} // End allowed_html_tags

		// ******************** WOOCOMMERCE ACTIONS ************************

		public function woocommerce_product_title( $title, $product ) {
			 $player = '';
			ob_start();
			$this->include_main_player( $product );
			$player .= ob_get_contents();
			ob_end_clean();
			return $player . $title;
		} // End woocommerce_product_title

		/**
		 * Load the additional attributes to select the player layout
		 */
		public function woocommerce_player_settings() {
			 include_once 'views/player_options.php';
		} // End woocommerce_player_settings

		public function get_player(
			$audio_url,
			$args = array()
		) {
			$default_args = array(
				'media_type'      => 'mp3',
				'player_style'    => WCMP_DEFAULT_PLAYER_LAYOUT,
				'player_controls' => WCMP_DEFAULT_PLAYER_CONTROLS,
				'duration'        => false,
				'volume'          => 1,
			);

			$args = array_merge( $default_args, $args );
			$id   = ( ! empty( $args['id'] ) ) ? 'id="' . esc_attr( $args['id'] ) . '"' : '';

			$preload = ( ! empty( $args['preload'] ) ) ? $args['preload'] : $GLOBALS['WooCommerceMusicPlayer']->get_global_attr(
				'_wcmp_preload',
				// This option is only for compatibility with versions previous to 1.0.28
					$GLOBALS['WooCommerceMusicPlayer']->get_global_attr( 'preload', 'none' )
			);

			return '<audio ' . (
					(
						isset( $args['volume'] ) &&
						is_numeric( $args['volume'] ) &&
						0 <= $args['volume'] * 1 &&
						$args['volume'] * 1 <= 1
					) ? 'volume="' . esc_attr( $args['volume'] ) . '"' : ''
				) . ' ' . $id . ' preload="none" data-lazyloading="' . esc_attr( $preload ) . '" class="wcmp-player ' . esc_attr( $args['player_controls'] ) . ' ' . esc_attr( $args['player_style'] ) . '" ' . ( ( ! empty( $args['duration'] ) ) ? 'data-duration="' . esc_attr( $args['duration'] ) . '"' : '' ) . '><source src="' . esc_url( $audio_url ) . '" type="audio/' . esc_attr( $args['media_type'] ) . '" /></audio>';

		} // End get_player

		public function get_product_files( $id ) {
			$product = wc_get_product( $id );
			if ( ! empty( $product ) ) {
				return $this->_get_product_files(
					array(
						'product' => $product,
						'all'     => 1,
					)
				);
			}
			return array();
		}

		public function generate_audio_url( $product_id, $file_id, $file_data = array() ) {
			 return $this->_generate_audio_url( $product_id, $file_id, $file_data );
		}

		public function include_main_player_filter( $value, $id ) {
			if ( $this->_force_hook_title ) {
				try {
					if ( ( wp_doing_ajax() || ! is_admin() ) && ( ! function_exists( 'is_product' ) || ! is_product() ) && ! is_cart() && ! is_page( 'cart' ) && ! is_checkout() && is_int( $id ) ) {
						$p = wc_get_product( $id );
						if ( ! empty( $p ) ) {
							add_filter( 'esc_html', array( &$this, 'esc_html' ), 10, 2 );

							$player = '';
							ob_start();
							$this->include_main_player( $p );
							$player = ob_get_contents();
							ob_end_clean();
							$value = $player . $value;
						}
					}
				} catch ( Exception $err ) {
					error_log( $err->getMessage() );
				}
			}
			return $value;
		}

		public function include_main_player( $product = '', $_echo = true ) {
			$output = '';
			if ( ! $this->_insert_player ) {
				return $output;
			}
			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}
			if ( ! is_object( $product ) ) {
				$product = wc_get_product();
			}
			$files = $this->_get_product_files(
				array(
					'product' => $product,
					'first'   => true,
				)
			);
			if ( ! empty( $files ) ) {
				$id = $product->get_id();

				$show_in = $this->get_product_attr( $id, '_wcmp_show_in', 'all' );
				if (
					( 'single' == $show_in && ( ! function_exists( 'is_product' ) || ! is_product() ) ) ||
					( 'multiple' == $show_in && ( function_exists( 'is_product' ) && is_product() ) && get_queried_object_id() == $id )
				) {
					return $output;
				}
				$preload = $this->get_product_attr( $id, '_wcmp_preload', '' );
				$this->enqueue_resources();

				$player_style    = $this->get_product_attr( $id, '_wcmp_player_layout', WCMP_DEFAULT_PLAYER_LAYOUT );
				$player_controls = ( $this->get_product_attr( $id, '_wcmp_player_controls', WCMP_DEFAULT_PLAYER_CONTROLS ) != 'all' ) ? 'track' : '';
				$volume          = @floatval( $this->get_product_attr( $id, '_wcmp_player_volume', WCMP_DEFAULT_PLAYER_VOLUME ) );

				$file      = reset( $files );
				$index     = key( $files );
				$audio_url = $this->_generate_audio_url( $id, $index, $file );
				$duration  = $this->_get_duration_by_url( $file['file'] );
				$audio_tag = apply_filters(
					'wcmp_audio_tag',
					$this->get_player(
						$audio_url,
						array(
							'player_controls' => $player_controls,
							'player_style'    => $player_style,
							'media_type'      => $file['media_type'],
							'duration'        => $duration,
							'preload'         => $preload,
							'volume'          => $volume,
						)
					),
					$id,
					$index,
					$audio_url
				);

				do_action( 'wcmp_before_player_shop_page', $id );

				$output = '<div class="wcmp-player-container product-' . esc_attr( $file['product'] ) . '">' . $audio_tag . '</div>';
				if ( $_echo ) {
					print $output;
				}

				do_action( 'wcmp_after_player_shop_page', $id );

				return $output;
			}
		} // End include_main_player

		public function include_all_players( $product = '' ) {
			if ( ! $this->_insert_player ) {
				return;
			}
			if ( ! is_object( $product ) ) {
				$product = wc_get_product();
			}
			$files = $this->_get_product_files(
				array(
					'product' => $product,
					'all'     => true,
				)
			);
			if ( ! empty( $files ) ) {
				$id = $product->get_id();

				$show_in = $this->get_product_attr( $id, '_wcmp_show_in', 'all' );
				if (
					( 'single' == $show_in && ! is_singular() ) ||
					( 'multiple' == $show_in && is_singular() )
				) {
					return;
				}
				$preload = $this->get_product_attr( $id, '_wcmp_preload', '' );
				$this->enqueue_resources();
				$player_style       = $this->get_product_attr( $id, '_wcmp_player_layout', WCMP_DEFAULT_PLAYER_LAYOUT );
				$volume             = @floatval( $this->get_product_attr( $id, '_wcmp_player_volume', WCMP_DEFAULT_PLAYER_VOLUME ) );
				$player_controls    = $this->get_product_attr( $id, '_wcmp_player_controls', WCMP_DEFAULT_PLAYER_CONTROLS );
				$player_title       = intval( $this->get_product_attr( $id, '_wcmp_player_title', WCMP_DEFAULT_PlAYER_TITLE ) );
				$merge_grouped      = intval( $this->get_product_attr( $id, '_wcmp_merge_in_grouped', 0 ) );
				$merge_grouped_clss = ( $merge_grouped ) ? 'merge_in_grouped_products' : '';

				$counter = count( $files );

				do_action( 'wcmp_before_players_product_page', $id );
				if ( 1 == $counter ) {
					$player_controls = ( 'button' == $player_controls ) ? 'track' : '';
					$file            = reset( $files );
					$index           = key( $files );
					$audio_url       = $this->_generate_audio_url( $id, $index, $file );
					$duration        = $this->_get_duration_by_url( $file['file'] );
					$audio_tag       = apply_filters(
						'wcmp_audio_tag',
						$this->get_player(
							$audio_url,
							array(
								'player_controls' => $player_controls,
								'player_style'    => $player_style,
								'media_type'      => $file['media_type'],
								'duration'        => $duration,
								'preload'         => $preload,
								'volume'          => $volume,
							)
						),
						$id,
						$index,
						$audio_url
					);
					$title           = esc_html( ( $player_title ) ? apply_filters( 'wcmp_file_name', $file['name'], $id, $index ) : '' );
					print '<div class="wcmp-player-container ' . esc_attr( $merge_grouped_clss ) . ' product-' . esc_attr( $file['product'] ) . '">' . $audio_tag . '</div><div class="wcmp-player-title">' . $title . '</div><div style="clear:both;"></div>';
				} elseif ( $counter > 1 ) {
					$before = '<table class="wcmp-player-list ' . $merge_grouped_clss . '">';
					$after  = '';
					foreach ( $files as $index => $file ) {
						$evenOdd = ( 1 == $counter % 2 ) ? 'wcmp-odd-row' : 'wcmp-even-row';
						$counter--;
						$audio_url = $this->_generate_audio_url( $id, $index, $file );
						$duration  = $this->_get_duration_by_url( $file['file'] );
						$audio_tag = apply_filters(
							'wcmp_audio_tag',
							$this->get_player(
								$audio_url,
								array(
									'player_style'    => $player_style,
									'player_controls' => ( 'all' != $player_controls ) ? 'track' : '',
									'media_type'      => $file['media_type'],
									'duration'        => $duration,
									'preload'         => $preload,
									'volume'          => $volume,
								)
							),
							$id,
							$index,
							$audio_url
						);
						$title     = esc_html( ( $player_title ) ? apply_filters( 'wcmp_file_name', $file['name'], $id, $index ) : '' );

						print $before;
						$before = '';
						$after  = '</table>';
						if ( 'all' != $player_controls ) {
							print '<tr class="' . esc_attr( $evenOdd ) . ' product-' . esc_attr( $file['product'] ) . '"><td class="wcmp-player-container wcmp-column-player-' . esc_attr( $player_style ) . '">' . $audio_tag . '</td><td class="wcmp-player-title wcmp-column-player-title">' . $title . '</td></tr>';
						} else {
							print '<tr class="' . esc_attr( $evenOdd ) . ' product-' . esc_attr( $file['product'] ) . '"><td><div class="wcmp-player-container">' . $audio_tag . '</div><div class="wcmp-player-title wcmp-column-player-title">' . $title . '</div></td></tr>';
						}
					}
					print $after;
				}
				do_action( 'wcmp_after_players_product_page', $id );
			}
		} // End include_all_players

		public function player_in_cart( $cart_item, $cart_item_key ) {
			$product = wc_get_product( $cart_item['product_id'] );
			$this->include_all_players( $product );
		} // player_in_cart

		// Integration with woocommerce-product-table by barn2media
		public function product_table_data_name( $name, $product ) {
			ob_start();
			$this->include_main_player( $product );
			$player = ob_get_contents();
			ob_end_clean();
			$player = str_replace( '<div ', '<div style="display:inline-block" ', $player );
			return $player . $name;
		} // product_table_data_name

		public function add_data_product( $player, $product_id, $index, $url ) {
			$player = preg_replace( '/<audio\b/i', '<audio controlslist="nodownload" data-product="' . esc_attr( $product_id ) . '" ', $player );
			return $player;
		} // End add_data_product

		public function add_class_attachment( $html, $product, $size, $attr, $placeholder, $image ) {
			$id   = $product->get_id();
			$html = $this->_add_class( $html, $product );
			return $html;
		} // End add_class_attachment

		public function add_class_single_product_image( $html, $post_thumbnail_id ) {
			global $product;

			if ( ! empty( $product ) ) {
				$html = $this->_add_class( $html, $product );
			}
			return $html;
		} // add_class_single_product_image

		// ******************** PRIVATE METHODS ************************

		private function _init_force_in_title() {
			$this->_force_hook_title = $this->get_global_attr( '_wcmp_main_player_hook_title', 1 );

			// Integration with "WOOF – Products Filter for WooCommerce" by realmag777
			if ( isset( $_REQUEST['action'] ) && 'woof_draw_products' == $_REQUEST['action'] ) {
				$this->_force_hook_title = 1;
			}

		} // End _init_force_in_title

		private function _get_post_types( $mysql_in = false ) {
			 $post_types = array( 'product' );
			if ( ! empty( $GLOBALS['wcmp_post_types'] ) && is_array( $GLOBALS['wcmp_post_types'] ) ) {
				$post_types = $GLOBALS['wcmp_post_types'];
			}
			if ( $mysql_in ) {
				return '"' . implode( '","', $post_types ) . '"';
			}
			return $post_types;
		} // End _get_post_types

		private function _load_addons() {
			$path = __DIR__ . '/addons';
			$wcmp = $this;

			if ( file_exists( $path ) ) {
				$addons = dir( $path );
				while ( false !== ( $entry = $addons->read() ) ) {
					if ( strlen( $entry ) > 3 && strtolower( pathinfo( $entry, PATHINFO_EXTENSION ) ) == 'php' ) {
						include_once $addons->path . '/' . $entry;
					}
				}
			}
		} // End _load_addons

		private function _preview() {
			$user          = wp_get_current_user();
			$allowed_roles = array( 'editor', 'administrator', 'author' );

			if ( array_intersect( $allowed_roles, $user->roles ) ) {
				if ( ! empty( $_REQUEST['wcmp-preview'] ) ) {
					// Sanitizing variable
					$preview = sanitize_text_field( wp_unslash( $_REQUEST['wcmp-preview'] ) );

					// Remove every shortcode that is not in the plugin
					remove_all_shortcodes();
					add_shortcode( 'wcmp-playlist', array( &$this, 'replace_playlist_shortcode' ) );

					if ( has_shortcode( $preview, 'wcmp-playlist' ) ) {
						print '<!DOCTYPE html>';
						$if_empty = __( 'There are no products that satisfy the block\'s settings', 'music-player-for-woocommerce' );
						wp_enqueue_script( 'jquery' );
						$output = do_shortcode( $preview );
						if ( preg_match( '/^\s*$/', $output ) ) {
							$output = '<div>' . $if_empty . '</div>';
						}

						// Deregister all scripts and styles for loading only the plugin styles.
						global  $wp_styles, $wp_scripts;
						if ( ! empty( $wp_scripts ) ) {
							$wp_scripts->reset();
						}
						$this->enqueue_resources();
						if ( ! empty( $wp_styles ) ) {
							$wp_styles->do_items();
						}
						if ( ! empty( $wp_scripts ) ) {
							$wp_scripts->do_items();
						}

						print '<div class="wcmp-preview-container">' . $output . '</div>';
						print '<script type="text/javascript">jQuery(window).on("load", function(){ var frameEl = window.frameElement; if(frameEl) frameEl.height = jQuery(".wcmp-preview-container").outerHeight(true)+25; });</script>';
						exit;
					}
				}
			}
		} // End _preview

		private function _createDir() {
			 // Generate upload dir
			$_files_directory            = wp_upload_dir();
			$this->_files_directory_path = rtrim( $_files_directory['basedir'], '/' ) . '/wcmp/';
			$this->_files_directory_url  = rtrim( $_files_directory['baseurl'], '/' ) . '/wcmp/';
			$this->_files_directory_url  = preg_replace( '/^http(s)?:\/\//', '//', $this->_files_directory_url );
			if ( ! file_exists( $this->_files_directory_path ) ) {
				@mkdir( $this->_files_directory_path, 0755 );
			}
		} // End _createDir

		private function _deleteDir( $dirPath ) {
			try {
				if ( ! is_dir( $dirPath ) ) {
					return;
				}
				if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {
					$dirPath .= '/';
				}
				$files = glob( $dirPath . '*', GLOB_MARK );
				foreach ( $files as $file ) {
					if ( is_dir( $file ) ) {
						$this->_deleteDir( $file );
					} else {
						unlink( $file );
					}
				}
				rmdir( $dirPath );
			} catch ( Exception $err ) {
				return;
			}
		} // End _deleteDir

		private function _get_duration_by_url( $url ) {
			 global $wpdb;
			try {
				$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid RLIKE %s;", $url ) );
				if ( empty( $attachment ) ) {
					$uploads_dir = wp_upload_dir();
					$uploads_url = $uploads_dir['baseurl'];
					$parsed_url  = explode( parse_url( $uploads_url, PHP_URL_PATH ), $url );
					$this_host   = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
					$file_host   = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
					if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
						return false;
					}
					$file       = trim( $parsed_url[1], '/' );
					$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_wp_attached_file' AND meta_value RLIKE %s;", $file ) );
				}
				if ( ! empty( $attachment ) ) {
					$metadata = wp_get_attachment_metadata( $attachment[0] );
					if ( false !== $metadata && ! empty( $metadata['length_formatted'] ) ) {
						return $metadata['length_formatted'];
					}
				}
			} catch ( Exception $err ) {
				error_log( $err->getMessage() );
			}
			return false;
		} // End _get_duration_by_url

		private function _generate_audio_url( $product_id, $file_index, $file_data = array() ) {
			if ( ! empty( $file_data['file'] ) ) {
				$file_url = $file_data['file'];
				if ( ! empty( $file_data['play_src'] ) || $this->_is_playlist( $file_url ) ) {
					return $file_url; // Play src audio file, without copying or truncate it.
				}

				// If the playback of music are tracked with Google Analytics, should not be loaded directly the audio files.
				$_wcmp_analytics_property = trim( $this->get_global_attr( '_wcmp_analytics_property', '' ) );
				if ( '' == $_wcmp_analytics_property ) {
					$file_name = $this->_demo_file_name( $file_url );

					$file_path = $this->_files_directory_path . $file_name;

					if ( $this->_valid_demo( $file_path ) ) {
						return 'http' . ( ( is_ssl() ) ? 's:' : ':' ) . $this->_files_directory_url . $file_name;
					}
				}
			}
			$url  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$url .= ( ( strpos( $url, '?' ) === false ) ? '?' : '&' ) . 'wcmp-action=play&wcmp-product=' . $product_id . '&wcmp-file=' . $file_index;
			return $url;
		} // End _generate_audio_url

		private function _delete_truncated_files( $product_id ) {
			$files_arr = get_post_meta( $product_id, '_downloadable_files', true );
			if ( ! empty( $files_arr ) && is_array( $files_arr ) ) {
				foreach ( $files_arr as $file ) {
					if ( is_array( $file ) && ! empty( $file['file'] ) ) {
						$ext       = pathinfo( $file['file'], PATHINFO_EXTENSION );
						$file_name = md5( $file['file'] ) . ( ( ! empty( $ext ) ) ? '.' . $ext : '' );
						@unlink( $this->_files_directory_path . $file_name );
					}
				}
			}

		} // End _delete_truncated_files

		/**
		 * Check if the file is an m3u or m3u8 playlist
		 */
		private function _is_playlist( $file_path ) {
			return preg_match( '/\.(m3u|m3u8)$/i', $file_path );
		} // End _is_playlist

		/**
		 * Check if the file is an audio file and return its type or false
		 */
		private function _is_audio( $file_path ) {
			if ( preg_match( '/\.(mp3|ogg|oga|wav|wma|mp4)$/i', $file_path, $match ) ) {
				return $match[1];
			}
			if ( preg_match( '/\.m4a$/i', $file_path ) ) {
				return 'mp4';
			}
			if ( $this->_is_playlist( $file_path ) ) {
				return 'hls';
			}

			// From troubleshoot
			$extension                      = pathinfo( $file_path, PATHINFO_EXTENSION );
			$troubleshoot_default_extension = $GLOBALS['WooCommerceMusicPlayer']->get_global_attr( '_wcmp_default_extension', false );
			if ( ( empty( $extension ) || ! preg_match( '/^[a-z\d]{3,4}$/i', $extension ) ) && $troubleshoot_default_extension ) {
				return 'mp3';
			}

			return false;
		} // End _is_audio

		private function _sort_list( $product_a, $product_b ) {
			if (
				! is_object( $product_a ) || ! method_exists( $product_a, 'get_menu_order' ) ||
				! is_object( $product_b ) || ! method_exists( $product_b, 'get_menu_order' )
			) {
				return 0;
			}

			$menu_order_a = $product_a->get_menu_order();
			$menu_order_b = $product_b->get_menu_order();
			if ( $menu_order_a == $menu_order_b ) {
				if (
				! method_exists( $product_a, 'get_name' ) ||
				! method_exists( $product_b, 'get_name' )
				) {
					return 0;
				}

				$name_a = $product_a->get_name();
				$name_b = $product_b->get_name();
				if ( $name_a == $name_b ) {
					return 0;
				}
				return ( $name_a < $name_b ) ? -1 : 1;
			}
			return ( $menu_order_a < $menu_order_b ) ? -1 : 1;
		} // End _sort_list

		private function _edit_files_array( $product_id, $files, $play_src = 0 ) {
			$p_files = array();
			foreach ( $files as $key => $file ) {
				$p_key = $key . '_' . $product_id;
				if ( gettype( $file ) == 'object' ) {
					$file = (array) $file->get_data();
				}
				$file['product']   = $product_id;
				$file['play_src']  = $play_src;
				$p_files[ $p_key ] = $file;
			}
			return $p_files;
		} // end _edit_files_array

		private function _get_recursive_product_files( $product, $files_arr ) {
			if ( ! is_object( $product ) || ! method_exists( $product, 'get_type' ) ) {
				return $files_arr;
			}

			$product_type = $product->get_type();
			$id           = $product->get_id();

			if ( 'variation' == $product_type ) {
				// $_files = $product->get_files();
				$_files    = $product->get_downloads();
				$_files    = $this->_edit_files_array( $id, $_files );
				$files_arr = array_merge( $files_arr, $_files );
			} else {

				if ( ! $this->get_product_attr( $id, '_wcmp_enable_player', false ) ) {
					return $files_arr;
				}

				switch ( $product_type ) {
					case 'variable':
					case 'grouped':
						$children = $product->get_children();

						foreach ( $children as $key => $child_id ) {
							$children[ $key ] = wc_get_product( $child_id );
						}

						uasort( $children, array( &$this, '_sort_list' ) );

						foreach ( $children as $child_obj ) {
							$files_arr = $this->_get_recursive_product_files( $child_obj, $files_arr );
						}
						break;
					default:
						$_files    = $product->get_downloads();
						$_files    = $this->_edit_files_array( $id, $_files );
						$files_arr = array_merge( $files_arr, $_files );
						break;
				}
			}
			return $files_arr;
		} // End _get_recursive_product_files

		private function _get_product_files( $args ) {
			if ( empty( $args['product'] ) ) {
				return false;
			}

			$product = $args['product'];
			$files   = $this->_get_recursive_product_files( $product, array() );

			if ( empty( $files ) ) {
				return false;
			}

			$audio_files = array();
			foreach ( $files as $index => $file ) {
				if ( ! empty( $file['file'] ) && false !== ( $media_type = $this->_is_audio( $file['file'] ) ) ) {
					$file['media_type'] = $media_type;

					if ( ! empty( $args['file_id'] ) ) {
						if ( $args['file_id'] == $index ) {
							$audio_files[ $index ] = $file;
							return $audio_files;
						}
					} elseif ( ! empty( $args['first'] ) ) {
						$audio_files[ $index ] = $file;
						return $audio_files;
					} elseif ( ! empty( $args['all'] ) ) {
						$audio_files[ $index ] = $file;
					}
				}
			}

			return $audio_files;
		} // End _get_product_files

		private function _demo_file_name( $url ) {
			$file_extension = pathinfo( $url, PATHINFO_EXTENSION );
			$file_name      = md5( $url ) . ( ( ! empty( $file_extension ) && preg_match( '/^[a-z\d]{3,4}$/i', $file_extension ) ) ? '.' . $file_extension : '.mp3' );
			return $file_name;
		} // End _demo_file_name

		private function _valid_demo( $file_path ) {
			if ( ! file_exists( $file_path ) || filesize( $file_path ) == 0 ) {
				return false;
			}
			if ( function_exists( 'finfo_open' ) ) {
				$finfo = finfo_open( FILEINFO_MIME );
				return substr( finfo_file( $finfo, $file_path ), 0, 4 ) !== 'text';
			}
			return true;
		} // End _valid_demo

		/**
		 * Create a temporal file and redirect to the new file
		 */
		private function _output_file( $args ) {
			if ( empty( $args['url'] ) ) {
				return;
			}
			$url = $args['url'];
			$url = do_shortcode( $url );

			if ( file_exists( $url ) ) {
				$url_fixed = $url;
			} elseif ( strpos( $url, '//' ) === 0 ) {
				$url_fixed = 'http' . ( is_ssl() ? 's:' : ':' ) . $url;
			} elseif ( strpos( $url, '/' ) === 0 ) {
				$url_fixed = rtrim( WCMP_WEBSITE_URL, '/' ) . $url;
			} else {
				$url_fixed = $url;
			}

			$file_name = $this->_demo_file_name( $url );
			$text      = 'The requested URL was not found on this server';
			$file_path = $this->_files_directory_path . $file_name;

			if ( $this->_valid_demo( $file_path ) ) {
				header( 'location: http' . ( ( is_ssl() ) ? 's:' : ':' ) . $this->_files_directory_url . $file_name );
				exit;
			} else {
				try {
					$c = false;
					if ( ( $path = $this->_is_local( $url_fixed ) ) !== false ) {
						$c = copy( $path, $file_path );
					} else {
						$response = wp_remote_get(
							$url_fixed,
							array(
								'timeout'  => WCMP_REMOTE_TIMEOUT,
								'stream'   => true,
								'filename' => $file_path,
							)
						);
						if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] ) {
							$c = true;
						}
					}

					if ( true === $c ) {
						header( 'location: http' . ( ( is_ssl() ) ? 's:' : ':' ) . $this->_files_directory_url . $file_name );
						exit;
					}
				} catch ( Exception $err ) {
					error_log( $err->getMessage() );
				}
				$text = 'It is not possible to generate the file for demo. Possible causes are: - the amount of memory allocated to the php script on the web server is not enough, - the execution time is too short, - or the "uploads/wcmp" directory does not have write permissions.';
			}
			$this->_print_page_not_found( $text );
		} // End _output_file

		/**
		 *  Add the class name: product-<product id> to cover images associated to the products.
		 *
		 *  @param $html, a html piece of code that includes the <img> tag.
		 *  @param $product, the product object.
		 */
		private function _add_class( $html, $product ) {
			if ( preg_match( '/<img\b[^>]*>/i', $html, $image ) ) {
				$id = $product->get_id();
				if ( $GLOBALS['WooCommerceMusicPlayer']->get_product_attr( $id, '_wcmp_on_cover', 0 ) ) {
					if ( preg_match( '/\bclass\s*=/i', $image[0] ) ) {
						$tmp_image = preg_replace( '/\bclass\s*=\s*[\'"]/i', "$0product-$id ", $image[0] );
					} else {
						$tmp_image = preg_replace( '/<img\b/i', "<img $0 class=\"product-$id\" ", $image[0] );
					}

					$html = str_replace( $image[0], $tmp_image, $html );
				}
			}

			return $html;
		} // End _add_class

		/**
		 * Print not found page if file it is not accessible
		 */
		private function _print_page_not_found( $text = 'The requested URL was not found on this server' ) {
			header( 'Status: 404 Not Found' );
			echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
				  <HTML><HEAD>
				  <TITLE>404 Not Found</TITLE>
				  </HEAD><BODY>
				  <H1>Not Found</H1>
				  <P>' . esc_html( $text ) . '</P>
				  </BODY></HTML>
				 ';
		} // End _print_page_not_found

		private function _is_local( $url ) {
			$file_path = false;
			if ( file_exists( $url ) ) {
				$file_path = $url;
			} else {
				$attachment_id = attachment_url_to_postid( $url );
				if ( $attachment_id ) {
					$attachment_path = get_attached_file( $attachment_id );
					if ( $attachment_path && file_exists( $attachment_path ) ) {
						$file_path = $attachment_path;
					}
				}
			}
			return apply_filters( 'wcmp_is_local', $file_path, $url );
		} // End _is_local

		private function _tracking_play_event( $product_id, $file_url ) {
			$_wcmp_analytics_integration = $this->get_global_attr( '_wcmp_analytics_integration', 'ua' );
			$_wcmp_analytics_property    = trim( $this->get_global_attr( '_wcmp_analytics_property', '' ) );
			$_wcmp_analytics_api_secret  = trim( $this->get_global_attr( '_wcmp_analytics_api_secret', '' ) );
			if ( ! empty( $_wcmp_analytics_property ) ) {
				$cid = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 555;
				try {
					if ( isset( $_COOKIE['_ga'] ) ) {
						$cid_parts = explode( '.', sanitize_text_field( wp_unslash( $_COOKIE['_ga'] ) ), 3 );
						$cid       = $cid_parts[2];
					}
				} catch ( Exception $err ) {
					error_log( $err->getMessage() );
				}

				if ( 'ua' == $_wcmp_analytics_integration ) {
					$_response = wp_remote_post(
						'http://www.google-analytics.com/collect',
						array(
							'body' => array(
								'v'   => 1,
								'tid' => $_wcmp_analytics_property,
								'cid' => $cid,
								't'   => 'event',
								'ec'  => 'Music Player for WooCommerce',
								'ea'  => 'play',
								'el'  => $file_url,
								'ev'  => $product_id,
							),
						)
					);
				} else {
					$_response = wp_remote_post(
						'https://www.google-analytics.com/mp/collect?api_secret=' . $_wcmp_analytics_api_secret . '&measurement_id=' . $_wcmp_analytics_property,
						array(
							'sslverify' => true,
							'headers'   => array(
								'Content-Type' => 'application/json',
							),
							'body'      => json_encode(
								array(
									'client_id' => $cid,
									'events'    => array(
										array(
											'name'   => 'play',
											'params' => array(
												'event_category' => 'Music Player for WooCommerce',
												'event_label' => $file_url,
												'event_value' => $product_id,
											),
										),
									),
								)
							),
						)
					);
				}

				if ( is_wp_error( $_response ) ) {
					error_log( $_response->get_error_message() );
				}
			}
		} // _tracking_play_event

		public static function troubleshoot( $option ) {
			if ( ! is_admin() ) {
				// Solves a conflict caused by the "Speed Booster Pack" plugin
				if ( is_array( $option ) && isset( $option['jquery_to_footer'] ) ) {
					unset( $option['jquery_to_footer'] );
				}
			}
			return $option;
		} // End troubleshoot
	} // End Class WooCommerceMusicPlayer

	$GLOBALS['WooCommerceMusicPlayer'] = new WooCommerceMusicPlayer();
}
