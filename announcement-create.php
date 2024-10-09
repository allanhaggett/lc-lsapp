<?php 
require('inc/lsapp.php');
if(isAdmin()):
$fromform = $_POST;
// creqID,ClassID,Date,NotedBy,Note
$author = stripIDIR($_SERVER["REMOTE_USER"]);
$now = date('Y-m-d H:i:s');
$blogID = stripIDIR($_SERVER["REMOTE_USER"]) . '-' . date('Ymd-His');
$newblog = Array($blogID,$now,$author,h($fromform['Body']));
$fp = fopen('data/announcements.csv', 'a+');
fputcsv($fp, $newblog);
fclose($fp);
header('Location: /lsapp/');
else:
include('templates/noaccess.php');
endif;