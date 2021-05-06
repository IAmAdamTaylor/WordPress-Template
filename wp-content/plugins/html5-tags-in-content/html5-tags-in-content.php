<?php

/**
 * Main plugin file.
 *
 * @package    HTML5 Tags in WordPress Content
 * @license    GPL2
 * @author     Adam Taylor <AdamTaylor@core-marketing.co.uk>
 *
 * List of tag enhancements:
 * - <img> wrapped with <figure> and <figcaption>
 */

/**
 * Plugin Name: HTML5 Tags in WordPress Content
 * Description: Converts tags and shortcodes in the WordPress post_content field into their HTML5 equivalents. See the full list of enhancements at the top of the main plugin file.
 * Version: 1.0.0
 * Author: Adam Taylor <AdamTaylor@core-marketing.co.uk>
 * License: GPL2
 */

/*  
  Copyright 2018  Core Marketing  (email : AdamTaylor@core-marketing.co.uk)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !class_exists('HTML5_Tags_In_Content') ) {
  // Class wrapper to contain functionality
  class HTML5_Tags_In_Content {
    public function __construct() {
      add_filter( 'img_caption_shortcode', array( $this, 'replace_caption_shortcode' ), 10, 3 );
      add_filter( 'the_content', array( $this, 'replace_image_autop_wrappers' ), 999 );
    }        

    /**
     * Improves the WordPress caption shortcode with HTML5 figure & figcaption, microdata & wai-aria attributes
     *
     * Author: @joostkiens
     * @see  https://gist.github.com/JoostKiens/4477366 Github Gist where this code was found.
     * Licensed under the MIT license
     *
     * @param  string $val     Empty
     * @param  array  $attr    Shortcode attributes
     * @param  string $content Shortcode content
     * @return string          Shortcode output
     */
    public function replace_caption_shortcode( $value, $attr, $content = null ) {
      extract( shortcode_atts( array(
        'id'      => '',
        'align'   => 'aligncenter',
        'width'   => '',
        'caption' => ''
      ), $attr ) );
          
      // No caption, no dice...
      if ( 1 > (integer)$width || empty( $caption ) ) {
        return $val;
      }
       
      if ( $id ) {
        $id = esc_attr( $id );
      } else {
        $id = uniqid();
      }
           
      // Add itemprop="contentURL" to image - Ugly hack
      $content = str_replace('<img', '<img itemprop="contentURL"', $content);

      $output = '';

      $output .= sprintf( 
        '<figure id="%1$s" aria-describedby="figcaption_%1$s" class="wp-caption %2$s" itemscope itemtype="http://schema.org/ImageObject">',
        $id,
        esc_attr( $align )
      );
      $output .= do_shortcode( $content );
      $output .= '<figcaption id="figcaption_'. $id . '" class="wp-caption-text" itemprop="description">' . $caption . '</figcaption>';
      $output .= '</figure>';

      return $output;
    }

    /**
     * Wrap img tags in WordPress content in figure tags instead of auto-generated p tags.
     * @param  string $content The post content.
     * @return string
     */
    public function replace_image_autop_wrappers( $content ) {
      // Add itemprop="contentURL" to image - Ugly hack
      $content = str_replace('<p><img', '<p><img itemprop="contentURL"', $content);

      $content = preg_replace( 
        '/<p>(.*<img.*(class=\"[^\"]*\")[^>]*>.*)<\/p>/', 
        '<figure $2 itemscope itemtype="http://schema.org/ImageObject">$1</figure>', 
        $content 
      );

      return $content; 
    }
  }

  $_html5_tags_in_content_instance = new HTML5_Tags_In_Content();
}
