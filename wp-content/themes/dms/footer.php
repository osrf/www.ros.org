<?php
/**
 * FOOTER
 *
 * This file controls the ending HTML </body></html> and common graphical
 * elements in your site footer. You can control what shows up where using
 * WordPress and PageLines PHP conditionals
 *
 * @package     PageLines Framework
 * @since       1.0
 *
 * @link        http://www.pagelines.com/
 * @link        http://www.pagelines.com/DMS
 *
 * @author      PageLines   http://www.pagelines.com/
 * @copyright   Copyright (c) 2008-2013, PageLines  hello@pagelines.com
 *
 * @internal    last revised February November 21, 2011
 * @version     ...
 *
 * @todo Define version
 */

if(!has_action('override_pagelines_body_output')):
	
			pagelines_register_hook('pagelines_start_footer'); // Hook ?>
						</div>
						<?php pagelines_register_hook('pagelines_after_main'); // Hook ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
	<?php pagelines_register_hook('pagelines_before_footer'); // Hook ?>
			<footer id="footer" class="footer pl-region" data-region="footer">
				<div class="page-area outline pl-area-container fix">
				<?php pagelines_template_area('pagelines_footer', 'footer'); // Hook ?>
				</div>
			</footer>
		</div>		
	</div>
	<?php pagelines_register_hook('pagelines_after_footer'); // Hook ?>	
</div>
<?php

endif;

	print_pagelines_option('footerscripts'); // Load footer scripts option
	wp_footer(); // Hook (WordPress)
?>
</body>
</html>