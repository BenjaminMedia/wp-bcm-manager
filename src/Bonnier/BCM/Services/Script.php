<?php
/**
 * Script class file
 */
namespace Bonnier\BCM\Services;

use Bonnier\BCM\Settings\SettingsPage;

/**
 * Script class
 */
class Script {
	
	/**
	 * Settings object
	 *
	 * @var SettingsPage $objSettings
	 */
	private static $objSettings;

	/**
	 * Bootstrap the service by adding to the wordpress action wp_footer
	 *
	 * @param SettingsPage $objSettings
	 * @return null
	 */
	public static function bootstrap(SettingsPage $objSettings) {
		self::$objSettings = $objSettings;
		add_action('wp_footer', [__CLASS__, 'add_script']);
	}

	/**
	 * Add BCM javascript file to the page
	 *
	 * @return null
	 */
	public static function add_script() {
		if (self::$objSettings->enabled) {
			$strScriptLine = str_replace([
					'#country#',
					'#brand#',
					'#type#'
				],[
					self::$objSettings->country,
					self::$objSettings->brand,
					self::$objSettings->type
				],
				'<script type="text/javascript" src="https://bcm.interactives.dk/script/#country#/#brand#/#type#"></script>'
			);
			echo $strScriptLine;
		}
	}
}