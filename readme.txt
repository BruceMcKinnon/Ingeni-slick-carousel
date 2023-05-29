=== Ingeni Slick Carousel ===

Contributors: Bruce McKinnon
Tags: carousel, slick slider
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2023.05

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

order: Defaults to 'ASC'

center_mode: When using variable width, center the image in the div. Defaults to 0.

variable_width: Cope with variable width images. Default to 0. 

fade: Defaults to 1 for fade transitions. NB, slide transition is forced when using variable_width and center_mode. 

adaptive_height - When set to 1, enables adaptive height for single slide horizontal carousels. Default = 0

thumbnail_size - If displaying the thumbnail or featured image of a post, specify the size to use. Default is 'full',

show_title - If showing a carousel of images, set to 1 to have the image title displayed. Default = 0

translucent_layer_class - Specify the a translucent class name. Default = "".

link_post => Set to 1 to linking to slides sourced from posts. Default = 0

delay_start - Msec to delay video/slider start. Defaults to 0. Max value = 60000.

slides_to_show - Number of slides to show at one time. Default = 1

slides_to_scroll  - Number of slides to scroll in a single scroll. Default = 1

show_content - If 1, display content from a post to be used an an overlay - e.g., text overlaying image. Defaults to 0

template - Specify a slider template. Will search in the {theme}/ingeni-slick-templates and then the plugin template folder for a matching template file.

template_function_call: specify the calling function in a template file. Defaults to 'do_slick_template'. Required when multiple sliders on a single page.

responsive_breakpoints: Comma delimited string containing responsive breakpoints. For example: "640,1024". Default is blank = non-responsive display.

responsive_slides_to_show: Comma delimited string containing number of slides to show for each breakpoint. For example: "2,3". Defaults is blank = non-responsive display.

slider_class - specify unique JS class to permit multiple sliders on a single page. Defaults to 
'slider-'.

pause_on_hover - If set to 0 hovering over slider won't pause it. Defaults to 1 - pause on hover.

lightbox - If set to 1, a Lightbox will open when the image is clicked.



== Examples ==


One image background images:

[ingeni-slick source_path="/assets/2020/home-photos/" show_thumbs=0 show_arrows=1 show_dots=0 variable_width=0 fade=1 speed=3000 bg_images=0]



Three image carousel:

[ingeni-slick source_path="/products/" show_thumbs=0 show_arrows=1 show_dots=0 slides_to_show=3 slides_to_scroll=3 center_mode=1 variable_width=0 wrapper_class="product_slider" speed=5000 fade=0]



Responsive slider. Small = 1, Medium = 2, Large = 3:

[ingeni-slick source_path="/products/" show_thumbs=0 show_arrows=1 show_dots=0 slides_to_show=1 slides_to_scroll=1 center_mode=1 variable_width=0 wrapper_class="product_slider" speed=3000 fade=0 bg_images=1 responsive_breakpoints="640,1024" responsive_slides_to_show="2,3"]


NOTE: MobileFirst is auto selected, so the mobile settings is defined by slides_to_show and slides_to_scroll. Larger sizes are defined in the responsive_breakpoints and responsive_slide_to_show parameters.



Carousel with content from Content Blocks and template:

[ingeni-slick show_thumbs=0 shuffle=0 post_ids="153,158" post_type="content_block" template="right-featured.php" template_function_call="do_right_featured_template"]


Carousel of Woocommerce Products, with responsive breakpoints and custom template:

[ingeni-slick show_thumbs=0 shuffle=0 post_ids="1291,1293,1296,1297" post_type="product" template="product_thumb.php" template_function_call="do_product_thumb_template" show_arrows=0 show_dots=0 slides_to_show=1 slides_to_scroll=1 center_mode=1 variable_width=0 wrapper_class="mbm_product_slider" speed=50000 fade=0 responsive_breakpoints="640,1024" responsive_slides_to_show="2,4" slider_class="mbm_carousel]





== Adding a Lightbox ==

Lightbox functionality is supported using slick-lightbox ( https://www.npmjs.com/package/slick-lightbox ).

Simply add the lightbox=1 parameter. E.g.:

[ingeni-slick source_path="/gallery/ceremony/" slider_class="photo_gallery" show_thumbs=0 show_arrows=1 show_dots=0 slides_to_show=2 slides_to_scroll=1 center_mode=1 variable_width=0 speed=3000 fade=0 bg_images=1 lightbox=1]







== Changelog ==

v2017.01 - Initial version, based on Ingeni slick Carousel v2016.01

v2019.01 - Added Github-based updating.
					- Refreshed with Slick Slider 1.9.0 - https://github.com/kenwheeler/slick/
v2019.02	- Improved calling getcwd()
					- Implemented displaying images as background images
					- Implemented displaying the featured images from posts of a specific category

v2019.03  - Added the 'file_ids' parameter. Allows you to pass in a list if media IDs, as you get when you create a gallery within a post.

v2019.04 - Added the 'post_ids', 'post_type' and 'orderby' options - supply a list of post ids that become the content of the slider.

v2019.05 - Added support for 'fade', 'center_mode', 'variable_width' options.

v2019.06 - Added the 'link_post' option. Allows linking to slides sourced from posts.

v2019.07 - Added the 'show_dots' option. Defaults to 0 or off.

v2019.08 - More complete implementation of link_posts.
 Added the 'translucent_layer_class' option.
 Trapping of invalid paths at scandir().

v2019.09 - Added the 'slides_to_show' option.
 - Added support for MP4 videos

v2020.01 - Added delay_start msec timer to delay video/slider start. Defaults to 0. Max value = 60000.

v2020.02 - Added the slides_to_scroll option. Defaults to 1.

v2020.03 - Fixed bug where slides_to_show could be set < 1.
 - Updated plugin checker updater to 4.9
 - Reverted to slick carousel 1.8.1 - latest supported version

v2020.04 - Plugin update code should have been called by the WP init hook.

v2020.05 - When loading background videos, the source_path was not being respected.

v2020.06 - Added 'show_content' option - display content from a post to be used an an overlay - e.g., text overlaying image
 - Added the 'order' param.

v2020.07 - Make sure the path exists before calling scandir().
 - show_dots now respected in the slider nav block.

v2020.08 - Added support for templates via the 'template' shortcode parameter. Will search in the {theme}/ingeni-slick-templates and then the plugin template folder for a matching template file.

v2020.09 - Fixed bug calling wrong function during Exception handling, plus extra error msging when no photos found.

v2020.10 - Fixed bug - was not checking the absolute path for a template file stored in the theme folder
 - 'order' parameter now used when querying posts.
 - For template or content based slides, the post_ids argument is now included.

v2020.11 - Added the 'template_function_call' parameter - allows you to specify a custom 'do_slick_template' function name in slider templates. Required when you have multiple sliders on a single page.
 - For template based slides, the 'category' parameter now specifies the category name, not the category ID.

v2020.12 - Was not correctly checking for the existence of the function specified by the 'template_function_call' parameter. 

v2021.01 - Fixed a bug in my JS call - cannot use the fade attrib when slidesToShow > 1.

v2021.02 - Added support for responsive carousels via the responsive_breakpoints and responsive_slides_to_show params.

v2021.03 - Make sure there are no double commas in the Slick JS parameters.

v2021.04 - Added the 'slider_class' parameter - allows you to specify unique JS class to permit multiple sliders on a single page.

v2021.05 - Added the 'pause_on_hover' parameter - defaults to 1.

v2021.06 - Added a 'data' attribute to the div of background images, which contains the URL of the image.
- Added the 'lightbox' parameter - implements the lightbox from https://www.npmjs.com/package/slick-lightbox

v2021.07 - Product carousels now use the slides_to_show parameter to control how many products are displayed. max_thumbs is used to determine how many products are retrieved from the database. Therefore max_thumbs should equal the largest value of responsive_slides_to_show.

v2022.01 - do_ingeni_slick() - Fixed problem with trying to shuffle empty array of photos.

v2022.02 - do_ingeni_slick() - Misc PHP 8 fixes.

v2022.03 - do_ingeni_slick() - Initialise home_page variable.

v2022.04 - do_ingeni_slick() - Implemented 'orderby' param for image based slides when content comes from a post, content_block, etc featured image.

v2023.01 - do_ingeni_slick() - When using templates, also check for a child theme path ( using get_stylesheet_directory() ).

v2023.02 - do_ingeni_slick() - Support the use of 'attachment' post_type (e.g., media library items). Used when overriding the WP gallery shortcode.


v2023.03 - Make sure calls to get_posts() include the parameter 'posts_per_page' = max_thumbs;
- Set max_thumbs default to -1 (i.e, get all).
- Added the image_size parameter - allows you to decide which size image to retrieve from the WP media centre.

v2023.04 - Support post_ids parameter when using a custom template. Hint, add orderby="post__in" to order of the posts as listed in the post_ids comma separated string.

v2023.05 - When using post_ids parameter and a custom template, make sure to set the post_type as part of the query.


