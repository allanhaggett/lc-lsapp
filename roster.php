<?php 
require('inc/lsapp.php');
$classid = (isset($_GET['classid'])) ? $_GET['classid'] : 0;
$deets = getClass($classid);
$rosterfile = 'rosters/' . $deets[7] . '.csv';
$rostersentfile = 'rosters/' . $deets[7] . '-sent.csv';


if(file_exists($rostersentfile)) {
	$rsf = fopen($rostersentfile, 'r'); 
	$sentheaders = fgetcsv($rsf);
	$sentto = array();
	while ($row = fgetcsv($rsf)) {
		array_push($sentto,$row);
	}
	fclose($rsf);
}


if(file_exists($rosterfile)) {
	$rf = fopen($rosterfile, 'r'); 
	$rosterheaders = fgetcsv($rf);
	$enrolled = array();
	$enrolledandsent = array();
	$waitlist = array();
	while ($participant = fgetcsv($rf)) {
		if($participant[8] == 'Enrolled'){
			$totessent = 0;
			foreach($sentto as $sent) {
				if($sent[0] == $participant[4]) {
					$totessent = $sent[1];
				}
			}
			if($totessent) {
				$participant[21] = $totessent;
				array_push($enrolledandsent,$participant);
			} else {
				array_push($enrolled,$participant);
			}
		} elseif($participant[8] == 'Waitlisted'){
			array_push($waitlist,$participant);
		}
	}
	fclose($rf);
}



getHeader();

?>

<title>Roster Communication Manager</title>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<?php getScripts() ?>

<?php getNavigation() ?>
<?php if(file_exists($rosterfile)): ?>
<div class="container-fluid">
<div class="row justify-content-md-center mb-3">
<div class="col-md-3">
<div class="alert alert-danger">
	THIS IS A WORK IN PROGRESS. 
	Contact Allan about it if you're curious, 
	but please don't try to use it just yet. Thanks!
</div>
<form enctype="multipart/form-data" method="post" 
									accept-charset="utf-8" 
									class="up" 
									action="roster-controller.php">
<input type="hidden" name="classid" id="classid" value="<?= $classid ?>">
<input type="hidden" name="itemcode" id="itemcode" value="<?= $deets[7] ?>">
<input type="hidden" name="action" id="action" value="upload">
<div class="card ">
<div class="card-header">
	<h3 class="card-title">Upload ELM Roster export</h3>
</div>
<div class="card-body">

	Last updated: <?php echo date ("F d Y H:i", filemtime($rosterfile)) ?><br>

	<?= $deets[7] ?><br>
	
	<a href="https://learning.gov.bc.ca/psc/CHIPSPLM_17/EMPLOYEE/ELM/q/?ICAction=ICQryNameURL=PUBLIC.GBC_ROSTER_EXPANDED" target="_blank">Expanded Roster</a>

	<label>Roster CSV:<br>
		<input type="file" name="elmfile" class="form-control-file">
	</label>
	
	<input type="submit" class="btn btn-primary btn-block" value="Upload">
</div>
</div>
</form>
</div>
<div class="col-md-9">
<h1><a href="course.php?courseid=<?= $deets[5] ?>"><?= $deets[6] ?></a></h1>
<h2><a href="class.php?classid=<?= $classid ?>"><?= goodDateLong($deets[8],$deets[9]) ?></a></h2>

<small>Enrollment numbers as of <?php echo date ("F d Y H:i", filemtime('data/elm.csv')) ?>:</small><br>
Enrolled <span class="badge badge-dark"><?= h($deets[18]) ?></span> 
Reserved <span class="badge badge-dark"><?= h($deets[19]) ?></span> 
Pending <span class="badge badge-dark"><?= h($deets[20]) ?></span> 
Waitlisted <span class="badge badge-dark"><?= h($deets[21]) ?></span> 
Dropped <span class="badge badge-dark"><?= h($deets[22]) ?></span> 

<?php $percentfull = floor($deets[18] / $deets[12] * 100) ?>
<?php $percentmin = floor($deets[11] / $deets[12] * 100) ?>
<?php 
$percentstatus = 'bg-warning';
if($percentfull > $percentmin) $percentstatus = 'bg-success';
?>
<div class="progress my-3 progress-bar-striped">
	<div class="progress-bar progress-bar-striped  <?= $percentstatus ?>" 
		role="progressbar" 
		style="width: <?= $percentfull ?>%" 
		aria-valuenow="<?= $percentfull ?>" 
		aria-valuemin="0" 
		aria-valuemax="100">
			<?= $percentfull ?>% full
	</div>
</div>
<div class="row">


<div class="col-md-12">

<?php
$f = fopen('data/communication-templates.csv', 'r');
fgetcsv($f);
$templates = array();
while ($row = fgetcsv($f)) {
	
	if($row[1] == $deets[5]) {
		array_push($templates,$row);
	}
}
fclose($f);
if(count($templates) > 0) {
	foreach($templates as $t) {
		echo '<div><a href="#">' . $t[2] . '</a><br>';
		echo '<textarea class="summernote">' . $t[3] . '</textarea></div>';
	}
}
?>

</div>





<div class="col-md-4">
<h2><span class="badge badge-dark"><?= count($enrolled) ?></span> Enrolled</h2>
<form method="post" action="roster-controller.php">
<input type="hidden" name="classid" id="classid" value="<?= $classid ?>">
<input type="hidden" name="itemcode" id="itemcode" value="<?= $deets[7] ?>">
<input type="hidden" name="action" id="action" value="send">
<input type="submit" value="Send" class="btn btn-block btn-success">
<?php foreach($enrolled as $person): ?>
	<div>
		<input type="checkbox" name="sendto[]" id="sendto<?= $person[4] ?>" value="<?= $person[4] ?>" checked>
		<a href="mailto: <?= $person[20] ?>">
			<?= $person[2] ?> <?= $person[3] ?>
		</a> 
		<div class="alert alert-warning" style="display:none">
		<?= $person[4] ?><br>
		<?= $person[5] ?><br>
		<?= $person[6] ?><br>
		<?= $person[7] ?><br>
		</div>
	</div>
<?php endforeach ?>

</form>
</div>
<div class="col-md-4">
<h2><span class="badge badge-dark"><?= count($enrolledandsent) ?></span> Enrolled &amp; Sent</h2>
<?php if($enrolledandsent): ?>
<?php foreach($enrolledandsent as $person): ?>
	<div>
		<a href="mailto: <?= $person[20] ?>">
			<?= $person[2] ?> <?= $person[3] ?>
		</a> - <?= $person[21] ?>
		<div class="alert alert-warning" style="display:none">
		<?= $person[4] ?><br>
		<?= $person[5] ?><br>
		<?= $person[6] ?><br>
		<?= $person[7] ?><br>
		</div>
	</div>
<?php endforeach ?>
<?php endif ?>
</div>

<div class="col-md-4">

<?php if($waitlist): ?>
<h2><span class="badge badge-dark"><?= count($waitlist) ?></span> Waitlist</h2>
<?php foreach($waitlist as $person): ?>
	<div>
		<a href="mailto: <?= $person[20] ?>">
			<?= $person[2] ?> <?= $person[3] ?>
		</a>
		<div class="alert alert-warning" style="display:none">
		<?= $person[4] ?><br>
		<?= $person[5] ?><br>
		<?= $person[6] ?><br>
		<?= $person[7] ?><br>
		</div>
	</div>
<?php endforeach ?>
<?php endif ?>



</div>




</div>


</div>
</div>
</div>
<?php else: ?>
<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-5">
<div class="alert alert-danger">
	THIS IS A WORK IN PROGRESS. 
	Contact Allan about it if you're curious, 
	but please don't try to use it just yet. Thanks!
</div>
<form enctype="multipart/form-data" method="post" 
									accept-charset="utf-8" 
									class="up" 
									action="roster-controller.php">
<input type="hidden" name="classid" id="classid" value="<?= $classid ?>">
<input type="hidden" name="itemcode" id="itemcode" value="<?= $deets[7] ?>">
<div class="card ">
<div class="card-header">
	<h3 class="card-title">Roster Synchronization</h3>
</div>
<ul class="list-group list-group-flush">

<li class="list-group-item">1. Copy the ITEM code: <?= $deets[7] ?></li>
<li class="list-group-item">
	2. Launch the 
	<a href="https://learning.gov.bc.ca/psc/CHIPSPLM_17/EMPLOYEE/ELM/q/?ICAction=ICQryNameURL=PUBLIC.GBC_ROSTER_EXPANDED" target="_blank">
		Expanded Roster Query on ELM
	</a>
</li>
<li class="list-group-item">3. Paste the item code into the box and run the query</li>
<li class="list-group-item">4. Download the CSV file</li>
<li class="list-group-item">4. Come back here and use the form below to upload the file you just downloaded</li>
</ul>
<div class="card-body">

	<label>Roster CSV:<br>
		<input type="file" name="elmfile" class="form-control-file">
	</label>
	
	<input type="submit" class="btn btn-primary btn-block" value="Upload">
</div>
</div>
</form>
</div>
</div>
</div>
<?php endif ?>
<?php require('templates/javascript.php') ?>
<script src="/lsapp/js/summernote-bs4.js"></script>

<script>
$(document).ready(function(){

	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
		],
		placeholder: 'Type here'
	});	

});
</script>
<?php require('templates/footer.php') ?>
