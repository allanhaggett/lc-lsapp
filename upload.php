<?php 

if($_POST):

	header('Location: index.php');

else: ?>

<?php require('inc/lsapp.php') ?>

<?php getHeader() ?>

<title>Upload PUBLIC.GBC_CURRENT_COURSE_INFO for audit</title>

<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
</div>
<div class="col-md-6">

<h1>Temporarily Disabled</h1>
<p>Check back soon.</p>






</div>
</div>
</div>


<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>
	
<?php endif ?>