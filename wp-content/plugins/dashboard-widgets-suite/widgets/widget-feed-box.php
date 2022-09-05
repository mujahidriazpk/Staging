<?php // Dashboard Widgets Suite - Feed Box
	
if (!defined('ABSPATH')) exit;

function dashboard_widgets_suite_feed_box() {
	
	$items = dashboard_widgets_suite_feed_box_content();
	
	echo $items;
	
	do_action('dashboard_widgets_suite_feed_box', $items);
	
}

function dashboard_widgets_suite_feed_box_content() {
	
	global $dws_options_feed_box;
	
	$feed_url     = isset($dws_options_feed_box['widget_feed_box_feed'])    ? esc_url(trim($dws_options_feed_box['widget_feed_box_feed'])) : '';
	$feed_limit   = isset($dws_options_feed_box['widget_feed_box_limit'])   ? intval($dws_options_feed_box['widget_feed_box_limit'])       : '';
	$feed_length  = isset($dws_options_feed_box['widget_feed_box_length'])  ? intval($dws_options_feed_box['widget_feed_box_length'])      : '';
	$feed_excerpt = isset($dws_options_feed_box['widget_feed_box_excerpt']) ? boolval($dws_options_feed_box['widget_feed_box_excerpt'])    : '';
	
	$feed = fetch_feed($feed_url);
	
	$feed_max   = false; 
	$feed_items = false;
	
	if (!is_wp_error($feed)) {
		
		$feed_max   = $feed->get_item_quantity($feed_limit);
		$feed_items = $feed->get_items(0, $feed_max);
		
	}
	
	apply_filters('dashboard_widgets_suite_feed_box_data', $feed_items);
	
	$return = '<div id="dws-feed-box" class="dws-dashboard-widget">';
	
	if (!empty($feed_max)) {
		
		$return .= '<div class="dws-feed-box">';
		$return .= '<div class="dws-feed-box-meta">';
		$return .= '<span class="fa fa-rss"></span> <a class="dws-feed-box-site" target="_blank" rel="noopener noreferrer" href="'. $feed->get_permalink() .'">'. $feed->get_title() .'</a> ';
		$return .= '<a class="dws-feed-box-desc" target="_blank" rel="noopener noreferrer" href="'. $feed_url .'">'. $feed->get_description() .'</a>';
		$return .= '</div>';
		$return .= '<ul>';
		
		foreach ($feed_items as $item) {
			
			$return .= '<li>';
			$return .= '<span class="dws-feed-box-title"><a target="_blank" rel="noopener noreferrer" href="'. esc_url($item->get_permalink()) .'">'. esc_attr($item->get_title()) .'</a></span> ';
			$return .= '<span class="dws-feed-box-date">'. date_i18n('j F Y @ g:i a', $item->get_date('U')) .'</span> ';
			
			if ($feed_excerpt) $return .= '<span class="dws-feed-box-excerpt">'. simplepie_shorten($item->get_description(), $feed_length) .'</span>';
			
			$return .= '</li>';
			
		}
		
		$return .= '</ul>';
		$return .= '</div>';
		
	}
	
	$return .= '</div>';
	
	apply_filters('dashboard_widgets_suite_feed_box_output', $return);
	
	return $return;
	
}

function simplepie_shorten($string, $length) {
	
	$suffix = '<span class="dws-feed-box-suffix"> [&hellip;]</span>';
	
	$suffix = apply_filters('dashboard_widgets_suite_feed_box_suffix', $suffix);
	
	$short_desc = trim(str_replace(array("\r","\n", "\t"), ' ', strip_tags($string)));
	
	if (strlen($short_desc) < $length) {
		
		$desc = $short_desc;
		
	} else {
		
		$desc = trim(substr($short_desc, 0, strpos($short_desc, ' ', $length)));
		
	}
	
	$lastchar = substr($desc, -1, 1);
	
	if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?') $suffix = '';
	
	$desc .= $suffix;
	
	return $desc;
	
}
