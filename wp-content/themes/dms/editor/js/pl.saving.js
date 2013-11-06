!function ($) {


	/*
	 * AJAX Actions
	 */
	$.plSave = {

		save: function( opts ){
			
			var args = {
					mode: 'fast_save'
				,	run: 'all'
				,	store: $.pl.data
				,	savingText: 'Saving.'
				,	refresh: false
				,	refreshText: 'Successfully saved! Refreshing page...'
				, 	templateMode: $.pl.config.templateMode || 'local'
			}
			
			$.pageBuilder.updatePage({ location: 'save-data' })

			$.extend( args, opts )

			var response = $.plAJAX.run( args )

		}


	}



}(window.jQuery);