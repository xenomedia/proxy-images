<?php
/**
 * Plugin Name:     Proxy Images
 * Plugin URI:      github
 * Description:     Keep path for images
 * Author:          caromanel
 * Author URI:      caromanel
 * Text Domain:     proxy-images
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Proxy_Images
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Loads admin settings page.
require_once plugin_dir_path( __FILE__ ) . 'includes/admin.php';

// Loads class output.
require_once plugin_dir_path( __FILE__ ) . 'includes/output.php';

/**
 * Gets plugin settings.
 *
 * @param $arg - string  name of the setting.
 * @return $settings with or settings, $string with specific setting.
 *
 */
function pxy_get_settings( $arg = null ) {
	$options = get_option( 'pxy_settings' );
	if ( ! empty( $arg ) ) {
		$option = empty( $options[ $arg ] ) ? false : $options[ $arg ];
	}
	return $option;
}

/**
 * Displays banner with information about current site and images.
 */
function pxy_images_banner() {
	?>
	<style>
	.pxy-banner{background:#4a9e03;bottom:0;color:#000;left:0;float:left;margin:0;opacity:.8;padding:3px;position:fixed;width:100%;vertical-align:middle;z-index:999;font-weight:600;text-align:center;z-index: 9991;}
	</style>
	<?php
	$options = pxy_get_settings( $arg = 'pxy_banner' );

	if ( 'show' !== $options ) {
		return false;
	}

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) { // Check ip from share internet.
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // Check ip is pass from proxy.
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	$original_url = pxy_get_settings( $arg = 'pxy_original_url' );
	$from_site = ( $current_site === $original_url ) ? ' current site' : esc_url( $original_url );

	echo '<div class="pxy-banner">';
	echo sprintf( __( 'Current site: %1$s [IP: %2$s] &#x21C4; Images pointing to %3$s ', 'proxy-images' ), get_site_url(), esc_attr( $ip ), $from_site );
	echo '</div>';

}
add_action( 'wp_footer', 'pxy_images_banner' );
add_action( 'admin_footer', 'pxy_images_banner' );
