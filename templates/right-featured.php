<?php 
//
// Half-width featured image plus content template for Ingeni Slick Slider
//

function do_right_featured_template( $this_post ) {
	try {
        $style = '';
        if ( has_post_thumbnail( $this_post->ID ) ) {
            $thumb_id = get_post_thumbnail_id($this_post->ID);
            $thumb_url = wp_get_attachment_image_src($thumb_id,'full', false);

            $style = '<div class="bg_img" style="background-image: url('. $thumb_url[0] .');"></div>';
        }

		$templateHtml = '';
		$templateHtml .= '<div class="grid-container slick-template-wrap"><div class="grid-x grid-margin-x">';

			$templateHtml .= '<div class="cell small-12 medium-6">';
			    $templateHtml .= $this_post->post_content;
			$templateHtml .= '</div>';

			$templateHtml .= '<div class="cell small-12 medium-6">';
			    $templateHtml .= $style;
			$templateHtml .= '</div>';

		$templateHtml .= '</div></div>';


	} catch (Exception $ex) {
		$templateHtml = '<p>do_slick_template: '.$ex->getMessage().'</p>';
	}

	return $templateHtml;
}

?>