<?php
$f = fopen('../data/classes.csv', 'r');
if(!$f) $f = fopen('classes.csv', 'r');
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
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="1800">
<title>Today at The Learning Centre</title>
<link rel="stylesheet" href="bootstrap.min.css">


<style>
body, html {
	/*BCPSA-mountains-bigblur.png*/
	background: #FFF url('BC-sun.png') bottom center no-repeat;
	color: rgba(35,64,117,1);
	height: 100%;
}
img {
	height: auto;
	max-width: 100%;
}
.deet {
	font-size: 30px;
	font-weight: 400;
	margin: 0 0 10px 0;
	text-transform: uppercase;
}
.noclasses,
.shadow {
	background: rgba(255,255,255,.8);
	border-radius: 10px;
	box-shadow: 0 0 60px rgba(0,0,0,.1);
	margin: 60px 0;
	padding: 60px;
}
.grad {
  background: #8bc646; /* Old Browsers */
  background: -webkit-linear-gradient(left,#8bc646,#0191c6); /*Safari 5.1-6*/
  background: -o-linear-gradient(left,#8bc646,#0191c6); /*Opera 11.1-12*/
  background: -moz-linear-gradient(left,#8bc646,#0191c6); /*Fx 3.6-15*/
  background: linear-gradient(to right, #8bc646, #0191c6); /*Standard*/
  
}
.imprint {
	text-shadow: -1px 0 1px #FFF;
}
</style>
</head>
<body class="" onload="setInterval('updateClock()', 200);" > <!--style="background: rgba(35,64,117,1) ; color: #FFF"-->
<div class="container">
<div class="row">
<div class="col-md-12 text-center" style="margin-top: 60px;">
<div class="imprint">
<h1 style="font-size: 380%; font-weight: 700"><?php echo date('l, F j') ?> <span id="clock"></span></h1>
<h2 class="text-uppercase" style="font-size: 350%; font-weight:700; margin: 0 0 0 0;">the Learning Centre</h2>

<div style="font-size: 200%; font-weight: 400">Training Rooms on the <strong>5th Floor</strong></div>
</div>
<?php
$pilot = array();
$pilotcount = 0;
$discovery = array();
$discoverycount = 0;
$lcentre = array();
$lcount = 0;
$today = date('Y-m-d');
?>
<?php foreach($c as $row): ?>
<?php if($row[1] != 'Inactive' && $row[1] != 'Deleted'): ?>
<?php if($today >= $row[8] && $today <= $row[9]): ?>
<?php if($row[23] == 186): // Discovery Island ?>
<?php 	array_push($discovery,$row) ?>
<?php elseif($row[23] == 239): // Pilot Bay ?>
<?php 	array_push($pilot,$row) ?>
<?php elseif($row[23] == 188): // Learning Centre ?>
<?php 	array_push($lcentre,$row) ?>
<?php endif // Discovery; Pilot; LC ?>
<?php endif // $row[8] == $today ?>
<?php endif // != ' Inactive' ?>
<?php endforeach ?>

<?php if(!$discovery && !$pilot && !$lcentre): ?>
<div class="row justify-content-md-center">
<div class="col-md-8">
<div class="noclasses">
<h1 class="text-center text-uppercase">No classes here today</h1>
</div>
</div>
</div>
<?php else: ?>
<div class="row justify-content-md-center">
<div class="col-md-12">
<?php if(count($lcentre)>0): ?>
<?php foreach($lcentre as $lc): ?>
<?php //print_r($lc) ?>
<h2><?= $lc[10] ?></h2>
<h1 style="font-size:250%"><?= $lc[6] ?></h1>
<h1 style="font-weight:1000">Entire 5th Floor</h1>
<hr>
<?php endforeach ?>
<?php endif ?>
</div>
<?php if(count($discovery)>0): ?>
<div class="col-md-6">
<div class="shadow">
Today in
<h1 style="font-weight:1000" class="text-uppercase mb-0">Discovery Island</h1>
<div class="directions text-uppercase" style="font-size: 24px;">End of the hall on your left</div>
<?php foreach($discovery as $disc): ?>

<div class="deet" style="background:rgba(255,255,255,.6); border-radius:3px; margin: 20px 0;"><!-- Day 1 of <?= $disc[17] ?> | --><?= $disc[10] ?></div>
<h1 style="background: rgba(255,255,255,.7); border-radius: 3px; font-size:250%"><?= $disc[6] ?></h1>

<?php endforeach ?>

</div>
</div>
<?php endif ?>
<?php if(count($pilot)>0): ?>
<div class="col-md-6">
<div class="shadow">
Today in
<h1 style="font-weight:1000" class="text-uppercase">Pilot Bay</h1>
<div class="directions text-uppercase" style="font-size: 20px;">DOWN THE HALL, FIRST DOOR ON YOUR LEFT</div>
<?php foreach($pilot as $pil): ?>
<div class="deet" style="background:rgba(255,255,255,.6); border-radius:3px; margin: 20px 0;"><?= $pil[10] ?></div>
<h1 style="background: rgba(255,255,255,.7); border-radius: 3px; font-size:250%"><?= $pil[6] ?></h1>
<?php endforeach ?>

</div>
</div>
<?php endif ?>
</div>
<?php endif ?>

<!--<hr>
<img src="BCPS_IDEAS_RGB_pos-onwhite.png" width="600" class="my-3">-->
</div>
</div>
</div>
<nav class="navbar fixed-bottom navbar-light" style="background:rgba(255,255,255,.7);">
  <a class="navbar-brand" href="#"><img src="BCID_BCPSA_rgb_pos-alpha.png" width="400"></a>
</nav>
<script type="text/javascript">
	// This function gets the current time and injects it into the DOM
	// function updateClock() {
	// 	// Gets the current time
	// 	var now = new Date();
	// 	// Get the hours, minutes and seconds from the current time
	// 	var hours = now.getHours();
	// 	var minutes = now.getMinutes();
	// 	var seconds = now.getSeconds();
	// 	// Format hours, minutes and seconds
	// 	if (hours < 10) {
	// 		hours = "0" + hours;
	// 	}
	// 	if (minutes < 10) {
	// 		minutes = "0" + minutes;
	// 	}
	// 	if (seconds < 10) {
	// 		seconds = "0" + seconds;
	// 	}
	// 	// Gets the element we want to inject the clock into
	// 	var elem = document.getElementById('clock');
	// 	// Sets the elements inner HTML value to our clock data
	// 	elem.innerHTML = hours + ':' + minutes + ':' + seconds;
	}
</script>
</body>
</html>
