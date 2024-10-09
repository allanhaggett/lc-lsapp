<?php 
require('inc/lsapp.php');

if($_POST): 

$fromform = $_POST;
$commentid = $fromform['commentid'];
$reqid = $fromform['reqid'];
$f = fopen('data/changes-course-comments.csv','r');
$temp_table = fopen('data/changes-course-comments-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $commentid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/changes-course-comments-temp.csv','data/changes-course-comments.csv');

header('Location: course-change-view.php?changeid=' . $reqid);

endif;
