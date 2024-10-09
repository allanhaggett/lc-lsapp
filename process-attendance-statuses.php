<?php
//
// This runs through the output of the GBC_CURRENT_COURSE_INFO ELM query,
// matches the ITEM Code with one in LSApp, and then updates LSApp with the 
// current ELM status and attendance numbers for that class.
// This is _ridiculously_ server intensive as it is currently written, 
// and is only supposed to be run once a day during off hours when demand is low.
//
// TODO HIGH PRIORITY to rewrite this to be less intensive and scary 
//
//
// THIS IS THE WORST CODE I'VE EVER WRITTEN!!! _WHAT WAS I THINKING!??!_
//
//
require('inc/lsapp.php');
$f = fopen('data/elm.csv', 'r');
// Pop the headers row off
fgetcsv($f);
$count = 0;
while ($row = fgetcsv($f)) {
	
	//echo $row[1] . ' | ';
	// This ELM class, is there a class in LSApp with the same item code?
	$check = getClassByItemCode($row[1]);
	// $check 18-Enrolled,19-ReservedSeats,20-PendingApproval,21-Waitlisted,22-Dropped
	// $row 8,9,10,11,12
	// if there is an LSApp class with the same item code, we're going to update it
	// with the enrollment numbers and status from ELM
	if(sizeof($check)>0) {
		
		$classcsv = fopen('data/classes.csv','r');
		$temp_classes = fopen('data/classes-temp.csv','w');
		$classheaders = fgetcsv($classcsv);
		fputcsv($temp_classes,$classheaders);

		while (($data = fgetcsv($classcsv)) !== FALSE){
			
			// if LSApp item code equals ELM item code
			if($data[7] == $row[1]) {
				
				$data[1] = $row[5]; // status
				$data[18] = $row[8]; // enrolled
				$data[19] = $row[9]; // Reserved
				$data[20] = $row[10]; // Pending
				$data[21] = $row[11]; // Waitlist
				$data[22] = $row[12]; // Dropped
			}
			fputcsv($temp_classes,$data);
		}
		fclose($classcsv);
		fclose($temp_classes);
		
		rename('data/classes-temp.csv','data/classes.csv');
		
		//echo 'Matched and updated<br>';
		
	} else {
		//echo '<strong>No match</strong><br>';
	}
	$count++;	
}
// Close elm.csv 
fclose($f);

header('Location: /lsapp/admin.php?message=ELM+Synchronized');
