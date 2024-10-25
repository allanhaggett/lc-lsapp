<?php 
require('inc/lsapp.php');
if(isAdmin()):
if($_POST):
	$fromform = $_POST;
	$avid = LOGGED_IN_IDIR . '-' . date('Ymd-His');
	
	$av = Array($avid,
				'',
				h($fromform['Type']),
				h($fromform['AVCode']),
				h($fromform['Details']),
				h($fromform['Condition']),
				h($fromform['Status'])
		);
		
	$newav = array($av);
	$fp = fopen('data/audio-visual.csv', 'a+');
	foreach ($newav as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
	header('Location: /lsapp/av-dashboard.php');
else: ?>


<?php getHeader() ?>

<title>Create Audio Visual</title>

<?php getScripts() ?>
<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>Create Audio Visual</h1>


<form method="post" action="av-create.php" class="mb-3 pb-3" id="avcreate">



<div class="form-group">
	<label for="Type">Type: </label>
	<input class="form-control" id="Type" type="text" name="Type" >
</div>
<div class="form-group">
	<label for="AVCode">AV Code: </label>
	<input class="form-control" id="AVCode" type="text" name="AVCode">
</div>
<div class="form-group">
	<label for="Details">Details: </label>
	<input class="form-control" id="Details" type="text" name="Details">
</div>
<div class="form-group">
	<label for="Condition">Condition: </label>
	<input class="form-control" id="Condition" type="text" name="Condition" >
</div>
<div class="form-group">
	<label for="Status">Status: </label>
	<input class="form-control" id="Status" type="text" name="Status">
</div>

<button class="btn btn-block btn-primary my-3">Create AV</button>

</form>
	
</div>
</div>


</div>

<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>

<?php endif ?>
<?php endif ?>
