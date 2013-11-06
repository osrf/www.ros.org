<?php
/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
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

require_once( dirname(__FILE__).'/icwp-optionshandler-base.php' );

if ( !class_exists('ICWP_OptionsHandler_Wptb') ):

class ICWP_OptionsHandler_Wptb extends ICWP_OptionsHandler_Base_WPTB {

	const TwitterVersion			= '3.0.0'; //should reflect the Bootstrap version folder name
	const TwitterVersionLegacy		= '2.3.2'; //should reflect the Bootstrap version folder name
	const NormalizeVersion			= '2.1.3';
	const YUI3Version				= '3.10.0';
	
	public function getTwitterBootstrapVersion() {
		if ( $this->getOpt( 'option' ) == 'twitter-legacy' ) {
			return self::TwitterVersionLegacy;
		}
		return self::TwitterVersion;
	}
	
	public function updatePluginOptionsFromSubmit( $insAllOptionsInput ) {
		parent::updatePluginOptionsFromSubmit( $insAllOptionsInput );

		$sCustomUrl = $this->getOpt( 'customcss_url' );
		if ( !empty($sCustomUrl) && $this->getOpt( 'customcss' ) == 'Y' ) {

			require_once( dirname(__FILE__).'/icwp-wpfunctions.php' );
			$oWpFunctions = new ICWP_WpFunctions_WPTB();
			if ( $oWpFunctions->isUrlValid( $sCustomUrl ) ) {
				$this->setOpt( 'customcss_url', $sCustomUrl );
			}
			else {
				$this->setOpt( 'customcss_url', '' );
			}
		}
	}
	
	public function definePluginOptions() {

		$this->m_aDirectSaveOptions = array();
		
		$aNonUiOptions = array(
			'feedback_admin_notice',
			'includes_list',
			'inc_responsive_css'
		);
		$this->mergeNonUiOptions( $aNonUiOptions );
		
		$aBootstrapOptions = array( 'select',
			array( 'none', 				'None' ),
			array( 'twitter',			'Twitter Bootstrap CSS v'.self::TwitterVersion ),
			array( 'twitter-legacy',	'Twitter Bootstrap CSS v'.self::TwitterVersionLegacy ),
			array( 'normalize',			'Normalize CSS v'.self::NormalizeVersion ),
			array( 'yahoo-reset',		'Yahoo UI Reset CSS v2.9.0' ),
			array( 'yahoo-reset-3',		'Yahoo UI Reset CSS v'.self::YUI3Version )
		);

		$aBootstrapSection = 	array(
			'section_title' => 'Choose Bootstrap CSS Options',
			'section_options' => array(
				array(
					'option',
					'',
					'none',
					$aBootstrapOptions,
					'Bootstrap Option',
					'Choose Your Preferred Bootstrap Option',
					''
				),
				array(
					'enq_using_wordpress',
					'',
					'N',
					'checkbox',
					'Use WordPress System',
					"Not recommended- Use native WordPress CSS enqueue for Bootstrap files.",
					"This can't guarantee the file will be loaded first (which it should be)."
				),
				array(
					'customcss',
					'',
					'N',
					'checkbox',
					'Custom Reset CSS',
					'Enable custom CSS link',
					'(note: linked after any bootstrap/reset CSS selected above)'
				),
				array(
					'customcss_url',
					'',
					'http://',
					'text',
					'Custom CSS URL',
					'Provide the <strong>full</strong> URL path.',
					''
				)
			)
		);
		$aTwitterBootstrapSection = 	array(
			'section_title' => 'Twitter Bootstrap Javascript Library Options',
			'section_options' => array(
				array(
					'all_js',
					'',
					'none',
					'checkbox',
					'All Javascript Libraries',
					'Include ALL Bootstrap Javascript libraries',
					'This will also include the jQuery library if it is not already included'
				),
				array(
					'js_head',
					'',
					'N',
					'checkbox',
					'JavaScript Placement',
					'Place Javascript in &lt;HEAD&gt;',
					'Only check this option if know you need it.'
				),
			),
		);
		$aExtraTwitterSection = 	array(
			'section_title' => 'Extra Twitter Bootstrap Options',
			'section_options' => array(
				array(
					'useshortcodes',
					'',
					'N',
					'checkbox',
					'Bootstrap Shortcodes',
					'Enable Twitter Bootstrap Shortcodes',
					'Loads WordPress shortcodes for fast use of Twitter Bootstrap Components.'
				),
				array(
					'use_minified_css',
					'',
					'N',
					'checkbox',
					'Minified',
					'Use Minified CSS/JS libraries',
					'Uses minified CSS libraries where available.'
				),
				array(
					'use_compiled_css',
					'',
					'N',
					'checkbox',
					'Enabled LESS',
					'Enables LESS Compiler Section',
					'Use the LESS Compiler to customize your Twitter Bootstrap CSS.'
				),
				array(
					'replace_jquery_cdn',
					'',
					'N',
					'checkbox',
					'Replace JQuery',
					'Replace JQuery library with JQuery from CDNJS',
					"In case your WordPress version is too old and doesn't have the necessary JQuery version, this will replace your JQuery with a compatible version served from CDNJS."
				)
			)
		);
		
		$aMiscOptionsSection = 	array(
			'section_title' => 'Miscellaneous Plugin Options',
			'section_options' => array(
				array(
					'use_cdnjs',
					'',
					'N',
					'checkbox',
					'Use CDNJS',
					'Link to CDNJS libraries',
					'Instead of serving libraries locally, use a dedicated CDN to serve files (<a href="http://wordpress.org/extend/plugins/cdnjs/" target="_blank">CDNJS</a>).'
				),
				array(
					'enable_shortcodes_sidebarwidgets',
					'',
					'N',
					'checkbox',
					'Sidebar Shortcodes',
					'Enable Shortcodes in Sidebar Widgets',
					'Allows you to use Twitter Bootstrap (and any other) shortcodes in your Sidebar Widgets.'
				),
				array(
					'inc_bootstrap_css_in_editor',
					'',
					'N',
					'checkbox',
					'CSS in Editor',
					'Include Twitter Bootstrap CSS in the WordPress Post Editor',
					'Only select this if you want to have Bootstrap styles show in the editor.'
				),
				array(
					'inc_bootstrap_css_wpadmin',
					'',
					'N',
					'checkbox',
					'Admin Bootstrap CSS',
					'Include Twitter Bootstrap CSS in the WordPress Admin',
					'Not a standard Twitter Bootstrap CSS. <a href="http://bit.ly/HgwlZI" target="_blank"><span class="label label-info">more info</span></a>'
				),
				array(
					'hide_dashboard_rss_feed',
					'',
					'N',
					'checkbox',
					'Hide RSS News Feed',
					'Hide the iControlWP Blog news feed from the Dashboard',
					'Hides our news feed from inside your Dashboard.'
				),
				array(
					'delete_on_deactivate',
					'',
					'N',
					'checkbox',
					'Delete Plugin Settings',
					'Delete All Plugin Settings Upon Plugin Deactivation',
					'Careful: Removes all plugin options when you deactivite the plugin.'
				),
				array(
					'prettify',
					'',
					'N',
					'checkbox',
					'Display Code Snippets',
					'Include Google Prettify/Pretty Links Javascript',
					'If you display code snippets or similar on your site, enabling this option will include the Google Prettify Javascript library for use with these code blocks.'
				)
			)
		);

		$this->m_aOptions = array(
			$aBootstrapSection,
			$aTwitterBootstrapSection,
			$aExtraTwitterSection,
			$aMiscOptionsSection
		);
	}

	public function updateHandler() {
		
		$sCurrentVersion = $this->getPluginOptionsVersion();

		if ( version_compare( $sCurrentVersion, '3.0.0-2', '<' ) ) {
			$aOptions = array(
				'option',
				'enq_using_wordpress',
				'js_head',
				'all_js',
				'use_cdnjs',
				'use_compiled_css',
				'use_minified_css',
				'customcss',
				'customcss_url',
				'delete_on_deactivate',
				'enable_shortcodes_sidebarwidgets',
				'hide_dashboard_rss_feed',
				'inc_bootstrap_css_in_editor',
				'inc_bootstrap_css_wpadmin',
				'inc_responsive_css',
				'replace_jquery_cdn',
				'useshortcodes',
				'prettify',
				'feedback_admin_notice',
				'current_plugin_version',
				'includes_list'
			);
			foreach( $aOptions as $sOption ) {
				$mPreviousOption = $this->getOption( $sOption );
				if ( $mPreviousOption !== false ) {
					$this->setOpt( $sOption, $this->getOption( $sOption ) );
				}
				$this->deleteOption( $sOption );
			}
		}
		
		if ( version_compare( $sCurrentVersion, '3.0.0-1', '<' ) ) {
			if ( $this->getOpt( 'option' ) == 'twitter' ) {
				$this->setOpt( 'option', 'twitter-legacy' );
			}
		}
	}
}

endif;