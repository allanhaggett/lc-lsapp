<?php 
require('inc/lsapp.php');
$idir = LOGGED_IN_IDIR;
$person = getPerson($idir);
$keplerpeople = getKeplerPeople();
$istorepeople = getiStoreDesignees();
?>

<?php getHeader() ?>

<title>Large File Finder</title>

<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">

<div class="col-md-12">
<h2 class="mb-4">Large Files Dashboard</h2>

    <?php
    // Set directory to scan
    $directory = $_SERVER['DOCUMENT_ROOT'] . '/learning//';

    // Default size limit in bytes (10MB)
    $defaultSizeLimit = 10 * 1024 * 1024;

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

    echo "<p>File size threshold: " . htmlspecialchars($sizeThreshold ?? '10MB') . "</p>";
    ?>

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
    $fileSizeBytes = $file['size'];
    $fileSize = $fileSizeBytes >= (1024 * 1024 * 1024) ? 
        round($fileSizeBytes / (1024 * 1024 * 1024), 2) . ' GB' : 
        round($fileSizeBytes / (1024 * 1024), 2) . ' MB';

    echo "<tr><td>{$filePath}</td><td>{$fileSize}</td></tr>";
}
?>

        </tbody>
    </table>

</div>
</div>
</div>


<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>