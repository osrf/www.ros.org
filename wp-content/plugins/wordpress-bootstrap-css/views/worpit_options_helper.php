<?php

function getIsHexColour($insColour) {
	return preg_match( '/^#[a-fA-F0-9]{3,6}$/', $insColour );
}

function printAllPluginOptionsForm( $inaAllPluginOptions, $insVarPrefix = '', $iOptionsPerRow = 1 ) {
	
	if ( empty($inaAllPluginOptions) ) {
		return;
	}

	$iRowWidth = 8; //8 spans.
	$iOptionWidth = $iRowWidth / $iOptionsPerRow;
	$sOptionValue;
	
	//Take each Options Section in turn
	foreach ( $inaAllPluginOptions as $sOptionSection ) {
		
		$sRowId = str_replace( ' ', '', $sOptionSection['section_title'] );
		//Print the Section Title
		echo '
				<div class="row" id="'.$sRowId.'">
					<div class="span9" style="margin-left:0px">
						<fieldset>
							<legend>'.$sOptionSection['section_title'].'</legend>
		';
		
		$rowCount = 1;
		$iOptionCount = 0;
		//Print each option in the option section
		foreach ( $sOptionSection['section_options'] as $aOption ) {
			
			$iOptionCount = $iOptionCount % $iOptionsPerRow;

			if ( $iOptionCount == 0 ) {
				echo '
				<div class="row row_number_'.$rowCount.'">';
			}
			
			echo getPluginOptionSpan( $aOption, $iOptionWidth, $insVarPrefix );

			$iOptionCount++;

			if ( $iOptionCount == $iOptionsPerRow ) {
				echo '
				</div> <!-- / options row -->';
				$rowCount++;
			}
	
		}//foreach option
	
		echo '
					</fieldset>
				</div>
			</div>
		';
		/*
		//ensure the intermediate save button is not printed at the end.
		end($inaAllPluginOptions);
		$skey = key($inaAllPluginOptions);
		if ( $sOptionSection['section_title'] != $skey ) {
			echo '
				<div class="form-actions">
					<button type="submit" class="btn btn-primary" name="submit" '.($hlt_compiler_enabled ? '':' disabled').'>'. _hlt__( 'Save All Settings' ).'</button>
				</div>
			';
		}
		*/

	}//foreach section

}//printAllPluginOptionsForm

function getPluginOptionSpan( $inaOption, $iSpanSize, $insVarPrefix = '' ) {
	
	list( $sOptionKey, $sOptionSaved, $sOptionDefault, $mOptionType, $sOptionHumanName, $sOptionTitle, $sOptionHelpText ) = $inaOption;
	
	if ( $sOptionKey == 'spacer' ) {
		$sHtml = '
			<div class="span'.$iSpanSize.'">
			</div>
		';
	} else {
	
		$sSpanId = 'span_'.$insVarPrefix.$sOptionKey;
		$sHtml = '
			<div class="span'.$iSpanSize.'" id="'.$sSpanId.'">
				<div class="control-group">
					<label class="control-label" for="'.$insVarPrefix.$sOptionKey.'">'.$sOptionHumanName.'<br /></label>
					<div class="controls">
					  <div class="option_section'.( ($sOptionSaved == 'Y')? ' selected_item':'' ).'" id="option_section_'.$insVarPrefix.$sOptionKey.'">
						<label>
		';
		$sAdditionalClass = '';
		$sTextInput = '';
		$sChecked = '';
		$sHelpSection = '';
		
		if ( $mOptionType === 'checkbox' ) {
			
			$sChecked = ( $sOptionSaved == 'Y' )? 'checked="checked"' : '';
			
			$sHtml .= '
				<input '.$sChecked.'
						type="checkbox"
						name="'.$insVarPrefix.$sOptionKey.'"
						value="Y"
						class="'.$sAdditionalClass.'"
						id="'.$insVarPrefix.$sOptionKey.'" />
						'.$sOptionTitle;
			
			$sOptionHelpText = '<p class="help-block">'.$sOptionHelpText.'</p>';

		} else if ( $mOptionType === 'text' ) {
			$sTextInput = esc_attr( $sOptionSaved );
			$sHtml .= '
				<p>'.$sOptionTitle.'</p>
				<input type="text"
						name="'.$insVarPrefix.$sOptionKey.'"
						value="'.$sTextInput.'"
						placeholder="'.$sTextInput.'"
						id="'.$insVarPrefix.$sOptionKey.'"
						class="span5" />';
			
			$sOptionHelpText = '<p class="help-block">'.$sOptionHelpText.'</p>';
			
		} else if ( is_array($mOptionType) ) { //it's a select, or radio
			
			$sInputType = array_shift($mOptionType);

			if ( $sInputType == 'select' ) {
				$sHtml .= '<p>'.$sOptionTitle.'</p>
				<select id="'.$insVarPrefix.$sOptionKey.'" name="'.$insVarPrefix.$sOptionKey.'">';
			}
			
			foreach( $mOptionType as $aInput ) {
				
				$sHtml .= '
					<option value="'.$aInput[0].'" id="'.$insVarPrefix.$sOptionKey.'_'.$aInput[0].'"' . (( $sOptionSaved == $aInput[0] )? ' selected="selected"' : '') .'>'. $aInput[1].'</option>';
			}
			
			if ($sInputType == 'select') {
				$sHtml .= '
				</select>';
			}
			
			$sOptionHelpText = '<p class="help-block">'.$sOptionHelpText.'</p>';

		} else if ( strpos( $mOptionType, 'less_' ) === 0 ) {	//dealing with the LESS compiler options

			if ( empty($sOptionSaved) ) {
				$sOptionSaved = $sOptionDefault;
			}
			
			$sHtml .= '<input class="span2'.$sAdditionalClass.'"
						type="text"
						placeholder="'.esc_attr( $sOptionSaved ).'"
						name="'.$insVarPrefix.$sOptionKey.'"
						value="'.esc_attr( $sOptionSaved ).'"
						id="'.$insVarPrefix.$sOptionKey.'" />';
			
			$sToggleTextInput = '';
			
			if ( $mOptionType === 'less_color' ) {
				
				if ( !getIsHexColour( $sOptionSaved ) ) {
					$sChecked = ' checked';
				}
				
				$sToggleTextInput= '
							<span class="toggle_checkbox">
							  <label>
								<input type="checkbox"
									name="hlt_toggle_'.$sOptionKey.'"
									id="hlt_toggle_'.$sOptionKey.'"'.$sChecked.'
									style="vertical-align: -2px;" /> edit as text
							  </label>
							</span>';
				
			} else if ( $mOptionType === 'less_size' || $mOptionType === 'less_font' ) {
			}
			
			$sHelpSection = '
					<div class="help_section">
						<span class="label label-less-name">@'.str_replace( HLT_BootstrapLess::LessOptionsPrefix, '', $sOptionKey ).'</span>
						'.$sToggleTextInput.'
						<span class="label label-less-name">'.$sOptionDefault.'</span>
					</div>';
			
		} else {
			echo 'we should never reach this point';
		}
		
		$sHtml .= '
						</label>
						'.$sOptionHelpText.'
					  </div>
					</div><!-- controls -->'
					.$sHelpSection.'
				</div><!-- control-group -->
			</div>
		';
	}
	
	return $sHtml;
}//getPluginOptionSpan
