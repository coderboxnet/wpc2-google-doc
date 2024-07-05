<?php
/**
 * Main plugin
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

use CODERBOX\Wpc2GoogleDoc\WPC2_Google_Doc_Admin;

/**
 * WPC2_Google_Doc_Plugin class definition
 */
class WPC2_Google_Doc_Plugin {

	/**
	 * Instance of our admin class.
	 *
	 * @var WPC2_Google_Doc_Admin
	 */
	private $admin;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->admin = new WPC2_Google_Doc_Admin();
	}

	/**
	 * Activate plugin
	 */
	public function activate() {}

	/**
	 * Deactivate plugin
	 */
	public function deactivate() {}

	/**
	 * Register all WP actions managed by this plugin.
	 */
	public function register_wp_actions() {
		$this->admin->setup_admin_actions();
	}

	/**
	 * Main entry point of this plugin.
	 */
	public function start() {
		$this->register_wp_actions();
	}
}
