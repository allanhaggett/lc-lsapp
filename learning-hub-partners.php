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

<div id="partnerlist">
<input class="search form-control  mb-3" placeholder="search">

<ul class="list-group list mb-5">
<?php foreach($partners as $p): ?>
	
	<li class="list-group-item">
		<span class="partnername">
			<a href="learning-hub-partner.php?partnerid=<?= urlencode($p->name) ?>">
				<?= $p->name ?>
			</a>

			<small>
				<a href="learning-hub-partner-manage.php?id=<?= $p->id ?>">
					Edit
				</a>
			</small>
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