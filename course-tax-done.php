<?php
require('inc/lsapp.php');
if(isAdmin()):
	$cid = $_GET['cid'];
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$f = fopen('data/courses.csv','r');
	$temp_table = fopen('data/courses-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $cid) {		
			$data[48] = $user;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);

	rename('data/courses-temp.csv','data/courses.csv');
	echo 'Course update completed. Thank you!';
	
	//header('Location: /lsapp/courses.php?sort=dateadded');
else:
	include('templates/noaccess.php');
endif;