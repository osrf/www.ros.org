!function ($) {

	PL_Lessify = function () {

		this.compiler = new ( less.Parser )
		
		this.core_less = $('#pl-less-inline')
		
		this.css_container = $('#pagelines-draft-css')
		
		this.setUIBindings()
		
		this.loadCSS()

		$(document).trigger('pl-less-loaded')
	}
	PL_Lessify.prototype = {

		loadCSS : function( newVars ){
			
			//var core = this.core_less.text()
			
			var that = this
			,	newVars = newVars || ''
			
			code = $('#pl-less-vars').text()
			
			if(newVars != '')
				$('#pl-less-tools').append( that.createVarLESS( newVars ) )
					
			code += $('#pl-less-tools').text()
			
			code += $('#pl-less-core').text()
				
			code += $('#pl-less-sections').text()
			
			var start = new Date();
		
			var CSS = this.compile( code )
			
			this.saveCSS( CSS )
			
			console.log(new Date() - start);
			
			$('#pagelines-draft-css').text( CSS )
	
		}
		
		,	createVarLESS : function( vars ){
			
			
			var newVarsLESS = "";
			
		    $.each(vars, function(name, value) {
				
				newVarsLESS += sprintf('@%s:%s;', name, value)
			
		    })
			console.log(newVarsLESS)
		    return newVarsLESS
		}
		
		,	saveCSS : function ( CSS ) {
			
			$.plSave.save({
				mode: 'save_css'
				, run: 'save'
				, store: CSS
				, log: true
			})
			
		} 

		, 	compile : function ( code ) {
				
				var compiled = ''
				this.compiler.parse( code, function ( err, tree ) {
					if ( err )
						return console.log( err )
					
					
					compiled = tree.toCSS()
					
				} )
				return compiled || ''
			}


		,	setUIBindings : function () {
				that = this

				// $('.lstn-css').on('blur', function(){
				// 				
				// 				var theVar = $(this).data('var')
				// 				,	theVal = $(this).val()
				// 				, 	theObj = {}
				// 				
				// 				
				// 				
				// 				if( theVar != '' ){
				// 					
				// 					theObj[theVar] = theVal
				// 				
				// 					$.plLessify.loadCSS( theObj )
				// 					
				// 				}
				// 			
				// 			})

			}
	}

//	$.plLessify = new PL_Lessify
	$(document).ready(function() {
			$.plLessify = new PL_Lessify
		})

}( window.jQuery );