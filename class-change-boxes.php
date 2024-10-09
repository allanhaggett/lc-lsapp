<?php
require('inc/lsapp.php');
if(isAdmin()):
	$cid = $_GET['cid'];
	$boxes = $_GET['boxes'];
	$courier = $_GET['courier'];
	$trackingout = $_GET['TrackingOut'];
	$trackingin = $_GET['TrackingIn'];
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$notadmin = 0;
	$f = fopen('data/classes.csv','r');
	$temp_table = fopen('data/classes-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $cid) {		
			$data[42] = date('Y-m-d-Hi');
			$data[34] = $boxes;
			$data[36] = $courier;
			$data[37] = $trackingout;
			$data[38] = $trackingin;
			$data[43] = $user;
			$data[44] = $user;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);

	rename('data/classes-temp.csv','data/classes.csv');
	//echo $user;
	header('Location: class-labels.php?classid=' . $cid);
else:
	include('templates/noaccess.php');
endif;