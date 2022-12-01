<?php

// require_once('OSM_Import_Request.php');
	
class OSM_Import_OverpassRequest extends OSM_Import_Request {
	
	public $overpass_query = null;
	
	function __construct($params_in = []) {
// 		$this->request_endpoint = 'http://localhost/overpass-response.json';	
		$this->request_endpoint = 'https://overpass.kumi.systems/api/interpreter';	

		$this->set_config('output_type', 'json');

		foreach($params_in as $key => $value) {
//			if(is_string($value)) {
				$this->set_config($key, $value);		
//			}
		}
	}

	function build_request_parameters($params_in = []) {
		$Query = $this->get_config('Query');

		$query_area = $Query->get_query_area();
		$query_string = $Query->get_query_overpass();

		//Check for required data
		if(! $query_area || ! $query_string) {
			return false;
		}

		
		// ==== Headers ====

		
		//Output type
		$overpass_query = '[out:' . $this->get_config('output_type') . ']';

		//Query area
		switch($query_area['type']) {
			case 'bounds' :
				//Convert from Leaflet to Overpass
				$overpass_bounding_box = OSM_Import_Overpass::leaflet_bb_to_overpass_bb($query_area['area']);
				
				//Create header
				$overpass_query .= '[bbox:' . $overpass_bounding_box . ']';
			
				break;		
		}
		$overpass_query .= ';';

		// ==== User Query ====
		
		$overpass_query .= $query_string;

		//Query area
		switch($query_area['type']) {
			case 'polygon' :
				//Convert from Leaflet to Overpass
				$overpass_polygon = OSM_Import_Overpass::leaflet_poly_to_overpass_poly($query_area['area']);
				
				//Create header
				$overpass_query .= '(poly:"' . $overpass_polygon . '")';

				break;				
		}

		$overpass_query .= ';';

		// ==== Output ====

		switch($Query->get_parameter('query_cast_overlay')) {
			case 'marker' :
				$overpass_query .= 'out center';

				break;

			case 'line' :
				$overpass_query .= 'out geom';	

				break;				
		}
		$overpass_query .= ';';

		//Make safe
		$overpass_query = str_replace('+', '%20', $overpass_query);
	
		//Save
		$this->overpass_query = $overpass_query;

		$return = [
			'data' => $overpass_query
		];
		
		return $return;
	}

	function process_response($response_raw) {
		$response_out = [
			'status' => 'init'
		];

		$response_raw = trim(preg_replace('/\s+/', ' ', $response_raw));
		$response_out['raw'] = $response_raw;

		$Query = $this->get_config('Query');			
		$query_cast_overlay = $Query->get_parameter('query_cast_overlay');

		$response_out['status'] = 'success';

		//Ensure is Array
		$response_json = json_decode($response_out['raw'], null, 512, JSON_OBJECT_AS_ARRAY);

		//Get Overlays GeoJSON		
		$response_geojson = OSM_Import_Overpass::overpass_json_to_geojson($response_json, $query_cast_overlay);

		//If we have overlays
		$overlay_count = OSM_Import_GeoJSON::get_feature_count($response_geojson);					
		if($overlay_count) {
			//What kind of Overlay?
			switch($query_cast_overlay) {
				//Markers
				case 'marker' :
					$query_data = OSM_Import_GeoJSON::update_feature_property($response_geojson, 'type', $Query->get_parameter('query_cast_marker_type'));						

					break;

				//Lines
				case 'line' :
					$query_data = OSM_Import_GeoJSON::update_feature_property($response_geojson, 'type', $Query->get_parameter('query_cast_line_type'));

					break;
			}		


			$response_out['query_data'] = json_encode($query_data);
		}

		return $response_out;
	}	
}