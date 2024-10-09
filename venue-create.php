<?php 
require('inc/lsapp.php');
if(canAccess()):

	$fromform = $_POST;
	$venueid = date('YmdHis');

	// 0-VenueID,1-VenueName,2-ContactName,3-BusinessPhone,4-Address,5-City,6-StateProvince,7-ZIPPostal,
	// 8-email,9-Notes,10-Active,11-Union,12-Region,13-Votes
	
	$newvenue = Array($venueid,
					h($fromform['VenueName']),
					h($fromform['ContactName']),
					h($fromform['BusinessPhone']),
					h($fromform['Address']),
					h($fromform['City']),
					h($fromform['StateProvince']),
					h($fromform['ZIPPostal']),
					h($fromform['email']),
					h($fromform['Notes']),
					'Suggested',
					'No',
					'',
					''
		);
	$venue = array($newvenue);
	$fp = fopen('data/venues.csv', 'a+');
	foreach ($venue as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
	header('Location: /lsapp/venue.php?vid=' . $venueid);
else:
	include('templates/noaccess.php');
endif;