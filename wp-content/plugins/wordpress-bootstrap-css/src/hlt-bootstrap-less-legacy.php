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

require_once( dirname(__FILE__).'/hlt-bootstrap-less-base.php' );

if ( !class_exists('HLT_BootstrapLess') ):

class HLT_BootstrapLess extends HLT_BootstrapLess_Base {
	
	const LessOptionsPrefix = 'less_';
	
	static public $LESS_PREFIX;
	static public $LESS_OPTIONS_DB_KEY = 'all_less_options';

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
								array( self::LessOptionsPrefix.'black', 		'', '#000',	'less_color',	'Black',		'@black' ),
								array( self::LessOptionsPrefix.'grayDarker',	'', '#222',	'less_color',	'Darker Gray',	'@grayDarker' ),
								array( self::LessOptionsPrefix.'grayDark',		'', '#333',	'less_color',	'Dark Gray',	'@grayDark' ),
								array( self::LessOptionsPrefix.'gray',			'', '#555',	'less_color',	'Gray',			'@gray' ),
								array( self::LessOptionsPrefix.'grayLight',		'', '#999',	'less_color',	'Light Gray',	'@grayLight' ),
								array( self::LessOptionsPrefix.'grayLighter',	'', '#eee',	'less_color',	'Lighter Gray',	'@grayLighter' ),
								array( self::LessOptionsPrefix.'white',			'', '#fff',	'less_color',	'White',		'@white' )
						)
				),
	
				array(
						'section_title' => 'Fonts, Colours & Links',
						'section_options' => array(
						array( self::LessOptionsPrefix.'bodyBackground',	'', '@white',			'less_color',		'Body Background Colour',			'@bodyBackground' ), //@white
						array( self::LessOptionsPrefix.'textColor',			'', '@grayDark',		'less_color',		'Text Colour',						'@textColor' ),
						array( self::LessOptionsPrefix.'linkColor',			'', '#08c',				'less_color',		'Link Colour',						'@linkColor' ),
						array( self::LessOptionsPrefix.'linkColorHover', 	'', 'darken(@linkColor, 15%)',			'less_color',	'Link Hover Colour',	'@linkColorHover' ), //darken(@linkColor, 15%)
						array( self::LessOptionsPrefix.'blue', 				'', '#049cdb',			'less_color',		'Blue',								'@blue' ),
						array( self::LessOptionsPrefix.'blueDark',			'', '#0064cd',			'less_color',		'Dark Blue',						'@blueDark' ),
						array( self::LessOptionsPrefix.'green',				'', '#46a546',			'less_color',		'Green',							'@green' ),
						array( self::LessOptionsPrefix.'red',				'', '#9d261d',			'less_color',		'Red',								'@red' ),
						array( self::LessOptionsPrefix.'yellow',			'', '#ffc40d',			'less_color',		'Yellow',							'@yellow' ),
						array( self::LessOptionsPrefix.'orange',			'', '#f89406',			'less_color',		'Orange',							'@orange' ),
						array( self::LessOptionsPrefix.'pink', 				'', '#c3325f',			'less_color',		'Pink',								'@pink' ),
						array( self::LessOptionsPrefix.'purple', 			'', '#7a43b6',			'less_color',		'Purple',							'@purple' ),
						array( self::LessOptionsPrefix.'baseFontSize',		'', '13px',				'less_size',			'Font Size',						'@baseFontSize' ),
						array( self::LessOptionsPrefix.'baseLineHeight', 	'', '18px',				'less_size',			'Base Line Height',					'@baseLineHeight' ),
						array( self::LessOptionsPrefix.'baseFontFamily',	'', '"Helvetica Neue", Helvetica, Arial, sans-serif',	'less_font',	'Fonts',	'@baseFontFamily' ),
						array( self::LessOptionsPrefix.'altFontFamily',		'', 'Georgia, "Times New Roman", Times, serif',	'less_font',	'Alternative Fonts',	'@altFontFamily' ),
						)
						),
	
						array(
						'section_title' => 'Button Styling',
						'section_options' => array(
						array( self::LessOptionsPrefix.'btnBackground', 				'', '@white',							'less_color',	'Background' ),				//@white
						array( self::LessOptionsPrefix.'btnBackgroundHighlight',		'', 'darken(@white, 10%)',				'less_color',	'Background Highlight' ),	//darken(@white, 10%);
						array( self::LessOptionsPrefix.'btnPrimaryBackground',			'', '@linkColor',						'less_color',	'Primary Btn Background' ),	//@linkColor
						array( self::LessOptionsPrefix.'btnPrimaryBackgroundHighlight',	'', 'spin(@btnPrimaryBackground, 15%)',	'less_color',	'Primary Btn Highlight' ),	//spin(@btnPrimaryBackground, 15%)
						array( self::LessOptionsPrefix.'btnInfoBackground',				'', '#5bc0de',							'less_color',	'Info Btn Background' ),
						array( self::LessOptionsPrefix.'btnInfoBackgroundHighlight',	'', '#2f96b4',							'less_color',	'Info Btn Highlight' ),
						array( self::LessOptionsPrefix.'btnSuccessBackground',			'', '#62c462',							'less_color',	'Success Btn Background' ),
						array( self::LessOptionsPrefix.'btnSuccessBackgroundHighlight',	'', '#51a351',							'less_color',	'Success Btn Highlight' ),
						array( self::LessOptionsPrefix.'btnWarningBackground',			'', 'lighten(@orange, 15%)',			'less_color',	'Warning Btn Background' ),	//lighten(@orange, 15%)
						array( self::LessOptionsPrefix.'btnWarningBackgroundHighlight',	'', '@orange',							'less_color',	'Warning Btn Highlight' ),	//@orange
						array( self::LessOptionsPrefix.'btnDangerBackground',			'', '#ee5f5b',							'less_color',	'Danger Btn Background' ),
						array( self::LessOptionsPrefix.'btnDangerBackgroundHighlight',	'', '#bd362f',							'less_color',	'Danger Btn Highlight' ),
						array( self::LessOptionsPrefix.'btnInverseBackground',			'', '@gray',							'less_color',	'Inverse Btn Background' ),	//@gray
						array( self::LessOptionsPrefix.'btnInverseBackgroundHighlight',	'', '@grayDarker',						'less_color',	'Inverse Btn Highlight' ),	//@grayDarker
						array( self::LessOptionsPrefix.'btnBorder',						'', 'darken(@white, 20%)',				'less_color',	'Button Border' ),			//darken(@white, 20%)
						)
						),
	
						array(
						'section_title' => 'Alerts and Form States',
						'section_options' => array(
						array( self::LessOptionsPrefix.'warningText', 		'', '#c09853',			'less_color',	'Warning Text Colour' ),
						array( self::LessOptionsPrefix.'warningBackground',	'', '#fcf8e3',			'less_color',	'Warning Background Colour' ),
						array( self::LessOptionsPrefix.'warningBorder',		'', 'darken(spin(@warningBackground, -10), 3%)',			'less_color',	'Warning Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'errorText', 		'', '#b94a48',			'less_color',	'Error Text Colour' ),
						array( self::LessOptionsPrefix.'errorBackground',	'', '#f2dede',			'less_color',	'Error Background Colour' ),
						array( self::LessOptionsPrefix.'errorBorder',		'', 'darken(spin(@errorBackground, -10), 3%)',			'less_color',	'Error Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'successText', 		'', '#468847',			'less_color',	'Success Text Colour' ),
						array( self::LessOptionsPrefix.'successBackground',	'', '#dff0d8',			'less_color',	'Success Background Colour' ),
						array( self::LessOptionsPrefix.'successBorder',		'', 'darken(spin(@successBackground, -10), 5%)',			'less_color',	'Success Border Colour' ),
						array( 'spacer' ),
						array( self::LessOptionsPrefix.'infoText', 			'', '#3a87ad',			'less_color',	'Info Text Colour' ),
						array( self::LessOptionsPrefix.'infoBackground',	'', '#d9edf7',			'less_color',	'Info Background Colour' ),
						array( self::LessOptionsPrefix.'infoBorder',		'', 'darken(spin(@infoBackground, -10), 7%)',			'less_color',	'Info Border Colour' ),
						array( 'spacer' )
						)
						),
	
						array(
						'section_title' => 'The Grid',
						'section_options' => array(
						array( self::LessOptionsPrefix.'gridColumns', 		'', '12',			'less_text',	'Grid Columns' ),
						array( self::LessOptionsPrefix.'gridColumnWidth',	'', '60px',			'less_size',	'Grid Column Width' ),
						array( self::LessOptionsPrefix.'gridGutterWidth',	'', '20px',			'less_size',	'Grid Gutter Width' ),
						array( self::LessOptionsPrefix.'gridRowWidth',		'', '(@gridColumns * @gridColumnWidth) + (@gridGutterWidth * (@gridColumns - 1))',	'less_size',	'Grid Row Width' )
						)
						),
		);
		return true;
	}
	
	public function compileAllBootstrapLess() {
		parent::compileAllBootstrapLess();
		$this->compileLess( 'responsive' );
	}
	
	/**
	 * @param $insCompileTarget - currently only either 'bootstrap' or 'responsive'
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
			
			if ( $insCompileTarget == 'responsive' ) {
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap-responsive.less';
			}
			else if ($insCompileTarget == 'bootstrap') {
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap.less';
			}
			else { //Are there others?
				$sLessFile = $this->m_sBsDir.'css'.ICWP_DS.'bootstrap.less';
			}
			
			// Write normal CSS
			$oLessCompiler = new lessc();
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

	
}

endif;
