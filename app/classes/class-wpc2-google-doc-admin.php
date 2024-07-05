<?php
/**
 * Plugin WP-Admin functionalities
 *
 * @package wpc2-google-doc
 */

// Define plugin namespace.
namespace CODERBOX\Wpc2GoogleDoc;

/**
 * WPC2_Google_Doc_Admin class definition
 */
class WPC2_Google_Doc_Admin {

	const ADMIN_MENU_SLUG = 'cbox-wpc2-google-doc-settings';

	/**
	 * Register all WP-Admin actions managed by this plugin.
	 */
	public function setup_admin_actions() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add our admin menu link to our settings page.
	 */
	public function add_admin_menu() {
		add_menu_page(
			'WPC2 Google Doc Settings',
			'WPC2 Google Doc',
			'manage_options',
			self::ADMIN_MENU_SLUG,
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Configure our admin settings page.
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'WPC2 Google Doc Settings', 'wpc2-google-doc' ); ?></h2>
		</div>
		<?php
	}
}
