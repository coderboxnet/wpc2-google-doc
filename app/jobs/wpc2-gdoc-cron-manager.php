<?php
/**
 * Main entry point for the site background jobs.
 *
 * @package wpc2-google-doc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include our dependencies.
require_once __DIR__ . '/wpc2-gdoc-google-jobs.php';

/**
 * Register plugin background process
 */
function cbox_wpc2_gdoc_setup_jobs() {
	// Refresh token job.
	wpc2_gdoc_register_refresh_google_token_job();

	// Google drive backup job.
	wpc2_gdoc_register_run_gdrive_backup_job();
}
