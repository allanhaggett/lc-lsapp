<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>PSALS Course Catalog Feed Generator</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-6 col-xl-4">

<h1>Course Catalog Generator</h1>
<p>Synchronize ELM courses that have a "Learning Partner" keyword with LSApp,
    and then generate a new "feed" for the LearningHUB to consume.</p>

<div class="btn-group mb-3">
    <a href="https://learn.bcpublicservice.gov.bc.ca/learning-hub/bcps-corporate-learning-courses.json"
        target="_blank"
        rel="noopener"
        class="btn btn-light">
          View the public JSON feed
    </a>
    <a href="https://learningcentre.gww.gov.bc.ca/learninghub/wp-admin/edit.php?post_type=course&page=systems-sync"
        target="_blank"
        rel="noopener"
        class="btn btn-light">
          LearningHUB Systems Sync
    </a>
  </div>
<?php $m = $_GET['message'] ?? ''; ?>
<?php if($m == 'Success'): ?>
  <div class="alert alert-success">
<h2>Successful Upload</h2>
<p><a href="./" class="btn btn-success">Synchronize Again</a>
    <a href="./process.php" class="btn btn-success">Process Again</a>
</div>
<?php else: ?>
<ol>
<li class="mb-3"> 
<a href="https://learning.gov.bc.ca/psc/CHIPSPLM_3/EMPLOYEE/ELM/q/?ICAction=ICQryNameURL%3DPUBLIC.GBC_LEARNINGHUB_SYNC2"
  target="_blank"
  rel="noopener"
  class="font-weight-bold">
    GBC_LEARNINGHUB_SYNC2
</a> <em>download as a CSV</em>
</li>
<li class="mb-3"> 
<a href="https://learning.gov.bc.ca/psc/CHIPSPLM_3/EMPLOYEE/ELM/q/?ICAction=ICQryNameURL%3DPUBLIC.GBC_ATWORK_CATALOG_KEYWORDS"
  target="_blank"
  rel="noopener"
  class="font-weight-bold">
	  GBC_ATWORK_CATALOG_KEYWORDS
</a>  <em>download as a CSV</em>
</li>
<li class="mb-3">
Upload both of those CSV files here:<br>
<form enctype="multipart/form-data" method="post" 
									accept-charset="utf-8" 
									class="up p-3 mb-3" 
									action="controller.php">
  <div class="bg-light-subtle px-3 pb-0">
	<label>GBC_LEARNINGHUB_SYNC2.csv:<br>
		<input type="file" name="catsfile" class="form-control-file btn btn-lg btn-light bg-light-subtle">
	</label>
</div>
  <div class="bg-light-subtle px-3 pt-0">
	<label>GBC_ATWORK_CATALOG_KEYWORDS.csv:<br>
		<input type="file" name="keysfile" class="form-control-file btn btn-lg btn-light bg-light-subtle">
	</label>
  </div>
	<input type="submit" class="btn btn-primary btn-block mt-1" value="Upload CSV Files">
</form>
</li>
</ol>
<?php endif ?>

</div>
<div class="col-md-3">
<?php
// Path to the persistent sync log
$persistentLogPath = '../data/course-sync-logs/elm_sync_log.txt';

// Check if the persistent log file exists and get the last sync time from the first line
$lastSyncMessage = "No sync history found.";
if (file_exists($persistentLogPath)) {
    $lines = file($persistentLogPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines)) {
        $lastSyncMessage = $lines[0]; // First line contains the last sync date
    }
}

// Define the pattern to match files like course-sync-log-YYYYMMDDHHiiss.log
$pattern = '/^course-sync-log-(\d{14})\.txt$/';

// Scan the ../data directory for files
$files = scandir('../data/course-sync-logs');

// Filter files that match the pattern
$logFiles = array_filter($files, function($file) use ($pattern) {
    return preg_match($pattern, $file);
});

// Sort log files in reverse chronological order by extracting the timestamp
usort($logFiles, function($a, $b) use ($pattern) {
    preg_match($pattern, $a, $matchesA);
    preg_match($pattern, $b, $matchesB);
    return strcmp($matchesB[1], $matchesA[1]); // Sort in descending order
});

// Display the "Last time synced" from the persistent log file
echo "<p><strong>Last time synced:</strong> $lastSyncMessage</p>";

// Display the list of log files if there are any
if (!empty($logFiles)) {
    echo "<h3>Course Sync Logs:</h3><ul class='list-group' id='logFilesList'>";
    $displayedCount = 0;
    $maxInitialDisplay = 12;
    $validLogFiles = [];
    
    // First, filter out empty log files and prepare data for display
    foreach ($logFiles as $logFile) {
        $filePath = "../data/course-sync-logs/$logFile";
        $content = trim(file_get_contents($filePath));
        
        if (!empty($content) && preg_match($pattern, $logFile, $matches)) {
            $dateStr = $matches[1];
            $date = DateTime::createFromFormat('YmdHis', $dateStr);
            $formattedDate = $date->format('F j, Y, g:i:s A');
            
            $validLogFiles[] = [
                'file' => $logFile,
                'path' => $filePath,
                'date' => $formattedDate
            ];
        }
    }
    
    // Display first 12 log files
    foreach ($validLogFiles as $index => $logData) {
        if ($displayedCount < $maxInitialDisplay) {
            echo "<li class='list-group-item log-file-item'>
                    <a href=\"#{$logData['path']}\" data-bs-toggle='modal' data-bs-target='#logModal' onclick='loadLogContent(\"{$logData['path']}\")'>{$logData['date']}</a>
                  </li>";
            $displayedCount++;
        } else {
            break;
        }
    }
    
    echo "</ul>";
    
    // Add Load More button if there are more files to display
    if (count($validLogFiles) > $maxInitialDisplay) {
        echo "<button class='btn btn-primary mt-3' id='loadMoreBtn' onclick='loadMoreLogs()'>Load More</button>";
    }
    
    // Pass all log files data to JavaScript for pagination
    echo "<script>
    var allLogFiles = " . json_encode($validLogFiles) . ";
    var currentIndex = $maxInitialDisplay;
    var itemsPerPage = 12;
    </script>";
} else {
    echo "<p>No course sync log files found.</p>";
}
?>
<!-- Modal Template -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">Log File Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="logContent" class="text-start">Loading...</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
function loadLogContent(logFile) {
    // Fetch the log content and display it in the modal
    fetch(`/lsapp/data/${logFile}`)
        .then(response => response.text())
        .then(content => {
            document.getElementById('logContent').textContent = content;
        })
        .catch(error => {
            document.getElementById('logContent').textContent = "Failed to load log content.";
            console.error("Error loading log content:", error);
        });
}

function loadMoreLogs() {
    var logList = document.getElementById('logFilesList');
    var loadMoreBtn = document.getElementById('loadMoreBtn');
    var endIndex = Math.min(currentIndex + itemsPerPage, allLogFiles.length);
    
    // Add the next batch of log files
    for (var i = currentIndex; i < endIndex; i++) {
        var logData = allLogFiles[i];
        var li = document.createElement('li');
        li.className = 'list-group-item log-file-item';
        li.innerHTML = '<a href="#' + logData.path + '" data-bs-toggle="modal" data-bs-target="#logModal" onclick=\'loadLogContent("' + logData.path + '")\'>' + logData.date + '</a>';
        logList.appendChild(li);
    }
    
    currentIndex = endIndex;
    
    // Hide the Load More button if all files are displayed
    if (currentIndex >= allLogFiles.length) {
        loadMoreBtn.style.display = 'none';
    }
}
</script>

</div>
</div>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>