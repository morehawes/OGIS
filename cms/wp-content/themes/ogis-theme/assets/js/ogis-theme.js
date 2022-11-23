function ogis_setup_home() {
	var body = jQuery('body.home').first();

	if(! body.length) {
		return;
	}

// 	body.css({
// 		'height': '100%',
// 		'background': 'red'
// 	});

	var body_height = body.height();
	console.log(body_height);
	
	var waymark_container = jQuery('#waymark-map');
	var Waymark_Instance = waymark_container.data('Waymark');

	if(typeof Waymark_Instance !== 'object') {
		return;
	}	
	
 	waymark_container.addClass('ogis-home');

	var map_height = body_height * (2/3);	
	
	//Unset
	waymark_container.css({
// 		'height': ''
	});
	
	Waymark_Instance.map.invalidateSize();
}
jQuery('document').ready(function() {
	ogis_setup_home();
});