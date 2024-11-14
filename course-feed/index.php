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

<div>PSA Learning System </div>
<h1>Course Catalog Generator</h1>

<div class="btn-group mb-3">
    <a href="https://learn.bcpublicservice.gov.bc.ca/learning-hub/learning-partner-courses.json"
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
<h3>Screencast of Process:</h3>
<video src="lsapp-sync-elm-with-learninghub.mp4" width="100%" height="320" controls="true"></video>
</div>
<div class="col-md-6">
<?php
  // Define the pattern to match files like course-sync-log-YYYYMMDDHHiiss.log
  $pattern = '/^course-sync-log-(\d{14})\.log$/';

  // Scan the ../data directory for files
  $files = scandir('../data');

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

  // Display the "Last time synced" if there are any log files
  if (!empty($logFiles)) {
      // Extract the timestamp from the most recent log file
      if (preg_match($pattern, $logFiles[0], $matches)) {
          $dateStr = $matches[1];
          $lastSyncDate = DateTime::createFromFormat('YmdHis', $dateStr);
          $formattedLastSyncDate = $lastSyncDate->format('F j, Y, g:i:s A');
          echo "<p><strong>Last time synced:</strong> $formattedLastSyncDate</p>";
      }

      echo "<h3>Course Sync Logs:</h3><ul class='list-group'>";
      foreach ($logFiles as $logFile) {
          // Check if the file has substantive content (more than just whitespace or newline)
          $filePath = "../data/$logFile";
          $content = trim(file_get_contents($filePath)); // Trim whitespace and newlines

          if (!empty($content)) { // Only proceed if there's actual content
              // Extract the date portion (YYYYMMDDHHiiss) from the filename
              if (preg_match($pattern, $logFile, $matches)) {
                  $dateStr = $matches[1]; // Extracted timestamp

                  // Create a DateTime object from the extracted timestamp
                  $date = DateTime::createFromFormat('YmdHis', $dateStr);

                  // Format the date nicely
                  $formattedDate = $date->format('F j, Y, g:i:s A');

                  // Display the link with the formatted date, triggering the modal
                  echo "<li class='list-group-item'>
                          <a href='#' data-bs-toggle='modal' data-bs-target='#logModal' onclick='loadLogContent(\"$logFile\")'>$formattedDate</a>
                        </li>";
              }
          }
      }
      echo "</ul>";
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
</script>

</div>
</div>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>