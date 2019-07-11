=== Ingeni Slick Carousel ===

Contributors: Bruce McKinnon
Tags: carousel, slick slider
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2019.04

A Slick Slider-based carousel, that provides support for synchronised thumbnails with content sourced from a folder relative to the home URL.

Also allows content to be sources from WP posts (e.g., content blocks)



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

bg_images: Display images as background images. Default = 0 (foreground images)

category: Display the featured images from posts of a specific category. Provide the category name as the parameter value.

file_ids: Comma separated list of media library file IDs. Easy way to get this list is to create a post gallery of the required images. The standard [gallery] shortcode contains a list of file IDs.

post_ids: Comma separated list of post IDs.

post_type: Used in-conjunction with the post_ids parameter. E.g., ‘post’, ‘page’. Defaults to ’content_block’.

orderby: Order in which the slides appear. Used in-conjunction with the post_ids parameter. E.g., ‘post__in’. Defaults to ‘title’.




== Changelog ==

v2017.01 - Initial version, based on Ingeni slick Carousel v2016.01

v2019.01 - Added Github-based updating.
					- Refreshed with Slick Slider 1.9.0 - https://github.com/kenwheeler/slick/
v2019.02	- Improved calling getcwd()
					- Implemented displaying images as background images
					- Implemented displaying the featured images from posts of a specific category

v2019.03  - Added the 'file_ids' parameter. Allows you to pass in a list if media IDs, as you get when you create a gallery within a post.

v2019.04 - Added the 'post_ids', 'post_type' and 'orderby' options - supply a list of post ids that become the content of the slider.

