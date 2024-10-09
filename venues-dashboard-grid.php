<?php
// Let's get started
require('inc/lsapp.php');

if(isAdmin()):
// Get the full class list
$c = getClasses();
// Pop the headers off the top
array_shift($c);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the start date to
// the temp array
foreach($c as $line) {
	$tmp[] = $line[8];
}
// Sort the whole kit and kaboodle by start date from
// oldest to newest
array_multisort($tmp, SORT_ASC, $c);
//
// Now let's run through the whole thing and process it, removing
// classes with dates older than "today" and any requested classes
//
$count = 0;
$inactive = 0;
$upclasses = array();
$today = date('Y-m-d');
foreach($c as $row) {

	if($row[1] == 'Deleted') continue;
	if($row[1] == 'Inactive') continue;
	if($row[9] < $today) continue;
	if($row[45] == 'Webinar') continue;
	$tbdcitytest = explode('TBD - ',$row[25]);
	$tbdvenuetest = explode('TBD - ',$row[24]);
	if(isset($tbdcitytest[1]) || isset($tbdvenuetest[1])) {
		array_push($upclasses,$row);
		$count++;
	}
	
}
?>
<?php getHeader() ?>
<title>Venue Booking Dashboard</title>
<?php getScripts() ?>
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
<div id="upcoming-classes">
	

	<h1 class="text-center mb-0">
		<span class="classcount"><?= ($count - $inactive) ?></span> 
		Classes needing a venue
	</h1>
	<div class="text-center"><small>Enrollment numbers as of <?php echo date ("F d Y H:i", filemtime('data/elm.csv')) ?></small></div>
	<input class="search form-control my-2 mx-auto w-50" placeholder="search">
	<div class="w-50 row mt-2 mx-auto">
		
		<div class="alert alert-primary col-4 p-0 text-center">Shipped</div>
		<div class="alert alert-success col-4 p-0 text-center">Arrived</div>
		<div class="alert alert-warning col-4 p-0 text-center"><a href="#" class="sort asc" data-sort="status">Requested</a></div>
		<!--<div class="alert alert-danger col-4 p-0 text-center">Alert!</div>-->
	</div>
	<div class="row justify-content-md-center">

	<?php foreach($upclasses as $row): ?>
	<?php 

	$statrow = '';
	$issueflag = '';
	if(!$row[7] && $row[4] != 'Dedicated' && $row[1] != 'Requested') {
		$issueflag = '<span class="badge badge-danger">???</span>';
	}
	//if($row[1] == 'Pending') {
	//	$statrow = 'table-primary';
	//} else
	if($row[1] == 'Inactive') {
		$statrow = 'cancelled';
	}
	if($row[1] == 'Requested') {
		$statrow = 'table-warning';
	}	
	if($row[49] == 'Shipped') {
		$statrow = 'table-primary';
	} elseif($row[49] == 'Arrived') {
		$statrow = 'table-success';
	}
	?>
	<div class="col-md-6 <?= $statrow ?>">
	<div class="card mb-3">
	<div class="card-header">
			<div class="status"><div style="display:none"><?= h($row[1]) ?> <?= h($row[49]) ?></div></div>
		<div class="itemcode">
			<?= $issueflag ?>
			<small><?= h($row[7]) ?></small>
			<?php if($row[4] == 'Dedicated'): ?>
			<span class="badge badge-light">Dedicated</span>
			<?php endif ?>
		</div>
		<div class="">
			<a href="class.php?classid=<?= h($row[0]) ?>">
				<?php print goodDateShort($row[8],$row[9]) ?>
			</a>
			<div class="stardivate" style="display: none"><?= h($row[8]) ?></div>
		</div>
		<div class="course"><a href="course.php?courseid=<?= h($row[5]) ?>"><?= h($row[6]) ?></a></div>
	</div>
	<div class="card-body">


		<div class="city"><a href="city.php?name=<?= h($row[25]) ?>"><?= h($row[25]) ?></a></div>
		<div class="requestnotes"><?= h($row[32]) ?></div>
		<!--<div><?= h($row[47]) ?></div>-->
		<div class="enrolled">
		<?php if($row['18'] < $row[11] && $row[1] != 'Inactive'): ?>
		<span class="badge badge-danger" title="Enrollment is currently below the set minimum">Enrollment <?= h($row[18]) ?></span>
		<?php else: ?>
		<span class="badge badge-light">Enrollment <?= h($row[18]) ?></span>
		<?php endif ?>
		</div>
		<div class=""><span class="badge badge-light">Maximum <?= $row[12] ?></span></div>
		<div><a href="venue-inquire.php?classid=<?= $row[0] ?>" class="btn btn-success">Inquire</a></div>
		<div>
		<?php $note = getBookingNotes($row[0]) ?>
		<?php $foo = array_reverse($note) ?>
		<?php if($note): ?>
		<small>On <?= h($foo[0][2]) ?> <?= h($foo[0][3]) ?> said:</small><br>
		<?= h($foo[0][4]) ?>
		<?php endif ?>

		</div>
		</div>
		</div>
		</div>

<?php endforeach ?>

</div>
</div>
</div>
</div>
</div>

<?php require('templates/javascript.php') ?>

<script>
$(document).ready(function(){

	$('.search').focus();
	
	var upcomingoptions = {
		valueNames: [ 'status', 'stardivate', 'course', 'facilitator', 'region', 'venue', 'city', 'itemcode', 'enrolled' ]
	};
	var upcomingClasses = new List('upcoming-classes', upcomingoptions);
	upcomingClasses.on('searchComplete', function(){
		//console.log(upcomingClasses.update().matchingItems.length);
		$('.classcount').html(upcomingClasses.update().matchingItems.length);
	});
});
</script>

<?php require('templates/footer.php'); ?>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>