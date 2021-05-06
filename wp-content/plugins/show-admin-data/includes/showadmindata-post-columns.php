<?php

/**
 * Add Posts custom columns.
 */

function showadmindata_add_post_columns( $columns ) {
  $new_columns = array();

  foreach ($columns as $key => $value) {
    if ( $key == 'title' ) {
      $new_columns['post_thumbnail'] = "Thumbnail";
    }
    $new_columns[ $key ] = $value;

  }

  return $new_columns;
}


function showadmindata_add_post_columns_data( $column_name, $post_id ) {
  global $post;

  if ( 'post' == $post->post_type ) {

    switch($column_name) {
      case 'post_thumbnail':
        if ( has_post_thumbnail() ) {
          
          $thumbnail_id = get_post_thumbnail_id();
          $thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
          $thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
          ?>
          
          <a href="<?php echo get_the_permalink( $post_id ); ?>">
            <img src="<?php echo $thumbnail[0]; ?>" alt="<?php echo $thumbnail_alt; ?>" width="<?php echo $thumbnail[1]; ?>" height="<?php echo $thumbnail[2]; ?>" style="display:block; max-width:100%; width:100px; height:auto;" >
          </a>

          <?php
          
        } else {
          ?>

          <span aria-hidden="true">—</span>
          <span class="screen-reader-text">No thumbnail</span>

          <?php
        }

        break;

      default:
        break;
    }
      
  }

}
