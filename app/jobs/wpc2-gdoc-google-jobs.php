<?php
/**
 * Main file for all google background job process
 *
 * @package wpc2-google-doc
 */

/**
 * Refresh google auth token if expired.
 */
function wpc2_gdoc_job_refresh_google_token_func() {
	$auth = \CODERBOX\Wpc2GoogleDoc\WPC2_GDoc_Auth::get_instance();
	if ( ! $auth->is_token_valid() ) {
		$refreshed = $auth->refresh_auth_token();
		if ( $refreshed ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WP2_GDoc_Job::refresh_google_token() -> updated' );
			// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}
}

/**
 * Register wpc2_gdoc_job_refresh_google_token action to be run hourly.
 */
function wpc2_gdoc_register_refresh_google_token_job() {
	// Schedule wpc2_gdoc_job_refresh_google_token is not already scheduled.
	if ( ! wp_next_scheduled( 'wpc2_gdoc_job_refresh_google_token' ) ) {
		wp_schedule_event( time(), 'hourly', 'wpc2_gdoc_job_refresh_google_token' );
	}
	add_action( 'wpc2_gdoc_job_refresh_google_token', 'wpc2_gdoc_job_refresh_google_token_func' );
}

/**
 * Backup the posts in Google drive dialy.
 */
function wpc2_gdoc_job_gdrive_backup_func() {
	// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log( 'WP2_GDoc_Job::gdrive_backup() -> running backups' );
	// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

/**
 * Register wpc2_gdoc_job_gdrive_backup action to be run daily.
 */
function wpc2_gdoc_register_run_gdrive_backup_job() {
	// Schedule wpc2_gdoc_job_refresh_google_token is not already scheduled.
	if ( ! wp_next_scheduled( 'wpc2_gdoc_job_gdrive_backup' ) ) {
		wp_schedule_event( time(), 'hourly', 'wpc2_gdoc_job_gdrive_backup' );
	}
	add_action( 'wpc2_gdoc_job_gdrive_backup', 'wpc2_gdoc_job_gdrive_backup_func' );
}