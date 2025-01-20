<?php 
opcache_reset();
require('inc/lsapp.php');
require('inc/Parsedown.php');
$Parsedown = new Parsedown();
?>
<?php //opcache_reset() ?>
<?php if(canACcess()): ?>
<?php 
$courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0;

$topics = getAllTopics();
$audience = getAllAudiences ();
$deliverymethods = getDeliveryMethods();
$levels = getLevels ();
$reportinglist = getReportingList();
$deets = getCourse($courseid);
$audits = getCourseAudits($courseid);

$stewsdevs = getCoursePeople($courseid);

// echo '<pre>'; print_r($stewsdevs); exit;

// 0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,
// 10-CourseOwner,11-MinMax,12-CourseNotes,
// 13-Requested, 14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,19-Keywords,
// 20-Category,21-Method,22-elearning
?>
<?php getHeader() ?>

<title><?= $deets[2] ?></title>
<!-- <link href="/lsapp/css/summernote-bs4.css" rel="stylesheet"> -->
<style>
.abstract {
	height: 100px;
	overflow-y: scroll;
}
</style>
<?php getScripts() ?>

<body>
<?php getNavigation() ?>

<div class="container mb-5">
<div class="row">
<div class="col-md-12 col-lg-8">
<!--<div class="text-uppercase">LC Ship? <?= $deets[23] ?></div>-->
<div class="row mb-3 py-2 bg-light-subtle border border-secondary-subtle rounded-3">
	<div class="col-6 col-md-3"><strong>Status:</strong><br><?= $deets[1] ?></div>
	<div class="col-6 col-md-3"><strong>Short name:</strong><br> <?= $deets[3] ?></div>
	<div class="col-6 col-md-3">
		<strong>ELM Code:</strong><br> 
		<?= $deets[4] ?>
		<span style="font-size:10px">
		(<a target="_blank" href="https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_COURSESTRUCTURE.LM_CI_LA_CMP.GBL?LM_CI_ID=<?= h($deets[50]) ?>"><?= $deets[50] ?></a>)</span>
	</div>
	<div class="col-6 col-md-3"><strong>Delivery method:</strong><br> <?= $deets[21] ?></div>
</div>
<?php if(isAdmin()): ?>
	<div class="float-right">
		<a href="course-update.php?courseid=<?= $courseid ?>" class="btn btn-light float-end">Edit course</a>
	</div>
	<?php endif ?>
<h1><?= $deets[2] ?></h1>
<div class="col-12">DESCRIPTION</div>
<div class=""><?= $deets[16] ?></div>
<details class="p-2">
	<summary>Full Abstract</summary>
	<div class="p-3 bg-light-subtle rounded-3">
	
	<?= $Parsedown->text($deets[17])  ?>
	</div>
</details>
</div>
</div>
<div class="row justify-content-md-center my-3">
<div class="col-md-6">

<div class="row mb-3 py-2 bg-light-subtle border border-secondary-subtle rounded-3">
	<div class="col-12">TAXONOMIES</div>
	<div class="mb-2 col-md-6"><strong>Topic:</strong><br> <a href="/lsapp/courses.php?topic=<?= urlencode($deets[38]) ?>"><?= $deets[38] ?></a></div>
	<div class="mb-2 col-md-6"><strong>Audience:</strong><br> <a href="/lsapp/courses.php?audience=<?= urlencode($deets[39]) ?>"><?= $deets[39] ?></a></div>
	<div class="col-md-6"><strong>Group:</strong><br> <a href="/lsapp/courses.php?level=<?= urlencode($deets[40]) ?>"><?= $deets[40] ?></a></div>
	<div class="col-md-6"><strong>Reporting:</strong><br> <a href="/lsapp/courses.php?reporting=<?= urlencode($deets[41]) ?>"><?= $deets[41] ?></a></div>

<div class="col-12">
<details class="mt-2">
	<summary>Taxonomy Quick Update</summary>
	<form method="post" action="/lsapp/course-new-tax-up.php" class="mb-3 pb-3">
	<input type="hidden" name="CourseID" value="<?= h($deets[0]) ?>">
	
	<label for="Topics">Topic</label><br>
	<select name="Topics" id="Topics" class="form-select">
	<?php foreach($topics as $t): ?>
	<?php if($deets[38] == $t): ?>
	<option selected><?= $t ?></option>
	<?php else: ?>
	<option><?= $t ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	
	<label for="Audience">Audience</label><br>
	<select name="Audience" id="Audience" class="form-select">
	<?php foreach($audience as $a): ?>
	<?php if($deets[39] == $a): ?>
	<option selected><?= $a ?></option>
	<?php else: ?>
	<option><?= $a ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	<label for="Levels">Group</label><br>
	<select name="Levels" id="Levels" class="form-select">
	<?php foreach($levels as $l): ?>
	<?php if($deets[40] == $l): ?>
	<option selected><?= $l ?></option>
	<?php else: ?>
	<option><?= $l ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	<label for="Reporting">Reporting</label><br>
	<select name="Reporting" id="Reporting" class="form-select">
	<?php foreach($reportinglist as $r): ?>
	<?php if($deets[41] == $r): ?>
	<option selected><?= $r ?></option>
	<?php else: ?>
	<option><?= $r ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	<button class="btn btn-primary my-3">Save Course Info</button>
	</form>
</details>
</div>
<div class="mt-2 col-md-12">
		<strong>Keywords:</strong><br> 
		<?php if(!empty($deets[19])): ?>
		<?php $keys = explode(',',$deets[19]) ?>
		<?php foreach($keys as $k): ?>
			<span class="badge text-light-emphasis bg-light-subtle"><?= $k ?></span>
		<?php endforeach ?>
		<?php endif ?>
	</div>
</div>
<div class="row">
<div class="col-12">PEOPLE</div>

	<div class="col-md-4">
	<div class=""><strong>Steward:</strong></div>
	<?php if(!empty($stewsdevs['stewards'][0][2])): ?>
	<a href="/lsapp/person.php?idir=<?= $stewsdevs['stewards'][0][2] ?>"><?= $stewsdevs['stewards'][0][2] ?></a>
	<?php if(count($stewsdevs['stewards']) > 1): ?>
	<details>
		<summary>History</summary>
		<?php 
		foreach($stewsdevs['stewards'] as $p) {
			echo '<div>' . $p[2] . '<br>Since: ' . $p[3] . '</div>';
		}
		?>
	</details>
	<?php endif ?> 
	<?php else: ?>
		<div class="alert alert-danger">No steward set!</div>
	<?php endif ?> 

	
	

</div>
<div class="col-md-4">
<?php //$dev = getPerson($deets[34]) ?>
<div class=""><strong>Developer:</strong></div>
	<?php if(!empty($stewsdevs['developers'][0][2])): ?>
	<a href="/lsapp/person.php?idir=<?= $stewsdevs['developers'][0][2] ?>"><?= $stewsdevs['developers'][0][2] ?></a>
	<?php if(count($stewsdevs['developers']) > 1): ?>
	<details>
		<summary>History</summary>
		<?php 
		foreach($stewsdevs['developers'] as $p) {
			echo '<div>' . $p[2] . '<br> Since: ' . $p[3] . '</div>';
		}
		?>
	</details>
	<?php endif ?> 
	<?php else: ?>
		<div class="alert alert-danger">No developer set!</div>
	<?php endif ?> 
</div>
<div class="col-md-4">
<div class=""><strong>Corp. Partner:</strong><br> <a href="learning-hub-partner.php?partnerid=<?php echo urlencode($deets[36]) ?>"><?= $deets[36] ?></a></div>
</div>
</div>


<?php if($deets[21] !== 'eLearning'): ?>
<div class="row my-3">
	<div class="col-12">DETAILS</div>
	<div class="col-3"><strong>Alchemer?</strong><br> <?= $deets[37] ?></div>
	<div class="col-3"><strong>Times:</strong><br> <?= $deets[5] ?></div>
	<div class="col-3"><strong>Days:</strong><br> <?= $deets[6] ?></div>
	<div class="col-3"><strong>MinMax:</strong><br> <?= $deets[28] ?>/<?= $deets[29] ?></div>
</div>
<?php endif ?>

<?php if(!empty($deets[12])): ?>
<div class="row my-3 py-2 bg-light-subtle">
<div class="col-12">
	<strong>Notes:</strong><br>
	<?= $Parsedown->text($deets[12])  ?>
</div>
</div>
<?php endif ?>

	<details class="mb-3 p-2 border border-secondary-subtle rounded-3">
		<summary>File Paths &amp; URLs</summary>
		<div class="p-3 mb-3 bg-light-subtle">
		<?php if($deets[22]): ?>
		<div class=""><strong>eLearning link:</strong> <a href="<?= $deets[22] ?>" target="_blank"><?= $deets[22] ?></a></div>
		<?php endif ?>
		<!-- //42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams -->
		<div><strong>LAN Path:</strong> \\<?= $deets[42] ?>\ <button class="copy btn btn-sm btn-light" data-clipboard-text="\\<?= $deets[42] ?>\">Copy</button></div>
		<div><strong>Staging Path:</strong> <?= $deets[43] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[43] ?>">Copy</button></div>
		<div><strong>Live Path:</strong> <?= $deets[44] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[44] ?>">Copy</button></div>
		<div><strong>NIK Path:</strong> <?= $deets[45] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[45] ?>">Copy</button></div>
		<div><strong>Teams Path:</strong> <?= $deets[46] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[46] ?>">Copy</button></div>
		<?php if(!empty($deets[7])): ?>
			<!-- <a href="<?= $deets[7] ?>" target="_blank" class="btn btn-success">ELM</a> -->
		<?php endif ?>
		<?php if(!empty($deets[8])): ?>
			<a href="<?= $deets[8] ?>" target="_blank" class="btn btn-primary">PreWork</a>
		<?php endif ?>
		<?php if(!empty($deets[9])): ?>
			<a href="<?= $deets[9] ?>" target="_blank" class="btn btn-primary">PostWork</a>
		<?php endif ?>
		<?php if(!empty($deets[35])): ?>
			<a href="<?= $deets[35] ?>" target="_blank" class="btn btn-primary">Evaluation Report</a>
		<?php endif ?>
		<!-- <a href="https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&MODE=ADV&TITLE=<?php echo urlencode($deets[2]) ?>"
			target="_blank" 
			class="btn btn-dark">
			ELM Search
			</a> -->
		<!-- <a href="class-request.php?courseid=<?= $deets[0] ?>" class="btn btn-success">New Date Request</a> -->
		</div>
	</details>
	
	<details class="mb-3 p-2 border border-secondary-subtle rounded-3">
		<summary>Reviews</summary>
	<!-- <div class="m-3"><a href="/lsapp/audit-form.php?courseid=<?= $deets[0] ?>" class="btn btn-secondary">Create new audit for this course</a></div> -->
	<?php if(!empty($audits)): ?>
	<?php foreach($audits as $audit): ?>
		<div class="m-2 p-2 bg-light-subtle rounded-3">
			<div>
				<span class="badge bg-light-subtle "><?= $audit[6] ?></span> 
				<a href="/learning/resource-review/review.php?auditid=<?= $audit[0] ?>"><?= $audit[1] ?></a>
				by <?= $audit[2] ?>
			</div>
			<?php if($audit[7] == 25): ?>
				<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="25">25% - Significant work to align</meter>
				25% - Significant work to align 
			<?php elseif($audit[7] == 50): ?>
				<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="50">50% - Partially in alignment</meter>
				50% - Partially in alignment 
			<?php elseif($audit[7] == 75): ?>
				<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="75">75% - Mostly in alignment</meter>
				75% - Mostly in alignment 
			<?php elseif($audit[7] == 100): ?>
				<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="100">100% - Completely in alignment</meter>
				100% - Completely in alignment 
			<?php else: ?>
				Alignment Unknown! Please <a href="/learning/resource-review/review-update-form.php?auditid=<?= $audit->AuditID ?>#overallprinciplepercent">edit</a> and update.
			<?php endif ?>
		</div>
	<?php endforeach ?>
	<?php endif ?>
	</details>

<div>

	<!-- <div class="">Color:</div> 
		<div class="">
			<?= $deets[32] ?>
			<div style="background-color:<?= $deets[32] ?>; height: 10px; width: 100px;"></div>
		</div> -->
	<?php if($deets[18]): ?>
	<!-- <div class="">Prerequisites: <?= $deets[18] ?></div> -->
	<?php endif ?>
<details class="mb-3 p-2 border border-secondary-subtle rounded-3">
	<summary>Print Materials Operating Codes</summary>
	<div class="">Project Number: <?= $deets[24] ?>
		</div>
	<div class="">Responsibility: <?= $deets[25] ?>
		</div>
	<div class="">Service Line: <?= $deets[26] ?>
		</div>
	<div class="">STOB: <?= $deets[27] ?>
		</div>
</details>

	
	
	<div>

	

<?php if(!empty($deets[20])): ?>
<details class="mb-3">
	<summary>Old Categories</summary>
	<?php $cats = explode(',',$deets[20]) ?>
	<?php foreach($cats as $cat): ?>
		<a href="courses.php?category=<?php echo urlencode($cat) ?>"><?= $cat ?></a>, 
	<?php endforeach ?>
</details>
<?php endif ?>




</div>
</div>
</div>

<div class="col-md-6">
	<div><a href="/lsapp/class-bulk-insert.php?courseid=<?= $deets[0] ?>" class="btn btn-primary btn-block">New Date Requests</a></div>
	<hr>
<?php 
$inactive = 0;
$closed = 0;
$upcount = 0;
$classes = getCourseClasses($deets[0]);
foreach($classes as $class):
	$today = date('Y-m-d');
	if($class[9] < $today && $class[45] !== "eLearning") continue;
	if($class[1] == 'Inactive') $inactive++;
	if($class[1] == 'Closed' && $class[45] == "eLearning") $closed++;
$upcount++;
endforeach;
$finalcount = $upcount - $inactive - $closed;
?>


<?php if($finalcount > 0): ?>
<div class="mb-3" id="upcoming-classes">
	<div class="mb-3 p-3 bg-light-subtle sticky-top shadow-sm">
		<h3><span class="classcount"><?= $finalcount ?></span>  Current Offering<?php if($finalcount > 1) echo 's' ?></h3>
	</div>
	
<!-- <div class="btn-group">
<a href="course-classes-export.php?courseid=<?= $deets[0] ?>" class="btn btn-primary">Export to Excel</a>
<button class="btn btn-primary copy" 
	href="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical-course.php?courseid=<?= $deets[0] ?>"
	data-clipboard-text="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical-course.php?courseid=<?= $deets[0] ?>"
	title="All scheduled classes for this course">
	Calendar Subscribe
</button>
</div> -->
<!-- <input class="search form-control my-2" placeholder="search"> -->
<table class="table table-sm">
<tbody class="list">
<?php foreach($classes as $class): ?>
<?php
// We only wish to see classes which have an end date greater than today
$today = date('Y-m-d');
if($class[9] < $today && $class[45] !== 'eLearning') continue;
// elseif($class[45] == 'eLearning' && $class[1] == 'Closed') continue; // Only show the current active eLearning
?>
<?php if($class[1] == 'Inactive'): ?>
<tr class="cancelled">
<?php else: ?>
<tr>
<?php endif ?>
	<td>
		<?php if($class[4] == 'Dedicated'): ?>
		<span class="badge bg-light-subtle ">Dedicated</span>
		<?php endif ?>
		<small><?= $class[7] ?></small>
		
	</td>
	<td>
		<a href="/lsapp/class.php?classid=<?= $class[0] ?>">
		<?php echo goodDateShort($class[8],$class[9]) ?>
		</a>
		<div class="classdate" style="display:none"><?= $class[8] ?></div>
	</td>
	<td class="Venue">
        <a href="Venue.php?name=<?= $class[25] ?>"><?= $class[25] ?></a>
        <?php if(!$class[25]): ?>
        <?= h($class[45]) ?>
        <?php endif ?>
    </td>
    <td class="status">
		<?= $class[1] ?>
	</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif; //finalcount ?>
<div class="">

	<h3 class="mb-1">
		Change Requests
		<a class="badge text-light-emphasis bg-light-subtle" href="/lsapp/course-changes.php">All Requests</a>
	</h3>
	
<details class="mb-2 p-1">
	<summary>Add new change request</summary>
	<div class="p-3 my-3 bg-light-subtle rounded-3">
	<p>Request a change to the information <em>at the course level</em>. 
	To request a change to a class, please navigate to that class page 
	and submit your request there.</p>
		<form action="course-change-create.php" method="post">
		<input type="hidden" name="CourseName" id="CourseName" value="<?= h($deets[2]) ?>">
		<input type="hidden" name="CourseID" id="CourseID" value="<?= h($deets[0]) ?>">
		<label>Assign to: 
			<select class="form-select Assigned" name="AssignedTo" id="AssignedTo">
				<option>Unassigned</option>
				<?php getPeople($deets[44]) ?>
			</select>
		</label>
		<label>Request Type: 
			<select class="form-select RequestType" name="RequestType">
				<option disabled selected>Select &hellip;</option>
				<option value="Close">Close Course</option> 
				<option value="Update">Simple Content Update</option>
				<option value="Overhaul">Complete Content Overhaul</option>
				<option value="Moodle">Moodle</option>
				<option value="Other">Other</option>
			</select>
		</label>
		<label>Priority: 
			<select class="form-select Priority" name="Priority" id="Priority">
				<option value="Backlog">Backlog</option>
				<option value="NotUrgent" selected>Not urgent</option>
				<option value="ASAP">As Soon As Possible</option>
				<option value="HighPriority">High Priority</option>
			</select>
		</label>
		<div class="mt-3 guidance closecoursehelp alert alert-warning">
			<h5>Closing this course?</h5>
			<p>Don't forget to communicate with enrolled learners about the 
				closure.</p>
			<p>Example for comms around course closure could be things like 
				"Hey learner, last chance to complete this course before we 
				close it for good!"</p>
		</div>
		<div class="mt-3 guidance moodlehelp alert alert-warning">
			<h5>Moodle?</h5>
			<p>Instruction for Moodle workflows will go here.</p>
		</div>
		<textarea name="ChangeRequest" id="ChangeRequest" class="form-control" rows="8" required></textarea>
		<input type="submit" class="btn btn-sm btn-primary btn-block" value="Add Change Request">
		</form>
		</div>
</details>






<?php 
$chgs = getCourseChanges($courseid);
$completedchanges = []; 
?>
<?php if(isset($chgs)): ?>
	<!-- <h4>Pending Change Requests</h4> -->
<?php foreach($chgs as $ch): ?>
<?php if($ch[5] == 'Pending'): ?>
<div class="p-3 my-3 bg-light-subtle rounded-3">
<?php //0-creqID,1-CourseID,3-CourseName,4-DateRequested,5-RequestedBy,6-Status,7-CompletedBy,8-CompletedDate,9-Request ?>
	<?php if(isSuper()): ?>
	<form method="post" action="course-change-delete.php" class="float-end">
	<input type="hidden" name="CourseID" value="<?= $deets[0] ?>">
	<input type="hidden" name="reqID" value="<?= $ch[0] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-dark del">
	</form>
	<?php endif ?>
	<div>
		<strong>Requested <?= h($ch[3]) ?> by <?= h($ch[4]) ?></strong> 
	</div>
	<div class="">
		<span>Priority 
		<?php 
		if($ch[11] == 'NotUrgent'):
			$urgencybadge = 'primary';
		elseif($ch[11] == 'Backlog'):
			$urgencybadge = 'dark';
		elseif($ch[11] == 'ASAP'):
			$urgencybadge = 'warning';
		elseif($ch[11] == 'HighPriority'):
			$urgencybadge = 'danger';
		endif; 
		?>
		<span class="badge text-bg-<?= $urgencybadge ?>"><?= h($ch[11]) ?></span>
		<!-- <span>Status <span class="badge badge-secondary"><?= h($ch[5]) ?> </span></span> -->
		<span>Change type <span class="badge text-bg-secondary"><?= h($ch[9]) ?></span></span>
		Assigned to <span class="badge text-bg-secondary"><?= h($ch[10]) ?></span>
		<a href="/lsapp/course-change-claim.php?changeid=<?= h($ch[0]) ?>&courseid=<?= h($deets[0]) ?>" class="btn btn-sm btn-light">Claim</a>
	</div>
	<?php if($ch[5] != 'Pending'): ?>
		<?= h($ch[5]) ?> by <?= h($ch[6]) ?> on <?= h($ch[7]) ?>
	<?php endif ?>
	
	<div class="p-3 bg-light-subtle rounded-3">
		
		<?= $Parsedown->text($ch[8])  ?>
		<?php if($ch[5] == 'Pending'): ?>
		<!-- <span>Status <span class="badge text-bg-dark"><?= h($ch[5]) ?></span></span> -->
		<a href="/lsapp/course-change-process.php?changeid=<?= h($ch[0]) ?>&courseid=<?= h($deets[0]) ?>" class="btn btn-sm btn-success">Mark Complete</a>
		<a href="/lsapp/course-change-view.php?changeid=<?= h($ch[0]) ?>" class="btn btn-sm btn-secondary">Comments</a>
		<?php endif ?>
	</div>
	<?php if($ch[9] == 'Close'): ?>
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
	<?php endif ?>
	<?php if($ch[9] == 'Moodle'): ?>
	<div class="mt-3">
		<h4>Moodle Change</h4>
		<ol>
			<li>We'll have instructions specific to this case ASAP.
		</ol>
	</div>
	<?php endif ?>
</div>
<?php else: ?>
<?php array_push($completedchanges, $ch) ?>
<?php endif ?>
<?php endforeach ?>
<?php endif ?>



<?php if(!empty($completedchanges)): ?>
	<details class="p-1">
		<summary>Completed Changes</summary>
<?php foreach($completedchanges as $ch): ?>
<div class="p-3 my-3 bg-light-subtle rounded-3">
<?php //creqID,CourseID,CourseName,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request,RequestType,AssignedTo,Urgency ?>
	<?php if(isSuper()): ?>
	<form method="post" action="course-change-delete.php" class="float-right">
	<input type="hidden" name="CourseID" value="<?= $deets[0] ?>">
	<input type="hidden" name="reqID" value="<?= $ch[0] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-dark del">
	</form>
	<?php endif ?>
	<div><strong>Requested <?= h($ch[3]) ?> by <?= h($ch[4]) ?></strong></div>
	<div class="">
		<span>Priority 
		<?php 
		if($ch[11] == 'NotUrgent'):
			$urgencybadge = 'primary';
		elseif($ch[11] == 'ASAP'):
			$urgencybadge = 'warning';
		elseif($ch[11] == 'HighPriority'):
			$urgencybadge = 'danger';
		endif; 
		?>
		<span class="badge text-bg-<?= $urgencybadge ?>"><?= h($ch[11]) ?></span>
		<!-- <span>Status <span class="badge badge-secondary"><?= h($ch[5]) ?> </span></span> -->
		<span>Change type <span class="badge text-bg-secondary"><?= h($ch[9]) ?></span></span>
		Assigned to <span class="badge text-bg-secondary"><?= h($ch[10]) ?></span>
		
	</div>
	<?php if($ch[5] != 'Pending'): ?>
		<div><?= h($ch[5]) ?> by <?= h($ch[6]) ?> on <?= h($ch[7]) ?></div>
	<?php endif ?>
	
	<div class="p-3 bg-light-subtle rounded-3">
		
		<?= h($ch[8]) ?>
		<?php if($ch[5] == 'Pending'): ?>
		<span>Status <span class="badge text-bg-dark"><?= h($ch[5]) ?></span></span>
		<?php endif ?>
		<div>
			<a href="/lsapp/course-change-view.php?changeid=<?= h($ch[0]) ?>" 
				class="btn btn-sm btn-secondary">
					Comments
			</a>
		</div>
	</div>
</div>

<?php endforeach ?>
</details>
<?php endif ?>


</div>

</div> <!-- /.card -->




</div>
<div class="col-12">
<div class="p-3 my-3 bg-light-subtle rounded-3">Created on <?php echo goodDateLong($deets[13]) ?> by <a href="person.php?idir=<?= $deets[14] ?>"><?= $deets[14] ?></a></div>
</div>
</div>

<?php if(isSuper()): ?>
<!-- <div>
<div class="col-md-6">
<div class="alert alert-warning">
A WORK IN PROGRESS. Please don't mess with it :)
<form method="post" action="communication-template-create.php">
<input type="hidden" name="CourseID" id="CourseID" value="<?= h($deets[0]) ?>">
Template Name: <input type="text" id="TemplateName" name="TemplateName" class="form-control"><br>
Template:<br>
<textarea class="form-control summernote" name="Template" id="Template"></textarea>
<input type="submit" class="btn btn-block btn-success" value="Add Template">
</form>
</div>
</div>
</div> -->
<?php endif ?>


</div>
</div>
</div>




<?php else: ?>
<?php getHeader() ?>

<title>LSApp | Dashboard</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php require('templates/noaccess.php') ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>

<script>
$(document).ready(function(){
	$('.guidance').addClass('d-none');
	$('.RequestType').on('change', function(){
		let type = $(this).val();
		$('.guidance').addClass('d-none');
		if(type == 'Close') {
			$('.closecoursehelp').removeClass('d-none');
		}
		if(type == 'Moodle') {
			$('.moodlehelp').removeClass('d-none');
		}

	});
}); 
</script>

<script src="/lsapp/js/clipboard.min.js"></script>
<script>
$(document).ready(function(){

	var clipboard = new Clipboard('.copy');
	$('.copy').on('click',function(){ alert('File path copied!'); });

	
	// $('.summernote').summernote({
	// 	toolbar: [
	// 		// [groupName, [list of button]]
	// 		['style', ['bold', 'italic']],
	// 		['para', ['ul', 'ol']],
	// 	],
	// 	placeholder: 'Type here'
	// });	
	// $('.search').focus();
	
	var upcomingoptions = {
		valueNames: [ 'classdate', 
						'Venue',
						'status'
					]
	};
	var upcomingClasses = new List('upcoming-classes', upcomingoptions);
	upcomingClasses.on('searchComplete', function(){
		//console.log(upcomingClasses.update().matchingItems.length);
		$('.classcount').html(upcomingClasses.update().matchingItems.length);
	});
});
</script>

<?php require('templates/footer.php') ?>

