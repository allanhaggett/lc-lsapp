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
</div>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>