<?php require('inc/lsapp.php') ?>
<?php 
if(isAdmin()):
if($_POST): 

$fromform = $_POST;
$orderid = $fromform['OrderID'];
$f = fopen('data/materials-orders.csv','r');
$temp_table = fopen('data/materials-orders-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $orderid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/materials-orders-temp.csv','data/materials-orders.csv');




$f = fopen('data/materials-order-items.csv','r');
$temp_table = fopen('data/materials-order-items-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
				
while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $orderid) {
		// no nothin' deleeeets it
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/materials-order-items-temp.csv','data/materials-order-items.csv');


header('Location: materials.php');

endif;
endif;