<?php

/**
 * Main plugin file.
 *
 * @package    Show Admin Data
 * @license    GPL2
 * @author     Adam Taylor <sayhi@adamtaylor.dev>
 */

/**
 * Plugin Name: Show Admin Data
 * Description: Enhances the default post types within WordPress, page, post and media, with custom columns to show relevant data. Also shows the post title in the tab when editing a post.
 * Version: 1.0.0
 * Author: Adam Taylor <sayhi@adamtaylor.dev>
 * License: GPL2
 */

/*  Copyright 2019 Adam Taylor (email : sayhi@adamtaylor.dev)

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


require_once plugin_dir_path(__FILE__) . 'includes/showadmindata-post-columns.php';
require_once plugin_dir_path(__FILE__) . 'includes/showadmindata-page-columns.php';
require_once plugin_dir_path(__FILE__) . 'includes/showadmindata-media-columns.php';

function showadmindata_show_post_title_when_editing( $admin_title ) {
    global $pagenow;
    global $post;

    // Make sure we are in the admin view
    // And check we are on edit post/page
    if ( is_admin() && in_array( $pagenow, array( 'post.php' ) ) ) {
        $admin_title = 'Edit '.$post->post_title.' &lsaquo; '.get_bloginfo( 'name' );
    }

    return $admin_title;
}

function showadmindata_add_admin_css() {
    ?><style>
        /* New alt column in media library */
        .fixed .column-alt {
            width: 20%;
        }

        /* Style custom columns added to post table */
        .post-type-post .wp-list-table.fixed {
            table-layout: initial !important;
        }
        .post-type-post .wp-list-table.fixed #post_thumbnail,
        .post-type-post .wp-list-table.fixed .post_thumbnail {
            width: 100px;
        }
        .post-type-post .wp-list-table.fixed #title,
        .post-type-post .wp-list-table.fixed .title {
            width: 200px;
        }
    </style><?php
}

add_action('plugins_loaded', 'showadmindata_run');
function showadmindata_run() {
    // Setup WP hooks
    add_filter('manage_edit-page_columns', 'showadmindata_add_page_columns');
    add_action('manage_pages_custom_column', 'showadmindata_add_page_columns_data', 20, 2);

    add_filter('manage_edit-post_columns', 'showadmindata_add_post_columns');
    add_action('manage_posts_custom_column', 'showadmindata_add_post_columns_data', 20, 2);

    add_filter('manage_media_columns', 'showadmindata_add_media_columns');
    add_action('manage_media_custom_column', 'showadmindata_add_media_columns_data', 20, 2);

    add_filter('admin_title', 'showadmindata_show_post_title_when_editing');

    add_action('admin_head', 'showadmindata_add_admin_css');
}
