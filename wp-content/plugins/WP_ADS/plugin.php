<?php
/*
Plugin Name: WP_ADS
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ADS_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'ADS', 'sp' ), //singular name of the listed records
			'plural'   => __( 'ADS', 'sp' ), //plural name of the listed records
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
	 public static function shorten_title( $title,$lenght) {
			if(strlen($title) > $lenght){
				$newTitle = substr( $title, 0, $lenght ); // Only take the first 20 characters
				return $newTitle." &hellip;"; // Append the elipsis to the text (...) 
			}else{
				return $title;
			}
		}
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


class SP_Plugin_ADS {

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
			'ADS',
			'ADS',
			'shopadoc_admin_cap',
			'ADS',
			[ $this, 'plugin_settings_page' ]
		);
		//add_submenu_page( 'performance_auction', 'Auction #', 'Auction #','shopadoc_admin_cap', 'admin.php?page=auctions');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
			//echo do_shortcode("[ad_section2]");
			$current_date_option_label = "_".date('m_y');
			//$current_date_option_label = "_04_22";
			$firstDateOfNextMonth =strtotime('first day of next month') ;
			$next_date_option_label = "_".date('m_y', $firstDateOfNextMonth);
		?>
<link rel="stylesheet" href="https://getbootstrap.com/docs/4.2/dist/css/bootstrap.min.css" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="/wp-content/plugins/WP_ADS/js/select2.js"></script>
<?php /*?>
<select style="width:300px" id="source">
               <optgroup label="Alaskan/Hawaiian Time Zone">
                   <option value="AK">Alaska</option>
                   <option value="HI">Hawaii</option>
               </optgroup>
               <optgroup label="Pacific Time Zone">
                   <option value="CA">California</option>
                   <option value="NV">Nevada</option>
                   <option value="OR">Oregon</option>
                   <option value="WA">Washington</option>
               </optgroup>
               <optgroup label="Mountain Time Zone">
                   <option value="AZ">Arizona</option>
                   <option value="CO">Colorado</option>
                   <option value="ID">Idaho</option>
                   <option value="MT">Montana</option><option value="NE">Nebraska</option>
                   <option value="NM">New Mexico</option>
                   <option value="ND">North Dakota</option>
                   <option value="UT">Utah</option>
                   <option value="WY">Wyoming</option>
               </optgroup>
               <optgroup label="Central Time Zone">
                   <option value="AL">Alabama</option>
                   <option value="AR">Arkansas</option>
                   <option value="IL">Illinois</option>
                   <option value="IA">Iowa</option>
                   <option value="KS">Kansas</option>
                   <option value="KY">Kentucky</option>
                   <option value="LA">Louisiana</option>
                   <option value="MN">Minnesota</option>
                   <option value="MS">Mississippi</option>
                   <option value="MO">Missouri</option>
                   <option value="OK">Oklahoma</option>
                   <option value="SD">South Dakota</option>
                   <option value="TX">Texas</option>
                   <option value="TN">Tennessee</option>
                   <option value="WI">Wisconsin</option>
               </optgroup>
               <optgroup label="Eastern Time Zone">
                   <option value="CT">Connecticut</option>
                   <option value="DE">Delaware</option>
                   <option value="FL">Florida</option>
                   <option value="GA">Georgia</option>
                   <option value="IN">Indiana</option>
                   <option value="ME">Maine</option>
                   <option value="MD">Maryland</option>
                   <option value="MA">Massachusetts</option>
                   <option value="MI">Michigan</option>
                   <option value="NH">New Hampshire</option><option value="NJ">New Jersey</option>
                   <option value="NY">New York</option>
                   <option value="NC">North Carolina</option>
                   <option value="OH">Ohio</option>
                   <option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option>
                   <option value="VT">Vermont</option><option value="VA">Virginia</option>
                   <option value="WV">West Virginia</option>
               </optgroup>
              </select>
              <script type="text/javascript">
			 
    function format(state) {
        if (!state.id) return state.text; // optgroup
        return "<img class='flag' src='images/flags/" + state.id.toLowerCase() + ".png'/>" + state.text;
    }
    jQuery("#source").select2({
        formatResult: format,
        formatSelection: format,
        escapeMarkup: function(m) { return m; }
    });
	jQuery("#source").on("change", function(e) { alert(jQuery(this).val()); });
	
			  </script>*/
			  ?>
<div class="wrap">
  
 <div style="float:left;width:100%;"><h2 class="pull-left main_heading">ADS</h2> <a href="javascript:addUser('','Advertiser');" title="addUser" style="float:right" class="btn btn-primary">Add Company</a></p>
  <style type="text/css">
  			#toplevel_page_admin-page-ADS a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-ADS a:after {
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
				.secLabel{
					font-size: 15px;
					font-weight: normal;
				}
				.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons button, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons button{text-transform:none !important;}
			</style>
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
          <style type="text/css">
				  .form-table th{padding:4px 0 !important;}
				  .form-table td{padding:5px 10px !important; }
				  .wp-core-ui select{width:75%;}
				  .containerMain {
						border: solid 1px #000;
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
					.jconfirm-cell .row{
						background:none !important;
						border-bottom:none !important;
					}
					.rowMain{
						background: #000 !important;
						color:#fff !important;
					}
				 .containerMain .col{
							border-right: solid 1px #000;
							max-width: 100%;
							padding: 2px 0;
							text-align: center;
							position:relative;
							    display: flex;
							align-items: center;
							justify-content: center;
						}
						.popupView .col:nth-child(2){
							margin-left:10px;
						}
					.impression_col label{width:100%;float:left;}
					.detail_View .col{
						display:inline-block !important;
					}	
					.containerMain .row .col:last-child{
						border-right:none !important;
					}
					.rowMain .col{
						color:#fff;
						/*border-right: solid 1px #fff !important;*/
						text-transform:uppercase;
						margin-left:1px;
					}
					select{
						display:none !important;
					}
					.select2-container,.th-container,.view_main{width:100% !important;}
					.select2-dropdown--below{width:200px !important;}
					.select2-container--default .select2-selection--single .select2-selection__placeholder {
						color: #DB2D69;
						font-weight: bold;
					}
					#ui-datepicker-div{width:auto !important;}
					.select2-container .select2-selection--single .select2-selection__arrow{/*display:none !important;*/}
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
					.view_main a{
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
					.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow{
						z-index:1;
					}
					.detail_View {
						position: absolute;
						background: #fff;
						z-index: 10001;
						width: 510px;
						top: 40px;
						text-align: left;
						padding: 20px;
						display:none;
						box-shadow:0px 3px 25px #00000040;
						border-radius:5px;
					}
									.option_img{
							float:left;
							margin-right:5px;
						}
					.ad_details label {
						font-size: 13px;
						color: #000;
						margin: 0;
						font-weight: 500;
						margin-top: -3px;
						margin-right: 5px;
					}
					.ad_details .popup_val{color:#000;font-size:13px;font-weight:bold;}
					.ad_details img{width:100%;max-width:100%;}
					.ad_details .row,.ad_details .col{border:none;text-align:left;}
					.edit_link,.delete_link{font-size:11px;font-weight:bold;}
					.edit_link{color:#10C168;}
					.delete_link{color:#DB2D69;}
					.btn {
								font-size: 14px !important;
								font-weight: 400 !important;
								font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
							}
							.wc-wp-version-gte-53 .select2-container .select2-selection--single{border:none !important;/*box-shadow:none !important;*/}
							.number_col_head{width:55px;    display: flex;
												/* align-content: center; */
												justify-content: center;}
							.number_col{width:28px;    display: flex;
												/* align-content: center; */
												justify-content: center;}
							.select2-container--default .select2-selection--single .select2-selection__placeholder{background:#fff;}
							.select2-container--default .select2-selection--single{background-color:transparent !important;}
							.wc-wp-version-gte-53 .select2-dropdown--below {
									box-shadow: 0px 3px 6px #00000033 !important;
								}
								.main_heading{font-size:16px !important;font-weight:bold !important;line-height:33px !important;}
								.month-heading{background:#fff;margin-top:5px;padding:5px;font-size:14px !important;font-weight:bold !important;margin-left:-5px !important;}
								.sub_heading{font-size:14px !important;font-weight:bold !important;}
								.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow{z-index:1;}
								.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__rendered{background:#fff;}
								.popupClose{
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
								.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow{top:-12px;}
								.view_main.available a{color:#DB2D69 !important;}
						</style>
          <div style="float:left;width:100%;">
            <?php 
				global $wpdb;
				/*$query = "SELECT * FROM wp_posts where (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '% C%' or post_content = 'C' ) ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				$html1 = '';
				$args = array(
					'role'    => 'advanced_ads_user',
					'orderby' => 'user_nicename',
					'order'   => 'ASC'
				);
				$users = get_users( $args );
				foreach($results as $row){
					if($row->ID !=""){
						$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
						$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
						$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
						$src = $img_atts[0];
						$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'">'.$row->post_title.'</option>';
					}
				}*/
				$exclude_array = array(2199,2200,2201,2202,1877,1887,1897,1907,2199);
				$args = array(
					'role'    => 'advanced_ads_user',
					'orderby' => 'user_nicename',
					'order'   => 'ASC'
				);
				$users = get_users( $args );
				$html1 = '';
				foreach($users as $user){
					$query = "SELECT * FROM wp_posts where post_author = '".$user->ID."' and ID NOT IN ('".implode("','",$exclude_array)."') and (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '%C %' or post_excerpt = 'C' ) ORDER BY ID ASC";
					$results = $wpdb->get_results($query, OBJECT);
					if ($wpdb->num_rows > 0) {
						$ad_demo_company_name_group = get_user_meta($user->ID, 'ad_demo_company_name',true);
						$deactivate_advertiser = get_user_meta($user->ID, 'deactivate_advertiser',true);
						if($deactivate_advertiser=='Yes'){
							$ad_demo_company_name_group .= " (deactivated)";
						}
						$html1 .='<optgroup label="'.$ad_demo_company_name_group.'">';
						foreach($results as $row){
							if($row->ID !=""){
								$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
								if(strtotime(date('Y-m-d H:i:s')) < $advanced_ads_ad_options['expiry_date'] || $advanced_ads_ad_options['expiry_date'] == 0){
									$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
									$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
									$src = $img_atts[0];
									$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'" title="'.$ad_demo_company_name_group.'">'.str_replace("&nbsp;","-",$row->post_title).'</option>';
								}
							}
						}
						$html1 .='</optgroup>';
					}
				}
				$title_lenght = 33;
			?>
            
            <!--<h5>MONTH YEAR - <strong><?php echo strtoupper(date('F Y'));?></strong></h5>-->
            <h3 class="month-heading"><?php echo strtoupper(date('F Y'));?></h3>
            <div style="float:left;width:49.5%;margin-right:1%;">
              <h3 class="sub_heading">CLIENT</h3>
              <div class="containerMain">
                <div class="container">
                  <div class="row rowMain">
                    <div class="col col-md-auto"><span class="number_col_head">ROTATIONS</span></div>
                    <div class="col ">
                      <div class="th-container">Column A</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column B</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column C</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column D</div>
                    </div>
                  </div>
                  <?php for($i = 1;$i <= 10;$i++){ 
					$option_name1 = 'position'.$i.'_col1_client'.$current_date_option_label;
					$option_name2 = 'position'.$i.'_col2_client'.$current_date_option_label;
					$option_name3 = 'position'.$i.'_col3_client'.$current_date_option_label;
					$option_name4 = 'position'.$i.'_col4_client'.$current_date_option_label;
					
					$ad_user1 = get_post_meta(get_option($option_name1),"ad_user",true);
					$ad_demo_company_name1 = get_user_meta($ad_user1, 'ad_demo_company_name',true);
					$ad_user2 = get_post_meta(get_option($option_name2),"ad_user",true);
					$ad_demo_company_name2 = get_user_meta($ad_user2, 'ad_demo_company_name',true);
					$ad_user3 = get_post_meta(get_option($option_name3),"ad_user",true);
					$ad_demo_company_name3 = get_user_meta($ad_user3, 'ad_demo_company_name',true);
					$ad_user4 = get_post_meta(get_option($option_name4),"ad_user",true);
					$ad_demo_company_name4 = get_user_meta($ad_user4, 'ad_demo_company_name',true);
		
					//$ad_demo_company_name1_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name1)));
					$ad_demo_company_name1_title =get_the_title(get_option($option_name1));
					$ad_demo_company_name2_title =get_the_title(get_option($option_name2));
					$ad_demo_company_name3_title =get_the_title(get_option($option_name3));
					$ad_demo_company_name4_title =get_the_title(get_option($option_name4));
					
					$ad_demo_company_name1_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name1)));
					$ad_demo_company_name2_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name2)));
					$ad_demo_company_name3_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name3)));
					$ad_demo_company_name4_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name4)));
					
					/*$advanced_ads_ad_options1 = maybe_unserialize(get_post_meta(get_option($option_name1), 'advanced_ads_ad_options', true ));
					$attach_id1 = $advanced_ads_ad_options1['output']['image_id'] ;
					$img_atts1 = wp_get_attachment_image_src($attach_id1, 'thumbnail');
					$ADImageName1 = basename($img_atts1[0]);
					
					$advanced_ads_ad_options2 = maybe_unserialize(get_post_meta(get_option($option_name2), 'advanced_ads_ad_options', true ));
					$attach_id2 = $advanced_ads_ad_options2['output']['image_id'] ;
					$img_atts2 = wp_get_attachment_image_src($attach_id2, 'thumbnail');
					$ADImageName2 = basename($img_atts2[0]);
					
					$advanced_ads_ad_options3 = maybe_unserialize(get_post_meta(get_option($option_name3), 'advanced_ads_ad_options', true ));
					$attach_id3 = $advanced_ads_ad_options3['output']['image_id'] ;
					$img_atts3 = wp_get_attachment_image_src($attach_id3, 'thumbnail');
					$ADImageName3 = basename($img_atts3[0]);
					
					$advanced_ads_ad_options4 = maybe_unserialize(get_post_meta(get_option($option_name4), 'advanced_ads_ad_options', true ));
					$attach_id4 = $advanced_ads_ad_options4['output']['image_id'] ;
					$img_atts4 = wp_get_attachment_image_src($attach_id4, 'thumbnail');
					$ADImageName4 = basename($img_atts3[0]);*/
					
					$date_array1 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name1)))); 
					$ad_demo_company_name1_title = ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)."<span class='ad_expire'>".$date_array1[1].'</span>';
					$date_array2 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name2)))); 
					$ad_demo_company_name2_title = ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)."<span class='ad_expire'>".$date_array2[1].'</span>';
					$date_array3 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name3)))); 
					$ad_demo_company_name3_title = ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)."<span class='ad_expire'>".$date_array3[1].'</span>';
					$date_array4 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name4)))); 
					$ad_demo_company_name4_title = ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)."<span class='ad_expire'>".$date_array4[1].'</span>';
					
				?>
                  <div class="row">
                    <div class="col col-md-auto"><span class="number_col"><strong><?php echo $i?></strong></span></div>
                    <div class="col" id="<?php echo $option_name1."_main"?>"> <span id="<?php echo $option_name1;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)));?>">
                      <?php if(get_option($option_name1) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name1;?>','C','<?php echo get_option($option_name1);?>','<?php echo $i?>','A');" ><?php echo $ad_demo_company_name1_title;?></a></span><span id="<?php echo $option_name1;?>_detail_popup" class="detail_View">&nbsp; 
                      <script type="text/javascript">
						//jQuery("#<?php echo $option_name1;?>_open").on("click", function () {jQuery("#<?php echo $option_name1;?>").select2("open"); });
                     	</script>
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name1."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name1."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name1."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name1."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name1."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#<?php echo $option_name1."_main"?> #select2-<?php echo $option_name1;?>-container').on( "click", function() {
								jQuery('#<?php echo $option_name1;?>').select2('open');
							});
						});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name1;?>" id="<?php echo $option_name1;?>" onchange="">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name1;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name1;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name2."_main"?>"> <span id="<?php echo $option_name2;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)));?>">
                      <?php if(get_option($option_name2) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name2;?>','C','<?php echo get_option($option_name2);?>','<?php echo $i?>','B');" ><?php echo $ad_demo_company_name2_title;?></a></span><span id="<?php echo $option_name2;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name2."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name2."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name2."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name2."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name2."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#<?php echo $option_name2."_main"?> #select2-<?php echo $option_name2;?>-container').on( "click", function() {
							  	jQuery('#<?php echo $option_name2;?>').select2('open');
							});
						});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name2;?>" id="<?php echo $option_name2;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name2;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name2;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name3."_main"?>"> <span id="<?php echo $option_name3;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)));?>">
                      <?php if(get_option($option_name3) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name3;?>','C','<?php echo get_option($option_name3);?>','<?php echo $i?>','C');" ><?php echo $ad_demo_company_name3_title;?></a></span><span id="<?php echo $option_name3;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name3."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name3."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name3."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name3."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name3."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name3."_main"?> #select2-<?php echo $option_name3;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name3;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name3;?>" id="<?php echo $option_name3;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name3;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name3;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name4."_main"?>"> <span id="<?php echo $option_name4;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)));?>">
                      <?php if(get_option($option_name4) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name4;?>','C','<?php echo get_option($option_name4);?>','<?php echo $i?>','D');" ><?php echo $ad_demo_company_name4_title;?></a></span><span id="<?php echo $option_name4;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name4."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name4."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name4."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name4."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name4."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name4."_main"?> #select2-<?php echo $option_name4;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name4;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name4;?>" id="<?php echo $option_name4;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name4;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name4;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                  </div>
                  <script type="text/javascript">
						
						<?php if($option_name1==""){?>
							document.getElementById('<?php echo $option_name1;?>').value = '<?php echo get_option($option_name1);?>' ;
						<?php }?>
						<?php if($option_name2==""){?>
							document.getElementById('<?php echo $option_name2;?>').value = '<?php echo get_option($option_name2);?>' ;
						<?php }?>
						<?php if($option_name3==""){?>
							document.getElementById('<?php echo $option_name3;?>').value = '<?php echo get_option($option_name3);?>' ;
						<?php }?>
						<?php if($option_name4==""){?>
							document.getElementById('<?php echo $option_name4;?>').value = '<?php echo get_option($option_name4);?>' ;
						<?php }?>
						
						function formatState (state) {
						  if (!state.id || state.text =='+ Add Creative' || state.text =='Vacate') {
							  if(state.text =='+ Add Creative' || state.text =='Vacate'){
								 	  var state = jQuery('<strong class="blue">' + state.text + '</strong>');
						  			 return state;
								 }else{
										return state.text;
								}
						  }
						  var state = jQuery('<span><span class="option_img"><img src="'+state.element.dataset.select2Id+ '" class="img-flag" width="25" height="25" /></span> <span class="option_txt">' + state.text.replace("-","<br />") + '</span></span>');
						  return state;
						}
						function formatStateSelect (state) {
						  if (state.text=='- AVAILABLE -') {
						  			return '- AVAILABLE -'
							}else{
								//console.log(state);
									var state = jQuery('<span id="'+state.id+'_view" class="view_main_select">&nbsp;<a href="">' + state.title + '</a></span>');
						  			return state;
							}
						}
						/*jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,							
							placeholder: '- AVAILABLE -',
						});*/
						jQuery("#<?php echo $option_name1;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,							
							placeholder: '- AVAILABLE -',
						});
						jQuery("#<?php echo $option_name2;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,							
							placeholder: '- AVAILABLE -',
						});
						jQuery("#<?php echo $option_name3;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,							
							placeholder: '- AVAILABLE -',
						});
						jQuery("#<?php echo $option_name4;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,							
							placeholder: '- AVAILABLE -',
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:open', function (e) {
							jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").val(null).trigger('change');
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:select', function (e) {
							var data = e.params.data;
							if(data.id=='add'){
								AdPopup(jQuery(this).attr("id"),'C','');
							}else if(data.id=='Vacate'){
								VacatePopup(jQuery(this).attr("id"),'C','')
							}else{
								AdSelected(data.id,jQuery(this).attr("id"),'C');
							}
						});
					
						/*jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on("change", function(e) { 
							if(jQuery(this).val()=='add'){
								AdPopup(jQuery(this).attr("id"),'C','')
							}else{
								alert(jQuery(this).val());
								alert(jQuery("#"+jQuery(this).attr("id")).val());
								//AdSelected(jQuery(this).val(),jQuery(this).attr("id"),'C');
							}
					 	});*/
				</script>
                  <?php 
				
				}?>
                </div>
              </div>
            </div>
            <?php 
					global $wpdb;
					/*$query = "SELECT * FROM wp_posts where (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '% D%' or post_content = 'D' )  ORDER BY ID ASC";
					$results = $wpdb->get_results($query, OBJECT);
					$html1 = '';
					foreach($results as $row){
						if($row->ID !=""){
							$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
							$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
							$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
							$src = $img_atts[0];
							$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'">'.$row->post_title.'</option>';
						}
					}*/
				$html1 = '';
				foreach($users as $user){
					$query = "SELECT * FROM wp_posts where post_author = '".$user->ID."' and ID NOT IN ('".implode("','",$exclude_array)."') and (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '%D %' or post_excerpt = 'D' ) ORDER BY ID ASC";
					$results = $wpdb->get_results($query, OBJECT);
					if ($wpdb->num_rows > 0) {
						$ad_demo_company_name_group = get_user_meta($user->ID, 'ad_demo_company_name',true);
						$html1 .='<optgroup label="'.$ad_demo_company_name_group.'">';
						foreach($results as $row){
							if($row->ID !=""){
								$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
								if(strtotime(date('Y-m-d H:i:s')) < $advanced_ads_ad_options['expiry_date'] || $advanced_ads_ad_options['expiry_date'] == 0){
									$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
									$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
									$src = $img_atts[0];
									$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'" title="'.$ad_demo_company_name_group.'">'.str_replace("&nbsp;","-",$row->post_title).'</option>';
								}
							}
						}
						$html1 .='</optgroup>';
					}
				}
				
				?>
            <div style="float:left;width:49.5%;">
              <h3 class="sub_heading">DENTIST</h3>
              <div class="containerMain">
                <div class="container">
                  <div class="row rowMain">
                    <div class="col col-md-auto"><span class="number_col_head">ROTATIONS</span></div>
                    <div class="col ">
                      <div class="th-container">Column A</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column B</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column C</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column D</div>
                    </div>
                  </div>
                  <?php for($i = 1;$i <= 10;$i++){
                        $option_name1 = 'position'.$i.'_col1_dentist'.$current_date_option_label;
                        $option_name2 = 'position'.$i.'_col2_dentist'.$current_date_option_label;
                        $option_name3 = 'position'.$i.'_col3_dentist'.$current_date_option_label;
                        $option_name4 = 'position'.$i.'_col4_dentist'.$current_date_option_label;
						
					$ad_user1 = get_post_meta(get_option($option_name1),"ad_user",true);
					$ad_demo_company_name1 = get_user_meta($ad_user1, 'ad_demo_company_name',true);
					$ad_user2 = get_post_meta(get_option($option_name2),"ad_user",true);
					$ad_demo_company_name2 = get_user_meta($ad_user2, 'ad_demo_company_name',true);
					$ad_user3 = get_post_meta(get_option($option_name3),"ad_user",true);
					$ad_demo_company_name3 = get_user_meta($ad_user3, 'ad_demo_company_name',true);
					$ad_user4 = get_post_meta(get_option($option_name4),"ad_user",true);
					$ad_demo_company_name4 = get_user_meta($ad_user4, 'ad_demo_company_name',true);
					
					$ad_demo_company_name1_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name1)));
					$ad_demo_company_name2_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name2)));
					$ad_demo_company_name3_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name3)));
					$ad_demo_company_name4_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name4)));
					
					$date_array1 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name1)))); 
					$ad_demo_company_name1_title = ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)."<span class='ad_expire'>".$date_array1[1].'</span>';
					$date_array2 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name2)))); 
					$ad_demo_company_name2_title = ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)."<span class='ad_expire'>".$date_array2[1].'</span>';
					$date_array3 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name3)))); 
					$ad_demo_company_name3_title = ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)."<span class='ad_expire'>".$date_array3[1].'</span>';
					$date_array4 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name4)))); 
					$ad_demo_company_name4_title = ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)."<span class='ad_expire'>".$date_array4[1].'</span>';
                    ?>
                  <div class="row">
                    <div class="col col-md-auto"><span class="number_col"><strong><?php echo $i?></strong></span></div>
                    <div class="col" id="<?php echo $option_name1."_main"?>"> <span id="<?php echo $option_name1;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)));?>">
                      <?php if(get_option($option_name1) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name1;?>','D','<?php echo get_option($option_name1);?>','<?php echo $i?>','A');"><?php echo $ad_demo_company_name1_title;?></a></span><span id="<?php echo $option_name1;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name1."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name1."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name1."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name1."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name1."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name1."_main"?> #select2-<?php echo $option_name1;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name1;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name1;?>" id="<?php echo $option_name1;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name1;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name1;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name2."_main"?>"> <span id="<?php echo $option_name2;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)));?>">
                      <?php if(get_option($option_name2) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name2;?>','D','<?php echo get_option($option_name2);?>','<?php echo $i?>','B');"><?php echo $ad_demo_company_name2_title;?></a></span><span id="<?php echo $option_name2;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name2."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name2."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name2."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name2."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name2."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name2."_main"?> #select2-<?php echo $option_name2;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name2;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name2;?>" id="<?php echo $option_name2;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name2;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name2;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name3."_main"?>"> <span id="<?php echo $option_name3;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)));?>">
                      <?php if(get_option($option_name3) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name3;?>','D','<?php echo get_option($option_name3);?>','<?php echo $i?>','C');"><?php echo $ad_demo_company_name3_title;?></a></span><span id="<?php echo $option_name3;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name3."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name3."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name3."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name3."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name3."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name3."_main"?> #select2-<?php echo $option_name3;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name3;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name3;?>" id="<?php echo $option_name3;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name3;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name3;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name4."_main"?>"> <span id="<?php echo $option_name4;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)));?>">
                      <?php if(get_option($option_name4) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name4;?>','D','<?php echo get_option($option_name4);?>','<?php echo $i?>','D');"><?php echo $ad_demo_company_name4_title;?></a></span><span id="<?php echo $option_name4;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name4."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name4."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name4."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name4."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name4."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name4."_main"?> #select2-<?php echo $option_name4;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name4;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name4;?>" id="<?php echo $option_name4;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name4;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name4;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                  </div>
                  <script type="text/javascript">
                           
                            <?php if($option_name1==""){?>
							document.getElementById('<?php echo $option_name1;?>').value = '<?php echo get_option($option_name1);?>' ;
						<?php }?>
						<?php if($option_name2==""){?>
							document.getElementById('<?php echo $option_name2;?>').value = '<?php echo get_option($option_name2);?>' ;
						<?php }?>
						<?php if($option_name3==""){?>
							document.getElementById('<?php echo $option_name3;?>').value = '<?php echo get_option($option_name3);?>' ;
						<?php }?>
						<?php if($option_name4==""){?>
							document.getElementById('<?php echo $option_name4;?>').value = '<?php echo get_option($option_name4);?>' ;
						<?php }?>
					
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,
							//formatSelection: format,
							placeholder: '- AVAILABLE -',
							//escapeMarkup: function(m) { return m; }
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:open', function (e) {
							jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").val(null).trigger('change');
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:select', function (e) {
							var data = e.params.data;
							if(data.id=='add'){
								AdPopup(jQuery(this).attr("id"),'D','');
							}else if(data.id=='Vacate'){
								VacatePopup(jQuery(this).attr("id"),'D','');
							}else{
								AdSelected(data.id,jQuery(this).attr("id"),'D');
							}
						});
                    </script>
                  <?php }?>
                </div>
              </div>
            </div>
          </div>
          
          <!---------------------Next Month ADS------------------------------------------------------>
          <div style="float:left;width:100%;padding-top:20px;">
            <?php 
				global $wpdb;
				/*$query = "SELECT * FROM wp_posts where (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and (post_title like '% C%' or post_content = 'C' )  ORDER BY ID ASC";
				$results = $wpdb->get_results($query, OBJECT);
				$html1 = '';
				foreach($results as $row){
					if($row->ID !=""){
						$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
						$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
						$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
						$src = $img_atts[0];
						$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'">'.$row->post_title.'</option>';
					}
				}*/
				$html1 = '';
				foreach($users as $user){
					$query = "SELECT * FROM wp_posts where post_author = '".$user->ID."' and ID NOT IN ('".implode("','",$exclude_array)."') and  (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '%C %' or post_excerpt = 'C' ) ORDER BY ID ASC";
					$results = $wpdb->get_results($query, OBJECT);
					if ($wpdb->num_rows > 0) {
						$ad_demo_company_name_group = get_user_meta($user->ID, 'ad_demo_company_name',true);
						$html1 .='<optgroup label="'.$ad_demo_company_name_group.'">';
						foreach($results as $row){
							if($row->ID !=""){
								$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
								if(strtotime(date('Y-m-d H:i:s')) < $advanced_ads_ad_options['expiry_date'] || $advanced_ads_ad_options['expiry_date'] == 0){
									$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
									$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
									$src = $img_atts[0];
									$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'" title="'.$ad_demo_company_name_group.'">'.str_replace("&nbsp;","-",$row->post_title).'</option>';
								}
							}
						}
						$html1 .='</optgroup>';
					}
				}
				
			?>
            <h3 class="month-heading"><?php echo strtoupper(date('F Y',$firstDateOfNextMonth));?></h3>
            <div style="float:left;width:49.5%;margin-right:1%;">
              <h3 class="sub_heading">CLIENT</h3>
              <div class="containerMain">
                <div class="container">
                  <div class="row rowMain">
                    <div class="col col-md-auto"><span class="number_col_head">ROTATIONS</span></div>
                    <div class="col ">
                      <div class="th-container">Column A</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column B</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column C</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column D</div>
                    </div>
                  </div>
                  <?php for($i = 1;$i <= 10;$i++){ 
					$option_name1 = 'position'.$i.'_col1_client'.$next_date_option_label;
					$option_name2 = 'position'.$i.'_col2_client'.$next_date_option_label;
					$option_name3 = 'position'.$i.'_col3_client'.$next_date_option_label;
					$option_name4 = 'position'.$i.'_col4_client'.$next_date_option_label;
					//$value1 = get_option('position'.$i.'_col1_client');
					
					$ad_user1 = get_post_meta(get_option($option_name1),"ad_user",true);
					$ad_demo_company_name1 = get_user_meta($ad_user1, 'ad_demo_company_name',true);
					$ad_user2 = get_post_meta(get_option($option_name2),"ad_user",true);
					$ad_demo_company_name2 = get_user_meta($ad_user2, 'ad_demo_company_name',true);
					$ad_user3 = get_post_meta(get_option($option_name3),"ad_user",true);
					$ad_demo_company_name3 = get_user_meta($ad_user3, 'ad_demo_company_name',true);
					$ad_user4 = get_post_meta(get_option($option_name4),"ad_user",true);
					$ad_demo_company_name4 = get_user_meta($ad_user4, 'ad_demo_company_name',true);
					
					$ad_demo_company_name1_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name1)));
					$ad_demo_company_name2_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name2)));
					$ad_demo_company_name3_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name3)));
					$ad_demo_company_name4_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name4)));
					
					$date_array1 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name1)))); 
					$ad_demo_company_name1_title = ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)."<span class='ad_expire'>".$date_array1[1].'</span>';
					$date_array2 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name2)))); 
					$ad_demo_company_name2_title = ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)."<span class='ad_expire'>".$date_array2[1].'</span>';
					$date_array3 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name3)))); 
					$ad_demo_company_name3_title = ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)."<span class='ad_expire'>".$date_array3[1].'</span>';
					$date_array4 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name4)))); 
					$ad_demo_company_name4_title = ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)."<span class='ad_expire'>".$date_array4[1].'</span>';
				?>
                  <div class="row">
                    <div class="col col-md-auto"><span class="number_col"><strong><?php echo $i?></strong></span></div>
                    <div class="col" id="<?php echo $option_name1."_main"?>"> <span id="<?php echo $option_name1;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)));?>">
                      <?php if(get_option($option_name1) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name1;?>','C','<?php echo get_option($option_name1);?>','<?php echo $i?>','A');"><?php echo $ad_demo_company_name1_title;?></a></span><span id="<?php echo $option_name1;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name1."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name1."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name1."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name1."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name1."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name1."_main"?> #select2-<?php echo $option_name1;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name1;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name1;?>" id="<?php echo $option_name1;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name1;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name1;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name2."_main"?>"> <span id="<?php echo $option_name2;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)));?>">
                      <?php if(get_option($option_name2) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name2;?>','C','<?php echo get_option($option_name2);?>','<?php echo $i?>','B');"><?php echo $ad_demo_company_name2_title;?></a></span><span id="<?php echo $option_name2;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name2."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name2."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name2."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name2."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name2."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name2."_main"?> #select2-<?php echo $option_name2;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name2;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name2;?>" id="<?php echo $option_name2;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name2;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name2;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name3."_main"?>"> <span id="<?php echo $option_name3;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)));?>">
                      <?php if(get_option($option_name3) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name3;?>','C','<?php echo get_option($option_name3);?>','<?php echo $i?>','C');"><?php echo $ad_demo_company_name3_title;?></a></span><span id="<?php echo $option_name3;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name3."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name3."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name3."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name3."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name3."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name3."_main"?> #select2-<?php echo $option_name3;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name3;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name3;?>" id="<?php echo $option_name3;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name3;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name3;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name4."_main"?>"> <span id="<?php echo $option_name4;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)));?>">
                      <?php if(get_option($option_name4) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name4;?>','C','<?php echo get_option($option_name4);?>','<?php echo $i?>','D');"><?php echo $ad_demo_company_name4_title?></a></span><span id="<?php echo $option_name4;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name4."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name4."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name4."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name4."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name4."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name4."_main"?> #select2-<?php echo $option_name4;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name4;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name4;?>" id="<?php echo $option_name4;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name4;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name4;?>_select','C');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                  </div>
                  <?php
							$ids = array(); 
							if(get_option($option_name1) ==""){
								array_push($ids,"#".$option_name1);
							}
							if(get_option($option_name2) ==""){
								array_push($ids,"#".$option_name2);
							}
							if(get_option($option_name3) ==""){
								array_push($ids,"#".$option_name3);
							}
							if(get_option($option_name4) ==""){
								array_push($ids,"#".$option_name4);
							}
							$ids_str = implode(",",$ids);
						?>
                  <script type="text/javascript">
						
						<?php if($option_name1==""){?>
							document.getElementById('<?php echo $option_name1;?>').value = '<?php echo get_option($option_name1);?>' ;
						<?php }?>
						<?php if($option_name2==""){?>
							document.getElementById('<?php echo $option_name2;?>').value = '<?php echo get_option($option_name2);?>' ;
						<?php }?>
						<?php if($option_name3==""){?>
							document.getElementById('<?php echo $option_name3;?>').value = '<?php echo get_option($option_name3);?>' ;
						<?php }?>
						<?php if($option_name4==""){?>
							document.getElementById('<?php echo $option_name4;?>').value = '<?php echo get_option($option_name4);?>' ;
						<?php }?>
						
						
						var example = jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,
							//formatSelection: format,
							placeholder: '- AVAILABLE -',
							//escapeMarkup: function(m) { return m; }
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:open', function (e) {
							jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").val(null).trigger('change');
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:select', function (e) {
							var data = e.params.data;
							if(data.id=='add'){
								AdPopup(jQuery(this).attr("id"),'C','');
							}else if(data.id=='Vacate'){
								VacatePopup(jQuery(this).attr("id"),'C','');
							}else{
								AdSelected(data.id,jQuery(this).attr("id"),'C');
							}
							
						});
	
				</script>
                  <?php 
				
				}?>
                </div>
              </div>
            </div>
            <?php 
					global $wpdb;
					/*$query = "SELECT * FROM wp_posts where (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and (post_title like '% D%' or post_content = 'D' )  ORDER BY ID ASC";
					$results = $wpdb->get_results($query, OBJECT);
					$html1 = '';
					foreach($results as $row){
						if($row->ID !=""){
							$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
							$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
							$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
							$src = $img_atts[0];
							$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'">'.$row->post_title.'</option>';
						}
					}*/
				$html1 = '';
				foreach($users as $user){
					$query = "SELECT * FROM wp_posts where post_author = '".$user->ID."' and ID NOT IN ('".implode("','",$exclude_array)."') and  (post_status = 'publish' or post_status = 'future') and post_type = 'advanced_ads' and ( post_title like '%D %' or post_excerpt = 'D' ) ORDER BY ID ASC";
					$results = $wpdb->get_results($query, OBJECT);
					if ($wpdb->num_rows > 0) {
						$ad_demo_company_name_group = get_user_meta($user->ID, 'ad_demo_company_name',true);
						$html1 .='<optgroup label="'.$ad_demo_company_name_group.'">';
						foreach($results as $row){
							if($row->ID !=""){
								$advanced_ads_ad_options = maybe_unserialize(get_post_meta($row->ID, 'advanced_ads_ad_options', true ));
								if(strtotime(date('Y-m-d H:i:s')) < $advanced_ads_ad_options['expiry_date'] || $advanced_ads_ad_options['expiry_date'] == 0){
									$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
									$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
									$src = $img_atts[0];
									$html1 .= '<option value="'.$row->ID.'" data-select2-id="'.$src.'" title="'.$ad_demo_company_name_group.'">'.str_replace("&nbsp;","-",$row->post_title).'</option>';
								}
							}
						}
						$html1 .='</optgroup>';
					}
				}
				
				?>
            <div style="float:left;width:49.5%;">
              <h3 class="sub_heading">DENTIST</h3>
              <div class="containerMain">
                <div class="container">
                  <div class="row rowMain">
                    <div class="col col-md-auto"><span class="number_col_head">ROTATIONS</span></div>
                    <div class="col ">
                      <div class="th-container">Column A</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column B</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column C</div>
                    </div>
                    <div class="col ">
                      <div class="th-container">Column D</div>
                    </div>
                  </div>
                  <?php for($i = 1;$i <= 10;$i++){
                        $option_name1 = 'position'.$i.'_col1_dentist'.$next_date_option_label;
                        $option_name2 = 'position'.$i.'_col2_dentist'.$next_date_option_label;
                        $option_name3 = 'position'.$i.'_col3_dentist'.$next_date_option_label;
                        $option_name4 = 'position'.$i.'_col4_dentist'.$next_date_option_label;
						
						$ad_user1 = get_post_meta(get_option($option_name1),"ad_user",true);
					$ad_demo_company_name1 = get_user_meta($ad_user1, 'ad_demo_company_name',true);
					$ad_user2 = get_post_meta(get_option($option_name2),"ad_user",true);
					$ad_demo_company_name2 = get_user_meta($ad_user2, 'ad_demo_company_name',true);
					$ad_user3 = get_post_meta(get_option($option_name3),"ad_user",true);
					$ad_demo_company_name3 = get_user_meta($ad_user3, 'ad_demo_company_name',true);
					$ad_user4 = get_post_meta(get_option($option_name4),"ad_user",true);
					$ad_demo_company_name4 = get_user_meta($ad_user4, 'ad_demo_company_name',true);
					
					$ad_demo_company_name1_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name1)));
					$ad_demo_company_name2_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name2)));
					$ad_demo_company_name3_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name3)));
					$ad_demo_company_name4_title = str_replace("&nbsp;","<br />",get_the_title(get_option($option_name4)));
					
					$date_array1 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name1)))); 
					$ad_demo_company_name1_title = ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)."<span class='ad_expire'>".$date_array1[1].'</span>';
					$date_array2 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name2)))); 
					$ad_demo_company_name2_title = ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)."<span class='ad_expire'>".$date_array2[1].'</span>';
					$date_array3 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name3)))); 
					$ad_demo_company_name3_title = ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)."<span class='ad_expire'>".$date_array3[1].'</span>';
					$date_array4 = explode(" – ",html_entity_decode(get_the_title(get_option($option_name4)))); 
					$ad_demo_company_name4_title = ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)."<span class='ad_expire'>".$date_array4[1].'</span>';
                    ?>
                  <div class="row">
                    <div class="col col-md-auto"><span class="number_col"><strong><?php echo $i?></strong></span></div>
                    <div class="col" id="<?php echo $option_name1."_main"?>"> <span id="<?php echo $option_name1;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name1,$title_lenght)));?>">
                      <?php if(get_option($option_name1) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name1;?>','D','<?php echo get_option($option_name1);?>','<?php echo $i?>','A');"><?php echo $ad_demo_company_name1_title;?></a></span><span id="<?php echo $option_name1;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name1."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name1."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name1."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name1."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name1."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name1."_main"?> #select2-<?php echo $option_name1;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name1;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name1;?>" id="<?php echo $option_name1;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name1;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name1;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name2."_main"?>"> <span id="<?php echo $option_name2;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name2,$title_lenght)));?>">
                      <?php if(get_option($option_name2) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name2;?>','D','<?php echo get_option($option_name2);?>','<?php echo $i?>','B');"><?php echo $ad_demo_company_name2_title;?></a></span><span id="<?php echo $option_name2;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name2."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name2."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name2."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name2."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name2."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name2."_main"?> #select2-<?php echo $option_name2;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name2;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name2;?>" id="<?php echo $option_name2;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name2;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name2;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name3."_main"?>"> <span id="<?php echo $option_name3;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name3,$title_lenght)));?>">
                      <?php if(get_option($option_name3) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name3;?>','D','<?php echo get_option($option_name3);?>','<?php echo $i?>','C');"><?php echo $ad_demo_company_name3_title;?></a></span><span id="<?php echo $option_name3;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name3."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name3."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name3."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name3."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name3."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name3."_main"?> #select2-<?php echo $option_name3;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name3;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name3;?>" id="<?php echo $option_name3;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name3;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name3;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                    <div class="col" id="<?php echo $option_name4."_main"?>"> <span id="<?php echo $option_name4;?>_view" class="view_main <?php echo strtolower(str_replace(' ','_',ADS_List::shorten_title($ad_demo_company_name4,$title_lenght)));?>">
                      <?php if(get_option($option_name4) !=""){?>
                      <a href="javascript:detailPopup('<?php echo $option_name4;?>','D','<?php echo get_option($option_name4);?>','<?php echo $i?>','D');"><?php echo $ad_demo_company_name4_title;?></a></span><span id="<?php echo $option_name4;?>_detail_popup" class="detail_View">&nbsp;
                      </span>
					<?php }else{?>
                    	<style type="text/css">
						#<?php echo $option_name4."_main"?> .select2-selection__arrow{display:none !important;}
						#<?php echo $option_name4."_main"?> .select2-selection__placeholder{font-size:1.4em;}
						#<?php echo $option_name4."_main"?> .select2-container{height: 100%;}
						#<?php echo $option_name4."_main"?> .selection{height: 100%;width:100%;float:left;}
						#<?php echo $option_name4."_main"?> .select2-selection--single{height: 100%;display: flex;align-items: center; justify-content: center;}
						</style>
                        <script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('#<?php echo $option_name4."_main"?> #select2-<?php echo $option_name4;?>-container').on( "click", function() {
									jQuery('#<?php echo $option_name4;?>').select2('open');
								});
							});
						</script>
                    <?php }?>
                      <select name="<?php echo $option_name4;?>" id="<?php echo $option_name4;?>">
                        <option value="">- AVAILABLE -</option>
						<option value="Vacate">Vacate</option>
                        <option value="add">+ Add Creative</option>
                        <?php echo $html1;?>
                      </select>
                      <?php /*?><span  id="<?php echo $option_name4;?>_select" class="hide"><a href="javascript:AdPopup('<?php echo $option_name4;?>_select','D');">- AVAILABLE -</a></span><?php */?>
                      <?php /*}*/?>
                    </div>
                  </div>
                  <script type="text/javascript">
                        
                          <?php if($option_name1==""){?>
							document.getElementById('<?php echo $option_name1;?>').value = '<?php echo get_option($option_name1);?>' ;
						<?php }?>
						<?php if($option_name2==""){?>
							document.getElementById('<?php echo $option_name2;?>').value = '<?php echo get_option($option_name2);?>' ;
						<?php }?>
						<?php if($option_name3==""){?>
							document.getElementById('<?php echo $option_name3;?>').value = '<?php echo get_option($option_name3);?>' ;
						<?php }?>
						<?php if($option_name4==""){?>
							document.getElementById('<?php echo $option_name4;?>').value = '<?php echo get_option($option_name4);?>' ;
						<?php }?>
							
					
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").select2({
							templateResult: formatState,
							templateSelection: formatStateSelect,
							//formatSelection: format,
							placeholder: '- AVAILABLE -',
							//escapeMarkup: function(m) { return m; }
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:open', function (e) {
							jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").val(null).trigger('change');
						});
						jQuery("#<?php echo $option_name1;?>,#<?php echo $option_name2;?>,#<?php echo $option_name3;?>,#<?php echo $option_name4;?>").on('select2:select', function (e) {
							var data = e.params.data;
							if(data.id=='add'){
								AdPopup(jQuery(this).attr("id"),'D','');
							}else if(data.id=='Vacate'){
								VacatePopup(jQuery(this).attr("id"),'D','');
							}else{
								AdSelected(data.id,jQuery(this).attr("id"),'D');
							}
						});
                    </script>
                  <?php }?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
.jconfirm-content-pane{height:auto !important;/*max-height:100% !important;*/}
@media print {
    .myDivToPrint {
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
	.ad_details .popup_val,.ad_details label,.edit_link, .delete_link,p{
		font-size: 18px !important; 
        line-height: 27px !important;
	}	
}
</style>
<script type="text/javascript">
function SelectAdType(id,value){
	jQuery(".jconfirm-content-pane").css("height","auto");
	jQuery(".jconfirm-content-pane").css("max-height","auto");
	if(value=='plain'){
		jQuery("#ad_code_container").show();
		jQuery("#ad_image_container").hide();
	}else{
		jQuery("#ad_code_container").hide();
		jQuery("#ad_image_container").show();
	}
}
function load_image(id,ext){
		var control = document.getElementById(id);
		var files = control.files;
		for (var i = 0; i < files.length; i++) {
			if(files[i].type.search("image") >= 0){
				 var _URL = window.URL || window.webkitURL;
				img = new Image();
				var imgwidth = 0;
				var imgheight = 0;
				var maxwidth = 640;
				var maxheight = 640;
				img.src = _URL.createObjectURL(files[i]);
				//alert(img.src);
				jQuery("#ad_image_src").show();
				jQuery("#ad_image_src").attr("src",img.src);
				img.onload = function() {
					imgwidth = this.width;
					imgheight = this.height;
					
					/*alert(imgwidth);
					alert(imgheight);*/
				}
				//alert("Filename: " + files[i].name);
				//alert("Type: " + files[i].type);
				//alert("Size: " + files[i].size + " bytes");
				return;
			}
		}
		alert("upload only Image format");
		jQuery("#ad_image_src").hide();
		document.getElementById(id).value='';

		document.getElementById(id).focus();

		return;

		

	}

	function validateExtension(v){

		var allowedExtensions = new Array("jpg","JPG","jpeg","JPEG","gif","GIF","png","PNG","pdf");

		for(var ct=0;ct<allowedExtensions.length;ct++){

			sample = v.lastIndexOf(allowedExtensions[ct]);

			if(sample != -1){return true;}

		}

		return false;

	}
	function AdSelected(val,id,type){
		 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'saveAdPosition','ad_id':val,'postion_name':id,'type':type},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
							const myArr = data.split("##");
							jQuery('#'+id).html(myArr[0]);
							jQuery('#'+id+"_view").html(myArr[1]);
						
					}
				});
	}
	var t;
	function refreshStats(id,selected_ad) {
		 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getStats','ad_id':id,'selected_ad':selected_ad},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					//jQuery("#"+id+"_detail_popup").html(data).show();
					//setInterval(refreshStats(id,selected_ad), 3000);
				}
				
		});
	}
	jQuery( function($) {
				$(document).on('click', '.print', function(e) {
					e.preventDefault();
					window.print();
				});
	});
	function ClosePopup(id,type,selected_ad){
		jQuery("#"+id+"_detail_popup").html('').hide();
		//clearInterval(t);
	}
	function detailPopup(id,type,selected_ad,rotation,column){
		jQuery(".detail_View").html('').hide();
		var ad_id = '';
		//var type = '';
		var user_id = '';
				
	 //alert("test");
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getDetailAD','ad_id':id,'type':type,'selected_ad':selected_ad,'rotation':rotation,'column':column},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery("#"+id+"_detail_popup").html(data).show();
					jQuery("#"+id+"_detail_popup").addClass("myDivToPrint");
					
					//t = setInterval(refreshStats(id,selected_ad), 3000);
					//const t = setInterval(refreshStats(id,selected_ad), 3000);
				}
				
		});
	}
	function detailPopupReload(id,type,selected_ad,rotation,column){
		jQuery("#"+id+"_reload_content").html('<img src="/wp-content/themes/dokan-child/woo_loading.gif" align="right" title="Reload" style="width:40px;" class="print_icon"/>');
		var ad_id = '';
		//var type = '';
		var user_id = '';
				
	 //alert("test");
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getDetailADReload','ad_id':id,'type':type,'selected_ad':selected_ad,'rotation':rotation,'column':column},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery("#"+id+"_reload_content").html(data).show();
					
					//t = setInterval(refreshStats(id,selected_ad), 3000);
					//const t = setInterval(refreshStats(id,selected_ad), 3000);
				}
				
		});
	}
	function VacatePopup(id,type,selected_ad){
			ClosePopup(id,type,selected_ad);
			//alert(id);
			var ad_id = '';
			//var type = '';
			var user_id = '';
					
		 //alert("test");
		 var columnArray = {1: 'A', 2: 'B', 3:'C',4:'D'};
		 var tmp = id.split("_");
		 var rotation = tmp[0].replace("position","");
		 var column = columnArray[tmp[1].replace("col","")];
		 
		 jQuery.confirm({
					title: '',
					columnClass: 'col-md-10 no-button popup_vacate',
					closeIcon: true, // hides the close icon.
					content: '',
					buttons: {
						Yes: {
							text: "Do You Want to Vacate Current rotation?",
						   	btnClass: 'btn-default pull-left',
							//keys: ['enter'],
							action: function(){
								jQuery.ajax({
										url: '<?php echo get_site_url();?>/ajax.php',
										type: 'post',
										data:{'mode':'makeRotationVacate','vacateType':'current','id':id,'ad_id':ad_id,'type':type,'selected_ad':selected_ad},
										beforeSend: function() {},
										complete: function() {
										},
										success: function (response) {
											window.location.replace(window.location.href + "&update=vacate");
											return true;
											/*const myArr = response.split("##");
											jQuery('#'+id).html(myArr[0]);
											jQuery('#'+id+"_view").html(myArr[1]);*/
										},  
										error: function (response) {
										 //console.log('error');
										}
			
									});
							}
						},
						No: {
							text: "Do You Want to Vacate All rotations?",
						   	btnClass: 'btn-blue pull-left',
							//keys: ['enter'],
							action: function(){
								jQuery.ajax({
										url: '<?php echo get_site_url();?>/ajax.php',
										type: 'post',
										data:{'mode':'makeRotationVacate','vacateType':'all','id':id,'ad_id':ad_id,'type':type,'selected_ad':selected_ad},
										beforeSend: function() {},
										complete: function() {
										},
										success: function (response) {
											window.location.replace(window.location.href + "&update=vacate");
											return true;
											//window.location.replace(window.location.href + "&update=vacate");
											//return true;
											/*const myArr = response.split("##");
											jQuery('#'+id).html(myArr[0]);
											jQuery('#'+id+"_view").html(myArr[1]);*/
										},  
										error: function (response) {
										 //console.log('error');
										}
			
									});
							}
						}
					},onContentReady: function () {
					}

				});
	}
	function AdPopup(id,type,selected_ad){
		ClosePopup(id,type,selected_ad);
		//alert(id);
		var ad_id = '';
		//var type = '';
		var user_id = '';
				
	 //alert("test");
	 var columnArray = {1: 'A', 2: 'B', 3:'C',4:'D'};
	 var tmp = id.split("_");
	 var rotation = tmp[0].replace("position","");
	 var column = columnArray[tmp[1].replace("col","")];
	 var column_val = tmp[1].replace("col","");
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getAddADPopup','ad_id':ad_id,'type':type,'selected_ad':selected_ad,'rotation':rotation,'column':column},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-10 no-button popup_grey',
					closeIcon: true, // hides the close icon.
					content: data,
					buttons: {
						Yes: {
							text: "Save",
						   	btnClass: 'btn-blue pull-left',
							keys: ['enter'],
							action: function(){
								if(!jQuery("#wpforms-form-ad").validationEngine('validate')){
									return false;
								}
								//jQuery("#wpforms-form-ad").submit();
								
						var company = jQuery('#company').val();
						var ad_link = jQuery('#ad_link').val();
						var start_date = jQuery('#start_date').val();
						var end_date = jQuery('#end_date').val();
						var ad_type = jQuery('#ad_type').val();
						var code = jQuery('#code').val();
						var rotation_new = jQuery('#rotation_new').val();
						var column_new = jQuery('#column_new').val();
						
                        var file_data = jQuery('#ad_image').prop('files')[0];

                        var form_data = new FormData();

                        form_data.append('file', file_data);
                        form_data.append('mode', 'submitAD');
                        form_data.append('company', company);
                        form_data.append('ad_link', ad_link);
                        form_data.append('start_date', start_date);
                        form_data.append('end_date', end_date);
						form_data.append('id', id);
						form_data.append('type', type);
						form_data.append('selected_ad', selected_ad);
						
						form_data.append('ad_type', ad_type);
						form_data.append('code', code);
						form_data.append('column', column);
						
						form_data.append('column_val', column_val);
						form_data.append('rotation', rotation);
						form_data.append('rotation_new', rotation_new);
						form_data.append('column_new', column_new);
								 jQuery.ajax({
										url: '<?php echo get_site_url();?>/ajax.php',
										type: 'post',
										contentType: false,
										processData: false,
										data: form_data,
										success: function (response) {
											window.location.replace(window.location.href + "&update=add");
											/*const myArr = response.split("##");
											jQuery('#'+id).html(myArr[0]);
											jQuery('#'+id+"_view").html(myArr[1]);*/
											return true;
										},  
										error: function (response) {
										 //console.log('error');
										}
			
									});
									//return true;
								
							}
						}
					},onContentReady: function () {
							jQuery("#rotation_new").val(rotation);
							jQuery("#column_new").val(column_val);
							var from = jQuery('#start_date'),to = jQuery('#end_date');
							jQuery( '#start_date,#end_date' ).datepicker();
							from.on( 'change', function() {
								to.datepicker( 'option', 'minDate', from.val() );
							});
							to.on( 'change', function() {
								from.datepicker( 'option', 'maxDate', to.val() );
							});					}

				});
				
							
				}
				
		
				});
		
	
			}
			
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

		$this->stats_obj = new ADS_List();
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
	SP_Plugin_ADS::get_instance();
} );