<?php require('inc/lsapp.php') ?>
<?php $idir = LOGGED_IN_IDIR; ?>
<?php if(canACcess()): ?>
<?php 
$cityname = (isset($_GET['name'])) ? $_GET['name'] : 0;
$venues = getVenues($cityname);
?>
<?php getHeader() ?>

<title>Subscribe to LSApp from within Outlook</title>

<?php getScripts() ?>
<body class="bg-light-subtle">
<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">
<div class="card card-primary mb-3">
<div class="card-header">
<h1 class="card-title">LSApp in Outlook</h1>
</div>
<div class="card-body">
<div class="alert alert-warning">
<h2>BETA STATUS. Outlook may complain about formatting errors, but it still works.</h2>
</div>
<p>You can "subscribe" to LSApp from within Outlook. Changes made in LSApp will automatically 
reflect in Outlook (but not the other way around). Classes in Outlook contain relevant info and a link to the LSApp page.</p>
<ol>
<li>
<button class="btn btn-primary copy" 
	href="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical.php"
	data-clipboard-text="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical.php"
	title="Copy calendar URL to your clipboard">
	All classes
</button>
<button class="btn btn-primary copy" 
	href="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical.php"
	data-clipboard-text="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical-person.php?idir=<?= $idir ?>"
	title="You are assigned as the facilitator for these classes">
	Your classes
</button>
</li>
<li>Open Outlook's calendar</li>
<li>In the Home tab, in the "Manage Calendars" section, there's an "Open Calendar" button<br>
<img src="img/calendar-open-from-internet.png" alt="Screenshot from Outlook showing the menu"></li>
<li>Choose the "From Internet" option</li>
<li>Paste the URL you copied from here into the box that pops up</li>
<li>Say Yes when it asks you to confirm</li>
<li>Enjoy LSApp from within Outlook</li>
</ol>
<h3>The URLs:</h3>
<div class="mb-3">
	All classes:<br>
	https://gww.bcpublicservice.gov.bc.ca/lsapp/ical.php
</div>
<div class="mb-3">
	Classes for which you are assigned as the facilitator:<br>
	https://gww.bcpublicservice.gov.bc.ca/lsapp/ical-person.php?idir=<?= $idir ?>
</div>

</div>
</div>


</div>
</div>
</div>



<?php require('templates/javascript.php') ?>
<script src="js/clipboard.min.js"></script>
<script>
$(document).ready(function(){
	
	var clipboard = new Clipboard('.copy');
	$('.copy').on('click',function(){ alert('Calendar URL Copied! In Outlook, Add Calendar From Internet, and paste it'); });
	
});
</script>

<?php require('templates/footer.php') ?>

<?php else: ?>


<?php require('templates/noaccess.php') ?>

<?php endif ?>