<?php
opcache_reset();
require('inc/lsapp.php');
//
// https://learning.gov.bc.ca/psc/CHIPSPLM_1/EMPLOYEE/ELM/q/?ICAction=ICQryNameURL=PUBLIC.GBC_CURRENT_COURSE_INFO
//
// The GBC_CURRENT_COURSE_INFO query produces a CSV file that has multiple lines per class, oriented 
// around the "Enrollment Status" field; each line contains identical class information from the 
// previous line, except for the bit at the end. One line will have the 
// "No. Learners" for the "Enrolled" status, while the next will have the number for "Denied"
// For example, for a given ITEM-CODE, there will be a line present for each of the "Enrollment Status" 
// that have a number present. If nobody has been "Denied" for that course, there will be no line in the file;
// conversely, if there are 5 people "Pending Approval" then there will be a line for it.
// 
// Example bit from a GBC_CURRENT_COURSE_INFO.csv
//"Course Name","Class","Start Date","End Date","Type","Facility","Class Status","Min Enroll","Max Enroll","Enrollment Status","Reserved Seats","No. Learners","Enroll Date","Drop Date","Reminder Date","Last Wtlst Date"
//"Building Bridges Through Understanding the Village","ITEM-1027-10","14/01/2020","14/01/2020","Classroom","Kelowna, BC - TBD","Active","15","35","Denied","0","1","07/01/2020","13/01/2020","07/01/2020","10/01/2020"
//"Building Bridges Through Understanding the Village","ITEM-1027-10","14/01/2020","14/01/2020","Classroom","Kelowna, BC - TBD","Active","15","35","Dropped","0","9","07/01/2020","13/01/2020","07/01/2020","10/01/2020"
//"Building Bridges Through Understanding the Village","ITEM-1027-10","14/01/2020","14/01/2020","Classroom","Kelowna, BC - TBD","Active","15","35","Enrolled","0","35","07/01/2020","13/01/2020","07/01/2020","10/01/2020"
//"Building Bridges Through Understanding the Village","ITEM-1027-10","14/01/2020","14/01/2020","Classroom","Kelowna, BC - TBD","Active","15","35","Pending Approval","0","8","07/01/2020","13/01/2020","07/01/2020","10/01/2020"
//
// For handy reference:
// -----------------------
// GBC_CURRENT_COURSE_INFO.csv headers w/index numbers as produced by ELM query of the same name:
//
// [0] => Course Name [1] => Class [2] => Start Date [3] => End Date [4] => Type [5] => Facility 
// [6] => Class Status [7] => Min Enroll [8] => Max Enroll 
// [9] => Enrollment Status [10] => Reserved Seats [11] => No. Learners 
// [12] => Enroll Date [13] => Drop Date [14] => Reminder Date [15] => Last Wtlst Date
// -----------------------
//

// Setup variables
$code = '';
$lastcode = '';
$newelm = array();
$tempnew = array();

$name = '';
$class = '';
$start_date = '';
$end_date = '';
$type = '';
$facility = '';
$city = '';
$status = '';
$min = 0;
$max = 0;
$datenroll = '';
$datedrop = '';
$dateremind = '';
$datewait = '';

$enrolled = 0;
$pending = 0;
$reserved = 0;
$pendingapproval = 0;
$waitlisted = 0;
$dropped = 0;
$denied = 0;
$completed = 0;
$notcompleted = 0;
$inprogress = 0;
$planned = 0;
$waived = 0;

$count = 0;

//
// Let's start by opening the file. We are running this script immediately after this file has
// been uploaded; uploads-controller.php handles uploading the files, then redirects here to 
// process the raw CSV file from ELM. HIstorically (pre-April 1 2019), this transformation has been performed by 
// Randy Dzenkiw's VBA macro via Excel.
//
$f = fopen('data/GBC_CURRENT_COURSE_INFO.csv', 'r');

// Pop the headers row off so we're starting the loop on a data row

fgetcsv($f);
// Start looping through each line of the CSV,
// When this has gone through every line, the resulting $newelm array is then ready to be read back,
// line-by-line re-writing the elm.csv file that existed before
//
while ($row = fgetcsv($f)) {
	// Set the code so that we can compare it with the last loop's code
	$code = $row[1];
	// if this is the first line, then the code won't equal
	// the $lastcode, so we don't want to perform the check
	if($count > 0) {
		// If this line's code doesn't equal the last line's code
		// then we're at a new course, and so we write the previous 
		// line to the new array, while resetting all the enrollment
		// variables to 0
		if($code != $lastcode) {
			$tempnew = array($name,
							$lastcode,
							$start_date,
							$type,
							$facility,
							$status,
							$min,
							$max,
							$enrolled,
							$reserved,
							$pending,
							$waitlisted,
							$dropped,
							$denied,
							$completed,
							$notcompleted,
							$inprogress,
							$planned,
							$waived,
							$datenroll,
							$datedrop,
							$dateremind,
							$datewait,
							$city);
			
			array_push($newelm,$tempnew);
			$enrolled = 0;
			$pending = 0;
			$reserved = 0;
			$pendingapproval = 0;
			$waitlisted = 0;
			$dropped = 0;
			$denied = 0;
			$completed = 0;
			$notcompleted = 0;
			$inprogress = 0;
			$planned = 0;
			$waived = 0;
		}
	} 
	
	// Assign current row values to all the things.
	$name = $row[0];
	$start_date = elmMakeSane($row[2]);
	$end_date = $row[3];
	$type = $row[4];
	$facility = $row[5];
	$city = $row[16];
	$status = $row[6];
	$min = $row[7];
	$max = $row[8];
	$reserved = $row[10];
	
	$datenroll = $row[12];
	$datedrop = $row[13];
	$dateremind = $row[14];
	$datewait = $row[15];
	
	// This where things start to change on a line-to-line basis.
	$enrollmentstatus = $row[9];
	$numoflearners = $row[11];
	
	//
	// Above, we test to see if this is a new class item code. Regardless of the test, we still want to record
	// values from this row into the necessary variables.
	// As we loop through each line, we test to see which "Enrollment Status" line we're on, and record
	// its number in the corresponding variable. As we loop through, we're building up the variables
	// so where the code changes, we can write all of them to the same line at once.
	//
	if($enrollmentstatus == 'Enrolled') {
		$enrolled = $numoflearners;
	} elseif ($enrollmentstatus == 'Pending Approval') {
		$pending = $numoflearners;
	} elseif ($enrollmentstatus == 'Waitlisted') {
		$waitlisted = $numoflearners;
	} elseif ($enrollmentstatus == 'Dropped') {
		$dropped = $numoflearners;
	} elseif ($enrollmentstatus == 'Denied') {
		$denied = $numoflearners;
	} elseif ($enrollmentstatus == 'Completed') {
		$completed = $numoflearners;
	} elseif ($enrollmentstatus == 'Not Completed') {	
		$notcompleted = $numoflearners;
	} elseif ($enrollmentstatus == 'In-Progress') {
		$inprogress = $numoflearners;
	} elseif ($enrollmentstatus == 'Planned') {
		$planned = $numoflearners;
	} elseif ($enrollmentstatus == 'Waived') {
		$waived = $numoflearners;
	}
	
	// Now that we're at the end of the loop, we can assign the lastcode variable
	// as the current code. At the very beginning we overwrite $code with the current
	// line's code, but this variable remains the same; this allows us to check if we're
	// on a new item code ...
	$lastcode = $code;
	// Increment the counter so that we know that we're past the first line 
	$count++;
	
}

// Close GBC_CURRENT_COURSE_INFO.csv 
fclose($f);

// Create the header row for the new elm.csv file
// TODO just reuse them from the first lines!! 
$headers = array("Course Name",
					"Class",
					"Start Date",
					"Type",
					"Facility",
					"Class Status",
					"Min Enroll",
					"Max Enroll",
					"Enrolled",
					"Reserved Seats",
					"Pending Approval",
					"Waitlisted",
					"Dropped",
					"Denied",
					"Completed",
					"Not Completed",
					"In-Progress",
					"Planned",
					"Waived",
					"Enroll Date",
					"Drop Date",
					"Reminder Date",
					"Last Waitlist Date",
					"City");
					
// "Enroll Date","Drop Date","Reminder Date","Last Wtlst Date"
// Open the elm.csv file; note the 'w' as we're opening the file, 
// removing all its existing content, and starting the write at the beginning of the file
$fp = fopen('data/elm.csv', 'w');
// Add the headers
fputcsv($fp, $headers);
// Now loop through the $newelm array created above and write each line to the file
foreach ($newelm as $fields) {
	fputcsv($fp, $fields);
}
// Close the file
fclose($fp);
// Redirect to sync the numbers from ELM with LSApp
header('Location: elm-sync-process.php');
