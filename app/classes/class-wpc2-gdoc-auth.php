<?php
/**
 * Plugin Authentication features
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_GDoc_Auth class definition
 */
class WPC2_GDoc_Auth {

	const STATUS_CONNECTED    = 'CONNECTED';
	const STATUS_DISCONNECTED = 'DISCONNECTED';

	/**
	 * Plugin options instance.
	 *
	 * @var WPC2_GDoc_Options
	 */
	private $options;

	/**
	 * Google client instance.
	 *
	 * @var \Google\Client
	 */
	private $client;

	/**
	 * Singleton class instance.
	 *
	 * @var WPC2_GDoc_Auth
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 */
	protected function __construct() {
		$this->options = WPC2_GDoc_Options::get_instance();
		$this->client  = new \Google\Client();
		$this->setup_client_credentials();
		$this->setup_scopes();
	}

	/**
	 * Get self instance.
	 *
	 * @return WPC2_Google_Doc_Auth
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Setup the client id and secret for the Google client instance.
	 */
	private function setup_client_credentials() {
		$credentials = $this->options->get_client_credentials();
		$this->client->setClientId( $credentials['client_id'] );
		$this->client->setClientSecret( $credentials['client_secret'] );
		$this->client->setAccessType( 'offline' );

		$token = $this->options->get_access_token();
		$this->client->setAccessToken( $token );
	}

	/**
	 * Configure proper OAuth services scopes.
	 */
	private function setup_scopes() {
		$this->client->addScope( \Google\Service\Drive::DRIVE );
	}

	/**
	 * Generate the code verifier used in the auth process.
	 */
	public function setup_code_verifier() {
		$code_verifier = $this->client->getOAuth2Service()->generateCodeVerifier();
		$this->options->save_auth_code_verifier( $code_verifier );
	}

	/**
	 * Set the Google client instance redirect uri.
	 *
	 * @param string $settings_page The slug of the admin settings page.
	 */
	public function setup_redirect_uri( $settings_page ) {
		$uri = \admin_url( "admin.php?page={$settings_page}" );
		$this->client->setRedirectUri( $uri );
	}

	/**
	 * Get the URl to init the login process with the Google account.
	 *
	 * @return string
	 */
	public function get_auth_url() {
		$this->setup_code_verifier();
		return $this->client->createAuthUrl();
	}

	/**
	 * Retrieve the generated redirect URI.
	 *
	 * @return string
	 */
	public function get_redirect_uri() {
		return $this->client->getRedirectUri();
	}

	/**
	 * Check if the plugin is already connected with a Google account.
	 *
	 * @return bool
	 */
	public function is_connected() {
		return self::STATUS_CONNECTED === $this->options->get_connection_status();
	}

	/**
	 * Check if the corrent configured token is still valid.
	 *
	 * @return bool
	 */
	public function is_token_valid() {
		if ( $this->client->isAccessTokenExpired() ) {
			return false;
		}
		return true;
	}

	/**
	 * Reset any stored values as they expired.
	 */
	public function disconnect() {
		$this->client->setAccessToken( '' );
		$this->options->update_connection_status( self::STATUS_DISCONNECTED );
		$this->options->update_access_token( '' );
	}

	/**
	 * Generate and save the access token from an existing auth flow.
	 */
	public function check_oauth_flow() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['code'] ) ) {
			try {
				$code     = \sanitize_text_field( \wp_unslash( $_GET['code'] ) );
				$verifier = $this->options->get_auth_code_verifier();
				if ( ! empty( $verifier ) ) {
					$token = $this->client->fetchAccessTokenWithAuthCode( $code, $verifier );

					// Set client access token.
					$this->update_auth_token( $token );
				}

				// redirect back to our settings page.
				$final_url = $this->get_redirect_uri();
				\wp_safe_redirect( $final_url );
			} catch ( \Throwable $th ) {
				// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( $th->getMessage() );
				error_log( $th->getTraceAsString() );
				// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Calculate how much is left for the token to expire.
	 *
	 * @return int
	 */
	public function get_expiration_time() {
		$token = $this->client->getAccessToken();
		if ( is_array( $token ) && isset( $token['created'], $token['expires_in'] ) ) {
			$timestamp = $token['created'] + $token['expires_in'];
			return $timestamp;
		}
		return 0;
	}

	/**
	 * Refresh current auth token if it's expired.
	 */
	public function refresh_auth_token() {
		try {
			$refresh_token = $this->client->getRefreshToken();
			if ( ! is_null( $refresh_token ) ) {
				$token = $this->client->fetchAccessTokenWithRefreshToken( $refresh_token );
				$this->update_auth_token( $token );
				return true;
			}
		} catch ( \Throwable $th ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WPC2_GDoc_Auth::refresh_auth_token() -> Caught exception' );
			error_log( $th->getMessage() );
			error_log( $th->getTraceAsString() );
			return false;
			// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Update google account auth token
	 *
	 * @param array $token Access token.
	 */
	private function update_auth_token( $token ) {
		// Set client access token.
		$this->client->setAccessToken( $token );

		// store in the token.
		$this->options->update_access_token( $token );

		// update the connection status to connected.
		$this->options->update_connection_status( self::STATUS_CONNECTED );
	}

	/**
	 * Retrive the Google Client
	 *
	 * @return \Google\Client
	 */
	public function get_google_client() {
		return $this->client;
	}
}
