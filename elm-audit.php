<?php
opcache_reset();

// Standard dates for each type:
function WeekBefore($date) {
	$weekbefore = date("d/m/Y", strtotime($date . ' - 7 days'));
	return $weekbefore;
}

function DayBefore($date) {
	$daybefore = date("d/m/Y", strtotime($date . ' - 1 days'));
}
//
// This runs through the output of the GBC_CURRENT_COURSE_INFO ELM query
// (which is produced by ELM, but then processed into elm.csv to put all
// enrollment numbers onto one line; see elm-sync-process-gbc.php for details)
// matches the ITEM Code with one in LSApp, and if it doesn't exist in LSApp,
// or it exists but there's some discrepancy between the two, then we show
// the details on the page for further admin investigation
//
//
require('inc/lsapp.php');

$elm = fopen('data/elm.csv', 'r');
// Pop the headers row off
fgetcsv($elm);

// Open up the courses file and read it into an array so that we can quickly 
// iterate through the array instead of open/closing the file for each check
$lsappcourses = fopen('data/courses.csv', 'r');
$courselist = array();
while ($row = fgetcsv($lsappcourses)) {
	array_push($courselist,$row);
}
fclose($lsappcourses);

// Open up the classes file and read it into an array so that we can quickly 
// iterate through the array instead of open/closing the file for each check
$lsapp = fopen('data/classes.csv', 'r');
$lsappclasses = array();
while ($row = fgetcsv($lsapp)) {
	array_push($lsappclasses,$row);
}
fclose($lsapp); 

$today = date('Y-m-d');
$lastupdated = date ("Y-m-d", filemtime('data/elm.csv'));

getHeader(); 

?>

<title>ELM - LSApp Audit Tool</title>
<style>
.upcount {
	font-size: 30px;
	margin: 30px 0;
}
</style>

<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
</div>
<div class="col-md-4">
<h2>ELM - LSApp Audit Tool</h2>
<?php if($today != $lastupdated): ?>
<div class="alert alert-warning">
It doesn't look like LSapp has been 
<a href="elm-sync-upload.php">synchronized</a> with ELM today.
You should do that before trusting the info below.
</div>
<?php endif ?>
<p>This tool compares the classes scheduled in ELM with the classes
scheduled in LSApp, showing any classes that are not scheduled in LSApp,
while also showing any classes that are in LSApp, but which have differing 
status, course name, start date, city, or venue information.</p>
<p><strong>Currently auditing:</strong> start date, delivery method, facility (not yet included),
status, min enroll, and max enroll.</p>
<!--<p>The class ITEM code may not exist in LSApp, but maybe the course does. 
If the course is not in LSApp, then chances are we're not responsible for it,
and you can safely ignore its presence in the list.</p>-->

</div>
<div class="col-md-12">
<table class="table table-sm table-striped">
<thead>
<tr>
	<th>Item Code</th>
	<th>Status &amp; Delivery</th>
	<th>Course</th>
	<th>Start Date</th>
	<th>Venue</th>
	<th>Enrolment</th>
	<th>Last enrol date</th>
</tr>
</thead>
<tbody class="list">
<?php if(isAdmin()): ?>
<?php 
// loop through each line of the elm file
while ($elmrow = fgetcsv($elm)) {
	// Reset all the variable values to 0 on each loop
	$foundcount = 0;
	$coursefound = 0;
	$discrepancy = 0;
	$classid = 0;
	$status_discrepancy = 0;
	$startdate_discrepancy = 0;
	$deliverymethod_discrepancy = 0;
	$minenroll_discrepancy = 0;
	$maxenroll_discrepancy = 0;
	$enrolldate_discrepancy = 0;

	// Loop through each of the LSApp classes and do an 
	// array_search on the ELM item code on each
	foreach($lsappclasses as $lsappclass) {
		// Does the item code from the ELM file 
		// match this classes item code?
		if($elmrow[1] == trim($lsappclass[7])) {
			// Increment the foundcount counter so that 
			// when we test for it below it is greater than zero 
			// and we can tell whether we should display it or not
			$foundcount++;
			// do comparisons between ELM and LSApp to see if there 
			// are any differences if there are differences we flag them
			// Check on: 
			// "Course Name",Class,"Start Date",Type,Facility,
			// "Class Status","Min Enroll","Max Enroll"
			// Reminder date,
			// last enrol date, 
			// last waitlist enrol date, and 
			// last drop date
			// to the query and check them against the calculated standards
			// 
			// Status
			if($elmrow[5] != $lsappclass[1]) {
				$discrepancy++;
				$status_discrepancy = $lsappclass[1];
			}
			// Start Date
			if($elmrow[2] != $lsappclass[8]) {
				$discrepancy++;
				$startdate_discrepancy = $lsappclass[8];
			}
			// Type
			if($elmrow[3] != $lsappclass[45]) {
				$discrepancy++;
				$deliverymethod_discrepancy = $lsappclass[45];
			}
			// min_enroll
			if($elmrow[6] != $lsappclass[11]) {
				$discrepancy++;
				$minenroll_discrepancy = $lsappclass[11];
			}
			// max_enroll
			if($elmrow[7] != $lsappclass[12]) {
				$discrepancy++;
				$maxenroll_discrepancy = $lsappclass[12];
			}
			

			// Last enroll date
			
			
			if($elmrow[19] != WeekBefore($elmrow[2])) {
				$discrepancy++;
				$enrolldate_discrepancy = WeekBefore($elmrow[2]);
			}
			if($discrepancy) $classid = $lsappclass[0];
			
		} 
		
	}
	// Loop through each LSApp course and check to see if the course name
	// matches anything in our list; if there's a match, we make the course name 
	// a link to it's page on LSApp
	foreach($courselist as $course) {
		if($elmrow[0] == $course[2]) {
			$coursefound = $course[0];
		}
	}
	// if the class isn't found in LSApp, or there is a discrepancy in any of the inspected
	// fields, then we show it. We don't show anything if it's a total match.
	if($foundcount === 0 || $discrepancy) {

		if($coursefound) {
			$courselink = '<a href="course.php?courseid='.$coursefound.'">' . $elmrow[0] . '</a>';
		} else {
			$courselink = $elmrow[0];
		}
		echo '<tr>';
		echo '<td>' . $elmrow[1] . '</td>';
		echo '<td>';
		if($status_discrepancy) {
			echo '<span class="badge badge-light">' . $elmrow[5] . '</span> <br>';
			echo '<span class="badge badge-danger">LSApp: ' . $status_discrepancy . '</span> ';
		} else {
			echo '<span class="badge badge-light">' . $elmrow[5] . '</span> ';
		}

		if($deliverymethod_discrepancy) {
			echo '<span class="badge badge-light">' . $elmrow[3] . '</span> <br>';
			echo '<span class="badge badge-danger">LSApp: ' . $deliverymethod_discrepancy . '</span> ';
		} else {
			echo '<span class="badge badge-light">' . $elmrow[3] . '</span> ';
		}
		echo '</td>';
		echo '<td>';
		echo $courselink . ' ';
		if($coursefound && !$discrepancy) {
			//echo '<form method="post" action="class-create.php">';
			//echo '<input type="hidden" name="CourseCode" id="CourseCode" value="'.$coursefound.'">';
			//echo '<input type="button" class="btn btn-sm btn-success" value="Import into LSApp">';
			//echo '</form>';
		}
		echo '</td>';
		echo '<td>';
		if($discrepancy) {
			echo '<a href="class.php?classid=' . $classid . '">' . goodDateShort($elmrow[2]) . '</a>';
			if($startdate_discrepancy) { 
				echo '<br><span class="badge badge-danger">LSApp: ' . goodDateShort($startdate_discrepancy) . '</span>';
			}
		} else {
			echo goodDateShort($elmrow[2]) . '';
		}
		echo '</td>';
		echo '<td>';
		echo '' . $elmrow[4]. ' - ';
		echo '</td>';
		echo '<td>';
		echo '<span class="badge badge-light">Min: ' . $elmrow[6] . '</span><br>';
		if($minenroll_discrepancy) {
			echo '<span class="badge badge-danger">LSApp Min: ' . $minenroll_discrepancy . '</span><br>';
		}
		echo '<span class="badge badge-light">Max: ' . $elmrow[7] . '</span> ';
		if($maxenroll_discrepancy) {
			echo '<br><span class="badge badge-danger">LSApp Max: ' . $maxenroll_discrepancy . '</span> ';
		} else {
			
		}
		echo '</td>';
		echo '<td>';
		if($enrolldate_discrepancy) {
			echo '<span class="badge badge-light">ELM: ' . $elmrow[19] . '</span> <br>';
			echo '<span class="badge badge-danger">' . $enrolldate_discrepancy . '</span> ';
		}
		echo '</td>';
		echo '</tr>';
	}
	
}
// Close elm.csv 
fclose($elm);

endif; ?>
</tbody>
</table>
</div>

</div>
</div>

<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>