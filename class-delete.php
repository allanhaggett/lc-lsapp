<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$classid = $fromform['classid'];
$user = stripIDIR($_SERVER["REMOTE_USER"]);

$f = fopen('data/classes.csv','r');
$temp_table = fopen('data/classes-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $classid) {
		// no nothin' deleeeets it
		// But WAIT, we don't want to delete things, do we?
		// FIRST we need to check to see if there are any change requests for the class
		// If there are requests, deleting this will orphan them
		$data[1] = 'Deleted';
		fputcsv($temp_table,$data);
	} else {
		// this is all messed up if($data[1] != 'Active' && $data[1] != 'Closed') {
			fputcsv($temp_table,$data);
		//}
	}
}
fclose($f);
fclose($temp_table);

rename('data/classes-temp.csv','data/classes.csv');

header('Location: index.php?message=Class+deleted');

else:
	echo 'What are you doing, Dave?';
endif;
endif;