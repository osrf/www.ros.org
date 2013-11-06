/**
 * Framework Javascript Functions
 * Copyright (c) PageLines 2008 - 2013
 *
 * Written By PageLines
 */

!function ($) {
	
$(document).ready(function(){

	if($("#pl-dms-less").length){
	
		var cm_mode = jQuery("#pl-dms-less").data('mode')
		,	cm_config = jQuery.extend( cm_base_config, { cm_mode : cm_mode } )
		,	editor3 = CodeMirror.fromTextArea(jQuery("#pl-dms-less").get(0), cm_config)
		
	}

	if(jQuery("#pl-dms-scripts").length){
		
		var cm_mode = jQuery("#pl-dms-scripts").data('mode')
		,	cm_config = jQuery.extend( cm_base_config, { cm_mode : cm_mode } )
		,	editor4 = CodeMirror.fromTextArea(jQuery("#pl-dms-scripts").get(0), cm_config);
	}
	
	jQuery('.dms-update-setting').on('submit', function(e){
		
		var theSetting = jQuery(this).data('setting')
		,	theValue = jQuery('.input_'+theSetting).val()
		,	saveText = jQuery(this).find('.saving-confirm');

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'pl_dms_admin_actions'
				, value: theValue
				, setting: theSetting
				, mode: 'setting_update'
				, flag: 'admin_fallback'
			},
			beforeSend: function(){
			
				
				saveText.show().text('Saving'); // text while saving
				
				interval = window.setInterval(function(){
					var text = saveText.text();
					if (text.length < 10){	saveText.text(text + '.'); }
					else { saveText.text('Saving'); }
				}, 400);
				
				
			},
			success: function(response) {
				window.clearInterval(interval); // clear dots...
			
				saveText.text('Saved!');
				
				saveText
					.delay(800)
					.fadeOut('slow')
			}
		});

		return false;
		
	})





});
// End AJAX Uploading


/*
 * ###########################
 *   jQuery Extension
 * ###########################
 */

jQuery.fn.center = function ( relative_element ) {

    this.css("position","absolute");
    this.css("top", ( jQuery(window).height() - this.height() ) / 4+jQuery(window).scrollTop() + "px");
    this.css("left", ( jQuery(relative_element).width() - this.width() ) / 2+jQuery(relative_element).scrollLeft() + "px");
    return this;
}

jQuery.fn.exists = function(){return jQuery(this).length>0;}

}(window.jQuery);