<?php 

require('inc/lsapp.php');
if(canAccess()):
	$fromform = $_POST;
	
	$requestor = LOGGED_IN_IDIR;
	$now = date('Y-m-d H:i:s');
	$noteID = date('YmdHis');
	$newnote = Array($noteID,
					h($fromform['VenueID']),
					$now,
					$requestor,
					h($fromform['Note'])
				);
	$fp = fopen('data/notes-venue.csv', 'a+');
	fputcsv($fp, $newnote);
	fclose($fp);
	header('Location: /lsapp/venue.php?vid=' . $fromform['VenueID']);
else:
	require('template/noaccess.php');
endif;

