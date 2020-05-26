<?php
/*
	Section: TextBox
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A simple box for text and HTML.
	Class Name: PageLinesTextBox
	Filter: component
	Loading: active
*/

class PageLinesTextBox extends PageLinesSection {

	function section_opts(){
		$opts = array(
			array(
				'type'		=> 'multi',
				'key'		=> 'textbox_text', 
				'opts'		=> array(
					array(
						'type' 			=> 'text',
						'key'			=> 'textbox_title',
						'label' 		=> __( 'Title (Optional)', 'pagelines' ),
					),
					array(
						'type' 			=> 'select',
						'key'			=> 'textbox_title_wrap',
						'label' 		=> __( 'Title wrapper', 'pagelines' ),
						'default'		=> 'strong',
						'opts'			=> array(
							'strong'		=> array('name' => '&lt;strong&gt; (default)'),
							'h1'			=> array('name' => '&lt;h1&gt;'),
							'h2'			=> array('name' => '&lt;h2&gt;'),
							'h3'			=> array('name' => '&lt;h3&gt;'),
							'h4'			=> array('name' => '&lt;h4&gt;'),
							'h5'			=> array('name' => '&lt;h5&gt;'),
							'none'			=> array('name' => 'none'),
						)
					),					
					array(
						'type' 			=> 'textarea',
						'key'			=> 'textbox_content',
						'label' 		=> __( 'Text Content', 'pagelines' ),
					),
					
				)
			), 
			array(
				'type'		=> 'multi',
				'key'		=> 'textbox_config', 
				'title'		=> 'Textbox Display',
				'col'		=> 2,
				'opts'		=> array(
					array(
						'key'			=> 'textbox_pad',
						'type' 			=> 'text',
						'label' 	=> __( 'Padding <small>(CSS Shorthand)</small>', 'pagelines' ),
						'ref'		=> __( 'This option uses CSS padding shorthand. For example, use "15px 30px" for 15px padding top/bottom, and 30 left/right.', 'pagelines' ),
						
					),
					array(
						'key'			=> 'textbox_font_size',
						'type'			=> 'count_select',
						'count_start'	=> 10,
						'count_number'	=> 30,
						'suffix'		=> 'px',
						'title'			=> __( 'Textbox Font Size', 'pagelines' ),
						'default'		=> '', 
					),
					
					array(
						'type' 			=> 'select',
						'key'			=> 'textbox_align',
						'label' 		=> 'Alignment',
						'opts'			=> array(
							'textleft'		=> array('name' => 'Align Left (Default)'),
							'textright'		=> array('name' => 'Align Right'),
							'textcenter'	=> array('name' => 'Center'),
							'textjustify'	=> array('name' => 'Justify'),
						)
					),
					array(
						'type' 			=> 'select_animation',
						'key'			=> 'textbox_animation',
						'label' 		=> __( 'Viewport Animation', 'pagelines' ),
						'help' 			=> __( 'Optionally animate the appearance of this section on view.', 'pagelines' ),
					),
					
				)
			),
			
			
			
		);

		return $opts;

	}

	function section_template() {

		global $pldraft;
		$edit = false;
		$extra = '';
		if( is_object( $pldraft ) && 'draft' == $pldraft->mode )
			$edit = true;

		$title_wrap = ( '' != $this->opt( 'textbox_title_wrap' ) ) ? $this->opt( 'textbox_title_wrap' ) : 'strong';

		$text = $this->opt('textbox_content');

		$title = $this->opt('textbox_title');
		
		if( ! $text && ! $title ){
			$title = 'Textbox Section';
			$text = "Add Content!";
		} 
		
		if( 'strong' == $title_wrap )
			$extra = '<br />';
		
		if( '' != $title ) {
			if( 'none' != $title_wrap )
				$title = sprintf( '<%s data-sync="textbox_title">%s</%s>%s', $title_wrap, $title, $title_wrap, $extra );
			else
				$title = sprintf( '<span data-sync="textbox_title">%s</span>', $title );
		}
		
		$text = sprintf('<div class="hentry" data-sync="textbox_content">%s</div>', do_shortcode( wpautop($text) ) ); 
		
		$class = $this->opt('textbox_animation');
			
		$align = $this->opt('textbox_align');
		
		$pad = ($this->opt('textbox_pad')) ? sprintf('padding: %s;', $this->opt('textbox_pad')) : ''; 
		$size = ($this->opt('textbox_font_size')) ? sprintf('font-size: %spx;', $this->opt('textbox_font_size')) : ''; 
		
		printf('<div class="textbox-wrap pl-animation %s %s" style="%s%s">%s%s</div>', $align, $class, $pad, $size, $title, $text);

	}
}


