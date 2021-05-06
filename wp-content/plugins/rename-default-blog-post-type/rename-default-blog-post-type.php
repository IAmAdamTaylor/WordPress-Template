<?php

/**
 * Main plugin file.
 *
 * @package    Rename 'Post' Post Type
 * @license    GPL2
 * @author     Adam Taylor <AdamTaylor@core-marketing.co.uk>
 */

/**
 * Plugin Name: Rename 'Post' Post Type
 * Plugin URI: https://www.core-marketing.co.uk/
 * Description: Changes the labels for the default 'post' Post Type to better represent their use in this website. The text that the labels are changed to can be edited in the main plugin file.
 * Version: 1.0.0
 * Author: Adam Taylor <AdamTaylor@core-marketing.co.uk>
 * Author URI: https://www.core-marketing.co.uk/
 * License: GPL2
 */

/*  Copyright 2018  Core Marketing  (email : AdamTaylor@core-marketing.co.uk)

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
* Class wrapper for functionality.
*/
class RPT_Rename_Posts {
    
    /**
     * The new names for the post type
     */
    const SINGULAR_NAME = 'News';
    const PLURAL_NAME = 'News';

    /**
     * The post type this class runs on.
     */
    const POST_TYPE = 'post';

    function __construct() {
        add_action( 'registered_post_type', array( $this, 'after_post_type_registered' ), 20, 2 );
    }

    /**
     * Fires after a post type is registered.
     *
     * @since 3.3.0
     * @since 4.6.0 Converted the `$post_type` parameter to accept a WP_Post_Type object.
     *
     * @param string       $post_type        Post type.
     * @param WP_Post_Type $post_type_object Arguments used to register the post type.
     */
    public function after_post_type_registered( $post_type, $post_type_object ) {
        if ( self::POST_TYPE === $post_type ) {
            $labels = &$post_type_object->labels;

            $singular = self::SINGULAR_NAME;
            $plural = self::PLURAL_NAME;
            $plural_lowercase = strtolower( self::PLURAL_NAME );

            $labels->name = "{$plural}";
            $labels->singular_name = "{$singular}";
            $labels->add_new = "Add a {$singular} Item";
            $labels->add_new_item = "Add a {$singular} Item";
            $labels->edit_item = "Edit {$singular}";
            $labels->new_item = "{$singular}";
            $labels->view_item = "View {$singular}";
            $labels->search_items = "Search {$plural}";
            $labels->not_found = "No {$plural_lowercase} found";
            $labels->not_found_in_trash = "No {$plural_lowercase} found in Trash";
            $labels->all_items = "All {$plural}";
            $labels->menu_name = "{$plural}";
            $labels->name_admin_bar = "{$plural}";
        }
    }
}

$rpt_rename_posts = new RPT_Rename_Posts();
