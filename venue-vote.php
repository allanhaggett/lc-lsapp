<?php
require('inc/lsapp.php');
if(canAccess()):
	$venueid = $_GET['venueid'];
	$updown = (isset($_GET['updown'])) ? $_GET['updown'] : 'up';
	$f = fopen('data/venues.csv','r');
	$temp_table = fopen('data/venues-temp.csv','w');
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $venueid) {
			if($data[13] > 0) {
				if($updown == 'down') {
					$data[13] = $data[13] - 1;
				} else {
					$data[13] = $data[13] + 1;
				}
			} else {
				if($updown == 'down') {
					$data[13] = 0;
				} else {
					$data[13] = 1;
				}
			}
		}
		fputcsv($temp_table,$data);
	}
	fclose($f);
	fclose($temp_table);
	rename('data/venues-temp.csv','data/venues.csv');
	$go = 'Location: venue.php?vid=' . $venueid;
	header($go);
else:
	include('templates/noaccess.php');
endif;