<?php
/*
Plugin Name: WP_AD_Stats
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( !class_exists( 'WP_Post_Stats' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class AD_Stats_List extends WP_List_Table {

  /** Class constructor */
  public function __construct() {

    parent::__construct( [
      'singular' => __( 'Analytics', 'sp' ), //singular name of the listed records
      'plural' => __( 'Analytics', 'sp' ), //plural name of the listed records
      'ajax' => false //does this table support ajax?
    ] );

  }
  public static function get_dates_of_quarter( $quarter = 'current', $year = null, $format = 'Y-m-d' ) {

    if ( !is_int( $year ) ) {
      $year = ( new DateTime )->format( 'Y' );
    }
    $current_quarter = ceil( ( new DateTime )->format( 'n' ) / 3 );
    switch ( strtolower( $quarter ) ) {
      case 'this':
      case 'current':
        $quarter = ceil( ( new DateTime )->format( 'n' ) / 3 );
        break;

      case 'previous':
        $year = ( new DateTime )->format( 'Y' );
        if ( $current_quarter == 1 ) {
          $quarter = 4;
          $year--;
        } else {
          $quarter = $current_quarter - 1;
        }
        break;

      case 'first':
        $quarter = 1;
        break;

      case 'last':
        $quarter = 4;
        break;

      default:
        $quarter = ( !is_int( $quarter ) || $quarter < 1 || $quarter > 4 ) ? $current_quarter : $quarter;
        break;
    }
    if ( $quarter === 'this' ) {
      $quarter = ceil( ( new DateTime )->format( 'n' ) / 3 );
    }
    $start = new DateTime( $year . '-' . ( 3 * $quarter - 2 ) . '-1' );
    $end = new DateTime( $year . '-' . ( 3 * $quarter ) . '-' . ( $quarter == 1 || $quarter == 4 ? 31 : 30 ) . '' );

    return array(
      'start' => $format ? $start->format( $format ) : $start,
      'end' => $format ? $end->format( $format ) : $end,
    );
  }
  /**
   * Initializes an Analytics Reporting API V4 service object.
   *
   * @return An authorized Analytics Reporting API V4 service object.
   */


  /**
   * Queries the Analytics Reporting API V4.
   *
   * @param service An authorized Analytics Reporting API V4 service object.
   * @return The Analytics Reporting API V4 response.
   */
  public static function getReport( $analytics, $start, $end, $period ) {}


  public static function get_stats( $per_page = 20, $page_number = 1 ) {
    global $wpdb;
    //$result_user = count_users();
    $arg_client = array( 'role' => 'seller' );
    $arg_dentist = array( 'role' => 'customer' );
    if ( isset( $_POST[ 'user_city' ] ) && $_POST[ 'user_city' ] != "" ) {
      $arg_client[ 'meta_query' ] = array(
        //'relation' => 'OR',
        array(
          'key' => 'client_city',
          'value' => $_POST[ 'user_city' ],
          'compare' => 'LIKE'
        ) );
      $arg_dentist[ 'meta_query' ] = array(
        //'relation' => 'OR',
        array(
          'key' => 'dentist_office_city',
          'value' => $_POST[ 'user_city' ],
          'compare' => 'LIKE'
        ),
        /*array(
        	'key'     => 'dentist_home_city',
        	'value'   => $_POST['user_city'],
        	'compare' => 'LIKE'
        ),*/
      );
      $result[ 0 ][ 'user_city' ] = $_POST[ 'user_city' ];
      $result[ 1 ][ 'user_city' ] = $_POST[ 'user_city' ];
    }

    if ( isset( $_POST[ 'user_state' ] ) && $_POST[ 'user_state' ] != "" ) {
      $arg_client[ 'meta_query' ][] = array( array( 'key' => 'client_state', 'value' => $_POST[ 'user_state' ], 'compare' => 'LIKE' ) );
      $arg_client[ 'meta_query' ][ 'relation' ] = 'AND';

      $arg_dentist[ 'meta_query' ][] = array( /*'relation' => 'OR',*/ array( 'key' => 'dentist_office_state', 'value' => $_POST[ 'user_state' ], 'compare' => 'LIKE' ), /*array('key'     => 'dentist_home_state','value'   => $_POST['user_city'],'compare' => 'LIKE')*/ );
      $arg_dentist[ 'meta_query' ][ 'relation' ] = 'AND';

      $result[ 0 ][ 'user_state' ] = $_POST[ 'user_state' ];
      $result[ 1 ][ 'user_state' ] = $_POST[ 'user_state' ];
    }
    if ( isset( $_POST[ 'user_zip_code' ] ) && $_POST[ 'user_zip_code' ] != "" ) {
      $arg_client[ 'meta_query' ][] = array( array( 'key' => 'client_zip_code', 'value' => $_POST[ 'user_zip_code' ], 'compare' => 'LIKE' ) );
      $arg_client[ 'meta_query' ][ 'relation' ] = 'AND';

      $arg_dentist[ 'meta_query' ][] = array( /*'relation' => 'OR',*/ array( 'key' => 'dentist_office_zip_code', 'value' => $_POST[ 'user_zip_code' ], 'compare' => 'LIKE' ) /*,array('key'     => 'dentist_home_zip_code','value'   => $_POST['user_zip_code'],'compare' => 'LIKE')*/ );
      $arg_dentist[ 'meta_query' ][ 'relation' ] = 'AND';

      $result[ 0 ][ 'user_zip_code' ] = $_POST[ 'user_zip_code' ];
      $result[ 1 ][ 'user_zip_code' ] = $_POST[ 'user_zip_code' ];
    }
    //print_r($arg_client);

    // WP_User_Query arguments


    $user_query_client = new WP_User_Query( $arg_client );
    $total_client = $user_query_client->get_total();
    $user_query_dentist = new WP_User_Query( $arg_dentist );
    $total_dentist = $user_query_dentist->get_total();

    $result[ 0 ][ 'user_type' ] = 'Client';
    $result[ 0 ][ 'users' ] = $total_client;
    $result[ 1 ][ 'user_type' ] = 'Dentist';
    $result[ 1 ][ 'users' ] = $total_dentist;
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
      "{$wpdb->prefix}customers", [ 'ID' => $id ], [ '%d' ]
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
        $user_city = ( isset( $item[ $column_name ] ) && $item[ $column_name ] ) ? $item[ $column_name ] : '-';
        return $user_city;
        //return get_post_meta($item['post_id'],'business_name',true);
      case 'user_state':
        $user_state = ( isset( $item[ $column_name ] ) && $item[ $column_name ] ) ? $item[ $column_name ] : '-';
        return $user_state;
      case 'user_zip_code':
        $user_zip_code = ( isset( $item[ $column_name ] ) && $item[ $column_name ] ) ? $item[ $column_name ] : '-';
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

    $title = '<strong>' . $item[ 'name' ] . '</strong>';

    $actions = [
      'delete' => sprintf( '<a href="?page=%s&action=%s&stat=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST[ 'page' ] ), 'delete', absint( $item[ 'ID' ] ), $delete_nonce )
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
      'user_type' => __( 'Type', 'sp' ),
      'users' => __( '# of Users', 'sp' ),
      'user_city' => __( 'City', 'sp' ),
      'user_state' => __( 'State', 'sp' ),
      'user_zip_code' => __( 'Zip', 'sp' ),
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

    $per_page = $this->get_items_per_page( 'stats_per_page', 20 );
    $current_page = $this->get_pagenum();
    $total_items = self::record_count();

    $this->set_pagination_args( [
      'total_items' => $total_items, //WE have to calculate the total number of items
      'per_page' => $per_page //WE have to determine how many items to show on a page
    ] );

    $this->items = self::get_stats( $per_page, $current_page );
  }

  public function process_bulk_action() {

    //Detect when a bulk action is being triggered...
    if ( 'delete' === $this->current_action() ) {

      // In our file that handles the request, verify the nonce.
      $nonce = esc_attr( $_REQUEST[ '_wpnonce' ] );

      if ( !wp_verify_nonce( $nonce, 'sp_delete_stat' ) ) {
        die( 'Go get a life script kiddies' );
      } else {
        self::delete_stat( absint( $_GET[ 'stat' ] ) );

        // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
        // add_query_arg() return the current url
        wp_redirect( esc_url_raw( add_query_arg() ) );
        exit;
      }

    }

    // If the delete bulk action is triggered
    if ( ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'bulk-delete' ) ||
      ( isset( $_POST[ 'action2' ] ) && $_POST[ 'action2' ] == 'bulk-delete' )
    ) {

      $delete_ids = esc_sql( $_POST[ 'bulk-delete' ] );

      // loop over the array of record IDs and delete them
      foreach ( $delete_ids as $id ) {
        self::delete_stat( $id );

      }

      // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
      // add_query_arg() return the current url
      wp_redirect( esc_url_raw( add_query_arg() ) );
      exit;
    }
  }

}


class SP_Plugin_AD_Stats {

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
      'ShopADoc Analytics',
      'ShopADoc Analytics',
      'shopadoc_admin_cap',
      'AD_Stats', [ $this, 'plugin_settings_page' ]
    );
    //add_submenu_page('AD_Stats', 'ShopADoc Analytics', 'ShopADoc Analytics','shopadoc_admin_cap', 'admin.php?page=AD_Stats');

    add_action( "load-$hook", [ $this, 'screen_option' ] );

  }


  /**
   * Plugin settings page
   */
  public function plugin_settings_page() {

    ?>
<div class="wrap">
  <div class="AD_Stats_logo">
	  <h1 class="entry-title" style="float:left;width:33%;">ShopADoc Analytics</h1>
	  <!--<a href="javascript:" class="not_print print" style="float:right;"><img src="<?php echo home_url('/wp-content/plugins/WP_GA/print.png');?>" align="right" title="print" width="20px" class="print_icon"/></a>--></div>
  <!-- <h2>Google Analytics</h2>-->
  <style type="text/css">
	  label{margin: 0 !important;}
  			.AD_Stats_logo{float:left;width:100%;}
  			#toplevel_page_admin-page-AD_Stats a{
				background: #2271b1 !important;
				color: #fff !important;
			}
			#toplevel_page_admin-page-AD_Stats a:after {
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
			
			</style>
  <?php
  if ( isset( $_POST[ "submit" ] ) ) {}
  ?>
  <script type="text/javascript">
			jQuery( function($) {
						$(document).on('click', '.print', function(e) {
							e.preventDefault();
							window.print();
						});
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
						var img_asc="/wp-content/plugins/WP_Sale_Graph/img/desc_sort.gif";	
						var img_desc="/wp-content/plugins/WP_Sale_Graph/img/asc_sort.gif";	
						var img_nosort="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif";
						$('#dest th').append('<img src="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif" class="sorttable_img" style="cursor: pointer; margin-left: 10px;">');
						$('th').click(function(){
							var table = $(this).parents('table').eq(0)
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
						function getCellValue(row, index){ return $(row).children('td').eq(index).text() }
						$(".period").on("change",function(){
							if(jQuery(this).val()=='custom'){
                                to.val('');
                                from.val('')
								/*if(to.val()!="" && from.val()!=""){
									$("#filterForm").submit();
								}*/
							}else{
								$("#filterForm").submit();
							}
							
						});
						$(".period").val('<?php echo $period;?>');
						
					});
					function filterAD_Stats(){  
								var rex = new RegExp(jQuery('#filterText').val());
								if(rex =="/all/"){clearFilter()}else{
									jQuery('.content').hide();
									jQuery('.content').filter(function() {
										return rex.test(jQuery(this).text());
									}).show();
							}
							}
						function filterAD_StatsDate(){  
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
  <style type="text/css"> 
				#filter_date {
					float: left;
					border: solid 3px #000;
					padding: 10px;
					margin-bottom: 10px;
					border-radius:3px;
				}
               
               input[name="mishaDateFrom"], input[name="mishaDateTo"]{
                    line-height: 28px;
                    height: 37px;
                    margin: 0;
                    width: 100%;
					font-weight:bold;
					font-size:12px;
					background:url(calendar.png);background-repeat:no-repeat;background-size:20px 20px;background-position:right;
                }
				.period{
                    line-height: 28px;
                    height: 37px;
                    margin: 0;
                    width: auto;
					font-weight:bold;
					font-size:12px;
					background:url(calendar.png);background-repeat:no-repeat;background-size:20px 20px;background-position:right;
					background:#0A7BE2 !important;
					color:#fff !important;
					font-weight:normal !important;
					border:1px solid #F5F5F5 !important;
					padding:0 5px 0 5px !important;
                }
				#filterText,#filterTextDate{
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
             </style>
  <script type="text/javascript">
		
		</script>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
		<?php /*?>
          <form method="post" style="float:none;margin:0 auto;width:70%;padding:50px 0;" id="filterForm">
            <?php $firstname = ( isset( $_POST['firstname'] ) && $_POST['firstname'] ) ? $_POST['firstname'] : '';?>
            <?php $lastname = ( isset( $_POST['lastname'] ) && $_POST['lastname'] ) ? $_POST['lastname'] : '';?>
            <div style="float:left;width:20%;padding:10px;">
              <select id='filterText' style='display:inline-block' onchange='filterAD_Stats();'>
                <option value="all">- Advertiser -</option>
                <?php echo $userDropdown;?>
              </select>
            </div>
            <!--<div style="float:left;width:20%;padding:10px;">
            	<select id='filterTextDate' style='display:inline-block' onchange='filterAD_StatsDate();'>
                <option value="all">- Run Period -</option>
                  <?php echo $filter_dateDropdown;?>
                </select>
            </div>-->
            <div style="float:left;width:30%;padding:10px;">
              <input type="text" name="mishaDateFrom" placeholder="Start Date" value="<?php echo $from;?>" autocomplete="off">
            </div>
            <div style="float:left;width:2%;padding:15px 0;text-align:right;"> &nbsp;-&nbsp; </div>
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
		  <?php */?>
          <?php
          global $wpdb;
          //echo $wpdb->last_query;
          //$ads = $wpdb->get_results( "SELECT post_id as ID FROM {$wpdb->prefix}postmeta WHERE meta_key = 'ad_user' and meta_value=".get_current_user_id(), OBJECT );
	  	  if(isset($_GET['company'])&& $_GET['company'] !=""){
			 //$args['meta_query'] = array(array('key' => 'ad_user',$_GET['company'],'compare' => 'EQUAL'));
			  $where = '';
			  if(isset($_GET['creative'])&& $_GET['creative'] !=""){
				 $where = ' and post_id = '.$_GET['creative'];
			  }
			  $ads = $wpdb->get_results( "SELECT post_id as ID FROM {$wpdb->prefix}postmeta WHERE meta_key = 'ad_user' and meta_value=".$_GET['company'].$where, OBJECT );
			  $ads_dropdown = $wpdb->get_results( "SELECT post_id as ID FROM {$wpdb->prefix}postmeta WHERE meta_key = 'ad_user' and meta_value=".$_GET['company'], OBJECT );
		  }else{
			
			$args = array(
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby' => 'post_date',
			'post_type' => 'advanced_ads',
			'order' => 'DESC',
			'posts_per_page' => -1,
			//'meta_query' => array(array('key' => 'ad_user',get_current_user_id(),'compare' => 'EQUAL')),
			);
			if(isset($_GET['creative'])&& $_GET['creative'] !=""){
				$args['post__in'] = array($_GET['creative']);
			}
			$query = new WP_Query( $args );
			$ads = $query->posts;
			unset($args['post__in']);
			$query_dropdown = new WP_Query( $args );
			$ads_dropdown = $query_dropdown->posts;
		  }
	  		//$key = array_search($_GET['creative'], (array) $ads);
          ?>
          <style type="text/css">
				.my-listing-custom{
					float: left;
					width: 100%;
				}
				.table > thead > tr > th {
					font-size:17px;
				}
				.table > tbody > tr > td {
					font-size:14px;
				}
				tbody.scroll {
					display:block;
					height:167px;
					overflow:auto;
				}
				thead, tbody tr,tfoot {
					display:table;
					width:100%;
					table-layout:fixed;
				}
				thead,tfoot {
					width: calc( 100% - 1em )
				}
				table {
					width:100%;
				}
				@media (max-width: 448px) {
					.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td{
						padding:8px 2px !important;
					}
				}
			  #select2-company-container,#select2-creative-container{text-align: left;}
			  form{padding-bottom:10px;}
			</style>
          <?php
          $periods = array(
            'today' => __( 'today', 'advanced-ads-tracking' ),
            'yesterday' => __( 'yesterday', 'advanced-ads-tracking' ),
            'last7days' => __( 'last 7 days', 'advanced-ads-tracking' ),
            'thismonth' => __( 'this month', 'advanced-ads-tracking' ),
            'lastmonth' => __( 'last month', 'advanced-ads-tracking' ),
            'thisyear' => __( 'this year', 'advanced-ads-tracking' ),
            'lastyear' => __( 'last year', 'advanced-ads-tracking' ),
            // -TODO this is not fully supported for ranges of more than ~200 points; should be reviewed before 2015-09-01
            'custom' => __( 'custom', 'advanced-ads-tracking' ),
          );

          ?>
		 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="<?php echo home_url();?>/wp-content/plugins/WP_ADS/js/select2.js"></script>
          <script src='/wp-includes/js/jquery/ui/datepicker.min.js?ver=1.11.4'></script>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <table style="width: 100%;">
            <tbody>
              <tr>
                <td align="right"><form method="get" id="period-form" action="<?php echo home_url('/wp-admin/admin.php?page=AD_Stats');?>">
                    <input type="hidden" name="page" value="AD_Stats" />
					<label>
                      <?php _e( 'Filter by Company', 'advanced-ads-tracking' ); ?>
                      :&nbsp;</label>
						<?php 
						$query = new WP_User_Query;
							$query->prepare_query( [
								'role'    => 'advanced_ads_user',
								'orderby' => 'user_nicename',
								'order'   => 'ASC',
							] );
							// Now modify the WHERE clause.
							$query->query_where .= " AND user_status = 0";
							// Then run the SQL command.
							$query->query();
							$users = $query->get_results();
	  					$html1 ='<option value="" title="filter by company">Filter By Company</option>';
	  					foreach($users as $user){
							$deactivate_advertiser = get_user_meta($user->ID, 'deactivate_advertiser',true);
							if($deactivate_advertiser !='Yes'){
								$ad_demo_company_name = get_user_meta( $user->ID, 'ad_demo_company_name', true);
								$selected ='';
								if($_GET['company']==$user->ID){
									$selected = ' selected="selected" ';
								}
								$html1 .= '<option value="'.$user->ID.'" title="'.$ad_demo_company_name.'" '.$selected.'>'.$ad_demo_company_name.'</option>'; 
							}
						}?>
						<select id="company" name="company" style="width: 200px;text-align: left;height: 40px !important;">
								<?php echo $html1;?>
						</select>
					<?php
						if ( count( $ads_dropdown ) > 0 || 1==1) {
							echo '<label>Filter by Creative:&nbsp;</label>';
							$html2 ='<option value="" title="filter by creative">Filter By Creative</option>';
							foreach ( $ads_dropdown as $ad ) {
							  $post_title = $wpdb->get_var( "SELECT post_title FROM {$wpdb->prefix}posts where ID = '" . $ad->ID . "'" );
								$tmp = explode(' ',str_replace('&nbsp;',' ',$post_title));
								$CREATIVE = $tmp[0].' '.$tmp[1].' '.$tmp[2];
								$selected ='';
								if($_GET['creative']==$ad->ID){
									$selected = ' selected="selected" ';
								}
								$html2 .= '<option value="'.$ad->ID.'" title="'.$post_title.'" '.$selected.'>'.$CREATIVE.'</option>'; 
							}
							echo '<select id="creative" name="creative" style="width: 200px;text-align: left;height: 40px !important;">'.$html2.'</select>';
						}
					?>
                    <label>
                      <?php _e( 'Period', 'advanced-ads-tracking' ); ?>
                      :&nbsp;</label>
                    <select id="period" name="period" class="advads-stats-period" onchange="selectDateField();" style="min-height: 40px !important;">
                      <option value="">- Please Select -</option>
                      <?php foreach($periods as $_period_key => $_period) : ?>
                      <option value="<?php echo $_period_key; ?>" <?php if(isset($_GET['period']) && $_GET['period']==$_period_key){ echo 'selected="selected"';}else{}?> ><?php echo $_period; ?></option>
                      <?php endforeach; ?>
                    </select>
                    <?php
                    $period = $_GET[ 'period' ];
                    $from = '';
                    $to = '';
                    if ( $period == 'custom' ) {
                      if ( isset( $_GET[ 'from' ] ) && $_GET[ 'from' ] != "" ) {
                        $from = $_GET[ 'from' ];
                      }
                      if ( isset( $_GET[ 'from' ] ) && $_GET[ 'from' ] != "" ) {
                        $to = $_GET[ 'to' ];
                      }
                    }
                    ?>
                    <input type="text" id="from" name="from" class="advads-stats-from advads-datepicker <?php if($period !== 'custom') echo ' hidden'; ?>" value="<?php echo $from; ?>" size="10" maxlength="10" placeholder="<?php _e( 'from', 'advanced-ads-tracking' ); ?>" style="min-height: 40px !important;"/>
                    <input type="text" id="to" name="to" class="advads-stats-to<?php
                            if($period !== 'custom') echo ' hidden'; ?>" value="<?php
                            echo $to; ?>" size="10" maxlength="10" placeholder="<?php _e( 'to', 'advanced-ads-tracking' ); ?>" style="min-height: 40px !important;"/>
                    <input type="submit" class="button button-primary" value="<?php echo esc_attr( __( 'Load Stats', 'advanced-ads-tracking' ) ); ?>" />
					<script type="text/javascript">
						function formatState (state) {
							
						  /*if (!state.id || state.text =='' || state.text =='Vacate') {
							  if(state.text =='+ Add Creative' || state.text =='Vacate'){
								 	  var state = jQuery('<strong class="blue">' + state.text + '</strong>');
						  			 return state;
								 }else{
										return state.text;
								}
						  }*/
						  var state = jQuery('<span><span class="option_txt">' + state.text.replace("-","<br />") + '</span></span>');
						  return state;
						}
						function formatStateSelect (state) {
							var state = jQuery('<span id="'+state.id+'_view" class="view_main_select">&nbsp;' + state.title + '</span>');
						  			return state;
						}
						jQuery("#company").select2({
							templateResult: formatState,
							//templateSelection: formatStateSelect,							
							placeholder: 'Filter By Company',
							allowClear: true,
						});
						jQuery("#creative").select2({
							templateResult: formatState,
							//templateSelection: formatStateSelect,							
							placeholder: 'Filter By Creative',
							allowClear: true,
						});
					</script>
                  </form></td>
              </tr>
            </tbody>
          </table>
          <script>
	 jQuery( function() {
		jQuery( "#from,#to" ).datepicker({format:"mm/dd/yyyy"});
	  } );
	  function selectDateField(){
		  	var val = jQuery('#period').val();
		  	jQuery('#from,#to').removeClass('hidden');
		  	if(val=='custom'){
				jQuery('#from,#to').removeClass('hidden');
			}else{
				jQuery('#from,#to').addClass('hidden');
				jQuery('#from,#to').val('');
			}
	  }
	function selectAll(){
		jQuery('input[name="check_box_print"]').prop('checked', true);
		jQuery('tr.content').removeClass('not_print');
	}
	jQuery(document).ready(function(){
		jQuery('input[name="check_box_print"]').click(function(){ 
			//alert(jQuery(this).val());
			if(jQuery(this).prop('checked')){
				jQuery('#row_'+jQuery(this).val()).removeClass('not_print');	
			}else{
				jQuery('#row_'+jQuery(this).val()).addClass('not_print');
			}
			//jQuery('input[name="check_box_print"]').not(this).prop('checked', false);
			//var checkedvalue = $.map($('input[name="counting"]:checked'), function(c){return c.value; })
			//alert(checkedvalue);
		});
	});
	</script>
			<style type="text/css">
				@media print {
    				body {
							background-image: url(<?php echo home_url('/wp-content/themes/dokan-child/watermark.png');?>) !important;
							background-repeat: no-repeat !important;
							background-color: #F2F2F2 !important;
							background-position: center 5% !important;
							/*background-attachment: fixed !important;*/
							background-size: auto;
						}
					#menu-management .menu-edit, #menu-settings-column .accordion-container, .comment-ays, .feature-filter, .imgedit-group, .manage-menus, .menu-item-handle, .popular-tags, .stuffbox, .widget-inside, .widget-top, .widgets-holder-wrap, .wp-editor-container, p.popular-tags, table.widefat {
						border: none !important;
						box-shadow: 0 1px 1px rgba(0,0,0,.0) !important;
					}
					th{text-transform: uppercase;}
					.footer_print{width: 100%;background-color: #000;color: #fff;padding: 5px 15px;font-size: 13px;display: block !important;position: absolute;bottom: 0px;}
					.wrap{height: 87vh !important;float:left;width: 100%;}
				}
			</style>
          <div class="dokan-dashboard-wrap nano">
            
            <div class="dokan-dashboard-content dokan-product-listing my-listing-custom">
              <article class="dokan-product-listing-area">
                <div class="table-wrap"> 
                  <!--<table class="table table-striped product-listing-table">-->
                  <div class="actions_div not_print" style="padding-bottom:10px;" ><a href="javascript:" class="not_print print" ><img src="<?php echo home_url('/wp-content/plugins/WP_GA/print.png');?>" title="print" width="20px" class="print_icon"/></a> | <a href="javascript:" onclick="selectAll();">Select All</a></div>
                  <table class="wp-list-table widefat fixed striped table-view-list posts">
                    <thead style="width: 100%;">
                      <tr>
                        <th scope="col" class="hide" align="center"><span>Ad User</span></th>
						<th scope="col" class="not_print" align="center" width="3%"><span>&nbsp;</span></th>
                        <th scope="col" align="left"  style="text-align:left !important;" width="37%"><span>Creative <img src="<?php echo home_url('/wp-content/uploads/2021/10/Darker_Green.jpg');?>" style="height:26px;">&nbsp;&nbsp;&nbsp;Company&nbsp;&nbsp;&nbsp;Run Period</span></th>
                        <th scope="col" align="center" width="20%"><span>Impressions</span></th>
                        <!--<th scope="col" align="center"><span>Unique Impressions</span></th>-->
                        <th scope="col" align="center" width="15%"><span>Clicks</span></th>
                        <!--<th scope="col" align="center"><span>Unique Clicks</span></th>-->
                        <th scope="col" align="center" width="25%"><span>CTR</span></th>
                      </tr>
                    </thead>
                    <tbody id="the-list">
                      <?php

                      $util = Advanced_Ads_Tracking_Util::get_instance();
                      $gmt_offset = 3600 * floatval( get_option( 'gmt_offset', 0 ) );
                      // day start in seconds
                      $now = $util->get_timestamp();
                      $today_start = $now - $now % Advanced_Ads_Tracking_Util::MOD_HOUR;
                      $start = null;
                      $end = null;
                      $start = $today_start;
                      if ( isset( $_GET[ 'period' ] ) ) {
                        // time handling; blog time offset in seconds


                        //$_GET['period'] = 'thisyear';
                        switch ( $_GET[ 'period' ] ) {
                          case 'today':
                            $start = $today_start;
                            break;
                          case 'yesterday':
                            $start = $util->get_timestamp( time() - DAY_IN_SECONDS );
                            $start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
                            $end = $today_start;
                            break;
                          case 'last7days':
                            // last seven full days // -TODO might do last or current week as well
                            $start = $util->get_timestamp( time() - WEEK_IN_SECONDS );
                            $start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
                            break;
                          case 'thismonth':
                            // timestamp from first day of the current month
                            $start = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
                            break;
                          case 'lastmonth':
                            // timestamp from first day of the last month
                            $start = $util->get_timestamp( mktime( 0, 0, 0, date( "m" ) - 1, 1, date( "Y" ) ) );
                            $end = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
                            break;
                          case 'thisyear':
                            // timestamp from first day of the current year
                            $start = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
                            break;
                          case 'lastyear':
                            // timestamp from first day of previous year
                            $start = $util->get_timestamp( mktime( 0, 0, 0, 1, 1, date( 'Y' ) - 1 ) );
                            $end = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
                            break;
                          case 'custom':
                            $start = $util->get_timestamp( strtotime( $_GET[ 'from' ] ) - $gmt_offset );
                            $end = $util->get_timestamp( strtotime( $_GET[ 'to' ] ) - $gmt_offset + ( 24 * 3600 ) );
                            break;
                          case 'default':
                            $start = $today_start;
                            break;
                        }
                      }
                      // TODO limit range (mind groupIncrement/ granularity)
                      // values might be null (not set) or false (error in input)

                      $where = '';
                      if ( isset( $start ) && $start ) {
                        $where .= " AND `timestamp` >= $start";
                      }
                      if ( isset( $end ) && $end ) {
                        if ( $where ) {
                          $where .= " AND `timestamp` < $end";
                        } else {
                          $where .= " AND `timestamp` < $end";
                        }
                      }
                      if ( count( $ads ) > 0 ) {
                        foreach ( $ads as $ad ) {
                          $post_title = $wpdb->get_var( "SELECT post_title FROM {$wpdb->prefix}posts where ID = '" . $ad->ID . "'" );
                          $impression_count = $wpdb->get_var( "SELECT SUM(count) as total FROM `wp_advads_impressions` where ad_id='" . $ad->ID . "'" . $where );
                          $click_count = $wpdb->get_var( "SELECT SUM(count) as total FROM `wp_advads_clicks` where ad_id='" . $ad->ID . "'" . $where );
                          $ctr = ( $click_count / $impression_count ) * 100;
                          $advanced_ads_ad_options = maybe_unserialize( get_post_meta( $ad->ID, 'advanced_ads_ad_options', TRUE ) );
                          $image_id = $advanced_ads_ad_options[ 'output' ][ 'image_id' ];
                          $image = wp_get_attachment_image_src( $image_id, 'thumb' );
                          if ( $image ) {
                            list( $image_url, $image_width, $image_height ) = $image;
                          }
                          if ( $ctr < 1 ) {
                            $ctr = number_format( ( $click_count / $impression_count ) * 100, 7 );
                          } else {
                            $ctr = number_format( ( $click_count / $impression_count ) * 100, 2 );
                          }
                          ?>
                      <tr class="content not_print" id="row_<?php echo $ad->ID;?>">
						  <td class="post-status not_print" style="text-align: left !important;" width="3%"><input type="checkbox" id="check_box_print<?php echo $ad->ID;?>" name="check_box_print" value="<?php echo $ad->ID;?>"/></td>
                        <td class="post-status" style="text-align: left !important;" width="37%"><img src="<?php echo $image_url;?>" alt=""  width="" height="26px" />&nbsp;<?php echo $post_title;?></td>
                        <td class="post-status " width="20%" ><?php echo ($impression_count==0)? '0':$impression_count;?></td>
                        <td class="post-status " width="15%"><?php echo ($click_count==0)? '0' : $click_count;?></td>
                        <td class="post-status " width="25%"><?php echo ( 0 == $click_count )? '0.00 %' : $ctr . ' %';?></td>
                      </tr>
                      <?php } ?>
                      <?php } else { ?>
                      <tr >
						  <td align="left">-</td>
                        <td align="left">-</td>
                        <td align="left">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                      </tr>
                      
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>
<!--<div class="col-md-12 footer_print" style="display: none;">
          <img src="<?php echo home_url('/wp-content/themes/dokan-child/google_by_logo.png');?>" alt="google_data_logo" style="width:195px;margin-left:-8px;">
        </div>
<div class="col-md-12 footer_print" style="display:none;">© 2018-2022 ShopADoc Inc. All Rights Reserved</div>-->
<?php

}

/**
 * Screen options
 */
public function screen_option() {

  $option = 'per_page';
  $args = [
    'label' => 'Stats',
    'default' => 20,
    'option' => 'stats_per_page'
  ];

  add_screen_option( $option, $args );

  $this->stats_obj = new AD_Stats_List();
}


/** Singleton instance */
public static function get_instance() {
  if ( !isset( self::$instance ) ) {
    self::$instance = new self();
  }

  return self::$instance;
}

}


add_action( 'plugins_loaded', function () {
  SP_Plugin_AD_Stats::get_instance();
} );
