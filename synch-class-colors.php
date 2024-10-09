<?php

require('inc/lsapp.php');	

$coursescsv = fopen('data/courses.csv','r');
$temp_courses = fopen('data/courses-temp.csv','w');
$courseheaders = fgetcsv($coursescsv);
fputcsv($temp_courses,$courseheaders);

while (($data = fgetcsv($coursescsv)) !== FALSE){

	$coursecats = explode(',',$data[20]);
	$catdeets = getCategory($coursecats[0]);
	
	$data[32] = $catdeets[2];
	
	fputcsv($temp_courses,$data);

}
fclose($coursescsv);
fclose($temp_courses);

rename('data/courses-temp.csv','data/courses.csv');

echo 'Updated<br>';