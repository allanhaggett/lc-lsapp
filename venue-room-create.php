<?php 
require('inc/lsapp.php');

	$fromform = $_POST;
	$roomid = date('YmdHis');
	$venueid = h($fromform['VenueID']);

	//RoomID,VenueID,RoomName,RoomCapacity,RoomDimensions,RoomNotes,Likes
	
	$newroom = Array($roomid,
					$venueid,
					h($fromform['RoomName']),
					h($fromform['RoomCapacity']),
					h($fromform['RoomDimensions']),
					h($fromform['RoomNotes']),
					0
		);
	$room = array($newroom);
	$fp = fopen('data/venue-rooms.csv', 'a+');
	foreach ($room as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
	header('Location: venue.php?vid=' . $venueid);
