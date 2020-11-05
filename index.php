<?php
/* 
Plugin name: WP Geo Search
Description: Add a location-aware geographic search to WP_Query for use in your templates
Version: 0.1
Author: Jaye Hackett
Plugin URI: jayehackett.com
*/

require "query.php";
require "hooks.php";

register_activation_hook( __FILE__, 'gs_track_all_latlongs' );