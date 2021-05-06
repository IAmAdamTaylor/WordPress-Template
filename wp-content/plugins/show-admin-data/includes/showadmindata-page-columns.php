<?php

/**
 * Add Pages custom columns.
 */

function showadmindata_add_page_columns( $columns ) {
	$new_columns = array();

	foreach ($columns as $key => $value) {
		$new_columns[ $key ] = $value;
		if ( $key == 'title' ) {
			$new_columns['page_template'] = "Page Template";
			$new_columns['menu_order'] = "Order";
		}
	}

	return $new_columns;
}

function showadmindata_add_page_columns_data( $column_name, $post_id ) {
  global $post;

  if ( 'page' == $post->post_type ) {

    switch($column_name) {
      case 'page_template':
        $template_name = get_page_template_slug( $post->ID );

        if( 0 == strlen( trim( $template_name ) ) || !file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {

          $template_name = 'Default';

        } else {

          $template_contents = file_get_contents( get_stylesheet_directory() . '/' . $template_name );

          $pattern = '/Template ';
          $pattern .= 'Name:(.*)\n/';
          preg_match($pattern, $template_contents, $template_name);

          if ( count($template_name) > 0 ) {
          $template_name = trim($template_name[1]);
          } else {
            $template_name = false;
          }

          if ( !$template_name ) {
            $template_name = 'Default';
          }

        }

        $template_name = str_replace('Page Template', '', $template_name);
        echo $template_name;

        break;

      case 'menu_order':
        $menu_order = $post->menu_order;

        $post_ancestors = get_post_ancestors( $post );
        foreach ($post_ancestors as $key => $value) {
          $ancestor = get_post( $value );

          $menu_order = $ancestor->menu_order . '.' . $menu_order;
        }

        echo $menu_order;
        break;

      default:
        break;
    }
      
  }

}
