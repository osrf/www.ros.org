jQuery(document).ready(function(){

	var a = 1

	// Do this for every drop down
	jQuery(".pldrop > li > ul").each(function(){

		var b = ""

		jQuery(this).addClass("dropdown-menu");

		if( jQuery(this).siblings("a").children(".caret").length===0 ){
			b = ' <b class="caret"/>'
		}

		jQuery(this).siblings("a")
			.addClass("dropdown-toggle")
			.attr( "href", "#m" + a )
			.attr("data-toggle","dropdown")
			.append(b)
			.parent()
			.attr( "id", "m" + a++ )
			.addClass("dropdown")

		jQuery(this)
			.find('.sub-menu')
			.addClass("dropdown-menu")
			.parent()
			.addClass('dropdown-submenu')
	})

	jQuery(".dropdown-toggle").dropdown()

	touchFix();

})

function touchFix(){
	jQuery('body')
		.on('touchstart.dropdown', '.dropdown-menu', function (e) {e.stopPropagation();})
		.on('touchstart.dropdown', '.dropdown-submenu', function (e) {e.preventDefault();});
}

