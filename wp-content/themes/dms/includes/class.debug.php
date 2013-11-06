<?php
/**
 *
 * PageLines Debugging system
 *
 * Enabled in Framework admin 'advanced' tab
 * Show server debug info using special URL
 *
 * @package PageLines DMS
 * @subpackage Debugging
 * @since 2.1
 *
 */


/**
 * PageLines Debugging Information Class
 *
 * @package PageLines DMS
 * @subpackage Debugging
 * @since 2.1
 *
 */
class PageLinesDebug {

	// Array of debugging information
	var $debug_info = array();


	/**
	*
	* @TODO document
	*
	*/
	function __construct( ) {

		$this->wp_debug_info();
		$this->debug_info_template();
	}

	/**
	 * Main output.
	 * @return str Formatted results for page.
	 *
	 */
	function debug_info_template(){

		$out = '';
		foreach($this->debug_info as $element ) {

			$class = '';
			if ( $element['value'] ) {
				if( isset( $element['style'] ) )
					$style = sprintf( ' style="%s"', $element['style'] );
				$out .= sprintf( '<p><strong><span%s>%s</span></strong><br />%s', $style, ucfirst($element['title']), ucfirst($element['value']) );
				
				$out .= (isset($element['extra'])) ? "<br /><kbd>{$element['extra']}</kbd>" : '';
				$out .= '</p>';
			}
		}
		wp_die( sprintf( '<h2>DMS Debug Info</h2>%s', $out ), 'PageLines Debug Info', array( 'response' => 200, 'back_link' => true) );
	}

	/**
	 * Debug tests.
	 * @return array Test results.
	 */
	function wp_debug_info(){

		global $wpdb, $wp_version, $platform_build;

			// Set data & variables first
			$uploads = wp_upload_dir();
			// Get user role
			$current_user = wp_get_current_user();
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);

			// Format data for processing by a template

			$this->debug_info[] = array(
				'title'	=> 'WordPress Version',
				'value' => $wp_version,
			);

			$this->debug_info[] = array(
				'title'	=> 'Multisite Enabled',
				'value' => ( is_multisite() ) ? 'Yes' : 'No',
			);

			$this->debug_info[] = array(
				'title'	=> 'Current Role',
				'value' => $user_role,
			);

			$this->debug_info[] = array(
				'title'	=> 'Framework Path',
				'value' => '<kbd>' . get_template_directory() . '</kbd>',
			);

			$this->debug_info[] = array(
				'title'	=> 'Framework URI',
				'value' => '<kbd>' . get_template_directory_uri() . '</kbd>',
			);

			$this->debug_info[] = array(
				'title'	=> 'Framework Version',
				'value' => PL_CORE_VERSION,
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Version',
				'value' => floatval( phpversion() ),
			);

			$this->debug_info[] = array(
				'title'	=> 'Child theme',
				'value' => ( get_template_directory() != get_stylesheet_directory() ) ? 'Yes' : '',
				'extra' => get_stylesheet_directory() . '<br />' . get_stylesheet_directory_uri()
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Safe Mode',
				'value' => ( (bool) ini_get('safe_mode') ) ? 'Yes! Deprecated as of PHP 5.3 and removed in PHP 5.4':'',
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Open basedir restriction',
				'value' => ( (bool) ini_get('open_basedir') ) ? 'Yes!':'',
				'extra'	=> ini_get('open_basedir')
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Register Globals',
				'value' => ( (bool) ini_get('register_globals') ) ? 'Yes! Deprecated as of PHP 5.3 and removed in PHP 5.4':'',
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Magic Quotes gpc',
				'value' => ( (bool) ini_get('magic_quotes_gpc') ) ? 'Yes! Deprecated as of PHP 5.3 and removed in PHP 5.4':'',
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP memory',
				'value' => intval(ini_get('memory_limit') ),
			);

			$this->debug_info[] = array(
				'title'	=> 'Mysql version',
				'value' => ( version_compare( $wpdb->get_var("SELECT VERSION() AS version"), '6' ) < 0  ) ? $wpdb->get_var("SELECT VERSION() AS version"):'',
			);


			$this->debug_info[] = array(
				'title'	=> 'PHP type',
				'value' => php_sapi_name(),
			);

			$processUser = ( ! function_exists( 'posix_geteuid') || ! function_exists( 'posix_getpwuid' ) ) ? 'posix functions are disabled on this host!' : posix_getpwuid(posix_geteuid());
			if ( is_array( $processUser ) )
				$processUser = $processUser['name'];

			$this->debug_info[] = array(
				'title'	=> 'PHP User',
				'value' => $processUser,
			);

			$this->debug_info[] = array(
				'title'	=> 'OS',
				'value' => PHP_OS,
			);

			if ( pl_is_pro() ) {
				$status = get_option( 'dms_activation' );
				$this->debug_info[] = array(
					'title'	=> 'Licence OK',
					'value' => $status['email'],
					'extra'	=> '',
				);
			}

			$this->debug_info[] = array(
				'title'	=> 'Installed Plugins',
				'value' => $this->debug_get_plugins(),
				'level'	=> false
			);
			if( get_theme_mod( 'less_last_error' ) ) {
			$this->debug_info[] = array(
				'title'	=> 'DMS Internal Warning',
				'value' => 'Less Subsystem',
				'extra'	=> get_theme_mod( 'less_last_error' ),
				'style'	=> 'color:red;'
			);
		}
	}
	/**
	 * Get active plugins.
	 * @return str List of plugins.
	 *
	 */
	function debug_get_plugins() {
		$plugins = get_option('active_plugins');
		if ( $plugins ) {
			$plugins_list = '';
			foreach($plugins as $plugin_file) {
					$plugins_list .= '<kbd>' . $plugin_file . '</kbd>';
					$plugins_list .= '<br />';
			}
			return ( isset( $plugins_list ) ) ? "{$plugins_list}" : '';
		}
	}
//-------- END OF CLASS --------//
}

if ( ! is_admin() ) {
	if( isset( $_GET['pldebug'] ) )
		new PageLinesDebug;
}