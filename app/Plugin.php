<?php
/**
 * Plugin Class.
 *
 * @package wpc2-google-doc
 */

namespace Wpc2GoogleDoc;
require_once plugin_dir_path( __FILE__ ) . '../vendor/google/apiclient-services/autoload.php';

use Google\Client;
use Google\Service\Drive;
use Google\Service\DriveFile;
/**
 * Class Plugin.
 */
class Plugin {
    private $client;
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
        $this->googleClient();
	}

	/**
	 * Activate plugin
	 */
	public function activate() {

    }

	/**
	 * Deactivate plugin
	 */
	public function deactivate() {}

    /**
	 * Initialize plugin
	 */
	private function init() {
        add_action( 'admin_init', array( $this, 'googleClient' ) );
        add_action( 'admin_menu', array( $this, 'addWPC2PluginMenu' ) );
        add_action( 'admin_init', array( $this, 'registerSettings' ) );
        add_action( 'wp_enqueue_scripts', $this->enqueuePluginScript() );
        add_filter( 'post_row_actions', array( $this, 'addCustomPostLink' ), 10, 2 );
        add_action( 'wp_ajax_push_doc_action', array( $this, 'pushDocActionCallback' ) );

    }

    public function addCustomPostLink($actions, $post) {

        $actions['google'] = '<a href="#" class="push-google-doc" aria-label="Push to Google Doc" data-id="' . $post->ID . '">Push to Google Doc</a>';
        return $actions;
    }
    public function pushDocActionCallback() {
        echo $this->createGoogleDocument('HOLA', 'This is the content');

        wp_send_json_success('Action completed successfully.');
    }

    // Enqueue JavaScript where you call the AJAX action
    public function enqueuePluginScript() {
        if ( is_admin() ) {
           wp_enqueue_script( 'wpc2-google-doc-script', plugin_dir_url( __FILE__ ).'assets/js/wpc2-google-doc.js', array( 'jquery' ), '1.0', true );
           wp_localize_script( 'wpc2-google-doc-script', 'wpc2Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
       }
       return true;
    }
    // Add admin menu
    public function addWPC2PluginMenu() {
        add_menu_page(
            'WPC2 Google Doc Settings',
            'WPC2 Google Doc',
            'manage_options',
            'your-plugin-settings',
            array( $this, 'wpc2SettingsPage' )
        );
    }

    // Plugin settings page
    public function wpc2SettingsPage() {
        ?>
        <div class="wrap">
            <h2>WPC2 Google Doc Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'wpc2_google_doc_options' ); ?>
                <?php do_settings_sections( 'wpc2_google_doc_options' ); ?>
                <!-- Your form fields here -->
                <input type="submit" class="button-primary" value="Save Settings">
            </form>
        </div>
        <?php
    }

    // Register settings
    public function registerSettings() {
        register_setting( 'wpc2_google_doc_options', 'wpc2_google_doc_options', array( $this, 'sanitizeOptions' ) );
        add_settings_section( 'wpc2_google_doc_section', 'Google API Settings', array( $this, 'sectionCallback' ), 'wpc2_google_doc_options' );
        add_settings_field( 'client_id', 'Client ID', array( $this, 'clientIdCallback' ), 'wpc2_google_doc_options', 'wpc2_google_doc_section' );
        // Add more fields as needed (Client Secret, Redirect URI, etc.)
    }

    // Sanitize options
    public function sanitizeOptions( $input ) {
        // Sanitization code here
        return $input;
    }

    public function sectionCallback() {
        echo "<strong>Please insert your Cliend ID below</strong>";
    }
    // Callbacks for settings fields
    public function clientIdCallback() {
        $options = get_option( 'wpc2_google_doc_options' );
        $clientId = $options['client_id'] ?? "";
        echo '<input type="text" id="client_id" name="wpc2_google_doc_options[client_id]" value="' . esc_attr( $clientId ) . '" />';
    }


    public function googleClient() {
        $this->client = new \Google_Client();
        $this->client->setApplicationName( 'WPC2 Google Doc' );
        $this->client->setScopes( [Drive::DRIVE] ); // Adjust scopes as needed
        $this->client->setAuthConfig( plugin_dir_path( __FILE__ ) . 'assets/credentials.json' );
        $this->client->setAccessType( 'offline' ); // 'offline' or 'online'
    }

    public function createGoogleDocument( $title, $content ) {
        // Initialize Drive service
        $service = new Drive( $this->client );
        // Create a new Google Document
        $fileMetadata = new \Google_Service_Drive_DriveFile();
        $fileMetadata->setName( $title );
        $fileMetadata->setMimeType( 'application/vnd.google-apps.document' );

        $file = $service->files->create( $fileMetadata, array(
            'fields' => 'id',
            'data' => $content,
        ));
        // Handle the response (e.g., get the document ID)
        return $file->id;
    }



}
