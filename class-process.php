<?php
require('inc/lsapp.php');
if(isAdmin()):
	$classid = $_GET['classid'];
	$item = $_GET['itemcode'];
	$user = LOGGED_IN_IDIR;
	$f = fopen('data/classes.csv','r');
	$temp_table = fopen('data/classes-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $classid) {
			$data[1] = 'Active';
			$data[7] = $item;
			$data[42] = date('Y-m-d');
			$data[43] = $user;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/classes-temp.csv','data/classes.csv');
	header('Location: /lsapp/class.php?classid=' . $classid);
else:
	include('templates/noaccess.php');
endif;