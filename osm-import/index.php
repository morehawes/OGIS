<?php

spl_autoload_register(function($class_name) {
	$file_name = substr($class_name, strripos($class_name, '_')+1);
	$file_name .= '.php';

	include 'inc/' . $file_name;	
});

//Form Output
$Query = new OSM_Import_Query();
$Queries = [];
$map_queries = [];

if(sizeof($_POST) && sizeof($_POST['map_queries'])) {
	foreach($_POST['map_queries'] as $q) {
		$Query = new OSM_Import_Query($q);	
		
		if($Query->can_execute()) {		
			$Queries[] = $Query;
			$map_queries[] = $q;
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>OSM Import Test</title>

		<!-- jQuery -->
		<script src="https://code.jquery.com/jquery-3.6.1.slim.min.js" integrity="sha256-w8CvhFs7iHNVUtnSP0YKEg00p9Ih13rlL9zGqvLdePA=" crossorigin="anonymous"></script>

		<!-- OSM Import -->
		<link rel="stylesheet" href="assets/css/osm-import.css" type="text/css" media="all" />
		<script src="assets/js/osm-import.js"></script>
		
		<!-- Waymark JS -->		
		<link rel="stylesheet" href="assets/css/waymark-js.min.css" type="text/css" media="all" />
		<script src="assets/js/waymark-js.min.js"></script>		
	</head>

	<body>
		<!-- Form -->
		<form id="osm-import" method="post">
			<?php 
			//Valid Queries		
			if(sizeof($map_queries)) {
				$Query->create_map_form($map_queries);			
			//Blank
			} else {
				$Query->create_map_form();		
			}			
			?>
			
			<input type="submit" />
		</form>
		
		<!-- Map -->
		<div id="waymark-map" class="waymark-instance"></div>

		<script>
		const waymark_user_config = {
			"tile_layers":[{"layer_name":"Open Street Map","layer_url":"https:\/\/{s}.tile.openstreetmap.org\/{z}\/{x}\/{y}.png","layer_attribution":"\u00a9 <a href=\"https:\/\/www.openstreetmap.org\/copyright\">OpenStreetMap<\/a> contributors"}],
			"marker_types":[{"marker_title":"Toilet","marker_shape":"rectangle","marker_size":"small","icon_type":"text","marker_icon":"WC","marker_colour":"#ffffff","icon_colour":"#000000","marker_display":"1","marker_submission":"1"}],
			"line_types":[{}],
			"shape_types":[{}]
		};

		jQuery(document).ready(function() {
			const waymark_viewer = window.Waymark_Map_Factory.viewer();
			waymark_viewer.fallback_latlng = [47.525026,-52.638029327392585];
			waymark_viewer.fallback_zoom = 13;
	
			const waymark_config = jQuery.extend({}, waymark_user_config);
			waymark_config.map_div_id = "waymark-map";
			waymark_config.map_height = 450;
			waymark_viewer.init(waymark_config);
	
			<?php if(sizeof($Queries)) : foreach($Queries as $Query) : ?>
			waymark_viewer.load_json(<?php echo $Query->get_parameter('query_data'); ?>);
			<?php endforeach; endif; ?>
		});
		</script>
	</body>
</html>