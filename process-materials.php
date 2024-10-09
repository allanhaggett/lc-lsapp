<?php
require('inc/lsapp.php');	
$classcsv = fopen('data/materials.csv','r');
$temp_classes = fopen('data/materials-temp.csv','w');
$classheaders = fgetcsv($classcsv);
fputcsv($temp_classes,$classheaders);

while (($data = fgetcsv($classcsv)) !== FALSE){
	
	fputcsv($temp_classes,$data);
}
fclose($classcsv);
fclose($temp_classes);

rename('data/materials-temp.csv','data/materials.csv');

echo 'Updated<br>';