<?php

// Let's get started
require('inc/lsapp.php');
// Get the full class list
$c = getClasses();
// Grab the headers for fun
$headers = $c[0];
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
?>

<?php getHeader() ?>
<title>Requested Classes not yet processed into the Learning System</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>

<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-12">

<h1>Requested Classes</h1>
<p>The following classes have been requested by the Delivery Team, and have yet to be processed by the Operations Team.</p>

<div class="table-responsive" id="upcoming-classes">
<input class="search form-control mb-3 w-50" placeholder="search">
    <table class="table table-sm table-hover table-striped">
        <thead>
            <tr>
				<th scope="col" width="120">Requested On</th>
				<th scope="col" width="120">Requested By</th>
				<th scope="col" width="120">Claimed</th>
                <th scope="col" width="138">Class Date</th>
                <th scope="col" width="300"><a href="#" class="sort" data-sort="course">Course</a></th>
                <th scope="col" width="180"><a href="#" class="sort" data-sort="city">City</th>
                <th scope="col" width="200">Request Notes</th>
            </tr>
        </thead>
	<tbody class="list">

<?php foreach($c as $row): ?>
	<?php 
	$today = date('Y-m-d');
	if($row[1] != 'Requested') continue;
	?>
	<tr <?php if($row[4] == 'Dedicated') echo 'class="table-active"' ?>>

		<td><?= $row[2] ?></td>
		<td><a href="person.php?idir=<?= $row[3] ?>"><?= $row[3] ?></a></td>
		<td>
		<?php if(isset($row[44])): ?>
		<a href="person.php?idir=<?= $row[44] ?>"><?= $row[44] ?></a>
		<?php endif ?>		
		</td>
		<td><a href="class.php?classid=<?= $row[0] ?>"><?php echo goodDateShort($row[8],$row[9]) ?></a></td>
		<td class="course">
		<?php if($row[4] == 'Dedicated'): ?>
		<span class="badge badge-dark">DEDICATED</span>
		<?php endif ?>
		<a href="course.php?courseid=<?= $row[5] ?>"><?= h($row[6]) ?></a>
		</td>
		<td><a href="city.php?name=<?= $row[25] ?>"><?= $row[25] ?></a></td>
		<td><?= $row[32] ?></td>
		
	</tr>

<?php endforeach ?>
</table>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>

</div>
</div>
</div>
<?php require('templates/javascript.php') ?>

<script>
$(document).ready(function(){

	$('.search').focus();
	
	var upcomingoptions = {
		valueNames: [ 'startdate', 'course', 'facilitator', 'region', 'venue', 'city', 'itemcode' ]
	};
	var upcomingClasses = new List('upcoming-classes', upcomingoptions);
	upcomingClasses.on('searchComplete', function(){
		//console.log(upcomingClasses.update().matchingItems.length);
		$('.classcount').html(upcomingClasses.update().matchingItems.length);
	});
});
</script>
<?php require('templates/footer.php'); ?>