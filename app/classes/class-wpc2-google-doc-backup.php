<?php
/**
 * Google backup functionalities
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Doc_Backup class definition
 */
class WPC2_Google_Doc_Backup implements WPC2_Google_Backup_Provider {


	/**
	 * Google drive service
	 *
	 * @var \Google\Service\Drive
	 */
	private $drive;

	/**
	 * Plugin options instance.
	 *
	 * @var WPC2_Google_Doc_Options
	 */
	private $options;

	/**
	 * Singleton class instance.
	 *
	 * @var WPC2_Google_Doc_Backup
	 */
	private static $instance = null;

	/**
	 * Class constructor
	 */
	protected function __construct() {
		// Google Auth Client.
		$auth = WPC2_Google_Doc_Auth::get_instance();

		// Google Drive Service.
		$this->drive = new \Google\Service\Drive( $auth->get_google_client() );

		// WP Options API instance.
		$this->options = WPC2_Google_Doc_Options::get_instance();
	}

	/**
	 * Get self instance.
	 *
	 * @return WPC2_Google_Doc_Backup
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Register all WP actions managed by this provider.
	 */
	public function setup_actions() {
		\add_action( 'save_post', array( $this, 'backup_post' ), 2, 40 );
	}

	/**
	 * Handle the `save_post` action from WP
	 *
	 * @param int     $post_id The WP post id.
	 * @param WP_Post $post the WP post object.
	 */
	public function backup_post( $post_id, $post ) {
		// Check first if we need to backup this post type.
		if ( $this->should_backup( $post->post_type ) ) {
			// Proceed with the backup.
			$this->create_backup( $post );
		}
	}

	/**
	 * Save the blog post content in a remote file in Google Drive.
	 *
	 * @param WP_Post $post the WP post object.
	 */
	public function create_backup( $post ) {
		try {
			// code...
		} catch ( \Throwable $th ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $th->getMessage() );
			error_log( $th->getTraceAsString() );
			// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Check if this post type should be backup or not.
	 *
	 * @param string $post_type The post type to validate.
	 * @return bool
	 */
	public function should_backup( $post_type ) {
		$allowed_post_types = $this->options->get_allowed_post_types();
		if ( empty( $allowed_post_types ) ) {
			return true;
		}
		if ( in_array( $post_type, $allowed_post_types, true ) ) {
			return true;
		}
		return false;
	}
}
