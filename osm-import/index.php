<?php

spl_autoload_register(function($class_name) {
	$file_name = substr($class_name, strripos($class_name, '_')+1);
	$file_name .= '.php';

	include 'inc/' . $file_name;	
});

$Query = new OSM_Import_Query([
	'query_area_type' => 'bounds',
	'query_area_bounds' => '-52.648029327392585,47.505026,-52.605972,47.53435623467226',
	'query_overpass_request' => 'nwr["amenity"]',
	'query_cast_overlay' => 'marker'	
]);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>OSM Import Test</title>

		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
		<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>		
	</head>

	<body>
		<div id="map" style="height:400px"></div>

		<script>
			const query_data = <?php echo $Query->get_parameter('query_data'); ?>;
			
			const map = L.map('map').setView([47.525026,-52.638029327392585], 13);
			const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19,
				attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
			}).addTo(map);

			const data_layer = L.geoJSON(query_data, {
				pointToLayer(feature, latlng) {
					return L.marker(latlng);
				}
			})
			.bindPopup(function (layer) {
		    return layer.feature.properties.description;
		  })
			.addTo(map);
		</script>
	</body>
</html>