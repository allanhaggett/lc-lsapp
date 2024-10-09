<?php require('inc/lsapp.php') ?>
<?php 
if($_POST): 
$functionid = $_POST['functionid'];
$idir = $_POST['idir'];
$f = fopen('data/functional-map-people.csv','r');
$temp_table = fopen('data/functional-map-people-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $idir && $data[1] == $functionid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/functional-map-people-temp.csv','data/functional-map-people.csv');

$go = 'Location: function-map.php?functionid=' . $_POST['functionid'];
header($go);

endif;