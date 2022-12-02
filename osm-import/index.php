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

//Post
if(sizeof($_POST)) {
	//Queries submitted
	if(isset($_POST['map_queries']) && sizeof($_POST['map_queries'])) {
		foreach($_POST['map_queries'] as $q) {
			$Query = new OSM_Import_Query($q);	
		
			if($Query->can_execute()) {		
				$Queries[] = $Query;
				$map_queries[] = $q;
			}
		}
	
	//Import?
	} elseif(isset($_POST['queries_import']) && ! empty($_POST['queries_import'])) {
		$queries_import = json_decode($_POST['queries_import'], true);
		
		foreach($queries_import as $q) {
			$Query = new OSM_Import_Query($q);	
		
			if($Query->can_execute()) {		
				$Queries[] = $Query;
				$map_queries[] = $q;
			}
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
			<textarea id="queries-import" name="queries_import">[{"query_area_type":"polygon","query_area_polygon":"47.514418190183974 -52.62691497802735,47.51986712922054 -52.620563507080085,47.52543141908685 -52.61713027954102,47.52751787563009 -52.620563507080085,47.523692641902485 -52.63240814208985","query_area_bounds":"-52.711132049560554,47.504212065464706,-52.565013885498054,47.54594230319564","query_overpass_request":"nwr[\"amenity\"=\"parking\"]","query_cast_overlay":"marker","query_cast_marker_type":"parking","query_cast_line_type":"one"},{"query_area_type":"polygon","query_area_polygon":"47.52177992043101 -52.628073692321784,47.51630219587258 -52.62498378753663,47.51937442928042 -52.62099266052246,47.5232579386009 -52.61794567108155,47.525576314583965 -52.61966228485108,47.52615589257003 -52.623610496520996,47.523779582130516 -52.62725830078126,47.523055075827415 -52.62841701507569","query_area_bounds":"-52.6264193058014,47.5218559936326,-52.621853113174446,47.52316012758208","query_overpass_request":"nwr[\"amenity\"=\"toilets\"]","query_cast_overlay":"marker","query_cast_marker_type":"toilet","query_cast_line_type":"one"},{"query_area_type":"bounds","query_area_polygon":"47.51601220735539 -52.64144611358643,47.51601220735539 -52.62318134307862,47.51601220735539 -52.60491657257081,47.52122887031379 -52.60491657257081,47.526445533272195 -52.60491657257081,47.526445533272195 -52.62318134307862,47.526445533272195 -52.64144611358643,47.521229275564295 -52.64144611358643","query_area_bounds":"-52.64144611358643,47.51601220735539,-52.60491657257081,47.526445533272195","query_overpass_request":"nwr[\"man_made\"=\"lighthouse\"]","query_cast_overlay":"marker","query_cast_marker_type":"lighthouse","query_cast_line_type":"one"}]</textarea>
		
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
			"marker_types":[
				{"marker_title":"Toilet","marker_shape":"rectangle","marker_size":"small","icon_type":"text","marker_icon":"WC","marker_colour":"#ffffff","icon_colour":"#000000","marker_display":"1","marker_submission":"1"},
				{"marker_title":"Parking","marker_shape":"rectangle","marker_size":"small","icon_type":"text","marker_icon":"P","marker_colour":"#000000","icon_colour":"#ffffff","marker_display":"1","marker_submission":"1"},
				{"marker_title":"Lighthouse","marker_shape":"circle","marker_size":"medium","icon_type":"html","marker_icon":"🌞","marker_colour":"#000000","icon_colour":"#ffffff","marker_display":"1","marker_submission":"1"}
			],
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
		
		<!-- Export -->
		<textarea id="queries-export"><?php echo json_encode($map_queries); ?></textarea>
	</body>
</html>