<?php

function jhgs_get_the_distance($post = 0, $units = 0){
    $post = get_post( $post );
    $distance = round($post->distance);
    if(isset($post->distance)){
        return round($post->distance);
    }
}

function jhgs_the_distance($small = "Less than a mile away", $one = "About a mile away", $multiple = "About %s miles away" ) {
    $distance = jhgs_get_the_distance();
    if ( strlen( $distance ) == 0 ) {
        return;
    }

    if($distance < 1){
        printf($small, $distance);
    } elseif($distance === 1) {
        printf($one, $distance);
    }else{
        printf($multiple, $distance);
    }
}
