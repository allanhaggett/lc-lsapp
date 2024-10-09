<?php
//
// This runs through the output of the GBC_CURRENT_COURSE_INFO ELM query,
// matches the ITEM Code with one in LSApp, and then updates LSApp with the 
// current ELM status and attendance numbers for that class.
//
//
require('inc/lsapp.php');

$elm = fopen('data/elm.csv', 'r');
// Pop the headers row off
$elmheaders = fgetcsv($elm);

$classesbackup = 'data/classes.csv';
$newfile = 'data/backups/classes'.date('Ymd\THis').'.csv';
if (!copy($classesbackup, $newfile)) {
    echo "Failed to backup $newfile...\nPlease inform the Team Lead ASAP";
	exit;
}
$coursesbackup = 'data/courses.csv';
$newcoursefile = 'data/backups/courses'.date('Ymd\THis').'.csv';
if (!copy($coursesbackup, $newcoursefile)) {
    echo "Failed to backup $newcoursefile...\nPlease inform the Team Lead ASAP";
	exit;
}

$lsapp = fopen('data/classes.csv', 'r');
// Pop the headers row off and save them so when we rewrite this file below,
// we use them to start the new file
$lsappheaders = fgetcsv($lsapp);
$lsappclasses = array();
while ($row = fgetcsv($lsapp)) {
	array_push($lsappclasses,$row);
}
fclose($lsapp); 
$updatedcount = 0;
?>

<?php getHeader() ?>

<title>Upload PUBLIC.GBC_CURRENT_COURSE_INFO</title>

<style>
.upcount {
	font-size: 30px;
	margin: 30px 0;
}
</style>
<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
</div>
<div class="col-md-6">
<h2>ELM - LSApp Enrolment Number Synchronize</h2>
<div class="alert alert-success">Synchronization Completed</div>
<ul class="list-group">
<?php if(isAdmin()): ?>
<?php 
// loop through each line of the elm file
while ($elmrow = fgetcsv($elm)) {
	// Reset lsappcount after every loop 
	$lsappcount = 0;
	// Loop through each of the LSApp classes and do an 
	// array_search on the ELM item code on each
	foreach($lsappclasses as $lsappclass) {
		// Look through the single class row to see if the ITEM code matches
		if($elmrow[1] == $lsappclass[7]) {
			// If there's a difference between ELM and LSApp, then we 
			// update LSApp accordingly and output the updated class 
			// to the screen. If there's no difference, we just move on
			$newEnrolled = intval($elmrow[8]) + intval($elmrow[16]);
			if($newEnrolled != $lsappclass[18] || 
				$elmrow[9] != $lsappclass[19] || 
				$elmrow[10] != $lsappclass[20] || 
				$elmrow[11] != $lsappclass[21] || 
				$elmrow[12] != $lsappclass[22]) {
					
				$updatedcount++;
				echo '<li class="list-group-item">';
				//echo '<div class="upcount float-left">' . $updatedcount . '</div>';
				echo '<a href="class.php?classid=' . $lsappclass[0] . '">';
				echo '<strong>' . $lsappclass[6] . '</strong><br>';
				echo goodDateLong($lsappclass[8],$lsappclass[9]) . '<br>';
				echo $lsappclass[7] . ' UPDATED.';
				echo '</a>';
				echo '<div class="alert alert-warning">';
				echo 'ELM Enrolled/In-Progress: ' . $newEnrolled . ' | ';
				if($newEnrolled != $lsappclass[18]) {
					echo '<strong>LSApp Enrolled: ' . $lsappclass[18] . '</strong><br>';
				} else {
					echo 'LSApp Enrolled: ' . $lsappclass[18] . '<br>';
				}
				echo 'ELM Reserved: ' . $elmrow[9] . ' | ';
				if($elmrow[9] != $lsappclass[19]) {
					echo '<strong>LSApp Reserved: ' . $lsappclass[19] . '</strong><br>';
				} else {
					echo 'LSApp Reserved: ' . $lsappclass[19] . '<br>';
				}
				echo 'ELM Pending: ' . $elmrow[10] . ' | ';
				if($elmrow[10] != $lsappclass[20]) {
					echo '<strong>LSApp Pending: ' . $lsappclass[20] . '</strong><br>';
				} else {
					echo 'LSApp Pending: ' . $lsappclass[20] . '<br>';
				}
				echo 'ELM Waitlist: ' . $elmrow[11] . ' | ';
				if($elmrow[11] != $lsappclass[21]) {
					echo '<strong>LSApp Waitlist: ' . $lsappclass[21] . '</strong><br>';
				} else {
					echo 'LSApp Waitlist: ' . $lsappclass[21] . '<br>';
				}
				echo 'ELM Dropped: ' . $elmrow[12] . ' | ';
				if($elmrow[12] != $lsappclass[22]) {
					echo '<strong>LSApp Dropped: ' . $lsappclass[22] . '</strong><br>';
				} else {
					echo 'LSApp Dropped: ' . $lsappclass[22] . '<br>';
				}
				echo '</div>';
				echo '</li>';
				
				$lsappclasses[$lsappcount][1] = $elmrow[5]; // status
				$lsappclasses[$lsappcount][18] = intval($elmrow[8]) + intval($elmrow[16]); // enrolled + in-progress
				$lsappclasses[$lsappcount][19] = $elmrow[9]; // Reserved
				$lsappclasses[$lsappcount][20] = $elmrow[10]; // Pending
				$lsappclasses[$lsappcount][21] = $elmrow[11]; // Waitlist
				$lsappclasses[$lsappcount][22] = $elmrow[12]; // Dropped
				
			}
		} 
		$lsappcount++;
	}
	
}
// Close elm.csv 
fclose($elm);

// Now write lsappclass back to classes.csv in one go
// Open the elm.csv file; note the 'w' as we're opening the file, 
// removing all its existing content, and starting the write at the beginning of the file
$newclasses = fopen('data/classes.csv', 'w');
// Add the headers
fputcsv($newclasses, $lsappheaders);
// Now loop through the $newelm array created above and write each line to the file
foreach ($lsappclasses as $fields) {
	fputcsv($newclasses, $fields);
}
// Close the file
fclose($newclasses);

endif; ?>
</ul>
</div>
<div class="col-md-4">
<h2><span class="badge text-bg-dark"><?= $updatedcount ?></span> Updated.</h2>
</div>
</div>
</div>

<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php') ?>
