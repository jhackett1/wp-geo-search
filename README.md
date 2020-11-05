# 🌍 WP geo search

A plugin to add location-aware geographical search to [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/).

You can use it to power location-aware apps, such as showing a user results near them.

## 🔎 Using it in a query

Adding a `geo_query` parameter to WP_Query will add a "distance" column to the returned results, provided they have the right metadata.

You can then display this in your templates.

```
$query = new WP_Query(array(
    "geo_query" => array(
            "latitude" => -52.005,
            "longitude" => 0.005,
    )
))
```

Optionally, you can then filter by search radius.

By default, distances are given in miles. You can provide `"units" => "km"` if you need kilometres.

```
$query = new WP_Query(array(
    "geo_query" => array(
            "latitude" => -52.005,
            "longitude" => 0.005,
            "radius" => 10
    )
))
```

Or order by nearness:

```
$query = new WP_Query(array(
    "geo_query" => array(
            "latitude" => -52.005,
            "longitude" => 0.005
    ),
    "orderby" => "geo"
))
```

## 📍 Populating latitude and longitude data

It looks for two [custom field](https://wordpress.org/support/article/custom-fields/) values with the keys `latitude` and `longitude` on your posts.

It's agnostic about how you supply this data. The simplest thing to do is type it in using WordPress's built-in custom field editor.

You could also hook into the `save_post` action to populate meta, by adding a snippet like this to your theme's `functions.php`:

```
function example_update_latlngs($post_id, $post){
    $location = get_field("location", $post);
    if(isset($location)){
        update_post_meta($post, "longitude", $location["lng"]);
        update_post_meta($post, "latitude", $location["lat"]);
    }
}

add_action("save_post", "example_update_latlngs", 10, 3);`
```

This example assumes you are using an [ACF Google Map](https://www.advancedcustomfields.com/resources/google-map/) field called "location", but the data could come from anywhere, including a custom meta box you code yourself, so long as the post meta keys are right.

### Bulk-updating existing posts

If you have many posts that you need to add longitude and latitude meta to in bulk, you could add something like this to `functions.php`, which will run on [theme activation](https://developer.wordpress.org/reference/hooks/after_switch_theme/):

```
function example_update_all_latlngs(){
    $query = new WP_Query(array(
        "posts_per_page" => -1
    ));
    foreach($query->get_posts() as $post){
        gs_update_latlngs($post);
    }
}

add_action('after_switch_theme', 'example_update_all_latlngs');
```

