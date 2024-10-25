<?php
require('inc/lsapp.php');
if(isAdmin()):
	$cid = $_POST['cid'];
	$action = $_POST['action'];
	$matid = $_POST['matid'];
	$newstock = $_POST['InStock'];
	if(isset($_POST['Restock'])) {
		$restock = $_POST['Restock'];
	} else {
		$restock = FALSE;
	}
	$user = LOGGED_IN_IDIR;
	$notadmin = 0;
	$f = fopen('data/materials.csv','r');
	$temp_table = fopen('data/materials-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	//0-MaterialID,1-CourseName,2-CourseID,3-MaterialName,4-PerCourse,5-InStock,6-Partial,7-Restock,8-Notes,9-FileName -->
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $matid) {
			$data[5] = $newstock;
			$data[7] = $restock;
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/materials-temp.csv','data/materials.csv');
	if($action == 'materials') {
		header('Location: /lsapp/materials.php');
	} else {
		header('Location: /lsapp/class.php?classid=' . $cid);
	}
else: 
	echo "<p>You don't have permission to do this, sorry. Contact the Operations Team Lead if you think this is in error.</p>";
endif;