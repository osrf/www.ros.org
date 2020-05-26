<?php
/*
	Section: Highlight
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds a highlight sections with a splash image and 2-big lines of text.
	Class Name: PageLinesHighlight
	Workswith: templates, main, header, morefoot, sidebar1, sidebar2, sidebar_wrap
	Cloning: true
	Filter: component
	Loading: active
*/

/**
 * Highlight Section
 *
 * @package PageLines DMS
 * @author PageLines
 */
class PageLinesHighlight extends PageLinesSection {

	var $tabID = 'highlight_meta';


	function section_opts(){
		$opts = array(
			array(
				'type' 			=> 'select',
				'title' 		=> __( 'Select Format', 'pagelines' ),
				'key'			=> '_highlight_splash_position',
				'label' 		=> __( 'Highlight Format', 'pagelines' ),
				'opts'=> array(
					'top'			=> array( 'name' => __( 'Image on top of text', 'pagelines' ) ),
					'bottom'	 	=> array( 'name' => __( 'Image on bottom of text' , 'pagelines' )),
					'notext'	 	=> array( 'name' => __( 'No text, just the image', 'pagelines' ) )
				),
			),
			'hl_text' => array(
				'type' 			=> 'multi',
				'col'			=> 2,
				'title' 		=> __( 'Highlight Text', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'			=> '_highlight_head',
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',
						'label' 		=> __( 'Highlight Header Text (Optional)', 'pagelines' ),
					),
					array(
						'key'			=> '_highlight_subhead',
						'version' 		=> 'pro',
						'type' 			=> 'textarea',
						'label' 		=> __( 'Highlight Subheader Text (Optional)', 'pagelines' ),
					)

				)
			),
			'hl_image' => array(
				'type' 			=> 'multi',
				'col'			=> 3,
				'title' 		=> __( 'Highlight Image and Format', 'pagelines' ),
				'opts'	=> array(

					 array(
						'key'			=> '_highlight_splash',
						'type' 			=> 'image_upload',
						'label'			=> __( 'Upload Splash Image', 'pagelines' )
					),
					array(
						'key'				=> '_highlight_image_frame',
						'type' 				=> 'check',
						'label' 			=> __( 'Add frame to image?', 'pagelines' )
					),
				)
			)

		);

		return $opts;

	}
	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){

		$settings = wp_parse_args($settings, $this->optionator_default);

		$metatab_array = array(

			'hl_options' => array(
				'version' 		=> 'pro',
				'type' 			=> 'multi_option',
				'title' 		=> __( 'Highlight Header Text (Optional)', 'pagelines' ),
				'shortexp' 		=> __( 'Add the main header text for the highlight section.', 'pagelines' ),
				'selectvalues'	=> array(
					'_highlight_head' => array(
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',
						'inputlabel' 	=> __( 'Highlight Header Text (Optional)', 'pagelines' ),
					),
					'_highlight_subhead' => array(
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',
						'inputlabel' 	=> __( 'Highlight Subheader Text (Optional)', 'pagelines' ),
					),

					'_highlight_splash' => array(
						'version' 		=> 'pro',
						'type' 			=> 'image_upload',
						'inputlabel'	=> __( 'Upload Splash Image', 'pagelines' )
					),
					'_highlight_splash_position' => array(
						'version' 		=> 'pro',
						'type' 			=> 'select',
						'inputlabel' 		=> __( 'Highlight Image Style', 'pagelines' ),
						'selectvalues'=> array(
							'top'			=> array( 'name' => __( 'Image on top of text', 'pagelines' ) ),
							'bottom'	 	=> array( 'name' => __( 'Image on bottom of text', 'pagelines' ) ),
							'notext'	 	=> array( 'name' => __( 'No text, just the image', 'pagelines' ) )
						),
					),
					'_highlight_image_frame' => array(
						'type' 				=> 'check',
						'inputlabel' 		=> __( 'Add frame to image?', 'pagelines' )
					),
				)
			)

		);

		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Highlight',
				'icon' 		=> $this->icon,
				'clone_id'	=> $settings['clone_id'],
				'active'	=> $settings['active']
			);

		register_metatab($metatab_settings, $metatab_array);
	}

	/**
	*
	* @TODO document
	*
	*/
	function section_template() {

		$h_head = $this->opt('_highlight_head', $this->tset);



		$h_subhead = $this->opt('_highlight_subhead', $this->tset);

		$h_splash = $this->opt('_highlight_splash', $this->tset);
		$h_splash_position = $this->opt('_highlight_splash_position', $this->oset);

		$frame_class = ($this->opt('_highlight_image_frame', $this->oset)) ? 'pl-imageframe' : '';

		if(!$h_head && !$h_subhead && !$h_splash){
			$h_head = __("Here's to the crazy ones...", 'pagelines');
			$h_subhead = __("This is your Highlight section. Set up the options to configure.", 'pagelines');
		}

		?>
		<div class="highlight-area">
			<?php

				if( $h_splash_position == 'top' && $h_splash)
					printf('<div class="highlight-splash hl-image-top %s"><img data-sync="_highlight_splash" src="%s" alt="" /></div>', $frame_class, $h_splash);

				if( $h_splash_position != 'notext' ){

					if($h_head)
						printf('<h2 class="highlight-head" data-sync="_highlight_head">%s</h2>', __( $h_head, 'pagelines' ) );

					if($h_subhead)
						printf('<div class="highlight-subhead" data-sync="_highlight_subhead">%s</div>', __( $h_subhead, 'pagelines' ) );

				}

				if( $h_splash_position != 'top' && $h_splash)
					printf('<div class="highlight-splash hl-image-bottom %s"><img data-sync="_highlight_splash" src="%s" alt="" /></div>', $frame_class, apply_filters( 'pl_highlight_splash', $h_splash ) );
			?>
		</div>
	<?php

	}
}