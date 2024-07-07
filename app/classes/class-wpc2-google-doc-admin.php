<?php
/**
 * Plugin WP-Admin functionalities
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Doc_Admin class definition
 */
class WPC2_Google_Doc_Admin {

	const ADMIN_SETTINGS_PAGE = 'cbox-wpc2-google-doc-settings';
	const SETTINGS_MESSAGES   = 'wporg_messages';

	/**
	 * WP Settigns
	 *
	 * @var WPC2_Google_Doc_Admin_Settings
	 */
	private $settings;

	/**
	 * Auth instance.
	 *
	 * @var WPC2_Google_Doc_Auth
	 */
	private $auth;



	/**
	 * Class constructor.
	 */
	public function __construct() {

		// All related to settings fields.
		$this->settings = new WPC2_Google_Doc_Admin_Settings( self::ADMIN_SETTINGS_PAGE );

		// Google Auth Client.
		$this->auth = WPC2_Google_Doc_Auth::get_instance();
		$this->auth->setup_redirect_uri( self::ADMIN_SETTINGS_PAGE );
		$this->auth->check_oauth_flow();
	}

	/**
	 * Register all WP-Admin actions managed by this plugin.
	 */
	public function setup_admin_actions() {
		\add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		\add_action( 'admin_init', array( $this, 'register_plugin_options' ) );
	}

	/**
	 * Add our admin menu link to our settings page.
	 */
	public function add_admin_menu() {
		\add_menu_page(
			'WPC2 Google Doc Settings',
			'WPC2 Google Doc',
			'manage_options',
			self::ADMIN_SETTINGS_PAGE,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register custom option fields and settings sections.
	 */
	public function register_plugin_options() {
		$this->settings->setup_settings();
		$this->settings->setup_sections();
		$this->settings->setup_fields();
	}

	/**
	 * Display our admin settings page.
	 */
	public function render_settings_page() {
		$this->settings->render_messages();
		echo '<div class="wrap">';
		printf( '<h1>%s</h1>', \esc_html__( 'WPC2 Google Doc Settings', 'wpc2-google-doc' ) );
		$this->render_connection_status();
		$this->render_redirect_uri_info();
		$this->settings->render_sections();
		echo '</div>';
	}

	/**
	 * Display the current app connection status with a Google account.
	 */
	private function render_connection_status() {
		echo '<div>';
		printf( '<h2>%s</h2>', \esc_html__( 'Connection Status', 'wpc2-google-doc' ) );
		if ( $this->auth->is_connected() && $this->auth->is_token_valid() ) {
			printf( '<strong>%s</strong>', \esc_html__( 'CONNECTED', 'wpc2-google-doc' ) );
		} else {
			$this->auth->disconnect();
			$this->auth->setup_code_verifier();
			printf( '<strong>%s</strong>', \esc_html__( 'NOT CONNECTED', 'wpc2-google-doc' ) );
			printf(
				'<p><a href="%s">%s</a></p>',
				\esc_attr( $this->auth->get_auth_url() ),
				\esc_html__( 'CONNECT', 'wpc2-google-doc' )
			);
		}
		echo '</div>';
	}

	/**
	 * Display the redirect uri information.
	 */
	private function render_redirect_uri_info() {
		echo '<div>';
		printf( '<h2>%s</h2>', \esc_html__( 'Redirect URI', 'wpc2-google-doc' ) );
		printf( '<p>%s</p>', \esc_html__( 'This is needed for your app configuration in Google', 'wpc2-google-doc' ) );
		printf( '<strong>%s</strong>', \esc_html( $this->auth->get_redirect_uri() ) );
		echo '</div>';
	}
}
