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
// add_action( 'the_title', function() {
// 	if(! is_admin()) {
// 		return '';
// 	}
// });

//Enqueue
add_action('wp_enqueue_scripts', function() {
	//CSS
	wp_enqueue_style('ogis-theme', get_template_directory_uri() . '/assets/css/ogis-theme.css');
	
	//JS
	wp_enqueue_script('ogis-theme', get_template_directory_uri() . '/assets/js/ogis-theme.js', 'jquery', false, true);	
});


add_filter('do_shortcode_tag', function($output, $tag, $attr) {
	global $post;
	
	if($tag != 'Waymark') {
		return $output;
	}

	$sidebar = '';

	$WP_Object = get_queried_object();

	//Only Add *very* specific Waymark Shortcodes...
	//Waymark_Helper::debug($attr);

	if(! array_key_exists('map_hash', $attr)) {
		return '';
	}

/*
	//Single Map
	if(is_a($WP_Object, 'WP_Post') && is_single()) {

	//Collection
	} elseif(is_a($WP_Object, 'WP_Term') && isset($attr['collection_id'])) {

	}
*/
	
	return $output;
}, 10, 3);

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