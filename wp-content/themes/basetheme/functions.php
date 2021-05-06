<?php

/**
 * Load a separate functions file.
 * @param  string $filename The filename to load.
 */
function _load_include( $filename ) {
  require_once 'includes/' . $filename . '.php';
}

/**
 * Load included files.
 */
_load_include( 'class-wp-image' );
_load_include( 'asset-paths' );
_load_include( 'dom-classes' );
_load_include( 'wordpress-menu-query/load' );

/**
 * Set up the theme
 */
add_action( 'after_setup_theme', 'basetheme_setup_theme' );
function basetheme_setup_theme() {
	load_theme_textdomain( 'basetheme' );

	// Turn off the admin bar. Opinionated.
	show_admin_bar( false );

	add_filter( 'embed_oembed_html', 'wrap_oembed_in_ratio_container', 99, 4 );
}

function wrap_oembed_in_ratio_container( $cache, $url, $attr, $post_ID ) {
  // Add these classes to all embeds.
  $classes = array(
  );
  $anchor_id = '';

  if ( 
  	false !== strpos( $url, 'vimeo.com' ) || 
  	false !== strpos( $url, 'youtube.com' )
  ) {
  	// For video providers, create a 16:9 container
    $classes[] = 'ratio';
    $classes[] = 'ratio--16:9';

    // Add the video ID as an anchor ID
    $match = preg_match('/(\d+)/', $url, $matches);
    if ( $matches ) {
	    $anchor_id = $matches[1];
    }
  }

  $ret = sprintf( 
  	'<div %s class="%s">%s</div>',
  	( $anchor_id ? 'id="' . $anchor_id . '"' : '' ),
  	esc_attr( implode( ' ', $classes ) ),
  	$cache
  );

  return $ret;
}

/**
 * Get a post with a specific slug.
 * @param  string  $slug      A slug compatible string (lowercase, no spaces).
 * @param  string  $post_type A valid post type, post, page or a custom type.
 * @return WP_Post 						A post object if it exists, null otherwise.
 */
function get_post_by_slug( $slug, $post_type = 'post' ) {
	if ( !$slug ) {
		return null;
	}
	
	$args = array(
	  'name'   => $slug,
	  'post_type'   => $post_type,
	  'post_status' => 'publish',
	  'numberposts' => 1
	);

	$posts = get_posts( $args );

	return $posts ? reset( $posts ) : null;
}

/**
 * Gets a path to a template part.
 * @param  string $slug   The slug name for the generic template.
 * @param  string $name   Optional. The name of the specialised template.
 */
function get_template_part_location( $slug, $name = null ) {
  // From WordPress's get_template_part()

  /**
   * Fires before the specified template part file is loaded.
   *
   * The dynamic portion of the hook name, `$slug`, refers to the slug name
   * for the generic template part.
   *
   * @since 3.0.0
   *
   * @param string      $slug The slug name for the generic template.
   * @param string|null $name The name of the specialized template.
   */
  do_action( "get_template_part_{$slug}", $slug, $name );

  $templates = array();
  $name = (string) $name;
  if ( '' !== $name ) { 
    $templates[] = "{$slug}-{$name}.php";
  }

  $templates[] = "{$slug}.php";

  return locate_template($templates);
}

/**
 * Gets a template part and allows passing of variables from the calling context.
 * @param  string $slug   The slug name for the generic template.
 * @param  string $name   Optional. The name of the specialised template.
 * @param  array  $params An array of variables that will be available in the template part.
 *                        The array keys will become variable names.
 */
function get_contextual_template_part( $slug, $name = null, $params = array() ) {
	// Unpack the params for our custom function
	if ( count( $params ) > 0 ) {
		/**
	   * Fires before the specified params are unpacked.
	   *
	   * The dynamic portion of the hook name, `$slug`, refers to the slug name
	   * for the generic template part.
	   *
	   * @param array       $params The params passed to the function.
	   * @param string      $slug   The slug name for the generic template.
	   * @param string|null $name   The name of the specialized template.
	   */
	  $params = apply_filters( "get_contextual_template_part_params", $params, $slug, $name );
	  $params = apply_filters( "get_contextual_template_part_params_{$slug}", $params, $slug, $name );

		extract( $params );
	}

	// From WordPress's get_template_part()

	/**
   * Fires before the specified template part file is loaded.
   *
   * The dynamic portion of the hook name, `$slug`, refers to the slug name
   * for the generic template part.
   *
   * @since 3.0.0
   *
   * @param string      $slug The slug name for the generic template.
   * @param string|null $name The name of the specialized template.
   */
  do_action( "get_template_part_{$slug}", $slug, $name );

  $templates = array();
  $name = (string) $name;
  if ( '' !== $name ) {	
    $templates[] = "{$slug}-{$name}.php";
  }

  $templates[] = "{$slug}.php";

  $template = locate_template($templates);
  if ( $template ) {
	  include $template;
  }
}

/**
 * Get the privacy policy permalink.
 * If added through WordPress's new privacy menu, that will be returned with a string fallback if not.
 * @return string 
 */
function get_privacy_page_permalink() {
	$option = get_option( 'wp_page_for_privacy_policy' );

	if ( $option ) {
		$permalink = get_the_permalink( $option );
	} else {
		$permalink = home_url( '/privacy-policy/' );
	}

	return apply_filters( 'privacy_page_permalink', $permalink );
}
