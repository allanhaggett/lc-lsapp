<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$idir = $fromform['idir'];

$f = fopen('data/people.csv','r');
$temp_table = fopen('data/people-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $idir) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/people-temp.csv','data/people.csv');


header('Location: people.php');

endif;
endif;