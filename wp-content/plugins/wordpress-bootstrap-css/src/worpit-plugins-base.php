<?php

require_once( dirname(__FILE__).'/icwp-pure-base.php' );

if ( !class_exists('ICWP_WTB_Base_Plugin') ):

class ICWP_WTB_Base_Plugin extends ICWP_Pure_Base_WPTB {

	const ParentName		= 'Twitter Bootstrap';

	protected $m_aAllPluginOptions;
	
	static protected $m_fUpdateSuccessTracker;
	static protected $m_aFailedUpdateOptions;
	
	public function __construct() {
		parent::__construct();
		
		/**
		 * We make the assumption that all settings updates are successful until told otherwise
		 * by an actual failing update_option call.
		 */
		self::$m_fUpdateSuccessTracker = true;
		self::$m_aFailedUpdateOptions = array();

		$this->m_sParentMenuIdSuffix = 'base';
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
	 * Reads the current value for ALL plugin options from the WP options db.
	 * 
	 * Assumes the standard plugin options array structure. Over-ride to change.
	 * 
	 * NOT automatically executed on any hooks.
	 */
	protected function populateAllPluginOptions() {

		if ( empty($this->m_aAllPluginOptions) && !$this->initPluginOptions() ) {
			return false;
		}
		$this->PopulatePluginOptions( $this->m_aAllPluginOptions );
	}
	
	public function PopulatePluginOptions( &$inaAllOptions ) {

		if ( empty($inaAllOptions) ) {
			return false;
		}
		foreach ( $inaAllOptions as &$aOptionsSection ) {
			$this->PopulatePluginOptionsSection($aOptionsSection);
		}
	}
	
	public function PopulatePluginOptionsSection( &$inaOptionsSection ) {

		if ( empty($inaOptionsSection) ) {
			return false;
		}
		foreach ( $inaOptionsSection['section_options'] as &$aOptionParams ) {
			
			list( $sOptionKey, $sOptionCurrent, $sOptionDefault ) = $aOptionParams;
			$sCurrentOptionVal = $this->getOption( $sOptionKey );
			$aOptionParams[1] = ($sCurrentOptionVal == '' )? $sOptionDefault : $sCurrentOptionVal;
		}
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

	public function updateOption( $insKey, $inmValue ) {
		$fResult = parent::updateOption( $insKey, $inmValue );
		if ( !$fResult ) {
			self::$m_fUpdateSuccessTracker = false;
			self::$m_aFailedUpdateOptions[] = $this->m_sOptionPrefix.$insKey;
		}
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
		
	}

	protected function getAnswerFromPost( $insKey, $insPrefix = null ) {
		if ( is_null( $insPrefix ) ) {
			$insKey = $this->m_sOptionPrefix.$insKey;
		}
		return ( isset( $_POST[$insKey] )? $_POST[$insKey]: null );
	}

}//CLASS ICWP_WTB_Base_Plugin

endif;

