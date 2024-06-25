<?php
/**
 * Save to Google Doc Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Save to Google Doc
 * Plugin URI:        https://github.com/coderboxnet/wpc2-google-doc
 * Description:       Saves new WordPress posts to a Google Doc.
 * Version:           0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Coderbox
 * Author URI:        https://github.com/coderboxnet/wpc2-google-doc
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

require_once 'vendor/autoload.php';

use Wpc2GoogleDoc\Plugin;

if ( class_exists( 'Wpc2GoogleDoc\Plugin' ) ) {
	$the_plugin = new Plugin();
}

register_activation_hook( __FILE__, array( $the_plugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( $the_plugin, 'deactivate' ) );
