<?php
//echo 'This ain ready for prime time yet. Check back.'; exit;
require('inc/lsapp.php');	

function getCourseID ($itemcode) {
	$hubcsv = fopen('course-feed/data/courses.csv','r');
	$elmcourseid = '';
	$count = 0;
	while (($data = fgetcsv($hubcsv)) !== FALSE){
		$count++;
		//echo $count . '-' .$data[0] . '<br>';
		if($data[0] == $itemcode) {
			//$elmcourseid = $h[13];
			$elmcourseid = $data[13];
		}
	}
	fclose($hubcsv);
	return $elmcourseid;
}

$coursecsv = fopen('data/courses.csv','r');
$temp_courses = fopen('data/courses-temp.csv','w');
$courseheaders = fgetcsv($coursecsv);
fputcsv($temp_courses,$courseheaders);

while (($data = fgetcsv($coursecsv)) !== FALSE){

	$elmcourseid = getCourseID($data[4]);
	
	$data[50] = $elmcourseid;
	
	fputcsv($temp_courses,$data);

}

fclose($coursecsv);
fclose($temp_courses);

rename('data/courses-temp.csv','data/courses.csv');

echo 'Updated<br>';