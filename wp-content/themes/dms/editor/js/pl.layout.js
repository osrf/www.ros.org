!function ($) {

$.widthResize = {

	checkWindowEdges: function( widthSel ){

		if(widthSel.width() >= ($(window).width() - 10))
			$('body').addClass('width-resize-edge')
		else
			$('body').removeClass('width-resize-edge')
	}

	, startUp: function(){

		var	widthSel = ($('body').hasClass('display-boxed')) ? $('.boxed-wrap') : $('.pl-content')

		$('body').addClass('width-resize')

		$.widthResize.checkWindowEdges(widthSel)

		widthSel.resizable({
			handles: "e, w",
			minWidth: 400,

			start: function(event, ui){
				$('body').addClass('width-resizing')

				$('.btn-layout-resize').addClass('active')
			}

			, stop: function(event, ui){
				$('body').removeClass('width-resizing')
				$('.btn-layout-resize').removeClass('active')

				$.plAJAX.saveData( )
			}

			, resize: function(event, ui) {

				var resizeWidth = ui.size.width
				,	resizeOrigWidth = ui.originalSize.width
				,	resizeNewWidth = resizeOrigWidth + ((resizeWidth - resizeOrigWidth) * 2)
				,	windowWidth = $(window).width()
				, 	layoutMode = $.pl.flags.layoutMode


				resizeNewWidth = (resizeNewWidth < 480) ? 480 : resizeNewWidth
				resizeNewWidth = ( resizeNewWidth  >= windowWidth ) ? windowWidth : resizeNewWidth

				var percentWidth = Math.round( ( resizeNewWidth / windowWidth ) * 100 ) + '%'
				,	pixelWidth = resizeNewWidth+'px'
				,	theSetWidth = (layoutMode == 'percent') ? percentWidth : pixelWidth


				widthSel
					.css('left', 'auto')
					.css('height', 'auto')
					.width( 'auto' )
					.css('max-width', theSetWidth)

				$.widthResize.checkWindowEdges(widthSel)

				// always set options w/ arrays
				$.pl.data.global.settings.content_width_px = pixelWidth
				$.pl.data.global.settings.content_width_percent = percentWidth

				$('.resize-px').html(pixelWidth)
				$('.resize-percent').html(percentWidth)

			}
		})

		$('.ui-resizable-handle')
			.hover(
				function () {
					$('body').addClass("resize-hover")
				}
				, function () {
					$('body').removeClass("resize-hover")
				}
			)

	}
	, shutDown: function(){

		var	widthSel = $('.pl-content')

		$('body').removeClass('width-resize')

		$(".ui-resizable-handle").unbind('mouseenter mouseleave')

		widthSel.resizable( "destroy" )



	}
}

}(window.jQuery);