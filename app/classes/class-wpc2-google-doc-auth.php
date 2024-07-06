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
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = new WPC2_Google_Doc_Options();
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
