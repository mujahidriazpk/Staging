<?php
/*
Plugin Name: WP_GA
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Post_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class GA_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Analytics', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Analytics', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}
public static function get_dates_of_quarter($quarter = 'current', $year = null, $format = 'Y-m-d')
{

    if ( !is_int($year) ) {        
       $year = (new DateTime)->format('Y');
    }
    $current_quarter = ceil((new DateTime)->format('n') / 3);
    switch (  strtolower($quarter) ) {
    case 'this':
    case 'current':
       $quarter = ceil((new DateTime)->format('n') / 3);
       break;

    case 'previous':
       $year = (new DateTime)->format('Y');
       if ($current_quarter == 1) {
          $quarter = 4;
          $year--;
        } else {
          $quarter =  $current_quarter - 1;
        }
        break;

    case 'first':
        $quarter = 1;
        break;

    case 'last':
        $quarter = 4;
        break;

    default:
        $quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
        break;
    }
    if ( $quarter === 'this' ) {
        $quarter = ceil((new DateTime)->format('n') / 3);
    }
    $start = new DateTime($year.'-'.(3*$quarter-2).'-1');
    $end = new DateTime($year.'-'.(3*$quarter).'-'.($quarter == 1 || $quarter == 4 ? 31 : 30) .'');

    return array(
        'start' => $format ? $start->format($format) : $start,
        'end' => $format ? $end->format($format) : $end,
    );
}
/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
public static function initializeAnalytics()
{

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = $_SERVER['DOCUMENT_ROOT']. '/service-account-credentials.json';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_AnalyticsReporting($client);
  return $analytics;
}


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
public static function getReport($analytics,$start,$end,$period) {

  // Replace with your view ID, for example XXXX.

  $VIEW_ID = "218158068";
  // Create the DateRange object.
  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
 /* $dateRange->setStartDate("90daysAgo");
  $dateRange->setEndDate("today");*/
 
 if(isset($period) && $period =="custom"){
	}else{
	 $start = date("Y-m-d");
		//$end = date('Y-m-d', strtotime( 'friday this week' ) ).' 23:59:59';
		$end = date('Y-m-d');
		if($period=='yesterday'){
			$start =date('Y-m-d',strtotime("-1 days"));
			$end = date('Y-m-d',strtotime("-1 days"));
		}elseif($period=='7days'){
			$start = date("Y-m-d",strtotime( "monday this week" ));
			$end = date('Y-m-d', strtotime( 'friday this week' ) );
		}elseif($period=='last7days'){
			$start = date("Y-m-d",strtotime( "monday last week" ));
			$end = date('Y-m-d', strtotime( 'friday last week' ) );
		}elseif($period=='lastmonth'){
			$start = date("Y-m-d", strtotime("first day of previous month"));
			$end = date("Y-m-d", strtotime("last day of previous month"));
		}elseif($period=='thisyear'){
			$start = date("Y")."-01-01";
			$end = date("Y")."-12-31";
		}elseif($period=="quarter"){
			$quarter = GA_List::get_dates_of_quarter();
			$start = $quarter['start'];
			$end = $quarter['end'];
		}
	}
 if($start==""){
	  $start = date("Y-m-d");
 }
  if($end==""){
	  $end = date("Y-m-d");
 }
  $dateRange->setStartDate(date("Y-m-d",strtotime($start)));
  $dateRange->setEndDate(date("Y-m-d",strtotime($end)));
  /*$dateRange->setStartDate("today");
  $dateRange->setEndDate("today");*/

  // Create the Metrics object.
  $sessions = new Google_Service_AnalyticsReporting_Metric();
  $sessions->setExpression("ga:sessions");
  $sessions->setAlias("sessions");
  
  $pageviews = new Google_Service_AnalyticsReporting_Metric();
  $pageviews->setExpression("ga:pageviews");
  $pageviews->setAlias("pageviews");
  
  $users = new Google_Service_AnalyticsReporting_Metric();
  $users->setExpression("ga:users");
  $users->setAlias("users");
  

// Create the Metrics object.
$ev = new Google_Service_AnalyticsReporting_Metric();
$ev->setExpression("ga:eventValue");
$ev->setAlias("EventValue");

$tEv = new Google_Service_AnalyticsReporting_Metric();
$tEv->setExpression("ga:totalEvents");
$tEv->setAlias("Total Events");

$uEv = new Google_Service_AnalyticsReporting_Metric();
$uEv->setExpression("ga:uniqueEvents");
$uEv->setAlias("Unique Events");

//analytics.eventCategory:Advanced%20Ads,analytics.eventLabel:%5B1887%5D%20Purple%20D

$avg = new Google_Service_AnalyticsReporting_Metric();
$avg->setExpression("ga:avgEventValue");
$avg->setAlias("Avg Value");

//Create the dimensions
// $sc = new Google_Service_AnalyticsReporting_Dimension();
// $sc->setName("ga:subContinent");


$ec = new Google_Service_AnalyticsReporting_Dimension();
$ec->setName("ga:eventCategory");

$ea = new Google_Service_AnalyticsReporting_Dimension();
$ea->setName("ga:eventAction");

$el = new Google_Service_AnalyticsReporting_Dimension();
$el->setName("ga:eventLabel");

/*$dimensionFilter = new Google_Service_AnalyticsReporting_DimensionFilter();
$dimensionFilter->setDimensionName('ga:eventLabel');
$dimensionFilter->setOperator('EXACT');
$dimensionFilter->setExpressions(array('[1887] Purple D'));*/

  // Create the ReportRequest object.
  // Create the ReportRequest object.
  $request = new Google_Service_AnalyticsReporting_ReportRequest();
  $request->setViewId($VIEW_ID);
  $request->setDateRanges($dateRange);
  //$request->setMetrics(array($sessions, $pageviews, $users,$ev,$tEv,$avg));
  $request->setDimensions(array($el,$ea));
 // $request->setDimensionFilterClauses(array($dimensionFilterClause));
  $request->setMetrics(array($tEv,$uEv));
  $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
  $body->setReportRequests( array( $request) );

  return $analytics->reports->batchGet( $body );
}


/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 */
public static function printResults($reports) {
	//print_r($reports);
	
  $array_date = array();
  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
    $report = $reports[ $reportIndex ];
    $header = $report->getColumnHeader();
    $dimensionHeaders = $header->getDimensions();
    $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
    $rows = $report->getData()->getRows();
	
	$ad_array = array();
    for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
      $row = $rows[ $rowIndex ];
	  //print_r($row);
      $dimensions = $row->getDimensions();
      $metrics = $row->getMetrics();
	  $array_label = array();
      for ($i = 0; $i < count($metrics); $i++) {
        //print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "</br>");
		$values = $metrics[$i]->getValues();
		if($dimensions[1] =='Clicks'  || $dimensions[1] =='Impressions' ){
			//print_r($values);
			//echo $dimensions[0]."==".$dimensions[1]."==".$values[0]."==".$values[1]."<br />";
			//$ad_array[$dimensions[0]] = array();
			//$dimensions_label = str_replace("&nbsp;"," ",htmlspecialchars_decode($dimensions[0]));
			//$dimensions_label = str_replace("-","–",str_replace("&nbsp;"," ",$dimensions[0]));
			$dimensions_label = str_replace("&nbsp;"," ",$dimensions[0]);
			//$dimensions_label = $dimensions[0];
			$tmp = explode(" ",$dimensions_label);
			$date_label = $tmp[count($tmp)-3]." – ".end($tmp);
			array_push($array_date,$date_label);
			$ad_array[$dimensions_label]['date_content'] = $date_label;
			if($dimensions[1]=='Clicks'){
				//$ad_array[$dimensions_label]['AdLabel'] = ( isset($values[0]) && $values[0] ) ? $values[0] : '0';
				$ad_array[$dimensions_label]['Clicks'] = ( isset($values[0]) && $values[0] ) ? $values[0] : '0';
				$ad_array[$dimensions_label]['UClicks'] =  ( isset($values[1]) && $values[1] ) ? $values[1] : '0';
			}
			if($dimensions[1]=='Impressions'){
				$ad_array[$dimensions_label]['Impressions'] =  ( isset($values[0]) && $values[0] ) ? $values[0] : '0';
				$ad_array[$dimensions_label]['UImpressions'] =  ( isset($values[1]) && $values[1] ) ? $values[1] : '0';
			}
		}
		//print_r($values);
      }

      /*for ($j = 0; $j < count($metrics); $j++) {
        $values = $metrics[$j]->getValues();
        for ($k = 0; $k < count($values); $k++) {
          $entry = $metricHeaders[$k];
          //print($entry->getName() . ": " . $values[$k] . "</br>");
        }
      }*/
    }
  }
  $ad_array["filter_date"] = array_unique($array_date);
  return $ad_array;
}
	/**
	 * Retrieve stats data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_UsersFunc( $firstname,$lastname ) {}
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


class SP_Plugin_GA {

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
			'Analytics',
			'Analytics',
			'shopadoc_admin_cap',
			'Analytics',
			[ $this, 'plugin_settings_page' ]
		);
		//add_submenu_page( 'performance_auction', 'Auction #', 'Auction #','shopadoc_admin_cap', 'admin.php?page=GA');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		if(isset($_GET['mode']) && $_GET['mode']=="test"){?>
        <div style="float:left;width:100%;height:100vh;">
            <iframe src="http://staging.shopadoc.com/" frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>
        </div>
		<?php }else{
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
		require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

		$analytics = GA_List::initializeAnalytics();
		
		$response = GA_List::getReport($analytics,$from,$to,$period);
		?>

<div class="wrap">
	<div class="GA_logo"><img src="/wp-content/plugins/WP_GA/Google-Analytics-Logo_web.png" align="left" alt="" title="Google-Analytics-Logo" />
    <a href="javascript:" class="not_print print" style="float:right;"><img src="/wp-content/plugins/WP_GA/print.png" align="right" title="print" width="20px" class="print_icon"/></a><span style="float:right;">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a href="https://analytics.google.com/analytics/web/#/report/content-event-events/a166289038w232260644p218158068/explorer-segmentExplorer.segmentId=analytics.eventLabel&_r.drilldown=analytics.eventCategory:Advanced%20Ads&explorer-table.plotKeys=%5B%5D" class="not_print" style="float:right;" target="_blank">Google Analytics Dashboard</a>
    </div>
 <!-- <h2>Google Analytics</h2>-->
  <style type="text/css">
  			.GA_logo{float:left;width:100%;}
  			#toplevel_page_admin-page-Analytics a{
				background: #2271b1 !important;
				color: #fff !important;
			}
			#toplevel_page_admin-page-Analytics a:after {
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
				if (isset($_POST["submit"])) {}
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
								if(to.val()!="" && from.val()!=""){
									$("#filterForm").submit();
								}
							}else{
								$("#filterForm").submit();
							}
							
						});
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
		$userDropdown .= '<option value="company-'.$user->ID.'" '.$selected.'>'.$ad_demo_company_name.'</option>';
	}
	$results = GA_List::printResults($response);
	//print_r($results['filter_date']);
	$filter_dates = $results['filter_date'];
	$filter_dateDropdown = '';
	foreach($filter_dates as $filter_date){
		$selected ='';
		$filter_dateDropdown .= '<option value="company-'.$filter_date.'">'.$filter_date.'</option>';
	}
	?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <form method="post" style="float:none;margin:0 auto;width:70%;padding:50px 0;" id="filterForm">
            <?php $firstname = ( isset( $_POST['firstname'] ) && $_POST['firstname'] ) ? $_POST['firstname'] : '';?>
            <?php $lastname = ( isset( $_POST['lastname'] ) && $_POST['lastname'] ) ? $_POST['lastname'] : '';?>
         	<div style="float:left;width:20%;padding:10px;">
            	<select id='filterText' style='display:inline-block' onchange='filterGA();'>
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
          <?php 
	
		//print_r($response);
		
		
			 ?>
          <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
              <tr>
              <th scope="col" class="hide" align="center"><span>Ad User</span></th>
                <th scope="col" align="left"  style="text-align:left !important;" width="30%"><span>Creative  <img src="/wp-content/uploads/2021/10/Darker_Green.jpg" style="height:26px;">&nbsp;&nbsp;&nbsp;Company&nbsp;&nbsp;&nbsp;Run Period</span></th>
                <th scope="col" align="center" width="20%"><span>Impressions</span></th>
                <!--<th scope="col" align="center"><span>Unique Impressions</span></th>-->
                <th scope="col" align="center" width="20%"><span>Clicks</span></th>
                <!--<th scope="col" align="center"><span>Unique Clicks</span></th>-->
                <th scope="col" align="center"><span>CTR</span></th>
              </tr>
            </thead>
            <tbody id="the-list">
            <?php if(count($results) > 0){?>
            	<?php foreach($results as $key =>$result){
							if($key!='filter_date'){
							$tmp = explode("]",$key,2);
							$id = str_replace("[","",$tmp[0]);
							$advanced_ads_ad_options = maybe_unserialize(get_post_meta($id, 'advanced_ads_ad_options', true ));
							$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
							$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
							$src = $img_atts[0];
							$ad_title = "<img src='".$src."' style='height:26px;'> &nbsp;".$tmp[1];
							$ctr = 0;
							if($result['Impressions'] >0){
								$ctr  = ($result['Clicks']/ $result['Impressions']) * 100;
								if($ctr < 1){
									$ctr  =  number_format(($result['Clicks'] / $result['Impressions']) * 100,4);
								}else{
									$ctr  = number_format(($result['Clicks']/ $result['Impressions']) * 100,2);
								}
							}
							$ad_user = get_post_meta($id, 'ad_user',true);
							/*https://analytics.google.com/analytics/web/#/report/content-event-events/a166289038w232260644p218158068/_u.date00=20220408&_u.date01=20220408&_r.drilldown=analytics.eventCategory:Advanced%20Ads,analytics.eventLabel:%5B5971%5D%20C%20Client%20D1%2003~2F01~2F22%20%E2%80%93%2012~2F31~2F22&explorer-table.plotKeys=%5B%5D/*/
					?>
                        <tr class="content">
                        	<td align="left" class="hide" >company-<?php echo $ad_user;?></td>
                            <td align="left" class="hide" >company-<?php echo $result['date_content'];?></td>
                            <td align="left" style="text-align:left !important;"><a href="https://analytics.google.com/analytics/web/#/report/content-event-events/a166289038w232260644p218158068/_u.date00=<?php echo date("Ymd",strtotime($from_GA));?>&_u.date01=<?php echo date("Ymd",strtotime($to_GA));?>&_r.drilldown=analytics.eventCategory:Advanced%20Ads,analytics.eventLabel:<?php echo utf8_encode(str_replace("–","&ndash;",str_replace("/","~2F",htmlspecialchars($key))));?>&explorer-table.plotKeys=%5B%5D/" target="_new"><?php echo $ad_title;?></a></td>
                            <td align="center"><?php echo ( isset( $result['Impressions']) && $result['Impressions'] ) ? $result['Impressions']: '0';?></td> 
                            <!--<td align="center"><?php echo ( isset( $result['UImpressions']) && $result['UImpressions'] ) ? $result['UImpressions']: '0';?></td>-->
                            <td align="center"><?php echo ( isset( $result['Clicks']) && $result['Clicks'] ) ? $result['Clicks']: '0';?></td>
                            <!--<td align="center"><?php echo ( isset( $result['UClicks']) && $result['UClicks'] ) ? $result['UClicks']: '0';?></td>-->
                            <td align="center"><?php echo ( 0 == $result['Clicks'] || $ctr==0 )? '0.00 %' : $ctr . ' %';?></td>
                      </tr>
                      <?php }
					  	}?>
                      
          <?php }else{?>
          				<tr >
                        	<td align="left">-</td>
                            <td align="left">-</td>
                            <td align="center">-</td> 
                            <td align="center">-</td>
                            <td align="center">-</td>
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

<?php
		}
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

		$this->stats_obj = new GA_List();
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
	SP_Plugin_GA::get_instance();
} );

