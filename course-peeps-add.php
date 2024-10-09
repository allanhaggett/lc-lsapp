<?php
echo 'already ran this.'; exit; 
require('inc/lsapp.php');
if(isAdmin()):
	$classid = $_GET['classid'];
	$newstatus = $_GET['status'];
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$courses = fopen('data/courses.csv','r');
	$cpeeps = fopen('data/course-people.csv','w');



	while (($data = fgetcsv($courses)) !== FALSE){
		$cid = $data[0];
		$dev = $data[34];
		$stew = $data[10];
		// CourseID,Role,IDIR,date
		if(!empty($dev)) {
			$peepdata = [$cid,'dev',$dev,'2024-04-09'];
			fputcsv($cpeeps,$peepdata);
		}
		if(!empty($stew)) {
			$peepdata = [$cid,'steward',$stew,'2024-04-09'];
			fputcsv($cpeeps,$peepdata);
		}
	}
	fclose($courses);
	fclose($cpeeps);

	echo 'Done.';

else:
	include('templates/noaccess.php');
endif;

// echo 'already ran this.'; exit; 
// require('inc/lsapp.php');
// if(isAdmin()):
// 	$classid = $_GET['classid'];
// 	$newstatus = $_GET['status'];
// 	$user = stripIDIR($_SERVER["REMOTE_USER"]);
// 	$courses = fopen('data/courses.csv','r');
// 	$cpeeps = fopen('data/course-people.csv','w');



// 	while (($data = fgetcsv($courses)) !== FALSE){
// 		$cid = $data[0];
// 		$dev = $data[34];
// 		$stew = $data[10];
// 		// CourseID,Role,IDIR,date
// 		if(!empty($dev)) {
// 			$peepdata = [$cid,'dev',$dev,'2024-04-09'];
// 			fputcsv($cpeeps,$peepdata);
// 		}
// 		if(!empty($stew)) {
// 			$peepdata = [$cid,'steward',$stew,'2024-04-09'];
// 			fputcsv($cpeeps,$peepdata);
// 		}
// 	}
// 	fclose($courses);
// 	fclose($cpeeps);

// 	echo 'Done.';

// else:
// 	include('templates/noaccess.php');
// endif;