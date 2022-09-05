<?php 
$file = 'usa-al.svg';
header('Content-type: image/svg+xml');
header("Content-Disposition: attachment; filename=".$file);
	$content = file_get_contents('http://woocommerce-401140-1262735.cloudwaysapps.com/wp-content/plugins/mapsvg/maps/not-calibrated/usa/counties/usa-al.svg');
	$dom = new domDocument;
	//$dom->loadHTML($content);
	$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	//$paths = $dom->getElementsByTagName('svg');
	$nodes = $dom->getElementsByTagName("path");
	while ($nodes->length > 0) {
    $node = $nodes->item(0);
    remove_node($node);
}
function remove_node(&$node) {
    $pnode = $node->parentNode;
    remove_children($node);
    $pnode->removeChild($node);
}

function remove_children(&$node) {
    while ($node->firstChild) {
        while ($node->firstChild->firstChild) {
            remove_children($node->firstChild);
        }

        $node->removeChild($node->firstChild);
    }
}
	/*foreach ($paths as $path) {
		echo $path->getAttribute('id');
	}*/
	$content = $dom->saveHTML();
	//echo $content;
	$content2 = file_get_contents('usa-al.svg');
	$dom2 = new domDocument;
	//$dom->loadHTML($content);
	$dom2->loadHTML($content2, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	//$paths = $dom->getElementsByTagName('svg');
	$nodes2 = $dom->getElementsByTagName("svg");
	while ($nodes2->length > 0) {
    	$node2 = $nodes2->item(0);
	 	$node2->removeChild();
	}
	$content2 = $dom2->saveHTML();
	echo $content2;
?>