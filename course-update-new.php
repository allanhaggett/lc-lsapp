<?php 
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
$course = getCourse($courseid);
$audits = getCourseAudits($courseid);
//echo '<pre>'; print_r($audits); exit;
// 0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,
// 10-CourseOwner,11-MinMax,12-CourseNotes,
// 13-Requested, 14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,19-Keywords,
// 20-Category,21-Method,22-elearning
?>
<?php getHeader() ?>

<title><?= $course[2] ?></title>
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

<form method="post" action="course-update.php" class="mb-3 pb-3" id="courseupdateform">
<input class="Requested" type="hidden" name="Requested" value="<?= h($course[13]) ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="<?= h($course[14]) ?>">

<div class="container mb-5">
<div class="row">
<div class="col-md-12 col-lg-8">
<!--<div class="text-uppercase">LC Ship? <?= $course[23] ?></div>-->
<div class="row mb-3 py-2 bg-light-subtle rounded-3">
	<div class="col-6 col-md-3"><strong>Status:</strong><br>
	<?php $statuses = Array('Requested','Active','Inactive') ?>
	<select name="Status" id="Status" class="form-control">
	<?php foreach($statuses as $s): ?>
	<?php if($s == $course[1]): ?>
	<option selected><?= $s ?></option>
	<?php else: ?>
	<option><?= $s ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>
	<div class="col-6 col-md-3"><strong>Short name:</strong><br> 
	<input type="text" name="CourseShort" id="CourseShort" class="form-control" required value="<?= h($course[3]) ?>">	
	</div>
	<div class="col-6 col-md-3"><strong>ELM Code:</strong><br> 
	<input class="form-control ItemCode" type="text" name="ItemCode" value="<?= h($course[4]) ?>">	
	</div>
	<div class="col-6 col-md-3"><strong>Delivery method:</strong><br>
	<select name="" class="form-control">
	<?php $methods = array('Classroom','eLearning','Blended','Webinar') ?>
	<?php foreach($methods as $method): ?>
	<?php if($method == $course[21]): ?>
	<option selected><?= $method ?></option>
	<?php else: ?>
	<option><?= $method ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>
</div>

	<div class="float-right">
		<a href="course.php?courseid=<?= $courseid ?>" class="btn btn-light float-end">View course</a>
	</div>

<h1><?= $course[2] ?></h1>
<textarea name="CourseDescription" id="CourseDescription" class="form-control" rows="5" required><?= h($course[16]) ?></textarea>
<details class="p-2">
	<summary>Full Abstract</summary>
	<div class="p-3 bg-light-subtle rounded-3">
	<textarea name="CourseAbstract" id="CourseAbstract" class="form-control" rows="5" required><?= h($course[17]) ?></textarea>
	</div>
</details>
</div>
</div>
<div class="row my-3">
<div class="col-md-6">

<div class="row mb-3 py-2 bg-light-subtle rounded-3">
	<div class="col-12">TAXONOMIES</div>

	
<div class="col-12">

	<label for="Topics">Topic</label><br>
	<select name="Topics" id="Topics" class="form-control">
	<?php foreach($topics as $t): ?>
	<?php if($course[38] == $t): ?>
	<option selected><?= $t ?></option>
	<?php else: ?>
	<option><?= $t ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	


	<label for="Levels">Group</label><br>
	<select name="Levels" id="Levels" class="form-control">
	<?php foreach($levels as $l): ?>
	<?php if($course[40] == $l): ?>
	<option selected><?= $l ?></option>
	<?php else: ?>
	<option><?= $l ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	<label for="Reporting">Reporting</label><br>
	<select name="Reporting" id="Reporting" class="form-control">
	<?php foreach($reportinglist as $r): ?>
	<?php if($course[41] == $r): ?>
	<option selected><?= $r ?></option>
	<?php else: ?>
	<option><?= $r ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>


	
</div>
<div class="mt-2 col-md-12">
		<strong>Keywords:</strong><br> 
		<?php if(!empty($course[19])): ?>
		<?php $keys = explode(',',$course[19]) ?>
		<?php foreach($keys as $k): ?>
			<span class="badge bg-light-subtle "><?= $k ?></span>
		<?php endforeach ?>
		<?php endif ?>
	</div>
</div>
<div class="row">
<div class="col-12">PEOPLE</div>
	<div class="col-md-4">
	<strong>Steward:</strong><br>
	<select class="form-control CourseOwner" name="CourseOwner" id="CourseOwner">
	<?php getPeople($course[10]) ?>
	</select>
</div>
<div class="col-md-4">
<strong>Developer:</strong><br> 
<select class="form-control Developer" name="Developer" id="Developer">
<?php getPeople($course[34]) ?>
</select>

</div>
<div class="col-md-4">
<div class=""><strong>Corp. Partner:</strong><br> 
<?php $learnpartners = getPartners(); ?>
<select name="LearningHubPartner" id="LearningHubPartner" class="form-control" required>
<?php foreach($learnpartners as $part): ?>
	<?php if($part->name == $course[36]): ?>
	<option selected><?= $part->name ?></option>
	<?php else: ?>
	<option><?= $part->name ?></option>
	<?php endif ?>
<?php endforeach ?>
</select>	
</div>
</div>
</div>


<?php if($course[21] !== 'eLearning'): ?>
<div class="row my-3">
	<div class="col-12">DETAILS</div>
	<div class="col-3"><strong>Alchemer?</strong><br> <?= $course[37] ?></div>
	<div class="col-3"><strong>Times:</strong><br> <?= $course[5] ?></div>
	<div class="col-3"><strong>Days:</strong><br> <?= $course[6] ?></div>
	<div class="col-3"><strong>MinMax:</strong><br> <?= $course[28] ?>/<?= $course[29] ?></div>
</div>
<?php endif ?>

<?php if(!empty($course[12])): ?>
<div class="row my-3 py-2 bg-light-subtle">
<div class="col-12">
	<strong>Notes:</strong><br>
	<?= $Parsedown->text($course[12])  ?>
</div>
</div>
<?php endif ?>

	
	<details class="p-2">
		<summary>File Paths &amp; URLs</summary>
		<div class="p-3 mb-3 bg-light-subtle">
		<?php if($course[22]): ?>
		<div class=""><strong>eLearning link:</strong> <a href="<?= $course[22] ?>" target="_blank"><?= $course[22] ?></a></div>
		<?php endif ?>
		<!-- //42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams -->
		<div><strong>LAN Path:</strong> \\<?= $course[42] ?>\ <button class="copy btn btn-sm btn-light" data-clipboard-text="\\<?= $course[42] ?>\">Copy</button></div>
		<div><strong>Staging Path:</strong> <?= $course[43] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $course[43] ?>">Copy</button></div>
		<div><strong>Live Path:</strong> <?= $course[44] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $course[44] ?>">Copy</button></div>
		<div><strong>NIK Path:</strong> <?= $course[45] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $course[45] ?>">Copy</button></div>
		<div><strong>Teams Path:</strong> <?= $course[46] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $course[46] ?>">Copy</button></div>
		<?php if(!empty($course[7])): ?>
			<!-- <a href="<?= $course[7] ?>" target="_blank" class="btn btn-success">ELM</a> -->
			<?php endif ?>
			<?php if(!empty($course[8])): ?>
				<a href="<?= $course[8] ?>" target="_blank" class="btn btn-primary">PreWork</a>
				<?php endif ?>
				<?php if(!empty($course[9])): ?>
					<a href="<?= $course[9] ?>" target="_blank" class="btn btn-primary">PostWork</a>
					<?php endif ?>
					<!-- <a href="https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&MODE=ADV&TITLE=<?php echo urlencode($course[2]) ?>"
					target="_blank" 
					class="btn btn-dark">
					ELM Search
				</a> -->
				<!-- <a href="class-request.php?courseid=<?= $course[0] ?>" class="btn btn-success">New Date Request</a> -->
			</div>
	</details>

	
	<details class="p-2">
		<summary>Audits</summary>
	<div class="m-3"><a href="/lsapp/audit-form.php?courseid=<?= $course[0] ?>" class="btn btn-secondary">Create new audit for this course</a></div>
	<?php if(!empty($audits)): ?>
	<?php foreach($audits as $audit): ?>
		<div class="m-2 p-2 bg-light-subtle rounded-3">
			<div>
				<span class="badge bg-light-subtle "><?= $audit[6] ?></span> 
				<a href="/lsapp/audit.php?auditid=<?= $audit[0] ?>"><?= $audit[1] ?></a>
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
				Alignment Unknown! Please <a href="/lsapp/audit-update-form.php?auditid=<?= $audit->AuditID ?>#overallprinciplepercent">edit</a> and update.
			<?php endif ?>
		</div>
	<?php endforeach ?>
	<?php endif ?>
	</details>


<div>

	

	<!-- <div class="">Color:</div> 
		<div class="">
			<?= $course[32] ?>
			<div style="background-color:<?= $course[32] ?>; height: 10px; width: 100px;"></div>
		</div> -->
	<?php if($course[18]): ?>
	<!-- <div class="">Prerequisites: <?= $course[18] ?></div> -->
	<?php endif ?>
<details class="p-2">
	<summary>Print Materials Operating Codes</summary>
	<div class="">Project Number: <?= $course[24] ?>
		</div>
	<div class="">Responsibility: <?= $course[25] ?>
		</div>
	<div class="">Service Line: <?= $course[26] ?>
		</div>
	<div class="">STOB: <?= $course[27] ?>
		</div>
</details>


	
	<?php if($course[35]): ?>
	<div class=mb-3">Evaluations link: <?= $course[35] ?></div>
	<?php endif ?>

	
	
	
	<div>

	

<?php if(!empty($course[20])): ?>
<details class="mb-3">
	<summary>Old Categories</summary>
	<?php $cats = explode(',',$course[20]) ?>
	<?php foreach($cats as $cat): ?>
		<a href="courses.php?category=<?php echo urlencode($cat) ?>"><?= $cat ?></a>, 
	<?php endforeach ?>
</details>
<?php endif ?>




</div>
</div>
</div>

<div class="col-12">
<div class="p-3 my-3 bg-light-subtle rounded-3">Created on <?php echo goodDateLong($course[13]) ?> by <a href="person.php?idir=<?= $course[14] ?>"><?= $course[14] ?></a></div>
</div>
</div>



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

<!-- <script src="/lsapp/js/summernote-bs4.js"></script> -->

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

