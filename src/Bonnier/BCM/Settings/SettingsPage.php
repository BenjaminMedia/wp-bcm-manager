<?php
/**
 * SettingsPage class file
 */

namespace Bonnier\BCM\Settings;

use Bonnier\BCM\Services\Locale;

/**
 * SettingsPage class
 */
class SettingsPage {
	
	
	const SETTINGS_KEY = 'wp_bcm_settings';
	const SETTINGS_GROUP = 'wp_bcm_settings_group';
	const SETTINGS_SECTION = 'wp_bcm_settings_section';
	const SETTINGS_PAGE = 'wp_bcm_settings_page';
	const Settings_PAGE_NAME = 'BCM';
	const Settings_PAGE_TITLE = 'WP BCM settings:';
	const NOTICE_PREFIX = 'WP BCM:';

	/**
	 * Settings fields
	 *
	 * @var array $arrFields
	 */
	private $arrFields = [
		'enabled' => [
			'type' => 'checkbox',
			'name' => 'Enable banners (BCM tags are always shown)',
		],
		'type' => [
			'type' => 'text',
			'name' => 'Type ("site", "app", "blog", "shop")',
		],
		'brand' => [
			'type' => 'text',
			'name' => 'Brand',
		],
		'sub' => [
			'type' => 'text',
			'name' => 'Sub (this overwrites default value (top category))',
		],
		'tablet_breakpoint' => [
			'type' => 'text',
			'name' => 'Tablet breakpoint (768)'
		],
		'mobile_breakpoint' => [
			'type' => 'text',
			'name' => 'Mobile breakpoint (480)'
		]
	];

	/**
	 * Backend settings values
	 *
	 * @var array $arrSettingsValues
	 */
	private $arrSettingsValues;

	/**
	 * Constructor
	 *
	 * @return SettingsPage
	 */
	public function __construct() {
		
		// get saved settings
		$this->arrSettingsValues = get_option(self::SETTINGS_KEY);
		
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_init', array($this, 'register_settings'));
	}

	/**
	 * Add options page
	 *
	 * @return null
	 */
	public function add_plugin_page() {

		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			self::Settings_PAGE_NAME,
			'manage_options',
			self::SETTINGS_PAGE,
			array($this, 'create_admin_page')
		);
	}

	/**
	 * Options page callback
	 *
	 * @return null
	 */
	public function create_admin_page() {

		// Set class property
		?>
			<div class="wrap">
				<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields(self::SETTINGS_GROUP);
						do_settings_sections(self::SETTINGS_PAGE);
						submit_button();
					?>
				</form>
			</div>
		<?php
	}

	/**
	 * Register and add settings
	 *
	 * @return null
	 */
	public function register_settings() {

		register_setting(
			self::SETTINGS_GROUP, // Option group
			self::SETTINGS_KEY, // Option name
			array($this, 'sanitize') // Sanitize
		);

		add_settings_section(
			self::SETTINGS_SECTION, // ID
			self::Settings_PAGE_TITLE, // Title
			array($this, 'print_section_info'), // Callback
			self::SETTINGS_PAGE // Page
		);

		foreach ($this->arrFields as $settingsKey => $settingField) {
			add_settings_field(
				$settingsKey, // ID
				$settingField['name'], // Title
				array($this, $settingsKey), // Callback
				self::SETTINGS_PAGE, // Page
				self::SETTINGS_SECTION // Section
			);
		}
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 * @return array
	 */
	public function sanitize($input) {
		$sanitizedInput = [];

		foreach ($this->arrFields as $fieldKey => $settingsField) {
			if (isset($input[$fieldKey])) {
				if ($settingsField['type'] === 'checkbox') {
					$sanitizedInput[$fieldKey] = absint($input[$fieldKey]);
				}
				if ($settingsField['type'] === 'text' || $settingsField['type'] === 'select') {
					$sanitizedInput[$fieldKey] = sanitize_text_field($input[$fieldKey]);
				}
			}
		}

		return $sanitizedInput;
	}

	/**
	 * Print the Section text
	 *
	 * @return null
	 */
	public function print_section_info() {
		print 'Enter your settings below:';
	}

	/**
	 * Catch callbacks for creating setting fields
	 *
	 * @param string $function
	 * @param array $arguments
	 * @return bool
	 */
	public function __call($function, $arguments) {

		if (!isset($this->arrFields[$function])) {
			return false;
		}

		$field = $this->arrFields[$function];
		$this->create_settings_field($field, $function);
	}

	/**
	 * Get setting value
	 *
	 * @param string $strKey
	 * @return string|null
	 */
	private function get_setting_value($strKey) {

		if(!$this->arrSettingsValues) {
			$this->arrSettingsValues = get_option(self::SETTINGS_KEY);
		}

		if (isset($this->arrSettingsValues[$strKey]) && !empty($this->arrSettingsValues[$strKey])) {
			return apply_filters('wp_bcm_option_'.$strKey, $this->arrSettingsValues[$strKey]);
		}

		return null;
	}
	
	
	/**
	 * Magic method for getting specific setting
	 *
	 * @param string $strKey
	 * @return string
	 */
	public function __get($strKey) {
		
		if ($strKey == 'country') {
			return Locale::get_instance()->get_country();
		}
		
		return $this->get_setting_value($strKey);
	}

	/**
	 * Create html code for given field
	 *
	 * @return null
	 */
	private function create_settings_field($field, $fieldKey) {

		$fieldName = self::SETTINGS_KEY . "[$fieldKey]";
		$fieldOutput = false;

		if ($field['type'] === 'text') {
			$fieldValue = isset($this->arrSettingsValues[$fieldKey]) ? esc_attr($this->arrSettingsValues[$fieldKey]) : '';
			$fieldOutput = "<input type='text' name='$fieldName' value='$fieldValue' class='regular-text' />";
		}
		if ($field['type'] === 'checkbox') {
			$checked = isset($this->arrSettingsValues[$fieldKey]) && $this->arrSettingsValues[$fieldKey] ? 'checked' : '';
			$fieldOutput = "<input type='hidden' value='0' name='$fieldName'>";
			$fieldOutput .= "<input type='checkbox' value='1' name='$fieldName' $checked />";
		}

		if ($fieldOutput) {
			print $fieldOutput;
		}
	}
}