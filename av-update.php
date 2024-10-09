<?php 
require('inc/lsapp.php');
if(isAdmin()):

if($_POST):

	$fromform = $_POST;

	$f = fopen('data/audio-visual.csv','r');
	$temp_table = fopen('data/av-temp.csv','w');
	// pop the headers off the source file and start the new file with those headers
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	
	$avid = $fromform['avID'];
	// AVID,ClassID,Type,AVCode,Details,Condition,Status
	$av = Array($avid,
				h($fromform['ClassID']),
				h($fromform['Type']),
				h($fromform['AVCode']),
				h($fromform['Details']),
				h($fromform['Condition']),
				h($fromform['Status'])
		);
	
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $avid) {
			fputcsv($temp_table,$av);
		} else {
			fputcsv($temp_table,$data);
		}
	}
	
	fclose($f);
	fclose($temp_table);

	rename('data/av-temp.csv','data/audio-visual.csv');

	header('Location: av.php?avid=' . $avid);
	
else: ?>



<?php $avid = $_GET['avid'] ?>
<?php $v = getAV($avid) ?>

<?php getHeader() ?>

<title>Edit <?= $v[3] ?></title>

<?php getScripts() ?>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<body>
<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>Edit <?= $v[3] ?></h1>


<form method="post" action="av-update.php" class="mb-3 pb-3" id="avupdate">


<!--AVID,ClassID,Type,AVCode,Details,Condition,Status-->
<input class="form-control" id="avID" type="hidden" name="avID" value="<?= $v[0] ?>">



<div class="form-group">
	<label for="ClassID">Class ID: </label>
	<input class="form-control" id="ClassID" type="text" name="ClassID" value="<?= $v[1] ?>">
</div>
<div class="form-group">
	<label for="Type">Type: </label>
	<input class="form-control" id="Type" type="text" name="Type" value="<?= $v[2] ?>">
</div>
<div class="form-group">
	<label for="AVCode">AV Code: </label>
	<input class="form-control" id="AVCode" type="text" name="AVCode" value="<?= $v[3] ?>">
</div>
<div class="form-group">
	<label for="Details">Details: </label>
	<textarea class="form-control summernote" id="Details" type="text" name="Details"><?= $v[4] ?></textarea>
</div>
<div class="form-group">
	<label for="Condition">Condition: </label>
	<input class="form-control" id="Condition" type="text" name="Condition" value="<?= $v[5] ?>">
</div>
<div class="form-group">
	<label for="Status">Status: </label>
	<?php $stats = array('Active','Inactive','Missing') ?>
	<select name="Status" id="Status" class="form-control">
	<?php foreach($stats as $stat): ?>
	<?php if($v[6] == $stat): ?>
	<option selected><?= $stat ?></option>
	<?php else: ?>
	<option><?= $stat ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	<!--<input class="form-control" id="Status" type="text" name="Status" value="<?= $v[6] ?>">-->
</div>

<button class="btn btn-block btn-primary my-3">Save AV</button>

</form>
	
</div>
</div>


</div>

<?php require('templates/javascript.php') ?>
<script src="/lsapp/js/summernote-bs4.js"></script>
<script>
	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['color', ['color']],
			['link']
		],
		placeholder: 'Type here'
		
	});	
	</script>
<?php require('templates/footer.php') ?>

<?php endif ?>

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>