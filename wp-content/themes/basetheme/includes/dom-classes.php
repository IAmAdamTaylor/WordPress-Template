<?php

// Remove certain classes from the body class.
add_filter( 'body_class', 'basetheme_remove_body_classes', 10, 2 );
add_filter( 'body_class', 'basetheme_add_post_name_as_body_class', 10, 2 );

/**
 * Remove specific classes from the <body> so that we can add them to the <html>
 * @param  array $wp_classes     WordPress defined classes.
 * @param  array $extra_classes  Extra classes defined by the user.
 * @return array
 */
function basetheme_remove_body_classes( $wp_classes, $extra_classes ) {
  $blacklist = array(
    'error404',
  );

  foreach ($wp_classes as $key => $value) {
    if ( in_array( $value, $blacklist ) ) {
      unset( $wp_classes[ $key ] );
    }
  }

  return array_merge( $wp_classes, (array) $extra_classes );
}

/**
 * Add the post slug to the body_class.
 * @param  array $wp_classes     WordPress defined classes.
 * @param  array $extra_classes  Extra classes defined by the user.
 * @return array
 */
function basetheme_add_post_name_as_body_class( $wp_classes, $extra_classes ) {  
  global $post;

  if ( isset( $post, $post->post_name, $post->post_type ) ) {
    $wp_classes[] = $post->post_type . '-' . $post->post_name;
  }

  return array_merge( $wp_classes, (array) $extra_classes );
}

/**
 * Get classes for the <html> element.
 * Echoes the classes directly.
 * @param  array  $extra_classes Extra classes defined by the user.
 */
function html_class( $extra_classes = array() ) {
  $html_class = array(
    'no-js'
  );

  if ( is_404() ) {
    $html_class[] = 'error404';
  }

  // Add extra classes
  $html_class = array_merge( $html_class, (array) $extra_classes );

  /**
   * Filter the <html> classes.
   * @param array $html_class The classes currently defined.
   * @return array
   */
  $html_class = apply_filters( 'html_class', $html_class );

  echo sprintf( 'class="%s"', implode( ' ', $html_class ) );
}
