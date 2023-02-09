<?php
/*
Plugin Name: Ingeni Slick Carousel
Version: 2023.01

Plugin URI: https://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: https://ingeni.net
Description: Slick-based carousel for Wordpress
*/

/*
Copyright (c) 2021 Ingeni Web Solutions
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Disclaimer: 
	Use at your own risk. No warranty expressed or implied is provided.
	This program is free software; you can redistribute it and/or modify 
	it under the terms of the GNU General Public License as published by 
	the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 	See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Requires : Wordpress 3.x or newer ,PHP 5 +

v2017.01 - Initial version, based on Ingeni slick Carousel v2016.01
v2019.01 - Integrated Github plugin updating.
					- Refreshed with Slick Slider 1.9.0 - https://github.com/kenwheeler/slick/
v2019.02	- Improved calling getcwd()
					- Implemented displaying images as background images
					- Implemented displaying the featured images from posts of a specific category
v2019.03  - Added the 'file_ids' parameter. Allows you to pass in a list if media IDs,
						as you get when you create a gallery within a post.
v2019.04	- Added the 'post_ids', 'post_type' and 'orderby' options - supply a list of post ids that become the content of the slider.
v2019.05	- Added support for 'fade', 'center_mode', 'variable_width' options.
v2019.06  - Added the 'link_post' option. Allows linking to slides sourced from posts.
v2019.07  - Added the 'show_dots' option. Defaults to 0 or off.
v2019.08  - More complete implementation of link_posts.
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
v2020.07	- Make sure the path exists before calling scandir().
					- show_dots now respected in the slider nav block.
v2020.08 - Added support for templates via the 'template' shortcode parameter. Will search in the {theme}/ingeni-slick-templates and then the plugin template folder for a matching template file.
v2020.09 - Fixed bug - calling wrong function during Exception handling, plus extra error msging when no photos found.
v2020.10 - Fixed bug - was not checking the absolute path for a template file stored in the theme folder
				 - 'order' parameter now used when querying posts.
				 - For template or content based slides, the post_ids argument is now included.
v2020.11 - Added the 'template_function_call' parameter - allows you to specify a custom 'do_slick_template' function name in slider templates. Required when you have multiple sliders on a single page.
				 - For template based slides, the 'category' parameter now specifies the category name, not the category ID.
v2020.12 - Was not correctly checking for the existance of the function specified by the 'template_function_call' parameter. 

v2021.01 - Fixed a bug in my JS call - cannot use the fade attrib when slidesToShow > 1.

v2021.02 - Added support for responsive carousels via the responsive_breakpoints and responsive_slides_to_show params.

v2021.03 - Make sure there are no double commas in the Slick JS parameters.

v2021.04 - Added the 'slider_class' parameters- allows you to specify unique JS class to permit multiple sliders on a single page.

v2021.05 - Added the 'pause_on_hover' parameter - defaults to 1.

v2021.06 - Added a 'data' attribute to the div of background images, which contains the URL of the image. The attribute can use used when adding a lightbox to the slider.
	     - Added the 'lightbox' parameter - implements the lightbox from https://www.npmjs.com/package/slick-lightbox

v2021.07 - Product carousels now use the slides_to_show parameter to control how many products are displayed.

v2022.01 - do_ingeni_slick() - Fixed problem with trying to shuffle empty array of photos.

v2022.02 - do_ingeni_slick() - Misc PHP 8 fixes.

v2022.03 - do_ingeni_slick() - Initialise home_page variable.

v2022.04 - do_ingeni_slick() - Implemented 'orderby' param for image based slides when content comes from a post, content_block, etc featured image.

v2023.01 - do_ingeni_slick() - When using templates, also check for a child theme path ( using get_stylesheet_directory() ).

*/

if (!function_exists("ingeni_slick_log")) {
	function ingeni_slick_log($msg) {
		$upload_dir = wp_upload_dir();
		$logFile = $upload_dir['basedir'] . '/' . 'ingeni_slick_log.txt';
		date_default_timezone_set('Australia/Sydney');

		// Now write out to the file
		$log_handle = fopen($logFile, "a");
		if ($log_handle !== false) {
			fwrite($log_handle, date("H:i:s").": ".$msg."\r\n");
			fclose($log_handle);
		}
	}
}

if (!function_exists("endsWith")) {
	function endsWith($haystack, $needle) {
			// search forward starting from end minus needle length characters
			return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}
}


add_shortcode( 'ingeni-slick','do_ingeni_slick' );
function do_ingeni_slick( $args ) {

	$params = shortcode_atts( array(
		'source_path' => '/photos-bucket/',
		'wrapper_class' => 'ingeni-slick-wrap',
		'sync_thumbs' => 1,
		'max_thumbs' => 0,
		'show_thumbs' => 1,
		'show_arrows' => 1,
		'show_dots' => 0,
		'shuffle' => 1,
		'file_list' => "",
		'file_ids' => "",
		'file_path' => "",
		'autoplay' => 1,
		'start_path' => "",
		'bg_images' => 0,
		'category' => '',
		'speed' => 2000,
		'post_ids' => '',
		'post_type' => 'content_block',
		'orderby' => 'title',
		'order' => 'ASC',
		'center_mode' => 0,
		'variable_width' => 0,
		'fade' => 1,
		'adaptive_height' => 1,
		'thumbnail_size' => 'full',
		'show_title' => 0,
		'translucent_layer_class' => '',
		'link_post' => 0,
		'slides_to_show' => 1,
		'delay_start' => 0,
		'slides_to_scroll' => 1,
		'show_content' => 0,
		'template' => '',
		'template_function_call' => 'do_slick_template',
		'responsive_breakpoints' => '',
		'responsive_slides_to_show' => '',
		'slider_class' => 'ingeni_slider_',
		'pause_on_hover' => 1,
		'lightbox' => 0,
	), $args );


	$titles = array();
	$links = array();
	$content = array();

	$photos = null;
	$home_path = '';

	$slider_for_class = $params['slider_class'] . '_for';
	$slider_nav_class = $params['slider_class'] . '_nav';
	
	$sort_order = strtoupper($params['order']);
	if ($params['order'] != 'DESC') {
		$sort_order = 'ASC';
	}

//ingeni_slick_log('params:'.print_r($params,true));

		// Attempt to load a template file
		$template_file = '';
		if ( $params['template'] != '' ) {

			if ( file_exists( plugin_dir_path( __FILE__ ) . '/templates/'.$params['template'] ) ) {
				$template_file = plugin_dir_path( __FILE__ ) . '/templates/'.$params['template'];
			}
			if ( file_exists( get_template_directory() .'/ingeni-slick-templates/'.$params['template'] ) ) {
				$template_file = get_template_directory() .'/ingeni-slick-templates/'.$params['template'];
			}
			if ( file_exists( get_stylesheet_directory() .'/ingeni-slick-templates/'.$params['template'] ) ) {
				$template_file = get_stylesheet_directory() .'/ingeni-slick-templates/'.$params['template'];
			}
		}
//ingeni_slick_log('template_file:'.$template_file);

		if ( file_exists( $template_file ) ) {
			// Template-based  content
			include_once($template_file);

			$id_array = array();
			if (strlen($params['post_ids']) > 0) {
				$id_array = explode(",",$params['post_ids']);
			}
	
			if ( function_exists($params['template_function_call']) ) {
	
				// Handle WooCommerce products

				if ( $params['post_type'] == 'product' ) {
					$args = array(
						'orderby' => $params['orderby'],
						'order' => $sort_order,
						'numberposts' => $params['max_thumbs'],
						'post_type' => $params['post_type'],
						'tax_query' => array(
								array(
										'taxonomy' => 'product_cat',
										'terms' => array_map( 'sanitize_title', explode( ',', $params['category'] ) ),
										'field' => 'slug',
										'operator' => 'AND',
								)
							)
					);

					if ($id_array) {
						$args = array_merge($args, array('post__in' => $id_array) );
					}

				} else {
					$args = array(
						'category_name' => $params['category'],
						'post_type' => $params['post_type'],
						'orderby' => $params['orderby'],
						'order' => $sort_order,
						'numberposts' => $params['max_thumbs'],
					);

					if ($id_array) {
						$args = array_merge($args, array('post__in' => $id_array) );
					}
				}
ingeni_slick_log(print_r($args,true));
				$idx = 0;
				$content_post = get_posts( $args );
	
				$inline_style = "";
	
				$sync1 = "";
				$sync2 = "";
	
	
				//$slider_for_class = "slider-for";
				//$slider_nav_class = "slider-nav";
	
				foreach( $content_post as $post ) {
//ingeni_slick_log('template call: '.$params['template_function_call']);
	
					$sync1 .= '<div class="item">' . call_user_func( $params['template_function_call'], $post ) . '</div>';
				}
			}
		} elseif ( strlen($params['post_ids']) > 0 ) {
//ingeni_slick_log('no template. post ids');
		//
		// Content based slides
		//
		$id_array = explode(",",$params['post_ids']);

		$args = array(
			'post__in' => $id_array,
			'post_type' => $params['post_type'],
			'class' => 'content_block_featured',
			'orderby' => $params['orderby'],
			'order' => $sort_order,
		);
//fb_log(print_r($args,true));
		$idx = 0;
		$content_post = get_posts( $args );

		$inline_style = "";

		$sync1 = "";
		$sync2 = "";


		//$slider_for_class = "slider-for";
		//$slider_nav_class = "slider-nav";
		$content = array();

		foreach( $content_post as $post ) {
//fb_log(print_r($post,true));
			if ( has_post_thumbnail( $post->ID ) ) {
				$thumb_id = get_post_thumbnail_id($post->ID);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', false);
				$style = 'background-image: url('. $thumb_url[0] .')';
				
				array_push( $titles, get_the_title($post->ID) );

				array_push( $links, get_the_permalink($post->ID) );

				array_push( $content, get_the_content($post->ID) );

				if ($params['link_post'] > 0) {
					$sync1 .= '<a href="'.get_the_permalink($post->ID).'">';
				}

				$sync1 .= '<div class="item"><div style="'.$style.'"><div class="title-layer"><h3>' . get_the_title($post->ID) .'</h3></div></div></div>';

				if ($params['link_post'] > 0) {
					$sync1 .= '</a>';
				}


			} else {
				$sync1 .= '<div class="item">' . apply_filters('the_content', $post->post_content) . '</div>';
			}

			++$idx;
			if ( ($idx > $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
				break;
			}
		}

	}	else {
//ingeni_slick_log('image based');
		//
		// Image-based slides
		//
		if ( strlen($params['category']) > 0 ) {
//ingeni_slick_log('cat');
			$photos = array();

			$order_by = $params['orderby'];
			if ( $params['shuffle'] > 0) {
				$order_by = 'rand';
			}

			$sort_order = strtoupper($params['order']);
			if ($params['order'] != 'DESC') {
				$sort_order = 'ASC';
			}

			$post_attribs = array (
				'posts_per_page' => $params['max_thumbs'],
				'offset' => 0,
				'category_name' => $params['category'],
				'orderby' => $order_by,
				'order' => $sort_order,
			);

			//ingeni_slick_log(print_r($post_attribs,true));
			$myquery = new WP_Query( $post_attribs );
		
			if ( $myquery->have_posts() ) {
				while ( $myquery->have_posts() ) {
					$myquery->the_post();
					$thumb_url = get_the_post_thumbnail_url( get_the_ID(), $params['thumbnail_size'] );

					array_push( $photos, $thumb_url );
					array_push( $titles, get_the_title() );
					array_push( $links, get_the_permalink() );
					array_push( $content, get_the_content() );
				}
			}

		} elseif ( strlen($params['file_list']) > 0 ) {
//ingeni_slick_log('file list');
			//
			// A list of file names were passed in
			//
			$photos = explode(",",$params['file_list']);
			$home_path = $params['file_path'];

		} elseif ( strlen($params['file_ids']) > 0 ) {
//ingeni_slick_log('file ids');
			//
			// If a list of media ID, get the source URLs and create a file_list
			//
			$photos = array();
			$home_path = "";
			//ingeni_slick_log('file ids='.$params['file_ids']);

			$media_ids = array();
			$media_ids = explode(",",$params['file_ids']);

			$source_urls = "";
			$idx = 0;


			foreach($media_ids as $media_id) {
				$source_urls .= wp_get_attachment_url( $media_id ) . ',';
			}
			$source_urls = substr($source_urls,0,strlen($source_urls)-1);

			$params['file_list'] = $source_urls;
			$params['file_path'] = "";

			// Now prepare the list of the slider
			$photos = explode(",",$params['file_list']);
			$home_path = $params['file_path'];

		
		} else {
			try {
				if ($params['start_path'] != '') {
					chdir($params['start_path']);
				}
//ingeni_slick_log('curr path:'.getcwd() .'|'.$params['source_path']);
				$root_dir = getcwd();
				if (stripos($root_dir, '/wp-admin') !== FALSE ) {
					$root_dir = str_ireplace('/wp-admin','',$root_dir);
				}
				if ( !file_exists($root_dir . $params['source_path']) ) {
					throw new Exception('Path does not exist: '.$root_dir . $params['source_path']);
				} else {
					$photos = scandir( $root_dir . $params['source_path']);
					if (!$photos) {
						throw new Exception('Error while scanning: '.$root_dir . $params['source_path']);
					}
				}
			} catch (Exception $ex) {
				if ( function_exists("ingeni_slick_log") ) {
					ingeni_slick_log('Scanning folder '.$params['source_path'].' : '.$ex->getMessage());
				}
			}
			$home_path = get_bloginfo('url') . $params['source_path'];
		}

		$sync1 = "";
		$sync2 = "";

		//$slider_for_class = "slider-for";
		//$slider_nav_class = "slider-nav";

		if ($params['bg_images'] == 1) {
			$params['adaptive_height'] = 'false';
		} else {
			if ($params['adaptive_height'] == 0) {
				$params['adaptive_height'] = 'false';
			} else {
				$params['adaptive_height'] = 'true';
			}			
		}

		$idx = 0;
		if ( ($params['shuffle'] > 0) && ($params['show_title'] == 0) ) {
			if ( $photos ) {
				shuffle($photos);
			}
		}
//ingeni_slick_log('photos to show: '.print_r($photos,true));

		if ( !$photos ) {
			// We have no photos
			return '<div class="'.$params['wrapper_class'].'"><p>Sorry, nothing to show!</p></div>';

		} else {
			foreach ($photos as $photo) {
				if ( (strpos(strtolower($photo),'.jpg') !== false) || (strpos(strtolower($photo),'.png') !== false)  || (strpos(strtolower($photo),'.mp4') !== false) ) {		
	//ingeni_slick_log('photo to show: '.$home_path . $photo);
					if ($params['bg_images'] > 0) {

						if ($params['link_post'] > 0) {
							$sync1 .= '<a href="'.$links[$idx].'">';
						}

						if ( endsWith($photo,'.mp4') ) {

							// Disable autoplay if the first slide is a video
							if ($idx == 0) {
								$params['autoplay'] = 0;
							}

							$sync1 .= '<div class="item"><div class="slick-video-wrap hide-for-small" >';
							$sync1 .= '<video class="slick-video" id="slick-video-'.$idx.'"muted preload data-origin-x="0" data-origin-y="0" >';
								$source_img = get_bloginfo('url') . '/' .$params['source_path'] .'/' . $photo;
								$source_img = str_replace('\/\/','\/',$source_img);
							$sync1 .= '<source src="' . $source_img . '" type="video/mp4">';
							$sync1 .= 'Your browser does not support the video tag.</video>';

						} else {
							$sync1 .= '<div class="item" id="slick-image-'.$idx.'"><div class="bg-item" style="background-image:url('. $home_path . $photo .')" data="'. $home_path . $photo .'" draggable="false">';
						}

						if ($params['translucent_layer_class'] !== '') {
							$sync1 .= '<div class="' . $params['translucent_layer_class'] . '"></div>';
						}
						if ($params['show_title'] > 0) {

							if ( count($titles) > $idx ) {
								$slide_title = $titles[$idx];
							} else {
								$slide_title = '';
							}

							$sync1 .= '<div class="slide_title">' . $slide_title . '</div>';

						} elseif ($params['show_content'] > 0) {
							// v2020.06 - Insert the content from a post
							if ( count($content) > $idx ) {
								$slide_content = $content[$idx];
							} else {
								$slide_content = '';
							}

							$sync1 .= '<div class="slide_content">' . $slide_content . '</div>';
						}

						$sync1 .= '</div></div>';

						if ($params['link_post'] > 0) {
							$sync1 .= '</a>';
						}
		
					} else {
	//ingeni_slick_log($home_path . $photo);
						$sync1 .= '<div class="item"><img src="'. $home_path . $photo .'" draggable="false"></img></div>';
					}
					++$idx;
					if ( ($idx > $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
						break;
					}
				}
			}
		}
	}

	//$sync2 = $sync1;

	$sync2 = str_replace($links,"#",$sync1);

	
	//ingeni_slick_log('links: '.print_r($links,true));
	//ingeni_slick_log('titles: '.print_r($titles,true));
	

	//if ( (!is_int($params['slides_to_show']) ) || ($params['slides_to_show'] < 0) ) {
		//$params['slides_to_show'] = 1;
	//}
	//if ( (!is_int($params['slides_to_scroll']) ) || ($params['slides_to_scroll'] < 1) ) {
		//$params['slides_to_scroll'] = 1;
	//}


	if ( $params['slides_to_show'] < 1 ) {
		$params['slides_to_show'] = 1;
		$params['fade'] = "false";
	}

	// Grab the responsive settings
	$responsive_settings = '';

//fb_log('param breaks:'.$params['responsive_breakpoints']);
//fb_log('param show:'.$params['responsive_slides_to_show']);
	if ( strlen($params['responsive_breakpoints']) > 1 ) {

		$responsive_breakpoints = explode(',',$params['responsive_breakpoints']);
		$responsive_slides_to_show = explode(',',$params['responsive_slides_to_show']);
//fb_log('breaks:'.print_r($responsive_breakpoints,true));
//fb_log('show:'.print_r($responsive_slides_to_show,true));

		if ( is_array($responsive_breakpoints) && is_array($responsive_slides_to_show) ) {
//fb_log('a');
			if ( count($responsive_breakpoints) == count($responsive_slides_to_show) ) {
				$total_breakpoints = count($responsive_breakpoints);
//fb_log('count:'.$total_breakpoints);
				$idx_break = 0;
				for ($idx_break = 0; $idx_break < $total_breakpoints; ++$idx_break) {
					//if ( (is_int($responsive_slides_to_show[$idx_break])) && (is_int($responsive_slides_to_show[$idx_break])) ) {
//fb_log('b '.$idx_break);
						$responsive_settings .= '{ breakpoint: '.$responsive_breakpoints[$idx_break].',';
							$responsive_settings .= 'settings: { slidesToShow: '.$responsive_slides_to_show[$idx_break].', slidesToScroll: '.$responsive_slides_to_show[$idx_break].' }';
						$responsive_settings .= '},';
					//}
				}
			}
		}

		if ( strlen($responsive_settings) > 0 ) {
			$responsive_settings = 'mobileFirst:true,responsive: [' . $responsive_settings . ']';
		}
//fb_log('responsive_settings: '.$responsive_settings);		
	}


	if ($params['autoplay'] == 1) {
		$params['autoplay'] = 'true';
	} else {
		$params['autoplay'] = 'false';
	}

	if ($params['show_arrows'] == 1) {
		$params['show_arrows'] = 'true';
	} else {
		$params['show_arrows'] = 'false';
	}
	if ($params['show_dots'] == 1) {
		$params['show_dots'] = 'true';
	} else {
		$params['show_dots'] = 'false';
	}

	if ($params['fade'] == 1) {
		$params['fade'] = 'true';
	} else {
		$params['fade'] = 'false';
	}


	$data_attribs = ' data-slick=\'{"slidesToShow":'.$params['slides_to_show'].',"slidesToScroll":'.$params['slides_to_scroll'].',"arrows":'.$params['show_arrows'].',"speed":'.$params['speed'].',"fade":'.$params['fade'].',"autoplay":'.$params['autoplay'].'}\'';
	$data_attribs = '';


	
	$sync1 = '<div class="'.$slider_for_class.'">' . $sync1 . '</div>';
	if ($params['show_thumbs']  > 0) {
		$sync2 = '<div class="'.$slider_nav_class.'">' . $sync2 . '</div>';
	} else {
		$sync2 = '';
	}

	


	// Can't use fade and centerMode/variableWidth together.
	if ( ($params['center_mode'] == 1) && ($params['variable_width'] == 1) ) {
		$params['fade'] = 'false';
	}

	if ($params['center_mode'] == 1) {
		$params['center_mode'] = 'true';
	} else {
		$params['center_mode'] = 'false';
	}

	if ($params['pause_on_hover'] == 1) {
		$params['pause_on_hover'] = 'true';
	} else {
		$params['pause_on_hover'] = 'false';
	}

	if ($params['variable_width'] == 1) {
		$params['variable_width'] = 'true';
	} else {
		$params['variable_width'] = 'false';
	}


	if ( !is_numeric( $params['delay_start'] ) ) {
		$params['delay_start']  = 0;
	} else {
		$params['delay_start'] = intval($params['delay_start']);
	}

	if ( $params['delay_start'] < 0 ) {
		$params['delay_start'] = 0;
	} elseif ( $params['delay_start'] > 60000 ) {
		$params['variable_width'] = 60000;
	}

	$js = "<script>if ( jQuery('.slick-video').length > 0 ) {
//console.log('there is video');
		jQuery('.".$slider_for_class."').on('init', function(event, slick) {

			setTimeout( function() { slickCheckVideo(0); }, " . $params['delay_start'] . ");
		
		});



		jQuery('.".$slider_for_class."').on('afterChange', function(event, slick, currentSlide, nextSlide) {
			slickCheckVideo(currentSlide);
		});

		function slickCheckVideo( currentSlide ) {
//console.log('checking current slide:'+currentSlide);
			if ( jQuery('#slick-video-'+currentSlide).length > 0 ) {
				try {
					jQuery('.".$slider_for_class."').slick('slickPause');
				} catch (exc) {
					console.log('checking current slide:'+currentSlide);
				}
				try {
					jQuery('#slick-video-'+currentSlide)[0].play(); 
				} catch(exe) {
					console.log('play exception current slide:'+currentSlide);
					jQuery('.".$slider_for_class."').slick('slickPlay');
				}
			}
		}
		jQuery('.slick-video').on('paused',function(){
console.log('** paused');           
			jQuery('.".$slider_for_class."').slick('slickPlay');
		});
		jQuery('.slick-video').on('ended',function(){           
			jQuery('.".$slider_for_class."').slick('slickPlay');
		});
	}</script>";

	$js .= "<script>var $ = jQuery();jQuery(document).ready(
			function($) {
				console.log('initialising slider ".$slider_for_class."');
				jQuery('.".$slider_for_class."').slick({
					slidesToShow: " . $params['slides_to_show'] . ",
					slidesToScroll: " . $params['slides_to_scroll'] . ",
					infinite: true,
					adaptiveHeight: " . $params['adaptive_height'] . ",
					arrows: ". $params['show_arrows'] . ",
					dots:  ". $params['show_dots'] . ",
					autoplay: ". $params['autoplay'] . ",
					autoplaySpeed: " . $params['speed'] . ",
					centerMode: " . $params['center_mode'] . ",
					pauseOnHover: " . $params['pause_on_hover'] . ",
					variableWidth: " . $params['variable_width'].",".$responsive_settings;
	if ( ($params['slides_to_show'] < 2) ) {
		$js .= ",fade: " . $params['fade'];
	}		
	if ( ($params['show_thumbs'] != 0) && ($params['sync_thumbs'] != 0) ) {
		$js .= ",asNavFor: '.".$slider_nav_class."',";
	}


	$js .= "});";

	// Make sure there are no double commas
	$js = str_replace(",,",",",$js);

	if ($params['show_thumbs'] != 0) {
		$js .= "jQuery('.".$slider_nav_class."').slick({
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows: false,
					dots: true,
					asNavFor: '.".$slider_for_class."',
					centerMode: true,
					focusOnSelect: true
				});";
		}

	if ($params['lightbox'] == 1) {
		$obj_target = 'src';
		if ($params['bg_images'] > 0) {
			$obj_target = '.bg-item';
		}
		$js .= "jQuery('.".$slider_for_class."').slickLightbox({
			src: 'data',
			itemSelector: '.item ".$obj_target."'
		});";
	}

	$js .= "});";

	$js .= "</script>";


	return '<div class="'.$params['wrapper_class'].'">'.$sync1.$sync2.'</div>'.$js;
}


function ingeni_load_slick() {
	$dir = plugins_url( 'slick/', __FILE__ );

	// Slick slider
	wp_enqueue_style( 'slick-css', $dir . 'slick.css' );
	wp_enqueue_style( 'slick-theme-css', $dir . 'slick-theme.css' );

	wp_register_script( 'slick_js', $dir .'slick.min.js', false, '1.8', true );
	wp_enqueue_script( 'slick_js' );


	// Slick lightbox - https://www.npmjs.com/package/slick-lightbox
	$dir = plugins_url( 'slick-lightbox/', __FILE__ );
	wp_enqueue_style( 'slick-lightbox-css', $dir . 'slick-lightbox.css' );
	wp_register_script( 'slick_lightbox_js', $dir .'slick-lightbox.js', false, '0.1', true );
	wp_enqueue_script( 'slick_lightbox_js' );

	//
	// Plugin CSS
	//
	wp_enqueue_style( 'ingeni-slick-css', plugins_url('ingeni-slick-carousel.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'ingeni_load_slick' );


function ingeni_update_slick() {
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/Ingeni-slick-carousel',
		__FILE__,
		'Ingeni-slick-carousel'
	);
	
	//Optional: If you're using a private repository, specify the access token like this:
	//$myUpdateChecker->setAuthentication('your-token-here');
	
	//Optional: Set the branch that contains the stable release.
	//$myUpdateChecker->setBranch('stable-branch-name');

}
add_action( 'init', 'ingeni_update_slick' );


// Plugin activation/deactivation hooks
function ingeni_slick_activation() {
	flush_rewrite_rules( false );
}
register_activation_hook(__FILE__, 'ingeni_slick_activation');

function ingeni_slick_deactivation() {
  flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'ingeni_slick_deactivation' );

?>