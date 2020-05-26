<?php
/**
 * PageLines Posts Handling
 *
 * @package     PageLines Framework
 * @subpackage  Posts
 * @since       2.0.b2
 */
class PageLinesPosts {

	var $tabs = array();

	/** PHP5 constructor */
	function __construct( PageLinesPostLoop $section ) {

		global $pagelines_layout;
		global $post;
		global $wp_query;
		global $ex_length;
		global $ex_tags;
		
		$this->section = $section;

		$this->count = 1;  // Used to get the number of the post as we loop through them.
		$this->clipcount = 2; // The number of clips in a row

		$this->post_count = $wp_query->post_count;  // Used to prevent markup issues when there aren't an even # of posts.
		$this->paged = intval(get_query_var('paged')); // Control output if on a paginated page

		$this->thumb_space = get_option('thumbnail_size_w') + 33; // Space for thumb with padding

		
		$cr_link = $this->section->opt('continue_reading_text');
		$ex_length = $this->section->opt('excerpt_len');
		$ex_tags = $this->section->opt('excerpt_tags');
	
		$this->continue_reading = ($cr_link) ? $cr_link : __('Read More &raquo;', 'pagelines');

		add_filter('pagelines_post_metabar', 'do_shortcode', 20);


	}


	/**
     * Load Loop
     *
	 * Loads the content using WP's standard output functions, if no posts exists the framework's 404 page is loaded instead.
     *
     * @uses    get_article
     * @uses    posts_404
	 *
	 * @since   2.0.0
	 */
	function load_loop(){

		if( have_posts() )
			while ( have_posts() ) : the_post();  $this->get_article(); endwhile;
		else
			$this->posts_404();

	}


	/**
     * Get Article
     *
     * Builds the post being displayed by the load_loop function adding clip formatting as required as well as relevant post classes
     *
     * @uses    pagelines_show_clip
     * @uses    post_header
     * @uses    post_entry
     *
     * @internal uses filter 'pagelines_get_article_post_classes'
     * @internal uses filter 'pageliens_get_article_output'
     */
	function get_article(){
		global $wp_query;
		global $post;

		/* clip handling */
		$clip = ( $this->pagelines_show_clip( $this->count, $this->paged ) ) ? true : false;

		$meta_mode = $this->section->opt('pl_meta_mode');

		$format = ( $clip ) ? 'clip' : 'feature';
		$clip_row_start = ( $this->clipcount % 2 == 0 ) ? true : false;
		$clip_right = ( ( $this->clipcount+1 ) % 2 == 0 ) ? true : false;
		$clip_row_end = ( $clip_right || $this->count == $this->post_count ) ? true : false;

		$post_type_class = ( $clip ) ? ( $clip_right ? 'clip clip-right' : 'clip' ) : 'fpost';
		
		$meta_mode_class = ( isset($meta_mode) && $meta_mode != '') ? 'meta-mode-'.$meta_mode : '';

		$pagelines_post_classes = apply_filters( 'pagelines_get_article_post_classes', sprintf( '%s post-number-%s', $post_type_class, $this->count ) );

		$post_classes = join( ' ', get_post_class( $pagelines_post_classes ) );

		$wrap_type = ($clip) ? 'clip_box' : 'article-wrap';
		
		$wrap_start = (($clip && $clip_row_start) || !$clip ) ? sprintf('<div class="%s %s fix">', $wrap_type, $meta_mode_class) : '';
		 
		$wrap_end = ( ($clip && $clip_row_end) || !$clip ) ? sprintf( '</div>' ) : '';

		$author_tag = (!$clip && $meta_mode == 'author') ? $this->get_author_tag() : '';


		$post_args = array(
			'header'		=> $this->post_header( $format ),
			'entry'			=> $this->post_entry(),
			'classes'		=> $post_classes,
			'pad-class'		=> ( $clip ) ? 'hentry-pad blocks' : 'hentry-pad',
			'wrap-start'	=> $wrap_start,
			'wrap-end'		=> $wrap_end,
			'format'		=> $format,
			'count'			=> $this->count
		);

		$post_args['markup-start'] = sprintf(
			'%s<article class="%s" id="post-%s">%s<div class="wrp"><div class="%s">',
			$post_args['wrap-start'],
			
			$post_args['classes'],
			$post->ID,
			$author_tag,
			$post_args['pad-class']
			
		);

		$post_args['markup-end'] = sprintf(
			'</div></div></article>%s',
			$post_args['wrap-end']
		);

		$original = join(array(
				$post_args['markup-start'],
				$post_args['header'],
				$post_args['entry'],
				$post_args['markup-end']
			));

		echo apply_filters( 'pagelines_get_article_output', $original, $post, $post_args );

		// Count the clips
		if( $clip )
			$this->clipcount++;

		// Count the posts
		$this->count++;
	 }
	
	function get_author_tag(){
		global $post;
		$author_email = get_the_author_meta('email', $post->post_author);
		$author_name = get_the_author();
		$default_avatar = PL_IMAGES . '/avatar_default.gif';
		$author_desc = custom_trim_excerpt( get_the_author_meta('description', $post->post_author), 10);
		ob_start();
		?>
		<div class="author-tag">
			<div class="tmb"><?php echo get_avatar( $author_email, '120'); ?></div>
			<p>[post_author_posts_link]</p>
			<hr/>
		<?php
		if( has_action( 'pl_get_author_tag' ) ) {			
			do_action( 'pl_get_author_tag' );
		} else {	
		?>
			<p><strong>Published</strong><br/> [post_date][post_edit]</p>
			 
			<p class="hidden-sm hidden-phone">In [post_categories]</p>
			<hr/>
			<p class="tag-comments hidden-sm hidden-phone"><i class="icon-comment"></i> [post_comments zero="Add Comment" one="1" more="%"]</p>
		
		<?php
		}
		echo '</div>';
	return do_shortcode( ob_get_clean() );	
	}


	/**
     * Post Entry
     *
     * @uses    pagelines_show_content
     * @internal uses filter 'pagelines_post_entry'
     *
     * @return  mixed|string|void
     */
	function post_entry(){

		$id = get_the_ID();

		if( $this->pagelines_show_content( $id ) ){

			$excerpt_mode = ($this->section->opt( 'excerpt_mode_full' )) ? $this->section->opt( 'excerpt_mode_full' ) : 'top';


			if( ( $excerpt_mode == 'left-excerpt' || $excerpt_mode == 'right-excerpt' ) && is_single() && $this->pagelines_show_thumb( $id ) )
				$thumb = $this->post_thumbnail_markup( $excerpt_mode );
			else
				$thumb = '';

			$post_entry = sprintf( '<div class="entry_wrap fix"><div class="entry_content">%s%s</div></div>', $thumb, $this->post_content() );

			return apply_filters( 'pagelines_post_entry', $post_entry );

		} else
			return '';
	}


	/**
     * Post Content
     *
     * Captures the post content wrapped in 'pageslines_loop_*_post_content' hooks and returns it
     *
     * @uses    pageslines_register_hook
     * @uses    pledit
     *
     * @return  string - the content
     */
	function post_content(){

		ob_start();

			pagelines_register_hook( 'pagelines_loop_before_post_content', 'theloop' ); // Hook

		//	global  $post;

			$content = get_the_content( $this->continue_reading );

			$content .= pledit( get_the_ID() );

			echo apply_filters( 'the_content', $content );

			if( is_single() || is_page() ){

				$pgn = array(
					'before' 			=> __( "<div class='pagination'><span class='desc'>pages:</span><ul>", 'pagelines' ),
					'after' 			=> '</ul></div>',
					'link_before'		=> '<span class="pg">',
					'link_after'		=> '</span>'
				);

				wp_link_pages( $pgn );
			}

			if ( is_single() && get_the_tags() )
				printf(
					'<div class="p tags">%s&nbsp;</div>',
					get_the_tag_list(
						__( "<span class='note'>Tagged with &rarr;</span> ", 'pagelines' ),
						' &bull; ',
						''
					)
				);

			pagelines_register_hook( 'pagelines_loop_after_post_content', 'theloop' ); // Hook

		$the_content = ob_get_clean();

		return $the_content;

	}


    /**
     * Post Header
     *
     * Creates the post header information adding classes as required for clipped format and thumbnails images as required
     *
     */
	function post_header( $format = '' ){

		
		if( $this->show_post_header() ){


			global $post;

			$id = get_the_ID();

			$excerpt_mode = ( $format == 'clip' ) ? $this->section->opt( 'excerpt_mode_clip' ) : $this->section->opt( 'excerpt_mode_full' );
			
			$excerpt_mode = ( $excerpt_mode ) ? $excerpt_mode : 'top';
			
			$thumb = ( $this->pagelines_show_thumb( $id ) ) ? $this->post_thumbnail_markup( $excerpt_mode, $format ) : '';

			$excerpt_thumb = ( $thumb && ( $excerpt_mode == 'left-excerpt' || $excerpt_mode == 'right-excerpt' ) ) ? '' : $thumb;

			$excerpt = ( $this->pagelines_show_excerpt( $id ) ) ? $this->post_excerpt_markup( $excerpt_mode, $excerpt_thumb ) : '';
			
			$classes = 'post-meta fix ';
			$classes .= ( ! $this->pagelines_show_thumb( $id ) ) ? 'post-nothumb ' : '';
			$classes .= ( ! $this->pagelines_show_content( $id ) ) ? 'post-nocontent ' : '';

			$title = sprintf( '<section class="bd post-title-section fix"><div class="post-title fix">%s</div>%s</section>', $this->pagelines_get_post_title( $format ), $this->pagelines_get_post_metabar( $format ) );

			if( ( $excerpt_mode == 'left-excerpt' || $excerpt_mode == 'right-excerpt' ) && ! is_single() )
				$post_header = sprintf( '<section class="%s"><section class="bd post-header fix" >%s %s%s</section></section>', $classes, $title, $thumb, $excerpt );
			elseif( $excerpt_mode == 'top' )
				$post_header = sprintf( '<section class="%s">%s<section class="bd post-header fix" >%s %s</section></section>',$classes, $thumb, $title, $excerpt );
			elseif( $excerpt_mode == 'left' )
				$post_header = sprintf( '<section class="%s media">%s<section class="bd post-header fix" >%s %s</section></section>', $classes, $thumb, $title, $excerpt );
			else
				$post_header = sprintf( '<section class="%s">%s<section class="bd post-header fix" >%s %s</section></section>',$classes, '', $title, $excerpt );

			return apply_filters( 'pagelines_post_header', $post_header, $format );

		} else
			return '';

	}



	/**
	 * Determines if the post title area should be shown
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if the title area should be shown
	 */
	function show_post_header( ) {

		if( !is_page() || (is_page() && $this->section->opt('pagetitles')) )
			return true;
		else
			return false;

	}

	/**
	 * Get post excerpt and markup
	 *
	 * @since 2.0.0
	 *
	 * @return string the excerpt markup
	 */
	function post_excerpt_markup( $mode = '', $thumbnail = '' ) {

		ob_start();

		pagelines_register_hook( 'pagelines_loop_before_excerpt', 'theloop' ); // Hook

		if($mode == 'left-excerpt' || $mode == 'right-excerpt')
			printf( '<aside class="post-excerpt">%s %s</aside>', $thumbnail, get_the_excerpt() );
		else
			printf( '<aside class="post-excerpt">%s</aside>', get_the_excerpt() );


		if(pagelines_is_posts_page() && !$this->pagelines_show_content( get_the_ID() )) // 'Continue Reading' link
			echo $this->get_continue_reading_link( get_the_ID() );

		pagelines_register_hook( 'pagelines_loop_after_excerpt', 'theloop' ); // Hook

		$pagelines_excerpt = ob_get_clean();

		return apply_filters('pagelines_excerpt', $pagelines_excerpt);

	}


    /**
     * Post Thumbnail Markup
     *
     * Get post thumbnail and markup
     *
     * @since   2.0.0
     *
     * @param   string $mode - right, left, or top
     * @param   string $format - ...
     * @param   string $frame - not used
     *
     * @return  string - the thumbnail markup
     *
     * @version 2.2 - fixed image size when thumbnail is displayed on top of excerpt
     * @todo review if top displayed image should be centered above post, or remain left aligned
     */
	function post_thumbnail_markup( $mode = '', $format = '', $frame = '' ) {

		$thumb_width = get_option( 'thumbnail_size_w' );

		$classes = 'post-thumb img fix';

		$percent_width  = ( $mode == 'top' ) ? 100 : 25;

        $style = ( 'top' == $mode ) ? 'width: 100%' : sprintf( 'width: %s%%; max-width: %spx', apply_filters( 'pagelines_thumb_width', $percent_width ), $thumb_width );

		if ( $mode == 'left-excerpt' )
			$classes .= ' alignleft';
		elseif ( $mode == 'right-excerpt' )
			$classes .= ' alignright';
        /** By default image will left align, explicitly adding this class for 'top' == $mode is not needed at this time.
         * elseif ( $mode == 'top' ) $classes .= ' left';
         */

		global $post;

		$img = ( $mode == 'top' ) ? get_the_post_thumbnail( null, 'landscape-thumb' ) : get_the_post_thumbnail( null, 'thumbnail' );

		$the_image = sprintf( '<span class="c_img">%s</span>', $img );

		$thumb_link = sprintf( '<a class="%s" href="%s" rel="bookmark" title="%s %s" style="%s">%s</a>', $classes, get_permalink( $post ), __( 'Link To', 'pagelines' ), the_title_attribute( array( 'echo' => false ) ), $style, $the_image );

        $output = ( 'top' == $mode ) ? sprintf( '<div class="full_img fix">%s</div>', $thumb_link ) : $thumb_link;

		return apply_filters( 'pagelines_thumb_markup', $output, $mode, $format );

	}

	/**
	 * Adds the metabar or byline under the post title
	 *
	 * @since 1.1.0
	 */
	function pagelines_get_post_metabar( $format = '' ) {

		$metabar = '';
		$before = '<em>';
		$after = '</em>';
		if ( is_page() )
			return; // don't do post-info on pages

		if( $format == 'clip'){

			$metabar = ( $this->section->opt( 'metabar_clip' ) )
				? $before . $this->section->opt( 'metabar_clip' ) . $after
				: sprintf( '%s%s [post_date] %s [post_author_posts_link] [post_edit]%s', $before, __('On','pagelines'), __('By','pagelines'), $after );

		} elseif( 'author' !== $this->section->opt('pl_meta_mode') ) {
			$metabar = ( $this->section->opt( 'metabar_standard' ) )
				? $before . $this->section->opt( 'metabar_standard' ) . $after
				: sprintf( '%s%s [post_author_posts_link] %s [post_date] &middot; [post_comments] &middot; %s [post_categories] [post_edit]%s', $before, __('By','pagelines'), __('On','pagelines'), __('In','pagelines'), $after);

		}

		return sprintf( '<div class="metabar"><div class="metabar-pad">%s</div></div>', apply_filters('pagelines_post_metabar', $metabar, $format) );

	}

    /**
     * PageLines Get Post Title
     *
     * Gets the post title for all posts
     *
     * @package     PageLines Framework
     * @subpackage  Functions Library
     * @since       1.1.0
     *
     * @param       string $format
     *
     * @uses        pagelines_option( 'pagetitles' )
     * @uses        get_the_title - default WordPress post title text
     *
     * @internal    adds filter 'pagelines_post_title_text'
     * @internal    adds filter 'pagelines_post_title_output'
     *
     * @return      string - (new) Post $title
     */
	function pagelines_get_post_title( $format = '' ){

		global $post;
		global $pagelines_ID;

		/** Check if page and show page title option is set to true */
        if( is_page() && $this->section->opt('pagetitles') && ! has_filter( "pagelines_no_page_title_{$pagelines_ID}" ) ) {
	
	
			$title = sprintf( '<h1 class="entry-title pagetitle">%s</h1>', apply_filters( 'pagelines_post_title_text', get_the_title() ) );
			
		} elseif(!is_page()) {

			if ( is_singular() )
				$title = sprintf( '<h1 class="entry-title">%s</h1>', apply_filters( 'pagelines_post_title_text', get_the_title() ) );
			elseif( $format == 'clip')
				$title = sprintf( '<h4 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h4>', get_permalink( $post ), the_title_attribute('echo=0'), apply_filters( 'pagelines_post_title_text', get_the_title() ) );
			else
				$title = sprintf( '<h2 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h2>', get_permalink( $post ), the_title_attribute('echo=0'), apply_filters( 'pagelines_post_title_text', get_the_title() ) );

		} else {$title = '';}


		return apply_filters('pagelines_post_title_output', $title) . "\n";

	}



	/**
	 *
	 *  Gets the continue reading link after excerpts
	 *
	 *  @package PageLines DMS
	 *  @subpackage Functions Library
	 *  @since 1.3.0
	 *
	 */
	function get_continue_reading_link($post_id){

		$link = sprintf(
			'<a class="continue_reading_link" href="%s" title="%s %s">%s</a>',
			get_permalink(),
			__("View", 'pagelines'),
			the_title_attribute(array('echo'=> 0)),
			$this->continue_reading
		);

		return apply_filters('continue_reading_link', $link);
	}


	/**
	*
	* @TODO document
	*
	*/
	function pagelines_show_thumb($post = null, $location = null){

		 if( function_exists('the_post_thumbnail') && has_post_thumbnail($post) ){

			if( pl_has_editor() ){

				if( is_page() )
					return false;

				if($this->section->opt('hide_thumb'))
					return false;
				else
					return true;
					
			} else{

				if( $location == 'clip' && pl_setting('thumb_clip') ) return true;

				if( !isset($location) ){

					if( pl_has_editor() ){
						if($this->section->opt('hide_thumb'))
							return false;
						else
							return true;
					} else{

						// Thumb Page
						if( is_single() && pl_setting('thumb_single') ) return true;

						// Blog Page
						elseif( is_home() && pl_setting('thumb_blog') ) return true;

						// Search Page
						elseif( is_search() && pl_setting('thumb_search') ) return true;

						// Category Page
						elseif( is_category() && ! is_date() && pl_setting('thumb_category') ) return true;

						// Archive Page
						elseif( ! is_category() && is_archive() && pl_setting('thumb_archive') ) return true;

						else return false;

					}


				} else
					return false;


			}

		} else
			return false;

	}


	/**
	*
	* @TODO document
	*
	*/
	function pagelines_show_excerpt( $post = null ){

			if( is_page() || is_single() )
				return false;
				
		

			if( pl_has_editor() ){

				if( $this->section->opt('hide_excerpt')){
					return false;
				} else
					return true;

			} else {

				// Thumb Page
				if( is_single() && pl_setting('excerpt_single') )
					return true;

				// Blog Page
				elseif( is_home() && pl_setting('excerpt_blog') )
					return true;

				// Search Page
				elseif( is_search() && pl_setting('excerpt_search') )
					return true;

				// Category Page
				elseif( is_category() && ! is_date() && pl_setting('excerpt_category') )
					return true;

				// Archive Page
				elseif( ! is_category() && is_archive() && pl_setting('excerpt_archive') )
					return true;

				else
					return false;

			}
	}


	/**
	*
	* @TODO document
	*
	*/
	function pagelines_show_content($post = null){
			// For Hook Parsing
			if( is_admin() )
				return true;

			// show on single post pages only
			if( is_page() || is_single() )
				return true;

			elseif(pl_has_editor() && $this->section->opt('show_content'))
				return true;

			// Blog Page
			elseif( is_home() && pl_setting('content_blog') )
				return true;

			// Search Page
			elseif( is_search() && pl_setting('content_search') )
				return true;

			// Category Page
			elseif( is_category() && pl_setting('content_category') )
				return true;

			// Archive Page
			elseif( ! is_category() && is_archive() && pl_setting('content_archive') )
				return true;

			else
				return false;

	}

	/*
		Show clip or full width post
	*/
	function pagelines_show_clip($count, $paged){

		if(!VPRO)
			return false;

		$archives = apply_filters( 'pagelines_full_width_archives', false );

		if( ( is_home() || $archives ) && $this->section->opt('blog_layout_mode') == 'magazine' && $count <= $this->section->opt('full_column_posts') && $paged == 0)
			return false;

		elseif($this->section->opt('blog_layout_mode') != 'magazine')
			return false;

		elseif(is_page() || is_single())
			return false;

		else
			return true;
	}



	/**
	*
	* @TODO document
	*
	*/
	function posts_404(){

		$head = ( is_search() ) ? sprintf(__('No results for &quot;%s&quot;', 'pagelines'), get_search_query()) : __('Nothing Found', 'pagelines');

		$subhead = ( is_search() ) ? __('Try another search?', 'pagelines') : __("Sorry, what you are looking for isn't here.", 'pagelines');

		$the_text = sprintf('<h2 class="center">%s</h2><p class="subhead center">%s</p>', $head, $subhead);

			printf( '<section class="billboard">%s <div class="center fix">%s</div></section>', apply_filters('pagelines_posts_404', $the_text), pagelines_search_form( false ));

	}


}
/* ------- END OF CLASS -------- */
