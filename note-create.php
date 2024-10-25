<?php 

require('inc/lsapp.php');

$fromform = $_POST;
// creqID,ClassID,Date,NotedBy,Note
$requestor = LOGGED_IN_IDIR;
$now = date('Y-m-d H:i:s');
$noteID = LOGGED_IN_IDIR . '-' . date('Ymd-His');
$newchange = Array(
				$noteID,
				h($fromform['ClassID']),
				$now,
				$requestor,
				h($fromform['Note'])
			);
	

$change = array($newchange);
$fp = fopen('data/notes.csv', 'a+');
foreach ($change as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);

//$go = 'Location: ' . $_SERVER['HTTP_REFERER'];
$go = 'Location: /lsapp/class.php?classid=' . $fromform['ClassID'];
header($go);
?>