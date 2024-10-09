<?php 
require('inc/lsapp.php');
if(canAccess()):

	$fromform = $_POST;
	$eid = date('YmdHis');

	$newvenue = Array($eid,
					h($fromform['email'])
		);
	$venue = array($newvenue);
	$fp = fopen('data/external-mailing-list.csv', 'a+');
	foreach ($venue as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
	header('Location: external-mailing-list.php');
else:
	include('templates/noaccess.php');
endif;