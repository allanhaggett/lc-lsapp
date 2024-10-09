<?php 
require('inc/lsapp.php');
if(canACcess()):

require('inc/Parsedown.php');
$Parsedown = new Parsedown();

$changeid = (isset($_GET['changeid'])) ? $_GET['changeid'] : 0;

$chg = getCourseChange($changeid);
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
	<?php 
	if($chg[0][5] == 'Pending'):
		$statbadge = 'primary';
	elseif($chg[0][5] == 'Completed'):
		$statbadge = 'success';
	endif; 
	?>
	<span class="badge text-bg-<?= $statbadge ?>"><?= h($chg[0][5]) ?></span>
	<h1>
		Course Change Request
	</h1>
</div>
<div class="row">
<div class="col-md-8 col-lg-6">
	
<h2 class="mb-2">
	<a href="course.php?courseid=<?= h($chg[0][1]) ?>">
		<?= h($chg[0][2]) ?>
	</a>
</h2>

<div class="mb-2">
	<span>Priority 
	<?php 
	if($chg[0][11] == 'Backlog'):
		$urgencybadge = 'dark';
	elseif($chg[0][11] == 'NotUrgent'):
		$urgencybadge = 'primary';
	elseif($chg[0][11] == 'ASAP'):
		$urgencybadge = 'warning';
	elseif($chg[0][11] == 'HighPriority'):
		$urgencybadge = 'danger';
	endif; 
	?>
	<span class="badge text-bg-<?= $urgencybadge ?>"><?= h($chg[0][11]) ?></span>
	<!-- <span>Status <span class="badge badge-secondary"><?= h($chg[0][5]) ?> </span></span> -->
	<span>Change type <span class="badge text-bg-secondary"><?= h($chg[0][9]) ?></span></span>
	Assigned to <span class="badge text-bg-secondary"><?= h($chg[0][10]) ?></span>
	<a href="/lsapp/course-change-claim.php?changeid=<?= h($chg[0][0]) ?>&courseid=<?= h($chg[0][1]) ?>" class="btn btn-sm btn-light">Claim</a>
</div>

<div class="mb-2">
	On <?= h($chg[0][3]) ?>, 
	<a href="/lsapp/person.php?idir=<?= h($chg[0][4]) ?>"><?= h($chg[0][4]) ?></a>
	requested:
</div>
<blockquote class="blockquote mb-4 p-3 bg-light-subtle rounded-3">
	<?= $Parsedown->text(h($chg[0][8]))  ?>
</blockquote>

<?php if($chg[0][9] == 'Close'): ?>
	<hr>
<div class="mt-3">
	<h4>How to close a course</h4>
	<ol>
		<li>Check that course steward has communicated with enrolled learners.
		<li>Make sure all classes are processed and closed (under all delivery methods).
		<li>Edit the course to set to "Closed".
		<li>Decommission/archive Alchemer surveys.
		<li>Update LSApp course list to "Closed".
		<li>Sign off on course change request.
	</ol>
</div>
<hr>
<?php endif ?>
<?php if($chg[0][9] == 'Moodle'): ?>
<div class="mt-3">
	<h4>Moodle Change</h4>
	<ol>
		<li>We'll have instructions specific to this case ASAP.
	</ol>
</div>
<?php endif ?>

<?php if($chg[0][5] != 'Completed'): ?>
<div>
	<a class="btn btn-sm btn-secondary" href="/lsapp/course-change-update.php?changeid=<?= h($chg[0][0]) ?>">Edit</a>
	<a href="/lsapp/course-change-process.php?changeid=<?= h($chg[0][0]) ?>&courseid=<?= h($chg[0][1]) ?>" class="btn btn-sm btn-success">
		Mark Complete
	</a>
</div>
<?php endif ?>
</div>
<div class="col-md-4 col-lg-6">
<?php $comments = getCourseChangeComments($changeid) ?>
<h3>Comments</h3>
<details class="mb-2 p-1">
	<summary>Add Comment</summary>
	<div class="p-3 my-3 bg-light-subtle rounded-3">
	<form action="course-change-comment-create.php" method="post">
		<input type="hidden" name="CourseName" id="CourseName" value="<?= h($chg[0][2]) ?>">
		<input type="hidden" name="CourseID" id="CourseID" value="<?= h($chg[0][1]) ?>">
		<input type="hidden" name="creqID" id="creqID" value="<?= h($chg[0][0]) ?>">
		<textarea name="Comment" id="Comment" class="form-control" rows="8" required></textarea>
		<input type="submit" class="btn btn-sm btn-primary btn-block" value="Add Comment">
	</form>
	</div>
</details>
<?php 
//commentID,creqID,CourseID,CourseName,created,Comment,Commenter
//Array ( [0] => Array ( [0] => ahaggett-20240120-232712 
						// [1] => shamitch-20240108-161923 
						// [2] => 20231023144725-96 
						// [3] => Writing for the web: Plain Language training 
						// [4] => 2024-01-21T12:34:33
						// [5] => Yeah sure! no problem. 
						// [6] => ahaggett ) )
?>
<?php foreach($comments as $c): ?>
<div class="mb-2 p-3 bg-light-subtle rounded-3">
	<?php if($c[6] == $loggedinuser): ?>
	<form method="post" action="course-change-comment-delete.php" class="float-end">
	<input type="hidden" name="commentid" value="<?= $c[0] ?>">
	<input type="hidden" name="reqid" value="<?= $c[1] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-light del">
	</form>
	<?php endif ?>
	<div>
		On <?= h($c[4]) ?>, 
		<a href="/lsapp/person.php?idir=<?= h($c[6]) ?>"><?= h($c[6]) ?></a>
		said:</div>
	<blockquote class="blockquote">
		<?= $Parsedown->text(h($c[5]))  ?>
	</blockquote>
	
</div>
<?php endforeach ?>
<?php endif ?>
</div>
</div>
</div>

<?php include('templates/footer.php') ?>