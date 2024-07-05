<?php
/**
 * Plugin Class.
 *
 * @package wpc2-google-doc
 */

namespace Wpc2GoogleDoc;
require_once plugin_dir_path( __FILE__ ) .'../vendor/autoload.php';
//require_once plugin_dir_path( __FILE__ ) . '../vendor/google/apiclient-services/autoload.php';

use Google\Client;
use Google\Service\Drive;
use Google\Service\DriveFile;
/**
 * Class Plugin.
 */
class Plugin {
    private $client;
    private $service;
    private $redirect_uri;
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
        $this->googleClient();
        $this->redirect_uri = 'https://' . $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'] . '?page=wpc2-google-settings';
        
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
        add_action( 'wp_ajax_google_connect_action', array( $this, 'googleConnectActionCallback' ) );

    }

    public function addCustomPostLink($actions, $post) {

        $actions['google'] = '<a href="#" class="push-google-doc" aria-label="Push to Google Doc" data-id="' . $post->ID . '">Push to Google Doc</a>';
        return $actions;
    }
    public function pushDocActionCallback() {
        $response = array(
            'status' => true,
            'authURL' => $this->createGoogleDocument()
        );

        wp_send_json_success($response);
    }

    public function googleConnectActionCallback() {
        $response = array(
            'status' => true,
            'authURL' => $this->googleConnectAccount()
        );

        wp_send_json_success($response);
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
            'wpc2-google-settings',
            array( $this, 'wpc2SettingsPage' )
        );
    }

    // Plugin settings page
    public function wpc2SettingsPage() {
        ?>
        <div class="wrap">
            <h2>WPC2 Google Doc Settings</h2>
            <form method="post" action="options.php">
                <?php do_settings_sections( 'wpc2_google_doc_options' ); ?>
                </form>
        </div>
        <?php
    }

    // Register settings
    public function registerSettings() {
        register_setting( 'wpc2_google_doc_options', 'wpc2_google_doc_options', array( $this, 'sanitizeOptions' ) );
        add_settings_section( 'wpc2_google_doc_section', 'Google API Settings', array( $this, 'sectionCallback' ), 'wpc2_google_doc_options' );
        add_settings_field( 'google_account_connect', 'Google Account Connect', array( $this, 'clientIdCallback' ), 'wpc2_google_doc_options', 'wpc2_google_doc_section' );
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
        if (isset($_GET['code'])) {
            try {
                if ( isset($_SESSION['code_verifier'])) {
                    $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code'], $_SESSION['code_verifier']);
                    $this->client->setAccessToken($token);
            
                    // store in the session also
                    $_SESSION['upload_token'] = $token;
                    echo $token;
                }
                // redirect back to the example
                //header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            } catch (\Throwable $th) {
                var_dump($th);
            }
        
        }
        $options = get_option( 'wpc2_google_doc_options' );
        $clientId = $options['client_id'] ?? "";
        $upload_token = $options['upload_token'] ?? "";
        if (!$upload_token) {
            echo '<input type="button" id="connect_google" value="Connect with Google">';
        } else { settings_fields( 'wpc2_google_doc_options' );
           do_settings_sections( 'wpc2_google_doc_options' );
           echo '<input type="submit" class="button-primary" value="Save Settings">';
        }

        if(isset($_GET['code'])) {
            try {
                $token = $this->client->fetchAccessTokenWithAuthCode(sanitize_text_field($_GET['code']), get_option('code_verifier'));
                $this->client->setAccessToken($token);
                // store in the options settings also
                update_option( 'upload_token', $token );
               
                // redirect back to the example
                header('Location: ' . filter_var($this->redirect_uri, FILTER_SANITIZE_URL));
            } catch (\Throwable $th) {
                var_dump($th);
            }
        }
       
    }


    public function googleClient() {
        
        $this->client = new \Google\Client();
        $this->client->setRedirectUri($this->redirect_uri);
        $this->client->addScope(\Google\Service\Drive::DRIVE);
        $this->service = new \Google\Service\Drive($this->client);
        $this->client->setAuthConfig( plugin_dir_path( __FILE__ ) . 'assets/credentials.json' );
        $this->client->setAccessType( 'offline' ); // 'offline' or 'online'
        
    }

    public function createGoogleDocument() {
        // Initialize Drive service
       // $service = new \Google\Service\Drive($this->client);
        
        // Create a new Google Document
        /*$fileMetadata = new \Google_Service_Drive_DriveFile();
        $fileMetadata->setName( $title );
        $fileMetadata->setMimeType( 'application/vnd.google-apps.document' );

        $file = $service->files->create( $fileMetadata, array(
            'fields' => 'id',
            'data' => $content,
        ));
        // Handle the response (e.g., get the document ID)
        return $file->id;*/
        if (!empty($_SESSION['upload_token'])) {
            try {
                $this->client->setAccessToken($_SESSION['upload_token']);
                if ($this->client->isAccessTokenExpired()) {
                    unset($_SESSION['upload_token']);
                } else {
                    // Now lets try and send the metadata as well using multipart!
                    $file = new \Google\Service\Drive\DriveFile();
                    $file->setName("DemoFile2.txt");
                    $result = $this->service->files->create(
                        $file,
                        [
                            'data' => "Sample content goes here",
                            'mimeType' => 'text/plain'
                        ]
                    );
                    var_dump($result);
                }
            } catch (\Throwable $th) {
                var_dump($th);
            }
        
        } else {
            try {
                $_SESSION['code_verifier'] = $this->client->getOAuth2Service()->generateCodeVerifier();
                return $this->client->createAuthUrl();
                
                //header("Location: {$authUrl}");
            } catch (\Throwable $th) {
                var_dump($th);
            }
        
        }
    }

    public function googleConnectAccount() {
        $options = get_option( 'wpc2_google_doc_options' );
        $upload_token = $options['upload_token'] ?? "";
        // Initialize Drive service
       // $service = new \Google\Service\Drive($this->client);
        
        // Create a new Google Document
        /*$fileMetadata = new \Google_Service_Drive_DriveFile();
        $fileMetadata->setName( $title );
        $fileMetadata->setMimeType( 'application/vnd.google-apps.document' );

        $file = $service->files->create( $fileMetadata, array(
            'fields' => 'id',
            'data' => $content,
        ));
        // Handle the response (e.g., get the document ID)
        return $file->id;*/
        if (!empty($upload_token)) {
            try {
                $this->client->setAccessToken($upload_token);
                if ($this->client->isAccessTokenExpired()) {
                   // unset($_SESSION['upload_token']);
                } else {
                    echo "do nothing";
                }
            } catch (\Throwable $th) {
                var_dump($th);
            }
        
        } else {
            try {

                update_option( 'code_verifier', $this->client->getOAuth2Service()->generateCodeVerifier());
                return $this->client->createAuthUrl();
                
                //header("Location: {$authUrl}");
            } catch (\Throwable $th) {
                var_dump($th);
            }
        
        }
    }

}
