<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$reqid = $fromform['reqID'];
$courseid = $fromform['CourseID'];
$f = fopen('data/changes-course.csv','r');
$temp_table = fopen('data/changes-course-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $reqid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/changes-course-temp.csv','data/changes-course.csv');

header('Location: course.php?courseid=' . $courseid);

endif;
endif;