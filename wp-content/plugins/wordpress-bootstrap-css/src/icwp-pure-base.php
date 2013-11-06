<?php

if ( !defined('ICWP_DS') ) {
	define( 'ICWP_DS', DIRECTORY_SEPARATOR );
}

if ( !function_exists( '_hlt_e' ) ) {
	function _hlt_e( $insStr ) {
		_e( $insStr, 'hlt-wordpress-bootstrap-css' );
	}
}
if ( !function_exists( '_hlt__' ) ) {
	function _hlt__( $insStr ) {
		return __( $insStr, 'hlt-wordpress-bootstrap-css' );
	}
}

require_once( dirname(__FILE__).'/icwp-wpfunctions.php' );

if ( !class_exists('ICWP_Pure_Base_WPTB') ):

class ICWP_Pure_Base_WPTB {

	const BaseTitle			= 'iControlWP Plugins';
	const BasePermissions	= 'manage_options';
	const BaseSlug			= 'icwp';
	
	const ViewExt			= '.php';
	const ViewDir			= 'views';
	const VariablePrefix	= 'worpit';

	/**
	 * @var string
	 */
	protected $m_sVersion;

	/**
	 * @var string
	 */
	protected $m_sPluginHumanName;
	/**
	 * @var string
	 */
	protected $m_sPluginMenuTitle;

	/**
	 * @var string
	 */
	protected $m_sPluginRootFile;
	/**
	 * @var string
	 */
	protected $m_sPluginName;
	/**
	 * @var string
	 */
	protected $m_sPluginPath;
	/**
	 * @var string
	 */
	protected $m_sPluginFile;
	/**
	 * @var string
	 */
	protected $m_sPluginUrl;
	/**
	 * @var string
	 */
	protected $m_sOptionPrefix;

	protected $m_aPluginMenu;

	protected $m_aAllPluginOptions;
	
	protected $m_sParentMenuIdSuffix;
	
	protected $m_fShowMarketing = '';
	
	protected $m_fAutoPluginUpgrade = false;
	
	/**
	 * @var ICWP_WpFunctions_WPTB;
	 */
	protected $m_oWpFunctions;

	public function __construct() {
	
		add_action( 'plugins_loaded',			array( $this, 'onWpPluginsLoaded' ) );
		add_action( 'init',						array( $this, 'onWpInit' ), 0 );
		if ( is_admin() ) {
			add_action( 'admin_init',			array( $this, 'onWpAdminInit' ) );
			add_action( 'admin_notices',		array( $this, 'onWpAdminNotices' ) );
			add_action( 'admin_menu',			array( $this, 'onWpAdminMenu' ) );
			add_action( 'plugin_action_links',	array( $this, 'onWpPluginActionLinks' ), 10, 4 );
		}
		add_action( 'shutdown',					array( $this, 'onWpShutdown' ) );
		
		$this->setPaths();
		$this->registerActivationHooks();
	}
	
	/**
	 * Registers the plugins activation, deactivate and uninstall hooks.
	 */
	protected function registerActivationHooks() {
		register_activation_hook( $this->m_sPluginRootFile, array( $this, 'onWpActivatePlugin' ) );
		register_deactivation_hook( $this->m_sPluginRootFile, array( $this, 'onWpDeactivatePlugin' ) );
	//	register_uninstall_hook( $this->m_sPluginRootFile, array( $this, 'onWpUninstallPlugin' ) );
	}
	
	/**
	 * @since v3.0.0
	 */
	protected function setPaths() {
		
		if ( empty( $this->m_sPluginRootFile ) ) {
			$this->m_sPluginRootFile = __FILE__;
		}
		$this->m_sPluginName	= basename( $this->m_sPluginRootFile );
		$this->m_sPluginPath	= plugin_basename( dirname( $this->m_sPluginRootFile ) );
		$this->m_sPluginFile	= plugin_basename( $this->m_sPluginRootFile );
		$this->m_sPluginDir		= WP_PLUGIN_DIR.ICWP_DS.$this->m_sPluginPath.ICWP_DS;
		$this->m_sPluginUrl		= plugins_url( '/', $this->m_sPluginRootFile ) ; //this seems to use SSL more reliably than WP_PLUGIN_URL
	}
	
	public function doPluginUpdateCheck() {
		$this->loadWpFunctions();
		$this->m_oWpFunctions->getIsPluginUpdateAvailable( $this->m_sPluginPath );
	}

	protected function getFullParentMenuId() {
		return self::BaseSlug .'-'. $this->m_sParentMenuIdSuffix;
	}

	protected function display( $insView, $inaData = array() ) {
		$sFile = $this->m_sPluginDir.self::ViewDir.ICWP_DS.$insView.self::ViewExt;

		if ( !is_file( $sFile ) ) {
			echo "View not found: ".$sFile;
			return false;
		}

		if ( count( $inaData ) > 0 ) {
			extract( $inaData, EXTR_PREFIX_ALL, self::VariablePrefix );
		}

		ob_start();
		include( $sFile );
		$sContents = ob_get_contents();
		ob_end_clean();

		echo $sContents;
		return true;
	}

	protected function getImageUrl( $insImage ) {
		return $this->m_sPluginUrl.'resources/images/'.$insImage;
	}
	protected function getCssUrl( $insCss ) {
		return $this->m_sPluginUrl.'resources/css/'.$insCss;
	}
	protected function getJsUrl( $insJs ) {
		return $this->m_sPluginUrl.'resources/js/'.$insJs;
	}

	protected function getSubmenuPageTitle( $insTitle ) {
		return self::BaseTitle.' - '.$insTitle;
	}
	protected function getSubmenuId( $insId ) {
		return $this->getFullParentMenuId().'-'.$insId;
	}

	public function onWpPluginsLoaded() {

		if ( is_admin() ) {
			//Handle plugin upgrades
			$this->handlePluginUpgrade();
			$this->doPluginUpdateCheck();
		}

		if ( $this->isIcwpPluginFormSubmit() ) {
			$this->handlePluginFormSubmit();
		}
	}

	public function onWpInit() { }

	public function onWpAdminInit() {
		//Do Plugin-Specific Admin Work
		if ( $this->isIcwpPluginAdminPage() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueBootstrapAdminCss' ), 99 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueuePluginAdminCss' ), 99 );
		}
	}
	
	public function onWpAdminMenu() {

		$sFullParentMenuId = $this->getFullParentMenuId();

		add_menu_page( self::BaseTitle, $this->m_sPluginMenuTitle, self::BasePermissions, $sFullParentMenuId, array( $this, 'onDisplayMainMenu' ), $this->getImageUrl( 'icontrolwp_16x16.png' ) );

		//Create and Add the submenu items
		$this->createPluginSubMenuItems();
		if ( !empty($this->m_aPluginMenu) ) {
			foreach ( $this->m_aPluginMenu as $sMenuTitle => $aMenu ) {
				list( $sMenuItemText, $sMenuItemId, $sMenuCallBack ) = $aMenu;
				add_submenu_page( $sFullParentMenuId, $sMenuTitle, $sMenuItemText, self::BasePermissions, $sMenuItemId, array( &$this, $sMenuCallBack ) );
			}
		}
		$this->fixSubmenu();
	}

	protected function createPluginSubMenuItems(){
		/* Override to create array of sub-menu items
		 $this->m_aPluginMenu = array(
		 		//Menu Page Title => Menu Item name, page ID (slug), callback function onLoad.
		 		$this->getSubmenuPageTitle( 'Content by Country' ) => array( 'Content by Country', $this->getSubmenuId('main'), 'onDisplayCbcMain' ),
		 );
		*/
	}

	protected function fixSubmenu() {
		global $submenu;
		$sFullParentMenuId = $this->getFullParentMenuId();
		if ( isset( $submenu[$sFullParentMenuId] ) ) {
			$submenu[$sFullParentMenuId][0][0] = 'Dashboard';
		}
	}

	/**
	 * The callback function for the main admin menu index page
	 */
	public function onDisplayMainMenu() {
		$aData = array(
			'plugin_url'	=> $this->m_sPluginUrl,
			'fShowAds'		=> $this->isShowMarketing()
		);
		$this->display( self::BaseSlug.'_'.$this->m_sParentMenuIdSuffix.'_index', $aData );
	}
	
	protected function isShowMarketing() {

		if ( $this->m_fShowMarketing == 'Y' ) {
			return true;
		}
		elseif ( $this->m_fShowMarketing == 'N' ) {
			return false;
		}
		
		$sServiceClassName = 'Worpit_Plugin';
		$this->m_fShowMarketing = 'Y';
		if ( class_exists( 'Worpit_Plugin' ) ) {
			if ( method_exists( 'Worpit_Plugin', 'IsLinked' ) ) {
				$this->m_fShowMarketing = Worpit_Plugin::IsLinked() ? 'N' : 'Y';
			}
			elseif ( function_exists( 'get_option' )
					&& get_option( Worpit_Plugin::$VariablePrefix.'assigned' ) == 'Y'
					&& get_option( Worpit_Plugin::$VariablePrefix.'assigned_to' ) != '' ) {
		
				$this->m_fShowMarketing = 'N';
			}
		}
		return $this->m_fShowMarketing === 'N' ? false : true;
	}

	/**
	 * The Action Links in the main plugins page.
	 * 
	 * @param $inaLinks
	 * @param $insFile
	 */
	public function onWpPluginActionLinks( $inaLinks, $insFile ) { }

	/**
	 * Override this method to handle all the admin notices
	 */
	public function onWpAdminNotices() {
		// Do we have admin priviledges?
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
		$this->adminNoticePluginUpgradeAvailable();
	}
	
	/**
	 * Hooked to 'shutdown'
	 */
	public function onWpShutdown() { }

	/**
	 * This is called from within onWpAdminInit. Use this solely to manage upgrades of the plugin
	 */
	protected function handlePluginUpgrade() {

		if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if ( $this->m_fAutoPluginUpgrade ) {
			$this->loadWpFunctions();
			$this->m_oWpFunctions->doPluginUpgrade( $this->m_sPluginFile );
		}
	}

	protected function handlePluginFormSubmit() { }
	
	protected function adminNoticePluginUpgradeAvailable() {

		// Don't show on the update page.
		if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'update.php' ) {
			return;
		}
		// We need to have the correct plugin file set before proceeding.
		if ( !isset( $this->m_sPluginFile ) ) {
			return;
		}

		$this->loadWpFunctions();
		$oUpdate = $this->m_oWpFunctions->getIsPluginUpdateAvailable( $this->m_sPluginFile );
		if ( !$oUpdate ) {
			return;
		}
		$sNotice = $this->getAdminNoticePluginUpgradeAvailable();
		$this->getAdminNotice( $sNotice, 'updated', true );
	}

	public function enqueueBootstrapAdminCss() {
		wp_register_style( 'worpit_bootstrap_wpadmin_css', $this->getCssUrl( 'bootstrap-wpadmin.css' ), false, $this->m_sVersion );
		wp_enqueue_style( 'worpit_bootstrap_wpadmin_css' );
		wp_register_style( 'worpit_bootstrap_wpadmin_css_fixes', $this->getCssUrl('bootstrap-wpadmin-fixes.css'),  array('worpit_bootstrap_wpadmin_css'), $this->m_sVersion );
		wp_enqueue_style( 'worpit_bootstrap_wpadmin_css_fixes' );
	}

	public function enqueuePluginAdminCss() {
		$iRand = rand();
		wp_register_style( 'worpit_plugin_css'.$iRand, $this->getCssUrl('worpit-plugin.css'), array('worpit_bootstrap_wpadmin_css_fixes'), $this->m_sVersion );
		wp_enqueue_style( 'worpit_plugin_css'.$iRand );
	}
	
	/**
	 * Provides the basic HTML template for printing a WordPress Admin Notices
	 *
	 * @param $insNotice - The message to be displayed.
	 * @param $insMessageClass - either error or updated
	 * @param $infPrint - if true, will echo. false will return the string
	 * @return boolean|string
	 */
	protected function getAdminNotice( $insNotice = '', $insMessageClass = 'updated', $infPrint = false ) {

		$sFullNotice = '
			<div id="message" class="'.$insMessageClass.'">
				<style>
					#message form { margin: 0px; }
				</style>
				'.$insNotice.'
			</div>
		';

		if ( $infPrint ) {
			echo $sFullNotice;
			return true;
		} else {
			return $sFullNotice;
		}
	}//getAdminNotice

	protected function redirect( $insUrl, $innTimeout = 1 ) {
		echo '
			<script type="text/javascript">
				function redirect() {
					window.location = "'.$insUrl.'";
				}
				var oTimer = setTimeout( "redirect()", "'.($innTimeout * 1000).'" );
			</script>';
	}

	/**
	 * A little helper function that populates all the plugin options arrays with DB values
	 */
	protected function readyAllPluginOptions() {
		$this->initPluginOptions();
		$this->populateAllPluginOptions();
	}

	/**
	 * Override to create the plugin options array.
	 * 
	 * Returns false if nothing happens - i.e. not over-rided.
	 */
	protected function initPluginOptions() {
		return false;
	}

	/**
	 * $sAllOptionsInput is a comma separated list of all the input keys to be processed from the $_POST
	 */
	protected function updatePluginOptionsFromSubmit( $sAllOptionsInput ) {

		if ( empty($sAllOptionsInput) ) {
			return;
		}

		$aAllInputOptions = explode( ',', $sAllOptionsInput);
		foreach ( $aAllInputOptions as $sInputKey ) {
			$aInput = explode( ':', $sInputKey );
			list( $sOptionType, $sOptionKey ) = $aInput;
			
			$sOptionValue = $this->getAnswerFromPost( $sOptionKey );
			if ( is_null($sOptionValue) ) {
				
				if ( $sOptionType == 'text' ) { //if it was a text box, and it's null, don't update anything
					continue;
				} else if ( $sOptionType == 'checkbox' ) { //if it was a checkbox, and it's null, it means 'N'
					$sOptionValue = 'N';
				}
				
			}
			$this->updateOption( $sOptionKey, $sOptionValue );
		}
		
		return true;
	}//updatePluginOptionsFromSubmit
	
	protected function collateAllFormInputsForAllOptions($aAllOptions, $sInputSeparator = ',') {

		if ( empty($aAllOptions) ) {
			return '';
		}
		$iCount = 0;
		foreach ( $aAllOptions as $aOptionsSection ) {
			
			if ( $iCount == 0 ) {
				$sCollated = $this->collateAllFormInputsForOptionsSection($aOptionsSection, $sInputSeparator);
			} else {
				$sCollated .= $sInputSeparator.$this->collateAllFormInputsForOptionsSection($aOptionsSection, $sInputSeparator);
			}
			$iCount++;
		}
		return $sCollated;
		
	}//collateAllFormInputsAllOptions

	/**
	 * Returns a comma seperated list of all the options in a given options section.
	 *
	 * @param array $aOptionsSection
	 */
	protected function collateAllFormInputsForOptionsSection( $aOptionsSection, $sInputSeparator = ',' ) {

		if ( empty($aOptionsSection) ) {
			return '';
		}
		$iCount = 0;
		foreach ( $aOptionsSection['section_options'] as $aOption ) {

			list($sKey, $fill1, $fill2, $sType) =  $aOption;
			
			if ( is_array( $sType ) ) { //prevents a PHP warning.
				$sType = 'Array';
			}

			if ( $iCount == 0 ) {
				$sCollated = $sType.':'.$sKey;
			} else {
				$sCollated .= $sInputSeparator.$sType.':'.$sKey;
			}
			$iCount++;
		}
		return $sCollated;
	}//collateAllFormInputsForOptionsSection

	protected function isIcwpPluginAdminPage() {
		$sSubPageNow = isset( $_GET['page'] )? $_GET['page']: '';
		if ( is_admin() && !empty($sSubPageNow) && (strpos( $sSubPageNow, $this->getFullParentMenuId() ) === 0 )) { //admin area, and the 'page' begins with 'worpit'
			return true;
		}
		return false;
	}

	protected function isIcwpPluginFormSubmit() {
		return isset( $_POST['icwp_plugin_form_submit'] );
	}
	
	protected function deleteAllPluginDbOptions() {

		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if ( empty($this->m_aAllPluginOptions) && !$this->initPluginOptions() ) {
			return;
		}
		
		foreach ( $this->m_aAllPluginOptions as &$aOptionsSection ) {
			foreach ( $aOptionsSection['section_options'] as &$aOptionParams ) {
				if ( isset( $aOptionParams[0] ) ) {
					$this->deleteOption($aOptionParams[0]);
				}
			}
		}
		
	}//deleteAllPluginDbOptions

	protected function getAnswerFromPost( $insKey, $insPrefix = null ) {
		if ( is_null( $insPrefix ) ) {
			$insKey = $this->m_sOptionPrefix.$insKey;
		}
		return ( isset( $_POST[$insKey] )? $_POST[$insKey]: null );
	}

	/**
	 * Gets the WordPress option based on this object's option prefix.
	 * @param string $insKey
	 * @return mixed
	 */
	public function getOption( $insKey ) {
		return get_option( $this->m_sOptionPrefix.$insKey );
	}

	/**
	 * @param string $insKey
	 * @param mixed $insValue
	 * @return boolean
	 */
	public function addOption( $insKey, $inmValue ) {
		return add_option( $this->m_sOptionPrefix.$insKey, $inmValue );
	}

	/**
	 * @param string $insKey
	 * @param mixed $inmValue
	 * @return boolean
	 */
	public function updateOption( $insKey, $inmValue ) {
		if ( !is_object( $inmValue ) && $this->getOption( $insKey ) == $inmValue ) {
			return true;
		}
		return update_option( $this->m_sOptionPrefix.$insKey, $inmValue );
	}

	/**
	 * @param string $insKey
	 * @return boolean
	 */
	public function deleteOption( $insKey ) {
		return delete_option( $this->m_sOptionPrefix.$insKey );
	}

	public function onWpActivatePlugin() { }
	public function onWpDeactivatePlugin() { }
	public function onWpUninstallPlugin() { }
	
	protected function loadWpFunctions() {
		if ( !isset( $this->m_oWpFunctions ) ) {
			$this->m_oWpFunctions = new ICWP_WpFunctions_WPTB();
		}
	}

	protected function flushCaches() {
		if (function_exists('w3tc_pgcache_flush')) {
			w3tc_pgcache_flush();
		}
	}
	
	/**
	 * Takes an array, an array key, and a default value. If key isn't set, sets it to default.
	 */
	protected function def( &$aSrc, $insKey, $insValue = '' ) {
		if ( !isset( $aSrc[$insKey] ) ) {
			$aSrc[$insKey] = $insValue;
		}
	}
	/**
	 * Takes an array, an array key and an element type. If value is empty, sets the html element
	 * string to empty string, otherwise forms a complete html element parameter.
	 *
	 * E.g. noEmptyElement( aSomeArray, sSomeArrayKey, "style" )
	 * will return String: style="aSomeArray[sSomeArrayKey]" or empty string.
	 */
	protected function noEmptyElement( &$inaArgs, $insAttrKey, $insElement = '' ) {
		$sAttrValue = $inaArgs[$insAttrKey];
		$insElement = ( $insElement == '' )? $insAttrKey : $insElement;
		$inaArgs[$insAttrKey] = ( empty($sAttrValue) ) ? '' : ' '.$insElement.'="'.$sAttrValue.'"';
	}

}//CLASS ICWP_WTB_Base_Plugin

endif;

