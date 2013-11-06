<?php
/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 * 
 * Version: 2013-08-27-A
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

if ( !class_exists('ICWP_OptionsHandler_Base_WPTB') ):

class ICWP_OptionsHandler_Base_WPTB {
	
	const CollateSeparator = '--SEP--';
	
	/**
	 * @var string
	 */
	protected $m_sOptionPrefix;
	
	/**
	 * @var string
	 */
	protected $m_sVersion;

	/**
	 * @var array
	 */
	protected $m_aOptions;
	
	/**
	 * @var array
	 */
	protected $m_aDirectSaveOptions;
	
	/**
	 * @var boolean
	 */
	protected $m_fIsMultisite;
	
	/**
	 * This is used primarily for the options deletion/cleanup.  We store the names
	 * of options here that are not modified directly by the user/UI so that we can
	 * cleanup later on.
	 * 
	 * @var array
	 */
	protected $m_aIndependentOptions;
	
	/**
	 * These are options that need to be stored, but are never set by the UI.
	 * 
	 * @var array
	 */
	protected $m_aNonUiOptions;

	/**
	 * @var array
	 */
	protected $m_aOptionsValues;
	/**
	 * @var array
	 */
	protected $m_aOptionsStoreName;
	
	public function __construct( $insPrefix, $insStoreName, $insVersion ) {
		$this->m_sOptionPrefix = $insPrefix;
		$this->m_aOptionsStoreName = $insStoreName;
		$this->m_sVersion = $insVersion;
		
		$this->m_fIsMultisite = function_exists( 'is_multisite' ) && is_multisite();
		
		// Build the whole options system.
		$this->initOptions();
		
		// Handle any upgrades as necessary (only go near this if it's the admin area)
		add_action( 'plugins_loaded', array( $this, 'doUgrade' ) );
	}
	
	public function doUgrade() {
		if ( $this->hasPluginManageRights() ) {
			$this->initOptions();
			$this->updateHandler();
		}
	}
	
	public function hasPluginManageRights() {
		if ( !current_user_can( 'manage_options' ) ) {
			return false;
		}
		if ( $this->m_fIsMultisite && is_network_admin() ) {
			return true;
		}
		else if ( !$this->m_fIsMultisite && is_admin() ) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->m_sVersion;
		$sCurrentVersion = empty( $this->m_aOptionsValues[ 'current_plugin_version' ] )? '0.0' : $this->m_aOptionsValues[ 'current_plugin_version' ];
	}

	/**
	 * Sets the value for the given option key
	 * 
	 * @param string $insKey
	 * @param mixed $inmValue
	 * @return boolean
	 */
	public function setOpt( $insKey, $inmValue ) {
		if ( !isset( $this->m_aOptionsValues ) ) {
			$this->loadPluginOptions();
		}
		$this->m_aOptionsValues[ $insKey ] = $inmValue;
		return true;
	}

	/**
	 * @param string $insKey
	 * @return Ambigous <boolean, multitype:>
	 */
	public function getOpt( $insKey ) {
		if ( !isset( $this->m_aOptionsValues ) ) {
			$this->initOptions();
		}
		return isset( $this->m_aOptionsValues[ $insKey ] )? $this->m_aOptionsValues[ $insKey ] : false;
	}
	
	public function getOptions() {
		if ( !isset( $this->m_aOptions ) ) {
			$this->initOptions();
		}
		$this->buildPluginOptions();
		return $this->m_aOptions;
	}
	
	public function getOptionValues() {
		if ( !isset( $this->m_aOptionsValues ) ) {
			$this->initOptions();
		}
		return $this->m_aOptionsValues;
	}
	
	public function collateAllFormInputsForAllOptions() {
	
		$this->initOptions();

		$aToJoin = array();
		foreach ( $this->m_aOptions as $aOptionsSection ) {
			
			if ( empty( $aOptionsSection ) ) {
				continue;
			}
			foreach ( $aOptionsSection['section_options'] as $aOption ) {
				list($sKey, $fill1, $fill2, $sType) =  $aOption;
				$aToJoin[] = $sType.':'.$sKey;
			}
		}
		return implode( self::CollateSeparator, $aToJoin );
	}
	
	/**
	 * Handles the building of all options, processing their meta data and their values.
	 * @return array
	 */
	protected function initOptions() {
		
		// We have non-UI options that all plugin option objects should maintain.
		// Objects that extend this should merge their other non-ui options with this.
		$aNonUiOptions = array(
			'current_plugin_version'
		);
		
		$this->loadPluginOptions();
		$this->buildPluginOptions();
		
		// Now handle some outside cases.
		/*
		// We only set the version here. When there is a plugin upgrade, we may externally update this.
		// This is so that upgrades are properly handled.
		if ( empty ( $this->m_aOptionsValues[ 'current_plugin_version' ] ) ) {
			$this->m_aOptionsValues[ 'current_plugin_version' ] = $this->m_sVersion;
		}
		*/
	}
	
	/**
	 * Loads the options and their stored values from the WordPress Options store.
	 */
	protected function loadPluginOptions() {
		if ( empty( $this->m_aOptionsValues ) ) {
			$this->m_aOptionsValues = $this->getOption( $this->m_aOptionsStoreName );
			if ( is_null( $this->m_aOptionsValues ) || !$this->m_aOptionsValues ) {
				$this->m_aOptionsValues = array();
			}
		}
	}

	protected function definePluginOptions() {
		$aMisc = array(
			'section_title' => 'Miscellaneous Plugin Options',
			'section_options' => array(
				array(
					'delete_on_deactivate',
					'',
					'N',
					'checkbox',
					'Delete Plugin Settings',
					'Delete All Plugin Settings Upon Plugin Deactivation',
					'Careful: Removes all plugin options when you deactivite the plugin.'
				),
			),
		);
		$this->m_aOptions = array( $aMisc );
	}

	/**
	 * Will initiate the plugin options structure for use by the UI builder.
	 * 
	 * It will also fill in $this->m_aOptionsValues with defaults where appropriate.
	 * 
	 * It doesn't set any values, just populates the array created in buildPluginOptions()
	 * with values stored.
	 * 
	 * It has to handle the conversion of stored values to data to be displayed to the user.
	 * 
	 * @param string $insUpdateKey - if only want to update a single key, supply it here.
	 */
	protected function buildPluginOptions( $insUpdateKey = '' ) {
		
		if ( !isset( $this->m_aOptions ) ) {
			$this->definePluginOptions();
		}

		$fQuit = false;
		foreach ( $this->m_aOptions as &$aOptionsSection ) {
			
			if ( empty( $aOptionsSection ) || !isset( $aOptionsSection['section_options'] ) ) {
				continue;
			}
			
			foreach ( $aOptionsSection['section_options'] as &$aOptionParams ) {
				
				list( $sOptionKey, $sOptionValue, $sOptionDefault, $sOptionType ) = $aOptionParams;
				
				if ( !empty( $insUpdateKey ) ) {
					if ( $insUpdateKey != $insUpdateKey ) {
						continue;
					}
					else {
						$fQuit = true;
					}
				}
				
				if ( isset( $this->m_aOptionsValues[ $sOptionKey ] ) ) {
					$mCurrentOptionVal = $this->m_aOptionsValues[ $sOptionKey ];
				}
				else {
					$mCurrentOptionVal = $sOptionDefault;
					$this->m_aOptionsValues[ $sOptionKey ] = $mCurrentOptionVal;
				}
				
				if ( $sOptionType == 'ip_addresses' ) {
					
					if ( empty( $mCurrentOptionVal ) ) {
						$mCurrentOptionVal = '';
					}
					else {
						$mCurrentOptionVal = implode( "\n", $this->convertIpListForDisplay( $mCurrentOptionVal ) );
					}
				}
				else if ( $sOptionType == 'comma_separated_lists' ) {
					
					if ( empty( $mCurrentOptionVal ) ) {
						$mCurrentOptionVal = '';
					}
					else {
						$aNewValues = array();
						foreach( $mCurrentOptionVal as $sPage => $aParams ) {
							$aNewValues[] = $sPage.', '. implode( ", ", $aParams );
						}
						$mCurrentOptionVal = implode( "\n", $aNewValues );
					}
				}
				$aOptionParams[1] = $mCurrentOptionVal;
				
				//small optimization when updating a single key.
				if ( $fQuit ) {
					return;
				}
			}
		}
		
		// Cater for Non-UI options that don't necessarily go through the UI
		if ( isset($this->m_aNonUiOptions) && is_array($this->m_aNonUiOptions) ) {
			foreach( $this->m_aNonUiOptions as $sOption ) {
				if ( !isset( $this->m_aOptionsValues[ $sOption ] ) ) {
					$this->m_aOptionsValues[ $sOption ] = '';
				}
			}
		}
	}
	
	/**
	 * Saves the options to the WordPress Options store.
	 * 
	 * It will also update the stored plugin options version.
	 */
	public function savePluginOptions( $infReinit = true ) {
		$this->loadPluginOptions();
		$this->updatePluginOptionsVersion();
		if ( $this->updateOption( $this->m_aOptionsStoreName, $this->m_aOptionsValues ) && $infReinit ) {
			$this->initOptions();
		}
		
		// Direct save options allow us to get fast access to certain values without loading the whole thing
		if ( is_array( $this->m_aDirectSaveOptions ) ) {
			foreach( $this->m_aDirectSaveOptions as $sOptionKey ) {
				$this->updateOption( $sOptionKey, $this->m_aOptionsValues[ $sOptionKey ] );
			}
		}
	}

	/**
	 * Will return the 'current_plugin_version' if it is set, 0.0 otherwise.
	 * 
	 * @return string
	 */
	public function getPluginOptionsVersion() {
		return ( empty( $this->m_aOptionsValues[ 'current_plugin_version' ] )? '0.0' : $this->m_aOptionsValues[ 'current_plugin_version' ] );
	}
	
	/**
	 * Updates the 'current_plugin_version' to the offical plugin version.
	 */
	protected function updatePluginOptionsVersion() {
		$this->m_aOptionsValues[ 'current_plugin_version' ] = $this->m_sVersion;
	}
	
	/**
	 * Deletes all the options including direct save.
	 */
	public function deletePluginOptions() {
		$this->loadPluginOptions();
		$this->deleteOption( $this->m_aOptionsStoreName );
		
		// Direct save options allow us to get fast access to certain values without loading the whole thing
		if ( isset($this->m_aDirectSaveOptions) && is_array( $this->m_aDirectSaveOptions ) ) {
			foreach( $this->m_aDirectSaveOptions as $sOptionKey ) {
				$this->deleteOption( $sOptionKey );
			}
		}
		// Independent options are those untouched by the User/UI that are saved elsewhere and directly to the WP Options table. They are "meta" options
		if ( isset($this->m_aIndependentOptions) && is_array( $this->m_aIndependentOptions ) ) {
			foreach( $this->m_aIndependentOptions as $sOptionKey ) {
				$this->deleteOption( $sOptionKey );
			}
		}
	}
	
	protected function convertIpListForDisplay( $inaIpList = array() ) {

		$aDisplay = array();
		if ( empty( $inaIpList ) || empty( $inaIpList['ips'] ) ) {
			return $aDisplay;
		}
		foreach( $inaIpList['ips'] as $sAddress ) {
			// offset=1 in the case that it's a range and the first number is negative on 32-bit systems
			$mPos = strpos( $sAddress, '-', 1 );
			
			if ( $mPos === false ) { //plain IP address
				$sDisplayText = long2ip( $sAddress );
			}
			else {
				//we "remove" the first character in case this might be '-'
				$aParts = array( substr( $sAddress, 0, 1 ), substr( $sAddress, 1 ) );
				list( $nStart, $nEnd ) = explode( '-', $aParts[1], 2 );
				$sDisplayText = long2ip( $aParts[0].$nStart ) .'-'. long2ip( $nEnd );
			}
			$sLabel = $inaIpList['meta'][ md5($sAddress) ];
			$sLabel = trim( $sLabel, '()' );
			$aDisplay[] = $sDisplayText . ' ('.$sLabel.')';
		}
		return $aDisplay;
	}

	/**
	 * @param string $sAllOptionsInput - comma separated list of all the input keys to be processed from the $_POST
	 * @return void|boolean
	 */
	public function updatePluginOptionsFromSubmit( $sAllOptionsInput ) {
	
		if ( empty( $sAllOptionsInput ) ) {
			return;
		}
		
		$aAllInputOptions = explode( self::CollateSeparator, $sAllOptionsInput);
		foreach ( $aAllInputOptions as $sInputKey ) {
			$aInput = explode( ':', $sInputKey );
			list( $sOptionType, $sOptionKey ) = $aInput;

			$sOptionValue = $this->getFromPost( $sOptionKey );
			if ( is_null($sOptionValue) ) {
	
				if ( $sOptionType == 'text' || $sOptionType == 'email' ) { //if it was a text box, and it's null, don't update anything
					continue;
				} else if ( $sOptionType == 'checkbox' ) { //if it was a checkbox, and it's null, it means 'N'
					$sOptionValue = 'N';
				} else if ( $sOptionType == 'integer' ) { //if it was a integer, and it's null, it means '0'
					$sOptionValue = 0;
				}
			}
			else { //handle any pre-processing we need to.
	
				if ( $sOptionType == 'integer' ) {
					$sOptionValue = intval( $sOptionValue );
				}
				else if ( $sOptionType == 'ip_addresses' ) { //ip addresses are textareas, where each is separated by newline
						
					if ( !class_exists('ICWP_DataProcessor') ) {
						require_once ( dirname(__FILE__).'/icwp-data-processor.php' );
					}
					$oProcessor = new ICWP_DataProcessor();
					$sOptionValue = $oProcessor->ExtractIpAddresses( $sOptionValue );
				}
				else if ( $sOptionType == 'email' && function_exists( 'is_email' ) && !is_email( $sOptionValue ) ) {
					$sOptionValue = '';
				}
				else if ( $sOptionType == 'comma_separated_lists' ) {
					if ( !class_exists('ICWP_DataProcessor') ) {
						require_once ( dirname(__FILE__).'/icwp-data-processor.php' );
					}
					$oProcessor = new ICWP_DataProcessor();
					$sOptionValue = $oProcessor->ExtractCommaSeparatedList( $sOptionValue );
				}
			}
			$this->m_aOptionsValues[ $sOptionKey ] = $sOptionValue;
		}
		return $this->savePluginOptions();
	}
	
	/**
	 * Should be over-ridden by each new class to handle upgrades.
	 * 
	 * Called upon construction and after plugin options are initialized.
	 */
	protected function updateHandler() { }
	
	/**
	 * @param array $inaNewOptions
	 */
	protected function mergeNonUiOptions( $inaNewOptions = array() ) {

		if ( !empty( $this->m_aNonUiOptions ) ) {
			$this->m_aNonUiOptions = array_merge( $this->m_aNonUiOptions, $inaNewOptions );
		}
		else {
			$this->m_aNonUiOptions = $inaNewOptions;
		}
	}
	
	/**
	 * Copies WordPress Options to the options array and optionally deletes the original.
	 * 
	 * @param array $inaOptions
	 * @param boolean $fDeleteOld
	 */
	protected function migrateOptions( $inaOptions, $fDeleteOld = false ) {
		foreach( $inaOptions as $sOptionKey ) {
			$mCurrentValue = $this->getOption( $sOptionKey );
			if ( $mCurrentValue === false ) {
				continue;
			}
			$this->setOpt( $sOptionKey, $mCurrentValue );
			if ( $fDeleteOld ) {
				$this->deleteOption( $sOptionKey );
			}
		}
	}
	
	protected function getVisitorIpAddress( $infAsLong = true ) {
		require_once( dirname(__FILE__).'/icwp-base-processor.php' );
		return ICWP_BaseProcessor_WPTB::GetVisitorIpAddress( $infAsLong );
	}
	
	/**
	 * @param string $insKey		-	the POST key
	 * @param string $insPrefix
	 * @return Ambigous <null, string>
	 */
	protected function getFromPost( $insKey, $insPrefix = null ) {
		$sKey = ( is_null( $insPrefix )? $this->m_sOptionPrefix : $insPrefix ) . $insKey;
		return ( isset( $_POST[ $sKey ] )? $_POST[ $sKey ]: null );
	}
	public function getOption( $insKey ) {
		return get_option( $this->m_sOptionPrefix.$insKey );
	}
	public function addOption( $insKey, $insValue ) {
		return add_option( $this->m_sOptionPrefix.$insKey, $insValue );
	}
	public function updateOption( $insKey, $insValue ) {
		return update_option( $this->m_sOptionPrefix.$insKey, $insValue );
	}
	public function deleteOption( $insKey ) {
		return delete_option( $this->m_sOptionPrefix.$insKey );
	}
}

endif;