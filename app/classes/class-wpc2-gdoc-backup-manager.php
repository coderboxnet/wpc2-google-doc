<?php
/**
 * Main backup features
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_GDoc_Backup_Manager class definition
 */
class WPC2_GDoc_Backup_Manager {
	private const BACKUP_STATUS_PENDING = 'PENDING';
	private const BACKUP_STATUS_SUCCESS = 'SUCCESS';
	private const BACKUP_STATUS_FAILED  = 'FAILED';

	/**
	 * Array of backup providers
	 *
	 * @var array
	 */
	private $providers;

	/**
	 * Plugin options instance.
	 *
	 * @var WPC2_GDoc_Options
	 */
	private $options;

	/**
	 * Singleton class instance.
	 *
	 * @var WPC2_GDoc_Backup_Manager
	 */
	private static $instance = null;

	/**
	 * Class constructor
	 */
	protected function __construct() {
		$this->providers['gdrive'] = WPC2_GDoc_GDrive_Backup::get_instance();

		// WP Options API instance.
		$this->options = WPC2_GDoc_Options::get_instance();
	}

	/**
	 * Get self instance.
	 *
	 * @return WPC2_GDoc_Backup
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
		\add_action( 'save_post', array( $this, 'backup_post' ), 40, 2 );
	}

	/**
	 * Get the different status for all backup providers.
	 *
	 * @param int $post_id The post id.
	 * @return array
	 */
	public function get_all_backup_status( $post_id ) {
		$result = array();
		foreach ( $this->providers as $name => $provider ) {
			$result[ $name ] = array(
				'status'     => $provider->get_backup_status( $post_id, self::BACKUP_STATUS_PENDING ),
				'updated_at' => $provider->get_backup_date( $post_id, self::BACKUP_STATUS_PENDING ),
			);
		}
		return $result;
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
			$this->run_backups( $post );
		}
	}

	/**
	 * Execute the backup task of each backup provider
	 *
	 * @param WP_Post $post the WP post object.
	 */
	public function run_backups( $post ) {
		$args = array(
			'post_id'      => $post->ID,
			'file_name'    => $this->generate_backup_file_name( $post ),
			'file_content' => $this->generate_backup_file_content( $post ),
			'file_type'    => apply_filters( 'cbox_wpc2_gdoc_backup_file_type', 'text/plain' ),
		);
		foreach ( $this->providers as $provider ) {
			$created = $provider->create_backup( $args );
			if ( $created ) {
				$provider->update_backup_status( $post->ID, self::BACKUP_STATUS_SUCCESS );
			} else {
				$provider->update_backup_status( $post->ID, self::BACKUP_STATUS_FAILED );
			}
			$provider->update_backup_date( $post->ID, wp_date( 'Y-m-d H:i:s' ) );
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

	/**
	 * Return the backup file name.
	 *
	 * @param WP_Post $post The WP post.
	 * @return string
	 */
	private function generate_backup_file_name( $post ) {
		// Apply any filters to our file name before returning.
		// Example:
		// add_filter('cbox_wpc2_gdoc_backup_file_name', 'cb_func', 10, 2 );
		// function cb_func($file_name, $post) { return $file_name; }
		// This usually goes in the theme functions.php file.
		return apply_filters( 'cbox_wpc2_gdoc_backup_file_name', "backup-{$post->post_type}-{$post->ID}.txt", $post );
	}

	/**
	 * Return the backup file content.
	 *
	 * @param WP_Post $post The WP post.
	 * @return string
	 */
	private function generate_backup_file_content( $post ) {
		// Fetch post data.
		$post_author = get_the_author_meta( 'display_name', $post->post_author );

		// Data to be saved.
		$content  = "Title: {$post->post_title}\n";
		$content .= "Author: $post_author\n";
		$content .= "Date: {$post->post_date}\n";
		$content .= "\n{$post->post_content}\n";

		// Apply any filters to our content before returning.
		// Example:
		// add_filter('cbox_wpc2_gdoc_backup_file_content', 'cb_func', 10, 2 );
		// function cb_func($content, $post) { return $content; }
		// This usually goes in the theme functions.php file.
		$content = apply_filters( 'cbox_wpc2_gdoc_backup_file_content', $content, $post );
		return $content;
	}
}
