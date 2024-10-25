<?php
require('inc/lsapp.php');
if(isAdmin()) {
	$changeid = $_GET['changeid'];
	$courseid = $_GET['courseid'];
	$user = LOGGED_IN_IDIR;
	$f = fopen('data/changes-course.csv','r');
	$temp_table = fopen('data/changes-course-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	// 0-creqID,1-CourseID,2-CourseName,3-DateRequested,4-RequestedBy,5-Status,
	// 6-CompletedBy,7-CompletedDate,8-Request
	// 9-RequestType,10-AssignedTo,11-Urgency
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $changeid) {
			//$data[5] = 'Completed';
			$data[10] = $user;
			//$data[7] = date('Y-m-d H:i');
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/changes-course-temp.csv','data/changes-course.csv');
	header('Location: /lsapp/course.php?courseid=' . $courseid);
} else {
	include('templates/noaccess.php');
}