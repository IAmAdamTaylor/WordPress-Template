<?php

/**
 * Asset Path functions.
 */

/**
 * Get the file path for an asset inside the theme.
 * Checks for the assets existence within the child theme first,
 * and falls back to the base theme if that doesn't exist.
 * @param  string $path The path to the asset relative to the theme directory.
 * @return string|boolean The path to the asset if it exists, or false.
 */
function get_asset_path( $path ) {
	if ( child_has_asset( $path ) ) {
		$dir = get_stylesheet_directory();
	} else {
		$dir = get_template_directory();
	}

	$dir = trailingslashit( $dir );

	/**
	 * Filter the asset path directory.
	 * @param  string $dir  The directory path.
	 * @param  string $path The file path that was passed,
	 * @return string 		  The new path for the directory.
	 */
	$dir = apply_filters( 'get_asset_path_directory', $dir, $path );

	// Using a leading underscore on the variable name 
	// to keep reference to original as well
	$_path = $dir . $path;

	// Check that asset exists, return false if it doesn't
	if ( !file_exists( $_path ) || !is_readable( $_path ) ) {
		return false;
	}

	/**
	 * Filter the found asset path.
	 * @param  string $_path The full asset path.
	 * @param  string $path  The original passed asset path (without the directory).
	 * @return string 			 The new path for the asset.
	 */
	return apply_filters( 'get_asset_path', $_path, $path );
}

/**
 * Get the URI for an asset inside the theme.
 * Checks for the assets existence within the child theme first,
 * and falls back to the base theme if that doesn't exist.
 * @param  string $path The path to the asset relative to the theme directory.
 * @return string|boolean The asset URI if it exists, or false.
 */
function get_asset_uri( $path ) {
	// Check that asset exists, return false if it doesn't
	if ( false === get_asset_path( $path ) ) {
		return false;
	}

	if ( child_has_asset( $path ) ) {
		$dir = get_stylesheet_directory_uri();
	} else {
		$dir = get_template_directory_uri();
	}

	$dir = trailingslashit( $dir );

	/**
	 * Filter the asset directory URI.
	 * @param  string $dir  The directory URI.
	 * @param  string $path The file path that was passed,
	 * @return string 		  The new path for the directory URI.
	 */
	$dir = apply_filters( 'get_asset_uri_directory', $dir, $path );

	// Using a leading underscore on the variable name 
	// to keep reference to original as well
	$_path = $dir . $path;

	/**
	 * Filter the found asset URI.
	 * @param  string $_path The full asset URI.
	 * @param  string $path  The original passed asset path (without the directory URI).
	 * @return string 			 The new URI for the asset.
	 */
	return apply_filters( 'get_asset_uri', $_path, $path );
}

/**
 * Include an asset file into the document.
 * @param  string $path The path to the asset relative to the theme directory.
 */
function include_asset( $path ) {
	$path = get_asset_path( $path );

	if ( $path ) {
		include $path;
	}
}

/**
 * Check if an asset is included within the child theme or not.
 * @param  string  $path The path to the asset, relative to the theme directory.
 * @return boolean       
 */
function child_has_asset( $path ) {
	$child_theme = get_stylesheet_directory();
	$path = trailingslashit( $child_theme ) . $path;

	return file_exists( $path ) && is_readable( $path );
}
