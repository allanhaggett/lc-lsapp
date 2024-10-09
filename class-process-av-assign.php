<?php
require('inc/lsapp.php');
if(isAdmin()):
	$classid = $_GET['classid'];
	$avid = $_GET['avid'];
	$action = '';
	if(isset($_GET['action'])) {
		$action = $_GET['action'];
	}
	if($action) {
		$classid = '';
	}
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$f = fopen('data/audio-visual.csv','r');
	$temp_table = fopen('data/av-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $avid) {
			$data[1] = $classid;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/av-temp.csv','data/audio-visual.csv');

	//
	// Update the class date itself with a record of the assigned AV for historical 
	// purposes. When you "assign" AV to a class, it records the classid with the AV
	// (in audio-visual.csv file), but that gets "unassigned" when things get returned. 
	// In order to keep a record that we sent a case with a class, we also write it here.
	//
	
	$f = fopen('data/classes.csv','r');
	$temp_table = fopen('data/classes-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		$existing = $data[51];
		if($data[0] == $classid) {
			$data[51] = $existing . ',' . $avid;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/classes-temp.csv','data/classes.csv');
	header('Location: /lsapp/class.php?classid=' . $_GET['classid']);
	
	
	
else:
	include('templates/noaccess.php');
endif;


