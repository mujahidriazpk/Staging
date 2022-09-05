<?php
/*
Plugin Name: WP_Auction
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Auctions_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Auction #', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Auction #', 'sp' ), //plural name of the listed records
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
	public static function get_Auctions( $searchStr ) {

		global $wpdb;
		/*$where = '';
		if(isset($_POST['auction_city']) && $_POST['auction_city'] !=""){
				$where  = " and auction_city like '%".$_POST['auction_city']."%' or company like '%".$_POST['auction_city']."%'";
		}
		if(isset($_POST['mishaDateFrom']) && $_POST['mishaDateFrom'] !="" && isset($_POST['mishaDateTo']) && $_POST['mishaDateTo'] !=""){
				$from = date('Y-m-d',strtotime($_POST['mishaDateFrom']));
				$to = date('Y-m-d',strtotime($_POST['mishaDateTo']));
				$where  .= " and dated >= '".$from."' AND dated <= '".$to."'";
		}*/
		global $demo_listing;
		$post_statuses = array('publish');
		$ids = array();
		$args = array(
						'post__not_in' => array($demo_listing),
						//'post_status'         => $post_statuses,
						'ignore_sticky_posts' => 1,
						'meta_key' => '_auction_dates_from',
						'orderby' => 'meta_value',
						'order'               => 'desc',
						'posts_per_page'      => -1,
						'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
						'auction_archive'     => TRUE,
						'show_past_auctions'  => TRUE,
					);
	if(isset($searchStr) && $searchStr !="" ){
		$args['meta_query'] =    array(
					'key'   => 'auction_#',
					'compare'   => 'LIKE',
					'value'   => $searchStr,
		
				);	
		$result['auction_city'] = $auction_city;
	}
	if(isset($auction_state) && $auction_state !="" ){
		$args['meta_query'] =    array(
					'key'   => 'auction_state',
					'compare'   => 'LIKE',
					'value'   => $auction_state,
		
				);	
		$result['auction_state'] = $auction_state;
	}
	if(isset($auction_zip_code) && $auction_zip_code !="" ){
		$args['meta_query'] =    array(
					'key'   => 'auction_zip_code',
					'compare'   => 'LIKE',
					'value'   => $auction_zip_code,
		
				);	
		$result['auction_zip_code'] = $auction_zip_code;
	}
	if(isset($mishaDateFrom) && $mishaDateFrom !="" && $mishaDateTo ==""){
		$args['meta_query'] =    array(
					'key'   => '_auction_dates_from_org',
					'compare'   => '>=',
					'value'   => date('Ymd',strtotime($mishaDateFrom)),
					'type'        => 'date'
		
				);	
	
	}elseif($mishaDateFrom =="" && isset($mishaDateTo) && $mishaDateTo !=""){
		$args['meta_query'] = array(   
				array(
					'key'   => '_auction_dates_to',
					'compare'   => '<=',
					'value'   => date('Ymd',strtotime($mishaDateTo)),
					'type'        => 'date'
		
				));	
	}elseif(isset($mishaDateFrom) && $mishaDateFrom !="" && isset($mishaDateTo) && $mishaDateTo !=""){
		$args['meta_query'] = array( 
				'relation' => 'AND',              
				array(
					'key'   => '_auction_dates_from_org',
					'compare'   => '>=',
					'value'   =>date('Ymd',strtotime($mishaDateFrom)),
					'type'        => 'date'
		
				),
				array(
					'key'   => '_auction_dates_to',
					'compare'   => '<=',
					'value'   => date('Ymd',strtotime($mishaDateTo)),
					'type'        => 'date'
		
				));	
	}
		$product_query = new WP_Query( $args );
		$count = $product_query->found_posts;
		$posts = $product_query->posts;
		
		$result['no_auctions'] = $count;
		$result['auction_data'] = $posts;
		return $result;
	}
	public static function get_stats( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		//$result_user = count_users();
		$arg_client = array( 'role' => 'seller' );
		$arg_dentist = array( 'role' => 'customer' );
		if(isset($_POST['user_city']) && $_POST['user_city'] !="" ){
			$arg_client['meta_query'] = array(
									//'relation' => 'OR',
									array(
										'key'     => 'client_city',
										'value'   => $_POST['user_city'],
										'compare' => 'LIKE'
									));
			$arg_dentist['meta_query'] = array(
									//'relation' => 'OR',
									array(
										'key'     => 'dentist_office_city',
										'value'   => $_POST['user_city'],
										'compare' => 'LIKE'
									),
									/*array(
										'key'     => 'dentist_home_city',
										'value'   => $_POST['user_city'],
										'compare' => 'LIKE'
									),*/
								);
			$result[0]['user_city'] = $_POST['user_city'];
			$result[1]['user_city'] = $_POST['user_city'];
		}
		
		if(isset($_POST['user_state']) && $_POST['user_state'] !="" ){
			$arg_client['meta_query'][] = array(array('key'     => 'client_state','value'   => $_POST['user_state'],'compare' => 'LIKE'));
			$arg_client['meta_query']['relation'] = 'AND';
			
			$arg_dentist['meta_query'][] = array(/*'relation' => 'OR',*/array('key' => 'dentist_office_state','value'   => $_POST['user_state'],'compare' => 'LIKE'),/*array('key'     => 'dentist_home_state','value'   => $_POST['user_city'],'compare' => 'LIKE')*/);
			$arg_dentist['meta_query']['relation'] = 'AND';
			
			$result[0]['user_state'] = $_POST['user_state'];
			$result[1]['user_state'] = $_POST['user_state'];
		}
		if(isset($_POST['user_zip_code']) && $_POST['user_zip_code'] !="" ){
			$arg_client['meta_query'][] = array(array('key'     => 'client_zip_code','value'   => $_POST['user_zip_code'],'compare' => 'LIKE'));
			$arg_client['meta_query']['relation'] = 'AND';
			
			$arg_dentist['meta_query'][] = array(/*'relation' => 'OR',*/array('key' => 'dentist_office_zip_code','value'   => $_POST['user_zip_code'],'compare' => 'LIKE')/*,array('key'     => 'dentist_home_zip_code','value'   => $_POST['user_zip_code'],'compare' => 'LIKE')*/);
			$arg_dentist['meta_query']['relation'] = 'AND';
			
			$result[0]['user_zip_code'] = $_POST['user_zip_code'];
			$result[1]['user_zip_code'] = $_POST['user_zip_code'];
		}
		//print_r($arg_client);
 
// WP_User_Query arguments


		$user_query_client = new WP_User_Query($arg_client);
		$total_client = $user_query_client->get_total();
		$user_query_dentist = new WP_User_Query($arg_dentist);
		$total_dentist = $user_query_dentist->get_total();
		
		$result[0]['user_type'] = 'Client';
		$result[0]['users'] = $total_client;
		$result[1]['user_type'] = 'Dentist';
		$result[1]['users'] = $total_dentist;
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
		if(isset($_POST['user_city']) && $_POST['user_city'] !=""){
				$where  = " and user_city like '%".$_POST['user_city']."%' or company like '%".$_POST['user_city']."%'";
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
		_e( 'No auction avaliable.', 'sp' );
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
		
		switch ( $column_name ) {
			case 'user_type':
				return $item[ $column_name ];
			case 'users':
				return $item[ $column_name ];
			case 'user_city':
				$user_city = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $user_city;
				//return get_post_meta($item['post_id'],'business_name',true);
			case 'user_state':
				$user_state = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $user_state;
			case 'user_zip_code':
    			$user_zip_code = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $user_zip_code;
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
			'user_type'    => __( 'Type', 'sp' ),
			'users'    => __( '# of Users', 'sp' ),
			'user_city'    => __( 'City', 'sp' ),
			'user_state'    => __( 'State', 'sp' ),
			'user_zip_code'    => __( 'Zip', 'sp' ),
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


class SP_Plugin_Auction {

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
			'Auction #',
			'Auction #',
			'shopadoc_admin_cap',
			'auctions',
			[ $this, 'plugin_settings_page' ]
		);
		//add_submenu_page( 'performance_auction', 'Auction #', 'Auction #','shopadoc_admin_cap', 'admin.php?page=auctions');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
		$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		$user_city = ( isset( $_POST['user_city'] ) && $_POST['user_city'] ) ? $_POST['user_city'] : '';
		$user_state = ( isset( $_POST['user_state'] ) && $_POST['user_state'] ) ? $_POST['user_state'] : '';
		$user_zip_code = ( isset( $_POST['user_zip_code'] ) && $_POST['user_zip_code'] ) ? $_POST['user_zip_code'] : '';
		?>

<div class="wrap">
  <h2>Auction #</h2>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>-child/autosuggest/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>-child/autosuggest/css/autosuggest_inquisitor.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/css/validationEngine.jquery.css" type="text/css"/>
  <script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script> 
  <script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
  <style type="text/css">
  #toplevel_page_admin-page-auctions a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-auctions a:after {
			right: 0;
			border: solid 8px transparent;
			content: " ";
			height: 0;
			width: 0;
			position: absolute;
			pointer-events: none;
			border-right-color: #f0f0f1;
			top: 50%;
			margin-top: -8px;
			}
				/*th#user_city{width:25%;}*/
				th,td{font-size:16px !important;}
				.error,.notice{display:none;}
				#6036312b3586a{}
				.widefat th,.widefat th.sortable, .widefat th.sorted{padding:8px 1px !important;}
			</style>
  <?php 
				if (isset($_REQUEST["action"]) && ($_REQUEST["action"]=='publish' || $_REQUEST["action"] =='Suspend')) {
					$post_id = $_REQUEST["post"];
					if($_REQUEST["action"]=='Suspend'){
						 $my_post = array('ID'    =>$post_id,'post_status'   => 'pending',);
					}else{
						$my_post = array('ID'    =>$post_id,'post_status'   => 'publish',);
					}
					$auction_no = get_post_meta($post_id, 'auction_#' , TRUE);
					// Insert the post into the database
					wp_update_post( $my_post );
					header("location:/wp-admin/admin.php?page=auctions&search_str=".$auction_no);
					exit;
				}
			?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <form method="post" style="float:none;margin:0 auto;width:55%;padding:50px 0;" id="searchForm">
            <?php $search_str = ( isset( $_REQUEST['search_str'] ) && $_REQUEST['search_str'] ) ? $_REQUEST['search_str'] : '';?>
            <input type="text" class="validate[required]" name="search_str" id="search_str" value="<?php echo $search_str;?>" style="width:79%; margin-right: 1%;padding:10px;float:left;" data-prompt-position="topLeft:0,3"/>
            <input type="submit" name="submit" value="Search" class="btn btn-primary" style="padding: 15px 10px;float:left;width:20%;"/>
          </form>
          <script type="text/javascript">
		  	jQuery("#searchForm").validationEngine({'custom_error_messages' : {
						'#search_str' : {
							'required': {
								'message': "Please enter Auction's Ref. No. e.g 'CA90031-2021-0914-0341-5122'"
							},
						},
					}
				});
		  </script>
          <?php if(isset( $_REQUEST['search_str'] ) && $_REQUEST['search_str']!=''){
			  global $US_state,$today_date_time;
			  $item = Auctions_List::get_Auctions($_REQUEST['search_str']);
			  if($item['no_auctions'] > 0){
			 ?>
          <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
                <th scope="col" id="6036312b3586a" class="manage-column column-6036312b3586a  desc" style="padding:8px 10px !important;"><span>Start Date</span></th>
                <th scope="col" id="name" class="manage-column column-name column-primary  desc"><span>Service</span></th>
                <th scope="col" id="price" class="manage-column column-price sortable desc"><span>Ask / Bid</span></th>
                <!--<th scope="col" id="5fc7b856ae0bc" class="manage-column column-5fc7b856ae0bc">Auction #</th>-->
                <th scope="col" id="6036312b3a9dc" class="manage-column column-6036312b3a9dc">City</th>
                <th scope="col" id="60e5c718d8654" class="manage-column column-60e5c718d8654">State</th>
                <th scope="col" id="6036312b3ad5c" class="manage-column column-6036312b3ad5c">Zip</th>
                <th scope="col" id="6039002027393" class="manage-column column-6039002027393">Status</th>
                <th scope="col" id="603906d6d0106" class="manage-column column-603906d6d0106" style="text-align:center;">Client</th>
                <th scope="col" id="60390b3f8622a" class="manage-column column-60390b3f8622a" style="text-align:center;">Dentist</th>
              </tr>
            </thead>
            <tbody id="the-list">
              <?php foreach($item['auction_data'] as $auction){
				
				$_auction_dates_from_org = get_post_meta($auction->ID, '_auction_dates_from_org' , TRUE);
				$auction_no = get_post_meta($auction->ID, 'auction_#' , TRUE);
				$auction_city = get_post_meta($auction->ID, 'auction_city' , TRUE);
				$auction_state = get_post_meta($auction->ID, 'auction_state' , TRUE);
				$auction_zip_code = get_post_meta($auction->ID, 'auction_zip_code' , TRUE);
				$_auction_current_bider = get_post_meta( $auction->ID, '_auction_current_bider' , TRUE);
				$seller = get_user_by( 'id', $auction->post_author );
				
				if($_auction_current_bider){
					  $Dentist =  "<a onclick=\"openUser('".$_auction_current_bider."','dentist');\" href='javascript:'><strong>D</strong></a>";
				}else{
						$Dentist = "-";
				}
				
			$product = dokan_wc_get_product( $auction->ID);
			if($auction->post_status =='pending'){
				$status = '<span>suspended</span>';
			}else{
				if($product->is_closed() === TRUE){	
				
				$_flash_cycle_start = get_post_meta( $product->get_id() , '_flash_cycle_start' , TRUE);
				$_flash_cycle_end = get_post_meta( $product->get_id() , '_flash_cycle_end' , TRUE);
				if(strtotime($today_date_time) < strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
					if(!$_auction_current_bid){
						$status = '<span>countdown to <span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span></span>';
						$class = " ended";
					}else{
						if($customer_winner == $user_id){
							$status = '<span class="red">✓ Email (Spam)</span>';
						}else{
							$status = '<span>ended</span>';
						}
						$class = " ended";
					}
				}else{
					if($customer_winner == $user_id){
						$status = '<span class="red">✓ Email (Spam)</span>';
					}else{
						$status = '<span>ended</span>';
					}
					$class = " ended";
				}
				/*if($customer_winner == $user_id){
					$status = '<span class="red">✓ Email (Spam)</span>';
				}else{
					$status = '<span>ended</span>';
				}*/
				$class = " ended";
		}else{
			if(($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )){
				if($post->post_status=='pending'){
					$status = '<span>countdown to auction</span>';
					$class = " upcoming-pending";
				}else{
					$status = '<span>countdown to auction</span>';
					$class = " upcoming";
				}
			}else{
				if($post->post_status=='pending'){
					$status = '<span>Live: Pending Review</span>';
					$class = " live";
				}else{
					if ($_auction_dates_extend == 'yes') {
						$status = '<span>extended</span>';
						$class = " extended";
					}else{
						if($_flash_status == 'yes'){
							$status = '<span><span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span> live</span>';
							$class = " live";
						}else{
							$status = '<span style="color:red;">auction live</span>';
							$class = " live";
						}
					}
				}
			}
		}
			}

			?>
              <tr id="post-<?php echo $auction->ID;?>" class="iedit author-other level-0 post-<?php echo $auction->ID;?> type-product status-publish has-post-thumbnail hentry product_cat-retrofit-upper-or-lower-denture-to-implants-by-locator-attachments" data-id="<?php echo $auction->ID;?>">
                <td class="6036312b3586a column-6036312b3586a column-meta" data-colname="Start Date"><?php echo date('m/d/y',strtotime($_auction_dates_from_org));?></td>
                <td class="name column-name has-row-actions column-primary" data-colname="Service"><strong><a class="row-title" href="/auction-<?php echo $auction->ID;?>/<?php echo $auction->post_name;?>/"><?php echo $auction->post_title;?></a></strong>
                  <div class="row-actions"><span class="id">ID: <?php echo $auction->ID;?> | </span><!--<span class="edit"><a href="/wp-admin/post.php?post=<?php echo $auction->ID;?>&amp;action=edit" aria-label="Edit “<?php echo $auction->post_title;?>”">Edit</a> | --></span><span class="view"><a href="/auction-<?php echo $auction->ID;?>/<?php echo $auction->post_name;?>/" rel="bookmark" aria-label="View “<?php echo $auction->post_title;?>”">View</a> | </span>
                    <?php if($auction->post_status == 'pending'){?>
                    <span class="publish"><a href="/wp-admin/admin.php?page=auctions&post=<?php echo $auction->ID;?>&action=publish" rel="bookmark" aria-label="publish “<?php echo $auction->post_title;?>”">Publish</a></span>
                    <?php }else{?>
                    <span class="Suspend"><a href="/wp-admin/admin.php?page=auctions&post=<?php echo $auction->ID;?>&action=Suspend" rel="bookmark" aria-label="Suspend “<?php echo $auction->post_title;?>”">Suspend</a></span>
                    <?php }?>
                  </div></td>
                <td class="price column-price" data-colname="Ask Fee / Current Bid"><?php echo str_replace(" ","",str_replace("Current Bid:","",$product->get_price_html())); ?></td>
                <!--<td class="5fc7b856ae0bc column-5fc7b856ae0bc column-meta" data-colname="Auction #"><?php echo $auction_no;?></td>-->
                <td class="6036312b3a9dc column-6036312b3a9dc column-meta" data-colname="City"><?php echo $auction_city;?></td>
                <td class="60e5c718d8654 column-60e5c718d8654 column-shortcode" data-colname="State"><?php echo $US_state[$auction_state];?></td>
                <td class="6036312b3ad5c column-6036312b3ad5c column-meta" data-colname="Zip"><?php echo $auction_zip_code;?></td>
                <td class="6039002027393 column-6039002027393 column-shortcode" data-colname="Status"><?php echo $status;?></td>
                <td class="603906d6d0106 column-603906d6d0106 column-shortcode" data-colname="Client" align="center"><a onClick="openUser('<?php echo $auction->post_author;?>','client');" href="javascript:"><strong>C</strong></a></td>
                <td class="60390b3f8622a column-60390b3f8622a column-shortcode" data-colname="Dentist" align="center"><?php echo $Dentist;?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
          <?php }?>
          <?php }else{?>
          <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
                <th scope="col" id="6036312b3586a" class="manage-column column-6036312b3586a  desc" style="padding:8px 5px !important;"><span>Start Date</span></th>
                <th scope="col" id="name" class="manage-column column-name column-primary  desc"><span>Service</span></th>
                <th scope="col" id="price" class="manage-column column-price sortable desc"><span>Ask / Bid</span></th>
                <!--<th scope="col" id="5fc7b856ae0bc" class="manage-column column-5fc7b856ae0bc">Auction #</th>-->
                <th scope="col" id="6036312b3a9dc" class="manage-column column-6036312b3a9dc">City</th>
                <th scope="col" id="60e5c718d8654" class="manage-column column-60e5c718d8654">State</th>
                <th scope="col" id="6036312b3ad5c" class="manage-column column-6036312b3ad5c">Zip</th>
                <th scope="col" id="6039002027393" class="manage-column column-6039002027393">Status</th>
                <th scope="col" id="603906d6d0106" class="manage-column column-603906d6d0106" style="text-align:center;">Client</th>
                <th scope="col" id="60390b3f8622a" class="manage-column column-60390b3f8622a" style="text-align:center;">Dentist</th>
              </tr>
            </thead>
            <tbody id="the-list">
             
              <tr id="post" class="iedit author-other level-0 post type-product status-publish has-post-thumbnail hentry product_cat-retrofit-upper-or-lower-denture-to-implants-by-locator-attachments">
                <td class="6036312b3586a column-6036312b3586a column-meta" data-colname="Start Date">-</td>
                <td class="name column-name has-row-actions column-primary" data-colname="Service">-</td>
                <td class="price column-price" data-colname="Ask Fee / Current Bid">-</td>
                <td class="6036312b3a9dc column-6036312b3a9dc column-meta" data-colname="City">-</td>
                <td class="60e5c718d8654 column-60e5c718d8654 column-shortcode" data-colname="State">-</td>
                <td class="6036312b3ad5c column-6036312b3ad5c column-meta" data-colname="Zip">-</td>
                <td class="6039002027393 column-6039002027393 column-shortcode" data-colname="Status">-</td>
                <td class="603906d6d0106 column-603906d6d0106 column-shortcode" data-colname="Client" align="center">-</td>
                <td class="60390b3f8622a column-60390b3f8622a column-shortcode" data-colname="Dentist" align="center">-</td>
              </tr>
            </tbody>
          </table>
          <?php }?>
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
					var as_json = new bsn.AutoSuggest('user_city', options);

					
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

		$this->stats_obj = new Auctions_List();
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
	SP_Plugin_Auction::get_instance();
} );