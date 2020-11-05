# WP geo search

Plugin to add location-aware geographical search to WP_Query.

## Using it in a query

Adding a `geo_query` parameter to WP_Query will add a "distance" column to the returned results, provided they have the right metadata.

```
$query = new WP_Query(array(
    "geo_query" => array(
            "latitude" => -52.005,
            "longitude" => 0.005,
    )
))
```

Optionally, you can then filter by radius.

By default, distances are given in miles. You can provide `"units" => "km"` if you need kilometres.

```
$query = new WP_Query(array(
    "geo_query" => array(
            "latitude" => -52.005,
            "longitude" => 0.005,
            "within" => 10
    )
))
```

Or order by distance:

```
$query = new WP_Query(array(
    "geo_query" => array(
            "latitude" => -52.005,
            "longitude" => 0.005
    ),
    "orderby" => "geo"
))
```

## Providing latitude and longitude data

It looks for two custom field values with the keys `latitude` and `longitude` on your posts.

It's agnostic about how you supply this data. The simplest thing to do is add it using WordPress's built-in custom field editor.

You could also hook into the `save_post` action to populate meta:

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

This example assumes you are using an ACF Google Map field called "location", but the data could come from anywhere, so long as the meta keys are `longitude` and `latitude`.