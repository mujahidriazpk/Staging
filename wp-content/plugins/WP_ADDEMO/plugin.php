<?php
/*
Plugin Name: WP_ADDEMO
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ADDEMO_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'AD DEMO', 'sp' ), //singular name of the listed records
			'plural'   => __( 'AD DEMO', 'sp' ), //plural name of the listed records
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
	public static function get_ADDEMOs( $searchStr ) {

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


class SP_Plugin_ADDEMO {

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
			'AD DEMO',
			'AD DEMO',
			'shopadoc_admin_cap',
			'ADDEMO',
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
  <h2>AD DEMO</h2>
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
  div.wpforms-container-full .wpforms-form input:disabled, div.wpforms-container-full .wpforms-form textarea:disabled, div.wpforms-container-full .wpforms-form select:disabled{color:#333 !important;}
  div.wpforms-container-full .wpforms-form .wpforms-field-label{font-weight:normal !important}
  #toplevel_page_admin-page-ADDEMO a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-ADDEMO a:after {
			right: 0;
			border: solid 8px transparent;
			content: " ";
			height: 0;
			width: 0;
			position: absolute;
			pointer-events: none;
			border-right-color: #f0f0f1;
			top: 50%;
			}
				/*th#user_city{width:25%;}*/
				th,td{font-size:16px !important;}
				.error,.notice{display:none;}
				.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons{float:left !important;}
				.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
					float: left;
				}
				.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons button, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons button{
				background:#0A7BE2 !important;
				border:1px solid #F5F5F5 !important;
				border-radius:5px;
				padding:8px 10px !important;
				color:#fff !important;
			}
			.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
    float: left !important;
}
			.upgrade{color:#10C168 !important;}
			</style>
  <?php 
  				if (isset($_GET["action"]) && $_GET["action"] == 'upgrade') {
					$user = get_user_by( 'id', $_GET['user']);
					$user->remove_role( 'ad_demo' );
					$user->add_role( 'advanced_ads_user' );
					header("location:/wp-admin/admin.php?page=ADDEMO&update=upgradeUser");
					exit;
				}
				if (isset($_GET["action"]) && $_GET["action"] == 'approve') {
					update_user_meta($_GET['user'], 'wpforms-pending','');
					header("location:admin.php?page=ADDEMO&update=approveUser");
					exit;
				}
				if (isset($_GET["action"]) && $_GET["action"] == 'unapprove') {
					update_user_meta($_GET['user'], 'wpforms-pending',true);
					header("location:admin.php?page=ADDEMO&update=unapproveUser");
					exit;
				}
				$messages = array();
				$params = array();
				//print_r($_SERVER);
				parse_str($_SERVER['QUERY_STRING'], $params);
				//print_r($params);
		if ( isset( $params['update'] ) ) :
			switch ( $params['update'] ) {
				case 'del':
				case 'del_many':
					$message = __( 'User deleted.' );
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . $message . '</p></div>';
					break;
				case 'add':
					$message = __( 'New user created.' );
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . $message . '</p></div>';
					break;
				case 'edit':
					$message = __( 'User updated.' );
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . $message . '</p></div>';
					break;
				case 'approveUser':
					$message = __( 'user approved.' );
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . $message . '</p></div>';
					break;
				case 'resetpassword':
					$message = __( 'Password reset link sent.' );
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p><p>' . $message . '</p></div>';
					break;
				case 'promote':
					$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Changed roles.' ) . '</p></div>';
					break;
				case 'err_admin_remove':
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( "You can't remove the current user." ) . '</p></div>';
					$messages[] = '<div id="message" class="updated notice is-dismissible fade"><p>' . __( 'Other users have been removed.' ) . '</p></div>';
					break;
			}
		endif;
		if ( ! empty( $messages ) ) {
			foreach ( $messages as $msg ) {
				echo $msg;
			}
		}
		//print_r($_SERVER);
			?>
<!--  <?php if (isset($_GET["update"]) && $_GET["update"] == 'update') {?>
  <div id="setting-error-wpforms-approved" class="notice notice-success settings-error is-dismissible" style="display:block;">
    <p><strong>user approved.</strong></p>
    <button type="button" class="notice-dismiss" onclick="jQuery('#setting-error-wpforms-approved').hide();"><span class="screen-reader-text">Dismiss this notice.</span></button>
  </div>
  <?php }?>
  <?php if (isset($_GET["msg"]) && $_GET["msg"] == 'unapproveUser') {?>
  <div id="setting-error-wpforms-unapproved" class="notice notice-success settings-error is-dismissible" style="display:block;">
    <p><strong>user unapproved.</strong></p>
    <button type="button" class="notice-dismiss" onclick="jQuery('#setting-error-wpforms-unapproved').hide();"><span class="screen-reader-text">Dismiss this notice.</span></button>
  </div>
  <?php }?>-->
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <?php
          $args = array(
						'role'    => 'ad_demo',
						'orderby' => 'user_nicename',
						'order'   => 'ASC'
					);
					$users = get_users( $args );

					
		  ?>
          <p class="pull-right"><a href="javascript:addUser('','Demo');" title="addUser" style="float:right" class="btn btn-primary">Add New</a></p>
          <table class="wp-list-table widefat fixed striped table-view-list users">
            <thead>
              <tr>
                <th scope="col" id="username" class="manage-column column-primary  desc"><span>Company</span></th>
                <th scope="col" id="name" class="manage-column desc"><span>Agent</span></th>
                <th scope="col" id="6036312b3a9dc" class="manage-column column-6036312b3a9dc">Address</th>
                <th scope="col" class="manage-column column-email  desc"><span>Username / Email</span></th>
                <th scope="col" id="role" class="manage-column column-role  desc"><span>Cell</span></th>
              </tr>
            </thead>
            <tbody id="the-list" data-wp-lists="list:user">
              <?php foreach($users as $user){// print_r($user);
			  			$ad_demo_company_name = get_user_meta( $user->ID, 'ad_demo_company_name', true);
						$ad_demo_agent = get_user_meta( $user->ID, 'ad_demo_agent', true);
						$wpforms_pending = get_user_meta( $user->ID, 'wpforms-pending', true);
						$billing_address_1 = get_user_meta( $user->ID, 'billing_address_1', true);
						$billing_address_2 = get_user_meta( $user->ID, 'billing_address_2', true);
						$billing_city = get_user_meta( $user->ID, 'billing_city', true);
						$billing_state = get_user_meta( $user->ID, 'billing_state', true);
						$billing_postcode = get_user_meta( $user->ID, 'billing_postcode', true);
						$billing_phone = get_user_meta( $user->ID, 'billing_phone', true);
						$address = $billing_address_1.' '.$billing_address_2.' '.$billing_city.', '.$billing_state.' '.$billing_postcode;
						$url = self_admin_url( 'users.php?action=delete&users='.$user->ID.'' );
						//$url = str_replace( '&amp;', '&', wp_nonce_url( $url,'action') );
						$url = wp_nonce_url( "users.php?action=delete&user=".$user->ID, 'bulk-users' );
			  ?>
              <tr id="user-<?php echo $user->ID;?>" data-id="<?php echo $user->ID;?>" <?php if($wpforms_pending){?>style="background-color: rgb(255, 255, 224);"<?php }?>>
                <td class="username has-row-actions column-primary" data-colname="Username"><strong><!--<a href="/wp-admin/user-edit.php?user_id=<?php echo $user->ID;?>&wp_http_referer=/wp-admin/admin.php?page=ADDEMO">--><a href="javascript:addUser('<?php echo $user->ID;?>','');"><?php echo $ad_demo_company_name;?></a></strong><br>
                  <div class="row-actions"> <span class="edit"><a href="javascript:addUser('<?php echo $user->ID;?>','');">Details</a> | </span> 
                  <span class="delete"><a class="submitdelete" href="<?php echo wp_nonce_url( "users.php?action=delete&user=".$user->ID."&wp_http_referer=/wp-admin/admin.php?page=ADDEMO&update=del", 'bulk-users' );?>">Delete</a> | </span>
                  <!--<span class="view"><a href="javascript:viewUser('<?php echo $user->ID;?>','view');" aria-label="View posts by Ad Demo">View</a> | </span> --><span class="resetpassword"><a class="resetpassword" href="<?php echo wp_nonce_url( "users.php?action=resetpassword&user=".$user->ID."&wp_http_referer=/wp-admin/admin.php?page=ADDEMO&update=resetpassword", 'bulk-users' );?>">Send password reset</a></span>
                    <?php if($wpforms_pending){?>
                    <span class="wpforms-approve"><a class="submitapprove" href="<?php echo wp_nonce_url('admin.php?page=ADDEMO&action=approve&user='.$user->ID);?>"> | Renew</a></span>
                    <?php }else{?>
                   <!-- <span class="wpforms-unapprove"><a class="submitunapprove" href="<?php echo wp_nonce_url('admin.php?page=ADDEMO&action=unapprove&user='.$user->ID);?>">Unapprove</a></span>-->
                    <?php }?>
                    <span class="wpforms-upgrade"><a class="upgrade" href="<?php echo wp_nonce_url('admin.php?page=ADDEMO&action=upgrade&user='.$user->ID);?>" style="color:#10C168;"> | Upgrade to Advertiser</a></span>
                  </div></td>
                <td><?php echo $ad_demo_agent;?></td>
                <td><?php echo $address ;?></td>
                <td class="email column-email" data-colname="Email"><!--<?php echo $user->user_login;?> / --><?php echo $user->user_email;?></td>
                <td class="role column-role" data-colname="Role"><?php echo $billing_phone;?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
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

		$this->stats_obj = new ADDEMO_List();
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
	SP_Plugin_ADDEMO::get_instance();
} );



