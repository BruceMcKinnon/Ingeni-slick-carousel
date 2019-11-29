<?php
/*
Plugin Name: Ingeni Slick Carousel
Version: 2019.08
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Slick-based carousel for Wordpress
*/

/*
Copyright (c) 2019 Ingeni Web Solutions
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
*/

if (!function_exists("fb_log")) {
	function fb_log($msg) {
		$upload_dir = wp_upload_dir();
		$logFile = $upload_dir['basedir'] . '/' . 'fb_log.txt';
		date_default_timezone_set('Australia/Sydney');

		// Now write out to the file
		$log_handle = fopen($logFile, "a");
		if ($log_handle !== false) {
			fwrite($log_handle, date("H:i:s").": ".$msg."\r\n");
			fclose($log_handle);
		}
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
		'center_mode' => 0,
		'variable_width' => 0,
		'fade' => 1,
		'adaptive_height' => 0,
		'thumbnail_size' => 'full',
		'show_title' => 0,
		'translucent_layer_class' => '',
		'link_post' => 0,
	), $args );


	$titles = array();
	$links = array();

//fb_log('params:'.print_r($params,true));

	if ( strlen($params['post_ids']) > 0 ) {
		//
		// Content based slides
		//
		$id_array = explode(",",$params['post_ids']);

		$args = array(
			'post__in' => $id_array,
			'post_type' => $params['post_type'],
			'class' => 'content_block_featured',
			'orderby' => $params['orderby'],
		);

		$idx = 0;
		$content_post = get_posts( $args );

		$inline_style = "";

		$sync1 = "";
		$sync2 = "";


		$slider_for_class = "slider-for";
		$slider_nav_class = "slider-nav";

		foreach( $content_post as $post ) {
			if ( has_post_thumbnail( $post->ID ) ) {
				$thumb_id = get_post_thumbnail_id($post->ID);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', false);
				$style = 'background-image: url('. $thumb_url[0] .')';
				
				array_push( $titles, get_the_title($post->ID) );
				array_push( $links, get_the_permalink($post->ID) );

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
		//
		// Image-based slides
		//
		if ( strlen($params['category']) > 0 ) {
			$photos = array();

			$order_by = 'date';
			if ( $params['shuffle'] > 0) {
				$order_by = 'rand';
			}
			$post_attribs = array (
				'posts_per_page' => $params['max_thumbs'],
				'offset' => 0,
				'category_name' => $params['category'],
				'orderby' => $order_by,
			);

			fb_log(print_r($post_attribs,true));
			$myquery = new WP_Query( $post_attribs );
		
			if ( $myquery->have_posts() ) {
				while ( $myquery->have_posts() ) {
					$myquery->the_post();
					$thumb_url = get_the_post_thumbnail_url( get_the_ID(), $params['thumbnail_size'] );

					array_push( $photos, $thumb_url );
					array_push( $titles, get_the_title() );
					array_push( $links, get_the_permalink() );
				}
			}

		} elseif ( strlen($params['file_list']) > 0 ) {
			//
			// A list of file names were passed in
			//
			$photos = explode(",",$params['file_list']);
			$home_path = $params['file_path'];

		} elseif ( strlen($params['file_ids']) > 0 ) {
			//
			// If a list of media ID, get the source URLs and create a file_list
			//
			$photos = array();
			$home_path = "";
			//fb_log('file ids='.$params['file_ids']);

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
	//fb_log('curr path:'.getcwd() .'|'.$source_path);
				$root_dir = getcwd();
				if (stripos($root_dir, '/wp-admin') !== FALSE ) {
					$root_dir = str_ireplace('/wp-admin','',$root_dir);
				}
				$photos = scandir( $root_dir . $params['source_path']);
				if (!$photos) {
					throw new Exception('Error while scanning: '.$root_dir . $params['source_path']);
				}
			} catch (Exception $ex) {
				if ( function_exists("fb_log") ) {
					fb_log('Scanning folder '.$params['source_path'].' : '.$ex->message);
				}
			}
			$home_path = get_bloginfo('url') . $params['source_path'];
		}

		$sync1 = "";
		$sync2 = "";

		$slider_for_class = "slider-for";
		$slider_nav_class = "slider-nav";

		if ($params['bg_images'] == 1) {
			$params['adaptiveHeight'] = 'false';
		} else {
			if ($params['adaptiveHeight'] == '0') {
				$params['adaptiveHeight'] = 'false';
			} else {
				$params['adaptiveHeight'] = 'true';
			}			
		}


		$idx = 0;
		if ( ($params['shuffle'] > 0) && ($params['show_title'] == 0) ) {
			shuffle($photos);
		}
		foreach ($photos as $photo) {
			if ( (strpos(strtolower($photo),'.jpg') !== false) || (strpos(strtolower($photo),'.png') !== false) ) {		
				if ($params['bg_images'] > 0) {

					if ($params['link_post'] > 0) {
						$sync1 .= '<a href="'.$links[$idx].'">';
					}

					$sync1 .= '<div class="item"><div class="bg-item" style="background-image:url('. $home_path . $photo .')" draggable="false">';

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
					}
					$sync1 .= '</div></div>';

					if ($params['link_post'] > 0) {
						$sync1 .= '</a>';
					}
	
				} else {
					$sync1 .= '<div class="item"><img src="'. $home_path . $photo .'" draggable="false"></img></div>';
				}
				++$idx;
				if ( ($idx > $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
					break;
				}
			}
		}

	}

	//$sync2 = $sync1;

	$sync2 = str_replace($links,"#",$sync1);

	
	fb_log('links: '.print_r($links,true));
	//fb_log('titles: '.print_r($titles,true));
	

	
	$params['fade'] = "true";
	if ( $params['slides_to_show'] < 1 ) {
		$params['fade'] = "false";
	}
	$data_attribs = ' data-slick=\'{"slidesToShow":'.$params['slides_to_show'].',"slidesToScroll":1,"arrows":'.$show_arrows.',"speed":'.$params['speed'].',"fade":'.$fade.',"autoplay":true}\'';
	$data_attribs = '';


	
	$sync1 = '<div class="'.$slider_for_class.'">' . $sync1 . '</div>';
	if ($params['show_thumbs']  > 0) {
		$sync2 = '<div class="'.$slider_nav_class.'">' . $sync2 . '</div>';
	} else {
		$sync2 = '';
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

	// Can't use fade and centerMode/variableWidth together.
	if ( ($params['center_mode'] == 1) && ($params['variable_width'] == 1) ) {
		$params['fade'] = 'false';
	}

	if ($params['center_mode'] == 1) {
		$params['center_mode'] = 'true';
	} else {
		$params['center_mode'] = 'false';
	}

	if ($params['variable_width'] == 1) {
		$params['variable_width'] = 'true';
	} else {
		$params['variable_width'] = 'false';
	}



	$js = "<script>jQuery(document).ready(
			function($) {
				jQuery('.".$slider_for_class."').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					adaptiveHeight: " . $params['adaptive_height'] . ",
					arrows: ". $params['show_arrows'] . ",
					dots:  ". $params['show_dots'] . ",
					autoplay: ". $params['autoplay'] . ",
					autoplaySpeed: " . $params['speed'] . ",
					fade: " . $params['fade'] . ",
					centerMode: " . $params['center_mode'] . ",
					variableWidth: " . $params['variable_width'];
	if ( ($params['show_thumbs'] != 0) && ($params['sync_thumbs'] != 0) ) {
		$js .= ",asNavFor: '.".$slider_nav_class."',";
	}
	$js .= "});";

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
	$js .= "});</script>";
	return '<div class="'.$params['wrapper_class'].'">'.$sync1.$sync2.'</div>'.$js;
}


function ingeni_load_slick() {
	$dir = plugins_url( 'slick/', __FILE__ );

	// Slick slider
	wp_enqueue_style( 'slick-css', $dir . 'slick.css' );
	wp_enqueue_style( 'slick-theme-css', $dir . 'slick-theme.css' );

	wp_register_script( 'slick_js', $dir .'slick.min.js', false, '1.8', true );
	wp_enqueue_script( 'slick_js' );


	// Init auto-update from GitHub repo
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/Ingeni-slick-carousel',
		__FILE__,
		'Ingeni-slick-carousel'
	);
}
add_action( 'wp_enqueue_scripts', 'ingeni_load_slick' );


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