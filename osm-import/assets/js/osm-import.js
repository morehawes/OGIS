function waymark_handle_repeatable_clone(clone) {
	//Must be jQuery
	if(! clone instanceof jQuery) {
		return;
	}	

	//Get context
	var form = clone.parents('.waymark-form');
	
	//Map Queries
	if(form && form.hasClass('waymark-map-query')) {
		var waymark_container = jQuery('.waymark-map-container').first();
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
});