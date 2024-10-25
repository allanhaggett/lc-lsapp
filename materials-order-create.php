<?php

require('inc/lsapp.php');
if(isAdmin()):

$fromform = $_POST;
$orderid = date('YmdHis');
$created = date('Y-m-d:His');
$today = date('Y-m-d');

$twoweeksout = date("Y-m-d", strtotime($today . ' + 14 days'));

// MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,8-Notes,FileName,ProjectNumber,Responsibility,ServiceLine,STOB
//OrderID,MaterialID,MaterialName,MaterialQTY,MaterialDetails

$matline = array();
foreach($fromform['material'] as $mat) {
	$matdeets = getMaterial($mat);
	$newmat = array($orderid,$mat,$matdeets[3],0,$matdeets[8],$matdeets[4],$matdeets[5]);
	array_push($matline,$newmat);
}

$m = fopen('data/materials-order-items.csv', 'a+');
foreach ($matline as $fields) {
    fputcsv($m, $fields);
}
fclose($m);


// OrderID,Status,Created,CreatedBy,Modified,ModifiedBy,CourseID,CourseName,Cost,
// DateOrdered,DateArrived,Notes,FilePath,QuotedBy,SigningAuthority,PONumber,ConsigneeFile,PreviousStatus

$orderid = date('YmdHis');
$created = date('YmdHis');
$createdby = LOGGED_IN_IDIR;
$courseid = h($fromform['CourseID']);
$neworder = Array($orderid,
				'Draft',
				$created,
				$createdby,
				$created,
				$createdby,
				$courseid,
				h($fromform['CourseName']),
				0,
				$today,
				$twoweeksout,
				'',
				'',
				'',
				'',
				'',
				'',
			  'Draft');	
$fp = fopen('data/materials-orders.csv', 'a+');
fputcsv($fp, $neworder);
fclose($fp);
header('Location: /lsapp/materials-order.php?orderid=' . $orderid);

else:

include('templates/noaccess.php');

endif;
