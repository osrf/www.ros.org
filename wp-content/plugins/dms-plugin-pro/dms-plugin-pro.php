<?php
/*

Plugin Name: DMS Professional Tools
Plugin URI: http://www.pagelines.com/
Description: Pro member code and utilities for PageLines DMS.
Version: 1.0.0
Author: PageLines
PageLines: true

*/

class DMSPluginPro {
	
	private $plugin_path;
    private $plugin_url;
    private $l10n;
    private $plpro;
	private $settings;

	function __construct() {
		
		global $dmspro_plugin_url;
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );
		$dmspro_plugin_url = $this->plugin_url;
		
		$this->l10n = 'wp-settings-framework';
		$this->load_libs();
		
		add_action( 'admin_init', array( $this, 'fix_wporg' ), 9 );
		
        add_action( 'admin_menu', array( $this, 'admin_menu'), 99 );
		add_action( 'template_redirect', array( $this, 'section_cache' ) );
		add_action( 'template_redirect', array( $this, 'browsercss' ) );
		add_action( 'init', array( $this, 'memcheck' ) );
		
//		add_action( 'wp', array( $this, 'load_overrides_early' ), 6 );
		add_action( 'init', array( $this, 'load_overrides' ) );
		add_filter( 'pagelines_global_notification', array( $this, 'pro_nag' ) );
		
		$this->plpro = new WordPressSettingsFramework( $this->plugin_path .'settings/settings-general.php' );
		$this->settings = wpsf_get_settings( $this->plugin_path .'settings/settings-general.php' );
		add_filter( $this->plpro->get_option_group() .'_settings_validate', array( $this, 'validate_settings' ) );

		// has to be mega early...
		if( '1' === $this->settings['settingsgeneral_cdn_cdn-enabled'] ) {			
			define( 'WP_STACK_CDN_DOMAIN', $this->settings['settingsgeneral_cdn_cdn-url'] );
			define( 'WP_STAGE', 'production' );
			new WP_Stack_CDN_Plugin;
		}
		new DMS_Hacks;		
	}

	function pro_nag( $note ) {

		if( pl_is_pro() )
			return $note;

		ob_start(); ?>
		
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert" href="#">&times;</button>
		  	<strong><i class="icon-star"></i> Upgrade to Pro!</strong> ( <i class="icon-star-half-empty"></i> You're currently using DMS basic. ) <br/>Activate this site with Pro for additional sections, effects, capabilities and support.
			<a href="http://www.pagelines.com/DMS" class="btn btn-mini" target="_blank"><i class="icon-thumbs-up"></i> Learn More</a>
			&mdash; <em>Already a Pro?</em> <a href="#" class="btn btn-mini" data-tab-link="account" data-stab-link="pl_account"><i class="icon-star"></i> Activate Site</a> 
		</div>
		
		<?php 
		
		$note .= ob_get_clean();
		return $note;
	}

	function fix_wporg( ) {
		global $wpsf_settings;
		if( defined( 'PL_WPORG' ) ) {
			foreach( $wpsf_settings as $key => $setting ) {
				if( 'cdn' == $setting['section_id'] )
					unset( $wpsf_settings[$key] );
				if( 'section_cache' == $setting['section_id'] )
					unset( $wpsf_settings[$key] );
			}
		}
	}

	function section_cache() {
		if( '1' === wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'section_cache', 'cache-enabled' ) ) {
			new Sections_Cache;
		}
	}
	
	function browsercss() {
		if( '1' === wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'browsercss', 'enabled' ) ) {
			new Browser_Pro_Specific_CSS;
		}
	}

	function memcheck() {
		if( '1' === wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'memtest', 'enabled' ) ) {
			new PL_Memcheck;
		}
	}

	function load_overrides() {
		
		if( ! defined( 'PL_WPORG' ) )
			return;
		require_once( $this->plugin_path . 'libs/class.updates.php' );
		new PL_WPORG_Updates;
		add_filter( 'pl_wporg_hiddentext', array( $this, 'hiddentext' ) );
		add_filter( 'pl_wporg_hiddenclass', array( $this, 'hiddenclass' ) );
		
		// updates for core... converts wporg version to full version...
		require_once( $this->plugin_path . 'libs/class.updates-core.php' );
		if ( is_admin() )
			new PL_WPORG_Updates_Core( PL_CORE_VERSION );
	}

	// function load_overrides_early() {
	// 	if( ! defined( 'PL_WPORG' ) )
	// 		return;
	// 	
	// 	require_once( $this->plugin_path . 'libs/class.account.php' );		
	// 	new PL_WPORG_PLAccountPanel;
	// }

	function hiddentext($text) {
		
		return '(Pro Edition Only)';
	}

	function hiddenclass($text) {
		return 'pro-only-disabled';
	}

	function load_libs(){

		require_once( $this->plugin_path . 'libs/class.hacks.php' );
		require_once( $this->plugin_path . 'libs/class.cdn.libs.php' );
		require_once( $this->plugin_path . 'libs/wp-settings-framework.php' );
		require_once( $this->plugin_path . 'libs/class.section.cache.php' );
		require_once( $this->plugin_path . 'libs/class.memtest.php' );
		require_once( $this->plugin_path . 'libs/class.browsercss.php' );
	}

    function admin_menu() {
	
		// if( defined( 'PL_WPORG' ) )
		// 	return false;
        $page_hook = add_menu_page( __( 'PageLines PRO', $this->l10n ), __( 'PageLines PRO', $this->l10n ), 'update_core', 'plpro', array(&$this, 'settings_page') );
        add_submenu_page( 'plpro', __( 'Settings', $this->l10n ), __( 'Settings', $this->l10n ), 'update_core', 'plpro', array(&$this, 'settings_page') );
    }
    function settings_page()
	{
	    // Your settings page
	    ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>PageLines PRO Plugin Settings</h2>
			<?php 
			// Output your settings form
			$this->plpro->settings();
			do_action( 'dmspro_extra_settings' );
		echo '</div>';
	}

	function validate_settings( $input )
	{
	    // Do your settings validation here
	    // Same as $sanitize_callback from http://codex.wordpress.org/Function_Reference/register_setting
    	return $input;
	}

}

new DMSPluginPro;