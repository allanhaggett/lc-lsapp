<?php
opcache_reset();
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
$weekbefore = 7;
$tomorrow = date("Y-m-d", strtotime($today . ' + ' . $daysuntil . ' days'));


foreach($c as $row):
	// If this class is already closed or cancelled, skip it
	if($row[1] != 'Inactive' && $row[1] != 'Deleted'):
		if($row[49] == 'To Ship' && $row[45] != 'Webinar') array_push($toship,$row);
		if($row[49] == 'Shipped') array_push($shipped,$row);
		if($row[49] == 'Arrived') array_push($arrived,$row);
		if($row[1] != 'Closed'):
			if($row[49] == 'Returned') array_push($returned,$row);
		endif;
	endif;
endforeach; // end looping through classes

$classid = (isset($_GET['classid'])) ? $_GET['classid'] : 0;
if(!$classid) $classid = $toship[0][0];
$deets = getClass($classid);

?>
<?php getHeader() ?>

<title>LSApp | Shipping</title>

<link rel="stylesheet" href="css/rome.min.css">

<?php getScripts() ?>


<body>
<?php getNavigation() ?>


<div class="container-fluid">
<div class="row">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>

<?php
$sections = array('Outgoing' => 'shipping-outgoing.php',
			'Shipped/Arrived' => 'shipping-intransit.php',
			'Returned' => 'shipping-incoming.php');

$current = $_SERVER['REQUEST_URI'];
$fulllink = explode('/lsapp/',$current);
$currentpage = explode('?',$fulllink[1]);
?>
<ul class="nav nav-tabs justify-content-center mb-3">
<?php foreach($sections as $page => $link): ?>
<?php
if($currentpage[0] === $link) {
	$active = 'active';
} else {
	$active = '';
}
 ?>
<li class="nav-item"><a href="<?= $link ?>" class="nav-link <?= $active ?>"><?= $page ?></a></li>
<?php endforeach ?>
</ul>

</div>
<div class="col-md-3">

<ul class="list-group">
<?php foreach($toship as $row): ?>

<?php
if($row[23] == 186 || $row[23] == 188 || $row[23] == 239) {
	$shouldbeshipping = date("Y-m-d", strtotime($row[8] . ' -1 days'));
} else {
	$shouldbeshipping = date("Y-m-d", strtotime($row[8] . ' -7 days'));
}
?>
<?php
// Get first and second IDs so we can use it for the "next" button
if($count == 0) {
	$firstid = $row[0];
} elseif ($count == 1) {
	$secondid = $row[0];
}
?>
<?php if($count < 20): ?>
<?php
$istoday = '';
if($row[0] == $classid) $istoday = 'background: #333; color: #FFF;';
?>
<li class="list-group-item" style="<?= $istoday ?>">
	Shipping <?php echo goodDateShort($row[13]) ?><br>
	<div><a href="shipping-outgoing.php?classid=<?= $row[0] ?>" style="<?= $istoday ?>">
		<?php echo goodDateShort($row[8],$row[9]) ?>
		<?= $row[6] ?> in <?= $row[25] ?>
	</a>
	<?php if($row[23] == 186 || $row[23] == 188 || $row[23] == 239): ?>
	<span class="badge badge-success">On Campus</span>
	<?php endif ?>
	</div>



	<?php if($row[13] != $shouldbeshipping): ?>
	<div class="alert alert-warning my-2">
		<?php if($row[23] == 186 || $row[23] == 188 || $row[23] == 239): ?>
		On-campus day-before shipping date:
		<?php else: ?>
		Standard 7-day shipping date: 
		<?php endif ?>
		
		<?= $shouldbeshipping ?>
		
	</div>
	<?php endif ?>




	<?php if($row[13] == $today): ?>
	<div class="alert alert-success mb-0">Ship this today</div>
	<?php endif ?>
	<?php if($row[13] < $today): ?>
	<div class="alert alert-danger mb-0 mt-2">SHIP DATE HAS PASSED!</div>
	<?php endif ?>

</li>
<?php endif ?>
<?php $count++ ?>
<?php endforeach ?>
</ul>
</div>
<div class="col-md-4">

<form method="post" action="class-process-shipping.php" class="shippingadjust">
<input type="hidden" name="classid" id="classid" value="<?= $deets[0] ?>">

	<div class="card mb-3">
	<div class="card-header">
		<strong>Shipping</strong>
		<h2 class="card-title"><a href="course.php?courseid=<?= h($deets[5]) ?>"><?= h($deets[6]) ?></a></h2>
		<h3>
			<a href="class.php?classid=<?= h($deets[0]) ?>" target="_blank">
			<?php print goodDateLong($deets[8]) ?>
			</a>
		</h3>
	</div>
	<div class="card-body">
		
		<h2><?= h($deets[24]) ?> in <?= h($deets[25]) ?></h2>
		<?= h($deets[28]) ?><br>
		<?= h($deets[29]) ?><br>
		<?= h($deets[30]) ?><br>
		<?= h($deets[26]) ?><br>
		<?= h($deets[25]) ?><br>
		<?= h($deets[27]) ?><br>
		<a href="/lsapp/venue.php?vid=<?= h($deets[23]) ?>">Current Venue Info</a>
		<h3>Notes</h3>
		<ul class="list-group">
		<?php $notes = getNotes($deets[0]) ?>
		<?php if(isset($notes)): ?>
		<?php foreach($notes as $note): ?>
		<li class="list-group-item">
			<small>On <?= h($note[2]) ?> <?= h($note[3]) ?> said:</small><br>
			<?php $n = preg_replace('/(^|\s)@([\w_\.]+)/', '$1<a href="person.php?idir=$2">@$2</a>', $note[4]) ?>
			<?= $n ?>

		</li>
		<?php endforeach ?>
		<?php endif ?>

		</ul>


	</div>
	</div>


	<ul class="list-group">
		<li class="list-group-item">
			<label>Ship Date (<?php print goodDateLong($deets[13]) ?>)
			<input type="text" class="date form-control" name="ShipDate" placeholder="ShipDate" value="<?= h($deets[13]) ?>">
			</label>
			<input type="submit" class="btn btn-primary" value="Save">
		</li>
		<li class="list-group-item">
			<label>Who is shipping this class?
			<select class="form-control Shipper" name="Shipper">
			<option>Unassigned</option>
			<?php getPeople($deets[33]) ?>
			</select>
			</label>
			<input type="submit" class="btn btn-primary" value="Save">
		</li>
		<li class="list-group-item">
			Print Roster <?= h($deets[7]) ?>
			<a href="#" class="copy btn btn-sm btn-light" data-clipboard-text="<?= h($deets[7]) ?>">Copy Code and open ELM Rosters</a>
		</li>

		<li class="list-group-item">
		<a class="btn btn-dark btn-block" href="/lsapp/class-checklist.php?classid=<?= h($deets[0]) ?>" target="_blank">Print Checklist</a>
		</li>
		<?php if($deets[23] != 186 && $deets[23] != 188 && $deets[23] != 239 ): ?>
		<li class="list-group-item">
			<div class="row">
				<div class="col-md-12">
					<label for="Courier">Which courier?</label>
					<select name="Courier" class="form-control">
						<option>Select a courier</option>
						<?php $couriers = getCouriers($deets[36]) ?>
						<?php foreach($couriers as $courier): ?>
						<?php if($courier[1] == $deets[36]): ?>
						<option selected><?= $courier[1] ?></option>
						<?php else: ?>
						<option><?= $courier[1] ?></option>
						<?php endif ?>
						<?php endforeach ?>
					</select>
				</div>
				<div class="col-md-6">
					<label for="Boxes">Boxes</label>
					<input type="text" class="form-control" name="Boxes" placeholder="Boxes" value="<?= h($deets[34]) ?>">
				</div>
				<div class="col-md-6">
					<label for="Weight">Weight</label>
					<input type="text" class="form-control" name="Weight" placeholder="Weight" value="<?= h($deets[35]) ?>">
				</div>

				<div class="col-md-6">
					<label for="TrackingOut">Outgoing Tracking #</label>
					<input type="text" class="form-control" name="TrackingOut" placeholder="TrackingOut" value="<?= h($deets[37]) ?>">
				</div>
				<div class="col-md-6">
					<label for="TrackingIn">Incoming Tracking #</label>
					<input type="text" class="form-control" name="TrackingIn" placeholder="TrackingIn" value="<?= h($deets[38]) ?>">

					<label for="PickupIn">Incoming Pickup #</label>
					<input type="text" class="form-control" name="PickupIn" id="PickupIn" value="<?= h($deets[50]) ?>">
				</div>
			</div>
			<input type="submit" class="btn btn-primary" value="Save">

		</li>

		<li class="list-group-item">
		<a class="btn btn-dark btn-block" href="/lsapp/class-labels.php?classid=<?= h($deets[0]) ?>" target="_blank">Print Labels</a>
		</li>
		<?php endif ?>
		
		<li class="list-group-item">
		<h3>Audio/Visual Tech Assign</h3>
		<div class="dropdown">
		<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Available Audio/Visual
		</button>
		<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		<?php $avavailable = getAVunassigned() ?>
		<?php if(isset($avavailable)): ?>
		<?php foreach($avavailable as $av): ?>
			<a class="dropdown-item assignav" 
				href="/lsapp/class-process-av-assign.php?classid=<?= $deets[0] ?>&avid=<?= $av[0] ?>" 
				title="Assign <?= $av[3] ?> to this class">
					<?= $av[3] ?>
			</a>
		<?php endforeach ?>
		<?php endif ?>
		</div>
		</div>
		
		<div class="avassigned"></div>
		
			<h5>Assigned A/V</h5>
			<?php $avassigned = getAVassigned($deets[0]) ?>
			<?php foreach($avassigned as $av): ?>
			<a href="/lsapp/av.php?classid=<?= $deets[0] ?>&avid=<?= $av[0] ?>"
			title=""><?= $av[3] ?></a>
			<?= $av[4] ?>
			<?php endforeach ?>

		</li>
		<li class="list-group-item">
			<label for="CheckedBy">Double-checked by</label>
			<select class="form-control CheckedBy" name="CheckedBy">
			<option>Not Checked</option>
			<?php getPeople($deets[48]) ?>
			</select>
			<input type="submit" class="btn btn-primary" value="Save">
		</li>
		<li class="list-group-item">

			<label for="ShippingStatus">Shipping Status</label>
			<select name="ShippingStatus" id="ShippingStatus" class="form-control">
			<?php $shippingstatuses = array('To Ship','Shipped','Arrived','Returned','No Ship') ?>
			<?php foreach($shippingstatuses as $sstat): ?>
			<?php if($sstat == $deets[49]): ?>
			<option selected><?= $sstat ?></option>
			<?php else: ?>
			<option><?= $sstat ?></option>
			<?php endif ?>
			<?php endforeach ?>
			</select>
			<input type="submit" class="btn btn-primary" value="Save">

		</li>

		<li class="list-group-item">

			<?php if($deets[23] != 186 && $deets[23] != 188 && $deets[23] != 239 ): ?>
			<?php if(!$deets[41] || $deets[41] == 'Not'): ?>
			<a class="btn btn-primary" href="/lsapp/class-venue-notify.php?classid=<?= h($deets[0]) ?>">Notify Venue</a>
			<div class="alert alert-warning mt-3">The venue has not been notified.</div>
			<?php endif ?>
			<?php endif ?>

		</li>

	</ul>
	</form>

	<a href="shipping-outgoing.php?classid=<?= $secondid ?>" class="btn btn-block btn-success text-uppercase my-3">Next Shipment</a>
	<hr style="margin-bottom: 100px">
</div>

<div class="col-md-5">


<h4 class="mt-3">Materials Inventory <a href="materials.php" class="btn btn-light btn-sm">Manage</a></h4>

<div class="table-responsive">
<table class="table table-sm table-striped">
<tr>

	<th>Material Name</th>
	<th class="text-center">Per Course</th>
	<th class="text-center">In Stock</th>
	<th class="text-center">New Stock</th>

</tr>
<!-- // 0-MaterialID,1-CourseName,2-MaterialName,3-PerCourse,4-InStock,5-Partial,6-Restock,7-Notes-->
<!-- 0-MaterialID,1-CourseName,2-CourseID,3-MaterialName,4-PerCourse,5-InStock,6-Partial,7-Restock,8-Notes,9-FileName -->
<?php $materials = getMaterials($deets[5]) ?>
<?php foreach($materials as $mat): ?>
<?php
$per = $mat[4];
$in = $mat[5];
$newstock = 0;
$classesworth = 0;
if($in > 0 && $per > 0) {
	$classesworth = floor($in / $per);
	$newstock = ($in - $per);
	if($newstock < 1) $newstock = 0;
}
?>
<tr>
	<td>
	<div><a href="material.php?mid=<?= h($mat[0]) ?>"><?= h($mat[3]) ?></a></div>
	</td>
	<td class="text-center"><?= h($mat[4]) ?></td>
	<td class="text-center">
	<?= h($mat[5]) ?><br>
	<small><?= $classesworth ?> classes worth</small>
	</td>
	<td class="text-center" width="160">
	<form method="post" action="materials-process.php" class="inventoryadjust">
	<input type="hidden" name="action" id="action" value="class">
		<input type="hidden" name="cid" id="cid" value="<?= $deets[0] ?>">
		<input type="hidden" name="matid" id="matid" value="<?= $mat[0] ?>">
		<input type="text" class="" name="InStock" id="InStock" size="3" value="<?= $newstock ?>">
		<button class="btn btn-primary">Set</button>
		<?php if($mat[7] == 'TRUE' || $mat[7] == 'on'): ?>
		<input type="checkbox" class="" name="Restock" id="Restock" checked>
		<?php else: ?>
		<input type="checkbox" class="" name="Restock" id="Restock">
		<?php endif ?>
	</form>
	</td>


</tr>
<?php endforeach ?>
</table>
</div> <!-- /responsive -->


</div>
</div>
</div>

<?php require('templates/javascript.php') ?>

<script src="js/clipboard.min.js"></script>

<script>
$(document).ready(function(){


	var whorag = rome(document.querySelector('.date'), { time: false, dateValidator: function (d) {
		return moment(d).day() !== 6;
	} });
	var clipboard = new Clipboard('.copy');
	$('.copy').on('click',function(e){
		e.preventDefault();
		window.open("https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_ADMINISTRATIVE.LM_ROSTER.GBL?PAGE=LM_ROSTER_SRCH", "_blank");
	});

	$('.shippingadjust').on('submit',function(e){


		var form = $(this);
		var url = form.attr('action');

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize(),
			success: function(data)
			{
				//form.before('<div class="alert alert-success p-0">Adjusted</div>');
				alert('Saved');
				console.log(data);
			},
			statusCode:
			{
				403: function() {
					btn.after('<div class="alert alert-warning">You must be logged in.</div>');
				},
				500: function() {
					//alert('');
					//btn.after('<div class="alert alert-warning">You must be logged in.</div>');
				}

			}
		});
		e.preventDefault();

	});
	$('.inventoryadjust').on('submit',function(e){

		var form = $(this);
		var url = form.attr('action');

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize(),
			success: function(data)
			{
				form.after('<div class="alert alert-success p-0">Adjusted</div>');
			},
			statusCode:
			{
				403: function() {
					form.after('<div class="alert alert-warning">You must be logged in.</div>');
				}
			}});
		e.preventDefault();

	});

	$('.assignav').on('click',function(e){

		e.preventDefault();
		var gourl = $(this).attr('href');
		
		$.ajax({
			type: "GET",
			url: gourl,
			success: function(data)
			{
				$('.avassigned').html('<div class="alert alert-success">AV Assigned</div>');
			},
			statusCode:
			{
				403: function() {
					form.after('<div class="alert alert-warning">You must be logged in.</div>');
				}
			}});
		

	});
});
</script>
<?php require('templates/footer.php'); ?>
