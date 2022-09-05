<?php 
$file = $_GET['file'];
header('Content-type: image/svg+xml');
header("Content-Disposition: attachment; filename=".$file);
	$content = file_get_contents($file);
	$dom = new domDocument;
	//$dom->loadHTML($content);
	$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	$paths = $dom->getElementsByTagName('path');
	foreach ($paths as $path) {
		$path->setAttribute('id',str_replace("sm_state_","",$path->getAttribute('class')));
	}
	$content = $dom->saveHTML();
	echo $content;
?>