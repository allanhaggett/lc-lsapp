<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$noteid = $fromform['NoteID'];
$classid = $fromform['ClassID'];
$f = fopen('data/notes.csv','r');
$temp_table = fopen('data/notes-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $noteid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/notes-temp.csv','data/notes.csv');


header('Location: class.php?classid=' . $classid);

endif;
endif;