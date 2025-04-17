<?php ob_start(); ?>
<?php require('inc/lsapp.php') ?>
<?php opcache_reset(); ?>
<?php if(isAdmin()): ?>
<?php if($_POST): ?>
<?php 

// Start by validating if pre and post work links are secure HTTPS 
// protocol and silently force-update them if they're not.
//
$prework = h($_POST['PreWork']);
$securepre = $prework;
$scheem = parse_url($prework, PHP_URL_SCHEME);
if($scheem != 'https') {
	$securepre = str_replace('http://', 'https://', $prework );
}
$postwork = h($_POST['PostWork']);
$securepost = $postwork;
$scheem = parse_url($securepost, PHP_URL_SCHEME);
if($scheem != 'https') {
	$securepost = str_replace('http://', 'https://', $postwork );
}


$f = fopen('data/courses.csv','r');
$temp_table = fopen('data/courses-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);


$coursecat = '';
// foreach($_POST['Category'] as $c) {
// 	$coursecat .= $c . ',';
// }

if(!isset($_POST['WeShip'])) {
	$weship = 'No';
} else {
	$weship = 'Yes';
}

if(!isset($_POST['Alchemer'])) {
	$alchemer = 'No';
} else {
	$alchemer = 'Yes';
}

if(!isset($_POST['HUBInclude'])) {
	$hubInclude = 'No';
} else {
	$hubInclude = 'Yes';
}

$now = date('Y-m-d\TH:i:s');

$combinedtimes = h($_POST['StartTime']) . ' - ' . h($_POST['EndTime']);

$lanpathfront = ltrim(trim($_POST['PathLAN']),'\\');
$lanpathvalid = rtrim($lanpathfront,'\\');

$slug = createSlug($_POST['CourseName']);

$featured = $_POST['Featured'] ?? ''; // Default to empty string if not provided
$developer = $_POST['Developer'] ?? ''; // Default to empty string if not provided
$levels = $_POST['Levels'] ?? '';
$reporting = $_POST['Reporting'] ?? '';
$isMoodle = $_POST['isMoodle'] ?? '';
$taxonomyProcessed = $_POST['TaxonomyProcessed'] ?? '';
$taxonomyProcessedBy = $_POST['TaxonomyProcessedBy'] ?? '';


$course = Array($_POST['CourseID'],
				h($_POST['Status']),
				h($_POST['CourseName']),
				h($_POST['CourseShort']),
				h($_POST['ItemCode']),
				$combinedtimes,
				h($_POST['ClassDays']),
				h($_POST['ELM']),
				$securepre,
				$securepost,
				h($_POST['CourseOwner'] ?? ''),
				'', // used to be minmax
				h($_POST['CourseNotes']),
				h($_POST['Requested']),
				h($_POST['RequestedBy']),
				h($_POST['EffectiveDate']),
				h($_POST['CourseDescription']),
				h($_POST['CourseAbstract']),
				h($_POST['Prerequisites']),
				h($_POST['Keywords']),
				$coursecat,
				h($_POST['Method']),
				h($_POST['elearning']),
				h($weship),
				h($_POST['ProjectNumber']),
				h($_POST['Responsibility']),
				h($_POST['ServiceLine']),
				h($_POST['STOB']),
				h($_POST['MinEnroll']),
				h($_POST['MaxEnroll']),
				h($_POST['StartTime']),
				h($_POST['EndTime']),
				h($_POST['CourseColor']),
				$featured,
				$developer,
				h($_POST['EvaluationsLink']),
				h($_POST['LearningHubPartner']),
				h($alchemer),
				h($_POST['Topics']),
				h($_POST['Audience'] ?? ''),
				$levels,
				$reporting,
				$lanpathvalid,
				h($_POST['PathStaging']),
				h($_POST['PathLive']),
				h($_POST['PathNIK']),
				h($_POST['PathTeams']),
			    $isMoodle,
				$taxonomyProcessed,
				$taxonomyProcessedBy,
				h($_POST['ELMCourseID']),
				$now,
				h($_POST['Platform']),
				$hubInclude,
				h($_POST['RegistrationLink']),
				$slug,
				h($_POST['HubExpirationDate']),
				h($_POST['OpenAccessOptin'])

			);

$courseid = $_POST['CourseID'];
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $courseid) {
		$coursesteward = $data[10];
		$coursedeveloper = $data[34];
		fputcsv($temp_table,$course);
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/courses-temp.csv','data/courses.csv');

// CourseID,Role,IDIR,Date
$peoplefp = fopen('data/course-people.csv', 'a+');
if(($_POST['CourseOwner'] ?? '') != $coursesteward) {
	$stew = [$courseid,'steward',$_POST['CourseOwner'] ?? '', $now];
	fputcsv($peoplefp, $stew);
}
if(isset($_POST['Developer']) && $_POST['Developer'] != $coursedeveloper) {
	$dev = [$courseid,'dev',$_POST['Developer'], $now];
	fputcsv($peoplefp, $dev);
}

fclose($peoplefp);



header('Location: course.php?courseid=' . $courseid);?>


<?php else: ?>

<?php $courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0 ?>
<?php $course = getCourse($courseid) ?>
<?php getHeader() ?>

<title>Update <?= $course[2] ?></title>
<!-- <link href="/lsapp/css/summernote-bs4.css" rel="stylesheet"> -->

<?php getScripts() ?>
<body class="">
<?php getNavigation() ?>

<?php if(canAccess()): ?>
<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8 mb-3">

<h1><?= h($course[2]) ?></h1>


<form method="post" action="course-update.php" class="mb-3 pb-3" id="courseupdateform">

<input class="TaxonomyUpdated" type="hidden" name="TaxonomyUpdated" value="<?= h($course[48]) ?>">
<input class="TaxonomyUpdatedBy" type="hidden" name="TaxonomyUpdated" value="<?= h($course[49]) ?>">
<input class="ELMCourseID" type="hidden" name="TaxonomyUpdated" value="<?= h($course[50]) ?>">


<input class="Requested" type="hidden" name="Requested" value="<?= h($course[13]) ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="<?= h($course[14]) ?>">
<div class="form-group">
<label for="Status">Status</label><br>
<?php $statuses = Array('Requested','Active','Inactive') ?>
<select name="Status" id="Status" class="form-select">
<?php foreach($statuses as $s): ?>
<?php if($s == $course[1]): ?>
<option selected><?= $s ?></option>
<?php else: ?>
<option><?= $s ?></option>
<?php endif ?>
<?php endforeach ?>
</select>
</div>
<div class="form-group">
<label for="LearningHubPartner">Learning Hub Partner</label><br>
<?php $learnpartners = getPartnersNew(); ?>
<select name="LearningHubPartner" id="LearningHubPartner" class="form-select" required>
<?php foreach($learnpartners as $part): ?>
	<?php if($part->name == $course[36]): ?>
	<option selected><?= $part->name ?></option>
	<?php else: ?>
	<option><?= $part->name ?></option>
	<?php endif ?>
<?php endforeach ?>
</select>
</div>

<div class="form-group">
<?php $platforms = getAllPlatforms(); ?>
<label for="Platform">Platform</label><br>
<select name="Platform" id="Platform" class="form-select">
<?php foreach($platforms as $pl): ?>
<?php if($course[52] == $pl): ?>
<option selected><?= $pl ?></option>
<?php else: ?>
<option><?= $pl ?></option>
<?php endif ?>
<?php endforeach ?>
</select>
</div>

<div id="notelm" class="d-none alert alert-primary">
	<div class="form-group mb-3">
		<label for="RegistrationLink">Registration Link</label><br>
		<small>If this course does not have registration in the Learning System, 
			then where do you go to register for it?</small>
		<input type="text" name="RegistrationLink" id="RegistrationLink" class="form-control" value="<?= $course[54] ?>">
	</div>
	<div class="form-group">
		<label for="HubExpirationDate">Expiration date</label><br>
		<small>Date after which the course will be removed from the search results.</small>
		<input type="date" name="HubExpirationDate" id="HubExpirationDate" class="form-control" value="<?= $course[56] ?>">
	</div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const platformSelect = document.getElementById("Platform");
    const notElmDiv = document.getElementById("notelm");

    platformSelect.addEventListener("change", function () {
        if (platformSelect.value === "PSA Learning System") {
            notElmDiv.classList.add("d-none");
        } else {
            notElmDiv.classList.remove("d-none");
        }
    });
});
</script>


<div class="form-group">
<?php if($course[55] == 'on' || $course[55] == 'Yes'): ?>
	<input type="checkbox" name="HUBInclude" id="HUBInclude" checked>
	<label for="HUBInclude">Include in LearningHUB?</label>
<?php else: ?>
	<input type="checkbox" name="HUBInclude" id="HUBInclude">
	<label for="HUBInclude">Include in LearningHUB?</label>
<?php endif ?>
</div>
<div class="form-group">
<?php if(!empty($course[3])): ?>
	<?php if($course[57] == 'on' || $course[57] == 'Yes'): ?>
	<input type="checkbox" name="OpenAccessOptin" id="OpenAccessOptin" checked>
	<label for="OpenAccessOptin">OpenAccess Publish?</label>
	<?php else: ?>
	<input type="checkbox" name="OpenAccessOptin" id="OpenAccessOptin">
	<label for="OpenAccessOptin">OpenAccess Publish?</label>
	<?php endif ?>
<?php else: ?>
	<input type="checkbox" name="OpenAccessOptin" id="OpenAccessOptin" disabled>
	<label for="OpenAccessOptin">OpenAccess Publish?</label>
	<div class="alert alert-primary my-1">Cannot be published on Open Access server until a short name is set.</div>
<?php endif ?>
</div>

<div class="form-group">
<?php if($course[57] == 'on' || $course[57] == 'Yes'): ?>
	<input type="checkbox" name="OpenAccessOptin" id="OpenAccessOptin" checked>
	<label for="OpenAccessOptin">OpenAccess Publish?</label>
<?php else: ?>
	<input type="checkbox" name="OpenAccessOptin" id="OpenAccessOptin">
	<label for="OpenAccessOptin">OpenAccess Publish?</label>
<?php endif ?>
</div>





<div class="form-group">
<?php if($course[33] == 'on' || $course[33] == 'Yes'): ?>
<input type="checkbox" name="Featured" id="Featured" checked> Featured?
<?php else: ?>
<input type="checkbox" name="Featured" id="Featured"> Featured?
<?php endif ?>
</div>


<div class="form-group">
<?php if($course[23] == 'on' || $course[23] == 'Yes'): ?>
<input type="checkbox" name="WeShip" id="WeShip" checked> We Ship?
<?php else: ?>
<input type="checkbox" name="WeShip" id="WeShip"> We Ship?
<?php endif ?>
</div>


<div class="form-group">
<?php if($course[37] == 'on' || $course[37] == 'Yes'): ?>
<label><input type="checkbox" name="Alchemer" id="Alchemer" checked> Alchemer?</label>
<?php else: ?>
<label><input type="checkbox" name="Alchemer" id="Alchemer"> Alchemer?</label>
<?php endif ?>
</div>




<div class="form-group">	
<label for="CourseName">Course Name (Long)</label><br>
<small>(Max# characters, alpha/numeric =200) | Full/Complete title of the course</small>


<input type="text" name="CourseName" id="CourseName" class="form-control" required value="<?= h($course[2]) ?>">


<div class="alert alert-success" id="CNLNum"></div>
</div>
<div class="my-3">
<label>
	ELM Course ID
	<input type="text" name="ELMCourseID" id="ELMCourseID" class="form-control" value="<?= h($course[50]) ?>">
</label>
</div>
<div class="form-group">
<label for="CourseShort">Course Name (Short)</label><br>
<small>(Max# characters, alpha/numeric= 10; <strong>no spaces</strong>) | <a href="#" title="coming soon">Appropriate acronym following LC guidelines</a></small>
<input type="text" name="CourseShort" id="CourseShort" class="form-control" value="<?= h($course[3]) ?>">
<div class="alert alert-success" id="CNSNum"></div>
</div>





<input class="form-control CourseID" type="hidden" name="CourseID" value="<?= h($course[0]) ?>">

<div class="row">
<div class="col-md-6">

<label for="ItemCode">Item Code</label>
<input class="form-control ItemCode" type="text" name="ItemCode" value="<?= h($course[4]) ?>">

</div>
<div class="col-md-6">

<label for="CourseColor">Color</label>
<input class="form-control CourseColor" type="text" name="CourseColor" value="<?= h($course[32]) ?>">

</div>
</div>
<div class="form-group">
<label for="EvaluationsLink">Evaluation Link</label>
<input class="form-control ELM" type="text" name="EvaluationsLink" value="<?= h($course[35]) ?>">
</div>
<div class="form-group">
<label for="ELM">ELM Link</label>
<input class="form-control ELM" type="text" name="ELM" value="<?= h($course[7]) ?>">
</div>
<div class="form-group">
<label for="CourseNotes">Notes</label>
<textarea class="form-control CourseNotes" type="text" name="CourseNotes"><?= h($course[12]) ?></textarea>
</div>

<div class="row my-4">
<div class="col-md-6">
<label for="CourseOwner">Steward</label><br>
<small>Responsible for delivery.</small>
<select class="form-select CourseOwner" name="CourseOwner" id="CourseOwner">
<option selected disabled>Unassigned</option>
<?php getPeople($course[10]) ?>
</select>
</div>
<div class="col-md-6">

<label for="Developer">Developer</label><br>
<small>Responsible for materials creation/revisions.</small>
<select class="form-select Developer" name="Developer" id="Developer">
<option selected disabled>Unassigned</option>
<?php getPeople($course[34]) ?>
</select>

</div>
</div>
<div class="col">
<div class="form-group">
<label for="EffectiveDate">Effective date</label><br>
<small>Date the course should be made visible to learners</small>
<input type="text" name="EffectiveDate" id="EffectiveDate" class="form-control" required value="<?= h($course[15]) ?>">
</div>
</div>



<div class="form-group">
<label for="CourseDescription">Course Description</label><br>
<small>(Max# characters, alpha/numeric= 254)<br>
The overall purpose of the training in 2 to 3 sentences (maximum) inclusive of:<br>
<ol>
<li>Course duration (# of days)
<li>Target learners
<li>Delivery method.
</ol>
</small>

<textarea name="CourseDescription" id="CourseDescription" class="form-control" required><?= h($course[16]) ?></textarea>
<div class="alert alert-success" id="CDNum"></div>
</div>

<div class="form-group">
<label for="CourseAbstract">Course Abstract</label><br>
<small>(Max# characters, alpha/numeric=4,000) <br>
<div>An elaboration of the Course Description providing more information on course context, design and development as well as structure. It has the following information:</div>
<ol>
<li>Background – clarifying business case, the strategic intent and the need it addresses
<li>Learning Objectives
<li>Organizational Benefits
<li>Course Development (if relevant to understanding the course: e.g., developed with the Aboriginal community or the Project Management Community of Practice)
<li>Course Structure (if relevant to understanding the course: e.g., six sections (modularized)
<li>Competencies
</ol></small>
<textarea name="CourseAbstract" id="CourseAbstract" class="form-control">
<?= h($course[17]) ?>
</textarea>
<div class="alert alert-success" id="CANum"></div>
</div>

<div class="form-group">
<label for="Prerequisites">Pre-requisites</label><br>
<small>Any required stand-alone course/s and/or resources that course registrant needs to attend/complete any time prior to attendance of this course</small>
<input type="text" name="Prerequisites" id="Prerequisites" class="form-control" value="<?= h($course[18]) ?>">

</div>

<h2>Taxonomies</h2>
<div class="row">
<!-- Topics,Audience,Levels,Reporting -->
<?php
$topics = getAllTopics();
$audience = getAllAudiences ();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();
?>
<div class="col-6">
<div class="form-group">
<label for="Topics">Topics</label><br>
<select name="Topics" id="Topics" class="form-select">
	<option selected disabled>Unassigned</option>
	<?php foreach($topics as $t): ?>
	<?php if($course[38] == $t): ?>
	<option selected><?= $t ?></option>
	<?php else: ?>
	<option><?= $t ?></option>
	<?php endif ?>
	<?php endforeach ?>
</select>
</div>
<div class="form-group">
<label for="Audience">Audience</label><br>
<select name="Audience" id="Audience" class="form-select">
	<option selected disabled>Unassigned</option>
	<?php foreach($audience as $a): ?>
	<?php if($course[39] == $a): ?>
	<option selected><?= $a ?></option>
	<?php else: ?>
	<option><?= $a ?></option>
	<?php endif ?>
	<?php endforeach ?>
</select>
</div>


</div>
<div class="col-6">

<div class="form-group">

<label for="Levels">Levels</label><br>
<select name="Levels" id="Levels" class="form-select">
	<option selected disabled>Unassigned</option>
	<?php foreach($levels as $l): ?>
	<?php if($course[40] == $l): ?>
	<option selected><?= $l ?></option>
	<?php else: ?>
	<option><?= $l ?></option>
	<?php endif ?>
	<?php endforeach ?>
</select>
</div>

<div class="form-group">
<label for="Reporting<?= $course[0] ?>">Evaluation</label><br>
<select name="Reporting" id="Reporting<?= $course[0] ?>" class="form-select">
	<option selected disabled>Unassigned</option>
	<?php foreach($reportinglist as $r): ?>
	<?php if($course[41] == $r): ?>
	<option selected><?= $r ?></option>
	<?php else: ?>
	<option><?= $r ?></option>
	<?php endif ?>
	<?php endforeach ?>
</select>
</div>


</div>
</div>

<div class="form-group">
<label for="Keywords">Keywords</label><br>
<small>Any word not included in the title or short description that could be used by a learner to search for the course</small>
<input type="text" name="Keywords" id="Keywords" class="form-control" value="<?= h($course[19]) ?>">
</div>
<div class="row">

<div class="col">

<div class="form-group">

<label>Delivery Method</label><br>
<small>Please select from these options</small>
<div class="mb-3 p-3 bg-light-subtle rounded-3">

<?php $methods = array('Classroom','eLearning','Blended','Webinar') ?>
<?php foreach($methods as $method): ?>
<div class="form-check">
	<?php if($method == $course[21]): ?>
  <input type="radio" name="Method" id="<?= $method ?>" class="form-check-input" value="<?= $method ?>" checked>
  <?php else: ?>
  <input type="radio" name="Method" id="<?= $method ?>" class="form-check-input" value="<?= $method ?>">
  <?php endif ?>
  <label class="form-check-label" for="<?= $method ?>"><?= $method ?></label>
</div>
<?php endforeach ?>
</div>

<div class="mb-3 p-2 bg-light-subtle rounded-3">
<?php if($course[47] == 'on' || $course[47] == 'Yes'): ?>
<input type="checkbox" name="isMoodle" id="isMoodle" checked> Moodle Course?
<?php else: ?>
<input type="checkbox" name="isMoodle" id="isMoodle"> Moodle Course?
<?php endif ?>
</div>
</div>
</div>
</div>
<h2>Administative Details</h2>
<div class="row">

<div class="col-3">
<div class="form-group">
<label for="MinEnroll">Minimum # of Participants</label><br>
<input type="text" name="MinEnroll" id="MinEnroll" class="form-control" value="<?= $course[28] ?>">
</div>
</div>

<div class="col-3">
<div class="form-group">
<label for="MaxEnroll">Maximum # of Participants</label><br>
<input type="text" name="MaxEnroll" id="MaxEnroll" class="form-control" value="<?= $course[29] ?>">
</div>
</div>

<div class="col">
<div class="form-group">
<label for="elearning">eLearning Course</label><br>
<small>Include the URL link for the course.</small>
<input type="text" name="elearning" id="elearning" class="form-control" value="<?= $course[22] ?>">
</div>
</div>
</div>

<div class="row">


<div class="col-6">
<div class="form-group"> 	
<label for="PreWork">Pre-work Link</label><br>
<input type="text" name="PreWork" id="PreWork" class="form-control" value="<?= h($course[8]) ?>">
<label for="PostWork">Post-work Link</label><br>
<input type="text" name="PostWork" id="PostWork" class="form-control" value="<?= h($course[9]) ?>">
</div>
</div>

<div class="col-6">
<div class="form-group"> 	
<label for="ClassDays">How Many Days?</label><br>
<input type="text" name="ClassDays" id="ClassDays" class="form-control" value="<?= h($course[6]) ?>">
<div class="row">
<div class="col-md-6">
<label for="st">Start time</label>
<input class="form-select starttime" id="st" type="text" name="StartTime" value="<?= h($course[30]) ?>">
</div>
<div class="col-md-6">
<label for="et">End time</label>
<input class="form-select endtime" id="et" type="text" name="EndTime" value="<?= h($course[31]) ?>">
</div>
</div>
<!--
<label for="ClassTimes">What Times?</label><br>
<input type="text" name="ClassTimes" id="ClassTimes" class="form-control" value="<?= h($course[5]) ?>">
-->
</div>
</div>
</div>



<h2>Print Details</h2>

<div class="row">
<div class="col-6">
<div class="form-group"> 	
<label for="ProjectNumber">Project Number</label><br>
<input type="text" name="ProjectNumber" id="ProjectNumber" class="form-control" value="<?= h($course[24]) ?>">
<label for="Responsibility">Responsibility</label><br>
<input type="text" name="Responsibility" id="Responsibility" class="form-control" value="<?= h($course[25]) ?>">
</div>
</div>
<div class="col-6">
<div class="form-group"> 	
<label for="ServiceLine">Service Line</label><br>
<input type="text" name="ServiceLine" id="ServiceLine" class="form-control" value="<?= h($course[26]) ?>">
<label for="STOB">STOB</label><br>
<input type="text" name="STOB" id="STOB" class="form-control" value="<?= h($course[27]) ?>">
</div>
</div>
</div>

<h2>Developer File Paths</h2>
<!-- //42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams -->
<div class="row">
<div class="col-6">
<div class="form-group"> 	
<label for="PathLAN">LAN Path</label><br>
<input type="text" name="PathLAN" id="PathLAN" class="form-control" value="<?= h($course[42]) ?>">
<!-- <div class="alert alert-warning">Please remove leading and trailing slashes from LAN paths, e.g., <br>
<div class="p-3">\\sfp.idir.bcgov\S142\S4333\Learning Centre\8. Courses 57820-20\Information Management\IM 117 - Privacy, Access and Records Management\ </div>
<div><strong>would become</strong></div>
<div class="p-3">sfp.idir.bcgov\S142\S4333\Learning Centre\8. Courses 57820-20\Information Management\IM 117 - Privacy, Access and Records Management</div>
  </div> -->
<label for="PathStaging">Staging Path</label><br>
<input type="text" name="PathStaging" id="PathStaging" class="form-control" value="<?= h($course[43]) ?>">
<label for="PathTeams">Teams Path</label><br>
<input type="text" name="PathTeams" id="PathTeams" class="form-control" value="<?= h($course[46]) ?>">
</div>
</div>
<div class="col-6">
<div class="form-group"> 	
<label for="PathLive">Live Path</label><br>
<input type="text" name="PathLive" id="PathLive" class="form-control" value="<?= h($course[44]) ?>">
<label for="PathNIK">NIK Path</label><br>
<input type="text" name="PathNIK" id="PathNIK" class="form-control" value="<?= h($course[45]) ?>">
</div>
</div>


</div>
	
<button class="btn btn-block btn-primary my-3">Save Course Info</button>
</form>
	
</div>
</div>
</div>



<?php else: ?>


<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-3">


	<p class="my-3 p-3" style="background: #000">This is a restricted tool. Please email <a href="mailto:Learning.Centre.Admin@gov.bc.ca?subject=Service Request Access Request">Learning Centre Operations</a> with your IDIR to request access.</p>

	
	</div>
</div>
</div>






<?php endif ?>
<?php endif ?>
<?php else: ?>
<?php require('templates/noaccess.php') ?>
<?php endif ?>




<?php require('templates/javascript.php') ?>
<!-- <script src="/lsapp/js/summernote-bs4.js"></script> -->
<script>
$(document).ready(function(){
	var moment = rome.moment;
	var endtime = rome(document.querySelector('.endtime'), { 
						date: false,
						timeValidator: function (d) {
							var m = moment(d);
							var start = m.clone().hour(07).minute(59).second(59);
							var end = m.clone().hour(16).minute(30).second(1);
							return m.isAfter(start) && m.isBefore(end);
						}
				});
	var starttime = rome(document.querySelector('.starttime'), { 
						date: false,
						timeValidator: function (d) {
							var m = moment(d);
							var start = m.clone().hour(07).minute(59).second(59);
							var end = m.clone().hour(16).minute(00).second(1);
							return m.isAfter(start) && m.isBefore(end);
						}
				});

	// $('.summernote').summernote({
	// 	toolbar: [
	// 		// [groupName, [list of button]]
	// 		['style', ['bold', 'italic']],
	// 		['para', ['ul', 'ol']],
	// 		['color', ['color']],
	// 		['link']
	// 	],
	// 	placeholder: 'Type here'
		
	// });	

	

	$('#CourseName').keyup(function () {
	  var max = 254;
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#CNLNum').text(' you have reached the limit');
	  } else {
		var char = max - len;
		$('#CNLNum').text(char + ' characters left');
	  }
	});

	$('#CourseShort').keyup(function () {
	  var max = 11;
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#CNSNum').text(' you have reached the limit');
	  } else {
		var char = max - len;
		$('#CNSNum').text(char + ' characters left');
	  }
	});	
	$('#CourseDescription').keyup(function () {
	  var max = 254;
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#CDNum').text(' you have reached the limit');
	  } else {
		var char = max - len;
		$('#CDNum').text(char + ' characters left');
	  }
	});
	$('#CourseAbstract').keyup(function () {
	  var max = 4000;
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#CANum').text(' you have reached the limit');
	  } else {
		var char = max - len;
		$('#CANum').text(char + ' characters left');
	  }
	});		
	
	
});
</script>
<?php require('templates/footer.php') ?>