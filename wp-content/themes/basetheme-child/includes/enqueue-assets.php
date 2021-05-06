<?php

// These functions will be used by basethemechild_setup_theme() in functions.php

/**
 * Get your Google API Credentials from https://console.developers.google.com/apis/
 * Make sure to set up restrictions so that the key can only be used from certain HTTP Referrers.
 */
define( 'GOOGLE_API_KEY', '' );

/**
 * Registers and enqueues the stylesheets that the theme requires.
 */
function basethemechild_enqueue_styles() {	
	if( !is_admin() ) {	
		// remove Gutenberg CSS
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');

		/**
		 * Load default theme stylesheet.
		 * false -> No dependancies.
		 */
		wp_register_style( 'theme_css', get_asset_uri( 'dist/css/screen.min.css' ), false );
		wp_enqueue_style( 'theme_css' );

		/**
		 * Load print stylesheet.
		 * false -> No dependancies.
		 */
		wp_register_style( 'print_css', get_asset_uri( 'dist/css/print.min.css' ), false, false, 'print' );
		wp_enqueue_style( 'print_css' );
	}
}

/**
 * Registers and enqueues the scripts that the theme requires.
 */
function basethemechild_enqueue_scripts() {
	if ( !is_admin() ) {

		// Load specific jQuery library from CDN, in noConflict mode ($ not defined)
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', apply_filters( 'basetheme_jquery_url', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js' ), false, false, true );
		wp_enqueue_script( 'jquery' );
		wp_add_inline_script( 'jquery', 'jQuery.noConflict();' );

		/**
		 * Load header scripts.
		 * No dependancies, in header -> default for wp_register_script().
		 */
		wp_register_script ( 'head_js', get_asset_uri( 'dist/js/head.min.js' ) );
		wp_enqueue_script ( 'head_js' );
		
		/**
		 * Load footer scripts
		 * Dependancies: jQuery
		 * false -> No version string (versions will be revisioned by Gulp.js)
		 * true  -> Load in footer
		 */
		wp_register_script ( 'footer_js', get_asset_uri( 'dist/js/footer.min.js' ), array( 'jquery' ), false, true );
		$footer_js_args = array(
			'template_directory_uri' => trailingslashit(get_template_directory_uri()),
			'stylesheet_directory_uri' => trailingslashit(get_stylesheet_directory_uri()),
		);
		wp_localize_script( 'footer_js', 'wp', $footer_js_args );
		wp_enqueue_script ( 'footer_js' );

	}
}

/**
 * Register and enqueue Google Maps scripts for pages that require it.
 */
function basethemechild_enqueue_google_scripts() {
	global $post;

	if ( 
		!is_admin() && 
		'' !== GOOGLE_API_KEY && 
		apply_filters( 'page_has_google_map', false, $post ) 
	) {

		/**
		 * Load Google Maps JavaScript API.
		 * No dependancies
		 * false -> No version string
		 * true  -> Load in footer
		 */
		wp_register_script ("google-maps-api", "https://maps.googleapis.com/maps/api/js?libraries=places&key=" . GOOGLE_API_KEY, array(), false, true);
		wp_enqueue_script ("google-maps-api");

		/**
		 * Load script to initialise all maps on page.
		 * Dependancies: jQuery
		 * false -> No version string
		 * true  -> Load in footer
		 */
		wp_register_script ("initialise-google-maps", get_asset_uri( 'dist/js/google-maps.min.js' ), array( 'jquery' ), false, true);
		wp_enqueue_script ("initialise-google-maps");

	}
}

/**
 * Add the Google API key to the Advanced Custom Fields plugin.
 * @param  array $api  The API credentials in use.
 * @return array
 */
add_filter( 'acf/fields/google_map/api', 'basethemechild_add_acf_api_creds' );
function basethemechild_add_acf_api_creds( $api ) {
	if ( '' !== GOOGLE_API_KEY ) {
		$api['key'] = GOOGLE_API_KEY;
	}

	return $api;
}
