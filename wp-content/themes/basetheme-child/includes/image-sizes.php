<?php

/**
 * Define the custom image sizes used by this theme.
 * @return array The image sizes required.
 */
function basethemechild_get_theme_image_sizes() {
  $image_sizes = array();

  /** Example
  $image_sizes[ 'banner' ] = array(
    'width' => 1600,
    'height' => 500,
    /**
     * Whether to crop the image to the exact dimensions specified.
     * true = hard crop, will lose some of the image.
     * false = soft crop, the whole image will be visible but may not 
     *         be the exact width and height specified above.
     *         The image will be scaled to the largest of the 2 dimensions.
    * /
    'hard_crop' => true,
  );
  //*/

  /**
   * Filter the image sizes.
   * @param array $image_sizes The custom sizes currently defined.
   * @return array
   */
  return apply_filters( 'basethemechild_custom_image_sizes', $image_sizes );
}
