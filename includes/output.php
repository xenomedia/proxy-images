<?php
/**
 * Class to replace the uploads path with the live path
 *
 * @package  Proxy_Images
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class the keeps live path for images
 *
 * @package     Searchmetrics
 */
class Proxy_Images_Output_Buffer {

	/**
	 * Page html content.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public $html;

	/**
	 * Holds the live uploads url.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private $live_url;

	/**
	 * Holds the current site url.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private $site_url;

	/**
	 * Holds patterns for search replace.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private $patterns;

	/**
	 * Contructor.
	 *
	 * @param  string - $html - actual html code for page.
	 * @param   $args - $array url of current site and original site plus patterns to be replaced.
	 * @access  protected
	 * @since   1.0.0
	 */
	public function __construct( $html, $args ) {

		$this->site_url = $args['site'];
		$this->live_url = $args['live'];
		$this->patterns = $args['patterns'];

		if ( ! empty( $html ) ) {
			$this->parse_html( $html );
		}

	}

	/**
	 * Magic method.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function __toString() {
		return $this->html;
	}

	/**
	 * Replace string uploads with live site.
	 *
	 * @param   string - $html - actual html code for page.
	 * @access  public
	 * @since   1.0.0
	 */
	protected function str_replace_html( $html ) {
		foreach ( $this->patterns as $pattern => $patt ) {
			$temp = preg_replace( $patt['pattern'], $patt['replace'], $html );
			if ( ! empty( $temp ) ) {
				$html = $temp;
			}
		}

		return $html;
	}

	/**
	 * Calls str replace functions
	 *
	 * @param   string - $html - actual html code for page
	 * @access  public
	 * @since   1.0.0
	 */
	public function parse_html( $html ) {
		$this->html = $this->str_replace_html( $html );
	}

}

/**
 * Calling class Proxy_Images_Output_Buffer.
 *
 * @param   string - $html - actual html code for page
 * @param   string - $html - actual html code for page
 * @return   object - object of class Proxy_Images_Output_Buffer
 */
function pxy_html_compression_finish( $html, $args ) {
	return new Proxy_Images_Output_Buffer( $html, $args );
}

/**
 * Filter to return original url to be replace.
 *
 * @param void
 * @return string if null then boolean original url
 */
function pxy_output_original_url() {

	$url = pxy_get_settings( $arg = 'pxy_original_url' );

	return ( ! empty( $url ) ) ? trailingslashit( $url ) : false;

}

/**
 * Calling get_header hook.
 */
function pxy_html_compression_start() {

	// Gets original url from settigns.
	$live_url = pxy_output_original_url( 'pxy_output_original_url' );
	$live_url = trailingslashit( $live_url );

	// Gets site url.
	$site_url = apply_filters( 'pxy_site_url', trailingslashit( get_site_url() ) );

	// Set patterns to search and replace

	$site_url_formatted= str_replace("/", "\/", $site_url);
	$live_url_formatted= str_replace("/", "\/", $live_url);

	$patterns = apply_filters( 'pxy_patterns', array(
			[
				'pattern' => '/src[ ]*=[ ]*"(.*)\/wp-content\/uploads\/.*?/',
				'replace' => 'src="' . $live_url . 'wp-content/uploads/'
			],
			[
				'pattern'=>"/src[ ]*=[ ]*'(.*)\/wp-content\/uploads\/.*?/",
				'replace' => "src='" . $live_url . 'wp-content/uploads/'
			],
			[
				'pattern' => '/' . $site_url_formatted . 'wp-content\/uploads\/.*?/',
				'replace' => $live_url_formatted . 'wp-content/uploads/'
			],
		)
	);

	// Args for class Proxy_Images_Output_Buffer.
	$args = array(
		'site' => $site_url,
		'live' => $live_url,
		'patterns' => $patterns,
	);

	// Creates class only if original url differes from site url.
	if ( ! empty( $live_url ) && ( $live_url !== $site_url ) ) {

		ob_start( function( $buffer ) use ( $site_url, $args ) {
			return pxy_html_compression_finish( $buffer, $args );
		});

	}
}
add_action( 'get_header', 'pxy_html_compression_start' );
