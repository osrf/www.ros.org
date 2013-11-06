<?php



class EditorXList{

	function __construct(){

		add_action('pagelines_editor_scripts', array( $this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){

		// Isotope
		wp_enqueue_script( 'isotope', PL_JS . '/utils.isotope.js', array('jquery'), PL_CORE_VERSION, true);

		wp_enqueue_script( 'pl-js-xlist', $this->url . '/js/pl.xlist.js', array('jquery'), PL_CORE_VERSION, true);

	}

	function defaults(){
		$d = array(
			'id'			=> '',
			'class_array' 	=> array(),
			'data_array'	=> array(),
			'thumb'			=> '',
			'splash'		=> '',
			'name'			=> 'No Name',
			'sub'			=> false,
			'actions'		=> '',
			'format'		=> 'touchable',
			'icon'			=> ''
		);

		return $d;
	}

	function get_x_list_item( $args ){

		$args = wp_parse_args($args, $this->defaults());

		$classes = join(' ', $args['class_array']);

		$popover_content = sprintf('<img src="%s" />', $args['splash']);

		$img = sprintf('<img width="300" height="225" src="%s" />', $args['thumb']);

		$datas = '';
		foreach($args['data_array'] as $field => $val){
			$datas .= sprintf("data-%s='%s' ", $field, $val);
		}

		$sub = ($args['sub']) ? sprintf('<div class="x-item-sub">%s</div>', stripslashes( $args['sub'] ) ) : '';

		$thumb = ($args['thumb'] != '') ? sprintf("<div class='x-item-frame'><div class='pl-vignette'>%s</div></div>", $img) : '';

		$icon = ($args['format'] == 'media' && $args['icon'] != '') ? sprintf("<div class='img rtimg'><i class='icon-3x %s'></i></div>", $args['icon']) : '';

		$pad_class = ($args['format'] == 'media') ? 'media fix' : '';

		$xID = ($args['id'] != '') ? sprintf("data-extend-id='%s'", $args['id']) : '';

		$list_item = sprintf(
			"<section id='%s_%s' class='x-item x-extension %s %s' %s %s>
				<div class='x-item-pad'>
					<div class='%s'>
						%s
						%s
						<div class='x-item-text bd'>
							<span class='x-name'>%s</span>
							%s
						</div>

					</div>
					%s
				</div>
			</section>",
			$args['id'],
			substr(uniqid(), 0, 6),
			'filter-'.$args['id'],
			$classes,
			$datas,
			$xID,
			$pad_class,
			$thumb,
			$icon,
			$args['name'],
			$sub,

			$args['actions']
		);

		return $list_item;

	}

	function get_action_out( $actions ){

		if(!empty($actions)){

			foreach($actions as $action){

				$action = wp_parse_args($action, $this->defaults());

				$action_classes = join(' ', $action['class_array']);

				$action_datas = '';
				foreach($action['data_array'] as $field => $val){
					$action_datas .= sprintf("data-%s='%s' ", $field, $val);
				}

				$action_name = $action['name'];

				$action_output .= sprintf('<a class="btn btn-mini %s" %s>%s</a> ', $action_classes, $action_datas, $action_name);

			}
			return sprintf('<div class="x-item-actions">%s</div>', $action_output);

		} else
			return '';



	}

}