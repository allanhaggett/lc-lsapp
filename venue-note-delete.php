<?php require('inc/lsapp.php') ?>
<?php 
if(canAccess()):
if($_POST): 

$fromform = $_POST;
$noteid = $fromform['noteid'];
$venueid = $fromform['venueid'];

$f = fopen('data/notes-venue.csv','r');
$temp_table = fopen('data/notes-venue-temp.csv','w');
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

rename('data/notes-venue-temp.csv','data/notes-venue.csv');


header('Location: venue.php?vid=' . $venueid);

endif;
endif;