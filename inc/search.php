<?php

function jhgs_earth_radius($query){
    if(isset($query->get("geo_query")["units"]) && $query->get("geo_query")["units"] == "km") return 6371;
    return 3959;
}

function jhgs_handle_geo_select($select_clause, $query) {
    if(isset($query->get("geo_query")["latitude"]) && isset($query->get("geo_query")["latitude"])){

        $earth_radius = jhgs_earth_radius($query);

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

add_filter('posts_fields','jhgs_handle_geo_select', 10, 2);


function jhgs_handle_geo_joins($join_clause, $query) {
    if(isset($query->get("geo_query")["latitude"]) && isset($query->get("geo_query")["latitude"])){
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

add_filter('posts_join_paged','jhgs_handle_geo_joins', 10, 2);


function jhgs_handle_geo_where($where_clause, $query){
    if(isset($query->get("geo_query")["latitude"]) && isset($query->get("geo_query")["latitude"]) && isset($query->get("geo_query")["radius"])){

        $earth_radius = jhgs_earth_radius($query);

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
add_filter("posts_where", "jhgs_handle_geo_where", 10, 2);


function jhgs_handle_geo_orderby($orderby_clause, $query) {
    if(isset($query->get("geo_query")["latitude"]) && isset($query->get("geo_query")["latitude"])){
        $orderby_clause = "-distance DESC";
    }
    return $orderby_clause;
}

add_filter('posts_orderby', 'jhgs_handle_geo_orderby', 10, 2);