<?php
/**
 * Plugin Class.
 *
 * @package wpc2-google-doc
 */

namespace Wpc2GoogleDoc;

/**
 * Class Plugin.
 */
class Plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize plugin
	 */
	private function init() {
		new PostBackup();
	}

	/**
	 * Activate plugin.
	 * This method can be used for activation routines if needed.
	 */
	public function activate() {
		// Activation code if needed
	}

	/**
	 * Deactivate plugin.
	 * This method can be used for deactivation routines if needed.
	 */
	public function deactivate() {
		// Deactivation code if needed
	}
}
