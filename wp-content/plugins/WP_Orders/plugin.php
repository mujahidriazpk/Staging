<?php
/*
Plugin Name: WP_Order
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Orders_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Order #', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Order #', 'sp' ), //plural name of the listed records
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
	public static function get_Orders( $searchStr ) {

	global $wpdb;
	$result = array();
	if(isset($searchStr) && $searchStr !="" ){
		$query = 'SELECT ID FROM `wp_posts` WHERE ID in (SELECT post_id FROM `wp_postmeta` where meta_key="order_ref_#" and  meta_value = "'.$searchStr.'") and post_type = "shop_order" order by ID desc ';
		$result['searchStr'] = $searchStr;
		$result['orders_data'] = $wpdb->get_results ($query);
		$result['no_orders'] = $wpdb->num_rows;
		
	}
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


class SP_Plugin_Order {

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
			'Order #',
			'Order #',
			'shopadoc_admin_cap',
			'orders',
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
  <h2>Order #</h2>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>-child/autosuggest/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>-child/autosuggest/css/autosuggest_inquisitor.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/css/validationEngine.jquery.css" type="text/css"/>
  <script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
  <style type="text/css">
  #toplevel_page_admin-page-orders a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-orders a:after {
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
				.widefat th,.widefat th.sortable, .widefat th.sorted{padding:8px 1px !important;}
			</style>
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
								'message': "Please enter Order's Ref. No. e.g '2021-0914-1042-5123'"
							},
						},
					}
				});
		  </script>
          <?php if(isset( $_REQUEST['search_str'] ) && $_REQUEST['search_str']!=''){
			  global $US_state,$today_date_time;
			  $item = Orders_List::get_Orders($_REQUEST['search_str']);
			  if($item['no_orders'] > 0){
			 ?>
          		<table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
                <th scope="col" id="6036312b3586a" class="manage-column " style="padding:8px 10px !important;"><span>Date</span></th>
                <th scope="col" id="name" class="manage-column"><span>Originator</span></th>
                <th scope="col" id="6036312b3a9dc" class="manage-column column-6036312b3a9dc">City</th>
                <th scope="col" id="60e5c718d8654" class="manage-column column-60e5c718d8654">State</th>
                <th scope="col" id="6036312b3ad5c" class="manage-column column-6036312b3ad5c">Zip</th>
                <th scope="col" id="6039002027393" class="manage-column column-6039002027393">Payment</th>
                <th scope="col" id="603906d6d0106" class="manage-column column-603906d6d0106">Processed</th>
                <th scope="col" id="60390b3f8622a" class="manage-column column-60390b3f8622a">Last 4</th>
              </tr>
            </thead>
            <tbody id="the-list">
            <?php foreach($item['orders_data'] as $row){
							$order = wc_get_order( $row->ID );
							$user_id   = $order->get_user_id(); // Get the costumer ID
							$user      = $order->get_user();
							$first_name = get_user_meta( $user_id, 'first_name', true);
							$last_name = get_user_meta( $user_id, 'last_name', true);
							$billing_city = $order->get_billing_city();
							$billing_state = $order->get_billing_state();
							$billing_postcode = $order->get_billing_postcode();
							$Originator = $first_name.' '.$last_name;
						
							$designation = get_user_meta( $user_id, 'designation', true );
							if($designation!=""){
								$Originator .=', '.$designation;
							}
							$order_status  = $order->get_status(); // Get the order status (see the conditional method has_status() below)
							$currency      = $order->get_currency(); // Get the currency used  
							$payment_method = $order->get_payment_method(); // Get the payment method ID
							$payment_title = $order->get_payment_method_title();
							$date_created  = $order->get_date_created(); // Get date created (WC_DateTime object)
							$date_modified = $order->get_date_modified();
							$order_total = $order->get_total();
							$credit_card_number = get_post_meta($row->ID, '_credit_card_number',true);
					?>
                    <tr>
              			<td><?php echo date('m/d/y',strtotime($date_created));?></td>
                        <td><?php echo $Originator ;?></td>
                        <td><?php echo $billing_city ;?></td>
                        <td><?php echo $billing_state ;?></td>
                        <td><?php echo $billing_postcode ;?></td>
                        <td><?php echo '$'.$order_total ;?></td>
                        <td><?php echo date('m/d/y',strtotime($date_modified));?></td>
                        <td><?php echo $credit_card_number;?></td>
                        </tr>
              <?php }?>
            </tbody>
          </table>
          <?php }?>
          <?php }else{?>
          <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
                <th scope="col" id="6036312b3586a" class="manage-column " style="padding:8px 5px !important;"><span>Date</span></th>
                <th scope="col" id="name" class="manage-column"><span>Originator</span></th>
                <th scope="col" id="6036312b3a9dc" class="manage-column column-6036312b3a9dc">City</th>
                <th scope="col" id="60e5c718d8654" class="manage-column column-60e5c718d8654">State</th>
                <th scope="col" id="6036312b3ad5c" class="manage-column column-6036312b3ad5c">Zip</th>
                <th scope="col" id="6039002027393" class="manage-column column-6039002027393">Payment</th>
                <th scope="col" id="603906d6d0106" class="manage-column column-603906d6d0106">Processed</th>
                <th scope="col" id="60390b3f8622a" class="manage-column column-60390b3f8622a">Last 4</th>
              </tr>
            </thead>
            <tbody id="the-list">
            <tr>
              			<td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
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
<!--
<form action="/wp-admin/admin-ajax.php" method="post">
  	<input type="hidden" name="action" value="tp_translation" />
  	<input type="hidden" name="ln0" value="es" />
  	<input type="hidden" name="sr" value="0" />
  	<input type="hidden" name="items" value="1" />
  	<input type="text" id="tk0" placeholder="Original" />
  	<input type="text" id="tr0" placeholder="trasnlate"/>
    <input type="button" value="trasnlate"  onclick="Translate();"/>
  </form>
  -->
<script type="text/javascript">
function Translate(){
	var tk0 = jQuery("#tk0").val();
	var tr0 = jQuery("#tr0").val();
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/wp-admin/admin-ajax.php',	
				type:'POST',
				data:{'action':'tp_translation','ln0':'es','sr':'0','items':'1','tk0':tk0,'tr0':tr0},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery("#tk0").val('');
					jQuery("#tr0").val('');
				}
				
		
				});
		
	
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

		$this->stats_obj = new Orders_List();
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
	SP_Plugin_Order::get_instance();
} );