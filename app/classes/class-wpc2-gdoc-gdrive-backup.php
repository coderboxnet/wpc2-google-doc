<?php
/**
 * Google backup functionalities
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_GDoc_GDrive_Backup class definition
 */
class WPC2_GDoc_GDrive_Backup implements WPC2_GDoc_Backup_Provider {

	private const BACKUP_POST_META_KEY = '_wpc2gd_gdrive_backup_status';

	/**
	 * Singleton class instance.
	 *
	 * @var WPC2_GDoc_GDrive_Backup
	 */
	private static $instance = null;

	/**
	 * Class constructor
	 */
	protected function __construct() {}

	/**
	 * Get self instance.
	 *
	 * @return WPC2_GDoc_GDrive_Backup
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Save the blog post content in a remote file in Google Drive.
	 *
	 * @param array $args Array with the file_name, file_content and file_type keys.
	 */
	public function create_backup( $args ) {
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		try {
			// Google Auth Client.
			$auth = WPC2_GDoc_Auth::get_instance();

			if ( ! $auth->is_connected() ) {
				error_log( 'WPC2_GDoc_GDrive_Backup::create_backup() -> Skip GDrive Backup, account not connected' );
				return false;
			}

			if ( ! $auth->is_token_valid() ) {
				error_log( 'WPC2_GDoc_GDrive_Backup::create_backup() -> Skip GDrive Backup, token expired' );
				return false;
			}

			// Google Drive Service.
			$drive = new \Google\Service\Drive( $auth->get_google_client() );

			// Google Document.
			$file = new \Google\Service\Drive\DriveFile();
			$file->setName( $args['file_name'] );
			$result = $drive->files->create(
				$file,
				array(
					'data'     => $args['file_content'],
					'mimeType' => $args['file_type'],
				)
			);
			return true;
		} catch ( \Throwable $th ) {
			error_log( 'WPC2_GDoc_GDrive_Backup::create_backup() -> Caught exception' );
			error_log( $th->getMessage() );
			error_log( $th->getTraceAsString() );
		}
		// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Get the backup status meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $default_value A default value if no status if found.
	 * @return string
	 */
	public function get_backup_status( $post_id, $default_value ) {
		$status = get_post_meta( $post_id, self::BACKUP_POST_META_KEY, true );

		if ( false === $status ) {
			return 'UNKNOWN';
		}
		return empty( $status ) ? $default_value : $status;
	}

	/**
	 * Update the backup status meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $status The backup status.
	 */
	public function update_backup_status( $post_id, $status ) {
		update_post_meta( $post_id, self::BACKUP_POST_META_KEY, $status );
	}
}
