=== Ingeni Slick Carousel ===

Contributors: Bruce McKinnon
Tags: carousel, slick slider
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2019.01

A Slick Slider-based carousel, that provides support for synchronised thumbnails with content sourced from a folder relative to the home URL.



== Description ==

* - Images are added by adding them to a folder (hosted on the web server).

* - Based on Slick Slider




== Installation ==

1. Upload the 'ingeni-slick-carousel' folder to the '/wp-content/plugins/' directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Display the carousel using the shortcode



== Frequently Asked Questions ==



= How do a display the carousel? =

Use the shortcode [ingeni-slick]

The following parameters may be included:

source_path: Directory relative to the home page that contains the images to b displayed. Defaults to '/photos-bucket/',
wrapper_class: Wrapping class name. Defaults to 'ingeni-slick-wrap'.
show_thumbs: Display a horzontal list of thumbnails below the main image. Defaults to 1 (show thumbnails). Used inconjunction with sync_thumbs.
sync_thumbs: Keep the main image and thumbnail list in sync. Defaults to 1 (equals sync the thumbnails). Used inconjunction with show_thumbs.
max_thumbs: Max. number of thumbnails to display. Defaults to 0 (show all thumbnails).
show_arrows: Show navigation arrows. Defaults to 1 (show arrows).
shuffle: Randomly shuffle the order of the images. Defaults to 1 (shuffle images).
speed: msecs to display image before moving to the next. Defaults to 2000 (2 secs).


== Changelog ==

v2017.01 - Initial version, based on Ingeni slick Carousel v2016.01

v2019.01 - Added Github-based updating.