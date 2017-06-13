## Wordpress bcm manager plugin

When installed it will create a __BCM__ settings page where custom values can be set.

In order to generate the following meta tags this plugin must be enabled from the settings page.

#### Meta tags present

- `bcm-type` - __required__ - can be set in settings page
- `bcm-country` - __required__ - this will be fetched automatically
- `bcm-brand` - __required__ - can be set in settings page
- `bcm-sub` - __optional__ - can be set in settings page
- `bcm-tablet-breakpoint` - __optional__ - can be set in settings page
- `bcm-mobile-breakpoint` - __optional__ - can be set in settings page
- `bcm-title` - automatically fetched on article page and overwritten by filter `wp_bcm_set_title`
- `bcm-content-type` - this will be fetched automatically and corresponds to get_fields(get_post()->ID)['kind']
- `bcm-categories` - automatically fetched on article page and overwritten by filter `wp_bcm_set_categories`
- `bcm-tags` - automatically fetched on article page and overwritten by filter `wp_bcm_set_tags`
- `bcm-advertorial-type` - this will be fetched automatically and corresponds to get_fields(get_post()->ID)['commercial_type']
- `bcm-advertorial-label` - this will be fetched automatically and corresponds to pll__(get_fields(get_post()->ID)['commercial_type'])


#### Inclusion script

The javascript code will be inserted at `wp_footer` action and it looks like: `<script type="text/javascript" src="https://bcm.interactives.dk/script/#country#/#brand#/#type#"></script>`, where all the placeholders will be replaced by correct ones from above listing.

#### Country code

This will be fetched from polylang plugin by looking at the current language code and fetching the proper country code.
If that is not possible then it will be fetched from current wordpress locale.

#### Worpress filters available

- __wp_bcm_set_title__
``` php
add_filter('wp_bcm_set_title', function($strTitle) {
	// tamper title
	return $strTitle;
});
```

- __wp_bcm_set_categories__
``` php
add_filter('wp_bcm_set_categories', function($arrCategories) {
	// tamper with categories
	return $arrCategories;
});
```

- __wp_bcm_set_tags__
``` php
add_filter('wp_bcm_set_tags', function($arrTags) {
	// tamper with tags
	return $arrTags;
});
```

- __wp_bcm_set_content_type__
``` php
add_filter('wp_bcm_set_content_type', function($strContentType) {
	// tamper content type
	return $strContentType;
});
```
