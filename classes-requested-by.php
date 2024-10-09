<?php require('inc/lsapp.php') ?>
<?php $idir = (isset($_GET['idir'])) ? $_GET['idir'] : 0; ?>

<?php getHeader() ?>
<title>All Service Requests by <?= $idir ?></title>
<?php getScripts() ?>
<?php getNavigation() ?>

<?php if(canAccess()): ?>


<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8">

<h1>All Service Requests by <?= $idir ?></h1>
<ul class="list-group">
<?php 
$u = fopen('data/classes.csv', 'r');
// Remove the headers
fgetcsv($u);
?>
<?php //creqID,ClassID,CourseName,StartDate,City,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request,RequestType,Response ?>
<?php while ($row = fgetcsv($u)): ?>
<?php if($row[3] === $idir): ?>
	<li class="list-group-item">
		<a target="_blank" href="class.php?classid=<?= $row[0] ?>">
			<?= $row[6] ?> - <?php echo goodDateLong($row[8]) ?> - <?= $row[25] ?>
		</a>
	</li>
<?php endif ?>
<?php endwhile ?>
<?php fclose($u) ?>
</ul>
</div>
</div>
</div>


<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>


<?php require('templates/javascript.php') ?>
<?php include('templates/footer.php') ?>