<?php

/**
 * Load a separate functions file.
 * @param  string $filename The filename to load.
 */
function _load_child_include( $filename ) {
  require_once 'includes/' . $filename . '.php';
}

/**
 * Load included files.
 */
_load_child_include( 'enqueue-assets' );
_load_child_include( 'image-sizes' );

/**
 * Setup the theme.
 */
add_action( 'after_setup_theme', 'basethemechild_setup_theme' );
function basethemechild_setup_theme() {

	add_theme_support('post-thumbnails');
	load_theme_textdomain( 'CLIENTNAME' );

	/**
	 * Register menus
	 */
	register_nav_menus( array(
    'header-menu' => __( 'Header Menu' ),
    'footer-menu' => __( 'Footer Menu' ),
    'policies'    => __( 'Policies' ),
  ) );

	/**
	 * Add custom image sizes
	 */
	foreach ( basethemechild_get_theme_image_sizes() as $crop_name => $crop_values) {
		add_image_size( $crop_name, $crop_values['width'], $crop_values['height'], $crop_values['hard_crop'] );
	}


	/**
	 * Remove query strings from loaded resources
	 */
	add_action( 'script_loader_src', 'basethemechild_remove_script_version' );
	add_action( 'style_loader_src', 'basethemechild_remove_script_version' );

	
	/**
	 * Load our theme resources
	 */
	add_filter( 'get_asset_uri', 'basethemechild_revision_assets', 20, 2 );
	add_action( 'wp_enqueue_scripts', 'basethemechild_enqueue_styles' );
	add_action( 'wp_enqueue_scripts', 'basethemechild_enqueue_scripts' );
	add_action( 'wp_enqueue_scripts', 'basethemechild_enqueue_google_scripts' );
	
	/**
	 * Clean up header tags
	 * @see https://crunchify.com/how-to-clean-up-wordpress-header-section-without-any-plugin/ 
	 */
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'wp_shortlink_wp_head');
	remove_action('wp_head', 'rest_output_link_wp_head', 10);
	remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
	remove_action('template_redirect', 'rest_output_link_header', 11, 0);
}

/**
 * Remove query string from enqueued assets.
 * Opinionated, as this can cause assets to be cached more often.
 * Plugin or WP core assets will be especially susceptible to this, as they will
 * normally use this query string to force the new version.
 * A good cache invalidation strategy will be needed to ensure this works as intended.
 * @param  string $src The URI of an enqueued asset.
 * @return string      
 */
function basethemechild_remove_script_version( $src ) {
	if ( !is_admin() ) {
		$src = remove_query_arg( 'ver', $src );
	}
	
	return $src;
}

/**
 * Replace any revisioned assets with their new revision names.
 * @param  string $uri  The full URI to the asset.
 * @param  string $path The original path relative to the theme directory.
 * @return string
 */
function basethemechild_revision_assets( $uri, $path ) {
	$manifest_filepath = trailingslashit( get_stylesheet_directory() ) . 'dist/rev-manifest.json';

	if ( file_exists( $manifest_filepath ) && is_readable( $manifest_filepath ) ) {
		$manifest = json_decode( file_get_contents( $manifest_filepath ), true );

		foreach ($manifest as $original_name => $revisioned_name) {
			if ( preg_match( "#" . $original_name . "$#", $uri ) ) {
				$uri = str_replace( $original_name, $revisioned_name, $uri );
				break;
			}
		}
	}

	return $uri;
}
