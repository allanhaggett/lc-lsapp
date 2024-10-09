<?php 
require('inc/lsapp.php');

// Get the full list of partners
$partners = getPartnersNew();


?>
<?php getHeader() ?>
<title>Learning Hub Partners</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row justify-content-md-center mb-3">

<div class="col-md-6">
<h1>Learning Hub Partners <span class="badge badge-dark"><?php echo count($partners) ?></span></h1>
<!-- <div class="alert alert-warning">This list is out of date. We're working on it. In the meantime, please refer to <a href="https://learningcentre.gww.gov.bc.ca/learninghub/corporate-learning-partners/">the list on the hub</a>. Thanks!</div> -->
<div class="bg-light-subtle p-3 mb-3">

<p>These are the <?php echo count($partners) ?> 
<a href="https://learningcentre.gww.gov.bc.ca/learninghub/corporate-learning-partners/" target="_blank" rel="nooperner">LearningHUB Partners</a>
whose courses are included in the Learning Hub. These partners can include groups with whom Learning Centre
deals with directly, or not, and they don't have to use the PSA Learning System (PSALS). Many of 
these partners don't work the PSALS and will have courses listed in LearningHUB that are not managed elsewhere
(e.g. EventBrite) and thus won't have any courses listed here at all.</p>
<p>This page is mostly just a list of the partners for reference, but it is important, when we're entering 
	new courses in PSALS that we are responsible for, that we apply the correct "Learning Partner" keyword 
	to that course if it is to be included in the LearningHUB.</p>


</div>
<div id="partnerlist">
<input class="search form-control  mb-3" placeholder="search">

<ul class="list-group list mb-5">
<?php foreach($partners as $p): ?>
	
	<li class="list-group-item">
		<span class="partnername">
			<a href="https://learningcentre.gww.gov.bc.ca/learninghub/learning_partner/<?= $p->slug ?>">
				<?= $p->name ?>
			</a>
		</span>
	</li>

<?php endforeach ?>
</ul>

</div> <!-- /.partnerlist -->
</div> <!-- /.col -->
</div> <!-- /.row -->

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>
<script>
$(document).ready(function(){

	$('.search').focus();
	
	var partneroptions = {
		valueNames: [ 'partnername' ]
	};
	var partners = new List('partnerlist', partneroptions);

});
</script>

<?php include('templates/footer.php') ?>