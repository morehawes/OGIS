function waymark_setup_map_query() {
	var query_index = 1;	

	jQuery('.waymark-query-form.waymark-map-query .waymark-parameters-container').each(function() {
		jQuery(this).attr('data-index', query_index);
		
		//Update on change
// 		var inputs = jQuery('.waymark-input', jQuery(this));
// 		inputs.each(function() {
// 			var input = jQuery(this);
// 		
// 			//Execute on change
// 			input.on('change', function() {
// 				waymark_execute_query(jQuery(this).parents('.waymark-parameters-container'));	
// 			});
// 		});
// 		
// 		//Inital view
// 		inputs.last().trigger('change');		
		
		//Render Map Query
		jQuery(this).hover(
			function() {
				waymark_render_map_query(jQuery(this))
			},
			function() {
				waymark_unrender_map_query(jQuery(this))
			}		
		);

		query_index++;	
	});			
}

function waymark_setup_dropdowns() {
	jQuery('.waymark-parameters-container').each(function() {
		var container = jQuery(this);
		
		jQuery('select', container).each(function() {
			//Prefix
			var class_string = 'waymark-dropdown-' + jQuery(this).data('id') + '-';			

			//Add new
			class_string += jQuery(this).val();
			container.addClass(class_string);
			
			//On Change
			jQuery(this).on('change', function() {			
				//Prefix
				var class_string = 'waymark-dropdown-' + jQuery(this).data('id') + '-';			
				
				//Remove old
				jQuery('option', jQuery(this)).each(function() {
					container.removeClass(class_string + jQuery(this).attr('value'))
				});

				//Add new
				class_string += jQuery(this).val();
				container.addClass(class_string);
			});
		});			
	});
}

function waymark_render_map_query(query_container = false) {
	//Must be jQuery
	if(! query_container instanceof jQuery) {
		return;
	}

	var waymark_container = jQuery('.waymark-instance').first();
	var Waymark_Instance = waymark_container.data('Waymark');					

	//Not while editing
	if(Waymark_Instance.is_bounds_editing()) {
		return;					
	}

	query_container.addClass('waymark-active');		

	//Get data
	var area_type_input = jQuery('.waymark-input-query_area_type', query_container).first();
	var area_type = area_type_input.val();

	var area_bounds_input = jQuery('.waymark-input-query_area_bounds', query_container).first();
	var area_polygon_input = jQuery('.waymark-input-query_area_polygon', query_container).first();

	switch(area_type) {
		case 'bounds' :
			var area_val = area_bounds_input.val();
	
			break;

		case 'polygon' :
			var area_val = area_polygon_input.val();

			break;
	}

	//Create Layer	
	var selector_layer = Waymark_Instance.draw_bounds_selector(area_type, area_val);
	
	//On edit
	var data = {
		'Waymark': Waymark,
		'area_type': area_type,
		'area_bounds_input' : area_bounds_input,
		'area_polygon_input' : area_polygon_input
	}
	selector_layer.on('editable:vertex:dragend', function() {
		//Update
		switch(this.area_type) {
			case 'bounds' :
				var bounds = selector_layer.getBounds();
				
				this.area_bounds_input.val(this.Waymark.bounds_to_string(bounds));	
				this.area_bounds_input.trigger('change');	

				break;

			case 'polygon' :
				var latlng_array = selector_layer.getLatLngs();						
				
				this.area_polygon_input.val(this.Waymark.polygon_array_to_string(latlng_array));
				this.area_polygon_input.trigger('change');	
					
				break;
		}
	}, data);

	//Add Edit button
	var edit_button = jQuery('<button />')
		.html('<i class="ion ion-edit"></i>')
		.addClass('button waymark-edit')
		.on('click', data, function(e) {
			e.preventDefault();

			if(! e.data.Waymark.is_bounds_editing()) {
				Waymark.edit_bounds_selector();
				jQuery(this).html('<i class="ion ion-android-done"></i>')							
			} else {
				Waymark.unedit_bounds_selector();
				jQuery(this).html('<i class="ion ion-edit"></i>')																			
			}							
		})
	;

	query_container.append(edit_button);
}

function waymark_unrender_map_query(query_container = false) {
	//Must be jQuery
	if(! query_container instanceof jQuery) {
		return;
	}	

	var waymark_container = jQuery('.waymark-instance').first();
	var Waymark_Instance = waymark_container.data('Waymark');		

	//Not while editing
	if(! Waymark_Instance.is_bounds_editing()) {
		//Remove Edit button
		jQuery('.waymark-edit', query_container).remove();
		
		Waymark_Instance.undraw_selectors();

		query_container.removeClass('waymark-active');		
	}
}

function waymark_handle_repeatable_clone(clone) {
	//Must be jQuery
	if(! clone instanceof jQuery) {
		return;
	}	

	//Get context
	var form = clone.parents('.waymark-form');
	
	//Map Queries
	if(form && form.hasClass('waymark-map-query')) {
		var waymark_container = jQuery('.waymark-instance').first();
		var Waymark_Instance = waymark_container.data('Waymark');		
		
		var default_bounds = Waymark_Instance.get_default_bounds();


		var area_bounds_input = jQuery('.waymark-input-query_area_bounds', clone).first();
		area_bounds_input.val(Waymark_Instance.bounds_to_string(default_bounds))

		var area_polygon_input = jQuery('.waymark-input-query_area_polygon', clone).first();
		var latlng_array = Waymark.latlng_bounds_to_latlng_array(default_bounds);
		area_polygon_input.val(Waymark_Instance.polygon_array_to_string(latlng_array));
		
		clone.hover(function() {
			waymark_render_map_query(jQuery(this));		
		},
		function() {
			waymark_unrender_map_query(jQuery(this));		
		});	
	}

	return clone;
}

// function waymark_handle_repeatable_template(template) {
// 	//Must be jQuery
// 	if(! template instanceof jQuery) {
// 		return;
// 	}	
// 	
// 	//Get context
// 	var form = template.parents('.waymark-form');
// 	
// 	//Map Queries
// 	if(form && form.hasClass('waymark-map-query')) {
// 	
// 	}	
// 	
// 	return template;
// }

function waymark_setup_repeatable_parameters() {
	jQuery('.waymark-repeatable-container').each(function() {
		var repeatable_container = jQuery(this);
		var repeatable_count = repeatable_container.data('count');
		
		var template = jQuery('.waymark-repeatable-template', repeatable_container);
		template.removeClass('waymark-repeatable-template');

		//Do stuff to template (while it's still in the DOM)...			
// 		template = waymark_handle_repeatable_template(template);		
		
		template.remove();

		//Each
		jQuery('.waymark-parameters-container', repeatable_container).each(function() {
			var parameter_container = jQuery(this);
			
			var delete_button = jQuery('<button />')
				.html('<i class="ion ion-android-delete"></i>')
				.addClass('button waymark-delete')
				.on('click', function(e) {
					e.preventDefault();

					parameter_container.remove();						
				})
			;
			parameter_container.append(delete_button);		
		});

		//Add		
		var add_button = jQuery('.waymark-repeatable-add', repeatable_container).first();
		add_button.on('click', function(e) {
			e.preventDefault();
	
			var clone = template.clone();
			
			//Update inputs
			jQuery('.waymark-input', clone).each(function() {
				var input = jQuery(this);
			
				input.attr('name', input.attr('name').replace('__count__', repeatable_count));
			});	

			jQuery('.waymark-control-label', clone).each(function() {
				var label = jQuery(this);
			
				label.attr('for', label.attr('for').replace('__count__', repeatable_count));
			});							

			//Add		
			add_button.before(clone);

			//Do stuff to clone (now it's in the DOM)...			
			clone = waymark_handle_repeatable_clone(clone);
			
			waymark_setup_dropdowns();
			
			//Update count
			repeatable_container.data('count', ++repeatable_count);
			
			return false;
		});
	});
}

jQuery(document).ready(function() {
	waymark_setup_repeatable_parameters();
	waymark_setup_dropdowns();
	waymark_setup_map_query();
});