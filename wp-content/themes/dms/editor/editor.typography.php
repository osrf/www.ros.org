<?php


class EditorTypography{

	var $default_font = '"Helvetica", Arial, serif';
	var $import_fonts = array();

	function __construct( PageLinesFoundry $foundry ){

		$this->foundry = $foundry;

 		add_filter('pl_settings_array', array( $this, 'add_settings'));
		add_filter('pless_vars', array( $this, 'add_less_vars'));
		add_action('wp_print_styles', array( $this, 'add_google_imports'));
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_styles'));
		
	}

	function add_less_vars( $vars ){

		$vars['plFontSize'] = (pl_setting('base_font_size')) ? sprintf( '%spx', pl_setting('base_font_size' ) ) : '14px';
		$vars['plHeaderSize'] = (pl_setting('header_base_size')) ? sprintf( '%spx', pl_setting('header_base_size' ) ) : '14px';

		// Base Font
		$primary = $this->import_fonts[] = (pl_setting('font_primary')) ? pl_setting('font_primary') : $this->default_font;
		$hdr = $this->import_fonts[] = (pl_setting('font_headers')) ? pl_setting('font_headers') : $this->default_font;

		$vars['plBaseFont'] = $this->foundry->get_stack( $primary );
		$vars['plHeaderFont'] = $this->foundry->get_stack( $hdr );


		$vars['plBaseWeight'] = ( pl_setting('font_primary_weight') ) ? pl_setting('font_primary_weight') : 'normal';
		$vars['plHeaderWeight'] = ( pl_setting('font_headers_weight') ) ? pl_setting('font_headers_weight') : 'bold';

		return $vars;
	}

	function enqueue_styles(){
		wp_enqueue_style(
			'open-sans',
			'//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=latin-ext,latin',
			false,
			'20130605'
		);
		
	}
	
	function add_google_imports(){

		$gcss = $this->foundry->google_import( $this->import_fonts, 'link' );
		
		$added = (pl_setting('font_extra')) ? pl_setting('font_extra') : ''; 
		
		if($gcss != '')
			$gcss .= '|'.$added;
		else 
			$gcss .= $added;
			
		if($gcss != '' )
			printf( "<link id='master_font_import' rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=%s'>\n", $gcss );
			
		
	}

	function add_settings( $settings ){

		$settings['typography'] = array(
				'name' 	=> __( 'Typography', 'pagelines' ),
				'icon'	=> 'icon-font',
				'pos'	=> 3,
				'opts' 	=> $this->options()
		);

		return $settings;
	}

	function options(){

		$settings = array(
			
			array(
				'type' 	=> 	'multi',
				'col'	=> 1,
				'title' => __( 'Primary Text', 'pagelines' ),
				'help' 		=> __( 'The base font size is a reference that will be scaled for text used throughout the site. ', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'			=> 'font_primary',
						'type' 			=> 'type',
						'label' 		=> __( 'Select Font Face', 'pagelines' ),
						'default'		=> 'open_sans',
						
					),
					array(
						'key'			=> 'base_font_size',
						'type'			=> 'count_select',
						'count_start'	=> 10,
						'count_number'	=> 30,
						'suffix'		=> 'px',
						'title'			=> __( 'Base Font Size', 'pagelines' ),
						'default'		=> 14, 
					),
					array(
						'key'			=> 'font_primary_weight',
						'type' 			=> 'select',
						'classes'		=> 'font-weight',
						'label'			=> __( 'Font Weight', 'pagelines' ),
						'opts'			=> array(
							'300'	=> array('name' => 'Light (300)*'),
							'400'	=> array('name' => 'Normal (400)'),
							'600'	=> array('name' => 'Semi-Bold (600)*'),
							'800'	=> array('name' => 'Bold (800)')
						),
						'default' 		=> '300',
						'help'			=> __( '*These weights don\'t alwaye have an effect.', 'pagelines' ),
					),
					
				),

			),
			array(
				'type' 	=> 	'multi',
				'col'	=> 2,
				'title' => __( 'Text Headers <small>(h1-h6)</small>', 'pagelines' ),
				'help' 		=> __( 'Configure the typography for the text headers across your site.', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'			=> 'font_headers',
						'type' 			=> 'type',
						'label' 		=> __( 'Header Font', 'pagelines' ),
						'default'		=> 'source_sans_pro',
						

					),
					array(
						'key'			=> 'header_base_size',
						'type'			=> 'count_select',
						'count_start'	=> 10,
						'count_number'	=> 30,
						'suffix'		=> 'px',
						'title'			=> __( 'Base Font Size', 'pagelines' ),
						'default'		=> 16, 
					),
					array(
						'key'			=> 'font_headers_weight',
						'type' 			=> 'select',
						'classes'			=> 'font-weight',
						'label'			=> __( 'Font Weight', 'pagelines' ),
						'opts'			=> array(
							'300'	=> array('name' => 'Light (300)'),
							'400'	=> array('name' => 'Normal (400)'),
							'600'	=> array('name' => 'Semi-Bold (600)'),
							'800'	=> array('name' => 'Bold (800)')
						),
						'default' 		=> '600',
					),
				),

			),
			array(
				'type' 	=> 	'multi',
				'col'	=> 3,
				'title' => __( 'Extra Fonts', 'pagelines' ),
				'help' 		=> __( '<p>Add additional <a href="http://www.google.com/fonts" target="_blank">Google fonts</a> to your sites using this option.</p><p>For example, to add "Yellowtail" and "Lato Bold" you would enter: <strong>Yellowtail|Lato:700</strong>.</p><p> You can then reference these fonts in custom CSS.</p>', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'			=> 'font_extra',
						'type' 			=> 'text',
						'label' 		=> __( 'Extra Google Fonts', 'pagelines' ),
						
					),
					
				),

			),

		

		);


		return $settings;

	}

}





