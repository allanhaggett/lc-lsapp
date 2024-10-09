<?php 
require('inc/lsapp.php');
if(canAccess()):

if($_POST):

	$fromform = $_POST;

	$f = fopen('data/venues.csv','r');
	$temp_table = fopen('data/venues-temp.csv','w');
	// pop the headers off the source file and start the new file with those headers
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	
	$vid = $fromform['VenueID'];
	
	$venue = Array($vid,
				h($fromform['VenueName']),
				h($fromform['ContactName']),
				h($fromform['BusinessPhone']),
				h($fromform['Address']),
				h($fromform['City']),
				h($fromform['StateProvince']),
				h($fromform['ZIPPostal']),
				h($fromform['email']),
				h($fromform['Notes']),
				h($fromform['Active']),
				h($fromform['Union']),
				h($fromform['Region']),
				h($fromform['Votes'])
		);
	
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $vid) {
			fputcsv($temp_table,$venue);
		} else {
			fputcsv($temp_table,$data);
		}
	}
	
	fclose($f);
	fclose($temp_table);

	rename('data/venues-temp.csv','data/venues.csv');

	header('Location: venue.php?vid=' . $vid);
	
else: ?>



<?php $venueid = $_GET['vid'] ?>
<?php $v = getVenue($venueid) ?>

<?php getHeader() ?>

<title>Edit <?= $v[1] ?></title>



<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">

<?php getScripts() ?>

<body>
<?php getNavigation() ?>


<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>Edit <?= $v[1] ?></h1>


<form method="post" action="venue-update.php" class="mb-3 pb-3" id="serviceRequestForm">

<!--<input class="Requested" type="hidden" name="Requested" value="<?php echo date('Y-m-d') ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="">-->

<input class="VenueID" type="hidden" name="VenueID" value="<?= $v[0] ?>">
<input class="Active" type="hidden" name="Active" value="TRUE">
<input class="Union" type="hidden" name="Union" value="FALSE">

<div class="form-group">
	<label for="Region">Region</label>
<?php 
$regions = array(
			array('Vancouver Island','VI'),
			array('Lower Mainland','LM'),
			array('Southern BC','SBC'),
			array('Central BC','CBC'),
			array('Northern BC','NBC')
			);
?>
	<select name="Region" id="Region" class="form-control" required>
	<option></option>
	<?php foreach($regions as $r): ?>
	<?php if($v[12] == $r[1]): ?>
		<option value="<?= $r[1] ?>" selected><?= $r[0] ?></option>
	<?php else: ?>
		<option value="<?= $r[1] ?>"><?= $r[0] ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
</div>
<div class="form-group">
	<label for="VenueName">Venue: </label>
	<input type="text" name="VenueName" id="VenueName" id="" class="form-control" value="<?= $v[1] ?>">
</div>
<div class="form-group">
	<label for="Address">Street Address: </label>
	<input type="text" name="Address" id="Address" class="form-control" value="<?= $v[4] ?>">
</div>
<div class="form-group">
	<label for="City">City: </label>
	<input type="text" name="City" id="City" class="form-control" value="<?= $v[5] ?>">
</div>
<div class="form-group">
	<label for="StateProvince">Province:</label>
	<input type="text" name="StateProvince" id="StateProvince" class="form-control" value="BC" readonly>
</div>
<div class="form-group">
	<label for="ZIPPostal">Postal Code: </label>
	<input type="text" name="ZIPPostal" id="ZIPPostal" class="form-control" value="<?= $v[7] ?>">
</div>
<div class="form-group">
	<label for="ContactName">Contact Name: </label>
	<input type="text" name="ContactName" id="ContactName" class="form-control" value="<?= $v[2] ?>">
</div>
<div class="form-group">
	<label for="BusinessPhone">Phone #: </label>
	<input type="text" name="BusinessPhone" id="BusinessPhone" class="form-control" value="<?= $v[3] ?>">
</div>
<div class="form-group">
	<label for="email">Email address: </label>
	<input type="text" name="email" id="email" class="form-control" value="<?= $v[8] ?>">
</div>
<div class="form-group">
	<label for="votes">Votes: </label>
	<input type="text" name="Votes" id="Votes" class="form-control" value="<?= $v[13] ?>">
</div>
<div class="form-group">
	<label for="Notes">Cancellation Policy:</label>
	<textarea name="Notes" id="Notes" class="form-control summernote"><?= $v[9] ?></textarea>
</div>

<button class="btn btn-block btn-primary my-3">Save Changes</button>

</form>
	
</div>
</div>


</div>


<?php require('templates/javascript.php') ?>

<script src="js/summernote-bs4.js"></script>
<script>
$(document).ready(function(){
	$('.summernote').summernote({
		toolbar: [
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
		],
		placeholder: 'Type here'
	});	
});
</script>
<?php require('templates/footer.php') ?>


<?php endif ?>

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>