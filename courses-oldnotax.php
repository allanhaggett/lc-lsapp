<?php 
require('inc/lsapp.php');

// Get the full class list
$courses = getCourses();
// Grab the headers
// $headers = $courses[0];
// Pop the headers off the top
array_shift($courses);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the name to
// the temp array
$sortdir = SORT_ASC;
$sortfield = 2;
if($_GET['sort'] == 'dateadded') {
	$sortdir = SORT_DESC;
	$sortfield = 0;
}

foreach($courses as $line) {
	$tmp[] = $line[$sortfield];
}

// Sort the whole kit and kaboodle by name
array_multisort($tmp, $sortdir, $courses);

?>
<?php getHeader() ?>
<title>Courses</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row justify-content-md-center mb-3">
<?php $cats = getCategories() ?>
<!--<div class="col-md-3">
<h1>Categories <span class="badge badge-dark"><?php echo count($cats) ?></span></h1>
<ul class="list-group">
<?php foreach($cats as $cat): ?>
<li class="list-group-item">
	<a href="category.php?category=<?php echo urlencode($cat[1]) ?>"><?= $cat[1] ?></a>
	<div style="background-color: <?= $cat[2] ?>; height:3px; width: 100%;"></div>
</li>
<?php endforeach ?>
</ul>
</div>-->
<div class="col-md-6">
<h1>Courses <span class="badge badge-dark"><?php echo count($courses) ?></span></h1>
<div class="bg-light-subtle p-3 mb-3">

These are the <?php echo count($courses) ?> core corporate courses that The Learning Centre is directly 
responsible for maintaining within PeopleSoft ELM (The Learning System). 
There are over 1000 <em>other</em> courses within ELM; while we can 
provide support and admin training for those courses, most are intended to 
be managed by an owner organizations' staff.
<nav class="nav justify-content-center mt-3">
	<a href="courses.php?sort=dateadded" class="nav-link">Recently Added</a>
	<a href="courses.php" class="nav-link">Alphabetical</a>
</nav>

</div>
<div id="courselist">
<input class="search form-control  mb-3" placeholder="search">

<ul class="list-group list">
<?php foreach($courses as $course): ?>
	<?php if($course[1] == 'Active'): ?>
	<li class="list-group-item">
		<span class="coursename">
			<a href="/lsapp/course.php?courseid=<?= h($course[0]) ?>">
				<?= h($course[2]) ?>
			</a>
		</span>
		<!--<br><?= $course[0] ?>-->
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
	var courses = new List('courselist', courseoptions);

});
</script>

<?php include('templates/footer.php') ?>