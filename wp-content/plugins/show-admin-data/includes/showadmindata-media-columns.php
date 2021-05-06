<?php

/**
 * Add Media custom columns.
 */

function showadmindata_add_media_columns( $columns ) {
	$new_columns = array();

	foreach ($columns as $key => $column) {
		
		// Remove Uploaded To column
		if ( 'parent' !== $key ) {

			$new_columns[ $key ] = $column;

			// Add new columns after date column
			if ( 'date' === $key ) {
				$new_columns['alt'] = 'Alt Text';
			}
			
		}

	}

	return $new_columns;
}

function showadmindata_add_media_columns_data( $column_name, $post_id ) {
	global $post;

	if ( 'alt' === $column_name ) {
		echo get_post_meta( $post_id, '_wp_attachment_image_alt', true);
	}
}
