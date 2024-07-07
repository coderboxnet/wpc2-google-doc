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

	private const SETTINGS_MESSAGES    = 'cbox_wpc2gdoc_messages';
	private const MESSAGE_CODE         = 'cbox_wpc2gdoc_message_code';
	private const APP_SETTINGS_SECTION = 'cbox_wpc2gdoc_app_settings';

	/**
	 * Plugin options instance.
	 *
	 * @var WPC2_Google_Doc_Options
	 */
	private $options;

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

		// Manage the WP Options API.
		$this->options = WPC2_Google_Doc_Options::get_instance();

		// Our settings page slug.
		$this->settings_page = $settings_page;
	}

	/**
	 * Register the plugin settings to be used in WP-Admin.
	 */
	public function setup_settings() {
		register_setting(
			WPC2_Google_Doc_Options::OPTION_GROUP,
			WPC2_Google_Doc_Options::OPTION_NAME
		);
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
			self::APP_SETTINGS_SECTION,
			__( 'App Connection Settings', 'wpc2-google-doc' ),
			array( $this, 'cb_section_app_settings' ),
			$this->settings_page
		);
	}

	/**
	 * App Settings Section Callback
	 * Used to display content before the form fields.
	 *
	 * @param array $args The settings array, defining title, id, callback.
	 */
	public function cb_section_app_settings( $args ) {}

	/**
	 * Register client id and secret fields.
	 */
	public function register_fields_app_connection() {
		// Client ID field.
		add_settings_field(
			WPC2_Google_Doc_Options::CLIENT_ID,
			__( 'Client ID', 'wpc2-google-doc' ),
			array( $this, 'cb_field_client_id' ),
			$this->settings_page,
			self::APP_SETTINGS_SECTION,
		);

		// Client Secret field.
		add_settings_field(
			WPC2_Google_Doc_Options::CLIENT_SECRET,
			__( 'Client Secret', 'wpc2-google-doc' ),
			array( $this, 'cb_field_client_secret' ),
			$this->settings_page,
			self::APP_SETTINGS_SECTION,
		);
	}

	/**
	 * Handle the client id field
	 */
	public function cb_field_client_id() {
		printf(
			'<input type="text" id="%s" name="%s[%s]" value="%s" />',
			esc_attr( WPC2_Google_Doc_Options::CLIENT_ID ),
			esc_attr( WPC2_Google_Doc_Options::OPTION_NAME ),
			esc_attr( WPC2_Google_Doc_Options::CLIENT_ID ),
			esc_attr( $this->options->get_client_id() )
		);
	}

	/**
	 * Handle the client secret field
	 */
	public function cb_field_client_secret() {
		printf(
			'<input type="password" id="%s" name="%s[%s]" value="%s" />',
			esc_attr( WPC2_Google_Doc_Options::CLIENT_SECRET ),
			esc_attr( WPC2_Google_Doc_Options::OPTION_NAME ),
			esc_attr( WPC2_Google_Doc_Options::CLIENT_SECRET ),
			esc_attr( $this->options->get_client_secret() )
		);
	}

	/**
	 * Output the different settings sections and fields.
	 */
	public function render_sections() {
		echo '<form action="options.php" method="post">';
		settings_fields( WPC2_Google_Doc_Options::OPTION_GROUP );
		do_settings_sections( $this->settings_page );
		submit_button( 'Save Settings' );
		echo '</form>';
	}

	/**
	 * Show error/update messages.
	 */
	public function render_messages() {
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// add settings saved message with the class of "updated".
			add_settings_error( self::SETTINGS_MESSAGES, self::MESSAGE_CODE, __( 'Settings Saved', 'wpc2-google-doc' ), 'updated' );
		}
		// display messages.
		settings_errors( self::SETTINGS_MESSAGES );
	}
}
