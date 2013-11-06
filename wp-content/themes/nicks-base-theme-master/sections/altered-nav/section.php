<?php
/*
	Section: Altered Nav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates footer navigation.
	Class Name: AlteredNav
*/

class AlteredNav extends PageLinesSection {

	function section_persistent(){
		register_nav_menus( array( 'simple_nav' => __( 'Simple Nav Section', 'pagelines' ) ) );

	}

   function section_template() { 

	if(function_exists('wp_nav_menu'))
		wp_nav_menu( array('menu_class'  => 'inline-list simplenav font-sub', 'theme_location'=>'simple_nav','depth' => 1,  'fallback_cb'=>'simple_nav_fallback') );
	else
		nav_fallback();
	}

}

if(!function_exists('simple_nav_fallback')){

	function simple_nav_fallback() {
		printf('<ul id="simple_nav_fallback" class="inline-list simplenav font-sub">%s</ul>', wp_list_pages( 'title_li=&sort_column=menu_order&depth=1&echo=0') );
	}
}
