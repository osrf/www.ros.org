!function ($) {
	$(window).load(function(){
	
		$('.flipper').each(function(){
			
	    	var flipper = $(this)
			,	scrollSpeed
			, 	easing
			, 	shown = flipper.data('shown') || 3
			,	scrollSpeed = flipper.data('scroll-speed') || 700
			,	easing = flipper.data('easing') || 'linear'
			
	    	flipper.carouFredSel({
	    		circular: 	true
	    		, responsive: true
				, auto: true
				, height: "variable"
				, onCreate: function(){
					
				}
				, items       : {
					width : 353,
					height: "variable",
			        visible     : {
			            min         : 1
			            , max         : shown
			        }
			    }
			    , swipe       : {
			        onTouch     : true
			    }
			    , scroll: {
			    	easing          : easing
		            , duration        : scrollSpeed
			    }
		        , prev    : {
			        button  : function() {
			           return flipper.parents('.flipper-wrap').prev(".flipper-heading").find('.flipper-prev');
			        }
		    	}
			    , next    : {
		       		button  : function() {
			           return flipper.parents('.flipper-wrap').prev(".flipper-heading").find('.flipper-next');
			        }
			    }
			    , auto    : {
			    	play: false
			    }
				
		    }).addClass('flipper-loaded').animate({'opacity': 1},1300);
		
			$.plCommon.plVerticalCenter('.flipper-info', '.pl-center', -20)
		
	    });
		
	})
}(window.jQuery);