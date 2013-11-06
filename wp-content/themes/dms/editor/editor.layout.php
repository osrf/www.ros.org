<?php

class EditorLayout {


	function __construct(  ){
		
		add_filter('pl_settings_array', array( $this, 'add_settings'));
		add_filter('pless_vars', array( $this, 'add_less_vars'));
		add_filter('pagelines_body_classes', array( $this, 'add_body_classes'));

	}
	
	function add_body_classes($classes){
		
		$classes[] = ( pl_setting( 'layout_display_mode' ) == 'display-boxed' ) ? 'display-boxed' : 'display-full';
	
		return $classes;
		
	}
	
	function add_settings( $settings ){

		$settings['layout'] = array(
			'name' 	=> __( 'Layout <span class="spamp">&amp;</span> Nav', 'pagelines' ),
			'icon' 	=> 'icon-fullscreen',
			'pos'	=> 2,
			'opts' 	=> $this->options()
		);

		return $settings;
	}
	
	
	function options(){



		$settings = array(
			array(
				'key'		=> 'layout_mode',
				'type' 		=> 'select',
				'label' 	=> __( 'Select Layout Mode', 'pagelines' ),
				'title' 	=> __( 'Layout Mode', 'pagelines' ),
				'opts' 		=> array(
					'pixel' 	=> array('name' => __( 'Pixel Width Based Layout', 'pagelines' )),
					'percent' 	=> array('name' => __( 'Percentage Width Based Layout', 'pagelines' ))
				),
				'default'	=> 'pixel',
				'help'	 	=> __( 'Select pixel width mode and your site maximum width will be controlled by pixels.<br/><br/> If you select "percent" width the width of your content will be a percentage of window size.', 'pagelines' )
			),
			array(
				'key'		=> 'layout_display_mode',
				'type' 		=> 'select',
				'label' 	=> __( 'Select Layout Display', 'pagelines' ),
				'title' 	=> __( 'Display Mode', 'pagelines' ),
				'opts' 		=> array(
					'display-full' 		=> array('name' => __( 'Full Width Display', 'pagelines' )),
					'display-boxed' 	=> array('name' => __( 'Boxed Display', 'pagelines' ))
				),
				'default'	=> 'display-full',
				'help'	 	=> __( '"Full" display mode allows areas to be the full width of the window, with content width sections.<br/><br/> "Boxed" mode contains everything in a central content box. Boxed mode is ideal for use with background images.', 'pagelines' )
			),
			
			

		);


		$settings[] = array(

			'key'			=> 'layout_navigations',
			'col'			=> 2,
			'type' 			=> 'multi',
			'label' 	=> __( 'Global Navigation Setup', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'		=> 'primary_navigation_menu',
					'type' 		=> 'select_menu',
					'label' 	=> __( 'Select Primary Navigation Menu', 'pagelines' ),
				
					'help'	 	=> __( 'This will be used in mobile menus and optionally other places throughout your site.', 'pagelines' )
				),
				array(
					'key'		=> 'secondary_navigation_menu',
					'type' 		=> 'select_menu',
					'label' 	=> __( 'Select Secondary Navigation Menu', 'pagelines' ),
				
					'help'	 	=> __( 'This will be shown subtly in areas throughout the site and in your mobile menu.', 'pagelines' )
				),
				
			),
		);
		
		

		return apply_filters('pl_layout_settings', $settings);

	}


	function add_less_vars( $less_vars ){

		// if pixel mode assign pixel option

		if( pl_setting( 'layout_mode' ) == 'percent' )
			$value = (pl_setting( 'content_width_percent' )) ? pl_setting( 'content_width_percent' ) : '80%';
		else
			$value = (pl_setting( 'content_width_px' ) && pl_setting( 'content_width_px' ) != '') ? pl_setting( 'content_width_px' ) : '1100px';

	
		// if percent mode assign percent option

		$less_vars['plContentWidth'] = $value;
		$less_vars['pl-page-width'] = $value;

		return $less_vars;

	}

	function get_layout_mode(){

		$value = (pl_setting( 'layout_mode' )) ? pl_setting( 'layout_mode' ) : 'pixel';

		return $value;

	}


}
