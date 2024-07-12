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

	private const BACKUP_POST_META_STATUS     = '_wpc2gd_gdrive_backup_status';
	private const BACKUP_POST_META_FILE_ID    = '_wpc2gd_gdrive_backup_file_id';
	private const BACKUP_POST_META_UPDATED_AT = '_wpc2gd_gdrive_backup_updated_at';

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
	 * @param array $args Array with the post_id, file_name, file_content and file_type keys.
	 */
	public function create_backup( $args ) {

		// Google Auth Client.
		$auth = WPC2_GDoc_Auth::get_instance();

		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		if ( ! $auth->is_connected() ) {
			error_log( 'WPC2_GDoc_GDrive_Backup::create_backup() -> Skip GDrive Backup, account not connected' );
			return false;
		}

		if ( ! $auth->is_token_valid() ) {
			error_log( 'WPC2_GDoc_GDrive_Backup::create_backup() -> Skip GDrive Backup, token expired' );
			return false;
		}
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

		$file_id = get_post_meta( $args['post_id'], self::BACKUP_POST_META_FILE_ID, true );
		if ( empty( $file_id ) ) {
			return $this->create_file( $args, $auth );
		} else {
			return $this->update_file( $file_id, $args, $auth );
		}
	}

	/**
	 * Save the blog post content in a new remote file in Google Drive.
	 *
	 * @param array                $args Array with the post_id, file_name, file_content and file_type keys.
	 * @param WPC2_Google_Doc_Auth $auth Google auth session.
	 */
	private function create_file( $args, $auth ) {

		try {

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
			$this->update_backup_file_id( $args['post_id'], $result->getId() );
			return true;
		} catch ( \Throwable $th ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WPC2_GDoc_GDrive_Backup::create_file() -> Caught exception' );
			error_log( $th->getMessage() );
			error_log( $th->getTraceAsString() );
			// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Update remote file in Google Drive.
	 *
	 * @param string               $file_id Google drive file id.
	 * @param array                $args Array with the post_id, file_name, file_content and file_type keys.
	 * @param WPC2_Google_Doc_Auth $auth Google auth session.
	 */
	private function update_file( $file_id, $args, $auth ) {
		try {

			// Google Drive Service.
			$drive = new \Google\Service\Drive( $auth->get_google_client() );

			// Google Document.
			$file = new \Google\Service\Drive\DriveFile();

			$result = $drive->files->update(
				$file_id,
				$file,
				array(
					'data'     => $args['file_content'],
					'mimeType' => $args['file_type'],
				)
			);
			return true;
		} catch ( \Throwable $th ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WPC2_GDoc_GDrive_Backup::update_file() -> Caught exception' );
			error_log( $th->getMessage() );
			error_log( $th->getTraceAsString() );
			// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Get the backup status meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $default_value A default value if no status if found.
	 * @return string
	 */
	public function get_backup_status( $post_id, $default_value ) {
		$status = get_post_meta( $post_id, self::BACKUP_POST_META_STATUS, true );

		if ( false === $status ) {
			return 'UNKNOWN';
		}
		return empty( $status ) ? $default_value : $status;
	}

	/**
	 * Get the backup updated_at meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $default_value A default value if no date if found.
	 * @return string
	 */
	public function get_backup_date( $post_id, $default_value ) {
		$updated_at = get_post_meta( $post_id, self::BACKUP_POST_META_UPDATED_AT, true );

		if ( false === $updated_at ) {
			return 'UNKNOWN';
		}
		return empty( $updated_at ) ? $default_value : $updated_at;
	}

	/**
	 * Update the backup status meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $status The backup status.
	 */
	public function update_backup_status( $post_id, $status ) {
		update_post_meta( $post_id, self::BACKUP_POST_META_STATUS, $status );
	}

	/**
	 * Save the generate file id for this given post.
	 *
	 * @param int    $post_id The post id.
	 * @param string $file_id Generated Google drive file id.
	 */
	private function update_backup_file_id( $post_id, $file_id ) {
		update_post_meta( $post_id, self::BACKUP_POST_META_FILE_ID, $file_id );
	}

	/**
	 * Update the backup updated_at meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $updated_at A MySQL date format string.
	 */
	public function update_backup_date( $post_id, $updated_at ) {
		update_post_meta( $post_id, self::BACKUP_POST_META_UPDATED_AT, $updated_at );
	}
}
