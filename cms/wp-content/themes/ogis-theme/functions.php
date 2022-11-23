<?php

//Wp Head
remove_action('wp_head', 'feed_links_extra', 3 );
remove_action('wp_head', 'feed_links' );
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link');
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_oembed_add_discovery_links');

//Strip from Front-End
add_action( 'the_title', function() {
	if(! is_admin()) {
		return '';
	}
});

//Enqueue
add_action('wp_enqueue_scripts', function() {
	//CSS
	wp_enqueue_style('ogis-theme', get_template_directory_uri() . '/assets/css/ogis-theme.css');
	
	//JS
	wp_enqueue_script('ogis-theme', get_template_directory_uri() . '/assets/js/ogis-theme.js', 'jquery', false, true);	
});


// add_filter('do_shortcode_tag', function($output, $tag, $attr) {
// 	global $post;
// 	
// 	if($tag != 'Waymark') {
// 		return $output;
// 	}
// 
// 	$sidebar = '';
// 
// 	$WP_Object = get_queried_object();
// 
// 	//Single Map
// 	if(is_a($WP_Object, 'WP_Post') && is_single()) {
// 		$map_meta = Waymark_Helper::get_meta($post->ID);
// 		
// 		if(isset($map_meta['waymark_map_data']) || isset($map_meta['waymark_query_data'])) {
// 			$map_data = Waymark_GeoJSON::string_to_feature_collection($map_meta['waymark_map_data']);
// 			$query_data = Waymark_GeoJSON::string_to_feature_collection($map_meta['waymark_query_data']);
// 
// 			//Map Data
// 			$map_data_features = [];
// 			if(isset($map_data['features']) && is_array($map_data['features'])) {
// 				$map_data_features = $map_data['features'];
// 			}
// 	
// 			//Query Data
// 			$query_data_features = [];
// 			if(isset($query_data['features']) && is_array($query_data['features'])) {
// 				$query_data_features = $query_data['features'];
// 			}	
// 	
// 			$overlay_features = array_merge($map_data_features, $query_data_features);
// 			
// 			$overlay_features = map_first_sort_overlay_features($overlay_features);
// 
// 			$sidebar = map_first_sidebar($overlay_features, ['markers', 'lines']);	
// 		}		
// 
// 	//Collection
// 	} elseif(is_a($WP_Object, 'WP_Term') && isset($attr['collection_id'])) {
// 		$overlay_features = array();
// 
// 		//Create Collection object
// 		$Collection = new Waymark_Collection($WP_Object->term_id);	
// 		foreach($Collection->Maps as $Map) {
// 			//Map Data
// 			$map_data_features = [];	
// 			if(isset($Map->data['map_data'])) {
// 				$map_data = Waymark_GeoJSON::string_to_feature_collection($Map->data['map_data']);
// 				
// 				//Add Map ID to properties
// 				$map_data = Waymark_GeoJSON::update_feature_property($map_data, 'post_id', $Map->post_id);
// 				
// 				if(isset($map_data['features']) && is_array($map_data['features'])) {
// 					$map_data_features = $map_data['features'];
// 				}				
// 			}
// 
// 			//Query Data
// 			$query_data_features = [];
// 			if(isset($Map->data['query_data'])) {
// 				$query_data = Waymark_GeoJSON::string_to_feature_collection($Map->data['query_data']);
// 		
// 				if(isset($query_data['features']) && is_array($query_data['features'])) {
// 					$query_data_features = $query_data['features'];
// 				}	
// 			}			
// 
// 			$overlay_features = array_merge_recursive($overlay_features, map_first_sort_overlay_features(array_merge($map_data_features, $query_data_features)));
// 		}
// 
// 		$sidebar = map_first_sidebar($overlay_features, ['lines', 'markers'], ['markers' => ['photo', 'peak']]);	
// 	}
// 	
// 	if($sidebar) {
// 		$out = '<!-- START Map First Sidebar Wrapper -->' . "\n";
// 		$out .= '<div class="ogis-theme-sidebar-wrapper">' . "\n";
// 		$out .= $output;
// 		$out .= $sidebar;	
// 		$out .= '</div>' . "\n";
// 		$out .= '<!-- END Map First Sidebar Wrapper -->' . "\n";	
// 		
// 		return $out;
// 	} else {
// 		return $output;
// 	}
// 	
// 	return $out;
// }, 10, 3);

/**
 * =====================================
 * ============ Submission =============
 * =====================================
 */

add_filter('wp_footer', function($args) { 
	//Bounds
	$editor_bounds = Waymark_Helper::country_code_to_bounds();
	if($editor_bounds) {
		Waymark_JS::add_call('
			//Random Country
			if(typeof Waymark_Map_Editor.map == "object") {
				Waymark_Map_Editor.map.fitBounds([
					[' . $editor_bounds[1] . ', ' . $editor_bounds[0] . '],
					[' . $editor_bounds[3] . ', ' . $editor_bounds[2] . ']
				]);			
			}
		');
	}
});

/**
 * =====================================
 * ============== CRON =================
 * =====================================
 */

//Add Cron Schedule
add_filter('cron_schedules', function($schedules) { 
	$schedules['daily'] = [
		//Day
		'interval' => 86400,
		'display' => esc_html__('Daily')
	];
	
	return $schedules;
});

//Create Cron Hook
add_action('ogis_cron_daily_hook', 'ogis_cron_daily_hook');

//Schedule
if(! wp_next_scheduled('ogis_cron_daily_hook')) {
	wp_schedule_event(time(), 'daily', 'ogis_cron_daily_hook');
}

//Unschedule on deactivation
register_deactivation_hook(__FILE__, function() {
	$timestamp = wp_next_scheduled('ogis_cron_daily_hook');
	
	wp_unschedule_event($timestamp, 'ogis_cron_daily_hook');
});

function ogis_cron_daily_hook() {
	global $wpdb;

	//Get Guest attachment & waymark_map posts older than 24 hours
	$query = "
		SELECT
			ID, post_type
		FROM 
			{$wpdb->posts}
		WHERE
			post_type IN ('attachment', 'waymark_map')
		AND 
			post_author = '0'
		AND
			#Stale after 24 hours
			TIMESTAMPDIFF(second, post_date, CURDATE()) / 3600.0 >= 24		
	";
	$results = $wpdb->get_results($query);

	//Each post
	foreach($results as $p) {
		//By type
		switch($p->post_type) {
			case 'attachment' :
				$data[] = $p;
			
				//Delete ("Force")
				wp_delete_attachment($p->ID, true);
			
				break;
			default :
				$data[] = $p;
				
				//Delete ("Force")
				wp_delete_post($p->ID, true);

				break;
		}				
	}
}