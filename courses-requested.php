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
foreach($courses as $line) {
	$tmp[] = $line[2];
}
// Sort the whole kit and kaboodle by name
array_multisort($tmp, SORT_ASC, $courses);

?>
<?php getHeader() ?>
<title>Requested Courses</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6">
<h1>Requested Courses</h1>
<div id="courselist">
<!--<input class="search form-control  mb-3" placeholder="search">-->
<div class="list">
<ul class="list-group">
<?php foreach($courses as $course): ?>
	<?php if($course[1] == 'Requested'): ?>
	<li class="list-group-item name"><a href="/lsapp/course.php?courseid=<?= h($course[0]) ?>"><?= h($course[2]) ?></a></li>
	<?php endif ?>
<?php endforeach ?>
</ul>
</div> <!-- /.list -->
</div> <!-- /.courselist -->
</div> <!-- /.col -->
</div> <!-- /.row -->

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>

<?php include('templates/footer.php') ?>