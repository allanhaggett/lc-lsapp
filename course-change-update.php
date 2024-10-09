<?php require('inc/lsapp.php') ?>
<?php opcache_reset(); ?>

<?php if($_POST): ?>
<?php 

// #TODO check to see if the requester and the person making this update are
// the same person and deny if not.

$f = fopen('data/changes-course.csv','r');
$temp_table = fopen('data/changes-course-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);

$now = date('Y-m-d\TH:i:s');

// 0-creqID,1-CourseID,2-CourseName,3-DateRequested,4-RequestedBy,5-Status,6-CompletedBy,
// 7-CompletedDate,8-Request,9-RequestType,10-AssignedTo,11-Urgency

$updatedchange = Array(
	h($_POST['ChangeID']),
	h($_POST['CourseID']),
	h($_POST['CourseName']),
	$now,
	h($_POST['RequestedBy']),
	'Pending',
	'',
	'',
	h($_POST['ChangeRequest']),
	h($_POST['RequestType']),
	h($_POST['AssignedTo']),
	h($_POST['Priority'])
);

$changeid = $_POST['ChangeID'];

while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $changeid) {
		fputcsv($temp_table,$updatedchange);
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/changes-course-temp.csv','data/changes-course.csv');

header('Location: course-change-view.php?changeid=' . $changeid);?>


<?php else: // Not a POST request so show the form ?>



<?php $changeid = (isset($_GET['changeid'])) ? $_GET['changeid'] : 0 ?>
<?php $change = getCourseChange($changeid) ?>

<?php getHeader() ?>

<title>Update Change Request for <?= h($change[0][2]) ?></title>

<?php getScripts() ?>
<body class="">
<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8 mb-3">

<h1>Update Change Request</h1>
<h2>
	<a href="/lsapp/course.php?courseid=<?= h($change[0][1]) ?>">
		<?= h($change[0][2]) ?>
	</a>
</h2>
<form action="course-change-update.php" method="post">
	<input type="hidden" name="ChangeID" id="ChangeID" value="<?= h($change[0][0]) ?>">
	<input type="hidden" name="CourseName" id="CourseName" value="<?= h($change[0][2]) ?>">
	<input type="hidden" name="CourseID" id="CourseID" value="<?= h($change[0][1]) ?>">
	<input type="hidden" name="DateRequested" id="DateRequested" value="<?= h($change[0][3]) ?>">
	<input type="hidden" name="RequestedBy" id="RequestedBy" value="<?= h($change[0][4]) ?>">
	<input type="hidden" name="Status" id="Status" value="<?= h($change[0][5]) ?>">
	<input type="hidden" name="CompletedBy" id="CompletedBy" value="<?= h($change[0][6]) ?>">
	<input type="hidden" name="CompletedDate" id="CompletedDate" value="<?= h($change[0][7]) ?>">
	<input type="hidden" name="RequestType" id="RequestType" value="<?= h($change[0][9]) ?>">

	<!-- <label>Request Type: 
		<select class="form-select RequestType" name="RequestType">
			<option disabled selected>Select &hellip;</option>
			<option value="Close">Close Course</option> 
			<option value="Update">Simple Content Update</option>
			<option value="Overhaul">Complete Content Overhaul</option>
			<option value="Moodle">Moodle</option>
			<option value="Other">Other</option>
		</select>
	</label>-->
	<label>RequestType: 
		<input type="text" name="RequestType" id="RequestType" value="<?= h($change[0][9]) ?>" disabled>
	</label>
	<div>If you need to edit the type of request, please delete this request and start a new one.</div>
	
	<label>Assign to: 
	<select class="form-select Assigned" name="AssignedTo" id="AssignedTo">
			<option>Unassigned</option>
			<?php getPeople($change[0][10]) ?>
		</select>
	</label>
	<?php 
	$priors = [
				['Backlog','Backlog',],
				['NotUrgent','Not urgent',],
				['ASAP','As Soon As Possible',],
				['HighPriority','High Priority']
			];
	?>
	<label>Priority: 
		<select class="form-select Priority" name="Priority" id="Priority">
			<?php foreach($priors as $p): ?>
				<?php $sel = '' ?>
				<?php if($p[0] == $change[0][11]) $sel = 'selected' ?>
				<option value="<?= $p[0] ?>" <?= $sel ?>><?= $p[1] ?></option>
			<?php endforeach ?>
		</select>
	</label>
	<textarea name="ChangeRequest" id="ChangeRequest" class="form-control" rows="8" required><?= h($change[0][8]) ?></textarea>
	<input type="submit" class="btn btn-sm btn-primary btn-block" value="Update Change Request">
</form>


</div>
</div>
</div>


<?php endif ?>


<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php') ?>