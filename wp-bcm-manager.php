<?php
/**
 * Plugin Name: WP BCM Manager
 * Version: 1.1.2
 * Plugin URI: https://github.com/BenjaminMedia/wp-bcm-manager
 * Description: This plugin integrates Bonnier Commercial Manager script into your website
 * Author: Bonnier
 * License: GPL v3
 */

namespace Bonnier\BCM;

use Bonnier\BCM\Settings\SettingsPage;
use Bonnier\BCM\Services\MetaTag;
use Bonnier\BCM\Services\Script;

// Do not access this file directly
if (!defined('ABSPATH')) {
	exit;
}

// Handle autoload so we can use namespaces
spl_autoload_register(function ($strClass) {
	if (strpos($strClass, __NAMESPACE__) !== false) {
		$strClass = str_replace("\\", DIRECTORY_SEPARATOR, $strClass);
		require_once(__DIR__ . DIRECTORY_SEPARATOR . Plugin::CLASS_DIR . DIRECTORY_SEPARATOR . $strClass . '.php');
	}
});

// Load plugin globally
require_once (__DIR__ . '/' . Plugin::CLASS_DIR . '/global.php');

/**
 * Plugin class
 */
class Plugin {

	/**
	 * Text domain for translators
	 *
	 * @var string $strTextDomain
	 */
	public static $strTextDomain = 'wp-bcm-manager';

	const CLASS_DIR = 'src';

	/**
	 * Plugin instance
	 *
	 * @var object Instance of this class.
	 */
	private static $objInstance;

	/**
	 * SettingsPage instance
	 *
	 * @var SettingsPage $objSettings
	 */
	public $objSettings;

	/**
	 * Basename of this class
	 *
	 * @var string $basename
	 */
	public $basename;

	/**
	 * Plugins directory for this plugin
	 *
	 * @var string $plugin_dir
	 */
	public $plugin_dir;

	/**
	 * Plugins url for this plugin
	 *
	 * @var string $plugin_url
	 */
	public $plugin_url;
	
	/**
	 * Returns the instance of this class
	 *
	 * @return Plugin
	 */
	public static function getInstance() {
		if (!self::$objInstance) {
			self::$objInstance = new self;
			global $wp_cxense;
			$wp_cxense = self::$objInstance;
			self::$objInstance->bootstrap();

			// Run after the plugin has been loaded.
			do_action('wp_bcm_manager_loaded');
		}

		return self::$objInstance;
	}
	
	/**
	 * Constructor
	 *
	 * @return Plugin
	 */
	private function __construct() {

		// Set plugin file variables
		$this->basename = plugin_basename(__FILE__);
		$this->plugin_dir = plugin_dir_path(__FILE__);
		$this->plugin_url = plugin_dir_url(__FILE__);

		// Load textdomain
		load_plugin_textdomain(self::$strTextDomain, false, dirname($this->basename) . '/languages');

		$this->objSettings = new SettingsPage();
	}

	private function bootstrap() {
		Script::bootstrap($this->objSettings);
		MetaTag::bootstrap($this->objSettings);
	}
}

/**
 * @return Plugin
 */
function instance() {
	return Plugin::getInstance();
}

add_action('plugins_loaded', __NAMESPACE__ . '\instance', 0);