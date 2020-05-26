<?php
/*
	Section: Primary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The main widgetized sidebar.
	Class Name: PrimarySidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap
	Persistant: true
	Filter: widgetized
	Loading: active
*/

/**
 * Primary Sidebar Section
 *
 * @package PageLines DMS
 * @author PageLines
*/
class PrimarySidebar extends PageLinesSection {

	function section_persistent(){
		
		register_sidebar( array(
		    'id'          => $this->id,
		    'name'        => $this->name,
		    'description' => $this->description
		) );
		
	}


	function section_template() {
		pagelines_draw_sidebar( $this->id, $this->name, $this->default_template() );
	}

	function default_template(){
		ob_start();
		?>
		<li id="dcategories" class="widget_categories widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Categories','pagelines'); ?></h3>
				<ul>
					<?php wp_list_categories('sort_column=name&optioncount=1&hierarchical=0&title_li='); ?>
				</ul>
			</div>
		</li>

		<li id="darchive" class="widget_archive widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Archives','pagelines'); ?></h3>
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</div>
		</li>

		<li id="dlinks" class="widget_links widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Calendar','pagelines'); ?></h3>

					<?php get_calendar( ); ?>

			</div>
		</li>

		<li id="dmeta" class="widget_meta widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Meta','pagelines'); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li class="login"><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
					<li class="rss"><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','pagelines');?></a></li>
				</ul>
			</div>
		</li>
		<?php 
	
		return ob_get_clean();
	}

}