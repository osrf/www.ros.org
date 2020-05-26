!function ($) {

PL_Code = function () {
	this.handletab = $('[data-action="pl-design"]')
	this.panel = $('.panel-pl-design')
	this.tabs     = $('[data-tab-action="user_less"], [data-tab-action="user_scripts"]')
	this.compiler = new ( less.Parser )
	this.txtarea  = {
		less : $("#custom_less")
		, htmlmixed : $("#custom_scripts")
	}
	this.editors = {
		less : 0
		, htmlmixed : 0
	}
	this.lookup = {
		user_less : 'less'
		, user_scripts : 'htmlmixed'
	}
	this.core_less = $('#pl_core_less')

	this.activateEditors()
	
	this.setUIBindings()

	$(document).trigger('plcode-loaded')
}
PL_Code.prototype = {

	compile : function ( code ) {

		var core = this.core_less.text()

		code = core + code

		var compiled = ''
		this.compiler.parse( code, function ( err, tree ) {
			if ( err )
				return plPrint( err )
			compiled = tree.toCSS()
		} )
		return compiled || ''
	}

	,	activateEditors : function() {

		that = this

		$.each( that.txtarea, function ( mode, $area ) {

			if ($area.hasClass( 'mirrored' )) return
			// config setup
			var config = $.extend( cm_base_config, { mode : mode } )
			// instantiate codemirror
			that.editors[ mode ] = CodeMirror.fromTextArea( $area.addClass('mirrored').get(0), config )
			// set bindings
			that.setEditorBindings( mode, $area )
		})
	}

	,	setEditorBindings : function ( mode, $area ) {
		that = this
		editor = this.editors[ mode ]

		// common bindings
		editor.on('blur', that.triggerSave )
		editor.on('change', function ( instance ) {
			// Update the content of the textarea.
			instance.save()
			// get data object
			dataobj = $area.parent().formParams();
			// extend
			$.pl.data.global = $.extend(true, $.pl.data.global, dataobj)
		})

		if ('less' === mode) {
			editor.on('keydown', function (instance, e) {
				if ( e.which == 13 && (e.metaKey || e.ctrlKey) ) {
					var code = instance.getValue()
					// update custom css
					$('#pagelines-custom').text( that.compile( code ) )
				}
			} )
		}
	}

	,	setUIBindings : function () {
		that = this

		this.handletab.click( that.refreshEditors )
		this.panel.on('shown', that.refreshEditors )
		this.tabs.on('click', function () {
			var type = $(this).data('tab-action')
			,	mode = that.lookup[ type ]
			,	editor = that.editors[ mode ]
			editor.refresh()
		})
	}

	,	refreshEditors : function() {
		that = $.plCode

		$.each( that.editors, function (mode, editor) {
			editor.refresh()
		})
	}

	,	triggerSave : function () {
		$.plAJAX.saveData()
	}
}


$(document).ready(function() {
	$.plCode = new PL_Code
})

}( window.jQuery );