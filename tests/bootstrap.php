<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Proxy_Images
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {

	// ** PROXY IMAGES ACTIVATE PLUGIN ** //
	$plugins_to_active = array(
		'proxy-images/proxy-images.php'
	);

	update_option( 'active_plugins', $plugins_to_active );

	// ** END PROXY IMAGES ACTIVATE PLUGIN  ** //

	require dirname( dirname( __FILE__ ) ) . '/proxy-images.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';


