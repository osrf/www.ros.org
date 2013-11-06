<?php
/*
	Section: Masthead
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width splash and text area. Great for getting big ideas across quickly.
	Class Name: PLMasthead
	Edition: pro
	Workswith: templates, main, header, morefoot
	Filter: component
	Loading: active
*/

/**
 * Main section class
 *
 * @package PageLines DMS
 * @author PageLines
 */
class PLMasthead extends PageLinesSection {

    var $tabID = 'masthead_meta';

    function section_head() {

    	if($this->opt('pagelines_masthead_html',$this->oset)) { ?>
	    		<script>
	    		  jQuery(document).ready(function(){
				    jQuery(".video-splash").fitVids();
				  });
	    		</script>
	    	<?php }
    }

     function section_scripts() {
     	if( $this->opt( 'pagelines_masthead_html', $this->oset ) )
    	wp_enqueue_script( 'pagelines-fitvids',$this->base_url . '/jquery.fitvids.js', array( 'jquery' ), PL_CORE_VERSION, true );
    }

	
	function section_opts(  ){

		$options = array(
				array(
					'key'	=> 'pagelines_masthead_splash_multi',
					'type' 	=> 'multi',
					'title' => __('Masthead Splash Options','pagelines'),
					'opts'	=> array(
						array(
							'key'			=> 'pagelines_masthead_img',
							'type' 			=> 'image_upload',
							'imagepreview' 	=> '270',
							'label' 	=> __( 'Upload custom image', 'pagelines' ),
						),
						array(
							'key'			=> 'pagelines_masthead_html',
							'type' 			=> 'textarea',
							'label' 	=> __( 'Masthead Video (optional, to be used instead of image)', 'pagelines' ),
						),
						array(
							'key'			=> 'masthead_html_width',
							'type' 			=> 'text',
							'label' 	=> __( 'Maximum width of splash in px (default is full width)', 'pagelines' ),
						),
					),
					'help'                   => __( 'Upload an image to serve as a splash image, or use an embed code for full width video.', 'pagelines' ),
				),
				array(
						'key'				=> 'pagelines_masthead_text',
						'type' 				=> 'multi',
						'label' 		=> __( 'Masthead Text', 'pagelines' ),
						'title' 			=> $this->name . __( ' Text', 'pagelines' ),
						'opts'	=> array(
							array(
								'key'		=> 'pagelines_masthead_title',
								'type'		=> 'text',
								'label'		=> __( 'Title', 'pagelines' ), 
							),
							array(
								'key'	=> 'pagelines_masthead_tagline',
								'type'	=> 'text',
								'label'	=>__( 'Tagline', 'pagelines' ), 
							)
						),

				),
		); 
			
		for($i = 1; $i <= 2; $i++){

			$options[] = array(
				'key'		=> 'masthead_button_multi_'.$i,
				'type'		=> 'multi',
				'title'		=> __('Masthead Action Button '.$i, 'pagelines'),
				'opts'	=> array(
					array(
						'key'		=> 'masthead_button_link_'.$i,
						'type' => 'text',
						'label' => __( 'Enter the link destination (URL - Required)', 'pagelines' ),

					),
					array(
						'key'		=> 'masthead_button_text_'.$i,
						'type' 			=> 'text',
						'label' 	=> __( 'Masthead Button Text', 'pagelines' ),
					 ),

					array(
						'key'		=> 'masthead_button_target_'.$i,
						'type'			=> 'check',
						'default'		=> false,
						'label'	=> __( 'Open link in new window.', 'pagelines' ),
					),
					array(
						'key'		=> 'masthead_button_theme_'.$i,
						'type'			=> 'select_button',
						'default'		=> false,
						'label'		=> __( 'Select Button Color', 'pagelines' ),
					
					),
				)
			);

		}
			
				
		$options[] = array(
					'key'		=> 'masthead_menu',
					'type' 			=> 'select_menu',
					'title'			=> __( 'Masthead Menu', 'pagelines' ),
					'inputlabel' 	=> __( 'Select Masthead Menu', 'pagelines' ),
				); 
		$options[] = array(
					'key'		=> 'masthead_meta',
					'type' 			=> 'text',
					'title'			=> __( 'Masthead Meta', 'pagelines' ),
					'inputlabel' 	=> __( 'Enter Masthead Meta Text', 'pagelines' ),
				); 

		

		return $options;
	}
	
	

	/**
	* Section template.
	*/
   function section_template() {
   		$mast_title = $this->opt('pagelines_masthead_title', $this->oset);
   		$mast_img = $this->opt('pagelines_masthead_img', $this->oset);
		$mast_tag = $this->opt('pagelines_masthead_tagline', $this->oset);
		$mast_menu = ($this->opt('masthead_menu', $this->oset)) ? $this->opt('masthead_menu', $this->oset) : null;
		$masthead_meta = $this->opt('masthead_meta', $this->oset);

		$masthtmlwidth = ($this->opt('masthead_html_width',$this->oset)) ? $this->opt('masthead_html_width',$this->oset).'px' : '';

		$mast_title = (!$mast_title) ? 'Hello.' : $mast_title;

		$classes = ($mast_img) ? 'with-splash' : '';
		
	?>

	<header class="jumbotron masthead <?php echo $classes;?>">
	  	<?php

	  		$theimg = sprintf('<img class="masthead-img" data-sync="pagelines_masthead_img" src="%s" />',$mast_img);
	  		$masthtml = $this->opt('pagelines_masthead_html',$this->oset);

	  		if($mast_img)
	  			printf('<div class="splash" style="max-width:%s;margin:0 auto;">%s</div>',$masthtmlwidth,$theimg);

	  		if($masthtml)
	  			printf('<div class="video-splash" style="max-width:%s;margin:0 auto;">%s</div>',$masthtmlwidth,$masthtml);

	  	?>

	  <div class="inner">
	  	<?php

	  		printf('<h1 class="masthead-title" data-sync="pagelines_masthead_title">%s</h1>',$mast_title);

			printf('<p class="masthead-tag" data-sync="pagelines_masthead_tagline">%s</p>',$mast_tag);

	  	?>

	    <p class="download-info">

	    <?php
			for ($i = 1; $i <= 2; $i++){
				$btn_link = $this->opt('masthead_button_link_'.$i, $this->oset); // Flag

				$btn_text = ($this->opt('masthead_button_text_'.$i, $this->oset)) ? $this->opt('masthead_button_text_'.$i, $this->oset) : __('Start Here', 'pagelines');

				$target = ( $this->opt( 'masthead_button_target_'.$i, $this->oset ) ) ? 'target="_blank"' : '';
				$btheme = ( $this->opt( 'masthead_button_theme_'.$i, $this->oset ) ) ? $this->opt( 'masthead_button_theme_'.$i, $this->oset ) : 'primary';

				if($btn_link)
					printf('<a %s class="btn %s btn-large" href="%s" data-sync="masthead_button_text_%s">%s</a> ', $target, $btheme, $btn_link, $i, $btn_text);
			}

	    ?>

	    </p>
	  </div>
		<div class="mastlinks">
			<?php
			if( is_array( wp_get_nav_menu_items( $mast_menu ) ) )
				wp_nav_menu(
					array(
						'menu_class'  => 'quick-links',
						'menu' => $mast_menu,
						'container' => null,
						'container_class' => '',
						'depth' => 1,
						'fallback_cb'=>''
					)
				);


			if($masthead_meta)
				printf( '<div class="quick-links mastmeta">%s</div>', do_shortcode($masthead_meta) );

			?>


		</div>
	</header>

		<?php

	

	}


	
	function section_optionator( $settings ){

		$settings = wp_parse_args($settings, $this->optionator_default);

		$option_array = array(
				'pagelines_masthead_splash_multi' => array(
					'type' 				=> 'multi_option',
					'title' 			=> __('Masthead Splash Options','pagelines'),
					'shortexp'	=> __('Enter the options for the masthead splash image. If no options are specified, no image will be shown.', 'pagelines'),
					'selectvalues'	=> array(
						'pagelines_masthead_img' => array(
							'type' 			=> 'image_upload',
							'imagepreview' 	=> '270',
							'inputlabel' 	=> 'Upload custom image',
						),
						'pagelines_masthead_html'   => array(
							'type' 			=> 'textarea',
							'inputlabel' 	=> 'Masthead Video (optional, to be used instead of image)',
						),
						'masthead_html_width'   => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Maximum width of splash in px (default is full width)',
						),
					),
					'exp'                   => 'Upload an image to serve as a splash image, or use an embed code for full width video.',
				),
				'pagelines_masthead_text' => array(
						'type' 				=> 'text_multi',
						'layout'			=> 'full',
						'inputlabel' 		=> 'Enter text for your masthead banner section',
						'title' 			=> $this->name.' Text',
						'selectvalues'	=> array(
							'pagelines_masthead_title'		=> array('inputlabel'=>'Title', 'default'=> ''),
							'pagelines_masthead_tagline'	=> array('inputlabel'=>'Tagline', 'default'=> '')
						),
						'shortexp' 			=> 'The text for the masthead section',

				),
				'masthead_button_multi_1' => array(
					'type'		=> 'multi_option',
					'title'		=> __('Masthead Action Button 1', 'pagelines'),
					'shortexp'	=> __('Enter the options for the masthead button. If no options are specified, no button will be shown.', 'pagelines'),
					'selectvalues'	=> array(
						'masthead_button_link_1' => array(
							'type' => 'text',
							'inputlabel' => 'Enter the link destination (URL - Required)',

						),
						'masthead_button_text_1' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Masthead Button Text',
						 ),

						'masthead_button_target_1' => array(
							'type'			=> 'check',
							'default'		=> false,
							'inputlabel'	=> 'Open link in new window.',
						),
						'masthead_button_theme_1' => array(
							'type'			=> 'select',
							'default'		=> false,
							'inputlabel'	=> 'Select Button Color',
							'selectvalues'	=> array(
								'primary'	=> array('name' => 'Blue'),
								'warning'	=> array('name' => 'Orange'),
								'important'	=> array('name' => 'Red'),
								'success'	=> array('name' => 'Green'),
								'info'		=> array('name' => 'Light Blue'),
								'reverse'	=> array('name' => 'Grey'),
							),
						),
					)
				),
				'masthead_button_multi_2' => array(
					'type'		=> 'multi_option',
					'title'		=> __('Masthead Action Button 2', 'pagelines'),
					'shortexp'	=> __('Enter the options for the masthead button. If no options are specified, no button will be shown.', 'pagelines'),
					'selectvalues'	=> array(
						'masthead_button_link_2' => array(
							'type' => 'text',
							'inputlabel' => 'Enter the link destination (URL - Required)',

						),
						'masthead_button_text_2' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Masthead Button Text',
						 ),

						'masthead_button_target_2' => array(
							'type'			=> 'check',
							'default'		=> false,
							'inputlabel'	=> 'Open link in new window.',
						),
						'masthead_button_theme_2' => array(
							'type'			=> 'select',
							'default'		=> false,
							'inputlabel'	=> 'Select Button Color',
							'selectvalues'	=> array(
								'primary'	=> array('name' => 'Blue'),
								'warning'	=> array('name' => 'Orange'),
								'important'	=> array('name' => 'Red'),
								'success'	=> array('name' => 'Green'),
								'info'		=> array('name' => 'Light Blue'),
								'reverse'	=> array('name' => 'Grey'),
							),
						),
					)
				),
				'masthead_menu' => array(
					'shortexp'	=> __('Choose a Wordpress menu to display (optional)', 'pagelines'),
						'type' 			=> 'select_menu',
						'title'			=> 'Masthead Menu',
						'inputlabel' 	=> 'Select Masthead Menu',
					),
				'masthead_meta' => array(
					'shortexp'	=> __('Enter text to be shown on Masthead (optional)', 'pagelines'),
						'type' 			=> 'textarea',
						'title'			=> 'Masthead Meta',
						'inputlabel' 	=> 'Enter Masthead Meta Text',
					),

			);

		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Masthead',
				'icon' 		=> $this->icon,
				'clone_id'	=> $settings['clone_id'],
				'active'	=> $settings['active']
			);

		register_metatab($metatab_settings, $option_array);
	}

}