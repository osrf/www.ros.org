<?php
/*
	Section: RevSlider
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A professional and versatile slider section. Can be customized with several transitions and a large number of slides.
	Class Name: plRevSlider
	Filter: full-width, slider
*/


class plRevSlider extends PageLinesSection {

	var $default_limit = 3;

	function section_opts(){

		$options = array();

		$options[] = array(

			'title' => __( 'Slider Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'revslider_delay',
					'type' 			=> 'text',
					'default'		=> 9000,
					'label' 	=> __( 'Time Per Slide (in Milliseconds)', 'pagelines' ),
				)
			)

		);

		$options[] = array(
			'key'		=> 'revslider_array',
	    	'type'		=> 'accordion', 
			'col'		=> 2,
			'title'		=> __('Slides Setup', 'pagelines'), 
			'post_type'	=> __('Slide', 'pagelines'), 
			'opts'	=> array(
				array(
					'key'		=> 'background',
					'label' 	=> __( 'Slide Background Image', 'pagelines' ),
					'type'		=> 'image_upload',
					'sizelimit'	=> 2097152, // 2M
					'help'		=> __( 'For high resolution, 2000px wide x 800px tall images. (2MB Limit)', 'pagelines' )
					
				),

				array(
					'key'	=> 'text',
					'label'	=> __( 'Slide Text', 'pagelines' ),
					'type'			=> 'text'
				),
				array(
					'key'	=> 'link',
					'label'	=> __( 'Slide Link URL', 'pagelines' ),
					'type'			=> 'text'
				),
				array(
					'key'	=> 'location',
					'label'	=> __( 'Slide Text Location', 'pagelines' ),
					'type'			=> 'select',
					'opts'	=> array(
						'left-side'	=> array('name'=> 'Text On Left'),
						'right-side'	=> array('name'=> 'Text On Right'),
						'centered'		=> array('name'=> 'Centered'),
					)
				),
				array(
					'key'		=> 'transition',
					'label'		=> __( 'Slide Transition', 'pagelines' ),
					'type'		=> 'select_same',
					'opts'		=> $this->slider_transitions()
				),
				array(
					'key'		=> 'extra',
					'label'		=> __( 'Slide Extra Elements', 'pagelines' ),
					'type'		=> 'textarea',
					'ref'		=> __( 'Add extra Revolution Slider markup here. Rev slider is based on Revolution Slider, a jQuery plugin. It supports a wide array of functionality including video embeds and additional transitions if you can handle HTML. Check out the <a href="http://www.orbis-ingenieria.com/code/documentation/documentation.html" target="_blank">docs here</a>.', 'pagelines' )
				),
				

			)
	    );



		// $slides = ($this->opt('revslider_count')) ? $this->opt('revslider_count') : $this->default_limit;
		// 
		// 	for($i = 1; $i <= $slides; $i++){
		// 
		// 
		// 		$options[] = array(
		// 			'title' 		=> __( 'RevSlider Slide ', 'pagelines' ) . $i,
		// 			'type' 			=> 'multi',
		// 			'col'			=> 2,
		// 			'opts' => array(
		// 				'revslider_bg_'.$i 	=> array(
		// 					'label' 	=> __( 'Slide Background Image', 'pagelines' ),
		// 					'type'		=> 'image_upload',
		// 					'sizelimit'	=> 2097152, // 2M
		// 					'help'		=> __( 'For high resolution, 2000px wide x 800px tall images. (2MB Limit)', 'pagelines' )
		// 				),
		// 
		// 				'revslider_text_'.$i 	=> array(
		// 					'label'	=> __( 'Slide Text', 'pagelines' ),
		// 					'type'			=> 'text'
		// 				),
		// 				'revslider_link_'.$i 	=> array(
		// 					'label'	=> __( 'Slide Link URL', 'pagelines' ),
		// 					'type'			=> 'text'
		// 				),
		// 				'revslider_text_location_'.$i 	=> array(
		// 					'label'	=> __( 'Slide Text Location', 'pagelines' ),
		// 					'type'			=> 'select',
		// 					'opts'	=> array(
		// 						'left-side'	=> array('name'=> 'Text On Left'),
		// 						'right-side'	=> array('name'=> 'Text On Right'),
		// 						'centered'		=> array('name'=> 'Centered'),
		// 					)
		// 				),
		// 				'revslider_transition_'.$i 	=> array(
		// 					'label'		=> __( 'Slide Transition', 'pagelines' ),
		// 					'type'		=> 'select_same',
		// 					'opts'		=> $this->slider_transitions()
		// 				),
		// 				'revslider_extra_'.$i 	=> array(
		// 					'label'		=> __( 'Slide Extra Elements', 'pagelines' ),
		// 					'type'		=> 'textarea',
		// 					'ref'		=> __( 'Add extra Revolution Slider markup here. Rev slider is based on Revolution Slider, a jQuery plugin. It supports a wide array of functionality including video embeds and additional transitions if you can handle HTML. Check out the <a href="http://www.orbis-ingenieria.com/code/documentation/documentation.html" target="_blank">docs here</a>.', 'pagelines' )
		// 				),
		// 			),
		// 
		// 		);
		// 
		// 	}

		return $options;
	}

	function slider_transitions(){

		$transitions = array(
			'boxslide',
			'boxfade',
			'slotzoom-horizontal',
			'slotslide-horizontal',
			'slotfade-horizontal',
			'slotzoom-vertical',
			'slotslide-vertical',
			'slotfade-vertical',
			'curtain-1',
			'curtain-2',
			'curtain-3',
			'slideleft',
			'slideright',
			'slideup',
			'slidedown',
			'fade',
			'random',
			'slidehorizontal',
			'slidevertical',
			'papercut',
			'flyin',
			'turnoff',
			'cube',
			'3dcurtain-vertical',
			'3dcurtain-horizontal',
		);

		return $transitions;

	}
	function section_styles(){

		wp_enqueue_script( 'revslider-plugins', $this->base_url.'/jquery.revslider.plugins.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'revslider', $this->base_url.'/jquery.revslider.min.js', array( 'jquery' ), PL_CORE_VERSION, true );


	}

	function section_head( ){

		?>
<script>
				jQuery(document).ready(function() {

					jQuery('<?php echo $this->prefix();?> .revslider-full').show().revolution(
						{
							delay:<?php echo $this->opt('revslider_delay', array('default' => 9000));?>,
							startwidth:940,
							startheight:480,
							onHoverStop:"on",
							thumbWidth: 100,
							thumbHeight: 50,
							thumbAmount: 3,
							hideThumbs: 200,
							navigationType:"bullet",
							navigationArrows:"solo",
							navigationStyle:"round",
							navigationHAlign:"center",
							navigationVAlign:"bottom",
							navigationHOffset:0,
							navigationVOffset:20,
							soloArrowLeftHalign:"left",
							soloArrowLeftValign:"center",
							soloArrowLeftHOffset:20,
							soloArrowLeftVOffset:0,
							soloArrowRightHalign:"right",
							soloArrowRightValign:"center",
							soloArrowRightHOffset:20,
							soloArrowRightVOffset:0,
							touchenabled:"on",
							stopAtSlide:-1,
							stopAfterLoops:-1,
							hideCaptionAtLimit:0,
							hideAllCaptionAtLilmit:0,
							hideSliderAtLimit:0,
							fullWidth:"on",
							shadow:0

						}

						);

				});

</script>
<?php }

	function render_slides(){
	
		$slide_array = $this->opt('revslider_array');
		
		$format_upgrade_mapping = array(
			'background'	=> 'revslider_bg_%s',
			'text'			=> 'revslider_text_%s',
			'link'			=> 'revslider_link_%s',
			'location'		=> 'revslider_text_location_%s',
			'transition'	=> 'revslider_transition_%s',
			'extra'			=> 'revslider_extra_%s'
		); 
		
		$slide_array = $this->upgrade_to_array_format( 'revslider_array', $slide_array, $format_upgrade_mapping, $this->opt('revslider_count'));
		
	
		$output = '';
		
		if( is_array($slide_array) ){
			
			
			foreach( $slide_array as $slide ){
				
				$the_bg = pl_array_get( 'background', $slide ); 
				$extra = pl_array_get( 'extra', $slide ); 

				if( $the_bg || $extra ){
					
					$the_text = pl_array_get( 'text', $slide ); 
					
					$the_link = pl_array_get( 'link', $slide ); 

					$the_location = pl_array_get( 'location', $slide ); 

					$transition = pl_array_get( 'transition', $slide, 'fade' ); 
					
					if($the_location == 'centered'){
						$the_x = 'center';
						$caption_class = 'centered sfb stb';
					} elseif ($the_location == 'right-side'){
						$the_x = '560';
						$caption_class = 'right-side sfr str';
					} else {
						$the_x =  '0';
						$caption_class = 'left-side sfl stl';
					}

					$bg = ($the_bg) ? sprintf('<img src="%s" data-fullwidthcentering="on">', $the_bg) : '';

					$content = sprintf('<h2><span class="slider-text">%s</span></h2>', $the_text);

					$link = ($the_link) ? sprintf('<a href="%s" class="slider-btn">%s</a>', $the_link, __('Read More', 'pagelines')) : '';

					if(!$extra){
						$caption = sprintf(
								'<div class="caption slider-content %s" data-x="%s" data-y="130" data-speed="300" data-start="500" data-easing="easeOutExpo">%s %s</div>',
								$caption_class,
								$the_x,
								$content,
								$link
						);
					} else
						$caption = '';


					$output .= sprintf('<li data-transition="%s" data-slotamount="7">%s %s %s</li>', $transition, $bg, $caption, $extra);
				}
				
			
			}
		
		}
				
				
		// for($i = 1; $i <= $slides; $i++){
		// 
		// 			$the_bg = $this->opt( 'revslider_bg_'.$i );
		// 			
		// 			$extra = $this->opt('revslider_extra_'.$i);
		// 			
		// 			if( $the_bg || $extra ){
		// 
		// 				$the_text = $this->opt('revslider_text_'.$i);
		// 				$the_link = $this->opt('revslider_link_'.$i);
		// 				
		// 				$the_location = $this->opt('revslider_text_location_'.$i);
		// 				$transition = $this->opt('revslider_transition_'.$i, array('default' => 'fade'));
		// 
		// 				if($the_location == 'centered'){
		// 					$the_x = 'center';
		// 					$caption_class = 'centered sfb stb';
		// 				} elseif ($the_location == 'right-side'){
		// 					$the_x = '560';
		// 					$caption_class = 'right-side sfr str';
		// 				} else {
		// 					$the_x =  '0';
		// 					$caption_class = 'left-side sfl stl';
		// 				}
		// 
		// 				$bg = ($the_bg) ? sprintf('<img src="%s" data-fullwidthcentering="on">', $the_bg) : '';
		// 
		// 				$content = sprintf('<h2><span class="slider-text">%s</span></h2>', $the_text);
		// 
		// 				$link = ($the_link) ? sprintf('<a href="%s" class="slider-btn">%s</a>', $the_link, __('Read More', 'pagelines')) : '';
		// 
		// 				if(!$extra){
		// 					$caption = sprintf(
		// 							'<div class="caption slider-content %s" data-x="%s" data-y="130" data-speed="300" data-start="500" data-easing="easeOutExpo">%s %s</div>',
		// 							$caption_class,
		// 							$the_x,
		// 							$content,
		// 							$link
		// 					);
		// 				} else
		// 					$caption = '';
		// 				
		// 
		// 				$output .= sprintf('<li data-transition="%s" data-slotamount="7">%s %s %s</li>', $transition, $bg, $caption, $extra);
		// 			}
		// 		}
		
		return $output;
	}

	function default_slides(){
		?>

			<li data-transition="fade" data-slotamount="10">
				<img src="<?php echo $this->base_url;?>/images/bg1.jpg" data-fullwidthcentering="on">
				<div class="caption slider-content right-side sfr str"
					 data-x="560"
					 data-y="130"
					 data-speed="300"
					 data-start="500"
					 data-easing="easeOutExpo"  >

						<h2><span class="slider-text">
						Welcome to DMS.<br/>
					 	A Drag <span class="spamp">&amp;</span> Drop Platform <br/> for Amazing Websites.
						</span></h2>
					 	<a href="#" class="slider-btn">Read More</a>

				</div>


			</li>
			<li data-transition="fade" data-slotamount="10"  >
				<img src="<?php echo $this->base_url;?>/images/bg2.jpg" data-fullwidthcentering="on">

				<div class="caption slider-content left-side sfl stl"
					 data-x="0"
					 data-y="130"
					 data-speed="300"
					 data-start="500"
					 data-easing="easeOutExpo">

						<h2><span class="slider-text">
						Build Amazing, <br/>
						Ultra-Responsive Sites<br/>
					 	Without Touching Code
						</span></h2>
					 	<a href="#" class="slider-btn">Read More</a>

				</div>
			</li>


			<li data-transition="fade" data-slotamount="10"  >

					<img src="<?php echo $this->base_url;?>/images/bg3.jpg" data-fullwidthcentering="on">

					<div class="caption fade fullscreenvideo"
						data-autoplay="false"
						data-x="0"
						data-y="0"
						data-speed="500"
						data-start="10"
						data-easing="easeOutBack">
							<iframe width="100%" height="100%" src="//www.youtube.com/embed/CL1jPb0_Auc?wmode=opaque" frameborder="0" allowfullscreen></iframe>
					</div>

			</li>

		<?php
	}

   function section_template( ) {


	?>
	<div class="revslider-container">
		<div class="header-shadow"></div>
			<div class="revslider-full" style="display:none;max-height:480px;height:480px;">
				<ul>
					<?php

						$slides = $this->render_slides();

						if( $slides == '' ){
							$this->default_slides();
						} else {
							echo $slides;
						}
					?>

				</ul>

				<div class="tp-bannertimer tp-bottom"></div>
			</div>
		</div>

		<?php
	}


}