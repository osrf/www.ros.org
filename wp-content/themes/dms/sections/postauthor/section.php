<?php
/*
	Section: PostAuthor
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds author information to pages and posts.
	Class Name: PageLinesPostAuthor
	Workswith: main-single, author
	Failswith: archive, category, posts, tags, search, 404_page
	Filter: component
	Loading: active
*/

/**
 * Post Author Section
 *
 * @package PageLines DMS
 * @author PageLines
 */
class PageLinesPostAuthor extends PageLinesSection {

	function section_opts(){
		global $post;

		if(!$post || !is_object($post))
			return '';

		$author_id = $post->post_author;

		$opts = array(
			array(
				'key'	=> 'author_setup',
				'type'	=> 'link',
				'url'	=> admin_url( 'user-edit.php?user_id='.$author_id ),
				'title'	=> __( 'Author Setup', 'pagelines' ),
				'label'		=> '<i class="icon-edit"></i> Edit Author Info',
				'help'		=> __( "This section uses the author's profile information. Set that in your admin.", 'pagelines' ),
			)
		);

		return $opts;
	}

	/**
	* Section template.
	*/
   function section_template() {
	global $post;
	setup_postdata($post);

	ob_start();
		the_author_meta('url');
	$link = ob_get_clean();

		$default_avatar = PL_IMAGES . '/avatar_default.gif';
		$author_email = get_the_author_meta('email', $post->post_author);
		$author_name = get_the_author();
		$author_desc = get_the_author_meta('description', $post->post_author);
		$google_profile = get_the_author_meta( 'google_profile' );
?>
		<div class="media author-info">
			<div class="img thumbnail author-thumb">
				<a class="thumbnail" href="<?php echo $link; ?>" target="_blank">
					<?php echo get_avatar( $author_email, '120', $default_avatar); ?>
				</a>
			</div>
			<div class="bd">
				<small class="author-note"><?php _e('Author', 'pagelines');?></small>
				<h2>
					<?php echo $author_name ?>
				</h2>
				<p><?php echo $author_desc; ?></p>
				<p class="author-details">
					<?php

					if( $link != '' )
						printf( '<a href="%s" class="btn" target="_blank"><i class="icon-external-link"></i> %s</a> ', $link, __( 'Visit Authors Website &rarr;', 'pagelines') );

					if ( $google_profile )
						printf( '<a href="%s" class="btn" rel="me"><i class="icon-google-plus"></i> %s</a>',  $google_profile, __( 'Authors Google Profile &rarr;', 'pagelines' ) );

					?>
				</p>
			</div>

		</div>
		<div class="clear"></div>
<?php	}
}