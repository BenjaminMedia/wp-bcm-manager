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

			// add tablet breaking point
			if (self::$objSettings->tablet_breakpoint) {
				$arrTags['bcm-tablet-breakpoint'] = self::$objSettings->tablet_breakpoint;
			}

			// add mobile breaking point
			if (self::$objSettings->mobile_breakpoint) {
				$arrTags['bcm-mobile-breakpoint'] = self::$objSettings->mobile_breakpoint;
			}

			// set content type
			if ($strContentType = self::get_bcm_content_type()) {
				$arrArticleTags['bcm-content-type'] = $strContentType;
			}

			// set advertorial type
			if ($strAdvertorialType = self::get_bcm_advertorial_type()) {
				$arrArticleTags['bcm-advertorial-type'] = $strAdvertorialType;
			}

			// set advertorial label
			if ($strAdvertorialLabel = self::get_bcm_advertorial_label()) {
				$arrArticleTags['bcm-advertorial-label'] = $strAdvertorialLabel;
			}

			// we only apply article tags when necessary
			if (!is_front_page() && (is_singular() || is_single())) {
				$arrTags = array_merge($arrTags, self::get_article_tags());
			}

			// add subcategory tag by overwriting if present
			if (self::$objSettings->sub) {
				$arrTags['bcm-sub'] = self::$objSettings->sub;
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

		// set main category
		if ($strMainCategory = self::get_article_category()) {
			$arrArticleTags['bcm-sub'] = $strMainCategory;
		}
		
		// set all categories
		if ($arrCategories = self::get_bcm_categories()) {
			$arrArticleTags['bcm-categories'] = implode(',', $arrCategories);
		}
		
		// set all tags
		if ($arrTags = self::get_bcm_tags()) {
			$arrArticleTags['bcm-tags'] = implode(',', $arrTags);
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
		return apply_filters('wp_bcm_set_content_type', null);
	}
	
	/**
	 * Get advertorial content type
	 *
	 * @return string
	 */
	private static function get_bcm_advertorial_type() {
		return apply_filters('wp_bcm_set_advertorial_type', null);
	}
	
	/**
	 * Get advertorial content label
	 *
	 * @return string
	 */
	private static function get_bcm_advertorial_label() {
		return apply_filters('wp_bcm_set_advertorial_label', null);
	}
	
	/**
	 * Get array of categories name from post
	 *
	 * @return array
	 */
	private static function get_bcm_categories() {
		return apply_filters('wp_bcm_set_categories', array_map(function($objCategory) {
					return $objCategory->name;
				},
				wp_get_post_terms(get_post()->ID, 'category')
			)
		);
	}
	
	/**
	 * Get array of tags from post
	 *
	 * @return array
	 */
	private static function get_bcm_tags() {
		return apply_filters('wp_bcm_set_tags', array_map(function($objTag) {
					return $objTag->name;
				},
				wp_get_post_terms(get_post()->ID, 'post_tag')
			)
		);
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

	/**
	 * Get main category of a post
	 *
	 * @return string|null
	 */
	private static function get_article_category() {
		$arrCategories = get_the_category();

		if (isset($arrCategories[0])) {
			if ($arrCategories[0]->parent) {
				return self::get_top_category($arrCategories[0]->cat_ID);
			}

			return $arrCategories[0]->name;
		}
		return null;
	}

	/**
	 * Get top category name based on category id
	 *
	 * @param integer $intCategoryId
	 * @return string
	 */
	private function get_top_category($intCategoryId) {
		$strTopName = null;

		while ($intCategoryId) {
			$objCategory = get_category($intCategoryId);
			$intCategoryId = $objCategory->category_parent;
			$strTopName = $objCategory->name;
		}

		return $strTopName;
	}
}