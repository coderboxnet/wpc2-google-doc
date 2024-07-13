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
	// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
	$auth = \CODERBOX\Wpc2GoogleDoc\WPC2_GDoc_Auth::get_instance();
	if ( ! $auth->is_token_valid() ) {
		error_log( 'WP2_GDoc_Job::refresh_google_token() -> token expired' );
		$refreshed = $auth->refresh_auth_token();
		if ( $refreshed ) {
			error_log( 'WP2_GDoc_Job::refresh_google_token() -> updated' );
		} else {
			error_log( 'WP2_GDoc_Job::refresh_google_token() -> unable to refresh token' );
		}
	} else {
		error_log( 'WP2_GDoc_Job::refresh_google_token() -> token still valid' );
		$timestap = $auth->get_expiration_time();
		$seconds  = $timestap - time();
		$message  = "token will expire in {$seconds} seconds";
		if ( $seconds > 60 ) {
			$minutes = floor( $seconds / 60 );
			$message = "token will expire in {$minutes} minutes";
		}
		error_log( "WP2_GDoc_Job::refresh_google_token() -> {$message}" );
	}
	// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
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

	// Get our classes.
	$options = \CODERBOX\Wpc2GoogleDoc\WPC2_GDoc_Options::get_instance();
	$backup  = \CODERBOX\Wpc2GoogleDoc\WPC2_GDoc_Backup_Manager::get_instance();

	// WP query args.
	$args = array(
		'post_type'   => $options->get_allowed_post_types(),
		'post_status' => 'any',
		'nopaging'    => true,
	);

	// Get our posts.
	$posts = get_posts( $args );
	$total = count( $posts );

	error_log( "WP2_GDoc_Job::gdrive_backup() -> Found {$total} total posts to backup" );

	// Backup.
	foreach ( $posts as $post ) {
		$backup->run_backups( $post );
	}

	error_log( 'WP2_GDoc_Job::gdrive_backup() -> all backups completed' );
	// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

/**
 * Register wpc2_gdoc_job_gdrive_backup action to be run daily.
 */
function wpc2_gdoc_register_run_gdrive_backup_job() {
	// Schedule wpc2_gdoc_job_refresh_google_token is not already scheduled.
	if ( ! wp_next_scheduled( 'wpc2_gdoc_job_gdrive_backup' ) ) {
		wp_schedule_event( time(), 'daily', 'wpc2_gdoc_job_gdrive_backup' );
	}
	add_action( 'wpc2_gdoc_job_gdrive_backup', 'wpc2_gdoc_job_gdrive_backup_func' );
}
