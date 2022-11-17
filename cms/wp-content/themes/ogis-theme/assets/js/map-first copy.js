function map_first_setup_sidebar() {
	var waymark_container = jQuery('.waymark-map').first();

	if(typeof waymark_container !== 'object') {
		return;
	}
	waymark_container.addClass('map-first-sidebar-active');

	var Waymark_Instance = waymark_container.data('Waymark');
	if(typeof Waymark_Instance !== 'object') {
		return;
	}

	var sidebar = jQuery('.map-first-sidebar').first();
	var container = jQuery('.waymark-shortcode.waymark-container').first();
	
	if(! sidebar.length || ! container.length) {
		return;
	}
	
	sidebar.remove();

	//Sidebar
	sidebar.hover(
		//On
		function() {
			var sidebar = jQuery(this);

			//Each Marker Type
			jQuery('.map-first-markers .map-first-overlay-type', sidebar).each(function() {
				var type_container = jQuery(this);
				var type_key = type_container.data('type_key');
		
				if(typeof type_key !== 'string') {
					return;
				}

				//Show This Marker Type only
				type_container.on('hover', { 'Waymark_Instance': Waymark_Instance, 'type_key': type_key }, function(e) {
					var type_container = jQuery(this);
			
					//Hide All
					for(key in e.data.Waymark_Instance.marker_sub_groups) {
						//Show this type
						if(key == e.data.type_key) {		
							var Waymark_Instance = e.data.Waymark_Instance;	
							var marker_group = Waymark_Instance.marker_sub_groups[key];

							Waymark_Instance.map.addLayer(marker_group);
						//Hide others
						} else {
							e.data.Waymark_Instance.map.removeLayer(e.data.Waymark_Instance.marker_sub_groups[key]);				
						}
					}
				});
		
				//Each Type
				for(i in Waymark_Instance.config.marker_types) {
					if(type_key === Waymark_Instance.make_key(Waymark_Instance.config.marker_types[i]['marker_title'])) {
						var type = Waymark_Instance.config.marker_types[i];
						var icon_data = Waymark_Instance.build_icon_data(type);
				
						// === Legend ===
						var legend = jQuery('legend', type_container).first();
				
						//Title
						legend.html(icon_data.html + '&nbsp;' + type.marker_title);			
						legend.css({
							'background' : type.marker_colour,
							'color' : type.icon_colour
						});
					};
				}

				//Each Marker
				var markers = jQuery('.map-first-overlay-marker', type_container);
				markers.each(function() {
					var marker = jQuery(this);

					var marker_latlng = marker.data('marker_latlng');

					//Find our Marker
					Waymark_Instance.marker_sub_groups[type_key].eachLayer(function(layer) {
						//Valid location
						if(typeof layer._latlng === 'object') {
							//Closer than a meter
							if(layer._latlng.distanceTo(marker_latlng) < 1) {
								var jquery_marker = jQuery(layer.getElement());						
								jquery_marker.data('sidebar_marker', marker)
						
								return false;
							}
						}
					});

								
					marker.hover(
						//On
						function() {
							var marker = jQuery(this);

							//Valid Marker Group
							if(typeof Waymark_Instance.marker_sub_groups[type_key] === 'object' && typeof marker.data('marker_latlng') === 'object') {
								var marker_latlng = marker.data('marker_latlng');
				
								//Find our Marker
								Waymark_Instance.marker_sub_groups[type_key].eachLayer(function(layer) {
									//Valid location
									if(typeof layer._latlng === 'object') {
										//Closer than a meter... this is it!
										if(layer._latlng.distanceTo(marker_latlng) < 1) {
											var jquery_marker = jQuery(layer.getElement());
									
											marker.on('click', function() {
												Waymark_Instance.map.setView(layer.getLatLng(), 14);										
											});
									
											if(typeof jquery_marker.css('width') === 'undefiend') {
												return;
											}
											
											if(! marker.data('width_px') && ! marker.data('height_px')) {
												marker.data({
													'width_px' : parseInt(jquery_marker.css('width').replace('px', '')),
													'height_px' : parseInt(jquery_marker.css('height').replace('px', '')),								
												});
											}	
									
											if(! jquery_marker.hasClass('waymark-active')) {
												jquery_marker.addClass('waymark-active');
							
												jquery_marker.css({
													'width' : marker.data('width_px') * 1.25 + 'px',
													'height' : marker.data('height_px') * 1.25  + 'px'							
												});									
											}
										}
									}
								});
							}
						},
				
						//Off
						function() {
							var marker = jQuery(this);
							marker.off('click');					

							//Valid Marker Group
							if(typeof Waymark_Instance.marker_sub_groups[type_key] === 'object' && typeof marker.data('marker_latlng') === 'object') {
								var marker_latlng = marker.data('marker_latlng');
				
								//Find our Marker
								Waymark_Instance.marker_sub_groups[type_key].eachLayer(function(layer) {
									//Valid location
									if(typeof layer._latlng === 'object') {
										//Closer than a meter
										if(layer._latlng.distanceTo(marker_latlng) < 1) {
											var jquery_marker = jQuery(layer.getElement());						
					
											if(typeof jquery_marker !== 'object') {
												return;
											}
									
											if(jquery_marker.hasClass('waymark-active')) {
												jquery_marker.removeClass('waymark-active');						

												jquery_marker.css({
													'width' : marker.data('width_px') + 'px',
													'height' : marker.data('height_px') + 'px'						
												});									
											}
										}
									}
								});
							}
						}				
					);
				});
			}
		);
	},	

	//Off
	function() {
		//Show All
		for(key in Waymark_Instance.marker_sub_groups) {
			Waymark_Instance.map.addLayer(Waymark_Instance.marker_sub_groups[key]);
		}	

		var map_markers = jQuery('.waymark-marker', waymark_container);
		map_markers.each(function() {
			var map_marker = 	jQuery(this);
			map_marker.on('click', function() {
				if(sidebar_marker = jQuery(this).data('sidebar_marker')) {
					sidebar_marker.css('background', 'red');
				}
			});
		});		
	});
	sidebar.trigger('mouseover');
	sidebar.trigger('mouseout');
	
	container.append(sidebar);
}

function map_first_setup_breadcrumbs() {
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
		map_first_setup_breadcrumbs();
		map_first_setup_sidebar();

		waymark_setup_accordions();
	}, 0);	
});