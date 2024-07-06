<?php
/**
 * Save to Google Doc Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Save to Google Doc
 * Plugin URI:        https://github.com/coderboxnet/wpc2-google-doc
 * Description:       Saves new WordPress posts to a Google Doc.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Coderbox
 * Author URI:        https://github.com/coderboxnet/wpc2-google-doc
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpc2-google-doc
 *
 * @package wpc2-google-doc
 */

// Enable our libraries autload.
require_once 'vendor/autoload.php';

// Init our plugin.
$wpc2gdoc_plugin = new \CODERBOX\Wpc2GoogleDoc\WPC2_Google_Doc_Plugin();
register_activation_hook( __FILE__, array( $wpc2gdoc_plugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( $wpc2gdoc_plugin, 'deactivate' ) );

// Execute plugin main entry point.
$wpc2gdoc_plugin->start();
