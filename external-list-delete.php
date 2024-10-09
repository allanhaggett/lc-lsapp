<?php require('inc/lsapp.php') ?>
<?php 
if(canAccess()):
if($_POST): 

$fromform = $_POST;
$emailid = $fromform['eid'];


$f = fopen('data/external-mailing-list.csv','r');
$temp_table = fopen('data/list-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $emailid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/list-temp.csv','data/external-mailing-list.csv');

header('Location: external-mailing-list.php');

endif;
endif;