<?php
/**
 * Plugin Options via WP OPTIONS API.
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Doc_Options class definition
 */
class WPC2_Google_Doc_Options {
	private const OPTION_ACCESS_TOKEN      = 'cbox_wpc2gdoc_token';
	private const OPTION_CONNECTION_STATUS = 'cbox_wpc2gdoc_constat';
	private const TRANSIENT_CODE_VERIFIER  = 'cbox_wpc2gdoc_codever';

	public const OPTION_GROUP  = 'cbox_wpc2gdoc_group';
	public const OPTION_NAME   = 'cbox_wpc2gdoc_options';
	public const CLIENT_ID     = 'client_id';
	public const CLIENT_SECRET = 'client_secret';

	/**
	 * Singleton class instance.
	 *
	 * @var WPC2_Google_Doc_Options
	 */
	private static $instance = null;


	/**
	 * Hold the app settings options
	 * This array keys will have the
	 * app client id and secret
	 *
	 * @var array
	 */
	private $app_options = array();

	/**
	 * Prevent direct construction calls
	 */
	protected function __construct() {
		$default_options   = $this->get_default_options();
		$this->app_options = \get_option( self::OPTION_NAME, $default_options );
	}

	/**
	 * Generate empty values as default options.
	 *
	 * @return array
	 */
	private function get_default_options() {
		return array(
			self::CLIENT_ID     => '',
			self::CLIENT_SECRET => '',
		);
	}

	/**
	 * Get self instance.
	 *
	 * @return WPC2_Google_Doc_Options
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}



	/**
	 * Get the stored values for the
	 * client id and client secret
	 *
	 * @return array
	 */
	public function get_client_credentials() {
		return array(
			'client_id'     => $this->get_client_id(),
			'client_secret' => $this->get_client_secret(),
		);
	}

	/**
	 * Get the stored client secret
	 *
	 * @return string
	 */
	public function get_client_secret() {
		return $this->app_options[ self::CLIENT_SECRET ];
	}

	/**
	 * Get the stored client id
	 *
	 * @return string
	 */
	public function get_client_id() {
		return $this->app_options[ self::CLIENT_ID ];
	}

	/**
	 * Get the value of the connection status option.
	 * Possible values are CONNECTED or DISCONNECTED.
	 *
	 * @return string
	 */
	public function get_connection_status() {
		return \get_option( self::OPTION_CONNECTION_STATUS, 'DISCONNECTED' );
	}

	/**
	 * Temporaly stores the auth code verifier.
	 * This expire in the next hour.
	 *
	 * @param string $code_verifier The generated verifier code.
	 */
	public function save_auth_code_verifier( $code_verifier ) {
		\set_transient( self::TRANSIENT_CODE_VERIFIER, $code_verifier, HOUR_IN_SECONDS );
	}

	/**
	 * Get the stored code verifier for the current OAuth process.
	 *
	 * @return string
	 */
	public function get_auth_code_verifier() {
		$code_verifier = \get_transient( self::TRANSIENT_CODE_VERIFIER );
		if ( false === $code_verifier ) {
			// Transient expired or not exists.
			return '';
		}
		return $code_verifier;
	}

	/**
	 * Set the connection status option.
	 *
	 * @param string $status The connection status, value are CONNECTED or DISCONNECTED.
	 */
	public function update_connection_status( $status ) {
		return update_option( self::OPTION_CONNECTION_STATUS, $status );
	}

	/**
	 * Store the generated auth token
	 *
	 * @param string $token The generated token from the OAuth Flow.
	 */
	public function update_access_token( $token ) {
		return \update_option( self::OPTION_ACCESS_TOKEN, $token );
	}

	/**
	 * Get the generated auth token
	 *
	 * @return string
	 */
	public function get_access_token() {
		return \get_option( self::OPTION_ACCESS_TOKEN, '' );
	}

	/**
	 * Get the selected post types to do the backup
	 *
	 * @return array
	 */
	public function get_allowed_post_types() {
		return array();
	}
}
