<?php

/**
 * Allows developers to query a WordPress menu with a similar interface to WP_Query.
 *
 * @link       https://github.com/IAmAdamTaylor/WP_Menu_Query
 * @since      1.0.0
 *
 * @package    WP_Menu_Query
 * @subpackage WP_Menu_Query
 */

/**
 * Allows developers to query a WordPress menu with a similar interface to WP_Query.
 *
 * @package    WP_Menu_Query
 * @subpackage WP_Menu_Query
 * @author     Adam Taylor <sayhi@adamtaylor.dev>
 */
if ( !class_exists('WP_Menu_Query') ) {
class WP_Menu_Query {

	/**
	 * The query args passed to the class.
	 * @var array
	 */
	public $query_vars;

	/**
	 * Filled with WP_Menu_Items when the items are fetched.
	 * @var array
	 */
	public $items;

	/**
	 * The total number of items found.
	 * @var array
	 */
	public $item_count;
	/**
	 * Alias of the number of items found.
	 * This class has no paging so these are equivalent.
	 */
	public $found_items;

	/**
	 * The index of the current item being displayed.
	 * Available during the loop.
	 * @var integer
	 */
	public $current_item;

	/**
	 * The current item being displayed.
	 * Available during the loop.
	 * @var WP_Menu_Item
	 */
	public $item;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    array    $args Optional, A set of query args to initialise the query.
	 *                          Defaults to null so that an instance can be created
	 *                          without args, and items fetched later.
	 */
	public function __construct( $args = null ) {
		if ( null !== $args ) {
			$this->query( $args );
		}
	}

	private function _get_default_args() {
		return array(
			/**
			 * The location the menu is attached to.
			 * @var string
			 */
			'location' => '',
			/**
			 * Specific menu items to include. Only these items 
			 * (if they exist in the menu) will be output.
			 * 
			 * Pass a URL to be matched against each menu item.
			 * Relative URLs are valid and will be mapped relative to the home URL.
			 * @var string 
			 */
			'include' => array(),
			/**
			 * Specific menu items to exclude. Any matching items will be removed.
			 * 
			 * Pass a URL to be matched against each menu item.
			 * Relative URLs are valid and will be mapped relative to the home URL.
			 */
			'exclude' => array(),
			/**
			 * Limit the amount of top level menu items returned.
			 * Use -1 to show all menu items.
			 * @var integer
			 */
			'limit' => -1,
			/**
			 * Limit the amount of child menu items returned.
			 * Use -1 to show all child menu items.
			 * @var integer
			 */
			'limit_children' => -1,
			/**
			 * The number of items to skip from the start of the menu before output.
			 * Will only be applied to top level items (parent == 0)
			 * @var integer
			 */
			'offset' => 0,
			/**
			 * Pass a specific URL to get child items for that URL.
			 * Pass an empty string to get top-level items.
			 *
			 * Absolute or relative URLs are both valid.
			 * Relative URLs will be mapped relative to the home URL.
			 * @var string
			 */
			'parent' => '',
		);
	}

	/**
	 * Get the value of a query arg.
	 * @param  string $var_name The name of a query arg.
	 * @return mixed
	 */
	public function get( $var_name ) {
		return $this->query_vars[ $var_name ];
	}

	/**
	 * Set the value of a query arg.
	 * @param string $var_name The name of a query arg.
	 * @param mixed  $value    The new query arg value.
	 */
	public function set( $var_name, $value ) {
		$this->query_vars[ $var_name ] = $value;
	}

	/**
	 * Check whether or not there are currently items 
	 * left to process while in the loop.
	 * @return boolean
	 */
	public function have_items() {
		return $this->current_item < count( $this->items );
	}

	/**
	 * Move to the next item and return it to the caller.
	 * Used in the loop.
	 * @return WP_Menu_Item The next item in the current query.
	 */
	public function the_item() {
		if ( isset( $this->items[ $this->current_item ] ) ) {
			$this->item = $this->items[ $this->current_item ];
			$this->current_item++;

			return $this->item;
		}

		return null;
	}

	/**
	 * Reset the internal pointers back to the start.
	 */
	public function rewind_items() {
		$this->current_item = 0;
		$this->item = null;
	}
	// Alias
	public function reset_items() {
		$this->rewind_items();
	}

	/**
	 * Kick-off the query. 
	 * @param  array $args An array of query args as per the __construct() function.
	 */
	public function query( $args = null ) {
		if ( null !== $args ) {
			// Merge new args with any currently set
			$this->query_vars = wp_parse_args( $args, $this->query_vars );
		}

		$defaults = $this->_get_default_args();
		$this->query_vars = wp_parse_args( $this->query_vars, $defaults );
		
		$this->query_vars = apply_filters( 'wp_menu_query_vars', $this->query_vars );

		$this->_fetch();
	}

	/**
	 * Fetches the menu items based on the args defined.
	 */
	private function _fetch() {
		if ( !$this->_is_location_valid() ) {
			return;
		}

		// Reset class properties
		$this->rewind_items();
		$this->items = array();

		// Get the items
		$this->items = $this->_get_menu_items();

		// Filter the items array to only include child items 
		// of the parent if parent option was passed.
		if ( $this->get( 'parent' ) ) {
			$this->_parent = $this->_map_parent_to_id( $this->get('parent') );
		} else {
			$this->_parent = 0;
		}

		$this->items = array_filter( $this->items, array( $this, '_filter_to_parent' ) );

		// Map the items to corresponding WP_Menu_Items
		foreach ($this->items as $key => &$item) {
			if ( !$item ) {
				continue;
			}

			$item = $this->_create_menu_item_object( $item );
			unset( $item );
		}

		// Remove any invalid items & re index the array
		$this->items = array_values( array_filter( $this->items ) );

		$this->_apply_limits();

		// Update class properties with number found
		$this->_update_counts();
	}

	private function _is_location_valid() {
		$location = $this->get( 'location' );

		// Test if the location query_var is set and not empty
		if ( 
			!isset( $location ) || 
			!$location || 
			'' == $location 
		) {
			throw new WP_Menu_Query_Exception("Location is a required key and must be set to fetch a query.");
			return false;
		}


		// Test if the location value passed is known to WordPress by register_nav_menus()
		$registered_locations = get_registered_nav_menus();

		if ( !isset($registered_locations[$location]) ) {
			throw new WP_Menu_Query_Exception("The location '$location' is not registered within WordPress.");
			return false;
		} 


		// Test if the registered location has an attached menu in the Appearance > Menus page
		$menu_locations = get_nav_menu_locations();

		if ( !isset($menu_locations[$location]) ) {
			throw new WP_Menu_Query_Exception("The location '$location' does not have an attached menu in the WordPress admin (Appearance > Menus).");
			return false;
		} 


		return true;
	}

	/**
	 * Get a WP_Menu object representing the menu currently queried.
	 * @return WP_Menu 
	 */
	public function get_menu() {
		$menu_location = $this->get( 'location' );

		$query_cache = WP_Menu_Query_Cache::get_instance();
		return $query_cache->get_location_term( $menu_location );
	}

	/**
	 * Get the menu items from the database and store them in the global cache.
	 * This speeds up subsequent calls for the same menu, e.g. get_children(), 
	 * as the items don't need to be reprocessed.
	 * @return array
	 */
	private function _get_menu_items() {
		global $wp_menu_query_cache;

		$location = $this->get( 'location' );
		$location_cache_key = 'ITEMS_' . $location;

		// Check cache for items before loading
		if ( isset($wp_menu_query_cache[$location_cache_key]) ) {
			return $wp_menu_query_cache[$location_cache_key];
		}

		// Check cache again for menu term
		$menu_term_cache_key = 'MENU_TERM_' . $location;

		if ( isset($wp_menu_query_cache[$menu_term_cache_key]) ) {
			$menu_term = $wp_menu_query_cache[$menu_term_cache_key];
		} else {
			$menu_locations = get_nav_menu_locations();
			$menu_term = wp_get_nav_menu_object( $menu_locations[$location] );

			// Store into the cache
			$wp_menu_query_cache[$menu_term_cache_key] = $menu_term;
		}

		$items = wp_get_nav_menu_items( $menu_term->term_id );
		$wp_menu_query_cache[$location_cache_key] = $items;

		return $items;
	}

	/**
	 * Convert a parent URL into a menu item ID.
	 * @param string $parent A URL string.
	 * @return integer
	 */
	private function _map_parent_to_id( $parent ) {
		if ( is_integer($parent) ) {
			return $parent;
		}

		if ( '' === $parent ) {
			return 0;
		}

		$parent = $this->_absolute_url($parent);
		$match = 0;

		foreach ($this->items as $key => $item) {
			if ( $item->url === $parent ) {
				$match = $item->ID;
				break;
			}
		}

		return $match;
	}

	/**
	 * Filter for function for use by array_filter().
	 * @param  WP_Menu_Item $item A WordPress menu item.
	 * @return boolean
	 */
	private function _filter_to_parent( $item ) {
		return $this->_parent === (integer)$item->menu_item_parent;
	}

	/**
	 * Create a WP_Menu_Item object from a WordPress menu item.
	 * @param  StdClass $item A WordPress menu item.
	 * @return WP_Menu_Item|false
	 */
	private function _create_menu_item_object( $item ) {
		/**
		 * Check include conditions if passed.
		 * Returns false if the item matches one in the include array.
		 */
		if ( count( $this->get( 'include' ) ) ) {
			
			$_include = array();

			foreach ($this->get( 'include' ) as $key => $url) {
				
				$url = $this->_absolute_url( $url );

				if ( $item->url === $url ) {
					$_include[] = 1;
				} else {
					$_include[] = 0;
				}

			}

			// If array is all 0's
			if ( !array_filter( $_include ) ) {
				return false;					
			}

		}


		/**
		 * Check exclude conditions if passed.
		 * Returns false if item matches one in the exclude array.
		 */
		if ( count( $this->get( 'exclude' ) ) ) {
			
			$_exclude = array();

			foreach ($this->get( 'exclude' ) as $key => $url) {
				
				$url = $this->_absolute_url( $url );

				if ( $item->url === $url ) {
					$_exclude[] = 1;
				} else {
					$_exclude[] = 0;
				}

			}

			// If array contains at least one 1
			if ( array_filter( $_exclude ) ) {
				return false;					
			}

		}


		// Convert the item to a WP_Menu_Item
		$item = new WP_Menu_Item( $item, $this->query_vars );

		// Only keep published posts
		if ( $item->type === 'post_type' ) {
			$_post = get_post( $item->object_id );

			if ( $_post->post_status != 'publish' ) {
				return false;
			}
		}

		return $item;
	}

	/**
	 * Convert a URL into an absolute URL.
	 * Relative URLs will be made absolute to the home URL.
	 * @param  string $url A URL string.
	 * @return string
	 */
	private function _absolute_url( $url ) {
		// Relative links do not start with http:// or https://
		if ( !( 0 === strpos( $url, 'https://' ) || 0 === strpos( $url, 'http://' ) ) ) {
			$url = trailingslashit( home_url( $url ) );
		}

		return $url;
	}

	/**
	 * Constrain the number of items returned to 
	 * the limit args passed.
	 */
	private function _apply_limits() {
		// Apply limit and offset if passed
		$limit = count( $this->items );

		if ( 0 == $this->get( 'parent' ) ) {
			
			// Use 'limit' arg for top level items
			if ( $this->get( 'limit' ) > -1 && is_numeric( $this->get( 'limit' ) ) ) {
				$limit = (integer)$this->get( 'limit' );
			}

		} else {

			// Use 'limit_children' arg for child items
			if ( $this->get( 'limit_children' ) > -1 && is_numeric( $this->get( 'limit_children' ) ) ) {
				$limit = (integer)$this->get( 'limit_children' );
			}

		}

		$this->items = array_slice( $this->items, $this->get( 'offset' ), $limit );
	}

	/**
	 * Update class count properties.
	 */
	private function _update_counts() {
		$this->item_count = count( $this->items );
		$this->found_items = $this->item_count;
	}

}
} // /!class_exists('WP_Menu_Query')
