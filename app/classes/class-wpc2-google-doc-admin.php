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
	 * Auth instance.
	 *
	 * @var WPC2_Google_Doc_Auth
	 */
	private $auth;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->auth = new WPC2_Google_Doc_Auth();
		$this->auth->setup_redirect_uri( self::ADMIN_MENU_SLUG );
	}

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
			<?php if ( $this->auth->is_connected() ) : ?>
				CONNECTED
			<?php else : ?>
				NOT CONNECTED
				<?php echo esc_attr( $this->auth->get_auth_url() ); ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
