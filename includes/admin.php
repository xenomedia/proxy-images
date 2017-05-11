<?php
/**
 * Admin settings for plugin proxy_images
 *
 * @package  proxy_Images
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Calls the class only in admin.
 */
if ( is_admin() ) {
	new Proxy_Images_Admin;
}

/**
 * Class that holds settings
 *
 * @package Proxy_Images
 */
class Proxy_Images_Admin {

	/**
	 * Holds settings group.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private $group = 'pxy_images';

	/**
	 * Holds settings settings page slug.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private $slug = 'pxy-images';

	/**
	 * Holds settings id.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private $settings = 'pxy_settings';

	/**
	 * Holds the permission to access settings page.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private $capability;

	/**
	 * Constructor of class.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct() {
		// Sets capability.
		$this->capability = apply_filters( 'pxy_capability', 'manage_options' );

		// Creates admin page.
		add_action( 'admin_menu', array( $this, 'pxy_add_admin_menu' ) );

		// Inits Settings.
		add_action( 'admin_init', array( $this, 'pxy_settings_init' ) );

		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

	}

	public function activation() {}
	public function deactivation() {}

	/**
	 * Loads text domain.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'proxy-images', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Creates settings page.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function pxy_add_admin_menu() {
		add_options_page( __( 'Proxy Images', 'proxy-images' ), __( 'Proxy Images', 'proxy-images' ), $this->capability, $this->slug, array( $this, 'pxy_options_page' ) );
	}

	/**
	 * Plugin settings.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function pxy_settings_init() {
		register_setting( $this->group, $this->settings, array( &$this, 'sanitize_settings' ) );

		add_settings_section( 'pxy_settings_section', '', '', $this->group );

		add_settings_field( 'pxy_original_url', __( 'Settings field description', 'proxy-images' ), array( $this, 'pxy_original_url_render' ), $this->group, 'pxy_settings_section'
		);

		add_settings_field( 'pxy_banner', __( 'Activate banner', 'proxy-images' ), array( $this, 'pxy_banner_render' ), $this->group, 'pxy_settings_section'
		);
	}

	/**
	 * Renders original url input.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function pxy_original_url_render() {
		$options = get_option( $this->settings );
		$url = empty( $options['pxy_original_url'] ) ? '' : $options['pxy_original_url'];
		?>
		<input type='text' name='pxy_settings[pxy_original_url]' value='<?php echo  esc_url( $url, null );?>'>

		<p class="description"><?php _e( 'The origin website. For example: "http://example.com" with no trailing slash.', 'proxy-images' ); ?></p>
		<?php
	}

	/**
	 * Renders elements banner checkbox.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function pxy_banner_render() {
		$options = get_option( $this->settings );
		$banner = empty( $options['pxy_banner'] ) ? '' : $options['pxy_banner'];
		?>
		<input type='checkbox' name='pxy_settings[pxy_banner]' value='show' <?php echo ( 'show' === $banner ) ? 'checked' : ''; ?>>

		<p class="description"><?php _e( 'Activate notification banner.', 'proxy-images' ); ?></p>
		<?php
	}

	/**
	 * Sanitizes form values.
	 *
	 * @param   array - $args - from $_REQUESTS.
	 * @return  array - $input - sanitized values.
	 * @access  public
	 * @since   1.0.0
	 */
	public function sanitize_settings( $args ) {

		$input = array();

		// Check for our nonce name.
		$nonce = ! empty( $_REQUEST['_pxy_nonce'] ) ? $_REQUEST['_pxy_nonce'] : false;
		if ( ! $nonce ) {
			wp_die( __( 'Sorry, your nonce did not verify.', 'proxy-images' ) );
		}

		if ( ! wp_verify_nonce( $nonce, '_pxy_nonce' ) ) {
			wp_die( __( 'Sorry, your nonce did not verify.', 'proxy-images' ) );
		}

		// Check uset capability.
		if ( ! current_user_can( $this->capability ) ) {
			 wp_die( __( 'You do not have sufficient permissions to access this page.', 'proxy-images' ) );
		}

		// Sanitizing url.
		if ( ! empty( $args['pxy_original_url'] ) ) {
			$url = filter_var( $args['pxy_original_url'], FILTER_VALIDATE_URL );

			if ( false === $url ) {
				add_settings_error( 'pxy_settings', 'pxy_invalid_url',__( 'Please enter a valid url', 'proxy-images' ), $type = 'error' );
			} else {
				$input['pxy_original_url'] = trailingslashit( $url );
			}
		}

		// Sanitizing url.
		if ( ! empty( $args['pxy_banner'] ) ) {
			$input['pxy_banner'] = ( 'show' === $args['pxy_banner'] ) ? $args['pxy_banner'] : null;
		}

		return $input;
	}

	/**
	 * Renders settings page.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function pxy_options_page() {
		?>
		<form action='options.php' method='post'>

			<h2>Proxi Images</h2>
			<?php
			wp_nonce_field( '_pxy_nonce', '_pxy_nonce' );
			settings_fields( $this->group );
			do_settings_sections( $this->group );
			submit_button();
			?>

		</form>
		<?php
	}
}

