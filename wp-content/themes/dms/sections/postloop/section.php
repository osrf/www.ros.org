<?php
/*
	Section: Content/PostLoop
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The Main Content area (Post Loop in WP speak). Includes content and post information.
	Class Name: PageLinesPostLoop
	Workswith: main
	Failswith: 404_page
	Filter: component
*/

/**
 * Main Post Loop Section
 *
 * @package PageLines DMS
 * @author PageLines
 */
class PageLinesPostLoop extends PageLinesSection {

	function section_opts(){

		$opts = array(

			array(
				'key'		=> 'post_content',
				'type'		=> 'edit_post',
				'title'		=> __( 'Edit Post Content', 'pagelines' ),
				'label'		=>	__( '<i class="icon-edit"></i> Edit Post Info', 'pagelines' ),
				'help'		=> __( 'This section uses WordPress posts. Edit post information using WordPress admin.', 'pagelines' ),
				'classes'	=> 'btn-primary'
			),
			array(
				'key'		=> 'pagetitles',
				'type'		=> 'check',
				'case'		=> 'page',
				'title'		=> __( 'Page Title', 'pagelines' ),
				'label'		=> 'Show page title?',
			),

			array(
				
				'title' 	=> __( 'Layout <span class="spamp">&amp;</span> Config', 'pagelines' ),
				'type'		=> 'multi',
				'col'		=> 2,
				'opts'		=> array(
					array(
						'type'		=> 'select',
						'key'		=> 'blog_layout_mode',
						'default'	=> 'magazine',
						'opts'	=> array(
							'magazine'	=> array('name' => __( "Magazine Layout Mode", 'pagelines' )),
							'blog'		=> array('name' => __( "List Layout Mode", 'pagelines' ))
							),
						'label'		=> __( 'Posts Layout Mode', 'pagelines' ),
						'title'		=> __( 'Posts Layout Mode', 'pagelines' ),
						'ref'		=> __( 'Choose between two magazine or blog layout mode. <br/><br/> <strong>Magazine Layout Mode</strong><br/> Magazine layout mode makes use of post <strong>clips</strong>. These are summarized excerpts shown at half the width of the main content column.<br/>  <strong>Note:</strong> There is an option for showing <strong>full-width</strong> posts on your main <strong>posts</strong> page.<br/><br/><strong>List Layout Mode</strong><br/> This is your classical post list layout. Posts span the entire width of the main content column.', 'pagelines' ),
					),

					array(
							'key'			=> 'full_column_posts',
							'case'			=> 'special',
							'default'		=> 2,
							'type'			=> 'count_select',
							'count_number'	=> get_option('posts_per_page'),
							'label'			=> __( 'Number of Full Width Posts? (Mag. Mode)', 'pagelines' ),
							'title'			=> __( 'Full Width Posts (Magazine Layout Mode Only)', 'pagelines' ),
							'help'			=> __( 'Select the number of posts you would like shown at the full width of the main content column in magazine layout mode (the rest will be half-width post <strong>clips</strong>).', 'pagelines' )
					),
					array(
							'key'		=> 'show_content',
							'case'		=> 'special',
							'type'		=> 'check',
							'label'		=> 'Show full content of posts?',
							'title'		=> __( 'Show Full Post Content', 'pagelines' ),
							'exp'		=> __( 'Optionally show full post content on special post listing pages.', 'pagelines' )
					),

				)
			),


			array(
				'title' 	=> __( 'Meta Config', 'pagelines' ),
				'col'		=> 3,
				'type'		=> 'multi',
				'ref'			=> __( 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li><li><strong>[post_type]</strong> - Type of post</li></ul>', 'pagelines' ),
				'opts'		=> array(
					array(
						'key'			=> 'pl_meta_mode',
						'type'			=> 'select',
						'label'			=> __( 'Select Meta Mode', 'pagelines' ),
						'title'			=> __( 'Meta Mode', 'pagelines' ),
						'opts'			=> array(
							'metabar'	=> array('name' => 'Metabar Mode (default)'),
							'author'	=> array('name' => 'Author Avatar Mode'),
						),
						'help'			=> __( 'Instead of the standard metabar (beneath post title), you can use author meta mode. This mode displays the author avatar and publish date on left.', 'pagelines' ),

					),
					array(
						'key'			=> 'metabar_standard',
						'default'		=> 'By [post_author_posts_link] On [post_date] &middot; [post_comments] [post_edit]',
						'type'			=> 'text',
						'label'			=> __( 'Configure Full Width Post Metabar', 'pagelines' ),
						'title'			=> __( 'Post Meta Information', 'pagelines' ),
					),

					array(
						'case'			=> 'special',
						'key'			=> 'metabar_clip',
						'default'		=> 'On [post_date] By [post_author_posts_link] [post_edit]',
						'type'			=> 'text',
						'label'			=> __( 'Configure Clip Metabar', 'pagelines' ),
						'title'			=> __( 'Configure Clip Metabar', 'pagelines' ),

					),
				)
			),

			array(
				'title' 	=> __( 'Thumbs', 'pagelines' ),
				'type'		=> 'multi',
				'col'		=> 2,
				'opts'		=> array(
					

					array(
						'type'		=> 'select',
						'key'		=> 'excerpt_mode_full',
						'default'	=> 'left',
						'opts'	=> array(
							'left'			=> array( 'name' => __( 'Left Justified', 'pagelines' ), 'offset' => '0px -50px' ),
							'top'			=> array( 'name' => __( 'On Top', 'pagelines' ), 'offset' => '0px 0px', 'version' => 'pro' ),
							'left-excerpt'	=> array( 'name' => __( 'Left, In Excerpt', 'pagelines' ), 'offset' => '0px -100px' ),
							'right-excerpt'	=> array( 'name' => __( 'Right, In Excerpt', 'pagelines' ), 'offset' => '0px -150px', 'version' => 'pro' ),
						),
						'title'		=> __( 'Full Width Thumbs Layout', 'pagelines' ),
						'help'		=> __( 'Use this option to configure how thumbs will be shown in full-width posts on your blog page.', 'pagelines' )

					),
					
					array(
						'case'		=> 'special',
						'type'		=> 'select',
						'key'		=> 'excerpt_mode_clip',
						'default'	=> 'left',
						'opts'	=> array(
							'left'			=> array( 'name' => __( 'Left Justified', 'pagelines' ), 'offset' => '0px -50px' ),
							'top'			=> array( 'name' => __( 'On Top', 'pagelines' ), 'offset' => '0px 0px' ),
							'left-excerpt'	=> array( 'name' => __( 'Left, In Excerpt', 'pagelines' ), 'offset' => '0px -100px' ),
							'right-excerpt'	=> array( 'name' => __( 'Right, In Excerpt', 'pagelines' ), 'offset' => '0px -150px' ),
						),
						'title'		=> __( 'Clip Thumbs Layout', 'pagelines' ),
						'help'		=> __( 'Use this option to configure how thumbs will be shown in clips. These are the smaller <strong>magazine</strong> style excerpts on your blog page.', 'pagelines' )
					),
					array(
							'key'		=> 'hide_thumb',
							'case'		=> 'special',
							'type'		=> 'check',
							'label'		=> __( 'Hide thumbs?', 'pagelines' ),
							'title'		=> __( 'Post Thumbnails', 'pagelines' ),
							'help'		=> __( 'Use this option to hide or show thumbs for posts on pages.', 'pagelines' )
					),
				)
			),


			array(
				'case'		=> 'special',
				'col'		=> 4,
				'title' 	=> __( 'Excerpts', 'pagelines' ),
				'type'		=> 'multi',
				'opts'		=> array(
					array(
							'key'		=> 'hide_excerpt',
							'case'		=> 'special',
							'type'		=> 'check',
							'label'		=> __( 'Hide excerpts?', 'pagelines' ),
							'title'		=> __( 'Hide Post Excerpt', 'pagelines' ),
							'help'		=> __( 'Excerpts are short pieces of content that are shown to give users a preview of a post. Hide them using this option.', 'pagelines' )
					),
					array(
							'case'		=> 'special',
							'key'		=> 'continue_reading_text',
							'default'	=> __( 'Read More &raquo;', 'pagelines' ),
							'type'		=> 'text',
							'label'		=> __( 'Continue Reading Link Text', 'pagelines' ),
							'title'		=> __( 'Excerpts <strong>Continue Reading</strong> Text', 'pagelines' ),
							'help' 		=> __( "This text will be used as the link to your full article when viewing articles on your posts page (when excerpts are turned on).", 'pagelines' )
					),
					array(
							'case'		=> 'special',
							'key'		=> 'excerpt_len',
							'default' 	=> 55,
							'type' 		=> 'text',
							'label'		=> __( 'Number of words.', 'pagelines' ),
							'title' 	=> __( 'Excerpt Length', 'pagelines' ),
							'help' 		=> __( 'Excerpts are set to 55 words by default.', 'pagelines' )
					),
					array(
							'case'		=> 'special',
							'key'		=> 'excerpt_tags',
							'default' 	=> '<a>',
							'type' 		=> 'text',
							'label'		=> __( 'Allowed Tags', 'pagelines' ),
							'title' 	=> __( 'Allow Tags in Excerpt', 'pagelines' ),
							'ref' 		=> __( 'By default WordPress strips all HTML tags from excerpts. You can use this option to allow certain tags. Simply enter the allowed tags in this field. <br/>An example of allowed tags could be: <strong>&lt;p&gt;&lt;br&gt;&lt;a&gt;</strong>. <br/><br/> <strong>Note:</strong> Enter a period <strong>.</strong> to disallow all tags.', 'pagelines' )
					)
				)
			)


		);

		return $opts;
	}

	function before_section_template( $location = '' ) {

		global $wp_query;

		if(isset($wp_query) && is_object($wp_query))
			$this->wrapper_classes[] = ( $wp_query->post_count > 1 ) ? 'multi-post' : 'single-post';

	}

	/**
	* Section template.
	*/
   function section_template() {
	
		
		if(do_special_content_wrap()){
			 global $integration_out;
			echo $integration_out;
			
		} else {	
		
			require_once( $this->base_dir . '/class.posts.php' );
			
			//Included in theme root for easy editing.
			$theposts = new PageLinesPosts( $this );
			$theposts->load_loop();
			
		}
	
	}

}