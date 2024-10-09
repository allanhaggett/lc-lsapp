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

<?php require('templates/header.php') ?>
<h1>Pending Classes</h1>
<p>The following classes were requested by the Delivery Team, and have been processed by the Operations Team, but are not yet active for one reason or another.</p>

<div class="table-responsive" id="upcoming-classes">

    <table class="table table-sm table-hover table-striped">
        <thead>
            <tr>
				<th scope="col" width="120">Requested On</th>
				<th scope="col" width="120">Requested By</th>
				<th scope="col" width="120">Item Code</th>
                <th scope="col" width="138">Class Date</th>
                <th scope="col" width="300"><a href="#" class="sort" data-sort="course">Course</a></th>
                <th scope="col" width="140">Processed</th>
                <th scope="col" width="180"><a href="#" class="sort" data-sort="city">City</th>
                <th scope="col">Request Notes</th>
            </tr>
        </thead>
	<tbody class="list">
<!-- 
0-ClassID 1-Status 2-Requested 3-RequestedBy 4-Dedicated +1 to everything from here on ****
4-CourseID 5-CourseName 6-ItemCode 7-StartDate 8-EndDate 9-Times 10-MinEnroll 
11-MaxEnroll 12-ShipDate 13-Facilitating 14-WebinarLink 15-WebinarDate 16-WebinarInfo 17-Enrolled 18-ReservedSeats 19-PendingApproval 20-Waitlisted 
21-Dropped 22-VenueID 23-VenueName 24-VenueCity 25-VenueAddress 26-VenueContact 27-Notes 28-RequestNotes 29-Boxes 
30-Weight 31-Courier 32-TrackingOut 33-TrackingIn 34-AttendanceReturned 35-EvaluationsReturned 36-VenueNotified 37-Modified 38-ModifiedBy 39-Completed 
-->
<?php foreach($c as $row): ?>
	<?php 
	
	$today = date('Y-m-d');
	if($row[1] != 'Pending') continue;
	$stime = strtotime($row[8]);
	$dateStart = date('D M j',$stime);
	if($row[8] != $row[9]) {
		$etime = strtotime($row[9]);
		$end = '- ' . date('j',$etime);
	} else {
		$end = '';
	}
	?>
	<tr <?php if($row[4] == 'Dedicated') echo 'class="table-active"' ?>>

		<td><?= $row[2] ?></td>
		<td><?= $row[3] ?></td>
		<td><?= $row[7] ?></td>
		<td><a href="class.php?classid=<?= $row[0] ?>"><?= $dateStart . $end?></a></td>
		<td>
		<?php if($row[4] == 'Dedicated'): ?>
		<span class="badge badge-dark">DEDICATED</span>
		<?php endif ?>
		<a href="course.php?courseid=<?= $row[5] ?>"><?= h($row[6]) ?></a>
		</td>
		<td><?= $row[40] ?></td>
		<td><?= $row[25] ?></td>
		<td><?= $row[29] ?></td>
		
	</tr>

<?php endforeach ?>
</table>


<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php'); ?>