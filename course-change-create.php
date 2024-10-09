<?php 

require('inc/lsapp.php');

$fromform = $_POST;
//creqID,CourseID,CourseName,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request,RequestType,AssignedTo,Priority
$requestor = stripIDIR($_SERVER["REMOTE_USER"]);
$now = date('Y-m-d H:i:s');
$creqID = stripIDIR($_SERVER["REMOTE_USER"]) . '-' . date('Ymd-His');
$newchange = Array(
				$creqID,
				h($fromform['CourseID']),
				h($fromform['CourseName']),
				$now,
				$requestor,
				'Pending',
				'',
				'',
				h($fromform['ChangeRequest']),
				h($fromform['RequestType']),
				h($fromform['AssignedTo']),
				h($fromform['Priority'])
			);
	

$change = array($newchange);
$fp = fopen('data/changes-course.csv', 'a+');
foreach ($change as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
header('Location: /lsapp/course.php?courseid=' . $fromform['CourseID']);
?>