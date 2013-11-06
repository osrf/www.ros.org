<?php
/*
Plugin Name: WordPress Twitter Bootstrap CSS
Plugin URI: http://www.icontrolwp.com/wordpress-twitter-bootstrap-css-plugin-home/
Description: Link Twitter Bootstrap CSS and Javascript files before all others regardless of your theme.
Version: 3.0.0-7
Author: iControlWP
Author URI: http://icwp.io/v
*/

/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 *
 * "WordPress Twitter Bootstrap CSS" (formerly "WordPress Bootstrap CSS") is
 * distributed under the GNU General Public License, Version 2,
 * June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110, USA
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once( dirname(__FILE__).'/src/worpit-plugins-base.php' );
require_once( dirname(__FILE__).'/src/icwp-optionshandler-wptb.php' );

if ( !class_exists('HLT_BootstrapCss') ):

class HLT_BootstrapCss extends ICWP_WTB_Base_Plugin {
	
	const PluginVersion				= '3.0.0-7';  //SHOULD BE UPDATED UPON EACH NEW RELEASE
	const InputPrefix				= 'hlt_bootstrap_';
	const OptionPrefix				= 'hlt_bootstrapcss_'; //ALL database options use this as the prefix.

	/**
	 * @var string
	 */
	static public $BOOSTRAP_DIR;
	/**
	 * @var string
	 */
	static public $BOOSTRAP_URL;

	/**
	 * @var array
	 */
	protected $m_aAllPluginOptions;
	/**
	 * @var string
	 */
	protected $m_sLessOptionsKey;
	/**
	 * @var array
	 */
	protected $m_aAllBootstrapLessOptions;

	/**
	 * @var array
	 */
	protected $m_aBootstrapOptions;
	/**
	 * @var array
	 */
	protected $m_aPluginOptions_BootstrapSection;
	/**
	 * @var array
	 */
	protected $m_aPluginOptions_TwitterBootstrapSection;
	/**
	 * @var array
	 */
	protected $m_aPluginOptions_ExtraTwitterSection;
	/**
	 * @var array
	 */
	protected $m_aPluginOptions_MiscOptionsSection;

	/**
	 * @var HLT_BootstrapLess
	 */
	protected $m_oBsLess;

	/**
	 * @var ICWP_OptionsHandler_Wptb
	 */
	protected $m_oWptbOptions;
	
	/**
	 * @var ICWP_WptbProcessor
	 */
	protected $m_oWptbProcessor;
	
	public function __construct() {
		
		$this->m_sPluginRootFile = __FILE__; //ensure all relative paths etc. are setup.
		parent::__construct();
		
		$this->m_sVersion			= self::PluginVersion;
		$this->m_sPluginHumanName	= "WordPress Twitter Bootstrap";
		$this->m_sPluginMenuTitle	= "Twitter Bootstrap";
		$this->m_sOptionPrefix		= self::OptionPrefix;

		$this->m_sParentMenuIdSuffix = 'wtb';
		$this->loadWptbOptions();

		self::$BOOSTRAP_DIR			= $this->m_sPluginDir.'resources'.ICWP_DS.'bootstrap-'.$this->m_oWptbOptions->getTwitterBootstrapVersion().ICWP_DS;
		self::$BOOSTRAP_URL			= plugins_url( 'resources/bootstrap-'.$this->m_oWptbOptions->getTwitterBootstrapVersion().'/', $this->m_sPluginRootFile ) ;
	}

	protected function loadAllOptions() {
		$this->loadWptbOptions();
	}
	
	/**
	 * @return void
	 */
	protected function loadWptbOptions() {
		if ( !isset( $this->m_oWptbOptions ) ) {
			$this->m_oWptbOptions = new ICWP_OptionsHandler_Wptb( self::OptionPrefix, 'plugin_options', $this->m_sVersion );
		}
	}

	/**
	 * @return void
	 */
	protected function loadBootstrapLess() {
		if ( isset( $this->m_oBsLess ) ) {
			return;
		}
		if ( $this->m_oWptbOptions->getOpt( 'option' ) == 'twitter' ) {
			require_once( dirname(__FILE__).'/src/hlt-bootstrap-less.php' );
		}
		else {
			require_once( dirname(__FILE__).'/src/hlt-bootstrap-less-legacy.php' );
		}
		
		$this->setLessOptionsKey();
		$this->m_oBsLess = new HLT_BootstrapLess( self::$BOOSTRAP_DIR, $this->m_sLessOptionsKey );
	}
	
	protected function setLessOptionsKey() {
		if ( !isset( $this->m_sLessOptionsKey ) ) {
			$this->m_sLessOptionsKey = $this->m_sOptionPrefix . 'all_less_options';
		}
	}
	
	protected function loadWptbProcessor() {
		if ( !isset( $this->m_oWptbProcessor ) ) {
			require_once( dirname(__FILE__).'/src/icwp-processor-wptb.php' );
			$this->loadWptbOptions();
			$this->m_oWptbProcessor = new ICWP_WptbProcessor( $this->m_oWptbOptions );
			$this->m_oWptbProcessor->setPaths( $this->m_sPluginDir, $this->m_sPluginUrl );
		}
	}
	
	public function onWpInit() {
		parent::onWpInit();
		if ( !is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) && !isset( $_GET['thesis_editor'] ) ) {
			
			if ( $this->m_oWptbOptions->getOpt( 'enq_using_wordpress' ) !== 'Y' ) { // see end of file for the alternative (enqueueing)
				$this->loadWptbProcessor();
				ob_start( array( $this->m_oWptbProcessor, 'onOutputBufferFlush' ) );
			}
		}
		
		add_action( 'wp_enqueue_scripts', array( $this, 'onWpPrintStyles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'onWpEnqueueScripts' ) );
		
		// if shortcodes are enabled, instantiatetwitter-legacy
		$sBootstrapOption = $this->m_oWptbOptions->getOpt( 'option' );
		if ( strpos( $sBootstrapOption, 'twitter' ) !== false && $this->m_oWptbOptions->getOpt( 'useshortcodes' ) == 'Y' ) {
			if ( $sBootstrapOption == 'twitter' ) {
				require_once( dirname(__FILE__).'/src/hlt-bootstrap-shortcodes.php' );
			}
			else {
				require_once( dirname(__FILE__).'/src/hlt-bootstrap-shortcodes-legacy.php' );
			}
			$oShortCodes = new HLT_BootstrapShortcodes();
		}
		
		// if option to enable shortcodes in sidebar is on, add filter
		$sShortcodeSidebarOption = $this->m_oWptbOptions->getOpt( 'enable_shortcodes_sidebarwidgets' );
		if ( $sShortcodeSidebarOption == 'Y' ) {
			add_filter('widget_text', 'do_shortcode');
		}
	}
	
	public function onWpAdminInit() {
		parent::onWpAdminInit();

		global $pagenow;
		//Loads the news widget on the Dashboard (if it hasn't been disabled)
		if ( $pagenow == 'index.php' ) {
			$sDashboardRssOption = $this->m_oWptbOptions->getOpt( 'hide_dashboard_rss_feed' );
			if ( empty( $sDashboardRssOption ) || $this->m_oWptbOptions->getOpt( 'hide_dashboard_rss_feed' ) == 'N' ) {
				include_once( dirname(__FILE__).'/hlt-rssfeed-widget.php' );
				$oHLT_DashboardRssWidget = new HLT_DashboardRssWidget();
			}
		}
		
		// Determine whether to show ads and marketing messages
		// Currently this is when the site uses the iControlWP service and is linked
		$this->isShowMarketing();
		
		// If it's a plugin admin page, we do certain things we don't do anywhere else.
		if ( $this->isIcwpPluginAdminPage()) {
			
			//JS color picker for the Bootstrap LESS
			if ( $_GET['page'] == $this->getSubmenuId( 'bootstrap-less' ) ) {
				wp_register_style( 'miniColors', $this->m_sPluginUrl.'inc/miniColors/jquery.miniColors.css', false, $this->m_sVersion );
				wp_enqueue_style( 'miniColors' );
	
				wp_register_script( 'miniColors', $this->m_sPluginUrl.'inc/miniColors/jquery.miniColors.min.js', false, $this->m_sVersion, true );
				wp_enqueue_script( 'miniColors' );
			}
		}
		
		//Enqueues the WP Admin Twitter Bootstrap files if the option is set or we're in a iControlWP admin page.
		if ( is_admin() && $this->m_oWptbOptions->getOpt( 'inc_bootstrap_css_wpadmin' ) == 'Y' ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueBootstrapAdminCss' ), 99 );
		}
		
		if ( is_admin() && $this->m_oWptbOptions->getOpt( 'inc_bootstrap_css_in_editor' ) == 'Y' ) {
			add_filter( 'mce_css', array( $this, 'filter_include_bootstrap_in_editor' ) );
		}
		
		//Multilingual support.
		load_plugin_textdomain( 'hlt-wordpress-bootstrap-css', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	protected function createPluginSubMenuItems(){
		$this->m_aPluginMenu = array(
			//Menu Page Title => Menu Item name, page ID (slug), callback function for this page - i.e. what to do/load.
			$this->getSubmenuPageTitle( 'Bootstrap CSS' ) => array( 'Bootstrap CSS', $this->getSubmenuId('bootstrap-css'), 'onDisplayWtbCss' ),
			$this->getSubmenuPageTitle( 'Bootstrap LESS' ) => array( 'Bootstrap LESS', $this->getSubmenuId('bootstrap-less'), 'onDisplayWtbLess' ),
		);
	}
	
	/**
	 */
	protected function handlePluginUpgrade() {
		$sCurrentPluginVersion = $this->m_oWptbOptions->getOpt( 'current_plugin_version' );
		
		if ( empty($sCurrentPluginVersion) ) {
			$sCurrentPluginVersion = '0.0';
		}
		
		// Forces a rebuild for the list of CSS includes
		if ( $sCurrentPluginVersion !== $this->m_sVersion ) {
			$this->m_oWptbOptions->setOpt( 'includes_list', false );
		}
		
		// ensure only valid users attempt this.
		if ( $sCurrentPluginVersion !== $this->m_sVersion && current_user_can( 'manage_options' ) ) {

			$this->loadBootstrapLess();
			$this->m_oBsLess->handleUpgrade( $sCurrentPluginVersion );
	
			//Recompile LESS CSS if applicable
			if ( $this->m_oWptbOptions->getOpt('use_compiled_css') == 'Y' ) {
				if ( $this->m_oBsLess->reWriteVariablesLess() ) {
					$this->m_oBsLess->compileAllBootstrapLess();
				}
			}
		
			//Set the flag so that this update handler isn't run again for this version.
			$this->m_oWptbOptions->setOpt( 'current_plugin_version', $this->m_sVersion );
		}

		//Someone clicked the button to acknowledge the update
		if ( isset( $_POST['hlt_hide_update_notice'] ) && isset( $_POST['hlt_user_id'] ) ) {
			$this->updateVersionUserMeta( $_POST['user_id'] );
			if ( $this->isShowMarketing() ) {
				wp_redirect( admin_url( "admin.php?page=".$this->getFullParentMenuId() ) );
			}
			else {
				wp_redirect( admin_url( $_POST['redirect_page'] ) );
			}
			exit();
		}
	}
	
	/**
	 * Updates the current (or supplied user ID) user meta data with the version of the plugin
	 *  
	 * @param unknown_type $innId
	 */
	protected function updateVersionUserMeta( $innId = null ) {
		if ( is_null( $innId ) ) {
			$oCurrentUser = wp_get_current_user();
			if ( !($oCurrentUser instanceof WP_User) ) {
				return;
			}
			$nUserId = $oCurrentUser->ID;
		}
		else {
			$nUserId = $innId;
		}
		update_user_meta( $nUserId, self::OptionPrefix.'current_version', $this->m_sVersion );
	}
	
	public function onWpAdminNotices() {
		
		//Do we have admin priviledges?
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
		$this->adminNoticeVersionUpgrade();
		$this->adminNoticeOptionsUpdated();
	}
	
	/**
	 * Shows the update notification - will bail out if the current user is not an admin 
	 */
	private function adminNoticeVersionUpgrade() {

		$oCurrentUser = wp_get_current_user();
		if ( !($oCurrentUser instanceof WP_User) ) {
			return;
		}
		$nUserId = $oCurrentUser->ID;

		$sCurrentVersion = get_user_meta( $nUserId, $this->m_sOptionPrefix.'current_version', true );
		// A guard whereby if we can't ever get a value for this meta, it means we can never set it.
		// If we can never set it, we shouldn't force the Ads on those users who can't get rid of it.
		if ( empty( $sCurrentVersion ) ) { //the value has never been set, or it's been installed for the first time.
			$result = update_user_meta( $nUserId, $this->m_sOptionPrefix.'current_version', $this->m_sVersion );
			return; //meaning we don't show the update notice upon new installations and for those people who can't set the version in their meta.
		}

		if ( $sCurrentVersion !== $this->m_sVersion ) {
			
			$sRedirectPage = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : 'index.php';
			ob_start();
			?>
				<style>
					a#fromIcwp { padding: 0 5px; border-bottom: 1px dashed rgba(0,0,0,0.1); color: blue; font-weight: bold; }
				</style>
				<form id="IcwpUpdateNotice" method="post" action="admin.php?page=<?php echo $this->getSubmenuId('bootstrap-css'); ?>">
					<input type="hidden" value="<?php echo $sRedirectPage; ?>" name="hlt_redirect_page" id="hlt_redirect_page">
					<input type="hidden" value="1" name="hlt_hide_update_notice" id="hlt_hide_update_notice">
					<input type="hidden" value="<?php echo $nUserId; ?>" name="hlt_user_id" id="hlt_user_id">
			
					<?php if ( false && $this->isShowMarketing() ) : ?>
			
					<h4 style="margin:10px 0 3px;">Quick question: Do you manage multiple WordPress sites and need a better way to do it?</h4>
					<input type="submit" value="Cool, but just show me what's new with this update and hide this notice" name="submit" class="button" style="float:right;">
					<p>
						Free up your time today and do it all from 1 place in a few clicks.
						<a href="http://icwp.io/5" id="fromIcwp" title="iControlWP: Secure WordPress Management" target="_blank">Tell me how</a>!<br />
					</p>
					<?php else : ?>
			
					<h4 style="margin:10px 0 3px;">Twitter Bootstrap plugin has been updated- there may or may not be <a href="http://icwp.io/1v" id="fromIcwp" title="Twitter Bootstrap Plugin Shortcodes" target="_blank">updates to shortcodes</a> or the Bootstrap CSS may have changed quite a bit.</h4>
					<input type="submit" value="Show me and hide this notice." name="submit" class="button" style="float:left; margin-bottom:10px;">
					<?php endif; ?>
			
					<div style="clear:both;"></div>
				</form>
			<?php
			$sNotice = ob_get_contents();
			ob_end_clean();
			
			$this->getAdminNotice( $sNotice, 'updated', true );
		}
		
	}//adminNoticeVersionUpgrade
	
	private function adminNoticeOptionsUpdated() {
		$sAdminFeedbackNotice = $this->m_oWptbOptions->getOpt( 'feedback_admin_notice' );
		if ( !empty( $sAdminFeedbackNotice ) ) {
			$sNotice = '<p>'.$sAdminFeedbackNotice.'</p>';
			$this->getAdminNotice( $sNotice, 'updated', true );
			$this->m_oWptbOptions->setOpt( 'feedback_admin_notice', '' );
		}
	}
	
	public function onDisplayMainMenu() {

		// To ensure the nag bar disappears if/when they visit the dashboard
		// regardless of clicking the button.
		$this->updateVersionUserMeta();
		parent::onDisplayMainMenu();
	}
	
	public function onDisplayWtbCss() {

		$aAvailableOptions = $this->m_oWptbOptions->getOptions();
		$sAllFormInputOptions = $this->m_oWptbOptions->collateAllFormInputsForAllOptions();
		
		$aData = array(
			'plugin_url'		=> $this->m_sPluginUrl,
			'var_prefix'		=> $this->m_sOptionPrefix,
			'fShowAds'			=> $this->isShowMarketing(),
			'aAllOptions'		=> $aAvailableOptions,
			'all_options_input'	=> $sAllFormInputOptions,
			'nonce_field'		=> $this->getSubmenuId('bootstrap_css_wtbcss'),
			'form_action'		=> 'admin.php?page='.$this->getSubmenuId('bootstrap-css'),
		);
		$this->display( 'bootstrapcss_index', $aData );
	}
	
	public function onDisplayWtbLess() {
		
		$this->loadBootstrapLess();
		$aAvailableOptions = $this->m_oBsLess->getAllBootstrapLessOptions( false );

		$aData = array(
			'plugin_url'				=> $this->m_sPluginUrl,
			'var_prefix'				=> $this->m_sOptionPrefix,
			'fShowAds'					=> $this->isShowMarketing(),
			'aAllOptions'				=> $aAvailableOptions,
			'compiler_enabled'			=> $this->m_oWptbOptions->getOpt( 'use_compiled_css' ) === 'Y',

			'less_prefix'				=> $this->m_oBsLess->LessOptionsPrefix,
			'less_file_location'		=> array( self::$BOOSTRAP_DIR.'css'.ICWP_DS.'bootstrap.less.css', self::$BOOSTRAP_URL.'css/bootstrap.less.css' ),
			'page_link_options'			=> $this->getSubmenuId('bootstrap-css'),
			
			'nonce_field'				=> $this->getSubmenuId('bootstrap_css_wtbcss'),
			'form_action'				=> 'admin.php?page='.$this->getSubmenuId('bootstrap-less')
		);
		$this->display( 'bootstrapcss_less', $aData );
	}
	
	/**
	 * This would only be called when $this->isIcwpPluginFormSubmit() is true
	 * (non-PHPdoc)
	 * @see ICWP_Pure_Base::handlePluginFormSubmit()
	 */
	protected function handlePluginFormSubmit() {
		
		$this->m_fSubmitCbcMainAttempt = true;
	
		if ( isset( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case $this->getSubmenuId( 'bootstrap-css' ):
					$this->handleSubmit_BootstrapCssOptions();
					break;
					
				case $this->getSubmenuId( 'bootstrap-less' ):
					$this->handleSubmit_BootstrapLess();
					break;
			}
		}
		$this->loadWptbProcessor();
		$this->m_oWptbProcessor->updateIncludesCache(); //clear it
		$this->flushCaches();
		
		if ( !self::$m_fUpdateSuccessTracker ) {
			$this->m_oWptbOptions->setOpt( 'feedback_admin_notice', 'Updating Twitter Bootstrap Settings <strong>Failed</strong>.' );
		}
		else {
			$this->m_oWptbOptions->setOpt( 'feedback_admin_notice', 'Updating Twitter Bootstrap Settings <strong>Succeeded</strong>.' );
		}
	}

	protected function handleSubmit_BootstrapCssOptions() {

		//Ensures we're actually getting this request from WP.
		check_admin_referer( $this->getSubmenuId('bootstrap_css_wtbcss') );
		
		if ( !isset($_POST[$this->m_sOptionPrefix.'all_options_input']) ) {
			return;
		}
		$this->m_oWptbOptions->updatePluginOptionsFromSubmit( $_POST[self::OptionPrefix.'all_options_input'] );
	}
	
	protected function handleSubmit_BootstrapLess() {

		// Ensures we're actually getting this request from WP.
		check_admin_referer( $this->getSubmenuId('bootstrap_css_wtbcss') );
		
		$this->loadBootstrapLess();

		if ( isset( $_POST['submit_reset'] ) ) {
			$this->m_oBsLess->resetToDefaultAllLessOptions();
			return;
		}

		$this->m_oBsLess->processNewLessOptions( $this->m_sOptionPrefix, !isset( $_POST['submit_preserve'] ) );
	}

	public function filter_include_bootstrap_in_editor( $mce_css ) {
		$mce_css = explode( ',', $mce_css);
		$mce_css = array_map( 'trim', $mce_css);
		array_unshift( $mce_css, self::$BOOSTRAP_URL.'css/bootstrap.min.css' );
		return implode( ',', $mce_css );
	}
	
	public function onWpPrintStyles() {
		if ( $this->m_oWptbOptions->getOpt( 'prettify' ) == 'Y' ) {
			$sUrl = $this->getCssUrl( 'google-code-prettify/prettify.css' );
			wp_register_style( 'prettify_style', $sUrl );
			wp_enqueue_style( 'prettify_style' );
		}
	}
	
	public function onWpEnqueueScripts() {
		$this->loadWptbProcessor();
		$this->m_oWptbProcessor->doEnqueueScripts();
	}
	
	public function onEnqueueResetCss() {
		$this->loadWptbProcessor();
		$this->m_oWptbProcessor->doEnqueueResetCss();
	}
	
	/**
	 * Not currently used, but could be useful once we work out what way the JS should be included.
	 * @param $insHandle	For example: 'prettify/prettify.css'
	 */
	protected function isRegistered( $insHandle ) {
		return (
			wp_script_is( $insHandle, 'registered' ) ||
			wp_script_is( $insHandle, 'queue' ) ||
			wp_script_is( $insHandle, 'done' ) ||
			wp_script_is( $insHandle, 'to_do' )
		);
	}
	
	public function onWpPluginActionLinks( $inaLinks, $insFile ) {
		if ( $insFile == $this->m_sPluginFile ) {
			$sSettingsLink = '<a href="'.admin_url( "admin.php" ).'?page='.$this->getSubmenuId('bootstrap-css').'">' . _hlt__( 'Settings' ) . '</a>';
			array_unshift( $inaLinks, $sSettingsLink );
		}
		return $inaLinks;
	}

	public function onWpShutdown() {
		parent::onWpShutdown();
		$this->saveProcessors_Action();
	}

	protected function saveProcessors_Action() {

		if ( isset( $this->m_oWptbOptions ) ) {
			$this->m_oWptbOptions->savePluginOptions( false );
		}
	}
	
	protected function deleteAllPluginDbOptions() {
		
		parent::deleteAllPluginDbOptions();
		
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
		$this->loadWptbOptions();
		$this->m_oWptbOptions->deletePluginOptions();
		
		$this->loadBootstrapLess();
		$this->m_oBsLess->processLessOptions( 'delete' );
		
		//legacy options
		$this->deleteOption( 'upgraded1to2' );
		
		remove_action( 'shutdown', array( $this, 'onWpShutdown' ) );
	}
	
	public function onWpDeactivatePlugin() {
		if ( $this->m_oWptbOptions->getOpt( 'delete_on_deactivate' ) == 'Y' ) {
			$this->deleteAllPluginDbOptions();
		}
	}
}

endif;

$oHLT_BootstrapCss = new HLT_BootstrapCss();

// Trying to enque the style as early as possible.
add_action( 'wp_enqueue_scripts', array( $oHLT_BootstrapCss, 'onEnqueueResetCss' ), 0 );
