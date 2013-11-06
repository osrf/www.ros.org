<?php
/**
 *
 *
 *  PageLines Page Handling
 *
 *
 */
class PageLinesPage {

	var $special_base = 70000000;
	var $opt_type_info = 'pl-type-info';

	function __construct( $args = array() ) {

		$args = wp_parse_args($args, $this->defaults());

		$mode = $args['mode'];

		if( $mode == 'ajax' ){

			$this->id = $args['pageID'];

			$this->typeid = $args['typeID'];

		} else {

			$this->id = $this->id();

			$this->type = $this->type();

			$this->typeid = $this->special_id();

			$this->template = $this->template();


			$this->type_name = ucwords( str_replace('_', ' ', $this->type()) );

		}

	}

	function defaults(){
		$d = array(
			'mode'		=> 'standard',
			'pageID'	=> '',
			'typeID'	=> ''
		);
		return $d;
	}

	function template(){

		$page = pl_local( $this->id, 'page-template' );
		$type = pl_local( $this->typeid, 'page-template' );
		$gbl = pl_global( 'page-template' );

		if( $page && $page != 'default' )
			$tpl = $page;
		elseif( $type && $type != 'default' )
			$tpl = $type;
		elseif( $gbl )
			$tpl = $gbl;
		else
			$tpl = 'default';

		return $tpl;

	}

	function id(){
		global $post;
		if(!$this->is_special() && isset($post) && is_object($post))
			return $post->ID;
		else
			return $this->special_id();

	}

	function special_id(){

		$index = $this->special_index_lookup();

		$id = $this->special_base + $index;

		return $id;

	}

	function special_index_lookup(){

		$lookup_array = array(
			'blog',
			'category',
			'search',
			'tag',
			'author',
			'archive',
			'page',
			'post',
			'404_page'
		);
		
		$index = array_search( $this->type(), $lookup_array );
		
		if( !$index )
			$index = pl_create_int_from_string( $this->type() ); 

		return $index;

	}

	function type(){

		if( is_404() )
			$type = '404_page';

		elseif( pl_is_cpt('archive') )
			$type = get_post_type_plural();

		elseif( is_tag() )
			$type = 'tag';

		elseif( is_search() )
			$type = 'search';

		elseif( is_category() )
			$type = 'category';

		elseif( is_author() )
			$type = 'author';

		elseif( is_archive() )
			$type = 'archive';

		elseif( is_home() )
			$type = 'blog';

		// ID is now set...
		elseif( pl_is_cpt() )
			$type = get_post_type();

		elseif( is_page() )
			$type = 'page';

		elseif( is_single() )
			$type = 'post';

		else
			$type = 'other';

		return $type;

	}
	
	function page_scope(){
		if(is_page() || $this->page->is_special()){
			return 'local';
		} else {
			return 'type';
		}
	}

	function is_special(){

		if ( is_404() || is_home() || is_search() || is_archive() )
			return true;
		else
			return false;

	}

	function is_posts_page(){

		if ( is_home() || is_search() || is_archive() || is_category() )
			return true;
		else
			return false;

	}


}

function pl_page_id(){
	global $plpg; 
	return $plpg->id; 
}

function pl_type_id(){
	global $plpg; 
	return $plpg->typeid; 
}

function pl_type_slug(){
	global $plpg; 
	return $plpg->type; 
}


