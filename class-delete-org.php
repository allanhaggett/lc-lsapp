<?php
require('inc/lsapp.php');
$cid = $_GET['classid'];
$user = LOGGED_IN_IDIR;
$notuser = 0;
$f = fopen('data/classes.csv','r');
$temp_table = fopen('data/classes-temp.csv','w');
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
while (($data = fgetcsv($f)) !== FALSE){
	if($data[0] == $cid) {
		if($data[3] == $user || isAdmin() && $data[1] != 'Active' && $data[1] != 'Closed') {
			continue;
		} else {
			$notuser = 1;
		}
	}
	fputcsv($temp_table,$data);
}
fclose($f);
fclose($temp_table);

if($notuser) {
	echo 'You cannot delete that. Contact ' . $data[2] . ' or the LSA Team to discuss.';
} else {
	rename('data/classes-temp.csv','data/classes.csv');
	header('Location: /lsapp/');
}