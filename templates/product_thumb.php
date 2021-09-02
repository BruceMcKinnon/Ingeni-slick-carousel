<?php 
//
// Product image plus content template for Ingeni Slick Slider
//

function do_product_thumb_template( $this_post ) {
	try {
        $style = '';
        if ( has_post_thumbnail( $this_post->ID ) ) {
            $thumb_id = get_post_thumbnail_id($this_post->ID);
            $thumb_url = wp_get_attachment_image_src($thumb_id,'full', false);

            $style = '<div class="bg_img" style="background-image: url('. $thumb_url[0] .');"></div>';
        }

		$templateHtml = '';
		$templateHtml .= '<div class="grid-container full slick-template-wrap"><div class="grid-x grid-margin-x">';

			$templateHtml .= '<div class="cell small-12">';
			    $templateHtml .= $style;
			$templateHtml .= '</div>';

			$templateHtml .= '<div class="cell small-12 text-center">';
			    $templateHtml .= '<h3>'.get_the_title($this_post->ID).'</h3>';
				$templateHtml .= '<a href="'.get_the_permalink($this_post->ID).'" class="button">Learn More</a>';
			$templateHtml .= '</div>';

			
		$templateHtml .= '</div></div>';


	} catch (Exception $ex) {
		$templateHtml = '<p>do_product_thumb_template: '.$ex->getMessage().'</p>';
	}

	return $templateHtml;
}

?>