<?php
/*
	Section: SimpleNav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a simple single line navigation. Select menu and alignment.
	Class Name: SimpleNav
	Workswith: footer
	Filter: nav
	Loading: active
*/

/**
 * Simple Nav Section
 *
 * @package PageLines DMS
 * @author PageLines
 */
class SimpleNav extends PageLinesSection {

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		register_nav_menus( array( 'simple_nav' => __( 'Simple Nav Section', 'pagelines' ) ) );

	}

	function section_opts(){
		$opts = array(
			array(
				'key'		=> 'simple_nav_menu_multi',
				'type' 		=> 'multi',
				'title'		=> __( 'Select Menu', 'pagelines' ),
				'help'		=> __( 'The SimpleNav uses WordPress menus. Select one for use.', 'pagelines' ),
				'opts'		=> array(
					array(
							'key'			=> 'simple_nav_menu' ,
							'type' 			=> 'select_menu',
							'label' 	=> __( 'Select Menu', 'pagelines' ),
						),
				),


			),
			array(
				'type' 			=> 'select',
				'title' 		=> 'Select Alignment',
				'key'			=> 'simple_nav_align',
				'label' 		=> __( 'Select Alignment', 'pagelines' ),
				'opts'=> array(
					'center'	=> array( 'name' => 'Align Center (Default)' ),
					'left'	 	=> array( 'name' => 'Align Left' ),
					'right'	 	=> array( 'name' => 'Align Right' )
				),
			),
		);

		return $opts;
	}

	/**
	* Section template.
	*/
   function section_template() {

		$menu = ( $this->opt( 'simple_nav_menu' ) ) ? $this->opt( 'simple_nav_menu' ) : null;

		$align = ( $this->opt( 'simple_nav_align' ) ) ? 'align-'.$this->opt( 'simple_nav_align' ) : 'align-center';

		$classes = sprintf('inline-list simplenav font-sub %s', $align);

		$args = array(
			'menu_class'  	=> $classes,
			'menu'			=> $menu,
			'depth' 		=> 1,
			'fallback_cb'	=> 'pl_nav_callback'
		);
		wp_nav_menu( $args );

	}

}
