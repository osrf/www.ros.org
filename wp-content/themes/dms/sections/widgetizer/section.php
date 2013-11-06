<?php
/*
	Section: Widgetizer
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Place this section wherever you like and select a widgetized area (configured in admin) for it to use.
	Class Name: PageLinesWidgetizer
	Filter: widgetized
	Loading: active
*/

/**
 * Post Author Section
 *
 * @package PageLines DMS
 * @author PageLines
 */
class PageLinesWidgetizer extends PageLinesSection {


	function section_opts(){



		$opts = array(
			array(
				'key'	=> 'widgetizer_area',
				'type'	=> 'select',
				'opts'	=> get_sidebar_select(),
				'title'	=> 'Select Widgetized Area',
				'label'		=>	'Select widgetized area',
				'help'		=> "Select the widgetized area you would like to use with this instance of Widgetizer.",
				
			),
			array(
				'key'	=> 'widgetizer_help',
				'type'	=> 'link',
				'url'	=> admin_url( 'widgets.php' ),
				'title'	=> 'Widgetized Areas Help',
				'label'		=>	'<i class="icon-retweet"></i> Edit Widgetized Areas',
				'help'		=> "This section uses widgetized areas that are created and edited in inside your admin.",
				'col'		=> 2
			)
		);

		if(!class_exists('CustomSidebars')){
			$opts[] = array(
				'key'	=> 'widgetizer_custom_sidebars',
				'type'	=> 'link',
				'url'	=> 'http://wordpress.org/extend/plugins/custom-sidebars/',
				'title'	=> 'Get Custom Sidebars',
				'label'		=>	'<i class="icon-external-link"></i> Check out plugin',
				'help'		=> "We have detected that you don't have the Custom Sidebars plugin installed. We recommend you install this plugin to create custom widgetized areas on demand.",
				'col'		=> 2
			);
		}

		return $opts;
	}


	/**
	* Section template.
	*/
   function section_template() {
	$area = $this->opt('widgetizer_area');

	if($area)
		pagelines_draw_sidebar( $area );
	else
		echo setup_section_notify( $this );

	}

}