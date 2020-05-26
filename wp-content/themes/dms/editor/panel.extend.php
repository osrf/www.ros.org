<?php



class PageLinesExtendPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array($this, 'toolbar'));
		add_action('pagelines_editor_scripts', array($this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		wp_enqueue_script( 'pl-js-extend', $this->url . '/js/pl.extend.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function toolbar( $toolbar ){
		$toolbar['pl-extend'] = array(
			'name'	=> __( 'Store', 'pagelines' ),
			'icon'	=> 'icon-download',
			'pos'	=> 80,
			'panel'	=> array(
				'heading'	=> __( "Extend PageLines", 'pagelines' ),
				'store'		=> array(
					'name'	=> __( 'PageLines Store', 'pagelines' ),
					'filter'=> '*',
					'type'	=> 'call',
					'call'	=> array($this, 'the_store_callback'),
					'icon'	=> 'icon-gears'
				),
				'heading2'	=> "<i class='icon-filter'></i> Filters",
				// 'plus'		=> array(
				// 	'name'	=> 'Plus Extensions',
				// 	'href'	=> '#store',
				// 	'filter'=> '.plus',
				// 	'icon'	=> 'icon-plus-sign'
				// ),
				'featured'		=> array(
					'name'	=> __( 'Featured', 'pagelines' ),
					'href'	=> '#store',
					'filter'=> '.featured',
					'icon'	=> 'icon-star'
				),
				
				'sections'		=> array(
					'name'	=> __( 'Sections', 'pagelines' ),
					'href'	=> '#store',
					'filter'=> '.sections',
					'icon'	=> 'icon-random'
				),
				'plugins'		=> array(
					'name'	=> __( 'Plugins', 'pagelines' ),
					'href'	=> '#store',
					'filter'=> '.plugins',
					'icon'	=> 'icon-download-alt'
				),
				'themes'		=> array(
					'name'	=> __( 'Themes', 'pagelines' ),
					'href'	=> '#store',
					'filter'=> '.themes',
					'icon'	=> 'icon-picture'
				),
				'free'		=> array(
					'name'	=> __( 'Free', 'pagelines' ),
					'href'	=> '#store',
					'filter'=> '.free-item',
					'icon'	=> 'icon-tag'
				),
				'premium'		=> array(
					'name'	=> __( 'Premium', 'pagelines' ),
					'href'	=> '#store',
					'filter'=> '.premium-item',
					'icon'	=> 'icon-shopping-cart'
				),
				'heading3'	=> "Tools",
		//		'upload'	=> array(
		//			'name'	=> 'Upload',
		//			'icon'	=> 'icon-upload',
		//			'call'	=> array($this, 'upload_callback'),
		//		),
				'search'	=> array(
					'name'	=> __( 'Search', 'pagelines' ),
					'icon'	=> 'icon-search',
					'call'	=> array($this, 'search_callback'),
				),
			)
		);

		return $toolbar;
	}

	function upload_callback(){
			?>

			<form class="opt standard-form form-save-template">
				<fieldset>
					<span class="help-block"><?php _e( 'Upload a .zip extension into your PageLines install using this tool.', 'pagelines' ); ?>
					</span>
					<label for="template-name"><?php _e( 'Extension File (zip file - required)', 'pagelines' ); ?>
					</label>
					<input type="upload" id="template-name" name="template-name" required />
					<button type="submit" class="btn btn-primary btn-save-template"><?php _e( 'Upload Extension', 'pagelines' ); ?>
					</button>
				</fieldset>
			</form>
			<?php
	}

	function search_callback(){
			?>

			<form class="opt standard-form form-store-search">
				<fieldset>
					<span class="help-block"><?php _e( 'Search the PageLines store for extensions.', 'pagelines' ); ?>
					</span>

					<input class="" id="appendedInputButton" type="text">
					
					<button id="ssearch" class="btn btn-primary" type="submit"><?php _e( 'Search Store', 'pagelines' ); ?>
					</button>

				</fieldset>
			</form>
			<ul id='results' class="store-search-results">
			</ul>
		<script>
		jQuery(document).ready(function(){
			jQuery(".form-store-search").on('submit', function(e){
				e.preventDefault()
				jQuery('.store-search-results').empty()
				
				jQuery.ajaxSetup({ cache: false });
				
				var s = jQuery('#appendedInputButton').val()
				
				var url = sprintf('http://api.pagelines.com/v4/search/index.php?s=%s&callback=?',s)
				
				jQuery.getJSON(url,function(result){
					//plPrint(result)
					
					jQuery(".store-search-results").append("<li><strong>" + result.results + " results</strong><?php _e( ' found for ', 'pagelines' ); ?>
					<strong>" + s + "</strong></li>");
					
					jQuery.each(result.data, function(i, field){
						
						var demo = field.demo || false
						
						if(demo) {
							demo = sprintf( ' <a href="%s" class="btn btn-mini"><?php _e( 'Demo ', 'pagelines' ); ?>
							<i class="icon-picture" target="_blank"></i></a>', demo)
						} else {
							demo = ''
						}
						
						var btns = sprintf('<br/><a href="%s" class="btn btn-mini"><?php _e( 'Overview ', 'pagelines' ); ?>
						<i class="icon-external-link" target="_blank"></i></a>%s', field.overview, demo)
						
						var output = sprintf('<div class="img" style="max-width: 130px;"><img src="%s" /></div><div class="bd"><h4>%s</h4><p>%s %s</p></div>', field.thumb, field.name, field.description, btns)
						
						var wrap = sprintf('<li style="search-results"><div class="media fix">%s</div></li>', output)
						
				    	jQuery("#results").append(wrap);
				
				     });
		    });
		  });
		});
		</script>
			<?php
	}


	function the_store_callback(){

		$this->xlist = new EditorXList;

		$list = '';

		global $storeapi;
		$mixed_array = $storeapi->get_latest();

		foreach( $mixed_array as $key => $item){

			$class = $item['class_array'];

			$class[] = 'x-storefront';

			$img = sprintf('<img src="%s" style=""/>', $item['thumb']);

			$sub = ($item['price'] == '0') ? __('Free!', 'pagelines') : '$'.$item['price'];
			
			if($item['price'] == '0')
				$sub = __('Free!', 'pagelines'); 
			elseif( $item['price'] == '' )
				$sub = __('<i class="icon-sun"></i> Karma', 'pagelines'); 
			else 
				$sub = '$'.$item['price'];
				
			if( $item['sale'] )
				$sub = sprintf( '<s>%s</s> %s', $item['price'], $item['sale']);

			if($item['price'] == '')
				$item['price'] = 0;

			$class[] = ($item['price'] == 'free' || $item['price'] == '0' || $item['price'] == '') ? 'free-item' : 'premium-item';

			$args = array(
				'id'			=> $item['slug'],
				'class_array' 	=> $class,
				'data_array'	=> array(
					'store-id' 	=> $item['slug']
				),
				'thumb'			=> $item['thumb'],
				'splash'		=> $item['splash'],
				'name'			=> $item['name'],
				'sub'			=> $sub
			);

			$list .= $this->xlist->get_x_list_item( $args );


		}

		printf('<div class="x-list x-store" data-panel="x-store">%s</div>', $list);
	}


}