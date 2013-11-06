!function ($) {

$.plTemplates = {

	init: function(){
		this.bindUIActions()
	}

	, bindUIActions: function(){
		var that = this

		// fix issue with drop down falling behind
		$('.actions-toggle').on('click', function(){
			$('.x-templates').css('z-index', 7);
			$(this).closest('.x-templates').css('z-index', 8)
		})

		$('.tpl-tag').tooltip({placement: 'top'})


		$(".load-template").on("click.loadTemplate", function(e) {

			e.preventDefault()

			var args = {
					mode: 'templates'
				,	run: 'load'
				,	confirm: true
				,	confirmText: "<h3>Are you sure?</h3><p>Loading a new template will overwrite the current page's configuration.</p>"
				,	savingText: 'Loading Template'
				,	refresh: true
				,	refreshText: 'Successfully Loaded. Refreshing page'
				, 	log: true
				,	key: $(this).closest('.x-item').data('key')
				,	templateMode: $.pl.config.templateMode
			}

			var response = $.plAJAX.run( args )
			
		
		})

		$(".delete-template").on("click.deleteTemplate", function(e) {

			e.preventDefault()

			var key = $(this).closest('.x-item').data('key')
			,	theIsotope = $(this).closest('.isotope')
			,	args = {
						mode: 'templates'
					,	run: 'delete'
					,	confirm: true
					,	confirmText: '<h3>Are you sure?</h3><p>This will delete this template. All pages using this template will be reverted to their default page configuration.</p>'
					,	savingText: 'Deleting Template'
					,	refresh: false
					, 	log: true
					,	key: key
					, 	beforeSend: function(){
							$( '.template_key_'+key ).fadeOut(300, function() {
								$(this).remove()

							})

					}
					,	postSuccess: function(){
						theIsotope.isotope( 'reLayout' )
					}
				}

			var response = $.plAJAX.run( args )

		})


		$(".form-save-template").on("submit.saveTemplate", function(e) {

			e.preventDefault()

			var form = $(this).formParams()
			,	args = {
						mode: 'templates'
					,	run: 'save'
					,	confirm: false
					,	savingText: 'Saving Template'
					,	refreshText: 'Successfully Saved. Refreshing page'
					,	refresh: true
					, 	log: true
					,	map: $.plMapping.getCurrentMap()
					,	settings: $.pl.data.local
				}
			,	args = $.extend({}, args, form) // add form fields to post


			var response = $.plAJAX.run( args )


		})


		$(".update-template").on("click", function(e) {

			e.preventDefault()

			var that = this
			,	key = $(this).closest('.x-item').data('key')
			,	args = {
						mode: 'templates'
					,	run: 'update'
					,	confirm: true
					,	confirmText: '<h3>Are you sure?</h3><p>This action will overwrite this template and its configuration. All pages using this template will be updated with the new config as well.</p>'
					,	savingText: 'Updating Template'
					,	successNote: true
					,	successText: 'Template successfully updated!'
					,	refresh: false
					, 	log: true
					,	key: key
					,	map: $.plMapping.getCurrentMap()
					,	settings: $.pl.data.local
				}

			var response = $.plAJAX.run( args )



		})


		$(".set-tpl").on("click.defaultTemplate", function(e) {

			e.preventDefault()

			var that = this
			,	value = $(this).closest('.x-item').data('key')
			,	run = $(this).data('run')
			,	args = {
						mode: 'templates'
					,	run: 'set_'+run
					,	confirm: false
					,	refresh: false
					, 	log: true
					, 	field: $(this).data('field')
					,	value: value
					, 	postSuccess: function( response ){

							// console.log("caller is " + arguments.callee.caller.toString());


							// $.Ajax parses argument values and calles this thing, probably supposed to do that a different way
							if(!response)
								return

							var theList = $(that).closest('.x-list')

								theList
									.find('.set-tpl[data-run="'+run+'"]')
									.removeClass('active')

								theList
									.find('.active-'+run)
									.removeClass('active-'+run)


							if(response.result && response.result != false){

								$(that)
									.addClass('active')
									.closest('.x-item-actions')
									.addClass('active-'+run)

							}else {
								plPrint('Response was false.')
								plPrint( response )
							}




						}
				}

			var response = $.plAJAX.run( args )


		})





	}


}

}(window.jQuery);