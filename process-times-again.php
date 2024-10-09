<?php
require('inc/lsapp.php');	
$classcsv = fopen('data/classes.csv','r');
$temp_classes = fopen('data/classes-temp.csv','w');
$classheaders = fgetcsv($classcsv);
fputcsv($temp_classes,$classheaders);

while (($data = fgetcsv($classcsv)) !== FALSE){

	$starttime = $data[54];
	$endtime = $data[55];	
	
	if($starttime == '0830') {
		$starttime = '08:30';
	} elseif($starttime == '0900') {
		$starttime = '09:00';
	}
	if($endtime == '0430') {
		$endtime = '16:30';
	} elseif($endtime == '1630') {
		$endtime = '16:30';
	}
	$data[54] = $starttime;
	$data[55] = $endtime;
	fputcsv($temp_classes,$data);

}
fclose($classcsv);
fclose($temp_classes);

rename('data/classes-temp.csv','data/classes.csv');

echo 'Updated<br>';