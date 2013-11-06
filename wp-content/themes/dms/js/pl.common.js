!function ($) {

	// --> Initialize
	$(document).ready(function() {
		
		$(document).trigger( 'sectionStart' )
		
		$.plCommon.init()
		$.plMobilizer.init()
	
		$(".fitvids").fitVids(); // fit videos
	
		$.plAnimate.initAnimation()
		
		$.plNavigation.init()
		
		$.plParallax.init()
		
		$('.pl-credit').show()
	})
	
	$(window).load(function() {
		$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		$('.pl-section').on('resize', function(){
			$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		})
	})
	
	$.plNavigation = {
		init: function(){
			
			var that = this
			
			that.initDrops()
		}
		, initDrops: function(){
			
			var a = 1
			
			$(".pl-dropdown > li > ul").each(function(){

				var b = ""

				$(this).addClass("dropdown-menu");

				if( $(this).siblings("a").children("i").length===0 ){
					b = ' <i class="icon-caret-down"></i>'
				}

				$(this).siblings("a")
					.addClass("dropdown-toggle")
					.attr( "href", "#m" + a )
					.attr("data-toggle","dropdown")
					.append(b)
					.parent()
					.attr( "id", "m" + a++ )
					.addClass("dropdown")

				$(this)
					.find('.sub-menu')
					.addClass("dropdown-menu")
					.parent()
					.addClass('dropdown-submenu')
			})

			$(".dropdown-toggle").dropdown()

		}
	}
	
	$.plParallax = {
	

		init: function(speed){
			
			var that = this

			if( $('.pl-parallax').length >= 1){
				
				$('.pl-parallax').each(function(element){
				$(this).parallax('50%', .3)
				})
			}
			
			

		}
	
	}
	
	$.plMobilizer = {
		
		init: function(){
			var that = this
			
			that.mobileMenu()
		}
		
		, mobileMenu: function(){
			var that = this
			, 	theBody = $('body')
			, 	menuToggle = $('.mm-toggle')
			,	siteWrap = $('.site-wrap')
			, 	mobileMenu = $('.pl-mobile-menu')
			
			mobileMenu.css('max-height', siteWrap.height()-10)
			
			menuToggle.on('click.mmToggle', function(e){
				
				e.stopPropagation()
				mobileMenu.css('max-height', siteWrap.height())
				
				if( !siteWrap.hasClass('show-mm') ){
					
					mobileMenu.show()
					
					siteWrap
						.addClass('show-mm')
					
					$('.site-wrap, .mm-close').one('click touchstart', function(){
						siteWrap.removeClass('show-mm')
						setTimeout(function () {
						    mobileMenu.hide()
						}, 500)
					})
					
					
					$('.mm-holder').waypoint(function() {
						siteWrap.removeClass('show-mm')
						setTimeout(function () {
						    mobileMenu.hide()
						}, 500)
					}, {
						offset: function() {
							return -$(this).height();
						}
					})
					
				} else {
					
					siteWrap.removeClass('show-mm')
					
					setTimeout(function () {
					    mobileMenu.hide()
					}, 500)
					
				}
			
			})
			
		
			
		}
		
	}

	$.plAnimate = {
		
		initAnimation: function(){
			
			var that = this
						
			$.plAnimate.plWaypoints()
		}
		
		, plWaypoints: function(selector, options_passed){
			
			var defaults = { 
					offset: '85%' // 'bottom-in-view' 
					, triggerOnce: true
				}
				, options  = $.extend({}, defaults, options_passed)
				, delay = 150
				
			$('.pl-animation-group')
				.find('.pl-animation')
				.addClass('pla-group')
				
			$('.pl-animation-group').each(function(){
				
				var element = $(this)

				element.waypoint(function(direction){
				 	$(this)
						.find('.pl-animation')
						.each( function(i){
							var element = $(this)
							
							setTimeout(
								function(){ 
									element.addClass('animation-loaded') 
								}
								, (i * 250)
							);
						})

				}
				, { offset: '80%' 
					, triggerOnce: true
				})
			})

			$('.pl-animation:not(.pla-group)').each(function(){
				
				var element = $(this)

				element.waypoint(function(direction){
					
					 	$(this)
							.addClass('animation-loaded')
							.trigger('animation_loaded')

					}
					, { offset: '85%' 
					, triggerOnce: true
				})

			
			})
		}
		
	}

	$.plCommon = {

		init: function(){
			var that = this
			that.setHeight()

			$.resize.delay = 100 // resize throttle

			$('.pl-fixed-top').on('resize', function(){
				that.setHeight()
			})
			
			$('.pl-make-link').on('click', function(){
				var url = $(this).data('href') || '#'
				, 	newWindow = $(this).attr('target') || false
				
				if( newWindow )
					window.open( url, newWindow )
				else
					window.location.href = url
			
			})

		}

		, setHeight: function(){

			var height = $('.pl-fixed-top').height()

			$('.fixed-top-pusher').height(height)

		}
		
		, plVerticalCenter: function( container, element, offset ) {

			jQuery( container ).each(function(){

				var colHeight = jQuery(this).height()
				,	centeredElement = jQuery(this).find( element )
				,	infoHeight = centeredElement.height()
				, 	offCenter = offset || 0

				centeredElement.css('margin-top', ((colHeight / 2) - (infoHeight / 2 )) + offCenter )
			})

		}
		

	}

}(window.jQuery);