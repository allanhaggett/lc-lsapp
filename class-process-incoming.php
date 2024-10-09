<?php
opcache_reset();
require('inc/lsapp.php');
if(isAdmin()):
if($_POST):
	
	$ShipDate = (isset($_POST['ShipDate'])) ? $_POST['ShipDate'] : 0;
	$Shipper = (isset($_POST['Shipper'])) ? $_POST['Shipper'] : 0;
	$Boxes = (isset($_POST['Boxes'])) ? $_POST['Boxes'] : 0;
	$Weight = (isset($_POST['Weight'])) ? $_POST['Weight'] : 0;
	$Courier = (isset($_POST['Courier'])) ? $_POST['Courier'] : 0;
	$TrackingOut = (isset($_POST['TrackingOut'])) ? $_POST['TrackingOut'] : 0;
	$TrackingIn = (isset($_POST['TrackingIn'])) ? $_POST['TrackingIn'] : 0;
	$PickupIn = (isset($_POST['PickupIn'])) ? $_POST['PickupIn'] : 0;
	$ShippingStatus = (isset($_POST['ShippingStatus'])) ? $_POST['ShippingStatus'] : 0;
	$CheckedBy = (isset($_POST['CheckedBy'])) ? $_POST['CheckedBy'] : 0;
	
	$classid = $_POST['classid'];
	
	$user = stripIDIR($_SERVER["REMOTE_USER"]);
	$f = fopen('data/classes.csv','r');
	$temp_table = fopen('data/classes-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $classid) {
			
			$data[13] = $ShipDate;
			$data[33] = $Shipper;
			$data[34] = $Boxes;
			$data[35] = $Weight; 
			$data[36] = $Courier; 
			$data[37] = $TrackingOut;
			$data[38] = $TrackingIn;
			$data[50] = $PickupIn;
			$data[49] = $ShippingStatus;
			$data[48] = $CheckedBy;
			
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/classes-temp.csv','data/classes.csv');
	//echo 'Good job.';
	//header('Location: /lsapp/shipping.php');
else:
	echo 'You no get. You post.';
endif;
else:
	include('templates/noaccess.php');
endif;