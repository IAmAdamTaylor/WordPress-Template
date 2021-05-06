<?php defined('ABSPATH') or die();

/**
 * @package KAversioncheck
 */
/*
Plugin Name: Kaweb Version Check
Plugin URI: http://www.kaweb.co.uk
Description: Helps ensure your WordPress installation is up to date.
Version: 1.0.0
Author: Kaweb
Author URI: http://www.kaweb.co.uk
*/

class KaVersionCheck {
	private static $instance;

	private $user = 'THyMNsbHUk5lLXpQYc5m';
	private $pass = '18rgaogYdZl4Cj3b67Nm';

	public function __construct() {
		register_rest_route('kaversioncheck/v1', '/plugins/', array(
			'methods' => 'POST',
			'callback' => array($this, 'get_plugins')
		));

		register_rest_route('kaversioncheck/v1', '/wordpress/', array(
			'methods' => 'POST',
			'callback' => array($this, 'get_wordpress')
		));

		register_rest_route('kaversioncheck/v1', '/themes/', array(
			'methods' => 'POST',
			'callback' => array($this, 'get_themes')
		));

		register_rest_route('kaversioncheck/v1', '/all/', array(
			'methods' => 'POST',
			'callback' => array($this, 'get_all')
		));
	}

	public function get_all() {
		$this->auth();

		$wp_version = $this->get_wordpress();
		$plugins = $this->get_plugins();
		$themes = $this->get_themes();

		return array(
			'site_version' => $wp_version['wordpress'],
			'site_version_simple' => preg_replace('/[^0-9]/', '', $wp_version['wordpress']),
			'plugins' => $this->map_plugin_data($plugins['plugins']),
			'themes' => $themes['themes']
		);
	}

	public function get_plugins() {
		$this->auth();

		if (!function_exists('get_plugins') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return array('plugins' => get_plugins());
	}

	function map_plugin_data($plugins) {

		$formatted_plugins = array();

		foreach($plugins as $plugin_key => $plugin) {

			$formatted_plugin = array(
				'plugin_name' => !empty($plugin['Name']) ? $plugin['Name'] : $plugin['Title'],
				'plugin_version' => $plugin['Version'],
				'plugin_identifier' => $plugin_key,
				'plugin_slug' => !empty($plugin['TextDomain']) ? $plugin['TextDomain'] : rtrim(str_replace(basename($plugin_key), '', $plugin_key ), '/')
			);
			$formatted_plugins[] = $formatted_plugin;
		}

		return $formatted_plugins;

	}

	public function get_wordpress() {
		$this->auth();

		global $wp_version;

		return array('wordpress' => $wp_version);
	}

	public function get_themes() {
		$this->auth();

		$themes = wp_get_themes();

		return array(
			'themes' => $this->map_theme_data($themes)
		);
	}

	function map_theme_data($themes) {

		$formatted_themes = array();

		foreach ($themes as $theme_key => $theme) {
			$formatted_theme = array(
				'theme_name' => $theme->get('Name'),
				'theme_version' => $theme->get('Version'),
				'theme_identifier' => $theme->get_stylesheet(),
				'theme_slug' => $theme->get_stylesheet()
			);

			$formatted_themes[] = $formatted_theme;

		}

		return $formatted_themes;

	}

	private function auth() {
		if (!isset($_POST['user']) || !isset($_POST['pass']) || $_POST['user'] != $this->user || $_POST['pass'] != $this->pass) {
			die();
		}

		return true;
	}

	public static function init() {
		static::$instance = new KaVersionCheck();
	}
}

add_action('rest_api_init', array('KaVersionCheck', 'init'));
