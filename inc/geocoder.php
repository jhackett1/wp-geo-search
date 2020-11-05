<?php

function gs_geocode($query){
    $curl = curl_init("https://nominatim.openstreetmap.org/search/${query}?format=json");
    curl_setopt( $curl, CURLOPT_USERAGENT, "WordPress Geo Search — By Jaye Hackett" );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec( $curl );
    curl_close( $curl );
    $data = json_decode($response);
    return @$data[0];
}

function gs_handle_geocoding($query){
    if(isset($query->get("geo_query")["location"])){
        $geo_query = $query->get("geo_query");
        $geometry = gs_geocode($geo_query["location"]);
        if($geometry){
            $geo_query["latitude"] = $geometry->lat;
            $geo_query["longitude"] = $geometry->lon;
            $query->set( "geo_query",  $geo_query);
        }
    }
}

add_action("pre_get_posts", "gs_handle_geocoding");