<?php
/**
 * Define rules that any backup process should implement
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Backup_Provider interface definition
 */
interface WPC2_Google_Backup_Provider {
	/**
	 * Singleton approach
	 */
	public static function get_instance();

	/**
	 * Make sure any backup provider setup their
	 * own WP actions
	 */
	public function setup_actions();

	/**
	 * Method that hooks in the `save_post` action.
	 *
	 * @param int     $post_id The WP post id.
	 * @param WP_Post $post the WP post object.
	 */
	public function backup_post( $post_id, $post );

	/**
	 * Method that implements the backup process.
	 *
	 * @param WP_Post $post the WP post object.
	 */
	public function create_backup( $post );

	/**
	 * Implements if the given post type should be backup or not.
	 *
	 * @param string $post_type The current post type.
	 * @return bool
	 */
	public function should_backup( $post_type );
}
