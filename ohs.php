<?php
// Let's get started
require('inc/lsapp.php');
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
	//
	// We only wish to see classes which have an end date greater than today
	//
	if($row[9] < $today) continue;
	$ohscheck = explode('OHS ',$row[6]);
	if(isset($ohscheck[1])) {
		//
		// We want to continue to show inactive classes, but we also want an accurate
		// count of classes that are upcoming; we count the inactives and subtract them 
		// from the total 
		//
		if($row[1] == 'Inactive') $inactive++;
		//
		// If the status is Requested, we skip the line entirely
		//
		//if($row[1] == 'Requested') continue;
		//
		// Add the class to the array that we'll loop through below
		//
		array_push($upclasses,$row);
		$count++;
	}
}
?>
<?php getHeader() ?>
<title>Upcoming Classes</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container-fluid">
<div class="row">
<div class="col-md-12">
<div id="upcoming-classes">
	

	<h1 class="text-center mb-0">
		<span class="classcount"><?= ($count - $inactive) ?></span> 
		Upcoming Occupation Health &amp; Safety Classes 
	</h1>
	<div class="text-center"><small>Enrollment numbers as of <?php echo date ("F d Y H:i", filemtime('data/elm.csv')) ?></small></div>
	<input class="search form-control my-2 mx-auto w-50" placeholder="search">
	<div class="text-center w-50 mx-auto mb-3"><a href="ohs-export.php" class="btn btn-block btn-dark">Download Excel</a></div>
	<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead>
            <tr>
				<th scope="col" width="5">Status</th>
				<th scope="col" width="100" class="text-right">Item Code</th>
                <th scope="col" width="138" class="text-right">Start Date</th>
                <th scope="col" width="300">Course</a></th>
                <th scope="col" width="250">Venue</a></th>
                <th scope="col" width="130">City</th>
                <th scope="col">Address</th>
                <th scope="col">Postal Code</th>
				<th scope="col" width="70">Enrolled</th>
            </tr>
        </thead>
	<tbody class="list">

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
	<tr class="<?= $statrow ?>">
		<td class="status"><?= h($row[1]) ?></td>
		<td class="text-right itemcode">
			<?= $issueflag ?>
			<small><?= h($row[7]) ?></small>
			<?php if($row[4] == 'Dedicated'): ?>
			<span class="badge badge-light">Dedicated</span>
			<?php endif ?>
		</td>
		<td class="text-right">
				<?php print goodDateShort($row[8],$row[9]) ?>
		</td>
		<td class="course"><?= h($row[6]) ?></td>
		<td class="venue"><?= h($row[24]) ?></td>
		<td class="city"><?= h($row[25]) ?></td>
		<td><?= h($row[26]) ?></td>
		<td><?= h($row[27]) ?></td>
		<td class="enrolled text-center">
		<?php if($row['18'] < $row[11] && $row[1] != 'Inactive'): ?>
		<span class="badge badge-danger" title="Enrollment is currently below the set minimum"><?= h($row[18]) ?></span>
		<?php else: ?>
		<span class="badge badge-light"><?= h($row[18]) ?></span>
		<?php endif ?>
		</td>

	</tr>
<?php endforeach ?>
</table>
</div>
</div>
</div>
</div>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>

<?php require('templates/javascript.php') ?>

<script>
$(document).ready(function(){

	$('.search').focus();
	
	var upcomingoptions = {
		valueNames: [ 'status', 'startdate', 'course', 'facilitator', 'region', 'venue', 'city', 'itemcode', 'enrolled' ]
	};
	var upcomingClasses = new List('upcoming-classes', upcomingoptions);
	upcomingClasses.on('searchComplete', function(){
		//console.log(upcomingClasses.update().matchingItems.length);
		$('.classcount').html(upcomingClasses.update().matchingItems.length);
	});
});
</script>

<?php require('templates/footer.php'); ?>