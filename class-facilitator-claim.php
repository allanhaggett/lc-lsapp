<?php
require('inc/lsapp.php');
if(canAccess()):
	$cid = $_GET['cid'];
	$unclaim = $_GET['unclaim'];
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$notadmin = 0;
	$f = fopen('data/classes.csv','r');
	$temp_table = fopen('data/classes-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $cid) {		
			if($unclaim == 'unclaim') { 
				$u = $data[14] . '';
				$newu = str_replace($user,'',$u);
				$data[14] = trim($newu);
			} else {
				$data[14] = trim($data[14]) . ' ' . trim($user);
			}
			$data[42] = date('Y-m-d-Hi');
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);

	rename('data/classes-temp.csv','data/classes.csv');
	if($unclaim == 'unclaim') {

		//echo 'Unknown';
	} else {

		echo $user;

	}

	//header('Location: /lsapp/class.php?classid=' . $cid);	
	
else:
	include('templates/noaccess.php');
endif;