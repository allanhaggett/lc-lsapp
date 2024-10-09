<?php require('inc/lsapp.php') ?>

<?php if(canACcess()): ?>
<?php 
$cityname = (isset($_GET['name'])) ? $_GET['name'] : 0;
$venues = getVenues($cityname);
?>
<?php getHeader() ?>

<title><?= h($cityname) ?></title>

<?php getScripts() ?>
<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-4">
<h1><?= h($cityname) ?></h1>
<ul class="list-group">
<?php //VenueID,VenueName,ContactName,BusinessPhone,Address,City,
//StateProvince,ZIPPostal,email,Notes,Active,Union,Region ?>
<?php foreach($venues as $venue): ?>
<?php if($venue[5] == $cityname): ?>
<li class="list-group-item"><a href="venue.php?vid=<?= $venue[0]?>"><?= $venue[1] ?></a></li>
<?php endif ?>
<?php endforeach ?>
</ul>
</div>
<div class="col-md-8">
<h3>Upcoming Classes</h3>
<?php $classes = getClasses() ?>
<table class="table table-sm">
<tr>
	<th width="140">Class Date</th>
	<th>Course Name</th>
	<th>Venue</th>
	<th>Status</th>
</tr>
<?php foreach($classes as $class): ?>
<?php	
// We only wish to see classes which have an end date greater than today
$today = date('Y-m-d');
if($class[9] < $today) continue;
if($class[25] == $cityname):
?>
<tr>
	<td class="text-right">
		<a href="/lsapp/class.php?classid=<?= $class[0] ?>">
		<?php echo goodDateShort($class[8],$class[9]) ?>
		</a>
	</td>
	<td>
		<a href="course.php?courseid=<?= $class[5] ?>"><?= $class[6] ?></a>
	</td>
	<td>
		<a href="venue.php?vid=<?= $class[23] ?>"><?= $class[24] ?></a>
	</td>
	<td>
		<span class="badge badge-light"><?= $class[1] ?></span>
	</td>
</tr>
<?php endif ?>
<?php endforeach ?>
</table>






</div>
</div>





</div>
</div>
</div>



<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php') ?>

<?php else: ?>


<?php require('templates/noaccess.php') ?>

<?php endif ?>