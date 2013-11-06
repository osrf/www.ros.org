<?php
/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 * 
 * Version: 2013-08-27-B
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
 *
 */
require_once( dirname(__FILE__).'/icwp-processor-base.php' );

if ( !class_exists('ICWP_WptbProcessor') ):

class ICWP_WptbProcessor {
	
	const CdnjsStem					= '//cdnjs.cloudflare.com/ajax/libs/'; //All cdnjs libraries are under this path
	const CdnJqueryVersion			= '1.10.2';

	/**
	 * @var ICWP_OptionsHandler_Wptb
	 */
	protected $m_oWptbOptions;
	/**
	 * @var string
	 */
	protected $m_sPluginUrl;
	
	/**
	 * @var string
	 */
	protected $m_sPluginDir;

	/**
	 * @param ICWP_OptionsHandler_Wptb $inoOptions
	 */
	public function __construct( $inoOptions ) {
		$this->m_oWptbOptions = $inoOptions;
	}
	
	public function setPaths( $insRootDir, $insRootUrl ) {
		$this->m_sPluginDir = $insRootDir;
		$this->m_sPluginUrl = $insRootUrl;
	}
	
	public function doEnqueueResetCss() {
		if ( is_admin()
				|| ( $this->m_oWptbOptions->getOpt( 'enq_using_wordpress' ) !== 'Y' )
				|| in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))
				|| isset( $_GET['thesis_editor'] )
			) {
			return true;
		}

		$aIncludesList = $this->getCssIncludeUrls();
		if ( !empty( $aIncludesList ) ) {
			foreach( $aIncludesList as $sKey => $sCssLinkUrl ) {
				wp_register_style( $sKey, $sCssLinkUrl );
				wp_enqueue_style( $sKey );
			}
		}
	}

	public function onOutputBufferFlush( $insContent ) {
		return $this->rewriteHead( $insContent );
	}
	
	/**
	 * Performs the actual rewrite of the <HEAD> to include the reset file(s)
	 *
	 * @param $insContents
	 */
	protected function rewriteHead( $insContents ) {
		$aIncludesList = $this->getCssIncludeUrls();
		
		if ( empty( $aIncludesList ) ) {
			return $insContents;
		}
		//Add the CSS link
		$sReplace = '${1}';
		$sReplace .= "\n<!-- This site uses the WordPress Twitter Bootstrap CSS plugin v".$this->m_oWptbOptions->getVersion()." from iControlWP http://icwp.io/w/ -->";
		
		foreach ( $aIncludesList as $sKey => $sIncludeLink ) {
			$sReplace .= "\n".'<link rel="stylesheet" type="text/css" href="'.$sIncludeLink.'" />';
		}

		$sReplace .= "\n<!-- / WordPress Twitter Bootstrap CSS Plugin from iControlWP. -->";
		
		$sRegExp = "/(<\bhead\b([^>]*)>)/i";
		return preg_replace( $sRegExp, $sReplace, $insContents, 1 );
	}
	
	/**
	 * @return multitype:|Ambigous <multitype:string , Ambigous, boolean, multitype:>
	 */
	protected function getCssIncludeUrls() {
		
		$aPossibleIncludeOptions = array( 'twitter', 'twitter-legacy', 'yahoo-reset', 'yahoo-reset-3', 'normalize' );
		$sIncludeOption = $this->m_oWptbOptions->getOpt( 'option' );

		// An unsupported option, so just return the custom CSS.
		if ( !in_array( $sIncludeOption, $aPossibleIncludeOptions ) ) {
			$aIncludesList = array();
			$this->addCustomCssLink( $aIncludesList );
			return $aIncludesList;
		}
		// We save the inclusions list so we don't work it out every page load.
		$aIncludesList = $this->m_oWptbOptions->getOpt( 'includes_list' );

		if ( !is_array($aIncludesList) ) { //process the list of CSS to be included

			$aIncludesList = array();

			// 'twitter', 'twitter-legacy', 'yahoo-reset', 'yahoo-reset-3', 'normalize'
			switch ( $sIncludeOption ) {
				case 'normalize':
					if ( $this->m_oWptbOptions->getOpt( 'use_cdnjs' ) == 'Y' ) {
						// cdnjs.cloudflare.com/ajax/libs/normalize/2.0.1/normalize.css
						$aIncludesList = array( 'normalize' => self::CdnjsStem.'normalize/'.ICWP_OptionsHandler_Wptb::NormalizeVersion.'/normalize.css' );
					}
					else {
						$aIncludesList = array( 'normalize' => $this->getCssURL( 'normalize.css' ) . '?ver='.ICWP_OptionsHandler_Wptb::NormalizeVersion );
					}
					break;
				case 'yahoo-reset':
					$aIncludesList = array( 'yahoo-reset-290' => $this->getCssURL( 'yahoo-2.9.0.min.css' ) );
					break;
				case 'yahoo-reset-3':
					$aIncludesList = array( 'yahoo-reset-3' => $this->getCssURL( 'yahoo-cssreset-min.css' ) . '?ver='.ICWP_OptionsHandler_Wptb::YUI3Version );
					break;
				default: //twitter
					$aIncludesList = $this->getTwitterCssUrls( $this->m_oWptbOptions->getOpt( 'use_minified_css' ) == 'Y' );
					break;
			}
			
			// At this point $aIncludesList should be an array of all the URLs to be included with their labels. 
			// Now add Custom/Reset CSS.
			$this->addCustomCssLink( $aIncludesList );
			$this->updateIncludesCache( $aIncludesList );
		}
		return $aIncludesList;
	}
	
	public function updateIncludesCache( $inaIncludesList = false ) {
		$this->m_oWptbOptions->setOpt( 'includes_list', $inaIncludesList ); //update our cached list
	}
	
	/**
	 * Depending on the configuration options set, will provide an array of the Twitter URLs to be included
	 *  
	 * @param $infMinified
	 * @return Array
	 */
	protected function getTwitterCssUrls( $infMinified = false ) {

		$sCssFileExtension = $infMinified? '.min.css' : '.css';
		$aUrls = array();

		// link to the Twitter LESS-compiled CSS (only if the files exists)
		if ( $this->m_oWptbOptions->getOpt( 'use_compiled_css' ) == 'Y' ) {
			$sLessStemUrl = 'css/bootstrap.less'.$sCssFileExtension;
			if ( file_exists( $this->getBootstrapDir( $sLessStemUrl ) ) ) {
				$aUrls[ 'twitter-bootstrap-less' ] = $this->getBootstrapUrl( $sLessStemUrl );
				return $aUrls;
			}
		}

		// Determine the Twitter URL stem based on local or if CDNJS selected
		if ( $this->m_oWptbOptions->getOpt( 'use_cdnjs' ) == 'Y' ) {
			$sTwitterStem = self::CdnjsStem.'twitter-bootstrap/%s/css/bootstrap';
			$sTwitterStem = sprintf( $sTwitterStem, $this->getTwitterBootstrapVersion() );
		}
		else {
			$sTwitterStem = $this->getBootstrapUrl( 'css/bootstrap' ); // default is to serve it "local"
		}
		$aUrls[ 'twitter-bootstrap' ] = $sTwitterStem.$sCssFileExtension;
		
		if ( $this->m_oWptbOptions->getOpt( 'inc_responsive_css' ) == 'Y' && $this->m_oWptbOptions->getOpt( 'option' ) == 'twitter-legacy' ) {
			
			if ( $this->m_oWptbOptions->getOpt( 'use_cdnjs' ) == 'Y' ) {
				$sTwitterStem = self::CdnjsStem.'twitter-bootstrap/%s/css/bootstrap-responsive';
				$sTwitterStem = sprintf( $sTwitterStem, $this->getTwitterBootstrapVersion() );
			}
			else {
				$sTwitterStem = $this->getBootstrapUrl( 'css/bootstrap-responsive' ); // default is to serve it "local"
			}
			$aUrls[ 'twitter-bootstrap-responsive' ] = $sTwitterStem.$sCssFileExtension;
		}
		
		return $aUrls;
	}
	
	protected function addCustomCssLink( &$inaCssList = array() ) {

		if ( $this->m_oWptbOptions->getOpt( 'customcss' ) == 'Y' ) {
			$sCustomCssUrl = $this->m_oWptbOptions->getOpt( 'customcss_url' );
			if ( !empty( $sCustomCssUrl ) ) {
				$inaCssList[ 'custom-reset' ] = $sCustomCssUrl;
			}
		}
	}
	
	/**
	 * Enqueue Javascript scripts according to the plugin options.
	 */
	public function doEnqueueScripts() {
		
		$fJsInFooter = ($this->m_oWptbOptions->getOpt( 'js_head' ) == 'Y')? false : true ;
		$sBootstrapOption = $this->m_oWptbOptions->getOpt( 'option' );
		
		if ( $this->isUseTwitter() && $this->m_oWptbOptions->getOpt( 'all_js' ) == 'Y' ) {
			
			$sExtension = ( $this->m_oWptbOptions->getOpt( 'use_minified_css' ) == 'Y' )? '.min.js' : '.js';

			if ( $this->m_oWptbOptions->getOpt( 'use_cdnjs' ) == 'Y' ) {
				//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/bootstrap.min.js
				//Since version 2.3.0, now changed to:
				//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.1/js/bootstrap.min.js
				$sUrlBootstrapJs = self::CdnjsStem.'twitter-bootstrap/%s/js/bootstrap'.$sExtension;
				$sUrlBootstrapJs = sprintf( $sUrlBootstrapJs, $this->getTwitterBootstrapVersion() );
			}
			else {
				$sUrlBootstrapJs = $this->getBootstrapUrl( 'js/bootstrap'.$sExtension );
			}

			if ( $this->m_oWptbOptions->getOpt( 'replace_jquery_cdn' ) == 'Y' ) {
				//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js
				$sJqueryCdnUri = self::CdnjsStem.'jquery/'.self::CdnJqueryVersion.'/jquery'.$sExtension;
				wp_deregister_script('jquery');
				wp_register_script( 'jquery', $sJqueryCdnUri, '', self::CdnJqueryVersion, false );
			}
			
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'bootstrap-all-min', $sUrlBootstrapJs, array('jquery'), $this->m_oWptbOptions->getVersion(), $fJsInFooter );
			wp_enqueue_script( 'bootstrap-all-min' );
		}
		
		if ( $this->m_oWptbOptions->getOpt( 'prettify' ) == 'Y' ) {
			$sUrl = $this->getJsUrl( 'google-code-prettify/prettify.js' );
			wp_register_script( 'prettify_script', $sUrl, false, $this->m_oWptbOptions->getVersion(), $fJsInFooter );
			wp_enqueue_script( 'prettify_script' );
		}
	}
	
	/**
	 * @return boolean - true if Twitter Bootstrap (legacy or otherwise) is the include option
	 */
	protected function isUseTwitter() {
		$sBootstrapOption = $this->m_oWptbOptions->getOpt( 'option' );
		return strpos( $sBootstrapOption, 'twitter') === 0;
	}
	
	protected function getBootstrapDir( $insResource = '' ) {
		$sPath = $this->getBootstrapSubPath( $insResource );
		return $this->m_sPluginDir . ( ( '/' == ICWP_DS )? $sPath : str_replace( '/', ICWP_DS, $sPath ) );
	}
	
	protected function getBootstrapUrl( $insResource = '' ) {
		return $this->m_sPluginUrl.$this->getBootstrapSubPath( $insResource );
	}
	
	protected function getBootstrapSubPath( $insResource = '' ) {
		return sprintf( 'resources/bootstrap-%s/'.$insResource, $this->getTwitterBootstrapVersion() ) ;
	}
	
	protected function getTwitterBootstrapVersion() {
		return $this->m_oWptbOptions->getTwitterBootstrapVersion();
	}
	
	/**
	 * @param string $insCss
	 * @return string
	 */
	protected function getCssUrl( $insCss ) {
		return $this->m_sPluginUrl.'resources/css/'.$insCss;
	}
	/**
	 * @param string $insJs
	 * @return string
	 */
	protected function getJsUrl( $insJs ) {
		return $this->m_sPluginUrl.'resources/js/'.$insJs;
	}
}
endif;