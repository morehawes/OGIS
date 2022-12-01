<?php

spl_autoload_register(function($class_name) {
	$file_name = substr($class_name, strripos($class_name, '_')+1);
	$file_name .= '.php';

	include 'inc/' . $file_name;	
});

$Query = new OSM_Import_Query([
	'query_area_type' => 'bounds',
	'query_area_bounds' => '-52.648029327392585,47.505026,-52.605972,47.53435623467226',
	'query_overpass_request' => 'nwr["amenity"]'
]);

OSM_Import_Helper::debug($Query);