<?php 
require('inc/lsapp.php');
$courserequests = getRequestedCourses();
$classrequests = getRequestedClasses();
$changes = getChanges();
$courses = count($courserequests);
$classes = count($classrequests);
$changes =  count($changes);
$totalrequests = $courses + $classes + $changes;
$coursep = ceil(($courses / $totalrequests) * 100);
$classp = ceil(($classes / $totalrequests) * 100);
$changep = ceil(($changes / $totalrequests) * 100);
?>
<link href="/learning/bootstrap-theme/dist/css/bootstrap-theme.min.css" rel="stylesheet">
<h2>Current LSA Status</h2>
<h3><span class="badge badge-dark"><?= $totalrequests ?></span> total requests </h3>

<div class="progress" style="height: 50px">
	<div class="progress-bar" role="progressbar" style="width: <?= $coursep ?>%" aria-valuenow="<?= $coursep ?>" aria-valuemin="0" aria-valuemax="100">
		<?= $courses ?> course requests 
	</div>
	<div class="progress-bar bg-success" role="progressbar" style="width: <?= $classp ?>%" aria-valuenow="<?= $classp ?>" aria-valuemin="0" aria-valuemax="100">
		<?= $classes ?> class requests 
	</div>
	<div class="progress-bar bg-info" role="progressbar" style="width: <?= $changep ?>%" aria-valuenow="<?= $changep ?>" aria-valuemin="0" aria-valuemax="100">
		<?= $changes ?> change requests 
	</div>
</div>