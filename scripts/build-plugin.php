<?php
/**
 * Build script that generate the zip file.
 *
 * @package wpc2-google-doc
 */

$plugin_dir = dirname( __DIR__ );
$build_dir  = $plugin_dir . '/build';
$zip_file   = $plugin_dir . '/build-zip/wpc2-google-doc.zip';


/**
 * Function to delete a directory and its contents recursively
 *
 * @param string $dir_path The directory path to be deleted.
 */
function delete_dir( $dir_path ) {
	if ( ! is_dir( $dir_path ) ) {
		return;
	}

	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $dir_path, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);

	foreach ( $files as $file_info ) {
		$todo = ( $file_info->isDir() ? 'rmdir' : 'unlink' );
		$todo( $file_info->getRealPath() );
	}
	rmdir( $dir_path ); //phpcs:ignore
}

// Remove and recreate the build directory.
delete_dir( $plugin_dir . '/build/' );
delete_dir( $plugin_dir . '/build-zip/' );
mkdir( $plugin_dir . '/build/' ); //phpcs:ignore
mkdir( $plugin_dir . '/build-zip/' ); //phpcs:ignore

// Copy all files except the ones to exclude.
$exclude_files = array(
	'.git',
	'.editorconfig',
	'.prettierrc.js',
	'.vscode',
	'CONTRIBUTING.md',
	'build',
	'build-zip',
	'scripts',
	'vendor',
);

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $plugin_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ( $files as $name => $file ) {
	$file_path     = $file->getRealPath();
	$relative_path = substr( $file_path, strlen( $plugin_dir ) + 1 );

	// Check if the file or its parent directory is in the exclude list.
	$exclude = false;
	foreach ( $exclude_files as $exclude_file ) {
		if ( strpos( $relative_path, $exclude_file ) === 0 ) {
			$exclude = true;
			break;
		}
	}

	if ( ! $exclude ) {
		$dest_path = $build_dir . '/' . $relative_path;
		if ( ! is_dir( dirname( $dest_path ) ) ) {
			mkdir( dirname( $dest_path ), 0755, true ); //phpcs:ignore
		}
		copy( $file_path, $dest_path );
	}
}

// Run composer install without dev dependencies.
chdir( $build_dir );
exec( 'composer install --no-dev --optimize-autoloader --no-interaction --no-scripts' ); //phpcs:ignore

// Create the zip archive.
$zip = new ZipArchive();
if ( $zip->open( $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) {
	exit( "Cannot open <$zip_file>\n" ); //phpcs:ignore
}

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $build_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ( $files as $name => $file ) {
	if ( ! $file->isDir() ) {
		$file_path     = $file->getRealPath();
		$relative_path = substr( $file_path, strlen( $build_dir ) + 1 );

		$zip->addFile( $file_path, $relative_path );
	}
}

$zip->close();

echo "Plugin zipped successfully.\n";
