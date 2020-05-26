<?php
/*
	Section: RapidTabs
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Displays your most popular, and latest posts as well as comments and tags in a tabbed format.
	Class Name: PLRapidTabs
	Filter: widgetized
	Edition: pro
*/

class PLRapidTabs extends PageLinesSection {

	function section_persistent(){

	}
	
	function section_styles(){
		pl_enqueue_script( 'jquery-ui-tabs' );
	}
	

	function section_head(){

		?>
		<script>
		!function ($) {
			$(document).on('sectionStart', function( e ) {
				$('.the-rapid-tabs').tabs({
					show: true
				})
			})
		}(window.jQuery);
		</script>
		<?php

	}


   function section_template() {
		
		global $plpg; 
		$pageID = $plpg->id;


		$num_posts = 4; 

		?>
	<div class="widget">
		<div class="widget-pad">
	<div class="the-rapid-tabs">
		<ul class="tabbed-list rapid-nav fix">
			<li><a href="#rapid-popular"><?php _e( 'Popular', 'pagelines' ); ?></a></li>
			<li><a href="#rapid-recent"><?php _e( 'Recent', 'pagelines' ); ?></a></li>
			<li><a href="#rapid-comments"><?php _e( 'Comments', 'pagelines' ); ?></a></li>
			<li><a href="#rapid-tags"><?php _e( 'Tags', 'pagelines' ); ?></a></li>
		</ul>

		<div id="rapid-popular">

				<h3 class="widget-title"><?php _e('Popular Posts','pagelines'); ?></h3>
				<ul class="media-list">
					<?php

					foreach( get_posts( array('numberposts' => $num_posts, 'ignore_sticky_posts' => 1, 'orderby' => 'comment_count', 'exclude' => $pageID) ) as $p ){
						$img = (has_post_thumbnail( $p->ID )) ? sprintf('<div class="img"><a class="the-media" href="%s" style="background-image: url(%s)"></a></div>', get_permalink( $p->ID ), pl_the_thumbnail_url( $p->ID, 'thumbnail')) : '';

						printf(
							'<li class="media fix">%s<div class="bd"><a class="title" href="%s">%s</a><span class="excerpt">%s</span></div></li>', 
							$img,
							get_permalink( $p->ID ), 
							$p->post_title, 
							pl_short_excerpt($p->ID)
						);

					} ?>


				</ul>
	
		</div>
		<div id="rapid-recent">
				<h3 class="widget-title"><?php _e('Recent Posts','pagelines'); ?></h3>
				<ul class="media-list">
					<?php

					foreach( get_posts( array('ignore_sticky_posts' => 1, 'orderby' => 'post_date', 'order' => 'desc', 'numberposts' => $num_posts, 'exclude' => $pageID) ) as $p ){
						$img = (has_post_thumbnail( $p->ID )) ? sprintf('<div class="img"><a class="the-media" href="%s" style="background-image: url(%s)"></a></div>', get_permalink( $p->ID ), pl_the_thumbnail_url( $p->ID, 'thumbnail')) : '';

						printf(
							'<li class="media fix">%s<div class="bd"><a class="title" href="%s">%s</a><span class="excerpt">%s</span></div></li>', 
							$img,
							get_permalink( $p->ID ), 
							$p->post_title, 
							pl_short_excerpt($p->ID)
						);

					} ?>


				</ul>
		</div>

		<div id="rapid-comments">
			<h3 class="widget-title"><?php _e('Comments','pagelines'); ?></h3>
			<ul class="media-list">
				<?php
				$comments = get_comments( array( 'number' => $num_posts, 'status' => 'approve' ) );
				if ( $comments ) {
					foreach ( (array) $comments as $comment) {
						
						if( 'comment' != get_comment_type( $comment ) )
							continue;

						$post = get_post( $comment->comment_post_ID );
						$link = get_comment_link( $comment->comment_ID ); 
						
						$avatar = $this->get_avatar_url( get_avatar( $comment ) ); 
						$img = ($avatar) ? sprintf('<div class="img"><a class="the-media" href="%s" style="background-image: url(%s)"></a></div>', $link, $avatar) : '';
						
						printf(
							'<li class="media fix">%s<div class="bd"><a class="title" href="%s">%s</a><span class="excerpt">%s</span></div></li>', 
							$img,
							$link, 
							wp_filter_nohtml_kses($comment->comment_author), 
							stripslashes( substr( wp_filter_nohtml_kses( $comment->comment_content ), 0, 50 ) )
						);
					}
					
				}
			 ?>


			</ul>
			
		</div>
		<div id="rapid-tags">
				<h3 class="widget-title"><?php _e('Tags','pagelines'); ?></h3>
				<div class="tags-list">
					<?php

					wp_tag_cloud( array('number'=> 30, 'smallest' => 10, 'largest' => 10) );
					 ?>


				</div>
		
		</div>
				
		</div>
	</div>
</div>
		<?php
		
		


	}


	function get_avatar_url( $avatar ){
	
	    preg_match("/src='(.*?)'/i", $avatar, $matches);
	
	    return (isset($matches) && isset($matches[1])) ? $matches[1] : '';
	
	}
	
}


