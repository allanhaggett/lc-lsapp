<?php 
require('inc/lsapp.php');
if(canACcess()):

require('inc/Parsedown.php');
$Parsedown = new Parsedown();

$changeid = (isset($_GET['changeid'])) ? $_GET['changeid'] : 0;

$chgs = getCourseChangesAll();
// echo '<pre>';print_r($chgs); exit;
// 0-creqID,1-CourseID,2-CourseName,3-DateRequested,4-RequestedBy,5-Status,6-CompletedBy,
// 7-CompletedDate,8-Request,9-RequestType,10-AssignedTo,11-Urgency

?>
<?php getHeader() ?>

<title><?= $deets[2] ?></title>

<?php getScripts() ?>

<body>

<?php getNavigation() ?>

<div class="container mb-5">
<div class="row">
<div class="col-12">
<h1 class="mb-4">Uncompleted Course Change Requests</h1>
<table class="table table-striped">
	<tr>
		<th>Status</th>
		<th>Priority</th>
		<th>Date</th>
		<th>Who</th>
		<th>Change</th>
		<th>Course Name</th>
		<th>Change type</th>
		<th>Assigned to</th>
	</tr>
<?php foreach($chgs as $chg): ?>
<tr>
<td>
	<?php 
	if($chg[5] == 'Pending'):
		$statbadge = 'primary';
	elseif($chg[5] == 'Completed'):
		$statbadge = 'success';
	endif; 
	?>
	<span class="badge text-bg-<?= $statbadge ?>"><?= h($chg[5]) ?></span>
</td>
<td>

	<?php 
	if($chg[11] == 'Backlog'):
		$urgencybadge = 'dark';
	elseif($chg[11] == 'NotUrgent'):
		$urgencybadge = 'warning';
	elseif($chg[11] == 'ASAP'):
		$urgencybadge = 'warning';
	elseif($chg[11] == 'HighPriority'):
		$urgencybadge = 'danger';
	endif; 
	?>
	<span class="badge text-bg-<?= $urgencybadge ?>"><?= h($chg[11]) ?></span>

</td>
<td>
	<?= h($chg[3]) ?>
</td>
<td>
	<a href="/lsapp/person.php?idir=<?= h($chg[4]) ?>"><?= h($chg[4]) ?></a>
</td>

<td>

	<a href="course-change-view.php?changeid=<?= h($chg[0]) ?>">
		<?= substr($chg[8],0,75) ?>&hellip;
	</a>
</td>
<td>

	<a href="course.php?courseid=<?= h($chg[1]) ?>">
		<?= h($chg[2]) ?>
	</a>
</td>

<td>

	<span> <span class="badge text-bg-secondary"><?= h($chg[9]) ?></span></span>

</td>
<td>

	 <span class="badge text-bg-secondary"><?= h($chg[10]) ?></span>
	<!-- <a href="/lsapp/course-change-claim.php?changeid=<?= h($chg[0]) ?>&courseid=<?= h($chg[1]) ?>" class="btn btn-sm btn-light">Claim</a> -->

</td>

</tr>

<?php endforeach ?>
</table>
<?php endif ?>
</div>
</div>
</div>

<?php include('templates/javascript.php') ?>
<?php include('templates/footer.php') ?>