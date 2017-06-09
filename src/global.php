<?php
/**
 * Returns an instance of the wp-bcm-manager plugin
 *
 * @return \Bonnier\BCM\Plugin|null
 */
function wp_bcm_manager() {
	return isset($GLOBALS['wp-bcm-manager']) ? $GLOBALS['wp-bcm-manager'] : null;
}