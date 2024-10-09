<?php
require('inc/lsapp.php');
if(isAdmin()) {
	$changeid = $_GET['changeid'];
	$classid = $_GET['classid'];
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$f = fopen('data/changes-class.csv','r');
	$temp_table = fopen('data/changes-class-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	//creqID,ClassID,CourseName,StartDate,City,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $changeid) {
			$data[7] = 'Completed';
			$data[8] = $user;
			$data[9] = date('Y-m-d H:i');
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/changes-class-temp.csv','data/changes-class.csv');
	//header('Location: /lsapp/class.php?classid=' . $classid);
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	
} else {
	include('templates/noaccess.php');
}