/* =========================================================
 * PageLines Toolbox -- Copyright PageLines 2012
 * =========================================================
 */

!function ($) {

  "use strict"; // jshint


 /* MODAL CLASS DEFINITION
  * ====================== */

	var ToolBox = function (element, options) {

	    this.options = options

		this.$element = $(element)

		this.$panel = this.$element.find('.toolbox-panel')
			.on('click', function(e){
				e.stopPropagation()
				
				var dropdowns = $(this).find('.dropdown-toggle')
				dropdowns.parent().removeClass('open')
			})

		this.$panel.on()

		this.$pusher =  $('.pl-toolbox-pusher')
		this.$tabnav =  $('.tabs-nav')

		this.resizer = $('.resizer-handle')
		this.closer = $('.btn-closer')
		this.offBtn = $('.btn-pl-toggle')
		this.handleHeight = 29

		this.resizePanel()
		this.scrollPanel()

		this.closer.on('click.toolbox.toggler', $.proxy(this.hide, this))



	}

  ToolBox.prototype = {

    constructor: ToolBox

    , toggle: function () {

		return this[!this.isShown ? 'show' : 'hide']()
	}

    , show: function () {

        var that = this
		,	e = $.Event('show')

        if (this.isShown || e.isDefaultPrevented())
			return that // chaining

        $('body').addClass('toolbox-open')


        this.isShown = true
		store.set('toolboxShown', true)


        this.keyboard()

		that.setHeight()

		that.$panel
			.show()
			.css('margin-bottom', 0)
			.addClass('in')
            .focus()

		that.$pusher
			.show()

		this.resizer
			.show()

		this.offBtn
			.hide()

		this.closer
			.fadeIn()



		return that // chaining
	}

    , hide: function (e) {

        var that = this
		,	e = $.Event('hide')
		, 	ht = this.$panel.height()

        //if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false
		store.set('toolboxShown', false)
        $('body')
			.removeClass('toolbox-open')



        this.keyboard()

        this.$panel
          	.removeClass('in')
			.css('margin-bottom', ht * -1)

		$('.btn-panel').removeClass('active-tab')

		this.resizer
			.hide()

		this.closer
			.hide()

		this.offBtn
			.fadeIn()

		that.$pusher
			.height(that.handleHeight)

      }

	, showPanel: function( key ){

		$.pageTools.showPanel(key)


	}

	, setHeight: function( newHeight ) {

		var obj = this
		,	originalHeight = 440
		,	savedHeight = Number( localStorage.getItem('toolboxHeight') )
		, 	handleHeight = obj.handleHeight

		if( newHeight !== undefined ){

			obj.$panel.height( newHeight )
			obj.$pusher.height( newHeight + handleHeight )
			obj.$tabnav.css('max-height', newHeight)

			localStorage.setItem('toolboxHeight', newHeight)

		} else {

			if( !savedHeight ){

				obj.$panel.height( originalHeight )
				obj.$pusher.height( originalHeight + handleHeight)
				obj.$tabnav.css('max-height', originalHeight)

				localStorage.setItem('toolboxHeight', originalHeight)

			} else {

				obj.$panel.height( savedHeight )
				obj.$pusher.height( savedHeight + handleHeight)
				obj.$tabnav.css('max-height', savedHeight)
				localStorage.setItem('toolboxHeight', savedHeight)
			}


		}

	}

	, resizePanel: function() {

		var obj = this

		this.resizer.on('mousedown', function(evnt) {

			evnt.stopPropagation()

			var startY = evnt.pageY
			, 	startHeight = obj.$panel.outerHeight()

			obj.resizer.addClass('resizing')

			$('body').addClass('disable-select')

			$(document).on('mousemove.resizehandle', function(e) {

				var newY = e.pageY
				,	newHeight = Math.max(0, startHeight + startY - newY)

				if(e.pageY > 50 && newHeight > 50){
					obj.setHeight(newHeight)
				}
			})



		})

		$(document).mouseup(function(event) {
			$(document).off('mousemove.resizehandle')
			obj.resizer.removeClass('resizing')
			$('body').removeClass('disable-select')
		})

		$(window).resize(function() {
			var fromTop = $('.pl-toolbox').position().top
			, 	startHeight = obj.$panel.outerHeight()
			, 	minHeight = 120

			if( fromTop < minHeight ){
				var adjust = startHeight - (minHeight - fromTop)

				if(adjust > minHeight)
					obj.setHeight(adjust)
			}

		})

	}
	, scrollPanel: function() {

		var obj = this;

		obj.$panel.find('.tabs-nav').hover(
			function () {
				$(this).addClass("hover");
			},
			function () {
				$(this).removeClass("hover");
			}
		)

		obj.$panel.bind('mousewheel', function(e, d) {

			if($(this).find('.tabs-nav').hasClass('hover')){
				var	nav = $('.current-panel .tabs-nav')
				,	height = nav.height()
				,	scrollHeight = nav[0].scrollHeight
				,	fromTop = nav.scrollTop()


			} else {
				var	height = obj.$panel.height()
				,	scrollHeight = obj.$panel[0].scrollHeight
				,	fromTop = this.scrollTop

			}
	//		console.log(height + '-'+scrollHeight+ '-'+fromTop)
			if((fromTop === (scrollHeight - height) && d < 0) || (fromTop === 0 && d > 0)) {
				e.preventDefault()
	    	}

		})

	}

	, keyboard: function () {
		var that = this

		// Escape key
		if ( this.isShown ) {

			$('body').on('keyup.dismiss.toolbox', function ( e ) {
				e.which == 27 && that.hide()
			})
		} else if (!this.isShown) {
			this.$panel.off('keyup.dismiss.toolbox')
		}
	
		
	
		
	}


  }


/* MODAL PLUGIN DEFINITION
 * ======================= */

	$.toolbox = function( option ){

		if(typeof option == 'string' && option == 'open')
			return ( $('body').hasClass('toolbox-open') ) ? true : false
		else
			return $('body').toolbox( option )


	}

	$.fn.toolbox = function ( option ) {

		return this.each( function() {
			var tbSelector = $('.pl-toolbox')
			,	toolBoxObject = tbSelector.data('toolbox')
			,	options = $.extend({}, $.fn.toolbox.defaults, tbSelector.data(), typeof option == 'object' && option)

			if ( !toolBoxObject )
				tbSelector.data( 'toolbox', ( toolBoxObject = new ToolBox( tbSelector, options ) ) )

			// Action
			if ( typeof option == 'string' )
				toolBoxObject[option]()
			else if ( $.isFunction( options.action ) )
				options.action.call( this )
			else if ( options.action == 'show' )
				toolBoxObject.show().showPanel( options.panel )
			else if ( this.isShown = store.get('toolboxShown') )
				toolBoxObject.show().showPanel( store.get('toolboxPanel') )
			else
				toolBoxObject.hide()



			// Panel Load

			if ( $.isFunction( options.info ) )
				options.info.call( this )

		})
	}

	$.fn.toolbox.defaults = {
		action: false
		, panel: false
		, persist: true
	}

	$.fn.toolbox.Constructor = ToolBox



}(window.jQuery);