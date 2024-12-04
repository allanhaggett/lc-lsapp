<?php 
require('inc/lsapp.php');
$idir = LOGGED_IN_IDIR;
$person = getPerson($idir);
$keplerpeople = getKeplerPeople();
$istorepeople = getiStoreDesignees();
?>

<?php getHeader() ?>

<title>Kepler Large File Finder</title>

<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">

<div class="col-md-12">

    <h1 class="mb-4">Large File Finder</h1>
    <p>A scan of Kepler's "/learning/" folder that houses the majority of the LC's 
        documents and courses, showing any files that are greater than the set file size threshold.</p>
    <div class="alert alert-warning">NOTE: This page is server intensive and can 
        upwards of 30 seconds to load. Please don't share the link widely or in 
        meetings where multiple people will attempt to load it at the same time.</div>
        <?php
// Set directory to scan - ensure it ends with a trailing slash
$directory = 'E:/WebSites/Prod/BCPSA/Intranet/wwwroot/learning' . DIRECTORY_SEPARATOR; // Full directory path you want to scan
$baseUrl = 'https://gww.bcpublicservice.gov.bc.ca/learning/'; // Base URL to replace directory path

// Default size limit in bytes (100MB)
$defaultSizeLimit = 100 * 1024 * 1024;

// Function to check if a string ends with a given substring (for PHP < 8)
function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

// Retrieve size threshold from _GET parameter and convert to bytes
if (isset($_GET['sizethreshold'])) {
    $sizeThreshold = strtoupper($_GET['sizethreshold']);
    if (endsWith($sizeThreshold, 'GB')) {
        $sizeLimit = (float) $sizeThreshold * 1024 * 1024 * 1024;
    } elseif (endsWith($sizeThreshold, 'MB')) {
        $sizeLimit = (float) $sizeThreshold * 1024 * 1024;
    } elseif (endsWith($sizeThreshold, 'KB')) {
        $sizeLimit = (float) $sizeThreshold * 1024;
    } else {
        $sizeLimit = $defaultSizeLimit; // Use default if format is unrecognized
    }
} else {
    $sizeLimit = $defaultSizeLimit;
}
?>

<form method="GET" class="mb-4">
    <label for="sizethreshold" class="form-label">File size threshold:</label>
    <select name="sizethreshold" id="sizethreshold" class="form-select w-auto d-inline">
        <option value="100MB" <?= isset($sizeThreshold) && $sizeThreshold === '100MB' ? 'selected' : '' ?>>100MB</option>
        <option value="200MB" <?= isset($sizeThreshold) && $sizeThreshold === '200MB' ? 'selected' : '' ?>>200MB</option>
        <option value="300MB" <?= isset($sizeThreshold) && $sizeThreshold === '300MB' ? 'selected' : '' ?>>300MB</option>
        <option value="400MB" <?= isset($sizeThreshold) && $sizeThreshold === '400MB' ? 'selected' : '' ?>>400MB</option>
        <option value="500MB" <?= isset($sizeThreshold) && $sizeThreshold === '500MB' ? 'selected' : '' ?>>500MB</option>
        <option value="600MB" <?= isset($sizeThreshold) && $sizeThreshold === '600MB' ? 'selected' : '' ?>>600MB</option>
        <option value="700MB" <?= isset($sizeThreshold) && $sizeThreshold === '700MB' ? 'selected' : '' ?>>700MB</option>
        <option value="800MB" <?= isset($sizeThreshold) && $sizeThreshold === '800MB' ? 'selected' : '' ?>>800MB</option>
        <option value="900MB" <?= isset($sizeThreshold) && $sizeThreshold === '900MB' ? 'selected' : '' ?>>900MB</option>
        <option value="1GB" <?= isset($sizeThreshold) && $sizeThreshold === '1GB' ? 'selected' : '' ?>>1GB</option>
    </select>
    <button type="submit" class="btn btn-primary">Set Threshold</button>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>File Path</th>
            <th width="200">File Size</th>
        </tr>
    </thead>
    <tbody>

<?php
$files = [];
$totalSize = 0; // Initialize total size variable

// Function to scan directory recursively and find large files
function findLargeFiles($dir, $sizeLimit, &$files, &$totalSize) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $fileSize = $file->getSize();
            $totalSize += $fileSize; // Accumulate total size

            if ($fileSize > $sizeLimit) {
                $files[] = [
                    'path' => $file->getPathname(),
                    'size' => $fileSize
                ];
            }
        }
    }
}

// Function to format file sizes
function formatSize($size) {
    if ($size >= 1024 * 1024 * 1024) {
        return round($size / (1024 * 1024 * 1024), 2) . ' GB';
    } elseif ($size >= 1024 * 1024) {
        return round($size / (1024 * 1024), 2) . ' MB';
    } elseif ($size >= 1024) {
        return round($size / 1024, 2) . ' KB';
    } else {
        return $size . ' bytes';
    }
}

// Run the function and store results
findLargeFiles($directory, $sizeLimit, $files, $totalSize);

// Sort files by size, largest first
usort($files, function($a, $b) {
    return $b['size'] - $a['size'];
});

// Display sorted results
foreach ($files as $file) {
    $filePath = $file['path'];
    $relativePath = str_replace($directory, '', $filePath); // Remove the directory part
    $fileUrl = $baseUrl . ltrim(str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), '/'); // Construct the URL
    $fileSizeBytes = $file['size'];
    $fileSize = formatSize($fileSizeBytes);

    echo "<tr><td><a href='{$fileUrl}' target='_blank'>{$fileUrl}</a></td><td>{$fileSize}</td></tr>";
}
?>

    </tbody>
</table>

<p><strong>Total size of the directory scanned:</strong> <?php echo formatSize($totalSize); ?></p>

</div>
</div>
</div>


<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>