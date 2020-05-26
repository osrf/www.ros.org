<?php
/*
	Section: ProPricing
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: An amazing, professional pricing section.
	Class Name: PLProPricing
	Filter: component
	Loading: active
	Edition: pro
*/


class PLProPricing extends PageLinesSection {

	var $default_limit = 3;

	function section_styles(){
		
	}


	function section_opts(){
		$options = array();

		$options[] = array(

			'title' => __( 'ProPricing Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				
				array(
					'key'			=> 'propricing_cols',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> '4',
					'label' 	=> __( 'Number of Columns for Each Plan (12 Col Grid)', 'pagelines' ),
				),
			)

		);

		$options[] = array(
			'key'		=> 'propricing_array',
	    	'type'		=> 'accordion', 
			'col'		=> 2,
			'title'		=> __('Pricing Setup', 'pagelines'), 
			'post_type'	=> __('Column', 'pagelines'), 
			'opts'	=> array(
				array(
					'key'	=> 'title',
					'label'	=> __( 'Title', 'pagelines' ),
					'type'			=> 'text'
				),
				array(
					'key'	=> 'price',
					'label'	=> __( 'Price', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'	=> 'price_pre',
					'label'	=> __( 'Before Price Text', 'pagelines' ),
					'type'	=> 'text',
					'help'	=> __( 'Typically you will add the monetary unit here. E.g. "$"', 'pagelines' ),
				),
				array(
					'key'	=> 'price_post',
					'label'	=> __( 'After Price Text', 'pagelines' ),
					'type'	=> 'text',
					'help'	=> __( 'Typically you will add the recurring amount here. E.g. "/ MO"', 'pagelines' ),
				),
				array(
					'key'	=> 'sub_text',
					'label'	=> __( 'Sub Text', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'	=> 'link',
					'label'	=> __( 'Link URL', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'	=> 'link_text',
					'label'	=> __( 'Link Text', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'	=> 'btn_theme',
					'label'	=> __( 'Button Theme', 'pagelines' ),
					'type'	=> 'select_button'
				),
				array(
					'key'	=> 'attributes',
					'label'	=> __( 'Attributes', 'pagelines' ),
					'type'	=> 'textarea',
					'help'	=> __( 'Add each attribute on a new line. Add a "*" in front to add emphasis.', 'pagelines' ),
				),
				
			)
	    );

		return $options;
	}


   function section_template( ) { 
	
		
	
		$item_array = $this->opt('propricing_array');

		$format_upgrade_mapping = array(
			'title'			=> 'propricing_title_%s',
			'price'			=> 'propricing_price_%s',
			'price_pre'		=> 'propricing_price_pre_%s',
			'price_post'	=> 'propricing_price_post_%s',
			'sub_text'		=> 'propricing_sub_%s',
			'link'			=> 'propricing_link_%s',
			'link_text'		=> 'propricing_link_text_%s',
			'btn_theme'		=> 'propricing_btn_%s',
			'attributes'	=> 'propricing_attributes_%s',
		); 

		$item_array = $this->upgrade_to_array_format( 'propricing_array', $item_array, $format_upgrade_mapping, $this->opt('propricing_count'));
	
		$cols = ($this->opt('propricing_cols')) ? $this->opt('propricing_cols') : 4;
		$width = 0;
		$output = '';
		$count = 1;
		$item_array = ( ! is_array($item_array) ) ? array( array(), array(), array() ) : $item_array;
		$num = count( $item_array );

		
		foreach( $item_array as $item){
			
			
			$title 		= pl_array_get( 'title', $item, 'Plan');
			$price_pre 	= pl_array_get( 'price_pre', $item, '$');
			$price 		= pl_array_get( 'price', $item, $count*8);
			$price_post = pl_array_get( 'price_post', $item, '/ MO');
			$sub 		= pl_array_get( 'sub_text', $item, sprintf('Billed annually or $%s/MO billed monthly.', $count*10) );
			$link 		= pl_array_get( 'link', $item, '#');
			$link_text 	= pl_array_get( 'link_text', $item);
			$btn_theme 	= pl_array_get( 'btn_theme', $item, 'btn-important');
			$attr 		= pl_array_get( 'attributes', $item);
		
		
			$attr_list = ''; 
			
			if($attr != ''){
				
				$attr_array = explode("\n", $attr);
				
				foreach($attr_array as $at){
					
					if(strpos($at, '*') === 0){
						$at = str_replace('*', '', $at); 
						$attr_list .= sprintf('<li class="emphasis">%s</li>', $at); 
					} else {
						$attr_list .= sprintf('<li>%s</li>', $at); 
					}
					
				}
				
			} 
			
			if($link != ''){
				
				$link_text = ($link_text != '') ? $link_text : 'Sign Up';
				$link_text = sprintf('<span class="btn-link-text" data-sync="propricing_link_text_%s">%s</span>', $count, $link_text);
				
				$formatted_link = sprintf('<li class="pp-link"><a href="%s" class="btn btn-large %s" >%s <i class="icon-chevron-sign-right"></i></a></li>', $link, $btn_theme, $link_text);
				
			} else {
				$formatted_link = ''; 
			}
			
			
			$attr_list = $formatted_link . $attr_list; 
			
			$formatted_attr = ($attr_list != '') ? sprintf('<div class="pp-attributes"><ul>%s</ul></div>', $attr_list) : '';
		
		
			$formatted_sub = ($sub != '') ? sprintf('<div class="price-sub" data-sync="propricing_sub_%s">%s</div>', $count, $sub) : ''; 
		
			if($width == 0)
				$output .= '<div class="row fix">';

			$output .= sprintf(
				'<div class="span%1$s pp-plan pl-animation pl-appear fix">
					<div class="pp-header">
						<div class="pp-title" data-sync="propricing_title_%8$s">
							%2$s
						</div>
						<div class="pp-price">
							<span class="price-pre" data-sync="propricing_price_pre_%8$s">%3$s</span>
							<span class="price" data-sync="propricing_price_%8$s">%4$s</span>
							<span class="price-post" data-sync="propricing_price_post_%8$s">%5$s</span>
							%6$s
						</div>
					</div>
					%7$s
				</div>',
				$cols,
				$title,
				$price_pre, 
				$price,
				$price_post,
				$formatted_sub,
				$formatted_attr, 
				$count
			);

			$width += $cols;

			if($width >= 12 || $count == $num){
				$width = 0;
				$output .= '</div>';
			}

			$count++;
		 }
	
	
	?>
	
	<div class="propricing-wrap pl-animation-group">
		<?php echo $output; ?>
	</div>

<?php }


}
