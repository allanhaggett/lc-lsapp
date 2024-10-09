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
fgetcsv($elm);

$classesbackup = 'data/classes.csv';
$newfile = 'data/classes'.date('Ymd\THis').'.csv';

if (!copy($classesbackup, $newfile)) {
    echo "Failed to copy $file...\n";
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
?>

<?php getHeader() ?>

<title>Upload PUBLIC.GBC_CURRENT_COURSE_INFO</title>

<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
</div>
<div class="col-md-8">
<ul class="list-group">
<?php if(isAdmin()): ?>
<?php 
// loop through each line of the elm file
while ($elmrow = fgetcsv($elm)) {
	
	$lsappcount = 0;
	// Loop through each of the LSApp classes and do an 
	// array_search on the ELM item code on each
	foreach($lsappclasses as $lsappclass) {
		
		$key = array_search($elmrow[1], $lsappclass);
		// If there's a match, then update lsappclass fields 
		// with elmrow values
		if($key) {
			echo '<li class="list-group-item">' . $lsappclass[6] . '<br>' . $lsappclass[7] . ' updated.<br> ';
			if($elmrow[8] != $lsappclass[18]) {
				echo 'ELM Enrolled: ' . $elmrow[8] . ' | <strong>LSApp Enrolled: ' . $lsappclass[18] . '</strong>';
			} else {
				echo 'ELM Enrolled: ' . $elmrow[8] . ' | LSApp Enrolled: ' . $lsappclass[18] . '';
			}
			echo '</li>';
			$lsappclasses[$lsappcount][1] = $elmrow[5]; // status
			$lsappclasses[$lsappcount][18] = $elmrow[8]; // enrolled
			$lsappclasses[$lsappcount][19] = $elmrow[9]; // Reserved
			$lsappclasses[$lsappcount][20] = $elmrow[10]; // Pending
			$lsappclasses[$lsappcount][21] = $elmrow[11]; // Waitlist
			$lsappclasses[$lsappcount][22] = $elmrow[12]; // Dropped
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
</div>
</div>

<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>