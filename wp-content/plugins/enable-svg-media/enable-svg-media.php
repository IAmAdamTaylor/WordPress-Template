<?php

/**
 * Enable the SVG mime type for use in WordPress Media Library.
 *
 * @package    Enable SVG in Media Library
 * @license    GPL2
 * @author     Adam Taylor <sayhi@iamadamtaylor.com>
 */

/**
 * Plugin Name: Enable SVG in Media Library
 * Description: Allows SVGs to be uploaded to the WordPress Media Library.
 * Version: 2.0.1
 * Author: Adam Taylor <sayhi@iamadamtaylor.com>
 * License: GPL2
 */

/*  Copyright 2017  I am Adam Taylor  (email : sayhi@iamadamtaylor.com)

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

if ( !class_exists('IAAT_Extension_Support') ) {
    class IAAT_Extension_Support {
        function fixExtensionIfNeeded($originalExtension, $filename) {
            if( $this->extensionIsTooSmall($originalExtension) ) {
                return $this->getExtensionFromFilename($filename);
            }
            return $originalExtension;
        }

        function extensionIsTooSmall($extension) {
            return ( strlen($extension) < 1 );
        }

        function getExtensionFromFilename($filename) {
            $Parts = explode('.', $filename);
            $LowerParts = array_map('strtolower', $Parts);
            $LastPart = array_pop($LowerParts);

            if( $this->dualPartExtension($LastPart) ) {
                $PenultimatePart = array_pop($LowerParts);
                return "{$PenultimatePart}.{$LastPart}";
            } elseif( $this->hasNoExtension($filename) ) {
                return "";
            }

            return $LastPart;
        }

        function isDotFile($filename) {
            return (strpos($filename, '.', 0) === 0);
        }

        function hasNoExtension($filename) {
            return (
                (strpos($filename, '.', 0) === FALSE)
                ||
                (
                    (substr_count($filename, '.') < 2)
                    &&
                    $this->isDotFile($filename)
                )
            );
        }

        function dualPartExtension($extension) {
            return in_array($extension, [
                'gz',
                'xz',
                'bz2',
            ], true);
        }
    }
}

if ( !class_exists('SVG_Support') ) {
    class SVG_Support {
        function init() {
            add_action( 'admin_init', array( $this, 'add_svg_upload' ), 75 );
            add_action( 'admin_head', array( $this, 'custom_admin_css' ), 75 );
            add_action( 'load-post.php', array( $this, 'add_editor_styles' ), 75 );
            add_action( 'load-post-new.php', array( $this, 'add_editor_styles' ), 75 );
            add_action( 'after_setup_theme', array( $this, 'theme_prefix_setup' ), 75 );
            add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_mime_type_svg' ), 75, 4 );
            add_filter( 'wp_generate_attachment_metadata', array( $this, 'ensure_svg_metadata' ), 10, 2 );
            add_filter( 'wp_update_attachment_metadata', array( $this, 'ensure_svg_metadata' ), 10, 2 );
        }

        public function add_svg_upload() {
            add_action( 'wp_ajax_adminlc_mce_svg.css', array( $this, 'tinyMCE_svg_css' ), 10 );
            add_filter( 'image_send_to_editor', array( $this, 'remove_dimensions_svg' ), 10, 1 );
            add_filter( 'upload_mimes', array( $this, 'filter_mimes' ), 10, 1 );
        }

        public function custom_admin_css() {
            echo '<style>';
            $this->custom_css();
            echo '</style>';
        }

        public function add_editor_styles() {
            add_filter( 'mce_css', array( $this, 'filter_mce_css' ) );
        }

        public function theme_prefix_setup() {
            $existing = get_theme_support( 'custom-logo' );
            if ( $existing ) {
                $existing = current( $existing );
                $existing['flex-width'] = true;
                $existing['flex-height'] = true;
                add_theme_support( 'custom-logo', $existing );
            }
        }

        /**
         * @codeCoverageIgnore
         * Simple Wrapper for fixExtensionIfNeeded
         */
        public function fix_mime_type_svg( $data=null, $file=null, $filename=null, $mimes=null ) {
            $OriginalExtension = (isset( $data['ext'] ) ? $data['ext'] : '');
            $extension_support = new IAAT_Extension_Support();
            $ext = $extension_support->fixExtensionIfNeeded($OriginalExtension, $filename);
            if( $ext === 'svg' ) {
                $data['type'] = 'image/svg+xml';
                $data['ext'] = 'svg';
            }
            return $data;
        }

        public function ensure_svg_metadata( $data, $id ) {
            $attachment = get_post( $id );
            $mime_type = $attachment->post_mime_type;

            if ( $mime_type == 'image/svg+xml' ) {
                if( $this->missingOrInvalidSVGDimensions( $data ) ) {
                    $xml = simplexml_load_file( get_attached_file( $id ) );
                    $attr = $xml->attributes();
                    $viewbox = explode( ' ', $attr->viewBox );

                    $this->fillSVGDimensions( $viewbox, $attr, $data, 'width', 2 );
                    $this->fillSVGDimensions( $viewbox, $attr, $data, 'height', 3 );
                }
            }
            return $data;
        }

        //
        // End of constructor functions.
        //

        public function tinyMCE_svg_css() {
            header( 'Content-type: text/css' );
            $this->custom_css();
            exit();
        }

        public function remove_dimensions_svg( $html = '' ) {
            return str_ireplace( [ " width=\"1\"", " height=\"1\"" ], "", $html );
        }

        public function filter_mimes( $mimes = [] ){
            $mimes[ 'svg' ] = 'image/svg+xml';
            return $mimes;
        }

        //
        // End of admin_init hook functions
        //

        public function filter_mce_css( $mce_css ) {
            global $current_screen;
            $mce_css .= ', ' . get_admin_url( 'admin-ajax.php?action=adminlc_mce_svg.css' );
            return $mce_css;
        }

        //
        // End of filter mce css hook
        //

        protected function custom_css() {
            $css = '';

            $css .= '.thumbnail img[src$=".svg"]:not(.emoji)';
            $css .= ', .acf-field-image img[src$=".svg"]:not(.emoji)';

            $css .= '{ width: 100% !important; height: auto !important; }';
            
            echo $css;
        }

        protected function missingOrInvalidSVGDimensions( $data ) {
            if(!is_array($data)) return true;
            if(!isset($data['width']) || !isset($data['height']) ) return true;
            if(is_nan($data['width']) || is_nan($data['height']) ) return true;
            return (
                empty( $data ) || empty( $data['width'] ) || empty( $data['height'] )
                ||
                intval($data['width'] < 1) || intval($data['height'] < 1)
            );
        }

        protected function fillSVGDimensions( $viewbox, $attr, &$data, $dimension, $viewboxoffset ) {
            if ( isset( $attr->{ $dimension } ) ) {
                $data[ $dimension ] = intval( $attr->{ $dimension } );
            }
            if( !isset( $data[ $dimension ] ) ) {
                $data[ $dimension ] = 0;
            }
            if ( is_nan( $data[ $dimension ] ) ) {
                $data[ $dimension ] = 0;
            }
            if ( $data[ $dimension ] < 1 ) {
                $data[ $dimension ] = count($viewbox) == 4 ? intval( $viewbox[$viewboxoffset] ) : null;
            }
        }
    }
}

add_action( 'plugins_loaded', 'svgsupp_init' );
function svgsupp_init() {
    $svg_support = new SVG_Support();
    $svg_support->init();
}
