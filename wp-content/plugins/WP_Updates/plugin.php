<?php

/*
Plugin Name: WP_Updates
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Updates' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Updates_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Updates', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Updates', 'sp' ), //plural name of the listed records
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


	public static function get_stats( $per_page = 20, $page_number = 1 ) {}


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
	public static function record_count() {}


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
	public function column_default( $item, $column_name ) {}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {}


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
	function get_columns() {}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {}

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


class SP_Plugin_Updates {

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
			'Updates',
			'Updates',
			'shopadoc_admin_cap',
			'updates',
			[ $this, 'plugin_settings_page' ]
		);
	//	add_submenu_page( 'updates', 'Updates', 'Updates','shopadoc_admin_cap', 'admin.php?page=updates');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		
		global $wpdb;
		$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
		$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		?>

<div class="wrap"> 
  
  <h2>Updates</h2>
  <style type="text/css">
  		#toplevel_page_admin-page-updates a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-updates a:after {
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
				/*th#order_city{width:25%;}*/
				th,td{font-size:12px !important;}
				.font-22{font-size:22px !important;}
				.error,.notice{display:none;}
				.green{color:green;}
				.red{color:red;}
			</style>
  <?php 
				if (isset($_POST["action"]) && $_POST["action"]=='submitFee') {
					global $wpdb;
					/*foreach($_POST['status_update'] as $key => $data){
						if($data !=""){
							  $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM wp_updates_log WHERE name = '".$key."' ");
							  if($rowcount > 0){
        					  	$wpdb->update('wp_updates_log', array('name'=>$key, 'version'=>$_POST[$key."_version"], 'status'=>$data), array('name'=>$key));
							  }else{
								  	$wpdb->insert('wp_updates_log', array('name'=>$key, 'version'=>$_POST[$key."_version"], 'status'=>$data));
							  }
						}
					}*/
					//print_r($_POST);
					foreach($_POST['log_ids'] as $id){
						if($_POST['status_update_'.$id] !=""){
        					  	$wpdb->update('wp_updates_log', array('version'=>$_POST["version_".$id], 'status'=>$_POST['status_update_'.$id]), array('id'=>$id));
						}
						//echo $id."<br />";
					}
				}
				$wp_version     = get_bloginfo( 'version' );
				$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );
			
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				$plugins = get_plugin_updates();
				if ( empty( $plugins ) ) {
					echo '<h2>' . __( 'Plugins' ) . '</h2>';
					echo '<p>' . __( 'Your plugins are all up to date.' ) . '</p>';
					return;
				}
				$form_action = 'update-core.php?action=do-plugin-upgrade';
			
				$core_updates = get_core_updates();
				if ( ! isset( $core_updates[0]->response ) || 'latest' === $core_updates[0]->response || 'development' === $core_updates[0]->response || version_compare( $core_updates[0]->current, $cur_wp_version, '=' ) ) {
					$core_update_version = false;
				} else {
					$core_update_version = $core_updates[0]->current;
				}
			
				$plugins_count = count( $plugins );
				//print_r($plugins);
				foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
					$plugin_data = (object) _get_plugin_data_markup_translate( $plugin_file, (array) $plugin_data, false, true );
					$Name = $plugin_data->Name;
					$slug = str_replace(" ","-",str_replace(" – ","-",strtolower($plugin_data->Name)));
					$version = $wpdb->get_var("SELECT version FROM wp_updates_log WHERE slug = '".$slug."' ");
					if($version!=$plugin_data->Version){
						 $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM wp_updates_log WHERE slug = '".$slug."' ");
						  if($rowcount > 0){
								$wpdb->update('wp_updates_log', array('name'=>$Name,'slug'=>$slug, 'version'=>$plugin_data->Version, 'status'=>''), array('slug'=>$slug));
						  }else{
								$wpdb->insert('wp_updates_log', array('name'=>$Name,'slug'=>$slug, 'version'=>$plugin_data->Version, 'status'=>'', 'dated'=>date('Y-m-d H:i:s')));
						  }
					}else{
							//$wpdb->update('wp_updates_log', array('status'=>''), array('name'=>$key));
					}
				}
			?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
        <form id="updateForm" name="updateForm" action="" method="post">
        	<input name="action" value="submitFee" type="hidden" />
          <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
                <th scope="col" id="6036312b3586a" class="manage-column " style="padding:8px 10px !important;"><span>Action</span></th>
                <th scope="col" id="6036312b3a9dc" class="manage-column column-6036312b3a9dc" width="30%">Incoming</th>
                <th scope="col" id="60e5c718d8654" class="manage-column column-60e5c718d8654">Essential</th>
                <th scope="col" id="6036312b3ad5c" class="manage-column column-6036312b3ad5c">Completed</th>
                <th scope="col" id="6039002027393" class="manage-column column-6039002027393">Non-Essential</th>
              </tr>
            </thead>
            <tbody id="the-list">
            <?php 
			$query = "SELECT * FROM wp_updates_log where status !='Archive'";
			$results = $wpdb->get_results($query, OBJECT);
			$active_auction = array();
			foreach($results as $row){
				//foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
					//$plugin_data = (object) _get_plugin_data_markup_translate( $plugin_file, (array) $plugin_data, false, true );
					//$slug = str_replace(" ","-",str_replace(" – ","-",strtolower($plugin_data->Name)));
					//$status = $wpdb->get_var("SELECT status FROM wp_updates_log WHERE name = '".$slug."' ");
					$status = $row->status;
			?>
              <tr>
                <td><select id="status_update_<?php echo $row->id;?>" name="status_update_<?php echo $row->id;?>" onchange="this.form.submit();">
                <option value=""></option>
                	<option value="Essential">Essential</option>
                	<option value="Non-Essential">Non-Essential</option>
                	<option value="Completed">Completed</option>
                    <option value="Archive">Archive</option>
                </select>
                <input type="hidden" name="log_ids[]" value="<?php echo $row->id;?>" />
              <input type="hidden" name="version_<?php echo $row->id;?>" value="<?php echo $row->version;?>" /></td>
                <td><strong><?php if($status==''){ echo $row->name."<br />".date("Y/m/d",strtotime($row->dated));}?></strong></td>
                <td>&nbsp;<?php if($status=='Essential'){ echo $row->name; echo '<span class="black">&nbsp;&nbsp;'.date("m/d/y",$row->update_date).'</span>';}?></td>
                <td>&nbsp;<?php if($status=='Completed'){ echo $row->name; echo '<span class="green">&nbsp;&nbsp;'.date("m/d/y",$row->update_date).'</span>';}?></td>
                <td>&nbsp;<?php if($status=='Non-Essential'){ echo '<span class="red">'.$row->name; echo '&nbsp;&nbsp;'.date("m/d/y",$row->update_date).'</span>';}?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
          </form>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	
   // jQuery("#feeForm").validationEngine(promptPosition : "centerRight", scroll: false);
	jQuery("#feeForm").validationEngine('attach', {promptPosition : "topLeft", scroll: false});
	<?php if(isset($_GET['state'])&&$_GET['state']!=""){?>
			jQuery("svg path").css('fill', '#D1DBDD');
			jQuery("#map-<?php echo $_GET['state'];?>").css('fill', '#12A94C').css('opacity', '1');
			jQuery.ajax({	
					url:'<?php echo  home_url('/ajax.php');?>',
					type:'POST',
					data:{'mode':'getCoupon','state_name':'<?php echo $_GET['state'];?>'},
					beforeSend: function() {},
					complete: function() {
					},
					success:function (data){
						jQuery("#coupon_div").html(data);
						
					}
			});
	<?php }?>	
});

function addCoupon(selected_coupon,catid,cat_name,state_name){
	
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getDiscountPopup','selected_coupon':selected_coupon,'catid':catid,'cat_name':cat_name,'state_name':state_name},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-6 col-md-offset-3 no-button popup_grey',
					closeIcon: true, // hides the close icon.
					content: data,
					buttons: {
						Yes: {
							text: "Save",
						   	btnClass: 'btn btn-primary pull-left',
							keys: ['enter'],
							action: function(){
								if(!jQuery("#wpforms-form-ad").validationEngine('validate')){
									return false;
								}
								//jQuery("#wpforms-form-ad").submit();
								
						var discount_type = jQuery('#discount_type').val();
						var coupon_amount = jQuery('#coupon_amount').val();
						var code = jQuery('#code').val();
						var start_date = jQuery('#start_date').val();
						var end_date = jQuery('#end_date').val();
						var form_data = new FormData();

                        form_data.append('mode', 'submitDiscount');
                        form_data.append('discount_type', discount_type);
                        form_data.append('coupon_amount', coupon_amount);
                        form_data.append('code', code);
                        form_data.append('state_name', state_name);
						 form_data.append('selected_coupon', selected_coupon);
						 form_data.append('coupons_category',catid);
						  form_data.append('cat_name',cat_name);
                        form_data.append('start_date', start_date);
                        form_data.append('end_date', end_date);
						//form_data.append('id', id);
						//form_data.append('type', type);
						//form_data.append('selected_ad', selected_ad);
								 jQuery.ajax({
										url: '<?php echo get_site_url();?>/ajax.php',
										type: 'post',
										contentType: false,
										processData: false,
										data: form_data,
										success: function (response) {
											 window.location.replace(window.location.href + "&state="+state_name);
											//jQuery('.Success-div').html("Form Submit Successfully")
											//jQuery('#'+id).html(response);
											/*const myArr = response.split("##");
											jQuery('#'+id).html(myArr[0]);
											jQuery('#'+id+"_view").html(myArr[1]);*/
											return true;
										},  
										error: function (response) {
										 //console.log('error');
										}
			
									});
									
								
							}
						}
					},onContentReady: function () {
							jQuery("#discount_type").on( 'change', function() {
									if(jQuery(this).val()=='percent'){
										jQuery("#wpforms-form-ad .currencyinput .symbol").text("%");
									}else{
										jQuery("#wpforms-form-ad .currencyinput .symbol").text("$");
									}
							});
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
function feePopup(type,id){
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'getfeePopup','id':id,'type':type},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){
					jQuery.confirm({
					title: '',
					columnClass: 'col-md-6 col-md-offset-3 no-button popup_grey',
					closeIcon: true, // hides the close icon.
					content: data,
					buttons: {
						Yes: {
							text: "Submit",
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
						
                        var file_data = jQuery('#ad_image').prop('files')[0];

                        var form_data = new FormData();

                        form_data.append('file', file_data);
                        form_data.append('mode', 'submitFee');
                        form_data.append('company', company);
                        form_data.append('ad_link', ad_link);
                        form_data.append('start_date', start_date);
                        form_data.append('end_date', end_date);
						form_data.append('id', id);
						form_data.append('type', type);
						form_data.append('selected_ad', selected_ad);
								 jQuery.ajax({
										url: '<?php echo get_site_url();?>/ajax.php',
										type: 'post',
										contentType: false,
										processData: false,
										data: form_data,
										success: function (response) {
											//jQuery('.Success-div').html("Form Submit Successfully")
											//jQuery('#'+id).html(response);
											const myArr = response.split("##");
											jQuery('#'+id).html(myArr[0]);
											jQuery('#'+id+"_view").html(myArr[1]);
											return true;
										},  
										error: function (response) {
										 //console.log('error');
										}
			
									});
									
								
							}
						}
					},onContentReady: function () {
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
			function isNumberKey(txt, evt) {
			  var charCode = (evt.which) ? evt.which : evt.keyCode;
			  if (charCode == 46) {
				//Check if the text already contains the . character
				if (txt.value.indexOf('.') === -1) {
				  return true;
				} else {
				  return false;
				}
			  } else {
				if (charCode > 31 &&
				  (charCode < 48 || charCode > 57))
				  return false;
			  }
			  return true;
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

		$this->stats_obj = new Updates_List();
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
	SP_Plugin_Updates::get_instance();
} );


