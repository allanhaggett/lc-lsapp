<?php 
opcache_reset();
require('inc/lsapp.php');

if(canACcess()):

$f = fopen('data/classes.csv', 'r');

$c = array();
while ($row = fgetcsv($f)) {
	array_push($c,$row);
}
fclose($f);
$headers = $c[0];
array_shift($c);
$tmp = array();
foreach($c as $line) {
	$tmp[] = $line[8];
}
array_multisort($tmp, SORT_ASC, $c);

$classes = array();
$classcount = 0;
$today = date('Y-m-d');
$dayofweek = date('w');
if($dayofweek == 5) {
	$tomorrow = new DateTime($today);
	$tomorrow->modify('+3 days');
} else {
	$tomorrow = new DateTime('tomorrow');
}
$ondeck = $tomorrow->format('Y-m-d');
$theday = $tomorrow->format('l');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="1800">
<title>All Classes Happening <?= $theday ?></title>
<?php getScripts() ?>
</head>
<body onload="setInterval('updateClock()', 200);" > <!--style="background: rgba(35,64,117,1) ; color: #FFF"-->
<?php getNavigation() ?>
<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-6">
<h1>End of Day Emails</h1>
<ol>
	<li><a href="onenote:///Z:\The%20Learning%20Centre\2.%20Admin,%20Facilities%20&%20Ops\LSA's%20documents\OneNote\Learning%20Centre\End%20Of%20Day.one#section-id={E5D4562C-B19E-468E-B67B-5FA93F1C0BFE}&end">
		Open the OneNote End of Day section</a>
	</li>
	<li>Copy today's page into a new page with tomorrow's date</li>
	<li>Update people task areas as necessary</li>
	<li>Copy the list below and paste it below the people task list</li>
	<li>Choose the "Email Page" button in the OneNote Home ribbon</li>
	<li>Send the resulting email to the team!</li>
</ol>
</div>
<div class="col-md-6">
<h2>Happening <?= $theday ?></h2>
<div>(<?= $ondeck ?>)</div>
<ul class="list-group">
<?php foreach($c as $row): ?>
<?php if(($row[1] != 'Inactive') || ($row[1] != 'Deleted')): ?>
<?php if($ondeck <= $row[8] && $ondeck >= $row[9]): ?>
<?php if($row[45] == 'Webinar'): ?>
<li class="list-group-item"><a href="https://gww.bcpublicservice.gov.bc.ca/lsapp/class.php?classid=<?= $row[0]  ?>"><?= $row[6]  ?> - Webinar</a></li>
<?php else: ?>
<li class="list-group-item"><a href="https://gww.bcpublicservice.gov.bc.ca/lsapp/class.php?classid=<?= $row[0]  ?>"><?= $row[6]  ?> - <?= $row[25]  ?></a></li>
<?php endif // webinar ?>
<?php endif // tomorrow ?>
<?php endif // != ' Inactive' ?>
<?php endforeach ?>
</ul>
</div>
</div>
</div>
<?php require('templates/javascript.php') ?>
<script type="text/javascript">
	// This function gets the current time and injects it into the DOM
	function updateClock() {
		// Gets the current time
		var now = new Date();
		// Get the hours, minutes and seconds from the current time
		var hours = now.getHours();
		var minutes = now.getMinutes();
		var seconds = now.getSeconds();
		// Format hours, minutes and seconds
		if (hours < 10) {
			hours = "0" + hours;
		}
		if (minutes < 10) {
			minutes = "0" + minutes;
		}
		if (seconds < 10) {
			seconds = "0" + seconds;
		}
		// Gets the element we want to inject the clock into
		var elem = document.getElementById('clock');
		// Sets the elements inner HTML value to our clock data
		elem.innerHTML = hours + ':' + minutes + ':' + seconds;
	}
</script>
</body>
</html>
<?php endif ?>