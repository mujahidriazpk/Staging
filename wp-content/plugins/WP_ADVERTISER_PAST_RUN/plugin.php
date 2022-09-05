<?php
/*
Plugin Name: WP_ADVERTISER_PAST_RUN
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ADVERTISER_PAST_RUN_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'ADVERTISER PAST RUN', 'sp' ), //singular name of the listed records
			'plural'   => __( 'ADVERTISERS PAST RUN', 'sp' ), //plural name of the listed records
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
	public static function get_ADVERTISERs_PAST_RUN( $searchStr ) {

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
						//'post__not_in' => array($demo_listing),
						'post_type' => 'shop_order',
						//'post_status'         => $post_statuses,
						'ignore_sticky_posts' => 1,
						//'meta_key' => '_auction_dates_from',
						//'orderby' => 'meta_value',
					//	'order'               => 'desc',
						'posts_per_page'      => -1,
						
					);
	if(isset($searchStr) && $searchStr !="" ){
		$args['meta_query'] =    array(
					'key'   => 'order_ref_#',
					'compare'   => 'LIKE',
					'value'   => $searchStr,
		
				);	
		$result['searchStr'] = $searchStr;
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


class SP_Plugin_ADVERTISER_PAST_RUN {

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
			'Past Run',
			'Past Run',
			'shopadoc_admin_cap',
			'ADVERTISER_PAST_RUN',
			[ $this, 'plugin_settings_page' ]
		);
		//add_submenu_page( 'performance_auction', 'Order #', 'Order #','shopadoc_admin_cap', 'admin.php?page=auctions');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>

<div class="wrap">
  <h2 class="not_print">ADVERTISERS<br /><span style="font-weight:normal">Past Runs</span></h2>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>-child/autosuggest/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>-child/autosuggest/css/autosuggest_inquisitor.css" type="text/css" />
  <!--<link rel="stylesheet" href="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/css/validationEngine.jquery.css" type="text/css"/>

</script>
<script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8">
</script>
<script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8">
</script>-->
<script type="text/javascript">
	/*jQuery(document).ready(function () {
		jQuery("#wpforms-form-ad-demo").validationEngine('attach', {
			promptPosition: 'centerRight',
			scroll: false,
			ajaxFormValidation: true,
			//onBeforeAjaxFormValidation: beforeCall,
			//onAjaxFormComplete: ajaxValidationCallback,
		});
	});
jQuery('#wpforms-form-ad-demo').submit(function(e) {
                e.preventDefault();
                var vars = jQuery("#wpforms-form-ad-demo").serialize();

                if (jQuery("#wpforms-form-ad-demo").validationEngine('validate')) {

                    jQuery.ajax({
                            url:"sample.php"
                    });

                }
            });*/
function submitUser(){

}
</script>
 <style type="text/css">
 .form-table th {
	padding:4px 0 !important;
}
.form-table td {
	padding:5px 10px !important;
}
.wp-core-ui select {
	width:75%;
}
.containerMain {
	/*border: solid 1px #000;*/
						float: left;
	width: 100%;
}
.containerMain .row {
	background: #fff;
	border-bottom: solid 1px #000;
	height:40px;
}
.detail_View .row {
	height:auto !important;
}
.jconfirm-cell .row {
	background:none !important;
	border-bottom:none !important;
}
.rowMain {
	background: #000 !important;
	color:#fff !important;
}
.containerMain .col {
	border-right: solid 1px #000;
	max-width: 100%;
	padding: 2px 0;
	text-align: center;
	position:relative;
	display: flex;
	align-items: center;
	justify-content: center;
}
.popupView .col:nth-child(2) {
	margin-left:0;
}
.impression_col label {
	width:100%;
	float:left;
}
.detail_View .col {
	display:inline-block !important;
}
.containerMain .row .col:last-child {
	border-right:none !important;
}
.rowMain .col {
	color:#fff;
	/*border-right: solid 1px #fff !important;*/
						text-transform:uppercase;
	margin-left:1px;
}
select {
/*display:none !important;*/
					}
.select2-container, .th-container, .view_main {
	width:100% !important;
}
.select2-dropdown--below {
	width:200px !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
	color: #DB2D69;
	font-weight: bold;
}
#ui-datepicker-div {
	width:auto !important;
}
.select2-container .select2-selection--single .select2-selection__arrow {/*display:none !important;*/
}
/*.select2-container .select2-selection--single .select2-selection__rendered{padding-right:8px !important;}*/
					.select2-container .select2-selection--single .select2-selection__rendered {
	padding: 0 !important;
	text-align: center;
}
.view_main {
	font-weight: bold;
	position: absolute;
	z-index: 1;
	background: #fff;
	top: 0%;
	left: 0%;
	width: 100% !important;
	line-height: 17px;
	text-align: left;
	display: table;
	padding: 0 2%;
	height: 100%;
}
.view_main a {
	color:#000C76 !important;
}
.view_main_select {
	font-weight: bold;
	position: absolute;
	z-index: 0;
	background: #fff;
	width: 83px !important;
	top: 7%;
	left: 1%;
	width: 70% !important;
	text-align: left;
}
/*.view_main{position:absolute;z-index:10000;background:#fff;width:90px !important;}*/
					.select2-selection__arrow_custom {
	right: 1px;
	height: 28px;
	width: 23px;
	background: url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%206l5%205%205-5%202%201-7%207-7-7%202-1z%22%20fill%3D%22%23555%22%2F%3E%3C%2Fsvg%3E) no-repeat right 5px top 55%;
	background-size: 16px 16px;
	float:right;
}
.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow {
	z-index:1;
}
.detail_View {
  background: #fff;
  z-index: 10001;
  width: 31%;
  top: 40px;
  text-align: left;
  padding: 10px;
  display: block;
  box-shadow: 0px 3px 25px #00000040;
  border-radius: 5px;
  float: left;
  margin-right: 1.5%;
  margin-bottom: 2%;
}
.cards_main .detail_View:last-child{
  margin-right: 0 !important;
}
.option_img {
	float:left;
	margin-right:5px;
}
.ad_details label {
	font-size: 11px;
	color: #000;
	margin: 0;
	font-weight: 500;
	margin-top: -3px;
	margin-right: 5px;
}
.ad_details .popup_val {
	color:#000;
	font-size:11px;
	font-weight:bold;
}
.ad_details img {
	width:100%;
	max-width:100%;
}
.ad_details .row, .ad_details .col {
	border:none;
	text-align:left;
}
.edit_link, .delete_link {
	font-size:11px;
	font-weight:bold;
}
.edit_link {
	color:#10C168;
}
.delete_link {
	color:#DB2D69;
}
.btn {
	font-size: 14px !important;
	font-weight: 400 !important;
	font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.wc-wp-version-gte-53 .select2-container .select2-selection--single {
	border:none !important;/*box-shadow:none !important;*/
}
.number_col_head {
	width:55px;
	display: flex;
	/* align-content: center; */
												justify-content: center;
}
.number_col {
	width:28px;
	display: flex;
	/* align-content: center; */
												justify-content: center;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
	background:#fff;
}
.select2-container--default .select2-selection--single {
	background-color:transparent !important;
}
.wc-wp-version-gte-53 .select2-dropdown--below {
	box-shadow: 0px 3px 6px #00000033 !important;
}
.main_heading {
	font-size:16px !important;
	font-weight:bold !important;
	line-height:33px !important;
}
.month-heading {
	background:#fff;
	margin-top:5px;
	padding:5px;
	font-size:14px !important;
	font-weight:bold !important;
	margin-left:-5px !important;
}
.sub_heading {
	font-size:14px !important;
	font-weight:bold !important;
}
.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow {
	z-index:1;
}
.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__rendered {
	background:#fff;
}
.popupClose {
	float: right;
	position: absolute;
	right: 2px;
	top: 2px;
	font-size: 12px;
	color: #000;
	font-weight: bold;
}
.ad_expire {
	color: #000;
	font-size: 8px;
	float: right;
	position: absolute;
	right: 0;
	bottom: 0px;
	font-weight: bolder;
}
.view_main a {
	font-size: 12px;
	display: table-cell;
	vertical-align: top;
}
.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow {
	top:-12px;
}
.address_area {
	height:70px;
}
#filter_date {
	float: left;
	border: solid 3px #000;
	padding: 10px;
	margin-bottom: 10px;
	border-radius:3px;
}
input[name="mishaDateFrom"], input[name="mishaDateTo"] {
	line-height: 28px;
	height: 37px;
	margin: 0;
	width: 100%;
	font-weight:bold;
	font-size:12px;
	background:url(calendar.png);
	background-repeat:no-repeat;
	background-size:20px 20px;
	background-position:right;
}
.period {
	line-height: 28px;
	height: 37px;
	margin: 0;
	width: auto;
	font-weight:bold;
	font-size:12px;
	background:url(calendar.png);
	background-repeat:no-repeat;
	background-size:20px 20px;
	background-position:right;
	background:#0A7BE2 !important;
	color:#fff !important;
	font-weight:normal !important;
	border:1px solid #F5F5F5 !important;
	padding:0 5px 0 5px !important;
}
#filterText, #filterTextDate {
	line-height: 28px;
	height: 37px;
	margin: 0;
	width: 100% !important;
	width: auto;
	font-weight: bold !important;
	font-size: 12px;
	background: transparent;
	border: 1px solid #8c8f94;
	padding: 0 5px 0 5px !important;
}
/* body {
				  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
				  font-size: 1rem;
				  font-weight: 400;
				  line-height: 1.5;
				  color: #212529;
				  text-align: left;
				}*/
				/*.row {
	display: -ms-flexbox;
	display: flex;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	margin-right: -15px;
	margin-left: -15px;
}
.row::before, .row::after {
	display: table;
	content: " ";
}
*, ::before, ::after {
	box-sizing: border-box;
}
.col {
	-ms-flex-preferred-size: 0;
	flex-basis: 0;
	-ms-flex-positive: 1;
	flex-grow: 1;
	max-width: 100%;
}
.col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl, .col-xl-auto {
	position: relative;
	width: 100%;
	padding-right: 15px;
	padding-left: 15px;
}*/

/* Create three unequal columns that floats next to each other */
.col {
  float: left;
  
  /*padding: 10px;
  height: 300px; /* Should be removed. Only for demonstration */
}

.left {
  width: 25%;
}
.right {
  width: 40%;
  padding-left:8px !important;
}
.middle {
  width: 35%;
  padding-left:20px !important;
}
.rowNew p{float:left;width:100%;margin:0 0 5px;}
/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
.cards_main {
	float:left;
	width:100%;
	padding-top:20px;
}
.ad_image_div{
	float:left;
	text-align:center
}
.ad_image_div img.ad_image {
    max-width: 100%!important;
    height: 70px !important;
    width: auto !important;
    float: none;
}
.wp-admin p label input[type=checkbox] {
    margin-top: 12px !important;
    margin-left: -15px;
}
input[type="file"]:focus, input[type="radio"]:focus, input[type="checkbox"]:focus{
	outline:unset !important;
}
.jconfirm.jconfirm-white .jconfirm-box, .jconfirm.jconfirm-light .jconfirm-box {
    background: #F2F2F2;
    background-image: none !important;
    background-repeat: no-repeat !important;
    /* background-size: contain; */
    /* min-height: calc(100vh - 31.7333px - 45px); */
    min-height:inherit !important;
}

/* Create a custom checkbox */
.checkmark_my {
	position: absolute !important;
	top:6px;
	left: 3px;
	height: 16px !important;
	width: 16px !important;
	background-color: #eee;
}


/* On mouse-over, add a grey background color */
.container_my:hover input ~ .checkmark_my, .container_my input ~ .checkmark_my {
 background-color: #fff;
 border:solid 2px red;
}

/* When the checkbox is checked, add a blue background */
.container_my input:checked ~ .checkmark_my {
 background-color: #0A7BE2;
}
/* Create the checkmark_my/indicator (hidden when not checked) */
.checkmark_my:after {
	content: "";
	position: absolute !important;
	display: none;
}

/* Show the checkmark_my when checked */
.container_my input:checked ~ .checkmark_my:after {
 display: block;
}
/* Style the checkmark_my/indicator */
.container_my .checkmark_my:after {
	left:3px;
	top: 1px;
	width: 5px;
	height: 9px;
	border: solid #fff;
	border-width: 0 3px 3px 0;
	-webkit-transform: rotate(45deg);
	-ms-transform: rotate(45deg);
	transform: rotate(45deg);
}
.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons button.btn-default, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons button.btn-default{
    background: #ddd !important;
}
@media print {
    /*.cards_main{
        background-color: white !important;
        height: 100% !important;
        width: 100% !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        margin: 0 !important;
        padding: 15px !important;
		z-index: 9999999 !important; 
    }
	*/
	#wpbody-content,#wpcontent,body{background:#fff !important;}
	 .cards_main{
		float:left;
		width:100%;
		height:auto;
		background-color: white !important;
		  top:-20px !important;
        left: 0 !important;
        margin: 0 !important;
        padding:0 !important;
		z-index: 9999999 !important; 
		position:absolute !important;
	}
	.myDivToPrint {
		float:left;
		width:100%;
		height:auto;
		background-color: white !important;
		  top: 0 !important;
        left: 0 !important;
        margin: 0 !important;
        padding:0 !important;
		z-index: 9999999 !important; 
		position:relative !important;
	}
	.ad_details .popup_val,.ad_details label,.edit_link, .delete_link,p{
		font-size: 18px !important; 
        line-height: 27px !important;
	}	
	.row{
		display: -ms-flexbox;
	display: block !important;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	margin-right: 0 !important;
	margin-left: 0 !important;
	float:left;
	
	}
}
</style>
<style id="bg-for-print"></style>
  <?php 
  				global $wpdb;		
  				if (isset($_GET["action"]) && $_GET["action"] == 'upgrade') {}
				if (isset($_GET["action"]) && $_GET["action"] == 'delete') {}
				if (isset($_GET["action"]) && $_GET["action"] == 'approve') {}
				if (isset($_GET["action"]) && $_GET["action"] == 'deactive') {}
			$period = ( isset( $_POST['period'] ) && $_POST['period'] ) ? $_POST['period'] : 'today';
		$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
		$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		$from = '';
		$to = '';
		if($period=='yesterday'){
			$from =date('m/d/y',strtotime("-1 days"));
			$to = date('m/d/y',strtotime("-1 days"));
		}elseif($period=='7days'){
			$from = date("m/d/y",strtotime( "monday this week" ));
			$to = date('m/d/y', strtotime( 'friday this week' ) );
		}elseif($period=='last7days'){
			$from = date("m/d/y",strtotime( "monday last week" ));
			$to = date('m/d/y', strtotime( 'friday last week' ) );
		}elseif($period=='lastmonth'){
			$from = date("m/d/y", strtotime("first day of previous month"));
			$to = date("m/d/y", strtotime("last day of previous month"));
		}elseif($period=='thisyear'){
			$from = "01/01/".date("y");
			$to = "12/31/".date("y");
		}elseif($period=="quarter"){
			$quarter = GA_List::get_dates_of_quarter('','','m/d/y');
			$from = $quarter['start'];
			$to = $quarter['end'];
		}
		$from_GA = $from;
		$to_GA = $to;
		if($from_GA==""){
			$from_GA = date('m/d/y');
			$to_GA = date('m/d/y');
		}
		if($period=='today' || $period=='yesterday')
		{$to = '';}
		if($_POST['period']=='custom'){
			$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
			$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		}
		if( isset( $_POST['filterText'] ) && $_POST['filterText'] ){
			$filterText = $_POST['filterText'];
		}else{
			$filterText = 'all';
		}
			
  ?>
  <script type="text/javascript">
  function printAll(){
	   jQuery("#bg-for-print").text("@media print {.cards_main{position:absolute !important;} .myDivToPrint{background-color: white !important;height: 100% !important;width: 100% !important;position: relative !important;top: 0 !important;left: 0 !important;margin: 0 !important;padding: 15px !important;z-index:9999999 !important;}}");
	  window.print();
  }
  function printSingle(id){
	  //alert(id);
	 // jQuery("#card_"+id).attr('style','background-color: white !important;height: 100% !important;width: 100% !important;position: fixed !important;top: 0 !important;left: 0 !important;margin: 0 !important;padding: 15px !important;z-index: 9999999 !important;');
		
	  jQuery("#bg-for-print").text("@media print {.cards_main{position:relative !important;} #card_"+id+"{background-color: white !important;height: 100% !important;width: 100% !important;position: fixed !important;top: 0 !important;left: 0 !important;margin: 0 !important;padding: 15px !important;z-index:10000000 !important;}}");
	  window.print();
  }
			jQuery( function($) {
						/*$(document).on('click', '.print', function(e) {
							e.preventDefault();
							window.print();
						});*/
						var maxHeight = 0;

						$(".cards_main .detail_View").each(function(){
						   if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
						});
						$(".cards_main .detail_View").height(maxHeight);
						
						var from = $('input[name="mishaDateFrom"]'),
						to = $('input[name="mishaDateTo"]');
						$.datepicker.setDefaults({
							dateFormat: "mm/dd/y"
						});
						$( 'input[name="mishaDateFrom"], input[name="mishaDateTo"]' ).datepicker();
						from.on( 'change', function() {
								to.datepicker( 'option', 'minDate', from.val());
								if(to.val()!="" && $(".period").val() == 'custom'){
									$("#filterForm").submit();
								}
							});
							to.on( 'change', function() {
								from.datepicker( 'option', 'maxDate', to.val());
								if(from.val()!="" && $(".period").val() == 'custom'){
									$("#filterForm").submit();
								}
							});
						$(".period").on("change",function(){
							if(jQuery(this).val()=='custom'){
								if(to.val()!="" && from.val()!=""){
									$("#filterForm").submit();
								}
							}else{
								$("#filterForm").submit();
							}
							
						});
						$("#filterText").on("change",function(){
							$("#filterForm").submit();
							
						});
						$("#filterText").val('<?php echo $filterText;?>');
						$(".period").val('<?php echo $period;?>');
						
					});
					function filterGA(){  
								var rex = new RegExp(jQuery('#filterText').val());
								if(rex =="/all/"){clearFilter()}else{
									jQuery('.content').hide();
									jQuery('.content').filter(function() {
										return rex.test(jQuery(this).text());
									}).show();
							}
							}
						function filterGADate(){  
								var rex = new RegExp(jQuery('#filterTextDate').val());
								if(rex =="/all/"){clearFilter()}else{
									jQuery('.content').hide();
									jQuery('.content').filter(function() {
										return rex.test(jQuery(this).text());
									}).show();
							}
							}
						function clearFilter(){
								jQuery('.filterText').val('');
								jQuery('.content').show();
							}
			</script>
             <?php 
			 $args = array(
						'role'    => 'advanced_ads_user',
						'orderby' => 'user_nicename',
						'order'   => 'ASC'
					);
			$users = get_users( $args );
			$userDropdown = '';
			foreach($users as $user){
				$selected ='';
				if($user->ID==$ad_user){
					$selected = ' selected="selected" '; 
				}
				$ad_demo_company_name =  get_user_meta($user->ID, 'ad_demo_company_name', true );
				$userDropdown .= '<option value="'.$user->ID.'" '.$selected.'>'.$ad_demo_company_name.'</option>';
			}
	?>
  <!--<link rel="stylesheet" href="https://getbootstrap.com/docs/4.2/dist/css/bootstrap.css" type="text/css" />-->

  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable containerMain">
        <form method="post" class="not_print" style="float:none;margin:0 auto;width:70%;padding:0 0;" id="filterForm">
         	<div style="float:left;width:20%;padding:10px;">
            	<select id='filterText' name="filterText" style='display:inline-block' >
                <option value="all">- Advertiser -</option>
                  <?php echo $userDropdown;?>
                </select>
            </div>
            <!--<div style="float:left;width:20%;padding:10px;">
            	<select id='filterTextDate' style='display:inline-block' onchange='filterGADate();'>
                <option value="all">- Run Period -</option>
                  <?php echo $filter_dateDropdown;?>
                </select>
            </div>-->
            <div style="float:left;width:30%;padding:10px;">
            	<input type="text" name="mishaDateFrom" placeholder="Start Date" value="<?php echo $from;?>" autocomplete="off">
            </div>
            <div style="float:left;width:2%;padding:15px 0;text-align:right;">
            	&nbsp;-&nbsp;
            </div>
            <div style="float:left;width:30%;padding:10px;">
            	<input type="text" name="mishaDateTo" placeholder="End Date" value="<?php echo $to;?>" autocomplete="off"/>
            </div>
            <div style="float:left;width:18%;padding:10px;">
            	<!--<input type="submit" name="submit" value="Filter" class="btn btn-primary"/>-->
                <select name="period" class="period" >
                  <option value="today">Today</option>
                  <option value="yesterday">Yesterday</option>
                   <option value="7days">This Week</option>
                  <option value="last7days">Last Week</option>
                  <option value="lastmonth">Last Month</option>
                  <option value="quarter">This Quarter</option>
                  <option value="thisyear">This Year</option>
                  <option value="custom">Custom</option>
                </select>
            </div>
          </form>
         
          <div class="cards_main">
          <div class="actions_div not_print" style="padding-bottom:10px;" ><a href="javascript:" onclick="printAll();">Print All</a> | <a href="javascript:" onclick="selectAll();">Select All</a> | <a href="javascript:" onclick="DelectAds();">Delete</a></div>
          <?php
			  if( isset( $_POST['filterText'] ) && $_POST['filterText'] ){
					$args = array(
					 'author'        => $_POST['filterText'],
					'post_status'         => array('advanced_ads_expired'),
					'posts_per_page'      => -1,
					'post_type'     => 'advanced_ads',
				  );
			 }else{
          		$args = array(
					'post_status'         => array('advanced_ads_expired'),
					'posts_per_page'      => -1,
					'post_type'     => 'advanced_ads',
				  );
			 }
			$query = new WP_Query($args );
			$posts = $query->posts;
			$ads_status = array();	
			//print_r($posts);
			//if ( isset( $request['addate'] ) && '' !== $request['addate'] ) {
				$new_posts = array();
				foreach ( $posts as $post ) {
						//$option = $this->all_ads_options[ $post->ID ];
						//$post_date_gmt = strtotime($post->post_date_gmt);
						$option = maybe_unserialize(get_post_meta($post->ID,'advanced_ads_ad_options',TRUE));
						if($from !="" && $to !=""){
							if ($option['expiry_date'] >= strtotime($from) &&  $option['expiry_date'] <= strtotime($to) ) {
								$new_posts[] = $post;
							}
						}elseif($from !=""){
							if ($option['expiry_date'] >= strtotime($from)) {
								$new_posts[] = $post;
							}
						}else{
							if ( $option['expiry_date'] && time() >= $option['expiry_date'] ) {
								$new_posts[] = $post;
							}
						}
				}
				$posts                  = $new_posts;
				//$the_query->found_posts = count( $posts );
				//$using_original         = false;
		//}
	foreach($new_posts as $post){
	$post_id= $post->ID;
	//$post   = get_post( $post_id );
	$advanced_ads_ad_options = maybe_unserialize(get_post_meta($post_id, 'advanced_ads_ad_options', true ));
	$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
	$user = get_userdata($post->post_author);
	$City =  get_user_meta($post->post_author, 'billing_city', true );
	$State =  get_user_meta($post->post_author, 'billing_state', true );
	$ad_demo_company_name = get_user_meta($post->post_author, 'ad_demo_company_name',true);
	
	$ad_type = $advanced_ads_ad_options['type'] ;
	$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
	$img_atts = wp_get_attachment_image_src($attach_id, 'full');
	$src = $img_atts[0];
	$ad_link = $advanced_ads_ad_options['url'];
	$post_date_gmt = date("M j, Y",strtotime($post->post_date_gmt));
	if($advanced_ads_ad_options['expiry_date']){
		$expiry_date = date("M j, Y",$advanced_ads_ad_options['expiry_date']);
	}
	$ad_user = get_post_meta( $post_id, 'ad_user', true);
	$ad_location  = get_post_meta( $post_id, 'ad_location', true );
	$user_id= $post->post_author;
	$ad_demo_agent = get_user_meta( $user_id, 'ad_demo_agent', true);
	$billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true);
	$billing_address_2 = get_user_meta( $user_id, 'billing_address_2', true);
	$billing_city = get_user_meta( $user_id, 'billing_city', true);
	$billing_state = get_user_meta( $user_id, 'billing_state', true );
	$billing_postcode = get_user_meta( $user_id, 'billing_postcode', true );
	$billing_phone = get_user_meta( $user_id, 'billing_phone', true );
	$address = $billing_address_1.' '.$billing_address_2.'<br />'.$billing_city.', '.$billing_state.' '.$billing_postcode;
	
	
	$ad_id =$post_id;
	
	$admin_class = new Advanced_Ads_Tracking_Admin();
	if($expiry_date==''){
		$to_date = date("m/d/Y");
	}else{
		$to_date = $expiry_date;
	}
	$args = array(
		'ad_id' => array( $ad_id ), // actually no effect
		'period' => 'custom',
		'groupby' => 'day',
		'groupFormat' => 'Y-m-d',
		'from' => date('m/d/Y',(strtotime ( '-1 day' , strtotime ( $post->post_date_gmt) ) )),
		'to' => date("m/d/Y",strtotime($to_date)),
	);
	$impr_stats = $admin_class->load_stats( $args, 'wp_advads_impressions' );
	$clicks_stats = $admin_class->load_stats( $args, 'wp_advads_clicks' );
	$impr_sum = 0; 
	$click_sum = 0; 
	$ctr = 0;
	if( isset( $impr_stats ) && is_array( $impr_stats ) ) :
		$impr_stats = array_reverse( $impr_stats );
		$impr_sum = 0; 
		$click_sum = 0; 
		foreach ( $impr_stats as $date => $all ) :
					$impr = ( isset( $all[$ad_id] ) )? $all[$ad_id] : 0;
					$impr_sum += $impr;
					$click = ( isset( $clicks_stats[$date] ) && isset( $clicks_stats[$date][$ad_id] ) )? $clicks_stats[$date][$ad_id] : 0;
					$click_sum += $click;
					
		endforeach; 
		if ( 0 != $impr_sum ) {
			$ctr = $click_sum / $impr_sum * 100;
		}
		if($ctr < 1 && $click_sum  > 0){
			$ctr = number_format( $ctr, 2) . ' %';
		}else{
			$ctr = number_format( $ctr, 2 ) . ' %';
		}
	endif;
	//echo $impr_sum."==".$click_sum."==".$ctr;
	if($ad_type=='plain'){
		if($post->post_content !=""){
		$newEnbedCode = preg_replace('/width="([0-9]+)" height="([0-9]+)"/', ' width="165" height="135" class="youtube"', $post->post_content);
		$image_html = '<div class="col" style="border:none;text-align:center;width:300px;height:250px;">'.$newEnbedCode.'</div>';
		}
	}else{
		//
		/*$image_html = ' <div class="col" style="border:none;text-align:left;"><img src="'.$src.'" id="ad_image_src"  /><img src="/wp-content/plugins/WP_ADS/GA_logo.jpg" id="GA_logo"  style="margin-top:10px;"/><br><a href="https://analytics.google.com/analytics/web/#/report/content-event-events/a166289038w232260644p218158068/explorer-segmentExplorer.segmentId=analytics.eventLabel&_r.drilldown=analytics.eventCategory:Advanced%20Ads&explorer-table.plotKeys=%5B%5D" class="not_print" style="float:left;" target="_blank"><strong>Link</strong></a></div>';*/
        $image_html = ' <div class="col left ad_image_div" style="border:none;text-align:left;"><img src="'.$src.'" id="ad_image_src"  class="ad_image"/><img src="/wp-content/plugins/WP_ADS/GA_logoNew.jpg" id="GA_logo"  style="margin-top:10px;"/><br><a href="https://analytics.google.com/analytics/web/#/report/content-event-events/a166289038w232260644p218158068/_u.date00='.date("Ymd",strtotime($post_date_gmt)).'&_u.date01='.date("Ymd",strtotime($expiry_date)).'&_r.drilldown=analytics.eventCategory:Advanced%20Ads,analytics.eventLabel:['.$post->ID.'] '.utf8_encode(str_replace("–","&ndash;",str_replace("/","~2F",htmlspecialchars(str_replace("&nbsp;"," ",$post->post_title))))).'&explorer-table.plotKeys=%5B%5D/" class="not_print" style="float:left;" target="_blank"><strong>Link</strong></a></div>';
	}
	if(strtotime(date("Y-m-d")) >= strtotime($expiry_date)){
		$tool_head = 'RUN TOTALS'; 
	}else{
		$tool_head = 'DYNAMIC TOTALS'; 
	}
	$query = "SELECT * FROM wp_posts where post_author = '".$user->ID."'and (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '% ".$_POST['type']."%' or post_excerpt = '".$_POST['type']."' ) ORDER BY ID ASC";
	
	$query = "SELECT option_name FROM `wp_options` where option_value = '".$post_id."' ORDER BY option_id desc limit 1";
	$option_name = $wpdb->get_var($query);
	if($option_name !=''){
		$tmp = explode("_",$option_name);
		$position = str_replace("position","",$tmp[0]);
		$col = str_replace("col","",$tmp[1]);
		$position_col = '<p><label>ROTATION:</label>&nbsp;&nbsp;&nbsp;<span class="popup_val" style="text-decoration:underline;">'.$position.'</span>&nbsp;&nbsp;&nbsp;&nbsp;<label>COLUMN:</label>&nbsp;&nbsp;&nbsp;<span class="popup_val" style="text-decoration:underline;">'.$col.'</span></p>';
	}else{
		$position_col = '<p><label>&nbsp;</label></p>';
	}
	$html = '';
	$html = '<div class="detail_View myDivToPrint" id="card_'.$post_id.'"><div class="ad_details popupView">
					<div style="width:100%;float:left;min-height:158px;position:relative;">
					<!--<a href=\'javascript:ClosePopup("'.$_REQUEST['ad_id'].'","'.$_REQUEST['type'].'","'.$_REQUEST['selected_ad'].'");\' class="not_print popupClose" style="float:right;width:20px;"><img src="/wp-content/plugins/WP_ADS/close.jpg" align="right" title="print" width="20px" class="print_icon"/></a>-->
					<p class="pull-right"><label>TODAY\'S DATE:</label><span class="popup_val" style="text-decoration:underline;">'.date("M j, Y").'</span><br /><!--<a href=\'javascript:ClosePopup("'.$_REQUEST['ad_id'].'","'.$_REQUEST['type'].'","'.$_REQUEST['selected_ad'].'");\' class="not_print delete_link" style="float:right;">close</a><a href=\'javascript:AdPopup("'.$_REQUEST['ad_id'].'","'.$_REQUEST['type'].'","'.$_REQUEST['selected_ad'].'");\' class="not_print edit_link" style="float:right;margin-right:10px;">Edit</a><br />--><a href="javascript:printSingle(\''.$post_id.'\')" class="not_print print" style="float:right;width:15px;padding-top: 5px;"><img src="/wp-content/plugins/WP_GA/print.png" align="right" title="print" width="20px" class="print_icon"/></a><!--<a href=\'javascript:detailPopupReload("'.$_REQUEST['ad_id'].'","'.$_REQUEST['type'].'","'.$_REQUEST['selected_ad'].'","'.$_REQUEST['rotation'].'","'.$_REQUEST['column'].'");\'  class="not_print reload" style="margin-right:24px;float:right;width:15px;padding-top: 5px;"><img src="/wp-content/plugins/WP_ADS/reload_icon.png" align="right" title="Reload" width="20px" class="print_icon"/></a>--></p>
					<p><label>CREATIVE:</label><span class="popup_val">'.$post->post_title.'</span></p>
					<p><label>COMPANY: <span class="popup_val">'.$ad_demo_company_name.'</span></label></p>
					<p><label>RUN PERIOD: <span class="popup_val">'.$post_date_gmt.' - '.$expiry_date.'</span></label></p>
					'.$position_col.'
					</div>
					<div class="rowNew" style="border:none;text-align:left;float:left;width:100%;position:relative;">
					 '.$image_html.'
					 <div class="col impression_col middle" style="border:none;text-align:left;" id="'.$_REQUEST['ad_id'].'_reload_content">
					  		<p><span style="text-decoration:underline;font-size:13px;color:#000;font-weight:bold;">'.$tool_head.'</span></p>
							<p><label>IMPRESSIONS:</label><span class="popup_val" style="text-decoration:underline;">'.$impr_sum.'</span></p>
							<p><label>CLICKS:</label><span class="popup_val" style="text-decoration:underline;">'.$click_sum.'</span></p>
							<p><label>CTR:</label><span class="popup_val" style="text-decoration:underline;">'.$ctr.'</span></p>
					  </div>
					  <div class="col right" style="border:none;text-align:left;">
					  		<p><label>AGENT:</label><br /><span class="popup_val">'.$ad_demo_agent.'</span></p>
							<p class="address_area"><label>ADDRESS:</label><br /><span class="popup_val" style="display:flex;">'.$address.'</span></p>
							<p><label>EMAIL:</label><br /><span class="popup_val" style="word-break:break-all;height:30px;float:left;">'.$user->user_email.'</span></p>
							<p><label>CELL:</label><span class="popup_val">'.$billing_phone.'</span></p>
						</div>
						<p class="pull-left not_print" style="position:absolute;bottom:8px;"><label class="checkbox container_my"><input style="border-radius:0px;" type="checkbox" name="ad_ids[]" id="ad_ids" value="'.$post_id.'" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"/><span class="checkmark_my"></span></label></p>
					</div>
				</div></div>';
	echo $html;


	}
		  ?>
          </div>
          
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script type="text/javascript">
	function selectAll(){
		jQuery('.cards_main input:checkbox').prop('checked', true);
	}
	function DelectAds(){
		var Ads = [];
		jQuery.each(jQuery('.cards_main input:checkbox:checked'), function(){
			Ads.push(jQuery(this).val());
		});
		if (Ads === undefined || Ads.length == 0) {
			alert("Please select an ad!");
		}else{
			var ads_id = Ads.join(",");
			jQuery.confirm({
							title: 'Confirm!',
							content: 'Are you sure you want to delete?  This action cannot be undone.',
							buttons: {
								/*confirm: function () {
									jQuery.alert('Confirmed!');
								},
								*/
								somethingElse: {
									text: 'Confirm',
									btnClass: 'btn-blue',
									keys: ['enter', 'shift'],
									action: function(){
										//jQuery.alert('Something else?');
										 jQuery.ajax({	
												url:'<?php echo get_site_url();?>/ajax.php',	
												type:'POST',
												data:{'mode':'deleteAds','ad_ids':ads_id},
												beforeSend: function() {},
												complete: function() {
												},
												success:function (data){
													window.location.replace(window.location.href + "&update=delete");
													return true;
												}
												
										});
									}
								},
								cancel: function () {
									//jQuery.alert('Canceled!');
								},
							}
						});
		}
	}
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

		$this->stats_obj = new ADVERTISER_PAST_RUN_List();
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
	SP_Plugin_ADVERTISER_PAST_RUN::get_instance();
} );