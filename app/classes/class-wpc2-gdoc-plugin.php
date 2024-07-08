<?php
/**
 * Main plugin
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_GDoc_Plugin class definition
 */
class WPC2_GDoc_Plugin {

	/**
	 * Instance of our admin class.
	 *
	 * @var WPC2_GDoc_Admin
	 */
	private $admin;

	/**
	 * Instance of our backup manager class.
	 *
	 * @var WPC2_GDoc_Backup_Manager
	 */
	private $backup;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->admin  = WPC2_GDoc_Admin::get_instance();
		$this->backup = WPC2_GDoc_Backup_Manager::get_instance();
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
		$this->admin->setup_actions();
		$this->backup->setup_actions();
	}

	/**
	 * Main entry point of this plugin.
	 */
	public function start() {
		$this->register_wp_actions();
	}
}
