<?php
/**
 * Define rules that any backup process should implement
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_GDoc_Backup_Provider interface definition
 */
interface WPC2_GDoc_Backup_Provider {
	/**
	 * Singleton approach
	 */
	public static function get_instance();


	/**
	 * Method that implements the backup process.
	 *
	 * @param string $file_name The backup file name.
	 * @param string $file_content The backup file content.
	 */
	public function create_backup( $file_name, $file_content );

	/**
	 * Get the backup status of a given post.
	 *
	 * @param int    $post_id The post id.
	 * @param string $default_value A default value if no status if found.
	 * @return string
	 */
	public function get_backup_status( int $post_id, $default_value );

	/**
	 * Update the backup status meta field.
	 *
	 * @param int    $post_id The post id.
	 * @param string $status The backup status.
	 */
	public function update_backup_status( $post_id, $status );
}
