<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$mid = $fromform['MaterialID'];
$courseid = $fromform['CourseID'];
$f = fopen('data/materials.csv','r');
$temp_table = fopen('data/materials-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $mid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/materials-temp.csv','data/materials.csv');


header('Location: course.php?courseid=' . $courseid);

endif;
endif;