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

if ( !class_exists('ICWP_OptionsHandler_Wptbless') ):

class ICWP_OptionsHandler_Wptbless extends ICWP_OptionsHandler_Base_WPTB {
	
	public function definePluginOptions() {

		$this->m_aDirectSaveOptions = array();
		
		$aNonUiOptions = array(
			'feedback_admin_notice'
		);
		$this->mergeNonUiOptions( $aNonUiOptions );
		
		$this->m_aOptions = array(
			array(
				'section_title' => 'Grays',
				'section_options' => array(
					array( 'black', 		'', '#000',	'less_color',	'Black',		'@black' ),
					array( 'grayDarker',	'', '#222',	'less_color',	'Darker Gray',	'@grayDarker' ),
					array( 'grayDark',		'', '#333',	'less_color',	'Dark Gray',	'@grayDark' ),
					array( 'gray',			'', '#555',	'less_color',	'Gray',			'@gray' ),
					array( 'grayLight',		'', '#999',	'less_color',	'Light Gray',	'@grayLight' ),
					array( 'grayLighter',	'', '#eee',	'less_color',	'Lighter Gray',	'@grayLighter' ),
					array( 'white',			'', '#fff',	'less_color',	'White',		'@white' )
				)
			),
	
			array(
				'section_title' => 'Fonts, Colours & Links',
				'section_options' => array(
					array( 'bodyBackground',	'', '@white',			'less_color',		'Body Background Colour',			'@bodyBackground' ), //@white
					array( 'textColor',			'', '@grayDark',		'less_color',		'Text Colour',						'@textColor' ),
					array( 'linkColor',			'', '#08c',				'less_color',		'Link Colour',						'@linkColor' ),
					array( 'linkColorHover', 	'', 'darken(@linkColor, 15%)',	'less_color',	'Link Hover Colour',	'@linkColorHover' ), //darken(@linkColor, 15%)
					array( 'blue', 				'', '#049cdb',			'less_color',		'Blue',								'@blue' ),
					array( 'blueDark',			'', '#0064cd',			'less_color',		'Dark Blue',						'@blueDark' ),
					array( 'green',				'', '#46a546',			'less_color',		'Green',							'@green' ),
					array( 'red',				'', '#9d261d',			'less_color',		'Red',								'@red' ),
					array( 'yellow',			'', '#ffc40d',			'less_color',		'Yellow',							'@yellow' ),
					array( 'orange',			'', '#f89406',			'less_color',		'Orange',							'@orange' ),
					array( 'pink', 				'', '#c3325f',			'less_color',		'Pink',								'@pink' ),
					array( 'purple', 			'', '#7a43b6',			'less_color',		'Purple',							'@purple' ),
					array( 'baseFontSize',		'', '13px',				'less_size',			'Font Size',						'@baseFontSize' ),
					array( 'baseLineHeight', 	'', '18px',				'less_size',			'Base Line Height',					'@baseLineHeight' ),
					array( 'baseFontFamily',	'', '"Helvetica Neue", Helvetica, Arial, sans-serif',	'less_font',	'Fonts',	'@baseFontFamily' ),
					array( 'altFontFamily',		'', 'Georgia, "Times New Roman", Times, serif',	'less_font',	'Alternative Fonts',	'@altFontFamily' ),
				)
			),
	
			array(
				'section_title' => 'Button Styling',
				'section_options' => array(
					array( 'btnBackground', 				'', '@white',							'less_color',	'Background' ),				//@white
					array( 'btnBackgroundHighlight',		'', 'darken(@white, 10%)',				'less_color',	'Background Highlight' ),	//darken(@white, 10%);
					array( 'btnPrimaryBackground',			'', '@linkColor',						'less_color',	'Primary Btn Background' ),	//@linkColor
					array( 'btnPrimaryBackgroundHighlight',	'', 'spin(@btnPrimaryBackground, 15%)',	'less_color',	'Primary Btn Highlight' ),	//spin(@btnPrimaryBackground, 15%)
					array( 'btnInfoBackground',				'', '#5bc0de',							'less_color',	'Info Btn Background' ),
					array( 'btnInfoBackgroundHighlight',	'', '#2f96b4',							'less_color',	'Info Btn Highlight' ),
					array( 'btnSuccessBackground',			'', '#62c462',							'less_color',	'Success Btn Background' ),
					array( 'btnSuccessBackgroundHighlight',	'', '#51a351',							'less_color',	'Success Btn Highlight' ),
					array( 'btnWarningBackground',			'', 'lighten(@orange, 15%)',			'less_color',	'Warning Btn Background' ),	//lighten(@orange, 15%)
					array( 'btnWarningBackgroundHighlight',	'', '@orange',							'less_color',	'Warning Btn Highlight' ),	//@orange
					array( 'btnDangerBackground',			'', '#ee5f5b',							'less_color',	'Danger Btn Background' ),
					array( 'btnDangerBackgroundHighlight',	'', '#bd362f',							'less_color',	'Danger Btn Highlight' ),
					array( 'btnInverseBackground',			'', '@gray',							'less_color',	'Inverse Btn Background' ),	//@gray
					array( 'btnInverseBackgroundHighlight',	'', '@grayDarker',						'less_color',	'Inverse Btn Highlight' ),	//@grayDarker
					array( 'btnBorder',						'', 'darken(@white, 20%)',				'less_color',	'Button Border' ),			//darken(@white, 20%)
				)
			),
	
			array(
				'section_title' => 'Alerts and Form States',
				'section_options' => array(
					array( 'warningText', 		'', '#c09853',			'less_color',	'Warning Text Colour' ),
					array( 'warningBackground',	'', '#fcf8e3',			'less_color',	'Warning Background Colour' ),
					array( 'warningBorder',		'', 'darken(spin(@warningBackground, -10), 3%)',			'less_color',	'Warning Border Colour' ),
					array( 'spacer' ),
					array( 'errorText', 		'', '#b94a48',			'less_color',	'Error Text Colour' ),
					array( 'errorBackground',	'', '#f2dede',			'less_color',	'Error Background Colour' ),
					array( 'errorBorder',		'', 'darken(spin(@errorBackground, -10), 3%)',			'less_color',	'Error Border Colour' ),
					array( 'spacer' ),
					array( 'successText', 		'', '#468847',			'less_color',	'Success Text Colour' ),
					array( 'successBackground',	'', '#dff0d8',			'less_color',	'Success Background Colour' ),
					array( 'successBorder',		'', 'darken(spin(@successBackground, -10), 5%)',			'less_color',	'Success Border Colour' ),
					array( 'spacer' ),
					array( 'infoText', 			'', '#3a87ad',			'less_color',	'Info Text Colour' ),
					array( 'infoBackground',	'', '#d9edf7',			'less_color',	'Info Background Colour' ),
					array( 'infoBorder',		'', 'darken(spin(@infoBackground, -10), 7%)',			'less_color',	'Info Border Colour' ),
					array( 'spacer' )
				)
			),
	
			array(
				'section_title' => 'The Grid',
				'section_options' => array(
					array( 'gridColumns', 		'', '12',			'less_text',	'Grid Columns' ),
					array( 'gridColumnWidth',	'', '60px',			'less_size',	'Grid Column Width' ),
					array( 'gridGutterWidth',	'', '20px',			'less_size',	'Grid Gutter Width' ),
					array( 'gridRowWidth',		'', '(@gridColumns * @gridColumnWidth) + (@gridGutterWidth * (@gridColumns - 1))',	'less_size',	'Grid Row Width' )
				)
			)
		);
	}

	public function updateHandler() {
		
		$sCurrentVersion = $this->getPluginOptionsVersion();

		if ( version_compare( $sCurrentVersion, '3.0.0-2', '<' ) ) {
			$aSettingsKey = array();
		}
	}
}

endif;