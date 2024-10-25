<?php 
require('inc/lsapp.php');
if(isAdmin()):
$fromform = $_POST;
// creqID,ClassID,Date,NotedBy,Note
$author = LOGGED_IN_IDIR;
$now = date('Y-m-d H:i:s');
$blogID = LOGGED_IN_IDIR . '-' . date('Ymd-His');
$newblog = Array($blogID,$now,$author,h($fromform['Body']));
$fp = fopen('data/blog.csv', 'a+');
fputcsv($fp, $newblog);
fclose($fp);
header('Location: /lsapp/');
else:
include('templates/noaccess.php');
endif;