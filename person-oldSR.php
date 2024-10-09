<?php require('inc/lsapp.php') ?>

<?php $idir = (isset($_GET['idir'])) ? $_GET['idir'] : 0; ?>
<?php $person = getPerson($idir) ?>
<?php $requests = getUserRequested($idir) ?>
<?php $assignments = getAdminAssigned($idir) ?>
<?php $changes = getUserChanges($idir) ?>
<?php $facilitating = getUserFacilitating($idir) ?>
<?php $owned = getCoursesOwned($idir) ?>

<?php getHeader() ?>
<title><?= $person[2] ?> | LSApp</title>
<?php getScripts() ?>
<?php getNavigation() ?>

<div class="container">
<div class="row mb-3">

<?php if(sizeof($person)>0): ?>
<div class="col-md-6">
<div class="float-right">
	<?php if(isSuper()): ?>
	<form method="post" action="person-delete.php">
	<input type="hidden" name="idir" value="<?= $person[0] ?>">
	<div class="btn-group">
	<a href="person-update.php?idir=<?= $person[0] ?>" class="btn btn-primary float-right">Edit Person</a>
	<input type="submit" value="Delete" class="btn btn-sm btn-danger del">
	</div>
	</form>
	<?php endif ?>
</div>
<!--IDIR,Role,Name,Email,Status,Phone,Title-->
<?php if($person[4] == 'Active'): ?>
<span class="badge badge-success"><?= $person[4] ?></span>
<?php else: ?>
<span class="badge badge-warning"><?= $person[4] ?></span>
<?php endif ?>
<h1><?= $person[2] ?></h1>
<?php if(isset($person[6])): ?>
<h2><?= $person[6] ?></h2>
<?php endif ?>
<div class="btn-group">
<a href="mailto:<?= $person[3] ?>" class="btn btn-light"><?= $person[3] ?></a>
<?php if(isset($person[5])): ?>
<a href="tel:<?= $person[5] ?>" class="btn btn-light"><?= $person[5] ?></a>
<?php endif ?>
</div>
<p>All outstanding requests for this person in the system, 
along with any classes assigned as facilitator, or courses assigned as owner.</p>

</div>


</div>
<div class="row">
<div class="col-md-5">
<?php if(sizeof($owned)>0): ?>


<h2>Courses Owned</h2>
<p>Assigned as the owner of these courses.</p>
<ul class="list-group mb-3">
<?php foreach($owned as $course): ?>
<li class="list-group-item">
	<?php if($course[1] == 'Active'): ?>
	<a href="class-request.php?courseid=<?= $course[0] ?>" class="float-right btn btn-light ml-3">New Date</a>
	<?php endif ?>
	<a href="course.php?courseid=<?= $course[0] ?>"><?= $course[2] ?></a>
	<?php if($course[1] == 'Inactive'): ?>
	<div><span class="badge badge-dark">Inactive</span></div>
	<?php endif ?>
</li>
<?php endforeach ?>
</ul>
<?php endif ?>

<?php $mentions = getMentions($idir) ?>
<?php if(sizeof($mentions)>0): ?>
<h3>Mentioned</h3>
<p>This person was mentioned in a pending change request in one of these upcoming classes.</p>
<ul class="list-group mb-3">
<?php foreach($mentions as $mention): ?>
<li class="list-group-item"><a href="class.php?classid=<?= $mention[1] ?>"><?= $mention[2] ?> <?= $mention[3] ?> in <?= $mention[4] ?></a></li>
<?php endforeach ?>
</ul>
<?php endif ?>
</div>

<?php if(sizeof($facilitating)>0): ?>
<div class="col-md-7">

<h2>Facilitating</h2>
<p>Assigned as the facilitator for these upcoming classes.</p>
<table class="table table-sm table-striped">
<tr>
	<th>Class Date</th>
	<th>Course</th>
	<th>City</th>
	<th>Status</th>
	<th>Enrolled</th>
</tr>
<?php $today = date('Y-m-d') ?>
<?php foreach($facilitating as $at): ?>
<?php if($at[8] > $today): ?>
<?php if($at[1] != 'Inactive'): ?>
<tr>
	<td width="120">
		<a href="/lsapp/class.php?classid=<?= h($at[0]) ?>">
			<?php echo goodDateShort($at[8],$at[9]) ?> 
		</a>
	</td>
	<td>
		<a href="course.php?courseid=<?= h($at[5]) ?>"><?= h($at[6]) ?></a>
	</td>
	<td>
		<a href="city.php?name=<?php echo urlencode($at[25]) ?>"><?= h($at[25]) ?></a>
	</td>	
	<td>
		<span class="badge badge-light"><?= h($at[1]) ?></span>
	</td>
	<td class="text-center">
		<span class="badge badge-secondary"><?= h($at[18]) ?></span>
	</td>
</tr>
<?php endif ?>
<?php endif ?>
<?php endforeach ?>
</table>

</div>
<?php endif ?>



<?php if(sizeof($requests)>0): ?>

<div class="col-md-6">


<h2>Classes Requested</h2>
<p>Classes requested for a course, but not yet entered in the Learning System</p>
<table class="table table-sm table-striped">
<?php foreach($requests as $rq): ?>
<tr>
	
	<td width="120">
		<a href="/lsapp/class.php?classid=<?= h($rq[0]) ?>">
			<?php echo goodDateShort($rq[8],$rq[9]) ?> 
		</a>
	</td>
	<td>
		<a href="course.php?courseid=<?= h($rq[5]) ?>"><?= h($rq[6]) ?></a>
	</td>
	<td>
		<a href="city.php?name=<?= h($rq[25]) ?>"><?= h($rq[25]) ?></a>
	</td>
</tr>
<?php endforeach ?>
</table>

</div>
<?php endif ?>

<?php if(sizeof($changes)>0): ?>
<div class="col-md-6">

<h2>Class Change Requests</h2>
<p>Change requests for classes that have not been addressed yet.</p>
<!-- //creqID,ClassID,CourseName,StartDate,City,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request -->
<table class="table table-sm table-striped">
<?php foreach($changes as $change): ?>
<div>
	
	<div>
		<a href="/lsapp/class.php?classid=<?= h($change[1]) ?>">
			<?php echo goodDateShort(h($change[3])) ?>
		<?= h($change[2]) ?>
		<?= h($change[4]) ?></a>
		<div class="alert alert-secondary">
		<?= h($change[10]) ?>
		</div>
	</div>
</div>
<?php endforeach ?>
</table>

</div>
<?php endif ?>


<?php if(sizeof($assignments) > 0): ?>
<div class="col-md-12">


<h2 class="card-title">Assigned</h2>
<p>Classes assigned and <em>not yet active.</em></p>
<table class="table table-sm table-striped">
<tr>
	<th>ELM Stuff</th>
	<th>Class Date</th>
	<th>Course Name</th>
	<th>City</th>
	<th width="240">Notes</th>
	<th width="200">Process</th>
</tr>
<?php $ccount = 0 ?>
<?php foreach($assignments as $ass): ?>
<?php $ccount++ ?>
<?php if($ass[1] == 'Requested'): ?>
<?php $coursedeets = getCourse($ass[5]) ?>
<tr>
	<td>	
<div><a href="<?= $coursedeets[7] ?>" target="_blank" rel="noopener">ELM Delivery Method</a></div>
<div id="elmdate<?= $ccount ?>"><?php echo trim(elmStartDate($ass[8])) ?></div>
<button class="copy btn btn-sm" data-clipboard-target="#elmdate<?= $ccount ?>">Copy</button>
			
		
	</td>	
	<td>
		<a href="/lsapp/class.php?classid=<?= h($ass[0]) ?>">
			<?php echo goodDateShort($ass[8],$ass[9]) ?> 
		</a>
	</td>
	<td>
		<a href="course.php?courseid=<?= h($ass[5]) ?>"><?= h($ass[6]) ?></a>
		<?php 
		//0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,10-CourseOwner,
		//11-MinMax,12-CourseNotes,13-Requested,14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,
		//19-Keywords,20-Categories,21-Method,22-WeShip
		?>
		
		<?php if($coursedeets[8]): ?>
		<br><a href="<?= $coursedeets[8] ?>">Pre-Work</a>
		<?php endif ?>
		<?php if($coursedeets[9]): ?>
		<br><a href="<?= $coursedeets[9] ?>">Post-Work</a>
		<?php endif ?>
		
	</td>
	<td>
		<?= h($ass[25]) ?>
	</td>	

	<td>
		<?= h($ass[32]) ?>
	</td>	
	<td width="260">
	<form method="get" action="class-process.php" class="form-inline">
		<input type="hidden" name="classid" id="classid" value="<?= h($ass[0]) ?>">
		<input type="text" class="form-control w-50" name="itemcode" id="itemcode" placeholder="ITEM code">
		<input type="submit" class="btn btn-success" value="Process">
	</form>
	</td>
</tr>
<?php endif ?>
<?php endforeach ?>
</table>

</div>
<?php endif ?>
</div>
<?php if(isSuper()): ?>
<!--
		<form method="post" action="people-controller.php" class="persondel">
			<input type="hidden" name="idir" id="idir" value="<?= $idir ?>">
			<input type="hidden" name="action" id="action" value="delete">
			<input type="submit" class="btn btn-danger btn-sm" value="Delete User">
		</form>
		-->
<?php endif ?>
</div>

<?php else: ?>
<div class="col-md-6">
	<h2>Person Not Found</h2>
	<p>Must be playin' hooky ;)</p>
	<p>Email the Learning Support Admin Team <<a href="mailto:learning.centre.admin@gov.bc.ca">learning.centre.admin@gov.bc.ca</a>> with any questions or concerns.</p>
	<p><img src="img/TrollFace.jpg" width="300px"></p>
</div>	
<?php endif ?>

</div>


<?php require('templates/javascript.php') ?>
<script src="js/clipboard.min.js"></script>
<script>
$(document).ready(function(){
	var clipboard = new Clipboard('.copy');
});
</script>

<?php require('templates/footer.php') ?>