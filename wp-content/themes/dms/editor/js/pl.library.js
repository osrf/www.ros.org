function plCallWhenSet( flag, callback, flip ){
	
	var flip = flip || false
	,	flagVal = (flip) ? !jQuery.pl.flags[flag] : jQuery.pl.flags[flag]

	
	plPrint(flag)
	plPrint(flagVal)
	
	if( ! flagVal ){
		setTimeout(function() {
		    plCallWhenSet( flag, callback, flip )
		}, 150)
		
	} else {
		plPrint('call function')
		callback.call( this )
	}
}

function plUniqueID( length ) {
	var length = length || 6
	
  // Math.random should be unique because of its seeding algorithm.
  // Convert it to base 36 (numbers + letters), and grab the first 9 characters
  // after the decimal.
  return Math.random().toString(36).substr(2, length);
};

function plIsset(variable){
	if(typeof(variable) != "undefined" && variable !== null)
		return true
	else
		return false
}

function plPrint(variable){
	if( true == jQuery.pl.config.devMode )
		console.log( variable )
}

/* Data cleanup and handling
 * ============================================= */
function pl_html_input( text ) {
	
	if( typeof text != 'string')
		return text
	else 	
		return jQuery.trim( pl_htmlEntities( pl_stripSlashes( text ) ) )
}	

function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}

function pl_stripSlashes (str) {

  return (str + '').replace(/\\(.?)/g, function (s, n1) {
    switch (n1) {
    case '\\':
      return '\\';
    case '0':
      return '\u0000';
    case '':
      return '';
    default:
      return n1;
    }
  });
}

function pl_htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function isset () {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: FremyCompany
  // +   improved by: Onno Marsman
  // +   improved by: Rafał Kukawski
  // *     example 1: isset( undefined, true);
  // *     returns 1: false
  // *     example 2: isset( 'Kevin van Zonneveld' );
  // *     returns 2: true
  var a = arguments,
    l = a.length,
    i = 0,
    undef;

  if (l === 0) {
    throw new Error('Empty isset');
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null) {
      return false;
    }
    i++;
  }
  return true;
}

function basename (path, suffix) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Ash Searle (http://hexmen.com/blog/)
  // +   improved by: Lincoln Ramsay
  // +   improved by: djmix
  // *     example 1: basename('/www/site/home.htm', '.htm');
  // *     returns 1: 'home'
  // *     example 2: basename('ecra.php?p=1');
  // *     returns 2: 'ecra.php?p=1'
  var b = path.replace(/^.*[\/\\]/g, '');

  if (typeof(suffix) == 'string' && b.substr(b.length - suffix.length) == suffix) {
    b = b.substr(0, b.length - suffix.length);
  }

  return b;
}

/* Simple Shortcode System
 * =============================================
 */
function pl_do_shortcode(opt) {
	
	var match = opt.match( /\[([^\]]*)/ ) || false
	var shortcode = (match) ? match[1] : false
	
	if(!shortcode)
		return opt
		
	switch(shortcode) {
		case 'pl_child_url':
			opt = opt.replace(/\[pl_child_url\]/g, jQuery.pl.config.urls.StyleSheetURL)
		case 'pl_parent_url':
			opt = opt.replace(/\[pl_parent_url\]/g, jQuery.pl.config.urls.ParentStyleSheetURL)
		case 'pl_site_url':
			opt = opt.replace(/\[pl_site_url\]/g, jQuery.pl.config.urls.siteURL)
	}
	return opt
}


/* Page refresh function with optional timeout.
 * =============================================
 */
function pl_url_refresh(url,timeout){
	if(!timeout)
		var timeout = 0
	setTimeout(function() {
	  window.location.href = url;
	}, timeout);
}

jQuery.fn.getInputType = function(){ return this[0].tagName == "INPUT" ? jQuery(this[0]).attr("type").toLowerCase() : this[0].tagName.toLowerCase(); }