<?php
class LazyLoad_Images_Pro {

	const version = '0.2';

	var $base_url = '';

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_filter( 'post_thumbnail_html', array( $this, 'add_image_placeholders' ) );
		add_filter( 'the_content', array( $this, 'add_image_placeholders' ) );
		add_filter( 'pl_box_image', array( $this, 'add_image_placeholders' ) );
		add_filter( 'pl_banner_image', array( $this, 'add_image_placeholders' ) );
		add_filter( 'pl_hero_image', array( $this, 'add_image_placeholders' ) );
		add_filter( 'pl_highlight_splash', array( $this, 'add_image_placeholders' ) );
	}

	function add_scripts() {
		wp_enqueue_script( 'wpcom-lazy-load-images',  $this->get_url( '/libs/js/lazy-load.js' ), array( 'jquery', 'jquery-sonar' ), self::version, true );
		wp_enqueue_script( 'jquery-sonar', $this->get_url( '/libs/js/jquery.sonar.min.js' ), array( 'jquery' ), self::version, true );
	}

	function add_image_placeholders( $content ) {
		// Don't lazyload for feeds, previews, mobile
		if( is_feed() || ( function_exists( 'is_mobile' ) && is_mobile() ) || isset( $_GET['preview'] ) )
			return $content;

		// dont lazyload woo cart images.
		if( false !== strpos( $content, 'attachment-shop_thumbnail' ) )
			return $content;

		// In case you want to change the placeholder image
		$image = apply_filters( 'lazyload_images_placeholder_image', $this->get_url( '/libs/images/1x1.trans.gif' ) );

		// This is a pretty simple regex, but it works
		$content = preg_replace( '#<img([^>]+?)src=#', sprintf( '<img${1}src="%s" data-lazy-src=', $image ), $content );

		return $content;
	}

	function get_url( $path = '' ) {
		return plugins_url( ltrim( $path ), dirname( __FILE__ ) );
	}
}
