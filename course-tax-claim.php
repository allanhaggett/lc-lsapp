<?php
require('inc/lsapp.php');
if(isAdmin()):
	$cid = $_GET['cid'];
	$user = LOGGED_IN_IDIR;
	$f = fopen('data/courses.csv','r');
	$temp_table = fopen('data/courses-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $cid) {		
			$data[49] = $user;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);

	rename('data/courses-temp.csv','data/courses.csv');
	echo $user;
	//header('Location: /lsapp/courses.php?sort=dateadded');
else:
	include('templates/noaccess.php');
endif;