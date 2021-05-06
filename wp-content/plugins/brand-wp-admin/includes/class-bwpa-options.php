<?php

/**
* Plugin Options Model
*/
class Brand_WP_Admin_Options {
	
	const SETTINGS_OPTIONS_NAME = 'brandwpadmin_plugin_options';

	/**
	 * The post ID of the logo image.
	 * @var integer
	 */
	public $logo_id;

	/**
	 * The size of the logo in pixels.
	 * @var integer
	 */
	public $logo_width;

	/**
	 * The post ID of the favicon image.
	 * @var integer
	 */
	public $favicon_id;

	function __construct() {}

	/**
	 * Get the default options.
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'logo_id' => 0,
			'logo_width' => 320,
			'favicon_id' => 0,
		);
	}

	/**
	 * Load the template options from the database.
	 * @return array The current options in the database.
	 */
	public function load() {
		$options = get_option( self::SETTINGS_OPTIONS_NAME );

		if ( $options && is_array($options) ) {
			foreach ($options as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			$options = array();
		}

		return $options;
	}

	/**
	 * Load the default options.
	 * @return array The default options.
	 */
	public function load_defaults() {
		$options = $this->get_defaults();

		if ( $options ) {
			foreach ($options as $key => $value) {
				$this->{$key} = $value;
			}
		}

		return $options;
	}

	/**
	 * Save the options to the database.
	 */
	public function save() {
		$options = array();
		foreach ($this as $key => $value) {
			$options[ $key ] = $value;
		}

		update_option( self::SETTINGS_OPTIONS_NAME, $options );
	}

	/**
	 * Remove the options from the database.
	 */
	public function delete() {
		delete_option( self::SETTINGS_OPTIONS_NAME );
	}
	
}
