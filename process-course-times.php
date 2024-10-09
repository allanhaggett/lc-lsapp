<?php
require('inc/lsapp.php');	
$classcsv = fopen('data/courses.csv','r');
$temp_classes = fopen('data/courses-temp.csv','w');
$classheaders = fgetcsv($classcsv);
fputcsv($temp_classes,$classheaders);

while (($data = fgetcsv($classcsv)) !== FALSE){

	$starttime = '';
	$endtime = '';	
	$classtimes = strtolower($data[5]);
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
		$starttime = date("H:i", strtotime($starttime));
		$endtime = date("H:i", strtotime($endtime));
	} 
	$data[] = $starttime;
	$data[] = $endtime;
	fputcsv($temp_classes,$data);

}
fclose($classcsv);
fclose($temp_classes);

rename('data/courses-temp.csv','data/courses.csv');

echo 'Updated<br>';