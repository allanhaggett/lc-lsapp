<?php 

require('inc/lsapp.php');
if(isAdmin()):
	$fromform = $_POST;
	// creqID,ClassID,Date,NotedBy,Note
	$requestor = LOGGED_IN_IDIR;
	$now = date('Y-m-d H:i:s');
	//$noteID = LOGGED_IN_IDIR . '-' . date('Ymd-His');
	$noteID = date('Ymd-His');
	$newinquiry = Array(
					$noteID,
					h($fromform['ClassID']),
					$now,
					$requestor,
					h($fromform['Note'])
				);
		

	$inquiry = array($newinquiry);
	$fp = fopen('data/notes-booking.csv', 'a+');
	foreach ($inquiry as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
	header('Location: /lsapp/class.php?classid=' . $fromform['ClassID']);
else:
	require('template/noaccess.php');
endif;

