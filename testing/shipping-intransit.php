<?php
require('inc/lsapp.php');
// Get an array of all classes in the system
$c = getClasses();
// Pop the headers off
array_shift($c);
// Create a temp array to hold course names for sorting
$tmp = array();
// Loop through the whole classes and add start dates to the temp array
foreach($c as $line) {
	$tmp[] = $line[13];
}
// Use the temp array to sort all the classes by start date
array_multisort($tmp, SORT_ASC, $c);

$toship = array();
$shipped = array();
$arrived = array();
$returned = array();

$count = 0;
// Create a today variable for comparison with class dates
$today = date('Y-m-d');
$daysuntil = 2;
$tomorrow = date("Y-m-d", strtotime($today . ' + ' . $daysuntil . ' days'));

foreach($c as $row): 
	// If this class is already closed or cancelled, skip it
	if($row[1] != 'Inactive' || $row[49] == 'Shipped' || $row[49] == 'Arrived'):
		if($row[49] == 'To Ship') array_push($toship,$row);
		if($row[49] == 'Shipped') array_push($shipped,$row);
		if($row[49] == 'Arrived') array_push($arrived,$row);
		if($row[1] != 'Closed'):
			if($row[49] == 'Returned') array_push($returned,$row);
		endif;
	endif;
endforeach; // end looping through classes
?>

<?php getHeader() ?>

<title>LSApp | Shipping</title>

<?php getScripts() ?>

<?php getNavigation() ?>

<body>
<div class="container-fluid">
<div class="row justify-content-center">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
<?php
$sections = array('Outgoing' => 'shipping-outgoing.php',
			'Shipped/Arrived' => 'shipping-intransit.php',
			'Returned' => 'shipping-incoming.php');

$currentpage = $_SERVER['REQUEST_URI'];
$currentpage = explode('/lsapp/',$currentpage);
//echo $currentpage[1];
$active = '';
?>
<ul class="nav nav-tabs justify-content-center mb-3">
<?php foreach($sections as $page => $link): ?>
<?php
if($currentpage[1] === $link) {
	$active = 'active';
} else {
	$active = '';
}
 ?>
<li class="nav-item"><a href="<?= $link ?>" class="nav-link <?= $active ?>"><?= $page ?></a></li>
<?php endforeach ?>
</ul>
</div>

<div class="col-md-4">
<h2>Shipped <span class="badge badge-pill badge-primary"><?php echo count($shipped) ?></span></h2>
<p>These materials have left The PSA Learning Centre and are en route to the class venue.</p>
<ul class="list-group">
<?php foreach($shipped as $row): ?>
<li class="list-group-item">
	<a href="class.php?classid=<?= $row[0] ?>">
		<?php echo goodDateShort($row[8],$row[9]) ?>
		<?= $row[6] ?> at <?= $row[24] ?> in <?= $row[25] ?>
	</a> 
	<br>
	<?= $row[37] ?> 
	<?php if($row[36] == 'Purolator'): ?>
	<a class="btn btn-sm btn-light" href="https://www.purolator.com/en/ship-track/tracking-details.page?pin=<?= $row[37] ?>" target="_blank">Track</a>
	<?php endif ?>
	<?php if($row[36] == 'Maximum Express'): ?>
	<a class="btn btn-sm btn-light" href="https://dwaybill.com/maximum" target="_blank">></a>
	<?php endif ?>
	<?php if($row[8] < $tomorrow): ?>
	<div class="alert alert-danger mb-0">NOT ARRIVING IN TIME!</div>
	<?php endif ?>
	<?php if($row[1] == 'Inactive'): ?>
	<div class="alert alert-warning">HEY!</div>
	<?php else: ?>
	<a href="class-process-shipstat.php?status=Arrived&classid=<?= $row[0] ?>" 
		class="btn btn-success btn-block">
			Mark as Arrived
	</a>
	<?php endif ?>
</li>
<?php endforeach ?>
</ul>
</div>
<div class="col-md-4">
<h2>Arrived <span class="badge badge-pill badge-primary"><?php echo count($arrived) ?></span></h2>
<p>These materials have arrived at the venue and are awaiting course delivery and then pickup.</p>
<ul class="list-group">
<?php foreach($arrived as $row): ?>
<li class="list-group-item">
	
	<h4 class="mb-1"><a href="class.php?classid=<?= $row[0] ?>">
		<?php echo goodDateShort($row[8],$row[9]) ?>
		<?= $row[6] ?> at <?= $row[24] ?> in <?= $row[25] ?>
	</a> </h4>
	<h5 class="mb-1">
	<?php if($row[38]): ?>
	Tracking: 
	<?= $row[38] ?> 
	<?php if($row[36] == 'Purolator'): ?>
	<a class="btn btn-sm btn-light" href="https://www.purolator.com/en/ship-track/tracking-details.page?pin=<?= $row[38] ?>" target="_blank">Track</a>
	<?php endif ?>
	<?php if($row[36] == 'Maximum Express'): ?>
	<a class="btn btn-sm btn-light" href="https://dwaybill.com/maximum" target="_blank">></a>
	<?php endif ?>
	<?php endif ?>
	</h5>
	<?php if($row[1] == 'Inactive' || $row[1] == 'Pending'): ?>
	<div class="alert alert-warning">
			<strong>Beware this class is <?= $row[1] ?>.</strong><br> Please adjust inventory from its <a href="class.php?classid=<?= $row[0] ?>">class page</a>.
			<em>(This class will not appear on the "returned" dashboard once marked as returned)</em>
	</div>
	<?php endif ?>
	<a href="class-process-shipstat.php?status=Returned&classid=<?= $row[0] ?>" class="btn btn-success btn-block">Mark as Returned</a>
</li>
<?php endforeach ?>
</ul>
</div>

</div>
</div>

<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php'); ?>