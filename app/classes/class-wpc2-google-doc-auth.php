<?php
/**
 * Plugin Authentication features
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Doc_Auth class definition
 */
class WPC2_Google_Doc_Auth {

	const STATUS_CONNECTED    = 'CONNECTED';
	const STATUS_DISCONNECTED = 'DISCONNECTED';

	/**
	 * Plugin options instance.
	 *
	 * @var WPC2_Google_Doc_Options
	 */
	private $options;

	/**
	 * Google client instance.
	 *
	 * @var Google\Client
	 */
	private $client;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = new WPC2_Google_Doc_Options();
		$this->client  = new \Google\Client();
		$this->setup_client_credentials();
		$this->setup_scopes();
	}

	/**
	 * Setup the client id and secret for the Google client instance.
	 */
	private function setup_client_credentials() {
		$credentials = $this->options->get_client_credentials();
		$this->client->setClientId( $credentials['client_id'] );
		$this->client->setClientSecret( $credentials['client_secret'] );
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
	private function setup_code_verifier() {
		$code_verifier = $this->client->getOAuth2Service()->generateCodeVerifier();
		$this->options->save_auth_code_verifier( $code_verifier );
	}

	/**
	 * Set the Google client instance redirect uri.
	 *
	 * @param string $admin_menu_slug The slug of the admin settings page.
	 */
	public function setup_redirect_uri( $admin_menu_slug ) {
		$uri = admin_url( "admin.php?page={$admin_menu_slug}" );
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
	 * Check if the plugin is already connected with a Google account.
	 *
	 * @return bool
	 */
	public function is_connected() {
		return $this->options->get_connection_status() === self::STATUS_CONNECTED;
	}
}
