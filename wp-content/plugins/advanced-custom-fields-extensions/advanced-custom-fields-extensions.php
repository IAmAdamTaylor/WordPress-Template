<?php

/**
 * Main plugin file.
 *
 * @package    Advanced Custom Fields Extensions
 * @license    GPL2
 * @author     Adam Taylor <sayhi@adamtaylor.dev>
 */

/**
 * Plugin Name: Advanced Custom Fields Extensions
 * Description: Extends functionality for the Advanced Custom Fields (ACF) plugin. Adds a default options page, promotes ACF metaboxes and adds custom location rules.
 * Version: 3.0.0
 * Author: Adam Taylor <sayhi@adamtaylor.dev>
 * Author URI: https://iamadamtaylor.dev
 * License: GPL2
 */

/*  Copyright 2019  Adam Taylor  (email : sayhi@adamtaylor.dev)

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

/**
 * Change the order of custom fields (created by plugins) on edit pages
 */
add_filter( 'acf/input/meta_box_priority', 'acfext_metabox_priority' );
function acfext_metabox_priority() {
	return 'high';
}

/**
 * Make the Yoast SEO metabox low priority, if it exists.
 * This will force it to load lower down the page than other metaboxes.
 * @return string
 */
add_filter( 'wpseo_metabox_prio', 'yoastext_seo_metabox_priority' );
function yoastext_seo_metabox_priority() {
  return 'low';
}

/**
 * Add an options page.
 */
add_action( 'init', 'acfext_setup_default_options_page' );
function acfext_setup_default_options_page() {
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page( array(
			'page_title' => 'Options',
			'position' => '58.9'
		) );        
	}
}


/**
 * Add a custom location rule to ACF: Has Post Parent
 */

add_filter('acf/location/rule_types', 'acfext_add_location_rules_types', 10, 1);
function acfext_add_location_rules_types( $choices ) {
    $choices['Post']['has_post_parent'] = 'Has Post Parent';

    return $choices;
}

add_filter('acf/location/rule_values/has_post_parent', 'acfext_add_location_rules_values_has_post_parent', 10, 1);
function acfext_add_location_rules_values_has_post_parent( $choices ) {
	$choices['true'] = 'True';
	$choices['false'] = 'False';

	return $choices;
}

add_filter('acf/location/rule_match/has_post_parent', 'acfext_add_location_rules_match_has_post_parent', 10, 3);
function acfext_add_location_rules_match_has_post_parent( $match, $rule, $options ) {
	$current_post = get_post( $options['post_id'] );

	if ( !$current_post ) {
    	return false;
	}

	$post_parent = $current_post->post_parent;
	$value = ( 'true' == $rule['value'] ) ? true : false;
	$operator = $rule['operator'];

	if ( 
		( $operator == "==" && $value ) ||
		( $operator == "!=" && !$value )
	) {
		$match = ( 0 != $post_parent );
	} elseif (
		( $operator == "==" && !$value ) ||
		( $operator == "!=" && $value )
	) {
		$match = ( 0 == $post_parent );
	}

	return $match;
}


/**
 * Add inline WYSIWYG toolbar support
 */

function acfext_add_inline_text_wysiwyg_toolbar( $toolbars ) {
  // Add a new toolbar called "Inline" - this toolbar has only 1 row of buttons
  // | characters are section separators between grouped buttons
  $toolbars['Inline' ] = array();
  $toolbars['Inline' ][1] = explode(',', 'forecolor,backcolor,|,bold,italic,underline,strikethrough,charmap,|,pastetext,removeformat,spellchecker,|,link,unlink,|,undo,redo');

  return $toolbars;
}

function acfext_style_inline_toolbar_wysiwyg() {
  ?><style>
    .acf-editor-wrap[data-toolbar="inline"] iframe {
      min-height: 100px;
      max-height: 155px;
    }
    .acf-editor-wrap[data-toolbar="inline"] textarea {
      height: 160px !important; /* beat inline style */
    }
  </style><script>
    if( typeof acf.add_action !== 'undefined' ) {   
      // Filter WYSIWYG field settings if inline toolbar used
      acf.add_filter( 'wysiwyg_tinymce_settings', function(init, id, $field) {      
        if ( $field.find('.acf-editor-wrap[data-toolbar="inline"]').length ) {
          init.wpautop = false;
          init.forced_root_blocks = false;
          init.forced_root_block = '';
        }

        return init;
      } );
    }
  </script><?php
}

function acfext_format_inline_text_wysiwyg_value( $value, $post_id, $field ) {
	if ( 'inline' === $field['toolbar'] ) {
		/**
		 * Filter allowed tags to allow overrides on a per-line level.
		 * add_filter('acf/format_value/type=wysiwyg/inline_tags', 'FUNC', 20, 2);
		 *
		 * @param strng $allowed_tags The tags that will remain in the transformed HTML.
		 * @param strng $html         The original HTML passed.
		 */
		$allowed_tags = apply_filters('acf/format_value/type=wysiwyg/inline_tags', '<a> <abbr> <b> <br> <circle> <code> <del> <em> <i> <path> <rect< <small> <span> <strong> <svg> <sub> <sup> <u> <use>', $value);
		return strip_tags($value, $allowed_tags);
	}

	return $value;
}

add_action('admin_head', 'acfext_style_inline_toolbar_wysiwyg');
add_filter('acf/fields/wysiwyg/toolbars', 'acfext_add_inline_text_wysiwyg_toolbar');
add_filter('acf/format_value/type=wysiwyg', 'acfext_format_inline_text_wysiwyg_value', 20, 3);
