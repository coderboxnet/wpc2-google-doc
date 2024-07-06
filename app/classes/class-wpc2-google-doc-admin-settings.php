<?php
/**
 * Plugin WP-Admin Settings
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Doc_Admin_Settings class definition
 */
class WPC2_Google_Doc_Admin_Settings {

	private const SETTINGS_OPTIONS_GROUP = 'cbox_wpc2gdoc_options';
	private const APP_CONNECTION_SECTION = 'cbox_wpc2gdoc_account_connection';
	private const FIELD_CLIENT_ID        = 'cbox_wpc2gdoc_field_client_id';
	private const FIELD_CLIENT_SECRET    = 'cbox_wpc2gdoc_field_client_secret';

	/**
	 * WP-Admin Settings Page Slug
	 *
	 * @var string
	 */
	private $settings_page;

	/**
	 * Class constructor
	 *
	 * @param string $settings_page Admin Settings Page Slug.
	 */
	public function __construct( $settings_page ) {
		$this->settings_page = $settings_page;
	}

	/**
	 * Register the plugin settings to be used in WP-Admin.
	 */
	public function setup_settings() {
		register_setting( $this->settings_page, self::SETTINGS_OPTIONS_GROUP );
	}

	/**
	 * Register all settings sections
	 */
	public function setup_sections() {
		$this->register_section_app_connection();
	}

	/**
	 * Register the fields for each of the sections.
	 */
	public function setup_fields() {
		$this->register_fields_app_connection();
	}

	/**
	 * Register the section to display the app connection fields.
	 */
	public function register_section_app_connection() {
		add_settings_section(
			self::APP_CONNECTION_SECTION,
			__( 'App Connection Settings', 'wpc2-google-doc' ),
			array( $this, 'cb_section_app_connection' ),
			$this->settings_page
		);
	}

	/**
	 * App Connection Section Callback
	 * Used to display content before the form fields.
	 *
	 * @param array $args The settings array, defining title, id, callback.
	 */
	public function cb_section_app_connection( $args ) {}

	/**
	 * Register client id and secret fields.
	 */
	public function register_fields_app_connection() {
		// Client ID field.
		add_settings_field(
			self::FIELD_CLIENT_ID,
			__( 'Client ID', 'wpc2-google-doc' ),
			array( $this, 'cb_field_client_id' ),
			$this->settings_page,
			self::APP_CONNECTION_SECTION,
		);

		// Client Secret field.
		add_settings_field(
			self::FIELD_CLIENT_SECRET,
			__( 'Client Secret', 'wpc2-google-doc' ),
			array( $this, 'cb_field_client_secret' ),
			$this->settings_page,
			self::APP_CONNECTION_SECTION,
		);
	}

	/**
	 * Handle the client id field
	 */
	public function cb_field_client_id() {
		printf(
			'<input type="text" id="%s" name="%s" />',
			esc_attr( self::FIELD_CLIENT_ID ),
			esc_attr( self::FIELD_CLIENT_ID )
		);
	}

	/**
	 * Handle the client secret field
	 */
	public function cb_field_client_secret() {
		printf(
			'<input type="password" id="%s" name="%s" />',
			esc_attr( self::FIELD_CLIENT_SECRET ),
			esc_attr( self::FIELD_CLIENT_SECRET )
		);
	}

	/**
	 * Output the different settings sections and fields.
	 */
	public function render_sections() {
		echo '<form action="options.php" method="post">';
		settings_fields( $this->settings_page );
		do_settings_sections( $this->settings_page );
		submit_button( 'Save Settings' );
		echo '</form>';
	}
}
