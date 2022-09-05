<?php
require(dirname(__FILE__) . '/wp-load.php');
ini_set('max_execution_time', 18000);
error_reporting(0);
global $wpdb;
$aInfo = array();
$input = strtolower($_GET['input'] );
$len = strlen($input);
if($_REQUEST['field']=='city'){
	$items = $wpdb->get_results('SELECT CityName FROM wp_cities WHERE CityName LIKE "'.$input.'%" ORDER BY CityName asc limit 100');
	$num_rows = $wpdb->num_rows;
	if ($num_rows > 0){
		$aUsers = array();
		foreach($items as $row){
			//$aUsers[] .= $row->CityName.', '.str_replace("US-","",$row->ProvinceCode);
			$aUsers[] .= $row->CityName;
		}
	}
	
}elseif($_REQUEST['field']=='service'){
	$aUsers = array("healthy mouth cleaning (prophy)","scaling & root planing (SRP)","partial denture - upper or lower flexible acrylic","partial dentures - upper & lower flexible acrylic","partial denture - upper or lower + metal frame","partial dentures - upper & lower + metal frames","interim partial denture (“flipper”) acrylic upper or lower","complete denture - upper or lower","complete dentures - upper & lower","*implant abutment supported complete upper or lower denture","*implant abutment supported complete upper & lower dentures","*abutments & denture(s) only","implant cylinder (1) surgical placement","implant cylinders (2) surgical placement","implant cylinders (4) surgical placement","implant cylinders (5) surgical placement","implant cylinders (6) surgical placement","implant cylinders (8) surgical placement","implant cylinders (10) surgical placement","implant cylinders (11) surgical placement","implant cylinders (12) surgical placement","in-office teeth whitening","lab veneer upper front (1)","lab veneers upper front (2)","lab veneers upper front (4)","lab veneers upper front (6)","lab veneers upper front (8)","removal 1 tooth","removal 1 tooth + bone graft","removal 4 wisdom teeth + sedation","removal all teeth","removal all teeth + sedation","removal all teeth + immediate upper & lower complete dentures","removal all teeth + sedation + immediate upper & lower dentures","composite filling","core buildup","core buildup + crown (ceramic)","crown (ceramic)","root canal therapy","post & core + crown (ceramic)","bridge (ceramic)","custom abutment + crown for implant","custom abutments + bridge for implants","traditional ortho (bands brackets wires)","Six Month Smiles® (brackets wires)","Invisalign® / (clear aligners)","upper or lower clear retainer","upper & lower clear retainers","horseshoe bite guard appliance","NTI-TSS® TMJ bruxism appliance","snoring & obstructive sleep apnea (OSA) appliance","reline upper complete denture","mini-implants placement w/ retrofit lower complete denture","retrofit upper or lower denture to implants by locator attachments","*locators & retrofit service only");
	
}else{}
	$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
	$aResults = array();
	$count = 0;
	if ($len){
		for ($i=0;$i<count($aUsers);$i++){
			// had to use utf_decode, here
			// not necessary if the results are coming from mysql
			//
			if (strtolower(substr(utf8_decode($aUsers[$i]),0,$len)) == $input){	
					$count++;
					$aResults[] = array( "id"=>($i+1) ,"value"=>htmlspecialchars($aUsers[$i]), "info"=>htmlspecialchars($aInfo[$i]));
				}
			if ($limit && $count==$limit)
				break;
		}
	}
	
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header ("Pragma: no-cache"); // HTTP/1.0
	if (isset($_REQUEST['json'])){
		header("Content-Type: application/json");
		echo "{\"results\": [";
		$arr = array();
		for ($i=0;$i<count($aResults);$i++){
			$arr[] = "{\"id\": \"".$aResults[$i]['id']."\", \"value\": \"".$aResults[$i]['value']."\", \"info\": \"".$aResults[$i]['info']."\"}";
		}
		echo implode(", ", $arr);
		echo "]}";
	}
	exit;
?>