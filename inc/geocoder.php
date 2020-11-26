<?php

function jhgs_geocode($query){
    $response = wp_remote_retrieve_body(wp_remote_get("https://nominatim.openstreetmap.org/search/${query}?format=json", array(
        "user-agent" => "WordPress Geo Search — By Jaye Hackett"
    )));
    $data = json_decode($response);
    return @$data[0];
}

function jhgs_handle_geocoding($query){
    if(isset($query->get("geo_query")["location"])){
        $geo_query = $query->get("geo_query");
        $geometry = jhgs_geocode($geo_query["location"]);
        if($geometry){
            $geo_query["latitude"] = $geometry->lat;
            $geo_query["longitude"] = $geometry->lon;
            $query->set( "geo_query",  $geo_query);
        }
    }
}

add_action("pre_get_posts", "jhgs_handle_geocoding");