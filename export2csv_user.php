<?php	
require(dirname(__FILE__) . '/wp-load.php');
set_time_limit ( 300 );
global $wpdb;
//$result_user = count_users();
$arg_client = array( 'role' => 'seller' );
$arg_dentist = array( 'role' => 'customer' );
if(isset($_GET['user_city']) && $_GET['user_city'] !="" ){
	$arg_client['meta_query'] = array(
							//'relation' => 'OR',
							array(
								'key'     => 'client_city',
								'value'   => $_GET['user_city'],
								'compare' => 'LIKE'
							));
	$arg_dentist['meta_query'] = array(
							//'relation' => 'OR',
							array(
								'key'     => 'dentist_office_city',
								'value'   => $_GET['user_city'],
								'compare' => 'LIKE'
							),
							/*array(
								'key'     => 'dentist_home_city',
								'value'   => $_GET['user_city'],
								'compare' => 'LIKE'
							),*/
						);
	$result[0]['user_city'] = $_GET['user_city'];
	$result[1]['user_city'] = $_GET['user_city'];
}

if(isset($_GET['user_state']) && $_GET['user_state'] !="" ){
	$arg_client['meta_query'][] = array(array('key'     => 'client_state','value'   => $_GET['user_state'],'compare' => 'LIKE'));
	$arg_client['meta_query']['relation'] = 'AND';
	
	$arg_dentist['meta_query'][] = array(/*'relation' => 'OR',*/array('key' => 'dentist_office_state','value'   => $_GET['user_state'],'compare' => 'LIKE'),/*array('key'     => 'dentist_home_state','value'   => $_GET['user_city'],'compare' => 'LIKE')*/);
	$arg_dentist['meta_query']['relation'] = 'AND';
	
	$result[0]['user_state'] = $_GET['user_state'];
	$result[1]['user_state'] = $_GET['user_state'];
}
if(isset($_GET['user_zip_code']) && $_GET['user_zip_code'] !="" ){
	$arg_client['meta_query'][] = array(array('key'     => 'client_zip_code','value'   => $_GET['user_zip_code'],'compare' => 'LIKE'));
	$arg_client['meta_query']['relation'] = 'AND';
	
	$arg_dentist['meta_query'][] = array(/*'relation' => 'OR',*/array('key' => 'dentist_office_zip_code','value'   => $_GET['user_zip_code'],'compare' => 'LIKE')/*,array('key'     => 'dentist_home_zip_code','value'   => $_GET['user_zip_code'],'compare' => 'LIKE')*/);
	$arg_dentist['meta_query']['relation'] = 'AND';
	
	$result[0]['user_zip_code'] = $_GET['user_zip_code'];
	$result[1]['user_zip_code'] = $_GET['user_zip_code'];
}
//print_r($arg_client);

// WP_User_Query arguments


$user_query_client = new WP_User_Query($arg_client);
$total_client = $user_query_client->get_total();

$user_query_dentist = new WP_User_Query($arg_dentist);
$total_dentist = $user_query_dentist->get_total();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Users.csv');
$output = fopen('php://output', 'w');
fputcsv($output, array('Name','Email','City','State','Zip'));
if($total_client > 0){
	$clients = $user_query_client->get_results();
	foreach($clients as $client){
		$seller = get_user_by( 'id',$client->ID);
		$record = array();
		$record['Name'] = $seller->first_name.' '.$seller->last_name; 
		$record['Email'] = $seller->user_email; 
		$record['City'] = $seller->client_city; 
		$record['State'] = $seller->client_state;  
		$record['Zip'] = $seller->client_zip_code; 
		fputcsv($output,$record);
	}
}
if($total_dentist > 0){
	$dentists = $user_query_dentist->get_results();
	//header('Content-Type: text/csv; charset=utf-8');
	//header('Content-Disposition: attachment; filename=Dentist.csv');
	//$output = fopen('php://output', 'w');
	fputcsv($output, array('','','','',''));
	fputcsv($output, array('<b>Dentists</b>','','','',''));
	foreach($dentists as $dentist){
		$customer = get_user_by( 'id',$dentist->ID);
		$record = array();
		$record['Name'] = $customer->first_name.' '.$customer->last_name; 
		$record['Email'] = $customer->user_email; 
		$record['City'] = $customer->dentist_office_city; 
		$record['State'] = $customer->dentist_office_state;  
		$record['Zip'] = $customer->dentist_office_zip_code; 
		fputcsv($output,$record);
	}
}
exit;
?>
