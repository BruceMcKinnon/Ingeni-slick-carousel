<?php
/*
Plugin Name: Ingeni Slick Carousel
Version: 2017.01
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Slick-based carousel for Wordpress
*/

/*
Copyright (c) 2017 Ingeni Web Solutions
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

*/

add_shortcode( 'ingeni-slick','do_ingeni_slick' );
function do_ingeni_slick( $atts ) {

	extract( shortcode_atts( array(
		'source_path' => '/photos-bucket/',
		'wrapper_class' => 'ingeni-slick-wrap',
		'sync_thumbs' => 1,
		'max_thumbs' => 0,
		'show_nav' => 1,
		'shuffle' => 1,
		'file_list' => "",
		'file_path' => "",
	), $atts ) );


	if ( strlen($file_list) > 0 ) {
		$photos = explode(",",$file_list);
		$home_path = $file_path;
	} else {
		$photos = scandir(getcwd() . $source_path);
		$home_path = get_bloginfo('url') . $source_path;
	}
	
	$sync1 = "";
	$sync2 = "";

	$slider_for_class = "slider-for";
	$slider_nav_class = "slider-nav";
	
	$idx = 0;
	if ($shuffle > 0) {
		shuffle($photos);
	}
	foreach ($photos as $photo) {
		if ( (strpos(strtolower($photo),'.jpg') !== false) || (strpos(strtolower($photo),'.png') !== false) ) {		
			$sync1 .= '<div class="item"><img src="'. $home_path . $photo .'" draggable="false"></img></div>';
			++$idx;
			if ( ($idx > $max_thumbs) && ($max_thumbs > 0) ) {
				break;
			}
		}
	}

	$sync2 = $sync1;
	
	$sync1 = '<div class="'.$slider_for_class.'">' . $sync1 . '</div>';
	if ($sync_thumbs  > 0) {
		$sync2 = '<div class="'.$slider_nav_class.'">' . $sync2 . '</div>';
	} else {
		$sync2 = '';
	}
	
	/*
	if ($show_nav > 0) {
		$sync1 .= '<div id="slick_show_nav"></div>';
	}
	*/
	

	$js = "<script>jQuery(document).ready(
			function($) {
				$('.slider-for').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					fade: true,
					asNavFor: '.".$slider_nav_class."',
					autoplay: true
				});
				$('.slider-nav').slick({
					slidesToShow: 3,
					slidesToScroll: 1,
					asNavFor: '.".$slider_for_class."',
					arrows: false,
					dots: true,
					centerMode: true,
					focusOnSelect: true
				});
			});</script>";

	return '<div class="'.$wrapper_class.'">'.$sync1.$sync2.'</div>'.$js;
}


function ingeni_load_slick() {
	$dir = plugins_url( 'slick/', __FILE__ );

	// Slick slider
	wp_enqueue_style( 'slick-css', $dir . 'slick.css' );
	wp_enqueue_style( 'slick-theme-css', $dir . 'slick-theme.css' );

	wp_register_script( 'slick_js', $dir .'slick.min.js', false, '1.8', true );
	wp_enqueue_script( 'slick_js' );
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