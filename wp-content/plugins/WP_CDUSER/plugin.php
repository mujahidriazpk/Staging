<?php
/*
Plugin Name: WP_CDUSER
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CDUSER_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Client / Dentist', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Client / Dentist', 'sp' ), //plural name of the listed records
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
	public static function get_UsersFunc( $firstname,$lastname ) {

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
		$users = new WP_User_Query( array(
						'role__in' => array( 'seller', 'customer' ),
						'search'         => '*' . $firstname.' '.$lastname . '*',
						'search_columns' => array(
							'first_name',
							'last_name',
						),
					) );
 
		$getusers = $users->get_results();
		$result['no_users'] = $users->get_total();
		$result['user_data'] =$users->get_results();
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


class SP_Plugin_CDUSER {

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
			'Client / Dentist',
			'Client / Dentist',
			'shopadoc_admin_cap',
			'CDUSER',
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
  <h2>Client / Dentist</h2>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>-child/autosuggest/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>-child/autosuggest/css/autosuggest_inquisitor.css" type="text/css" />
  <style type="text/css">
  				#toplevel_page_admin-page-CDUSER a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-CDUSER a:after {
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
				th,td{font-size:16px !important;text-align:center !important;}
				.error,.notice{display:none;}
				.secLabel{
					font-size: 15px;
					font-weight: normal;
					margin-left:11px;
				}
			.tooltips, .mytooltip .tooltips, .password_tooltip {
				  background: white !important;
				  background: url(/wp-content/themes/dokan-child/icons8-info-26.png) no-repeat !important;
				  text-indent: -100000px !important;
				  width: 26px !important;
				  text-align: center !important;
				  float: left !important;
				  border-radius: 0 !important;
				  color: #fff;
				  font-weight: bold;
				  margin-left: 5px !important;
				  font-size: 17px !important;
				  height: auto !important;
				  line-height: 1.8;
				  margin-top: 19px !important;
				  margin-right: 5px;
				}
				.Zebra_Tooltip {
					z-index: 10000000001 !important;
					top:75px !important;
				}
				.Zebra_Tooltip .Zebra_Tooltip_Message{
					
					background-color:white !important;
				}
				.Zebra_Tooltip_Arrow.Zebra_Tooltip_Arrow_Bottom div{
					border-color:white transparent transparent !important;
				}
				.circle_red {
					height: 25px;
					  width: 25px;
					  background-color: red;
					  border-radius: 50%;
					  display: inline-block;
				}
				.circle_green {
					height: 25px;
					  width: 25px;
					  background-color: green;
					  border-radius: 50%;
					  display: inline-block;
				}
				.btn_info{
					margin-right:16.6% !important;
				}
			</style>
            <link rel='stylesheet' id='simple_tooltips_style-css'  href='https://woocommerce-642855-2866716.cloudwaysapps.com/wp-content/plugins/simple-tooltips/zebra_tooltips.css?ver=1659441620' type='text/css' media='all' />
            <script type='text/javascript' src='https://woocommerce-642855-2866716.cloudwaysapps.com/wp-content/plugins/simple-tooltips/zebra_tooltips.js?ver=1659441620' id='simple_tooltips_base-js'></script>
  <?php 
				if (isset($_POST["submit"])) {
					$sub = $_POST["submit"];
				
					if (isset($sub["save"])) {
						// save something;
					} elseif (isset($sub["export"])) {
						
						header("location:/export2csv_user.php?user_city=".$user_city."&user_state=".$user_state."&user_zip_code=".$user_zip_code."&mishaDateFrom=".$from."&mishaDateTo=".$to);
						exit;
					}
				}
			?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <form method="post" style="float:none;margin:0 auto;width:70%;padding:50px 0;">
            <?php $firstname = ( isset( $_REQUEST['firstname'] ) && $_REQUEST['firstname'] ) ? $_REQUEST['firstname'] : '';?>
            <?php $lastname = ( isset( $_REQUEST['lastname'] ) && $_REQUEST['lastname'] ) ? $_REQUEST['lastname'] : '';?>
            <div style="float:left;width:10%;padding:10px;text-align:right;">
            	<label style="font-size:14px;padding:10px 0;"><strong>Name</strong></label>
            </div>
            <div style="float:left;width:40%;padding:10px;">
            	<input type="search" name="firstname" id="firstname" value="<?php echo $firstname;?>" style="width:100%;"/>
                <label class="secLabel">First</label>
            </div>
            <div style="float:left;width:40%;padding:10px;">
            <input type="search" name="lastname" id="lastname" value="<?php echo $lastname;?>" style="width:100%;" />
            <label class="secLabel">Last</label>
            </div>
            <div style="float:left;width:10%;padding:10px;">
            	<input type="submit" name="submit" value="Search" class="btn btn-primary"/>
            </div>
          </form>
          <?php 
		  $item = CDUSER_List::get_UsersFunc($_REQUEST['firstname'],$_REQUEST['lastname']);
		  if((isset( $_REQUEST['firstname'] ) && $_REQUEST['firstname']!='') || isset( $_REQUEST['lastname'] ) && $_REQUEST['lastname']!=''){
			  global $US_state,$today_date_time;
			  $item = CDUSER_List::get_UsersFunc($_REQUEST['firstname'],$_REQUEST['lastname']);
			  if($item['no_users'] > 0){
			 ?>
          <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
                <th scope="col" align="center"><span>Name</span></th>
                <th scope="col" align="center"><span>Order #</span></th>
                <th scope="col" align="center"><span>Date Processed</span></th>
                <th scope="col" align="center">Auction #</th>
                <th scope="col" align="center">Payor</th>
                <th scope="col" align="center">Status</th>
              </tr>
            </thead>
            <tbody id="the-list">
            <?php foreach($item['user_data'] as $user){
				$userArray = get_user_by( 'id', $user->ID );
				// Get all customer orders
				$customer_orders = get_posts(array(
					'numberposts' => -1,
					'meta_key' => '_customer_user',
					'orderby' => 'date',
					'order' => 'DESC',
					'meta_value' => $user->ID,
					'post_type' => wc_get_order_types(),
					'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-processing'),
				));
			
				$Order_Array = array();
				foreach ($customer_orders as $customer_order) {
					$orderq = wc_get_order($customer_order);
					$order_ref =get_post_meta($orderq->get_id(), 'order_ref_#',true);
					$items = $orderq->get_items();
					foreach ( $items as $item ) {
						$Auction_id = $item->get_meta('Auction #');
					}
					$Order_Array[] = array(
						"order_ref" => $order_ref,
						"Auction_id" => $Auction_id,
						"ID" => $orderq->get_id(),
						"Value" => $orderq->get_total(),
						"Date" => $orderq->get_date_modified()->date_i18n('m/d/y'),
					);
					$Order_Array[0]['display_name'] = $userArray->display_name;
					$deactivate_CD =  get_user_meta($userArray->ID, 'deactivate_CD', true );
					if($userArray->roles[0]=='seller'){
						$Order_Array[0]['popup'] =  '<a onClick="openUserCD(\''. $userArray->ID.'\',\'client\',\''. $deactivate_CD.'\');" href="javascript:"><strong>C</strong></a>';
						$Order_Array[0]['color'] =$deactivate_CD;
						if($deactivate_CD=='Yes'){
							$Order_Array[0]['status'] =  '<span class="circle_red">&nbsp;</span>';
						}else{
							$Order_Array[0]['status'] =  '<span class="circle_green">&nbsp;</span>';
						}
					}
					if($userArray->roles[0]=='customer'){
						$Order_Array[0]['popup']  = '<a onClick="openUserCD(\''. $userArray->ID.'\',\'dentist\',\''. $deactivate_CD.'\');" href="javascript:"><strong>D</strong></a>';
						$Order_Array[0]['color'] =$deactivate_CD;
						if($deactivate_CD=='Yes'){
							$Order_Array[0]['status'] = '<span class="circle_red">&nbsp;</span>';
						}else{
							$Order_Array[0]['status'] = '<span class="circle_green">&nbsp;</span>';
						}
					}			
				}
				
			?>
            <?php if(!empty($Order_Array)){?>
            	<?php foreach($Order_Array as $order){ ?>
					<?php if($order['order_ref']!=""){?>
                          <!--<tr <?php if($order['color']=='Yes'){?>style="background-color: rgb(255, 255, 224);"<?php }?>>-->
                          <tr>
                                <td align="center"><?php echo $order['display_name'];?></td>
                                <td align="center"><a href="admin.php?page=orders&search_str=<?php echo $order['order_ref'];?>" title="<?php echo $order['order_ref'];?>" target="_new"><?php echo $order['order_ref'];?></a></td> 
                                <td align="center"><?php echo $order['Date'];?></td>
                                <td align="center"><?php if($order['Auction_id']!=""){?><a href="admin.php?page=auctions&search_str=<?php echo $order['Auction_id'];?>" title="<?php echo $order['Auction_id'];?>" target="_new"><?php echo $order['Auction_id'];?></a><?php }else{?>&mdash;<?php }?></td>
                                <td align="center"><?php echo $order['popup'];?></td>
                                <td align="center"><?php echo $order['status'];?></td>
                          </tr>
                      <?php }?>
                  <?php }?>
                  <?php }else{
					  $deactivate_CD =  get_user_meta($userArray->ID, 'deactivate_CD', true );
					  if($userArray->roles[0]=='seller'){
						  $popup = '<a onClick="openUserCD(\''. $userArray->ID.'\',\'client\',\''. $deactivate_CD.'\');" href="javascript:"><strong>C</strong></a>';
					  }
					  if($userArray->roles[0]=='customer'){
						  $popup = '<a onClick="openUserCD(\''. $userArray->ID.'\',\'dentist\',\''. $deactivate_CD.'\');" href="javascript:"><strong>D</strong></a>';
					  }
					if($deactivate_CD=='Yes'){
						$status =  '<span class="circle_red">&nbsp;</span>';
					}else{
						$status =  '<span class="circle_green">&nbsp;</span>';
					}
					  ?>
                        <tr >
                            <td align="center"><?php echo $userArray->display_name;?></td>
                            <td align="center">&mdash;</td> 
                            <td align="center">&mdash;</td>
                            <td align="center">&mdash;</td>
                            <td align="center"><?php echo $popup;?></td>
                            <td align="center"><?php echo $status;?></td>
                      </tr>
                  <?php }?>
              <?php }?>
            </tbody>
          </table>
          <?php }?>
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

		$this->stats_obj = new CDUSER_List();
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
	SP_Plugin_CDUSER::get_instance();
} );

