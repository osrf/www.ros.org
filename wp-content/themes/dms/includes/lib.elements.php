<?php 


/**
 * PageLines Default Widget
 *
 * Calls default sidebar widget, or allows user with 'edit_themes' capability to adds widgets
 *
 * @since   ...
 *
 * @param   $id - widget area ID
 * @param   $name - name of sidebar widget area
 * @param   $default - ...
 *
 * @uses    pagelines
 * @todo Finish paramater definitions
 */
function pagelines_default_widget($id, $default){

	global $wp_registered_sidebars;
	if( isset($wp_registered_sidebars[ $id ]) && isset($wp_registered_sidebars[ $id ]['name']) )
		$name = $wp_registered_sidebars[ $id ]['name'];
	else
		$name = 'Widgetized Area';

	if(isset($default) && !pagelines('sidebar_no_default')):

		get_template_part( $default );

	elseif( current_user_can('edit_themes') ):
	?>

	<li class="widget widget-default setup_area no_<?php echo $id;?>">
		<div class="widget-pad">
			<h3 class="widget-title">Add Widgets (<?php echo $name;?>)</h3>
			<p class="fix">This is your <?php echo $name;?> but it needs some widgets!<br/> Easy! Just add some content to it in your <a href="<?php echo admin_url('widgets.php');?>">widgets panel</a>.
			</p>
			<p>
				<a href="<?php echo admin_url('widgets.php');?>" class="btn"><i class="icon-retweet"></i> <?php _e('Add Widgets &rarr;', 'pagelines');?></a>
			</p>

		</div>
	</li>

<?php endif;
	}

/**
 * PageLines Standard Sidebar
 *
 * Defines standard sidebar parameters
 *
 * @since   ...
 *
 * @param   string $name - Name of sidebar area
 * @param   string $description - Description of sidebar area
 *
 * @internal    @param  before_widget - markup that wraps the widget area
 * @internal    @param  after_widget - closing tags of markup added in `before_widget`
 * @internal    @param  before_title - markup that wraps the widget title text
 * @internal    @param  after_title - closing tags of markup added in `before_title`
 *
 * @return  array - all sidebar parameters
 */
function pagelines_standard_sidebar($name, $description){
	return array(
		'name'=> $name,
		'description' => $description,
	    'before_widget' => '<li id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
	    'after_widget' => '</div></li>',
	    'before_title' => '<h3 class="widget-title">',
	    'after_title' => '</h3>'
	);
}




/**
 * PageLines Search Form
 *
 * Writes the default "Search" text to the search form's input field.
 * Allows the $searchform to be filtered via the pagelines_search_form hook
 *
 * @since   ...
 *
 * @param   bool $echo - defaults to true, outputs $searchform
 *
 * @return  mixed|void - if $echo is false, returns $searchform
 */
function pagelines_search_form( $echo = true ){

	// BPG: Change the text in the search box.  Poor form to edit this here, but I couldn't find a different place to do it.
	//$searchfield = sprintf('<input type="text" value="" name="s" class="searchfield" placeholder="%s" />', __('Search', 'pagelines'));
	$searchfield = sprintf('<input type="text" value="" name="s" class="searchfield" placeholder="%s" />', __('Enter a search term', 'pagelines'));

	$searchform = sprintf('<form method="get" class="searchform" onsubmit="this.submit();return false;" action="%s/" ><fieldset>%s</fieldset></form>', home_url(), $searchfield);

	if ( $echo )
		echo apply_filters('pagelines_search_form', $searchform);
	else
		return apply_filters('pagelines_search_form', $searchform);
}



/**
 *
 *  Pagination Function
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 2.0.b12 moved
 *
 */
function pagelines_pagination() {
	if(function_exists('wp_pagenavi') && show_posts_nav() && VPRO):

		$args = array(
			'before' => '<div class="pagination pagenavi">',
			'after' => '</div>',
		);
		wp_pagenavi( $args );

	elseif (show_posts_nav()) : ?>
		<ul class="pager page-nav-default fix">
			<li class="previous previous-entries">
				<?php next_posts_link(__('&larr; Previous Entries','pagelines')) ?>
			</li>
			<li class="next next-entries">
			<?php previous_posts_link(__('Next Entries &rarr;','pagelines')) ?>
			</li>
		</ul>
<?php endif;
}

// As a callback with nav args associated
function pl_nav_callback( $args ){
	pl_nav_fallback( $args['menu_class'] );
}

function pl_nav_fallback($class = '', $limit = 6){

	$pages = wp_list_pages('echo=0&title_li=&sort_column=menu_order&depth=1');

	$pages_arr = explode("\n", $pages);

	$pages_out = '';
	for($i=0; $i < $limit; $i++){

		if(isset($pages_arr[$i]))
			$pages_out .= $pages_arr[$i];

	}

	printf('<ul class="%s">%s</ul>', $class, $pages_out);
}


/**
 *
 *  Blank Nav Fallback
 *
 */
function blank_nav_fallback() {

	if(current_user_can('edit_themes'))
		printf( __( "<ul class='inline-list'>Please select a nav menu for this area in the <a href='%s'>WordPress menu admin</a>.</ul>", 'pagelines' ), admin_url('nav-menus.php') );
}

/**
 *
 *  Returns child pages for subnav, setup in hierarchy
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_page_subnav(){
	global $post;
	if(!is_404() && isset($post) && is_object($post) && !pagelines_option('hide_sub_header') && ($post->post_parent || wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0'))):?>
	<ul class="secondnav_menu lcolor3">
		<?php
			if(count($post->ancestors)>=2){
				$reverse_ancestors = array_reverse($post->ancestors);
				$children = wp_list_pages('title_li=&depth=1&child_of='.$reverse_ancestors[0].'&echo=0&sort_column=menu_order');
			}elseif($post->post_parent){ $children = wp_list_pages('title_li=&depth=1&child_of='.$post->post_parent.'&echo=0&sort_column=menu_order');
			}else{	$children = wp_list_pages('title_li=&depth=1&child_of='.$post->ID.'&echo=0&sort_column=menu_order');}

			if ($children) { echo $children;}
		?>
	</ul>
	<?php endif;
}

/**
 * PageLines Draw Sidebar
 *
 * Writes sidebar markup.
 * If no dynamic sidebar (widget) exists it calls the default widget
 *
 * @since   ...
 *
 * @param   $id - Sidebar ID
 * @param   $name - Sidebar name
 * @param   null $default
 * @param   string $element - CSS wrapper element, default is `ul`
 *
 * @uses    pagelines_default_widget
 */
function pagelines_draw_sidebar($id, $name = '', $default = false, $element = 'ul'){

	printf('<%s id="%s" class="sidebar_widgets fix">', $element, 'list_'.$id);

	if ( !dynamic_sidebar($id) ){
		
		if( ! $default )
			pagelines_default_widget( $id, $default);
		else 
			echo $default;
	}
	
	printf('</%s>', $element);

}

/**
*
* @TODO do
*
*/
function pledit( $id = '', $type = 'post' ){

	if($type == 'user'){

		$the_uid = $id;

		global $current_user;

		if($current_user == $the_uid)
			$link = admin_url( 'profile.php' );
		elseif(current_user_can('edit_users'))
			$link = admin_url( sprintf('user-edit.php?user_id=%s', $the_uid) );
		else
			$link = false;

	} else {

		if($id == ''){
			global $post;
			$id = $post->ID;
		}

		 if ( false == ( $p = get_post( $id ) ) )
		 	return '';

		$post_type_object = get_post_type_object( $p->post_type );

		if ( !$post_type_object )
			return '';

		if ( !current_user_can( $post_type_object->cap->edit_post, $p->ID ) )
			return '';

		$link = get_edit_post_link( $p->ID );

	}

	if( $link ){
		$format = apply_filters( 'pagelines_pledit_filter', '[%s]' );
		$button = sprintf(" <a class='pledit' href='%s'><span class='pledit-pad'>{$format}</span></a> ",
			$link,
			__( 'edit', 'pagelines' )
			);

		return $button;
	} else
		return '';
}
