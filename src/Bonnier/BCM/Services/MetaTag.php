<?php
/**
 * MetaTag class file
 */
namespace Bonnier\BCM\Services;

use Bonnier\BCM\Settings\SettingsPage;

/**
 * MetaTag class
 */
class MetaTag {
	
	/**
	 * Settings object
	 *
	 * @var SettingsPage $objSettings
	 */
	private static $objSettings;

	/**
	 * Bootstrap the service by adding to the wordpress action wp_head
	 *
	 * @param SettingsPage $objSettings
	 * @return null
	 */
	public static function bootstrap(SettingsPage $objSettings) {
		self::$objSettings = $objSettings;
		add_action('wp_head', [__CLASS__, 'add_head_tags']);
	}

	/**
	 * Add meta tags
	 *
	 * @return null
	 */
	public static function add_head_tags() {
		
		if (self::$objSettings->enabled) {
			
			// add mandatory tags
			$arrTags = [
				'bcm-brand' => self::$objSettings->brand,
				'bcm-country' => self::$objSettings->country,
				'bcm-type' => self::$objSettings->type
			];
			
			// add subcategory tag
			if (self::$objSettings->sub) {
				$arrTags['bcm-sub'] = self::$objSettings->sub;
			}
			
			// add tablet breaking point
			if (self::$objSettings->tablet_breakpoint) {
				$arrTags['bcm-tablet-breakpoint'] = self::$objSettings->tablet_breakpoint;
			}
			
			// add mobile breaking point
			if (self::$objSettings->mobile_breakpoint) {
				$arrTags['bcm-mobile-breakpoint'] = self::$objSettings->mobile_breakpoint;
			}
			
			// we only apply article tags when necessary
			if (!is_front_page() && (is_singular() || is_single())) {
				$arrTags = array_merge($arrTags, self::get_article_tags());
			}
			
			// write the actual tags
			self::write_meta_tags($arrTags);
		}
	}
	
	/**
	 * Get specific article meta tags
	 *
	 * @return array
	 */
	private static function get_article_tags() {
		$arrArticleTags = [
			'bcm-title' => self::get_bcm_title()
		];

		// set content type
		if ($strContentType = self::get_bcm_content_type()) {
			$arrArticleTags['bcm-content-type'] = $strContentType;
		}
		
		// set all categories
		if ($arrCategories = self::get_bcm_categories()) {
			$arrArticleTags['bcm-categories'] = implode(',', $arrCategories);
		}
		
		// set all tags
		if ($arrTags = self::get_bcm_tags()) {
			$arrArticleTags['bcm-tags'] = implode(',', $arrTags);
		}
		
		// set advertorial type
		if ($strAdvertorialType = self::get_bcm_advertorial_type()) {
			$arrArticleTags['bcm-advertorial-type'] = implode(',', $strAdvertorialType);
		}
		
		// set advertorial label
		if ($strAdvertorialLabel = self::get_bcm_advertorial_label()) {
			$arrArticleTags['bcm-advertorial-label'] = implode(',', $strAdvertorialLabel);
		}
		
		return $arrArticleTags;
	}
	
	/**
	 * Get title from post
	 *
	 * @return string
	 */
	private static function get_bcm_title() {
		global $post;
		return apply_filters('wp_bcm_set_title', $post->post_title);
	}
	
	/**
	 * Get content type
	 *
	 * @return string
	 */
	private static function get_bcm_content_type() {
		$strContentType = null;
		return apply_filters('wp_bcm_set_content_type', $strContentType);
	}
	
	/**
	 * Get advertorial content type
	 *
	 * @return string
	 */
	private static function get_bcm_advertorial_type() {
		$strAdvertorialContentType = null;
		return apply_filters('wp_bcm_set_advertorial_type', $strAdvertorialContentType);
	}
	
	/**
	 * Get advertorial content label
	 *
	 * @return string
	 */
	private static function get_bcm_advertorial_label() {
		$strAdvertorialContentLabel = null;
		return apply_filters('wp_bcm_set_advertorial_label', $strAdvertorialContentLabel);
	}
	
	/**
	 * Get array of categories name from post
	 *
	 * @return array
	 */
	private static function get_bcm_categories() {
		global $post;
		
		$arrCategories = [];
		$arrPostCategories = wp_get_post_terms($post->ID, 'category');

		foreach ($arrPostCategories as $arrCategory) {
			$arrCategories[] = $arrCategory->name;
		}
		
		return apply_filters('wp_bcm_set_categories', $arrCategories);
	}
	
	/**
	 * Get array of tags from post
	 *
	 * @return array
	 */
	private static function get_bcm_tags() {
		global $post;
		
		$arrTags = [];
		$arrPostTags = wp_get_post_terms($post->ID, 'post_tag');
		
		foreach ($arrPostTags as $arrTag) {
			$arrTags[] = $arrTag->name;
		}
		
		return apply_filters('wp_bcm_set_tags', $arrTags);
	}
	
	/**
	 * Generate a meta tag
	 *
	 * @param array $arrTags
	 * @return null
	 */
	private static function write_meta_tags(array $arrTags) {
		foreach ($arrTags as $strKey => $strValue) {
			echo '<meta name="' . $strKey . '" content="' . trim($strValue) . '" />' . PHP_EOL;
		}
	}
}