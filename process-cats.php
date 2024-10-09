<?php
require('inc/lsapp.php');	
$classcsv = fopen('data/classes.csv','r');
$temp_classes = fopen('data/classes-temp.csv','w');
$classheaders = fgetcsv($classcsv);
fputcsv($temp_classes,$classheaders);

while (($data = fgetcsv($classcsv)) !== FALSE){

	$courseid = $data[5];
	$coursedeets = getCourse($courseid);
	
	$data[46] = $coursedeets[20];
	
	fputcsv($temp_classes,$data);

}
fclose($classcsv);
fclose($temp_classes);

rename('data/classes-temp.csv','data/classes.csv');

echo 'Updated<br>';