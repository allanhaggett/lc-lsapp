<?php require('inc/lsapp.php') ?>


<?php $classid = (isset($_GET['classid'])) ? $_GET['classid'] : 0; ?>
<?php $deets = getClass($classid) ?>
<?php getHeader() ?>
<title>
<?php if($deets[4] == 'Dedicated'): ?>
DEDICATED | 
<?php endif ?>
<?php if($deets[7]): ?>
<?= h($deets[7]) ?> | 
<?php endif ?>
<?php print goodDateLong($deets[8],$deets[9]) ?> | 
<?= h($deets[45]) ?> <?= h($deets[6]) ?>. 
<?php $tbdtest = explode('TBD - ',$deets[25]) ?> 
<?php if(isset($tbdtest[1])): ?><?= h($deets[25]) ?> 
<?php else: ?><?= h($deets[24]) ?> in <?= h($deets[25]) ?>. 
<?php endif ?>
<?php if($deets[14]): ?>
<?= h($deets[14]) ?> facilitating. 
<?php else: ?> 
Unknown facilitating. 
<?php endif ?>
</title>
<meta name="description" content="Learning Support Adminstration Application (LSApp)">

<?php getScripts() ?>
<style>
.matnote {
	font-size: 10px;
}
</style>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<?php getNavigation() ?>

<?php if(canAccess()): ?>

<div class="container-fluid">
<div class="row justify-content-md-center mb-3">

<?php if(isset($deets[0])): ?>
<div class="col-md-5">
<?php
//0-ClassID,1-Status,2-Requested,3-RequestedBy,4-Dedicated,5-CourseID,6-CourseName,7-ItemCode,8-StartDate,9-EndDate,10-Times,
//11-MinEnroll,12-MaxEnroll,13-ShipDate,14-Facilitating,
//15-WebinarLink,16-WebinarDate,17-CourseDays,18-Enrolled,19-ReservedSeats,20-PendingApproval,21-Waitlisted,22-Dropped,
//23-VenueID,24-VenueName,25-VenueCity,26-VenueAddress,27-VenuePostalCode,28-VenueContactName,29-VenuePhone,30-VenueEmail,
//31-VenueAttention,32-RequestNotes,33-Shipper,34-Boxes,35-Weight,36-Courier,37-TrackingOut,38-TrackingIn,
//39-AttendanceReturned,40-EvaluationsReturned,41-VenueNotified,42-Modified,43-ModifiedBy,44-Assigned,
//45-DeliveryMethod,46-CourseCatergories,47-Region
//48-CheckedBy,49-ShippingStatus,50-PickupIn
?>
<a href="/lsapp/class.php?classid=<?= $deets[0] ?>" class="btn btn-secondary mb-2">Back</a>
<div id="summernote">
<p>Hi <?= $deets[28] ?>,</p>

<p>Course materials have been shipped today, to your attention, for the 
<strong><?php print goodDateLong($deets[8],$deets[9]) ?> '<?= $deets[6] ?>'</strong> 
course booked by the Learning Centre. </p>

<p><strong>Please expect <?= $deets[34] ?> boxes to arrive soon via <?= $deets[36] ?>. </strong>
I have included some details below that may be useful:</p>
<ul>
	<li>Course Name: <strong><?= $deets[6] ?></strong>  (<?= $deets[7] ?>)</li>
	<li>Course Date: <strong><?php print goodDateLong($deets[8],$deets[9]) ?></strong></li>
	<li>No. of participants: <strong><?= $deets[12] ?></strong></li>
</ul>
<p>For room set up, course materials should typically be placed in the appropriate room by 
07:30 am on the morning of the class.</p>
<p><strong>Return pick-up of the materials is scheduled for 
<?php echo nextDay($deets[9]) ?> via <?= $deets[36] ?>.</strong></p>

<p>Thanks for your help! If you have any questions, please do not hesitate to ask.</p>

</div>

<a href="mailto:<?= $deets[30] ?>?subject=BCPSA Course Materials for '<?= $deets[6] ?>' on <?php print goodDateLong($deets[8],$deets[9]) ?>" 
	class="btn btn-block btn-primary mt-2 email"
	data-classid="<?= $deets[0] ?>">
	Email <?= $deets[30] ?>
</a>


<?php endif ?>

</div>
</div>
<?php else: // if canAccess() ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>

<?php require('templates/javascript.php') ?>


<script src="/lsapp/js/summernote-bs4.js"></script>


<script>

$(document).ready(function(){
	
	$('#summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['color', ['color']],
		],
		placeholder: ''
		
	});
	
	$('.email').on('click',function(){
		var cid = $(this).data('classid');
		var url = 'class-process-venue-notify.php?classid=' + cid;
		$.ajax({type: "GET", url: url });
	});
	
});
</script>
<?php require('templates/footer.php') ?>