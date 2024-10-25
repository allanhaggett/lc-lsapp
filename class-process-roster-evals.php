<?php
require('inc/lsapp.php');
if(isAdmin()):
	$classid = $_POST['classid'];
	if(isset($_POST['AttendanceReturned'])) {
		$roster = $_POST['AttendanceReturned'];
	}
	if(isset($_POST['EvaluationsReturned'])) {
		$evals = $_POST['EvaluationsReturned'];
	}
	if(isset($_POST['ELMStatus'])) {
		$elm = $_POST['ELMStatus'];
	}
	$user = LOGGED_IN_IDIR;
	$f = fopen('data/classes.csv','r');
	$temp_table = fopen('data/classes-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $classid) {
			if(isset($elm) == 'on') {
				$data[1] = 'Closed';
			}
			if(isset($roster)) {
				$data[39] = $roster;
			}
			if(isset($evals)) {
				$data[40] = $evals;
			}
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/classes-temp.csv','data/classes.csv');
	header('Location: /lsapp/shipping.php');
else:
	include('templates/noaccess.php');
endif;