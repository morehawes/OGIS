<?php

class OSM_Import_Helper {

	static public function debug($thing, $die = true) {		
		echo '<pre>';
		print_r($thing);
		echo '</pre>';
		if($die) {
			die;
		}
	}
}