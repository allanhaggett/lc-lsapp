<?php

require('inc/lsapp.php');
// Get the full course list
$courses = getCourses();
// Pop the headers off the top
array_shift($courses);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the name to
// the temp array
foreach($courses as $line) {
	$tmp[] = $line[2];
}
// Sort the whole kit and kaboodle by name
array_multisort($tmp, SORT_ASC, $courses);
$lsappcourses = [];
foreach($courses as $c) {
	// The LSApp course has had it's taxonomy updated and is active and ready
	if($c[48] == 1 && $c[1] == 'Active') {
		array_push($lsappcourses,$c);
	}
}

$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\course-feed\data\courses.csv';
$hc = fopen($path, 'r');
fgetcsv($hc);
$hubcourses = [];
while ($row = fgetcsv($hc)) {
	array_push($hubcourses,$row);
}
fclose($hc);

function getNewHubTax ($courseid) {

	$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\data\hub-courses-newtax.csv';
	$hct = fopen($path, 'r');
	fgetcsv($hct);
	$hubctax = [];
	while ($row = fgetcsv($hct)) {
		if($courseid == $row[0]) {
			$hubctax = $row;
		}
	}
	fclose($hct);
	return $hubctax;
}


$topics = getAllTopics();
$levels = getLevels ();
$user = stripIDIR($_SERVER["REMOTE_USER"]);

?>
<?php getHeader() ?>
<title>Taxonomy!</title>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<style>
.newannouce {
	display: none;
}
thead {
	background-color: #FFF;
}
</style>

<?php getScripts() ?>

<body>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row">

<div class="col-12">
	<h1>ELM Taxonomy Update Management Dashboard</h1>
</div>
</div>
<p>There are <?= count($hubcourses) ?> courses in the LearningHUB Sync that also reside within ELM.</p>
<details class="my-3 p-3">
	<summary>Details</summary>
	<div class="row mt-3">
		<div class="col-12">
			<p>While this doesn't cover all of the courses that need to have new taxnomy terms applied,
				it covers "corporate learning courses" that the Learning Centre and its partners manage.</p>
			<p>It's a place to start.</p>
		</div>
		<div class="col-4">
			<p>Includes courses that are:</p>
			<ul>
				<li>In ELM.
				<li>Has a Learning Partner keyword assigned.
				<li>Has active classes (for blended and classroom)
				<li>Considered Corporate Learning
			</ul>
		</div>
		<div class="col-4">
			<p>Does NOT include courses that are:</p>
			<ul>
				<li>Not in ELM. LearningHUB has ~50 courses that point to other systems.
				<li>Courses that don't have a partner assigned and are not included in the hub.
			</ul>
		</div>
		<div class="col-6">
			<p>Within ELM, there are two taxonomies: "Categories" and "Keywords."<p>
			<p>We are intending to put both "Groups" and "Topics" into ELM "Categories."</p>
			<p>Each Topic will be its own top-level category in ELM, using the category shortname of "topic" or "group" to 
				differentiate on the scripting side.</p>
		</div>
	</div>
</details>
<div class="row">
<div class="col-5">

	<?php 
	$lsappcount = 0;
	$lsappinelm = []; 
	$lsappnotinelm = []; 
	$lsapplcbutnotinelm = []; 
	$lsappnotinhub = []; 
	$not = 1;
	$lll = 0;
	?>
	<?php 
	foreach($hubcourses as $hc) {
		$not = 1;
		foreach($lsappcourses as $c) {
			if(strtolower($hc[0]) == strtolower($c[4])) {
				array_push($c,$hc[13]);
				array_push($lsappinelm,$c);
				$lsappcount++;
				$not = 0;
			}
		}
		if($not == 1) {
			array_push($lsappnotinelm,$hc);
		}
	}
	foreach($lsappcourses as $c) {
		foreach($hubcourses as $hc) {
			if($hc[0] != $c[4]) {
				array_push($lsappnotinhub,$c);
			}
		}
	}
	?>
	<!-- <div>Of these <?= count($hubcourses) ?> courses, there are</div> -->
	<h2 class="mt-0"><?= count($lsappnotinelm) ?> courses NOT in LSApp</h2>
	<style>
		.lc {
			font-size: 1.1em;
			font-weight: bold;
		}
		</style>
		
		<div>
<?php foreach($lsappnotinelm as $c): ?>
	<?php 
	$lc = 'notlc'; 
	if($c[12] == 'Learning Centre') { 
		$lc = 'lc'; $lll++; 
	} 
	?>
	<div class="mb-2 p-2 bg-light-subtle <?= $lc ?>">
		<a href="https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_COURSESTRUCTURE.LM_CI_LA_CMP.GBL?LM_CI_ID=<?= $c[13] ?>" target="_blank">
			<?= $c[1] ?>
		</a>
		<div>
			<?= $c[12] ?> <?= $c[0] ?> <?= $c[3] ?> <?= $c[13] ?>
		</div>
		<div>
			<?php $tax = getNewHubTax($c[13]) ?>
			<!-- Delivery: <?= $tax[1] ?>
			Group: <?= $tax[2] ?>
			Topic: <?= $tax[3] ?>
			Processed By: <?= $tax[4] ?> -->
		</div>

			<form method="post" action="/lsapp/hubcourse-new-tax.php" class="pb-3">
			<input type="hidden" name="CourseID" value="<?= h($c[0]) ?>">
			
			<div class="row">
			<div class="col-3">
			<select name="Levels" id="Levels" class="form-control">
				<option disabled selected>Select Group</option>
			<?php foreach($levels as $l): ?>
			<?php if($tax[2] == $l): ?>
			<option selected><?= $l ?></option>
			<?php else: ?>
			<option><?= $l ?></option>
			<?php endif ?>
			<?php endforeach ?>
			</select>

			</div>
			<div class="col-4">
			<select name="Topics" id="Topics" class="form-control">
			<option disabled selected>Select Topic</option>
			<?php foreach($topics as $t): ?>
			<?php if($tax[3] == $t): ?>
			<option selected><?= $t ?></option>
			<?php else: ?>
			<option><?= $t ?></option>
			<?php endif ?>
			<?php endforeach ?>
			</select>
			</div>
			<div class="col-3">
				<input type="text" name="ProcessedBy" id="processedby" value="<?= $user ?>" class="form-control">
			</div>
			<div class="col-1">
			<button class="btn btn-light bg-light-subtle">Save</button>
			</div>
			</div>
			</form>
		
	</div>
<?php endforeach ?>
</div>
</div>
<div class="col-5">
<?php foreach($lsapplcbutnotinelm as $lcc): ?>
	<li>
		<?= $lcc[2] ?> 
</li>
<?php endforeach ?>
<!-- <div>Of these <?= count($hubcourses) ?> courses, there are</div> -->
<h2 class="mt-0"><?= $lsappcount ?> courses in LSApp in LH sync</h2>
<div class="mt-2">
<?php foreach($lsappinelm as $c): ?>
	<div class="mb-2 p-2 bg-light-subtle">
		<div><?= $c[2] ?></div>
		
		<div>
			<a href="https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_COURSESTRUCTURE.LM_CI_LA_CMP.GBL?LM_CI_ID=<?= $c[50] ?>" target="_blank">
				ELM Edit
			</a>
			<a href="/lsapp/course.php?courseid=<?= $c[0] ?>">LSApp</a>
			<?= $c[36] ?> 
			<?= $c[4] ?> 
			| <?= $c[50] ?> | <?= $c[51] ?> 
		</div>
		<div>
			<a class="badge bg-light-subtle " href="courses.php?delivery=<?= $c[21] ?>">
					<?= $c[21] ?>
			</a>
			<a class="badge bg-light-subtle " href="courses.php?level=<?= urlencode($c[40]) ?>"><?= $c[40] ?></a>
			<a class="badge bg-light-subtle " href="courses.php?topic=<?= urlencode($c[38]) ?>"><?= $c[38] ?></a>
		</div>
	</div>
<?php endforeach ?>
</div>
</div>
<!-- <div class="col-2">
<div class="mt-2">
<div><?php echo count($lsappcourses) - $lsappcount - $lll ?> courses in LSApp but not in LH sync.</div>
<div><strong><?= $lll ?> eLearning courses in LH sync owned by LC but not in LSApp.</strong></div>
</div>
</div> -->
</div>
</div>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>

<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php'); ?>