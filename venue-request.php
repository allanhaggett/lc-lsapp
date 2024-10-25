<?php require('inc/lsapp.php') ?>
<?php $user = LOGGED_IN_IDIR ?>
<?php getHeader() ?>

<title>Suggest a Venue</title>

<?php getScripts() ?>
<?php getNavigation() ?>

<div class="container mb-3">

<?php if(canAccess()): ?>

<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>Suggest a Venue</h1>
<p>Do you know of a venue that would be good for course delivery? Fill out the form below to add it to our list for consideration.</p>

<form method="post" action="venue-create.php" class="mb-3 pb-3" id="serviceRequestForm">
<input class="Requested" type="hidden" name="Requested" value="<?php echo date('Y-m-d') ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="<?= $user ?>">

<div class="form-group">
	<label for="VenueName">Venue: </label>
	<input type="text" name="VenueName" id="VenueName" id="" class="form-control" required>

</div>

<div class="form-group">
	<label for="Address">Street Address: </label>
	<input type="text" name="Address" id="Address" class="form-control" required>
</div>
<div class="form-group">
	<label for="City">City: </label>
	<input type="text" name="City" id="City" class="form-control" required>
</div>
<div class="form-group">
	<label for="StateProvince">Province:</label>
	<input type="text" name="StateProvince" id="StateProvince" class="form-control" value="BC" readonly>
</div>
<div class="form-group">
	<label for="ZIPPostal">Postal Code: </label>
	<input type="text" name="ZIPPostal" id="ZIPPostal" class="form-control" required>
</div>
<div class="form-group">
	<label for="ContactName">Contact Name: </label>
	<input type="text" name="ContactName" id="ContactName" class="form-control">
</div>
<div class="form-group">
	<label for="BusinessPhone">Phone #: </label>
	<input type="text" name="BusinessPhone" id="BusinessPhone" class="form-control">
</div>
<div class="form-group">
	<label for="email">Email address: </label>
	<input type="text" name="email" id="email" class="form-control">
</div>
<div class="form-group">
	<label for="Notes">Cancellation Policy:</label>
	<textarea name="Notes" id="Notes" class="form-control"></textarea>
</div>

<button class="btn btn-block btn-primary my-3">Suggest a New Venue</button>

</form>
	
</div>
</div>




<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>


</div>

<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php') ?>