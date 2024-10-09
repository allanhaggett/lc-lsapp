<?php
opcache_reset();
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
$allclasses = array();
$today = date('Y-m-d');
foreach($c as $row) {
	//
	// If the status is Requested, we skip the line entirely
	//
	if($row[1] == 'Requested') continue;
	//
	// Add the class to the array that we'll loop through below
	//
	array_push($allclasses,$row);
	$count++;
}
?>
<?php getHeader() ?>

<title>Calendar</title>

<link href='fullcalendar/core/main.css' rel='stylesheet'>
<link href='fullcalendar/daygrid/main.css' rel='stylesheet'>
<link href='fullcalendar/timegrid/main.min.css' rel='stylesheet'>
<style>
.fc-event, .fc-event:hover {
	color: #0069d9;
}
.fc-day-grid-event .fc-content {
	padding: 3px;
}
.fc-timeGridWeek-view .fc-title {
	
}
.fc-view-container {
	background: #FFF;
}
</style>
<?php getScripts() ?>
<body class="bg-light-subtle">

<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container-fluid">

<div id="calendar"></div>


</div>


<?php require('templates/javascript.php') ?>

<script src='fullcalendar/core/main.js'></script>
<script src='fullcalendar/daygrid/main.js'></script>
<script src='fullcalendar/timegrid/main.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
var calendarEl = document.getElementById('calendar');

var calendar = new FullCalendar.Calendar(calendarEl, {
plugins: [ 'interaction', 'dayGrid', 'timeGrid' ],
defaultView: 'dayGridMonth',
height: 'auto',
header: {
	left: 'prev,next today',
	center: 'title',
	right: 'dayGridMonth,timeGridWeek,timeGridDay'
},
weekNumbers: true, 
hiddenDays: [ 0,6 ],
events: [
<?php foreach($allclasses as $class): 
$oncampus = '';
if($class[1] == 'Deleted') continue;
if($class[1] == 'Inactive') {
	$bgcolor = '#F1F1F1';
	$tcolor = '#666';
	$title = 'CANCELLED: ' . $class[25] . ' -' .  addslashes($class[6]);
} else {
	$bgcolor = $class[56];
	$tcolor = '#000';
	$shipwarn = '';
	if($class[13] < $today && $class[49] == 'To Ship') {
		$shipwarn = 'PROBLEM! ';
		$bgcolor = 'red';
		$tcolor = '#FFF';
	}
	if($class[23] == 186 || $class[23] == 188 || $class[23] == 239) {
		$title = $shipwarn . 'ON CAMPUS - ' . $class[24] . ' - ' . addslashes($class[6]) . ' - ' . $class[49];
	} elseif($class[45] == 'Webinar') {
		$title = 'Webinar - ' . addslashes($class[6]) . ' - ' . $class[49];
	} else {
		$title = $shipwarn . $oncampus . $class[25] . ' - ' .  addslashes($class[6]) . ' - ' . $class[49];
	}
}
 ?>
{
	title: '<?= $title ?>',
	start: '<?= $class[8] ?>',
	end: '<?= $class[9] ?> <?= $class[55] ?>',
	url: 'class.php?classid=<?= $class[0] ?>',
	backgroundColor: '<?= $bgcolor ?>',
	borderColor: '#FFF',
	textColor: '<?= $tcolor ?>'
},
<?php endforeach ?>
 ]
    });

    calendar.render();
  });
</script>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>

<?php require('templates/footer.php'); ?>