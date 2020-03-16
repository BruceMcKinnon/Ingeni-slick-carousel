=== Ingeni Slick Carousel ===

Contributors: Bruce McKinnon
Tags: carousel, slick slider
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2020.03

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

show_dots: Show navigation dots. Defaults to 0 (show dots).

shuffle: Randomly shuffle the order of the images. Defaults to 1 (shuffle images).

speed: msecs to display image before moving to the next. Defaults to 2000 (2 secs).

bg_images: Display images as background images. Default = 0 (foreground images)

category: Display the featured images from posts of a specific category. Provide the category name as the parameter value.

file_ids: Comma separated list of media library file IDs. Easy way to get this list is to create a post gallery of the required images. The standard [gallery] shortcode contains a list of file IDs.

post_ids: Comma separated list of post IDs.

post_type: Used in-conjunction with the post_ids parameter. E.g., ‘post’, ‘page’. Defaults to ’content_block’.

orderby: Order in which the slides appear. Used in-conjunction with the post_ids parameter. E.g., ‘post__in’. Defaults to ‘title’.

center_mode: When using variable width, center the image in the div. Defaults to 0.

variable_width: Cope with variable width images. Default to 0. 

fade: Defaults to 1 for fade transitions. NB, slide transition is forced when using variable_width and center_mode. 



== Changelog ==

v2017.01 - Initial version, based on Ingeni slick Carousel v2016.01

v2019.01 - Added Github-based updating.
					- Refreshed with Slick Slider 1.9.0 - https://github.com/kenwheeler/slick/
v2019.02	- Improved calling getcwd()
					- Implemented displaying images as background images
					- Implemented displaying the featured images from posts of a specific category

v2019.03  - Added the 'file_ids' parameter. Allows you to pass in a list if media IDs, as you get when you create a gallery within a post.

v2019.04 - Added the 'post_ids', 'post_type' and 'orderby' options - supply a list of post ids that become the content of the slider.

v2019.05	- Added support for 'fade', 'center_mode', 'variable_width' options.

v2019.06  - Added the 'link_post' option. Allows linking to slides sourced from posts.

v2019.07  - Added the 'show_dots' option. Defaults to 0 or off.

v2019.08  - More complete implementation of link_posts.
		Added the 'translucent_layer_class' option.
		Trapping of invalid paths at scandir().

v2019.09 - Added the 'slides_to_show' option.
		- Added support for MP4 videos
v2020.01 - Added delay_start msec timer to delay video/slider start. Defaults to 0. Max value = 60000.
v2020.02 - Added the 'slides_to_scroll' option. Defaults to 1.
v2020.03 - Fixed bug where slides_to_show could be set < 1.
	- Updated plugin checker updater to 4.9
	- Reverted to slick carousel 1.8.1 - latest supported version

