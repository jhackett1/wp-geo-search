<?php
/* 
Plugin name: WP Geo Search
Description: Add a location-aware geographic search to WP_Query for use in your templates
Version: 0.1
Author: Jaye Hackett
Plugin URI: jayehackett.com
*/

function gs_handle_geo_select($select_clause, $query) {
    if($query->get("geo_query")){

        $earth_radius = 3959;

        $lat = $query->get("geo_query")["latitude"];
        $lng = $query->get("geo_query")["longitude"];

        $select_clause = "
            wp_posts.*,
            lats.meta_value AS latitude,
            lngs.meta_value AS longitude,
            ( $earth_radius * acos(
                cos( radians( $lat ) )
                * cos( radians( latitude ) )
                * cos( radians( longitude ) - radians( $lng ) )
                + sin( radians( $lat ) )
                * sin( radians( latitude ) )
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
            JOIN wp_postmeta lats
                ON wp_posts.id = lats.post_id
                AND lats.meta_key = 'latitude'
                
            JOIN wp_postmeta lngs
                ON wp_posts.id = lngs.post_id
                AND lngs.meta_key = 'longitude'
        ";
    }
	return $join_clause;	
}
add_filter('posts_join_paged','gs_handle_geo_joins', 10, 2);

function gs_handle_geo_orderby($orderby_clause, $query) {
    if($query->get("geo_query")){
        $orderby_clause = "distance ASC";
    }
    return $orderby_clause;
}
add_filter('posts_orderby', 'gs_handle_geo_orderby', 10, 2);