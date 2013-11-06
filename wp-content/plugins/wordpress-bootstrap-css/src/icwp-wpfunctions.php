<?php
/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 * 
 * Version: 2013-08-14_A
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

if ( !class_exists('ICWP_WpFunctions_WPTB') ):

class ICWP_WpFunctions_WPTB {

	/**
	 * @var string
	 */
	protected $m_sWpVersion;
	
	public function __construct() {
	}

	/**
	 * @param string $insUrl
	 * @return boolean
	 */
	public function isUrlValid( $insUrl ) {
		$aResponse = wp_remote_get( $insUrl );
		return !is_wp_error($aResponse) && $aResponse['response']['code'] == 200;
	}
	
	/**
	 * @param string $insPluginFile
	 * @return boolean|stdClass
	 */
	public function getIsPluginUpdateAvailable( $insPluginFile ) {
		
		$aUpdates = $this->getWordpressUpdates();
		if ( empty( $aUpdates ) ) {
			return false;
		}
		if ( isset( $aUpdates[ $insPluginFile ] ) ) {
			return $aUpdates[ $insPluginFile ];
		}
		return false;
	}

	public function getPluginUpgradeLink( $insPluginFile ) {
		$sUrl = self_admin_url( 'update.php' ) ;
		$aQueryArgs = array(
			'action' 	=> 'upgrade-plugin',
			'plugin'	=> urlencode( $insPluginFile ),
			'_wpnonce'	=> wp_create_nonce( 'upgrade-plugin_' . $insPluginFile )
		);
		return add_query_arg( $aQueryArgs, $sUrl );
	}
	
	public function getWordpressUpdates() {
		$oCurrent = $this->getTransient( 'update_plugins' );
		return $oCurrent->response;
	}
	
	/**
	 * The full plugin file to be upgraded.
	 * 
	 * @param string $insPluginFile
	 * @return boolean
	 */
	public function doPluginUpgrade( $insPluginFile ) {

		if ( !$this->getIsPluginUpdateAvailable($insPluginFile)
			|| ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'update.php' ) ) {
			return true;
		}
		$sUrl = $this->getPluginUpgradeLink( $insPluginFile );
		wp_redirect( $sUrl );
		exit();
	}
	/**
	 * @param string $insKey
	 * @return object
	 */
	protected function getTransient( $insKey ) {
	
		// TODO: Handle multisite
	
		if ( version_compare( $this->getWordPressVersion(), '2.7.9', '<=' ) ) {
			return get_option( $insKey );
		}
	
		if ( function_exists( 'get_site_transient' ) ) {
			return get_site_transient( $insKey );
		}
	
		if ( version_compare( $this->getWordPressVersion(), '2.9.9', '<=' ) ) {
			return apply_filters( 'transient_'.$insKey, get_option( '_transient_'.$insKey ) );
		}
	
		return apply_filters( 'site_transient_'.$insKey, get_option( '_site_transient_'.$insKey ) );
	}
	
	/**
	 * @return string
	 */
	public function getWordPressVersion() {
		global $wp_version;
		
		if ( empty( $this->m_sWpVersion ) ) {
			$sVersionFile = ABSPATH.WPINC.'/version.php';
			$sVersionContents = file_get_contents( $sVersionFile );
			
			if ( preg_match( '/wp_version\s=\s\'([^(\'|")]+)\'/i', $sVersionContents, $aMatches ) ) {
				$this->m_sWpVersion = $aMatches[1];
			}
		}
		return $this->m_sWpVersion;
	}
}

endif;