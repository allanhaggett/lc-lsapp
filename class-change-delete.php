<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$reqid = $fromform['reqID'];
$classid = $fromform['ClassID'];
$f = fopen('data/changes-class.csv','r');
$temp_table = fopen('data/changes-class-temp.csv','w');
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

rename('data/changes-class-temp.csv','data/changes-class.csv');

header('Location: class.php?classid=' . $classid);

endif;
endif;