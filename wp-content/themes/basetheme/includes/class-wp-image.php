<?php

/**
 * Wrapper around a WordPress media library image.
 * 
 */
class WP_Image {
  /**
   * The image ID.
   * @var integer
   */
  public $ID;

  /**
   * The size label of the image.
   * @var string
   */
  private $_size;

  /**
   * The image source URI.
   * @var string
   */
  public $src;

  /**
   * The image alt text.
   * @var string
   */
  public $alt;

  /**
   * The image caption, added in the Media Library.
   * @var string
   */
  public $caption;

  /**
   * The image width in pixels.
   * @var integer
   */
  public $width;

  /**
   * The image height in pixels.
   * @var integer
   */
  public $height;

  function __construct( $image_id, $size = 'full' ) {
    $this->ID = $image_id;
    $image_post = get_post( $image_id );

    if ( $image_post && is_a( $image_post, 'WP_Post' ) ) {
      $this->caption = $image_post->post_excerpt;
    }

    $this->setSize( $size );
  }

  /**
   * Check if the image exists in the WordPress media library.
   * @return boolean
   */
  public function exists() {
    return '' !== $this->src;
  }

  /**
   * Get the set of attributes required to show this image on an <img> tag.
   * @param string|array $other_sizes  Any other WordPress defined sizes to get 
   *                                   as well the base size.
   *                                   These sizes will be added as srcset URIs.
   * @return string
   */
  public function getAttributes( $other_sizes = array() ) {
    // Convert strings into valid array formats
    if ( is_string( $other_sizes ) && strpos( $other_sizes, "," ) ) {
      $other_sizes = explode( ",", $other_sizes );
    }
    
    if ( !empty( $other_sizes ) && !is_array( $other_sizes ) ) {
      $other_sizes = array( $other_sizes );
    }

    if ( empty( $other_sizes ) ) {
      // if only one size is needed, return without srcset attribute
      return sprintf(
        'src="%s" alt="%s" width="%s" height="%s"',
        $this->src,
        $this->alt,
        $this->width,
        $this->height
      );
    } else {
      // If we have both, build a srcset attribute
      array_unshift( $other_sizes, $this->_size );

      foreach ($other_sizes as $key => &$value) {
        $sizeImageObject = wp_get_attachment_image_src( $this->ID, trim( $value ) );
        $value = $sizeImageObject[0] . ' ' . $sizeImageObject[1] . 'w';

        unset( $value );
      }

      return sprintf(
        'src="%s" srcset="%s" alt="%s" width="%s" height="%s"',
        $this->src,
        implode( ',', $other_sizes ),
        $this->alt,
        $this->width,
        $this->height
      );
    }
  }

  /**
   * Get the size of the image.
   * @return string|array
   */
  public function getSize() {
    return $this->_size;
  }

  /**
   * Set the size of the image.
   * @param  string|array $size Any valid image size, or an array of 
   *                            width and height values in pixels (in that order).
   * @return self
   */
  public function setSize( $size ) {
    $this->_size = $size;

    $imageObject = wp_get_attachment_image_src( $this->ID, $this->_size );

    if ( $imageObject ) {
      $this->src = $imageObject[0];
      $this->alt = get_post_meta( $this->ID, '_wp_attachment_image_alt', true );
      $this->width = $imageObject[1];
      $this->height = $imageObject[2];
    } else {
      $this->src = '';
      $this->alt = '';
      $this->width = 0;
      $this->height = 0;
    }

    return $this;
  }
}
