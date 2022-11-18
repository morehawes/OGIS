function ogis_setup_home() {
	var body = jQuery('body.home').first();

	if(! body.length) {
		return;
	}
	
	var waymark_container = jQuery('#waymark-map');
	var Waymark_Instance = waymark_container.data('Waymark');

	if(typeof Waymark_Instance !== 'object') {
		return;
	}	
	
// 	waymark_container.addClass('waymark-map-fullscreen');
	
	console.log(waymark_container);	
	
	waymark_container.css({
		'position': 'absolute',
		'top': '0',
		'left': '0',				
		'width': '100%',
		'border': '1px solid red'
	});
}

function ogis_setup_sidebar() {
	var waymark_container = jQuery('.waymark-map').first();

	if(typeof waymark_container !== 'object') {
		return;
	}
	waymark_container.addClass('ogis-theme-sidebar-active');
	
	var waymark_shortcode_container = waymark_container.parent('.waymark-shortcode');
	waymark_shortcode_container.addClass('ogis-theme-sidebar-active');

	var Waymark_Instance = waymark_container.data('Waymark');
	if(typeof Waymark_Instance !== 'object') {
		return;
	}

	var sidebar = jQuery('.ogis-theme-sidebar').first();
	var container = jQuery('.waymark-shortcode.waymark-container').first();
	
	if(! sidebar.length || ! container.length) {
		return;
	}
	//sidebar.remove();

	/* 
		 =========================== 
		 ========== SETUP ==========
		 ===========================	
	*/
	
// 	var sidebar_wrap = sidebar.parents('.ogis-theme-sidebar-wrapper');
	sidebar.css('height', waymark_shortcode_container.height() + 'px');
	

// 	jQuery('.ogis-theme-overlay').each(function() {
// 		var overlay_width = jQuery(this).outerWidth();
// 		
// 		//Make Overlay Square
// 		jQuery(this).css('height', overlay_width);
// 		
// 		jQuery('.ogis-theme-overlay-image', jQuery(this)).each(function() {
// 			jQuery(this).css({
// 				'width': overlay_width,
// 				'height': overlay_width,
// 				'maxWidth': overlay_width,
// 				'maxHeight': overlay_width								
// 			});
// 		});
// 	});

	/* 
		 =========================== 
		 ========= MARKERS =========
		 ===========================	
	*/
	
	jQuery('.ogis-theme-markers .ogis-theme-overlay-type', sidebar).each(function() {
		var type_container = jQuery(this);
		var type_key = type_container.data('type_key');

		if(typeof type_key !== 'string') {
			return;
		}

		if(typeof Waymark_Instance.marker_sub_groups[type_key] !== 'object') {
			console.log(Waymark_Instance);
			console.log(type_key);
		}

		//Show This Marker Type only
		type_container.hover(
			//On
			function() {
				var type_container = jQuery(this);
	
				//Iterate
				for(key in Waymark_Instance.marker_sub_groups) {
					//Show this type
					if(key == type_container.data('type_key')) {		
						var marker_group = Waymark_Instance.marker_sub_groups[key];

						Waymark_Instance.map.addLayer(marker_group);
					//Hide others
					} else {
						Waymark_Instance.map.removeLayer(Waymark_Instance.marker_sub_groups[key]);				
					}
				}
			},
			//Off
			function() {
				//Show All
				for(key in Waymark_Instance.marker_sub_groups) {
					var marker_group = Waymark_Instance.marker_sub_groups[key];

					Waymark_Instance.map.addLayer(marker_group);
				}
			}			
		);

		//Each Type
		for(i in Waymark_Instance.config.marker_types) {
			if(type_key === Waymark_Instance.make_key(Waymark_Instance.config.marker_types[i]['marker_title'])) {
				var type = Waymark_Instance.config.marker_types[i];
				var icon_data = Waymark_Instance.build_icon_data(type);
		
				// === Legend ===
				var legend = jQuery('legend', type_container).first();
		
				//Title
				var type_count = legend.html().replace(type_key, '');
				legend.html(icon_data.html + '&nbsp;' + type.marker_title + type_count);			
				legend.css({
					'background' : type.marker_colour,
					'color' : type.icon_colour
				});
			};
		}

		//Each Marker
		var markers = jQuery('.ogis-theme-overlay-marker', type_container);
		markers.each(function() {
			var marker = jQuery(this);

			var marker_latlng = marker.data('marker_latlng');

			//Sync Sidebar Markers
			Waymark_Instance.marker_sub_groups[type_key].eachLayer(function(layer) {
				//Valid location
				if(typeof layer._latlng === 'object') {
					//Closer than a meter
					if(typeof layer._latlng.distanceTo == 'function' && layer._latlng.distanceTo(marker_latlng) < 1) {
						var jquery_marker = jQuery(layer.getElement());						

						marker.data({
							'leaflet_layer': layer,
							'width_px' : parseInt(jquery_marker.css('width').replace('px', '')),
							'height_px' : parseInt(jquery_marker.css('height').replace('px', '')),								
						});
					}
				}
			});

			//Click			
			marker.on('click', function() {
				var marker = jQuery(this);

				var markers = jQuery('.ogis-theme-overlay-marker', type_container);
				markers.each(function() {
					jQuery(this).removeClass('ogis-theme-active');				
				});

				if(typeof marker.data('leaflet_layer') === 'object') {
					var layer = marker.data('leaflet_layer');		
					Waymark_Instance.map.setView(layer.getLatLng(), 15);
					
					marker.addClass('ogis-theme-active');					
				}										
			});

			marker.hover(
				//On
				function() {
					var marker = jQuery(this);
									
					if(typeof marker.data('leaflet_layer') === 'object') {
						var layer = marker.data('leaflet_layer');					
						var jquery_marker = jQuery(layer.getElement());
				
						if(! jquery_marker.hasClass('ogis-theme-active')) {
							jquery_marker.addClass('ogis-theme-active');
		
							jquery_marker.css({
								'width' : marker.data('width_px') * 1.25 + 'px',
								'height' : marker.data('height_px') * 1.25  + 'px'							
							});									
						}
					}
				},
		
				//Off
				function() {
					var marker = jQuery(this);

					if(typeof marker.data('leaflet_layer') === 'object') {
						var layer = marker.data('leaflet_layer');					
						var jquery_marker = jQuery(layer.getElement());
				
						if(jquery_marker.hasClass('ogis-theme-active')) {
							jquery_marker.removeClass('ogis-theme-active');
		
							jquery_marker.css({
								'width' : marker.data('width_px') + 'px',
								'height' : marker.data('height_px')  + 'px'							
							});									
						}
					}
				}	
			);
		});
	});	
	
	//Iterate all Leaflet *** Markers ***
	for(key in Waymark_Instance.marker_sub_groups) {
		Waymark_Instance.marker_sub_groups[key].eachLayer(function(layer) {
			//On Click
			layer.on('click', function(e) {		
				//Iterate Sidebar
				jQuery('.ogis-theme-sidebar .ogis-theme-overlay-marker').each(function() {
					var marker = jQuery(this);							
					var type_key = marker.parents('.ogis-theme-overlay-type').data('type_key');							

					//Match
					if(typeof marker.data('leaflet_layer') === 'object' && marker.data('leaflet_layer') === e.target) {
						//Exclude these
						if(type_key == 'toilet' || type_key == 'drinkingwater') {					
							return false;
						}
					
						//Hide others
						jQuery('.ogis-theme-sidebar .ogis-theme-overlay-type .waymark-accordion-group-content').each(function() {
							jQuery(this).hide();
						});
						
						marker.addClass('ogis-theme-active');
						
						//Show this
						marker.parents('.waymark-accordion-group-content').show();
					
						//Scroll To Marker
						marker.get(0).scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
					} else {
						marker.removeClass('ogis-theme-active');					
					}
				});
			});
		});	
	}

	/* 
		 =========================== 
		 ========== LINES ==========
		 ===========================	
	*/

	//Each Line Type
	jQuery('.ogis-theme-lines .ogis-theme-overlay-type', sidebar).each(function() {
		var type_container = jQuery(this);
		var type_key = type_container.data('type_key');

		if(typeof type_key !== 'string') {
			return;
		}

		if(typeof Waymark_Instance.line_sub_groups[type_key] !== 'object') {
			console.log(Waymark_Instance);
			console.log(type_key);
		}

		//Show This Line Type only
		type_container.hover(
			//On
			function() {
				var type_container = jQuery(this);
	
				//Iterate
				for(key in Waymark_Instance.line_sub_groups) {
					//Show this type
					if(key == type_container.data('type_key')) {		
						var line_group = Waymark_Instance.line_sub_groups[key];

						Waymark_Instance.map.addLayer(line_group);
					//Hide others
					} else {
						Waymark_Instance.map.removeLayer(Waymark_Instance.line_sub_groups[key]);				
					}
				}
			},
			//Off
			function() {
				//Show All
				for(key in Waymark_Instance.line_sub_groups) {
					var line_group = Waymark_Instance.line_sub_groups[key];

					Waymark_Instance.map.addLayer(line_group);
				}
			}			
		);

		//Each Type
		for(i in Waymark_Instance.config.line_types) {
			if(type_key === Waymark_Instance.make_key(Waymark_Instance.config.line_types[i]['line_title'])) {
				var type = Waymark_Instance.config.line_types[i];
	
				// === Legend ===
				var legend = jQuery('legend', type_container).first();
		
				//Title
				var type_count = legend.html().replace(type_key, '');
				legend.html(type.line_title + type_count);			
				legend.css({
					'background' : type.line_colour
				});
			};
		}

		//Each Line
		var lines = jQuery('.ogis-theme-overlay-line', type_container);
		lines.each(function() {
			var line = jQuery(this);

			var line_start_latlng = line.data('line_start_latlng');


			//Sync Sidebar Lines
			Waymark_Instance.line_sub_groups[type_key].eachLayer(function(layer) {
				//Valid location
				if(typeof layer._latlngs === 'object') {
				
					//
				
					//Closer than a meter
					if(typeof layer._latlng !== 'undefined' && layer._latlngs[0].distanceTo(line_start_latlng) < 1) {
// 						var jquery_line = jQuery(layer.getElement());						

						line.data({
							'leaflet_layer': layer
						});
					}
				}
			});

			//Click			
			line.on('click', function(e) {
// 				e.preventDefault();
			
				var line = jQuery(this);

				var lines = jQuery('.ogis-theme-overlay-line', type_container);
				lines.each(function() {
					jQuery(this).removeClass('ogis-theme-active');				
				});

				if(typeof line.data('leaflet_layer') === 'object') {
					var layer = line.data('leaflet_layer');		
					Waymark_Instance.map.fitBounds(layer.getBounds());
					
					line.addClass('ogis-theme-active');					
				}	
				
// 				return false;									
			});

			line.hover(
				//On
				function() {
					var line = jQuery(this);			
					
					if(typeof line.data('leaflet_layer') === 'object') {
						var layer = line.data('leaflet_layer');					
						
						//Elevation
						if(typeof Waymark_Instance.elevation_control === 'object') {					
							//Clear Map layer
							Waymark_Instance.elevation_control.clear();	
							if(typeof Waymark_Instance.elevation_control.layer !== 'undefined') {
								Waymark_Instance.elevation_control.layer.removeFrom(Waymark_Instance.map);
							}
							
							//Add this Line
							Waymark.elevation_control.loadData(layer.feature);	
						}
					}
				},
		
				//Off
				function() {
					var line = jQuery(this);

					if(typeof line.data('leaflet_layer') === 'object') {
// 						var layer = marker.data('leaflet_layer');					
// 						var jquery_marker = jQuery(layer.getElement());
// 				
// 						if(jquery_marker.hasClass('ogis-theme-active')) {
// 							jquery_marker.removeClass('ogis-theme-active');
// 		
// 							jquery_marker.css({
// 								'width' : marker.data('width_px') + 'px',
// 								'height' : marker.data('height_px')  + 'px'							
// 							});									
// 						}
					}
				}	
			);
		});				
	});	

	//Iterate all Leaflet *** Lines ***
	for(key in Waymark_Instance.line_sub_groups) {
		Waymark_Instance.line_sub_groups[key].eachLayer(function(layer) {
			//On Click
			layer.on('click', function(e) {
				//Iterate Sidebar
				jQuery('.ogis-theme-sidebar .ogis-theme-overlay-line').each(function() {
					var line = jQuery(this);

					//Match
					if(typeof line.data('leaflet_layer') === 'object' && line.data('leaflet_layer') === e.target) {
						//Hide others
						jQuery('.ogis-theme-sidebar .ogis-theme-overlay-type').each(function() {
							jQuery(this).removeClass('waymark-active');
							
							jQuery('.waymark-accordion-group-content', jQuery(this)).hide();
						});
						
						line.addClass('ogis-theme-active');
						
						//Show this
						line.parents('.ogis-theme-overlay-type').each(function() {
							jQuery(this).addClass('waymark-active');
							
							jQuery('.waymark-accordion-group-content', jQuery(this)).show();
						});
											
						//Scroll To Marker
						line.get(0).scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
					} else {
						line.removeClass('ogis-theme-active');					
					}
				});
			});
		});	
	}
	
	//container.append(sidebar);
}

function ogis_setup_breadcrumbs() {
	var nav = jQuery('select#breadcrumb-nav');
	
	if(! nav.length) {
		return;
	}

	nav.change(function() {
		document.location.href = jQuery(this).val();
	});

	var body = jQuery('body').first();
	
	switch(true) {
		//Single Map Page
		case body.hasClass('single-waymark_map') :
			var content = jQuery('.type-waymark_map').first();

			jQuery('table', content).each(function() {
				var table = jQuery(this);
				
				table.addClass('table table-striped');
			});
			
			break;
		
		//Collection Page
		case body.hasClass('tax-waymark_collection') :
			break;		
	}
}

jQuery('document').ready(function() {
	setTimeout(function() {
		ogis_setup_breadcrumbs();
		ogis_setup_sidebar();
		ogis_setup_home();

		//waymark_setup_accordions();
	}, 1000);	
});