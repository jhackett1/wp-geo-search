<?php

// update values after post save
function gs_track_latlongs($post){
    $location = get_field("location", $post);
    if($location){
        update_post_meta($post, "longitude", $location["lng"]);
        update_post_meta($post, "latitude", $location["lat"]);
    }
}
add_action("save_post", "gs_track_latlongs", 10, 3);

// update all values on plugin activation
function gs_track_all_latlongs(){
    $query = new WP_Query(array(
        "post_type" => "any",
        "posts_per_page" => -1
    ));
    foreach($query->get_posts() as $post){
        gs_track_latlongs($post);
    }
}