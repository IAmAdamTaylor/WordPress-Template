<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Brand_WP_Admin
 * @subpackage Brand_WP_Admin/includes
 */
class Brand_WP_Admin_Activator {

	/**
	 * Add the default options to the database.
	 * 
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bwpa-options.php';

		$options = new Brand_WP_Admin_Options();
		$values = $options->load();

		// If values are not saved in the database already, add the defaults
		if ( !$values ) {
			$options->load_defaults();
			$options->save();
		}
		
	}

}
