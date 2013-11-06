<?php

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
 *
 */

if ( !class_exists('HLT_BootstrapLess') ):

class HLT_BootstrapLess_Base {
	
	const LessOptionsPrefix = 'less_';
	
	/**
	 * @var string
	 */
	protected $m_sBsDir;
	
	/**
	 * @var string
	 */
	protected $m_sOptionsKey;
	/**
	 * @var array
	 */
	protected $m_aAllBootstrapLessOptions;
	
	/**
	 * @param string $insBsDir
	 * @param string $insKey
	 */
	public function __construct( $insBsDir, $insKey ) {
		$this->m_sOptionsKey = $insKey;
		$this->m_sBsDir = $insBsDir;
	}
	
	protected function initPluginOptions() {
		
		$this->m_aAllBootstrapLessOptions = get_option( $this->m_sOptionsKey );

		if ( !empty($this->m_aAllBootstrapLessOptions) ) {
			return true;
		}
	
		//Basically if the array is empty from the WP Options table, create it from scratch.
		$this->m_aAllBootstrapLessOptions = array(
				array(
					'section_title' => 'Grays',
					'section_options' => array(
						array( self::LessOptionsPrefix.'gray-darker',	'', 'lighten(#000, 13.5%)',	'less_color',	'Darker Gray',	'@grayDarker' ),
						array( self::LessOptionsPrefix.'gray-dark',		'', 'lighten(#000, 20%)',	'less_color',	'Dark Gray',	'@grayDark' ),
						array( self::LessOptionsPrefix.'gray',			'', 'lighten(#000, 33.5%)',	'less_color',	'Gray',			'@gray' ),
						array( self::LessOptionsPrefix.'gray-light',	'', 'lighten(#000, 60%)',	'less_color',	'Light Gray',	'@grayLight' ),
						array( self::LessOptionsPrefix.'gray-lighter',	'', 'lighten(#000, 93.5%)',	'less_color',	'Lighter Gray',	'@grayLighter' ),
					)
				),
		
				array(
					'section_title' => 'Brand Colours',
					'section_options' => array(
						array( self::LessOptionsPrefix.'brand-primary', 	'', '#428bca',		'less_color',	'Colour: Primary',		'@brand-primary' ),				//@white
						array( self::LessOptionsPrefix.'brand-success', 	'', '#5cb85c',		'less_color',	'Colour: Success',		'@brand-success' ),				//@white
						array( self::LessOptionsPrefix.'brand-warning', 	'', '#f0ad4e',		'less_color',	'Colour: Success',		'@brand-warning' ),				//@white
						array( self::LessOptionsPrefix.'brand-danger', 		'', '#d9534f',		'less_color',	'Colour: Success',		'@brand-danger' ),				//@white
						array( self::LessOptionsPrefix.'brand-info', 		'', '#5bc0de',		'less_color',	'Colour: Success',		'@brand-info' ),				//@white
					)
				),
		
				array(
					'section_title' => 'Fonts, Colours & Links',
					'section_options' => array(
						array( self::LessOptionsPrefix.'body-bg',					'', '#fff',												'less_color',	'Body Background Colour',		'@body-bg' ), //@white
						array( self::LessOptionsPrefix.'text-color',				'', '@gray-dark',										'less_color',	'Text Colour',					'@text-color' ),
						array( self::LessOptionsPrefix.'link-color',				'', '@brand-primary',												'less_color',	'Link Colour',					'@link-color' ),
						array( self::LessOptionsPrefix.'link-hover-color',		 	'', 'darken(@link-color, 15%)',							'less_color',	'Link Hover Colour',			'@link-hover-color' ), //darken(@linkColor, 15%)
						array( self::LessOptionsPrefix.'font-size-base',			'', '14px',												'less_size',	'Font Size Base',				'@baseFontSize' ),
						array( self::LessOptionsPrefix.'font-size-large',			'', 'ceil(@font-size-base * 1.25)',						'less_size',	'Font Size Large',				'@baseFontSize' ),
						array( self::LessOptionsPrefix.'font-size-small',			'', 'ceil(@font-size-base * 0.85)',						'less_size',	'Font Size Small',				'@baseFontSize' ),
						array( self::LessOptionsPrefix.'line-height-base', 			'', '1.428571429',										'less_size',	'Base Line Height',				'@baseLineHeight' ),
						array( self::LessOptionsPrefix.'font-family-sans-serif',	'', '"Helvetica Neue", Helvetica, Arial, sans-serif',	'less_font',	'Fonts: Sans Serif',			'@font-family-sans-serif' ),
						array( self::LessOptionsPrefix.'font-family-serif',			'', 'Georgia, "Times New Roman", Times, serif',			'less_font',	'Fonts: Serif',					'@font-family-serif' ),
						array( self::LessOptionsPrefix.'font-family-monospace',		'', 'Monaco, Menlo, Consolas, "Courier New", monospace','less_font',	'Fonts: Monospace',				'@font-family-monospace' ),
						array( self::LessOptionsPrefix.'font-family-base',			'', '@font-family-sans-serif',							'less_font',	'Fonts: Base',					'@font-family-base' ),
					)
				),
		
				array(
						'section_title' => 'Button Styling',
						'section_options' => array(
							array( self::LessOptionsPrefix.'btn-default-color', 			'', '#333',								'less_color',	'Default Colour',			'@btn-default-color' ),
							array( self::LessOptionsPrefix.'btn-default-bg',				'', '#fff',								'less_color',	'Default Background',		'@btn-default-bg' ),
							array( self::LessOptionsPrefix.'btn-default-border',			'', '#ccc',								'less_color',	'Default Border Colour',	'@btn-default-border' ),
							array( 'spacer' ),
							array( self::LessOptionsPrefix.'btn-primary-color', 			'', '#fff',								'less_color',	'Primary Colour',			'@btn-primary-color' ),
							array( self::LessOptionsPrefix.'btn-primary-bg',				'', '@brand-primary',					'less_color',	'Primary Background',		'@btn-primary-bg' ),
							array( self::LessOptionsPrefix.'btn-primary-border',			'', 'darken(@btn-primary-bg, 5%)',		'less_color',	'Primary Border Colour',	'@btn-primary-border' ),
							array( 'spacer' ),
							array( self::LessOptionsPrefix.'btn-success-color', 			'', '#fff',								'less_color',	'Success Colour',			'@btn-success-color' ),
							array( self::LessOptionsPrefix.'btn-success-bg',				'', '@brand-success',					'less_color',	'Success Background',		'@btn-success-bg' ),
							array( self::LessOptionsPrefix.'btn-success-border',			'', 'darken(@btn-success-bg, 5%)',		'less_color',	'Success Border Colour',	'@btn-success-border' ),
							array( 'spacer' ),
							array( self::LessOptionsPrefix.'btn-warning-color', 			'', '#fff',								'less_color',	'Warning Colour',			'@btn-warning-color' ),
							array( self::LessOptionsPrefix.'btn-warning-bg',				'', '@brand-warning',					'less_color',	'Warning Background',		'@btn-warning-bg' ),
							array( self::LessOptionsPrefix.'btn-warning-border',			'', 'darken(@btn-warning-bg, 5%)',		'less_color',	'Warning Border Colour',	'@btn-warning-border' ),
							array( 'spacer' ),
							array( self::LessOptionsPrefix.'btn-danger-color', 				'', '#fff',								'less_color',	'Danger Colour',			'@btn-danger-color' ),
							array( self::LessOptionsPrefix.'btn-danger-bg',					'', '@brand-danger',					'less_color',	'Danger Background',		'@btn-danger-bg' ),
							array( self::LessOptionsPrefix.'btn-danger-border',				'', 'darken(@btn-danger-bg, 5%)',		'less_color',	'Danger Border Colour',		'@btn-danger-border' ),
							array( 'spacer' ),
							array( self::LessOptionsPrefix.'btn-info-color', 				'', '#fff',								'less_color',	'Info Colour',				'@btn-info-color' ),
							array( self::LessOptionsPrefix.'btn-info-bg',					'', '@brand-info',						'less_color',	'Info Background',			'@btn-info-bg' ),
							array( self::LessOptionsPrefix.'btn-info-border',				'', 'darken(@btn-info-bg, 5%)',			'less_color',	'Info Border Colour',		'@btn-info-border' ),
							
							array( self::LessOptionsPrefix.'btn-link-disabled-color',		'', '@gray-light',						'less_color',	'Disabled Link Colour',		'@btn-link-disabled-color' )
						)
				),

				array(
					'section_title' => 'Alerts and Form States',
					'section_options' => array(
						array( self::LessOptionsPrefix.'state-warning-text', 		'', '#c09853',			'less_color',			'Warning Text Colour' ),
						array( self::LessOptionsPrefix.'state-warning-bg',			'', '#fcf8e3',			'less_color',			'Warning Background Colour' ),
						array( self::LessOptionsPrefix.'state-warning-border',		'', 'darken(spin(@state-warning-bg, -10), 3%)',			'less_color',	'Warning Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'state-danger-text', 		'', '#b94a48',			'less_color',	'Error Text Colour' ),
						array( self::LessOptionsPrefix.'state-danger-bg',			'', '#f2dede',			'less_color',	'Error Background Colour' ),
						array( self::LessOptionsPrefix.'state-danger-border',		'', 'darken(spin(@state-danger-bg, -10), 3%)',			'less_color',	'Error Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'state-success-text', 		'', '#468847',			'less_color',	'Success Text Colour' ),
						array( self::LessOptionsPrefix.'state-success-bg',			'', '#dff0d8',			'less_color',	'Success Background Colour' ),
						array( self::LessOptionsPrefix.'state-success-border',		'', 'darken(spin(@state-success-bg, -10), 5%)',			'less_color',	'Success Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'state-info-text', 			'', '#3a87ad',			'less_color',	'Info Text Colour' ),
						array( self::LessOptionsPrefix.'state-info-bg',				'', '#d9edf7',			'less_color',	'Info Background Colour' ),
						array( self::LessOptionsPrefix.'state-info-border',			'', 'darken(spin(@state-info-bg, -10), 7%)',			'less_color',	'Info Border Colour' ),
						array( 'spacer' )
					)
				),

				array(
					'section_title' => 'Code',
					'section_options' => array(
						array( self::LessOptionsPrefix.'code-color', 		'', '#c7254e',			'less_color',			'Code Colour' ),
						array( self::LessOptionsPrefix.'code-bg',			'', '#f9f2f4',			'less_color',			'Code Background Colour' ),
						array( self::LessOptionsPrefix.'pre-color', 		'', '@gray-dark',		'less_color',			'PRE Colour' ),
						array( self::LessOptionsPrefix.'pre-bg',			'', '#f5f5f5',			'less_color',			'PRE Background Colour' ),
						array( self::LessOptionsPrefix.'pre-border-color',	'', '#ccc',				'less_color',			'PRE Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'state-danger-text', 		'', '#b94a48',			'less_color',	'Error Text Colour' ),
						array( self::LessOptionsPrefix.'state-danger-bg',			'', '#f2dede',			'less_color',	'Error Background Colour' ),
						array( self::LessOptionsPrefix.'state-danger-border',		'', 'darken(spin(@state-danger-bg, -10), 3%)',			'less_color',	'Error Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'state-success-text', 		'', '#468847',			'less_color',	'Success Text Colour' ),
						array( self::LessOptionsPrefix.'state-success-bg',			'', '#dff0d8',			'less_color',	'Success Background Colour' ),
						array( self::LessOptionsPrefix.'state-success-border',		'', 'darken(spin(@state-success-bg, -10), 5%)',			'less_color',	'Success Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'state-info-text', 			'', '#3a87ad',			'less_color',	'Info Text Colour' ),
						array( self::LessOptionsPrefix.'state-info-bg',				'', '#d9edf7',			'less_color',	'Info Background Colour' ),
						array( self::LessOptionsPrefix.'state-info-border',			'', 'darken(spin(@state-info-bg, -10), 7%)',			'less_color',	'Info Border Colour' ),
						array( 'spacer' )
					)
				),

				array(
					'section_title' => 'Media Queries Breakpoints',
					'section_options' => array(
						array( self::LessOptionsPrefix.'screen-xs', 	'', '480px',	'less_text',	'Extra small screen' ),
						array( self::LessOptionsPrefix.'screen-sm',		'', '768px',	'less_size',	'Small screen' ),
						array( self::LessOptionsPrefix.'screen-md',		'', '992px',	'less_size',	'Medium screen' ),
						array( self::LessOptionsPrefix.'screen-lg',		'', '1200px',	'less_size',	'Large screen' )
					)
				),
		
				array(
					'section_title' => 'The Grid',
					'section_options' => array(
						array( self::LessOptionsPrefix.'grid-columns', 				'', '12',				'less_text',	'Grid Columns' ),
						array( self::LessOptionsPrefix.'grid-gutter-width',			'', '30px',				'less_size',	'Grid Gutter Width' ),
						array( self::LessOptionsPrefix.'grid-float-breakpoint',		'', '@screen-sm',		'less_size',	'Navbar stops collapsing' )
					)
				),
		);
		return true;
	}
	
	public function getAllBootstrapLessOptions( $fPopulate = false ) {
		
		if ( $fPopulate ) {
			$this->processLessOptions('populate');
		}
		
		if ( empty($this->m_aAllBootstrapLessOptions) && !$this->initPluginOptions() ) {
			return;
		}
		
		return $this->m_aAllBootstrapLessOptions;
	}
	
	public function processLessOptions( $insFunction, $inaData = array() ) {

		if ( empty($insFunction) ) {
			return;
		}
		
		if ( $insFunction == 'delete') {
			delete_option( $this->m_sOptionsKey );
			$this->m_aAllBootstrapLessOptions = null;
			return;
		}
		
		if ( !$this->initPluginOptions() ) {
			return;
		}
		
		if ( $insFunction == 'populate' ) { //the previous IF populated
			return;
		}
		
		foreach ( $this->m_aAllBootstrapLessOptions as &$aLessSection ) {
			
			foreach ( $aLessSection['section_options'] as &$aOptionParams ) {
				
				list( $sOptionKey, $sOptionSaved, $sOptionDefault, $sOptionType, $sOptionHumanName ) = $aOptionParams;
				
				if ( $sOptionKey == 'spacer' ) {
					continue;
				}
				
				switch ( $insFunction ) {
		
					case 'process-post':
						if ( empty($inaData) ) {
							return;
						}
						$sPostValue = $_POST[ $inaData['options-prefix'].$sOptionKey ];
						if ( $sOptionType == 'less_color' ) {
							
							if ( preg_match( '/^[a-fA-F0-9]{3,6}$/', $sPostValue ) ) {
		
								$sPostValue = '#'.$sPostValue;
							
							} else {
								//validate LESS?
							}
						} else if ( $sOptionType == 'less_size' ) {
							if ( preg_match( '/^\d+$/', $sPostValue ) ) {
								$sPostValue = $sPostValue.'px';
							}
							if ( !preg_match( '/^\d+(px|em|pt)$/', $sPostValue ) ) {
								$sPostValue = $sOptionDefault;
							}
						}
						
						$aOptionParams[1] = stripslashes( $sPostValue );
						
						break 1;
						
					case 'rewrite-variablesless':
						$sBootstrapLessVar = str_replace( self::LessOptionsPrefix, '', $sOptionKey );
						$sOptionValue = ( $sOptionSaved == '' )? $sOptionDefault : $sOptionSaved;
						$inaData['file-contents'] = preg_replace( '/^\s*(@'.$sBootstrapLessVar.':\s*)([^;]+)(;)\s*$/im', '${1}'.$sOptionValue.'${3}', $inaData['file-contents'] );
						break 1;
						
				}//switch
			
			}//foreach
		}//foreach
		
		if ( $insFunction == 'process-post' ) {
			update_option( $this->m_sOptionsKey, $this->m_aAllBootstrapLessOptions );
		}
		
		if ( $insFunction == 'rewrite-variablesless' ) {
			return $inaData['file-contents'];
		}
		
	}
	
	public function resetToDefaultAllLessOptions() {

		$this->processLessOptions( 'delete' );
		$this->reWriteVariablesLess( true );
		$this->compileAllBootstrapLess();

	}
	
	public function processNewLessOptions( $sOptionsPrefix = '', $infUseOriginalLessFile = TRUE ) {
		$this->processLessOptions( 'process-post', array('options-prefix' => $sOptionsPrefix) );
		if ( $this->reWriteVariablesLess( $infUseOriginalLessFile ) ) {
			$this->compileAllBootstrapLess();
		}
	}
	
	/**
	 * @param $insBootstrapDir
	 * @param $infUseOriginalLessFile - boolean on whether to use the original less file as the template or not. Defaults to TRUE
	 */
	public function reWriteVariablesLess( $infUseOriginalLessFile = TRUE ) {

		$fSuccess = true;
		
		$sFilePathVariablesLess = $this->m_sBsDir.'less'.ICWP_DS.'variables.less';
		if ( $infUseOriginalLessFile ) {
			$sContents = file_get_contents( $sFilePathVariablesLess.'.orig' );
		}
		else {
			$sContents = file_get_contents( $sFilePathVariablesLess );
		}
		
		if ( !$sContents ) {
			//The Variable.less file couldn't be read: bail!
			$fSuccess = false;
		}
		else {
			$sContents = $this->processLessOptions( 'rewrite-variablesless', array( 'file-contents' => $sContents) );
			file_put_contents( $sFilePathVariablesLess, $sContents );
		}
		return $fSuccess;
	
	}
	
	public function compileAllBootstrapLess() {
		
		if ( empty( $this->m_sBsDir ) ) {
			return false;
		}
		$this->compileLess( 'bootstrap' );
	}
	
	/**
	 * 
	 * @param $insBootstrapDir
	 * @param $insCompileTarget - currently only 'bootstrap'
	 */
	public function compileLess( $insCompileTarget = 'bootstrap' ) {
		
		if ( empty($this->m_sBsDir) ) {
			return false;
		}
		
		$sFilePathToLess = $this->m_sBsDir.'less'.ICWP_DS.$insCompileTarget.'.less';
		
		//parse LESS
		$this->includeLess();
		if ( lessc::$VERSION != 'v0.4.0' ) { //not running a supported version of the less compiler for bootstrap
			return false;
		}
		
		// New method
		$oLessCompiler = new lessc();
		
		// Original method
		//$oLessCompiler = new lessc( $sFilePathToLess );
		
		$sCompiledCss = '';
		
		try {
			/**
			 * New Method (to use new lessphp interface)
			 * 
			 * 1. Determine target filename(s)
			 * 2. Compile + write to disk
			 * 3. Compile + compress + write to disk
			 */
			
			//Remove 'responsive' as an option since 3.0.0 as it's not necessary
			if ( $insCompileTarget == 'bootstrap' ) {
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap.less';
			}
			else { //Are there others?
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap.less';
			}
			
			// Write normal CSS
			$oLessCompiler->compileFile( $sFilePathToLess, $sLessFile.'.css' );
			
			// Write compress CSS
			$oLessCompiler = new lessc(); //as of version 0.4.0 I have to recreate the object.
			$oLessCompiler->setFormatter( "compressed" );
			$oLessCompiler->compileFile( $sFilePathToLess, $sLessFile.'.min.css' );
			
			/**
			 * Original method
			 * 
			 * 1. Compile
			 * 2. Determine target filename(s)
			 * 3. Write to disk
			 * 4. Compress/Minify
			 * 5. Write to disk
			 */
			/*
			$sCompiledCss = $oLessCompiler->parse();
			
			if ($insCompileTarget == 'responsive') {
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap-responsive.less';
			} else if ($insCompileTarget == 'bootstrap') {
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap.less';
			} else { //Are there others?
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap.less';
			}
			
			file_put_contents( $sLessFile.'.css', $sCompiledCss );
		
			//Basic Minify
			$sCompiledCss = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $sCompiledCss);
			file_put_contents( $sLessFile.'.min.css', $sCompiledCss );
			*/
		}
		catch ( Exception $oE ) {
			echo "lessphp fatal error: ".$oE->getMessage();
		}
	}

	public function handleUpgrade( $insCurrentVersion ) { }
	
	protected function includeLess() {
		if ( !class_exists( 'lessc' ) ) {
			include_once( dirname(__FILE__).'/../inc/lessc/lessc.inc.php' );
		}
	}
}

endif;
