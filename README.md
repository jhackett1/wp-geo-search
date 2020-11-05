# WP geo search

Plugin to add location-aware geographical search to WP_Query.

## Providing latitude and longitude data

You could hook into the `save_post` action to populate meta, like this:

```
function example_handle_latlongs($post_id, $post){
    $location = get_field("location", $post);
    if($location){
        update_post_meta($post, "longitude", $location["lng"]);
        update_post_meta($post, "latitude", $location["lat"]);
    }
}

add_action("save_post", "example_handle_latlongs", 10, 3);`
```

This example assumes you are using an ACF Google Map field called "location", but the data could come from anywhere, so long as the meta keys are "longitude" and "latitude".