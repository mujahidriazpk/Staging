<?php

/*
Plugin Name: WP_Sale_Graph
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Sale_Graph' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Sale_Graph_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Home Performance Stat', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Home Performance Stat', 'sp' ), //plural name of the listed records
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
		$query = "SELECT order_items.order_id
				FROM {$wpdb->prefix}woocommerce_order_items as order_items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
				LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
				WHERE posts.post_type = 'shop_order'
				AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
				AND order_items.order_item_type = 'line_item'
				AND order_item_meta.meta_key = '_product_id'
				AND order_item_meta.meta_value = '$product_id' ";
		$results = $wpdb->get_col($query);
		return $results;
	}
public static function getSaleCustomFunc($state,$product_id,$type,$mishaDateFrom,$mishaDateTo){
		global $wpdb;
		$args = '';
		$order_status = array('wc-processing','wc-on-hold','wc-completed');
		$where = " 1=1 ";
		if($state !=""){
			$where .= ' and state="'.$state.'" ';
		}
		/*if($product_id !=""){
			$where .= ' and product_id="'.$product_id.'" ';
		}*/
		if($type !=""){
			$where .= ' and type="'.$type.'" ';
		}
		
		if(isset($mishaDateFrom) && $mishaDateFrom !="" && $mishaDateTo ==""){
			$where .= ' and date >="'.date("Y-m-d",strtotime($mishaDateFrom)).'" ';
		}elseif($mishaDateFrom =="" && isset($mishaDateTo) && $mishaDateTo !=""){
			$where .= ' and date <= "'.date("Y-m-d",strtotime($mishaDateTo)).'" ';
		}elseif(isset($mishaDateFrom) && $mishaDateFrom !="" && isset($mishaDateTo) && $mishaDateTo !=""){
			$where .= ' and date BETWEEN "'.date("Y-m-d",strtotime($mishaDateFrom)).'" AND "'.date("Y-m-d",strtotime($mishaDateTo)).'" ';
		}
		$query = "Select SUM(cost) as total_cost from wp_order_stats where ".$where;
		//echo $query."<br />";
		$total_cost = $wpdb->get_var($query);
		if($total_cost !=""){
			$total_cost;
		}else{
			$total_cost = 0;
		}
		//return wc_price($total_cost);
		return "$".number_format($total_cost,2);
		
	}
	public static function get_stats( $per_page = 20, $page_number = 1 ) {
	global $wpdb;
	return array();
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


class SP_Plugin_Sale_Graph {

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
			'Home',
			'Home',
			'shopadoc_admin_cap',
			'home_performance',
			[ $this, 'plugin_settings_page' ],0
		);
		//add_submenu_page( 'performance_auction', 'Home', 'Home','shopadoc_admin_cap', 'admin.php?page=home_performance');

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
  #toplevel_page_admin-page-home_performance a{
			background: #2271b1 !important;
			color: #fff !important;
			}
			#toplevel_page_admin-page-home_performance a:after {
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
				/*body{overflow:hidden !important;}*/
				th,td{font-size:16px !important;}
				.error,.notice{display:none;}
				.tooltip{top:10000000px;}
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
          <form method="post" id="filterForm">
			<script src="/wp-content/plugins/WP_Sale_Graph/lib/raphael.js"></script> 
            <!-- <script src="scale.raphael.js"></script> --> 
            <script src="/wp-content/plugins/WP_Sale_Graph/example/color.jquery.js"></script> 
            <script src="/wp-content/plugins/WP_Sale_Graph/us-map.js"></script> 
            <!--<script src="/wp-content/plugins/WP_Sale_Graph/sorttable.js"></script> -->
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
								//console.log('Mouse over: ' + data.name);
								var title = data.name;
								var html_tooltip = jQuery("#tooltip-html-"+data.name).html();
								jQuery('<p class="tooltip"></p>').html(html_tooltip).appendTo('body').fadeIn('slow');
								/*var mousex = event.pageX + 10; //Get X coordinates
								var mousey = event.pageY - 130; //Get Y coordinates
								jQuery('.tooltip').css({ top: mousey, left: mousex })*/
								jQuery('#map').mousemove(function(e) {
									var mousex = e.pageX + 20; //Get X coordinates
									//var mousey = e.pageY - 130; //Get Y coordinates
									var mousey = e.pageY; //Get Y coordinates
									if(title=='WA'){
										var mousey = e.pageY; //Get Y coordinates
									}
									jQuery('.tooltip').css({ top: mousey, left: mousex })
								});
							},
							'mouseout': function(event, data) {
								jQuery('.tooltip').remove();
							},
							'click' : function(event, data) {
										jQuery.ajax({	
										url:'<?php echo  home_url('/ajax.php');?>',
										type:'POST',
										data:{'mode':'getLink','state_name':data.name},
										beforeSend: function() {},
										complete: function() {
										},
										success:function (data){
											if(data!=""){
												window.location.replace("<?php echo home_url();?>/wp-admin/admin.php?page=state_performance&map_id="+data);
											}
										}
								});
							}
						});
						jQuery('#map svg').attr('width','100%');
						jQuery('#map svg').attr('height','100%');
						//jQuery("#dest").addSortWidget();
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
						/*$.datepicker.setDefaults({
						  showOn: "both",
						  buttonImageOnly: true,
						  buttonImage: "calendar.png",
						});*/
						$.datepicker.setDefaults({
							dateFormat: "mm/dd/y"
						});
						$( 'input[name="mishaDateFrom"], input[name="mishaDateTo"]' ).datepicker();
						from.on( 'change', function() {
							to.datepicker( 'option', 'minDate', from.val());
							if(to.val()!=""){
								$("#filterForm").submit();
							}
						});
						to.on( 'change', function() {
							from.datepicker( 'option', 'maxDate', to.val());
							if(from.val()!=""){
								$("#filterForm").submit();
							}
						});
						var img_asc="/wp-content/plugins/WP_Sale_Graph/img/desc_sort.gif";	
						var img_desc="/wp-content/plugins/WP_Sale_Graph/img/asc_sort.gif";	
						var img_nosort="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif";
						$('#dest th').append('<img src="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif" class="sorttable_img" style="cursor: pointer; margin-left: 10px;">');
						$('th').click(function(){
							var table = $(this).parents('table').eq(0);
							//var index_text = $(this).index().replace("$","");
				
							var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
							this.asc = !this.asc;
							$('th').find("img").attr('src',img_nosort);
							if (!this.asc){rows = rows.reverse();$(this).find("img").attr('src',img_asc);}else{$(this).find("img").attr('src',img_desc);}
							for (var i = 0; i < rows.length; i++){table.append(rows[i])}
						})
						function comparer(index) {
							return function(a, b) {
								var valA = getCellValue(a, index), valB = getCellValue(b, index)
								return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
							}
						}
						function getCellValue(row, index){ 
							var text = $(row).children('td').eq(index).text();
							return text.replace(",","").replace("$","");
						}
						$(document).on('click', '.print', function(e) {
							e.preventDefault();
							window.print();
						});
					});
			</script>
            <style type="text/css"> 
			th {
				cursor: pointer;
			}
			table.wp-list-table img {
				margin: 1px -1px !important;
				position: relative;
				right: -5px;
			}
			svg:not(:root) {
					position:fixed !important;
					top:auto !important;
					left:auto !important;
					width:60%;
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
					line-height: 0;/* text-align: left; */
				}
				table.scroll tbody {
					height: 630px;
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
					width: 30% !important;
				}
				table.scroll tbody td:first-child, table.scroll thead th:first-child{
					width: 40% !important;
				}
				table.scroll tbody td:last-child, table.scroll thead th:last-child {
					border-right: none;
				}
				table.scroll thead tr td, table.scroll thead tr th {
					color: #000;
					background: #fff;
				}
				#map {
					width:70%;
					height: 630px;
					float:left;
					margin-top:35px;
				}
				#details {
					width: 30%;
					float: left;
					background: #fff;
					padding: 1%;
					margin-top: -20px;
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
					border-radius:3px;
				}
               
                input[name="mishaDateFrom"], input[name="mishaDateTo"] {
                    line-height: 28px;
                    height: 28px;
                    margin: 0;
                    width: 45%;
					font-weight:bold;
					font-size:12px;
					background:url(calendar.png);background-repeat:no-repeat;background-size:20px 20px;background-position:right;
                }
				.pink{color:#DB2D69 !important;}
				.blue{color:#479CE9 !important;}
				.amt{font-size:20px;}
				.wrap {margin: 10px 0 0 2px;}
				.heading{float:left;width:100%;font-weight:bold;padding-bottom:10px;}
				.heading .heading1{float:left;font-size:14px;line-height:24px;}
				.heading .heading2{float:left;font-size:18px;line-height:24px;}
				.font-12{font-size:12px !important;}
				.font-18{font-size:18px !important;}
				.font-13{font-size:13px !important;}
				table.scroll tbody td, table.scroll thead th{font-size:12px !important;}
				.top_panel{background:#F6F7F7 0% 0% no-repeat padding-box;float:left;width:100%;padding:0 10px 10px;margin-top:10px;}
             </style>
            <div id="map" ></div>
            <div id="details" >
              <div id="filter_date" >
                <div class="heading"><span class="heading1">TOTAL REVENUE -</span><span class="heading2">&nbsp;NATIONWIDE</span> <a href="javascript:" class="not_print print" style="float:right;"><img src="/wp-content/plugins/WP_GA/print.png" align="right" title="print" width="20px" class="print_icon"/></a></div>
                <?php 
					$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
					$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
					global $US_state;
					$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
				?>
                <input type="text" name="mishaDateFrom" placeholder="Start Date" value="<?php echo $from;?>" autocomplete="off">
                &nbsp;-&nbsp;
                <input type="text" name="mishaDateTo" placeholder="End Date" value="<?php echo $to;?>" autocomplete="off"/>
                <!--<input type="submit" name="submit[filter]" value="Filter" class="btn btn-primary btn-filter" style="padding: 5px 9px !important;" />-->
                <?php 
					$client_total_sale = Sale_Graph_List::getSaleCustomFunc('','','seller',$from,$to);
					$dentist_total_sale = Sale_Graph_List::getSaleCustomFunc('','','dentist',$from,$to);
				?>
                <div class="top_panel">
                <table width="100%" style="margin-top:10px;">
                <thead>
                  <tr>
                    <th class="font-13">Client</th>
                    <th class="font-13">Dentist</th>
                  </tr>
                </thead>
                <tbody >
                	<tr>
                    	<td ><strong class="pink amt font-18"><?php echo $client_total_sale;?></strong></td>
                    	<td><strong class="blue amt font-18"><?php echo $dentist_total_sale;?></strong></td>
                    </tr>
                </tbody>
                </table>
                </div>
              </div>
              <table width="100%" class="scroll wp-list-table widefat fixed striped table-view-list posts" id="dest" style="width:100%;height:630px;overflow:scroll;">
                <thead>
                  <tr>
                    <th align="left"><strong>State</strong></th>
                    <th style="padding-left:0;"><strong>Client</strong></th>
                    <th style="padding-left:0;"><strong>Dentist</strong></th>
                  </tr>
                </thead>
                <tbody >
                  <?php unset($US_state['District of Columbia']);foreach($US_state as $k=>$v){
								//echo "'".$v."',";
								//$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
								$client_sale = Sale_Graph_List::getSaleCustomFunc($v,'','seller',$from,$to);
								$dentist_sale = Sale_Graph_List::getSaleCustomFunc($v,'','dentist',$from,$to);
								echo '<span id="tooltip-html-'.strtoupper($v).'" style="display:none;"><p><strong>TOTAL REVENUE - <span class="State_name">'.strtoupper($k).'</span></strong></p><p><p style="float:left;width:55%;"><strong>Client</strong><br /><strong class="pink amt">'.$client_sale.'</strong></p><p style="float:left;width:45%;"><strong>Dentist</strong><br /><strong class="blue amt">'.$dentist_sale.'</strong></p></p></span>';
								echo '<tr id="over-'.$v.'">';
									echo '<td align="left"><strong><span class="underline_amt">'.$k.'</span></strong></td>';
									echo '<td align="left" class="pink"><strong>'.$client_sale.'</strong></td>';
									echo '<td align="left" class="blue"><strong>'.$dentist_sale.'</strong></td>';
								echo '</tr>';
								//echo $v."==".$client_sale_listing."==".$dentist_sale_reg."<br />";
								?>
                  				<script>
											 jQuery('#over-<?php echo $v;?>').mouseover(function() {
													jQuery('#map').usmap('trigger', '<?php echo $v;?>', 'mouseover', event);
											  }).mouseout(function() {
													jQuery('#map').usmap('trigger', '<?php echo $v;?>', 'mouseout', event);
											  });
									</script>
                  <?php 
							}?>
                </tbody>
              </table>
            </div>
          </form>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>
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

		$this->stats_obj = new Sale_Graph_List();
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
	SP_Plugin_Sale_Graph::get_instance();
} );
