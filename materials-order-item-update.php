<?php require('inc/lsapp.php') ?>
<?php 
if(isAdmin()):
if($_POST): 

$fromform = $_POST;
$f = fopen('data/materials-order-items.csv','r');
$temp_table = fopen('data/materials-order-items-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
	
$orderid = h($fromform['OrderID']);
$modified = date('Y-m-d');
$modifiedby = stripIDIR($_SERVER["REMOTE_USER"]);
$matid = h($fromform['MaterialID']);

//OrderID,MaterialID,MaterialName,MaterialQTY,MaterialDetails,PerCourse,CurrentQTY
$order = Array($orderid,
			$matid,
			h($fromform['MaterialName']),
			h($fromform['MaterialQTY']),
			h($fromform['MaterialDetails']),
			h($fromform['PerCourse']),
			h($fromform['CurrentQTY']));

while (($data = fgetcsv($f)) !== FALSE){
	
	if($data[0] == $orderid) {
		if($data[1] == $matid) {
			fputcsv($temp_table,$order);
		} else {
			fputcsv($temp_table,$data);
		}
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);

rename('data/materials-order-items-temp.csv','data/materials-order-items.csv');

header('Location: materials-order.php?orderid=' . $orderid);?>


<?php endif ?>


<?php else: ?>
<?php require('templates/noaccess.php') ?>
<?php endif ?>