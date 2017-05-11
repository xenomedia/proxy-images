<?php
/**
 * Class UrlTest
 *
 * @package Proxy_Images
 */

/**
 * Testing options for live site.
 */
// class UrlTest extends WP_UnitTestCase {
class WP_PxyUnitTestCase extends WP_UnitTestCase {

	/**
	 * Checks if plugin is active.
	 */
	function test_is_pxy_active() {
		$this->assertTrue( is_plugin_active( 'proxy-images/proxy-images.php' ) );
	}
}
