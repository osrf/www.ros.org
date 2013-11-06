<?php
/*
	Section: Post/Page Pagination
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Pagination - A numerical post/page navigation. (Supports WP-PageNavi)
	Class Name: PageLinesPagination
	Workswith: main
	Filter: nav
	Isolate: posts_pages
	Loading: active
*/

/**
 * Post/Page navigation Section
 *
 * @package PageLines DMS
 * @author PageLines
 */
class PageLinesPagination extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() {
		pagelines_pagination();
	}
}