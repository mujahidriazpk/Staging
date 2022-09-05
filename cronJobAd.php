<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
function change_weight_old(){
		global $wpdb;
		$weights = get_option('advads-ad-weights', array() );
		$weights_array = array();
		//print_r($weights);
		foreach($weights as $key => $row){
			arsort($row);
			$keys = array_keys($row);
			//echo $keys[1]."==".$row[$keys[1]]."<br />";
			$weights_array[$key][$keys[0]] = 1;
			unset($weights[$key][$keys[0]]);
			$i = 10;
			$j = 1;
			foreach($row as $row_inside){
				$weights_array[$key][$keys[$j]] = $i;
				$i--;
				$j++;
				
			}
		}
		//print_r($weights_array);
		update_option('advads-ad-weights', $weights_array );
}
function change_weight($new_value){
	$option_name = 'current_rotation';
	if ( get_option( $option_name ) !== false ) {
		update_option( $option_name, $new_value );
	}else{
		$deprecated = null;
		$autoload = 'yes';
		add_option( $option_name, $new_value, $deprecated, $autoload );
	}
}
//change_weight();
for($i=1; $i <= 10 ; $i++){
	change_weight($i);
	sleep(6);
}
?>