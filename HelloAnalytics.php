<?php

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

$analytics = initializeAnalytics();

$response = getReport($analytics);
//print_r($response);
printResults($response);


/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics()
{

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';

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
function getReport($analytics) {

  // Replace with your view ID, for example XXXX.
  $VIEW_ID = "218158068";

  // Create the DateRange object.
  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
 /* $dateRange->setStartDate("90daysAgo");
  $dateRange->setEndDate("today");*/
  $dateRange->setStartDate("2021-08-01");
  $dateRange->setEndDate("2021-10-21");

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
  $request->setDimensions(array($ea,$el));
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
function printResults($reports) {
	//print_r($reports);
  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
    $report = $reports[ $reportIndex ];
    $header = $report->getColumnHeader();
    $dimensionHeaders = $header->getDimensions();
    $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
    $rows = $report->getData()->getRows();

    for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
      $row = $rows[ $rowIndex ];
      $dimensions = $row->getDimensions();
      $metrics = $row->getMetrics();
      for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
        print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
      }

      for ($j = 0; $j < count($metrics); $j++) {
        $values = $metrics[$j]->getValues();
        for ($k = 0; $k < count($values); $k++) {
          $entry = $metricHeaders[$k];
          print($entry->getName() . ": " . $values[$k] . "\n");
        }
      }
    }
  }
}
