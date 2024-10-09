<?php require('inc/lsapp.php') ?>
<?php opcache_reset() ?>
<?php $user = stripIDIR($_SERVER["REMOTE_USER"]); ?>
<?php if(canAccess()): ?>

<?php $json = (isset($_GET['json'])) ? $_GET['json'] : 0; ?>
<?php $classid = (isset($_GET['classid'])) ? $_GET['classid'] : 0; ?>
<?php $deets = getClass($classid) ?>
<?php $course = getCourse($deets[5]) ?>
<?php
if($json):
	$head = fopen('data/classes.csv', 'r');
	fclose($head);
	$headers = fgetcsv($head);
	$class = array_combine($headers,$deets);
	header('Content-Type: application/json');
	$json = json_encode($class);	
	print_r($json);
else:
?>
<?php getHeader() ?>
<title>
	<?php if($deets[4] == 'Dedicated'): ?>
	DEDICATED | 
	<?php endif ?>
	<?php if($deets[7]): ?>
	<?= h($deets[7]) ?> | 
	<?php endif ?>
	<?php print strip_tags(goodDateLong($deets[8],$deets[9])) ?> | 
	<?= h($deets[45]) ?> <?= h($deets[6]) ?>. 
	<?php $tbdtest = explode('TBD - ',$deets[25]) ?> 
	<?php if(isset($tbdtest[1])): ?><?= h($deets[25]) ?> 
	<?php else: ?><?= h($deets[24]) ?> in <?= h($deets[25]) ?>. 
	<?php endif ?>
</title>
<meta name="description" content="Learning Support Adminstration Application (LSApp)">
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<style>
.matnote {
	font-size: 10px;
}
.wording,
#ChangeRequestSubmit {
	display: none;
}
.btn-unclaim {
	background: #F3F3F3;
	border-radius: 50%;
	color: #333;
	font-size: 10px;
	height: 20px;
	margin: 0 5px 0 0;
	padding: 0;
	width: 20px;
	
}
</style>

<?php getScripts() ?>
<?php if($deets[1] == 'Deleted'): ?>
<body style="background: red">
<?php else: ?>
<body class="bg-light-subtle">
<?php endif ?>

<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center mb-3">

<?php if(isset($deets[0])): ?>
<div class="col-md-5">
	
<div class="card mb-4">
<div class="card-header">
<small><?= h($deets[1]) ?></small>
<div class="float-right">
	<?php if(isSuper()): ?>
	<form method="post" action="class-delete.php">
	<input type="hidden" name="classid" value="<?= $deets[0] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-danger del">
	</form>
	<?php endif ?>
</div>
<div class="float-right">
	<?php if($deets[44] && $deets[44] != 'Unassigned'): ?>
	<span class="assignedto">Assigned to <a href="/lsapp/person.php?idir=<?= h($deets[44]) ?>"><?= h($deets[44]) ?></a></span>
	<?php else: ?>
	<span class="assignedto">Not Assigned</span>
	<?php endif ?>
	<?php if(isAdmin()): ?>
	<form method="get" action="class-claim.php" class="float-right adminclaim">
		<input type="hidden" name="cid" id="cid" value="<?= h($deets[0]) ?>">
		<input type="submit" class="btn btn-sm btn-primary ml-3" value="Claim">
	</form>
	<?php endif ?>
	</div>
	<?php if($deets[4] == 'Dedicated'): ?>
	<strong>DEDICATED</strong>
	<?php endif ?>
	<?php if($deets[7]): ?>
	<span class="badge badge-success" id="itemcode"><?= h($deets[7]) ?></span>
	<button class="copy btn btn-sm btn-light" data-clipboard-text="<?= h($deets[7]) ?>">Copy Code</button>
	<?php endif ?>

</div>
<div class="card-body">


<?php if($deets[4] != 'Dedicated' && !$deets[7] && $deets[1] != 'Draft' && $deets[1] != 'Requested' && $deets[1] != 'Inactive' && $deets[1] != 'Deleted'): ?>
<div class="alert alert-danger">A class cannot be active <em>and</em> not have an item code</div>
<?php endif ?>
<?php if($deets[1] == 'Deleted'): ?>
<div class="alert alert-danger">THIS CLASS HAS BEEN DELETED. DO NOT USE IT FOR ANYTHING.</div>
<?php endif ?>
<?php if($deets[1] == 'Draft'): ?>
<div class="alert alert-danger">This is a DRAFT of a class date. It will not be processed by 
	Learning Support Admins until it is set to 'requested'
</div>
<?php endif ?>
<?php if(isAdmin()): ?>
<a class="btn btn-light float-right" href="class-update.php?classid=<?= h($deets[0]) ?>">Edit</a>
<?php endif ?>






<span class="badge badge-light">
		<?= strtolower(h($deets[45])) ?>
</span>

<!--<div><?= h($deets[46]) ?></div>-->
<h1 class="mb-0">
	<a href="/lsapp/course.php?courseid=<?= h($deets[5]) ?>">
		<?= h($deets[6]) ?>
	</a>
</h1>



<?php $tbdtest = explode('TBD - ',$deets[25]) ?>
<?php if(isset($tbdtest[1])): ?>
	<?= h($deets[25]) ?>
<?php else: ?>
<?php if($deets[1] == 'Requested' && $deets[45] == 'Webinar'): ?>
<div class="alert alert-danger">THIS IS A WEBINAR.<br> Ensure that you're setting this up in ELM as a web-based component with a session.</div>
<?php endif ?>
<?php if($deets[15]): ?>
<div class="webinar">
<div><a href="<?= h($deets[15]) ?>" class="btn btn-success text-uppercase">webinar</a> happening on <?= goodDateLong($deets[16]) ?> </div>
</div>
<?php elseif($deets[45] == 'eLearning'): ?>
ELEARNING
<?php else: ?>



	<h2 class="my-2"><a href="#venuedeets" class="showvenue"><?= h($deets[24]) ?></a> in <?= h($deets[25]) ?></h2>

	<div id="venuedeets" style="display: none" class="card mt-0 mr-3 mb-3 ml-3">
	<div class="p-3 venueaddress">
	<?= h($deets[28]) ?><br>
	<?= h($deets[29]) ?><br>
	<?= h($deets[30]) ?><br>
	<?= h($deets[26]) ?><br>
	<?= h($deets[25]) ?><br>
	<?= h($deets[27]) ?><br>
	<div class="alert alert-warning">
		Information at the time of booking. Venue contact info changes regularly. <br>
		<a href="/lsapp/venue.php?vid=<?= h($deets[23]) ?>">Current Venue Info</a>
	</div>
	</div>
	</div>
<?php endif ?>
<?php endif ?>
<h3 class="mb-0" id="longdate">
	<?php print goodDateLong($deets[8],$deets[9]) ?> 
	<div><?= h($deets[10]) ?></div>
</h3>












<div class="alert alert-primary my-2">

<?php $facilitators = explode(' ', $deets[14]); ?>

Facilitating: 
<span class="facilitators">
<?php if(!empty($deets[14])): ?>

<?php foreach($facilitators as $facilitator): ?>
<span class="<?= $facilitator ?>">
<a href="/lsapp/person.php?idir=<?php echo strip_tags(str_replace('@','',$facilitator)) ?>">
	<?= $facilitator ?>
</a> </span>
<?php endforeach ?>
<span class="<?= $user ?>"></span>
<?php else: ?>
<span class="unknown">Unknown</span>
<?php endif ?>
</span>
<?php if(in_array($user,$facilitators)): ?>
<a href="class-facilitator-claim.php?cid=<?= h($deets[0]) ?>&unclaim=unclaim" 
	class="facilitatorclaim btn btn-sm btn-light">
		UnClaim
</a>
<?php else: ?>
<a href="class-facilitator-claim.php?cid=<?= h($deets[0]) ?>" 
	class="facilitatorclaim btn btn-sm btn-light">
		Claim
</a>
<?php endif ?>
</div>






<div class="mb-3">
	<a href="<?= h($deets[58]) ?>" 
		target="_blank" 
		rel="noopener"
		class="btn btn-success">
		Evaluations Link
	</a>
	<?php if(!$deets[57]): ?>
		Evaluations <strong>NOT</strong> sent
	<?php else: ?>
		Evaluations sent
	<?php endif ?>
</div>










<div>
<?php if(isAdmin()): ?>
<div class="btn-group mb-2">
	<?php if(isAdmin()): ?>
	<a class="btn btn-sm btn-light" href="venue-inquire.php?classid=<?= $deets[0] ?>">Venue Inquire</a>
	<?php endif ?>
	<button class="copy btn btn-sm btn-light" data-clipboard-text="<?php print strip_tags(goodDateLong($deets[8],$deets[9])) ?> <?= h($deets[10]) ?>">Copy Date/Time</button>
	<button class="copy btn btn-sm btn-light" data-clipboard-text="<?php echo elmStartDate($deets[8]) ?>">Copy ELM Date</button>
	<a class="btn btn-sm btn-light" href="<?= $course[7] ?>" target="_blank">ELM Delivery Method</a>
</div>
<?php endif ?>

</div>
<div class="row text-center">
<div class="col-2">
Min <span class="badge badge-dark"><?= h($deets[11]) ?></span>
</div>
<div class="col-2">
Max <span class="badge badge-dark"><?= h($deets[12]) ?></span>
</div>
</div>

<div class="mb-3">
<div class="">

<?php if($deets[45] == 'eLearning'): ?>
	<div class="alert alert-warning my-2">
	eLearning classes do not synchronize attendance numbers with ELM
	</div>
<?php else: ?>

<?php if($deets[4] == 'Dedicated'): ?>
<a href="docs/dedicated-class-ADHOC-attendance-form.xlsx" class="btn btn-success mt-2">Ad Hoc Attendance form</a>
<?php else: ?>

<div class="row text-center">
<div class="col">
Enrolled <br><span class="badge badge-dark"><?= h($deets[18]) ?></span> 
</div>
<div class="col">
Reserved <br><span class="badge badge-dark"><?= h($deets[19]) ?></span> 
</div>
<div class="col">
Pending <br><span class="badge badge-dark"><?= h($deets[20]) ?></span> 
</div>
<div class="col">
Waitlisted <br><span class="badge badge-dark"><?= h($deets[21]) ?></span> 
</div>
<div class="col">
Dropped <br><span class="badge badge-dark"><?= h($deets[22]) ?></span> 
</div>
</div>
<?php if($deets[11] && $deets[12]): ?>
<?php $percentfull = floor(($deets[18] + $deets[19]) / $deets[12] * 100) ?>
<?php $percentmin = floor($deets[11] / $deets[12] * 100) ?>
<?php 
$percentstatus = 'bg-warning';
if($percentfull > $percentmin) $percentstatus = 'bg-success';
?>
<div class="progress progress-bar-striped mt-2">
	<div class="progress-bar progress-bar-striped  <?= $percentstatus ?>" 
		role="progressbar" 
		style="width: <?= $percentfull ?>%" 
		aria-valuenow="<?= $percentfull ?>" 
		aria-valuemin="0" 
		aria-valuemax="100">
			<?= $percentfull ?>% full
	</div>
</div>
<?php else: ?>
<div class="alert alert-danger my-3">There is an issue with the min/max values for this course. Please edit and review; values cannot be blank or zero.</div>
<?php endif; // end of min/max check ?>
<?php if($deets[18] < $deets[11] && $deets[1] != 'Requested' && $deets[4] != 'Dedicated' && $deets[1] != 'Inactive'): ?>
<?php $byhowmuch = $deets[11] - $deets[18] ?>
<div class="alert alert-warning my-2">Class enrollment is below the minimum by <?= h($byhowmuch) ?></div>
<?php endif ?>
<?php endif ?>
<?php endif ?>
<?= $lastsyncmessage ?>
</div>
</div>


<?php if($deets[49] != 'No Ship'): ?>
	
	<div class="card mt-3">
	<div class="card-body">
	<?php if($deets[13] < $today && $deets[49] == 'To Ship'): ?>
	<div class="alert alert-danger">SHIPPING DATE PASSED</div>
	<?php endif ?>
	<h3 class="m-0">Shipping on <?php print goodDateLong($deets[13]) ?></h3>
	<div class="mb-2">Courtesy of <a href="person.php?idir=<?= h($deets[33]) ?>"><?= h($deets[33]) ?></a></div>
	<a class="btn btn-primary" href="/lsapp/class-checklist.php?classid=<?= h($deets[0]) ?>">Checklist</a>
	<a class="btn btn-primary" href="/lsapp/class-labels.php?classid=<?= h($deets[0]) ?>">Labels</a>
	<?php if($deets[49] == 'Shipped'): ?>
	<?php if($deets[23] != 186 && $deets[23] != 188 && $deets[23] != 239 ): ?>
	<?php if(!$deets[41] || $deets[41] == 'Not'): ?>
	<a class="btn btn-primary" href="/lsapp/class-venue-notify.php?classid=<?= h($deets[0]) ?>">Notify Venue</a>
	<div class="alert alert-warning mt-3">The venue has not been notified.</div>
	<?php else: ?>
	Venue notified.
	<?php endif ?>
	<?php endif ?>
	<?php endif ?>
	<?php
	$stat = 'alert-secondary';
	if($deets[49] == 'Shipped') {
		$stat = 'alert-warning';
	} elseif($deets[49] == 'Arrived') {
		$stat = 'alert-success';
	}  elseif($deets[49] == 'Returned') {
		$stat = 'alert-success';
	} 
	?>
	<div class="alert <?= h($stat) ?> mt-2 mb-2">
	<span style="font-weight: bold; text-transform: uppercase"><?= h($deets[49]) ?></span>
	<?php if($deets[1] == 'Shipped'): ?>
	<?php $ago = daysAgo($deets[13]) ?>
	<?= h($ago) ?> days ago  
	<?php endif ?>
	</div>
	
	<?php if($deets[49] == 'Returned' && $deets[39] == 'Not'): ?>
	<div class="alert alert-warning">Attendance NOT returned</div>
	<?php endif ?>
	<?php if($deets[49] == 'Returned' && $deets[40] == 'Not'): ?>
	<div class="alert alert-warning">Evaluations NOT returned</div>
	<?php endif ?>
	
	<?php if($deets[23] != 186 && $deets[23] != 188 && $deets[23] != 239 ): ?>
	<div class="row">
		<div class="col-6">
		Tracking Outgoing: <?= h($deets[37]) ?><br>
		Tracking Incoming: <?= h($deets[38]) ?>
		</div>
	<?php $couriers = getCouriers($deets[36]) ?>
	<?php foreach($couriers as $courier): ?>
	<?php if($courier[1] == $deets[36]): ?>
	<?php $web = $courier[2] ?>
	<?php $phone = $courier[3] ?>
	<?php $courieruser = $courier[4] ?>
	<?php $pass = $courier[5] ?>
	<?php endif ?>
	<?php endforeach ?>
	<?php if(isset($web)): ?>
		<div class="col-6">
		<a href="<?= $web ?>" target="_blank"><?= $deets[36] ?></a><br>
		<a href="tel:<?= $phone ?>"><?= $phone ?></a><br>
		User: <?= $courieruser ?><br>
		Password: <?= $pass ?>
		</div>
	<?php endif ?>

	</div>
	</div>
	</div>

	<div class="card mt-2">
	<div class="card-body">

	<div class="dropdown float-right">
		<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		Available Audio/Visual
		</button>
		<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		<?php $avavailable = getAVunassigned() ?>
		<?php foreach($avavailable as $av): ?>
		<a class="dropdown-item" href="/lsapp/class-process-av-assign.php?classid=<?= $deets[0] ?>&avid=<?= $av[0] ?>" title="<?= $av[4] ?>"><?= $av[3] ?></a>
		<?php endforeach ?>
		
		</div>
	</div>
	<h5>Assigned A/V</h5>
	<?php $avassigned = getAVassigned($deets[0]) ?>
	<?php foreach($avassigned as $av): ?>
	<a href="/lsapp/av.php?classid=<?= $deets[0] ?>&avid=<?= $av[0] ?>"
		title=""><?= $av[3] ?></a>
		<?= $av[4] ?>
	<?php endforeach ?>
	
	<?php endif ?>
	</div>
	</div>
	
<?php else: ?>
<div class="alert alert-info my-3">
	The Learning Centre is not responsible for shipping 
	or otherwise managing the class materials for this course.
</div>

<?php endif ?>	





<?php $materials = getMaterials($deets[5]) ?>
<?php if(sizeof($materials) > 0): ?>

<h4 class="mt-3">Materials Inventory <a href="materials.php" class="btn btn-light btn-sm">Materials Dashboard</a></h4>

<div class="table-responsive">
<table class="table table-sm table-striped">
<tr>
	<th>MaterialName</th>
	<th class="text-center">PerCourse</th>
	<th class="text-center">In Stock</th>
	<th class="text-center">New Stock</th>
</tr>
<!-- // 0-MaterialID,1-CourseName,2-MaterialName,3-PerCourse,4-InStock,5-Partial,6-Restock,7-Notes-->
<!-- 0-MaterialID,1-CourseName,2-CourseID,3-MaterialName,4-PerCourse,5-InStock,6-Partial,7-Restock,8-Notes,9-FileName -->
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
</div>
<?php else: ?>
<div class="alert alert-warning mt-3">
	There are no materials currently assigned to this course. 
	<a href="https://gww.bcpublicservice.gov.bc.ca/lsapp/material-create.php?courseid=<?= $deets[5] ?>" class="btn btn-block btn-light mt-2">
		Add New Material
	</a>
</div>
<?php endif ?>
	</div> <!-- /.card-body -->
<div class="card-footer text-muted">

Created on <?= h($deets[2]) ?> by
<a href="/lsapp/person.php?idir=<?= h($deets[3]) ?>"><?= h($deets[3]) ?></a><br>
Last modified on <?= h($deets[42]) ?> by 
<a href="/lsapp/person.php?idir=<?= h($deets[43]) ?>"><?= h($deets[43]) ?></a>

</div>
</div> <!-- /.card -->
</div> 




<div class="col-md-3">
<div class="card mb-4">
<div class="card-header">
	<h4 class="card-title">Change Requests</h4>
	<p><small>Request a change to the particulars of <em>this</em> class date. Submit changes to the course info <a href="course.php?courseid=<?= h($deets[5]) ?>">on the course page</a></small></p>
</div>
<div class="card-body">
<?php if($deets[1] != 'Inactive'): ?>
<form action="class-change-create.php" method="post" id="chchchchanges">
	<input type="hidden" name="CourseName" id="CourseName" value="<?= h($deets[6]) ?>">
	<input type="hidden" name="StartDate" id="StartDate" value="<?= h($deets[8]) ?>">
	<input type="hidden" name="City" id="City" value="<?= h($deets[25]) ?>">
	<input type="hidden" name="ClassID" id="ClassID" value="<?= h($deets[0]) ?>">
	<p>What would like to change?</p>
	<div class="form-group m-0">
		<input type="radio" name="ChangeType" id="changeCancel" value="Cancel">
		<label for="changeCancel">Cancellation</label>
	</div>
	<div class="form-group m-0">
		<input type="radio" name="ChangeType" id="changeDate" value="Date">
		<label for="changeDate">Date change</label>
	</div>
	<div class="form-group m-0">
		<input type="radio" name="ChangeType" id="changeVenue" value="Venue">
		<label for="changeVenue">Venue change</label>
	</div>
	<div class="form-group m-0">
		<input type="radio" name="ChangeType" id="changeOther" value="Other">
		<label for="changeOther">Other</label>
	</div>
	<div class="alert alert-warning wording" id="cancelWording">
		Please state the reason for the cancellation.
	</div>
	<?php if($deets[1] != 'Requested'): ?>
	<div class="alert alert-warning wording" id="dateChangeWording">
	 
		Please submit a cancellation request, then submit a new service request for your new date.
	</div>
	<?php else: ?>
	<div class="alert alert-warning wording" id="dateChangeWording">
		Please be aware that after a class has been processed into the Learning System (is 'Active' and 
		has an ITEM code), changing a start date requires that that you cancel the class and submit a new request.
	</div>	
	<?php endif ?>
	<div class="alert alert-warning wording" id="venueChangeWording">
		We can change a venue within a city, but if you need to change the city, 
		please submit this as a cancellation request along with a new service request.
	</div>
	<textarea name="ChangeRequestNote" id="ChangeRequestNote" class="form-control summernote" required placeholder="Type here"></textarea>
	<input type="submit" class="btn btn-sm btn-primary btn-block" id="ChangeRequestSubmit" value="Add Change Request">
</form>
<?php else: ?>
<div class="alert alert-danger">This class has been cancelled.</div>
<?php endif ?>
</div>
<ul class="list-group list-group-flush">
<?php $chgs = getClassChanges($classid) ?>
<?php if(isset($chgs)): ?>
<?php foreach($chgs as $ch): ?>
<li class="list-group-item">

	<?php if(isSuper()): ?>
	<form method="post" action="class-change-delete.php" class="float-right">
	<input type="hidden" name="ClassID" value="<?= $deets[0] ?>">
	<input type="hidden" name="reqID" value="<?= $ch[0] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-danger del">
	</form>
	<?php endif ?>
	
	<small>On <?= h($ch[5]) ?> <?= h($ch[6]) ?> said:</small><br>
	<?php if($ch[11]): ?><strong><?= h($ch[11]) ?></strong><br><?php endif ?>
	<?php $n = preg_replace('/(^|\s)@([\w_\.]+)/', '$1<a href="person.php?idir=$2">@$2</a>', $ch[10]) ?>
	<?= $n ?><br>
	
	<div class="badge badge-dark"><?= h($ch[7]) ?></div>
	
	<small><?= h($ch[8]) ?>, <?= h($ch[9]) ?></small>
	<?php if(isAdmin()): ?>
	
	<a href="/lsapp/class-change-process.php?changeid=<?= h($ch[0]) ?>&classid=<?= h($deets[0]) ?>"
		class="	btn btn-sm btn-success">Do</a>
	<?php endif ?>
		</li>
<?php endforeach ?>
<?php endif ?>
</ul>

</div> <!-- /.card -->
<?php $otherclasses = getClassesByItemCode($deets[7]) ?>
<?php if(isset($otherclasses[1])): ?>
<div class="card mb-4">
<div class="card-header">
	<h4 class="card-title">
		Other Sessions
	</h4>
	<small>Other class dates with the same item code.</small>
</div>
<ul class="list-group list-group-flush">
<!-- Other classes with the same ITEM code -->

<?php foreach($otherclasses as $other): ?>
<?php if($other[0] == $deets[0]) continue ?>
<li class="list-group-item">
	<a href="class.php?classid=<?= $other[0] ?>">
		<?php echo goodDateLong($other[8],$other[9]) ?>
	</a> - 
	<?= $other[25] ?>
</li>
<?php endforeach ?>
</ul>
</div>
<?php endif ?>


</div>
<div class="col-md-3">




<div class="card mb-4">
<div class="card-header">
	<h4 class="card-title">
		Notes 
	</h4>
</div>
<div class="card-body">
<small><a href="/lsapp/person.php?idir=<?= h($deets[3]) ?>"><?= h($deets[3]) ?></a> said upon requesting:</small><br>
<?= h($deets[32]) ?>
<hr>
<form action="note-create.php" method="post">
<input type="hidden" name="ClassID" id="ClassID" value="<?= h($deets[0]) ?>">
<textarea name="Note" id="Note" class="form-control summernote" required></textarea>
<input type="submit" class="btn btn-sm btn-block btn-primary" value="Add Note">
</form>
</div>
<ul class="list-group list-group-flush">
<?php $notes = getNotes($classid) ?>
<?php if(isset($notes)): ?>
<?php foreach($notes as $note): ?>
<li class="list-group-item">
<!-- creqID,ClassID,Date,NotedBy,Note-->
	<?php if(isSuper()): ?>
	<form method="post" action="note-delete.php" class="float-right">
	<input type="hidden" name="ClassID" value="<?= $deets[0] ?>">
	<input type="hidden" name="NoteID" value="<?= $note[0] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-danger del">
	</form>
	<?php endif ?>
	<small>On <?= h($note[2]) ?> <?= h($note[3]) ?> said:</small><br>
	<?php $n = preg_replace('/(^|\s)@([\w_\.]+)/', '$1<a href="person.php?idir=$2">@$2</a>', $note[4]) ?>
	<?= $n ?>
	
</li>
<?php endforeach ?>
<?php endif ?>

</ul>

</div><!-- /.card -->


<div class="card mb-4">
<div class="card-header">
	<h4 class="card-title">
		Booking Notes 
	</h4>
</div>
<?php if(isAdmin()): ?>
<div class="card-body">
<form action="note-book-create.php" method="post">
<input type="hidden" name="ClassID" id="ClassID" value="<?= h($deets[0]) ?>">
<textarea name="Note" id="Note" class="form-control summernote" required></textarea>
<input type="submit" class="btn btn-sm btn-block btn-primary" value="Add Note">
</form>
</div>
<?php endif ?>
<ul class="list-group list-group-flush">
<?php $bnotesget = getBookingNotes($classid) ?>
<?php if(isset($bnotesget)): ?>
<?php $bnotes = array_reverse($bnotesget) ?>
<?php foreach($bnotes as $note): ?>
<li class="list-group-item">
	<!--<div class="float-right"><small><a class="" href="#?NoteID=<?= h($note[0]) ?>">delete</a></small></div>-->
	<small>On <?= h($note[2]) ?> <?= h($note[3]) ?> said:</small><br>
	<?= h($note[4]) ?>
</li>
<?php endforeach ?>


</ul>

</div><!-- /.card -->
<?php endif ?>

</div>
</div>




<?php else: ?>
<div class="col-md-6">
	<h2>Class Not Found</h2>
	<p>Must be playin' hooky ;)</p>
	<p>Email the Learning Support Admin Team <<a href="mailto:learning.centre.admin@gov.bc.ca">learning.centre.admin@gov.bc.ca</a>> with any questions or concerns.</p>
	<p><img src="img/TrollFace.jpg" width="300px"></p>
</div>	
<?php endif ?>

</div>
</div>


<?php require('templates/javascript.php') ?>

<script src="js/clipboard.min.js"></script>
<script src="/lsapp/js/summernote-bs4.js"></script>
<script>
$(document).ready(function(){
	
	var clipboard = new Clipboard('.copy');
	
	
	$('.showvenue').on('click',function(e){
		e.preventDefault();
		$('#venuedeets').toggle();
	});
	
	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['color', ['color']],
			['link']
		],
		placeholder: 'Type here',
		hint: {
			<?php $peeps = getPeopleAll() ?>
			mentions: [
			<?php foreach($peeps as $p): ?>
			'<?= $p[0] ?>',
			<?php endforeach ?>
			],
			match: /\B@(\w*)$/,
			search: function (keyword, callback) {
			callback($.grep(this.mentions, function (item) {
				return item.indexOf(keyword) == 0;
			}));
			},
			content: function (item) {
				return '@' + item;
			}
		}
		
	});	
	
	
	$('.inventoryadjust').on('submit',function(e){

		var form = $(this);
		var url = form.attr('action');

		//form.nextAll('.alert').first().fadeOut().remove();
		
		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize(),
			success: function(data)
			{
				form.after('<div class="alert alert-success p-0">Adjusted</div>');
				//form.closest('tr').fadeOut().remove();
				
			},
			statusCode: 
			{
				403: function() {
					form.after('<div class="alert alert-warning">You must be logged in.</div>');
				}
			}});
		e.preventDefault();

	});







	$('.adminclaim').on('submit',function(e){


		var form = $(this);
		var url = form.attr('action');

		//form.nextAll('.alert').first().fadeOut().remove();
		
		$.ajax({
			type: "GET",
			url: url,
			data: form.serialize(),
			success: function(data)
			{
				userlink = '<a href="person.php?idir='+data+'">'+data+'</a>';
				$('.assignedto').html('Assigned to ' + userlink);
				//form.after(userlink);
				//form.remove();
				//form.closest('tr').fadeOut().remove();
				
			},
			statusCode: 
			{
				403: function() {
					form.after('<div class="alert alert-warning">You must be logged in.</div>');
				}
			}});
		e.preventDefault();	
			



	});
	
	

	
		
	
	
	$('#chchchchanges input').on('change', function() {
		var ctype = $('input[name=ChangeType]:checked', '#chchchchanges').val();
		$('.wording').hide();
		$('#ChangeRequestSubmit').hide();
		//$('#ChangeRequestNote').hide();
		if(ctype == 'Cancel') {
			$('#cancelWording').show();
			$('#ChangeRequestSubmit').show();
			//$('#ChangeRequestNote').show().focus();
		}
		if(ctype == 'Date') {
			$('#dateChangeWording').show();
			<?php if($deets[1] == 'Requested'): ?>
			$('#ChangeRequestSubmit').show();
			//$('#ChangeRequestNote').show().focus();			
			<?php endif ?>
		}
		if(ctype == 'Venue') {
			$('#venueChangeWording').show();
			$('#ChangeRequestSubmit').show();
			//$('#ChangeRequestNote').show().focus();
		}
		if(ctype == 'Other') {
			$('#ChangeRequestSubmit').show();
			//$('#ChangeRequestNote').show().focus();
		}		
		//console.log(ctype);
	});



$('.facilitatorclaim').on('click',function(e){

	e.preventDefault();
	var link = $(this);
	$(this).remove();
	var url = $(this).attr('href');

	$.ajax({
		type: "GET",
		url: url,
		success: function(data)
			{
				if(!data) {
					$('.<?= $user ?>').empty();
				} else {
					userlink = '<a href="person.php?idir=' + data + '">' + data + '</a>';
					$('.<?= $user ?>').html(userlink);
					$('.unknown').hide();
				}
			},
		statusCode: 
			{
				403: function() {
					form.after('<div class="alert alert-warning">You must be logged in.</div>');
				}
			}
	});
		
});
	
});
</script>


<?php require('templates/footer.php') ?>
<?php endif ?>

<?php else: ?>
<?php require('templates/noaccess.php') ?>
<?php endif ?>