<?php

require('inc/lsapp.php');


// Get real path for our folder
$rootPath = realpath('data');

// Create ZIP file in the data folder with a unique name
$zipFile = $rootPath . '/LSApp-All-Data-' . time() . '.zip';

// Initialize archive object
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Cannot open ZIP file for writing");
}

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();

// Set headers for file download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="LSApp-All-Data.zip"');
header('Content-Length: ' . filesize($zipFile));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output the file
readfile($zipFile);

// Clean up temporary file
unlink($zipFile);