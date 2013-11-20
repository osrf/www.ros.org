<?php
/*
Plugin Name: Canonical URL's
Version: 2.0.1
Plugin URI: http://yoast.com/wordpress/canonical/
Description: Adds rel="canonical" URL's of your choice to the &lt;head&gt; of your posts and pages, instead of the default.
Author: Joost de Valk
Author URI: http://yoast.com/
Revision Author: Jesse Heap

Copyright 2009-2010 Joost de Valk (email: joost@yoast.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/*
	Changelog:
	2.0.1		Only remove the default canonical when there is a cross domain one
	2.0			Removed automatic canonical on pages, and added option to canonicalize URL's
	1.0.1 		Fixed double slashes on category and tag pages
	1.0 		Initial version
*/

if (!is_admin()) {

	function yoast_canonical_link() {
		global $post;
		$url = get_post_meta($post->ID, '_canonical', true);
		if ( $url ) {
			echo '<link rel="canonical" href="'.$url.'"/>'."\n";
			remove_action('wp_head','rel_canonical');
		}
	}
	add_action('wp_head', 'yoast_canonical_link',1);

} else {
	
	function save_canonical_postdata( $post_id ) {  
		if (isset($_POST['canonical'])) {
			$newcanonical = $_POST['canonical'];
			$curcanonical = get_post_meta($post_id, '_canonical');
			if($curcanonical == "")  
				add_post_meta($post_id, '_canonical', $newcanonical, true);  
			elseif($newcanonical != $curcanonical)  
				update_post_meta($post_id, '_canonical', $newcanonical);  
			elseif($newcanonical == "")  
				delete_post_meta($post_id, '_canonical', $curcanonical);	
		}		
	}
	
	function canonical_box() {
		global $post;
		echo '<table class="form_table">';
		echo '<tr>';
		echo '<th width="30%"><label for="canonical">Canonical link for this page</label></th>';
		echo '<td width="70%"><input type="text" size="60" name="canonical" id="canonical" value="'.get_post_meta($post->ID, '_canonical', true).'"/></td>';
		echo '</table>';
	}
	
	function create_canonical_meta_box() {  
		if ( function_exists('add_meta_box') ) {  
			add_meta_box( 'canonical-box', 'Canonical', 'canonical_box', 'post', 'normal', 'high' );  
			add_meta_box( 'canonical-box', 'Canonical', 'canonical_box', 'page', 'normal', 'high' );  
		}
	}
	add_action('admin_menu', 'create_canonical_meta_box');  
	add_action('save_post', 'save_canonical_postdata');
	
}

?>