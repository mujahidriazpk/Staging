<?php

/*
Plugin Name: WP_Pricing
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Pricing' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Pricing_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Pricing', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Pricing', 'sp' ), //plural name of the listed records
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


class SP_Plugin_Pricing {

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
			'Pricing',
			'Pricing',
			'shopadoc_admin_cap',
			'pricing',
			[ $this, 'plugin_settings_page' ]
		);
	//	add_submenu_page( 'pricing', 'Pricing', 'Pricing','shopadoc_admin_cap', 'admin.php?page=pricing');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
		$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		?>

<div class="wrap"> 
  <!--<h2>Sales</h2>-->
  <style type="text/css">
  		#toplevel_page_admin-page-pricing a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-pricing a:after {
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
			</style>
  <?php 
				if (isset($_POST["action"]) && $_POST["action"]=='submitFee') {
					foreach($_POST as $k=>$v){
						if($k!='action'){
							/*_regular_price	9.99
								_sale_price	
								_sale_price_dates_from	
								_sale_price_dates_to
								_price	9.99
								sale_price	9.99*/
								if($k==126 || $k==1141 || $k==942 || $k==948 || $k==1642 ){
									update_post_meta($k,'_regular_price',$v);
									update_post_meta($k,'_price',$v);
								}
						}
					}
					
				}
			?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable"> 
          <script src="/wp-content/plugins/WP_Sale_Graph/lib/raphael.js"></script> 
          <!-- <script src="scale.raphael.js"></script> --> 
          <script src="/wp-content/plugins/WP_Sale_Graph/example/color.jquery.js"></script> 
          <script src="/wp-content/plugins/WP_Sale_Graph/us-map.js"></script> 
          <script src="/wp-content/plugins/WP_Sale_Graph/sorttable.js"></script> 
          <script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery('#map').usmap({
								'showLabels' : false,
								'stateStyles': {
								fill: '#D1DBDD', 
								"stroke-width": 1,
								'stroke' : '#fff'
							},
							'stateHoverStyles': {
								fill: '#12A94C'
							},
							'mouseover': function(event, data) {
								/*var title = data.name;
								var html_tooltip = jQuery("#tooltip-html-"+data.name).html();
								jQuery('<p class="tooltip"></p>').html(html_tooltip).appendTo('body').fadeIn('slow');
								jQuery('#map').mousemove(function(e) {
									var mousex = e.pageX + 20; //Get X coordinates
									var mousey = e.pageY; //Get Y coordinates
									if(title=='WA'){
										var mousey = e.pageY; //Get Y coordinates
									}
									jQuery('.tooltip').css({ top: mousey, left: mousex })
								});*/
							},
							'mouseout': function(event, data) {
								//jQuery('.tooltip').remove();
								
							},
							'click' : function(event, data) {
										jQuery("svg path").css('fill', '#D1DBDD');
										jQuery("#map-"+data.name).css('fill', '#12A94C').css('opacity', '1');
										jQuery.ajax({	
										url:'<?php echo  home_url('/ajax.php');?>',
										type:'POST',
										data:{'mode':'getCoupon','state_name':data.name},
										beforeSend: function() {},
										complete: function() {
										},
										success:function (data){
											jQuery("#coupon_div").html(data);
										}
								});
							}
						});
						jQuery('#map svg').attr('width','100%');
						jQuery('#map svg').attr('height','100%');
						jQuery("#dest").addSortWidget();
						jQuery("table.scroll tr:odd").css({"background-color":"#F6F7F7","color":"#000"});
					});
					
					var $table = jQuery('table.scroll'),
					$bodyCells = $table.find('tbody tr:first').children(),
					colWidth;
					
  					
					// Adjust the width of thead cells when window resizes
					jQuery(window).resize(function() {
					// Get the tbody columns width array
					colWidth = $bodyCells.map(function() {
					return jQuery(this).width();
					}).get();
					// Set the width of thead columns
					$table.find('thead tr').children().each(function(i, v) {
						jQuery(v).width(colWidth[i]);
					});    
					}).resize();
					
					jQuery( function($) {
						var from = $('input[name="mishaDateFrom"]'),
						to = $('input[name="mishaDateTo"]');
						$( 'input[name="mishaDateFrom"], input[name="mishaDateTo"]' ).datepicker();
						from.on( 'change', function() {
							to.datepicker( 'option', 'minDate', from.val() );
						});
						to.on( 'change', function() {
							from.datepicker( 'option', 'maxDate', to.val() );
						});
					});
			</script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
          <style type="text/css">  
			svg:not(:root) {
					position:fixed !important;
					top:auto !important;
					left:auto !important;
					width:50%;
				}
				.widefat tbody tr {
					float:left;
					width:100%;
				}
				table.scroll {
					/* width: 100%; */ /* Optional */
					/* border-collapse: collapse; */
					border-spacing: 0;
					width:100%;
				}
				table.scroll tbody, table.scroll thead {
					display: block;
				}
				table.scroll thead tr th {
					height: 30px;
				}
				table.scroll tbody {
					height: auto;
					overflow-y: auto;
					overflow-x: hidden;
				}
				table.scroll tbody { /*border-top: 2px solid black; */
				}
				table.scroll thead tr {
					display:inline-table;
					width:100%;
				}
				table.scroll tbody td, table.scroll thead th {
					float: left !important;
					width:25% !important;
				}
				table.scroll tbody td:last-child, table.scroll thead th:last-child {
					border-right: none;
				}
				table.scroll thead tr td, table.scroll thead tr th {
					color: #000;
					background: #fff;
				}
				#map {
					width: 60%;
					height: 630px;
					float: left;
					margin-top: 35px;
				}
				#details {
					width: 40%;
					float: left;
					/*background: #fff;*/
					padding: 1%;
					margin-top: -20px;
					border-radius:3px;
				}
                .tooltip {
						 position: absolute;
						 /*border:1px solid black;*/
						 background: #fff;
						 color: #000;
						 font-size: 1.5 em;
						 padding: 2px 8px;
						 opacity:1;
						 border-radius: 2px;
						 width:300px;
						 box-shadow: 0px 1px 8px 1px #888;
						-moz-box-shadow:0px 1px 8px 1px #888;
						-webkit-box-shadow:0px 1px 8px 1px #888;
						z-index:100001;
					}
                /*text,rect{display:none;}*/
                .striped > tbody > :nth-child(2n+1), ul.striped > :nth-child(2n+1) {
                    background-color: #fff;
                }
				#filter_date {
					float: left;
					border: solid 3px #000;
					padding: 10px;
					margin-bottom: 10px;
					background:#fff;
					border-radius:3px;
				}
               
                input[name="mishaDateFrom"], input[name="mishaDateTo"] {
                    line-height: 28px;
                    height: 28px;
                    margin: 0;
                    width: 38%;
					font-weight:bold;
                }
				.pink{color:#DB2D69 !important;}
				.blue{color:#479CE9 !important;}
				.amt{font-size:20px;}
				.wrap {margin: 10px 0 0 2px;}
				.top_panel{background:#572D91;float:left;width:100%;padding:10px;margin-top:10px;}
				.table_bg strong,.table_bg th{color:#fff;}
				.widefat td, .widefat th {
					/*padding: 8px 0;*/
				}
				.widefat td, .widefat th {
						color: #000;
					}
				.fee_section{float:left;width:100%;padding-top:10px;}
				p label{width:38%;float:left;}
				.fee_section .button{width:75px;background:#fff !important;border:solid 1px green;border-radius:10px;color:#DB2D69 !important;font-weight:bold;}
				
					
					/*.currencyinput {
					border: 1px inset #ccc;
					float: left;
					text-align: left;
					width: 60%;
					background: #fff;
					padding-left: 6px;
				}
					p label{width:35%;float:left;line-height:32px}
					.currencyinput input, .currencyinput input:focus, .currencyinput input:focus-visible {
					float: right !important;
					width: 94% !important;
					padding-left: 0 !important;
					border: 0 !important;
					box-shadow: none !important;
					color:#DB2D69;
					font-weight:bold;
				}
				.currencyinput .symbol {
					line-height: 32px;
					color:#DB2D69;
					font-weight:bold;
				}*/
				.add_coupon{
					border: 1px solid #91AE74;
					float: none;
					text-align: center;
					width: 77%;
					background: #fff;
					padding-left: 5px;
					height: 21px;
					color: #DB2D69;
					display: inline-block;
					padding-right: 10px;
					border-radius: 5px;
					font-size:10px;
					margin-right:5px;
				}
				.add_couponlabel {
					border: 1px solid #91AE74;
					float: none;
					text-align: center;
					width: 100%;
					background: #fff;
					padding-left: 5px;
					height: 21px;
					color: #DB2D69;
					display: inline-block;
					padding-right: 10px;
					border-radius: 5px;
					font-size:10px;
				}
				#dest th,#dest td{font-size:11px !important;}
				.fee_section .currencyinput{
					border: 1px solid #91AE74;
					float: left;
					text-align: left;
					width: 70px;
					background: #fff;
					padding-left: 5px;
					height: 21px;
					display: flex;
					border-radius: 5px;
				}
				.fee_section .currencyinput .symbol {
					line-height: normal;
					color: #DB2D69;
					font-weight: bold;
					float: left;
					display: flex;
					align-items: center;
				}
				/*input[type=color], input[type=date], input[type=datetime-local], input[type=datetime], input[type=email], input[type=month], input[type=number], input[type=password], input[type=search], input[type=tel], input[type=text], input[type=time], input[type=url], input[type=week], select, textarea{
					min-height:18px !important;
					width: 88% !important;
					border:none !important;
					border: 0 !important;
					box-shadow: none !important;
					color: #DB2D69;
					font-weight: bold;
				}*/
				.closeico{position:relative;width:100%;}
				.closeico_img{z-index: 999920;
					position: absolute;
					float: right;
					right: 0;
					bottom: 0;
					width: 21px;
					cursor: pointer;
					top: 0;
					height: 21px;
					background-repeat: no-repeat;
					background-size: cover;
				}
				#coupon_div{float:left;width:100%;}
				.fee_section .currencyinput input[type=text],.fee_section .currencyinput input{
					float: left !important;
					width: 88% !important;
					padding-left: 0 !important;
					border: 0 !important;
					box-shadow: none !important;
					color: #DB2D69;
					font-weight: bold;
					margin: 0;
					padding: 0 !important;
					min-height: auto;
					line-height: normal;
				}
				.fee_section .currencyinput input[type=text],.fee_section .currencyinput input,.fee_section .currencyinput input:focus,.fee_section .currencyinput input:focus-visible {
					float: left !important;
					width: 88% !important;
					padding-left: 0 !important;
					border: 0 !important;
					box-shadow: none !important;
					color: #DB2D69;
					font-weight: bold;
					margin: 0;
					padding: 0 !important;
					min-height: auto;
					line-height: normal;
				}
				
				p {
					padding-bottom: 0px;
					float: left;
					width: 100%;
					margin:0;
				}
				/*.add_coupon{width:75px;background:#fff !important;border:solid 1px green;border-radius:10px;color:#DB2D69 !important;font-weight:bold;padding:3px 5px;cursor:pointer;}*/
				#wpforms-form-ad .currencyinput {
					border: 1px inset #ccc;
					float: left;
					text-align: left;
					width: 100%;
					background: #fff;
					padding-left: 6px;
				}
				#wpforms-form-ad .currencyinput input{
						float: right !important;
						width: 97% !important;
						padding-left: 0 !important;
						border: 0 !important;
						box-shadow:none !important;
						font-weight:normal !important;
					}
				#wpforms-form-ad .currencyinput input,#wpforms-form-ad .currencyinput input:focus,#wpforms-form-ad .currencyinput input:focus-visible {
						float: right !important;
						width: 97% !important;
						padding-left: 0 !important;
						border: 0 !important;
						box-shadow:none !important;
						font-weight:normal !important;
					}
				#wpforms-form-ad .currencyinput .symbol{
					line-height: 38px;
					color:#000 !important;
				}
				.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
    float: left !important;
}
				.heading{float:left;width:100%;font-weight:bold;padding-bottom:0;}
				.heading .heading1{float:left;font-size:14px;line-height:24px;}
				.heading .heading2{float:left;font-size:18px;line-height:24px;}
				.heading-discount{float:left;font-size:12px;font-weight:bold;float:left;width:100%;padding-bottom:5px;color:#000;}
				.widefat td, .widefat th {
					padding: 10px ​5px !important;
				}
				.btn.btn-primary, .btn.btn-primary:hover, .btn.btn-blue, .btn.btn-blue:hover {
					padding: 2px 10px !important;
					margin-top: -4px;
				}
				.jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons button, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons button{text-transform:none !important;}
             </style>
          <div id="map" ></div>
          <div id="details" >
            <div id="filter_date" >
              <div class="heading"><span class="heading1">PRICING -</span><span class="heading2">&nbsp;NATIONWIDE</span></div>
              <?php 
					$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
					$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
					global $US_state;
					$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
					/*_regular_price	9.99
					_sale_price	
					_sale_price_dates_from	
					_sale_price_dates_to
					_price	9.99
					sale_price	9.99*/
					$price_listing = get_post_meta(126,"_regular_price",true);
					$price_registration = get_post_meta(1141,"_regular_price",true);
					$price_weekly = get_post_meta(942,"_regular_price",true);
					$price_subscription = get_post_meta(948,"_regular_price",true);
					$price_relist = get_post_meta(1642,"_regular_price",true);
				?>
              <form method="post" class="feeForm" id="feeForm">
                <div class="fee_section">
                  <?php if (isset($_POST["action"]) && $_POST["action"]=='submitFee') {?>
                  <p style="padding-bottom:5px;" id="msg"><span class="green"> Pricing updated successfully. </span> </p>
                  <script type="text/javascript">
				  	setTimeout(function(){jQuery("#msg").hide('slow');}, 2000);
				  </script>
                  <?php }?>
                  <p class="medpaddingbottom">
                    <label><strong>C Listing Fee</strong></label>
                    <span class="currencyinput"><span class="symbol">$</span>
                    <input type="text" id="C_Listing_Fee" class="validate[required,custom[number]]" name="126" value="<?php echo number_format($price_listing, 2, '.', '');?>" onkeypress="return isNumberKey(this, event);" />
                    </span><!--<input type="button" class="button" name="C_Listing_Fee" value="$<?php echo number_format($price_listing, 2, '.', '');?>" onclick="feePopup('C_Listing_Fee',126);"/>--></p>
                    <p >
                    <label><strong>C Relist Fee</strong></label>
                    <span class="currencyinput"><span class="symbol">$</span>
                    <input type="text" id="Relist_Fee" class="validate[required]" name="1642" value="<?php echo number_format($price_relist, 2, '.', '');?>" onkeypress="return isNumberKey(this, event);" />
                    </span><!--<input type="button" class="button" name="Relist_Fee" value="$<?php echo number_format($price_relist, 2, '.', '');?>" onclick="feePopup('Relist_Fee',1642);"/>--></p>
                  <p >
                    <label><strong>D Registration</strong></label>
                    <span class="currencyinput"><span class="symbol">$</span>
                    <input type="text" id="D_Registration" class="validate[required]" name="1141" value="<?php echo number_format($price_registration, 2, '.', '');?>" onkeypress="return isNumberKey(this, event);" />
                    </span><!--<input type="button" class="button" name="D_Registration" value="$<?php echo number_format($price_registration, 2, '.', '');?>" onclick="feePopup('D_Registration',1141);"/>--></p>
                  <p >
                    <label><strong>D Weekly Fee</strong></label>
                    <span class="currencyinput"><span class="symbol">$</span>
                    <input type="text" id="D_Weekly_Fee" class="validate[required]" name="942" value="<?php echo number_format($price_weekly, 2, '.', '');?>" onkeypress="return isNumberKey(this, event);" />
                    </span><!--<input type="button" class="button" name="D_Weekly_Fee" value="$<?php echo number_format($price_weekly, 2, '.', '');?>" onclick="feePopup('D_Weekly_Fee',942);"/>--></p>
                  <p >
                    <label><strong>D Subscription</strong></label>
                    <span class="currencyinput"><span class="symbol">$</span>
                    <input type="text" id="D_Subscription" class="validate[required]" name="948" value="<?php echo number_format($price_subscription, 2, '.', '');?>" onkeypress="return isNumberKey(this, event);" />
                    </span><!--<input type="button" class="button" name="D_Subscription" value="$<?php echo number_format($price_subscription, 2, '.', '');?>" onclick="feePopup('D_Subscription',948);"/>--> <input type="submit" value="Save" class="btn btn-primary" style="margin-left:45px;"/></p>
                  
                  <input type="hidden" name="action" value="submitFee" />
                  <p >
                    
                  </p>
                </div>
              </form>
              
              <!--<input type="text" name="mishaDateFrom" placeholder="Start Date" value="<?php echo $from;?>" />
                &nbsp;-&nbsp;
                <input type="text" name="mishaDateTo" placeholder="End Date" value="<?php echo $to;?>" />
                <input type="submit" name="submit[filter]" value="Filter" />-->
              <?php /*?>
                <div class="fee_section">
               <p ><label><strong>C Listing Fee</strong></label> <span class="currencyinput"><span class="symbol">$</span><input type="text" id="C_Listing_Fee" class="wpforms-field-medium validate[required]" name="C_Listing_Fee" value="<?php echo number_format($price_listing, 2, '.', '');?>" data-prompt-position="topRight:-80,3"></span><!--<input type="button" class="button" name="C_Listing_Fee" value="$<?php echo number_format($price_listing, 2, '.', '');?>" onclick="feePopup('C_Listing_Fee',126);"/>--></p>
               <p ><label><strong>D Registration</strong></label> <input type="button" class="button" name="D_Registration" value="$<?php echo number_format($price_registration, 2, '.', '');?>" onclick="feePopup('D_Registration',1141);"/></p>
               <p ><label><strong>D Weekly Fee</strong></label> <input type="button" class="button" name="D_Weekly_Fee" value="$<?php echo number_format($price_weekly, 2, '.', '');?>" onclick="feePopup('D_Weekly_Fee',942);"/></p>
               <p ><label><strong>D Subscription</strong></label> <input type="button" class="button" name="D_Subscription" value="$<?php echo number_format($price_subscription, 2, '.', '');?>" onclick="feePopup('D_Subscription',948);"/></p>
               <p ><label><strong>C Relist Fee</strong></label> <input type="button" class="button" name="Relist_Fee" value="$<?php echo number_format($price_relist, 2, '.', '');?>" onclick="feePopup('Relist_Fee',1642);"/></p>
               </div>
			   <?php */?>
            </div>
            <div id="coupon_div"> </div>
          </div>
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
					columnClass: 'col-md-6 col-md-offset-3 no-button popup_grey price_popup',
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

		$this->stats_obj = new Pricing_List();
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
	SP_Plugin_Pricing::get_instance();
} );

