<?php 
require('inc/lsapp.php');
$fromform = $_POST;
//commentID,creqID,CourseID,CourseName,created,Comment,Commenter
$commenter = stripIDIR($_SERVER["REMOTE_USER"]);
$now = date('Y-m-d H:i:s');
$commentID = stripIDIR($_SERVER["REMOTE_USER"]) . '-' . date('Ymd-His');
$newcomment = Array(
				$commentID,
				h($fromform['creqID']),
				h($fromform['CourseID']),
				h($fromform['CourseName']),
				$now,
				h($fromform['Comment']),
				$commenter
			);

$fp = fopen('data/changes-course-comments.csv', 'a+');
fputcsv($fp, $newcomment);
fclose($fp);
header('Location: /lsapp/course-change-view.php?changeid=' . $fromform['creqID']);
