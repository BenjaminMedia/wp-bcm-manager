<?php
/**
 * Locale class file
 */

namespace Bonnier\BCM\Services;

/**
 * Locale class
 */
class Locale {
	
	/**
	 * Instance object
	 *
	 * @var Locale $objInstance
	 */
	private static $objInstance;
	
	/**
	 * Current country string
	 *
	 * @var string $strCurrentCountry
	 */
	 private $strCurrentCountry;
	
	/**
	 * Return instance of class
	 *
	 * @return Locale
	 */
	public static function get_instance() {
		if (!self::$objInstance) {
			self::$objInstance = new self();
		}
		return self::$objInstance;
	}
	
	/**
	 * Get country code
	 *
	 * @return string
	 */
	public function get_country() {
		
		if ($this->strCurrentCountry) {
			return $this->strCurrentCountry;
		}
		
		// fallback to Polylang default language
		if ($strLocale = $this->get_current_language()) {
			$this->strCurrentCountry = $this->locale_to_country_code($strLocale);
		}
		
		// fallback to locale from wordpress
		if (!$this->strCurrentCountry) {
			$this->strCurrentCountry = $this->locale_to_country_code(get_locale());
		}
		
		return $this->strCurrentCountry;
	}
	
	/**
	 * Returns the country code from locale: 'da_DK' becomes 'dk'
	 *
	 * @param string $strLocale
	 * @return string
	 */
	private function locale_to_country_code($strLocale) {
		return strtolower(substr($strLocale, -2, 2));
	}
	
	/**
	 * Get the current language by looking at the polylang functions results
	 *
	 * @return string|null
	 */
	private function get_current_language() {
		if (function_exists('pll_current_language')) {
			$arrLanguages = pll_the_languages([
				'raw'=>1
			]);
			return $arrLanguages[pll_current_language()]['locale'];
		}
		return null;
	}
}