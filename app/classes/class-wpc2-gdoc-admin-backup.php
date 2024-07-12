<?php
/**
 * Admin backup related info
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_GDoc_Admin_Backup class definition
 */
class WPC2_GDoc_Admin_Backup {
	private const BACKUP_CUSTOM_COLUMN_KEY = 'cbox_wp2gdoc_backup_column';

	/**
	 * Backup manager
	 *
	 * @var WPC2_GDoc_Backup_Manager
	 */
	private $bm;

	/**
	 * Plugin options instance.
	 *
	 * @var WPC2_GDoc_Options
	 */
	private $options;

	/**
	 * Singleton class instance.
	 *
	 * @var WPC2_GDoc_Admin_Backup
	 */
	private static $instance = null;

	/**
	 * Class constructor
	 */
	protected function __construct() {
		// WP Options API instance.
		$this->options = WPC2_GDoc_Options::get_instance();

		// Backup Manager Instance.
		$this->bm = WPC2_GDoc_Backup_Manager::get_instance();
	}

	/**
	 * Get self instance.
	 *
	 * @return WPC2_GDoc_Admin_Backup
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Register all WP actions.
	 */
	public function setup_actions() {
		$allowed_post_types = $this->options->get_allowed_post_types();
		foreach ( $allowed_post_types as $post_type ) {
			$hook_name = $this->get_hook_name_for_column_content( $post_type );
			\add_action( $hook_name, array( $this, 'backup_status_column_content' ), 40, 2 );
		}
	}

	/**
	 * Register all WP filters.
	 */
	public function setup_filters() {
		$allowed_post_types = $this->options->get_allowed_post_types();
		foreach ( $allowed_post_types as $post_type ) {
			$hook_name = $this->get_hook_name_for_column_filter( $post_type );
			\add_filter( $hook_name, array( $this, 'add_backup_status_column' ), 40 );
		}
	}

	/**
	 * Display content in the custom column.
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id Post ID.
	 */
	public function backup_status_column_content( $column_name, $post_id ) {
		if ( self::BACKUP_CUSTOM_COLUMN_KEY === $column_name && $this->user_can_see_backup_column() ) {
			// Get all the backup status .
			$backup_status = $this->bm->get_all_backup_status( $post_id );

			$content = '';

			foreach ( $backup_status as $provider => $backup ) {
				$content .= "<strong>Provider:</strong> {$provider}<br />\n";
				$content .= "<strong>Status:</strong> {$backup['status']}<br />\n";
				$content .= "<strong>Date:</strong> {$backup['updated_at']}<br />\n";
			}

			// Display content in the custom column for each post .
			echo wp_kses_post( $content );
		}
	}

	/**
	 * Hook to add custom column to post list.
	 *
	 * @param array $columns Existing columns.
	 * @return array Columns with custom column added.
	 */
	public function add_backup_status_column( $columns ) {
		if ( $this->user_can_see_backup_column() ) {
			$columns[ self::BACKUP_CUSTOM_COLUMN_KEY ] = __( 'Backup', 'wpc2-google-doc' );
		}
		return $columns;
	}

	/**
	 * Proper hook to display the backup column in the table.
	 *
	 * @param string $post_type The given post type.
	 * @return string
	 */
	private function get_hook_name_for_column_filter( $post_type ) {
		if ( 'post' === $post_type ) {
			// Needed for the built-in wp posts.
			return 'manage_posts_columns';
		} elseif ( 'page' === $post_type ) {
			// Needed for the built-in wp pages.
			return 'manage_pages_columns';
		} else {
			// any other post type.
			return "manage_{$post_type}_posts_columns";
		}
	}

	/**
	 * Proper hook to display the backup column content row.
	 *
	 * @param string $post_type The given post type.
	 * @return string
	 */
	private function get_hook_name_for_column_content( $post_type ) {
		if ( 'post' === $post_type ) {
			// Needed for the built-in wp posts.
			return 'manage_posts_custom_column';
		} elseif ( 'page' === $post_type ) {
			// Needed for the built-in wp pages.
			return 'manage_pages_custom_column';
		} else {
			// any other post type.
			return "manage_{$post_type}_posts_custom_column";
		}
	}

	/**
	 * Validates if the current logged user can see the backup column.
	 *
	 * @return bool
	 */
	private function user_can_see_backup_column() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		return true;
	}
}
