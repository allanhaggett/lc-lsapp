<?php
require('inc/lsapp.php');	
$classcsv = fopen('data/classes.csv','r');
$temp_classes = fopen('data/classes-temp.csv','w');
$classheaders = fgetcsv($classcsv);
fputcsv($temp_classes,$classheaders);

while (($data = fgetcsv($classcsv)) !== FALSE){

	$starttime = '';
	$endtime = '';	
	$classtimes = strtolower($data[10]);
	$times = explode(' - ', $classtimes);

	if(isset($times[1])) {
		
		$amcheck = explode('am',$times[0]);
		if(isset($amcheck[1])) {
			$starttime = $amcheck[0];
		} else {
			$starttime = $times[0];
		}
		$pmcheck = explode('pm',$times[1]);
		if(isset($pmcheck[1])) {
			$endtime = $pmcheck[0];
		} else {
			$endtime = $times[1];
		}
		$starttime = date("Hi", strtotime($starttime));
		$endtime = date("Hi", strtotime($endtime));
	} 
	$data[54] = $starttime;
	$data[55] = $endtime;
	fputcsv($temp_classes,$data);

}
fclose($classcsv);
fclose($temp_classes);

rename('data/classes-temp.csv','data/classes.csv');

echo 'Updated<br>';