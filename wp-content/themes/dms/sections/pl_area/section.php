<?php
/*
	Section: Section Area
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a full width area with a nested content width region for placing sections and columns.
	Class Name: PLSectionArea
	Filter: full-width
	Loading: active
*/


class PLSectionArea extends PageLinesSection {


	function section_persistent(){
	
		add_filter('pl_layout_settings', array( $this, 'add_global_options'));
	}
	
	function add_global_options( $settings ){
		$settings[] = array(

			'key'			=> 'layout_areas',
			'type' 			=> 'multi',
			'col'			=> 3,
			'label' 	=> __( 'Section Area Layout', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'		=> 'section_area_default_pad',
					'type' 		=> 'count_select',
					'label' 	=> __( 'Default Area Padding (px)', 'pagelines' ),
					'count_start'	=> 0,
					'count_number'	=> 200,
					'suffix'		=> 'px',
					'help'	 	=> __( 'If sections are added to full width areas, the area will be givin this default padding.', 'pagelines' )
				),
				
			),
		);
		
		return $settings;
	}
	
	function section_opts(){

		$options = array();

		$options[] = array(

			'key'			=> 'pl_area_pad_selects',
			'type' 			=> 'multi',
			'label' 	=> __( 'Set Area Padding', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'			=> 'pl_area_pad',
					'type' 			=> 'count_select_same',
					'count_start'	=> 0,
					'count_number'	=> 200,
					'suffix'		=> 'px',
					'label' 	=> __( 'Area Padding (px)', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_pad_bottom',
					'type' 			=> 'count_select_same',
					'count_start'	=> 0,
					'count_number'	=> 200,
					'suffix'		=> 'px',
					'label' 	=> __( 'Area Padding Bottom (if different)', 'pagelines' ),
				)
			),
			

		);
		
		$options[] = array(

			'key'			=> 'pl_area_styling',
			'type' 			=> 'multi',
			'col'			=> 3,
			'label' 	=> __( 'Area Styling', 'pagelines' ),
			'opts'	=> array(
				// array(
				// 
				// 				'key'			=> 'pl_area_class',
				// 				'type' 			=> 'text',
				// 				'label' 	=> __( 'Styling Classes', 'pagelines' ),
				// 				'help'		=> __( 'Separate with a space " "', 'pagelines' ),
				// 			),
				array(

					'key'			=> 'pl_area_bg',
					'type' 			=> 'select',
					'opts'	=> array(
						'pl-trans'		=> array('name'=> 'Transparent Background and Default Text Color'),
						'pl-contrast'	=> array('name'=> 'Contrast Color and Default Text Color'),
						'pl-black'		=> array('name'=> 'Black Background &amp; White Text'),
						'pl-grey'		=> array('name'=> 'Dark Grey Background &amp; White Text'),
						'pl-dark-img'	=> array('name'=> 'Image-Dark: Embossed Light Text.'),
						'pl-light-img'	=> array('name'=> 'Image-Light: Embossed Dark Text.'),
						'pl-base'		=> array('name'=> 'Base Background and Default Text Color'),
					),
					'label' 	=> __( 'Area Theme', 'pagelines' ),

				),
				array(
					'key'			=> 'pl_area_height',
					'type' 			=> 'text',
					'label' 	=> __( 'Area Minimum Height (px)', 'pagelines' ),
				)
			),
			

		);
		
		$options[] = array(

			'key'			=> 'pl_area_bg',
			'col'			=> 2,
			'type' 			=> 'multi',
			'label' 	=> __( 'Area Background', 'pagelines' ),
			'opts'	=> array(
				array(

					'key'			=> 'pl_area_image',
					'type' 			=> 'image_upload',
					'sizelimit'		=> 800000,
					'label' 	=> __( 'Background Image', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_bg_repeat',
					'type' 			=> 'check',
					'label' 	=> __( 'Repeat Background Image', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_parallax',
					'type' 			=> 'check',
					'label' 	=> __( 'Enable Background Parallax', 'pagelines' ),
				)
			),
			

		);
		
		
		
		


		return $options;
	}
	
	function before_section_template( $location = '' ) {

		$this->wrapper_classes['background'] = $this->opt('pl_area_bg');
		//$this->wrapper_classes['user_classes'] = $this->opt('pl_area_class');

	}

	

	function section_template( ) {
		
		$section_output = (!$this->active_loading) ? render_nested_sections( $this->meta['content'], 1) : '';
		
		$style = '';
		$inner_style = '';
		
		$inner_style .= ($this->opt('pl_area_height')) ? sprintf('min-height: %spx;', $this->opt('pl_area_height')) : '';
		
		$style .= ($this->opt('pl_area_image')) ? sprintf('background-image: url(%s);', $this->opt('pl_area_image')) : '';
		
		$classes = ($this->opt('pl_area_parallax')) ? 'pl-parallax' : '';
		$classes .= ($this->opt('pl_area_bg_repeat')) ? ' pl-bg-repeat' : ' pl-bg-cover';
		
		// If there is no output, there should be no padding or else the empty area will have height.
		if ( $section_output ) {
			
			// global
			$default_padding = pl_setting('section_area_default_pad', array('default' => '20'));
			// opt	
			$padding		= rtrim( $this->opt('pl_area_pad',			array( 'default' => $default_padding ) ), 'px' ); 			
			$padding_bottom	= rtrim( $this->opt('pl_area_pad_bottom',	array( 'default' => $padding ) ), 'px' ); 
			
			$style .= sprintf('padding-top: %spx; padding-bottom: %spx;',
				$padding,
				$padding_bottom
			);
			
			$content_class = $padding ? 'nested-section-area' : '';
			$buffer = pl_draft_mode() ? sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>') : '';
			$section_output = $buffer . $section_output . $buffer;
		}
		else {
			$pad_css = ''; 
			$content_class = '';
		}
		
	?>
	<div class="pl-area-wrap <?php echo $classes;?>" style="<?php echo $style;?>">
		<div class="pl-content <?php echo $content_class;?>">
			<div class="pl-inner area-region pl-sortable-area editor-row" style="<?php echo $inner_style;?>">
				<?php  echo $section_output; ?>
			</div>
		</div>
	</div>
	<?php
	}


}
