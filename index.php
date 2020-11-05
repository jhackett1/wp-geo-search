<?php
/* 
Plugin name: WP Geo Search
Description: Add a location-aware geographic search to WP_Query for use in your templates
Version: 0.1
Author: Jaye Hackett
Plugin URI: jayehackett.com
*/

function earth_radius($query){
    if(isset($query->get("geo_query")["units"]) && $query->get("geo_query")["units"] == "km") return 6371;
    return 3959;
}

function gs_handle_geo_select($select_clause, $query) {
    if($query->get("geo_query")){

        $earth_radius = earth_radius($query);

        $lat = $query->get("geo_query")["latitude"];
        $lng = $query->get("geo_query")["longitude"];

        $select_clause = "
            wp_posts.*,
            lats.meta_value AS latitude,
            lngs.meta_value AS longitude,
            ( $earth_radius * acos(
                cos( radians( $lat ) )
                * cos( radians( lats.meta_value ) )
                * cos( radians( lngs.meta_value ) - radians( $lng ) )
                + sin( radians( $lat ) )
                * sin( radians( lats.meta_value ) )
                ) )
                AS distance
        ";
    }
	return $select_clause;	
}

add_filter('posts_fields','gs_handle_geo_select', 10, 2);


function gs_handle_geo_joins($join_clause, $query) {
    if($query->get("geo_query")){
        $join_clause .= "       
            LEFT JOIN wp_postmeta lats
                ON wp_posts.id = lats.post_id
                AND lats.meta_key = 'latitude'
                
            LEFT JOIN wp_postmeta lngs
                ON wp_posts.id = lngs.post_id
                AND lngs.meta_key = 'longitude'
        ";
    }
	return $join_clause;	
}

add_filter('posts_join_paged','gs_handle_geo_joins', 10, 2);


function gs_handle_geo_where($where_clause, $query){
    if($query->get("geo_query") && isset($query->get("geo_query")["radius"])){

        $earth_radius = earth_radius($query);

        $lat = $query->get("geo_query")["latitude"];
        $lng = $query->get("geo_query")["longitude"];
        $radius = $query->get("geo_query")["radius"];

        $where_clause .= "AND 
            ( $earth_radius * acos(
            cos( radians( $lat ) )
            * cos( radians( lats.meta_value ) )
            * cos( radians( lngs.meta_value ) - radians( $lng ) )
            + sin( radians( $lat ) )
            * sin( radians( lats.meta_value ) )
            ) ) < $radius
        ";
    }
	return $where_clause;	
}
add_filter("posts_where", "gs_handle_geo_where", 10, 2);


function gs_handle_geo_orderby($orderby_clause, $query) {
    if($query->get("geo_query")){
        $orderby_clause = "-distance DESC";
    }
    return $orderby_clause;
}

add_filter('posts_orderby', 'gs_handle_geo_orderby', 10, 2);