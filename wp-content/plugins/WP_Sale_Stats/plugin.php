<?php

/*
Plugin Name: WP_Sale_Stats
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Sale_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Sale_Stats_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Slider Sale Stat', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Slider Sale Stat', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve stats data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_orders_ids_by_product_id_custom( $product_id, $order_status){
    global $wpdb;
    $results = $wpdb->get_col("
			SELECT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE posts.post_type = 'shop_order'
			AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
			AND order_items.order_item_type = 'line_item'
			AND order_item_meta.meta_key = '_product_id'
			AND order_item_meta.meta_value = '$product_id'
		");
	
		return $results;
	}
	public static function get_stats( $per_page = 20, $page_number = 1 ) {
	global $wpdb;
	$args = '';
	$order_status = array('wc-processing','wc-on-hold','wc-completed');
	$orders_ids_array = array();
	if(isset($_POST['service']) && $_POST['service'] !="" ){
		$orders_ids_array = $wpdb->get_col('select t1.order_id FROM wp_woocommerce_order_items as t1 JOIN wp_woocommerce_order_itemmeta as t2 ON t1.order_item_id = t2.order_item_id where t2.meta_key ="Service" and t2.meta_value ="'.$_POST['service'].'"' );
		//print_r($orders_ids_array);
		if(empty($orders_ids_array)){
			$orders_ids_array = array(0);
		}
		$result[0]['service'] = $_POST['service'];
	}
	if(isset($_POST['order_type']) && $_POST['order_type'] !="" ){
		$orders_ids_array = Sale_Stats_List::get_orders_ids_by_product_id_custom($_POST['order_type'],$order_status);
		$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
		$result[0]['revenue_type'] = $types[$_POST['order_type']];
	}
	$args = array(
					'post__in' 			=> $orders_ids_array ,
					'post_type' 			=> 'shop_order' ,
					'post_status'=>$order_status,
					'posts_per_page'         => '-1',
					//'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
					//'date_query' => array(array('year' => date('Y')))
					);
	if(isset($_POST['order_city']) && $_POST['order_city'] !="" ){
		$args['meta_query'][] =    array(
					'key'   => '_billing_city',
					'value'   => $_POST['order_city'],
					'compare'   => 'like',
		
				);	
				 
		$result[0]['order_city'] = $_POST['order_city'];
	}
	if(isset($_POST['order_state']) && $_POST['order_state'] !="" ){
		$args['meta_query'][] =    array(
					'key'   => '_billing_state',
					'compare'   => 'like',
					'value'   => $_POST['order_state'],
		
				);	
		$result[0]['order_state'] = $_POST['order_state'];
	}
	if(isset($_POST['order_zip_code']) && $_POST['order_zip_code'] !="" ){
		$args['meta_query'][] =    array(
					'key'   => '_billing_postcode',
					'compare'   => 'LIKE',
					'value'   => $_POST['order_zip_code'],
		
				);	
		$result[0]['order_zip_code'] = $_POST['order_zip_code'];
	}			
	if(isset($_POST['mishaDateFrom']) && $_POST['mishaDateFrom'] !="" && $_POST['mishaDateTo'] ==""){
		$args['date_query'] = array(
			array(
				'after' => $_POST['mishaDateFrom'],
				'inclusive' => false,
			)
		);	
	
	}elseif($_POST['mishaDateFrom'] =="" && isset($_POST['mishaDateTo']) && $_POST['mishaDateTo'] !=""){
		$args['date_query'] = array(
			array(
				'before' => $_POST['mishaDateTo'],
				'inclusive' => false,
			)
		);
	}elseif(isset($_POST['mishaDateFrom']) && $_POST['mishaDateFrom'] !="" && isset($_POST['mishaDateTo']) && $_POST['mishaDateTo'] !=""){
		
		$args['date_query'] = array(
			array(
				'after' => $_POST['mishaDateFrom'],
				'before' => $_POST['mishaDateTo'],
				'inclusive' => false,
			)
		);
		
	}
	//print_r($args);
	//$orders_ids_array = get_orders_ids_by_product_id( $product_id );
	
	$product_query = new WP_Query( $args );
	$count = $product_query->found_posts;
	$posts = $product_query->posts;
	
	
		$success_count = 0;
		$relist_count =0;
		if($count > 0){
			foreach($posts as $post){
				$_order_relist_expire = get_post_meta($post->ID, '_order_relist_expire',TRUE);
				if($_order_relist_expire){
					$relist_count++;
				}else{
					$success_count++;
				}
			}
			$success_percent = round(($success_count * 100)/$count,2);
			$relist_percent = round(($relist_count * 100)/$count,2);
		}
		
		/*$arg_success = $args;
		$arg_success['meta_query'][] = array('relation' => 'AND',array('key'=>'_order_relist_expire','compare' => 'NOT EXISTS'));
		
		$success_query = new WP_Query( $arg_success );
		$success_count = $success_query->found_posts;
		$success_percent = round(($success_count * 100)/$count,2);
		
		$arg_relist = $args;
		$arg_relist['meta_query'][] = array('relation' => 'AND',array('key'     => '_order_relist_expire','compare' => 'EXISTS'));
		$relist_query = new WP_Query( $arg_relist );
		$relist_count = $relist_query->found_posts;
		$relist_percent = round(($relist_count * 100)/$count,2);*/
		
		$result[0]['success'] = $success_percent;
		$result[0]['relist'] = $relist_percent;
		$result[0]['no_sales'] = $count;
		return $result;
	}


	/**
	 * Delete a stat record.
	 *
	 * @param int $id stat ID
	 */
	public static function delete_stat( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}customers",
			[ 'ID' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		/*global $wpdb;
		$where = '';
		if(isset($_POST['order_city']) && $_POST['order_city'] !=""){
				$where  = " and order_city like '%".$_POST['order_city']."%' or company like '%".$_POST['order_city']."%'";
		}
		if(isset($_POST['mishaDateFrom']) && $_POST['mishaDateFrom'] !="" && isset($_POST['mishaDateTo']) && $_POST['mishaDateTo'] !=""){
				$from = date('Y-m-d',strtotime($_POST['mishaDateFrom']));
				$to = date('Y-m-d',strtotime($_POST['mishaDateTo']));
				$where  .= " and dated >= '".$from."' AND dated <= '".$to."'";
		}
		$post_ids = get_posts(array(
								'numberposts'   => -1, // get all posts.
								'tax_query'     => array(
									array(
										'taxonomy'  => 'category',
										'field'     => 'id',
										'terms'     => 1559,
									),
								),
								'fields'        => 'ids', // Only get post IDs
							));	
		$sql = 'select COUNT(distinct post_id) FROM wp_post_stats where 1=1 and post_id in ("'.implode('","',$post_ids).'") '.$where;

		return $wpdb->get_var( $sql );*/
	}


	/** Text displayed when no stat data is available */
	public function no_items() {
		_e( 'No stats avaliable.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		global $wpdb;
		$where = '';
		if(isset($_POST['mishaDateFrom']) && $_POST['mishaDateFrom'] !="" && isset($_POST['mishaDateTo']) && $_POST['mishaDateTo'] !=""){
				$from = date('Y-m-d',strtotime($_POST['mishaDateFrom']));
				$to = date('Y-m-d',strtotime($_POST['mishaDateTo']));
				$where  = " and dated >= '".$from."' AND dated <= '".$to."'";
		}
		switch ( $column_name ) {
			case 'no_sales':
				return $item[ $column_name ];
			case 'order_city':
				$order_city = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $order_city;
				//return get_post_meta($item['post_id'],'business_name',true);
			case 'order_state':
				$order_state = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $order_state;
			case 'order_zip_code':
    			$order_zip_code = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $order_zip_code;
			case 'service':
				$service = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $service;
			case 'revenue_type':
				$revenue_type = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $revenue_type;
			default:
				return '-'; //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		/*return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);*/
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_stat' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&stat=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			//'cb'      => '<input type="checkbox" />',
			'no_sales'    => __( '# of Sales', 'sp' ),
			'order_city'    => __( 'City', 'sp' ),
			'order_state'    => __( 'State', 'sp' ),
			'order_zip_code'    => __( 'Zip', 'sp' ),
			'service'    => __( 'Service', 'sp' ),
			'revenue_type'    => __( 'Revenue Type', 'sp' ),
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', true ),
			'company' => array( 'company', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		/*$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;*/
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		//$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'stats_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_stats( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_stat' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_stat( absint( $_GET['stat'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_stat( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}

}


class SP_Plugin_Sale_Stat {

	// class instance
	static $instance;

	// stat WP_List_Table object
	public $stats_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Sales',
			'Sales',
			'shopadoc_admin_cap',
			'sale_performance',
			[ $this, 'plugin_settings_page' ]
		);
		//add_submenu_page( 'performance_auction', 'Sales', 'Sales','shopadoc_admin_cap', 'admin.php?page=sale_performance');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
		$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		$order_city = ( isset( $_POST['order_city'] ) && $_POST['order_city'] ) ? $_POST['order_city'] : '';
		$order_state = ( isset( $_POST['order_state'] ) && $_POST['order_state'] ) ? $_POST['order_state'] : '';
		$order_zip_code = ( isset( $_POST['order_zip_code'] ) && $_POST['order_zip_code'] ) ? $_POST['order_zip_code'] : '';
		$service = ( isset( $_POST['service'] ) && $_POST['service'] ) ? $_POST['service'] : '';
		$order_type = ( isset( $_POST['order_type'] ) && $_POST['order_type'] ) ? $_POST['order_type'] : '';
		?>
		<div class="wrap">
			<h2>Sales</h2>
            <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>-child/autosuggest/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>-child/autosuggest/css/autosuggest_inquisitor.css" type="text/css" />
			<style type="text/css">
				/*th#order_city{width:25%;}*/
				th,td{font-size:16px !important;}
				.error,.notice{display:none;}
			</style>
            <?php 
				if (isset($_POST["submit"])) {
					$sub = $_POST["submit"];
				
					if (isset($sub["save"])) {
						// save something;
					} elseif (isset($sub["export"])) {
						
						header("location:/export2csv_sale.php?order_city=".$order_city."&order_state=".$order_state."&order_zip_code=".$order_zip_code."&service=".$service."&mishaDateFrom=".$from."&mishaDateTo=".$to);
						exit;
					}
				}
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
                            <div style="float:right;">
                            <?php 
								
								$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
								$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
						 		$order_city = ( isset( $_POST['order_city'] ) && $_POST['order_city'] ) ? $_POST['order_city'] : '';
								$order_state = ( isset( $_POST['order_state'] ) && $_POST['order_state'] ) ? $_POST['order_state'] : '';
								$order_zip_code = ( isset( $_POST['order_zip_code'] ) && $_POST['order_zip_code'] ) ? $_POST['order_zip_code'] : '';
								$service = ( isset( $_POST['service'] ) && $_POST['service'] ) ? $_POST['service'] : '';
								$order_type = ( isset( $_POST['order_type'] ) && $_POST['order_type'] ) ? $_POST['order_type'] : '';
								global $US_state;
								$state_html = '<select name="order_state" id="order_state"><option value="">Select State</option>';
								foreach($US_state as $k=>$v){
									$selected ='';
									if($order_state==$v){
										$selected = ' selected="selected" ';
									}
									$state_html .="<option value='".$v."' ".$selected.">".$k."</option>";
								}
								$state_html .='</select>';
								$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
								$type_html = '<select name="order_type" id="order_type"><option value="">Select Type</option>';
								foreach($types as $k=>$v){
									$selected ='';
									if($order_type==$k){
										$selected = ' selected="selected" ';
									}
									$type_html .="<option value='".$k."' ".$selected.">".$v."</option>";
								}
								$type_html .='</select>';
								echo '<style>
								input[name="mishaDateFrom"], input[name="mishaDateTo"],#order_city,#order_state,#order_zip_code,#service,#order_type{
									line-height: 28px;
									height: 43px;
									margin: 0;
									width:125px;
								}
								</style>
								<input type="text" name="mishaDateFrom" placeholder="Start Date" value="' . $from . '" />
								<input type="text" name="mishaDateTo" placeholder="End Date" value="' . $to . '" />
						 		<input type="text" id="order_city" name="order_city"  autocomplete="off" placeholder="City" value="' . $order_city . '" />
								<!--<input type="text" id="order_state" name="order_state" placeholder="State" value="' . $order_state . '" />-->
								'.$state_html.'
								<input type="text" id="order_zip_code" name="order_zip_code" placeholder="Zip" value="' . $order_zip_code . '" />
								<input type="text" id="service" name="service" autocomplete="off" placeholder="Service" value="' . $service . '" />
								'.$type_html.'
								<!--<input type="submit" value="Filter" />-->
								<input type="submit" name="submit[filter]" value="Filter" />
								<input type="submit" name="submit[export]" value="Export / Print" />
						 
								<script>
								jQuery( function($) {
									var from = $(\'input[name="mishaDateFrom"]\'),
										to = $(\'input[name="mishaDateTo"]\');
						 
									$( \'input[name="mishaDateFrom"], input[name="mishaDateTo"]\' ).datepicker();
									// by default, the dates look like this "April 3, 2017" but you can use any strtotime()-acceptable date format
										// to make it 2017-04-03, add this - datepicker({dateFormat : "yy-mm-dd"});
						 
						 
										// the rest part of the script prevents from choosing incorrect date interval
										from.on( \'change\', function() {
										to.datepicker( \'option\', \'minDate\', from.val() );
									});
						 
									to.on( \'change\', function() {
										from.datepicker( \'option\', \'maxDate\', to.val() );
									});
						 
								});
								</script>';
							?>
                            </div>
								<?php
								$this->stats_obj->prepare_items();
								$this->stats_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
        <script type="text/javascript">
			var options = {
						script:"/autosuggest.php?json=true&field=city&",
						varname:"input",
						json:true,
						shownoresults:false,
						maxresults:6,
						minchars:3,
						timeout: 25000,
						callback: function (obj) { //document.getElementById('testid').value = obj.id; 
							//jQuery('.SearchButton').removeAttr('disabled');
						}
					};
					var as_json = new bsn.AutoSuggest('order_city', options);
			var options_service = {
						script:"/autosuggest.php?json=true&field=service&",
						varname:"input",
						json:true,
						shownoresults:false,
						maxresults:6,
						minchars:3,
						timeout: 25000,
						callback: function (obj) { //document.getElementById('testid').value = obj.id; 
							//jQuery('.SearchButton').removeAttr('disabled');
						}
					};
					var as_json = new bsn.AutoSuggest('service', options_service);
					
						</script>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Stats',
			'default' => 20,
			'option'  => 'stats_per_page'
		];

		add_screen_option( $option, $args );

		$this->stats_obj = new Sale_Stats_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


add_action( 'plugins_loaded', function () {
	SP_Plugin_Sale_Stat::get_instance();
} );