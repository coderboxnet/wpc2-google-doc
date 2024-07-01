<?php
/**
 * Post Class.
 *
 * @package wpc2-google-doc
 */

namespace Wpc2GoogleDoc;

/**
 * Class PostBackup.
 */
class PostBackup {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		add_action( 'save_post', array( $this, 'create_backup' ) );
		add_action( 'manage_post_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
		add_filter( 'manage_post_posts_columns', array( $this, 'add_custom_column' ) );
	}

	/**
	 * Display content in the custom column.
	 *
	 * @param string $column_name Column name.
	 * @param int $post_id Post ID.
	 */
	public function custom_column_content( $column_name, $post_id ) {
		if ( $column_name == 'custom_column' ) {
			// Get the current backup status
			$backup_status = get_post_meta( $post_id, '_wpc2gd_backup_status', true );

			// Display content in the custom column for each post
			echo ($backup_status) ? $backup_status : "PENDING";
		}
	}

	/**
	 * Hook to add custom column to post list.
	 *
	 * @param array $columns Existing columns.
	 * @return array Columns with custom column added.
	 */
	public function add_custom_column( $columns ) {
		$columns['custom_column'] = 'Backup';
		return $columns;
	}

	/**
	 * Create Backup when post is saved.
	 *
	 * @param int $post_id Post ID.
	 */
	public function create_backup( $post_id ) {
		// Verify if this is an autosave routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Get the post object
		$post = get_post( $post_id );

		// Basic checks before creating backup
		if ( ! $post || ! post_type_supports( $post->post_type, 'revisions' ) || 'auto-draft' === $post->post_status || ! wp_revisions_enabled( $post ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Fetch post data
		$post_title   = $post->post_title;
		$post_content = $post->post_content;
		$post_author  = get_the_author_meta( 'display_name', $post->post_author );
		$post_date    = $post->post_date;

		// Data to be saved
		$data  = "Title: $post_title\n";
		$data .= "Backup Status: SUCCESS\n";
		$data .= "Author: $post_author\n";
		$data .= "Date: $post_date\n";
		$data .= "Content:\n$post_content\n";
		$data .= "------------------------\n";

		// Specify the file path
		$file_path = WP_CONTENT_DIR . '/uploads/post-backups/backup-post-' . $post_id . '.txt';

		// Ensure the uploads directory exists
		if ( ! file_exists( dirname( $file_path ) ) ) {
			mkdir( dirname( $file_path ), 0755, true );
		}

		// Write the data to the file
		file_put_contents( $file_path, $data, LOCK_EX );

		// Update the post meta to indicate backup status
		update_post_meta( $post_id, '_wpc2gd_backup_status', 'SUCCESS' );
	}
}
