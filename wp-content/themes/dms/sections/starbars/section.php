<?php
/*
	Section: StarBars
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Awesome animated stat bars that animate as the user scrolls. Use them to show stats or other information.
	Class Name: PageLinesStarBars
	Cloning: true
	Edition: pro
	Filter: post-format
*/

class PageLinesStarBars extends PageLinesSection {

	var $default_limit = 3;

	function section_styles(){

		wp_enqueue_script( 'starbar', $this->base_url.'/starbar.js', array( 'pagelines-viewport', 'pagelines-easing' ), PL_CORE_VERSION, true );

	}

	function section_opts(){

		$options = array();

		$options[] = array(

			'title' => __( 'StarBar Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'starbar_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> 4,
					'label' 	=> __( 'Number of StarBars to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'starbar_total',
					'type' 			=> 'text',
					'default'		=> 100,
					'label' 		=> __( 'Starbar Total Count (Number)', 'pagelines' ),
					'help' 			=> __( 'This number will be used to calculate the percent of the bar filled. The StarBar values will be shown as a percentage of this value. Default is 100.', 'pagelines' ),
				),

				array(
					'key'			=> 'starbar_modifier',
					'type' 			=> 'text',
					'default'		=> '%',
					'label' 		=> __( 'Starbar Modifier (Text Added to Stats)', 'pagelines' ),
					'help' 			=> __( 'This will be added to the stat number.', 'pagelines' ),
				),
				array(
					'key'			=> 'starbar_format',
					'type' 			=> 'select',
					'opts'		=> array(
						'append'		=> array( 'name' => 'Append Modifier (Default)' ),
						'prepend'	 	=> array( 'name' => 'Prepend Modifier' ),
					),
					'default'		=> 'append',
					'label' 	=> __( 'Starbar Format', 'pagelines' ),
				),
				array(
					'key'			=> 'starbar_container_title',
					'type' 			=> 'text',
					'default'		=> 'StarBar',
					'label' 	=> __( 'StarBar Title (Optional)', 'pagelines' ),
				),
			)

		);

		$slides = ($this->opt('starbar_count')) ? $this->opt('starbar_count') : $this->default_limit;

		for($i = 1; $i <= $slides; $i++){

			$opts = array(
				
				'starbar_descriptor_'.$i 	=> array(
					'label'		=> __( 'Descriptor', 'pagelines' ),
					'type'		=> 'text'
				),
				'starbar_value_'.$i 	=> array(
					'label'	=> __( 'Value', 'pagelines' ),
					'type'	=> 'text',
					'help'	=> __( 'Shown as a percentage of the StarBar total in the config.', 'pagelines' ),
				),
			);


			$options[] = array(
				'col'		=> 2,
				'title' 	=> __( '<i class="icon-star"></i> StarBar #', 'pagelines' ) . $i,
				'type' 		=> 'multi',
				'opts' 		=> $opts,

			);

		}

		return $options;
	}

	function section_template(  ) {

		$starbar_title = $this->opt('starbar_container_title');
		$starbar_mod = $this->opt('starbar_modifier');
		$starbar_total = (int) $this->opt('starbar_total');
		$starbar_count = $this->opt('starbar_count');
		$starbar_format = $this->opt('starbar_format');

		$starbar_title = ($starbar_title) ? sprintf('<h3>%s</h3>', $starbar_title) : '';

		$format = ($starbar_format) ? $starbar_format : 'append';

		$mod = ($starbar_mod) ? $starbar_mod : '%';
		
		$total = ($starbar_total) ? $starbar_total : 100;
		
		$total = apply_filters('starbars_total', $total);
		
		$output = '';
		for($i = 1; $i <= $starbar_count; $i++){

			$descriptor = $this->opt('starbar_descriptor_'.$i);
			$value = (int) $this->opt('starbar_value_'.$i);
			
			$value = apply_filters('starbar_value', $value, $i, $descriptor, $this); 
			

			$desc = ($descriptor) ? sprintf('<p>%s</p>', $descriptor) : '';

			if(!$value)
				continue;

			if(is_int($value) && is_int($total))
				$width = floor( $value / $total * 100 ) ;
			else
				$width = 0;

			$value = ($width > 100) ? $total : $value;
			$width = ($width > 100) ? 100 : $width;


			$tag = ( $format == 'append' ) ? $value . $mod : $mod . $value;

			$total_tag = ( $format == 'append' ) ? $starbar_total . $mod : $mod . $starbar_total;

		//	$draw_total_tag = ($i == 1) ? sprintf('<strong>%s</strong>', $total_tag) : '';

			$output .= sprintf(
				'<li>%s<div class="bar-wrap"><span class="the-bar" data-width="%s"><strong>%s</strong></span></div></li>',
				$desc,
				$width.'%',
				$tag
			);
		}


		if($output == ''){
			$this->do_defaults();
		} else
			printf('<div class="starbars-wrap">%s<ul class="starbars">%s</ul></div>', $starbar_title, $output);



	}

	function do_defaults(){

		?>
		<div class="starbars-wrap">
			<h2>StarBar</h2>
			<ul class="starbars">
				
				<li>
					<p>Ninja Ability</p>
					<div class="bar-wrap">
						<span class="the-bar" data-width="70%"><strong>70%</strong></span>
					</div>
				</li>
				<li>
					<p>Tree Climbing Skills</p>
					<div class="bar-wrap">
						<span class="the-bar" data-width="90%"><strong>90%</strong></span>
					</div>
				</li>
			</ul>
		</div>
		<?php
	}
	// Deprecated, but left here because were using it for a standalone v2 version of this section.
	function section_optionator( $settings ){

		$settings = wp_parse_args( $settings, $this->optionator_default );

			$array = array(

				'starbar_options'	=> array(
					'type'	=> 'multi_option',
					'title' => 'StarBar Configuration',
					'selectvalues'	=> array(

						'starbar_count' => array(
							'type' 			=> 'count_select',
							'count_start'	=> 1,
							'count_number'	=> 12,
							'default'		=> '3',
							'inputlabel' 	=> __( 'Number of StarBars to Configure', 'starbar' )
						),
						'starbar_total'	=> array(
							'type' 			=> 'text',
							'default'		=> 100,
							'inputlabel' 		=> __( 'Starbar Total Count (Number)', 'starbar' )
						),
						'starbar_modifier'	=> array(
							'type' 			=> 'text',
							'default'		=> '%',
							'inputlabel' 		=> __( 'Starbar Modifier (Text Added to Stats)', 'starbar' )
						),
						'starbar_format'	=> array(
							'type' 			=> 'select',
							'selectvalues'		=> array(
								'append'		=> array( 'name' => 'Append Modifier (Default)' ),
								'prepend'	 	=> array( 'name' => 'Prepend Modifier' ),
							),
						'default'		=> 'append',
						'inputlabel' 	=> __( 'Starbar Format', 'pagelines' ),
						),
						'starbar_container_title'	=> array(
							'type' 			=> 'text',
							'default'		=> 'StarBar',
							'inputlabel' 	=> __( 'StarBar Title (Optional)', 'starbar' ),
						)
					)
				)
			);

		global $post_ID;

		$oset = array('post_id' => $post_ID, 'clone_id' => $settings['clone_id'], 'type' => $settings['type']);

		$slides = (pl_setting( 'starbar_count' , $oset )) ? pl_setting( 'starbar_count' , $oset ) : $this->default_limit;

		$opts = array();

		for($i = 1; $i <= $slides; $i++){

			$opts[ 'starbar_descriptor_' . $i ]	= array(
					'inputlabel'		=> sprintf( __( 'Starbar #%s Description', 'starbar' ), $i ),
					'type'		=> 'text'
			);
			$opts['starbar_value_' . $i ] = array(
					'inputlabel'	=> sprintf( __( 'Starbar #%s Value', 'starbar' ), $i ),
					'type'	=> 'text',

			);
		}
			$array['starbars_data'] = array(
				'title' 	=> __( 'StarBar Values', 'starbar' ),
				'type' 		=> 'multi_option',
				'selectvalues' 		=> $opts
				);

			$metatab_settings = array(
					'id' 		=> 'starbar_options',
					'name' 		=> __( 'Starbar', 'starbar' ),
					'icon' 		=> $this->icon,
					'clone_id'	=> $settings['clone_id'],
					'active'	=> $settings['active']
				);
			register_metatab( $metatab_settings, $array );
	}
}