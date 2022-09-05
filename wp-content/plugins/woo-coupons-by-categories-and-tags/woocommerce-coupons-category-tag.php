<?php
/*
Plugin Name: WooCommerce Coupons by Categories and Tags
Description: This plugin allows you to add Categories & Tags to your WooCommerce coupons.
Author: Tyche Softwares
Version: 1.1
Author URI: http://www.tychesoftwares.com/about
Contributor: Tyche Softwares, http://www.tychesoftwares.com/
*/

if ( !class_exists( 'woocommerce_coupons_by_categories_and_tags' ) ) {
    class woocommerce_coupons_by_categories_and_tags {

		public function __construct() {
			add_action( 'init',                                   array( $this, 'wct_create_category' ) );
			add_action( 'init',                                   array( $this, 'wct_create_tag' ) );
			
			add_filter( 'manage_edit-shop_coupon_columns',        array( $this, 'wct_custom_taxonomy_columns' ) );
			add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'wct_custom_taxonomy_content' ), 10, 2 );
			
			add_action( 'admin_menu',                             array( $this, 'wct_add_coupon_category_admin_submenu' ) );
			add_filter( 'parent_file',                            array( $this, 'wct_make_menu_active' ) );	
		}
		
		//Register custom taxonomy called Coupon categories 
		public function wct_create_category() {
	
			$labels = array(
				'name'              => __( 'Coupon categories', 'woocommerce-coupon-taxonomy' ),
				'singular_name'     => __( 'Category', 'woocommerce-coupon-taxonomy' ),
				'menu_name'         => _x( 'Categories', 'Admin menu name', 'woocommerce-coupon-taxonomy' ),
				'search_items'      => __( 'Search Coupon categories', 'woocommerce-coupon-taxonomy' ),
				'all_items'         => __( 'All categories', 'woocommerce-coupon-taxonomy' ),
				'parent_item'       => __( 'Parent category', 'woocommerce-coupon-taxonomy' ),
				'parent_item_colon' => __( 'Parent category:', 'woocommerce-coupon-taxonomy' ),
				'edit_item'         => __( 'Edit coupon category', 'woocommerce-coupon-taxonomy' ),
				'update_item'       => __( 'Update category', 'woocommerce-coupon-taxonomy' ),
				'add_new_item'      => __( 'Add new coupon category', 'woocommerce-coupon-taxonomy' ),
				'new_item_name'     => __( 'New category name', 'woocommerce-coupon-taxonomy' ),
				'not_found'         => __( 'No categories found', 'woocommerce-coupon-taxonomy' ),
			);
			register_taxonomy( 'coupon_category', array('shop_coupon'), array(
				'hierarchical' => true,
				'labels' => $labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'query_var' => true,
				'show_in_menu' => true,
				'public' => true,
				'rewrite' => array( 'slug' => 'coupon_category' ),
			) );
			register_taxonomy_for_object_type( 'coupon_category', 'shop_coupon' );
		}
		
		public function wct_create_tag() {
			$labels = array(
				'name'              => __( 'Coupon tags', 'woocommerce-coupon-taxonomy' ),
				'singular_name'     => __( 'Tag', 'woocommerce-coupon-taxonomy' ),
				'menu_name'         => _x( 'Tags', 'Admin menu name', 'woocommerce-coupon-taxonomy' ),
				'search_items'      => __( 'Search tags', 'woocommerce-coupon-taxonomy' ),
				'all_items'         => __( 'All tags', 'woocommerce-coupon-taxonomy' ),
				'parent_item'       => __( 'Parent tag', 'woocommerce-coupon-taxonomy' ),
				'parent_item_colon' => __( 'Parent tag:', 'woocommerce-coupon-taxonomy' ),
				'edit_item'         => __( 'Edit tag', 'woocommerce-coupon-taxonomy' ),
				'update_item'       => __( 'Update tag', 'woocommerce-coupon-taxonomy' ),
				'add_new_item'      => __( 'Add new tag', 'woocommerce-coupon-taxonomy' ),
				'new_item_name'     => __( 'New tag name', 'woocommerce-coupon-taxonomy' ),
				'not_found'         => __( 'No tags found', 'woocommerce-coupon-taxonomy' ),
			);
			register_taxonomy( 'coupon_tag', array('shop_coupon'), array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'query_var' => true,
				'show_in_menu' => true,
				'public' => true,
				'rewrite' => array( 'slug' => 'coupon_tag' ),
			) );
			register_taxonomy_for_object_type('coupon_tag', 'shop_coupon');
		}

		public function wct_custom_taxonomy_columns( $columns )
		{
			$columns['coupon_category'] = __('Categories');
			$columns['coupon_tag']      = __('Tags');
	
			return $columns;
		}

		public function wct_custom_taxonomy_content($column_name, $post_id) {
			if( $column_name == 'coupon_category' ) {
				$terms = get_the_terms( $post_id, 'coupon_category' );
				if( !empty($terms) ) {
					foreach ( $terms as $term ) {
						$output[] = '<a href="' . admin_url( 'edit.php?coupon_category' . '='.  $term->slug . '&post_type=' . 'shop_coupon' ) . '">' . $term->name . '</a>';
					}
					echo join( ', ', $output );
				} else {
					echo '-';
				}
		
			}
			if( $column_name == 'coupon_tag' ) {
				$terms = get_the_terms($post_id, 'coupon_tag' );
				if( !empty($terms) ){
					foreach ( $terms as $term ) {
						$output[] = '<a href="' . admin_url( 'edit.php?coupon_tag' . '='.  $term->slug . '&post_type=' . 'shop_coupon' ) . '">' . $term->name . '</a>';
					}
					echo join( ', ', $output );
				} else {
					echo '-';
				}
			}
		}
		
		public function wct_add_coupon_category_admin_submenu() {
			add_submenu_page( 'woocommerce', 'Coupon Categories', 'Coupon Categories', 'manage_options', 'edit-tags.php?taxonomy=coupon_category');
		}

		public function wct_make_menu_active( $parent_file ) {
			global $current_screen;

			$taxonomy = $current_screen->taxonomy;
			if ( $taxonomy == 'coupon_category' ) {
				$parent_file = 'woocommerce';
			}

			return $parent_file;
		}

	}
}

new woocommerce_coupons_by_categories_and_tags();