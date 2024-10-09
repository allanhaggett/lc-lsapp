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
?>
<?php getHeader() ?>
<title>Upcoming Classes</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row">
<div class="col-md-12">
<div id="upcoming-classes">
	

	<h1 class="text-center mb-0">
		<span class="classcount"><?= ($count - $inactive) ?></span> 
		Upcoming Classes 
	</h1>
	<div class="text-center"><small>Enrollment numbers as of <?php echo date ("F d Y H:i", filemtime('data/elm.csv')) ?></small></div>
	<input class="search form-control my-2 mx-auto w-50" placeholder="search">
	<div class="w-50 row mt-2 mx-auto">
		
		<div class="alert alert-primary col-4 p-0 text-center">Shipped</div>
		<div class="alert alert-success col-4 p-0 text-center">Arrived</div>
		<div class="alert alert-warning col-4 p-0 text-center"><a href="#" class="sort asc" data-sort="status">Requested</a></div>
		<!--<div class="alert alert-danger col-4 p-0 text-center">Alert!</div>-->
	</div>
	<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead>
            <tr>
				<th scope="col" width="5"></th>
				<th scope="col" width="100" class="text-right">Item Code</th>
                <th scope="col" width="138" class="text-right"><a href="#" class="sort" data-sort="startdate">Class Date</a></th>
                <th scope="col" width="300"><a href="#" class="sort" data-sort="course">Course</a></th>
                <th scope="col" width="250"><a href="#" class="sort" data-sort="venue">Venue</a></th>
                <th scope="col" width="130"><a href="#" class="sort" data-sort="city">City</th>
                <!--<th scope="col" width="50"><a href="#" class="sort" data-sort="region">Region</th>-->
				<th scope="col" width="70"><a href="#" class="sort" data-sort="enrolled">Enrolled</a></th>
            </tr>
        </thead>
	<tbody class="list">

	<?php foreach($upclasses as $row): ?>
	<?php 
	$statrow = '';
	$issueflag = '';
	if(!$row[7] && $row[4] != 'Dedicated' && $row[1] != 'Requested' && $row[1] != 'Inactive') {
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
		<td class="status"><div style="display:none"><?= h($row[1]) ?> <?= h($row[49]) ?></div></td>
		<td class="text-right itemcode">
			<?= $issueflag ?>
			<small><?= h($row[7]) ?></small>
			<?php if($row[4] == 'Dedicated'): ?>
			<span class="badge badge-light">Dedicated</span>
			<?php endif ?>
		</td>
		<td class="text-right">
			<a href="class.php?classid=<?= h($row[0]) ?>">
				<?php print goodDateShort($row[8],$row[9]) ?>
			</a>
			<div class="startdate" style="display: none"><?= h($row[8]) ?></div>
		</td>
		<td class="course"><a href="course.php?courseid=<?= h($row[5]) ?>"><?= h($row[6]) ?></a></td>
		<td class="venue"><a href="venue.php?vid=<?= h($row[23]) ?>"><?= h($row[24]) ?></a>
		<div style="display: none">
			<?= h($row[28]) ?><br>
			<?= h($row[29]) ?><br>
			<?= h($row[30]) ?><br>
			<?= h($row[26]) ?><br>
			<?= h($row[25]) ?><br>
			<?= h($row[27]) ?>
		</div>
		</td>
		<td class="city"><a href="city.php?name=<?= h($row[25]) ?>"><?= h($row[25]) ?></a></td>
		<!--<td><?= h($row[47]) ?></td>-->
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