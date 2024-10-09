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
<h1>Courses w/Pre-work</h1>
<div class="card mb-3">
<div class="card-body">
These are the courses that have a pre-work URL assigned.
</div>
</div>
<div id="courselist">
<input class="search form-control  mb-3" placeholder="search">

<ul class="list-group list">
	<?php $count = 0 ?>
<?php foreach($courses as $course): ?>
	<?php if($course[1] == 'Active'): ?>
	<?php if(!empty($course[8])): ?>
	<li class="list-group-item">
		<span class="coursename">
			<a href="/lsapp/course.php?courseid=<?= h($course[0]) ?>">
				<?= h($course[2]) ?>
			</a> - 
			<a href="<?= h($course[8]) ?>" target="_blank"><?= h($course[8]) ?></a>
		</span>
		<!--<br><?= $course[0] ?>-->
		<?php $count++ ?>
	</li>
	<?php endif ?>
	<?php endif ?>
<?php endforeach ?>
</ul>

</div> <!-- /.courselist -->
</div> <!-- /.col -->
<div class="col-md-2">
<p><span class="badge badge-dark"><?= $count ?></span> courses with pre-work URLS</p>
	</div>
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