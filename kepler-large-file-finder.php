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

    // Default size limit in bytes (10MB)
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
            <option value="100MB" <?= $sizeThreshold === '100MB' ? 'selected' : '' ?>>100MB</option>
            <option value="200MB" <?= $sizeThreshold === '200MB' ? 'selected' : '' ?>>200MB</option>
            <option value="300MB" <?= $sizeThreshold === '300MB' ? 'selected' : '' ?>>300MB</option>
            <option value="400MB" <?= $sizeThreshold === '400MB' ? 'selected' : '' ?>>400MB</option>
            <option value="500MB" <?= $sizeThreshold === '500MB' ? 'selected' : '' ?>>500MB</option>
            <option value="600MB" <?= $sizeThreshold === '600MB' ? 'selected' : '' ?>>600MB</option>
            <option value="700MB" <?= $sizeThreshold === '700MB' ? 'selected' : '' ?>>700MB</option>
            <option value="800MB" <?= $sizeThreshold === '800MB' ? 'selected' : '' ?>>800MB</option>
            <option value="900MB" <?= $sizeThreshold === '900MB' ? 'selected' : '' ?>>900MB</option>
            <option value="1GB" <?= $sizeThreshold === '1GB' ? 'selected' : '' ?>>1GB</option>
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

// Function to scan directory recursively and find large files
function findLargeFiles($dir, $sizeLimit, &$files) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getSize() > $sizeLimit) {
            $files[] = [
                'path' => $file->getPathname(),
                'size' => $file->getSize()
            ];
        }
    }
}

// Run the function and store results
findLargeFiles($directory, $sizeLimit, $files);

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
    $fileSize = $fileSizeBytes >= (1024 * 1024 * 1024) ? 
        round($fileSizeBytes / (1024 * 1024 * 1024), 2) . ' GB' : 
        round($fileSizeBytes / (1024 * 1024), 2) . ' MB';

    echo "<tr><td><a href='{$fileUrl}' target='_blank'>{$fileUrl}</a></td><td>{$fileSize}</td></tr>";
}
?>

        </tbody>
    </table>

</div>
</div>
</div>


<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>