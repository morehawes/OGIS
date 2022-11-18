<?php

function map_first_pre_get_posts($query){
  if(is_archive() && is_tax('waymark_collection')) {
		$query->set('order', 'ASC' );	  
  }
}
add_action('pre_get_posts', 'map_first_pre_get_posts'); 

function map_first_customize_register($wp_customize) {
	$wp_customize->add_section('map_first_section', array(
		'title' => 'Map First',
	));	

	$wp_customize->add_setting('map_first_header_logo', array(
		'default' => '',
		'transport' => 'refresh',
		'sanitize_callback' => 'esc_url_raw'
	));

	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'map_first_header_logo', array(
		'label' => 'Header Logo Image',
		'section' => 'map_first_section'
	)));
 
	$wp_customize->add_setting('map_first_header_background', array(
		'default' => '#212529',
		'transport' => 'refresh',
		'sanitize_callback' => 'sanitize_hex_color'
	));
 
	$wp_customize->add_control('map_first_header_background', array(
		'label' => 'Header Background Colour',
		'section' => 'map_first_section',
		'type' => 'color'
	));

	$wp_customize->add_setting('map_first_header_color', array(
		'default' => '#fff',
		'transport' => 'refresh',
		'sanitize_callback' => 'sanitize_hex_color'
	));
 
	$wp_customize->add_control('map_first_header_color', array(
		'label' => 'Header Text Colour',
		'section' => 'map_first_section',
		'type' => 'color'
	));	 
	 
	$wp_customize->add_setting('map_first_link_color', array(
		'default' => '#0056b3',
		'transport' => 'refresh',
		'sanitize_callback' => 'sanitize_hex_color'
	));
 
	$wp_customize->add_control('map_first_link_color', array(
		'label' => 'Content Link Colour',
		'section' => 'map_first_section',
		'type' => 'color'
	));		 
}
add_action('customize_register', 'map_first_customize_register' );

function map_first_widget() {
	register_sidebar(array(
		'name' => 'Map First Header Content',
		'id' => 'ogis-theme-header-content',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="text-white">',
		'after_title' => '</h4>'
	));
}
add_action('widgets_init', 'map_first_widget');

function map_first_menu() {
  register_nav_menu('ogis-theme-header-nav', 'Map First Header Nav');
}
add_action('init', 'map_first_menu');

function map_first_breadcrumb() {
	global $post; 
	

	if($post) {
		$crumbs = array();
		
		switch(true) {
			// ========= Home  =========
			case is_front_page() :
	
				break;
	
			// ========= Taxonomy  =========
			case is_archive() :
				$tax = get_queried_object();		
				if(isset($tax->name)) {
					$crumbs[] = array(
						'prepend' => ' Maps / ',
						'text' => $tax->name
					);			
				}
				
				break;
				
			// ========= Map  =========
			case is_singular('waymark_map') :
				//Collections
				$collections = wp_get_post_terms($post->ID, 'waymark_collection');
	
				//If we have Collections
				if(sizeof($collections)) {
					//Sort by count ASC
					usort($collections, function($a, $b) {
						return $a->count < $b->count;
					});
				
					$crumbs[] = array(
						'prepend' => ' Map / ',
						'text' => $collections[0]->name,
						'link' => get_term_link($collections[0])
					);
				}
	
				//Map Title
				$crumbs[] = array(
					'text' => get_the_title()
				);
				
				break;
			// ========= Posts / Pages  =========
			case is_page() :
			case is_single() :
				//Ancestors?
				if($post->post_parent) {
					$ancestors = get_post_ancestors($post);
					$ancestors = array_reverse($ancestors);
					
					foreach($ancestors as $ancestor) {
						$crumbs[] = array(
							'text' => get_the_title($ancestor),
							'link' => get_permalink($ancestor)
						);
					}				
				}			
				
				//Map Title
				$crumbs[] = array(
					'text' => get_the_title()
				);	
						
				break;	
		}	
		
		//Do we have something to display?
		if(sizeof($crumbs)) {
			echo '<span vocab="https://schema.org/" typeof="BreadcrumbList">' . "\n";
	
			$position = 1;
			foreach($crumbs as $crumb) {
				$prepend = isset($crumb['prepend']) ? $crumb['prepend'] : '';			
				$append = isset($crumb['append']) ? $crumb['append'] : '';
				$text = isset($crumb['text']) ? $crumb['text'] : '';
			
				echo '	/' . "\n";
				echo '	<span property="itemListElement" typeof="ListItem">' . "\n";
				echo $prepend;
				if(isset($crumb['link'])) {
					echo '		<a property="item" typeof="WebPage" href="' . $crumb['link'] . '">' . "\n";
				}
				echo '			<span property="name">' . $text . $append . '</span>' . "\n";
				if(isset($crumb['link'])) {
					echo '		</a>' . "\n";
				}
				echo $append;
				echo '		<meta property="position" content="' . $position . '" />' . "\n";
				echo '	</span>' . "\n";
			}	
	
			echo '	</span>' . "\n";
		}
		
		//Children?
		if(is_front_page()) {
			$post_children = get_posts(array(
				'post_type' => 'page',
				'post_parent' => '0'
			));		
		} else {
			$post_children = get_posts(array(
				'post_type' => $post->post_type,
				'post_parent' => $post->ID
			));
		}
			
		if($post_children) {
			echo '	/' . "\n";
			echo '<select id="breadcrumb-nav">' . "\n";
			echo '	<option selected="selected">...</option>' . "\n";
			foreach($post_children as $child) {
				//No home
				if($child->ID != get_option('page_on_front')) {
					echo '	<option value="' . get_permalink($child->ID) . '">' . $child->post_title . '</option>' . "\n";					
				}
			}
			echo '</select>' . "\n";
		}
		
	}
}

function map_first_enqueue_assets() {
	//CSS
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', false, '4.3.1');
	wp_enqueue_style('ogis-theme', get_template_directory_uri() . '/assets/css/ogis-theme.css');
	
	//JS
	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '4.3.1', true);
	wp_enqueue_script('ogis-theme', get_template_directory_uri() . '/assets/js/ogis-theme.js', array('bootstrap'), false, true);	
}
add_action('wp_enqueue_scripts', 'map_first_enqueue_assets');


//Thanks! https://njengah.com/wordpress-custom-pagination/
function map_first_archive_pagination() {
	global $wp_query;

	$big = 9999999;
	$pagination_list = paginate_links(array(
	 'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
	 'format' => '?paged=%#%',
	 'current' => max(1, get_query_var('paged')),
	 'total' => $wp_query->max_num_pages,
	 'type' => 'array',
	 'prev_text' => '&lt;',
	 'next_text' => '&gt;'	 
	));
	
	//If we have a list
	if(isset($pagination_list) && sizeof($pagination_list)) {
  	//Format for Bootstrap
	  echo '<ul class="pagination justify-content-center mt-5">' . "\n";

		$count = 0;
	  foreach($pagination_list as $link) {
	  	$class = 'page-item';
	  	if(strpos($link, '<span') !== false) {
	  		$class .= ' active';
	  	}
		  echo '<li class="' . $class . '">' . "\n";	  

	  	$bad = array(
	  		'page-numbers'	
	  	);
	  	$good = array(
	  		'page-link'		  	
	  	);	  	
	  	echo str_replace($bad, $good, $link);	  	

		  echo '</li>' . "\n";	  
	  }

	  echo '</ul>' . "\n";	  
	}
}

function map_first_single_pagination() {
	//Format for Bootstrap
	echo '<ul class="pagination justify-content-center mt-5">' . "\n";
	
  echo '	<li class="page-item">' . "\n";	  
  $link = get_previous_post_link('%link', '&lt; %title', true, '', 'waymark_collection');
  echo str_replace('<a', '<a class="page-link"', $link);
  echo '	</li>' . "\n";	  

  echo '	<li class="page-item">' . "\n";	    
  $link = get_next_post_link('%link', '%title &gt;', true, '', 'waymark_collection');
  echo str_replace('<a', '<a class="page-link"', $link); 
  echo '	</li>' . "\n";	
  
  echo '</ul>' . "\n";	    
}

function map_first_sort_overlay_features($overlay_features = []) {		
	$overlays = array(
		'lines' => array(),
		'markers' => array(),
		'shapes' => array()		
	);

	//Feature Collection
	if(sizeof($overlay_features)) {	
		foreach($overlay_features as $feature) {
			if(isset($feature['geometry']['type'])) {
				switch($feature['geometry']['type']) {
					case 'Point' :
						//Waymark_Helper::debug($feature);
				
						//Circle
						if(isset($feature['properties']['radius'])) {
							$overlays['shapes'][$feature['properties']['type']][] = $feature;										
						//Marker
						} else {
							$overlays['markers'][$feature['properties']['type']][] = $feature;					
						}

						break;

					case 'LineString' :
					case 'MultiLineString' :
						$overlays['lines'][$feature['properties']['type']][] = $feature;

						break;
					case 'Polygon' :
						$overlays['shapes'][$feature['properties']['type']][] = $feature;
				
						break;
				}
			}
		}	
	}	

	return $overlays;	
}

function map_first_overlay_description($overlay, $overlay_kind = 'marker') {
	//Check
	if(! isset($overlay['properties']['type']) || ! isset($overlay['properties']['description'])) {
		return;
	}

	//Original Description
	$description = $overlay['properties']['description'];

	return $description;
}

function map_first_overlay_content($overlay, $overlay_kind = 'marker') {
	$out = '';

	if(isset($overlay['properties']['type'])) {
		//Add Class
		$add_class = '';
		if(! empty($overlay['properties']['image_large_url'])) {
			$add_class .= ' ogis-theme-has-image';
		}

		//By Kind
		switch($overlay_kind) {
			case 'marker' :
				$out .= '<div class="ogis-theme-overlay ogis-theme-overlay-marker' . $add_class . '" data-marker_latlng="[' . $overlay['geometry']['coordinates'][1] . ',' . $overlay['geometry']['coordinates'][0] . ']">' . "\n";
			
				//Type Label
	// 			$out .= '	<div class="ogis-theme-overlay-type">' . $overlay['type'] . '</div>' . "\n";

				//Image
				if(isset($overlay['properties']['image_large_url'])) {
					$out .= '	<div class="ogis-theme-overlay-image" style="background-image:url(' . $overlay['properties']['image_large_url'] . ')"></div>' . "\n";
				}
						
				//Title
				if(isset($overlay['properties']['title'])) {		
					$out .= '	<div class="ogis-theme-overlay-title">' . $overlay['properties']['title'] . '</div>' . "\n";
				}
			
				//Description			
				$out .= '	<div class="ogis-theme-overlay-description">' . "\n";
				if(isset($overlay['properties']['description'])) {					
					switch($overlay['properties']['type']) {
						default :
							$out .= map_first_overlay_description($overlay, $overlay_kind);

							break;
					}
				}
				$out .= '	</div>' . "\n";

				$out .= '</div>' . "\n";		
			
				break;

			case 'line' :
			
// 				Waymark_Helper::debug($overlay['geometry']['coordinates'][0]);
			
				if(isset($overlay['properties']['post_id'])) {
					$out .= '<a href="' . get_permalink($overlay['properties']['post_id']) . '" class="ogis-theme-overlay ogis-theme-overlay-line' . $add_class . '" data-line_start_latlng="[' . $overlay['geometry']['coordinates'][0][1] . ',' . $overlay['geometry']['coordinates'][0][0] . ']">' . "\n";
				} else {
					$out .= '<div class="ogis-theme-overlay ogis-theme-overlay-line' . $add_class . '" data-line_start_latlng="[' . $overlay['geometry']['coordinates'][0][1] . ',' . $overlay['geometry']['coordinates'][0][0] . ']">' . "\n";				
				}
			
				//Image
				if(isset($overlay['properties']['image_large_url'])) {
					$out .= '	<div class="ogis-theme-overlay-image" style="background-image:url(' . $overlay['properties']['image_large_url'] . ')"></div>' . "\n";
				}
						
				//Title
				if(isset($overlay['properties']['title'])) {		
					$out .= '	<div class="ogis-theme-overlay-title">' . $overlay['properties']['title'] . '</div>' . "\n";
				}
			
				//Description			
				$out .= '	<div class="ogis-theme-overlay-description">' . "\n";
				if(isset($overlay['properties']['description'])) {					
					switch($overlay['properties']['type']) {
						default :
							$out .= map_first_overlay_description($overlay, $overlay_kind);

							break;
					}
				}
				$out .= '	</div>' . "\n";

				if(isset($overlay['properties']['post_id'])) {
					$out .= '	</a>' . "\n";
				} else {
					$out .= '	</div>' . "\n";				
				}
			
				break;				
		}		
	}
	
	return $out;
}

function map_first_sidebar($overlays, $display_kinds = ['markers', 'lines'], $display_types = false) {
	$out = '<!-- START Overlay Sidebar -->';
	$out .= '<div class="ogis-theme-sidebar waymark-accordion-container">';
	
	foreach($display_kinds as $overlay_kind) {

		if(! isset($overlays[$overlay_kind])) {
			continue;
		}

		$overlay_group = $overlays[$overlay_kind];
	
		switch($overlay_kind) {
			case 'markers' :
		
				//Peaks first!
				if(array_key_exists('peak', $overlay_group)) {
 					$peaks = $overlay_group['peak'];
// 					unset($overlay_group['photo']);					
 					$overlay_group = array_merge(['peak' => $peaks], $overlay_group);
				}
				
				$out .= '<div class="ogis-theme-markers">' . "\n";
				foreach($overlay_group as $marker_type => $markers) {
					if(isset($display_types['markers']) && is_array($display_types['markers']) && ! in_array($marker_type, $display_types['markers'])) {
						continue;
					}
				
					$out .= '	<div data-type_key="' . $marker_type . '" class="ogis-theme-overlay-type ogis-theme-overlay-type-' . $marker_type . ' waymark-accordion-group">' . "\n";				
					$out .= '		<legend>' . $marker_type . ' (' . sizeof($markers) . ')</legend>' . "\n";
					$out .= '		<div class="waymark-accordion-group-content">' . "\n";
					foreach($markers as $marker) {					
						$out .= map_first_overlay_content($marker, 'marker');
					}
					$out .= '		</div>' . "\n";
					$out .= '	</div>' . "\n";
				}
				$out .= '</div>' . "\n";
												
				break;	
			case 'lines' :
				$out .= '<div class="ogis-theme-lines">' . "\n";
				foreach($overlay_group as $line_type => $lines) {
					if(isset($display_types['lines']) && is_array($display_types['lines']) && ! in_array($line_type, $display_types['lines'])) {
						continue;
					}

					$out .= '	<div data-type_key="' . $line_type . '" class="ogis-theme-overlay-type ogis-theme-overlay-type-' . $line_type . ' waymark-accordion-group">' . "\n";				
					$out .= '		<legend>' . $line_type . ' (' . sizeof($lines) . ')</legend>' . "\n";
					$out .= '		<div class="waymark-accordion-group-content">' . "\n";
					foreach($lines as $line) {
						$out .= map_first_overlay_content($line, 'line');
					}
					$out .= '		</div>' . "\n";
					$out .= '	</div>' . "\n";
				}
				$out .= '</div>' . "\n";
												
				break;												
		}
	}

	$out .= '</div>';
	$out .= '<!-- END Overlay Sidebar -->';
	
	return $out;
}

add_filter('do_shortcode_tag', function($output, $tag, $attr) {
	global $post;
	
	if($tag != 'Waymark') {
		return $output;
	}

	$sidebar = '';

	$WP_Object = get_queried_object();

	//Single Map
	if(is_a($WP_Object, 'WP_Post') && is_single()) {
		$map_meta = Waymark_Helper::get_meta($post->ID);
		
		if(isset($map_meta['waymark_map_data']) || isset($map_meta['waymark_query_data'])) {
			$map_data = Waymark_GeoJSON::string_to_feature_collection($map_meta['waymark_map_data']);
			$query_data = Waymark_GeoJSON::string_to_feature_collection($map_meta['waymark_query_data']);

			//Map Data
			$map_data_features = [];
			if(isset($map_data['features']) && is_array($map_data['features'])) {
				$map_data_features = $map_data['features'];
			}
	
			//Query Data
			$query_data_features = [];
			if(isset($query_data['features']) && is_array($query_data['features'])) {
				$query_data_features = $query_data['features'];
			}	
	
			$overlay_features = array_merge($map_data_features, $query_data_features);
			
			$overlay_features = map_first_sort_overlay_features($overlay_features);

			$sidebar = map_first_sidebar($overlay_features, ['markers', 'lines']);	
		}		

	//Collection
	} elseif(is_a($WP_Object, 'WP_Term') && isset($attr['collection_id'])) {
		$overlay_features = array();

		//Create Collection object
		$Collection = new Waymark_Collection($WP_Object->term_id);	
		foreach($Collection->Maps as $Map) {
			//Map Data
			$map_data_features = [];	
			if(isset($Map->data['map_data'])) {
				$map_data = Waymark_GeoJSON::string_to_feature_collection($Map->data['map_data']);
				
				//Add Map ID to properties
				$map_data = Waymark_GeoJSON::update_feature_property($map_data, 'post_id', $Map->post_id);
				
				if(isset($map_data['features']) && is_array($map_data['features'])) {
					$map_data_features = $map_data['features'];
				}				
			}

			//Query Data
			$query_data_features = [];
			if(isset($Map->data['query_data'])) {
				$query_data = Waymark_GeoJSON::string_to_feature_collection($Map->data['query_data']);
		
				if(isset($query_data['features']) && is_array($query_data['features'])) {
					$query_data_features = $query_data['features'];
				}	
			}			

			$overlay_features = array_merge_recursive($overlay_features, map_first_sort_overlay_features(array_merge($map_data_features, $query_data_features)));
		}

		$sidebar = map_first_sidebar($overlay_features, ['lines', 'markers'], ['markers' => ['photo', 'peak']]);	
	}
	
	if($sidebar) {
		$out = '<!-- START Map First Sidebar Wrapper -->' . "\n";
		$out .= '<div class="ogis-theme-sidebar-wrapper">' . "\n";
		$out .= $output;
		$out .= $sidebar;	
		$out .= '</div>' . "\n";
		$out .= '<!-- END Map First Sidebar Wrapper -->' . "\n";	
		
		return $out;
	} else {
		return $output;
	}
	
	return $out;
}, 10, 3);