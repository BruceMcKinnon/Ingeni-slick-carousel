<?php
/*
Plugin Name: Ingeni Slick Carousel
Version: 2019.03
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
*/

add_shortcode( 'ingeni-slick','do_ingeni_slick' );
function do_ingeni_slick( $args ) {

	$params = shortcode_atts( array(
		'source_path' => '/photos-bucket/',
		'wrapper_class' => 'ingeni-slick-wrap',
		'sync_thumbs' => 1,
		'max_thumbs' => 0,
		'show_thumbs' => 1,
		'show_arrows' => 1,
		'shuffle' => 1,
		'file_list' => "",
		'file_ids' => "",
		'file_path' => "",
		'autoplay' => 1,
		'start_path' => "",
		'bg_images' => 0,
		'category' => '',
		'speed' => 2000,
	), $args );


	if ( strlen($params['category']) > 0 ) {
		$photos = array();

		$post_attribs = array (
			'posts_per_page' => $params['max_thumbs'],
			'offset' => 0,
			'category_name' => $params['category']
		);
		$myquery = new WP_Query( $post_attribs );
	
		if ( $myquery->have_posts() ) {
			while ( $myquery->have_posts() ) {
				$myquery->the_post();
				$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );

				array_push( $photos, $thumb_url );
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
	
	$idx = 0;
	if ($params['shuffle'] > 0) {
		shuffle($photos);
	}
	foreach ($photos as $photo) {
		if ( (strpos(strtolower($photo),'.jpg') !== false) || (strpos(strtolower($photo),'.png') !== false) ) {		
			if ($params['bg_images'] > 0) {
				$sync1 .= '<div class="item"><div class="bg-item" style="background-image:url('. $home_path . $photo .')" draggable="false"></div></div>';				
			} else {
				$sync1 .= '<div class="item"><img src="'. $home_path . $photo .'" draggable="false"></img></div>';
			}
			++$idx;
			if ( ($idx > $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
				break;
			}
		}
	}

	$sync2 = $sync1;



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

	$js = "<script>jQuery(document).ready(
			function($) {
				jQuery('.".$slider_for_class."').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: ". $params['show_arrows'] . ",
					fade: true,
					autoplay: ". $params['autoplay'] . ",
					autoplaySpeed: " . $params['speed'] . ",";
	if ( ($params['show_thumbs'] != 0) && ($params['sync_thumbs'] != 0) ) {
		$js .= "asNavFor: '.".$slider_nav_class."',";
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