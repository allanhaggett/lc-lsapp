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
$twomonthsout = date("Y-m-d", strtotime($today . ' + 60 days'));
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
	
	<input class="search form-control my-3 mx-auto w-50" placeholder="search">
	
	<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead>
            <tr>
				<th scope="col" width="5"></th>
				<th scope="col" width="100" class="text-right">Item Code</th>
                <th scope="col" width="138" class="text-right"><a href="#" class="sort" data-sort="startdate">Class Date</a></th>
                <th scope="col" width="300"><a href="#" class="sort" data-sort="course">Course</a></th>
                <th scope="col" width="130"><a href="#" class="sort" data-sort="city">City</th>
				<th width="80"></th>
				<th width="100">Request Note</th>
				<th width="200">Last Booking Note</th>
				
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

		<td class="city"><a href="city.php?name=<?= h($row[25]) ?>"><?= h($row[25]) ?></a></td>
		<td><a href="venue-inquire.php?classid=<?= $row[0] ?>" class="btn btn-success">Inquire</a></td>
		<td><?= h($row[32]) ?></td>

		<td width="300">
		<?php $note = getBookingNotes($row[0]) ?>
		<?php $foo = array_reverse($note) ?>
		<?php if($note): ?>
		<small>On <?= h($foo[0][2]) ?> <?= h($foo[0][3]) ?> said:</small><br>
		<?= h($foo[0][4]) ?>
		<?php endif ?>
		</td>
		
	</tr>
<?php endforeach ?>
</table>
</div>
</div>
</div>
</div>

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

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>