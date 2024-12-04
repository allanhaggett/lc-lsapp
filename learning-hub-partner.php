<?php 
require('inc/lsapp.php');


// $partner = getPartnerDetails(urldecode($_GET['partnerid']));

// Get the full list of partners
$courses = getCoursesByPartnerName($_GET['partnerid']);
// Grab the headers
// $headers = $courses[0];
// Pop the headers off the top
array_shift($courses);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the name to
// the temp array
$sortdir = SORT_ASC;
$sortfield = 1;

foreach($courses as $line) {
	$tmp[] = $line[$sortfield];
}

// Sort the whole kit and kaboodle by name
array_multisort($tmp, $sortdir, $courses);

?>
<?php getHeader() ?>
<title>Learning Hub Partner</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row justify-content-md-center mb-3">

<div class="col-md-6">
	<a href="learning-hub-partners.php">All LearningHUB Partners</a>
<h1><?= h($partner[1]) ?> Courses <span class="badge badge-dark"><?php echo count($courses) ?></span></h1>
<div class="bg-light-subtle p-3 mb-3">
<div><?= h($partner[2]) ?></div>
<div>
	<a href="#" class="btn btn-light bg-light-subtle mt-3" target="_blank" rel="noopener">LearningHUB page</a>
	<a href="<?= h($partner[3]) ?>" class="btn btn-light bg-light-subtle mt-3" target="_blank" rel="noopener">Partner Website</a>
</div>
</div>
<div class="bg-light-subtle p-3 mb-3">

These are the <?php echo count($courses) ?> from <?= h($_GET['partnerid']) ?> that 
are to be included in <a href="https://learningcentre.gww.gov.bc.ca/hub/" target="_blank" rel="nooperner">LearningHUB</a>.


</div>
<div id="courselist">
<input class="search form-control  mb-3" placeholder="Filter">

<ul class="list-group list mb-5">
<?php foreach($courses as $c): ?>
	<?php if($c[1] == 'Active'): ?>
	<li class="list-group-item">
		<span class="coursename">
			<a href="/lsapp/course.php?courseid=<?= h($c[0]) ?>">
				<?= h($c[2]) ?>
			</a>
		</span>
	</li>
	<?php endif ?>
<?php endforeach ?>
</ul>

</div> <!-- /.courselist -->
</div> <!-- /.col -->
</div> <!-- /.row -->

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>
<script>
$(document).ready(function(){

	$('.search').focus();
	
	var courseoptions = {
		valueNames: [ 'coursename' ]
	};
	var partners = new List('courselist', courseoptions);

});
</script>

<?php include('templates/footer.php') ?>