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
	const OPTION_CONNECTION_STATUS = 'cbox_wpc2gdoc_constat';

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
	 * Set the connection status option.
	 *
	 * @param string $status The connection status, value are CONNECTED or DISCONNECTED.
	 */
	public function update_connection_status( $status ) {
		return update_option( self::OPTION_CONNECTION_STATUS, $status );
	}
}
