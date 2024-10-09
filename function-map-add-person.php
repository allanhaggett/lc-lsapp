<?php require('inc/lsapp.php') ?>

<?php if(canAccess()): ?>

<?php
$addperson = [
    h($_POST['idirs']),
    h($_POST['functionid'])
];
$fp = fopen('data/functional-map-people.csv', 'a+');
fputcsv($fp, $addperson);
fclose($fp);
$go = 'Location: function-map.php?functionid=' . $_POST['functionid'];
header($go);
?>

<?php else: ?>

<?php require('templates/noaccess.php'); ?>

<?php endif ?>

