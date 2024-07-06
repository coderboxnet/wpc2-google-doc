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
	private const OPTION_CONNECTION_STATUS  = 'cbox_wpc2gdoc_constat';
	private const OPTION_CLIENT_CREDENTIALS = 'cbox_wpc2gdoc_clicreds';
	private const TRANSIENT_CODE_VERIFIER   = 'cbox_wpc2gdoc_codever';

	/**
	 * Get the stored values for the
	 * client id and client secret
	 *
	 * @return array
	 */
	public function get_client_credentials() {
		return array(
			'client_id'     => '',
			'client_secret' => '',
		);
	}

	/**
	 * Get the value of the connection status option.
	 * Possible values are CONNECTED or DISCONNECTED.
	 *
	 * @return string
	 */
	public function get_connection_status() {
		return get_option( self::OPTION_CONNECTION_STATUS, 'DISCONNECTED' );
	}

	/**
	 * Temporaly stores the auth code verifier.
	 * This expire in the next hour.
	 *
	 * @param string $code_verifier The generated verifier code.
	 */
	public function save_auth_code_verifier( $code_verifier ) {
		set_transient( self::TRANSIENT_CODE_VERIFIER, $code_verifier, HOUR_IN_SECONDS );
	}

	/**
	 * Set the connection status option.
	 *
	 * @param string $status The connection status, value are CONNECTED or DISCONNECTED.
	 */
	public function update_connection_status( $status ) {
		return update_option( self::OPTION_CONNECTION_STATUS, $status );
	}
}
