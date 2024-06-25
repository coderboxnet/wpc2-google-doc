<?php

$pluginDir = __DIR__;
$buildDir = $pluginDir . '/build';
$zipFile = __DIR__ . '/build-zip/wpc2-google-doc.zip';

// Function to delete a directory and its contents recursively
function deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        return;
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileInfo) {
        $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileInfo->getRealPath());
    }
    @rmdir($dirPath);
}

// Remove and recreate the build directory
deleteDir($buildDir);
deleteDir(__DIR__ . '/build-zip/');
mkdir($pluginDir . '/build/'); 
mkdir($pluginDir . '/build-zip/'); 

// Copy all files except the ones to exclude
$excludeFiles = [
    '.git',
    '.vscode',
    '.gitignore',
    '.editorconfig',
    'wpc2-google-doc-zip.php',
    'build',
    'build-zip',
];

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($pluginDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($pluginDir) + 1);

    // Check if the file or its parent directory is in the exclude list
    $exclude = false;
    foreach ($excludeFiles as $excludeFile) {
        if (strpos($relativePath, $excludeFile) === 0) {
            $exclude = true;
            break;
        }
    }

    if (!$exclude) {
        $destPath = $buildDir . '/' . $relativePath;
        if (!is_dir(dirname($destPath))) {
            mkdir(dirname($destPath), 0755, true);
        }
        copy($filePath, $destPath);
    }
}

// Run composer install without dev dependencies
chdir($buildDir);
exec('composer install --no-dev --optimize-autoloader --no-interaction --no-scripts');

// Create the zip archive
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Cannot open <$zipFile>\n");
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($buildDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($buildDir) + 1);

        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

// Clean up the build directory
//deleteDir($buildDir);

echo "Plugin zipped successfully.\n";
?>
