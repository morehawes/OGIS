function ogis_setup_add() {
	var submission_container = jQuery('#waymark-submission').first();

	if(! submission_container.length) {
		return;
	}

	var waymark_container = jQuery('#waymark-map', submission_container);
	var Waymark_Instance = waymark_container.data('Waymark');

	if(typeof Waymark_Instance !== 'object') {
		return;
	}	
	
	//Resize Map
 	var submit_form = jQuery('form.waymark-map-add', submission_container);
	waymark_container.css('height', submission_container.height() - submit_form.height());
	Waymark_Instance.map.invalidateSize();

	//Modify Submit Button
 	var submit_button = jQuery('input.button', submit_form);
 	submit_button.val('Save Map');
}

function ogis_setup_view() {
	var container = jQuery('.waymark-shortcode').first();

	if(! container.length) {
		return;
	}

	var waymark_container = jQuery('.waymark-map', container).first();
	var Waymark_Instance = waymark_container.data('Waymark');

	if(typeof Waymark_Instance !== 'object') {
		return;
	}	
	
	//Resize Map
	waymark_container.css('height', container.height());
	Waymark_Instance.map.invalidateSize();
}

jQuery('document').ready(function() {
	ogis_setup_add();
	ogis_setup_view();
});