<?php 

$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\inc\lsapp.php';
require($path);
$topicid = (isset($_GET['topic'])) ? $_GET['topic'] : 0;
$topic = urldecode($topicid); 
$audienceid = (isset($_GET['audience'])) ? $_GET['audience'] : 0;
$audience = urldecode($audienceid); 
$levelid = (isset($_GET['level'])) ? $_GET['level'] : 0;
$level = urldecode($levelid); 
$reportid = (isset($_GET['reporting'])) ? $_GET['reporting'] : 0;
$reporting = urldecode($reportid); 
$catid = (isset($_GET['category'])) ? $_GET['category'] : 0;
$cat = urldecode($catid); 

if ($topic) {
	$courses = getCoursesByTopic($topic);
	$heading = $topic;
} elseif($audience) {
	$courses = getCoursesByAudience($audience);
	$heading = $audience;
} elseif($level) {
	$courses = getCoursesByLevels($level);
	$heading = $level;
} elseif($reporting) {
	$courses = getCoursesByReporting($reporting);
	$heading = $reporting;
} elseif($catid) {
	$courses = getCoursesByCategory($cat);
	$heading = $cat;
} else {
	$courses = getCoursesActive($catid);
}
$categories = getCategories();
array_shift($categories);

$f = fopen('paths.csv','r');
fgetcsv($f);
$paths = array();
while($path = fgetcsv($f)) {
		array_push($paths,$path);
}

$audittype = getAuditTypes();
$topics = getAllTopics();
$audience = getAllAudiences ();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();
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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="author" content="Allan Haggett <allan.haggett@gov.bc.ca>">

<link rel="stylesheet" href="/learning/bootstrap-theme/dist/css/bootstrap-theme.min.css">

<title>Learning Centre Course Catalog</title>



<?php getScripts() ?>


<body class="bg-light-subtle">
<?php getNavigation() ?>


<div id="courses">
<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-4 col-xl-3">

<div class="mb-3">
<div class="">
<?php if($heading): ?>
<h1><span class="badge bg-primary"><?php echo count($courses) ?></span> <?= $heading ?> courses</h1>
<?php else: ?>
<h1><span class="badge bg-primary"><?php echo count($courses) ?></span> Courses</h1>
<?php endif ?>
<input class="search form-control mb-2" placeholder="search">
<div class="mb-3">
	<a class="badge bg-light-subtle " href="./courses-wtaxup.php">All Alphabetically</a> 
	<a class="badge bg-light-subtle " href="./courses-wtaxup.php?sort=dateadded">All Recent</a>
	<a class="badge bg-light-subtle " href="./courses.php?sort=dateadded">No Taxonomy</a>
</div>
<div class="mb-2">
<div>Levels</div>
	<div class="mb-3">
	<?php foreach($levels as $l): ?>
	<?php $active = 'light ' ?>
	<?php if($l == $level) $active = 'dark text-white' ?>
	<a href="courses-wtaxup.php?level=<?php echo urlencode($l) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $l ?>
	</a> 
	<?php endforeach ?>
	</div>
	<div class="mb-3">
	<div>Audience</div>
	<?php foreach($audiences as $a): ?>
	<?php $active = 'light ' ?>
	<?php if($a == $audience) $active = 'dark text-white' ?>
	<a href="courses-wtaxup.php?audience=<?php echo urlencode($a) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $a ?>
	</a> 
	<?php endforeach ?>
	</div>
	<div class="mb-3">
	<div>Topics</div>
	<?php foreach($topics as $t): ?>
	<?php $active = 'light ' ?>
	<?php if($t == $topic) $active = 'dark text-white' ?>
	<a href="courses-wtaxup.php?topic=<?php echo urlencode($t) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $t ?>
	</a> 
	<?php endforeach ?>

	</div>
	<div class="mb-3">
	
	<div>Reporting</div>
	<?php foreach($reportinglist as $r): ?>
	<?php $active = 'light ' ?>
	<?php if($r == $reporting) $active = 'dark text-white' ?>
	<a href="courses-wtaxup.php?reporting=<?php echo urlencode($r) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $r ?>
	</a> 
	<?php endforeach ?>
	</div>
	<details class="my-5">
		<summary>Old Categories</summary>
	<?php foreach($categories as $category): ?>
	<a href="courses-wtaxup.php?category=<?php echo urlencode($category[1]) ?>" 
		class="badge bg-light-subtle ">
		<?= $category[1] ?>
	</a> 
	<?php endforeach ?>
	</details>
</div>
</div>


</div>
</div>


<div class="col-md-5">

<div class="list">
<?php foreach($courses as $course): ?>
<?php $cats = explode(',', $course[20]) ?>

<div class="mb-3 p-3 bg-light-subtle rounded-3">
<div class="">
	<!-- <div class="badge bg-light-subtle delivery"><?= $course[21] ?></div> -->
	<h3 class="card-title name">
		<a href="/lsapp/course.php?courseid=<?= $course[0] ?>"><?= $course[2] ?></a>
	</h3>
</div>
<div class="">


<div>
	<a class="badge bg-light-subtle " href="courses-wtaxup.php?audience=<?= urlencode($course[39]) ?>"><?= $course[39] ?></a>
	<a class="badge bg-light-subtle " href="courses-wtaxup.php?level=<?= urlencode($course[40]) ?>"><?= $course[40] ?></a>
	<a class="badge bg-light-subtle " href="courses-wtaxup.php?topic=<?= urlencode($course[38]) ?>"><?= $course[38] ?></a>
	<!-- <a href="courses-wtaxup.php?reporting=<?= urlencode($course[41]) ?>"><?= $course[41] ?></a> -->
</div>
	<!-- <details class="desc">
		<summary>Description</summary>
		<?= $course[16] ?>
	</details> -->
	<details>
		<summary>Taxonomy quick update</summary>

	<form method="post" action="/lsapp/course-update-newfast.php" class="mb-3 pb-3">
	<input type="hidden" name="CourseID" value="<?= h($course[0]) ?>">
	<div class="form-group">
	<label for="Topics<?= $course[0] ?>">Topics</label><br>
	
	<select name="Topics" id="Topics<?= $course[0] ?>" class="form-control">
	<?php foreach($topics as $t): ?>
	<?php if($course[38] == $t): ?>
	<option selected><?= $t ?></option>
	<?php else: ?>
	<option><?= $t ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

	</div>
	<div class="form-group">

	<label for="Audience<?= $course[0] ?>">Audience</label><br>
	<select name="Audience" id="Audience<?= $course[0] ?>" class="form-control">
	<?php foreach($audience as $a): ?>
	<?php if($course[39] == $a): ?>
	<option selected><?= $a ?></option>
	<?php else: ?>
	<option><?= $a ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>


	<div class="form-group">
	
	<label for="Levels<?= $course[0] ?>">Levels</label><br>
	<select name="Levels" id="Levels<?= $course[0] ?>" class="form-control">
	<?php foreach($levels as $l): ?>
	<?php if($course[40] == $l): ?>
	<option selected><?= $l ?></option>
	<?php else: ?>
	<option><?= $l ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>

	<div class="form-group">
	<label for="Reporting<?= $course[0] ?>">Reporting</label><br>
	<select name="Reporting" id="Reporting<?= $course[0] ?>" class="form-control">
	<?php foreach($reportinglist as $r): ?>
	<?php if($course[41] == $r): ?>
	<option selected><?= $r ?></option>
	<?php else: ?>
	<option><?= $r ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	<!-- <input type="text" name="Reporting" id="Reporting<?= $course[0] ?>" class="form-control" value="<?= h($course[41]) ?>"> -->
	

	</div>
	<!-- <div>
		<a href="#" class="reportingtype" data-id="<?= $course[0] ?>">Consistent Course Evaluation</a>
		<a href="#" class="reportingtype" data-id="<?= $course[0] ?>">Self report of completion</a>
		<a href="#" class="reportingtype" data-id="<?= $course[0] ?>">Multiple assessments</a>
		<a href="#" class="reportingtype" data-id="<?= $course[0] ?>">Single summative assessment</a>
		<a href="#" class="reportingtype" data-id="<?= $course[0] ?>">Comprehensive Exam/Assignment</a>
		<a href="#" class="reportingtype" data-id="<?= $course[0] ?>">Report from Moodle</a>
	</div> -->
	<button class="btn btn-primary my-3">Save Course Info</button>
	</form>
	</details>


	
</div>
</div>
<?php endforeach ?>


</div> <!-- /.card-columns -->
</div> <!-- /#courses -->
</div>
</div>
</div>
<script src="/lsapp/js/jquery-3.4.0.min.js"></script>
<script src="/lsapp/js/popper.min.js"></script>
<script src="/lsapp/js/bootstrap.min.js"></script>
<script src="/lsapp/js/list.min.js"></script>
<script>
	var options = {
		valueNames: [ 'name', 
						'delivery',
						'category'
					]
	};
	var courses = new List('courses', options);
	
</script>
</body>
</html>