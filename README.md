# 🌍 WP geo search

A plugin to add location-aware geographical search to [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/).

You can use it to power location-aware apps, such as showing a user results near them.

[Nominatim](https://nominatim.org/)'s service is used for geocoding location searches.

[Here's an example](https://gist.github.com/jhackett1/0d1a68207d4e55a2ccae15af8972a8a1) of how you might use it in a theme.

## 🔎 Using it in a query

Adding a `geo_query` parameter to WP_Query will add a "distance" column to the returned results, provided they have the right metadata.

You can then display this in your templates.

You can use a location search parameter, which will be [geocoded](#geocoding) or directly provide latitude and longitude values:


```
$query = new WP_Query(array(
    "geo_query" => array(
            "location" => "London"
    )
))

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

### Displaying distance in templates

In a `WP_Query` loop that includes a `geo_query`, you can use two extra functions to show distance away:

- `get_the_distance(object $post)` - which returns a rounded integer for the distance away, similar to `get_the_title()`
- `the_distance(string $less_than_one, string $one, string $more_than_one)` - which displays an approximate human-readable string, similar to `the_title()`

`the_distance` will show one of three messages depending on whether the rounded distance is less than one, one, or greater than one. By default these are:

- "Less than a mile away"
- "About a mile away"
- "About %s miles away"

If you need to use different units or translations, can pass three [printf-formatted](https://www.php.net/manual/en/function.printf.php) strings to `the_distance()` to override these messages. Put `%s` where you want the value.

If you need the _exact_, unrounded value, you can use `$post->distance`.

### Geocoding

[Nominatim](https://nominatim.org/)'s service is used for geocoding location searches.

Using it is subject to an [acceptable use policy](https://operations.osmfoundation.org/policies/nominatim/) - if you use case will involve lots of API calls, you should replace it with a paid alternative, like [Google](https://developers.google.com/maps/documentation/geocoding/overview)'s.

## 📍 Populating latitude and longitude data

It looks for two [custom field](https://wordpress.org/support/article/custom-fields/) values with the keys `latitude` and `longitude` on your posts.

It's agnostic about how you supply this data. The simplest thing to do is type it in using WordPress's built-in custom field editor.

You could also hook into the `save_post` action to populate meta whenever you create or change a post, by adding a snippet like this to your theme's `functions.php`:

```
function example_update_latlngs($post){
    $location = get_field("location", $post);
    if(isset($location)){
        update_post_meta($post, "longitude", $location["lng"]);
        update_post_meta($post, "latitude", $location["lat"]);
    }
}

add_action("save_post", "example_update_latlngs", 10, 3);
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
        // Function from above
        example_update_latlngs($post);
    }
}

add_action('after_switch_theme', 'example_update_all_latlngs');
```
