<?php


class EditorColor{

	var $default_base = '#FFFFFF';
	var $default_text = '#000000';
	var $default_link = '#337EFF';
	var $background = '';

	function __construct( ){

		$this->background = pl_setting('page_background_image_url');

 		add_filter('pl_settings_array', array( $this, 'add_settings'));
		add_filter('pless_vars', array( $this, 'add_less_vars'));
		
		if($this->background && $this->background != '')
			add_filter('wp_enqueue_scripts', array( $this, 'background_fit'));
		
//		add_filter('pagelines_body_classes', array( $this, 'add_body_classes'));
		
		
	
	}
	
	function add_body_classes($classes){
		
		$classes[] = ( pl_setting('supersize_bg') ) ? 'fit-bg' : '';
	
		return $classes;
		
	}

	function add_less_vars( $vars ){
		$bg = pl_setting('bodybg');
		$this->base = $base = ( $bg && $bg != '' ) ? $bg : $this->default_base;

		$text = ( pl_setting('text_primary') ) ? pl_setting('text_primary') : $this->default_text;
		$link = ( pl_setting('linkcolor') ) ? pl_setting('linkcolor') : $this->default_link;

		$vars['pl-base'] 				= $this->hash( $base );
		$vars['pl-text']				= $this->hash( $text );
		$vars['pl-link']				= $this->hash( $link );
		$vars['pl-background']			= $this->background( $vars['pl-base'] );
		$vars['invert-dark']			= $this->invert();
		$vars['invert-light']			= $this->invert( 'light' );
		
		return $vars;
	}
	
	private function invert( $mode = 'dark', $delta = 5 ){

		if($mode == 'light'){

			if($this->color_detect() == -2)
				return 2*$delta;
			elseif($this->color_detect() == -1)
				return 1.5*$delta;
			elseif($this->color_detect() == 1)
				return -1.7*$delta;
			else
				return $delta;

		}else{
			if($this->color_detect() == -2)
				return -(2*$delta);
			elseif($this->color_detect() == -1)
				return -$delta;
			else
				return $delta;
		}
	}
	
	/**
     * Color Detect
     *
     * Takes the base color hex string and assigns a value to determine what "shade" the color is
     *
     * @return bool|int - a numeric value used in invert()
     */
	function color_detect(){

		$hex = str_replace( '#', '', $this->base );

		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

		if($r + $g + $b > 750){

			// Light
		    return 1;

		}elseif($r + $g + $b < 120){

			// Really Dark
			return -2;

		}
		elseif($r + $g + $b < 300){

			// Dark
			return -1;

		}else{

			// Meh
		    return false;

		}
	}

	function background( $bg_color ){
		
		$fit = pl_setting('supersize_bg');
		
		$image = ($this->background && $this->background != '') ? $this->background : false;

		if($image && $fit){
			
			$background = $bg_color;
			
		} elseif($image && !$fit){

			$repeat = pl_setting('page_background_image_repeat');
			$pos_x = pl_setting('page_background_image_pos_hor');
			$pos_y = pl_setting('page_background_image_pos_vert');
			$attach = pl_setting('page_background_image_attach');

			$repeat = ($repeat) ? $repeat : 'no-repeat';
			$pos_x = ( $pos_x !== false && $pos_x !== '' ) ? $pos_x.'%' : '50%';
			$pos_y = ( $pos_y !== false && $pos_y !== '' ) ? $pos_y.'%' : '0%';
			$attach = ($attach) ? $attach : 'fixed';

			$background = sprintf('%s url("%s") %s %s %s %s', $bg_color, $image, $repeat, $pos_x, $pos_y, $attach);

		} else
			$background = $bg_color;

		return $background;
	}

	function background_fit(){
		
		if( !pl_setting('supersize_bg') )
			return; 
			
		wp_enqueue_script( 'pagelines-supersize', PL_JS . '/script.supersize.js', array( 'jquery' ), '3.1.3', false );
		
		add_action('pl_scripts_on_ready', array( $this, 'run_background_fit'), 20);
	}

	function run_background_fit(){
	
		
		$image = $this->background;
		?>
		jQuery.supersized({ slides: [{ image : '<?php echo $image; ?>' }]})
<?php
	}


	function hash( $color ){

		$clean = str_replace('#', '', $color);

		if(preg_match('/^[a-f0-9]{6}$/i', $clean)){
			// IS A COLOR
		} elseif (preg_match('/^[a-f0-9]{3}$/i', $clean)){
			$clean = $clean.$clean;
		} else {
			$clean = 'FFFFFF';
		}

		

		return sprintf('#%s', $clean);

	}


	function add_settings( $settings ){

		$settings['color_control'] = array(
			'name' 	=> 'Color <span class="spamp">&amp;</span> BG',
			'icon'	=> 'icon-tint',
			'pos'	=> 3,
			'opts' 	=> $this->options()
		);

		return $settings;
	}

	function options(){

		$settings = array(
			array(
				'key'		=> 'canvas_colors',
				'type' 		=> 'multi',
				'title' 	=> __( 'Site Base Colors', 'pagelines' ),
				'help' 		=> __( 'The "base" color are a few standard colors used throughout DMS that plugins may use to calculate contrast or other colors to make sure everything looks great.', 'pagelines' ),
				'opts'		=> array(
					array(
						'key'			=> 'bodybg',
						'type'			=> 'color',
						'label' 		=> __( 'Background Base Color', 'pagelines' ),
						'default'		=> $this->default_base,
						'compile'		=> 'pl-base',
					),
					array(
						'key'			=> 'text_primary',
						'type'			=> 'color',
						'label' 		=> __( 'Text Base Color', 'pagelines' ),
						'default'		=> $this->default_text,
						'compile'		=> 'pl-text',

					),
					array(
						'key'			=> 'linkcolor',
						'type'			=> 'color',
						'label' 		=> __( 'Link Base Color', 'pagelines' ),
						'default'		=> $this->default_link,
						'compile'		=> 'pl-link',
					)
				)
			),
			array(
				'key'		=> 'background_image_upload',
				'type' 		=> 'multi',
				'col'		=> 2,
				'title' 	=> __( 'Background Image', 'pagelines' ),
				'help' 		=> '',
				'opts'		=> array(
					array(
						'key'			=> 'page_background_image_url',
						'imgsize' 		=> 	'150',
						'sizemode'		=> 'height',
						'sizelimit'		=> 1224000,
						'type'			=> 'image_upload',
						'label' 		=> __( 'Page Background Image', 'pagelines' ),
						'default'		=> '',
						'compile'		=> true,

					),
					
				)
			), 
			array(
				'key'		=> 'background_image_config',
				'type' 		=> 'multi',
				'col'		=> 3,
				'title' 	=> __( 'Background Image Settings', 'pagelines' ),
				'help' 		=> '',
				'opts'		=> array(
					array(
						'key'			=> 'supersize_bg',
						'type'			=> 'check',
						'label' 		=> __( 'Fit image to page?', 'pagelines' ),
						'default'		=> true,
						'compile'		=> true,
						'help'			=> __( 'If you use this option the image will be fit "responsively" to the background of your page. This means the settings below will have no effect.', 'pagelines' )
					),
					array(
						'key'			=> 'page_background_image_repeat',
						'type'			=> 'select',
						'label' 		=> __( 'Background Repeat', 'pagelines' ),
						'default'		=> 'no-repeat',
						'opts'	=> array(
							'no-repeat' => array('name' => __( 'No Repeat', 'pagelines' )),
							'repeat'	=> array('name' => __( 'Repeat', 'pagelines' )),
							'repeat-x'	=> array('name' => __( 'Repeat Horizontally', 'pagelines' )),
							'repeat-y'	=> array('name' => __( 'Repeat Vertically', 'pagelines' ))
						),
						'compile'		=> true,

					),
					array(
						'key'			=> 'page_background_image_pos_vert',
						'type'			=> 'count_select',
						'label' 		=> __( 'Vertical Background Position in Percent', 'pagelines' ),
						'default'		=> '0',
						'count_start'	=> 0,
						'count_number'	=> 100,
						'suffix'		=> '%',
						'compile'		=> true,

					),
					array(
						'key'			=> 'page_background_image_pos_hor',
						'type'			=> 'count_select',
						'label' 		=> __( 'Horizontal Background Position in Percent', 'pagelines' ),
						'default'		=> '50',
						'count_start'	=> 0,
						'count_number'	=> 100,
						'suffix'		=> '%',
						'compile'		=> true,

					),
					array(
						'key'			=> 'page_background_image_attach',
						'type'			=> 'select',
						'label' 		=> __( 'Set Background Attachment', 'pagelines' ),
						'default'		=> 'fixed',
						'opts'	=> array(
							'scroll'	=> array('name' => __( 'Scroll', 'pagelines' )),
							'fixed'		=> array('name' => __( 'Fixed', 'pagelines' )),
						),
						'compile'		=> true,

					)
				)
			)

		);


		return $settings;

	}

}





