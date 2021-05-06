<?php

/**
 * WordPress Menu Query loader.
 * Allows developers to query a WordPress menu with a similar interface to WP_Query.
 * @package    WP_Menu_Query
 * @subpackage WP_Menu_Query
 * @author     Adam Taylor <sayhi@adamtaylor.dev>
 */

// Custom exception handler 
class WP_Menu_Query_Exception extends Exception {}

// Simple key, value pair storage
global $wp_menu_query_cache;
$wp_menu_query_cache = array();

require_once 'class-wp-menu-query.php';
require_once 'class-wp-menu-item.php';
