<?php require('inc/lsapp.php') ?>
<?php $idir = (isset($_GET['idir'])) ? $_GET['idir'] : 0; ?>

<?php getHeader() ?>
<title>All change requests completed by <?= $idir ?></title>
<?php getScripts() ?>
<?php getNavigation() ?>

<?php if(canAccess()): ?>


<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8">

<h1>Change Requests Completed By <?= $idir ?></h1>

<?php 
$u = fopen('data/changes-class.csv', 'r');
// Remove the headers
fgetcsv($u);
?>
<?php //creqID,ClassID,CourseName,StartDate,City,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request,RequestType,Response ?>
<?php while ($row = fgetcsv($u)): ?>
<?php if($row[8] === $idir): ?>
	<div>
	<a target="_blank" href="class.php?classid=<?= $row[1] ?>"><?= $row[2] ?> - <?= $row[3] ?></a>
	</div>
<?php endif ?>
<?php endwhile ?>
<?php fclose($u) ?>

</div>
</div>
</div>


<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>


<?php require('templates/javascript.php') ?>
<?php include('templates/footer.php') ?>