!function ($) {

	$(document).ready(function() {
		$.plExtend.init()
	})

$.plExtend = {
	
	init: function(){
	
	}

	 
	, btnActions: function(){
		var that = this

		$('.btn-purchase-item').on('click', function(){

			var tbOpen	= $.toolbox('open')
			,	theID	= $(this).data('extend-id')

			if(tbOpen)
				$.toolbox('hide')

			theModal = that.purchaseModal( theID )
			bootbox.confirm( theModal, function( result ){

				if(result == true){
				

				} else {
					

					if( tbOpen )
						$.toolbox('show')
				}

			})
		})

	}
	, actionButtons: function( data ){
		var buttons 		= ''
		, 	theID			= data.extendId
		,	ext				= $.pl.config.extensions[theID] || false
		,	overviewLink	= ext.overview || false
		,	demoLink 		= ext.demo || false
		,	author			= ext.author || false
		,	authorURL		= ext.author_url || false
		,	purchase		= ext.purchase || false
		,	Owned			= ext.owned || false
		,	ptext			= 'Download'
		,	type			= ext.type || false
		,	adminURL		= $.pl.config.urls.adminURL
		,	Slug			= basename(ext.overview)
		,	component		= sprintf('?page=install-pl-extensions&pl_installed=%s', Slug)
		,	encoded			= encodeURIComponent(component)
		,	InstallUrl 		= sprintf('%sadmin.php%s', adminURL, component)
	//	,	PayUrl 		= sprintf('%sadmin.php%s', adminURL, encoded)
		,	payLink			= sprintf('%s|%s|%s',ext.purchase, adminURL, Slug)
		,	InstallLink 	= sprintf('%sadmin.php?page=install-pl-extensions&tgmpa-install=install-plugin&slug=%s&_wpnonce=%s&front=1',adminURL, Slug, $.pl.config.nonce)
	//	, 	Allowed			= (type != 'sections') ? true : false
		,	Status			= ext.status || false
		,	Activate 		= sprintf('%sadmin.php?page=install-pl-extensions&plugin=%s&tgmpa-activate=activate-plugin&pl_type=plugin&_wpnonce=%s&front=1', adminURL, Slug, $.pl.config.nonce)
		,	Deactivate 		= sprintf('%sadmin.php?page=install-pl-extensions&plugin=%s&tgmpa-deactivate=deactivate-plugin&pl_type=plugin&_wpnonce=%s&front=1', adminURL, Slug, $.pl.config.nonce)


		if(!Owned && !Status)
			buttons = sprintf('<a href="https://www.pagelines.com/api/paypal/button.php?paypal=%s" class="btn btn-primary x-remove"><i class="icon-money"></i> Purchase</a> ', payLink)

		// if(Status == 'active' )
		// 	buttons = sprintf('<a href="%s" class="btn btn-primary x-remove"><i class="icon-remove"></i> Deactivate</a> ', Deactivate )
		// if(Status == 'installed' )
		// 	buttons = sprintf('<a href="%s" class="btn btn-primary x-remove"><i class="icon-ok"></i> Activate</a> ', Activate)
		// if(Owned && !Status)
		// 	buttons = sprintf('<a href="%s" class="btn btn-primary x-remove"><i class="icon-cloud-download"></i> Install</a> ', InstallLink)

		if(overviewLink)
			buttons += sprintf('<a href="%s" class="btn btn-primary x-remove" target="_blank">View &amp; Download &nbsp;<i class="icon-chevron-sign-right"></i></a> ', overviewLink)

		if(demoLink)
			buttons += sprintf('<a href="%s" class="btn x-remove" target="_blank"><i class="icon-desktop"></i> Demo</a> ', demoLink)
			
		if(authorURL && author)
			buttons += sprintf('<a href="%s" class="btn x-remove" target="_blank"><i class="icon-external-link"></i> Author</a> ', authorURL )

		return buttons
	}

	, purchaseModal: function( theID ) {
		var	ext			= $.pl.config.extensions[theID]
		,	adminURL	= $.pl.config.urls.adminURL
		,	Slug		= basename(ext.overview)
		,	component	= sprintf('%sadmin.php?page=install-pl-extensions&pl_installed=%s', adminURL, Slug)
		,	InstallUrl	= encodeURIComponent(component)
		,	payLink		= sprintf('%s|%s',ext.purchase, InstallUrl)

		return sprintf("<iframe style='width: 100%%; height: 100px;' src='https://www.pagelines.com/api/paypal/button.php?paypal=%s'></iframe>", payLink)
	}
}



}(window.jQuery);