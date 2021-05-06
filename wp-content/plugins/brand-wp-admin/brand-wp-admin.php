<?php

/**
 * Main plugin file.
 *
 * @package    Brand WordPress Admin
 * @license    GPL2
 * @author     NAME <sayhi@adamtaylor.dev>
 */

/**
 * Plugin Name: Brand WordPress Admin
 * Description: Add custom branding to the WordPress admin. Choose a logo to show on the WordPress login page and a favicon to show on any WordPress admin tabs.
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

require_once plugin_dir_path(__FILE__) . 'includes/class-bwpa-options.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-bwpa-settings-page.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bwpa-activator.php
 */
function activate_bwpa() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bwpa-activator.php';
    Brand_WP_Admin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bwpa-deactivator.php
 */
function deactivate_bwpa() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bwpa-deactivator.php';
    Brand_WP_Admin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bwpa' );
register_deactivation_hook( __FILE__, 'deactivate_bwpa' );


/**
 * Enqueue the plugin assets.
 */
function bwpa_enqueue_admin_assets() {
    $current_screen = get_current_screen();

    if ( 'settings_page_brandwpadmin-settings' == $current_screen->id ) {
        wp_enqueue_media();
        wp_enqueue_style( 'bwpa_css', plugin_dir_url(__FILE__) . 'css/bwpa-admin.css', array(), '1.0.0', 'all' );
        wp_register_script( 'bwpa_js', plugin_dir_url(__FILE__) . 'js/bwpa-admin.js', array('jquery', 'media-upload', 'thickbox'), '1.0.0', true );
        wp_enqueue_script('bwpa_js');
    }
}

/**
 * Show the custom admin favicon if set.
 */
function bwpa_show_admin_favicon() {
    $options = new Brand_WP_Admin_Options();
    $options->load();

    if ( !$options ) {
        return;
    }

    if ( $options->favicon_id ) {
        $favicon = wp_get_attachment_image_src( $options->favicon_id, 'full' );

        if ( $favicon ) {
            $favicon_src = $favicon[0];
        } else {
            $favicon_src = bwpa_get_default_favicon_uri();
        }

    } else {
        $favicon_src = bwpa_get_default_favicon_uri();
    }

    ?><link rel="shortcut icon" href="<?php echo esc_attr( $favicon_src ); ?>" /><?php
}

/**
 * Get the default admin favicon.
 * @return string 
 */
function bwpa_get_default_favicon_uri() {
    return trailingslashit( plugin_dir_url(__FILE__) ) . 'images/favicon.ico';
}

function bwpa_use_siteurl_on_login_page( $site_url ) {
    $site_url = home_url();

    return $site_url;
}

function bwpa_use_sitename_on_login_page( $site_title ) {
    $site_title = get_bloginfo( 'name' );

    return $site_title;
}

function bwpa_change_login_logo() {
    $options = new Brand_WP_Admin_Options();
    $options->load();

    if ( !$options || !isset($options->logo_id) ) {
        return;
    }

    $logo = wp_get_attachment_image_src( $options->logo_id, 'full' );

    if ( !$logo ) {
        return;
    }

    $src = $logo[0];

    // Constrain height of image to a 320px width
    $width = $logo[1];
    $height = $logo[2];

    if ( $width > $options->logo_width ) {
        $height = $options->logo_width * ( $height / $width );
    }

    ?><style type="text/css">
        body.login div#login h1 a {
            width: 100%;
            height: <?php echo floor($height); ?>px;
            background-image: url(<?php echo $src; ?>);
            background-position: center center;
            background-repeat: no-repeat;
            background-size: contain;
        }
    </style><?php
}

add_action('plugins_loaded', 'bwpa_run');
function bwpa_run() {
    // Setup WP hooks
    add_action( 'admin_enqueue_scripts', 'bwpa_enqueue_admin_assets' );
    add_action( 'admin_head', 'bwpa_show_admin_favicon' );

    add_filter( 'login_headerurl', 'bwpa_use_siteurl_on_login_page' );

    global $wp_version;
    if ( version_compare( $wp_version, '5.2', '>=' ) ) {
        $login_link_title_hook = 'login_headertext';
    } else {
        $login_link_title_hook = 'login_headertitle';
    }
    add_filter( $login_link_title_hook, 'bwpa_use_sitename_on_login_page' );

    add_action( 'login_enqueue_scripts', 'bwpa_change_login_logo' );

    $settings_page = new Brand_WP_Admin_Settings( plugin_dir_path(__FILE__) );
    add_action( 'admin_menu', array( $settings_page, 'add_theme_page' ) );
    add_action( 'admin_init', array( $settings_page, 'register_settings' ) );
}
