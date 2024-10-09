<?php require('inc/lsapp.php') ?>
<?php 
if(isSuper()):
if($_POST): 

$fromform = $_POST;
$avid = $fromform['avid'];

$f = fopen('data/audio-visual.csv','r');
$temp_table = fopen('data/audiovisual-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $avid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/audiovisual-temp.csv','data/audio-visual.csv');


header('Location: av-dashboard.php');

endif;
endif;